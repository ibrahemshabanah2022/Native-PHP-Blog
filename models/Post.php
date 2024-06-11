<?php
// /models/Post.php

class Post {
    protected $queryBuilder;

    public function __construct($queryBuilder) {
        $this->queryBuilder = $queryBuilder;
    }

    public function createPost($data) {
        return $this->queryBuilder->table('posts')->insert($data);
    }

    public function getPosts() {
        return $this->queryBuilder->table('posts')->select()->get();
    }

    public function updatePost($id, $data) {
        return $this->queryBuilder->table('posts')->where('id', '=', $id)->update($data);
    }

    public function deletePost($id) {
        return $this->queryBuilder->table('posts')->where('id', '=', $id)->delete();
    }
}
