<?php

namespace app\models\core;


class Collection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected $model;

    protected $items = [];
    protected $items_ids = [];

    public $where_pairs = [];
    public $sort_pairs = [];

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

    public function count(): int
    {
        return count($this->items);
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
}
