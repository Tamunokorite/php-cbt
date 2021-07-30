<?php 

require_once '../require.php';

$data = [];

$db = new Database;
if ($_SERVER['REQUEST_METHOD' == 'GET']) {
    $db->query('SELECT * FROM aubjects WHERE student=:student and subject=:subject');
    $db->bind(':subject', $_GET['id']);
    $db->bind(':student', $_SESSION['user_id']);

    $subject = $db->single();

    if ($subject) {
        $data['time'] = $subject->test_time;
    }
    else {
        $data['time'] = 0;
    }
}

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $db->query('UPDATE  test_times SET student=:student, subject=:subject, time_left=:time');

//     $db->bind(':student', $data['student']);
//     $db->bind(':subject', $data['subject']);
//     $db->bind(':time', $data['time']);

//     $test = $db->single();

//     if ($test) {
//         $data['success'] = 'Successful.';
//     }
//     else {
//         $data['error'] = 'Error';
//     }
// }


echo json_encode($data);

?>