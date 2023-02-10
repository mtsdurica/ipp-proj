<?php

/**
 * @file instr_1_arg.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_1_arg implements Visitable
{
    private $op_code;
    private $order;
    private $arg;
    private $dom;
    private $xml;
    private $arg_type;

    function __construct($instr, $instr_order, $arg_type, $arg, $dom, $xml)
    {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->arg = $arg;
        $this->dom = $dom;
        $this->xml = $xml;
        $this->arg_type = $arg_type;
    }

    function get_order()
    {
        return $this->order;
    }

    function get_arg_type()
    {
        return $this->arg_type;
    }

    function get_arg()
    {
        return $this->arg;
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
        $visitor->visit_1_arg($this);
    }
}
