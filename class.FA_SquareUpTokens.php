<?php

/*******************************************
 * FA SquareUp Tokens Module
 * Imports Square tokens from CSV into FA database
 */

require_once( 'modules/ksf_modules_common/class.table_interface.php' );
require_once( 'modules/ksf_modules_common/class.generic_fa_interface.php' );

require_once( 'modules/ksf_modules_dao/src/ksf_ModulesDAO.php' );

// Autoloader for FA_SquareUpTokens classes
spl_autoload_register(function ($class) {
    if (strpos($class, 'FA_SquareUpTokens\\') === 0) {
        $path = str_replace('FA_SquareUpTokens\\', '', $class);
        $file = __DIR__ . '/src/' . str_replace('\\', '/', $path) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    } elseif (strpos($class, 'Ksfraser\\HTML\\') === 0) {
        $path = str_replace('Ksfraser\\HTML\\', '', $class);
        $file = __DIR__ . '/modules/ksfraser_html/src/Ksfraser/HTML/' . str_replace('\\', '/', $path) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    } elseif (strpos($class, 'Ksfraser\\ModulesDAO\\') === 0) {
        $path = str_replace('Ksfraser\\ModulesDAO\\', '', $class);
        $file = __DIR__ . '/modules/ksf_modules_dao/src/' . str_replace('\\', '/', $path) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    } elseif (strpos($class, 'Ksfraser\\Common\\') === 0) {
        $path = str_replace('Ksfraser\\Common\\', '', $class);
        $file = __DIR__ . '/modules/ksf_common/src/' . str_replace('\\', '/', $path) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

class FA_SquareUpTokens extends generic_fa_interface {
	var $lastoid;
	var $debug;
	var $table_interface;
	var $container;

	function __construct($pref_tablename)
	{
		parent::__construct( null, null, null, null, $pref_tablename );
		$this->tabs[] = array( 'title' => 'Import Completed', 'action' => 'form_import_completed', 'form' => 'form_import_completed', 'hidden' => TRUE );
		$this->tabs[] = array( 'title' => 'Import Square Tokens', 'action' => 'form_import', 'form' => 'form_import', 'hidden' => FALSE );
		$this->tabs[] = array( 'title' => 'Admin', 'action' => 'form_admin', 'form' => 'form_admin', 'hidden' => FALSE );
		//We could be looking for plugins here, adding menu's to the items.
		$this->add_submodules();
		$this->table_interface = new table_interface();
		$this->define_table();
		$this->setupContainer();

		return;
	}
	function action_show_form()
	{
		if (isset($_GET['download_missing']) && isset($_SESSION['missing_skus'])) {
			$this->downloadMissingSkus();
			return;
		}
		$this->install();
		parent::action_show_form();
	}
	function install()
	{
		$this->table_interface->create_table();
		parent::install();
	}
	function define_table()
	{
		$this->table_interface->fields_array[] = array('name' => 'last_updated', 'type' => 'timestamp', 'null' => 'NOT NULL', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->table_interface->fields_array[] = array('name' => 'stock_id', 'type' => 'varchar(255)' );
		$this->table_interface->fields_array[] = array('name' => 'square_token', 'type' => 'varchar(255)', 'unique' => true );

		$this->table_interface->table_details['tablename'] = $this->company_prefix . "square_tokens";
		$this->table_interface->table_details['primarykey'] = "stock_id";
	}
	function setupContainer()
	{
		$this->container = new \FA_SquareUpTokens\Container();
		$this->container->set('validator', function($c) {
			return new \FA_SquareUpTokens\CsvValidator();
		});
		$this->container->set('translator', function($c) {
			return new \FA_SquareUpTokens\ExceptionTranslator();
		});
		$this->container->set('dao', function($c) {
			return new \FA_SquareUpTokens\TokenDao();
		});
		$this->container->set('processor', function($c) {
			return new \FA_SquareUpTokens\CsvProcessor(
				$c->get('validator'),
				$c->get('translator'),
				$c->get('dao'),
				new \FA_SquareUpTokens\CsvRowFilter($c->get('dao'))
			);
		});
		$this->container->set('adminHandler', function($c) {
			return new \FA_SquareUpTokens\AdminActionHandler($c->get('dao'));
		});
	}
	function form_import()
	{
		try {
            /*
			$form = new \Ksfraser\HTML\HtmlElement('form', ['method' => 'post', 'enctype' => 'multipart/form-data']);
			$form->addChild(new \Ksfraser\HTML\HtmlElement('input', ['type' => 'file', 'name' => 'csv_file', 'accept' => '.csv']));
			$form->addChild(new \Ksfraser\HTML\HtmlElement('label', [], 'Skip stock IDs not in FA: '));
			$form->addChild(new \Ksfraser\HTML\HtmlElement('input', ['type' => 'checkbox', 'name' => 'skip_missing_skus', 'value' => '1']));
			$form->addChild(new \Ksfraser\HTML\HtmlElement('input', ['type' => 'submit', 'value' => 'Upload and Import']));
			echo $form->render();
            */
            start_form(true);
			start_table(TABLESTYLE2);
			table_section_title("Import Square Tokens from CSV");
			file_row(_("CSV File:"), 'csv_file', 'csv_file');
			check_row(_("Skip stock IDs not in FA:"), 'skip_missing_skus', null, false, _('If checked, stock IDs not found in FA will be skipped instead of added.'));
			end_table(1);
			submit_center('upload_import', _("Upload and Import"));
			end_form();
		} catch (\Exception $e) {
			display_error("Error rendering form: " . $e->getMessage());
		}
	}
	function form_import_completed()
	{
		try {
			$skipMissingSkus = isset($_POST['skip_missing_skus']) && $_POST['skip_missing_skus'] == '1';
			$processor = $this->container->get('processor');
			$counts = $processor->process($_FILES['csv_file']['tmp_name'], $skipMissingSkus);
			display_notification("Import completed: {$counts['updated']} updated, {$counts['skipped']} skipped, {$counts['not_in_table']} not in table.");

			if (!empty($counts['missing_in_fa'])) {
				display_warning("Warning: " . count($counts['missing_in_fa']) . " stock IDs from Square are not in FA. <a href='?download_missing=1'>Download CSV</a>");
				$_SESSION['missing_skus'] = $counts['missing_in_fa'];
			}
		} catch (\Exception $e) {
			display_error($e->getMessage());
		}
	}
	function form_admin()
	{
		try {
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$handler = $this->container->get('adminHandler');
				$handler->handle($_POST['action']);
			}
/*
			$form = new \Ksfraser\HTML\HtmlElement('form', ['method' => 'post']);
			$form->addChild(new \Ksfraser\HTML\HtmlElement('button', ['type' => 'submit', 'name' => 'action', 'value' => 'nullify'], 'Nullify All Tokens'));
			$form->addChild(new \Ksfraser\HTML\HtmlElement('button', ['type' => 'submit', 'name' => 'action', 'value' => 'insert'], 'Insert Stock IDs from Master Stock'));
			echo $form->render();
*/
            start_form(true);
			start_table(TABLESTYLE2);
			table_section_title("Admin Actions");
			end_table(1);
			submit_center_first('nullify', _("Nullify All Tokens"));
			submit_center_last('insert', _("Insert Stock IDs from Master Stock"));
			end_form();            *
		} catch (\Exception $e) {
			display_error("Error in admin form: " . $e->getMessage());
		}
	}

	function downloadMissingSkus()
	{
		if (!isset($_SESSION['missing_skus'])) {
			display_error("No missing SKUs to download.");
			return;
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="missing_skus.csv"');

		$output = fopen('php://output', 'w');
		fputcsv($output, ['Stock ID']);

		foreach ($_SESSION['missing_skus'] as $sku) {
			fputcsv($output, [$sku]);
		}

		fclose($output);
		unset($_SESSION['missing_skus']);
		exit;
	}
}

?>