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
include "instr_no_arg.php";
include "instr_1_arg.php";
include "instr_2_arg.php";
include "instr_3_arg.php";

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
                //echo "prd";
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
    //TODO: SKIP PRAZDNYCH RIADKOV A KOMENTAROV
    fwrite(STDERR, "HFLAG " . (string) $header_flag . "\n");
    fwrite(STDERR, "STREAM: " . $stream . "\n");
    $split_str = explode(" ", trim($stream, "\n"));
    $instr = strtoupper($split_str[0]);
    if ($header_flag && strcmp($instr, "") && !preg_match("/^\#.*/", $instr)) {
        fwrite(STDERR, "DEBUG: " . $instr . "\n");
        switch ($instr) {
            case "NOT":
            case "TYPE":
            case "INT2CHAR":
            case "STRLEN":
            case "MOVE":
                if (!empty($split_str[3]) || empty($split_str[2])) {
                    exit(23);
                }
                if (
                    preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[1]
                    )
                ) {
                    $arg1 = $split_str[1];
                    $arg1_type = "var";
                } else {
                    exit(23);
                }
                switch ($split_str[2]) {
                    case (bool) preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[2]
                    ):
                        $arg2 = $split_str[2];
                        if (
                            !strcmp($arg2, "") ||
                            trim(
                                $split_str[2],
                                strstr($split_str[2], "@", true) . "@"
                            )
                        ) {
                            //exit(23);
                        }
                        $arg2_type = "var";
                        break;
                    case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $split_str[2]):
                        $arg2 = trim(
                            $split_str[2],
                            strstr($split_str[2], "@", true) . "@"
                        );
                        if (!strcmp($arg2, "")) {
                            //exit(23);
                        }
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                        //TODO: support for octal and hexa format
                    case (bool) preg_match("/^(int)@[0-9]*/", $split_str[2]):
                        $arg2 = trim(
                            $split_str[2],
                            strstr($split_str[2], "@", true) . "@"
                        );
                        if (!strcmp($arg2, "")) {
                            exit(23);
                        }
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                    case (bool) preg_match(
                        "/^bool@(true|false)$/",
                        $split_str[2]
                    ):
                        $arg2 = trim(
                            $split_str[2],
                            strstr($split_str[2], "@", true) . "@"
                        );
                        if (!strcmp($arg2, "")) {
                            exit(23);
                        }
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                    case (bool) preg_match("/^nil@nil$/", $split_str[2]):
                        $arg2 = substr($split_str[2], 4);
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                    default:
                        //error handling
                        exit(23);
                        break;
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
                $obj->accept($parser);
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
                $obj = new Instr_no_arg($instr, $instr_cnt, $dom, $xml);
                $obj->accept($parser);
                $instr_cnt++;
                break;
            case "POPS":
            case "DEFVAR":
                if (!empty($split_str[2])) {
                    exit(23);
                }
                if (
                    preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[1]
                    )
                ) {
                    $arg = $split_str[1];
                    $arg_type = "var";
                } else {
                    exit(23);
                }
                $obj = new Instr_1_arg(
                    $instr,
                    $instr_cnt,
                    $arg_type,
                    $arg,
                    $dom,
                    $xml
                );
                $obj->accept($parser);
                $instr_cnt++;
                break;
            case "PUSHS":
            case "DPRINT":
            case "EXIT":
            case "WRITE":
                //tu zomiera basic/read_test
                if (!empty($split_str[2])) {
                    exit(23);
                }
                if (empty($split_str[1])) {
                    exit(23);
                }
                //TODO: func
                switch ($split_str[1]) {
                    case (bool) preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[1]
                    ):
                        $arg = $split_str[1];
                        $arg_type = "var";
                        break;
                    case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $split_str[1]):
                        $arg = trim(
                            $split_str[1],
                            strstr($split_str[1], "@", true) . "@"
                        );
                        $arg_type = strstr($split_str[1], "@", true);
                        break;
                        //TODO: support for octal and hexa format
                    case (bool) preg_match("/^(int)@[0-9]*/", $split_str[1]):
                        $arg = trim(
                            $split_str[1],
                            strstr($split_str[1], "@", true) . "@"
                        );
                        $arg_type = strstr($split_str[1], "@", true);
                        break;
                    case (bool) preg_match(
                        "/^bool@(true|false)$/",
                        $split_str[1]
                    ):
                        $arg = trim(
                            $split_str[1],
                            strstr($split_str[1], "@", true) . "@"
                        );
                        fwrite(STDERR, "PRD " . $arg . "\n");
                        $arg_type = strstr($split_str[1], "@", true);
                        break;
                    case (bool) preg_match("/^nil@nil$/", $split_str[1]):
                        $arg = substr($split_str[1], 4);
                        $arg_type = strstr($split_str[1], "@", true);
                        break;
                    default:
                        //error handling
                        exit(23);
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
                $obj->accept($parser);
                $instr_cnt++;
                break;
            case "LABEL":
            case "JUMP":
            case "CALL":
                if (!empty($split_str[2]) || empty($split_str[1])) {
                    exit(23);
                }
                if (
                    preg_match(
                        "/^[a-zA-Z\-_\$&%\*!\?][a-zA-Z\-_\$&%\*!\?]*$/",
                        $split_str[1]
                    )
                ) {
                    $arg = $split_str[1];
                    $arg_type = "label";
                    $obj = new Instr_1_arg(
                        $instr,
                        $instr_cnt,
                        $arg_type,
                        $arg,
                        $dom,
                        $xml
                    );
                    $obj->accept($parser);
                    $instr_cnt++;
                } else {
                    exit(23);
                }
                break;
            case "JUMPIFEQ":
            case "JUMPIFNEQ":
                if (empty($split_str[1])) {
                    exit(23);
                }
                if (
                    preg_match(
                        "/^[a-zA-Z\-_\$&%\*!\?][a-zA-Z\-_\$&%\*!\?]*$/",
                        $split_str[1]
                    )
                ) {
                    $arg1 = $split_str[1];
                    $arg1_type = "label";
                    if (empty($split_str[2])) {
                        exit(23);
                    }
                    switch ($split_str[2]) {
                        case (bool) preg_match(
                            "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                            $split_str[2]
                        ):
                            $arg2 = $split_str[2];
                            if (
                                !strcmp($arg2, "") ||
                                trim(
                                    $split_str[2],
                                    strstr($split_str[2], "@", true) . "@"
                                )
                            ) {
                                //exit(23);
                            }
                            $arg2_type = "var";
                            break;
                        case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $split_str[2]):
                            $arg2 = trim(
                                $split_str[2],
                                strstr($split_str[2], "@", true) . "@"
                            );
                            if (!strcmp($arg2, "")) {
                                exit(23);
                            }
                            $arg2_type = strstr($split_str[2], "@", true);
                            break;
                            //TODO: support for octal and hexa format
                        case (bool) preg_match("/^(int)@[0-9]*/", $split_str[2]):
                            $arg2 = trim(
                                $split_str[2],
                                strstr($split_str[2], "@", true) . "@"
                            );
                            if (!strcmp($arg2, "")) {
                                exit(23);
                            }
                            $arg2_type = strstr($split_str[2], "@", true);
                            break;
                        case (bool) preg_match(
                            "/^bool@(true|false)$/",
                            $split_str[2]
                        ):
                            $arg2 = trim(
                                $split_str[2],
                                strstr($split_str[2], "@", true) . "@"
                            );
                            if (!strcmp($arg2, "")) {
                                exit(23);
                            }
                            $arg2_type = strstr($split_str[2], "@", true);
                            break;
                        case (bool) preg_match("/^nil@nil$/", $split_str[2]):
                            $arg2 = substr($split_str[2], 4);
                            $arg2_type = strstr($split_str[2], "@", true);
                            break;
                        default:
                            //error handling
                            exit(23);
                            break;
                    }
                    if (empty($split_str[3])) {
                        exit(23);
                    }
                    switch ($split_str[3]) {
                        case (bool) preg_match(
                            "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                            $split_str[3]
                        ):
                            $arg3 = $split_str[3];
                            if (
                                !strcmp($arg3, "") ||
                                trim(
                                    $split_str[3],
                                    strstr($split_str[3], "@", true) . "@"
                                )
                            ) {
                                //exit(23);
                            }
                            $arg3_type = "var";
                            break;
                        case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $split_str[3]):
                            $arg3 = trim(
                                $split_str[3],
                                strstr($split_str[3], "@", true) . "@"
                            );
                            if (!strcmp($arg3, "")) {
                                exit(23);
                            }
                            $arg3_type = strstr($split_str[3], "@", true);
                            break;
                            //TODO: support for octal and hexa format
                        case (bool) preg_match("/^(int)@[0-9]*/", $split_str[3]):
                            $arg3 = trim(
                                $split_str[3],
                                strstr($split_str[3], "@", true) . "@"
                            );
                            if (!strcmp($arg3, "")) {
                                exit(23);
                            }
                            $arg3_type = strstr($split_str[3], "@", true);
                            break;
                        case (bool) preg_match(
                            "/^bool@(true|false)$/",
                            $split_str[3]
                        ):
                            $arg3 = trim(
                                $split_str[3],
                                strstr($split_str[3], "@", true) . "@"
                            );
                            if (!strcmp($arg3, "")) {
                                exit(23);
                            }
                            $arg3_type = strstr($split_str[3], "@", true);
                            break;
                        case (bool) preg_match("/^nil@nil$/", $split_str[3]):
                            $arg3 = substr($split_str[3], 4);
                            $arg3_type = strstr($split_str[3], "@", true);
                            break;
                        default:
                            //error handling
                            exit(23);
                            break;
                    }
                    $obj = new Instr_3_arg(
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
                    $obj->accept($parser);
                    $instr_cnt++;
                } else {
                    exit(23);
                }
                break;
            case "READ":
                if (empty($split_str[1]) || empty($split_str[2])) {
                    exit(23);
                }
                if (
                    preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[1]
                    )
                ) {
                    $arg1 = $split_str[1];
                    $arg1_type = "var";
                } else {
                    //TODO: ?
                    exit(23);
                }
                if (preg_match("/^(int|string|bool)$/", $split_str[2])) {
                    $arg2 = trim(
                        $split_str[2],
                        strstr($split_str[2], "@", true) . "@"
                    );
                    $arg2_type = "type";
                } else {
                    exit(23);
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
                $obj->accept($parser);
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
                if (empty($split_str[1]) || empty($split_str[2]) || empty($split_str[3])) {
                    exit(23);
                }
                if (
                    preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[1]
                    )
                ) {
                    $arg1 = $split_str[1];
                    $arg1_type = "var";
                } else {
                    exit(23);
                }
                switch ($split_str[2]) {
                    case (bool) preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[2]
                    ):
                        $arg2 = $split_str[2];
                        if (
                            !strcmp($arg2, "") ||
                            trim(
                                $split_str[2],
                                strstr($split_str[2], "@", true) . "@"
                            )
                        ) {
                            //exit(23);
                        }
                        $arg2_type = "var";
                        break;
                    case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $split_str[2]):
                        $arg2 = trim(
                            $split_str[2],
                            strstr($split_str[2], "@", true) . "@"
                        );
                        if (!strcmp($arg2, "")) {
                            exit(23);
                        }
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                        //TODO: support for octal and hexa format
                    case (bool) preg_match("/^(int)@[0-9]*/", $split_str[2]):
                        $arg2 = trim(
                            $split_str[2],
                            strstr($split_str[2], "@", true) . "@"
                        );
                        if (!strcmp($arg2, "")) {
                            exit(23);
                        }
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                    case (bool) preg_match(
                        "/^bool@(true|false)$$/",
                        $split_str[2]
                    ):
                        $arg2 = trim(
                            $split_str[2],
                            strstr($split_str[2], "@", true) . "@"
                        );
                        if (!strcmp($arg2, "")) {
                            exit(23);
                        }
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                    case (bool) preg_match("/^nil@nil$/", $split_str[2]):
                        $arg2 = substr($split_str[2], 4);
                        $arg2_type = strstr($split_str[2], "@", true);
                        break;
                    default:
                        //error handling
                        exit(23);
                        break;
                }
                switch ($split_str[3]) {
                    case (bool) preg_match(
                        "/^(LF|TF|GF)@[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*/",
                        $split_str[3]
                    ):
                        $arg3 = $split_str[3];
                        if (
                            !strcmp($arg3, "") ||
                            trim(
                                $split_str[3],
                                strstr($split_str[3], "@", true) . "@"
                            )
                        ) {
                            //exit(23);
                        }
                        $arg3_type = "var";
                        break;
                    case (bool) preg_match("/^string@(([\x{0021}-\x{0022}\x{0024}-\x{005B}\x{005D}-\x{FFFF}])*|(\\\[0-9]{3})*)*$/mu", $split_str[3]):
                        $arg3 = trim(
                            $split_str[3],
                            strstr($split_str[3], "@", true) . "@"
                        );
                        if (!strcmp($arg3, "")) {
                            exit(23);
                        }
                        $arg3_type = strstr($split_str[3], "@", true);
                        break;
                        //TODO: support for octal and hexa format
                    case (bool) preg_match("/^(int)@[0-9]*/", $split_str[3]):
                        $arg3 = trim(
                            $split_str[3],
                            strstr($split_str[3], "@", true) . "@"
                        );
                        if (!strcmp($arg3, "")) {
                            exit(23);
                        }
                        $arg3_type = strstr($split_str[3], "@", true);
                        break;
                    case (bool) preg_match(
                        "/^bool@(true|false)$$/",
                        $split_str[3]
                    ):
                        $arg3 = trim(
                            $split_str[3],
                            strstr($split_str[3], "@", true) . "@"
                        );
                        if (!strcmp($arg3, "")) {
                            exit(23);
                        }
                        $arg3_type = strstr($split_str[3], "@", true);
                        break;
                    case (bool) preg_match("/^nil@nil$/", $split_str[3]):
                        $arg3 = substr($split_str[3], 4);
                        $arg3_type = strstr($split_str[3], "@", true);
                        break;
                    default:
                        //error handling
                        exit(23);
                        break;
                }
                $obj = new Instr_3_arg(
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
                $obj->accept($parser);
                $instr_cnt++;
                break;
            default:
                exit(22);
                //error handling
                break;
        }
        $arg = null;
    }
}
echo $dom->saveXML($dom, LIBXML_NOEMPTYTAG);
