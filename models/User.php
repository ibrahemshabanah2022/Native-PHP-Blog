<?php
// /models/User.php

class User {
    protected $queryBuilder;

    public function __construct($queryBuilder) {
        $this->queryBuilder = $queryBuilder;
    }

    public function createUser($data) {
        return $this->queryBuilder->table('users')->insert($data);
    }

    public function getUsers() {
        return $this->queryBuilder->table('users')->select()->get();
    }
}
