<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="container py-5">
    
    <div class="text-center mb-5">
        <h1 class="fw-bold text-uppercase">Oversight</h1>
        <p class="text-muted small">Indicia Institute Management Suite</p>
    </div>

    <div class="row g-4 justify-content-center mb-5">
        <?php
        // Automation: Scan controllers directory
        $controllers = glob(APPROOT . '/controllers/*.php');
        $excluded = ['admin.php', 'pages.php', 'auth.php', 'error_handler.php'];

        foreach ($controllers as $file) {
            $name = basename($file, '.php');
            if (in_array($name, $excluded)) continue;

            require_once $file;
            if (class_exists($name)) {
                $reflect = new ReflectionClass($name);
                // Only show a tile if the controller has an admin() method
                if ($reflect->hasMethod('admin')) {
                    ?>
                    <div class="col-md-6 col-lg-3 text-center">
                        <div class="card h-100 border-0 shadow-sm p-3">
                            <div class="card-body">
                                <i class="bi bi-gear h1 d-block mb-3 text-primary"></i>
                                <h6 class="fw-bold text-capitalize"><?= $name; ?></h6>
                                <a href="/admin/<?= $name; ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">Manage</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="p-4 bg-light rounded-3 d-flex justify-content-between align-items-center border">
                <div>
                    <span class="fw-bold d-block text-uppercase small">Core Maintenance</span>
                    <small class="text-muted">Rebuild sitemaps, ROR files, and search indices.</small>
                </div>
                <a href="/admin/refresh_indices" class="btn btn-primary btn-sm px-4">Refresh Indices</a>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
