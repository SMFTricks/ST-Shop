<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Manage;

use Shop\Shop;
use Shop\Helper\Database;
use Shop\Helper\Import;

if (!defined('SMF'))
	die('No direct access...');

class Maintenance extends Dashboard
{
	private $_select_shop = [];

	private $_convert_from;

	private $_import;

	private $_importModel;

	/**
	 * Maintenance::__construct()
	 *
	 * Call certain administrative hooks and load the language files
	 */
	function __construct()
	{
		// Required languages
		loadLanguage('Shop/Shop');

		// Array of sections
		$this->_subactions = [
			'import' => 'import',
			'importdo' => 'import_do',
		];
		$this->_sa = isset($_GET['sa'], $this->_subactions[$_GET['sa']]) ? $_GET['sa'] : 'import';

		// Namespace
		$this->_import = 'Shop\\Helper\\Import\\';
	}

	public function main()
	{
		global $context;

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => Shop::getText('tab_maint') . ' - ' . Shop::getText('maint_import'),
			'description' => Shop::getText('maint_import_desc'),
			'tabs' => [
				'import' => ['description' => Shop::getText('maint_import_desc')],
			],
		];
		call_helper(__CLASS__ . '::' . $this->_subactions[$this->_sa] . '#');
	}

	public function import()
	{
		global $context;

		// Template
		loadTemplate('Shop/ShopAdmin');

		// Info
		$context['page_title'] = Shop::getText('tab_maint') . ' - ' . Shop::getText('maint_import');
		$context['sub_template'] = 'import';

		// Let's try to find what mod they had installed
		$this->discover();

		// From what mod?
		$context['shop_convert_from'] = $this->_convert_from;
		$context['shop_convert_data'] = $this->_select_shop;
	}

	public function discover()
	{
		// SMF Shop
		$this->_select_shop['smfshop'] = Database::list_columns('shop_items');
		if (!isset($this->_select_shop['smfshop']['stock']) || empty($this->_select_shop['smfshop']))
			unset($this->_select_shop['smfshop']);
		else
			$this->_convert_from = 'SMFShop';
		// ST Shop
		$this->_select_shop['stshop'] = Database::list_columns('shop_items');
		if (!isset($this->_select_shop['stshop']['count']) || empty($this->_select_shop['stshop']))
			unset($this->_select_shop['stshop']);
		else
			$this->_convert_from = 'STShop';
		// SA Shop
		$this->_select_shop['sashop'] = Database::list_columns('shop_item');
		if (empty($this->_select_shop['sashop']))
			unset($this->_select_shop['sashop']);
		else
			$this->_convert_from = 'SAShop';
	}

	public function import_do()
	{
		global $context, $user_info, $modSettings;

		$context['page_title'] = Shop::getText('tab_maint') . ' - '. Shop::getText('maint_import');
		$context[$context['admin_menu_name']]['current_subsection'] = 'import';

		// Make sure only admins
		if (empty($user_info['is_admin']))
			fatal_error(Shop::getText('import_only_admin'), false);

		// You already imported
		if (!empty($modSettings['Shop_importer_success']))
			fatal_error(Shop::getText('error_import_data'), false);

		checkSession();
		validateSession();

		// We got import select?
		$this->_convert_from = isset($_REQUEST['convert_from']) && !empty($_REQUEST['convert_from']) ? $_REQUEST['convert_from'] : 'SMFShop';
		$this->_import .= $this->_convert_from;

		// Create the respective model
		$this->_importModel = new $this->_import;

		// It's valid?
		if (empty($this->_importModel->Verify()))
			fatal_error(Shop::getText('import_empty'), false);
		// Time to delete everything then...
		else
			$this->_importModel->DropTables();

		// We only import if we have items, otherwise it's pointless
		if (!empty($this->_importModel->CountItems()))
		{
			// Items
			$context['shop_found']['items_total'] = $this->_importModel->countItems();
			$context['shop_imported']['items_total'] = $this->_importModel->importItems();

			// If we imported items, then we could try to do the rest
			if (!empty($this->_importModel->importItems()))
			{
				// Categories
				if (!empty($this->_importModel->countCategories()))
				{
					$context['shop_found']['cats_total'] = $this->_importModel->countCategories();
					$context['shop_imported']['cats_total'] = $this->_importModel->importCategories();
				}

				// Inventory
				if (!empty($this->_importModel->countInventory()))
				{
					$context['shop_found']['inventory_total'] = $this->_importModel->countInventory();
					$context['shop_imported']['inventory_total'] = $this->_importModel->importInventory();
				}
			}
		}

		// Do the money next
		$context['shop_imported']['cash_total'] = $this->_importModel->importMoney();

		// Convert board settings
		$context['shop_imported']['boards_total'] = $this->_importModel->importBoardSettings();

		// Convert shop settings
		$context['shop_imported']['settings_total'] = $this->_importModel->importSettings();

		// Update the settings for the converter
		// updateSettings(['Shop_importer_success' => 1]);

		// Template
		loadTemplate('Shop/ShopAdmin');

		// Info
		$context['page_title'] = Shop::getText('tab_maint') . ' - ' . Shop::getText('maint_import');
		$context['sub_template'] = 'import_results';
	}
}