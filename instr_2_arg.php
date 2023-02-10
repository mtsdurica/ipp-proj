<?php

/**
 * @file instr_2_arg.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_2_arg implements Visitable
{
    private $op_code;
    private $order;
    private $arg1;
    private $arg2;
    private $dom;
    private $xml;
    private $arg1_type;
    private $arg2_type;

    function __construct(
        $instr,
        $instr_order,
        $arg1_type,
        $arg2_type,
        $arg1,
        $arg2,
        $dom,
        $xml
    ) {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->dom = $dom;
        $this->xml = $xml;
        $this->arg1_type = $arg1_type;
        $this->arg2_type = $arg2_type;
    }

    function get_order()
    {
        return $this->order;
    }

    function get_arg1_type()
    {
        return $this->arg1_type;
    }

    function get_arg2_type()
    {
        return $this->arg2_type;
    }

    function get_arg1()
    {
        return $this->arg1;
    }

    function get_arg2()
    {
        return $this->arg2;
    }

    function get_op_code()
    {
        return $this->op_code;
    }

    function get_xml()
    {
        return $this->xml;
    }

    function get_dom()
    {
        return $this->dom;
    }

    function accept(Visitor $visitor)
    {
        $visitor->visit_2_arg($this);
    }
}
