<?php

namespace spec\Alterway\Component\Workflow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Alterway\Component\Workflow\ContextInterface as Ctx;
use Alterway\Component\Workflow\Node;
use Alterway\Component\Workflow\SpecificationInterface as Spec;

class NodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Argument::any());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alterway\Component\Workflow\Node');
    }

    function it_has_a_name()
    {
        $this->beConstructedWith('name');
        $this->getName()->shouldReturn('name');
    }

    function it_creates_transitions(Node $node, Spec $spec)
    {
        $this->addTransition($node, $spec)->shouldReturn(null);
    }

    function it_finds_open_transition(Node $node, Spec $true, Spec $false, Ctx $ctx)
    {
        $true->isSatisfiedBy($ctx)->willReturn(true);
        $false->isSatisfiedBy($ctx)->willReturn(false);

        $this->addTransition($node, $true);
        $this->addTransition($node, $false);

        $transitions = $this->getOpenTransitions($ctx);

        $transitions->shouldBeArray();
        $transitions->shouldHaveCount(1);

        $transitions[0]->shouldHaveType('Alterway\Component\Workflow\Transition');
    }
}
