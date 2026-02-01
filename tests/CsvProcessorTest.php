<?php

namespace FA_SquareUpTokens\Tests;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\CsvProcessorInterface;
use FA_SquareUpTokens\CsvProcessor;
use FA_SquareUpTokens\Interfaces\CsvValidatorInterface;
use FA_SquareUpTokens\Interfaces\ExceptionTranslatorInterface;
use FA_SquareUpTokens\Interfaces\CsvRowFilterInterface;
use FA_SquareUpTokens\Interfaces\TokenDaoInterface;

class CsvProcessorTest extends TestCase
{
    public function testProcessValidCsvReturnsCounts()
    {
        // Create a temp CSV file
        $csvContent = "SKU,Token,Other\nID1,TOKEN1,extra\nID2,TOKEN2,extra\nID1,TOKEN3,extra\n,TOKEN4,extra\nID3,TOKEN5,extra";
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, $csvContent);

        // Mock validator
        $validator = $this->createMock(CsvValidatorInterface::class);
        $validator->method('validate')->willReturn(true);

        // Mock translator
        $translator = $this->createMock(ExceptionTranslatorInterface::class);

        // Mock DAO
        $dao = $this->getMockBuilder(\FA_SquareUpTokens\Interfaces\TokenDaoInterface::class)->getMock();
        $dao->expects($this->once())->method('beginTransaction');
        $dao->expects($this->once())->method('nullTokens');
        $dao->expects($this->once())->method('insertStockIds')->willReturn(100);
        $dao->expects($this->once())->method('updateTokens')->with([
            ['stock_id' => 'ID1', 'token' => 'TOKEN1'],
            ['stock_id' => 'ID2', 'token' => 'TOKEN2']
        ])->willReturn(2);
        $dao->expects($this->once())->method('commit');

        // Mock filter
        $filter = $this->createMock(CsvRowFilterInterface::class);
        $filter->method('filter')->willReturnCallback(function($row, &$seen, $skip) {
            $sku = trim($row[0] ?? '');
            $token = trim($row[1] ?? '');
            if (empty($sku)) {
                return ['skip' => true, 'reason' => 'empty_sku', 'data' => null];
            }
            if (isset($seen[$sku])) {
                return ['skip' => true, 'reason' => 'duplicate_sku', 'data' => null];
            }
            $seen[$sku] = true;
            if ($sku === 'ID3') {
                return ['skip' => $skip, 'reason' => $skip ? 'not_in_table' : 'missing_in_fa', 'data' => $skip ? null : ['stock_id' => $sku, 'token' => $token]];
            }
            return ['skip' => false, 'reason' => '', 'data' => ['stock_id' => $sku, 'token' => $token]];
        });

        $processor = new CsvProcessor($validator, $translator, $dao, $filter);

        $counts = $processor->process($tempFile, true);

        $this->assertEquals(['updated' => 2, 'skipped' => 2, 'not_in_table' => 1, 'missing_in_fa' => []], $counts); // 2 updated, 1 duplicate skipped, 1 blank skipped, 1 not in table

        unlink($tempFile);
    }

    public function testProcessInvalidCsvThrowsException()
    {
        $validator = $this->createMock(CsvValidatorInterface::class);
        $validator->method('validate')->willThrowException(new \Exception('Invalid CSV'));

        $translator = $this->createMock(ExceptionTranslatorInterface::class);
        $translator->method('translate')->willReturn('Translated error');

        $dao = $this->getMockBuilder(\FA_SquareUpTokens\Interfaces\TokenDaoInterface::class)->getMock();
        $dao->expects($this->once())->method('rollback');

        $filter = $this->createMock(CsvRowFilterInterface::class);

        $processor = new CsvProcessor($validator, $translator, $dao, $filter);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Translated error');
        $processor->process('/path/to/invalid.csv');
    }
}