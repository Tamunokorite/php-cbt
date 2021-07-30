<?php

class Student {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function register($data) {
        $this->db->query('INSERT INTO students (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)');

        // bind values
        $this->db->bind(':first_name', $data['firstName']);
        $this->db->bind(':last_name', $data['lastName']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        // execute function
        if ($this->db->execute()) {
            return true;
        } else {
            false;
        }
    }

    public function login($email, $password) {
        $this->db->query('SELECT * FROM students WHERE email = :email');

        // bind value
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        $hashedPassword = $row->password;

        if (password_verify($password, $hashedPassword)) {
            return $row;
        } else {
            return false;
        }
    }

    /*public function getSt() {
        $this->db->query("SELECT * FROM users");

        $result = $this->db->resultSet();
        
        return $result;
    }*/

    // find student by id
    public function findStudentByEmail($email) {
        // prepared statement
        $this->db->query('SELECT * FROM students WHERE email = :email');

        // id param will be binded with the id variable
        $this->db->bind(':email', $email);

        // check if email already exists
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


}

?>