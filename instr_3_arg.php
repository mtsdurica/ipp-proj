<?php

/**
 * @file instr_3_arg.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_3_arg implements Visitable
{
    private $op_code;
    private $order;
    private $arg1;
    private $arg2;
    private $arg3;
    private $dom;
    private $xml;
    private $arg1_type;
    private $arg2_type;
    private $arg3_type;

    function __construct(
        $instr,
        $instr_order,
        $arg1_type,
        $arg2_type,
        $arg3_type,
        $arg1,
        $arg2,
        $arg3,
        $dom,
        $xml
    ) {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
        $this->dom = $dom;
        $this->xml = $xml;
        $this->arg1_type = $arg1_type;
        $this->arg2_type = $arg2_type;
        $this->arg3_type = $arg3_type;
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

    function get_arg3_type()
    {
        return $this->arg3_type;
    }

    function get_arg1()
    {
        return $this->arg1;
    }

    function get_arg2()
    {
        return $this->arg2;
    }

    function get_arg3()
    {
        return $this->arg3;
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
        $visitor->visit_3_arg($this);
    }
}
