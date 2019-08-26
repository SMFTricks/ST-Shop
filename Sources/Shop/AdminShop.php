<?php

/**
 * @package ST Shop
 * @version 3.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2018, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

 class AdminShop
 {
	 /**
	 * AdminShop::hookAreas()
	 *
	 * Adding the admin section
	 * @param array $admin_areas An array with all the admin areas
	 * @return
	 */
	public static function hookAreas(&$admin_areas)
	{
		global $scripturl, $txt, $modSettings;

		loadLanguage('Shop');

		$admin_areas['shop'] = array(
			'title' => $txt['Shop_admin_button'],
			'permission' => array('shop_canManage'),
			'areas' => array(
				'shopinfo' => array(
					'label' => $txt['Shop_tab_info'],
					'icon' => 'administration',
					'function' => 'self::Info',
				),
				'shopsettings' => array(
					'label' => $txt['Shop_tab_settings'],
					'icon' => 'features',
					'file' => 'Shop/AdminShop-Settings.php',
					'function' => 'AdminShop_Settings::Main',
					'permission' => array('admin_forum'),
					'subsections' => array(
						'general' => array($txt['Shop_settings_general']),
						'credits' => array($txt['Shop_settings_credits']),
						'permissions' => array($txt['Shop_settings_permissions']),
						'profile' => array($txt['Shop_settings_profile']),
					),
				),
				'shopitems' => array(
					'label' => $txt['Shop_tab_items'],
					'icon' => 'smiley',
					'file' => 'Shop/AdminShop-Items.php',
					'function' => 'AdminShop_Items::Main',
					'permission' => array('shop_canManage'),
					'custom_url' => $scripturl . '?action=admin;area=shopitems;sa=index',
					'subsections' => array(
						'index' => array($txt['Shop_tab_items']),
						'add' => array($txt['Shop_items_add']),
						'uploaditems' => array($txt['Shop_items_uploaditems']),
					),
				),
				'shopmodules' => array(
					'label' => $txt['Shop_tab_modules'],
					'icon' => 'modifications',
					'file' => 'Shop/AdminShop-Modules.php',
					'function' => 'AdminShop_Modules::Main',
					'permission' => array('admin_forum'),
					'custom_url' => $scripturl . '?action=admin;area=shopmodules;sa=index',
					'subsections' => array(
						'index' => array($txt['Shop_tab_modules']),
						'uploadmodules' => array($txt['Shop_modules_uploadmodules']),
					),
				),
				'shopcategories' => array(
					'label' => $txt['Shop_tab_categories'],
					'icon' => 'boards',
					'file' => 'Shop/AdminShop-Categories.php',
					'function' => 'AdminShop_Categories::Main',
					'permission' => array('shop_canManage'),
					'custom_url' => $scripturl . '?action=admin;area=shopcategories;sa=index',
					'subsections' => array(
						'index' => array($txt['Shop_tab_categories']),
						'add' => array($txt['Shop_categories_add']),
					),
				),
				'shopgames' => array(
					'label' => $txt['Shop_tab_games'],
					'icon' => 'paid',
					'file' => 'Shop/AdminShop-Games.php',
					'function' => 'AdminShop_Games::Main',
					'permission' => array('shop_canManage'),
					'custom_url' => $scripturl . '?action=admin;area=shopgames;sa=slots',
					'subsections' => array(
						'slots' => array($txt['Shop_games_slots']),
						'lucky2' => array($txt['Shop_games_lucky2']),
						'number' => array($txt['Shop_games_number']),
						'pairs' => array($txt['Shop_games_pairs']),
						'dice' => array($txt['Shop_games_dice']),
					),
				),
				'shopinventory' => array(
					'label' => $txt['Shop_tab_inventory'],
					'icon' => 'maintain',
					'file' => 'Shop/AdminShop-Inventory.php',
					'function' => 'AdminShop_Inventory::Main',
					'permission' => array('shop_canManage'),
					'custom_url' => $scripturl . '?action=admin;area=shopinventory;sa=search',
					'subsections' => array(
						'search' => array($txt['Shop_tab_inventory']),
						'restock' => array($txt['Shop_inventory_restock']),
						'groupcredits' => array($txt['Shop_inventory_groupcredits']),
						'usercredits' => array($txt['Shop_inventory_usercredits']),
						'useritems' => array($txt['Shop_inventory_useritems']),
					),
				),
				'shoplogs' => array(
					'label' => $txt['Shop_tab_logs'],
					'icon' => 'logs',
					'file' => 'Shop/AdminShop-Logs.php',
					'function' => 'AdminShop_Inventory::Main',
					'permission' => array('shop_canManage'),
					'custom_url' => $scripturl . '?action=admin;area=shoplogs',
					'subsections' => array(
						'admin_money' => array($txt['Shop_logs_admin_money']),
						'admin_items' => array($txt['Shop_logs_admin_items'], 'enabled' => !empty($modSettings['Shop_enable_shop'])),
						'buy' => array($txt['Shop_logs_buy'], 'enabled' => !empty($modSettings['Shop_enable_shop'])),
						'money' => array($txt['Shop_logs_money'], 'enabled' => !empty($modSettings['Shop_enable_gift'])),
						'items' => array($txt['Shop_logs_items'], 'enabled' => !empty($modSettings['Shop_enable_gift'])),
						'trade' => array($txt['Shop_logs_trade'], 'enabled' => !empty($modSettings['Shop_enable_trade'])),
						'bank' => array($txt['Shop_logs_bank'], 'enabled' => !empty($modSettings['Shop_enable_bank'])),
						'games' => array($txt['Shop_logs_games'], 'enabled' => !empty($modSettings['Shop_enable_games'])),
					),
				),
			),
		);

		// Permissions
		add_integration_function('integrate_load_permissions', 'AdminShop::Perms', false);
		add_integration_function('integrate_load_illegal_guest_permissions', 'AdminShop::IllegalPerms', false);
		// Boards settings
		add_integration_function('integrate_pre_boardtree', 'AdminShop::preboardTree', false);
		add_integration_function('integrate_boardtree_board', 'AdminShop::boardTree', false);
		add_integration_function('integrate_edit_board', 'AdminShop::boardEdit', false);
		add_integration_function('integrate_create_board', 'AdminShop::boardCreate', false);
		add_integration_function('integrate_modify_board', 'AdminShop::boardModify', false);
	}

	/**
	 * AdminShop::Perms()
	 *
	 * ST Shop permissions
	 * @param array $permissionGroups An array containing all possible permissions groups.
	 * @param array $permissionList An associative array with all the possible permissions.
	 * @return void
	 * @author Jessica González <suki@missallsunday.com>
	 */
	public static function Perms(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
	{
		global $txt;

		$shop_permissions = array(
			'shop_canAccess' => false,
			'shop_playGames' => false,
			'shop_canTrade' => false,
			'shop_canBank' => false,
			'shop_canGift' => false,
			'shop_viewInventory' => false,
			'shop_canBuy' => false,
			'shop_viewStats' => false,
			'shop_canManage' => false
		);

		$permissionGroups['membergroup'] = array('shop');
		foreach ($shop_permissions as $p => $s) {
			$permissionList['membergroup'][$p] = array($s,'shop','shop');
			$hiddenPermissions[] = $p;
		}
	}

	/**
	 * AdminShop::IllegalPerms()
	 *
	 * ST Shop Illegal permissions
	 * 
	 */
	public static function IllegalPerms()
	{
		global $context;

		$shop_permissions = array(
			'shop_canAccess',
			'shop_playGames',
			'shop_canTrade',
			'shop_canBank',
			'shop_canGift',
			'shop_viewInventory',
			'shop_canBuy',
			'shop_viewStats',
			'shop_canManage'
		);

		// Guests do not play nicely with this mod
		$context['non_guest_permissions'] = array_merge($context['non_guest_permissions'],$shop_permissions);
	}

	public static function Shop_preboardTree(&$boardColumns, &$boardParameters, &$boardJoins, &$boardWhere, &$boardOrder)
	{
		array_push($boardColumns,'b.Shop_credits_count','b.Shop_credits_topic','b.Shop_credits_post','b.Shop_credits_bonus');
	}

	public static function boardTree($row)
	{
		global $boards;

		$boards[$row['id_board']] += array(
			'Shop_credits_count' => $row['Shop_credits_count'],
			'Shop_credits_topic' => $row['Shop_credits_topic'],
			'Shop_credits_post' => $row['Shop_credits_post'],
			'Shop_credits_bonus' => $row['Shop_credits_bonus'],
		);
	}

	public static function boardEdit()
	{
		global $context, $txt, $modSettings;

		if (isset($context['board']['is_new']) && $context['board']['is_new'] === true) {
			$context['board']['Shop_credits_count'] = 1;
			$context['board']['Shop_credits_topic'] = 0;
			$context['board']['Shop_credits_post'] = 0;
			$context['board']['Shop_credits_bonus'] = 0;
		}

		$context['custom_board_settings']['Shop_credits_count'] = array(
			'dt' => '<strong>'. $txt['Shop_credits_count']. '</strong><br /><span class="smalltext">'. $txt['Shop_credits_count_desc']. '</span>',
			'dd' => '<input type="checkbox" name="Shop_credits_count" class="input_check"'. (!empty($context['board']['Shop_credits_count']) ? ' checked="checked"' : ''). '>',
		);
		$context['custom_board_settings']['Shop_credits_topic'] = array(
			'dt' => '<strong>'. $txt['Shop_credits_topic']. ':</strong><br /><span class="smalltext">'. $txt['Shop_credits_custom_override']. '</span>',
			'dd' => $modSettings['Shop_credits_prefix']. '<input class="input_text" type="text" name="Shop_credits_topic" size="5" value="'. (!empty($context['board']['Shop_credits_topic']) ? $context['board']['Shop_credits_topic'] : $modSettings['Shop_credits_topic']). '"> '. $modSettings['Shop_credits_suffix'],
		);
		$context['custom_board_settings']['Shop_credits_post'] = array(
			'dt' => '<strong>'. $txt['Shop_credits_post']. ':</strong><br /><span class="smalltext">'. $txt['Shop_credits_custom_override']. '</span>',
			'dd' => $modSettings['Shop_credits_prefix']. '<input class="input_text" type="text" name="Shop_credits_post" size="5" value="'. (!empty($context['board']['Shop_credits_topic']) ? $context['board']['Shop_credits_post'] : $modSettings['Shop_credits_post']). '"> '. $modSettings['Shop_credits_suffix'],
		);
		$context['custom_board_settings']['Shop_credits_bonus'] = array(
			'dt' => '<strong>'. $txt['Shop_credits_enable_bonus']. ':</strong><br /><span class="smalltext">'. $txt['Shop_credits_enable_bonus_desc']. '</span>',
			'dd' => '<input type="checkbox" name="Shop_credits_bonus" class="input_check"'. (!empty($context['board']['Shop_credits_bonus']) ? ' checked="checked"' : ''). '>',
		);
	}

	public static function boardCreate(&$boardOptions, &$board_columns, &$board_parameters)
	{
		$boardOptions += array(
			'Shop_credits_count' => 1,
			'Shop_credits_topic' => 0,
			'Shop_credits_post' => 0,
			'Shop_credits_bonus' => 0,
		);
	}

	public static function boardModify($id, $boardOptions, &$boardUpdates, &$boardUpdateParameters)
	{
		$boardOptions['Shop_credits_count'] = isset($_POST['Shop_credits_count']);
		$boardOptions['Shop_credits_topic'] = !empty($_POST['Shop_credits_topic']) ? (int) $_POST['Shop_credits_topic'] : 0;
		$boardOptions['Shop_credits_post'] = !empty($_POST['Shop_credits_post']) ? (int) $_POST['Shop_credits_post'] : 0;
		$boardOptions['Shop_credits_bonus'] = isset($_POST['Shop_credits_bonus']);

		if (isset($boardOptions['Shop_credits_count'])) {
			$boardUpdates[] = 'Shop_credits_count = {int:Shop_credits_count}';
			$boardUpdateParameters['Shop_credits_count'] = $boardOptions['Shop_credits_count'] ? 1 : 0;
		}		
		if (isset($boardOptions['Shop_credits_topic'])) {
			$boardUpdates[] = 'Shop_credits_topic = {int:Shop_credits_topic}';
			$boardUpdateParameters['Shop_credits_topic'] = (int) $boardOptions['Shop_credits_topic'];
		}
		if (isset($boardOptions['Shop_credits_post'])) {
			$boardUpdates[] = 'Shop_credits_post = {int:Shop_credits_post}';
			$boardUpdateParameters['Shop_credits_post'] = (int) $boardOptions['Shop_credits_post'];
		}
		if (isset($boardOptions['Shop_credits_bonus'])) {
			$boardUpdates[] = 'Shop_credits_bonus = {int:Shop_credits_bonus}';
			$boardUpdateParameters['Shop_credits_bonus'] = $boardOptions['Shop_credits_bonus'] ? 1 : 0;
		}
	}

	public static function Info()
	{
		global $sourcedir, $modSettings, $scripturl, $context, $user_info, $txt;

		loadTemplate('ShopAdmin');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_admin_button'] . ' - '. $txt['Shop_tab_info'];
		$context['sub_template'] = 'Shop_adminInfo';
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $context['page_title'],
			'description' => sprintf($txt['Shop_tab_info_desc'], $user_info['name']),
		);

		$context['Shop']['version'] = Shop::$version;
		$context['Shop']['support'] = Shop::$supportSite;
		$context['Shop']['credits'] = Shop::credits();

		// Feed news
		addInlineJavascript('
			$(function(){
				var shoplive = $("#smfAnnouncements");
				$.ajax({
					type: "GET",
					url: '. JavaScriptEscape($scripturl . '?action=shopfeed') .',
					cache: false,
					dataType: "xml",
					success: function(xml){
						var dl = $("<dl />");
						$(xml).find("item").each(function () {
							var item = $(this),
							title = $("<a />", {
								text: item.find("title").text(),
								href: item.find("link").attr("href")
							}),
							parsedTime = new Date(item.find("pubDate").text().replace("T", " ").split("+")[0]),
							updated = $("<span />").text( parsedTime.toDateString()),
							content = $("<div/>").html(item.find("description")).text(),
							dt = $("<dt />").html(title),
							dd = $("<dd />").html(content);
							updated.appendTo(dt);
							dt.appendTo(dl);
							dd.appendTo(dl);
						});
						shoplive.html(dl);
					},
					error: function (html){}
				});
			});
		', true);
	}

	public static function itemsCount()
	{
		global $smcFunc;

		// Count the items
		$items = $smcFunc['db_query']('', '
			SELECT itemid
			FROM {db_prefix}shop_items',
			array()
		);
		$count = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);

		return $count;
	}

	public static function itemsGet($start, $items_per_page, $sort)
	{
		global $context, $smcFunc;

		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT s.name, s.itemid, s.description, s.image, s.module, s.function, s.count, s.status, s.price, s.catid, c.name AS category, m.file
			FROM {db_prefix}shop_items AS s
				LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
				LEFT JOIN {db_prefix}shop_modules AS m ON (m.id = s.module)
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			)
		);

		// Return the data
		$context['shop_items_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_items_list'][] = $row;
		$smcFunc['db_free_result']($result);

		return $context['shop_items_list'];
	}

	public static function categoriesCount()
	{
		global $smcFunc;

		// Count the items
		$items = $smcFunc['db_query']('', '
			SELECT catid
			FROM {db_prefix}Shop_categories',
			array()
		);
		$count = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);

		return $count;
	}

	public static function categoriesGet($start, $items_per_page, $sort)
	{
		global $context, $smcFunc;

		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT s.name, s.catid, s.image, s.description, s.status
			FROM {db_prefix}Shop_categories AS s
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			)
		);

		// Return the data
		$context['shop_categories_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_categories_list'][] = $row;
		$smcFunc['db_free_result']($result);

		return $context['shop_categories_list'];
	}

	public static function categoriesItemsCount($cat)
	{
		global $smcFunc;

		$items = $smcFunc['db_query']('', '
			SELECT catid, status
			FROM {db_prefix}shop_items
			WHERE catid = {int:cat}',
			array(
				'cat' => $cat,
			)
		);
		$count = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);

		return $count;
	}

	public static function logsDelete($items, $redirect = NULL)
	{
		global $context, $smcFunc, $txt;

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_tab_logs'] . ' - '. $txt['Shop_items_delete'];

		// If nothing was chosen to delete (shouldn't happen, but meh)
		if (empty($items))
			fatal_error($txt['Shop_item_delete_error'], false);

		// Make sure all IDs are numeric
		foreach ($items as $key => $value)
			$items[$key] = (int) $value;

		// Delete all the items
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}shop_items
			WHERE itemid IN ({array_int:ids})',
			array(
				'ids' => $items,
			)
		);
		// If anyone owned this item, they don't anymore :P
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}shop_inventory
			WHERE itemid IN ({array_int:ids})',
			array(
				'ids' => $items,
			)
		);
		// Clean gift log
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}shop_log_gift
			WHERE itemid IN ({array_int:ids})',
			array(
				'ids' => $items,
			)
		);
		// Clean buy log
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}shop_log_buy
			WHERE itemid IN ({array_int:ids})',
			array(
				'ids' => $items,
			)
		);

		// Redirect the user
		if ($redirect != NULL)
			redirectexit($redirect);
	}

	public static function logInventory($userid, $receivers, $ids, $message, $amount = 0, $itemid = 0)
	{
		global $smcFunc;

		// He sent an item
		if ($amount == 0) {
			// Transfer the item to the new user
			foreach ($receivers as $memID)
			{
				// Insert the information in the log
				$smcFunc['db_insert']('',
					'{db_prefix}shop_inventory',
					array(
						'userid' => 'int',
						'itemid' => 'int',
						'date' => 'int',
					),
					array(
						$memID,
						$itemid,
						time()
					),
					array()
				);
				// And finally, the stock gets one less item
				$smcFunc['db_query']('', '
					UPDATE {db_prefix}shop_items
					SET	count = count - {int:count}
					WHERE itemid = {int:itemid}',
					array(
						'count' => 1,
						'itemid' => $itemid,
					)
				);
			}
		}
		// He sent money
		else {
			// Add the amount to users
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}members
				SET shopMoney = shopMoney + {int:amount}
				WHERE ' .$ids,
				array(
					'member_ids' => $receivers,
					'amount' => $amount,
				)
			);
		}

		foreach ($receivers as $memID)
		{
			// Insert the information in the log
			$smcFunc['db_insert']('',
				'{db_prefix}shop_log_gift',
				array(
					'userid' => 'int',
					'receiver' => 'int',
					'amount' => 'int',
					'itemid' => 'int',
					'invid' => 'int',
					'message' => 'string',
					'is_admin' => 'int',
					'date' => 'int',
				),
				array(
					$userid,
					$memID,
					$amount,
					$itemid,
					0,
					$message,
					1,
					time()
				),
				array()
			);
		}
	}
}