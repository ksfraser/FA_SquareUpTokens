<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for filtering CSV rows
 */
interface CsvRowFilterInterface
{
    /**
     * Filter a row and return the result
     * @param array $row
     * @param array $seenSkus
     * @param bool $skipMissingSkus
     * @return array ['skip' => bool, 'reason' => string, 'data' => array or null]
     */
    public function filter(array $row, array &$seenSkus, bool $skipMissingSkus = false): array;
}