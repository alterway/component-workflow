<?php


namespace Alterway\Component\Workflow;


class NodeMap
{
    /**
     * @var array
     */
    private $items;

    public function __construct()
    {
        $this->items = array();
    }

    /**
     * Gets a node by name.
     *
     * @param string $name
     *
     * @return Node
     */
    public function get($name)
    {
        $name = (string)$name;

        if (!isset($this->items[$name])) {
            $this->items[$name] = new Node($name);
        }

        return $this->items[$name];
    }

    /**
     * Checks if a node exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        $name = (string)$name;

        return isset($this->items[$name]);
    }
}
