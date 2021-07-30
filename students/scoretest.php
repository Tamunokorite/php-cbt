<?php 

require_once '../require.php';

$errors = [];
$data = [];

if (empty($_POST)) {
    $errors['answersError'] = 'No questions answered';
}

if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // $data['success'] = true;
    // $data['message'] = 'Success!';
    $db = new Database;
    $score = 0;
    $count = 0;
    // foreach($_POST as $key => $val); {
    //     // $db->query("SELECT * FROM choices WHERE id=:id");
    //     // $db->bind("id", );
    //     //array_push($data, $key);
    //     $data[$key] = $val;
    // }
    // $db->query("SELECT * FROM questions WHERE subject=:subject");
    // $db->bind(":subject", $_POST['subject']);

    // $questions = $db->resultSet();
    // foreach($questions as $key => $question) {
    //     $db->query('SELECT * FROM choices WHERE id=:id');
    //     $db->bind(':id', $);
        
    //     $result = $db->single();

    //     if ($result) {
    //         $score++;
    //     }
    // }
    $db->query('SELECT * FROM test_scores WHERE student=:student AND subject=:subject');
    $db->bind(':student', $_SESSION['user_id']);
    $db->bind(':subject', $_POST['subject']);

    $result = $db->single();

    if (!$result) {
        foreach ($_POST as $key => $val) {
            if ($key == 'subject') {
                continue;
            }
            if ($val == 0) {
                continue;
            }
            $db->query('SELECT * FROM choices WHERE id=:id');
            $db->bind(':id', $val);

            $result = $db->single();

            if ($result && $result->isCorrect == 1) {
                $count++;
            }
        }

        $db->query('SELECT * FROM subjects WHERE id=:subject');
        $db->bind(':subject', $_POST['subject']);

        $result = $db->single();

        if ($result) {
            $score = number_format(($count/$result->test_questions) * 100, 2);
            if ($score >= 70) {
                $grade = 'A';
            } elseif ($score >= 60) {
                $grade = 'B';
            } elseif ($score >= 50) {
                $grade = 'C';
            } elseif ($score >= 45) {
                $grade = 'D';
            }
            elseif ($score >= 40) {
                $grade = 'E';
            } else {
                $grade = 'F';
            }
            $db->query('INSERT INTO test_scores (student, subject, score, grade) VALUES (:student, :subject, :score, :grade)');
            $db->bind(':student', $_SESSION['user_id']);
            $db->bind(':subject', $_SESSION['subject_id']);
            $db->bind(':score', $score);
            $db->bind(':grade', $grade);

            if ($db->execute()) {
                $test_count = $result->tests_count + 1;
                $db->query('UPDATE subjects SET tests_count=:tests_count WHERE id=:id');
                $db->bind(':tests_count', $test_count);
                $db->bind(':id', $_POST['subject']);
                if ($db->execute()) {
                    $data['success'] = "successful";
                }
                else {
                    $data['error'] = 'error';
                }
            }
            else {
                $data['error'] = 'error';
            }
        }
        else {
            $data['error'] = 'error';
        }

        // $data['score'] = $score;
        // $data['subject'] = $_POST['subject'];
    }
    else {
        $data['error'] = 'student has already taken test';
    }
}

echo json_encode($data);

?>