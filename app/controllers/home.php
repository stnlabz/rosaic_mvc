<?php
declare(strict_types=1);

final class home extends controller
{
    public function index($url_params = null): void
    {
        $modules = $this->model('modules_model');

        $this->view('public/home/index');
    }
}
