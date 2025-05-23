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

    // Add these methods to your BaseModel class

    /**
     * Define a many-to-many relationship
     * 
     * @param string $related_class The related model class name
     * @param string|null $pivot_table Custom pivot table name (optional)
     * @param string|null $foreign_key The foreign key on pivot table for this model
     * @param string|null $related_key The foreign key on pivot table for related model
     * @return \app\models\core\Collection
     */
    protected function belongsToMany($related_class, $pivot_table = null, $foreign_key = null, $related_key = null)
    {
        static::connect();

        // Default naming convention if not specified
        $pivot_table = $pivot_table ?? $this->getPivotTableName(static::$table_name, $related_class::$table_name);
        $foreign_key = $foreign_key ?? $this->getSingularTableName(static::$table_name) . '_id';
        $related_key = $related_key ?? $this->getSingularTableName($related_class::$table_name) . '_id';

        $collection = new Collection($related_class);

        $query = "SELECT r.*, p.* 
                FROM " . $related_class::$table_name . " r
                JOIN $pivot_table p ON r.id = p.$related_key
                WHERE p.$foreign_key = ?";

        $stmt = static::$conn->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Extract pivot data
            $pivot_data = [];
            foreach ($row as $key => $value) {
                // If not in the main model fields, it's from the pivot table
                if (!in_array($key, $related_class::get_fields()) || $key === $foreign_key || $key === $related_key) {
                    $pivot_data[$key] = $value;
                }
            }

            $related_object = new $related_class($row);
            $related_object->pivot = (object)$pivot_data;
            $collection->add($related_object);
        }

        return $collection;
    }

    /**
     * Attach models to this model in a many-to-many relationship
     * 
     * @param string $related_class The related model class
     * @param array|int $ids The ID(s) to attach or array with pivot data
     * @param array $pivot Additional pivot data (when $ids is a single ID or array of IDs)
     * @param string|null $pivot_table Custom pivot table name (optional)
     * @return boolean
     */
    public function attach($related_class, $ids, $pivot = [], $pivot_table = null, $foreign_key = null, $related_key = null)
    {
        static::connect();

        // Default naming convention if not specified
        $pivot_table = $pivot_table ?? $this->getPivotTableName(static::$table_name, $related_class::$table_name);
        $foreign_key = $foreign_key ?? $this->getSingularTableName(static::$table_name) . '_id';
        $related_key = $related_key ?? $this->getSingularTableName($related_class::$table_name) . '_id';

        // Convert single ID to array
        if (!is_array($ids)) {
            $ids = [$ids => $pivot];
        } else if (!isset($ids[0])) {
            // If associative array like [1 => ['attr' => 'val']], keep as is
        } else if (isset($ids[0]) && !is_array($ids[0])) {
            // If sequential array of IDs, convert to associative with pivot data
            $idsWithPivot = [];
            foreach ($ids as $id) {
                $idsWithPivot[$id] = $pivot;
            }
            $ids = $idsWithPivot;
        }

        // Begin transaction
        static::$conn->begin_transaction();

        try {
            foreach ($ids as $id => $pivotData) {
                // Build field and value lists
                $fields = [$foreign_key, $related_key];
                $values = [$this->id, $id];
                $types = 'ii'; // Integer types for the IDs
                $placeholders = ['?', '?'];

                // Add additional pivot data if provided
                foreach ($pivotData as $key => $value) {
                    $fields[] = $key;
                    $values[] = $value;
                    $placeholders[] = '?';
                    $types .= $this->getBindType($value);
                }

                $fieldsStr = implode(', ', $fields);
                $placeholdersStr = implode(', ', $placeholders);

                // Build ON DUPLICATE KEY UPDATE part for existing relationships
                $updates = [];
                foreach ($pivotData as $key => $value) {
                    $updates[] = "$key = VALUES($key)";
                }
                $updateStr = !empty($updates) ? " ON DUPLICATE KEY UPDATE " . implode(', ', $updates) : '';

                $query = "INSERT INTO $pivot_table ($fieldsStr) VALUES ($placeholdersStr)$updateStr";

                $stmt = static::$conn->prepare($query);
                $stmt->bind_param($types, ...$values);
                $stmt->execute();
                $stmt->close();
            }

            // Commit transaction
            static::$conn->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback on error
            static::$conn->rollback();
            return false;
        }
    }

    /**
     * Detach models from this model in a many-to-many relationship
     * 
     * @param string $related_class The related model class
     * @param array|int|null $ids The ID(s) to detach or null for all
     * @param string|null $pivot_table Custom pivot table name (optional)
     * @return boolean
     */
    public function detach($related_class, $ids = null, $pivot_table = null, $foreign_key = null, $related_key = null)
    {
        static::connect();

        // Default naming convention if not specified
        $pivot_table = $pivot_table ?? $this->getPivotTableName(static::$table_name, $related_class::$table_name);
        $foreign_key = $foreign_key ?? $this->getSingularTableName(static::$table_name) . '_id';
        $related_key = $related_key ?? $this->getSingularTableName($related_class::$table_name) . '_id';

        // Begin transaction
        static::$conn->begin_transaction();

        try {
            if ($ids === null) {
                // Detach all relationships
                $query = "DELETE FROM $pivot_table WHERE $foreign_key = ?";
                $stmt = static::$conn->prepare($query);
                $stmt->bind_param('i', $this->id);
            } else {
                // Convert single ID to array
                if (!is_array($ids)) {
                    $ids = [$ids];
                }

                // Build placeholders for IDs
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $query = "DELETE FROM $pivot_table WHERE $foreign_key = ? AND $related_key IN ($placeholders)";

                $stmt = static::$conn->prepare($query);

                // Create bind param types and values
                $types = 'i' . str_repeat('i', count($ids));
                $bindValues = array_merge([$this->id], $ids);

                $stmt->bind_param($types, ...$bindValues);
            }

            $stmt->execute();
            $stmt->close();

            // Commit transaction
            static::$conn->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback on error
            static::$conn->rollback();
            return false;
        }
    }

    /**
     * Sync the many-to-many relationship with a new set of IDs
     * 
     * @param string $related_class The related model class
     * @param array $ids The IDs to sync or array with pivot data
     * @param string|null $pivot_table Custom pivot table name (optional)
     * @return boolean
     */
    public function sync($related_class, $ids, $pivot_table = null, $foreign_key = null, $related_key = null)
    {
        static::connect();

        // Default naming convention if not specified
        $pivot_table = $pivot_table ?? $this->getPivotTableName(static::$table_name, $related_class::$table_name);
        $foreign_key = $foreign_key ?? $this->getSingularTableName(static::$table_name) . '_id';
        $related_key = $related_key ?? $this->getSingularTableName($related_class::$table_name) . '_id';

        // Begin transaction
        static::$conn->begin_transaction();

        try {
            // First detach all
            $this->detach($related_class, null, $pivot_table, $foreign_key, $related_key);

            // Then attach the new ones
            if (!empty($ids)) {
                $this->attach($related_class, $ids, [], $pivot_table, $foreign_key, $related_key);
            }

            // Commit transaction
            static::$conn->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback on error
            static::$conn->rollback();
            return false;
        }
    }

    /**
     * Helper method to generate pivot table name from two table names
     * 
     * @param string $table1
     * @param string $table2
     * @return string
     */
    private function getPivotTableName($table1, $table2)
    {
        // Sort table names alphabetically for consistency
        $tables = [$this->getSingularTableName($table1), $this->getSingularTableName($table2)];
        sort($tables);
        return implode('_', $tables);
    }

    /**
     * Get the singular form of a table name (remove 's' at the end)
     * 
     * @param string $tableName
     * @return string
     */
    private function getSingularTableName($tableName)
    {
        // Very simple singularization, you might want to improve this
        return rtrim($tableName, 's');
    }

    /**
     * Get the mysqli bind type character for a value
     * 
     * @param mixed $value
     * @return string
     */
    private function getBindType($value)
    {
        if (is_int($value)) return 'i';
        if (is_double($value)) return 'd';
        return 's'; // Default to string for all other types
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

    public static function order_by($field, $sort = 'ASC'): Collection
    {
        $collection = new Collection(static::class);
        $collection->order_by($field, $sort);

        return $collection;
    }

    /**
     * Group results by one or more columns
     * @param string|array $columns Column(s) to group by
     * @return Collection
     */
    public static function group_by($columns): Collection
    {
        $collection = new Collection(static::class);
        return $collection->group_by($columns);
    }

    /**
     * Execute a raw query and return the count result
     * @param Collection $collection Collection with query parameters
     * @return int
     */
    public static function _get_query_count($collection)
    {
        static::connect();

        $table_name = static::$table_name;
        $fields = implode(', ', $collection->select_fields);

        // Start building the query
        $query = "SELECT $fields FROM `$table_name`";
        $params = [];

        // Add WHERE clauses
        $where_clauses = [];
        foreach ($collection->where_pairs as [$field, $operator, $value]) {
            $where_clauses[] = "`$field` $operator ?";
            $params[] = $value;
        }

        // Add raw WHERE clauses
        foreach ($collection->where_raw_conditions as $condition) {
            $where_clauses[] = "(" . $condition['sql'] . ")";
            $params = array_merge($params, $condition['params']);
        }

        // Add OR WHERE clauses
        $or_where_clauses = [];
        foreach ($collection->or_where_pairs as [$field, $operator, $value]) {
            $or_where_clauses[] = "`$field` $operator ?";
            $params[] = $value;
        }

        // Combine WHERE and OR WHERE clauses
        if (!empty($where_clauses) && !empty($or_where_clauses)) {
            $query .= " WHERE (" . implode(' AND ', $where_clauses) . ") OR (" . implode(' OR ', $or_where_clauses) . ")";
        } elseif (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        } elseif (!empty($or_where_clauses)) {
            $query .= " WHERE " . implode(' OR ', $or_where_clauses);
        }

        // Special handling for COUNT with GROUP BY
        if (!empty($collection->group_by_columns)) {
            // Use a subquery to count grouped results
            $original_select = $collection->select_fields;
            $collection->select_fields = ['*'];
            $subquery = static::_build_query($collection, false);

            $query = "SELECT COUNT(*) as count_total FROM ($subquery) as subq";

            // Restore original select
            $collection->select_fields = $original_select;
        } else {
            $query = "SELECT COUNT(*) as count_total FROM `$table_name`";
            // ... add WHERE clauses as before ...
        }

        // Add sorting
        $sort_clauses = [];
        foreach ($collection->sort_pairs as [$field, $direction]) {
            $sort_clauses[] = "`$field` $direction";
        }

        if (!empty($sort_clauses)) {
            $query .= " ORDER BY " . implode(', ', $sort_clauses);
        }

        // Add LIMIT and OFFSET if specified
        if ($collection->limit_value !== null) {
            $query .= " LIMIT ?";
            $params[] = $collection->limit_value;

            if ($collection->offset_value !== null) {
                $query .= " OFFSET ?";
                $params[] = $collection->offset_value;
            }
        }

        // Prepare and execute statement
        $stmt = static::$conn->prepare($query);

        if ($params) {
            // Determine parameter types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }

            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            return (int)($row['count_total'] ?? 0);
        }

        return 0;
    }

    /**
     * Build a query string without executing it
     * @param Collection $collection Collection with query parameters
     * @param bool $includeLimit Whether to include LIMIT/OFFSET clauses
     * @return string The constructed query string
     */
    protected static function _build_query($collection, $includeLimit = true)
    {
        $table_name = static::$table_name;
        $fields = implode(', ', $collection->select_fields);

        // Start building the query
        $query = "SELECT $fields FROM `$table_name`";
        $params = [];

        // Add WHERE clauses
        $where_clauses = [];
        foreach ($collection->where_pairs as [$field, $operator, $value]) {
            $where_clauses[] = "`$field` $operator ?";
            $params[] = $value;
        }

        // Add raw WHERE clauses
        foreach ($collection->where_raw_conditions as $condition) {
            $where_clauses[] = "(" . $condition['sql'] . ")";
            $params = array_merge($params, $condition['params']);
        }

        // Add OR WHERE clauses
        $or_where_clauses = [];
        foreach ($collection->or_where_pairs as [$field, $operator, $value]) {
            $or_where_clauses[] = "`$field` $operator ?";
            $params[] = $value;
        }

        // Combine WHERE and OR WHERE clauses
        if (!empty($where_clauses) && !empty($or_where_clauses)) {
            $query .= " WHERE (" . implode(' AND ', $where_clauses) . ") OR (" . implode(' OR ', $or_where_clauses) . ")";
        } elseif (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        } elseif (!empty($or_where_clauses)) {
            $query .= " WHERE " . implode(' OR ', $or_where_clauses);
        }

        // Add GROUP BY if specified
        if (!empty($collection->group_by_columns)) {
            $query .= " GROUP BY " . implode(', ', array_map(function ($column) {
                return "`$column`";
            }, $collection->group_by_columns));
        }

        // Add sorting
        $sort_clauses = [];
        foreach ($collection->sort_pairs as [$field, $direction]) {
            $sort_clauses[] = "`$field` $direction";
        }

        if (!empty($sort_clauses)) {
            $query .= " ORDER BY " . implode(', ', $sort_clauses);
        }

        // Add LIMIT and OFFSET if specified and includeLimit is true
        if ($includeLimit && $collection->limit_value !== null) {
            $query .= " LIMIT ?";
            $params[] = $collection->limit_value;

            if ($collection->offset_value !== null) {
                $query .= " OFFSET ?";
                $params[] = $collection->offset_value;
            }
        }

        // Convert parameters to their actual values in the query string
        foreach ($params as $param) {
            $position = strpos($query, '?');
            if ($position !== false) {
                if (is_null($param)) {
                    $replacement = 'NULL';
                } elseif (is_numeric($param)) {
                    $replacement = $param;
                } else {
                    $replacement = "'" . static::$conn->real_escape_string($param) . "'";
                }
                $query = substr_replace($query, $replacement, $position, 1);
            }
        }

        return $query;
    }

    /**
     * Execute a query and return the results
     * @param Collection $collection Collection with query parameters
     * @return Collection
     */
    public static function _get_query($collection)
    {
        static::connect();

        $table_name = static::$table_name;
        $fields = implode(', ', $collection->select_fields);

        // Start building the query
        $query = "SELECT $fields FROM `$table_name`";
        $params = [];

        // Add WHERE clauses
        $where_clauses = [];
        foreach ($collection->where_pairs as [$field, $operator, $value]) {
            $where_clauses[] = "`$field` $operator ?";
            $params[] = $value;
        }

        // Add raw WHERE clauses
        foreach ($collection->where_raw_conditions as $condition) {
            $where_clauses[] = "(" . $condition['sql'] . ")";
            $params = array_merge($params, $condition['params']);
        }

        // Add OR WHERE clauses
        $or_where_clauses = [];
        foreach ($collection->or_where_pairs as [$field, $operator, $value]) {
            $or_where_clauses[] = "`$field` $operator ?";
            $params[] = $value;
        }

        // Combine WHERE and OR WHERE clauses
        if (!empty($where_clauses) && !empty($or_where_clauses)) {
            $query .= " WHERE (" . implode(' AND ', $where_clauses) . ") OR (" . implode(' OR ', $or_where_clauses) . ")";
        } elseif (!empty($where_clauses)) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        } elseif (!empty($or_where_clauses)) {
            $query .= " WHERE " . implode(' OR ', $or_where_clauses);
        }

        // Add GROUP BY if specified
        if (!empty($collection->group_by_columns)) {
            $query .= " GROUP BY " . implode(', ', array_map(function ($column) {
                return "`$column`";
            }, $collection->group_by_columns));
        }

        // Add sorting
        $sort_clauses = [];
        foreach ($collection->sort_pairs as [$field, $direction]) {
            $sort_clauses[] = "`$field` $direction";
        }

        if (!empty($sort_clauses)) {
            $query .= " ORDER BY " . implode(', ', $sort_clauses);
        }

        // Add LIMIT and OFFSET if specified
        if ($collection->limit_value !== null) {
            $query .= " LIMIT ?";
            $params[] = $collection->limit_value;

            if ($collection->offset_value !== null) {
                $query .= " OFFSET ?";
                $params[] = $collection->offset_value;
            }
        }

        // Prepare and execute statement
        $stmt = static::$conn->prepare($query);

        if ($params) {
            // Determine parameter types
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }

            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();


        while ($row = $result->fetch_assoc()) {
            $collection->add(new static($row));
            
            // if ($fields === '*' || in_array('*', $collection->select_fields)) {
            // } else {
            //     // For custom SELECT queries, just return the raw data
            //     $collection->addRaw($row);
            // }
        }

        return $collection;
    }

    public static function query()
    {
        $collection = new Collection(static::class);
        return $collection;
    }

    /**
     * Static method to get count directly
     * @return int
     */
    public static function count()
    {
        $collection = new Collection(static::class);
        return $collection->count();
    }

    /**
     * Static method to select fields
     * @param string|array $fields Fields to select
     * @return Collection
     */
    public static function select($fields)
    {
        $collection = new Collection(static::class);
        return $collection->select($fields);
    }

    /**
     * Add a raw where condition
     * @param string $sql Raw SQL condition
     * @param array $params Parameters to bind
     * @return Collection
     */
    public static function where_raw($sql, $params = [])
    {
        $collection = new Collection(static::class);
        return $collection->where_raw($sql, $params);
    }

    /**
     * Add an OR WHERE condition
     * @param string $field Field name
     * @param string $operator Comparison operator
     * @param mixed $value Value to compare against
     * @return Collection
     */
    public static function or_where($field, $operator, $value)
    {
        $collection = new Collection(static::class);
        return $collection->or_where($field, $operator, $value);
    }

    /**
     * Limit the result set
     * @param int $limit Number of records to return
     * @return Collection
     */
    public static function limit($limit)
    {
        $collection = new Collection(static::class);
        return $collection->limit($limit);
    }

    /**
     * Skip a specific number of records
     * @param int $offset Number of records to skip
     * @return Collection
     */
    public static function offset($offset)
    {
        $collection = new Collection(static::class);
        return $collection->offset($offset);
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

        $stmt = static::$conn->prepare('INSERT INTO `' . static::$table_name . '` (`' . implode('`,`', array_keys($data)) . '`) VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')');
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
                $updates[] = "`$field` = ?";
                $values[] = $this->$field;
            }
        }

        $query = 'UPDATE `' . static::$table_name . '` SET ' . implode(', ', $updates) . ' WHERE `id` = ?';
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
