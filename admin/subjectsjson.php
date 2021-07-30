<?php

$db = new Database;
$db->query("SELECT * FROM subjects");
$data = $db->resultSet();
if($db->rowCount() <= 0) {
    $data = [];
}

$subjects = array();

foreach ($data as $key => $val) {
    array_push($subjects, $val);
}

$dataJSON = json_encode($subjects);

echo $dataJSON;

?>