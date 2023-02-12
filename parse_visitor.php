<?php

/**
 * @file parse_visitor.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

class Parse_visitor implements Visitor
{
    function __construct()
    {
    }

    public function visit_no_arg($instr)
    {
        $dom = $instr->get_dom();
        $xml = $instr->get_xml();
        $xml_instr = $dom->createElement("instruction");
        $xml_instr->setAttribute("order", $instr->get_order());
        $xml_instr->setAttribute("opcode", $instr->get_op_code());
        $xml->appendChild($xml_instr);
    }

    public function visit_1_arg($instr)
    {
        $dom = $instr->get_dom();
        $xml = $instr->get_xml();
        $xml_instr = $dom->createElement("instruction");
        $xml_instr->setAttribute("order", $instr->get_order());
        $xml_instr->setAttribute("opcode", $instr->get_op_code());
        $xml_arg = $dom->createElement(
            "arg1",
            htmlspecialchars($instr->get_arg())
        );
        $xml_arg->setAttribute("type", $instr->get_arg_type());
        $xml_instr->appendChild($xml_arg);
        $xml->appendChild($xml_instr);
    }

    public function visit_2_arg($instr)
    {
        $dom = $instr->get_dom();
        $xml = $instr->get_xml();
        $xml_instr = $dom->createElement("instruction");
        $xml_instr->setAttribute("order", $instr->get_order());
        $xml_instr->setAttribute("opcode", $instr->get_op_code());
        $xml_arg1 = $dom->createElement(
            "arg1",
            htmlspecialchars($instr->get_arg1())
        );
        $xml_arg2 = $dom->createElement(
            "arg2",
            htmlspecialchars($instr->get_arg2())
        );
        $xml_arg1->setAttribute("type", $instr->get_arg1_type());
        $xml_arg2->setAttribute("type", $instr->get_arg2_type());
        $xml_instr->appendChild($xml_arg1);
        $xml_instr->appendChild($xml_arg2);
        $xml->appendChild($xml_instr);
    }

    public function visit_3_arg($instr)
    {
        $dom = $instr->get_dom();
        $xml = $instr->get_xml();
        $xml_instr = $dom->createElement("instruction");
        $xml_instr->setAttribute("order", $instr->get_order());
        $xml_instr->setAttribute("opcode", $instr->get_op_code());
        $xml_arg1 = $dom->createElement(
            "arg1",
            htmlspecialchars($instr->get_arg1())
        );
        $xml_arg2 = $dom->createElement(
            "arg2",
            htmlspecialchars($instr->get_arg2())
        );
        $xml_arg3 = $dom->createElement(
            "arg3",
            htmlspecialchars($instr->get_arg3())
        );
        $xml_arg1->setAttribute("type", $instr->get_arg1_type());
        $xml_arg2->setAttribute("type", $instr->get_arg2_type());
        $xml_arg3->setAttribute("type", $instr->get_arg3_type());
        $xml_instr->appendChild($xml_arg1);
        $xml_instr->appendChild($xml_arg2);
        $xml_instr->appendChild($xml_arg3);
        $xml->appendChild($xml_instr);
    }
}
