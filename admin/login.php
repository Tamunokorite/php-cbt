<?php require_once '../require.php' ?>

<?php if(!isLoggedIn()) :?>

<?php 

$data = [
    'emailError' => '',
    'passwordError' => '',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database;

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'emailError' => '',
        'passwordError' => ''
    ];

    // validate username
    if (empty($data['email'])) {
        $data['emailError'] = 'Please enter an email address';
    }

    // validate password
    if (empty($data['password'])) {
        $data['passwordError'] = 'Please enter a password';
    }

    // check if errors are empty
    if (empty($data['emailError']) && empty($data['passwordError'])) {
        $db->query('SELECT * FROM administrators WHERE email = :email');

        // bind value
        $db->bind(':email', $data['email']);

        $admin = $db->single();
        
        if ($admin) {

            if ($admin->password == $data['password']) {
                $_SESSION['user_id'] = $admin->id;
                $_SESSION['email'] = $admin->email;
                $_SESSION['gender'] = $admin->gender;
                $_SESSION['dp'] = $admin->profile_pic;

                header('location: ' . URLROOT . '/admin/index.php');
            } else {
                $data['passwordError'] = 'Password is incorrect. Please try again.';
                header('location: ' . URLROOT . '/admin/login.php');
            }
        }

        if ($db->rowCount() == 0) {
            $data['passwordError'] = 'Password or email is incorrect. Please try again.';
            header('location: ' . URLROOT . '/admin/login.php');
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
  <title>Admin Login</title>

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
                <h3 class="card-title">Login</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email"><span class="text-red">*<?php echo $data['emailError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password"><span class="text-red">*<?php echo $data['passwordError']; ?></span>
                  </div>
                  <div class="form-group mb-0">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="terms" class="custom-control-input" id="exampleCheck1">
                      <label class="custom-control-label" for="exampleCheck1">I agree to the <a href="#">terms of service</a>.</label>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-dark">Login</button>
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
<?php else: ?>
    <?php header('location: ' . URLROOT . '/admin'); ?>
<?php endif; ?>