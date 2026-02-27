<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-0">Institutional Rolls</h2>
            <p class="text-muted small text-uppercase">Registry of Permanent Continuity</p>
        </div>
        <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#newRoll">Archive New Roll</button>
    </div>

    <div class="row g-4 justify-content-center">
        <?php foreach($data['rolls'] as $roll): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-3 <?= ($roll['status'] === 'superseded') ? 'opacity-75 bg-light' : ''; ?>">
                    <div class="card-body text-center">
                        <code class="small d-block mb-2 text-muted"><?= $roll['roll_id']; ?></code>
                        <h6 class="fw-bold mb-3"><?= htmlspecialchars($roll['title']); ?></h6>
                        
                        <div class="mb-3">
                            <?php if($roll['status'] === 'active'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border"><?= strtoupper($roll['status']); ?></span>
                            <?php endif; ?>
                        </div>

                        <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#edit<?= $roll['id']; ?>">
                            Manage Record
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="edit<?= $roll['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg text-dark">
                    <form action="/admin/rolls" method="POST" class="modal-content border-0 shadow">
                        <div class="modal-header border-0">
                            <h5 class="fw-bold">Modify Roll: <?= $roll['roll_id']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $roll['id']; ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-uppercase">Title</label>
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($roll['title']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-uppercase">Slug</label>
                                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($roll['slug']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-uppercase">Relationship (Parent ID)</label>
                                    <input type="text" name="parent_roll_id" class="form-control" value="<?= $roll['parent_roll_id']; ?>" placeholder="YYYY-MM-DD_X">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-uppercase">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" <?= $roll['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="superseded" <?= $roll['status'] == 'superseded' ? 'selected' : ''; ?>>Superseded</option>
                                        <option value="archived" <?= $roll['status'] == 'archived' ? 'selected' : ''; ?>>Archived</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold text-uppercase">Lineage (Supersedes Record)</label>
                                    <select name="supersedes_id" class="form-select">
                                        <option value="">-- No Succession --</option>
                                        <?php foreach($data['rolls'] as $option): ?>
                                            <?php if($option['id'] != $roll['id']): ?>
                                                <option value="<?= $option['id']; ?>" <?= ($roll['supersedes_id'] == $option['id']) ? 'selected' : ''; ?>>
                                                    <?= $option['roll_id']; ?> - <?= $option['title']; ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold text-uppercase">Content</label>
                                    <textarea name="content" class="form-control" rows="10"><?= htmlspecialchars($roll['content']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-primary px-4">Update Continuity</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="newRoll" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg text-dark">
    <form action="/admin/rolls" method="POST" class="modal-content border-0 shadow">
      <div class="modal-header border-0">
        <h5 class="fw-bold">New Institutional Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" name="action" value="create">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="small fw-bold text-uppercase">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="small fw-bold text-uppercase">URL Slug</label>
                <input type="text" name="slug" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="small fw-bold text-uppercase">Parent Roll ID (Optional)</label>
                <input type="text" name="parent_roll_id" class="form-control">
            </div>
            <div class="col-12">
                <label class="small fw-bold text-uppercase">Content</label>
                <textarea name="content" class="form-control" rows="6"></textarea>
            </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="submit" class="btn btn-primary px-4">Archive Record</button>
      </div>
    </form>
  </div>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
