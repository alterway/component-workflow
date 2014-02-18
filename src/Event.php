<?php


namespace Alterway\Component\Workflow;


use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

class Event extends SymfonyEvent
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var string
     */
    private $token;


    function __construct(ContextInterface $context, $token)
    {
        $this->context = $context;
        $this->token = $token;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getToken()
    {
        return $this->token;
    }
}
