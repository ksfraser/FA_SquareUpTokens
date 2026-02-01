# Technical Requirements

## Platform and Environment

- **FrontAccounting Version**: 2.3.22
- **PHP Version**: 7.3
- **Database**: MySQL (as used by FrontAccounting)

## Development Principles

### SOLID Principles
- **Single Responsibility Principle (SRP)**: Each class has one reason to change
- **Open/Closed Principle**: Classes open for extension, closed for modification
- **Liskov Substitution Principle**: Subtypes are substitutable for their base types
- **Interface Segregation Principle**: Clients not forced to depend on methods they don't use
- **Dependency Inversion Principle**: Depend on abstractions, not concretions

### Design Patterns and Practices
- **Dependency Injection (DI)**: Use constructor injection for dependencies
- **Avoid If/Switch Statements**: Use SRP classes and polymorphism instead of conditional logic where possible (following Martin Fowler's Replace Conditional with Polymorphism)
- **DRY (Don't Repeat Yourself)**: Use parent classes, traits, and composition
- **Composition over Inheritance**: Prefer composition where appropriate

## Code Quality

### Documentation
- **PHPDoc**: Comprehensive PHPDoc blocks for all classes, methods, and properties
- **Inline Comments**: Clear comments for complex logic
- **README**: Detailed usage instructions and API documentation

### HTML/UI Framework
- **ksfraser/HTML Library**: Used for all HTML generation instead of FA's built-in functions
- **Direct Instantiation Pattern**: HTML elements created with `new HtmlElement()` instead of builder chains
- **Avoid "Headers Already Sent" Issues**: No immediate echo output; all HTML generated as strings and output at once
- **Reusable Components**: Table classes for displaying data with edit/delete actions, Button classes for OK/Cancel operations
- **Composite Pattern**: Page objects containing tables, forms, fields, and buttons with recursive display() calls
- **SRP UI Components**: Complex UI sections extracted into dedicated classes implementing HTML library interfaces
- **Consistent UI**: All forms, tables, and UI elements generated through the library
- **Separation of Concerns**: UI generation separated from business logic

### Testing
- **Test-Driven Development (TDD)**: Write tests before implementing functionality (Red-Green-Refactor cycle)
- **Unit Tests**: 100% code coverage for all classes and methods
- **Edge Cases**: Test all boundary conditions, error scenarios, and invalid inputs
- **Mocking**: Use mocks/stubs for external dependencies (database, file system, etc.)
- **Test Frameworks**: PHPUnit for unit testing
- **Test Structure**: Tests in `tests/` directory with PHPUnit configuration
- **Coverage Reports**: HTML and text coverage reports generated automatically

### Interfaces and Contracts
- **Interfaces**: Define contracts for key components (Validators, Processors, etc.)
- **Abstract Classes**: Provide common implementations where appropriate
- **Traits**: Extract reusable functionality to avoid duplication

## Architecture

### Layered Architecture
- **Presentation Layer**: UI components and controllers
- **Business Logic Layer**: Domain services and validation
- **Data Access Layer**: DAO classes extending ksf_ModulesDAO
- **Infrastructure Layer**: External services (logging, file handling, etc.)

### Key Components
- **Validators**: Separate validation classes for CSV format and data integrity
- **Processors**: SRP classes for data processing and business logic
- **Factories**: For creating complex objects with dependencies
- **Utility Classes**: For data handling, reporting, and infrastructure
- **DAO Layer**: Wrap FA db_query calls with ksf_ModulesDAO (included as submodule from github.com/ksfraser/ksf_ModulesDAO), supporting transactions
- **Exceptions**: Custom exception hierarchy, wrapped in a translator for FA decoupling (using display_notification, etc.)

## User Acceptance Testing (UAT)

### Test Case Design
- **UI Test Cases**: Based on designed buttons and workflows
- **Integration Test Cases**: End-to-end scenarios
- **Error Handling Test Cases**: Invalid inputs, system failures
- **Performance Test Cases**: Large data sets, concurrent operations

### UAT Scenarios
- CSV import with various file formats and data conditions
- Review and edit functionality
- Bulk update operations
- Error reporting and recovery
- Programmatic API usage by external modules

## Diagrams and Documentation

### UML Diagrams
- **Entity-Relationship Diagram (ERD)**: Database schema and relationships
- **Class Diagrams**: System architecture and class relationships
- **Sequence Diagrams**: Message flow for key use cases
- **Activity Diagrams**: Logic flow charts for complex operations
- **Component Diagrams**: System components and dependencies

### Documentation Standards
- **API Documentation**: Complete API reference for programmatic interfaces
- **Deployment Guide**: Installation and configuration instructions
- **Troubleshooting Guide**: Common issues and solutions
- **Maintenance Guide**: Code structure and modification guidelines

## Implementation Roadmap

### Phase 1: Core Architecture
- Include ksf_ModulesDAO as a submodule: `git submodule add https://github.com/ksfraser/ksf_ModulesDAO.git modules/ksf_modules_dao`
- Include ksfraser/HTML as a submodule: `git submodule add https://github.com/ksfraser/html.git modules/ksfraser_html`
- Include ksfraser/Exceptions as a submodule: `git submodule add https://github.com/ksfraser/Exceptions.git modules/ksfraser_exceptions`
- Update submodules: `git submodule update --init --recursive`
- For UAT/Prod, use composer.json with VCS repositories:
  ```json
  {
    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/ksfraser/ksf_ModulesDAO.git"
      },
      {
        "type": "vcs",
        "url": "https://github.com/ksfraser/html.git"
      },
      {
        "type": "vcs",
        "url": "https://github.com/ksfraser/Exceptions.git"
      }
    ],
    "require": {
      "ksfraser/ksf_modules_dao": "dev-main",
      "ksfraser/html": "dev-main",
      "ksfraser/exceptions": "dev-main"
    }
  }
  ```
- Define interfaces and abstract classes
- Implement dependency injection container
- Create base validator and processor classes
- Implement exception translator for FA decoupling

### Phase 2: Business Logic
- Implement CSV processing with SRP classes
- Create validation pipeline
- Develop DAO layer with transaction management

### Phase 3: UI Layer
- Build controllers following MVC pattern
- Implement form handling and validation
- Create responsive UI components

### Phase 4: Testing and QA
- Write comprehensive unit tests using TDD (Red-Green-Refactor cycle)
- Create integration tests
- Develop UAT test scenarios
- Generate UML diagrams

### Phase 5: Documentation and Deployment
- Complete PHPDoc documentation
- Create user manuals and API docs
- Package as FA module: Create _init directory with module info text file (name, author, etc.), include sql/en_US-new.sql
- Final testing and validation

## Quality Assurance

### Code Review Checklist
- SOLID principles compliance
- PHPDoc completeness
- Test coverage verification
- Security considerations
- Performance implications

### Continuous Integration
- Automated testing on commits
- Code quality checks (PHPStan, PHPMD)
- Dependency vulnerability scanning
- Documentation generation

## Security Considerations

- Input validation and sanitization
- SQL injection prevention (parameterized queries)
- Access control integration with FrontAccounting
- Audit logging for all operations
- Data integrity checks

## Performance Requirements

- Efficient database queries with proper indexing
- Memory-efficient processing of large CSV files
- Transaction management for data consistency
- Caching where appropriate for repeated operations