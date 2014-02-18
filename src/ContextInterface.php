<?php


namespace Alterway\Component\Workflow;


interface ContextInterface
{
    /**
     * Adds parameters to the service container parameters.
     *
     * @param array $parameters
     */
    public function add(array $parameters);

    /**
     * Gets a service container parameter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Sets a service container parameter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);
}
