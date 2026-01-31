# Use Cases

## Use Case: Import Square Tokens

### Actors
- User (e.g., administrator or accountant)

### Preconditions
- User has exported the library from Square as a CSV file.
- CSV contains at least stock_id and square_token columns.
- Module UI is accessible.

### Main Flow
1. User selects the CSV file via the module's UI screen.
2. User initiates the import process.
3. System nulls the square_token for ALL stock_ids in the relevant table.
4. System inserts ALL stock_ids from the master_stock table into the relevant table.
5. System parses the CSV, extracting stock_id and square_token.
6. System updates the square_token in the database for matching stock_ids from the CSV.
7. System reports the total number of stock_ids in the table after insertion from master_stock.
8. System reports the number of tokens inserted/updated from the CSV.
9. System confirms successful import or reports errors.

### Postconditions
- Database updated with new square_tokens where applicable.
- All stock_ids from master_stock are present in the table.

### Alternative Flows
- If CSV is invalid (missing columns), display error and abort.
- If no matching stock_ids, log warnings but proceed.

### Exceptions
- File upload fails: Notify user.
- Database errors: Rollback changes and notify user.