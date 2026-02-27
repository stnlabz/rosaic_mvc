<?php

class accounts extends controller
{
    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    /*==========================
        ADMIN ACCOUNT MANAGEMENT
    ==========================*/
    public function admin($url = [])
    {
        // Strictly Level 9 for Global Account Management
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 9) {
            header("Location: /auth/login");
            exit;
        }

        $model = $this->model('accounts_model');
        $offices_model = $this->model('offices_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            if ($action === 'assign_office') {
                $account_id = (int)($_POST['account_id'] ?? 0);
                $office_id  = !empty($_POST['office_id']) ? (int)$_POST['office_id'] : null;

                $model->assign_office($account_id, $office_id);

                header("Location: /admin/accounts");
                exit;
            }

            if ($action === 'promote_to_director') {
                // Sets user to Level 7 and assigns to office
                $account_id = (int)($_POST['account_id'] ?? 0);
                $office_id  = (int)($_POST['office_id'] ?? 0);

                if ($account_id > 0 && $office_id > 0) {
                    $model->update_level($account_id, 7);
                    $model->assign_office($account_id, $office_id);
                }

                header("Location: /admin/accounts");
                exit;
            }

            if ($action === 'demote_to_staff') {
                // Reverts user to Level 1
                $account_id = (int)($_POST['account_id'] ?? 0);

                if ($account_id > 0) {
                    $model->update_level($account_id, 1);
                }

                header("Location: /admin/accounts");
                exit;
            }
        }

        $data['accounts'] = $model->get_all();
        $data['offices']  = $offices_model->get_all_active();

        $this->view('admin/accounts', $data);
    }
}
