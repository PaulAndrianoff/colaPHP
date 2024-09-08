<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Edit</title>
    <link rel="stylesheet" href="/colaPHP/public/css/styles.css">
</head>
<body>
    <h1>Table: <?= $modelName ?></h1>
    <h2>Edit Record</h2>
    <form method="POST" action="<?= $id; ?>">
        <?php foreach ($data['columns'] as $column => $type): ?>
            <?php if (isset($errors[$column])): ?>
                <p style="color: red;"><?= $errors[$column]; ?></p>
            <?php endif; ?>
            <label for="<?= $column; ?>"><?= ucfirst($column); ?></label>
            <input type="<?= $type ?>" id="<?= $column; ?>" name="<?= $column; ?>" value="<?= $colVal[$column]; ?>" required>
            <br>
        <?php endforeach; ?>
        <button type="submit">Update</button>
    </form>
</body>
</html>
