<?php require APPROOT . '/views/inc/head.php'; ?>
<p><small><a href="/admin">Admin</a> >> <strong>Covens</strong></small></p>
<div class="container py-3">
    <h3>COVENS OVERWATCH</h3>
    <p>
        Active Covens: <?php echo $data['total_covens']; ?> |
        Total Structural Seats: <?php echo $data['total_structural']; ?>
    </p>

    <table class="table table-dark table-bordered">
        <thead>
            <tr>
                <th>Coven</th>
                <th>Structural Seats</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['covens'] as $coven): ?>
                <tr>
                    <td><?php echo htmlspecialchars($coven['name']); ?></td>
                    <td><?php echo $coven['structural_count']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/inc/foot.php'; ?>
