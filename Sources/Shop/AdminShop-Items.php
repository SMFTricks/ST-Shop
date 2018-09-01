<?php

/**
 * @package ST Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

function Shop_adminItems()
{
	global $context, $txt;

	loadTemplate('ShopAdmin');

	$context['items_url'] = Shop::$itemsdir . '/';

	$subactions = array(
		'index' => 'Shop_itemsIndex',
		'add' => 'Shop_itemsAdd',
		'add2' => 'Shop_itemsAdd2',
		'add3' => 'Shop_itemsAdd3',
		'edit' => 'Shop_itemsEdit',
		'edit2' => 'Shop_itemsEdit2',
		'delete' => 'Shop_itemsDelete',
		'delete2' => 'Shop_itemsDelete2',
		'uploaditems' => 'Shop_itemsUpload',
		'uploaditems2' => 'Shop_itemsUpload2',
	);

	$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'index';

	// Create the tabs for the template.
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['Shop_tab_items'],
		'description' => $txt['Shop_tab_items_desc'],
		'tabs' => array(
			'index' => array('description' => $txt['Shop_tab_items_desc']),
			'add' => array('description' => $txt['Shop_items_add_desc']),
			'uploaditems' => array('description' => $txt['Shop_items_uploaditems_desc']),
		),
	);

	$subactions[$sa]();
}

function Shop_itemsIndex()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'itemslist';
	$context['page_title'] = $txt['Shop_tab_items']. ' - ' . $txt['Shop_tab_items'];

	// The entire list
	$listOptions = array(
		'id' => 'itemslist',
		'title' => $txt['Shop_tab_items'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shopitems;sa=index',
		'default_sort_col' => 'modify',
		'get_items' => array(
			'function' => 'Shop_itemsGet',
		),
		'get_count' => array(
			'function' => 'Shop_itemsCount',
		),
		'no_items_label' => $txt['Shop_no_items'],
		'no_items_align' => 'center',
		'columns' => array(
			'item_image' => array(
				'header' => array(
					'value' => $txt['Shop_item_image'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ return Shop::Shop_imageFormat($row['image']);},
					'style' => 'width: 4%',
					'class' => 'centertext',
				),
			),
			'item_name' => array(
				'header' => array(
					'value' => $txt['Shop_item_name'],
					'class' => 'lefttext',
				),
				'data' => array(
					'db' => 'name',
					'style' => 'width: 12%',
				),
				'sort' =>  array(
					'default' => 'name DESC',
					'reverse' => 'name',
				),
			),
			'price' => array(
				'header' => array(
					'value' => $txt['Shop_item_price'],
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'price',
					'style' => 'width: 4%',
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'price DESC',
					'reverse' => 'price',
				),
			),
			'stock' => array(
				'header' => array(
					'value' => $txt['Shop_item_stock'],
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'count',
					'style' => 'width: 3%',
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'count DESC',
					'reverse' => 'count',
				),
			),
			'category' => array(
				'header' => array(
					'value' => $txt['Shop_item_category'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ global $txt; return ($row['catid'] != 0 ? $row['category'] : $txt['Shop_item_uncategorized']);},
					'style' => 'width: 6%',
				),
				'sort' =>  array(
					'default' => 'category DESC',
					'reverse' => 'category',
				),
			),
			'module' => array(
				'header' => array(
					'value' => $txt['Shop_item_module'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ global $txt; return (!empty($row['module']) ? $row['module'] : $txt['Shop_items_na']);},
					'style' => 'width: 5%',
				),
				'sort' =>  array(
					'default' => 'module DESC',
					'reverse' => 'module',
				),
			),
			'status' => array(
				'header' => array(
					'value' => $txt['Shop_item_status'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ return ($row['status'] == 1 ? '<span class="generic_icons warning_watch"></span>' : '<span class="generic_icons warning_mute"></span>');},
					'style' => 'width: 1%',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'status DESC',
					'reverse' => 'status',
				)
			),
			'modify' => array(
				'header' => array(
					'value' => $txt['Shop_item_modify'],
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl. '?action=admin;area=shopitems;sa=edit;id=%1$d">'. $txt['Shop_item_modify']. '</a>',
						'params' => array(
							'itemid' => true,
						),
					),
					'style' => 'width: 5%',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'itemid DESC',
					'reverse' => 'itemid',
				)
			),
			'delete' => array(
				'header' => array(
					'value' => $txt['delete']. ' <input type="checkbox" onclick="invertAll(this, this.form, \'delete[]\');" class="input_check" />',
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<input type="checkbox" name="delete[]" value="%1$d" class="check" />',
						'params' => array(
							'itemid' => false,
						),
					),
					'class' => 'centertext',
					'style' => 'width: 3%',
				),
			),
		),
		'form' => array(
			'href' => '?action=admin;area=shopitems;sa=delete',
			'hidden_fields' => array(
				$context['session_var'] => $context['session_id'],
			),
			'include_sort' => true,
			'include_start' => true,
		),
		'additional_rows' => array(
			'submit' => array(
				'position' => 'below_table_data',
				'value' => '<input type="submit" size="18" value="'.$txt['delete']. '" class="button" />',
			),
			'updated' => array(
				'position' => 'top_of_list',
				'value' => (!isset($_REQUEST['deleted']) ? (!isset($_REQUEST['added']) ? (!isset($_REQUEST['updated']) ? '' : '<div class="infobox">'. $txt['Shop_items_updated']. '</div>') : '<div class="infobox">'. $txt['Shop_items_added']. '</div>') : '<div class="infobox">'. $txt['Shop_items_deleted']. '</div>'),
			),
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_itemsDelete()
{
	global $context, $smcFunc, $modSettings, $txt;

	if (!empty($modSettings['Shop_images_resize']))
			$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
	else
		$context['itemOpt'] = 'width: 32px; height: 32px;';

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_items'] . ' - '. $txt['Shop_items_delete'];
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $context['page_title'],
		'description' => $txt['Shop_items_delete'],
	);
	$context['sub_template'] = 'Shop_itemsDelete';

	checkSession();

	// If nothing was chosen to delete
	// TODO: Should this just return to the do=edit page, and show the error there?
	if (!isset($_REQUEST['delete']))
		fatal_error($txt['item_delete_error'], false);

	// Make sure all IDs are numeric
	foreach ($_REQUEST['delete'] as $key => $value)
		$_REQUEST['delete'][$key] = (int) $value;

	// Start with an empty array of items
	$context['shop_items_delete'] = array();

	// Get information on all the items selected to be deleted
	$result = $smcFunc['db_query']('', '
		SELECT itemid, name, image
		FROM {db_prefix}shop_items
		WHERE itemid IN ({array_int:ids})
		ORDER BY name ASC',
		array(
			'ids' => $_REQUEST['delete']
		)
	);

	// Loop through all the results...
	while ($row = $smcFunc['db_fetch_assoc']($result))
		// ... and add them to the array
		$context['shop_items_delete'][] = array(
			'id' => $row['itemid'],
			'name' => $row['name'],
			'image' => $row['image']
		);
	$smcFunc['db_free_result']($result);
}

function Shop_itemsDelete2()
{
	global $context, $smcFunc, $modSettings, $txt;

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_items'] . ' - '. $txt['Shop_items_delete'];
	$context[$context['admin_menu_name']]['current_subsection'] = 'index';

	checkSession();

	// If nothing was chosen to delete (shouldn't happen, but meh)
	if (!isset($_REQUEST['delete']))
		fatal_error($txt['Shop_item_delete_error'], false);

	// Remove all entries of this item from the logs and redirect
	Shop_logsDelete($_REQUEST['delete'], 'action=admin;area=shopitems;sa=index;deleted');
}

function Shop_itemsAdd()
{
	global $context, $txt, $smcFunc;

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_items_add'];
	$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];
	$context['sub_template'] = 'Shop_itemsAdd';

	$request = $smcFunc['db_query']('', '
		SELECT id, name, author, email, file
		FROM {db_prefix}shop_modules
		WHERE file != {string:defitem}
		ORDER BY name ASC',
		array(
			'defitem' => 'Default'
		)
	);

	$context['shop_modules'] = array();
	while($row = $smcFunc['db_fetch_assoc']($request))
		$context['shop_modules'][] = $row;
	$smcFunc['db_free_result']($request);

}

function Shop_itemsAdd2()
{
	global $context, $boarddir, $smcFunc, $modSettings, $txt, $item_info;

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_items_add'];
	$context[$context['admin_menu_name']]['current_subsection'] = 'add';

	// User should get here with item data, but might return from an error later on.
	// Also it would be his fault for not filling data properly.
	checkSession();

	// Image format
	if (!empty($modSettings['Shop_images_resize']))
		$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
	else
		$context['itemOpt'] = 'width: 32px; height: 32px;';

	// Find out if the user included a module
	$module = isset($_REQUEST['module']) ? (isset($_REQUEST['item']) ? $_REQUEST['item'] : 0) : 0;

	$request = $smcFunc['db_query']('', '
		SELECT id, name, require_input, editable_input, can_use, description, file, author, web, email, price
		FROM {db_prefix}shop_modules
		WHERE id = {int:module}',
		array(
			'module' => $module
		)
	);
	// TODO : Check for 0 matches in dB

	// Put all the details into an array
	$row = $smcFunc['db_fetch_assoc']($request);
	$context['shop_item_info'] = array(
		'name' => $row['file'],
		'friendlyname' => $row['name'],
		'desc' => $row['description'],
		'price' => $row['price'],
		'stock' => 50,
		'itemlimit' => 0,
		'require_input' => $row['require_input'],
		'can_use_item' => $row['can_use'],
		'delete_after_use' => 1,
		'author' => $row['author'],
		'web' => $row['web'],
		'email' => $row['email'],
		'addInput' => '',
		'module' => $module,
	);
	$smcFunc['db_free_result']($request);

	if(!empty($module)) {
		// Open tha file
		require_once($boarddir . Shop::$modulesdir . '/' . $context['shop_item_info']['name'] . '.php');
		// Create an instance of the item
		// TODO: Simplify this somehow?
		eval('$tempItem = new item_' . $context['shop_item_info']['name'] . ';');
		// Get the item's details
		// At this stage, there's no additional information
		$item_info = array(
			1 => '',
			2 => '',
			3 => '', 
			4 => '');
		$context['shop_item_info']['addInput'] = ($tempItem->getAddInput() == false) ? '' : $tempItem->getAddInput();
	}
	
	// Images...
	$context['shop_images_list'] = Shop::getImageList();
	// ... and categories
	$context['shop_categories_list'] = Shop::getCatList();

	// Let's put this below, so we can use the information we have
	$context['sub_template'] = 'Shop_itemsAdd2';
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $context['page_title'],
		'description' => sprintf($txt['Shop_items_add_desc2'], $context['shop_item_info']['name'], $context['shop_item_info']['author'], $context['shop_item_info']['email'],  $context['shop_item_info']['web']),
	);
}

function Shop_itemsAdd3()
{
	global $smcFunc, $txt;

	// If item is not set, something is terribly wrong or is trying to access this page without actually adding an item
	if (!isset($_REQUEST['item']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Wait a minute... The user shouldn't be able to add an item with an empty name
	if (!isset($_REQUEST['itemname']) || empty($_REQUEST['itemname']))
		fatal_error($txt['Shop_item_name_blank'], false);

	checkSession();

	// To avoid errors, check for non-existant values and set them to blank
	if (!isset($_REQUEST['info1']))
		$_REQUEST['info1'] = '';
	if (!isset($_REQUEST['info2']))
		$_REQUEST['info2'] = '';
	if (!isset($_REQUEST['info3']))
		$_REQUEST['info3'] = '';
	if (!isset($_REQUEST['info4']))
		$_REQUEST['info4'] = '';

	// If no image selected, default to 'blank.gif'
	if (!isset($_REQUEST['icon']) || $_REQUEST['icon'] == '[NONE]' || $_REQUEST['icon'] == '')
		$_REQUEST['icon'] = 'blank.gif';

	// Check that numeric inputs are indeed numeric
	$_REQUEST['itemprice'] = (float) $_REQUEST['itemprice'];
	$_REQUEST['itemstock'] = (int) $_REQUEST['itemstock'];
	$_REQUEST['itemlimit'] = (int) $_REQUEST['itemlimit'];
	$_REQUEST['require_input'] = $_REQUEST['require_input'] == 1 ? 1 : 0;
	$_REQUEST['can_use_item'] = $_REQUEST['can_use_item'] == 1 ? 1 : 0;
	$delete = isset($_REQUEST['itemdelete']) ? 1 : 0;
	$status = isset($_REQUEST['itemstatus']) ? 1 : 0;
	$_REQUEST['cat'] = (int) $_REQUEST['cat'];
	$_REQUEST['module'] = (int) $_REQUEST['module'];

	// Insert the actual item
	$smcFunc['db_insert']('',
		'{db_prefix}shop_items',
		array(
			'name' => 'string', 
			'description' => 'string',
			'price' => 'float',
			'module' => 'int',
			'function' => 'string',
			'count' => 'int',
			'itemlimit' => 'int',
			'input_needed' => 'int',
			'can_use_item' => 'int',
			'delete_after_use' => 'int',
			'info1' => 'string',
			'info2' => 'string',
			'info3' => 'string',
			'info4' => 'string',
			'image' => 'string',
			'catid' => 'int',
			'status' => 'int',
			),
		array(
			'name' => $smcFunc['htmlspecialchars']($_REQUEST['itemname'], ENT_QUOTES),
			'description' => $smcFunc['htmlspecialchars']($_REQUEST['itemdesc'], ENT_QUOTES),
			'price' => $_REQUEST['itemprice'], 
			'module' => $_REQUEST['module'], 
			'function' => (empty($_REQUEST['module']) ? 'Default' : ''),
			'count' => $_REQUEST['itemstock'],
			'itemlimit' => $_REQUEST['itemlimit'],
			'input_needed' => $_REQUEST['require_input'],
			'can_use_item' => $_REQUEST['can_use_item'],
			'delete_after_use' => $delete,
			'info1' => $_REQUEST['info1'],
			'info2' => $_REQUEST['info2'],
			'info3' => $_REQUEST['info3'],
			'info4' => $_REQUEST['info4'],
			'image' => $_REQUEST['icon'],
			'catid' => $_REQUEST['cat'],
			'status' => $status,
			),
		array()
	);

	// Send him to the items list
	redirectexit('action=admin;area=shopitems;sa=items;added');
}

function Shop_itemsEdit()
{
	global $context, $smcFunc, $boarddir, $modSettings, $item_info, $txt;

	// If item is not set, something is terribly wrong or is trying to access this page without actually editing an item
	if (!isset($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_items_edit'];
	$context['sub_template'] = 'Shop_itemsEdit';

	if (!empty($modSettings['Shop_images_resize']))
		$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
	else
		$context['itemOpt'] = 'width: 32px; height: 32px;';

	// Make sure ID is numeric
	$id = (int) $_REQUEST['id'];

	// Get the item's information
	$result = $smcFunc['db_query']('', '
		SELECT s.itemid, s.name, s.description, s.price, s.count, s.itemlimit, s.image, s.module, s.function, s.info1, s.info2, s.info3, s.info4, s.can_use_item, s.delete_after_use, s.catid, s.status, m.file
		FROM {db_prefix}shop_items AS s
		LEFT JOIN {db_prefix}shop_modules AS m ON (m.id = s.module)
		WHERE itemid = {int:id}',
		array(
			'id' => $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Let's check if we matched something. If it's empty that id is invalid
	if (empty($row))
		fatal_error($txt['Shop_item_notfound'], false);

	// Set all the information (for use in the template)
	$context['shop_item_edit'] = array(
		'itemid' => $id,
		'name' => $row['name'],
		'desc' => $row['description'],
		'price' => $row['price'],
		'stock' => $row['count'],
		'itemlimit' => $row['itemlimit'],
		'image' => $row['image'],
		'can_use_item' => $row['can_use_item'],
		'delete_after_use' => $row['delete_after_use'],
		'catid' => $row['catid'],
		'module' => $row['module'],
		'function' => $row['function'],
		'status' => $row['status'],
		'file' => $row['file'],
	);

	// Images...
	$context['shop_images_list'] = Shop::getImageList();
	// ... and categories
	$context['shop_categories_list'] = Shop::getCatList();
		
	// We need to grab the extra input required by this item.
	// The actual information.
	$item_info[1] = $row['info1'];
	$item_info[2] = $row['info2'];
	$item_info[3] = $row['info3'];
	$item_info[4] = $row['info4'];
	$context['shop_item_edit']['addInputEditable'] = false;

	if(!empty($context['shop_item_edit']['module']))
	{
		// Is the item still there?
		if (!file_exists($boarddir . Shop::$modulesdir . '/' . $context['shop_item_edit']['file'] . '.php'))
			fatal_lang_error('Shop_item_no_module', false, array(Shop::$modulesdir . '/' . $context['shop_item_edit']['file']));

		require_once($boarddir . Shop::$modulesdir . '/' . $context['shop_item_edit']['file'] . '.php');
		// Create an instance of the item (it's used below)
		eval('$tempItem = new item_' . $context['shop_item_edit']['file'] . ';');
		// Get the actual info
		$tempItem->getItemDetails();

		// Can we edit the getAddInput() info?
		if ($tempItem->addInput_editable == true) {
			$context['shop_item_edit']['addInputEditable'] = true;
			$context['shop_item_edit']['addInput'] = $tempItem->getAddInput();
		}
		else {
			$context['shop_item_edit']['addInputEditable'] = false;
		}
	}

	// Let's put this below, so we can use the information we have
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $context['page_title'],
		'description' => sprintf($txt['Shop_items_edit_desc'], $context['shop_item_edit']['name']),
	);
}

function Shop_itemsEdit2()
{
	global $smcFunc, $txt;

	// If item is not set, something is terribly wrong or is trying to access this page without actually editing an item
	if (!isset($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Wait a minute... The user shouldn't be able to add an item with an empty name
	if (!isset($_REQUEST['itemname']) || empty($_REQUEST['itemname']))
		fatal_error($txt['Shop_item_name_blank'], false);

	checkSession();

	// Make sure some inputs are numeric
	$_REQUEST['id'] = (int) $_REQUEST['id'];
	$_REQUEST['itemprice'] = (float) $_REQUEST['itemprice'];
	$_REQUEST['itemstock'] = (int) $_REQUEST['itemstock'];
	$_REQUEST['itemlimit'] = (int) $_REQUEST['itemlimit'];
	$_REQUEST['cat'] = (int) $_REQUEST['cat'];

	// Delete from inventory after use?
	$delete = isset($_REQUEST['itemdelete']) ? 1 : 0;
	// Does the item is going to be enabled?
	$status = isset($_REQUEST['itemstatus']) ? 1 : 0;

	// Additional fields to update
	$additional = '';

	if (isset($_REQUEST['info1']))
		$additional .= ', info1 = "' . intval($_REQUEST['info1']) . '"';
	if (isset($_REQUEST['info2']))
		$additional .= ', info2 = "' . intval($_REQUEST['info2']) . '"';
	if (isset($_REQUEST['info3']))
		$additional .= ', info3 = "' . intval($_REQUEST['info3']) . '"';
	if (isset($_REQUEST['info4']))
		$additional .= ', info4 = "' . intval($_REQUEST['info4']) . '"';

	// Update the item information
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}shop_items
		SET
			name = {string:name},
			description = {string:description},
			price = {float:price},
			count = {int:count},
			itemlimit = {int:itemlimit},
			image = {string:image},
			delete_after_use = {int:delete},
			catid = {int:cat},
			status = {int:status}
			{raw:additional}
		WHERE itemid = {int:id}
		LIMIT 1',
		array(
			'name' => $smcFunc['htmlspecialchars']($_REQUEST['itemname'], ENT_QUOTES),
			'description' => $smcFunc['htmlspecialchars']($_REQUEST['itemdesc'], ENT_QUOTES),
			'price' => $_REQUEST['itemprice'],
			'count' => $_REQUEST['itemstock'],
			'itemlimit' => $_REQUEST['itemlimit'],
			'image' => $_REQUEST['icon'],
			'delete' => $delete,
			'cat' => $_REQUEST['cat'],
			'additional' => $additional,
			'id' => $_REQUEST['id'],
			'status' => $status,
		)
	);

	$id = (int) $_REQUEST['id'];

	// Send him to the items list
	redirectexit('action=admin;area=shopitems;sa=items;'. ';updated');
}

function Shop_itemsUpload()
{
	global $context, $scripturl, $boarddir, $txt;

	// Set the title here
	$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_items_uploaditems'];
	// Set all the page stuff
	$context['page_title'] = $txt['Shop_admin_button'] . ' - '. $txt['Shop_items_uploaditems'];
	$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];
	$context['sub_template'] = 'Shop_itemsUpload';
}

function Shop_itemsUpload2()
{
	global $boarddir, $context, $txt;

	checkSession();

	$context[$context['admin_menu_name']]['current_subsection'] = 'uploaditems';
	$uploadto = $boarddir . Shop::$itemsdir . '/';
	$target_file = $uploadto . basename($_FILES['newitem']['name']);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	// Check if image file is a actual image or fake image
	if (isset($_REQUEST['submit'])) {
		$check = getimagesize($_FILES['newitem']['tmp_name']);
		if($check !== false) 
			$uploadOk = 1;
		else
			$uploadOk = 0;
	}
	// Allow certain file formats
	if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
		fatal_error($txt['Shop_file_error_type1'], false);
		$uploadOk = 0;
	}
	// Check if file already exists
	if (file_exists($target_file)) {
		fatal_error($txt['Shop_file_already_exists'], false);
		$uploadOk = 0;
	}
	// Check file size
	if ($_FILES['newitem']['size'] > 500000) {
		fatal_error($txt['Shop_file_too_large'], false);
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0)
		redirectexit('action=admin;area=shopitems;sa=uploaditems;error');
	// if everything is ok, try to upload file
	else
		if (move_uploaded_file($_FILES['newitem']['tmp_name'], $target_file))
			// Get me out of here
			redirectexit('action=admin;area=shopitems;sa=uploaditems;success');
		else
			// No luck? Sorry...
			redirectexit('action=admin;area=shopitems;sa=uploaditems;error');
}