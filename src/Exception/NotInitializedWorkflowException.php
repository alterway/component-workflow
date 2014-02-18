<?php


namespace Alterway\Component\Workflow\Exception;


class NotInitializedWorkflowException extends \LogicException
{
    public function __construct()
    {
        return parent::__construct('Workflow not initialized with a token');
    }
}
