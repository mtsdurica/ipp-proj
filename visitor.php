<?php

/**
 * @file visitor.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

interface Visitor
{
    public function visit_no_arg($instr): void;
    public function visit_1_arg($instr): void;
    public function visit_2_arg($instr): void;
    public function visit_3_arg($instr): void;
}
