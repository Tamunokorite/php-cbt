<?php require_once '../require.php' ?>

<?php if(!isLoggedIn() || isRegistered($_SESSION['user_id'])) : ?>
    <?php header('location: ' . URLROOT . '/students/login.php') ?>

<?php else : ?>

<?php

$db = new Database;
$db->query("SELECT * FROM subjects");
$data = $db->resultSet();
if($db->rowCount() <= 0) {
    $data = [];
}

function takenTest($student, $subject) {
  $db = new Database;
  $db->query('SELECT * FROM  test_scores WHERE student=:student AND subject=:subject');

  $db->bind(':student', $student);
  $db->bind(':subject', $subject);

  $result = $db->single();

  if ($result) {
    return $result;
  }
  else {
    return false;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $val) {
      $db->query('INSERT INTO students_subjects (student, subject) VALUES (:student, :subject)');
      $db->bind(':student', $_SESSION['user_id']);
      $db->bind(':subject', $val);

      if ($db->execute()) {
          $db->query('UPDATE students SET registered_courses=1 WHERE id=:id');
          $db->bind(':id', $_SESSION['user_id']);

          $db->execute();
      }
      else {
          die("Something went wrong. <a href='courseregistration.php'>Go back</a>");
      }
    }

    header('location: ' . URLROOT . '/students/courses.php' );
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
  <title><?php echo SITENAME; ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
      .card {
          display: block;
          /* margin-right: 1.4rem;
          width: 18rem; */
      }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo URLROOT; ?>/students" class="brand-link text-center">
      <span class="brand-text font-weight-light"><?php echo SITENAME;?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo profilePicture(); ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['first_name'] ." "  . $_SESSION['last_name'];?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                <a href="index.php" class="nav-link">
                  <p>Home</p>
                </a>
              </li>
              <!-- <li class="nav-item">
                <a href="subjects.php" class="nav-link">
                  <p>Subjects</p>
                </a>
              </li> -->
              <li class="nav-item">
                <a href="courses.php" class="nav-link active">
                  <p>Courses</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="courseregistration.php" class="nav-link">
                  <p>Course Registration</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="offset-lg-2"></div>
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="offset-lg-2"></div>
          <div class="col-lg-8">
              <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" id="quickForm" method="POST">
                <div class="card card-dark">
                    <div class="card-header">
                        <h2 class="card-title">Course Registration</h2>
                    </div>
                    <div class="card-body">
                    <?php foreach ($data as $key => $val) :?>
                        <input type="checkbox" name="<?= $val->id ?>" id="s<?= $val->id ?>" value="<?= $val->id ?>"><span><?= " " . $val->title ?></span><br>
                    <?php endforeach; ?>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-dark">Submit</button>
                    </div>
                </div>
              </form>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      <a href="logout.php">Logout</a>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2021 <a href="<?php echo URLROOT . '/students' ?>">CBT Portal</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
</body>
</html>
<?php endif; ?>
