<?php
// path: /app/controllers/signup.php

class signup extends controller {

    public function index() {
        $this->view('public/signup/instructions');
    }

    public function step_1() {
        $model = $this->model('signup_model');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_SESSION['temp_signup'] = [
                'region_id' => (int)($_POST['region_id'] ?? 0),
                'state_id'  => (int)($_POST['state_id'] ?? 0),
                'county_id' => (int)($_POST['county_id'] ?? 0)
            ];
            header('Location: ' . URLROOT . '/signup/step_2');
            exit;
        }
        $data['regions'] = $model->get_regions();
        $this->view('public/signup/step_1', $data);
    }

    public function step_2() {
        if (!isset($_SESSION['temp_signup'])) {
            header('Location: ' . URLROOT . '/signup/step_1');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate_birth_number'])) {
            $dob = $_POST['dob'] ?? '';
            $clean_dob = str_replace('-', '', $dob);
            $date_parts = explode('-', $dob);
            $mm = (int)($date_parts[1] ?? 0);
            $dd = (int)($date_parts[2] ?? 0);
            
            $_SESSION['temp_signup']['dob'] = $dob;
            $_SESSION['temp_signup']['birth_number'] = $this->reduce_to_single($clean_dob);
            $_SESSION['temp_signup']['zodiac'] = $this->get_zodiac($mm, $dd);
            $_SESSION['interrogation_step'] = 0;
            header('Location: ' . URLROOT . '/signup/step_3');
            exit;
        }
        $this->view('public/signup/step_2');
    }

    public function step_3() {
        if (!isset($_SESSION['temp_signup'])) {
            header('Location: ' . URLROOT . '/signup/step_1');
            exit;
        }
        $current_q = $_SESSION['interrogation_step'] ?? 0;
        $questions = [
            0 => "state your legal name for encryption.",
            1 => "do you believe in wiccan and neo-pagan ranks?",
            2 => "identify your chosen name and initials.",
            3 => "verify your choice to walk a path to sovereignty"
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $answer = trim($_POST['answer'] ?? '');
            if ($current_q == 0) {
                $_SESSION['temp_signup']['legal_name'] = $answer;
            } elseif ($current_q == 2) {
                $parts = explode(',', $answer);
                $_SESSION['temp_signup']['chosen_name'] = trim($parts[0] ?? '');
                $_SESSION['temp_signup']['initials'] = isset($parts[1]) ? trim($parts[1]) : '';
            }
            
            $_SESSION['interrogation_step']++;
            
            if ($_SESSION['interrogation_step'] >= count($questions)) {
                header('Location: ' . URLROOT . '/signup/finalize');
                exit;
            }
            header('Location: ' . URLROOT . '/signup/step_3');
            exit;
        }

        $data['question'] = $questions[$current_q];
        $this->view('public/signup/step_3', $data);
    }

    public function finalize() {
        if (!isset($_SESSION['temp_signup'])) {
            header('Location: ' . URLROOT . '/signup/index');
            exit;
        }
        $model = $this->model('signup_model');
        $member_id = $model->register_final_member($_SESSION['temp_signup']);
        if ($member_id) {
            $data['member_id'] = $member_id;
            unset($_SESSION['temp_signup']);
            $this->view('public/signup/finalize', $data);
        } else {
            die("critical error: grid anchor failed.");
        }
    }

    private function reduce_to_single($number) {
        $digits = str_split((string)$number);
        $sum = array_sum($digits);
        return ($sum > 9) ? $this->reduce_to_single($sum) : $sum;
    }

    private function get_zodiac($month, $day) {
        $signs = [
            ['capricorn', 19], ['aquarius', 18], ['pisces', 20], ['aries', 19],
            ['taurus', 20], ['gemini', 20], ['cancer', 22], ['leo', 22],
            ['virgo', 22], ['libra', 22], ['scorpio', 21], ['sagittarius', 21],
            ['capricorn', 31]
        ];
        return ($day <= $signs[$month - 1][1]) ? $signs[$month - 1][0] : $signs[$month][0];
    }

    public function get_states($rid) {
        $model = $this->model('signup_model');
        header('Content-Type: application/json');
        echo json_encode($model->get_states_by_region((int)$rid));
        exit;
    }

    public function get_counties($sid) {
        $model = $this->model('signup_model');
        header('Content-Type: application/json');
        echo json_encode($model->get_counties_by_state((int)$sid));
        exit;
    }
}
