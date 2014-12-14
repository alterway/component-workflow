<?php

namespace spec\Alterway\Component\Workflow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Alterway\Component\Workflow\ContextInterface as Ctx;
use Alterway\Component\Workflow\Node;
use Alterway\Component\Workflow\SpecificationInterface as Spec;

class TransitionSpec extends ObjectBehavior
{
    function let(Node $node, Spec $spec)
    {
        $this->beConstructedWith($node, $node, $spec);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alterway\Component\Workflow\Transition');
    }

    function it_has_a_destination()
    {
        $this->getDestination()->shouldHaveType('Alterway\Component\Workflow\Node');
    }

    function it_knows_if_it_is_open_or_not(Node $node, Spec $spec, Ctx $ctx)
    {
        $this->let($node, $spec);

        $spec->isSatisfiedBy($ctx)->willReturn(true);
        $this->isOpen($ctx)->shouldReturn(true);

        $spec->isSatisfiedBy($ctx)->willReturn(false);
        $this->isOpen($ctx)->shouldReturn(false);
    }
}
