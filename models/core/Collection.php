<?php

namespace app\models\core;


class Collection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected $model;

    protected $items = [];
    protected $items_ids = [];

    public $where_pairs = [];
    public $where_raw_conditions = [];
    public $or_where_pairs = [];
    public $sort_pairs = [];
    public $limit_value = null;
    public $offset_value = null;
    public $select_fields = ['*'];
    public $group_by_columns = [];

    public function __construct($data)
    {
        if (is_array($data)) {
            if (!isset($this->model) && isset($data[0])) {
                $this->model = get_class($data[0]);
            }

            foreach ($data as $item) {
                $this->add($item);
            }
        } else {
            $this->model = $data;
        }
    }

    /**
     * Group results by one or more columns
     * 
     * @param string|array $columns Column(s) to group by
     * @return Collection
     */
    public function group_by($columns): Collection
    {
        if (is_string($columns)) {
            $this->group_by_columns[] = $columns;
        } else if (is_array($columns)) {
            $this->group_by_columns = array_merge($this->group_by_columns ?? [], $columns);
        }
        return $this;
    }

    /**
     * Add raw data item to collection (for custom SELECT queries)
     * 
     * @param array $data Raw data array
     * @return Collection
     */
    public function addRaw($data)
    {
        $this->items[] = $data;
        return $this;
    }

    public function add($item): Collection
    {
        if (!isset($this->model)) {
            $this->model = get_class($item);
        }

        if (!in_array($item->get_id(), $this->items_ids)) {
            if ($item instanceof $this->model) {
                $this->items[] = $item;
                $this->items_ids[] = $item->id;
            }
        }

        return $this;
    }

    public function where($field, $operator, $value): Collection
    {
        $this->where_pairs[] = [$field, $operator, $value];

        return $this;
    }

    public function order_by($field, $direction = "ASC"): Collection
    {
        $this->sort_pairs[] = [$field, strtoupper($direction)];

        return $this;
    }

    public function get()
    {
        $data = $this->model::_get_query($this);

        return $data;
    }


    public function to_array(): array
    {
        return array_map(function ($item) {
            return method_exists($item, 'to_array') ? $item->to_array() : $item;
        }, $this->items);
    }

    public function json()
    {
        return  json_encode($this->to_array());
    }

    public function first()
    {
        return $this->items[0] ?? null;
    }

    public function last()
    {
        return end($this->items) ?: null;
    }

    public function take($count)
    {
        return new static(
            array_slice($this->items, 0, $count)
        );
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Reduce the collection to a single value using a callback function
     *
     * @param callable $callback The function to apply to each item
     * @param mixed $initial The initial value for the carry parameter
     * @return mixed The final reduced value
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->add($value);
        } else {
            if ($value instanceof $this->model) {
                $this->items[$offset] = $value;
                if (!in_array($value->get_id(), $this->items_ids)) {
                    $this->items_ids[] = $value->id;
                }
            }
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        if (isset($this->items[$offset])) {
            $item = $this->items[$offset];
            $key = array_search($item->id, $this->items_ids);
            if ($key !== false) {
                unset($this->items_ids[$key]);
            }
            unset($this->items[$offset]);
        }
    }

    /**
     * Add a raw where condition to the query
     * @param string $sql Raw SQL condition
     * @param array $params Parameters to bind
     * @return Collection
     */
    public function where_raw($sql, $params = []): Collection
    {
        $this->where_raw_conditions[] = ['sql' => $sql, 'params' => $params];
        return $this;
    }

    /**
     * Add an OR WHERE condition to the query
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param mixed $value Value to compare against
     * @return Collection
     */
    public function or_where($field, $operator, $value): Collection
    {
        $this->or_where_pairs[] = [$field, $operator, $value];
        return $this;
    }

    /**
     * Limit the result set to a specific number of records
     * @param int $limit Number of records to return
     * @return Collection
     */
    public function limit($limit): Collection
    {
        $this->limit_value = (int)$limit;
        return $this;
    }

    /**
     * Skip a specific number of records
     * @param int $offset Number of records to skip
     * @return Collection
     */
    public function offset($offset): Collection
    {
        $this->offset_value = (int)$offset;
        return $this;
    }

    /**
     * Select specific fields from the database
     * @param string|array $fields Fields to select
     * @return Collection
     */
    public function select($fields): Collection
    {
        if (is_string($fields)) {
            $this->select_fields = explode(',', $fields);
            // Trim whitespace from field names
            $this->select_fields = array_map('trim', $this->select_fields);
        } else if (is_array($fields)) {
            $this->select_fields = $fields;
        }
        return $this;
    }

    /**
     * Count the number of records that match the query
     * @return int
     */
    public function count(): int
    {
        if (
            empty($this->where_pairs) && empty($this->where_raw_conditions) &&
            empty($this->or_where_pairs) && count($this->items) > 0
        ) {
            return count($this->items);
        }

        // Set the select field to COUNT(*)
        $original_select = $this->select_fields;
        $this->select_fields = ['COUNT(*) as count_total'];

        // Get the count from the database
        $collection = $this->model::_get_query_count($this);

        // Restore the original select
        $this->select_fields = $original_select;

        return $collection;
    }
}
