<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold m-0">Lesson Builder</h2>
        <button class="btn btn-primary px-4" data-bs-toggle="collapse" data-bs-target="#newLesson">New Lesson</button>
    </div>

    <div class="collapse mb-5" id="newLesson">
        <div class="card border-0 shadow-sm p-4">
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="row g-3">
                    <div class="col-md-5"><input type="text" name="title" class="form-control" placeholder="Lesson Title" required></div>
                    <div class="col-md-4"><input type="text" name="slug" class="form-control" placeholder="URL-slug" required></div>
                    <div class="col-md-3">
                        <select name="office_id" class="form-select" required>
                            <?php foreach ($offices as $off): ?>
                                <option value="<?= $off['id']; ?>"><?= $off['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><textarea name="content" class="form-control" rows="3" placeholder="Lesson Content (Indicia Grammar)"></textarea></div>
                    <div class="col-12 text-end"><button type="submit" class="btn btn-primary">Save Lesson</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($lessons as $lesson): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-3 <?= $lesson['is_archived'] ? 'opacity-50' : ''; ?>">
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($lesson['title']); ?></h6>
                        <code class="small d-block mb-3">/<?= htmlspecialchars($lesson['slug']); ?></code>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $lesson['id']; ?>">Edit</button>
                            <form method="POST">
                                <input type="hidden" name="action" value="toggle_archive">
                                <input type="hidden" name="id" value="<?= $lesson['id']; ?>">
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                    <?= $lesson['is_archived'] ? 'Restore' : 'Archive'; ?>
                                </button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Delete permanently?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $lesson['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="edit<?= $lesson['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0">
                        <form method="POST">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold">Update Lesson</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $lesson['id']; ?>">
                                <div class="mb-3"><input type="text" name="title" class="form-control" value="<?= $lesson['title']; ?>"></div>
                                <div class="mb-3"><input type="text" name="slug" class="form-control" value="<?= $lesson['slug']; ?>"></div>
                                <div class="mb-3">
                                    <select name="office_id" class="form-select">
                                        <?php foreach ($offices as $off): ?>
                                            <option value="<?= $off['id']; ?>" <?= $off['id'] == $lesson['office_id'] ? 'selected' : ''; ?>>
                                                <?= $off['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3"><textarea name="content" class="form-control" rows="8"><?= $lesson['content']; ?></textarea></div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="submit" class="btn btn-primary w-100">Update Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
