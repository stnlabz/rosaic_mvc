<?php
// path: /app/controllers/covens.php

class covens extends controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('covens_model');
    }
    
    public function index()
    {
      $util = new utility();
      $util->redirect_to('/');
    }

    public function admin()
    {
        $covens = $this->model->get_active_covens();

        $totalStructural = 0;

        foreach ($covens as &$coven) {
            $count = $this->model->get_structural_count($coven['id']);
            $coven['structural_count'] = $count;
            $totalStructural += $count;
        }

        $data = [
            'covens' => $covens,
            'total_structural' => $totalStructural,
            'total_covens' => count($covens)
        ];

        $this->view('admin/covens', $data);
    }
}
