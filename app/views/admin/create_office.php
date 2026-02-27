<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-5">
    <h1 class="display-6 mb-4">Create Office</h1>

    <form method="POST" action="/admin/store_office">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="5" class="form-control"></textarea>
        </div>

        <button class="btn btn-outline-primary">
            Save Office
        </button>

    </form>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>

