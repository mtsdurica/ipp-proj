<?php

/**
 * @file functions.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

/**
 * Main parsing function
 *
 * @param mixed $file Source to parse from
 */
function parse_src_file($file): void
{
    $header_flag = false;
    $instr_cnt = 1;
    $parser = new Parse_visitor();
    $dom = new DOMDocument("1.0", "UTF-8");
    $dom->formatOutput = true;
    $xml = $dom->createElement("program");
    //Main parsing while cycle
    while ($stream = fgets($file)) {
        //Replacing multiple whitespaces with one whitespace and removing comments
        $stream = format_stream($stream);
        //Skipping blank lines
        if (preg_match("/^\s*$/", $stream)) {
            continue;
        }
        //Checking if header is present
        if (!$header_flag) {
            //Matching the header to a regexp, if not matched, exits with code 21
            if (preg_match("/^\.IPPcode23$/", $stream)) {
                $xml->setAttribute(
                    "language",
                    str_replace(".", "", $stream)
                );
                $xml = $dom->appendChild($xml);
                $header_flag = true;
                continue;
            } else {
                fwrite(STDERR, "Error: Missing or incorrect header!\n");
                exit(21);
            }
        } else if ($header_flag) {
            $split_str = explode(" ", $stream);
            $instr = strtoupper($split_str[0]);
            //Parsing switch statement, unknown instruction leads to exiting with code 22
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
                        $dom,
                        $xml,
                        $arg1,
                        $arg2,
                        $arg1_type,
                        $arg2_type
                    );
                    break;
                case "CREATEFRAME":
                case "PUSHFRAME":
                case "POPFRAME":
                case "RETURN":
                case "BREAK":
                    if (!empty($split_str[1])) {
                        exit(23);
                    }
                    $instr_obj = new Instr_no_arg(
                        $instr,
                        $instr_cnt,
                        $dom,
                        $xml
                    );
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
                        $dom,
                        $xml,
                        $arg,
                        $arg_type
                    );
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
                        $dom,
                        $xml,
                        $arg,
                        $arg_type,
                    );
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
                        $dom,
                        $xml,
                        $arg,
                        $arg_type
                    );
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
                        $dom,
                        $xml,
                        $arg1,
                        $arg2,
                        $arg3,
                        $arg1_type,
                        $arg2_type,
                        $arg3_type,
                    );
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
                        $dom,
                        $xml,
                        $arg1,
                        $arg2,
                        $arg1_type,
                        $arg2_type
                    );
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
                        $dom,
                        $xml,
                        $arg1,
                        $arg2,
                        $arg3,
                        $arg1_type,
                        $arg2_type,
                        $arg3_type
                    );
                    break;
                default:
                    fwrite(STDERR, "Error: Unknown instruction!\n");
                    exit(22);
                    break;
            }
            $instr_obj->parse($parser);
            $instr_cnt++;
        }
    }
    echo $dom->saveXML($dom, LIBXML_NOEMPTYTAG);
}

/**
 * Function trims and removes comments from string
 *
 * @param string $str String to be checked
 * @return string Returns formatted string
 */
function format_stream($str): string
{
    $str = preg_replace("/\s+/", " ", $str);
    $str = remove_comment($str);
    return trim($str);
}

/**
 * Function removes comments from string 
 *
 * @param string $str String to be checked
 * @return string Returns everything before the '#', or returns the inputted string if no '#' was found
 */
function remove_comment($str): string
{
    if (str_contains($str, "#")) {
        return strstr($str, "#", true);
    } else {
        return $str;
    }
}

/**
 * Checks if the inputted argument is in correct format
 *
 * @param string $arg Argument to be checked
 * @return array Argument identificator and argument type
 */
function check_var($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    if (
        preg_match(
            "/^(LF|TF|GF)@[a-zA-Z\-_\$&%\*!\?][a-zA-Z\-_\$&%\*!\?\d]*$/",
            $arg
        )
    ) {
        $arg_type = "var";
        return [$arg, $arg_type];
    } else {
        exit(23);
    }
}

/**
 * Checks if the inputted argument is in correct format
 *
 * @param string $arg Argument to be checked
 * @return array Argument identificator and argument type
 */
function check_label($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    if (preg_match("/^[a-zA-Z\-_\$&%\*!\?][a-zA-Z\-_\$&%\*!\?\d]*$/", $arg)) {
        $arg_type = "label";
        return [$arg, $arg_type];
    } else {
        exit(23);
    }
}

/**
 * Checks if the inputted argument is in correct format
 *
 * @param string $arg Argument to be checked
 * @return array Argument identificator and argument type
 */
function check_symb($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    switch ($arg) {
        case (bool) preg_match(
            "/^(LF|TF|GF)@[a-zA-Z\-_\$&%\*!\?][a-zA-Z\-_\$&%\*!\?\d]*$/",
            $arg
        ):
            $arg_type = "var";
            break;
        case (bool) preg_match(
            /*
            Matching everything except '\' because '#' is already taken care of and whitespaces are exploded,
            therefore taken as arguments of the instruction,which would lead to error in instructions,
            where it would be considered as too many arguments
            */
            "/^string@((?!\\\).|(\\\[\d]{3}))*$/",
            $arg
        ):
            $arg_type = strstr($arg, "@", true);
            $arg = trim($arg, strstr($arg, "@", true) . "@");
            break;
        case (bool) preg_match(
            "/^int@(\+|-)?(\d+|0o[0-7][0-7_]+|0x[a-fA-F\d][a-fA-F_\d]+)$/",
            $arg
        ):
            $arg_type = strstr($arg, "@", true);
            $arg = trim($arg, strstr($arg, "@", true) . "@");
            break;
        case (bool) preg_match("/^bool@(true|false)$/", $arg):
            $arg_type = strstr($arg, "@", true);
            $arg = trim($arg, strstr($arg, "@", true) . "@");
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
    return [$arg, $arg_type];
}

/**
 * Checks if the inputted argument is in correct format
 *
 * @param string $arg Argument to be checked
 * @return array Argument identificator and argument type
 */
function check_type($arg): array
{
    if (empty($arg)) {
        exit(23);
    }
    if (preg_match("/^(int|string|bool)$/", $arg)) {
        $arg_type = "type";
        return [$arg, $arg_type];
    } else {
        exit(23);
    }
}

/**
 * Prints help to STDOUT
 */
function print_help(): void
{
    echo "Made by Matúš Ďurica (xduric06) VUT FIT v Brně 2023\n";
    echo "\n";
    echo "\033[1mNAME\033[0m\n";
    echo "\tparse.php\t - Provides syntax and lexical analysis for IPPcode23 language inputted on STDIN and outputs results in XML to STDOUT\n";
    echo "\n";
    echo "\033[1mSYNOPSIS\033[0m\n";
    echo "\t\033[1mphp8.1\033[0m parse.php [--help]\n";
}
