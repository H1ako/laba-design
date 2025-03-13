<?php

namespace app\models\core;

abstract class BaseModel
{
    protected static $conn;
    protected static $table_name;

    protected static $public_fields = [];
    protected static $private_fields = ['id', 'updated_at', 'created_at'];

    protected $id;
    protected $updated_at;
    protected $created_at;

    protected function __construct(array $data = [])
    {
        $fields = static::get_fields();

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    public function __get($name)
    {
        $method = 'get_' . ucfirst($name) . '_attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \Exception("Property '{$name}' does not exist or is inaccessible.");
    }

    public static function format_date($date)
    {
        if ($date === null) {
            return 'Не задано';
        }
        return date('d.m.Y', strtotime($date));
    }

    public static function format_price($price)
    {
        if ($price === null) {
            return '0';
        }
        return number_format((int)$price, 0, '', ' ');
    }

    public function get_created_at_formatted_attribute()
    {
        return static::format_date($this->created_at);
    }

    public function get_id()
    {
        return $this->id;
    }

    public static function where($field, $operator, $value): Collection
    {
        $collection = new Collection(static::class);
        $collection->where($field, $operator, $value);

        return $collection;
    }

    public static function order_by($field, $sort='ASC'): Collection
    {
        $collection = new Collection(static::class);
        $collection->order_by($field, $sort);

        return $collection;
    }

    public static function _get_query($collection)
    {
        static::connect();

        $table_name = static::$table_name;

        // Start building the query
        $query = "SELECT * FROM `$table_name`";

        // Add WHERE clauses
        $where_clauses = [];
        foreach ($collection->where_pairs as [$field, $operator, $value]) {
            $sanitized_value = static::$conn->real_escape_string($value);
            $where_clauses[] = "`$field` $operator '$sanitized_value'";
        }

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        }

        // Add sorting
        $sort_clauses = [];
        foreach ($collection->sort_pairs as [$field, $direction]) {
            $sort_clauses[] = "`$field` $direction";
        }

        if (!empty($sort_clauses)) {
            $query .= " ORDER BY " . implode(', ', $sort_clauses);
        }

        $result = static::$conn->query($query);

        if (!$result) {
            throw new \Exception("Database Query Error: " . static::$conn->error);
        }

        while ($row = $result->fetch_assoc()) {
            $collection->add(new static($row));
        }

        return $collection;
    }

    protected static function get_fields()
    {
        return array_merge(static::$public_fields, static::$private_fields);
    }

    protected static function connect()
    {
        if (!static::$conn) {
            $database = Database::getInstance();
            static::$conn = $database->getConnection();
        }
    }

    public static function get_all()
    {
        static::connect();

        $collection = new Collection(static::class);
        $result = static::$conn->query('SELECT * FROM ' . static::$table_name);

        while ($row = $result->fetch_assoc()) {
            $collection->add(new static($row));
        }

        return $collection;
    }

    protected static function get_by_field($field, $value)
    {
        if (!in_array($field, static::$public_fields)) {
            return null;
        }

        return static::_get_by_field_protected($field, $value);
    }

    public static function get_by_id($id)
    {
        return static::_get_by_field_protected('id', $id);

        return $data ? new static($data) : null;
    }


    protected static function _get_by_field_protected($field, $value)
    {
        if (!in_array($field, static::get_fields())) {
            return null;
        }

        static::connect();

        $smtp = static::$conn->prepare('SELECT * FROM ' . static::$table_name . " WHERE $field = ?");
        $smtp->bind_param('s', $value);
        $smtp->execute();
        $result = $smtp->get_result();
        $data = $result->fetch_assoc();

        return $data ? new static($data) : null;
    }

    public static function create($data)
    {
        static::connect();

        $fields = static::get_fields();

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                unset($data[$field]);
            }
        }

        $stmt = static::$conn->prepare('INSERT INTO ' . static::$table_name . ' (' . implode(',', array_keys($data)) . ') VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')');
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $stmt->close();
        $createdId = static::$conn->insert_id;

        $query = static::$conn->query('SELECT * FROM ' . static::$table_name . " WHERE id = $createdId");
        $result = $query->fetch_assoc();

        return new static($result);
    }

    public function delete()
    {
        static::connect();

        $stmt = static::$conn->prepare('DELETE FROM ' . static::$table_name . ' WHERE id = ?');
        $stmt->bind_param('i', $this->id);
        $res = $stmt->execute();
        $stmt->close();

        return $res;
    }

    public function update($data)
    {
        foreach (static::$public_fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
        return $this;
    }

    public function _update_all($data)
    {
        $fields = static::get_fields();
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
        return $this;
    }

    public function save()
    {
        static::connect();

        $fields = static::get_fields();
        $updates = [];
        $values = [];
        unset($fields['id']);

        foreach ($fields as $field) {
            if (isset($this->$field)) {
                $updates[] = "$field = ?";
                $values[] = $this->$field;
            }
        }

        $query = 'UPDATE ' . static::$table_name . ' SET ' . implode(', ', $updates) . ' WHERE id = ?';
        $values[] = $this->id;

        $stmt = static::$conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
        $res = $stmt->execute();
        $stmt->close();

        return $res;
    }

    public function to_array()
    {
        $fields_list = static::$public_fields;
        array_push($fields_list, 'id');

        return array_filter(get_object_vars($this), function ($key) use ($fields_list) {
            return in_array($key, $fields_list);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function json()
    {
        return json_encode($this->to_array());
    }

    protected function to_array_all()
    {
        return array_filter(get_object_vars($this), function ($key) {
            return in_array($key, static::get_fields());
        }, ARRAY_FILTER_USE_KEY);
    }

    protected static function upload_image($file_data)
    {
        global $PROJECT_ROOT;

        $file = $file_data['tmp_name'];
        $file_name = $file_data['name'];
        $file_type = $file_data['type'];
        $file_size = $file_data['size'];

        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        if (!in_array($file_extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            return null;
        }

        $new_file_name = uniqid() . '.' . $file_extension;
        $new_file_path = '/uploads/images/' . $new_file_name;

        if (!move_uploaded_file($file, $PROJECT_ROOT . $new_file_path)) {
            return null;
        }

        return $new_file_path;
    }
}
