<?php
// path: /app/models/accounts_model.php

class accounts_model extends model
{

    public function authenticate(string $username, string $password)
    {
        $stmt = $this->query(
            "SELECT * FROM accounts WHERE username = ? LIMIT 1",
            [$username]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!isset($user['password_hash'])) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        return $user;
    }
    
    public function get_all()
    {
        return $this->query(
            "SELECT accounts.*, offices.name AS office_name
             FROM accounts
             LEFT JOIN offices ON accounts.office_id = offices.id
             ORDER BY accounts.created_at DESC"
        )->fetchAll();
    }

    public function create($data)
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->query(
            "INSERT INTO accounts (username, password_hash, user_level, office_id) VALUES (?, ?, ?, ?)",
            [$data['username'], $hash, $data['user_level'], $data['office_id']]
        );
    }

    public function update_account($id, $data)
    {
        // Only update password if a new one is provided
        if (!empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            return $this->query(
                "UPDATE accounts SET username = ?, password_hash = ?, user_level = ?, office_id = ? WHERE id = ?",
                [$data['username'], $hash, $data['user_level'], $data['office_id'], $id]
            );
        }

        return $this->query(
            "UPDATE accounts SET username = ?, user_level = ?, office_id = ? WHERE id = ?",
            [$data['username'], $data['user_level'], $data['office_id'], $id]
        );
    }

    public function delete($id)
    {
        return $this->query("DELETE FROM accounts WHERE id = ?", [(int)$id]);
    }

    // ... existing set_director and assign_office methods ...
}
