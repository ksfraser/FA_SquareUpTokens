# Testing Plan

## Unit Tests

### Objectives
Test individual components in isolation to ensure correctness.

### Test Cases
1. **CSV Parsing**
   - Input: Sample CSV with stock_id and square_token columns.
   - Expected: Correct extraction of data pairs.
   - Edge cases: Missing columns, extra columns, malformed data.

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

### Tools
- PHPUnit for PHP unit testing.

## Integration Tests

### Objectives
Test interactions between components, including database and UI.

### Test Cases
1. **Full Import Process**
   - Simulate UI file upload.
   - Process CSV, perform nulling, insertion, updates.
   - Verify database state and reported counts.

2. **Error Handling**
   - Invalid CSV: Check error reporting.
   - Database connection failure: Rollback and notify.

3. **Data Consistency**
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