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

class ShopBuy extends ShopHome
{
	public static function Main()
	{
		global $context, $smcFunc, $scripturl, $modSettings, $txt, $sourcedir;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canBuy');

		// Set all the page stuff
		require_once($sourcedir . '/Subs-List.php');
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_buy'];
		$context['template_layers'][] = 'Shop_main';
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'items_list';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=buy',
			'name' => $txt['Shop_shop_buy'],
		);

		// Just a text to inform the user that he doesn't have enough money
		$context['shop']['notenough'] = sprintf($txt['Shop_item_buy_i_ne'], $modSettings['Shop_credits_suffix']);
		// Item images...
		$context['items_url'] = Shop::$itemsdir;
		// ... and categories
		$context['shop_categories_list'] = Shop::getCatList();
		$context['form_url'] = '?action=shop;sa=buy'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? ';cat='.$_REQUEST['cat'] : '');

		// The entire list
		$listOptions = array(
			'id' => 'items_list',
			'items_per_page' => !empty($modSettings['Shop_items_perpage']) ? $modSettings['Shop_items_perpage'] : 15,
			'base_href' => $context['form_url'],
			'default_sort_col' => 'item_name',
			'default_sort_dir' => 'ASC',
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
						'style' => 'lefttext',
					),
					'sort' =>  array(
						'default' => 'name',
						'reverse' => 'name DESC',
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
				'item_options' => array(
					'header' => array(
						'value' => $txt['Shop_item_price'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ global $txt, $modSettings; return (($row['price'] == 0) ? '<i>' .$txt['Shop_item_free'].'</i>' : $txt['Shop_item_price']. ': '. $modSettings['Shop_credits_prefix']. $row['price']) . '<br />'. $txt['Shop_item_stock']. ': '. $row['count'];},
						'class' => 'centertext',
					),
					'sort' =>  array(
						'default' => 'price DESC',
						'reverse' => 'price',
					),
				),
				'item_buy' => array(
					'header' => array(
						'value' => $txt['Shop_item_buy'],
						'class' => 'centertext',
					),
					'data' => array(
						'function' => function($row){ global $txt, $context, $modSettings, $scripturl; 
							// If we don\'t have stock... Soldout!
							if ($row['count'] == 0)
								$message = $txt['Shop_buy_soldout'];
							// How much need the user to buy this item?
							elseif ($context['user']['shopMoney'] < $row['price'])
								$message = $context['shop']['notenough'];
							//Enough money? Buy it!
							else
								$message = '<a href="'. $scripturl. '?action=shop;sa=buy2;id='. $row['itemid']. ';'. $context['session_var'] .'='. $context['session_id'] .'">'. $txt['Shop_item_buy_i']. '</a>';
							return $message. '<br><a href="'. $scripturl. '?action=shop;sa=whohas;id='. $row['itemid']. '">'. $txt['Shop_buy_item_who_this']. '</a>';},
						'class' => 'centertext',
					),
					'sort' =>  array(
						'default' => 'itemid DESC',
						'reverse' => 'itemid',
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

	public static function Count($cat = null)
	{
		global $smcFunc;

		// Count the items
		$items = $smcFunc['db_query']('', '
			SELECT itemid, status, catid
			FROM {db_prefix}shop_items
			WHERE status = 1' . ($cat != null ? '
			AND catid = {int:cat}' : ''),
			array(
				'cat' => $cat,
			)
		);
		$count = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);

		return $count;
	}

	public static function Get($start, $items_per_page, $sort, $cat = null)
	{
		global $context, $smcFunc;

		// Get a list of all the item
		$result = $smcFunc['db_query']('', '
			SELECT s.name, s.itemid, s.description, s.image, s.module, s.function, s.count, s.price, s.status, s.catid, c.name AS category
			FROM {db_prefix}shop_items AS s
				LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
			WHERE s.status = 1' . ($cat != null ? '
			AND s.catid = {int:cat}' : ''). '
			ORDER by {raw:sort}
			LIMIT {int:start}, {int:maxindex}',
			array(
				'start' => $start,
				'maxindex' => $items_per_page,
				'sort' => $sort,
				'cat' => $cat,
			)
		);

		// Return the data
		$context['shop_items_list'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($result))
			$context['shop_items_list'][] = $row;
		$smcFunc['db_free_result']($result);

		return $context['shop_items_list'];
	}

	public static function Item()
	{
		global $smcFunc, $context, $user_info, $modSettings, $scripturl, $txt;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canBuy');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_buy'];
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=buy',
			'name' => $txt['Shop_shop_buy'],
		);

		// Check session
		checkSession('request');
		// You cannot get here without an item
		if (!isset($_REQUEST['id']))
			fatal_error($txt['Shop_buy_something'], false);

		// Make sure is an int
		$id = (int) $_REQUEST['id'];
		// Get the item's information
		$result = $smcFunc['db_query']('', '
			SELECT s.itemid, s.name, s.price, s.count, s.status, s.itemlimit
			FROM {db_prefix}shop_items AS s
			WHERE s.itemid = {int:id} AND s.status = 1',
			array(
				'id' => $id,
			)
		);
		$row = $smcFunc['db_fetch_assoc']($result);
		$smcFunc['db_free_result']($result);

		// We need to find out the difference if there's not enough money
		$notenough = ($row['price'] - $user_info['shopMoney']);
		// How many of this item does the user own?
		$limit = Shop_buyCheckLimit($id);

		// Is that id actually valid?
		// Also, let's check if this "smart" guy is not trying to buy a disabled item
		if (empty($row))
			fatal_error($txt['Shop_item_notfound'], false);
		// Already reached the limit?
		elseif (($row['itemlimit'] != 0) && ($row['itemlimit'] <= $limit))
			fatal_error($txt['Shop_item_limit_reached'], false);
		// Item valid and enabled then... Do we have items in stock?
		elseif ($row['count'] == 0)
			fatal_lang_error('Shop_buy_item_nostock', false, array($row['name']));
		// Fine... Do the user has enough money to buy this? This is just to avoid those "smart" guys
		elseif ($user_info['shopMoney'] < $row['price'])
			fatal_lang_error('Shop_buy_item_notenough', false, array($modSettings['Shop_credits_suffix'], $row['name'], $notenough, $modSettings['Shop_credits_prefix']));
		// Proceed
		else {
			// Handle item purchase and money deduction and log it
			Shop_logBuy($row['itemid'], $user_info['id'], $row['price']);
			// Let's get out of here and later we'll show a nice message
			redirectexit('action=shop;sa=buy3;id='. $id);
		}
	}

	public static function Item2()
	{
		global $context, $smcFunc, $modSettings, $scripturl, $user_info, $txt;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canBuy');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_buy'];
		$context['template_layers'][] = 'Shop_main';
		$context['sub_template'] = 'Shop_buyItem';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=buy',
			'name' => $txt['Shop_shop_buy'],
		);

		$id = (int) $_REQUEST['id'];
		// Get the item's information
		$result = $smcFunc['db_query']('', '
			SELECT itemid, name, can_use_item, status
			FROM {db_prefix}shop_items
			WHERE itemid = {int:id}',
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
		// Any of the above options? What are you doing here then?
		else
			$context['shop']['item_bought'] = $txt['Shop_buy_item_bought_error'];
	}
}