<?php

include "visitable.php";

class Instr_1_arg implements Visitable
{
    private $op_code;
    private $order;
    private $arg;

    function __construct($instr, $instr_cnt, $arg)
    {
        $this->op_code = $instr;
        $this->order = $instr_cnt;
        $this->arg = $arg;
    }

    function get_order()
    {
        return $this->order;
    }

    function get_arg()
    {
        return $this->arg;
    }

    function get_op_code()
    {
        return $this->op_code;
    }

    function accept(Visitor $visitor)
    {
        $visitor->visit_1_arg($this);
    }
}
?>
