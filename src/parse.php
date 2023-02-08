<?php
declare(strict_types=1);

ini_set("display_errors", "stderr");

$header_flag = false;

while ($stream = fgets(STDIN)) {
    if (!$header_flag && ($stream = ".IPPcode23")) {
        $header_flag = true;
    }

    echo $stream;
}
?>
