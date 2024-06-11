<?php
// /classes/QueryBuilder.php

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $fields = '*';
    protected $where = '';
    protected $bindings = [];
    protected $limitValue;
    protected $offsetValue;
    protected $joins = [];

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($fields = '*')
    {
        $this->fields = is_array($fields) ? implode(', ', $fields) : $fields;
        return $this;
    }

    public function where($field, $operator, $value)
    {
        $this->where = "WHERE {$field} {$operator} ?";
        $this->bindings[] = $value; // Ensure you're adding the value to the bindings array
        return $this;
    }

    public function limit($limit)
    {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offsetValue = $offset;
        return $this;
    }

    public function join($table, $first, $operator, $second)
    {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }

    public function get()
    {
        $sql = "SELECT {$this->fields} FROM {$this->table} ";
        if (!empty($this->joins)) {
            $sql .= implode(' ', $this->joins) . ' ';
        }
        if (!empty($this->where)) {
            $sql .= $this->where;
        }
        if (isset($this->limitValue)) {
            $sql .= " LIMIT {$this->limitValue}";
        }
        if (isset($this->offsetValue)) {
            $sql .= " OFFSET {$this->offsetValue}";
        }

        // Reset bindings if they are empty
        $bindings = empty($this->bindings) ? null : $this->bindings;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function first()
    {
        $result = $this->limit(1)->get();
        return !empty($result) ? $result[0] : null;
    }

    public function insert($data)
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->pdo->lastInsertId();
    }

    public function update($data)
    {
        $fields = '';
        $this->bindings = [];
        foreach ($data as $key => $value) {
            $fields .= "{$key} = ?, ";
            $this->bindings[] = $value;
        }
        $fields = rtrim($fields, ', ');
        $sql = "UPDATE {$this->table} SET {$fields} {$this->where}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} {$this->where}";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    public function getCommentsByPostId($postId)
    {
        $sql = "SELECT comments.*, users.username 
                FROM comments 
                JOIN users ON comments.user_id = users.id 
                WHERE comments.post_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
