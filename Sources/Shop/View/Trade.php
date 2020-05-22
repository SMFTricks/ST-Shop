<?php

/**
 * @package ST Shop
 * @version 3.2
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\View;

use Shop\Shop;
use Shop\Helper\Database;
use Shop\Helper\Format;
use Shop\Helper\Images;
use Shop\Helper\Log;
use Shop\Helper\Notify;

if (!defined('SMF'))
	die('No direct access...');

class Trade
{
	/**
	 * @var object Send notifications to the user receiving gifts.
	 */
	private $_notify;

	/**
	 * @var object Log any information regarding gifts.
	 */
	private $_log;
	
	/**
	 * @var array Save the section tabs.
	 */
	protected $_tabs = [];

	/**
	 * @var int The item being traded.
	 */
	private $_trade;

	/**
	 * @var array Information about the item being traded.
	 */
	private $_item;

	/**
	 * @var int The cost/amount for a certain item.
	 */
	private $_amount;

	/**
	 * @var array Load user data.
	 */
	private $_member = [];

	/**
	 * Trade::__construct()
	 *
	 * Set the tabs for the section and create instance of needed objects
	 */
	function __construct()
	{
		// Build the tabs for this section
		$this->tabs();

		// Prepare to log the gift
		$this->_log = new Log;

		// Notify
		$this->_notify = new Notify;
	}

	public function main()
	{
		global $context, $scripturl, $user_info, $modSettings;

		// What if the Trade center is disabled?
		if (empty($modSettings['Shop_enable_trade']))
			fatal_error(Shop::getText('currently_disabled_trade'), false);

		// Check if user is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canTrade');

		// Set all the page stuff
		$context['page_title'] = Shop::getText('main_button') . ' - ' . Shop::getText('main_trade');
		$context['page_welcome'] = Shop::getText('trade_welcome');
		$context['page_description'] = sprintf(Shop::getText('trade_desc'), $user_info['name']);
		// We want to do something a bit different for the trade center
		$context['template_layers'][] = 'trade';
		// And then just do the same as usual!
		$context['template_layers'][] = 'options';
		$context['sub_template'] = 'trade';
		$context['linktree'][] = [
			'url' => $scripturl . '?action=shop;sa=trade',
			'name' => Shop::getText('main_trade'),
		];
		// Sub-menu tabs
		$context['section_tabs'] = $this->_tabs;

		// Display some trading stats
		// Load our stats file first
		/*require_once($sourcedir. '/Shop/Shop-Stats.php');
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
		);*/
	}

	public function tabs()
	{
		$this->_tabs = [
			'trade' => [
				'action' => ['trade'],
				'label' => Shop::getText('trade_main'),
			],
			'tradelist' => [
				'action' => ['tradelist', 'trade2', 'trade3', 'traderemove'],
				'label' => Shop::getText('trade_list'),
			],
			'mytrades' => [
				'action' => ['mytrades', 'invtrade'],
				'label' => Shop::getText('trade_myprofile'),
			],
			'tradelog' => [
				'action' => ['tradelog'],
				'label' => Shop::getText('trade_log'),
			],
		];
	}

	public function set()
	{
		global $context, $scripturl, $modSettings;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_viewInventory');

		// The trade center is actually enabled?
		if (empty($modSettings['Shop_enable_trade']))
			fatal_error(Shop::getText('currently_disabled_trade'), false);

		// Is the user is allowed to trade items?
		if (!allowedTo('shop_canTrade') && !allowedTo('shop_canManage'))
			isAllowedTo('shop_canTrade');

		// Do we have an item? No? Bad luck...
		if (empty($_REQUEST['id']) || !isset($_REQUEST['id']))
			fatal_error(Shop::getText('item_notfound'), false);

		// Item id
		$this->_trade = (int) $_REQUEST['id'];

		// Set all the page stuff
		$context['page_title'] = Shop::getText('main_button') . ' - ' . Shop::getText('item_trade_go');
		$context['page_welcome'] = Shop::getText('main_trade') . ' - ' . Shop::getText('item_trade_go');
		$context['page_description'] = Shop::getText('item_trade_desc');
		$context['template_layers'][] = 'trade';
		$context['template_layers'][] = 'options';
		$context['sub_template'] = 'set_trade';
		$context['linktree'][] = [
			'url' => $scripturl . '?action=shop;sa=invtrade;id=' . $this->_trade,
			'name' => Shop::getText('item_trade_go'),
		];
		// Sub-menu tabs
		$context['section_tabs'] = $this->_tabs;

		// Validate the info
		$this->validate_set($this->_trade);

		// I want to use the info so the user can remember
		$context['shop_item'] = $this->_item;
	}

	public function set2()
	{
		global $context, $scripturl, $modSettings;

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_viewInventory');

		// The trade center is actually enabled?
		if (empty($modSettings['Shop_enable_trade']))
			fatal_error(Shop::getText('currently_disabled_trade'), false);

		// Is the user is allowed to trade items?
		if (!allowedTo('shop_canTrade') && !allowedTo('shop_canManage'))
			isAllowedTo('shop_canTrade');

		// Do we have an item? No? Bad luck...
		if (empty($_REQUEST['id']) || !isset($_REQUEST['id']))
			fatal_error(Shop::getText('item_notfound'), false);

		// Item info
		checkSession();
		$this->_trade = (int) $_REQUEST['id'];
		$this->_amount = (int) isset($_REQUEST['tradecost']) ? $_REQUEST['tradecost'] : 0;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('main_button') . ' - ' . Shop::getText('item_trade_go');
		$context['template_layers'][] = 'options';
		$context['linktree'][] = [
			'url' => $scripturl . '?action=shop;sa=invtrade;id=' . $this->_trade,
			'name' => Shop::getText('item_trade_go'),
		];
		// Sub-menu tabs
		$context['section_tabs'] = $this->_tabs;

		// Make sure we have a price
		if (empty($this->_amount))
			fatal_error(Shop::getText('item_notprice'), false);
		// No tricks with the price...
		elseif ($this->_amount <= 0)
			fatal_error(Shop::getText('item_price_notnegative'), false);

		// Validate the info
		$this->validate_set($this->_trade);

		// Set the item for trade
		Database::Update('shop_inventory', ['id' => $this->_trade, 'tradecost' => $this->_amount, 'date' => time()], 'trading = 1, tradecost = {int:tradecost}, tradedate = {int:date}', 'WHERE id = {int:id}');

		// Tell the user that the item was added successfully
		redirectexit('action=shop;sa=inventory;traded');
	}

	public function validate_set($trade)
	{
		global $user_info;

		// Load the info
		$this->_item = Database::Get('', '', '', 'shop_inventory AS si', Database::$profile_inventory, 'WHERE si.id = {int:tradeid} AND s.status = 1 AND si.userid = {int:mem}', true, 'LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = si.itemid)', ['tradeid' => $trade, 'mem' => $user_info['id']]);

		// No item found
		if (empty($this->_item))
			fatal_error(Shop::getText('item_notfound'), false);
		// It's already on the trade center
		elseif (!empty($this->_item['trading']))
			fatal_error(Shop::getText('item_alreadytraded'), false);
	}

	public function list()
	{
		global $context, $sourcedir, $scripturl, $modSettings, $user_info, $memberContext, $boardurl;

		// What if the Inventories are disabled?
		if (empty($modSettings['Shop_enable_trade']))
			fatal_error(Shop::getText('currently_disabled_trade'), false);

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canTrade');

		// Specific member only?
		if (isset($_REQUEST['u']) || isset($_REQUEST['user']))
		{
			$this->_member = loadMemberData((isset($_REQUEST['user']) ? $_REQUEST['user'] : ((isset($_REQUEST['u']) ? $_REQUEST['u'] : $user_info['id']))), isset($_REQUEST['user']), 'profile');

			// Don't mind me, just checking if it's a valid profile
			if (!$this->_member)
				fatal_error(Shop::getText('user_unable_tofind'), false);

			// If all went well, we have a valid member ID!
			list ($memID) = $this->_member;
			loadMemberContext($memID);
		}

		// Set all the page stuff
		require_once($sourcedir . '/Subs-List.php');
		$context['page_title'] = Shop::getText('main_button') . ' - ' . ($_REQUEST['sa'] == 'mytrades' ? Shop::getText('trade_myprofile') : (!isset($_REQUEST['u']) ? Shop::getText('trade_list') : sprintf(Shop::getText('trade_profile'), $memberContext[$memID]['name'])));
		$context['page_welcome'] = Shop::getText('main_trade') . ' - ' . ($_REQUEST['sa'] == 'mytrades' ? Shop::getText('trade_myprofile') : (!isset($_REQUEST['u']) ? Shop::getText('trade_list') : sprintf(Shop::getText('trade_profile'), $memberContext[$memID]['name'])));

		$context['page_description'] = ($_REQUEST['sa'] == 'mytrades' ? Shop::getText('trade_myprofile_desc') : (!isset($_REQUEST['u']) ? Shop::getText('trade_list_desc') : sprintf(Shop::getText('trade_profile_desc'), $memberContext[$memID]['name'])));
		$context['template_layers'][] = 'trade';
		$context['template_layers'][] = 'options';
		$context['sub_template'] = 'show_list';
		$context['default_list'] = 'inventory';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=' . $_REQUEST['sa'],
			'name' => ($_REQUEST['sa'] == 'mytrades' ? Shop::getText('trade_myprofile') : (!isset($_REQUEST['u']) ? Shop::getText('trade_list') : sprintf(Shop::getText('trade_profile'), $memberContext[$memID]['name']))),
		);
		// Sub-menu tabs
		$context['section_tabs'] = $this->_tabs;

		// Is it their own trades?
		if ($_REQUEST['sa'] != 'mytrades')
			$context['section_tabs']['tradelist#searchuser'] = [
				'action' => ['tradelist'],
				'anchor' => true,
				'label' => Shop::getText('inventory_search'),
			];

		// Images...
		$context['items_url'] = $boardurl . Shop::$itemsdir;
		$context['shop_images_list'] = Images::list();
		// ... and categories
		$context['shop_categories_list'] = Database::Get(0, 1000, 'sc.name', 'shop_categories AS sc', Database::$categories);
		$context['form_url'] = '?action=shop;sa=' . $_REQUEST['sa'] . (!isset($_REQUEST['u']) ? '' : ';u='. $memberContext[$memID]['id']) . (isset($_REQUEST['cat']) && $_REQUEST['cat'] >= 0 ? ';cat='.$_REQUEST['cat'] : '');

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');

		// The entire list
		$listOptions = Inventory::inventory_list((isset($_REQUEST['u']) ? $memberContext[$memID] : $user_info), $context['form_url'], ($_REQUEST['sa'] == 'mytrades' || isset($_REQUEST['u']) ? false : true), true);

		// Update default sorting
		$listOptions['default_sort_col'] = 'item_details';

		// Remove unnecessary stuff from this view
		unset($listOptions['columns']['item_category']);
		unset($listOptions['columns']['item_date']);
		unset($listOptions['columns']['item_use']);
		unset($listOptions['columns']['item_fav']);
		unset($listOptions['columns']['item_trade']);
		unset($listOptions['additional_rows']['traded']);

		// Change date
		$listOptions['columns']['item_details'] = [
			'header' => [
				'value' => Shop::getText('item_details'),
				'class' => 'lefttext',
			],
			'data' => [
				'function' => function($row)
				{
					global $scripturl;

					// Category
					$details = '<strong>' . Shop::getText('item_category') . ': </strong>' . (!empty($row['catid']) ? $row['category'] : Shop::getText('item_uncategorized'));

					// Date
					$details .= '<br><strong>' . Shop::getText('item_date') . ': </strong>' . timeformat($row['tradedate']);

					// Who owns this
					$details .= '<br><a href="' . $scripturl . '?action=shop;sa=owners;id=' . $row['itemid'] . '">' . Shop::getText('buy_item_who_this') . '</a>';

					return $details;
				},
				'class' => 'lefttext',
				'style' => 'width: 20%',
			],
			'sort' =>  [
				'default' => 'tradedate DESC',
				'reverse' => 'tradedate',
			],
		];

		// Owner
		$listOptions['columns']['item_owner'] = [
			'header' => [
				'value' => Shop::getText('item_member'),
				'class' => 'centertext',
			],
			'data' => [
				'sprintf' => [
					'format' => '<a href="' . $scripturl . '?action=profile;u=%1$d">%2$s</a>',
					'params' => [
						'userid' => false,
						'real_name' => true
					],
				],
				'class' => 'centertext',
			],
			'sort' => [
				'default' => 'real_name DESC',
				'reverse' => 'real_name',
			],
		];
		// Price
		$listOptions['columns']['item_price'] = [
			'header' => [
				'value' => Shop::getText('item_price'),
				'class' => 'centertext',
			],
			'data' => [
				'sprintf' => [
					'format' => Format::cash('%1$d'),
					'params' => [
						'tradecost' => false,
					],
				],
				'class' => 'centertext',
			],
			'sort' => [
				'default' => 'tradecost DESC',
				'reverse' => 'tradecost',
			],
		];
		// Purchase it
		$listOptions['columns']['item_buy'] = [
			'header' => [
				'value' => Shop::getText('item_buy'),
				'class' => 'centertext',
			],
			'data' => [
				'function' => function($row)
				{
					global $context, $user_info, $scripturl; 

					// How much need the user to buy this item?
					if ($user_info['shopMoney'] < $row['tradecost']) 
						return '
							<i>' . Shop::getText('buy_notenough') . '</i>';
					//Enough money? Buy it!
					else
						return '
							<a href="' . $scripturl . '?action=shop;sa=trade2;id=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">
								' . Shop::getText('item_buy') . '
							</a>';
				},
				'class' => 'centertext',
			],
			'sort' => [
				'default' => 'id DESC',
				'reverse' => 'id',
			],
		];

		// My trades
		if ($_REQUEST['sa'] == 'mytrades')
		{
			// Remove stuff
			unset($listOptions['columns']['item_owner']);
			unset($listOptions['columns']['item_buy']);

			// Remove it from trarde
			$listOptions['columns']['item_actions'] = [
				'header' => [
					'value' => Shop::getText('trade_mytrades_actions'),
					'class' => 'centertext',
				],
				'data' => [
					'function' => function($row)
					{
						global $context, $scripturl;

						// Remove item from trade center
						return '	
							<a href="' . $scripturl . '?action=shop;sa=traderemove;id=' . $row['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '">
								' . Shop::getText('trade_remove_item') . '
							</a>';
					},
					'class' => 'centertext',
				],
				'sort' => [
					'default' => 'id DESC',
					'reverse' => 'id',
				],
			];
			// Success removing it
			$listOptions['additional_rows']['removed'] = [
				'position' => 'above_column_headers',
				'value' => (isset($_REQUEST['removed']) ? '<div class="infobox">' . Shop::getText('trade_removed') . '</div>' : '')
			];
		}

		// Print our multiple lists with one line :o
		createList($listOptions);
	}

	public function remove()
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

	public static function Transaction()
	{
		global $smcFunc, $context, $user_info, $modSettings, $scripturl, $txt, $boardurl, $settings;

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
			SELECT p.id, p.itemid, p.trading, p.tradecost, p.userid, s.status, s.name, s.itemlimit, s.image
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

		// Little array of info
		$extra_items = array();
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
		// Add more info
		$extra_items['item_name'] = $row['name'];
		$extra_items['item_icon'] = $boardurl . self::$itemsdir . $row['image'];
		// The amount that the user received
		$totalrec = (int) ($row['tradecost'] - (($row['tradecost'] * $modSettings['Shop_items_trade_fee'])/100));
		// The actual fee he has to pay:
		$fee = (int) (($row['tradecost'] * $modSettings['Shop_items_trade_fee'])/100);
		// Send the info!
		parent::logBuy($row['itemid'], $user_info['id'], $row['tradecost'], $row['userid'], $fee, $row['id']);
		// Send a PM to the seller saying that his item was successfully bought
		self::sendPM($row['userid'], $row['name'], $row['tradecost'], $fee);
		// Send an alert
		if (!empty($modSettings['Shop_noty_trade']))
			Shop::deployAlert($row['userid'], 'traded', $row['id'], '?action=shop;sa=tradelog', $extra_items);
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
				'function' => 'ShopTrade::logGet',
			),
			'get_count' => array(
				'function' => 'ShopTrade::logCount',
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

							$sign ='';
							$color = '';
							// color it because it's bad?
							if ($fee > 0)
							{
								$color = 'red';
								$sign = '-';
							}

							return '<span'.(!empty($color) ? ' style="color: '.$color.'"' : '').'>'.$sign.Shop::formatCash($fee).'</span>';
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