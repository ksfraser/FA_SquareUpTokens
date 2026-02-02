-- SQL script for creating the square_tokens table in FrontAccounting module
-- The 0_ prefix will be replaced by FA with the actual table prefix (e.g., 1_)

CREATE TABLE 0_square_tokens (
  stock_id varchar(255) COLLATE latin1_swedish_ci NOT NULL,
  square_token varchar(255) COLLATE latin1_swedish_ci DEFAULT NULL UNIQUE,
  last_updated timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  KEY stock_id (stock_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;