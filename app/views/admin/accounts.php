<?php require APPROOT . '/views/inc/head.php'; ?>

<div class="container mt-4">

<h2>Accounts</h2>

<table class="table table-bordered">
<thead>
<tr>
<th>ID</th>
<th>Username</th>
<th>Name</th>
<th>Level</th>
<th>Active</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php foreach ($data['accounts'] as $a): ?>

<tr>
<td><?php echo $a['id']; ?></td>
<td><?php echo htmlspecialchars($a['username']); ?></td>
<td><?php echo htmlspecialchars($a['display_name']); ?></td>
<td><?php echo $a['user_level']; ?></td>
<td><?php echo $a['is_active'] ? 'yes' : 'no'; ?></td>

<td>
<form method="POST" action="<?php echo URLROOT; ?>/admin_accounts/delete/<?php echo $a['id']; ?>">
<button class="btn btn-sm btn-danger">delete</button>
</form>
</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>


<h3>Create Account</h3>

<form method="POST" action="<?php echo URLROOT; ?>/admin_accounts/create">

<div class="mb-2">
<label>Username</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="mb-2">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-2">
<label>Name</label>
<input type="text" name="display_name" class="form-control">
</div>

<div class="mb-2">
<label>User Level</label>
<input type="number" name="user_level" class="form-control" value="1">
</div>

<div class="mb-3">
<label>
<input type="checkbox" name="is_active" checked> Active
</label>
</div>

<button class="btn btn-primary">Create Account</button>

</form>

</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
