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
use Shop\Helper\Database;

class Who
{
	/**
	 * @var array ID's for profiles.
	 */
	var $_profiles = [];

	/**
	 * Who::who_allowed()
	 *
	 * Used in the who's online action.
	 * 
	 * @param array $allowedActions is the array of actions that require a specific permission, using ACTION as the key.
	 * @return void
	 */
	public function who_allowed(array &$allowedActions) : void
	{
		global $modSettings;

		if (empty($modSettings['Shop_enable_shop']))
			return;

		// Load the language file
		loadLanguage('Shop/Who');

		$allowedActions['shop'] = ['shop_canAccess', 'shop_canManage'];
		$allowedActions['shopinfo'] = ['shop_canManage'];
		$allowedActions['shopsettings'] = ['shop_canManage'];
		$allowedActions['shopitems'] = ['shop_canManage'];
		$allowedActions['shopmodules'] = ['shop_canManage'];
		$allowedActions['shopcategories'] = ['shop_canManage'];
		$allowedActions['shopgames'] = ['shop_canManage'];
		$allowedActions['shopinventory'] = ['shop_canManage'];
		$allowedActions['shoplogs'] = ['shop_canManage'];
	}

	/**
	 * Who::whos_online_after()
	 *
	 * Used in the who's online action.
	 * @param mixed $urls a single url (string) or an array of arrays, each inner array being (JSON-encoded request data, id_member)
	 * @param array $data Returns the correct strings for each action
	 * @return void
	 */
	public function whos_online_after(&$urls, &$data) : void
	{
		global $modSettings, $user_info;

		// Is the shop even enabled?
		if (empty($modSettings['Shop_enable_shop']))
			return;

		// Fix the anomaly where $urls is a string when coming from the profile section.
		$url_list = (!is_array($urls) ? [[$urls, $user_info['id']]] : $urls);
		foreach ($url_list as $k => $url)
		{
			// Get the request parameters..
			$actions = Database::json_decode($url[0], true);

			// Any actions?
			if ($actions === false)
				continue;

			// We only want shop actions here
			if (!isset($actions['action']) || $actions['action'] !== 'shop')
				continue;

			// They can't see anything, yet
			$data[$k] = Shop::getText('who_hidden', false);

			// Can they access the shop?
			if (!allowedTo('shop_canAccess'))
				continue;

			// Viewing just the shop
			$data[$k] = Shop::getText('who');

			// No Sub Acttions?
			if (!isset($actions['sa']) || (isset($actions['sa']) && $actions['sa'] === 'home'))
				continue;

			// Buying items
			if ($actions['sa'] === 'buy' && allowedTo('shop_canBuy'))
			{
				$data[$k] = Shop::getText('who_buy');
			}

			// Gift items / Send money
			elseif (($actions['sa'] === 'gift' || $actions['sa'] == 'sendmoney') && allowedTo('shop_canGift') && !empty($modSettings['Shop_enable_gift']))
			{
				// Regular gift
				$data[$k] = Shop::getText('who_gift');

				// Money
				if ($actions['sa'] == 'sendmoney')
					$data[$k] = sprintf(Shop::getText('who_sendmoney'), $modSettings['Shop_credits_suffix']);
			}

			// Bank
			elseif ($actions['sa'] === 'bank' && allowedTo('shop_canBank') && !empty($modSettings['Shop_enable_bank']))
			{
				$data[$k] = Shop::getText('who_bank');
			}

			// Stats
			elseif ($actions['sa'] === 'stats' && allowedTo('shop_viewStats') && !empty($modSettings['Shop_enable_stats']))
			{
				$data[$k] = Shop::getText('who_stats');
			}

			// Games Room
			elseif ($actions['sa'] === 'games' && allowedTo('shop_playGames') && !empty($modSettings['Shop_enable_games']))
			{
				$data[$k] = Shop::getText('who_games');

				// Playing a game?
				if (isset($actions['play']))
					$data[$k] = Shop::getText('who_games_' . $actions['play']);
			}

			// Searching
			elseif ($actions['sa'] === 'search' && allowedTo('shop_viewInventory'))
			{
				$data[$k] = Shop::getText('who_search');
			}

			// Viewing Inventory
			elseif ($actions['sa'] === 'inventory' && allowedTo('shop_viewInventory'))
			{
				// Whose?  Their own?
				if (empty($actions['u']))
					$actions['u'] = $url[1];

				$this->_profiles['inventory'][(int) $actions['u']][$k] = $actions['u'] == $url[1] ? Shop::getText('who_owninventory') : Shop::getText('who_inventory');
			}

			// Trade center
			elseif ($actions['sa'] === 'trade' && allowedTo('shop_canTrade'))
			{
				$data[$k] = Shop::getText('who_trade');
			}

			// Trade list
			elseif (($actions['sa'] === 'tradelist' || $actions['sa'] === 'mytrades') && allowedTo('shop_canTrade'))
			{
				// Whose?  Their own?
				if (empty($actions['u']))
					$actions['u'] = $url[1];

				$this->_profiles['trade'][(int) $actions['u']][$k] = $actions['u'] == $url[1] ? Shop::getText('who_tradelist' . ($actions['sa'] == 'mytrades' ? '_own' : '')) : Shop::getText('who_tradelist_other');
			}

			// Trade list
			elseif ($actions['sa'] === 'tradelog' && allowedTo('shop_canTrade'))
			{
				$data[$k] = Shop::getText('who_tradelog');
			}
		}

		// Fix strings for profile shop related (inventory, trades, etc)
		if (!empty($this->_profiles))
			$this->profile($data);
	}

	/**
	 * Who::profile()
	 *
	 * Formats the shop profile actions
	 * 
	 * @param array $data Returns the correct strings for each action
	 * @return void
	 */
	private function profile(array &$data) : void
	{
		// Inventory profiles and if they are allowed to view inventories
		if (empty($this->_profiles))
			return;

		// Check permissions for inventory
		if (isset($this->_profiles['inventory']) && !empty($this->_profiles['inventory']) && !allowedTo('shop_viewInventory'))
			unset($this->_profiles['inventory']);

		// Check permissions for trades
		if (isset($this->_profiles['trade']) && !empty($this->_profiles['trade']) && !allowedTo('shop_canTrade'))
			unset($this->_profiles['trade']);

		// Now get the profile ids from the actions, we only want the keys
		$profile_ids = array();
		foreach ($this->_profiles as $profiles)
		{
			foreach ($profiles as $id_member => $action)
				$profile_ids[] = $id_member;
		}

		// Get the profile ids
		$profile_ids = array_unique($profile_ids);

		// Get the names of the users
		$this->_query = Database::Get(
			0, count($profile_ids), 'id_member', 'members',
			['id_member', 'real_name'],
			'WHERE id_member IN ({array_int:member_list})', false, '',
			['member_list' => $profile_ids]
		);

		// Se the correct key
		$this->_result = array_column($this->_query, 'real_name', 'id_member');

		// Set the correct strings
		// Go through the current members we obtained
		foreach ($this->_result as $id_member => $name)
		{
			// Loop through the types of actions
			foreach ($this->_profiles as $type => $profiles)
			{
				// Loop through the actions
				if (isset($profiles[$id_member]))
				{
					foreach ($profiles[$id_member] as $k => $v)
						$data[$k] = sprintf($v, $id_member, $name);
				}
			}
		}
	}
}