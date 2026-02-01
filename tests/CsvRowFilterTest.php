<?php

namespace FA_SquareUpTokens;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\TokenDaoInterface;

/**
 * Test for CsvRowFilter
 */
class CsvRowFilterTest extends TestCase
{
    private TokenDaoInterface $dao;
    private CsvRowFilter $filter;

    protected function setUp(): void
    {
        $this->dao = $this->createMock(TokenDaoInterface::class);
        $this->filter = new CsvRowFilter($this->dao);
    }

    public function testFilterEmptySku()
    {
        $seenSkus = [];
        $result = $this->filter->filter(['', 'token'], $seenSkus);

        $this->assertTrue($result['skip']);
        $this->assertEquals('empty_sku', $result['reason']);
        $this->assertNull($result['data']);
    }

    public function testFilterDuplicateSku()
    {
        $seenSkus = ['sku1' => true];
        $result = $this->filter->filter(['sku1', 'token'], $seenSkus);

        $this->assertTrue($result['skip']);
        $this->assertEquals('duplicate_sku', $result['reason']);
        $this->assertNull($result['data']);
    }

    public function testFilterNotInTable()
    {
        $this->dao->expects($this->once())->method('stockIdExists')->with('sku1')->willReturn(false);
        $seenSkus = [];
        $result = $this->filter->filter(['sku1', 'token'], $seenSkus, true);

        $this->assertTrue($result['skip']);
        $this->assertEquals('not_in_table', $result['reason']);
        $this->assertNull($result['data']);
    }

    public function testFilterMissingInFa()
    {
        $this->dao->expects($this->once())->method('stockIdExists')->with('sku1')->willReturn(false);
        $seenSkus = [];
        $result = $this->filter->filter(['sku1', 'token'], $seenSkus, false);

        $this->assertFalse($result['skip']);
        $this->assertEquals('missing_in_fa', $result['reason']);
        $this->assertEquals(['stock_id' => 'sku1', 'token' => 'token'], $result['data']);
    }

    public function testFilterValid()
    {
        $this->dao->expects($this->once())->method('stockIdExists')->with('sku1')->willReturn(true);
        $seenSkus = [];
        $result = $this->filter->filter(['sku1', 'token'], $seenSkus);

        $this->assertFalse($result['skip']);
        $this->assertEquals('', $result['reason']);
        $this->assertEquals(['stock_id' => 'sku1', 'token' => 'token'], $result['data']);
        $this->assertArrayHasKey('sku1', $seenSkus);
    }
}