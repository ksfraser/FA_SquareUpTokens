<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\TokenDaoInterface;

/**
 * DAO for square_tokens table, wrapping ksf_ModulesDAO
 */
class TokenDao implements TokenDaoInterface
{
    private $dao; // Instance of ksf_ModulesDAO

    public function __construct($dao = null)
    {
        $this->dao = $dao ?: new \ksf_ModulesDAO(); // Allow injection for testing
    }

    public function beginTransaction(): void
    {
        $this->dao->beginTransaction();
    }

    public function commit(): void
    {
        $this->dao->commit();
    }

    public function rollback(): void
    {
        $this->dao->rollback();
    }

    public function nullTokens(): void
    {
        $sql = "UPDATE 0_square_tokens SET square_token = NULL";
        $this->dao->query($sql);
    }

    public function insertStockIds(): int
    {
        $sql = "INSERT IGNORE INTO 0_square_tokens (stock_id) SELECT stock_id FROM 0_stock_master WHERE inactive = 0";
        $this->dao->query($sql);
        return $this->dao->affectedRows(); // Assume method exists
    }

    public function updateTokens(array $data): int
    {
        // Placeholder: for each data, update
        $updated = 0;
        foreach ($data as $row) {
            $sql = "UPDATE 0_square_tokens SET square_token = ? WHERE stock_id = ?";
            $this->dao->query($sql, [$row['token'], $row['stock_id']]);
            $updated += $this->dao->affectedRows();
        }
        return $updated;
    }

    public function stockIdExists(string $stockId): bool
    {
        $sql = "SELECT COUNT(*) FROM 0_square_tokens WHERE stock_id = ?";
        $result = $this->dao->query($sql, [$stockId]);
        return $result[0]['COUNT(*)'] > 0; // Assume result format
    }
}