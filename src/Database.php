<?php
namespace Simcify;

use BadFunctionCallException;
use PDO;

class Database {
    
    /**
     * The conditions for this query
     * 
     * @var array
     */
    protected $conditions = array('1');
    
    /**
     * The connection to the database
     * 
     * @var \PDO
     */
    protected $conn;
    
    /**
     * The fields for this query
     * 
     * @var array
     */
    protected $fields = array();
    
    /**
     * The field-value pairs for this query
     * 
     * @var array
     */
    protected $fieldValuePairs = array();
    
    /**
     * The order of results
     * 
     * @var array
     */
    protected $order = '';
    
    /**
     * The results limit
     * 
     * @var array
     */
    protected $limit = '';
    
    /**
     * The values for this query
     * 
     * @var array
     */
    protected $values = array();
    
    /**
     * The name of the table to query
     * 
     * @var string
     */
    protected $tableName;
    
    /**
     * Create a new DB instance
     * 
     * @param   \PDO    $conn
     * @param   string  $table_name
     * 
     * @return  void
     */
    public function __construct(PDO $pdo, $table_name) {
        $this->conn = $pdo;
        $this->tableName = "`{$table_name}`";
    }
    
    /**
     * Add conditions
     * 
     * @param array $conditions
     * @return void
     */
    protected function addConditions(array $pairs, $comparator, $operator) {
        foreach($pairs as $field => $value) {
            $fieldArray = explode("(", $field);
            if ($fieldArray[0] == "MONTH" || $fieldArray[0] == "YEAR") {
                array_push($this->conditions, "{$operator} {$field} {$comparator} " . (is_string($value) ? "'{$value}'" : (is_bool($value) ? ($value ? '1' : '0') : $value)));
            }else{
                array_push($this->conditions, "{$operator} `{$field}` {$comparator} " . (is_string($value) ? "'{$value}'" : (is_bool($value) ? ($value ? '1' : '0') : $value)));
            }
        }
    }
    
    /**
     * Add fields
     * 
     * @param array $fields
     * @return void
     */
    protected function addFields(array $fields) {
        foreach($fields as $field) {
            $field = implode('`.`', explode('.', $field));
            if(!in_array($field, $this->fields)) {
                array_push($this->fields, $field);
            }
        }
    }
    
    /**
     * Add field and value pairs
     * 
     * @param array $pairs
     * @return void
     */
    protected function addFieldValuePairs(array $pairs) {
        foreach($pairs as $field => $value) {
            array_push($this->fieldValuePairs, "`{$field}` = " . (is_string($value) && !in_array($value, array('NOW()', 'CURDATE()')) ? "'{$value}'" : $value));
        }
    }
    
    /**
     * Add values
     * 
     * @param array $fields
     * @return void
     */
    protected function addValues(array $values) {
        foreach($values as $value) {
            array_push($this->values, (is_string($value) && !in_array($value, array('NOW()', 'CURDATE()')) ? "'{$value}'" : $value));
        }
    }
    
    /**
     * Get the count of a column
     * 
     * @param  string  $column
     * @param  string  $alias
     * @return DB
     */
    public function count($column, $alias) {
        return $this->get("COUNT(`{$column}`) AS `{$alias}`");
    }
    

    /**
     * SQL command (used by database updater)
     *
     * @return bool
     */
    public function command($command) {
        return $this->conn->query($command);
    }

    /**
     * Delete items from the database
     * 
     * @return bool
     */
    public function delete() {
        return $this->conn->query("DELETE FROM {$this->tableName} WHERE {$this->getConditions()}");
    }
    
    /**
     * Escape strings for security
     * 
     * @param string $str
     * @return string
     */
    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }
    
    /**
     * Get items from the database
     * 
     * @param mixed $id
     * @return mixed
     */
    public function find($id) {
        $this->where('id', $id);
        return $this->first();
    }
    
    /**
     * Get first item from the database
     * 
     * @return mixed
     */
    public function first() {
        $rows = $this->get();
        return count($rows) > 0 ? $rows[0] : null;
    }
    
    /**
     * Get items from the database
     * 
     * @param mixed
     * @return array
     */
    public function get() {
        if(func_num_args() === 1 && is_array(func_get_arg(0))) {
            $this->addFields(func_get_arg(0));
        }else if(func_num_args() > 0) {
            $this->addFields(func_get_args());
        }else if(func_num_args() < 1) {
            array_push($this->fields, '*');
        }else {
            throw new BadFunctionCallException('Invailid parameters for `Simcify\Database@get()` method');
        }
        // echo "SELECT {$this->getFields()} FROM {$this->tableName} WHERE {$this->getConditions()}{$this->order}";
        return $this->getResults("SELECT {$this->getFields()} FROM {$this->tableName} WHERE {$this->getConditions()}{$this->order}{$this->limit}");
    }
    
    /**
     * Seriallise $fields for the query
     * 
     * @return string
     */
    protected function getConditions() {
        return implode(' ', $this->conditions);
    }
    
    /**
     * Seriallise $fields for the query
     * 
     * @return string
     */
    protected function getFields() {
        return implode(', ', $this->fields);
    }
    
    /**
     * Seriallise $fieldsAndValues for the query
     * 
     * @return string
     */
    protected function getFieldValuePairs() {
        return implode(', ', $this->fieldValuePairs);
    }
    
    /**
     * Run the query and ret
     * 
     * @param string $query
     * @return array
     */
    protected function getResults($query) {
        $rows = array();
        $result = $this->conn->query($query);
        if($result) {
            while($row = $result->fetchObject()) {
                array_push($rows, $row);
            }
        }
        return $rows;
    }
    
    /**
     * Seriallise $values for the query
     * 
     * @param string $delimiter
     * @return string
     */
    protected function getValues($delimiter) {
        return implode($delimiter, $this->values);
    }
    
    /**
     * Inner join a table
     * 
     * @param mixed $table_name
     * @param string $col_1
     * @param string $col_2
     * @return bool
     */
    public function innerJoin($table_name, $col_1 = null, $col_2 = null) {
        if(!is_array($table_name)) {
            $table_name = array(array($table_name, $col_1, $col_2));
        }
        foreach($table_name as $set) {
            $set[1] = implode('`.`', explode('.', $set[1]));
            $set[2] = implode('`.`', explode('.', $set[2]));
            $this->tableName .= " INNER JOIN `{$set[0]}` ON `{$set[1]}` = `{$set[2]}`";
        }
        return $this;
    }
    
    /**
     * Insert field-value pair(s)
     * 
     * @param array $field_value_pairs
     * @return bool
     */
    public function insert(array $field_value_pairs) {
        $this->addFields(array_keys($field_value_pairs));
        $this->addValues(array_values($field_value_pairs));
        // echo "INSERT INTO {$this->tableName} ({$this->getFields()}) VALUES ({$this->getValues(',')})";
        return $this->conn->query("INSERT INTO {$this->tableName} ({$this->getFields()}) VALUES ({$this->getValues(',')})");
    }
    
    /**
     * Get the id of the lastly inserted row
     * 
     * @return int
     */
    public function insertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Insert field-value pair(s)
     * 
     * @param array $many_field_value_pairs
     * @return bool
     */
    public function insertMany(array $many_field_value_pairs) {
        $this->addFields(array_keys($many_field_value_pairs[0]));
        $values = array();
        foreach($many_field_value_pairs as $field_value_pairs) {
            $this->addValues(array_values($field_value_pairs));
            array_push($values, $this->getValues(','));
            $this->values = array();
        }
        $this->values = $values;
        return $this->conn->query("INSERT INTO {$this->tableName} ({$this->getFields()}) VALUES ({$this->getValues('), (')})");
    }
    
    /**
     * Get last item from the database
     * 
     * @return mixed
     */
    public function last() {
        return $this->orderBy('id', false)->first();
    }
    
    /**
     * Left join a table
     * 
     * @param mixed $table_name
     * @param string $col_1
     * @param string $col_2
     * @return bool
     */
    public function leftJoin($table_name, $col_1 = null, $col_2 = null) {
        if(!is_array($table_name)) {
            $table_name = array(array($table_name, $col_1, $col_2));
        }
        foreach($table_name as $set) {
            $set[1] = implode('`.`', explode('.', $set[1]));
            $set[2] = implode('`.`', explode('.', $set[2]));
            $this->tableName .= " LEFT JOIN `{$set[0]}` ON `{$set[1]}` = `{$set[2]}`";
        }
        return $this;
    }
    
    /**
     * Order Results
     * 
     * @param string $field
     * @param boolean $ascending
     * @return void
     */
    public function orderBy($field, $ascending) {
        $field = '`' . implode('`.`', explode('.', $field)) . '`';
        $this->order = " ORDER BY {$field} " . ($ascending ? 'ASC' : 'DESC');
        return $this;
    }
    
    /**
     * Limit Results
     * 
     * @param string $field
     * @param boolean $ascending
     * @return void
     */
    public function limit($number) {
        $this->limit = " LIMIT " .$number;
        return $this;
    }

    /**
     * Create a where condition with the OR operator
     * 
     * @return \Simcify\Database
     */
    public function orWhere() {
        if( func_num_args() === 3) {
            $this->where(array(
                func_get_arg(0) => func_get_arg(2),
            ), func_get_arg(1), 'OR');
        } else if( func_num_args() === 2) {
            $a0 = func_get_arg(0);
            if( is_array($a0) ) {
                $this->where($a0, func_get_arg(1), 'OR');
            } else {
                $this->where(array(
                    $a0 => func_get_arg(1),
                ), '=', 'OR');
            }
        } else {
            throw new BadFunctionCallException('Database\Simcify@orWhere expects at least two parameters! Less given.');
        }
        return $this;
    }
    
    /**
     * Right join a table
     * 
     * @param mixed $table_name
     * @param string $col_1
     * @param string $col_2
     * @return bool
     */
    public function rightJoin($table_name, $col_1 = null, $col_2 = null) {
        if(!is_array($table_name)) {
            $table_name = array(array($table_name, $col_1, $col_2));
        }
        foreach($table_name as $set) {
            $set[1] = implode('`.`', explode('.', $set[1]));
            $set[2] = implode('`.`', explode('.', $set[2]));
            $this->tableName .= " RIGHT JOIN `{$set[0]}` ON `{$set[1]}` = `{$set[2]}`";
        }
        return $this;
    }
    
    /**
     * Set a field-value pair(s)
     * 
     * @param mixed $mixed
     * @param mixed $value
     * @return DB
     */
    public function set($mixed, $value = null) {
        if(is_array($mixed)) {
            $this->addFieldValuePairs($mixed);
        }else if(func_num_args() < 2 || func_num_args() > 2) {
            throw new BadFunctionCallException('Invalid number of parameters!');
        }else {
            $this->addFieldValuePairs(array(
                $mixed => $value,
            ));
        }
        return $this;
    }
    
    /**
     * Get the sum of a column
     * 
     * @param  string  $column
     * @param  string  $alias
     * @return DB
     */
    public function sum($column, $alias) {
        return $this->get("IFNULL(SUM(`{$column}`), 0) AS `{$alias}`");
    }
    
    /**
     * Set instanciate class
     * 
     * @param   string  $table_name
     * 
     * @return  \Simcify\Database
     */
    public static function table($table_name) {
        $pdo = container(PDO::class);
        return new static($pdo, $table_name);
    }
    
    /**
     * Update items in the database
     * 
     * @param mixed $mixed
     * @param mixed $value
     * @return bool
     */
    public function update() {
        if(func_num_args() > 0) {
            if(is_array(func_get_arg(0))) {
                $this->addFieldValuePairs(func_get_arg(0));
            }else if(func_num_args() === 2) {
                $this->addFieldValuePairs(array(
                    func_get_arg(0) => func_get_arg(1),
                ));
            }else {
                throw new BadFunctionCallException('Invalid number of parameters!');
            }
        }
        return $this->conn->query("UPDATE {$this->tableName} SET {$this->getFieldValuePairs()} WHERE {$this->getConditions()}");
    }
    
    /**
     * Set conditions to the query statement 
     * 
     * @return DB
     */
    public function where() {
        if(func_num_args() === 3) {
            if(is_array(func_get_arg(0))) {
                $this->addConditions(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            } else {
                $this->addConditions(array(
                    func_get_arg(0) => func_get_arg(2),
                ), func_get_arg(1), 'AND');
            }
        }else if(func_num_args() === 2) {
            if(is_array(func_get_arg(0))) {
                $this->addConditions(func_get_arg(0), func_get_arg(1), 'AND');
            } else {
                $this->addConditions(array(
                    func_get_arg(0) => func_get_arg(1),
                ), '=', 'AND');
            }
        } else if(func_num_args() === 1) {
            if (is_array(func_get_arg(0))) {
                $this->addConditions(func_get_arg(0), '=', 'AND');
            } else {
                throw new BadFunctionCallException('\Simcify\Database@where expects the parameter given to be an array!');
            }
        } else {
            throw new BadFunctionCallException('\Simcify\Database@where expects at least two parameters! Less given.');
        }
        return $this;
    }
}
