<?php
/**********************************************
Name: FA SquareUp Tokens Import
For FrontAccounting 2.3.22
Free software under GNU GPL
***********************************************/

$page_security = 'SA_FA_SQUAREUPTOKENS';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();
set_ext_domain('modules/FA_SquareUpTokens');

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

error_reporting(E_ALL);
ini_set("display_errors", "on");

global $db; // Allow access to the FA database connection
$debug_sql = 0;  // Change to 1 for debug messages

include_once($path_to_root . "/modules/FA_SquareUpTokens/class.FA_SquareUpTokens.php");
require_once( 'FA_SquareUpTokens.inc.php' );
$fa_squareuptokens = new FA_SquareUpTokens( FA_SQUAREUPTOKENS_PREFS );
$found = $fa_squareuptokens->is_installed();
$fa_squareuptokens->set_var( 'found', $found );
$fa_squareuptokens->set_var( 'help_context', "Import Square Tokens" );
$fa_squareuptokens->set_var( 'redirect_to', "FA_SquareUpTokens.php" );
$fa_squareuptokens->run();

?>