<?php

namespace spec\Alterway\Component\Workflow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Alterway\Component\Workflow\ContextInterface as Ctx;

class EventSpec extends ObjectBehavior
{
    function let(Ctx $ctx)
    {
        $this->beConstructedWith($ctx, Argument::any());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alterway\Component\Workflow\Event');
    }

    function it_has_a_context()
    {
        $this->getContext()->shouldHaveType('Alterway\Component\Workflow\ContextInterface');
    }

    function it_has_a_token(Ctx $ctx)
    {
        $this->beConstructedWith($ctx, 'token');
        $this->getToken()->shouldReturn('token');
    }
}
