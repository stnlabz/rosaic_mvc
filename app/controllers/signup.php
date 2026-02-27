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
            
            if (empty($dob)) {
                die("Date of Birth is required.");
            }

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
            0 => "State your Legal Name for encryption.",
            1 => "Do you believe in Wiccan and Neo-Pagan Ranks such as High Priestess or Arch Druid?",
            2 => "Identify your Chosen Name and Initials (Example: Taylor Mei, P.T.M.)",
            3 => "Verify your choice to walk a path to Sovereignty"
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $answer = trim($_POST['answer'] ?? '');
            
            if ($current_q == 0) {
                $_SESSION['temp_signup']['legal_name'] = $answer;
            } elseif ($current_q == 1) {
                if (strpos(strtolower($answer), 'no') === false) {
                    $_SESSION['interrogation_error'] = "The Squire demands the truth regarding your thoughts.";
                    header('Location: ' . URLROOT . '/signup/step_3');
                    exit;
                }
            } elseif ($current_q == 2) {
                $parts = explode(',', $answer);
                $_SESSION['temp_signup']['chosen_name'] = trim($parts[0] ?? '');
                $_SESSION['temp_signup']['initials'] = isset($parts[1]) ? trim($parts[1]) : '';
            }

            unset($_SESSION['interrogation_error']);
            $_SESSION['interrogation_step']++;

            if ($_SESSION['interrogation_step'] >= count($questions)) {
                header('Location: ' . URLROOT . '/signup/finalize');
                exit;
            }
            header('Location: ' . URLROOT . '/signup/step_3');
            exit;
        }

        $data['question'] = $questions[$current_q];
        $data['error'] = $_SESSION['interrogation_error'] ?? null;
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
            $data['birth_num'] = $_SESSION['temp_signup']['birth_number'];
            unset($_SESSION['temp_signup']);
            unset($_SESSION['interrogation_step']);
            $this->view('public/signup/finalize', $data);
        } else {
            die("Critical Error: Grid Anchor Failed.");
        }
    }

    private function reduce_to_single($number) {
        $digits = str_split((string)$number);
        $sum = array_sum($digits);
        return ($sum > 9) ? $this->reduce_to_single($sum) : $sum;
    }

    private function get_zodiac($month, $day) {
        $signs = [
            ['Capricorn', 19], ['Aquarius', 18], ['Pisces', 20], ['Aries', 19],
            ['Taurus', 20], ['Gemini', 20], ['Cancer', 22], ['Leo', 22],
            ['Virgo', 22], ['Libra', 22], ['Scorpio', 21], ['Sagittarius', 21],
            ['Capricorn', 31]
        ];
        return ($day <= $signs[$month - 1][1]) ? $signs[$month - 1][0] : $signs[$month][0];
    }

    public function get_states($region_id) {
        $model = $this->model('signup_model');
        header('Content-Type: application/json');
        echo json_encode($model->get_states_by_region((int)$region_id));
        exit;
    }

    public function get_counties($state_id) {
        $model = $this->model('signup_model');
        header('Content-Type: application/json');
        echo json_encode($model->get_counties_by_state((int)$state_id));
        exit;
    }
}
