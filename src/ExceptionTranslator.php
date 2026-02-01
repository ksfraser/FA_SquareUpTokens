<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\ExceptionTranslatorInterface;

/**
 * Translates exceptions to FA display_notification calls
 */
class ExceptionTranslator implements ExceptionTranslatorInterface
{
    /**
     * @inheritDoc
     */
    public function translate(\Exception $exception): string
    {
        return 'Error: ' . $exception->getMessage();
    }
}