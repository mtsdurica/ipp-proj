<?php

/**
 * @file visitor.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

/**
 * Interface for visitor classes
 */
interface Visitor
{
    /**
     * Function for parsing no argument instruction
     * 
     * @param Instr_no_arg $instr Instruction to be parsed
     */
    public function visit_no_arg(Instr_no_arg $instr): void;

    /**
     * Function for parsing 1 argument instruction
     * 
     * @param Instr_1_arg $instr Instruction to be parsed
     */
    public function visit_1_arg(Instr_1_arg $instr): void;

    /**
     * Function for parsing 2 argument instruction
     * 
     * @param Instr_2_arg $instr Instruction to be parsed
     */
    public function visit_2_arg(Instr_2_arg $instr): void;

    /**
     * Function for parsing 3 argument instruction
     * 
     * @param Instr_3_arg $instr Instruction to be parsed
     */
    public function visit_3_arg(Instr_3_arg $instr): void;
}
