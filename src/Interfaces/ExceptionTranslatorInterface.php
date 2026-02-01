<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for translating custom exceptions to FA-compatible notifications
 */
interface ExceptionTranslatorInterface
{
    /**
     * Translate an exception to a notification message
     *
     * @param \Exception $exception
     * @return string
     */
    public function translate(\Exception $exception): string;
}