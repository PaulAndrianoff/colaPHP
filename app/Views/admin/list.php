<?php
$title = 'Admin Panel - List';
include __DIR__ . '/../parts/admin/admin_header.php';
?>
    <h1>Admin Panel - List</h1>
    <a href="<?= strtolower($model); ?>/create">Create New</a>
    <table border="1">
        <tr>
            <?php foreach (array_keys($data[0]) as $column): ?>
                <th><?= $column; ?></th>
            <?php endforeach; ?>
            <th>Actions</th>
        </tr>
        <?php foreach ($data as $row): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?= $value; ?></td>
                <?php endforeach; ?>
                <td>
                    <a href="<?= strtolower($model); ?>/edit/<?= $row['id']; ?>">Edit</a>
                    <a href="<?= strtolower($model); ?>/delete/<?= $row['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
