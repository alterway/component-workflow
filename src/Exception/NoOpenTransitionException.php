<?php


namespace Alterway\Component\Workflow\Exception;


class NoOpenTransitionException extends \LogicException
{
    public function __construct()
    {
        return parent::__construct('No open transition with current context');
    }
}
