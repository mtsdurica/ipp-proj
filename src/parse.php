<?php

declare(strict_types=1);

include "parse_visitor.php";
include "instr_1_arg.php";

ini_set("display_errors", "stderr");

$header_flag = false;

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

$instr_cnt = 1;

$parser = new Parse_visitor();

while ($stream = fgets(STDIN)) {
    //checking if header is present
    //TODO: error handling
    if (!$header_flag && ($stream = ".IPPcode23")) {
        echo "<program language=\"" . str_replace(".", "", $stream) . "\">\n";
        $header_flag = true;
    } else {
        //error handling
    }

    $split_str = explode(" ", trim($stream, "\n"));
    $instr = strtoupper($split_str[0]);

    //echo "debug " . $instr . "\n";
    //echo "debug " . $instr_cnt . "\n";

    switch ($instr) {
        case "DEFVAR":
            //case "WRITE":
            if (
                preg_match(
                    "/(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                    $split_str[1]
                )
            ) {
                $arg = $split_str[1];
            }
            $obj = new Instr_1_arg($instr, $instr_cnt, $arg);
            $parser->visit_1_arg($obj);
            $instr_cnt++;
            break;
    }
    $arg = null;
}

echo "</program>";
