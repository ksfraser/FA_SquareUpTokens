<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for dependency injection container
 */
interface ContainerInterface
{
    /**
     * Register a service
     *
     * @param string $id
     * @param callable $factory
     */
    public function set(string $id, callable $factory): void;

    /**
     * Get a service
     *
     * @param string $id
     * @return mixed
     */
    public function get(string $id);
}