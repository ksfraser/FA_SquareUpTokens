<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\CsvRowFilterInterface;
use FA_SquareUpTokens\Interfaces\TokenDaoInterface;

/**
 * Filters CSV rows for processing
 */
class CsvRowFilter implements CsvRowFilterInterface
{
    private TokenDaoInterface $dao;

    public function __construct(TokenDaoInterface $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @inheritDoc
     */
    public function filter(array $row, array &$seenSkus, bool $skipMissingSkus = false): array
    {
        $sku = trim($row[0] ?? '');
        $token = trim($row[1] ?? '');

        if (empty($sku)) {
            return ['skip' => true, 'reason' => 'empty_sku', 'data' => null];
        }

        if (isset($seenSkus[$sku])) {
            return ['skip' => true, 'reason' => 'duplicate_sku', 'data' => null];
        }

        $seenSkus[$sku] = true;

        if (!$this->dao->stockIdExists($sku)) {
            if ($skipMissingSkus) {
                return ['skip' => true, 'reason' => 'not_in_table', 'data' => null];
            } else {
                // Process but mark as missing
                return ['skip' => false, 'reason' => 'missing_in_fa', 'data' => ['stock_id' => $sku, 'token' => $token]];
            }
        }

        return ['skip' => false, 'reason' => '', 'data' => ['stock_id' => $sku, 'token' => $token]];
    }
}