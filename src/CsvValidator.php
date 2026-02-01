<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\CsvValidatorInterface;

/**
 * Validates CSV files for required columns and content
 */
class CsvValidator implements CsvValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            throw new \Exception('CSV file does not exist');
        }

        $content = file_get_contents($filePath);
        if (empty(trim($content))) {
            throw new \Exception('CSV file is empty');
        }

        // Open and check headers
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);
        fclose($handle);

        if (!$this->hasRequiredColumns($headers)) {
            throw new \Exception('CSV missing required columns: SKU and Token');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasRequiredColumns(array $headers): bool
    {
        return in_array('SKU', $headers) && in_array('Token', $headers);
    }
}