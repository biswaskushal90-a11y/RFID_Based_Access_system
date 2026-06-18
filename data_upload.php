<?php

require_once("dbcon.php");

if (!isset($_POST['uid'])) {
    exit("NO_UID");
}

$uid = strtoupper(trim($_POST['uid']));

$status = "ACCESS_DENIED";
$name   = "Unknown";

$sql = "SELECT * FROM authorized_cards WHERE uid='$uid'";
$result = $conn->query($sql);

if (!$result) {
    die("SELECT ERROR: " . $conn->error);
}

if ($result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    $status = "ACCESS_GRANTED";
    $name   = $row['name'];
}

$insert = "
INSERT INTO access_log
(uid,name,status)
VALUES
('$uid','$name','$status')
";

if (!$conn->query($insert)) {
    die("INSERT ERROR: " . $conn->error);
}

echo $status;

$conn->close();

?>