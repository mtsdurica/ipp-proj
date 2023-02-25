<?php

/**
 * @file instr_1_arg.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

/**
 * Implementation of class for instruction with 1 argument
 */
class Instr_1_arg implements Visitable
{
    private string $op_code;
    private int $order;
    private \DOMDocument $dom;
    private \DOMElement $xml;
    private string $arg;
    private string $arg_type;

    function __construct(string $instr, int $instr_order, \DOMDocument $dom, \DOMElement $xml, string $arg, string $arg_type)
    {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->dom = $dom;
        $this->xml = $xml;
        $this->arg = $arg;
        $this->arg_type = $arg_type;
    }

    function get_op_code(): string
    {
        return $this->op_code;
    }

    function get_order(): int
    {
        return $this->order;
    }

    function get_dom(): \DOMDocument
    {
        return $this->dom;
    }

    function get_xml(): \DOMElement
    {
        return $this->xml;
    }

    function get_arg(): string
    {
        return $this->arg;
    }

    function get_arg_type(): string
    {
        return $this->arg_type;
    }

    function parse(Visitor $visitor): void
    {
        $visitor->visit_1_arg($this);
    }
}
