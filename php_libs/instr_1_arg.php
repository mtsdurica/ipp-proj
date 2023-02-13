<?php

/**
 * @file instr_1_arg.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_1_arg implements Visitable
{
    private string $op_code;
    private int $order;
    private string $arg;
    private \DOMDocument $dom;
    private \DOMElement $xml;
    private string $arg_type;

    function __construct(string $instr, int $instr_order, string $arg_type, string $arg, \DOMDocument $dom, \DOMElement $xml)
    {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->arg = $arg;
        $this->dom = $dom;
        $this->xml = $xml;
        $this->arg_type = $arg_type;
    }

    function get_order(): int
    {
        return $this->order;
    }

    function get_arg_type(): string
    {
        return $this->arg_type;
    }

    function get_arg(): string
    {
        return $this->arg;
    }

    function get_op_code(): string
    {
        return $this->op_code;
    }

    function get_xml(): \DOMElement
    {
        return $this->xml;
    }

    function get_dom(): \DOMDocument
    {
        return $this->dom;
    }

    function parse(Visitor $visitor): void
    {
        $visitor->visit_1_arg($this);
    }
}
