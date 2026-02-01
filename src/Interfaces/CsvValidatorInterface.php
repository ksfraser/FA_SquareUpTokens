<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for validating CSV data
 */
interface CsvValidatorInterface
{
    /**
     * Validate the CSV file
     *
     * @param string $filePath
     * @return bool
     * @throws \Exception if invalid
     */
    public function validate(string $filePath): bool;

    /**
     * Check if required columns exist
     *
     * @param array $headers
     * @return bool
     */
    public function hasRequiredColumns(array $headers): bool;
}