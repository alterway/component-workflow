<?php

namespace spec\Alterway\Component\Workflow;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Alterway\Component\Workflow\SpecificationInterface as Spec;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BuilderSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alterway\Component\Workflow\Builder');
    }

    function it_can_be_opened(Spec $spec)
    {
        $this->open('start', $spec)->shouldHaveType('Alterway\Component\Workflow\Builder');
    }

    function it_creates_links_between_nodes(Spec $spec)
    {
        $this->open('start', $spec);
        $this->link('start', 'end', $spec)->shouldHaveType('Alterway\Component\Workflow\Builder');
    }

    function it_throws_exception_if_not_started_when_creating_links_between_nodes(Spec $spec)
    {
        $this->shouldThrow('Alterway\Component\Workflow\Exception\NoStartingNodeBuilderException')->duringLink('src', 'dst', $spec);
    }

    function it_builds_a_worflow_object(Spec $spec)
    {
        $this->open('start', $spec);
        $this->link('start', 'end', $spec);

        $this->getWorkflow()->shouldHaveType('Alterway\Component\Workflow\Workflow');
    }

    function it_throws_exception_if_not_started_when_building_a_workflow_object(Spec $spec)
    {
        $this->shouldThrow('Alterway\Component\Workflow\Exception\NoStartingNodeBuilderException')->duringGetWorkflow();
    }
}
