<?php
include __DIR__ . '/../header.php';
?>

<body>
<h1>Welcome to the Admin Panel</h1>
<ul>
    <li><a href="<?= getUrl('admin') ?>">Home</a></li>
    <li><a href="<?= getUrl('admin/configuration') ?>">Configuration Panel</a></li>
    <li><a href="<?= getUrl('logout') ?>">Logout</a></li>
</ul>