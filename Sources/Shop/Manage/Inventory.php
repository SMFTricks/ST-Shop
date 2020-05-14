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
use Shop\Helper;
use Shop\View\Inventory as Search;

if (!defined('SMF'))
	die('No direct access...');

class Inventory
{
	public function main()
	{
		global $context;

		loadLanguage('Shop/Errors');

		$subactions = [
			'usercredits' => 'credits',
			'usercredits2' => 'credits2',
			'groupcredits' => 'group',
			'groupcredits2' => 'group2',
			//'useritems' => 'AdminShopInventory::Items',
			//'useritems2' => 'AdminShopInventory::Items2',
			//'restock' => 'AdminShopInventory::Restock',
			//'restock2' => 'AdminShopInventory::Restock2',
			'search' => 'search',
			'search2' => 'search2',
			'userinv' => 'view_inventory',
			'delete' => 'delete',
		];
		$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'usercredits';

		// Create the tabs for the template.
		$context[$context['admin_menu_name']]['tab_data'] = [
			'title' => Shop::getText('tab_items'),
			'description' => Shop::getText('tab_items_desc'),
			'tabs' => [
				'groupcredits' => ['description' => Shop::getText('inventory_groupcredits_desc')],
				'usercredits' => ['description' => Shop::getText('inventory_usercredits_desc')],
				'useritems' => ['description' => Shop::getText('inventory_useritems_desc')],
				'restock' => ['description' => Shop::getText('inventory_restock_desc')],
				'search' => ['description' => Shop::getText('tab_inventory_desc')],
			],
		];
		call_helper(__CLASS__ . '::' . $subactions[$sa]);
	}

	public function credits()
	{
		global $context;

		// Inventory template
		loadTemplate('Shop/Inventory');

		// Set all the page stuff
		$context['page_title'] =  Shop::getText('tab_inventory') . ' - '. Shop::getText('inventory_usercredits');
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => Shop::getText('inventory_usercredits_desc'),
		);
		loadTemplate('Shop/ShopAdmin');
		$context['sub_template'] = 'send_credits';
		$context['template_layers'][] = 'send';

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
	}

	public function credits2()
	{
		global $context, $user_info;

		// Set all the page stuff
		$context['page_title'] =  Shop::getText('tab_inventory') . ' - '. Shop::getText('inventory_usercredits');
		$context[$context['admin_menu_name']]['current_subsection'] = 'usercredits';

		// Did we get a member?
		if (empty($_REQUEST['membername']) && empty($_REQUEST['memberid']))
			fatal_error(Shop::getText('user_unable_tofind'), false);

		// You need to send an amount...
		elseif(empty($_REQUEST['amount']))
			fatal_error(Shop::getText('gift_no_amount'), false);

		checkSession();

		$amount = (int) $_REQUEST['amount'];
		$member_query = [];
		$member_parameters = [];

		// Get all the members to be added... taking into account names can be quoted ;)
		$_REQUEST['membername'] = strtr(Helper\Database::sanitize($_REQUEST['membername']), ['&quot;' => '"']);
		preg_match_all('~"([^"]+)"~', $_REQUEST['membername'], $matches);
		$member_names = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['membername']))));

		foreach ($member_names as $index => $member_name)
		{
			$member_names[$index] = trim(Helper\Database::strtolower($member_names[$index]));

			if (strlen($member_names[$index]) == 0)
				unset($member_names[$index]);
		}

		// Any passed by ID?
		$member_ids = [];
		if (!empty($_REQUEST['memberid']))
			foreach ($_REQUEST['memberid'] as $id)
				if ($id > 0)
					$member_ids[] = (int) $id;

		// Construct the query elements.
		if (!empty($member_ids))
		{
			$member_query[] = 'id_member IN ({array_int:member_ids})';
			$member_parameters['member_ids'] = $member_ids;
		}
		if (!empty($member_names))
		{
			$member_query[] = 'LOWER(member_name) IN ({array_string:member_names})';
			$member_query[] = 'LOWER(real_name) IN ({array_string:member_names})';
			$member_parameters['member_names'] = $member_names;
		}

		$receivers = [];
		$members = [];
		if (!empty($member_query))
		{
			// List of users
			$receivers = Helper\Database::Get(0, 1000, 'id_member', 'members', ['id_member'], 'WHERE (' . implode(' OR ', $member_query) . ')', false, '', $member_parameters);

			// Nothing...
			if (empty($receivers))
				fatal_error(Shop::getText('user_unable_tofind'), false);

			// Handle the action
			else
			{
				// Tidy up
				foreach ($receivers as $key => $memID)
					$members[$key] = $memID['id_member'];

				// Handle everything
				Helper\Log::credits($user_info['id'], $members, $amount, true);

				// Redirect to a nice message of success
				redirectexit('action=admin;area=shopinventory;sa=usercredits;updated');
			}
		}
	}

	public function group()
	{
		global $context;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('tab_inventory') . ' - '. Shop::getText('inventory_groupcredits');
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => Shop::getText('inventory_groupcredits_desc'),
		);
		loadTemplate('Shop/ShopAdmin');
		$context['sub_template'] = 'groups';
		$context['template_layers'][] = 'send';

		// Get all non post-based membergroups
		$context['shop_usergroups'] = Helper\Database::Get(0, 10000, 'group_name', 'membergroups', ['id_group AS id', 'group_name AS name'], 'WHERE min_posts = -1 AND id_group <> 3');
	}

	public function group2()
	{
		global $context, $user_info;

		// Keep the tab active
		$context[$context['admin_menu_name']]['current_subsection'] = 'groupcredits';

		// Need to select at least one group
		if (empty($_REQUEST['usergroup']))
			fatal_error(Shop::getText('inventory_groupcredits_nogroups'), false);
		// You need to send an amount...
		elseif(empty($_REQUEST['amount']))
			fatal_error(Shop::getText('Shop_gift_no_amount'), false);

		checkSession();

		// Make sure all IDs are numeric
		foreach ($_REQUEST['usergroup'] as $key => $value)
			$_REQUEST['usergroup'][$key] = (int) $value;

		// Amount to add/ubstract
		$amount = ($_REQUEST['m_action'] == 'sub' ? (-1) : 1) * (int) $_REQUEST['amount'];

		$receivers = [];
		$members = [];
		// Get the list of members
		$receivers = Helper\Database::Get(0, 1000, 'id_member', 'members', ['id_member'], 'WHERE id_group IN ({array_int:usergroup}) OR additional_groups IN ({array_int:usergroup})', false, '', ['usergroup' => $_REQUEST['usergroup']]);

		// No members?
		if (empty($receivers))
			fatal_error(Shop::getText('inventory_usergroup_unable_tofind'), false);
		// Let's get the query
		else
		{
			// Make this array less complicated for the log to read it
			foreach ($receivers as $key => $memID)
				$members[$key] = $memID['id_member'];

			// Handle  everything and save it in the log
			Helper\Log::credits($user_info['id'], $members, $amount, true);

			// Redirect to a nice message of successful
			redirectexit('action=admin;area=shopinventory;sa=groupcredits;success');
		}
	}

	public function search()
	{
		global $context, $scripturl;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_viewInventory');

		// Load templates
		loadTemplate('Shop/ShopAdmin');
		loadTemplate('Shop/Inventory');

		// Set all the page stuff
		$context['page_title'] = Shop::getText('tab_inventory') . ' - ' . Shop::getText('inventory_search');
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => Shop::getText('tab_inventory_desc'),
		);
		$context['template_layers'][] = 'send';
		$context['template_layers'][] = 'shop_inventory_search';
		$context['sub_template'] = 'shop_inventory_search';
		// Form
		$context['form_url'] = '?action=admin;area=shopinventory;sa=search2';

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
	}

	public function Search2()
	{
		// This is a nasty hack
		Search::search_inventory();
	}

	public function view_inventory()
	{
		global $boardurl, $context, $scripturl, $modSettings, $user_info, $memberContext, $sourcedir;

		// Keep the tab active
		$context[$context['admin_menu_name']]['current_subsection'] = 'search';

		loadLanguage('Shop/Shop');

		// Get the actual owner of this inventory
		$memberResult = loadMemberData((isset($_REQUEST['user']) ? $_REQUEST['user'] : ((isset($_REQUEST['u']) ? $_REQUEST['u'] : $user_info['id']))), isset($_REQUEST['user']), 'profile');
	
		// Don't mind me, just checking if it's a valid profile
		if (!$memberResult)
			fatal_error(Shop::getText('user_unable_tofind'), false);

		// If all went well, we have a valid member ID!
		list ($memID) = $memberResult;
		
		// Let's have some information about this member ready, too.
		loadMemberContext($memID);
		$context['member'] = $memberContext[$memID];
		$context['user']['is_owner'] = $memID == $user_info['id'];

		// Set all the page stuff
		require_once($sourcedir . '/Subs-List.php');
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'inventory';
		$context['page_title'] = Shop::getText('tab_inventory') . ' - ' . Shop::getText('inventory_userinv');
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => Shop::getText('tab_inventory_desc'),
		);
		loadTemplate('Shop/ShopAdmin');
		$context['template_layers'][] = 'send';
		$context['form_url'] = '?action=admin;area=shopinventory;sa=userinv'. (!empty($context['user']['is_owner']) ? '' : ';u='. $context['member']['id']);

		// Load the list
		$listOptions = Search::inventory_list($memberContext[$memID], $context['form_url']);

		// Remove unnecessary stuff from this view
		unset($listOptions['columns']['item_use']);
		unset($listOptions['columns']['item_fav']);
		unset($listOptions['columns']['item_trade']);
		unset($listOptions['additional_rows']['traded']);

		// Delete
		$listOptions['columns']['delete'] = [
			'header' => [
				'value' => Shop::getText('delete', false). ' <input type="checkbox" onclick="invertAll(this, this.form, \'delete[]\');" class="input_check" />',
				'class' => 'centertext',
			],
			'data' => [
				'class' => 'centertext',
				'style' => 'width: 9%',
				'sprintf' => [
					'format' => '<input type="checkbox" name="delete[]" value="%1$d" class="check" />',
					'params' => [
						'id' => false,
					],
				],
			],
		];
		$listOptions['form'] = [
			'href' => '?action=admin;area=shopinventory;sa=delete;u=' . $context['member']['id'],
			'hidden_fields' => [
				$context['session_var'] => $context['session_id'],
			],
		];
		$listOptions['additional_rows']['submit'] = [
			'position' => 'below_table_data',
			'value' => '<input type="submit" size="18" value="'.Shop::getText('delete', false). '" class="button" />',
		];
		$listOptions['additional_rows']['deleted'] = [
			'position' => 'above_column_headers',
			'value' => (isset($_REQUEST['deleted']) ? '<div class="clear"></div><div class="infobox">'.sprintf(Shop::getText('inventory_items_deleted'), $context['member']['name']).'</div>' : ''),
		];

		createList($listOptions);
	}

	public static function delete()
	{
		global $context, $user_info;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('tab_inventory') . ' - '. Shop::getText('items_delete');
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => Shop::getText('items_delete'),
		);

		checkSession();

		// If nothing was chosen to delete
		if (!isset($_REQUEST['delete']))
			fatal_error(Shop::getText('item_delete_error'), false);

		// Make sure all IDs are numeric
		foreach ($_REQUEST['delete'] as $key => $value)
			$_REQUEST['delete'][$key] = (int) $value;

		// Delete selected items
		Helper\Database::Delete('shop_inventory', 'id', $_REQUEST['delete'], ' AND userid = ' .$_REQUEST['u']);

		// Send the user to the items list with a message
		redirectexit('action=admin;area=shopinventory;sa=userinv' . ($user_info['id'] == $_REQUEST['u'] ? '' : ';u='.$_REQUEST['u']) . ';deleted');
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