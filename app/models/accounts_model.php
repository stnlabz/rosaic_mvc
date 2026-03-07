<?php

class accounts_model extends model
{

    public function get_all()
    {
        return $this->fetchAll(
            "SELECT id, username, display_name, user_level, is_active, created_at
             FROM accounts
             ORDER BY id ASC"
        );
    }


    public function get($id)
    {
        return $this->fetch(
            "SELECT id, username, display_name, user_level, is_active
             FROM accounts
             WHERE id = :id
             LIMIT 1",
            ['id' => (int)$id]
        );
    }


    public function create($data)
    {
        $sql = "INSERT INTO accounts
                (username, password_hash, display_name, user_level, is_active)
                VALUES
                (:username, :password_hash, :display_name, :user_level, :is_active)";

        $params = [
            'username'      => $data['username'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'display_name'  => $data['display_name'],
            'user_level'    => (int)$data['user_level'],
            'is_active'     => (int)$data['is_active']
        ];

        $this->query($sql, $params);

        return $this->db->lastInsertId();
    }


    public function change_password($id, $password)
    {
        $this->update(
            'accounts',
            ['password_hash' => password_hash($password, PASSWORD_DEFAULT)],
            'id = :id',
            ['id' => (int)$id]
        );
    }


    public function delete($id)
    {
        $this->query(
            "DELETE FROM accounts WHERE id = :id",
            ['id' => (int)$id]
        );
    }
}
