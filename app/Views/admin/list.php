<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - List</title>
    <link rel="stylesheet" href="/colaPHP/public/css/styles.css">
</head>
<body>
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
