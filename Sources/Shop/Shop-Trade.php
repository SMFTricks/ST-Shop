<?php

/**
 * @package ST Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2018, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

class ShopTrade extends ShopHome
{
	public static function Main()
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
		$context['trade_tabs'] = self::Tabs();

		// Display some trading stats
		// Load our stats file first
		require_once($sourcedir. '/Shop/Shop-Stats.php');
		// Get the stats
		$context['trade_stats'] = array(
			// Most bought items trade
			'most_traded' => array(
				'label' => $txt['Shop_stats_most_traded'],
				'icon' => 'most_traded.png',
				'function' => ShopStats::MostTraded(),
				'enabled' => true,
			),
			// most expensive items (Deals)
			'most_expensive' => array(
				'label' => $txt['Shop_stats_most_expensive'],
				'icon' => 'most_expensive.png',
				'function' => ShopStats::MostExpensive(),
				'enabled' => true,
			),
			// Top profit
			'top_profit' => array(
				'label' => $txt['Shop_stats_top_profit'],
				'icon' => 'top_profit.png',
				'function' => ShopStats::TopProfit(),
				'enabled' => true,
			),
			// Top profit
			'top_spent' => array(
				'label' => $txt['Shop_stats_top_spent'],
				'icon' => 'top_spent.png',
				'function' => ShopStats::TopSpent(),
				'enabled' => true,
			),
		);
	}

	public static function Tabs()
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
				'label' => $txt['Shop_trade_myprofile'],
			),
			'tradelog' => array(
				'action' => array('tradelog'),
				'label' => $txt['Shop_trade_log'],
			),
		);

		return $context['trade_tabs'];
	}

	public static function List()
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
		$context['page_description'] = $txt['Shop_trade_list_desc'];
		$context['template_layers'][] = 'Shop_main';
		$context['template_layers'][] = 'Shop_mainTrade';
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'items_list';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=tradelist',
			'name' => $txt['Shop_trade_list'],
		);
		// Sub-menu tabs
		$context['trade_tabs'] = self::Tabs();

		// Sub-menu tabs
		$context['trade_tabs']['search'] = array(
			'link' => '#searchuser',
			'label' => $txt['Shop_inventory_search'],
		);

		// Just a text to inform the user that he doesn't have enough money
		$context['shop']['notenough'] = sprintf($txt['Shop_item_buy_i_ne'], $modSettings['Shop_credits_suffix']);
		// Item images...
		$context['items_url'] = Shop::$itemsdir;
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
				'function' => 'self::Get',
				'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null),
			),
			'get_count' => array(
				'function' => 'self::Count',
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
						'function' => function($row){ return Shop::ShopImageFormat($row['image']);},
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
							return $message. '<br><a href="'. $scripturl. '?action=shop;sa=whohas;id='. $row['itemid']. '">'. $txt['Shop_buy_item_who_this']. '</a>';},
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
				'class' => 'floatright clear',
				'style' => 'padding: 7px 0 5px; margin-top: -44px;',
			);
		}

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');

		// We want to search an user?
		$searchuser = '
			<br class="clear" />
			<a id="searchuser"></a>
			<div class="title_bar">
				<h4 class="titlebg">
					'. $txt['Shop_inventory_search']. '
				</h4>
			</div>
			<div class="windowbg stripe">
				<form method="post" action="'. $scripturl.'?action=shop;sa=tradesearch">
					'. $txt['Shop_inventory_member_name']. '
					&nbsp;<input class="input_text" type="text" name="membername" id="membername" />
					<div id="membernameItemContainer"></div>
					<span class="smalltext">'. $txt['Shop_inventory_member_name_desc']. '</span>
					<br /><br />
					<input class="button_submit floatleft" type="submit" value="'. $txt['search']. '" />
					<input type="hidden" name="'. $context['session_var']. '" value="'. $context['session_id']. '">
				</form>
			</div>
			<script>
				var oAddMemberSuggest = new smc_AutoSuggest({
					sSelf: \'oAddMemberSuggest\',
					sSessionId: \''. $context['session_id']. '\',
					sSessionVar: \''. $context['session_var']. '\',
					sSuggestId: \'to_suggest\',
					sControlId: \'membername\',
					sSearchType: \'member\',
					sPostName: \'memberid\',
					sURLMask: \'action=profile;u=%item_id%\',
					sTextDeleteItem: \''. $txt['autosuggest_delete_item']. '\',
					sItemListContainerId: \'membernameItemContainer\'
				});
			</script>';

		// Add the search box
		$listOptions['additional_rows']['search'] = array(
			'position' => 'below_table_data',
			'value' => $searchuser,
		);

		// Let's finishem
		createList($listOptions);
	}

	public static function Search()
	{
		global $smcFunc, $user_info, $txt;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canTrade');

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
			redirectexit('action=shop;sa=mytrades');	
		else
			redirectexit('action=shop;sa=mytrades;u='. $id['id_member']);
	}

	public static function Profile()
	{
		global $context, $smcFunc, $sourcedir, $user_info, $memberContext, $scripturl, $modSettings, $txt;

		// What if the Inventories are disabled?
		if (empty($modSettings['Shop_enable_trade']))
			fatal_error($txt['Shop_currently_disabled_trade'], false);

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canTrade');

		// Did we get the user by name...
		if (isset($_REQUEST['user']))
			$memberResult = loadMemberData($_REQUEST['user'], true, 'profile');
		// ... or by id_member?
		elseif (!empty($_REQUEST['u']))
			$memberResult = loadMemberData((int) $_REQUEST['u'], false, 'profile');
		// If it was just ?sa=mytrades, view your own trade list.
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
		$context['trades']['whos'] = ((!empty($context['user']['is_owner'])) ? $txt['Shop_trade_myprofile'] : sprintf($txt['Shop_trade_profile'], $context['member']['name']));

		// Set all the page stuff
		require_once($sourcedir . '/Subs-List.php');
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $context['trades']['whos'];
		$context['page_description'] = ((!empty($context['user']['is_owner'])) ? $txt['Shop_trade_myprofile_desc'] : sprintf($txt['Shop_trade_profile_desc'], $context['member']['name']));
		$context['template_layers'][] = 'Shop_main';
		$context['template_layers'][] = 'Shop_mainTrade';
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'items_list';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=mytrades;u='.$context['id_member'],
			'name' => ((!empty($context['user']['is_owner'])) ? $txt['Shop_trade_myprofile'] : $context['trades']['whos']),
		);
		// Sub-menu tabs
		$context['trade_tabs'] = self::Tabs();

		// Just a text to inform the user that he doesn't have enough money
		$context['shop']['notenough'] = sprintf($txt['Shop_item_buy_i_ne'], $modSettings['Shop_credits_suffix']);
		// Item images...
		$context['items_url'] = Shop::$itemsdir;
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
				'function' => 'self::Get',
				'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null, false, $context['member']['id']),
			),
			'get_count' => array(
				'function' => 'self::Count',
				'params' => array(isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? $_REQUEST['cat'] : null, false, $context['member']['id']),
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
						'function' => function($row){ return Shop::ShopImageFormat($row['image']);},
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
						'style' => 'width: 25%',
					),
					'sort' =>  array(
						'default' => 'description DESC',
						'reverse' => 'description',
					),
				),
				'item_category' => array(
					'header' => array(
						'value' => $txt['Shop_item_category'],
						'class' => 'lefttext',
					),
					'data' => array(
						'function' => function($row){ global $txt; return $row['catid'] != 0 ? $row['category'] : $txt['Shop_item_uncategorized'];},
						'class' => 'lefttext',
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
						'style' => 'width: 15%',
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
				'item_actions' => array(
					'header' => array(
						'value' => !empty($context['user']['is_owner']) ? $txt['Shop_trade_mytrades_actions'] : $txt['Shop_item_buy'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ global $txt, $context, $user_info, $scripturl;
							//Viewing his own profile?
							if (!empty($context['user']['is_owner']))
								$message = '<a href="'. $scripturl. '?action=shop;sa=traderemove;id='. $row['id']. ';'. $context['session_var'] .'='. $context['session_id'] .'">'. $txt['Shop_item_remove_ftrade']. '</a>';
							// Show buying links
							else
							{
								// How much need the user to buy this item?
								if ($user_info['shopMoney'] < $row['tradecost']) 
									$message = $context['shop']['notenough'];
								//Enough money? Buy it!
								else
									$message = '<a href="'. $scripturl. '?action=shop;sa=trade2;id='. $row['id']. ';'. $context['session_var'] .'='. $context['session_id'] .'">'. $txt['Shop_item_buy_i']. '</a>';
							}
							return $message. '<br><a href="'. $scripturl. '?action=shop;sa=whohas;id='. $row['itemid']. '">'. $txt['Shop_buy_item_who_this']. '</a>';},
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

		// Remove owner if he's viewing his own items
		if (!empty($context['user']['is_owner']))
			unset($listOptions['columns']['item_owner']);

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
				'class' => 'floatright clear',
				'style' => 'padding: 7px 0 5px; margin-top: -44px;',
			);
		}

		// Let's finishem
		createList($listOptions);
	}

	public static function Count($cat = null, $members = true, $memID = 0)
	{
		global $smcFunc, $user_info;

		// By default we want his own profile
		if ($memID == 0)
			$memID = $user_info['id'];

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
				'userid' => $memID,
			)
		);
		$count = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);

		return $count;
	}

	public static function Get($start, $items_per_page, $sort, $cat = null, $members = true, $memID = 0)
	{
		global $context, $smcFunc, $user_info;

		// By default we want his own profile
		if ($memID == 0)
			$memID = $user_info['id'];

		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT p.id, p.itemid, p.userid, p.trading, p.tradecost, s.name, s.itemid, s.description, s.image, s.count, s.price, s.status, s.catid, c.name AS category, m.real_name AS user
			FROM {db_prefix}shop_inventory AS p
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
				LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = p.userid)
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
				'userid' => $memID,
			)
		);

		$context['shop_items_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_items_list'][] = $row;
		$smcFunc['db_free_result']($result);

		return $context['shop_items_list'];
	}

	public static function Transaction()
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
			SELECT p.id, p.itemid, p.trading, p.tradecost, p.userid, s.status, s.name, s.itemlimit
			FROM {db_prefix}shop_inventory AS p
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
			WHERE p.id = {int:id} AND p.trading = 1 AND s.status = 1',
			array(
				'id' => $id,
			)
		);
		$row = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

		// How many of this item does the user own?
		$limit = parent::CheckLimit($row['itemid']);

		// Is that id actually valid?
		// Also, let's check if this "smart" guy is not trying to buy a disabled item or an item that is not set for trading
		if (empty($row))
			fatal_error($txt['Shop_item_notfound'], false);
		// Already reached the limit?
		elseif (($row['itemlimit'] != 0) && ($row['itemlimit'] <= $limit))
			fatal_error($txt['Shop_item_limit_reached'], false);
		// Are you really so stupid to buy your own item?
		elseif ($row['userid'] == $user_info['id'])
			fatal_error($txt['Shop_item_notbuy_own'], false);
		// Fine... Do the user has enough money to buy this? This is just to avoid those "smart" guys
		elseif ($user_info['shopMoney'] < $row['tradecost'])
		{
			// We need to find out the difference
			$notenough = ($row['tradecost'] - $user_info['shopMoney']);
			fatal_lang_error('Shop_buy_item_notenough', false, array($modSettings['Shop_credits_suffix'], $row['name'], $notenough, $modSettings['Shop_credits_prefix']));
		}

		// The amount that the user received
		$totalrec = (int) ($row['tradecost'] - (($row['tradecost'] * $modSettings['Shop_items_trade_fee'])/100));
		// The actual fee he has to pay:
		$fee = (int) (($row['tradecost'] * $modSettings['Shop_items_trade_fee'])/100);
		// Send the info!
		parent::logBuy($row['itemid'], $user_info['id'], $row['tradecost'], $row['userid'], $fee, $row['id']);
		// Send a PM to the seller saying that his item was successfully bought
		self::sendPM($row['userid'], $row['name'], $row['tradecost'], $fee);
		// Let's get out of here and later we'll show a nice message
		redirectexit('action=shop;sa=trade3;id='. $id);
	}

	public static function Transaction2()
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

	public static function Remove()
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

	public static function sendPM($seller, $itemname, $amount, $fee)
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

	public static function logCount()
	{
		global $smcFunc, $user_info;

		// Count the log entries
		$logs = $smcFunc['db_query']('', '
			SELECT l.id, l.sellerid, l.userid, s.status
			FROM {db_prefix}shop_log_buy AS l
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
			WHERE l.sellerid <> 0 AND  (l.sellerid = {int:user} OR l.userid = {int:user}) AND s.status = 1',
			array(
				'user' => $user_info['id'],
			)
		);
		$count = $smcFunc['db_num_rows']($logs);
		$smcFunc['db_free_result']($logs);

		return $count;
	}

	public static function logGet($start, $items_per_page, $sort)
	{
		global $context, $smcFunc, $user_info;

		
		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT l.itemid, l.userid, l.sellerid, l.amount, l.fee, l.date, m1.real_name AS name_buyer, m2.real_name AS name_seller,
				s.name, s.image, s.status, s.catid, c.name AS category
			FROM {db_prefix}shop_log_buy AS l
			LEFT JOIN {db_prefix}members AS m1 ON (m1.id_member = l.userid)
			LEFT JOIN {db_prefix}members AS m2 ON (m2.id_member = l.sellerid)
			LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
			LEFT JOIN {db_prefix}shop_categories AS c ON (s.catid = c.catid)
			WHERE l.sellerid <> 0 AND  (l.sellerid = {int:user} OR l.userid = {int:user}) AND s.status = 1
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
				'user' => $user_info['id'],
			)
		);

		// Return the data
		$context['shop_logs_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_logs_list'][] = $row;
		$smcFunc['db_free_result']($result);

		return $context['shop_logs_list'];
	}

	public static function Log()
	{
		global $context, $scripturl, $sourcedir, $modSettings, $txt;

		require_once($sourcedir . '/Subs-List.php');
		$context['page_title'] = $txt['Shop_main_button']. ' - ' . $txt['Shop_trade_log'];
		$context['page_description'] = $txt['Shop_trade_log_desc'];
		$context['template_layers'][] = 'Shop_main';
		$context['template_layers'][] = 'Shop_mainTrade';
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'trade_log';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=tradelog',
			'name' => $txt['Shop_trade_log'],
		);
		// Sub-menu tabs
		$context['trade_tabs'] = self::Tabs();

		// The entire list
		$listOptions = array(
			'id' => 'trade_log',
			'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
			'base_href' => '?action=shop;sa=tradelog',
			'default_sort_col' => 'date',
			'get_items' => array(
				'function' => 'self::logGet',
			),
			'get_count' => array(
				'function' => 'self::logCount',
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
						'function' => function($row){ return Shop::ShopImageFormat($row['image']);},
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
						'style' => 'width: 18%'
					),
					'sort' =>  array(
						'default' => 'name DESC',
						'reverse' => 'name',
					),
				),
				'item_category' => array(
					'header' => array(
						'value' => $txt['Shop_item_category'],
						'class' => 'lefttext',
					),
					'data' => array(
						'function' => function($row){ global $txt; return $row['catid'] != 0 ? $row['category'] : $txt['Shop_item_uncategorized'];},
						'class' => 'lefttext',
						'style' => 'width: 15%',
					),
					'sort' =>  array(
						'default' => 'category DESC',
						'reverse' => 'category',
					),
				),
				'item_user' => array(
					'header' => array(
						'value' => $txt['Shop_logs_user'],
						'class' => 'lefttext',
					),
					'data' => array(
						'function' => function($row) { global $user_info, $scripturl;
							// You bought it. From who?
							if ($row['userid'] == $user_info['id'])
							{
								$name = $row['name_seller'];
								$id = $row['sellerid'];
							}
							// You sold it. To who?
							elseif ($row['sellerid'] == $user_info['id'])
							{
								$name = $row['name_buyer'];
								$id = $row['userid'];
							}

							// Format a link to his inventory
							$user = '<a href="'. $scripturl . '?action=shop;sa=inventory;u='.$id.'">'.$name.'</a>';
							return $user;
						},
						'style' => 'width: 12%',
					),
				),
				'amount' => array(
					'header' => array(
						'value' => $txt['Shop_logs_amount'],
						'class' => 'lefttext',
					),
					'data' => array(
						'function' => function($row){ global $user_info;
							// Show a "-" if you bought it and a "+" if you sold it
							if ($row['userid'] == $user_info['id'])
							{
								$sign = '-';
								$color = 'red';
							}
							elseif ($row['sellerid'] == $user_info['id'])
							{
								$sign = '+';
								$color = 'green';
							}
		
							return '<span style="color: '.$color.'">'.$sign.Shop::formatCash($row['amount']).'</span>';
						},
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
						'function' => function($row){ global $user_info;
							// Only show fee if you sold it
							if ($row['sellerid'] == $user_info['id'])
								$fee = $row['fee'];
							else
								$fee = 0;

							return Shop::formatCash($fee);
						},
						'style' => 'width: 12%'
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
}