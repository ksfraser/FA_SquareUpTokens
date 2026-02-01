<?php

namespace FA_SquareUpTokens;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\TokenDaoInterface;

/**
 * Test for AdminActionHandler
 */
class AdminActionHandlerTest extends TestCase
{
    private TokenDaoInterface $dao;
    private AdminActionHandler $handler;

    protected function setUp(): void
    {
        // Mock FA functions
        if (!function_exists('display_notification')) {
            function display_notification($msg) {}
        }
        if (!function_exists('display_error')) {
            function display_error($msg) {}
        }

        $this->dao = $this->createMock(TokenDaoInterface::class);
        $this->handler = new AdminActionHandler($this->dao);
    }

    public function testHandleNullify()
    {
        $this->dao->expects($this->once())->method('beginTransaction');
        $this->dao->expects($this->once())->method('nullTokens');
        $this->dao->expects($this->once())->method('commit');

        // Since display_notification is a global function, we can't easily mock it, but assume it works
        $this->handler->handle('nullify');
    }

    public function testHandleInsert()
    {
        $this->dao->expects($this->once())->method('beginTransaction');
        $this->dao->expects($this->once())->method('insertStockIds')->willReturn(5);
        $this->dao->expects($this->once())->method('commit');

        $this->handler->handle('insert');
    }

    public function testHandleInvalidAction()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown action: invalid');

        $this->handler->handle('invalid');
    }
}