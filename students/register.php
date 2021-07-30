<?php require_once '../require.php' ?>

<?php if(!isLoggedIn()) :?>

<?php 

$data = [
    'emailError' => '',
    'passwordError' => '',
    'confirmPasswordError' => '',
    'firstNameError' => '',
    'lastNameError' => '',
    'genderError' => '',
    'profilePicError' => '',
    'phoneError' => '',
];


function findStudentByEmail($email) {
  $db = new Database;
  // prepared statement
  $db->query('SELECT * FROM students WHERE email = :email');

  $db->bind(':email', $email);

  $result = $db->single();

  // check if email already exists
  if ($result) {
      return true;
  } else {
      return false;
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $db = new Database;


    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $passwordValidation = "/^(.{0,7}|[^a-z]*|[^\d]*)$/i";

    $data = [
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirmPassword' => trim($_POST['confirm_password']),
        'firstName' => trim($_POST['first_name']),
        'lastName' => trim($_POST['last_name']),
        'phone' => trim($_POST['phone']),
        'gender' => trim($_POST['gender']),
        'profilePic' => $_FILES['profilePic'],
        'emailError' => '',
        'passwordError' => '',
        'confirmPasswordError' => '',
        'firstNameError' => '',
        'lastNameError' => '',
        'genderError' => '',
        'profilePicError' => '',
        'phoneError' => '',
    ];

    // validate username
    if (empty($data['email'])) {
        $data['emailError'] = 'Please enter an email address';
    }
    else {
      if (findStudentByEmail($data['email'])) {
        $data['emailError'] = 'An account with this email address already exists';
      }

      if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $data['emailError'] = "Invalid email format";
      }
    }

    // validate password
    if (empty($data['password'])) {
        $data['passwordError'] = 'Please enter a password';
    }
    else {
      if ($data['password'] == $data['confirmPassword']) {
        $password = $data["password"];
        $cpassword = $data["confirmPassword"];
        if (strlen($password) <= '8') {
            $data['passwordError'] = "Your Password Must Contain At Least 8 Characters!";
        }
        elseif (preg_match($passwordValidation, $data['password'])) {
          $data['passwordError'] = 'Password must be have at least one numeric value.';
        }
      }
      else {
        $data['passwordError'] = "Passwords don't match";
        $data['confirmPasswordError'] = "Passwords don't match";
      }
    }

    if (empty($data['firstName'])) {
      $data['firstNameError'] = 'Please enter first name';
    }
    else {
      if (!preg_match("/^[a-zA-Z-' ]*$/",$data['firstName'])) {
        $data['firstNameError'] = "Only letters and white space allowed";
      }
    }

    if (empty($data['lastName'])) {
      $data['lastNameError'] = 'Please enter last name';
    }
    else {
      if (!preg_match("/^[a-zA-Z-' ]*$/",$data['lastName'])) {
        $data['lastNameError'] = "Only letters and white space allowed";
      }
    }

    if (empty($data['phone'])) {
      $data['phoneError'] = 'Please enter phone number';
    }
    // else {
    //   if (!preg_match("/^[0]\d{9}$/",$data['phone'])) {
    //     $data['phoneError'] = 'Phone number can only contain digits and must start with 0';
    //   }
    // }

    if (empty($data['gender'])) {
      $data['genderError'] = 'Please enter gender';
    }

    if (empty($data['profilePic'])) {
      $data['profilePicError'] = 'Please select profile picture';
    }
    $profileImageName = time() . '-' . $_FILES["profilePic"]["name"];
    // For image upload
    $target_dir = "../media/images/";
    $target_file = $target_dir . basename($profileImageName);
    // VALIDATION
    // validate image size. Size is calculated in Bytes
    if($_FILES['profilePic']['size'] > 200000) {
      $data['profilePicError'] = "Image size should not be greated than 200Kb";
    }
    // check if file exists
    if(file_exists($target_file)) {
      $data['profilePicError'] = "File already exists";
    }

    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $target_file)) {
      // check if errors are empty
      if (empty($data['emailError']) && empty($data['passwordError']) && empty($data['confirmPasswordError']) && empty($data['firstNameError']) && empty($data['lastNameError']) && empty($data['genderError']) && empty($data['phoneError']) && empty($data['profilePicError'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $db->query('INSERT INTO students (first_name, last_name, email, password, phone, gender, profile_pic) 
        VALUES (:first_name, :last_name, :email, :password, :phone, :gender, :profilePic)');

        // bind value
        $db->bind(':first_name', $data['firstName']);
        $db->bind(':last_name', $data['lastName']);
        $db->bind(':email', $data['email']);
        $db->bind(':password', $data['password']);
        $db->bind(':phone', $data['phone']);
        $db->bind(':gender', $data['gender']);
        $db->bind(':profilePic', $target_file);

        if ($db->execute()) {
          $_SESSION['user_id'] = $db->lastInsertId();
          $_SESSION['first_name'] = $data['firstName'];
          $_SESSION['last_name'] = $data['lastName'];
          $_SESSION['email'] = $data['email'];
          $_SESSION['gender'] = $data['gender'];
          $_SESSION['phone'] = $data['phone'];
          $_SESSION['dp'] = $target_file;

          header('location: ' . URLROOT . '/students/index.php');
        }
        else {
          $files = $_FILES;
          $error_msg = "Unable to register user. Try again.";
          header('location: ' . URLROOT . '/students/index.php');
        }
      }
    }
    else {
      $data['profilePicError'] = 'There was an error uploading the profile picture';
      header('location: ' . URLROOT . '/students/register.php');
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
  <title>CBT Portal | Register</title>

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
      #profileDisplay { 
        display: block; 
        height: 210px; 
        width: 30%; 
        margin: 0px auto; 
        border-radius: 50%; 
      }

      .img-placeholder {
        width: 30%;
        color: white;
        height: 100%;
        background: black;
        opacity: .7;
        height: 210px;
        border-radius: 50%;
        z-index: 2;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        display: none;
      }

      .img-placeholder h4 {
        margin-top: 40%;
        color: white;
      }
      .img-div:hover .img-placeholder {
        display: block;
        cursor: pointer;
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
            <h1>CBT Portal</h1>
            <?php if (isset($error_msg)) : ?>
              <p class="text-danger"><?php echo $error_msg ?></p>
            <?php endif; ?>
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
                <h3 class="card-title">Register</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
              <div class="card-body">
                  <div class="form-group">
                    <label>Profile Picture</label>
                    <input type="file" name="profilePic" id="profileImage" class="form-control" value="">
                  </div> 
                  <div class="form-group">
                    <label for="exampleInputEmail1">First name</label>
                    <input type="text" name="first_name" class="form-control" id="exampleInputEmail1" placeholder="Enter first name" required><span class="text-red">*<?php echo $data['firstNameError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Last name</label>
                    <input type="text" name="last_name" class="form-control" id="exampleInputEmail1" placeholder="Enter last name" required><span class="text-red">*<?php echo $data['lastNameError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" required><span class="text-red">*<?php echo $data['emailError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputEmail1" placeholder="Enter Password" required><span class="text-red">*<?php echo $data['passwordError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" id="exampleInputEmail1" placeholder="Enter email" required><span class="text-red">*<?php echo $data['confirmPasswordError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" id="exampleInputEmail1" placeholder="Enter phone number" required><span class="text-red">*<?php echo $data['phoneError']; ?></span>
                  </div>
                  <div class="form-group">
                        <label>Gender</label>
                        <select class="custom-select" name="gender" required>
                          <option value="M">Male</option>
                          <option value="F">Female</option>
                          <option value="Other">Other</option>
                        </select>
                  </div>

                </div>

                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-dark">Register</button>
                  <span class="float-right">
                    Already have an account? <a href="login.php">Login</a>
                  </span>
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
    <strong>Copyright &copy; 2021 <a href="<?php echo URLROOT . '/students' ?>">CBT Portal</a>.</strong> All rights reserved.
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
    <?php header('location: ' . URLROOT . '/students'); ?>
<?php endif; ?>