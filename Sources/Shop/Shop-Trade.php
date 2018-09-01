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

function Shop_mainTrade()
{
	global $context, $txt, $scripturl, $user_info, $modSettings, $sourcedir;

	// What if the Trade center is disabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_trade'];
	$context['page_description'] = sprintf($txt['Shop_trade_desc'], $context['user']['name']);
	$context['template_layers'][] = 'Shop_main';
	$context['template_layers'][] = 'Shop_mainTrade';
	$context['sub_template'] = 'Shop_mainTrade';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=trade',
		'name' => $txt['Shop_shop_trade'],
	);
	// Sub-menu tabs
	$context['trade_tabs'] = Shop_tradeTabs();

	// Display some trading stats
	// Load our stats file first
	require_once($sourcedir. '/Shop/Shop-Stats.php');
	// Get the stats
	$context['trade_stats'] = array(
		// Most bought items trade
		'most_traded' => array(
			'label' => $txt['Shop_stats_most_traded'],
			'icon' => 'most_traded.png',
			'function' => Shop_statsMostTraded(),
			'enabled' => true,
		),
		// most expensive items (Deals)
		'most_expensive' => array(
			'label' => $txt['Shop_stats_most_expensive'],
			'icon' => 'most_expensive.png',
			'function' => Shop_statsMostExpensive(),
			'enabled' => true,
		),
		// Top profit
		'top_profit' => array(
			'label' => $txt['Shop_stats_top_profit'],
			'icon' => 'top_profit.png',
			'function' => Shop_statsTopProfit(),
			'enabled' => true,
		),
		// Top profit
		'top_spent' => array(
			'label' => $txt['Shop_stats_top_spent'],
			'icon' => 'top_spent.png',
			'function' => Shop_statsTopSpent(),
			'enabled' => true,
		),
	);
}

function Shop_tradeTabs()
{
	global $context, $modSettings, $txt;

	$context['trade_tabs'] = array(
		'trade' => array(
			'action' => array('trade'),
			'label' => $txt['Shop_trade_main'],
		),
		'tradelist' => array(
			'action' => array('tradelist', 'trade2', 'trade3', 'traderemove'),
			'label' => $txt['Shop_trade_list'],
		),
		'mytrades' => array(
			'action' => array('mytrades'),
			'label' => $txt['Shop_trade_profile'],
		),
		'tradelog' => array(
			'action' => array('tradelog'),
			'label' => $txt['Shop_trade_log'],
		),
	);

	return $context['trade_tabs'];
}

function Shop_tradeList()
{
	global $context, $smcFunc, $sourcedir, $scripturl, $modSettings, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Set all the page stuff
	require_once($sourcedir . '/Subs-List.php');
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_trade_list'];
	$context['template_layers'][] = 'Shop_main';
	$context['template_layers'][] = 'Shop_mainTrade';
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'items_list';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=tradelist',
		'name' => $txt['Shop_shop_trade'],
	);
	// Sub-menu tabs
	$context['trade_tabs'] = Shop_tradeTabs();

	// Just a text to inform the user that he doesn't have enough money
	$context['shop']['notenough'] = sprintf($txt['Shop_item_buy_i_ne'], $modSettings['Shop_credits_suffix']);
	// Item images...
	$context['items_url'] = Shop::$itemsdir . '/';
	// ... and categories
	$context['shop_categories_list'] = Shop::getCatList();
	$context['form_url'] = '?action=shop;sa=tradelist'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? ';cat='.$_REQUEST['cat'] : '');

	// The entire list
	$listOptions = array(
		'id' => 'items_list',
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => $context['form_url'],
		'default_sort_col' => 'item_name',
		'default_sort_dir' => 'DESC',
		'get_items' => array(
			'function' => 'Shop_tradeGet',
			'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null),
		),
		'get_count' => array(
			'function' => 'Shop_tradeCount',
			'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null),
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
					'style' => 'width: 10%',
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
			'item_owner' => array(
				'header' => array(
					'value' => $txt['Shop_item_member'],
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'user' => true
						),
					),
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'user DESC',
					'reverse' => 'user',
				),
			),
			'item_price' => array(
				'header' => array(
					'value' => $txt['Shop_item_price'],
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => $modSettings['Shop_credits_prefix']. '%1$d',
						'params' => array(
							'tradecost' => false,
						),
					),
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'tradecost DESC',
					'reverse' => 'tradecost',
				),
			),
			'item_buy' => array(
				'header' => array(
					'value' => $txt['Shop_item_buy'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ global $txt, $context, $user_info, $scripturl; 
						// How much need the user to buy this item?
						if ($user_info['shopMoney'] < $row['tradecost']) 
							$message = $context['shop']['notenough'];
						//Enough money? Buy it!
						else
							$message = '<a href="'. $scripturl. '?action=shop;sa=trade2;id='. $row['id']. ';'. $context['session_var'] .'='. $context['session_id'] .'">'. $txt['Shop_item_buy_i']. '</a>';
						return $message. '<br><a href="'. $scripturl. '?action=shop;sa=whohas;id='. $row['id']. '">'. $txt['Shop_buy_item_who_this']. '</a>';},
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'id DESC',
					'reverse' => 'id',
				),
			),
		),
		'additional_rows' => array(
		),
	);

	// Check first for categories
	if (!empty($context['shop_categories_list']))
	{
		// Create the select
		$catSelect = '
			<form action="'. $scripturl. $context['form_url']. '" method="post">
				<select name="cat" id="cat">
						<optgroup label="'. $txt['Shop_categories']. '">
							<option value="-1"'. (!isset($_REQUEST['cat']) || $_REQUEST['cat'] == -1 ? ' selected="selected"' : ''). '>'. $txt['Shop_categories_all']. '</option>
							<option value="0"'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] == 0 ? ' selected="selected"' : ''). '>'. $txt['Shop_item_uncategorized']. '</option>';
						// List the categories if there are
						foreach ($context['shop_categories_list'] as $category)
							$catSelect .= '<option value="'. $category['id']. '"'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] == $category['id'] ? ' selected="selected"' : ''). '>'. $category['name']. '</option>';
					$catSelect .= '</optgroup>
				</select>&nbsp;
				<input class="button_submit" type="submit" value="'. $txt['go']. '" />
			</form>';
		// Add the select to filter categories
		$listOptions['additional_rows']['catselect'] = array(
			'position' => 'top_of_list',
			'value' => $catSelect,
			'class' => 'floatright',
			'style' => 'padding: 7px 0 10px;',
		);
	}

	// Let's finishem
	createList($listOptions);
}

function Shop_tradeProfile()
{
	global $context, $smcFunc, $sourcedir, $scripturl, $modSettings, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Set all the page stuff
	require_once($sourcedir . '/Subs-List.php');
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_trade_list'];
	$context['template_layers'][] = 'Shop_main';
	$context['template_layers'][] = 'Shop_mainTrade';
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'items_list';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=mytrades',
		'name' => $txt['Shop_shop_trade'],
	);
	// Sub-menu tabs
	$context['trade_tabs'] = Shop_tradeTabs();

	// Just a text to inform the user that he doesn't have enough money
	$context['shop']['notenough'] = sprintf($txt['Shop_item_buy_i_ne'], $modSettings['Shop_credits_suffix']);
	// Item images...
	$context['items_url'] = Shop::$itemsdir . '/';
	// ... and categories
	$context['shop_categories_list'] = Shop::getCatList();
	$context['form_url'] = $scripturl. '?action=shop;sa=mytrades'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? ';cat='.$_REQUEST['cat'] : '');

	// The entire list
	$listOptions = array(
		'id' => 'items_list',
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=shop;sa=mytrades'. (isset($_REQUEST['sort']) && !empty($_REQUEST['sort']) ? ';sort='.$_REQUEST['sort'] : ''). (isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? ';cat='.$_REQUEST['cat'] : ''),
		'default_sort_col' => 'item_name',
		'default_sort_dir' => 'DESC',
		'get_items' => array(
			'function' => 'Shop_tradeGet',
			'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null, false),
		),
		'get_count' => array(
			'function' => 'Shop_tradeCount',
			'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null, false),
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
					'style' => 'width: 10%',
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
			'item_owner' => array(
				'header' => array(
					'value' => $txt['Shop_item_member'],
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'user' => true
						),
					),
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'user DESC',
					'reverse' => 'user',
				),
			),
			'item_price' => array(
				'header' => array(
					'value' => $txt['Shop_item_price'],
					'class' => 'centertext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => $modSettings['Shop_credits_prefix']. '%1$d',
						'params' => array(
							'tradecost' => false,
						),
					),
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'tradecost DESC',
					'reverse' => 'tradecost',
				),
			),
			'item_buy' => array(
				'header' => array(
					'value' => $txt['Shop_item_buy'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ global $txt, $context, $scripturl; 
						// Remove item from trade
						$message = '<a href="'. $scripturl. '?action=shop;sa=traderemove;id='. $row['id']. ';'. $context['session_var'] .'='. $context['session_id'] .'">'. $txt['Shop_item_remove_ftrade']. '</a>';
						return $message. '<br><a href="'. $scripturl. '?action=shop;sa=whohas;id='. $row['id']. '">'. $txt['Shop_buy_item_who_this']. '</a>';},
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'id DESC',
					'reverse' => 'id',
				),
			),
		),
		'additional_rows' => array(
			'removed' => array(
				'position' => 'above_column_headers',
				'value' => (isset($_REQUEST['removed']) ? '<div class="clear"></div><div class="infobox">'.$txt['Shop_item_trade_removed'].'</div>' : '')
			),
		),
	);

	// Check first for categories
	if (!empty($context['shop_categories_list']))
	{
		// Create the select
		$catSelect = '
			<form action="'. $scripturl. $context['form_url']. '" method="post">
				<select name="cat" id="cat">
						<optgroup label="'. $txt['Shop_categories']. '">
							<option value="-1"'. (!isset($_REQUEST['cat']) || $_REQUEST['cat'] == -1 ? ' selected="selected"' : ''). '>'. $txt['Shop_categories_all']. '</option>
							<option value="0"'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] == 0 ? ' selected="selected"' : ''). '>'. $txt['Shop_item_uncategorized']. '</option>';
						// List the categories if there are
						foreach ($context['shop_categories_list'] as $category)
							$catSelect .= '<option value="'. $category['id']. '"'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] == $category['id'] ? ' selected="selected"' : ''). '>'. $category['name']. '</option>';
					$catSelect .= '</optgroup>
				</select>&nbsp;
				<input class="button_submit" type="submit" value="'. $txt['go']. '" />
			</form>';
		// Add the select to filter categories
		$listOptions['additional_rows']['catselect'] = array(
			'position' => 'top_of_list',
			'value' => $catSelect,
			'class' => 'floatright',
			'style' => 'padding: 7px 0 10px;',
		);
	}

	// Let's finishem
	createList($listOptions);
}

function Shop_tradeCount($cat = null, $members = true)
{
	global $smcFunc, $user_info;

	$items = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.userid, p.trading, s.status, s.catid
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE s.status = 1 AND p.trading = 1' . ($cat != null ? '
		AND s.catid = {int:cat}' : ''). ($members == true ? '
		AND p.userid <> {int:userid}' : '
		AND p.userid = {int:userid}'),
		array(
			'cat' => $cat,
			'userid' => $user_info['id'],
		)
	);
	return $smcFunc['db_num_rows']($items);
}

function Shop_tradeGet($start, $items_per_page, $sort, $cat = null, $members = true)
{
	global $context, $smcFunc, $user_info;

	// Get a list of all the item
	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.userid, p.trading, p.tradecost, s.name, s.itemid, s.description, s.image, s.count, s.price, s.status, s.catid, c.name AS category, m.real_name AS user, m.id_group, g.online_color
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
			LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.userid)
			LEFT JOIN {db_prefix}membergroups AS g ON (g.id_group = m.id_group)
		WHERE s.status = 1 AND p.trading = 1' . ($cat != null ? '
		AND s.catid = {int:cat}' : ''). ($members == true ? '
		AND p.userid <> {int:userid}' : '
		AND p.userid = {int:userid}'). '
		ORDER by {raw:sort}
		LIMIT {int:start}, {int:maxindex}',
		array(
			'start' => $start,
			'maxindex' => $items_per_page,
			'sort' => $sort,
			'cat' => $cat,
			'userid' => $user_info['id'],
		)
	);

	$context['shop_items_list'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$context['shop_items_list'][] = $row;
	$smcFunc['db_free_result']($result);

	return $context['shop_items_list'];
}

function Shop_tradeTransaction()
{
	global $smcFunc, $context, $user_info, $modSettings, $scripturl, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_trade'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=trade',
		'name' => $txt['Shop_shop_trade'],
	);

	// Check session
	checkSession('request');

	// You cannot get here without an item
	if (!isset($_REQUEST['id']))
		fatal_error($txt['Shop_trade_something'], false);

	// Make sure is an int
	$id = (int) $_REQUEST['id'];

	// Get the item's information
	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.trading, p.tradecost, p.userid, s.status, s.name
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE p.id = {int:id} AND p.trading = 1',
		array(
			'id' => $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Is that id actually valid?
	// Also, let's check if this "smart" guy is not trying to buy a disabled item or an item that is not set for trading
	if (empty($row) || ($row['status'] == 0) || ($row['trading'] == 0))
		fatal_error($txt['Shop_item_notfound'], false);
	// Are you really so stupid to buy your own item?
	elseif ($row['userid'] == $user_info['id'])
		fatal_error($txt['Shop_item_notbuy_own'], false);
	// Fine... Do the user has enough money to buy this? This is just to avoid those "smart" guys
	elseif ($user_info['shopMoney'] < $row['tradecost']) {
		// We need to find out the difference
		$notenough = ($row['tradecost'] - $user_info['shopMoney']);
		fatal_lang_error('Shop_buy_item_notenough', false, array($modSettings['Shop_credits_suffix'], $row['name'], $notenough, $modSettings['Shop_credits_prefix']));
	}

	// The amount that the user received
	$totalrec = (int) ($row['tradecost'] - (($row['tradecost'] * $modSettings['Shop_items_trade_fee'])/100));
	// The actual fee he has to pay:
	$fee = (($row['tradecost'] * $modSettings['Shop_items_trade_fee'])/100);
	// Send the info!
	Shop_logBuy($row['itemid'], $user_info['id'], $row['tradecost'], $row['userid'], $fee, $row['id']);
	// Send a PM to the seller saying that his item was successfully bought
	Shop_tradePM($row['userid'], $row['name'], $row['tradecost'], $fee);
	// Let's get out of here and later we'll show a nice message
	redirectexit('action=shop;sa=trade3;id='. $id);
}

function Shop_tradeTransaction2()
{
	global $context, $smcFunc, $modSettings, $scripturl, $user_info, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_trade'];
	$context['sub_template'] = 'Shop_buyItem';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=trade',
		'name' => $txt['Shop_shop_trade'],
	);

	// You cannot get here without an item
	if (!isset($_REQUEST['id']))
		fatal_error($txt['Shop_trade_something'], false);

	$id = (int) $_REQUEST['id'];

	// Get the item's information
	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, s.name, s.can_use_item, s.status
		FROM {db_prefix}shop_inventory AS p
		LEFT JOIN {db_prefix}shop_items AS s ON (p.itemid = s.itemid)
		WHERE p.id = {int:id}',
		array(
			'id' => $id,
		)
	);
	$row = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// That item is not currently enabled!
	if (!isset($_REQUEST['id']) || empty($row) || ($row['status'] == 0))
		fatal_error($txt['Shop_item_notfound'], false);
	// Not an usable item?
	elseif (isset($_REQUEST['id']) && !empty($row) && ($row['can_use_item'] == 0))
		$context['shop']['item_bought'] = sprintf($txt['Shop_buy_item_bought'], $row['name'], $modSettings['Shop_credits_prefix'], $user_info['shopMoney'], $modSettings['Shop_credits_suffix']);
	// An usable item eh?
	elseif (isset($_REQUEST['id']) && !empty($row) && ($row['can_use_item'] == 1))
		$context['shop']['item_bought'] = sprintf($txt['Shop_buy_item_bought_use'], $row['name'], $modSettings['Shop_credits_prefix'], $user_info['shopMoney'], $modSettings['Shop_credits_suffix']);		
	// None of the above options? What are you doing here then?
	else
		$context['shop']['item_bought'] = $txt['Shop_buy_item_bought_error'];
}

function Shop_tradeRemove()
{
	global $context, $smcFunc, $user_info, $modSettings, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Make sure id is numeric
	$id = (int) $_REQUEST['id'];

	// Check session
	checkSession('request');

	// If nothing was chosen to delete (shouldn't happen, but meh)
	if (!isset($id))
		fatal_error($txt['Shop_item_delete_error'], false);

	// Search form the item
	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.userid
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE id = {int:id}',
		array(
			'id' => $id,
		)
	);

	$item = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// We didn't get results?
	if (empty($item))
		fatal_error($txt['Shop_item_delete_error'], false);
	// Is that YOUR item
	if ($item['userid'] != $user_info['id'])
		fatal_error($txt['Shop_item_notown'], false);

	// Remove item from trading
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}shop_inventory
		SET	trading = 0,
			tradecost = 0
		WHERE id = {int:id} AND userid = {int:user}',
		array(
			'id' => $item['id'],
			'user' => $user_info['id']
		)
	);

	// Send the user to the items list with a message
	redirectexit('action=shop;sa=mytrades;removed');
}

function Shop_tradePM($seller, $itemname, $amount, $fee)
{
	global $user_info, $sourcedir, $modSettings, $txt;

	// Who is sending the PM
	$pmfrom = array(
		'id' => 0,
		'name' => $txt['Shop_trade_notification_sold_from'],
		'username' => $txt['Shop_trade_notification_sold_from'],
	);

	// Who is receiving the PM		
	$pmto = array(
		'to' => array($seller),
		'bcc' => array()
	);
	// The message subject
	$subject = $txt['Shop_trade_notification_sold_subject'];
	$total = ($amount - $fee);

	if (!empty($modSettings['Shop_items_trade_fee']))
		// The actual message
		$message = sprintf($txt['Shop_trade_notification_sold_message2'], $user_info['id'], $user_info['name'], $itemname, Shop::formatCash($amount), Shop::formatCash($fee), Shop::formatCash($total));
	else
		// The actual message
		$message = sprintf($txt['Shop_trade_notification_sold_message1'], $user_info['id'], $user_info['name'], $itemname, Shop::formatCash($amount), $modSettings['Shop_credits_suffix']);

	// We need this file
	require_once($sourcedir . '/Subs-Post.php');
	// Send the PM
	sendpm($pmto, $subject, $message, false, $pmfrom);
}

class ShopTrade
{
	public static function itemWhat($id)
	{
		global $smcFunc;

		// Get the item's information
		$result = $smcFunc['db_query']('', '
			SELECT itemid, name, status
			FROM {db_prefix}shop_items
			WHERE itemid = {int:id}',
			array(
				'id' => $id,
			)
		);
		$row = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

		if (isset($_REQUEST['removed']) && isset($_REQUEST['id']) && !empty($row))
			$context['shop']['trade']['what'] = sprintf($txt['Shop_item_trade_removed'], $row['name']);

		// Return the result
		return $context['shop']['trade']['what'];

	}
}