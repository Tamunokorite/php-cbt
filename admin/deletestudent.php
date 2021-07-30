<?php require_once '../require.php'; ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php


$db = new Database;

$db->query('DELETE FROM students WHERE id=:id');
$db->bind(':id', $_GET['id']);

if ($db->execute()) {
    header('location: ' . URLROOT . '/admin/students.php');
}
else {
    die("Couldn't delete student. <a href='" . URLROOT . "/admin/students.php'>Go back</a>");
}

?> 

<?php endif; ?>