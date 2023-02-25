<?php

/**
 * @file visitable.php
 * 
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

/**
 * Interface for visitable classes
 */
interface Visitable
{
    public function parse(Visitor $visitor): void;
}
