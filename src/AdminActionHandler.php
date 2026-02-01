<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\AdminActionHandlerInterface;
use FA_SquareUpTokens\Interfaces\TokenDaoInterface;

/**
 * Handles admin actions for the module
 */
class AdminActionHandler implements AdminActionHandlerInterface
{
    private TokenDaoInterface $dao;

    public function __construct(TokenDaoInterface $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @inheritDoc
     */
    public function handle(string $action): void
    {
        switch ($action) {
            case 'nullify':
                $this->nullifyTokens();
                break;
            case 'insert':
                $this->insertStockIds();
                break;
            default:
                throw new \InvalidArgumentException("Unknown action: $action");
        }
    }

    private function nullifyTokens(): void
    {
        $this->dao->beginTransaction();
        $this->dao->nullTokens();
        $this->dao->commit();
        display_notification("All tokens nulled.");
    }

    private function insertStockIds(): void
    {
        $this->dao->beginTransaction();
        $count = $this->dao->insertStockIds();
        $this->dao->commit();
        display_notification("$count stock IDs inserted.");
    }
}