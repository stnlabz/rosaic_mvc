<?php
class auth extends controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = $this->model('accounts_model');
            $user = $model->authenticate($_POST['username'], $_POST['password']);

            if ($user) {
                session_regenerate_id();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_level'] = $user['user_level']; // Useful for admin permissions [cite: 2026-02-13]
                
                header("Location: /admin");
                exit;
            } else {
                $data['error'] = "Invalid Institutional Credentials.";
            }
        }
        $this->view('auth/login', $data ?? []);
    }
    
    public function logout() {
        // Clear all session variables [cite: 2026-02-13]
        $_SESSION = [];

        // If it's desired to kill the session cookie as well [cite: 2026-02-13]
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session [cite: 2026-02-13]
        session_destroy();

        // Redirect to the login page [cite: 2026-02-13]
        header("Location: /");
        exit;
    }
}
