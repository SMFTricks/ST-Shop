<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Integration;

use Shop\Shop;

if (!defined('SMF'))
	die('No direct access...');

class Who
{
	/**
	 * Who::who_allowed()
	 *
	 * Used in the who's online action.
	 * @param $allowedActions is the array of actions that require a specific permission.
	 * @return void
	 */
	public static function who_allowed(&$allowedActions)
	{
		$allowedActions = array_merge($allowedActions, [
			'shop' => ['shop_canAccess', 'shop_canManage'],
			'shopinfo' => ['shop_canManage'],
			'shopsettings' => ['shop_canManage'],
			'shopitems' => ['shop_canManage'],
			'shopmodules' => ['shop_canManage'],
			'shopcategories' => ['shop_canManage'],
			'shopgames' => ['shop_canManage'],
			'shopinventory' => ['shop_canManage'],
			'shoplogs' => ['shop_canManage'],
		]);
	}

	/**
	 * Who::whos_online()
	 *
	 * Used in the who's online action.
	 * @param $action It gets the request parameters 
	 * @return string A text for the current action
	 */
	public static function whos_online($actions)
	{
		global $memberContext, $txt, $modSettings;

		if (isset($actions['action']) && $actions['action'] === 'shop')
		{
			if (isset($actions['sa']))
			{
				// Buying items
				if ($actions['sa'] == 'buy' && allowedTo('shop_canBuy'))
					$who = $txt['whoallow_shop_buy'];
				// Gift items / Send money
				elseif (($actions['sa'] == 'gift' || $actions['sa'] == 'sendmoney') && allowedTo('shop_canGift'))
				{
					$who = $txt['whoallow_shop_gift'];
					if ($actions['sa'] == 'sendmoney')
						$who = sprintf($txt['whoallow_shop_sendmoney'], $modSettings['Shop_credits_suffix']);
				}
				// Viewing Inventory
				elseif (($actions['sa'] == 'inventory' || $actions['sa'] == 'search') && allowedTo('shop_viewInventory'))
				{
					// Searching
					if ($actions['sa'] == 'search')
						$who = $txt['whoallow_shop_search'];
					// Viewing
					else
					{
						$who = $txt['whoallow_shop_owninventory'];
						if (!empty($actions['u']))
						{
							$temp = loadMemberData($actions['u'], false, 'profile');
							loadMemberContext($actions['u']);
							$membername = $memberContext[$actions['u']]['name'];
							$who = sprintf($txt['whoallow_shop_inventory'], $membername, $actions['u']);
						}
					}
				}
				// Bank
				elseif ($actions['sa'] == 'bank' && allowedTo('shop_canBank'))
					$who = $txt['whoallow_shop_bank'];
				// Trade center
				elseif ($actions['sa'] == 'trade' && allowedTo('shop_canTrade'))
					$who = $txt['whoallow_shop_trade'];
				// Trade list
				elseif ($actions['sa'] == 'tradelist' && allowedTo('shop_canTrade'))
					$who = $txt['whoallow_shop_tradelist'];
				// Trade list
				elseif ($actions['sa'] == 'tradelog' && allowedTo('shop_canTrade'))
					$who = $txt['whoallow_shop_tradelog'];
				// Personal trade list
				elseif ($actions['sa'] == 'mytrades' && allowedTo('shop_canTrade'))
				{
					$who = $txt['whoallow_shop_owntrades'];
					if (!empty($actions['u']))
					{
						$temp = loadMemberData($actions['u'], false, 'profile');
						loadMemberContext($actions['u']);
						$membername = $memberContext[$actions['u']]['name'];
						$who = sprintf($txt['whoallow_shop_othertrades'], $membername, $actions['u']);
					}
				}
				// Stats
				elseif ($actions['sa'] == 'stats' && allowedTo('shop_viewStats'))
					$who = $txt['whoallow_shop_stats'];
				// Games Room
				elseif ($actions['sa'] == 'games' && allowedTo('shop_playGames'))
				{
					$who = $txt['whoallow_shop_games'];
					// Playing a game?
					if (isset($actions['play']))
					{
						// Slots
						if ($actions['play'] == 'slots')
							$who = $txt['whoallow_shop_games_slots'];
						// Lucky2
						elseif ($actions['play'] == 'lucky2')
							$who = $txt['whoallow_shop_games_lucky2'];
						// Number Slots
						elseif ($actions['play'] == 'number')
							$who = $txt['whoallow_shop_games_number'];
						// Pairs
						elseif ($actions['play'] == 'pairs')
							$who = $txt['whoallow_shop_games_pairs'];
						// Pairs
						elseif ($actions['play'] == 'dice')
							$who = $txt['whoallow_shop_games_dice'];
					}
				}
			}
		}

		if (!isset($who))
			return false;
		else
			return $who;
	}
}