<?php require_once '../require.php' ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php 

$data = [
    'titleError' => '',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database;

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
        'title' => trim($_POST['title']),
        'titleError' => '',
    ];


    if (empty($data['title'])) {
        $data['titleError'] = 'Please enter title';
    }

    // check if errors are empty
    if (empty($data['titleError'])) {
        $db->query('INSERT INTO subjects (title) VALUES (:title)');

        // bind values
        $db->bind(':title', $data['title']);

        // execute function
        if ($db->execute()) {
            $success = 'Subject added successfully!';
            header('location: ' . URLROOT . '/admin/addsubject.php?success=' . $success);
        } else {
            die("Couldn't add subject. <a href='" . URLROOT . "/admin/addsubject.php'>Go back</a>");
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
  <title>Add Subject</title>

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
            <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title">Add Subject</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Title</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title"><span class="text-red">*<?php echo $data['titleError']; ?></span>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-dark">Add</button>
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