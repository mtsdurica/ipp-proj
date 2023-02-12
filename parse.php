<?php

declare(strict_types=1);

/**
 * @file parse.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

include "visitor.php";
include "visitable.php";
include "parse_visitor.php";
include "instr_no_arg.php";
include "instr_1_arg.php";
include "instr_2_arg.php";
include "instr_3_arg.php";

ini_set("display_errors", "stderr");

$header_flag = false;
$instr_cnt = 1;
$parser = new Parse_visitor();
$dom = new DOMDocument("1.0", "UTF-8");
$dom->formatOutput = true;

while ($stream = fgets(STDIN)) {
    $stream = preg_replace("/\s+/", " ", $stream);
    $stream = remove_comment($stream);

    if (preg_match("/^\s*$/", $stream)) {
        continue;
    }
    //Checking if header is present
    if (!$header_flag) {
        //Matching the header to a regexp, if not correct exits with code 21
        if (preg_match("/^\s*\.IPPcode23/", $stream)) {
            $xml = $dom->createElement("program");
            $split_str = explode(" ", trim($stream, "\n"));
            if (!empty($split_str[0])) {
                if (!preg_match("/^\s*\.IPPcode23$/", $split_str[0])) {
                    exit(21);
                }
                $xml->setAttribute("language", str_replace(".", "", $split_str[0]));
            } else {
                if (!preg_match("/^\s*\.IPPcode23$/", $split_str[1])) {
                    exit(21);
                }
                $xml->setAttribute("language", str_replace(".", "", $split_str[1]));
            }
            $xml = $dom->appendChild($xml);
            $header_flag = true;
            continue;
        } elseif (preg_match("/^\#.*/", $stream)) {
            //Skipping comments before header
            continue;
        } else {
            fwrite(STDERR, "Error: Missing or incorrect header!\n");
            exit(21);
        }
    }
    fwrite(STDERR, "HFLAG " . (string) $header_flag . "\n");
    fwrite(STDERR, "STREAM: " . $stream . "\n");
    $split_str = explode(" ", trim($stream, "\n"));
    $instr = strtoupper($split_str[0]);
    if ($header_flag && !preg_match("/^\#.*/", $instr)) {
        fwrite(STDERR, "DEBUG: " . $instr . "\n");
        switch ($instr) {
            case "NOT":
            case "TYPE":
            case "INT2CHAR":
            case "STRLEN":
            case "MOVE":
                if (!empty($split_str[3])) {
                    exit(23);
                }
                list($arg1, $arg1_type) = check_var($split_str[1]);
                list($arg2, $arg2_type) = check_symb($split_str[2]);
                $instr_obj = new Instr_2_arg(
                    $instr,
                    $instr_cnt,
                    $arg1_type,
                    $arg2_type,
                    $arg1,
                    $arg2,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "CREATEFRAME":
            case "PUSHFRAME":
            case "POPFRAME":
            case "RETURN":
            case "BREAK":
                if (!empty($split_str[1])) {
                    exit(23);
                }
                $instr_obj = new Instr_no_arg($instr, $instr_cnt, $dom, $xml);
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "POPS":
            case "DEFVAR":
                if (!empty($split_str[2])) {
                    exit(23);
                }
                list($arg, $arg_type) = check_var($split_str[1]);
                $instr_obj = new Instr_1_arg(
                    $instr,
                    $instr_cnt,
                    $arg_type,
                    $arg,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "PUSHS":
            case "DPRINT":
            case "EXIT":
            case "WRITE":
                if (!empty($split_str[2])) {
                    exit(23);
                }
                list($arg, $arg_type) = check_symb($split_str[1]);
                $instr_obj = new Instr_1_arg(
                    $instr,
                    $instr_cnt,
                    $arg_type,
                    $arg,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "LABEL":
            case "JUMP":
            case "CALL":
                if (!empty($split_str[2])) {
                    exit(23);
                }
                list($arg, $arg_type) = check_label($split_str[1]);
                $instr_obj = new Instr_1_arg(
                    $instr,
                    $instr_cnt,
                    $arg_type,
                    $arg,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "JUMPIFEQ":
            case "JUMPIFNEQ":
                if (!empty($split_str[4])) {
                    exit(23);
                }
                list($arg1, $arg1_type) = check_label($split_str[1]);
                list($arg2, $arg2_type) = check_symb($split_str[2]);
                list($arg3, $arg3_type) = check_symb($split_str[3]);
                $instr_obj = new Instr_3_arg(
                    $instr,
                    $instr_cnt,
                    $arg1_type,
                    $arg2_type,
                    $arg3_type,
                    $arg1,
                    $arg2,
                    $arg3,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "READ":
                if (!empty($split_str[3])) {
                    exit(23);
                }
                list($arg1, $arg1_type) = check_var($split_str[1]);
                list($arg2, $arg2_type) = check_type($split_str[2]);
                $instr_obj = new Instr_2_arg(
                    $instr,
                    $instr_cnt,
                    $arg1_type,
                    $arg2_type,
                    $arg1,
                    $arg2,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            case "ADD":
            case "SUB":
            case "MUL":
            case "EQ":
            case "LT":
            case "GT":
            case "AND":
            case "STRI2INT":
            case "CONCAT":
            case "GETCHAR":
            case "SETCHAR":
            case "OR":
            case "IDIV":
                if (!empty($split_str[4])) {
                    exit(23);
                }
                list($arg1, $arg1_type) = check_var($split_str[1]);
                list($arg2, $arg2_type) = check_symb($split_str[2]);
                list($arg3, $arg3_type) = check_symb($split_str[3]);
                $instr_obj = new Instr_3_arg(
                    $instr,
                    $instr_cnt,
                    $arg1_type,
                    $arg2_type,
                    $arg3_type,
                    $arg1,
                    $arg2,
                    $arg3,
                    $dom,
                    $xml
                );
                $instr_obj->parse($parser);
                $instr_cnt++;
                break;
            default:
                exit(22);
                break;
        }
        $arg = "";
    }
}
echo $dom->saveXML($dom, LIBXML_NOEMPTYTAG);

/**
 * Functions checks for '#' in a string and if it finds one, it returns everything before the '#', if not it returns the inputted string
 * @param $str String to be checked
 */
function remove_comment($str): string
{
    if (strpos($str, "#")) {
        return $str = strstr($str, "#", true);
    } else {
        return $str;
    }
}

/**
 * Checks if the inputted argument is in correct format
 * @param $arg Argument to be checked
 */
function check_var($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    if (
        preg_match(
            "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
            $arg
        )
    ) {
        $arg_type = "var";
        return array($arg, $arg_type);
    } else {
        exit(23);
    }
}

/**
 * Checks if the inputted argument is in correct format
 * @param $arg Argument to be checked
 */
function check_label($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    if (
        preg_match(
            "/^[a-zA-Z\-_\$&%\*!\?][a-zA-Z\-_\$&%\*!\?]*$/",
            $arg
        )
    ) {
        $arg_type = "label";
        return array($arg, $arg_type);
    } else {
        exit(23);
    }
}

/**
 * Checks if the inputted argument is in correct format
 * @param $arg Argument to be checked
 */
function check_symb($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    switch ($arg) {
        case (bool) preg_match(
            "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
            $arg
        ):
            $arg_type = "var";
            break;
        case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $arg):
            $arg_type = strstr($arg, "@", true);
            $arg = trim(
                $arg,
                strstr($arg, "@", true) . "@"
            );
            break;
        case (bool) preg_match("/^(int)@[0-9]*/", $arg):
            $arg_type = strstr($arg, "@", true);
            $arg = trim(
                $arg,
                strstr($arg, "@", true) . "@"
            );
            if (!strcmp($arg, "")) {
                exit(23);
            }
            break;
        case (bool) preg_match(
            "/^bool@(true|false)$/",
            $arg
        ):
            $arg_type = strstr($arg, "@", true);
            $arg = trim(
                $arg,
                strstr($arg, "@", true) . "@"
            );
            if (!strcmp($arg, "")) {
                exit(23);
            }
            break;
        case (bool) preg_match("/^nil@nil$/", $arg):
            $arg_type = strstr($arg, "@", true);
            $arg = substr($arg, 4);
            break;
        default:
            //error handling
            exit(23);
            break;
    }
    return array($arg, $arg_type);
}

/**
 * Checks if the inputted argument is in correct format
 * @param $arg Argument to be checked
 */
function check_type($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    if (preg_match("/^(int|string|bool)$/", $arg)) {
        $arg = trim(
            $arg,
            strstr($arg, "@", true) . "@"
        );
        $arg_type = "type";
        return array($arg, $arg_type);
    } else {
        exit(23);
    }
}
