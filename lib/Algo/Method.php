<?php
/*
    Condorcet PHP Class, with Schulze Methods and others !

    By Julien Boudry - MIT LICENSE (Please read LICENSE.txt)
    https://github.com/julien-boudry/Condorcet
*/
namespace Condorcet\Algo;

use Condorcet\CondorcetVersion;
use Condorcet\Election;

// Generic for Algorithms
abstract class Method
{
    use CondorcetVersion;

    protected $_selfElection;

    public function __construct (Election $mother)
    {
        $this->_selfElection = $mother;
    }
}
