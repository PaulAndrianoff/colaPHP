<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Edit</title>
</head>
<body>
    <h1>Edit Record</h1>
    <form method="POST" action="<?= $id; ?>">
        <?php foreach ($data['columns'] as $column => $type): ?>
            <label for="<?= $column; ?>"><?= ucfirst($column); ?></label>
            <input type="<?= $type ?>" id="<?= $column; ?>" name="<?= $column; ?>" value="<?= $colVal[$column]; ?>" required>
            <br>
        <?php endforeach; ?>
        <button type="submit">Update</button>
    </form>
</body>
</html>
