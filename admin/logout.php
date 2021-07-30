<?php require_once '../require.php' ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>
    <?php

        unset($_SESSION['user_id']);
        unset($_SESSION['email']);
        header('location: ' . URLROOT . '/admin/login.php');

     ?>
<?php endif; ?>