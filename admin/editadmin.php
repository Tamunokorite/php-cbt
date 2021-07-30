<?php require_once '../require.php' ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php 

$data = [
    'emailError' => '',
    'genderError' => '',
];

$db = new Database;
$admin_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['admin_id'];

$db->query('SELECT * FROM administrators WHERE id=:id');
$db->bind(':id', $admin_id);

$admin = $db->single();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    $_SESSION['admin_id'] = $_POST['id'];

    $data = [
        'id' => $_POST['id'],
        'email' => trim($_POST['email']),
        'gender' => trim($_POST['gender']),
        'emailError' => '',
        'genderError' => '',
    ];

    // validate username
    if (empty($data['email'])) {
        $data['emailError'] = 'Please enter an email address';
    }

    if (empty($data['gender'])) {
        $data['genderError'] = 'Please enter gender';
    }

    // check if errors are empty
    if (empty($data['emailError']) && empty($data['genderError'])) {
        $db->query('UPDATE administrators SET email = :email, gender=:gender WHERE id=:id');

        // bind values
        $db->bind(':id', $data['id']);
        $db->bind(':email', $data['email']);
        $db->bind(':gender', $data['gender']);
        

        // execute function
        if ($db->execute()) {
            header('location: ' . URLROOT . '/admin/administrators.php');
        } else {
            die("Couldn't edit admin. <a href='" . URLROOT . "/admin/administrators.php'>Go back</a>");
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
  <title>Register Admin</title>

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
                <h3 class="card-title">Edit Admin</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">ID</label>
                    <input type="number" name="id" class="form-control" id="exampleInputEmail1" value=<?php echo $_GET['id'] ?> readonly>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" value="<?php echo $admin->email?>"><span class="text-red">*<?php echo $data['emailError']; ?></span>
                  </div>
                  <div class="form-group">
                        <label>Gender</label>
                        <select class="custom-select" name="gender">
                          <option value="M" <?php if($admin->gender == 'M') {echo 'selected';}?>>Male</option>
                          <option value="F" <?php if($admin->gender == 'F') {echo 'selected';}?>>Female</option>
                          <option value="Other" <?php if($admin->gender == 'Other') {echo 'selected';}?>>Other</option>
                        </select>
                  </div>
                </div>

                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-dark">Edit</button>
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