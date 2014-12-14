<?php

namespace spec\Alterway\Component\Workflow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NodeMapSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Alterway\Component\Workflow\NodeMap');
    }

    function it_holds_nothing_by_default()
    {
        $this->has('A')->shouldReturn(false);
    }

    function it_creates_node_on_get_and_keep_it()
    {
        $this->get('B')->shouldReturnAnInstanceOf('Alterway\Component\Workflow\Node');
        $this->has('B')->shouldReturn(true);
    }
}
