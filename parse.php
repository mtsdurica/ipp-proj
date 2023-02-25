<?php

declare(strict_types=1);

/**
 * @file parse.php
 *
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

include "./php_libs/visitor.php";
include "./php_libs/visitable.php";
include "./php_libs/parse_visitor.php";
include "./php_libs/instr_no_arg.php";
include "./php_libs/instr_1_arg.php";
include "./php_libs/instr_2_arg.php";
include "./php_libs/instr_3_arg.php";
include "./php_libs/functions.php";

ini_set("display_errors", "stderr");

if ($argc === 2  && !strcmp($argv[1], "--help")) {
    print_help();
    exit(0);
} else if ($argc === 1) {
    parse_src_file(STDIN);
} else {
    fwrite(STDERR, "ERROR: Unknown parameter or incorrect amount of parameters!\n");
    exit(10);
}
