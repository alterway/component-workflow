<?php


namespace Alterway\Component\Workflow\tests\unit;


use atoum;

class Workflow extends atoum
{
    public function beforeTestMethod($method)
    {
        // mocks
        $this->dispatcher = new \mock\Symfony\Component\EventDispatcher\EventDispatcherInterface();

        $this->specification = new \mock\Alterway\Component\Workflow\SpecificationInterface();
        $this->calling($this->specification)->isSatisfiedBy = function() { return true; };

        $this->context = new \mock\Alterway\Component\Workflow\ContextInterface();

        // objects
        $this->builder = new \Alterway\Component\Workflow\Builder($this->dispatcher);
    }

    public function testICanLaunchACorrectWorkflow()
    {
        $this
            ->builder
            ->open('A', $this->specification)
            ->getWorkflow()
            ->initialize()
            ->next($this->context)
            ;

        $this
            ->mock($this->dispatcher)
            ->call('dispatch')
            ->once()
            ;
    }

    public function testICannotAddLinkOnBuilderWorkflowWithoutStartingNode()
    {
        try {
            $this
                ->builder
                ->link('A', 'B', $this->specification)
                ;
        } catch (\Exception $e) {
            $this
                ->exception($e)
                ->isInstanceOf('Alterway\Component\Workflow\Exception\NoStartingNodeBuilderException')
                ;
        }
    }

    public function testICannotGetFromBuilderWorkflowWithoutStartingNode()
    {
        try {
            $this
                ->builder
                ->getWorkflow()
                ;
        } catch (\Exception $e) {
            $this
                ->exception($e)
                ->isInstanceOf('Alterway\Component\Workflow\Exception\NoStartingNodeBuilderException')
                ;
        }
    }

    public function testICantGoToNextBecauseOfWorkflowNotInitialized()
    {
        try {
            $this
                ->builder
                ->open('A', $this->specification)
                ->getWorkflow()
                ->next($this->context)
                ;
        } catch (\Exception $e) {
            $this
                ->exception($e)
                ->isInstanceOf('Alterway\Component\Workflow\Exception\NotInitializedWorkflowException')
                ;
        }
    }

    public function testICantGoToNextBecauseOfInvalidToken()
    {
        try {
            $this
                ->builder
                ->open('A', $this->specification)
                ->getWorkflow()
                ->initialize('B')
                ;
        } catch (\Exception $e) {
            $this
                ->exception($e)
                ->isInstanceOf('Alterway\Component\Workflow\Exception\InvalidTokenException')
                ;
        }
    }

    public function testICantGoToNextBecauseOfNoOpenTransition()
    {
        try {
            $this
                ->builder
                ->open('A', $this->specification)
                ->getWorkflow()
                ->initialize('A')
                ->next($this->context)
                ;
        } catch (\Exception $e) {
            $this
                ->exception($e)
                ->isInstanceOf('Alterway\Component\Workflow\Exception\NoOpenTransitionException')
                ;
        }
    }

    public function testICantHaveMoreThanTwoOpenedTransitions()
    {
        try {
            $this
                ->builder
                ->open('A', $this->specification)
                ->link('A', 'B', $this->specification)
                ->link('A', 'C', $this->specification)
                ->getWorkflow()
                ->initialize('A')
                ->next($this->context)
                ;
        } catch (\Exception $e) {
            $this
                ->exception($e)
                ->isInstanceOf('Alterway\Component\Workflow\Exception\MoreThanOneOpenTransitionException')
                ;
        }
    }
}
