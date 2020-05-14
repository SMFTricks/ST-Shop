<?php

/**
 * @package ST Shop
 * @version 3.2
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Manage;

use Shop\Shop;
use Shop\Helper;
use Shop\Modules as Module_Template;

if (!defined('SMF'))
	die('No direct access...');

class Modules
{
	private static $item_module = 'Shop\\Modules\\';
	private static $fields_data = [];
	private static $fields_type = [];

	public function main()
	{
		global $context;

		loadLanguage('Shop/Errors');
		loadLanguage('Shop/Shop');

		$subactions = [
			'index' => 'index',
			'upload' => 'upload',
			'upload2' => 'upload2',
			'delete' => 'delete',
			'delete2' => 'delete2',
		];

		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'index';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => Shop::getText('tab_modules'),
			'description' => Shop::getText('tab_modules_desc'),
			'tabs' => [
				'index' => ['description' => Shop::getText('tab_modules_desc')],
				'upload' => ['description' => Shop::getText('modules_upload_desc')],
			],
		];
		call_helper(__CLASS__ . '::' . $subactions[$sa]);
	}

	public function index()
	{
		global $context, $sourcedir, $modSettings;

		require_once($sourcedir . '/Subs-List.php');
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'moduleslist';
		$context['page_title'] = Shop::getText('tab_settings') . ' - ' . Shop::getText('tab_modules');

		// The entire list
		$listOptions = [
			'id' => 'moduleslist',
			'title' => Shop::getText('tab_modules'),
			'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
			'base_href' => '?action=admin;area=shopmodules;sa=index',
			'default_sort_col' => 'item_name',
			'get_items' => [
				'function' => 'Shop\Helper\Database::Get',
				'params' => ['shop_modules AS sm', Helper\Database::$modules],
			],
			'get_count' => [
				'function' => 'Shop\Helper\Database::Count',
				'params' => ['shop_modules AS sm', Helper\Database::$modules],
			],
			'no_items_label' => Shop::getText('no_modules'),
			'no_items_align' => 'center',
			'columns' => [
				'item_name' => [
					'header' => [
						'value' => Shop::getText('item_name'),
						'class' => 'lefttext',
					],
					'data' => [
						'db' => 'name',
						'style' => 'width: 12%',
					],
					'sort' =>  [
						'default' => 'name ASC',
						'reverse' => 'name DESC',
					],
				],
				'description' => [
					'header' => [
						'value' => Shop::getText('item_description'),
						'class' => 'lefttext',
					],
					'data' => [
						'db' => 'description',
						'style' => 'width: 20%',
					],
					'sort' =>  [
						'default' => 'description DESC',
						'reverse' => 'description',
					],
				],
				'class' => [
					'header' => [
						'value' => Shop::getText('module_class'),
					],
					'data' => [
						'db' => 'file',
						'style' => 'width: 5%',
					],
					'sort' =>  [
						'default' => 'file DESC',
						'reverse' => 'file',
					],
				],
				'delete' => [
					'header' => [
						'value' => Shop::getText('delete', false). ' <input type="checkbox" onclick="invertAll(this, this.form, \'delete[]\');" class="input_check" />',
						'class' => 'centertext',
					],
					'data' => [
						'sprintf' => [
							'format' => '<input type="checkbox" name="delete[]" value="%1$d" class="check" />',
							'params' => [
								'id' => false,
							],
						],
						'class' => 'centertext',
						'style' => 'width: 3%',
					],
				],
			],
			'form' => [
				'href' => '?action=admin;area=shopmodules;sa=delete',
				'hidden_fields' => [
					$context['session_var'] => $context['session_id'],
				],
				'include_sort' => true,
				'include_start' => true,
			],
			'additional_rows' => [
				'submit' => [
					'position' => 'below_table_data',
					'value' => '<input type="submit" size="18" value="'.Shop::getText('delete', false). '" class="button" />',
				],
			],
		];
		// Info?
		if (isset($_REQUEST['deleted']) || isset($_REQUEST['added']))
		{
			$listOptions['additional_rows']['updated'] = [
				'position' => 'top_of_list',
				'value' => '<div class="infobox">',
			];
			$listOptions['additional_rows']['updated']['value'] .= Shop::getText('module_' . (!isset($_REQUEST['deleted']) ? 'added' : 'deleted')) . '</div>';
		}
		// Let's finishem
		createList($listOptions);
	}

	public function upload()
	{
		global $context;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('tab_modules') . ' - '. Shop::getText('modules_upload');
		$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];
		loadTemplate('Shop/ShopAdmin');
		$context['sub_template'] = 'upload';
	}

	public function upload2()
	{
		global $context, $sourcedir;

		// Page stuff
		$context['page_title'] = Shop::getText('tab_modules') . ' - '. Shop::getText('modules_upload');
		$context[$context['admin_menu_name']]['current_subsection'] = 'upload';

		// Only admins can upload modules
		if (empty($context['user']['is_admin']))
			fatal_error($txt['Shop_modules_only_admin'], false);

		// No file? That can't be
		if (!isset($_FILES['newitem']) || empty($_FILES['newitem']))
			fatal_error(Shop::getText('file_error_empty'), false);

		checkSession();

		$fail = true;
		$filename = '';
		$upload = false;

		// Upload File Form
		if (isset($_FILES['newitem']['name']) && $_FILES['newitem']['name'] != '') {
			$filename = $_FILES['newitem']['name'];
			$filesize = $_FILES['newitem']['size'];
			$upload = true;
			$fail = false;
		}

		if ($fail == false)
		{
			// Copy the file
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$filename = str_replace(('.'.$ext), '', $filename);
			$target_file = $sourcedir . Shop::$modulesdir . basename($_FILES['newitem']['name']);

			// Allow certain file formats
			if ($ext != 'php') {
				fatal_error(Shop::getText('file_error_type2'), false);
				$uploadOk = false;
			}
			// Check if file already exists
			if (file_exists($target_file)) {
				fatal_error(Shop::getText('file_already_exists'), false);
				$upload = false;
			}
			// Check file size
			if ($_FILES['newitem']['size'] > 50000) {
				fatal_error(Shop::getText('file_too_large'), false);
				$upload = false;
			}

			if ($upload == true)
			{
				if (move_uploaded_file($_FILES['newitem']['tmp_name'], $target_file))
				{
					self::$item_module .= $filename;

					// Did we get the module in place?
					if (!class_exists(self::$item_module))
					{
						unlink($sourcedir . Shop::$modulesdir . basename($filename. '.php'));
						fatal_error(sprintf(Shop::getText('module_cant_instance'), Shop::$modulesdir . $filename), false);
					}
					// Let's use it!
					else
					{
						// Create the instance
						$itemModel = new self::$item_module;

						// Data
						self::$fields_data = [
							'name' => (string) Helper\Database::sanitize($itemModel->name),
							'description' => (string) Helper\Database::sanitize($itemModel->desc),
							'price' => (int) $itemModel->price,
							'author' => (string) Helper\Database::sanitize($itemModel->authorName),
							'email' => (string) Helper\Database::sanitize($itemModel->authorEmail),
							'web' => (string) Helper\Database::sanitize($itemModel->authorWeb),
							'require_input' => (int) $itemModel->require_input,
							'can_use_item' => (int) $itemModel->can_use_item,
							'editable_input' => (int) $itemModel->addInput_editable,
							'file' => (string) Helper\Database::sanitize($filename),
						];
						// Type
						foreach(self::$fields_data as $column => $type)
							self::$fields_type[$column] = str_replace('integer', 'int', gettype($type));

						// Insert the module in the database
						Helper\Database::Insert('shop_modules', self::$fields_data, self::$fields_type);
					
						// Get me out of here
						redirectexit('action=admin;area=shopmodules;sa=upload;success');
					}
				}
			}
		}
		else
			redirectexit('action=admin;area=shopmodules;sa=upload;error');
	}

	public function delete()
	{
		global $context;

		// Template...
		loadTemplate('Shop/ShopAdmin');

		// Set all the page stuff
		$context['page_title'] = Shop::getText('tab_modules') . ' - '. Shop::getText('modules_delete');
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => Shop::getText('modules_delete_desc'),
		);
		$context['sub_template'] = 'delete';
		$context['delete_description'] = Shop::getText('module_delete_also');

		checkSession();

		// If nothing was chosen to delete
		if (!isset($_REQUEST['delete']) || empty($_REQUEST['delete']))
			fatal_error(Shop::getText('item_delete_error'), false);

		// Make sure all IDs are numeric
		foreach ($_REQUEST['delete'] as $key => $value)
			$_REQUEST['delete'][$key] = (int) $value;

		// We want to delete these items?
		$context['shop_delete'] = Helper\Database::Get(0, 1000, 'sm.name', 'shop_modules AS sm', Helper\Database::$modules, 'WHERE sm.id IN ({array_int:delete})', false, '', ['delete' => $_REQUEST['delete']]);

		// Set the format
		foreach ($context['shop_delete'] as $id => $var)
			$context['shop_delete'][$id]['itemid'] = $var['id'];
	}

	public static function delete2()
	{
		global $context;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('tab_modules') . ' - '. Shop::getText('modules_delete');
		checkSession();

		// If nothing was chosen to delete (shouldn't happen, but meh)
		if (!isset($_REQUEST['delete']))
			fatal_error(Shop::getText('item_delete_error'), false);

		// Items using this module are... no longer using it
		Helper\Delete::modules($_REQUEST['delete'], 'action=admin;area=shopmodules;sa=index;deleted');
	}
}