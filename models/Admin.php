<?php

class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function login($email, $password) {
        $this->db->query('SELECT * FROM administrators WHERE email = :email');

        // bind value
        $this->db->bind(':email', $email);

        $admin = $this->db->single();

        if ($this->db->rowCount() == 0) {
            return false;
        }

        $hashedPassword = $admin->password;

        if (password_verify($password, $hashedPassword)) {
            return $admin;
        } else {
            return false;
        }
    }

    public function createSubject($title) {
        $this->db->query('INSERT INTO subjects (title) values (:title)');

        // bind value
        $this->db->bind(':title', $title);

        // execute function
        if ($this->db->execute()) {
            return true;
        } else {
            false;
        }
    }

    public function deleteSubject($id) {
        $this->db->query('DELETE FROM subjects WHERE id = :id');

        $this->db->bind(':id', $id);

        if (!$this->db->execute()) {
            return false;
        }

        return true;
    }

    public function createQuestion($data) {
        $this->db->query('INSERT INTO questions (subject, content) values (:subject, :content)');

        // bind values
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':content', $data['content']);
    }

      
}

?>