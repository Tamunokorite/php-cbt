<?php require_once '../require.php' ?>

<?php if(!isLoggedin()) :?>
    <?php header('location: ' . URLROOT . 'admin/login.php'); ?>
<?php else: ?>

<?php 

$data = [
    'questionError' => '',
    'choiceAError' => '',
    'choiceBError' => '',
    'choiceCError' => '',
    'choiceDError' => '',
    'correctChoiceError' => '',
];

$db = new Database;

$question_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['question_id'];

$db->query('SELECT * FROM questions WHERE id=:id');
$db->bind(':id', $question_id);

$question = $db->single();

$db->query('SELECT * FROM choices WHERE question=:id');
$db->bind(':id', $question_id);

$choices = $db->resultSet();

$db->query('SELECT * FROM choices WHERE question=:id AND isCorrect=1');
$db->bind(':id', $question_id);

$correct_choice = $db->single();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    $_SESSION['question_id'] = $_POST['question_id'];
    $_SESSION['subject'] = $_POST['subject'];

    $data = [
        'question' => trim($_POST['question']),
        'choiceA' => trim($_POST['choiceA']),
        'choiceB' => trim($_POST['choiceB']),
        'choiceC' => trim($_POST['choiceC']),
        'choiceD' => trim($_POST['choiceD']),
        'correctChoice' => trim($_POST['correctChoice']),
        'questionError' => '',
        'choiceAError' => '',
        'choiceBError' => '',
        'choiceCError' => '',
        'choiceDError' => '',
        'correctChoiceError' => '',
    ];


    if (empty($data['question'])) {
        $data['questionError'] = 'Please enter question';
    }

    if (empty($data['choiceA'])) {
        $data['choiceAError'] = 'Please enter Choice A';
    }

    if (empty($data['choiceB'])) {
        $data['choiceBError'] = 'Please enter Choice B';
    }

    if (empty($data['choiceC'])) {
        $data['choiceCError'] = 'Please enter Choice C';
    }

    if (empty($data['choiceD'])) {
        $data['choiceDError'] = 'Please enter Choice D';
    }

    if (empty($data['correctChoice'])) {
        $data['correctChoiceError'] = 'Please enter Correct Choice';
    }

    // check if errors are empty
    if (empty($data['questionError']) && empty($data['choiceAError']) && 
        empty($data['choiceBError']) && empty($data['choiceCError']) && 
        empty($data['choiceDError']) && empty($data['correctChoiceError'])) {
        $db->query('UPDATE questions SET content = :content WHERE id=:question');

        // bind values
        $db->bind(':question', $question_id);
        $db->bind(':content', $data['question']);

        // execute function
        if ($db->execute()) {
            // $id = $db->lastInsertId();

            $db->query('UPDATE choices SET content = :content, isCorrect = :is_correct WHERE id=:id');

            $db->bind(':id', $choices[0]->id);
            $db->bind(':content', $data["choiceA"]);
            $is_correct = $data['correctChoice'] == 'A' ? 1 : 0;
            $db->bind(':is_correct', $is_correct);

            $db->execute();

            $db->bind(':id', $choices[1]->id);
            $db->bind(':content', $data["choiceB"]);
            $is_correct = $data['correctChoice'] == 'B' ? 1 : 0;
            $db->bind(':is_correct', $is_correct);
            
            $db->execute();

            $db->bind(':id', $choices[2]->id);
            $db->bind(':content', $data["choiceC"]);
            $is_correct = $data['correctChoice'] == 'C' ? 1 : 0;
            $db->bind(':is_correct', $is_correct);
            
            $db->execute();

            $db->bind(':content', $data["choiceD"]);
            $db->bind(':id', $choices[3]->id);
            $is_correct = $data['correctChoice'] == 'D' ? 1 : 0;
            $db->bind(':is_correct', $is_correct);
            
            $db->execute();

            $success = 'Question edited successfully!';

            header('location: ' . URLROOT . '/admin/viewquestions.php?id=' . $_SESSION['subject']);
        } else {
            die("Couldn't add question. <a href='" . URLROOT . "/admin/viewquestions.php'>Go back</a>");
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
  <title>Add Questions</title>

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
                <h3 class="card-title">Edit Question</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form id="quickForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="card-body">
                <div class="form-group">
                      <label for="question">Subject</label>
                      <input type="text" name="subject" class="form-control" id="question" placeholder="" value="<?php echo $question->subject; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="question">Question</label>
                      <input type="text" name="question_id" class="form-control" id="question" placeholder="" value="<?php echo $question->id; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="question">Text</label>
                    <input type="text" name="question" class="form-control" id="question" placeholder="Enter Question" value="<?php echo $question->content; ?>"><span class="text-red">*<?php echo $data['questionError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="choiceA">Choice A</label>
                    <input type="text" name="choiceA" class="form-control" id="choiceA" placeholder="Enter Choice A" value="<?php echo $choices[0]->content; ?>"><span class="text-red">*<?php echo $data['choiceAError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="choiceB">Choice B</label>
                    <input type="text" name="choiceB" class="form-control" id="choiceB" placeholder="Enter Choice B" value="<?php echo $choices[1]->content; ?>"><span class="text-red">*<?php echo $data['choiceBError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="choiceC">Choice C</label>
                    <input type="text" name="choiceC" class="form-control" id="choiceC" placeholder="Enter Choice C" value="<?php echo $choices[2]->content; ?>"><span class="text-red">*<?php echo $data['choiceCError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="choiceD">Choice D</label>
                    <input type="text" name="choiceD" class="form-control" id="choiceD" placeholder="Enter Choice D" value="<?php echo $choices[3]->content; ?>"><span class="text-red">*<?php echo $data['choiceDError']; ?></span>
                  </div>
                  <div class="form-group">
                    <label for="choiceD">Correct Choice</label>
                    <input type="text" name="correctChoice" class="form-control" id="correctChoice" placeholder="Enter correct choice" value="<?php  $options = ['A', 'B', 'C', 'D']; foreach($choices as $key => $val) { if ($val->isCorrect == 1) {echo $options[$key]; break;}}   ?>"><span class="text-red">*<?php echo $data['correctChoiceError']; ?></span>
                  </div>
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