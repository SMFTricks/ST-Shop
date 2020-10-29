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

class SMFShop extends Import
{
	public $_insert = [];

	public $_result = [];

	public $_items_columns = [
		's.id',
		's.name',
		's.image',
		's.desc',
		's.price',
		's.stock',
		's.module',
		's.info1',
		's.info2',
		's.info3',
		's.info4',
		's.input_needed',
		's.can_use_item',
		's.delete_after_use',
		's.category',
	];

	public $_cats_columns = [
		'c.id',
		'c.name',
		'c.count',
	];

	public function Verify()
	{
		return (!empty(Database::list_columns('shop_items')) ? true : false);
	}

	public function importItems()
	{
		$this->_insert = [];
		$this->_result = [];
		$this->_result = Database::Get(0, 100000, 's.id', 'shop_items AS s', $this->_items_columns);

		foreach ($this->_result as $row)
			$this->_insert[$row['id']] = [
				'id' => $row['id'],
				'name' => $row['name'],
				'image' => $row['image'],
				'desc' => $row['desc'],
				'price' => $row['price'],
				'stock' => $row['stock'],
				'module' => $row['module'],
				'info1' => $row['info1'],
				'info2' => $row['info2'],
				'info3' => $row['info3'],
				'info4' => $row['info4'],
				'input' => $row['input_needed'],
				'can' => $row['can_use_item'],
				'delete' => $row['delete_after_use'],
				'catid' => $row['category'],
			];
		
		// Insert
		return $this->insertItems($this->_insert);
	}

	public function countItems()
	{
		return Database::Count('shop_items', ['id']);
	}

	public function importCategories()
	{
		$this->_insert = [];
		$this->_result = [];
		$this->_result = Database::Get(0, 100000, 'c.id', 'shop_categories AS c', $this->_cats_columns);

		foreach ($this->_result as $row)
			$this->_insert[$row['id']] = [
				'id' => $row['id'],
				'name' => $row['name'],
			];

		// Insert
		return $this->insertCategories($this->_insert);
	}

	public function countCategories()
	{
		return Database::Count('shop_categories', ['id']);
	}
}