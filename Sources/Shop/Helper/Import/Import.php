<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Helper\Import;

use Shop\Helper\Database;

if (!defined('SMF'))
	die('No direct access...');

abstract class Import
{
	public $_tables = ['stshop_items', 'stshop_inventory', 'stshop_categories'];

	public $_insert = [];

	public $_types;

	public $_total_imported;

	public $_shop_items = [];

	public $shop_categories = [];

	public $_shop_inventory = [];

	public $_shop_buy_log;

	abstract public function Verify();

	abstract public function importItems();

	abstract public function countItems();

	abstract public function importCategories();

	abstract public function countCategories();

	public function DropTables()
	{
		foreach ($this->_tables as $table)
			Database::Truncate($table);
	}

	public function insertItems($items)
	{
		global $smcFunc;

		$this->_insert = [];
		$this->_types = [];
		$this->_total_imported = 0;

		foreach($items as $id_item => $item)
			$this->_shop_items[] = [
				'itemid' => (int) $id_item,
				'name' => (string) $item['name'],
				'image' => (string) $item['image'],
				'description' => (string) $item['desc'],
				'price' => (int) $item['price'],
				'stock' => (int) $item['stock'],
				'module' => (int) $item['module'],
				'info1' => (int) $item['info1'],
				'info2' => (int) $item['info2'],
				'info3' => (int) $item['info3'],
				'info4' => (int) $item['info4'],
				'input_needed' => (int) $item['input'],
				'can_use_item' => (int) $item['can'],
				'delete_after_use' => (int) $item['delete'],
				'catid' => (int) $item['catid'],
				'status'=> (int) isset($item['status']) ? $item['status'] : 1,
				'itemlimit' => (int) isset($item['itemlimit']) ? $item['itemlimit'] : 0
			];

		// Import these items and count them again
		if (!empty($this->_shop_items))
		{
			// Type
			foreach($this->_shop_items[0] as $column => $type)
				$this->_types[$column] = str_replace('integer', 'int', gettype($type));

			// Insert the items into the database
			Database::Insert('stshop_items', $this->_shop_items, $this->_types, ['itemid'], 'replace');

			// Get our total of items imported
			$this->_total_imported = $smcFunc['db_affected_rows']();
		}

		return $this->_total_imported;
	}

	public function insertCategories($categories)
	{
		global $smcFunc;

		$this->_insert = [];
		$this->_types = [];
		$this->_total_imported = 0;

		foreach($categories as $id_cat => $cat)
			$this->shop_categories[] = [
				'catid' => (int) $id_cat,
				'name' => (string) $cat['name'],
				'image' => (string) isset($cat['image']) ? $cat['image'] : 'blank.gif',
				'description' => (string) isset($cat['desc']) ? $cat['desc'] : '',
			];

		// Import these items and count them again
		if (!empty($this->shop_categories))
		{
			// Type
			foreach($this->shop_categories[0] as $column => $type)
				$this->_types[$column] = str_replace('integer', 'int', gettype($type));

			// Insert the items into the database
			Database::Insert('stshop_categories', $this->shop_categories, $this->_types, ['catid'], 'replace');

			// Get our total of items imported
			$this->_total_imported = $smcFunc['db_affected_rows']();
		}

		return $this->_total_imported;
	}
}