<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Create</title>
</head>
<body>
    <h1>Create New Record</h1>
    <form method="POST" action="create">
        <?php foreach ($columns as $column => $type): ?>
            <label for="<?= $column; ?>"><?= ucfirst($column); ?></label>
            <input type="text" id="<?= $column; ?>" name="<?= $column; ?>" required>
            <br>
        <?php endforeach; ?>
        <button type="submit">Create</button>
    </form>
</body>
</html>
