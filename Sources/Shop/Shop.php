<?php

/**
 * @package SA Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

function Shop()
{
	global $context, $scripturl, $modSettings, $user_info, $txt, $sourcedir;

	loadLanguage('Shop');
	loadtemplate('Shop');

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop',
		'name' => $txt['Shop_main_button'],
	);

	// What if the Shop is disabled? User shouldn't be able to access the Shop
	if (empty($modSettings['Shop_enable_shop']))
		fatal_error($txt['Shop_currently_disabled'], false);

	// Last but not less important. Are they actually allowed to Access the Shop? If not.. YOU SHALL NOT PASS. 
	// Anyway if he can Manage the Shop, there's no problem.
	if (!empty($modSettings['Shop_enable_shop']) && !allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
		isAllowedTo('shop_canAccess');

	// Maintenance. Only Shop admins can access if the shop it's enabled
	if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_maintenance']) && allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
		fatal_error($txt['Shop_currently_maintenance'], false);

	// Games Pass, get the days!
	if ($user_info['gamesPass'] <= time())
		$context['user']['gamedays'] = 0;
	else
		$context['user']['gamedays'] = round((($user_info['gamesPass'] - time()) / 86400));

	// Lovely copyright in shop pages
	$context['shop']['copyright'] = Shop::shopCredits();
	// Shop tabs
	$context['shop']['menu'] = Shoptabs();
	
	$subactions = array(
		'home' => array(
			'function' => 'Shop_mainHome',
		),
		'whohas' => array(
			'function' => 'Shop_itemWho',
		),
		'buy' => array(
			'function' => 'Shop_mainBuy',
			'file' => $sourcedir . '/Shop/Shop-Buy.php',
		),
		'buy2' => array(
			'function' => 'Shop_buyItem',
			'file' => $sourcedir . '/Shop/Shop-Buy.php',
		),
		'buy3' => array(
			'function' => 'Shop_buyItem2',
			'file' => $sourcedir . '/Shop/Shop-Buy.php',
		),
		'inventory' => array(
			'function' => 'Shop_mainInv',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'userinv' => array(
			'function' => 'Shop_profileInventory',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'invtrade' => array(
			'function' => 'Shop_invTrade',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'invtrade2' => array(
			'function' => 'Shop_invTrade2',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'invuse' => array(
			'function' => 'Shop_invUse',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'invused' => array(
			'function' => 'Shop_invUsed',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'invfav' => array(
			'function' => 'Shop_invFav',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'search' => array(
			'function' => 'Shop_invSearch',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'search2' => array(
			'function' => 'Shop_invSearch2',
			'file' => $sourcedir . '/Shop/Shop-Inventory.php',
		),
		'gift' => array(
			'function' => 'Shop_mainGift',
			'file' => $sourcedir . '/Shop/Shop-Gift.php',
		),
		'gift2' => array(
			'function' => 'Shop_giftSend',
			'file' => $sourcedir . '/Shop/Shop-Gift.php',
		),
		'gift3' => array(
			'function' => 'Shop_giftSend2',
			'file' => $sourcedir . '/Shop/Shop-Gift.php',
		),
		'senditem' => array(
			'function' => 'Shop_mainGift',
			'file' => $sourcedir . '/Shop/Shop-Gift.php',
		),
		'sendmoney' => array(
			'function' => 'Shop_mainGift',
			'file' => $sourcedir . '/Shop/Shop-Gift.php',
		),
		'bank' => array(
			'function' => 'Shop_mainBank',
			'file' => $sourcedir . '/Shop/Shop-Bank.php',
		),
		'bank2' => array(
			'function' => 'Shop_bankTrans',
			'file' => $sourcedir . '/Shop/Shop-Bank.php',
		),
		'trade' => array(
			'function' => 'Shop_mainTrade',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'tradelist' => array(
			'function' => 'Shop_tradeList',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'mytrades' => array(
			'function' => 'Shop_tradeProfile',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'tradelog' => array(
			'function' => 'Shop_tradeLog',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'trade2' => array(
			'function' => 'Shop_tradeTransaction',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'trade3' => array(
			'function' => 'Shop_tradeTransaction2',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'traderemove' => array(
			'function' => 'Shop_tradeRemove',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'tradesearch' => array(
			'function' => 'Shop_tradeSearch',
			'file' => $sourcedir . '/Shop/Shop-Trade.php',
		),
		'stats' => array(
			'function' => 'Shop_mainStats',
			'file' => $sourcedir . '/Shop/Shop-Stats.php',
		),
		'games' => array(
			'function' => 'Shop_mainGames',
			'file' => $sourcedir . '/Shop/Shop-Games.php',
		),
	);

	if (isset($_REQUEST['sa']) && array_key_exists($_REQUEST['sa'], $subactions) && ($_REQUEST['sa'] != 'home')) {
		$sa = $_REQUEST['sa'];
		if (isset($subactions[$sa]['file']))
			require_once($subactions[$sa]['file']);
	}
	else
		$sa = 'home';
	$subactions[$sa]['function']();
}

function Shoptabs()
{
	global $context, $txt;

	$context['shop_links'] = array(
		'home' => array(
			'action' => array('home', 'whohas'),
			'label' => $txt['Shop_shop_home'],
			'permission' => 'shop_canAccess',
			'enable' => 'Shop_enable_shop'
		),
		'buy' => array(
			'action' => array('buy','buy2','buy3'),
			'label' => $txt['Shop_shop_buy'],
			'permission' => 'shop_canBuy',
			'enable' => 'Shop_enable_shop'
		),
		'gift' => array(
			'action' => array('gift','senditem','sendmoney','gift2','gift3'),
			'label' => $txt['Shop_shop_gift'],
			'permission' => 'shop_canGift',
			'enable' => 'Shop_enable_gift'
		),
		'inventory' => array(
			'action' => array('inventory', 'invtrade', 'invtrade2', 'invuse', 'invused', 'ownswhat','search','search2'),
			'label' => $txt['Shop_shop_inventory'],
			'permission' => 'shop_viewInventory',
			'enable' => 'Shop_enable_shop'
		),
		'bank' => array(
			'action' => array('bank', 'bank2'),
			'label' => $txt['Shop_shop_bank'],
			'permission' => 'shop_canBank',
			'enable' => 'Shop_enable_bank'
		),
		'trade' => array(
			'action' => array('trade', 'tradelist', 'mytrades', 'tradelog', 'trade2','trade3','traderemove'),
			'label' => $txt['Shop_shop_trade'],
			'permission' => 'shop_canTrade',
			'enable' => 'Shop_enable_trade'
		),
		'games' => array(
			'action' => array('games'),
			'label' => $txt['Shop_shop_games'],
			'permission' => 'shop_playGames',
			'enable' => 'Shop_enable_games'
		),
		'stats' => array(
			'action' => array('stats'),
			'label' => $txt['Shop_shop_stats'],
			'permission' => 'shop_viewStats',
			'enable' => 'Shop_enable_stats'
		),
	);
	// Return the tabs
	return $context['shop_links'];
}

function Shop_mainHome()
{
	global $context, $user_info, $modSettings, $scripturl, $txt, $sourcedir;

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_home'];
	$context['sub_template'] = 'Shop_mainHome';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=home',
		'name' => $txt['Shop_shop_home'],
	);

	// Forum name + Shop
	$context['shop']['forum_welcome'] = sprintf($txt['Shop_welcome_to'], $context['forum_name']);

	// Welcome message
	$context['shop']['welcome'] = sprintf($txt['Shop_welcome_text'], $user_info['name'], $modSettings['Shop_credits_suffix']);

	// Display some general stats
	// Load our stats file first
	require_once($sourcedir. '/Shop/Shop-Stats.php');
	// Get the stats
	$context['home_stats'] = array(
		// Last items added
		'last_added' => array(
			'label' => $txt['Shop_stats_last_added'],
			'icon' => 'last_added.png',
			'function' => Shop_statsLastItems(),
			'enabled' => true,
		),
		// Last items bought
		'last_bought' => array(
			'label' => $txt['Shop_stats_last_bought'],
			'icon' => 'last_bought.png',
			'function' => Shop_statsLastBought(),
			'enabled' => allowedTo('shop_canBuy'),
		),
		// Richest pocket
		'richest_pocket' => array(
			'label' => $txt['Shop_stats_richest_pocket'],
			'icon' => 'richest_pocket.png',
			'function' => Shop_statsRichest('pocket'),
			'enabled' => true,
		),
		// Richest bank
		'richest_bank' => array(
			'label' => $txt['Shop_stats_richest_bank'],
			'icon' => 'richest_bank.png',
			'function' => Shop_statsRichest('bank'),
			'enabled' => allowedTo('shop_canBank') && !empty($modSettings['Shop_enable_bank']),
		),
	);
}

function Shop_buyCheckLimit($id)
{
	global $smcFunc, $user_info;

	// Count the items
	$items = $smcFunc['db_query']('', '
		SELECT itemid, userid
		FROM {db_prefix}shop_inventory
		WHERE itemid = {int:id} AND userid = {int:userid}',
		array(
			'id' => $id,
			'userid' => $user_info['id'],
		)
	);
	$count = $smcFunc['db_num_rows']($items);
	$smcFunc['db_free_result']($items);

	return $count;
}

function Shop_logBuy($itemid, $buyer, $amount, $seller = 0, $fee = 0, $invid = 0)
{
	global $smcFunc;

	// Remove the money from the user pocket
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}members
		SET	shopMoney = shopMoney - {int:paid}
		WHERE id_member = {int:userid}',
		array(
			'userid' => $buyer,
			'paid' => $amount,
		)
	);
	// If he's buying an item from the actual shop
	if ($seller == 0) {
		// Insert the item in the user inventory
		$smcFunc['db_insert']('',
			'{db_prefix}shop_inventory',
			array(
				'userid' => 'int',
				'itemid' => 'int',
				'date' => 'int',
			),
			array(
				$buyer,
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
	// Ah, so he is in the trade center?
	else {
		// Insert the item in the user inventory
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}shop_inventory
			SET userid = {int:userid}, trading = 0, tradecost = 0, tradedate = {int:date}
			WHERE id = {int:invid}',
			array(
				'invid' => $invid,
				'userid' => $buyer,
				'date' => time()
			)
		);
		// Add the amount to the seller pocket
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET	shopMoney = shopMoney + ({int:paid} - {int:fee})
			WHERE id_member = {int:userid}',
			array(
				'userid' => $seller,
				'paid' => $amount,
				'fee' => $fee,
			)
		);
	}

	// Insert the information in the log
	$smcFunc['db_insert']('',
		'{db_prefix}shop_log_buy',
		array(
			'itemid' => 'int',
			'invid' => 'int',
			'userid' => 'int',
			'amount' => 'int',
			'sellerid' => 'int',
			'fee' => 'int',
			'date' => 'int',
		),
		array(
			$itemid,
			$invid,
			$buyer,
			$amount,
			$seller,
			$fee,
			time()
		),
		array()
	);
}

function Shop_logGift($userid, $receiver, $message, $amount = 0, $itemid = 0, $invid = 0)
{
	global $smcFunc;

	// He sent an item
	if ($amount == 0) {
		// Transfer the item to the new user
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}shop_inventory
			SET	userid = {int:receiver}
			WHERE id = {int:invid}',
			array(
				'receiver' => $receiver,
				'invid' => $invid,
			)
		);
	}
	// He sent money
	else {
		// Remove the money from the user pocket
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET	shopMoney = shopMoney - {int:amount}
			WHERE id_member = {int:userid}',
			array(
				'userid' => $userid,
				'amount' => $amount,
			)
		);
		// Add the amount to the receiver pocket
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET	shopMoney = shopMoney + {int:amount}
			WHERE id_member = {int:receiver}',
			array(
				'receiver' => $receiver,
				'amount' => $amount,
			)
		);
	}

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
			$receiver,
			$amount,
			$itemid,
			$invid,
			$message,
			0,
			time()
		),
		array()
	);
}

function Shop_logBank($userid, $amount, $fee, $type = 0)
{
	global $smcFunc;

	// It's a deposit
	if ($type == 0 || $type == 1)
	{
		// Add the amount to the user's bank and remove it from pocket
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET ' . ($type == 0 ? '
				shopBank = shopBank + {int:amount},
				shopMoney = shopMoney - {int:amount} - {int:fee}' : '
				shopBank = shopBank + {int:amount} - {int:fee},
				shopMoney = shopMoney - {int:amount}'). '
			WHERE id_member = {int:userid}',
			array(
				'amount' => $amount,
				'fee' => $fee,
				'userid' => $userid,
			)
		);
	}
	// Withdraw then
	else {
		// Add the amount to the user's pocket and remove it from bank
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET  ' . ($type == 2 ? '
				shopMoney = shopMoney + {int:amount} - {int:fee},
				shopBank = shopBank - {int:amount}' : '
				shopMoney = shopMoney + {int:amount},
				shopBank = shopBank - {int:amount} - {int:fee}'). '
			WHERE id_member = {int:userid}',
			array(
				'amount' => $amount,
				'fee' => $fee,
				'userid' => $userid,
			)
		);
	}

	// Insert the information in the log
	$smcFunc['db_insert']('',
		'{db_prefix}shop_log_bank',
		array(
			'userid' => 'int',
			'amount' => 'int',
			'fee' => 'int',
			'type' => 'int',
			'date' => 'int',
		),
		array(
			$userid,
			$amount,
			$fee,
			$type,
			time()
		),
		array()
	);
}

function Shop_logGames($userid, $amount, $game = 'slots')
{
	global $smcFunc;

	// Add/remove the amount from the player
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}members
		SET	shopMoney = shopMoney + {int:amount}
		WHERE id_member = {int:userid}',
		array(
			'userid' => $userid,
			'amount' => $amount,
		)
	);

	// Insert the information in the log
	$smcFunc['db_insert']('',
		'{db_prefix}shop_log_games',
		array(
			'userid' => 'int',
			'amount' => 'int',
			'game' => 'string',
			'date' => 'int',
		),
		array(
			$userid,
			$amount,
			$game,
			time()
		),
		array()
	);
}

function Shop_itemWho()
{
	global $smcFunc, $context, $scripturl, $txt, $modSettings, $sourcedir;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// We got an item?
	if (!isset($_REQUEST['id']) || empty($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Our item ID
	$itemid = $_REQUEST['id'];

	// Get item name
	$result = $smcFunc['db_query']('', '
		SELECT name
		FROM {db_prefix}shop_items
		WHERE itemid = {int:id} AND status = 1
		LIMIT 1',
		array(
			'id' => $itemid
		)
	);
	$context['item'] = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// No matches?
	if (empty($context['item']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Set all the page stuff
	require_once($sourcedir . '/Subs-List.php');
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . sprintf($txt['Shop_buy_item_who'], $context['item']['name']);
	$context['page_description'] = sprintf($txt['Shop_whohas_desc'], $context['item']['name']);
	$context['template_layers'][] = 'Shop_main';
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'who_list';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=whohas;id='.$itemid,
		'name' => sprintf($txt['Shop_buy_item_who'], $context['item']['name']),
	);

	// The entire list
	$listOptions = array(
		'id' => 'who_list',
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => $scripturl.'?action=shop;sa=whohas;id='.$itemid,
		'default_sort_col' => 'item_count',
		'default_sort_dir' => 'DESC',
		'get_items' => array(
			'function' => 'Shop_whoGet',
			'params' => array($itemid),
		),
		'get_count' => array(
			'function' => 'Shop_whoCount',
			'params' => array($itemid),
		),
		'no_items_label' => $txt['Shop_inventory_no_items'],
		'no_items_align' => 'center',
		'columns' => array(
			'item_owner' => array(
				'header' => array(
					'value' => $txt['Shop_item_member'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'user' => true
						),
					),
					'class' => 'lefttext',
					'style' => 'width: 50%',
				),
				'sort' =>  array(
					'default' => 'user DESC',
					'reverse' => 'user',
				),
			),
			'item_count' => array(
				'header' => array(
					'value' => $txt['Shop_user_count'],
					'class' => 'centertext',
				),
				'data' => array(
					'db' => 'count',
					'class' => 'centertext',
					'style' => 'width: 50%',
				),
				'sort' => array(
					'default' => 'count DESC',
					'reverse' => 'count',
				),
			),
		),
	);

	// Let's finishem
	createList($listOptions);
}

function Shop_whoCount($itemid)
{
	global $smcFunc;

	// Count the items
	$items = $smcFunc['db_query']('', '
		SELECT p.itemid, p.userid, s.status
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE p.itemid = {int:id} AND s.status = 1
		GROUP BY p.itemid, p.userid, s.status',
		array(
			'id' => $itemid,
		)
	);
	$count = $smcFunc['db_num_rows']($items);
	$smcFunc['db_free_result']($items);

	return $count;
}

function Shop_whoGet($start, $items_per_page, $sort, $itemid)
{
	global $context, $smcFunc, $user_info;

	// Get a list of all the item
	$result = $smcFunc['db_query']('', '
		SELECT p.itemid, p.userid, COUNT(*) AS count, m.real_name AS user
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.userid)
		WHERE s.status = 1 AND p.itemid = {int:itemid}
		GROUP BY p.userid, p.itemid, user
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:maxindex}',
		array(
			'start' => $start,
			'maxindex' => $items_per_page,
			'sort' => $sort,
			'itemid' => $itemid,
		)
	);

	$context['item_who_list'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$context['item_who_list'][] = $row;
	$smcFunc['db_free_result']($result);

	return $context['item_who_list'];
}