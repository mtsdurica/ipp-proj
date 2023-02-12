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