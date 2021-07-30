<?php require_once '../require.php' ?>

<?php if(!isLoggedIn()) : ?>
    <?php header('location: ' . URLROOT . '/students/login.php') ?>

<?php else : ?>

<?php

$db = new Database;

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

function testStarted($student, $subject) {
  $db = new Database;
  $db->query('SELECT * FROM  test_times WHERE student=:student AND subject=:subject');

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

function getEndTime($student, $subject) {
  $db = new Database;
  $db->query('SELECT * FROM  test_times WHERE student=:student AND subject=:subject');

  $db->bind(':student', $student);
  $db->bind(':subject', $subject);

  $result = $db->single();

  if ($result) {
    return $result->end_time;
  }
  else {
    return false;
  }
}

$db->query('SELECT * FROM subjects WHERE id=:id');

$db->bind(':id', $_GET['id']);

$_SESSION['subject_id'] = $_GET['id'];

$result = $db->single();

if ($result) {
    $subject = $result;
    if (!takenTest($_SESSION['user_id'], $_GET['id']) && !testStarted($_SESSION['user_id'], $_GET['id'])) {
      $start_time = new DateTime();
      $minutes_to_add = $subject->test_time;
      $end_time = new DateTime();
      $end_time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
      $start_time = $start_time->format('Y-m-d H:i:s');
      $end_time = $end_time->format('Y-m-d H:i:s');
      $db->query('INSERT INTO test_times (student, subject, start_time, end_time) VALUES (:student, :subject, :start_time, :end_time)');

      $db->bind(':student', $_SESSION['user_id']);
      $db->bind(':subject', $_GET['id']);
      $db->bind(':start_time', $start_time);
      $db->bind(':end_time', $end_time);

      if ($db->execute()) {
        if ($subject->test_ready == 1) {
          $data = [
            'questions' => [],
          ];
          $db->query('SELECT * FROM questions WHERE subject=:subject');
          $db->bind(':subject', $subject->id);
    
          $data['questions'] = $db->resultSet();
          shuffle($data['questions']);
          $data['questions'] = array_slice($data['questions'], 0, $subject->test_questions);
        }
        else {
          die('You cannot take this test now. <a href=\'courses.php\'>Go back</a>');
        }
      }
      else {
        die("Error getting test1. <a href='" . 'courses.php' . "'>Go back.</a>");
      }
    }
    else {
      if ($subject->test_ready == 1) {
        $data = [
          'questions' => [],
        ];
        $db->query('SELECT * FROM questions WHERE subject=:subject');
        $db->bind(':subject', $subject->id);
  
        $data['questions'] = $db->resultSet();
        shuffle($data['questions']);
        $data['questions'] = array_slice($data['questions'], 0, $subject->test_questions);
      }
    }
}
else {
    die("Error getting test3. <a href='" . 'courses.php' . "'>Go back.</a>");
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
      .test {
        display: none;
      }
      .activequestion {
        display: block;
      }
  </style>
</head>
<body class="">
<div class="wrapper">


    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="offset-md-2"></div>
          <div class="col-sm-6">
            <h1><?= $subject->title ?> Test</h1>
          </div>
          <div class="col-sm-2">
          <span id="count" class="btn btn-outline-dark disabled"></span>
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
                <form action="scoretest.php" id="quickForm">
                    <?php foreach($data['questions'] as $key => $question) :?>
                        <div class="card card-dark test" id="qid_<?= $question->id ?>">
                            <div class="card-header">
                                <h2 class="card-title">Question <?= $key+1 ?></h2>
                            </div>
                            <div class="card-body">
                                <p><?= $question->content ?></p>
                                <?php 
                                    $db->query("SELECT * FROM choices WHERE question=:question");
                                    $db->bind(':question', $question->id);
                                    $choices = $db->resultSet();
                                    $letters = ['A', 'B', 'C', 'D'];
                                ?>
                                <input type="hidden" id="qid" value="<?= $question->id; ?>">
                                <input type="radio" name="<?= $question->id ?>" id="q<?= $question->id ?>op1" value="<?= $choices[0]->id ?>"><span><?= " " . $choices[0]->content ?></span><br>
                                <input type="radio" name="<?= $question->id ?>" id="q<?= $question->id ?>op2" value="<?= $choices[1]->id ?>"><span><?= " " . $choices[1]->content ?></span><br>
                                <input type="radio" name="<?= $question->id ?>" id="q<?= $question->id ?>op3" value="<?= $choices[2]->id ?>"><span><?= " " . $choices[2]->content ?></span><br>
                                <input type="radio" name="<?= $question->id ?>" id="q<?= $question->id ?>op4" value="<?= $choices[3]->id ?>"><span><?= " " . $choices[3]->content ?></span><br>
                            </div>
                            <div class="card-footer">
                            <?php if($key != 0): ?><button class="btn btn-dark float-left prev_q">Prev</button><?php endif; ?>
                            <?php if($key != count($data['questions'])-1) : ?><button class="btn btn-dark float-right next_q">Next</button><?php endif; ?>
                            <?php if($key == count($data['questions'])-1) : ?><button type="submit" class="btn btn-dark float-right submit">Submit</button><?php endif;?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
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

    <?php
    
      $time = getEndTime($_SESSION['user_id'], $_GET['id']);

    ?>

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

<script>
jQuery(document).ready(function(){
jQuery('div.test:first').addClass('activequestion');



var time = "<?php echo $time ?>";
// console.log(time);
time = new Date(time).getTime();

var x = setInterval(function() {

// Get today's date and time
var now = new Date().getTime();

// Find the distance between now and the count down date
var distance = time - now;


// Time calculations for days, hours, minutes and seconds
// var days = Math.floor(distance / (1000 * 60 * 60 * 24));
// var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
var seconds = Math.floor((distance % (1000 * 60)) / 1000);

// console.log(minutes);
// console.log(seconds);

// Display the result in the element with id="demo"
document.getElementById("count").innerHTML = "Time left: " + minutes + "m " + seconds + "s ";

console.log(distance);

// If the count down is finished, write some text
if (seconds <= 0) {
  clearInterval(x);
  $('.submit').trigger('click');
  $('.submit').trigger('click');
  // document.getElementById("").innerHTML = "EXPIRED";
}
}, 1000);



// time = new Date(time).getTime();
// console.log(parseIt(time));
// var total_seconds = 40*60;
// var c_minutes = parseInt(total_seconds/60);
// var c_seconds = parseInt(total_seconds%60);

// function checkTime(){
// document.getElementById("count").innerHTML = 'Time Left: ' + c_minutes + ' : ' + c_seconds;

// if(total_seconds <= 0 ){
//     $(document).ready(function(){$(".submit").click();});
//     clearInterval(myInterval);
// }else{
//     total_seconds = total_seconds - 1;
//     c_minutes = parseInt(total_seconds/60);
//     c_seconds = parseInt(total_seconds%60);
// }
// }
// var myInterval = setInterval(checkTime, 1000);

jQuery('.next_q').click(function(event){
    event.preventDefault();
    var nonext=jQuery('.test:last').hasClass('activequestion');
    var qid = $("#qid").val(); /* GET THE question id */
    var selected = $("input[type='radio'][name='" + qid + "']:checked");
    // console.log(selected[0]['id']);
    if (selected.length > 0) { /* CHECK THE SELECTED RADIO BUTTON */
        answer = selected.val();
    }
    else {
      answer = 0;
    }
    // $.ajax({
    //     type: "POST", /* THE METHOD WE WILL BE USING TO PASS THE DATA */
    //     url: "scoretest.php", /* THIS IS WHERE THE DATA WILL GO */
    //     data: {"questionid" : qid, "answer" : answer, "submit": false}, /* THE DATA WE WILL BE PASSING */
    //     dataType : 'json',
    //     success: function(result){ /* WHEN IT IS SUCCESSFUL */
    //       /* THIS WILL REPLACE THE DATA IN OUR QUESTION PAGE */
    //     //   $("#qid").val(result.questionid);
    //     //   $("#question").html(result.question);
    //     //   $("#op1").val(result.op1);
    //     //   $("#op2").val(result.op2);
    //     //   $("#op3").val(result.op3);
    //     //   $("#op4").val(result.op4);
    //     //   $("#op1text").html(result.op1);
    //     //   $("#op2text").html(result.op2);
    //     //   $("#op3text").html(result.op3);
    //     //   $("#op4text").html(result.op4);
    //     // $("input[type='radio'][value=" + data['answer'] + "]").attr("checked", "checked");
    //     }
    //   }); /* END OF AJAX */
      
      if(nonext) { alert("no next available"); return false; }
      var currentdiv=jQuery('.activequestion').attr('id');
      jQuery('.test.activequestion').next().addClass('activequestion');
      jQuery('#'+currentdiv).removeClass('activequestion');
    });
    function getURLParameter(name) {
  return decodeURI(
   (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
   );
}

    jQuery('.submit').click(function(event){
    event.preventDefault();
    var nonext=jQuery('.test:last').hasClass('activequestion');
    var $inputs = $('#myForm :input');
    var qid = $("#qid").val(); /* GET THE question id */
    var selected = $("input[type='radio'][name='" + qid + "']:checked");
    // console.log(selected.val());
    var values = {};
    $.each($('#quickForm').serializeArray(), function(i, field) {
        values[field.name] = field.value;
    });
    values['subject'] = getURLParameter('id');
    //console.log(values);
    if (selected.length > 0) { /* CHECK THE SELECTED RADIO BUTTON */
        answer = selected.val();
    }
    $.ajax({
      type: "POST",
      url: "scoretest.php",
      data: values,
      dataType: "json",
      encode: true,
    }).done(function (data) {
        // console.log(data);
        $(location).attr("href", "courses.php");
    });

    //$('#quickForm').submit();
      }); /* END OF AJAX */

    //   if(nonext)
    //     { alert("no next available"); return false; }
    //     var currentdiv=jQuery('.activequestion').attr('id');
    //     jQuery('.test.activequestion').next().addClass('activequestion');
    //     jQuery('#'+currentdiv).removeClass('activequestion');
    //     }
    // });


jQuery('.prev_q').click(function(event){
    event.preventDefault();
    var noprevious=jQuery('.test:first').hasClass('activequestion');
    if(noprevious)
    { alert("no previous available"); return false; }
    //$data = $.get('scoretest.php');
    //console.log($data);
    // $("input[type='radio'][name=$data[]");
    var currentdiv=jQuery('.activequestion').attr('id');
    jQuery('.test.activequestion').prev().addClass('activequestion');
    jQuery('#'+currentdiv).removeClass('activequestion');
    });
});
</script>
</body>
</html>
<?php endif; ?>
