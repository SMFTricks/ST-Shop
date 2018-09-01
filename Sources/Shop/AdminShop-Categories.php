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

function Shop_adminCategories()
{
	global $context, $txt;

	loadTemplate('ShopAdmin');

	$context['items_url'] = Shop::$itemsdir . '/';

	$subactions = array(
		'index' => 'Shop_categoriesIndex',
		'add' => 'Shop_categoriesAdd',
		'add2' => 'Shop_categoriesAdd2',
		'edit' => 'Shop_categoriesEdit',
		'edit2' => 'Shop_categoriesEdit2',
		'delete' => 'Shop_categoriesDelete',
		'delete2' => 'Shop_categoriesDelete2',
	);

	$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'index';

	// Create the tabs for the template.
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['Shop_tab_categories'],
		'description' => $txt['Shop_tab_categories_desc'],
		'tabs' => array(
			'index' => array('description' => $txt['Shop_tab_categories_desc']),
			'add' => array('description' => $txt['Shop_categories_add_desc']),
		),
	);

	$subactions[$sa]();
}

function Shop_categoriesIndex()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'categorieslist';
	$context['page_title'] = $txt['Shop_tab_categories']. ' - ' . $txt['Shop_tab_categories'];

	// The entire list
	$listOptions = array(
		'id' => 'categorieslist',
		'title' => $txt['Shop_tab_items'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shopcategories;sa=index',
		'default_sort_col' => 'modify',
		'get_items' => array(
			'function' => 'Shop_categoriesGet',
		),
		'get_count' => array(
			'function' => 'Shop_categoriesCount',
		),
		'no_items_label' => $txt['Shop_no_categories'],
		'no_items_align' => 'center',
		'columns' => array(
			'item_image' => array(
				'header' => array(
					'value' => $txt['Shop_category_image'],
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
			'description' => array(
				'header' => array(
					'value' => $txt['Shop_item_description'],
				),
				'data' => array(
					'db' => 'description',
					'style' => 'width: 18%',
				),
				'sort' =>  array(
					'default' => 'description DESC',
					'reverse' => 'description',
				),
			),
			'items_in' => array(
					'header' => array(
						'value' => $txt['Shop_total_items'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ return Shop_categoriesItemsCount($row['catid']);},
						'style' => 'width: 3%',
						'class' => 'centertext',
					),
				),
			'modify' => array(
				'header' => array(
					'value' => $txt['Shop_item_modify'],
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl. '?action=admin;area=shopcategories;sa=edit;id=%1$d">'. $txt['Shop_item_modify']. '</a>',
						'params' => array(
							'catid' => true,
						),
					),
					'style' => 'width: 5%',
					'class' => 'centertext',
				),
				'sort' => array(
					'default' => 'catid DESC',
					'reverse' => 'catid',
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
							'catid' => false,
						),
					),
					'class' => 'centertext',
					'style' => 'width: 2%',
				),
			),
		),
		'form' => array(
			'href' => '?action=admin;area=shopcategories;sa=delete',
			'hidden_fields' => array(
				$context['session_var'] => $context['session_id'],
			),
			'include_sort' => true,
			'include_start' => true,
		),
		'additional_rows' => array(
			'submit' => array(
				'position' => 'below_table_data',
				'value' => '<input type="submit" size="18" value="'.$txt['delete']. '" class="button_submit" />',
			),
			'updated' => array(
				'position' => 'top_of_list',
				'value' => (!isset($_REQUEST['deleted']) ? (!isset($_REQUEST['added']) ? (!isset($_REQUEST['updated']) ? '' : '<div class="infobox">'. $txt['Shop_categories_updated']. '</div>') : '<div class="infobox">'. $txt['Shop_categories_added']. '</div>') : '<div class="infobox">'. $txt['Shop_categories_deleted']. '</div>'),
			),
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_categoriesAdd()
{
	global $context, $boarddir, $smcFunc, $modSettings, $txt, $item_info;

	// Image format
	if (!empty($modSettings['Shop_images_resize']))
		$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
	else
		$context['itemOpt'] = 'width: 32px; height: 32px;';

	// Images...
	$context['shop_images_list'] = Shop::getImageList();

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_categories'] . ' - '. $txt['Shop_categories_add'];
	$context[$context['admin_menu_name']]['tab_data']['title'] = $context['page_title'];
	$context['sub_template'] = 'Shop_categoriesAdd';
}

function Shop_categoriesAdd2()
{
	global $smcFunc, $txt;

	// Wait a minute... The user shouldn't be able to add an category with an empty name
	if (!isset($_REQUEST['catname']))
		fatal_error($txt['Shop_category_name_blank'], false);

	checkSession();

	// If no image selected, default to 'blank.gif'
	if (!isset($_REQUEST['caticon']) || $_REQUEST['caticon'] == '[NONE]' || $_REQUEST['caticon'] == '')
		$_REQUEST['caticon'] = 'blank.gif';

	// Insert the actual category
	$smcFunc['db_insert']('',
		'{db_prefix}shop_categories',
		array(
			'name' => 'string', 
			'description' => 'string',
			'image' => 'string',
		),
		array(
			'name' => $smcFunc['htmlspecialchars']($_REQUEST['catname'], ENT_QUOTES),
			'description' => $smcFunc['htmlspecialchars']($_REQUEST['catdesc'], ENT_QUOTES),
			'image' => $_REQUEST['caticon'],
		),
		array()
	);

	// Send him to the items list
	redirectexit('action=admin;area=shopcategories;sa=categories;added');
}

function Shop_categoriesEdit()
{
	global $context, $smcFunc, $sourcedir, $modSettings, $txt;

	// If item is not set, something is terribly wrong or is trying to access this page without actually editing an item
	if (!isset($_REQUEST['id']))
		fatal_error($txt['Shop_category_notfound'], false);

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_categories_edit'];
	$context['sub_template'] = 'Shop_categoriesEdit';

	if (!empty($modSettings['Shop_images_resize']))
		$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
	else
		$context['itemOpt'] = 'width: 32px; height: 32px;';

	// Make sure ID is numeric
	$id = (int) $_REQUEST['id'];

	// Get the item's information
	$result = $smcFunc['db_query']('', '
		SELECT catid, name, description, image
		FROM {db_prefix}shop_categories
		WHERE catid = {int:id}',
		array(
			'id' => $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Let's check if we matched something. If it's empty that id is invalid
	if (empty($row))
		fatal_error($txt['Shop_category_notfound'], false);

	// Set all the information (for use in the template)
	$context['shop_category_edit'] = array(
		'catid' => $id,
		'name' => $row['name'],
		'description' => $row['description'],
		'image' => $row['image'],
	);

	// Images...
	$context['shop_images_list'] = Shop::getImageList();
	// ... and categories
	$context['shop_categories_list'] = Shop::getCatList();
	// Let's put this below, so we can use the information we have
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $context['page_title'],
		'description' => sprintf($txt['Shop_categories_edit_desc'], $context['shop_category_edit']['name']),
	);
}

function Shop_categoriesEdit2()
{
	global $smcFunc;

	// What's going on?
	if (!isset($_REQUEST['id']))
		fatal_error($txt['Shop_category_notfound'], false);

	checkSession();

	// Make sure some inputs are numeric
	$_REQUEST['id'] = (int) $_REQUEST['id'];

	// Update the item information
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}shop_categories
		SET
			name = {string:name},
			description = {string:description},
			image = {string:image}
		WHERE catid = {int:id}',
		array(
			'name' => $smcFunc['htmlspecialchars']($_REQUEST['catname'], ENT_QUOTES),
			'description' => $smcFunc['htmlspecialchars']($_REQUEST['catdesc'], ENT_QUOTES),
			'image' => $_REQUEST['caticon'],
			'id' => $_REQUEST['id'],
		)
	);

	// Send him to the categories list
	redirectexit('action=admin;area=shopcategories;sa=categories;updated');
}

function Shop_categoriesDelete()
{
	global $context, $smcFunc, $modSettings, $txt;

	if (!empty($modSettings['Shop_images_resize']))
			$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
	else
		$context['itemOpt'] = 'width: 32px; height: 32px;';

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_categories'] . ' - '. $txt['Shop_categories_delete'];
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $context['page_title'],
		'description' => $txt['Shop_categories_delete'],
	);
	$context['sub_template'] = 'Shop_categoriesDelete';

	checkSession();

	// If nothing was chosen to delete
	// TODO: Should this just return to the do=edit page, and show the error there?
	if (!isset($_REQUEST['delete']))
		fatal_error($txt['item_delete_error'], false);

	// Make sure all IDs are numeric
	foreach ($_REQUEST['delete'] as $key => $value)
		$_REQUEST['delete'][$key] = (int) $value;

	// Start with an empty array of items
	$context['shop_categories_delete'] = array();

	// Get information on all the categories selected to be deleted
	$result = $smcFunc['db_query']('', '
		SELECT catid, name, image, description
		FROM {db_prefix}shop_categories
		WHERE catid IN ({array_int:ids})
		ORDER BY name ASC',
		array(
			'ids' => $_REQUEST['delete']
		)
	);

	// Loop through all the results...
	while ($row = $smcFunc['db_fetch_assoc']($result))
		// ... and add them to the array
		$context['shop_categories_delete'][] = array(
			'id' => $row['catid'],
			'name' => $row['name'],
			'image' => $row['image'],
			'description' => $row['description'],
		);
	$smcFunc['db_free_result']($result);
}

function Shop_categoriesDelete2()
{
	global $context, $smcFunc, $modSettings, $txt;

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_tab_categories'] . ' - '. $txt['Shop_categories_delete'];
	$context[$context['admin_menu_name']]['current_subsection'] = 'index';

	checkSession();

	// If nothing was chosen to delete (shouldn't happen, but meh)
	if (!isset($_REQUEST['delete']))
		fatal_error($txt['Shop_item_delete_error'], false);
	
	// Make sure all IDs are numeric
	foreach ($_REQUEST['delete'] as $key => $value)
		$_REQUEST['delete'][$key] = (int) $value;

	// Delete the categories
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}shop_categories
		WHERE catid IN ({array_int:ids})',
		array(
			'ids' => $_REQUEST['delete'],
		)
	);

	// So... He want to delete the items too?
	if (!empty($_REQUEST['deleteitems']))
	{
		// Let's just get the items ids to make our life easier
		$result = $smcFunc['db_query']('', '
			SELECT itemid
			FROM {db_prefix}shop_items
			WHERE catid IN ({array_int:ids})
			ORDER BY name ASC',
			array(
				'ids' => $_REQUEST['delete']
			)
		);
		$context['items_list'] = array();
		while ($row = $smcFunc['db_fetch_row']($result))
			$context['items_list'] = $row;
		$smcFunc['db_free_result']($result);

		// Remove all entries of this item from the logs and redirect
		Shop_logsDelete($context['items_list'], 'action=admin;area=shopcategories;sa=index;deleted');
	}
	// If he's not going to delete the items, let's update them to set catid to 0
	elseif (empty($_REQUEST['deleteitems'])) {
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}shop_items
			SET
				catid = {int:cat}
			WHERE catid IN ({array_int:ids})',
			array(
				'cat' => 0,
				'ids' => $_REQUEST['delete'],
			)
		);

		// Done with the delete categories?
		// Send the user to the categories list with a message
		redirectexit('action=admin;area=shopcategories;sa=index;deleted');
	}
}