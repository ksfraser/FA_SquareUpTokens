<?php

namespace FA_SquareUpTokens\Tests;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\TokenDaoInterface;
use FA_SquareUpTokens\TokenDao;

interface DaoInterface {
    public function query($sql, $params = []);
}

class TokenDaoTest extends TestCase
{
    public function testStockIdExistsReturnsBool()
    {
        $mockDao = $this->createMock(DaoInterface::class);
        $mockDao->method('query')->willReturn([['COUNT(*)' => 1]]);

        $dao = new TokenDao($mockDao);
        $result = $dao->stockIdExists('test_id');

        $this->assertTrue($result);
    }

    public function testStockIdExistsReturnsFalseWhenNotExists()
    {
        $mockDao = $this->createMock(DaoInterface::class);
        $mockDao->method('query')->willReturn([['COUNT(*)' => 0]]);

        $dao = new TokenDao($mockDao);
        $result = $dao->stockIdExists('test_id');

        $this->assertFalse($result);
    }
}