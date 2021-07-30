<?php require_once '../require.php'; ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php


$db = new Database;

$db->query('DELETE FROM administrators WHERE id=:id');
$db->bind(':id', $_GET['id']);

if ($db->execute()) {
    header('location: ' . URLROOT . '/admin/administrators.php');
}
else {
    die("Couldn't delete administrator. <a href='" . URLROOT . "/admin/administrators.php'>Go back</a>");
}

?> 

<?php endif; ?>