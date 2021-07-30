<?php require_once '../require.php'; ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php


$db = new Database;

$db->query('SELECT * FROM questions WHERE id=:id');
$db->bind(':id', $_GET['id']);

$question = $db->single();

$db->query('SELECT * FROM subjects WHERE id=:id');
$db->bind(':id', $question->subject);

$subject = $db->single();

$questions_count = $subject->questions_count-1;

$db->query('UPDATE subjects SET questions_count=:questions_count WHERE id=:id');
$db->bind(':questions_count', $questions_count);
$db->bind(':id', $subject->id);

if ($db->execute()) {
    $db->query('DELETE FROM questions WHERE id=:id');
    $db->bind(':id', $_GET['id']);

    if ($db->execute()) {
        $db->query('DELETE FROM choices WHERE question=:id');
        $db->bind(':id', $_GET['id']);
        if ($db->execute()) {
            header('location: ' . URLROOT . '/admin/viewquestions.php?id=' . $_GET['subject_id']); 
        }
        else {
            die("Couldn't delete question. <a href='" . URLROOT . "/admin/viewquestions.php?id=" . $_GET['subject_id'] . "'>Go back</a>");
        }
    }

    else {
        die("Couldn't delete question. <a href='" . URLROOT . "/admin/viewquestions.php?id=" . $_GET['subject_id'] . "'>Go back</a>");
    }
}
else {
    die("Couldn't delete subject. <a href='" . URLROOT . "/admin/viewquestions.php?id=" . $_GET['subject_id'] ."'>Go back</a>");
}

?> 

<?php endif; ?>