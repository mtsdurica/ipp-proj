<?php

declare(strict_types=1);

/**
 * @file parse.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

//TODO: error handling
include "visitor.php";
include "visitable.php";
include "parse_visitor.php";
//include "instr_no_arg.php";
include "instr_1_arg.php";
include "instr_2_arg.php";
//include "instr_3_arg.php";

ini_set("display_errors", "stderr");

function remove_comment($str)
{
    if (strpos($str, "#")) {
        return $str = strstr($str, "#", true);
    } else {
        return $str;
    }
}

$header_flag = false;
$instr_cnt = 1;
$parser = new Parse_visitor();
$dom = new DOMDocument("1.0", "UTF-8");
$dom->formatOutput = true;

while ($stream = fgets(STDIN)) {
    //Checking if header is present
    if (!$header_flag) {
        //Matching the header to a regexp, if not correct exits with code 21
        if (preg_match("/^(\.IPPcode2)[0-9]/", $stream)) {
            $xml = $dom->createElement("program");
            $split_str = explode(" ", trim($stream, "\n"));
            $xml->setAttribute("language", str_replace(".", "", $split_str[0]));
            $xml = $dom->appendChild($xml);
            $header_flag = true;
        } else {
            fwrite(STDERR, "Error: Missing or incorrect header!\n");
            exit(21);
        }
    }

    $split_str = explode(" ", trim($stream, "\n"));
    $instr = strtoupper($split_str[0]);

    switch ($instr) {
        case "DEFVAR":
            if (
                preg_match(
                    "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                    $split_str[1]
                )
            ) {
                $arg = $split_str[1];
                $arg_type = "var";
            }
            $obj = new Instr_1_arg(
                $instr,
                $instr_cnt,
                $arg_type,
                $arg,
                $dom,
                $xml
            );
            $parser->visit_1_arg($obj);
            $instr_cnt++;
            break;
        case "WRITE":
            //TODO: func
            switch ($split_str[1]) {
                case (bool) preg_match(
                    "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                    $split_str[1]
                ):
                    $arg = $split_str[1];
                    $arg = remove_comment($arg);
                    $arg_type = "var";
                    break;
                case (bool) preg_match("/^(string)@*/", $split_str[1]):
                    $arg = trim(
                        $split_str[1],
                        strstr($split_str[1], "@", true) . "@"
                    );
                    $arg = remove_comment($arg);
                    $arg_type = strstr($split_str[1], "@", true);
                    break;
                //TODO: support for octal and hexa format
                case (bool) preg_match("/^(int)@[0-9]*/", $split_str[1]):
                    $arg = trim(
                        $split_str[1],
                        strstr($split_str[1], "@", true) . "@"
                    );
                    $arg = remove_comment($arg);
                    $arg_type = strstr($split_str[1], "@", true);
                    break;
                case (bool) preg_match("/^(bool)@[true|false]/", $split_str[1]):
                    $arg = trim(
                        $split_str[1],
                        strstr($split_str[1], "@", true) . "@"
                    );
                    $arg = remove_comment($arg);
                    $arg_type = strstr($split_str[1], "@", true);
                    break;
                default:
                    //error handling
                    break;
            }
            $obj = new Instr_1_arg(
                $instr,
                $instr_cnt,
                $arg_type,
                $arg,
                $dom,
                $xml
            );
            $parser->visit_1_arg($obj);
            $instr_cnt++;
            break;
        case "READ":
            if (
                preg_match(
                    "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                    $split_str[1]
                )
            ) {
                $arg1 = $split_str[1];
                $arg1_type = "var";
            }
            if (preg_match("/^(int|string|bool)/", $split_str[2])) {
                $arg2 = trim(
                    $split_str[2],
                    strstr($split_str[2], "@", true) . "@"
                );
                $arg2 = remove_comment($arg2);
                $arg2_type = "type";
            }
            $obj = new Instr_2_arg(
                $instr,
                $instr_cnt,
                $arg1_type,
                $arg2_type,
                $arg1,
                $arg2,
                $dom,
                $xml
            );
            $parser->visit_2_arg($obj);
            $instr_cnt++;
            break;
        default:
            //error handling
            break;
    }
    $arg = null;
}

echo $dom->saveXML($dom, LIBXML_NOEMPTYTAG);
