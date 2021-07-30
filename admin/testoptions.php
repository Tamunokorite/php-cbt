<?php require_once '../require.php' ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php 


$data = [];

$db = new Database;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $subject_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['subject_id'];
  $_SESSION['subject_id'] = $subject_id;

  $db->query('SELECT * FROM subjects WHERE id=:id');
  $db->bind(':id', $subject_id);

  $subject = $db->single();

  if ($subject) {
    $questions = $subject->test_questions;
    $take_test = $subject->test_ready;
  } else {
    die("Subject doesn't exist. <a href='subjects.php>Go Back</a>'" . var_dump($_SESSION));
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_id = $_SESSION['subject_id'];

    $db->query('SELECT * FROM subjects WHERE id=:id');
    $db->bind(':id', $subject_id);

    $subject = $db->single();

    if ($subject) {
      $questions = $subject->test_questions;
      $take_test = $subject->test_ready;
    } else {
      die("Subject doesn't exist. <a href='subjects.php>Go Back</a>'" . var_dump($_SESSION));
    }

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    // $_SESSION['subject_id'] = $_POST['subject'];

    $data = [
        'questions' => $_POST['questions'],
        'takeTest' => isset($_POST['take_test']) ? $_POST['take_test'] : '',
        'time' => $_POST['duration'],
    ];

    if ($data['takeTest'] != '') {
      if ($subject->questions_count >= $data['questions']) {
        $db->query('UPDATE subjects SET test_questions=:questions, test_ready=1, test_time=:test_time WHERE id=:id');

        $db->bind(':questions', $data['questions']);
        $db->bind(':test_time', $data['time']);
        $db->bind(':id', $subject_id);

        // execute function
        if ($db->execute()) {
          $data['success'] = 'Options updated successfully!';
          header('location: testoptions.php?success=' . $data['success']);
        } else {
            die("Couldn't update test options. <a href='" . URLROOT . "/admin/subjects.php'>Go back</a>");
        }
      }
      else {
        $db->query('UPDATE subjects SET test_questions=:questions, test_ready=0, test_time=:test_time WHERE id=:id');

        $db->bind(':questions', $data['questions']);
        $db->bind(':test_time', $data['time']);
        $db->bind(':id', $subject_id);

        // execute function
        if ($db->execute()) {
          $data['success'] = 'Options updated successfully!';
          header('location: testoptions.php?success=' . $data['success']);
        } else {
            die("Couldn't update test options. <a href='" . URLROOT . "/admin/subjects.php'>Go back</a>");
        }
      }
    }
    else {
      $db->query('UPDATE subjects SET test_questions=:questions, test_time=:test_time WHERE id=:id');

      $db->bind(':questions', $data['questions']);
      $db->bind(':test_time', $data['time']);
      $db->bind(':id', $subject_id);

      // execute function
      if ($db->execute()) {
        $data['success'] = 'Options updated successfully!';
        header('location: testoptions.php?success=' . $data['success']);
      } else {
          die("Couldn't update test options. <a href='" . URLROOT . "/admin/subjects.php'>Go back</a>");
      }
    }
    }


?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Test Options</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
      .content {
          margin-top: 50px;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">


    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="offset-md-2"></div>
          <div class="col-sm-6">
            <h1>CBT Admin Portal</h1>
            <p><a href="<?php echo URLROOT; ?>/admin">Home</a></p>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="offset-md-2"></div>
          <div class="col-md-8">
            <!-- jquery validation -->
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title"><?= $subject->title ?> Test Options</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="card-body">
                  <div class="form-group">
                      <label for="subject">Subject</label>
                      <input type="text" name="subject" class="form-control" id="question" placeholder="" value="<?= $subject->id ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="question">Number of Test Questions</label>
                      <input type="text" name="questions" class="form-control" id="question" placeholder="" value="<?= $subject->test_questions ?>">
                  </div>
                  <div class="form-group">
                      <label for="duration">Test Duration (in minutes)</label>
                      <input type="number" name="duration" class="form-control" id="duration" placeholder="" value="<?= $subject->test_time ?>">
                  </div>
                  <?php if ($subject->questions_count >= $subject->test_questions): ?>
                    <div class="form-check">
                      <input type="checkbox" name="take_test" class="form-check-input" id="take_test" <?php if ($subject->test_ready == 1) {echo  "checked='checked'";} ?>>
                      <label for="take_test">Take Test</label>
                    </div>
                  <?php else: ?>
                    <span class="text-danger">Your test questions amount is higher than the number of questions you have set.<br> Please set more questions or reduce the amount of test questions if you want <br> students to be able to take the test. </span>
                  <?php endif; ?>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-dark">Edit</button>
                  <?php if (isset($_GET['success'])) : ?>
                  <span class="text-success float-right"><?php echo $_GET['success']; ?></span>
                  <?php endif; ?>
                </div>
              </form>
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section><br>
    <!-- /.content -->

  <!-- Main Footer -->
  <footer class="main-footer float-left">
    <!-- Default to the left -->
    <strong>Copyright &copy; 2021 <a href="<?php echo URLROOT . '/admin' ?>">CBT Portal</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>
<?php endif; ?>