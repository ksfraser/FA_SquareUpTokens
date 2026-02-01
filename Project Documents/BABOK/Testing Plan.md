# Testing Plan

## Database Schema

The module uses a single table to store Square tokens associated with stock IDs.

### Table Structure

The table is named `1_square_tokens` in the database (where `1_` is the FrontAccounting table prefix `TB_PREF`).

| # | Name         | Type        | Collation          | Attributes | Null | Default              | Comments | Extra                          | Action       |
|---|--------------|-------------|--------------------|------------|------|----------------------|----------|--------------------------------|--------------|
| 1 | stock_id    | Index      | varchar(255)      | latin1_swedish_ci |     | No   | None                 |         |                                | Change Change | Drop Drop |
| 2 | square_token| Index      | varchar(255)      | latin1_swedish_ci |     | Yes  | NULL                 |         | UNIQUE                         | Change Change | Drop Drop |
| 3 | last_updated| timestamp  |                    |            |      | No   | current_timestamp() |         | ON UPDATE CURRENT_TIMESTAMP() | Change Change | Drop Drop |

**Note**: For the module's SQL installation script, the table is defined as `0_square_tokens` (the `0_` is replaced by FrontAccounting with the actual prefix). The `square_token` field is unique to prevent duplicates.

## Unit Tests

### Objectives
Test individual components in isolation to ensure correctness.

### Test Cases
1. **CSV Parsing**
   - Input: Sample CSV with SKU and Token columns.
   - Expected: Correct extraction of stock_id and square_token pairs.
   - Edge cases: Empty CSV (error), missing SKU or Token columns (error), duplicate SKUs (use first, note), blank SKUs (skip, note), malformed rows.

2. **Database Nulling**
   - Input: Table with existing square_tokens.
   - Action: Null square_token for all stock_ids.
   - Expected: All square_tokens set to NULL.

3. **Insertion from Master Stock**
   - Input: Empty table, populated master_stock.
   - Action: Insert all stock_ids from master_stock.
   - Expected: Table populated with stock_ids, square_tokens NULL.

4. **Token Updates**
   - Input: Table with stock_ids, CSV data.
   - Action: Update square_tokens matching stock_ids.
   - Expected: Matching records updated, non-matching unchanged.

5. **Counting Functions**
   - Input: Populated table.
   - Action: Count stock_ids and updated tokens.
   - Expected: Accurate counts returned.

6. **Transaction Management**
   - Input: Valid CSV.
   - Action: Simulate failure during update.
   - Expected: All changes rolled back, no partial updates.

7. **Admin Nullify**
   - Input: Table with tokens.
   - Action: Nullify all tokens.
   - Expected: All square_tokens set to NULL.

8. **Admin Insert Stock IDs**
   - Input: Table missing some stock_ids from master_stock.
   - Action: Insert from master_stock.
   - Expected: All stock_ids present, no duplicates.

### Tools
- PHPUnit for PHP unit testing.

## Integration Tests

### Objectives
Test interactions between components, including database and UI.

### Test Cases
1. **Full Import Process**
   - Simulate UI file upload.
   - Process CSV, perform nulling, insertion, updates in transaction.
   - Verify database state and reported counts.

2. **Error Handling**
   - Invalid CSV: Empty file, missing SKU/Token columns – check error messages.
   - Database connection failure: Rollback and notify.

3. **Admin Actions**
   - Nullify tokens: Confirm popup, verify all tokens nulled.
   - Insert stock_ids: Verify insertion from master_stock.

4. **Data Consistency**
   - Ensure no duplicates, correct relationships.

### Tools
- Selenium for UI interactions, database assertions.

## User Acceptance Testing (UAT)

### Objectives
Validate the module meets user needs in a real environment.

### Test Scenarios
1. **Successful Import**
   - User exports CSV from Square.
   - Imports via UI.
   - Verifies reported counts and database updates.

2. **Edge Cases**
   - CSV with no matching stock_ids: Warnings logged.
   - Large CSV: Performance check.

3. **Failure Scenarios**
   - Invalid file: Error message displayed.
   - Partial updates: Data integrity maintained.

### Acceptance Criteria
- All stock_ids from master_stock inserted.
- Tokens updated accurately from CSV.
- Reports match actual counts.
- No data loss or corruption.

### Tools
- Manual testing with sample data, user feedback forms.