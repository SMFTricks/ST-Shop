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

function Shop_mainInv()
{
	global $smcFunc, $context, $scripturl, $txt, $modSettings, $user_info, $memberContext, $sourcedir;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// Did we get the user by name...
	if (isset($_REQUEST['user']))
		$memberResult = loadMemberData($_REQUEST['user'], true, 'profile');
	// ... or by id_member?
	elseif (!empty($_REQUEST['u']))
		$memberResult = loadMemberData((int) $_REQUEST['u'], false, 'profile');
	// If it was just ?sa=inventory, view your own inventory.
	else
		$memberResult = loadMemberData($user_info['id'], false, 'profile');
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
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $context['inventory']['whos'];
	$context['template_layers'][] = 'Shop_main';
	$context['template_layers'][] = 'Shop_invTabs';
	$context['sub_template'] = 'show_list';
	$context['default_list'] = 'items_list';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=inventory;u='.$context['id_member'],
		'name' => (($user_info['id'] == $context['id_member']) ? $txt['Shop_inventory_myinventory'] : $context['inventory']['whos']),
	);
	// Sub-menu tabs
	$context['inventory_tabs'] = Shop_invTabs();
	// Item images...
	$context['items_url'] = Shop::$itemsdir . '/';
	// ... and categories
	$context['shop_categories_list'] = Shop::getCatList();
	$context['form_url'] = '?action=shop;sa=inventory;u='. $context['id_member']. (isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? ';cat='.$_REQUEST['cat'] : '');


	/* TODO */

	// Marking an item as fav?
	if (isset($_REQUEST['fav']) && isset($_REQUEST['itemid']))
		Shop_invFav();

	// The entire list
	$listOptions = array(
		'id' => 'items_list',
		'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
		'base_href' => $context['form_url'],
		'default_sort_col' => 'item_date',
		'default_sort_dir' => 'DESC',
		'get_items' => array(
			'function' => 'Shop_invGet',
			'params' => array($context['id_member'], isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null),
		),
		'get_count' => array(
			'function' => 'Shop_invCount',
			'params' => array($context['id_member'], isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null),
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
			'item_use' => array(
				'header' => array(
					'value' => $txt['Shop_item_use'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ global $txt, $context, $scripturl;
						// Is item usable?
						if ($row['can_use_item'] == 1)
							$message = '<a href="'. $scripturl. '?action=shop;sa=invuse;id='. $row['id']. '">'. $txt['Shop_item_useit']. '</a>';
						else
							$message = '<strong>'. $txt['Shop_item_notusable']. '</strong>';
						return $message;},
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'id DESC',
					'reverse' => 'id',
				),
			),
			'item_fav' => array(
				'header' => array(
					'value' => $txt['Shop_item_fav'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ global $context, $scripturl, $settings;
						// Is item usable?
						$fav = '<a href="'. $scripturl. '?action=shop;sa=invfav;id='. $row['id']. ';fav='. (($row['fav'] == 1) ? 0 : 1). ';'. $context['session_var'] .'='. $context['session_id'] .'">							
									<img src="'. $settings['default_images_url']. '/icons/shop/fav'. (($row['fav'] == 1) ? '' : '-empty'). '.png" />
								</a>';
						return $fav;
					},
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'fav DESC',
					'reverse' => 'fav',
				),
			),
			'item_trade' => array(
				'header' => array(
					'value' => $txt['Shop_item_trade'],
					'class' => 'centertext',
				),
				'data' => array(
					'function' => function($row){ global $txt, $context, $scripturl;
						return '<a href="'. $scripturl. '?action=shop;sa=invtrade;id='. $row['id']. '">'. $txt['Shop_item_trade_go']. '</a>';},
					'class' => 'centertext',
				),
				'sort' =>  array(
					'default' => 'id DESC',
					'reverse' => 'id',
				),
			)
		),
		'additional_rows' => array(
			'traded' => array(
				'position' => 'above_column_headers',
				'value' => (isset($_REQUEST['traded']) ? '<div class="clear"></div><div class="infobox">'.$txt['Shop_item_traded'].'</div>' : ''),
			),
		),
	);

	// Remove the columns only the owner should see
	if (empty($context['user']['is_owner']))
	{
		unset($listOptions['columns']['item_use']);
		unset($listOptions['columns']['item_fav']);

		if (!empty($modSettings['Shop_enable_trade']))
		{
			unset($listOptions['columns']['item_trade']);
			unset($listOptions['additional_rows']['trade']);
		}
	}

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

function Shop_invCount($memid, $cat = null)
{
	global $smcFunc;

	// Count the items
	$items = $smcFunc['db_query']('', '
		SELECT p.itemid, p.userid, p.trading, s.status, s.catid
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE p.trading = 0 AND userid = {int:id} AND s.status = 1' . ($cat != null ? '
		AND s.catid = {int:cat}' : ''),
		array(
			'cat' => $cat,
			'id' => $memid,
		)
	);
	return $smcFunc['db_num_rows']($items);
}

function Shop_invGet($start, $items_per_page, $sort, $memid, $cat = null)
{
	global $context, $smcFunc;

	// Get a list of all the item
	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.userid, p.trading, p.date, p.tradedate, p.fav, s.can_use_item, s.name, s.image, s.description, s.catid, s.status, c.name AS category, m.real_name
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
			LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.userid)
		WHERE p.trading = 0 AND m.id_member = {int:memid} AND s.status = 1' . ($cat != null ? '
		AND s.catid = {int:cat}' : ''). '
		ORDER by {raw:sort}
		LIMIT {int:start}, {int:maxindex}',
		array(
			'start' => $start,
			'maxindex' => $items_per_page,
			'sort' => $sort,
			'cat' => $cat,
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

function Shop_invTabs()
{
	global $context, $txt;

	$context['inventory_tabs'] = array(
		'inventory' => array(
			'action' => array('inventory', 'invtrade', 'invtrade2', 'invuse'),
			'label' => $txt['Shop_inventory_myinventory'],
		),
		'search' => array(
			'action' => array('search', 'search2'),
			'label' => $txt['Shop_inventory_search'],
		),
	);

	return $context['inventory_tabs'];
}

function Shop_invSearch()
{
	global $context, $scripturl, $txt;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_inventory'];
	$context['template_layers'][] = 'Shop_main';
	$context['template_layers'][] = 'Shop_invTabs';
	$context['sub_template'] = 'Shop_invSearch';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=inventory',
		'name' => $txt['Shop_inventory_search_i'],
	);
	$context['inventory_tabs'] = Shop_invTabs();

	// Load suggest.js
	loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
}

function Shop_invSearch2()
{
	global $smcFunc, $user_info, $txt;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	checkSession();

	if (empty($_REQUEST['membername']) && !isset($_REQUEST['u']))
		fatal_error($txt['Shop_user_empty'], false);

	elseif (empty($_REQUEST['membername']) && isset($_REQUEST['u']))
			$id['id_member'] = (int) $_REQUEST['u'];

	elseif (!empty($_REQUEST['membername']) && !isset($_REQUEST['u']))
	{
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
			$id = $smcFunc['db_fetch_assoc']($request);
			$smcFunc['db_free_result']($request);
		}
	}

	if (empty($id))
		fatal_error($txt['Shop_user_unable_tofind'], false);

	// Why are you looking for your OWN user?
	if ($id['id_member'] == $user_info['id'])
		redirectexit('action=shop;sa=inventory');	
	else
		redirectexit('action=shop;sa=inventory;u='. $id['id_member']);
}

function Shop_invTrade()
{
	global $smcFunc, $user_info, $context, $scripturl, $modSettings, $txt;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// The trade center is actually enabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Is the user is allowed to trade items?
	if (!allowedTo('shop_canTrade') && !allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Do we have an item? No? Bad luck...
	if (empty($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Item id
	$tradeid = (int) $_REQUEST['id'];

	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.trading, p.userid, s.name
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE id = {int:tradeid}',
		array(
			'tradeid' => $tradeid,
		)
	);

	$item = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Load up the linktree!
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_item_trade_go'];
	$context['template_layers'][] = 'Shop_main';
	$context['template_layers'][] = 'Shop_invTabs';
	$context['sub_template'] = 'Shop_invTradeSet';
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=invtrade;id=' . $tradeid,
		'name' => sprintf($txt['Shop_item_trade_go'], $item['name'])
	);
	// Sub-menu tabs
	$context['inventory_tabs'] = Shop_invTabs();

	// No item found
	if (empty($item))
		fatal_error($txt['Shop_item_notfound'], false);
	// That item isn't yours, thanks for trying
	elseif ($item['userid'] != $user_info['id'])
		fatal_error($txt['Shop_item_notown'], false);
	// You cannot trade the same item twice
	elseif ($item['trading'] == 1)
		fatal_error($txt['Shop_item_alreadytraded'], false);
}

function Shop_invTrade2()
{
	global $smcFunc, $user_info, $context, $scripturl, $modSettings, $txt;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// The trade center is actually enabled?
	if (empty($modSettings['Shop_enable_trade']))
		fatal_error($txt['Shop_currently_disabled_trade'], false);

	// Is the user is allowed to trade items?
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canTrade');

	// Do we have an item? No? Bad luck...
	if (empty($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Item info
	$tradeid = (int) $_REQUEST['id'];
	$tradecost = (int) $_REQUEST['tradecost'];

	// Make sure we have a price
	if (empty($_REQUEST['tradecost']))
		fatal_error($txt['Shop_item_notprice'], false);
	// No tricks with the price...
	elseif ($tradecost <= 0)
		fatal_error($txt['Shop_item_price_notnegative'], false);

	// Check session
	checkSession();

	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.trading, p.userid, s.name
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE id = {int:tradeid}',
		array(
			'tradeid' => $tradeid,
		)
	);

	$item = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// Load up the linktree!
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_item_trade_go'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=invtrade;id=' . $tradeid,
		'name' => sprintf($txt['Shop_item_trade_go'], $item['name'])
	);

	// No item found
	if (empty($item))
		fatal_error($txt['Shop_item_notfound'], false);
	// That item isn't yours, thanks for trying
	elseif ($item['userid'] != $user_info['id'])
		fatal_error($txt['Shop_item_notown'], false);
	// You cannot trade the same item twice
	elseif ($item['trading'] == 1)
		fatal_error($txt['Shop_item_alreadytraded'], false);

	// Put the item into the trade center
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}shop_inventory
		SET	trading = 1,
			tradecost = {int:tradecost}
		WHERE id = {int:id}',
		array(
			'id' => $item['id'],
			'tradecost' => $tradecost,
		)
	);

	// Tell the user that the item was added successfully
	redirectexit('action=shop;sa=inventory;traded');
}

function Shop_invUse()
{
	global $smcFunc, $user_info, $context, $scripturl, $sourcedir, $boarddir, $item_info, $txt;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// Do we have an item? No? Bad luck...
	if (empty($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Get the item id
	$use = (int) $_REQUEST['id'];

	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.trading, p.userid, s.name, s.info1, s.info2, s.info3, s.info4, s.can_use_item, s.status, s.module, s.delete_after_use, m.file
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			LEFT JOIN {db_prefix}shop_modules AS m ON (m.id = s.module)
		WHERE p.id = {int:use} AND s.status = 1',
		array(
			'use' => $use,
		)
	);

	$item = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// No item? No lucky then
	if (empty($item))
		fatal_error($txt['Shop_item_notfound'], false);
	// You cannot use items you don't own
	elseif ($item['userid'] != $user_info['id'])
		fatal_error($txt['Shop_item_notown_use'], false);
	// The item should be usable
	elseif ($item['can_use_item'] == 0)
		fatal_error($txt['Shop_item_not_usable'], false);
	// The item should be usable
	elseif ($item['trading'] == 1)
		fatal_error($txt['Shop_item_currently_traded'], false);

	// Set page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_inventory']. ' - ' . $item['name'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=invuse=' . $use,
		'name' => sprintf($txt['Shop_item_using'], $item['name'])
	);
	$context['sub_template'] = 'Shop_invUseitem';

	// Additional info, just in case it's needed in getUseInput() function
	$item_info[1] = $item['info1'];
	$item_info[2] = $item['info2'];
	$item_info[3] = $item['info3'];
	$item_info[4] = $item['info4'];

	// Is the item still there?
	if (!file_exists($boarddir . Shop::$modulesdir . '/' . $item['file'] . '.php'))
	{
		// Update the item information
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}shop_items
			SET
				status = 0
			WHERE itemid = {int:id}
			LIMIT 1',
			array(
				'id' => $item['itemid'],
			)
		);
		fatal_error($txt['Shop_module_notfound_admin'], false);
	}

	//... and the actual item.
	require_once($boarddir . Shop::$modulesdir . '/' . $item['file'] . '.php');

	// Create the item, ...
	$context['shop']['use']['name'] = $item['name'];
	eval('$temp = new item_' . $item['file'] . ';');
	$context['shop']['use']['input'] = $temp->getUseInput();
	
}

function Shop_invUsed()
{
	global $smcFunc, $user_info, $context, $scripturl, $sourcedir, $boarddir, $item_info, $txt;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// Do we have an item? No? Bad luck...
	if (empty($_REQUEST['id']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Get the item id
	$use = (int) $_REQUEST['id'];

	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.trading, p.userid, s.name, s.info1, s.info2, s.info3, s.info4, s.can_use_item, s.module, s.delete_after_use, m.file
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			LEFT JOIN {db_prefix}shop_modules AS m ON (m.id = s.module)
		WHERE p.id = {int:use}',
		array(
			'use' => $use,
		)
	);

	$item = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// No item? No lucky then
	if (empty($item))
		fatal_error($txt['Shop_item_notfound'], false);
	// You cannot use items you don't own
	elseif ($item['userid'] != $user_info['id'])
		fatal_error($txt['Shop_item_notown_use'], false);
	// The item should be usable
	elseif ($item['can_use_item'] == 0)
		fatal_error($txt['Shop_item_not_usable'], false);
	// The item should be usable
	elseif ($item['trading'] == 1)
		fatal_error($txt['Shop_item_currently_traded'], false);

	// Set page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_inventory']. ' - ' . $item['name'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=invused=' . $use,
		'name' => sprintf($txt['Shop_item_using'], $item['name'])
	);
	$context['sub_template'] = 'Shop_invUsed';

	// Check session
	checkSession();

	// Additional info, just in case it's needed in getUseInput() function
	$item_info[1] = $item['info1'];
	$item_info[2] = $item['info2'];
	$item_info[3] = $item['info3'];
	$item_info[4] = $item['info4'];

	//... and the actual item.
	require_once($boarddir . Shop::$modulesdir . '/' . $item['file'] . '.php');
	$context['shop']['use']['name'] = $item['name'];

	// Create the item, ...
	eval('$temp = new item_' . $item['file'] . ';');
	$context['shop']['used']['input'] = $temp->onUse();

	// Dow we need to remove the item after use?
	if ($item['delete_after_use'] == 1)
	{
		$smcFunc['db_query']('', '
			DELETE FROM {db_prefix}shop_inventory
			WHERE id = {int:id}
			LIMIT 1',
			array(
				'id' => $use,
			)
		);
	}
}

function Shop_invFav()
{
	global $smcFunc, $txt, $user_info;

	// Check if he is allowed to access this section
	if (!allowedTo('shop_viewInventory') && !allowedTo('shop_canManage'))
		isAllowedTo('shop_viewInventory');

	// Make sure we got the info
	if (empty($_REQUEST['id']) || !isset($_REQUEST['id']) || !isset($_REQUEST['fav']))
		fatal_error($txt['Shop_item_notfound'], false);

	// Get the item id
	$itemid = (int) $_REQUEST['id'];
	// Fav value
	$fav = (int) $_REQUEST['fav'];

	// Check session
	checkSession('get');

	$result = $smcFunc['db_query']('', '
		SELECT p.id, p.itemid, p.trading, p.userid, p.fav, s.status
		FROM {db_prefix}shop_inventory AS p
		LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE p.id = {int:id} AND p.trading = 0 AND s.status = 1',
		array(
			'id' => $itemid,
		)
	);

	$item = $smcFunc['db_fetch_assoc']($result);
	$smcFunc['db_free_result']($result);

	// No item? No lucky then
	if (empty($item))
		fatal_error($txt['Shop_item_notfound'], false);
	// You cannot fav items you don't own
	elseif ($item['userid'] != $user_info['id'])
		fatal_error($txt['Shop_item_notown'], false);
	// Trading? Shouldn't happen
	elseif ($item['trading'] == 1)
		fatal_error($txt['Shop_item_alreadytraded'], false);
	// Success!
	else
	{
		// Update it's FAValue
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}shop_inventory
			SET	fav = {int:fav}
			WHERE id = {int:id}',
			array(
				'id' => $itemid,
				'fav' => $fav,
			)
		);
		// Out!
		redirectexit('action=shop;sa=inventory');
	}
}

function Shop_profileInventory($profile = array())
{
	global $smcFunc, $txt, $scripturl, $modSettings, $memberContext, $context, $user_info;

	// Set the id
	if (isset($profile['id_member']))
	{
		// Allowed to see inventories?
		if (!allowedTo('shop_viewInventory'))
			return false;
		$memID = $profile['id_member'];
	}
	else
	{
		// Allowed to see inventories?
		isAllowedTo('shop_viewInventory');
		$memID = (int) $_REQUEST['id'];
	}

	// Load member data!
	$id_member = $memID;
	// Let's have some information about this member ready, too.s
	$memberResult = loadMemberData((int) $memID, false, 'profile');
	list ($id_member) = $memberResult;
	loadMemberContext($id_member);
	$context['member'] = $memberContext[$id_member];
	$context['id_member'] = $context['member']['id'];

	if (!isset($profile['id_member']))
	{
		$context['template_layers'] = array();
		$context['from_ajax'] = true;

		// Help file
		loadLanguage('Help');

		// Redirect to home if we don't have an user
		if (!isset($_REQUEST['id']))
			redirectexit('action=shop');

		// Modify this string...
		$context['inventory']['whos'] = sprintf($txt['Shop_inventory_viewing_who'], $context['member']['name']);

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $context['inventory']['whos'];
		$context['sub_template'] = 'Shop_invBasic';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=userinv;id='. $context['id_member'],
			'name' => $txt['Shop_posting_inventory'],
		);
	}

	// We got a result?
	if (!$memID)
		fatal_lang_error('not_a_user', false, 404);

	// Get a list of all the item
	$result = $smcFunc['db_query']('', '
		SELECT p.userid, p.trading, '. (!empty($modSettings['Shop_inventory_show_same_once']) ? 'SUM(p.fav)' : 'p.fav'). ' AS favo, s.name, s.image, s.description, s.status
		FROM {db_prefix}shop_inventory AS p
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
		WHERE p.trading = 0 AND p.userid = {int:memid} AND s.status = 1' . (!empty($modSettings['Shop_inventory_show_same_once']) ? '
		GROUP BY p.itemid, p.userid, p.trading, s.name, s.status, s.image, s.description' : ''). '
		ORDER BY favo DESC, p.date DESC'. (isset($profile['id_member']) ? '
		LIMIT {int:max}' : ''),
		array(
			'memid' => $memID,
			'max' => isset($profile['id_member']) ? (empty($modSettings['Shop_inventory_items_num']) ? 5 : $modSettings['Shop_inventory_items_num']) : '',
		)
	);

	$context['shop_items_list'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$context['shop_items_list'][] = array(
			'image' => Shop::Shop_imageFormat($row['image'], un_htmlspecialchars($row['description'])),
			'name' => $row['name'],
		);
	$smcFunc['db_free_result']($result);
	$context['shop_items_list']['user'] = $context['member']['name'];
	$context['shop_items_list']['userid'] = $context['id_member'];

	if (isset($profile['id_member']))
		return $context['shop_items_list'];
}