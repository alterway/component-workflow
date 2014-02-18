<?php


namespace Alterway\Component\Workflow\Exception;


class MoreThanOneOpenTransitionException extends \LogicException
{
    public function __construct()
    {
        return parent::__construct('More than one open transition with current context');
    }
}
