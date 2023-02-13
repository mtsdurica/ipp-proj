<?php

/**
 * @file instr_1_arg.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_no_arg implements Visitable
{
    private string $op_code;
    private int $order;
    private \DOMDocument $dom;
    private \DOMElement $xml;

    function __construct(string $instr, int $instr_order, \DOMDocument $dom, \DOMElement $xml)
    {
        $this->op_code = $instr;
        $this->order = $instr_order;
        $this->dom = $dom;
        $this->xml = $xml;
    }

    function get_order(): int
    {
        return $this->order;
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
        $visitor->visit_no_arg($this);
    }
}
