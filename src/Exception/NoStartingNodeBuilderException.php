<?php


namespace Alterway\Component\Workflow\Exception;


class NoStartingNodeBuilderException extends \LogicException
{
    public function __construct()
    {
        return parent::__construct('No starting node in current workflow');
    }
}
