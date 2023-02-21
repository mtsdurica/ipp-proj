<?php

/**
 * @file instr_2_arg.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Instr_2_arg implements Visitable
{
    private string $op_code;
    private int $order;
    private string $arg1;
    private string $arg2;
    private \DOMDocument $dom;
    private \DOMElement $xml;
    private string $arg1_type;
    private string $arg2_type;

    function __construct(
        string $instr,
        int $instr_order,
        string $arg1_type,
        string $arg2_type,
        string $arg1,
        string $arg2,
        \DOMDocument $dom,
        \DOMElement $xml
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

    function get_order(): int
    {
        return $this->order;
    }

    function get_arg1_type(): string
    {
        return $this->arg1_type;
    }

    function get_arg2_type(): string
    {
        return $this->arg2_type;
    }

    function get_arg1(): string
    {
        return $this->arg1;
    }

    function get_arg2(): string
    {
        return $this->arg2;
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
        $visitor->visit_2_arg($this);
    }
}
