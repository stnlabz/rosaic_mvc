<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold m-0 text-dark">Personnel Registry</h2>
        <button class="btn btn-primary px-4" data-bs-toggle="collapse" data-bs-target="#newAccount">Create Account</button>
    </div>

    <div class="collapse mb-5" id="newAccount">
        <div class="card border-0 shadow-sm p-4">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="row g-3">
                    <div class="col-md-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                    <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                    <div class="col-md-2">
                        <select name="user_level" class="form-select">
                            <option value="1">Level 1 (Staff)</option>
                            <option value="7">Level 7 (Director)</option>
                            <option value="9">Level 9 (Admin)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="office_id" class="form-select">
                            <option value="">No Office Assignment</option>
                            <?php foreach($offices as $off): ?>
                                <option value="<?= $off['id']; ?>"><?= $off['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1"><button type="submit" class="btn btn-primary w-100">Save</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($accounts as $acc): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center p-3">
                    <div class="card-body">
                        <i class="bi bi-person-circle h2 text-primary d-block mb-3"></i>
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($acc['username']); ?></h6>
                        <small class="text-muted d-block mb-3">
                            Level <?= $acc['user_level']; ?> • <?= $acc['office_name'] ?? 'Unassigned'; ?>
                        </small>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $acc['id']; ?>">Edit</button>
                            <form method="POST" onsubmit="return confirm('Permanently delete this account?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $acc['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="edit<?= $acc['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content border-0">
                        <form method="POST">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold">Update Account</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $acc['id']; ?>">
                                <div class="mb-3">
                                    <label class="small fw-bold">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= $acc['username']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">New Password (Leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">User Level</label>
                                    <select name="user_level" class="form-select">
                                        <option value="1" <?= $acc['user_level'] == 1 ? 'selected' : ''; ?>>Level 1</option>
                                        <option value="7" <?= $acc['user_level'] == 7 ? 'selected' : ''; ?>>Level 7</option>
                                        <option value="9" <?= $acc['user_level'] == 9 ? 'selected' : ''; ?>>Level 9</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold">Office Assignment</label>
                                    <select name="office_id" class="form-select">
                                        <option value="">Unassigned</option>
                                        <?php foreach($offices as $off): ?>
                                            <option value="<?= $off['id']; ?>" <?= $acc['office_id'] == $off['id'] ? 'selected' : ''; ?>><?= $off['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-primary w-100">Update Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
