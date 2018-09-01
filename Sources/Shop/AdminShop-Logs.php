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

function Shop_adminLogs()
{
	global $context, $txt, $modSettings;

	loadTemplate('ShopAdmin');

	$context['items_url'] = Shop::$itemsdir . '/';

	$subactions = array(
		'admin_money' => 'Shop_logsMoney',
		'admin_items' => 'Shop_logsItems',
		'buy' => 'Shop_logsBuy',
		'money' => 'Shop_logsMoney',
		'items' => 'Shop_logsItems',
		'trade' => 'Shop_logsTrade',
		'bank' => 'Shop_logsBank',
		'games' => 'Shop_logsGames',
	);

	// Disabled sections?
	if (empty($modSettings['Shop_enable_shop']))
	{
		unset($subactions['buy']);
		unset($subactions['admin_items']);
		if (empty($modSettings['Shop_enable_trade']))
			unset($subactions['trade']);
	}
	if (empty($modSettings['Shop_enable_gift']))
	{
		unset($subactions['money']);
		unset($subactions['items']);
	}
	if (empty($modSettings['Shop_enable_bank']))
		unset($subactions['bank']);
	if (empty($modSettings['Shop_enable_games']))
		unset($subactions['games']);

	$sa = isset($_GET['sa'], $subactions[$_GET['sa']]) ? $_GET['sa'] : 'admin_money';

	// Create the tabs for the template.
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['Shop_tab_logs'],
		'description' => $txt['Shop_tab_logs_desc'],
		'tabs' => array(
			'admin_money' => array('description' => sprintf($txt['Shop_logs_money_desc'], $modSettings['Shop_credits_suffix'])),
			'admin_items' => array('description' => $txt['Shop_logs_items_desc']),
			'buy' => array('description' => $txt['Shop_logs_buy_desc']),
			'money' => array('description' => sprintf($txt['Shop_logs_money_desc'], $modSettings['Shop_credits_suffix'])),
			'items' => array('description' => $txt['Shop_logs_items_desc']),
			'trade' => array('description' => $txt['Shop_logs_trade_desc']),
			'bank' => array('description' => $txt['Shop_logs_bank_desc']),
			'games' => array('description' => sprintf($txt['Shop_logs_games_desc'], $modSettings['Shop_credits_suffix'])),
		),
	);

	// Disabled sections?
	if (empty($modSettings['Shop_enable_shop']))
	{
		$context[$context['admin_menu_name']]['tab_data']['tabs']['buy']['disabled'] = true;
		$context[$context['admin_menu_name']]['tab_data']['tabs']['admin_items']['disabled'] = true;
		if (empty($modSettings['Shop_enable_trade']))
			$context[$context['admin_menu_name']]['tab_data']['tabs']['trade']['disabled'] = true;
	}
	if (empty($modSettings['Shop_enable_gift']))
	{
		$context[$context['admin_menu_name']]['tab_data']['tabs']['money']['disabled'] = true;
		$context[$context['admin_menu_name']]['tab_data']['tabs']['items']['disabled'] = true;
	}
	if (empty($modSettings['Shop_enable_bank']))
		$context[$context['admin_menu_name']]['tab_data']['tabs']['bank']['disabled'] = true;
	if (empty($modSettings['Shop_enable_games']))
		$context[$context['admin_menu_name']]['tab_data']['tabs']['games']['disabled'] = true;

	$subactions[$sa]();
}

function Shop_logsCount($type, $is_admin = false)
{
	global $smcFunc;

	// Sent money or item
	if ($type == 'items' || $type == 'money') {
		// Count the log entries
		$logs = $smcFunc['db_query']('', '
			SELECT l.id, l.amount, l.is_admin, l.itemid, s.status
			FROM {db_prefix}shop_log_gift AS l
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
			WHERE '.($type == 'money' ? ' l.itemid = 0' : ' l.amount = 0 AND s.status = 1') . (!empty($is_admin) ? ' AND l.is_admin = 1' : ''),
			array()
		);
	}
	// Bought items from the actual shop or traded
	elseif ($type == 'buy' || $type == 'trade') {
		// Count the log entries
		$logs = $smcFunc['db_query']('', '
			SELECT l.id, l.sellerid, s.status
			FROM {db_prefix}shop_log_buy AS l
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
			WHERE '. ($type == 'buy' ? ' l.sellerid = 0' : ' l.invid <> 0'). ' AND s.status = 1',
			array()
		);
	}
	// Transactions in the bank
	elseif ($type == 'bank') {
		// Count the log entries
		$logs = $smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}shop_log_bank',
			array()
		);
	}
	// Playing in the games room
	elseif ($type == 'games') {
		// Count the log entries
		$logs = $smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}shop_log_games',
			array()
		);
	}

	return $smcFunc['db_num_rows']($logs);
}

function Shop_logsGet($start, $items_per_page, $sort, $type, $is_admin = false)
{
	global $context, $smcFunc;

	// Sent money or item
	if ($type == 'items' || $type == 'money') {
		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT l.userid, l.receiver, l.amount, l.is_admin, l.itemid, l.invid, l.message, l.date, m1.real_name AS name_sender, m2.real_name AS name_receiver,
			       s.name, s.image, s.status
			FROM {db_prefix}shop_log_gift AS l
			LEFT JOIN {db_prefix}members AS m1 ON (m1.id_member = l.userid)
			LEFT JOIN {db_prefix}members AS m2 ON (m2.id_member = l.receiver)
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
			WHERE'. ($type == 'money' ? ' l.itemid = 0' : ' l.amount = 0 AND s.status = 1') . (!empty($is_admin) ? ' AND l.is_admin = 1' : ''). '
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			)
		);
	}
	// Bought item in shop or traded item
	elseif ($type == 'buy' || $type == 'trade') {
		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT l.itemid, l.userid, l.sellerid, l.amount, l.fee, l.date, m1.real_name AS name_buyer, m2.real_name AS name_seller,
			       s.name, s.image, s.status
			FROM {db_prefix}shop_log_buy AS l
			LEFT JOIN {db_prefix}members AS m1 ON (m1.id_member = l.userid)
			LEFT JOIN {db_prefix}members AS m2 ON (m2.id_member = l.sellerid)
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
			WHERE'. ($type == 'buy' ? ' l.sellerid = 0' : ' l.invid <> 0'). ' AND s.status = 1
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			)
		);
	}
	// Bank log for withdraws and deposits
	elseif ($type == 'bank') {
		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT l.userid, l.amount, l.fee, l.type, l.date, m.real_name
			FROM {db_prefix}shop_log_bank AS l
			LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.userid)
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			)
		);
	}
	// Money lost or won in the games
	elseif ($type == 'games') {
		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT l.userid, l.amount, l.game, l.date, m.real_name
			FROM {db_prefix}shop_log_games AS l
			LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.userid)
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
			)
		);
	}

	// Return the data
	$context['shop_logs_list'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$context['shop_logs_list'][] = $row;
	$smcFunc['db_free_result']($result);

	return $context['shop_logs_list'];
}

function Shop_logsMoney()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'moneylist';
	$context['page_title'] = $txt['Shop_tab_logs']. ' - ' . $txt['Shop_logs_money'];

	// Admin log?
	if ((isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'admin_money') || !isset($_REQUEST['sa']))
		$is_admin = true;
	else
		$is_admin = false;

	// The entire list
	$listOptions = array(
		'id' => 'moneylist',
		'title' => $txt['Shop_logs_money'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shoplogs;sa=money',
		'default_sort_col' => 'date',
		'get_items' => array(
			'function' => 'Shop_logsGet',
			'params' => array('money', $is_admin),
		),
		'get_count' => array(
			'function' => 'Shop_logsCount',
			'params' => array('money', $is_admin),
		),
		'no_items_label' => $txt['Shop_logs_empty'],
		'no_items_align' => 'center',
		'columns' => array(
			'from_user' => array(
				'header' => array(
					'value' => $txt['Shop_logs_user_sending'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'name_sender' => true,
						),
					),
					'style' => 'width: 18%',
				),
				'sort' =>  array(
					'default' => 'name_sender DESC',
					'reverse' => 'name_sender',
				),
			),
			'amount' => array(
				'header' => array(
					'value' => $txt['Shop_logs_amount'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ return Shop::formatCash($row['amount']);},
					'style' => 'width: 18%'
				),
				'sort' =>  array(
					'default' => 'amount DESC',
					'reverse' => 'amount',
				),
			),
			'for_user' => array(
				'header' => array(
					'value' => $txt['Shop_logs_user_receiving'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'receiver' => false,
							'name_receiver' => true,
						),
					),
					'style' => 'width: 18%',
				),
				'sort' =>  array(
					'default' => 'name_receiver DESC',
					'reverse' => 'name_receiver',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['Shop_logs_date'],
					'class' => ' lefttext',
				),
				'data' => array(
					'function' => function($row) {return timeformat($row['date']);},
					'style' => 'width: 25%',
				),
				'sort' =>  array(
					'default' => 'date DESC',
					'reverse' => 'date',
				),
			),
		),
		'additional_rows' => array(
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_logsItems()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'itemslist';
	$context['page_title'] = $txt['Shop_tab_logs']. ' - ' . $txt['Shop_logs_items'];

	// Admin log?
	if (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'admin_items')
		$is_admin = true;
	else
		$is_admin = false;

	// The entire list
	$listOptions = array(
		'id' => 'itemslist',
		'title' => $txt['Shop_logs_items'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shoplogs;sa=items',
		'default_sort_col' => 'date',
		'get_items' => array(
			'function' => 'Shop_logsGet',
			'params' => array('items', $is_admin),
		),
		'get_count' => array(
			'function' => 'Shop_logsCount',
			'params' => array('items', $is_admin),
		),
		'no_items_label' => $txt['Shop_logs_empty'],
		'no_items_align' => 'center',
		'columns' => array(
			'item_image' => array(
				'header' => array(
					'value' => $txt['Shop_item_image'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ return Shop::Shop_imageFormat($row['image']);},
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
					'style' => 'width: 25%'
				),
				'sort' =>  array(
					'default' => 'name DESC',
					'reverse' => 'name',
				),
			),
			'from_user' => array(
				'header' => array(
					'value' => $txt['Shop_logs_user_sending'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'name_sender' => true,
						),
					),
					'style' => 'width: 16%',
				),
				'sort' =>  array(
					'default' => 'name_sender DESC',
					'reverse' => 'name_sender',
				),
			),
			'for_user' => array(
				'header' => array(
					'value' => $txt['Shop_logs_user_receiving'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'receiver' => false,
							'name_receiver' => true,
						),
					),
					'style' => 'width: 16%',
				),
				'sort' =>  array(
					'default' => 'name_receiver DESC',
					'reverse' => 'name_receiver',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['Shop_logs_date'],
					'class' => ' lefttext',
				),
				'data' => array(
					'function' => function($row) {return timeformat($row['date']);},
					'style' => 'width: 25%',
				),
				'sort' =>  array(
					'default' => 'date DESC',
					'reverse' => 'date',
				),
			),
		),
		'additional_rows' => array(
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_logsBuy()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'buylist';
	$context['page_title'] = $txt['Shop_tab_logs']. ' - ' . $txt['Shop_logs_buy'];

	// The entire list
	$listOptions = array(
		'id' => 'buylist',
		'title' => $txt['Shop_logs_buy'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shoplogs;sa=buy',
		'default_sort_col' => 'date',
		'get_items' => array(
			'function' => 'Shop_logsGet',
			'params' => array('buy'),
		),
		'get_count' => array(
			'function' => 'Shop_logsCount',
			'params' => array('buy'),
		),
		'no_items_label' => $txt['Shop_logs_empty'],
		'no_items_align' => 'center',
		'columns' => array(
			'item_image' => array(
				'header' => array(
					'value' => $txt['Shop_item_image'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ return Shop::Shop_imageFormat($row['image']);},
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
					'style' => 'width: 25%'
				),
				'sort' =>  array(
					'default' => 'name DESC',
					'reverse' => 'name',
				),
			),
			'buyer' => array(
				'header' => array(
					'value' => $txt['Shop_logs_buyer'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'name_buyer' => true,
						),
					),
					'style' => 'width: 16%',
				),
				'sort' =>  array(
					'default' => 'name_buyer DESC',
					'reverse' => 'name_buyer',
				),
			),
			'amount' => array(
				'header' => array(
					'value' => $txt['Shop_logs_amount'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ return Shop::formatCash($row['amount']);},
					'style' => 'width: 16%'
				),
				'sort' =>  array(
					'default' => 'amount DESC',
					'reverse' => 'amount',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['Shop_logs_date'],
					'class' => ' lefttext',
				),
				'data' => array(
					'function' => function($row) {return timeformat($row['date']);},
					'style' => 'width: 25%',
				),
				'sort' =>  array(
					'default' => 'date DESC',
					'reverse' => 'date',
				),
			),
		),
		'additional_rows' => array(
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_logsTrade()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'tradelist';
	$context['page_title'] = $txt['Shop_tab_logs']. ' - ' . $txt['Shop_logs_trade'];

	// The entire list
	$listOptions = array(
		'id' => 'tradelist',
		'title' => $txt['Shop_logs_trade'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shoplogs;sa=trade',
		'default_sort_col' => 'date',
		'get_items' => array(
			'function' => 'Shop_logsGet',
			'params' => array('trade'),
		),
		'get_count' => array(
			'function' => 'Shop_logsCount',
			'params' => array('trade'),
		),
		'no_items_label' => $txt['Shop_logs_empty'],
		'no_items_align' => 'center',
		'columns' => array(
			'item_image' => array(
				'header' => array(
					'value' => $txt['Shop_item_image'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ return Shop::Shop_imageFormat($row['image']);},
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
					'style' => 'width: 20%'
				),
				'sort' =>  array(
					'default' => 'name DESC',
					'reverse' => 'name',
				),
			),
			'buyer' => array(
				'header' => array(
					'value' => $txt['Shop_logs_buyer'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'name_buyer' => true,
						),
					),
					'style' => 'width: 12%',
				),
				'sort' =>  array(
					'default' => 'name_buyer DESC',
					'reverse' => 'name_buyer',
				),
			),
			'amount' => array(
				'header' => array(
					'value' => $txt['Shop_logs_amount'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ return Shop::formatCash($row['amount']);},
					'style' => 'width: 15%'
				),
				'sort' =>  array(
					'default' => 'amount DESC',
					'reverse' => 'amount',
				),
			),
			'fee' => array(
				'header' => array(
					'value' => $txt['Shop_logs_fee'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ return Shop::formatCash($row['fee']);},
					'style' => 'width: 12%'
				),
				'sort' =>  array(
					'default' => 'fee DESC',
					'reverse' => 'fee',
				),
			),
			'seller' => array(
				'header' => array(
					'value' => $txt['Shop_logs_seller'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'sellerid' => false,
							'name_seller' => true,
						),
					),
					'style' => 'width: 12%',
				),
				'sort' =>  array(
					'default' => 'name_seller DESC',
					'reverse' => 'name_seller',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['Shop_logs_date'],
					'class' => ' lefttext',
				),
				'data' => array(
					'function' => function($row) {return timeformat($row['date']);},
					'style' => 'width: 25%',
				),
				'sort' =>  array(
					'default' => 'date DESC',
					'reverse' => 'date',
				),
			),
		),
		'additional_rows' => array(
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_logsBank()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'banklist';
	$context['page_title'] = $txt['Shop_tab_logs']. ' - ' . $txt['Shop_logs_bank'];

	// The entire list
	$listOptions = array(
		'id' => 'banklist',
		'title' => $txt['Shop_logs_bank'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shoplogs;sa=bank',
		'default_sort_col' => 'date',
		'get_items' => array(
			'function' => 'Shop_logsGet',
			'params' => array('bank'),
		),
		'get_count' => array(
			'function' => 'Shop_logsCount',
			'params' => array('bank'),
		),
		'no_items_label' => $txt['Shop_logs_empty'],
		'no_items_align' => 'center',
		'columns' => array(
			'from_user' => array(
				'header' => array(
					'value' => $txt['Shop_logs_user'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'real_name' => true,
						),
					),
					'style' => 'width: 12%',
				),
				'sort' =>  array(
					'default' => 'real_name DESC',
					'reverse' => 'real_name',
				),
			),
			'trans_type' => array(
				'header' => array(
					'value' => $txt['Shop_logs_transaction'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ global $txt;
						if ($row['type'] == 0 || $row['type'] == 1)
							return $txt['Shop_logs_trans_deposit'];
						else
							return $txt['Shop_logs_trans_withdraw'];
					},
					'style' => 'width: 7%',
				),
				'sort' => array(
					'default' => 'type DESC',
					'reverse' => 'type',
				),
			),
			'amount' => array(
				'header' => array(
					'value' => $txt['Shop_logs_amount'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ return Shop::formatCash($row['amount']);},
					'style' => 'width: 17%'
				),
				'sort' =>  array(
					'default' => 'amount DESC',
					'reverse' => 'amount',
				),
			),
			'fee' => array(
				'header' => array(
					'value' => $txt['Shop_logs_fee'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ global $txt;
						$fee = Shop::formatCash($row['fee']);
						if ($row['fee'] != 0)
							if ($row['type'] == 0 || $row['type'] == 2)
								$fee .= $txt['Shop_logs_fee_type1'];
							else
								$fee .= $txt['Shop_logs_fee_type2'];
						return $fee;
					},
					'style' => 'width: 18%'
				),
				'sort' =>  array(
					'default' => 'fee DESC',
					'reverse' => 'fee',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['Shop_logs_date'],
					'class' => ' lefttext',
				),
				'data' => array(
					'function' => function($row){ return timeformat($row['date']);},
					'style' => 'width: 21%',
				),
				'sort' =>  array(
					'default' => 'date DESC',
					'reverse' => 'date',
				),
			),
		),
		'additional_rows' => array(
		),
	);
	// Let's finishem
	createList($listOptions);
}

function Shop_logsGames()
{
	global $context, $scripturl, $sourcedir, $modSettings, $txt;

	require_once($sourcedir . '/Subs-List.php');
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'gameslist';
	$context['page_title'] = $txt['Shop_tab_logs']. ' - ' . $txt['Shop_logs_games'];

	// The entire list
	$listOptions = array(
		'id' => 'gameslist',
		'title' => $txt['Shop_logs_bank'],
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => '?action=admin;area=shoplogs;sa=games',
		'default_sort_col' => 'date',
		'get_items' => array(
			'function' => 'Shop_logsGet',
			'params' => array('games'),
		),
		'get_count' => array(
			'function' => 'Shop_logsCount',
			'params' => array('games'),
		),
		'no_items_label' => $txt['Shop_logs_empty'],
		'no_items_align' => 'center',
		'columns' => array(
			'from_user' => array(
				'header' => array(
					'value' => $txt['Shop_logs_user'],
					'class' => 'lefttext',
				),
				'data' => array(
					'sprintf' => array(
						'format' => '<a href="'. $scripturl . '?action=profile;u=%1$d">%2$s</a>',
						'params' => array(
							'userid' => false,
							'real_name' => true,
						),
					),
					'style' => 'width: 12%',
				),
				'sort' =>  array(
					'default' => 'real_name DESC',
					'reverse' => 'real_name',
				),
			),
			'game' => array(
				'header' => array(
					'value' => $txt['Shop_logs_games_type'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ global $scripturl;
						return '<a href="'. $scripturl. '?action=shop;sa=games;play='.$row['game'].'">'.ucfirst($row['game']).'</a>';
					},
					'style' => 'width: 7%',
				),
				'sort' => array(
					'default' => 'type DESC',
					'reverse' => 'type',
				),
			),
			'amount' => array(
				'header' => array(
					'value' => $txt['Shop_logs_amount'],
					'class' => 'lefttext',
				),
				'data' => array(
					'function' => function($row){ return Shop::formatCash($row['amount']);},
					'style' => 'width: 17%'
				),
				'sort' =>  array(
					'default' => 'amount DESC',
					'reverse' => 'amount',
				),
			),
			'date' => array(
				'header' => array(
					'value' => $txt['Shop_logs_date'],
					'class' => ' lefttext',
				),
				'data' => array(
					'function' => function($row){ return timeformat($row['date']);},
					'style' => 'width: 21%',
				),
				'sort' =>  array(
					'default' => 'date DESC',
					'reverse' => 'date',
				),
			),
		),
		'additional_rows' => array(
		),
	);
	// Let's finishem
	createList($listOptions);
}