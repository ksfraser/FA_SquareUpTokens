<?php

namespace FA_SquareUpTokens\Tests;

use PHPUnit\Framework\TestCase;
use FA_SquareUpTokens\Interfaces\CsvValidatorInterface;
use FA_SquareUpTokens\CsvValidator;

class CsvValidatorTest extends TestCase
{
    public function testHasRequiredColumnsReturnsTrueForValidHeaders()
    {
        $validator = new CsvValidator();
        $headers = ['SKU', 'Token', 'Other'];

        $this->assertTrue($validator->hasRequiredColumns($headers));
    }

    public function testHasRequiredColumnsReturnsFalseForMissingSku()
    {
        $validator = new CsvValidator();
        $headers = ['Token', 'Other'];

        $this->assertFalse($validator->hasRequiredColumns($headers));
    }

    public function testHasRequiredColumnsReturnsFalseForMissingToken()
    {
        $validator = new CsvValidator();
        $headers = ['SKU', 'Other'];

        $this->assertFalse($validator->hasRequiredColumns($headers));
    }

    public function testValidateThrowsExceptionForEmptyFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CSV file is empty');

        $tempFile = tempnam(sys_get_temp_dir(), 'empty');
        file_put_contents($tempFile, ''); // Empty file

        $validator = new CsvValidator();
        $validator->validate($tempFile);

        unlink($tempFile);
    }

    public function testValidateThrowsExceptionForNonExistentFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('CSV file does not exist');

        $validator = new CsvValidator();
        $validator->validate('/non/existent/file.csv');
    }
}