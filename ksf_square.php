<?php
/**********************************************
Name: COAST purchase order export
modified for COAST 1.5.1 and FrontAccounting 2.3.15 by kfraser 
Free software under GNU GPL
***********************************************/

$page_security = 'SA_ksf_square';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();
set_ext_domain('modules/ksf_square');

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
//include_once($path_to_root . "/modules/ksf_modules_common/class.eventloop.php");

error_reporting(E_ALL);
ini_set("display_errors", "on");

global $db; // Allow access to the FA database connection
$debug_sql = 0;  // Change to 1 for debug messages
//global $prefsDB;	//defined in class.ksf_square.php
	include_once($path_to_root . "/modules/ksf_square/class.ksf_square.php");
	require_once( 'ksf_square.inc.php' );
	$coastc = new ksf_square( KSF_QOH_PREFS );
	$found = $coastc->is_installed();
	$coastc->set_var( 'found', $found );
	$coastc->set_var( 'help_context', "Generate Catalogue" );
	$coastc->set_var( 'redirect_to', "ksf_square.php" );
	$coastc->run();


?>
