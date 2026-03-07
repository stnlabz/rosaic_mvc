<?php

class accounts_model extends model
{

    public function all()
    {
        return $this->fetchAll(
            "SELECT id, username, user_level, display_name
             FROM accounts
             ORDER BY id ASC"
        );
    }


    public function create($username, $password, $name, $level)
    {
        $this->query(
            "INSERT INTO accounts
            (username, password_hash, display_name, user_level)
            VALUES
            (:u, :p, :n, :l)",
            [
                'u' => $username,
                'p' => password_hash($password, PASSWORD_DEFAULT),
                'n' => $name,
                'l' => (int)$level
            ]
        );
    }


    public function change_password($id, $password)
    {
        $this->query(
            "UPDATE accounts
             SET password_hash = :p
             WHERE id = :id",
            [
                'p'  => password_hash($password, PASSWORD_DEFAULT),
                'id' => (int)$id
            ]
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
