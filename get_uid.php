<?php

if(file_exists("current_uid.txt"))
{
    echo trim(
        file_get_contents(
            "current_uid.txt"
        )
    );
}

?>