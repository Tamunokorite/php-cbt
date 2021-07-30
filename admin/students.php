<?php require_once '../require.php' ?>

<?php if(!isLoggedIn()) : ?>
    <?php header('location: ' . URLROOT . '/admin/login.php') ?>

<?php else : ?>

<?php

$db = new Database;
$db->query("SELECT * FROM students");
$data = $db->resultSet();
if($db->rowCount() <= 0) {
    $data = [];
}

function studentProfilePicture($student) {
  if (!is_null($student->profile_pic)) {
          return $student->profile_pic;
  }

  else {
      if ($student->gender == 'M') {
          return URLROOT . '/dist/img/avatar.png';
      }

      if ($student->gender == 'F') {
          return URLROOT .'/dist/img/avatar2.png';
      }

      return URLROOT . '/dist/img/boxed-bg.jpg';
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
  <title><?php echo SITENAME; ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
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

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Messages Dropdown Menu -->
      
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo URLROOT; ?>/admin" class="brand-link text-center">
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
          <a href="#" class="d-block"><?php echo $_SESSION['email'];?></a>
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
              <li class="nav-item">
                <a href="subjects.php" class="nav-link">
                  <p>Subjects</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="students.php" class="nav-link active">
                  <p>Students</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="administrators.php" class="nav-link">
                  <p>Administrators</p>
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
          <div class="col-sm-6">
            <h1 class="m-0">All Students (<?php echo count($data) ?>)</h1>
          </div><!-- /.col -->
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
          <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Pie Chart</h3>
              </div>
              <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 487px;" width="487" height="250" class="chartjs-render-monitor"></canvas>
              </div>
              <!-- /.card-body -->
            </div>
            <?php foreach ($data as $key => $val) :?>
                <div class="card">
                    <div class="card-body">
                        <img src="<?php echo studentProfilePicture($val); ?>" class="img-circle img-md elevation-2 float-right" alt="User Image">
                        <h5><?php echo $val->first_name . ' ' . $val->last_name ;?></h5>
                        <p class="text-muted">Phone: <?php $phone = !is_null($val->phone) ? $val->phone : "Not Provided"; echo $phone ?></p>
                        <p class="text-muted">Gender: <?php echo $val->gender ?></p>
                    </div>
                    <div class="card-footer">
                        <a href="mailto:<?php echo $val->email; ?>"><?php echo $val->email; ?></a>
                        <div class="float-right">
                          <a href="editstudent.php?id=<?php echo $val->id ;?>" class="btn btn-dark btn-sm">Edit</a>
                          <a href="deletestudent.php?id=<?php echo $val->id ;?>" class="btn btn-dark btn-sm">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
    <strong>Copyright &copy; 2021 <a href="<?php echo URLROOT . '/admin' ?>">CBT Portal</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<?php 

$male_count = 0;
$female_count = 0;
$other_count = 0;

foreach ($data as $key => $val) {
  if ($val->gender == 'M') {
    $male_count++;
  }
  else if ($val->gender == 'F') {
    $female_count++;
  }
  else {
    $other_count++;
  }
}

$gender_count = [$male_count, $female_count, $other_count];

?>

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    // //Create pie or douhnut chart
    // // You can switch between pie and douhnut using the method below.
    // new Chart(donutChartCanvas, {
    //   type: 'doughnut',
    //   data: donutData,
    //   options: donutOptions
    // })

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieData        = {
        labels: [
            'Male',
            'Female',
            'Other',
        ],
        datasets: [
          {
            data: [<?php echo $male_count ?>,<?php echo $female_count ?>,<?php $other_count ?>],
            backgroundColor : ['#f56954', '#00a65a', '#f39c12'],
          }
        ]
      }
    var pieOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(pieChartCanvas, {
      type: 'pie',
      data: pieData,
      options: pieOptions
    })

    // //-------------
    // //- BAR CHART -
    // //-------------
    // var barChartCanvas = $('#barChart').get(0).getContext('2d')
    // var barChartData = $.extend(true, {}, areaChartData)
    // var temp0 = areaChartData.datasets[0]
    // var temp1 = areaChartData.datasets[1]
    // barChartData.datasets[0] = temp1
    // barChartData.datasets[1] = temp0

    // var barChartOptions = {
    //   responsive              : true,
    //   maintainAspectRatio     : false,
    //   datasetFill             : false
    // }

    // new Chart(barChartCanvas, {
    //   type: 'bar',
    //   data: barChartData,
    //   options: barChartOptions
    // })

    // //---------------------
    // //- STACKED BAR CHART -
    // //---------------------
    // var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
    // var stackedBarChartData = $.extend(true, {}, barChartData)

    // var stackedBarChartOptions = {
    //   responsive              : true,
    //   maintainAspectRatio     : false,
    //   scales: {
    //     xAxes: [{
    //       stacked: true,
    //     }],
    //     yAxes: [{
    //       stacked: true
    //     }]
    //   }
    // }

    // new Chart(stackedBarChartCanvas, {
    //   type: 'bar',
    //   data: stackedBarChartData,
    //   options: stackedBarChartOptions
    // })
  })
</script>
</body>
</html>
<?php endif; ?>
