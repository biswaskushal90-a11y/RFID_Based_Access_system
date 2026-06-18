<?php

file_put_contents(
    "current_uid.txt",
    $_GET['uid']
);

echo "UID Saved";

?>