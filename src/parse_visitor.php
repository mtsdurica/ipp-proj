<?php
include "visitor.php";
class Parse_visitor implements Visitor
{
    function __construct()
    {
    }

    public function visit_no_arg($instr)
    {
    }
    public function visit_1_arg($instr)
    {
        echo "\t<instruction order=\"" .
            $instr->get_order() .
            "\" opcode=\"" .
            $instr->get_op_code() .
            "\">\n";
        echo "\t\t<arg1 type=\"var\">" . $instr->get_arg() . "</arg1>\n";
        echo "\t</instruction>\n";
    }

    public function visit_2_arg($instr)
    {
    }
    public function visit_3_arg($instr)
    {
    }
}
?>
