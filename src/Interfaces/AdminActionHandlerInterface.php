<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for handling admin actions
 */
interface AdminActionHandlerInterface
{
    /**
     * Handle the given action
     * @param string $action
     * @return void
     */
    public function handle(string $action): void;
}