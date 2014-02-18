<?php


namespace Alterway\Component\Workflow;


class Transition
{
    /**
     * @var Node
     */
    private $src;

    /**
     * @var Node
     */
    private $dst;

    /**
     * @var SpecificationInterface
     */
    private $spec;


    public function __construct(Node $src, Node $dst, SpecificationInterface $spec)
    {
        $this->src = $src;
        $this->dst = $dst;
        $this->spec = $spec;
    }

    /**
     * Checks if the current transition satisfies the specifiation on the given context.
     *
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function isOpen(ContextInterface $context)
    {
        return $this->spec->isSatisfiedBy($context);
    }

    /**
     * Returns the destination of the current transition.
     *
     * @return Node
     */
    public function getDestination()
    {
        return $this->dst;
    }
}
