# Use Cases

## Use Case: Import Square Tokens

### Actors
- User (e.g., administrator or accountant)

### Preconditions
- User has exported the catalog from Square as a CSV file (e.g., 2XJWG21S422RM_catalog-2026-01-28-1936.csv).
- CSV contains at least "SKU" (mapped to stock_id) and "Token" (mapped to square_token) columns; other columns are ignored.
- Module UI is accessible.

### Main Flow
1. User selects the CSV file via the module's UI screen.
2. User optionally checks the "Skip stock IDs not in FA" checkbox (default: unchecked).
3. User initiates the import process.
4. System begins a database transaction.
5. System sets square_token to NULL for ALL stock_ids in the square_tokens table.
6. System inserts (with ignore) ALL stock_ids from the master_stock table into the square_tokens table.
7. System parses the CSV, extracting SKU as stock_id and Token as square_token.
8. System updates the square_token in the database for matching stock_ids from the CSV.
9. System commits the transaction.
10. System reports the total number of stock_ids in the table after insertion from master_stock.
11. System reports the number of tokens updated from the CSV, skipped rows (e.g., blank SKU), and stock_ids in CSV not found in the table.
12. If there are stock_ids in CSV not found in the table and "Skip" was unchecked, system generates a warning and provides a downloadable CSV listing the missing stock_ids.
13. System confirms successful import or reports errors.

### Postconditions
- Database updated with new square_tokens where applicable.
- All stock_ids from master_stock are present in the table.

### Alternative Flows
- If CSV is invalid (missing required columns: SKU and Token), display error and abort (rollback transaction).
- If CSV is empty, display error and abort (rollback transaction).
- If duplicate SKUs in CSV, use the first occurrence (filter out subsequent), note for review, and proceed.
- If SKU is blank, skip the row and note for review.
- If stock_id from CSV not in table:
  - If "Skip stock IDs not in FA" is checked: Skip the row and note for review.
  - If "Skip stock IDs not in FA" is unchecked: Process the row (insert token), note for review, and include in downloadable CSV of missing stock_ids.

### Exceptions
- File upload fails: Notify user.
- Database errors: Rollback transaction and notify user.

## Use Case: Admin Actions

### Actors
- Administrator

### Preconditions
- Module UI is accessible.
- User has admin privileges.

### Main Flow (Nullify Tokens)
1. User accesses the "admin" screen.
2. User selects "Nullify All Tokens" action.
3. System displays a popup warning: "Are you sure you want to nullify all square_tokens? This action cannot be undone."
4. User confirms.
5. System begins a database transaction.
6. System sets square_token to NULL for ALL stock_ids in the square_tokens table.
7. System commits the transaction.
8. System reports success.

### Main Flow (Insert Stock IDs)
1. User accesses the "admin" screen.
2. User selects "Insert Stock IDs from Master Stock" action.
3. System begins a database transaction.
4. System inserts (with ignore) ALL stock_ids from the master_stock table into the square_tokens table.
5. System commits the transaction.
6. System reports the number of stock_ids inserted.