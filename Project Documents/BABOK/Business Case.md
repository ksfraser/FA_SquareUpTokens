# Business Case

## Project Overview
The purpose of this module is to import Square Up tokens from an inventory library export into our tables so that another module can use those tokens for updating the Square library. For our purposes, we need the stock_id and the square_token. Integration of SquareUp payment tokens with FrontAccounting 2.3 system.

## Problem Statement
Current system lacks efficient import mechanism for Square Up tokens from inventory exports, hindering the ability to update the Square library seamlessly.

## Proposed Solution
Develop a module to import Square Up tokens, storing stock_id and square_token in the defined table, enabling subsequent updates to the Square library. The module will delete existing tokens first to avoid errors from stale tokens, as Square changes tokens over time.

## Scope
- Sole purpose: Upload the Square export CSV and update the token table.
- Does not perform any further actions, such as updating Square from FrontAccounting.
- Other modules are responsible for using the tokens to update Square, etc., from FA (out of scope for this module).

## Benefits
- Enables seamless integration with Square for catalog updates via other modules

## Costs
- Development time

## Risks
- Compatibility with PHP 7.3 and FA 2.3

## Success Criteria
- Successful token generation and processing
- No disruption to existing workflows