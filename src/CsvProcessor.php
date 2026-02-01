<?php

namespace FA_SquareUpTokens;

use FA_SquareUpTokens\Interfaces\CsvProcessorInterface;
use FA_SquareUpTokens\Interfaces\CsvValidatorInterface;
use FA_SquareUpTokens\Interfaces\ExceptionTranslatorInterface;
use FA_SquareUpTokens\Interfaces\CsvRowFilterInterface;

/**
 * Processes CSV imports for Square tokens
 */
class CsvProcessor implements CsvProcessorInterface
{
    private CsvValidatorInterface $validator;
    private ExceptionTranslatorInterface $translator;
    private $dao;
    private CsvRowFilterInterface $filter;

    public function __construct(CsvValidatorInterface $validator, ExceptionTranslatorInterface $translator, $dao, CsvRowFilterInterface $filter = null)
    {
        $this->validator = $validator;
        $this->translator = $translator;
        $this->dao = $dao;
        $this->filter = $filter ?? new CsvRowFilter($dao);
    }

    /**
     * @inheritDoc
     */
    public function process(string $filePath, bool $skipMissingSkus = false): array
    {
        try {
            $this->validator->validate($filePath);

            $this->dao->beginTransaction();
            $this->dao->nullTokens();
            $inserted = $this->dao->insertStockIds();

            // Parse CSV
            $data = $this->parseCsv($filePath, $skipMissingSkus);

            $updated = $this->dao->updateTokens($data['toUpdate']);
            $this->dao->commit();

            return [
                'updated' => $updated,
                'skipped' => $data['skipped'],
                'not_in_table' => $data['notInTable'],
                'missing_in_fa' => $data['missingInFa']
            ];
        } catch (\Exception $e) {
            $this->dao->rollback();
            throw new \Exception($this->translator->translate($e));
        }
    }

    private function parseCsv(string $filePath, bool $skipMissingSkus): array
    {
        try {
            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception("Unable to open CSV file: $filePath");
            }
            $headers = fgetcsv($handle);
            $skuIndex = array_search('SKU', $headers);
            $tokenIndex = array_search('Token', $headers);

            $seenSkus = [];
            $toUpdate = [];
            $skipped = 0;
            $notInTable = 0;
            $missingInFa = [];

            while (($row = fgetcsv($handle)) !== false) {
                $filteredRow = $this->filter->filter([$row[$skuIndex] ?? '', $row[$tokenIndex] ?? ''], $seenSkus, $skipMissingSkus);

                if ($filteredRow['skip']) {
                    if ($filteredRow['reason'] === 'not_in_table') {
                        $notInTable++;
                    } else {
                        $skipped++;
                    }
                    continue;
                }

                if ($filteredRow['reason'] === 'missing_in_fa') {
                    $missingInFa[] = $filteredRow['data']['stock_id'];
                }

                $toUpdate[] = $filteredRow['data'];
            }

            fclose($handle);

            return ['toUpdate' => $toUpdate, 'skipped' => $skipped, 'notInTable' => $notInTable, 'missingInFa' => $missingInFa];
        } catch (\Exception $e) {
            throw new \Exception("Error parsing CSV: " . $e->getMessage());
        }
    }
}