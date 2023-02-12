<?php

/**
 * @file instr_1_arg.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_no_arg implements Visitable
{
    private $op_code;
    private $order;
    private $dom;
    private $xml;

    function __construct($instr, $instr_order, $dom, $xml)
    {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->dom = $dom;
        $this->xml = $xml;
    }

    function get_order()
    {
        return $this->order;
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
        $visitor->visit_no_arg($this);
    }
}
