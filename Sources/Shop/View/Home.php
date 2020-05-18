<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\View;

use Shop\Shop;
use Shop\Helper\Format;

if (!defined('SMF'))
	die('No direct access...');

class Home
{
	var $shop_tabs = [];
	var $subactions = [];

	function __construct()
	{
		// Load language files
		loadLanguage('Shop/Shop');
		loadLanguage('Shop/Errors');

		// Load template file
		loadTemplate('Shop/Shop');
	}

	public function main()
	{
		global $context, $scripturl, $modSettings, $user_info;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('main_button');
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop',
			'name' => Shop::getText('main_button'),
		);

		// What if the Shop is disabled? User shouldn't be able to access the Shop
		if (empty($modSettings['Shop_enable_shop']))
			fatal_error(Shop::getText('currently_disabled'), false);

		// Last but not less important. Are they actually allowed to Access the Shop? If not.. YOU SHALL NOT PASS. 
		// Anyway if he can Manage the Shop, there's no problem.
		if (!empty($modSettings['Shop_enable_shop']) && !allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
			isAllowedTo('shop_canAccess');

		// Maintenance. Only Shop admins can access if the shop it's enabled
		if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_maintenance']) && allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
			fatal_error(Shop::getText('currently_maintenance'), false);

		// Games Pass, get the days!
		$context['user']['gamedays'] = ($user_info['gamesPass'] <= time() || empty($user_info['gamesPass']) ? 0 : Format::gamespass($user_info['gamesPass']));

		// Lovely copyright in shop pages
		$context['shop']['copyright'] = $this->copyright();
		// Shop tabs
		$context['shop']['tabs'] = $this->tabs();
		
		// Big array of actions
		$this->subactions = [
			'home' =>  'Home::portal',
			'buy' => 'Buy::main',
			'buy2' => 'Buy::purchase',
			'inventory' => 'Inventory::main',
			'search' => 'Inventory::search',
			'search2' => 'Inventory::search_inventory',
			'invuse' => 'Inventory::use',
			'invused' => 'Inventory::used',
			'invfav' => 'Inventory::fav',
			'owners' => 'Inventory::owners',
			'invdisp' => 'Inventory::display_extend',
			'gift' => 'Gift::main',
			'senditem' => 'Gift::main',
			'sendmoney' => 'Gift::main',
			'gift2' => 'Gift::send',
		];

		$subactions2 = [
			'invtrade' => array(
				'function' => 'ShopInventory::Trade',
			),
			'invtrade2' => array(
				'function' => 'ShopInventory::Trade2',
			),
			
			'bank' => array(
				'function' => 'ShopBank::Main',
			),
			'bank2' => array(
				'function' => 'ShopBank::Trans',
			),
			'trade' => array(
				'function' => 'ShopTrade::Main',
			),
			'tradelist' => array(
				'function' => 'ShopTrade::List',
			),
			'mytrades' => array(
				'function' => 'ShopTrade::Profile',
			),
			'tradelog' => array(
				'function' => 'ShopTrade::Log',
			),
			'trade2' => array(
				'function' => 'ShopTrade::Transaction',
			),
			'trade3' => array(
				'function' => 'ShopTrade::Transaction2',
			),
			'traderemove' => array(
				'function' => 'ShopTrade::Remove',
			),
			'tradesearch' => array(
				'function' => 'ShopTrade::Search',
			),
			'stats' => array(
				'function' => 'ShopStats::Main',
			),
			'games' => array(
				'function' => 'ShopGames::Main',
			),
		];
		// More sections?
		call_integration_hook('integrate_shop_home_actions', array(&$this->subactions));

		// Invoke the function
		$sa = isset($_GET['sa'], $this->subactions[$_GET['sa']]) ? $_GET['sa'] : 'home';
		call_helper(__NAMESPACE__ . '\\' . $this->subactions[$sa] . '#');
	}

	public function tabs()
	{
		$this->shop_tabs = [
			'home' => [
				'action' => ['home'],
				'label' => Shop::getText('main_home'),
				'permission' => 'shop_canAccess',
				'enable' => 'Shop_enable_shop'
			],
			'buy' => [
				'action' => ['buy','buy2','buy3', 'whohas'],
				'label' => Shop::getText('main_buy'),
				'permission' => 'shop_canBuy',
				'enable' => 'Shop_enable_shop'
			],
			'inventory' => [
				'action' => ['inventory', 'invdisp', 'invuse', 'invused', 'owners','search','search2'],
				'label' => Shop::getText('main_inventory'),
				'permission' => 'shop_canBuy',
				'enable' => 'Shop_enable_shop'
			],
			'gift' => [
				'action' => ['gift','senditem','sendmoney','gift2','gift3'],
				'label' => Shop::getText('main_gift'),
				'permission' => 'shop_canGift',
				'enable' => 'Shop_enable_gift'
			],
			'bank' => [
				'action' => ['bank', 'bank2'],
				'label' => Shop::getText('main_bank'),
				'permission' => 'shop_canBank',
				'enable' => 'Shop_enable_bank'
			],
			'trade' => [
				'action' => ['trade', 'tradelist', 'mytrades', 'tradelog', 'trade2','trade3','traderemove', 'invtrade', 'invtrade2',],
				'label' => Shop::getText('main_trade'),
				'permission' => 'shop_canBuy',
				'enable' => 'Shop_enable_trade'
			],
			'games' => [
				'action' => ['games'],
				'label' => Shop::getText('main_games'),
				'permission' => 'shop_playGames',
				'enable' => 'Shop_enable_games'
			],
			'stats' => [
				'action' => ['stats'],
				'label' => Shop::getText('main_stats'),
				'permission' => 'shop_viewStats',
				'enable' => 'Shop_enable_stats'
			],
		];
		// Magic tabs?
		call_integration_hook('integrate_shop_home_tabs', [&$this->shop_tabs]);

		return $this->shop_tabs;
	}

	public function portal()
	{
		global $context, $user_info, $modSettings, $scripturl;

		// Set all the page stuff
		$context['page_title'] = Shop::getText('main_button') . ' - ' . Shop::getText('main_home');
		$context['sub_template'] = 'home';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=home',
			'name' => Shop::getText('main_home'),
		);

		// Forum name + Shop
		$context['shop']['forum_welcome'] = sprintf(Shop::getText('welcome_to'), $context['forum_name']);

		// Welcome message
		$context['shop']['welcome'] = sprintf(Shop::getText('welcome_text'), $user_info['name'], $modSettings['Shop_credits_suffix']);

		// Profile action??
		if (isset($_REQUEST['u']) && !empty($_REQUEST['u']))
			redirectexit('action=shop;sa=gift;u='.$_REQUEST['u']);

		// Display some general stats
		/*$context['home_stats'] = array(
			// Last items added
			'last_added' => array(
				'label' => $txt['Shop_stats_last_added'],
				'icon' => 'last_added.png',
				'function' => ShopStats::LastItems(),
				'enabled' => true,
			),
			// Last items bought
			'last_bought' => array(
				'label' => $txt['Shop_stats_last_bought'],
				'icon' => 'last_bought.png',
				'function' => ShopStats::LastBought(),
				'enabled' => allowedTo('shop_canBuy'),
			),
			// Richest pocket
			'richest_pocket' => array(
				'label' => $txt['Shop_stats_richest_pocket'],
				'icon' => 'richest_pocket.png',
				'function' => ShopStats::Richest('pocket'),
				'enabled' => true,
			),
			// Richest bank
			'richest_bank' => array(
				'label' => $txt['Shop_stats_richest_bank'],
				'icon' => 'richest_bank.png',
				'function' => ShopStats::Richest('bank'),
				'enabled' => allowedTo('shop_canBank') && !empty($modSettings['Shop_enable_bank']),
			),
		);*/
	}



	public static function logBank($userid, $amount, $fee, $type = 0)
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

	public static function logGames($userid, $amount, $game = 'slots')
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



	/**
	 * Shop::copyright()
	 *
	 * Used in the credits action.
	 * @param boolean $return decide between returning a string or append it to a known context var.
	 * @return string A link for copyright notice
	 */
	public function copyright($return = false)
	{
		return '
			<br /><div style="text-align: center;"><span class="smalltext">Powered by <a href="https://smftricks.com" target="_blank" rel="noopener">ST Shop</a></span></div>';
	}
}