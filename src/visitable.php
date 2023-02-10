<?php

/**
 * @file visitable.php
 * @author Matúš Ďurica (xduric06@stud.fit.vutbr.cz)
 */

interface Visitable
{
    public function accept(Visitor $visitor);
}
