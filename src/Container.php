<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\ContainerInterface;

/**
 * Simple dependency injection container
 */
class Container implements ContainerInterface
{
    private $services = [];
    private $instances = [];

    /**
     * @inheritDoc
     */
    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception("Service $id not found");
        }

        if (!isset($this->instances[$id])) {
            $this->instances[$id] = $this->services[$id]($this);
        }

        return $this->instances[$id];
    }
}