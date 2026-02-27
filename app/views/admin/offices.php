<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-5">

    <h1 class="display-6 mb-4">Manage Offices</h1>

    <form method="POST" action="/admin/offices" class="mb-4 p-3 bg-light border rounded shadow-sm">
        <input type="hidden" name="action" value="create">

        <div class="row g-2">
            <div class="col-md-3">
                <label class="small text-muted">Office Name</label>
                <input type="text" name="name" class="form-control" placeholder="Office Name" required>
            </div>
            <div class="col-md-2">
                <label class="small text-muted">Slug</label>
                <input type="text" name="slug" class="form-control" placeholder="Slug" required>
            </div>
            <div class="col-md-3">
                <label class="small text-muted">Parent Department</label>
                <select name="parent_id" class="form-select">
                    <option value="">No Parent (Top Level)</option>
                    <?php foreach ($offices as $opt): ?>
                        <option value="<?= $opt['id']; ?>"><?= htmlspecialchars($opt['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small text-muted">Description</label>
                <input type="text" name="description" class="form-control" placeholder="Description">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100">Add</button>
            </div>
        </div>
    </form>

    <?php if (!empty($offices)): ?>

        <?php foreach ($offices as $office): ?>

            <div class="card mb-4 p-3 border-0 shadow-sm">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="h5"><?= htmlspecialchars($office['name']); ?></strong>
                        <?php if ($office['parent_id']): ?>
                            <span class="badge bg-info ms-2">Sub-Dept</span>
                        <?php endif; ?>
                        <br>
                        <small class="text-muted">/offices/<?= htmlspecialchars($office['slug']); ?></small>
                    </div>
                    
                    <form method="POST" action="/admin/offices">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $office['id']; ?>">
                        <button class="btn btn-sm <?= $office['is_active'] ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                            <?= $office['is_active'] ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </form>
                </div>

                <form method="POST" action="/admin/offices" class="mt-3">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $office['id']; ?>">

                    <textarea name="description"
                              class="form-control mb-2"
                              rows="2" 
                              placeholder="Academic description..."><?= htmlspecialchars($office['description']); ?></textarea>

                    <button class="btn btn-sm btn-outline-secondary">
                        Save Description
                    </button>
                </form>

            </div>

        <?php endforeach; ?>

    <?php else: ?>
        <p class="alert alert-info">No offices found. Create your first academic faculty above.</p>
    <?php endif; ?>

</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
