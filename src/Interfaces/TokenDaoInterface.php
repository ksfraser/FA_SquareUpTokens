<?php

namespace FA_SquareUpTokens\Interfaces;

/**
 * Interface for DAO operations on square_tokens table
 */
interface TokenDaoInterface
{
    /**
     * Begin a database transaction
     */
    public function beginTransaction(): void;

    /**
     * Commit the transaction
     */
    public function commit(): void;

    /**
     * Rollback the transaction
     */
    public function rollback(): void;

    /**
     * Null all square_token values
     */
    public function nullTokens(): void;

    /**
     * Insert stock_ids from master_stock (ignore duplicates)
     *
     * @return int Number inserted
     */
    public function insertStockIds(): int;

    /**
     * Update tokens from CSV data
     *
     * @param array $data [['stock_id' => string, 'token' => string], ...]
     * @return int Number updated
     */
    public function updateTokens(array $data): int;

    /**
     * Check if stock_id exists in table
     *
     * @param string $stockId
     * @return bool
     */
    public function stockIdExists(string $stockId): bool;
}