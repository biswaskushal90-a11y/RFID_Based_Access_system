<?php

require_once("dbcon.php");

$uid = trim($_GET['uid']);

$stmt = $conn->prepare(
"SELECT name FROM authorized_cards WHERE uid=?"
);

$stmt->bind_param("s",$uid);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    echo json_encode([
        "exists" => true,
        "name" => $row['name']
    ]);
}
else
{
    echo json_encode([
        "exists" => false
    ]);
}