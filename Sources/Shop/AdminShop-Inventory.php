<?php

/**
 * @package ST Shop
 * @version 3.2
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

if (!defined('SMF'))
	die('No direct access...');

class AdminShopInventory extends AdminShop
{
	public static function Main()
	{
		global $context, $txt;

		loadTemplate('ShopAdmin');

		$context['items_url'] = Shop::$itemsdir;

		$subactions = array(
			'search' => 'AdminShopInventory::Search',
			'search2' => 'AdminShopInventory::Search2',
			'userinv' => 'AdminShopInventory::User',
			'delete' => 'AdminShopInventory::Delete',
			'restock' => 'AdminShopInventory::Restock',
			'restock2' => 'AdminShopInventory::Restock2',
			'groupcredits' => 'AdminShopInventory::Group',
			'groupcredits2' => 'AdminShopInventory::Group2',
			'usercredits' => 'AdminShopInventory::Credits',
			'usercredits2' => 'AdminShopInventory::Credits2',
			'useritems' => 'AdminShopInventory::Items',
			'useritems2' => 'AdminShopInventory::Items2',
		);

		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'search';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['Shop_tab_inventory'],
			'tabs' => array(
				'search' => array('description' => $txt['Shop_tab_inventory_desc']),
				'restock' => array('description' => $txt['Shop_inventory_restock_desc']),
				'groupcredits' => array('description' => $txt['Shop_inventory_groupcredits_desc']),
				'usercredits' => array('description' => $txt['Shop_inventory_usercredits_desc']),
				'useritems' => array('description' => $txt['Shop_inventory_useritems_desc']),
			),
		);
		$subactions[$sa]();
	}

	public static function Search()
	{
		global $context, $txt;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_tab_inventory'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_tab_inventory_desc'],
		);
		$context['sub_template'] = 'adminShop_invSearch';

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
	}

	public static function Search2()
	{
		global $context, $txt, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_tab_inventory'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_tab_inventory_desc'],
		);

		checkSession();

		// Did we get a member?
		if (!isset($_REQUEST['membername']))
			fatal_error($txt['Shop_user_unable_tofind'], false);

		$member_query = array();
		$member_parameters = array();

		// Get the member name...
		$_REQUEST['membername'] = strtr($smcFunc['htmlspecialchars']($_REQUEST['membername'], ENT_QUOTES), array('&quot;' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['membername'], $matches);
		$member_name = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['membername']))));

		foreach ($member_name as $index => $name)
		{
			$member_name[$index] = trim($smcFunc['strtolower']($member_name[$index]));

			if (strlen($member_name[$index]) == 0)
				unset($member_name[$index]);
		}

		// Construct the query
		if (!empty($member_name))
		{
			$member_query[] = 'LOWER(member_name) IN ({array_string:member_name})';
			$member_query[] = 'LOWER(real_name) IN ({array_string:member_name})';
			$member_parameters['member_name'] = $member_name;
		}

		if (!empty($member_query))
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_member
				FROM {db_prefix}members
				WHERE (' . implode(' OR ', $member_query) . ')
				LIMIT 1',
				$member_parameters
			);
			$row = $smcFunc['db_fetch_assoc']($request);
				$memID = $row['id_member'];
			$smcFunc['db_free_result']($request);
		}

		if (empty($memID))
			fatal_error($txt['Shop_user_unable_tofind'], false);

		// Send him to the inventory
		redirectexit('action=admin;area=shopinventory;sa=userinv;u='. $memID);
	}

	public static function Count($memid)
	{
		global $smcFunc;

		// Count the items
		$items = $smcFunc['db_query']('', '
			SELECT p.itemid, p.userid, p.trading, s.status, s.catid
			FROM {db_prefix}shop_inventory AS p
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			WHERE p.trading = 0 AND userid = {int:id} AND s.status = 1',
			array(
				'id' => $memid,
			)
		);
		$count = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);

		return $count;
	}

	public static function Get($start, $items_per_page, $sort, $memid)
	{
		global $context, $smcFunc;

		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT p.id, p.itemid, p.userid, p.trading, p.date, p.tradedate, p.fav, s.can_use_item, s.name, s.image, s.description, s.catid, s.status, c.name AS category, m.real_name
			FROM {db_prefix}shop_inventory AS p
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
				LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.userid)
			WHERE p.trading = 0 AND m.id_member = {int:memid} AND s.status = 1
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
				'memid' => $memid,
			)
		);

		// Return the data
		$context['shop_items_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_items_list'][] = $row;
		$smcFunc['db_free_result']($result);

		return $context['shop_items_list'];
	}

	public static function User()
	{
		global $context, $scripturl, $sourcedir, $memberContext, $modSettings, $txt, $user_info;

		// Did we get the user by name...
		if (isset($_REQUEST['user']))
			$memberResult = loadMemberData($_REQUEST['user'], true, 'profile');
		// ... or by id_member?
		elseif (!empty($_REQUEST['u']))
			$memberResult = loadMemberData((int) $_REQUEST['u'], false, 'profile');

		// Check if loadMemberData() has returned a valid result.
		if (!$memberResult)
			fatal_lang_error('not_a_user', false, 404);

		// If all went well, we have a valid member ID!
		list ($memID) = $memberResult;
		$context['id_member'] = $memID;
		// Let's have some information about this member ready, too.
		loadMemberContext($memID);
		$context['member'] = $memberContext[$memID];
		$context['user']['is_owner'] = $memID == $user_info['id'];

		// Viewing X inventory
		$context['inventory']['whos'] = sprintf($txt['Shop_inventory_viewing_who'], $context['member']['name']);

		// Set all the page stuff
		require_once($sourcedir . '/Subs-List.php');
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'inv_list';
		$context['page_title'] = $txt['Shop_tab_inventory']. ' - ' . $context['inventory']['whos'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_tab_inventory_desc'],
		);

		// The entire list
		$listOptions = array(
			'id' => 'inv_list',
			'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
			'base_href' => '?action=admin;area=shopinventory;sa=userinv;u=' . $context['id_member'],
			'default_sort_col' => 'item_date',
			'default_sort_dir' => 'DESC',
			'get_items' => array(
				'function' => 'AdminShopInventory::Get',
				'params' => array($context['id_member']),
			),
			'get_count' => array(
				'function' => 'AdminShopInventory::Count',
				'params' => array($context['id_member']),
			),
			'no_items_label' => $txt['Shop_inventory_no_items'],
			'no_items_align' => 'center',
			'columns' => array(
				'item_image' => array(
					'header' => array(
						'value' => $txt['Shop_item_image'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ return Shop::ShopImageFormat($row['image']);},
						'style' => 'width: 9%',
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
						'style' => 'width: 22%',
					),
					'sort' =>  array(
						'default' => 'name DESC',
						'reverse' => 'name',
					),
				),
				'item_description' => array(
					'header' => array(
						'value' => $txt['Shop_item_description'],
						'class' => 'lefttext',
					),
					'data' => array(
						'db' => 'description',
					),
					'sort' =>  array(
						'default' => 'description DESC',
						'reverse' => 'description',
					),
				),
				'item_category' => array(
					'header' => array(
						'value' => $txt['Shop_item_category'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ global $txt; return $row['catid'] != 0 ? $row['category'] : $txt['Shop_item_uncategorized'];},
						'class' => 'centertext',
					),
					'sort' =>  array(
						'default' => 'category DESC',
						'reverse' => 'category',
					),
				),
				'item_date' => array(
					'header' => array(
						'value' => $txt['Shop_item_date'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ 
							// Bought in trade center?
							if ($row['tradedate'] != 0)
								$date = $row['tradedate'];
							// Bought in the catalogue
							else
								$date = $row['date'];
							// Display the date
							return timeformat($date);
						},
						'class' => 'centertext',
					),
					'sort' =>  array(
						'default' => 'GREATEST(tradedate, date) DESC',
						'reverse' => 'GREATEST(tradedate, date)',
					),
				),
				'delete' => array(
					'header' => array(
						'value' => $txt['delete']. ' <input type="checkbox" onclick="invertAll(this, this.form, \'delete[]\');" class="input_check" />',
						'class' => 'centertext',
					),
					'data' => array(
						'class' => 'centertext',
						'style' => 'width: 9%',
						'sprintf' => array(
							'format' => '<input type="checkbox" name="delete[]" value="%1$d" class="check" />',
							'params' => array(
								'id' => false,
							),
						),
					),
				),
			),
			'form' => array(
				'href' => '?action=admin;area=shopinventory;sa=delete;u=' . $context['id_member'],
				'hidden_fields' => array(
					$context['session_var'] => $context['session_id'],
				),
			),
			'additional_rows' => array(
				'submit' => array(
					'position' => 'below_table_data',
					'value' => '<input type="submit" size="18" value="'.$txt['delete']. '" class="button" />',
				),
				'updated' => array(
					'position' => 'above_column_headers',
					'value' => (isset($_REQUEST['updated']) ? '<div class="clear"></div><div class="infobox">'.sprintf($txt['Shop_inventory_items_deleted'], $context['member']['name']).'</div>' : ''),
				),
			),
		);
		// Let's finishem
		createList($listOptions);
	}

	public static function Delete()
	{
		global $context, $smcFunc, $modSettings, $txt;

		if (!empty($modSettings['Shop_images_resize']))
				$context['itemOpt'] = 'width: '. $modSettings['Shop_images_width']. '; height: '. $modSettings['Shop_images_height']. ';';
		else
			$context['itemOpt'] = 'width: 32px; height: 32px;';

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_inventory'] . ' - '. $txt['Shop_items_delete'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_items_delete'],
		);

		checkSession();

		// If nothing was chosen to delete
		if (!isset($_REQUEST['delete']))
			fatal_error($txt['item_delete_error'], false);

		// Make sure all IDs are numeric
		foreach ($_REQUEST['delete'] as $key => $value)
				$_REQUEST['delete'][$key] = (int) $value;

		// Delete all the items
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}shop_inventory
			WHERE id IN ({array_int:ids}) AND userid = {int:user}',
			array(
				'ids' => $_REQUEST['delete'],
				'user' => $_REQUEST['u'],
			)
		);

		// Send the user to the items list with a message
		redirectexit('action=admin;area=shopinventory;sa=userinv;u=' . $_REQUEST['u'] . ';updated');
	}

	public static function Restock()
	{
		global $context, $txt, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_inventory'] . ' - '. $txt['Shop_inventory_restock'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_inventory_restock_desc'],
		);
		$context['sub_template'] = 'Shop_invRestock';


		// Start with an empty list
		$context['shop_select_items'] = array();
			
		// Get all non post-based membergroups
		$result = $smcFunc['db_query']('', '
			SELECT itemid, name, status, image
			FROM {db_prefix}shop_items
			WHERE status = 1
			ORDER by name ASC',
			array()
		);
		
		// For each membergroup, add it to the list
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_select_items'][] = array(
				'id' => $row['itemid'],
				'name' => $row['name'],
				'image' => Shop::ShopImageFormat($row['image']),
			);
		$smcFunc['db_free_result']($result);
	}

	public static function Restock2()
	{
		global $smcFunc, $txt;

		// If he selected some specific items, we should have at least one...
		if (($_REQUEST['whatitems'] == 'selected') && (!isset($_REQUEST['restockitem']) || empty($_REQUEST['restockitem'])))
			fatal_error($txt['Shop_restock_error_noitems'], false);

		$stock = !empty($_REQUEST['stock']) ? (int) $_REQUEST['stock'] : 0;
		$restock = !empty($_REQUEST['add']) ? (int) $_REQUEST['add'] : 0;

		if ($_REQUEST['whatitems'] == 'selected')
			// Make sure all IDs are numeric
			foreach ($_REQUEST['restockitem'] as $key => $value)
				$_REQUEST['restockitem'][$key] = (int) $value;
		
		// Update the stock
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}shop_items
				SET
					count = count + {int:restock}
				WHERE'. (($_REQUEST['whatitems'] == 'all') ? ' count <= {int:limit}' : ' itemid IN ({array_int:ids})'),
			array(
				'restock' => $restock,
				'limit' => $stock,
				'ids' => $_REQUEST['restockitem'],
			)
		);

		// Get out of here...
		redirectexit('action=admin;area=shopinventory;sa=restock;success');
	}

	public static function Group()
	{
		global $context, $txt, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_inventory'] . ' - '. $txt['Shop_inventory_groupcredits'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_inventory_groupcredits_desc'],
		);
		$context['sub_template'] = 'Shop_invGroup';

		// Get all non post-based membergroups
		$result = $smcFunc['db_query']('', '
			SELECT id_group, group_name
			FROM {db_prefix}membergroups
			WHERE min_posts = {int:post} AND id_group <> {int:mod}',
			array(
				'post' => -1,
				'mod' => 3,
			)
		);
		
		// For each membergroup, add it to the list
		$context['shop_usergroups'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_usergroups'][] = array(
				'id' => $row['id_group'],
				'name' => $row['group_name']
			);
		$smcFunc['db_free_result']($result);
	}

	public static function Group2()
	{
		global $context, $user_info, $smcFunc, $txt;

		// Keep the tab active
		$context[$context['admin_menu_name']]['current_subsection'] = 'groupcredits';

		// Need to select at least one group
		if (empty($_REQUEST['usergroup']))
			fatal_error($txt['Shop_groupcredits_nogroups'], false);
		// You need to send an amount...
		elseif(empty($_REQUEST['amount']))
			fatal_error($txt['Shop_gift_no_amount'], false);

		checkSession();

		// Make sure all IDs are numeric
		foreach ($_REQUEST['usergroup'] as $key => $value)
				$_REQUEST['usergroup'][$key] = (int) $value;

		// Amount to add/ubstract
		$amount = ($_REQUEST['m_action'] == 'sub' ? (-1) : 1) * (int) $_REQUEST['amount'];

		$member_query = array();
		$member_parameters = array();

		// Do it!
		$result = $smcFunc['db_query']('', '
			SELECT m.id_member
			FROM {db_prefix}members AS m
			WHERE m.id_group IN ({array_int:usergroup}) OR m.additional_groups IN ({array_int:usergroup})',
			array(
				'usergroup' => $_REQUEST['usergroup'],
			)
		);
		while ($row = $smcFunc['db_fetch_row']($result))
			$member_parameters[] = $row;
		$smcFunc['db_free_result']($result);

		// No members?
		if (empty($member_parameters))
			fatal_error($txt['Shop_usergroup_unable_tofind'], false);
		// Let's get the query
		else
		{
			// Make this array less complicated for the log to read it
			$member_parameters['member_ids'] = array();
			foreach ($member_parameters as $key => $id)
				$member_parameters['member_ids'] = array_merge($member_parameters['member_ids'], $id);
			$member_query = 'id_member IN ({array_int:member_ids})';

			// Handle  everything and save it in the log
			parent::logInventory($user_info['id'], $member_parameters['member_ids'], $member_query, '', $amount, 0, 0);

			// Redirect to a nice message of successful
			redirectexit('action=admin;area=shopinventory;sa=groupcredits;success');
		}
	}

	public static function Credits()
	{
		global $context, $txt, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_inventory'] . ' - '. $txt['Shop_inventory_usercredits'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_inventory_usercredits_desc'],
		);
		$context['sub_template'] = 'Shop_invCredits';

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
	}

	public static function Credits2()
	{
		global $context, $txt, $user_info, $sourcedir, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_inventory_usercredits'];
		$context[$context['admin_menu_name']]['current_subsection'] = 'usercredits';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_tab_inventory_desc'],
		);

		checkSession();

		// Did we get a member?
		if (empty($_REQUEST['membername']) && empty($_REQUEST['memberid']))
			fatal_error($txt['Shop_user_unable_tofind'], false);
		// You need to send an amount...
		elseif(empty($_REQUEST['amount']))
			fatal_error($txt['Shop_gift_no_amount'], false);

		$amount = (int) $_REQUEST['amount'];
		$member_query = array();
		$member_parameters = array();
		$member_parameters['member_ids'] = array();

		// Get the member name...
		$_REQUEST['membername'] = strtr($smcFunc['htmlspecialchars']($_REQUEST['membername'], ENT_QUOTES), array('&quot;' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['membername'], $matches);
		$member_name = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['membername']))));

		foreach ($member_name as $index => $name)
		{
			$member_name[$index] = trim($smcFunc['strtolower']($member_name[$index]));

			if (strlen($member_name[$index]) == 0)
				unset($member_name[$index]);
		}

		// Any passed by ID?
		$member_ids = array();
		if (!empty($_REQUEST['memberid']))
			foreach ($_REQUEST['memberid'] as $id)
				if ($id > 0)
					$member_ids[] = (int) $id;

		// Construct the query pelements.
		if (!empty($member_ids))
		{
			$member_query = 'id_member IN ({array_int:member_ids})';
			$member_parameters['member_ids'] = $member_ids;
		}
		// I want only ID's
		if (!empty($member_name))
		{
			$result = $smcFunc['db_query']('', '
				SELECT id_member
				FROM {db_prefix}members
				WHERE LOWER(member_name) IN ({array_string:member_name}) OR LOWER(real_name) IN ({array_string:member_name})',
				array(
					'member_name' => $member_name,
				)
			);
			while ($row = $smcFunc['db_fetch_row']($result))
				$member_parameters['ids'][] = $row;
			$smcFunc['db_free_result']($result);


			// Make this array less complicated for the log to read it
			foreach ($member_parameters['ids'] as $key => $id)
				$member_parameters['member_ids'] = array_merge($member_parameters['member_ids'], $id);
			$member_query = 'id_member IN ({array_int:member_ids})';
		}

		// No members?
		if (empty($member_parameters) || empty($member_query))
			fatal_error($txt['Shop_user_unable_tofind'], false);
		// Handle  everything and save it in the log
		else
		{
			parent::logInventory($user_info['id'], $member_parameters['member_ids'], $member_query, '', $amount, 0, 0);
			// Redirect to a nice message of success
			redirectexit('action=admin;area=shopinventory;sa=usercredits;updated');
		}
	}

	public static function Items()
	{
		global $context, $txt, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_inventory'] . ' - '. $txt['Shop_inventory_useritems'];
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_inventory_useritems_desc'],
		);
		$context['sub_template'] = 'Shop_invItems';

		// Items list
		$context['shop_items_list'] = Shop::getShopItemsList(1);

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
	}

	public static function Items2()
	{
		global $context, $txt, $user_info, $sourcedir, $smcFunc;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_settings'] . ' - '. $txt['Shop_inventory_useritems'];
		$context[$context['admin_menu_name']]['current_subsection'] = 'useritems';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => $txt['Shop_tab_inventory_desc'],
		);

		checkSession();

		// Did we get a member?
		if (empty($_REQUEST['membername']) && empty($_REQUEST['memberid']))
			fatal_error($txt['Shop_user_unable_tofind'], false);
		// You need to send an item...
		elseif(empty($_REQUEST['item']))
			fatal_error($txt['Shop_gift_no_item'], false);

		$item = (int) $_REQUEST['item'];
		$member_query = array();
		$member_parameters = array();
		$member_parameters['member_ids'] = array();

		// Get the member name...
		$_REQUEST['membername'] = strtr($smcFunc['htmlspecialchars']($_REQUEST['membername'], ENT_QUOTES), array('&quot;' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['membername'], $matches);
		$member_name = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['membername']))));

		foreach ($member_name as $index => $name)
		{
			$member_name[$index] = trim($smcFunc['strtolower']($member_name[$index]));

			if (strlen($member_name[$index]) == 0)
				unset($member_name[$index]);
		}

		// Any passed by ID?
		$member_ids = array();
		if (!empty($_REQUEST['memberid']))
			foreach ($_REQUEST['memberid'] as $id)
				if ($id > 0)
					$member_ids[] = (int) $id;

		// Construct the query pelements.
		if (!empty($member_ids))
		{
			$member_query = 'id_member IN ({array_int:member_ids})';
			$member_parameters['member_ids'] = $member_ids;
		}
		// I want only ID's
		if (!empty($member_name))
		{
			$result = $smcFunc['db_query']('', '
				SELECT id_member
				FROM {db_prefix}members
				WHERE LOWER(member_name) IN ({array_string:member_name}) OR LOWER(real_name) IN ({array_string:member_name})',
				array(
					'member_name' => $member_name,
				)
			);
			while ($row = $smcFunc['db_fetch_row']($result))
				$member_parameters['ids'][] = $row;
			$smcFunc['db_free_result']($result);

			// Make this array less complicated for the log to read it
			$member_parameters['member_ids'] = array();
			foreach ($member_parameters['ids'] as $key => $id)
				$member_parameters['member_ids'] = array_merge($member_parameters['member_ids'], $id);
			$member_query = 'id_member IN ({array_int:member_ids})';
		}

		// Count the items for the stock...
		$stock = count($member_parameters['member_ids']);
		$item_query = $smcFunc['db_query']('', '
			SELECT count
			FROM {db_prefix}shop_items
			WHERE itemid = {int:id}',
			array(
				'id' => $item,
			)
		);
		$count = $smcFunc['db_fetch_assoc']($item_query)['count'];
		$smcFunc['db_free_result']($item_query);

		// Enough stock?
		if ($stock > $count)
			fatal_error($txt['Shop_inventory_useritems_nostock'], false);
		// No members?
		elseif (empty($member_parameters) || empty($member_query))
			fatal_error($txt['Shop_user_unable_tofind'], false);
		// Handle  everything and save it in the log
		else
		{
			parent::logInventory($user_info['id'], $member_parameters['member_ids'], $member_query, '', 0, $item);

			// Redirect to a nice message of success
			redirectexit('action=admin;area=shopinventory;sa=useritems;updated');
		}
	}
}