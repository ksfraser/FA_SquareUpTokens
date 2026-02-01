<?php

namespace FA_SquareUpTokens\Tests;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\ExceptionTranslatorInterface;
use FA_SquareUpTokens\ExceptionTranslator;

class ExceptionTranslatorTest extends TestCase
{
    public function testTranslateReturnsFormattedMessage()
    {
        $translator = new ExceptionTranslator();
        $exception = new \Exception('Test error');

        $message = $translator->translate($exception);

        $this->assertEquals('Error: Test error', $message);
    }
}