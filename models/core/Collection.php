<?php

namespace app\models\core;


class Collection implements \IteratorAggregate, \Countable
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
}
