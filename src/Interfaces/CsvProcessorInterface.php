<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for processing CSV imports
 */
interface CsvProcessorInterface
{
    /**
     * Process the CSV file
     *
     * @param string $filePath
     * @param bool $skipMissingSkus
     * @return array Counts: ['updated' => int, 'skipped' => int, 'not_in_table' => int, 'missing_in_fa' => array]
     * @throws \Exception
     */
    public function process(string $filePath, bool $skipMissingSkus = false): array;
}