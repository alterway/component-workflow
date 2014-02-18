<?php


namespace Alterway\Component\Workflow;


interface SpecificationInterface
{
	/**
	 * Tests if the given context satisfies the specification.
     *
     * @param ContextInterface $context
     *
     * @return bool
	 */
    public function isSatisfiedBy(ContextInterface $context);
}
