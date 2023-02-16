<?php

/**
 * @file visitable.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

/**
 * Interface for parsing
 */
interface Visitable
{
    public function parse(Visitor $visitor): void;
}
