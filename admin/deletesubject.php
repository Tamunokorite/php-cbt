<?php require_once '../require.php'; ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php


$db = new Database;

$db->query('DELETE FROM subjects WHERE id=:id');
$db->bind(':id', $_GET['id']);

if ($db->execute()) {
    $db->query('DELETE FROM questions WHERE subject=:id');
    $db->bind(':id', $_GET['id']);
    if ($db->execute()) {
        header('location: ' . URLROOT . '/admin/subjects.php');
    }
    else {
        die("Couldn't delete question. <a href='" . URLROOT . "/admin/subjects.php'>Go back</a>");
    }
}
else {
    die("Couldn't delete subject. <a href='" . URLROOT . "/admin/subjects.php'>Go back</a>");
}

?> 

<?php endif; ?>