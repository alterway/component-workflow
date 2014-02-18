<?php


namespace Alterway\Component\Workflow;


use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Builder
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var NodeMap
     */
    private $nodes;

    /**
     * @var Node
     */
    private $start;


    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();
        $this->nodes = new NodeMap();
        $this->start = null;
    }

    /**
     * Opens a workflow.
     *
     * @param string $src
     * @param SpecificationInterface $spec
     *
     * @return Builder
     */
    public function open($src, SpecificationInterface $spec)
    {
        $this->start = $this->nodes->get(uniqid());
        $this->start->addTransition($this->nodes->get($src), $spec);

        return $this;
    }

    /**
     * Adds a link to the workflow.
     *
     * @param string $src
     * @param string $dst
     * @param SpecificationInterface $spec
     *
     * @return Builder
     *
     * @throws Exception\NoStartingNodeBuilderException
     */
    public function link($src, $dst, SpecificationInterface $spec)
    {
        if (null === $this->start) {
            throw new Exception\NoStartingNodeBuilderException();
        };

        $this->nodes->get($src)->addTransition($this->nodes->get($dst), $spec);

        return $this;
    }

    /**
     * Returns the workflow being built.
     *
     * @return Workflow
     *
     * @throws Exception\NoStartingNodeBuilderException
     */
    public function getWorkflow()
    {
        if (null === $this->start) {
            throw new Exception\NoStartingNodeBuilderException();
        };

        return new Workflow($this->start, $this->nodes, $this->eventDispatcher);
    }
}
