<?php


namespace Alterway\Component\Workflow\Exception;


class InvalidTokenException extends \LogicException
{
    public function __construct()
    {
        return parent::__construct('Invalid token for current workflow');
    }
}
