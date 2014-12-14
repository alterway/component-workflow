<?php

namespace spec\Alterway\Component\Workflow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Alterway\Component\Workflow\Builder;
use Alterway\Component\Workflow\ContextInterface as Ctx;
use Alterway\Component\Workflow\SpecificationInterface as Spec;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WorkflowSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher, Spec $specA, Spec $specAB, Spec $specAC)
    {
        $builder = (new Builder($dispatcher->getWrappedObject()))
            ->open('A', $specA->getWrappedObject())
            ->link('A', 'B', $specAB->getWrappedObject())
            ->link('A', 'C', $specAC->getWrappedObject());

        $this->beConstructedThrough([$builder, 'getWorkflow']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alterway\Component\Workflow\Workflow');
    }

    function it_is_initializable_with_an_empty_token()
    {
        $this->initialize()->shouldHaveType('Alterway\Component\Workflow\Workflow');
    }

    function it_is_initializable_with_a_known_token()
    {
        $this->initialize('A')->shouldHaveType('Alterway\Component\Workflow\Workflow');
    }

    function it_throws_exception_if_initialized_with_an_unknown_token()
    {
        $this->shouldThrow('Alterway\Component\Workflow\Exception\InvalidTokenException')->duringInitialize('D');
    }

    function it_throws_exception_when_advancing_if_not_initialized(Ctx $ctx)
    {
        $this->shouldThrow('Alterway\Component\Workflow\Exception\NotInitializedWorkflowException')->duringNext($ctx);
    }

    function it_advances_when_only_one_way_exists(EventDispatcherInterface $dispatcher, Spec $specA, Spec $specAB, Spec $specAC, Ctx $ctx)
    {
        $this->let($dispatcher, $specA, $specAB, $specAC);

        $dispatcher->dispatch('B', Argument::type('Alterway\Component\Workflow\Event'))->shouldBeCalled();

        $specAB->isSatisfiedBy($ctx)->willReturn(true);
        $specAC->isSatisfiedBy($ctx)->willReturn(false);

        $this->initialize('A');

        $this->next($ctx)->shouldHaveType('Alterway\Component\Workflow\Workflow');
    }

    function it_throws_exception_when_no_way_exist(EventDispatcherInterface $dispatcher, Spec $specA, Spec $specAB, Spec $specAC, Ctx $ctx)
    {
        $this->let($dispatcher, $specA, $specAB, $specAC);

        $specAB->isSatisfiedBy($ctx)->willReturn(false);
        $specAC->isSatisfiedBy($ctx)->willReturn(false);

        $this->initialize('A');

        $this->shouldThrow('Alterway\Component\Workflow\Exception\NoOpenTransitionException')->duringNext($ctx);
    }

    function it_throws_exception_when_more_than_one_way_exist(EventDispatcherInterface $dispatcher, Spec $specA, Spec $specAB, Spec $specAC, Ctx $ctx)
    {
        $this->let($dispatcher, $specA, $specAB, $specAC);

        $specAB->isSatisfiedBy($ctx)->willReturn(true);
        $specAC->isSatisfiedBy($ctx)->willReturn(true);

        $this->initialize('A');

        $this->shouldThrow('Alterway\Component\Workflow\Exception\MoreThanOneOpenTransitionException')->duringNext($ctx);
    }
}
