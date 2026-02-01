# System Architecture

## Overview
The FA SquareUp Tokens module follows a layered architecture with separation of concerns, using dependency injection and SRP classes. The module integrates with FrontAccounting as a standard extension, providing UI screens for importing tokens and administrative actions.

## UI Screens

### Import Screen
- **Purpose**: Allow users to upload a Square catalog CSV and import tokens into the database.
- **Components**:
  - File input field for selecting the CSV file.
  - Upload/Import button to initiate the process.
  - Progress feedback and error messages.
- **Workflow**: User selects file, clicks upload, system processes in transaction, displays results.

### Admin Screen
- **Purpose**: Provide administrative tools for managing the token table.
- **Components**:
  - Button to "Nullify All Tokens" with a confirmation popup ("Are you sure?").
  - Button to "Insert Stock IDs from Master Stock".
  - Feedback messages for actions.
- **Workflow**: User selects action, confirms if needed, system performs operation, displays results.

## Database Schema
- Table: `0_square_tokens` (replaced with `1_square_tokens` in FA)
- Fields:
  - `stock_id` (varchar 255, indexed)
  - `square_token` (varchar 255, unique)
  - `last_updated` (timestamp, auto-update)

## Component Diagram
- **Presentation Layer**: UI screens using ksfraser/HTML Library.
- **Business Logic Layer**: Validators, Processors for import and admin logic.
- **Data Access Layer**: DAO wrapper for FA db_query, with transaction support.
- **Infrastructure Layer**: Exception translator, file handling.

## Sequence Diagrams
- Import Process: User → UI → Processor → DAO → DB (errors bubble back to UI)
- Admin Actions: User → UI → Processor → DAO → DB (errors bubble back to UI)

## Security
- Access controlled via FA hooks, restricted to authorized users.
- Admin actions may require higher privileges.