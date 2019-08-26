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

class ShopStats extends ShopHome
{
	public static function Main()
	{
		global $context, $scripturl, $modSettings, $txt;

		// What if the Inventories are disabled?
		if (empty($modSettings['Shop_enable_stats']))
			fatal_error($txt['Shop_currently_disabled_stats'], false);

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_viewStats');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_stats'];
		$context['template_layers'][] = 'Shop_main';
		$context['sub_template'] = 'shop_stats';
		$context['page_description'] = $txt['Shop_stats_desc'];
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=stats',
			'name' => $txt['Shop_shop_stats'],
		);

		// Get the stats
		$context['stats_blocks'] = array(
			// Normal stats
			'shop' => array(
				// Most bought
				'most_bought' => array(
					'label' => $txt['Shop_stats_most_bought'],
					'icon' => 'most_bought.png',
					'function' => self::MostBought(),
					'enabled' => allowedTo('shop_canBuy') || allowedTo('shop_canTrade'),
				),
				// Top categories
				'top_cats' => array(
					'label' => $txt['Shop_stats_top_cats'],
					'icon' => 'top_cats.png',
					'function' => self::TopCats(),
					'enabled' => true,
				),
				// Top inventories
				'top_inventories' => array(
					'label' => $txt['Shop_stats_top_inventories'],
					'icon' => 'top_inventories.png',
					'function' => self::TopInventories(),
					'enabled' => allowedTo('shop_canBuy') || allowedTo('shop_viewInventory') || (allowedTo('shop_canTrade') && !empty($modSettings['Shop_enable_trade'])),
				),
				// Top buyers
				'top_buyers' => array(
					'label' => $txt['Shop_stats_top_buyers'],
					'icon' => 'top_buyers.png',
					'function' => self::TopBuyers(),
					'enabled' => allowedTo('shop_canBuy') || (allowedTo('shop_canTrade') && !empty($modSettings['Shop_enable_trade'])),
				),
				// Top gifts sent
				'top_gifts_s' => array(
					'label' => $txt['Shop_stats_top_gifts_sent'],
					'icon' => 'top_gifts_s.png',
					'function' => self::TopGifts(),
					'enabled' => allowedTo('shop_canGift'),
				),
				// Top gifts received
				'top_gifts_r' => array(
					'label' => $txt['Shop_stats_top_gifts_received'],
					'icon' => 'top_gifts_r.png',
					'function' => self::TopGifts('received'),
					'enabled' => allowedTo('shop_canGift'),
				),
				// Top money sent
				'top_money_s' => array(
					'label' => $txt['Shop_stats_top_money_sent'],
					'icon' => 'top_money_s.png',
					'function' => self::TopMoney(),
					'enabled' => allowedTo('shop_canGift'),
				),
				// Top money received
				'top_money_r' => array(
					'label' => $txt['Shop_stats_top_money_received'],
					'icon' => 'top_money_r.png',
					'function' => self::TopMoney('received'),
					'enabled' => allowedTo('shop_canGift'),
				),
				// Richest pocket
				'richest_pocket' => array(
					'label' => $txt['Shop_stats_richest_pocket'],
					'icon' => 'richest_pocket.png',
					'function' => self::Richest('pocket'),
					'enabled' => true,
				),
				// Richest bank
				'richest_bank' => array(
					'label' => $txt['Shop_stats_richest_bank'],
					'icon' => 'richest_bank.png',
					'function' => self::Richest('bank'),
					'enabled' => allowedTo('shop_canBank') && !empty($modSettings['Shop_enable_bank']),
				),
			),
		);

		// Add more stats?
		call_integration_hook('integrate_shop_stats', array(&$context['stats_blocks']));
	}

	public static function Richest($type = 'pocket')
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['richest_'.($type == 'bank' ? 'bank' : 'pocket')] = cache_get_data('shopStats_'.($type == 'bank' ? 'bank' : 'pocket'), $modSettings['Shop_stats_refresh'])) == null)
		{
			// Richest top 5.
			$members_result = $smcFunc['db_query']('', '
				SELECT m.id_member, m.real_name, m.'. ($type == 'bank' ? 'shopBank' : 'shopMoney'). '
				FROM {db_prefix}members AS m
				WHERE'. ($type == 'bank' ? ' m.shopBank' : ' m.shopMoney'). ' > {int:money}
				ORDER BY'. ($type == 'bank' ? ' m.shopBank' : ' m.shopMoney'). ' DESC
				LIMIT 5',
				array(
					'money' => 0,
				)
			);
			$context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')] = array();
			$max_num_money = 1;
			while ($row_members = $smcFunc['db_fetch_assoc']($members_result))
			{
				$context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')][] = array(
					'num' => $row_members[($type == 'bank' ? 'shopBank' : 'shopMoney')],
					'name' => $row_members['real_name'],
					'id' => $row_members['id_member'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_members['id_member'] . '">' . $row_members['real_name'] . '</a>'
				);

				if ($max_num_money < $row_members[($type == 'bank' ? 'shopBank' : 'shopMoney')])
					$max_num_money = $row_members[($type == 'bank' ? 'shopBank' : 'shopMoney')];
			}
			$smcFunc['db_free_result']($members_result);

			foreach ($context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')] as $i => $rich)
			{
				$context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')][$i]['percent'] = round(($rich['num'] * 100) / $max_num_money);
				$context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')][$i]['num'] = comma_format($context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')][$i]['num']);
			}

			cache_put_data('shopStats'.($type == 'bank' ? '_bank' : '_pocket'), $context['shop_stats']['richest'.($type == 'bank' ? '_bank' : '_pocket')], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['richest' . ($type == 'bank' ? '_bank' : '_pocket')];
	}

	public static function LastItems()
	{
		global $smcFunc, $context;

			// Last idems
			$items_result = $smcFunc['db_query']('', '
				SELECT itemid, name, image, status
				FROM {db_prefix}shop_items
				WHERE status = 1
				ORDER BY itemid DESC
				LIMIT 5',
				array()
			);
			$context['shop_stats']['last_added'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['last_added'][] = array(
					'id' => $row_items['itemid'],
					'name' => $row_items['name'],
					'image' => Shop::ShopImageFormat($row_items['image'])
				);
			}
			$smcFunc['db_free_result']($items_result);

		return $context['shop_stats']['last_added'];
	}

	public static function LastBought()
	{
		global $smcFunc, $context, $modSettings;

		if (($context['shop_stats']['last_bought'] = cache_get_data('shopStats_lastbought', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Last bought.
			$items_result = $smcFunc['db_query']('', '
				SELECT l.id, l.itemid, s.name, s.image, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				WHERE s.status = 1
				ORDER BY l.id DESC
				LIMIT 5',
				array(
				)
			);
			$context['shop_stats']['last_bought'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['last_bought'][] = array(
					'name' => $row_items['name'],
					'id' => $row_items['itemid'],
					'image' => Shop::ShopImageFormat($row_items['image'])
				);
			}
			$smcFunc['db_free_result']($items_result);

			cache_put_data('shopStats_lastbought', $context['shop_stats']['last_bought'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['last_bought'];
	}

	public static function MostBought()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['most_bought'] = cache_get_data('shopStats_mostbought', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Most bought.
			$items_result = $smcFunc['db_query']('', '
				SELECT l.itemid, count(*) AS count, s.name, s.image, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				WHERE s.status = 1
				GROUP BY l.itemid, s.name, s.image, s.status
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['most_bought'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['most_bought'][] = array(
					'id' => $row_items['itemid'],
					'name' => $row_items['name'],
					'image' => Shop::ShopImageFormat($row_items['image']),
					'num' => $row_items['count']
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['most_bought'] as $i => $bought)
			{
				$context['shop_stats']['most_bought'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['most_bought'][$i]['num'] = comma_format($context['shop_stats']['most_bought'][$i]['num']);
			}

			cache_put_data('shopStats_mostbought', $context['shop_stats']['most_bought'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['most_bought'];
	}

	public static function TopCats()
	{
		global $smcFunc, $context, $modSettings;

		if (($context['shop_stats']['top_cats'] = cache_get_data('shopStats_topcats', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top categories
			$items_result = $smcFunc['db_query']('', '
				SELECT s.catid, s.status, count(*) AS count, c.name, c.image
				FROM {db_prefix}shop_items AS s
				LEFT JOIN {db_prefix}shop_categories AS c ON (c.catid = s.catid)
				WHERE s.catid <> 0 AND s.status = 1
				GROUP BY s.catid, s.status, c.name, c.image
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_cats'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_cats'][] = array(
					'id' => $row_items['catid'],
					'name' => $row_items['name'],
					'image' => Shop::ShopImageFormat($row_items['image']),
					'num' => $row_items['count']
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_cats'] as $i => $bought)
			{
				$context['shop_stats']['top_cats'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_cats'][$i]['num'] = comma_format($context['shop_stats']['top_cats'][$i]['num']);
			}

			cache_put_data('shopStats_topcats', $context['shop_stats']['top_cats'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_cats'];
	}

	public static function TopBuyers()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['top_buyers'] = cache_get_data('shopStats_topbuyers', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top buyers
			$items_result = $smcFunc['db_query']('', '
				SELECT l.userid, count(*) AS count, m.real_name, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.userid)
				WHERE s.status = 1
				GROUP BY l.userid, m.real_name, s.status
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_buyers'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_buyers'][] = array(
					'id' => $row_items['userid'],
					'name' => $row_items['real_name'],
					'num' => $row_items['count'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_items['userid'] . '">' . $row_items['real_name'] . '</a>'
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_buyers'] as $i => $bought)
			{
				$context['shop_stats']['top_buyers'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_buyers'][$i]['num'] = comma_format($context['shop_stats']['top_buyers'][$i]['num']);
			}

			cache_put_data('shopStats_topbuyers', $context['shop_stats']['top_buyers'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_buyers'];
	}

	public static function TopInventories()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['top_inventories'] = cache_get_data('shopStats_topinvs', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top inventories
			$items_result = $smcFunc['db_query']('', '
				SELECT inv.userid, inv.trading, count(*) AS count, m.real_name, s.status
				FROM {db_prefix}shop_inventory AS inv
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = inv.userid)
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = inv.itemid)
				WHERE s.status = 1 AND inv.trading = 0
				GROUP BY inv.userid, inv.trading, s.status, m.real_name
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_inventories'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_inventories'][] = array(
					'id' => $row_items['userid'],
					'name' => $row_items['real_name'],
					'num' => $row_items['count'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_items['userid'] . '">' . $row_items['real_name'] . '</a>'
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_inventories'] as $i => $bought)
			{
				$context['shop_stats']['top_inventories'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_inventories'][$i]['num'] = comma_format($context['shop_stats']['top_inventories'][$i]['num']);
			}

			cache_put_data('shopStats_topinvs', $context['shop_stats']['top_inventories'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_inventories'];
	}

	public static function TopGifts($type = NULL)
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['gift_'.($type == 'received' ? 'received' : 'sent')] = cache_get_data('shopStats_gift'.($type == 'received' ? 'rec' : 'sent'), $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top gifts sent/received
			$items_result = $smcFunc['db_query']('', '
				SELECT '.($type == 'received' ? 'l.receiver' : 'l.userid').', l.is_admin, count(*) AS count, m.real_name, s.status
				FROM {db_prefix}shop_log_gift AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = '.($type == 'received' ? 'l.receiver' : 'l.userid').')
				WHERE s.status = 1 AND l.amount = 0 AND l.is_admin = 0
				GROUP BY '.($type == 'received' ? 'l.receiver' : 'l.userid').', l.amount, l.is_admin, m.real_name, s.status
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_gifts'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_gifts'][] = array(
					'id' => $row_items[($type == 'received' ? 'receiver' : 'userid')],
					'name' => $row_items['real_name'],
					'num' => $row_items['count'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_items[($type == 'received' ? 'receiver' : 'userid')] . '">' . $row_items['real_name'] . '</a>'
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_gifts'] as $i => $bought)
			{
				$context['shop_stats']['top_gifts'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_gifts'][$i]['num'] = comma_format($context['shop_stats']['top_gifts'][$i]['num']);
			}

			cache_put_data('shopStats_gift'.($type == 'received' ? 'rec' : 'sent'), $context['shop_stats']['gift_'.($type == 'received' ? 'received' : 'sent')], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_gifts'];
	}

	public static function TopMoney($type = NULL)
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['money_'.($type == 'received' ? 'received' : 'sent')] = cache_get_data('shopStats_money'.($type == 'received' ? 'rec' : 'sent'), $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top money sent/received
			$items_result = $smcFunc['db_query']('', '
				SELECT '.($type == 'received' ? 'l.receiver' : 'l.userid').', l.itemid, l.is_admin, sum(l.amount) AS count, m.real_name
				FROM {db_prefix}shop_log_gift AS l
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = '.($type == 'received' ? 'l.receiver' : 'l.userid').')
				WHERE l.itemid = 0 AND l.is_admin = 0
				GROUP BY '.($type == 'received' ? 'l.receiver' : 'l.userid').', l.itemid, l.is_admin, m.real_name
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_money'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_money'][] = array(
					'id' => $row_items[($type == 'received' ? 'receiver' : 'userid')],
					'name' => $row_items['real_name'],
					'num' => $row_items['count'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_items[($type == 'received' ? 'receiver' : 'userid')] . '">' . $row_items['real_name'] . '</a>'
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_money'] as $i => $bought)
			{
				$context['shop_stats']['top_money'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_money'][$i]['num'] = comma_format($context['shop_stats']['top_money'][$i]['num']);
			}

			cache_put_data('shopStats_money'.($type == 'received' ? 'rec' : 'sent'), $context['shop_stats']['money_'.($type == 'received' ? 'received' : 'sent')], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_money'];
	}

	public static function TopProfit()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['top_profit'] = cache_get_data('shopStats_topprofit', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top profit
			$items_result = $smcFunc['db_query']('', '
				SELECT l.sellerid, sum(l.amount) AS count, m.real_name, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.sellerid)
				WHERE s.status = 1 AND l.sellerid <> 0
				GROUP BY l.sellerid, m.real_name, s.status
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_profit'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_profit'][] = array(
					'id' => $row_items['sellerid'],
					'name' => $row_items['real_name'],
					'num' => $row_items['count'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_items['sellerid'] . '">' . $row_items['real_name'] . '</a>'
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_profit'] as $i => $bought)
			{
				$context['shop_stats']['top_profit'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_profit'][$i]['num'] = comma_format($context['shop_stats']['top_profit'][$i]['num']);
			}

			cache_put_data('shopStats_topprofit', $context['shop_stats']['top_profit'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_profit'];
	}

	public static function TopSpent()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['top_spent'] = cache_get_data('shopStats_topspent', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Top spent
			$items_result = $smcFunc['db_query']('', '
				SELECT l.userid, l.sellerid, sum(l.amount) AS count, m.real_name, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				LEFT JOIN {db_prefix}members AS m ON (m.id_member = l.userid)
				WHERE s.status = 1 AND l.sellerid <> 0
				GROUP BY l.userid, l.sellerid, m.real_name, s.status
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['top_spent'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['top_spent'][] = array(
					'id' => $row_items['userid'],
					'name' => $row_items['real_name'],
					'num' => $row_items['count'],
					'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row_items['userid'] . '">' . $row_items['real_name'] . '</a>'
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['top_spent'] as $i => $bought)
			{
				$context['shop_stats']['top_spent'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['top_spent'][$i]['num'] = comma_format($context['shop_stats']['top_spent'][$i]['num']);
			}

			cache_put_data('shopStats_topspent', $context['shop_stats']['top_spent'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['top_spent'];
	}

	public static function MostTraded()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['most_traded'] = cache_get_data('shopStats_mosttraded', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Most traded
			$items_result = $smcFunc['db_query']('', '
				SELECT l.itemid, count(*) AS count, s.name, s.image, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				WHERE l.sellerid <> 0 AND s.status = 1 
				GROUP BY l.itemid, s.name, s.image, s.status
				ORDER BY count DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['most_traded'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['most_traded'][] = array(
					'id' => $row_items['itemid'],
					'name' => $row_items['name'],
					'image' => Shop::ShopImageFormat($row_items['image']),
					'num' => $row_items['count']
				);

				if ($max_num < $row_items['count'])
					$max_num = $row_items['count'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['most_traded'] as $i => $bought)
			{
				$context['shop_stats']['most_traded'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['most_traded'][$i]['num'] = comma_format($context['shop_stats']['most_traded'][$i]['num']);
			}

			cache_put_data('shopStats_mosttraded', $context['shop_stats']['most_traded'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['most_traded'];
	}

	public static function MostExpensive()
	{
		global $smcFunc, $context, $scripturl, $modSettings;

		if (($context['shop_stats']['most_expensive'] = cache_get_data('shopStats_mostexp', $modSettings['Shop_stats_refresh'])) == null)
		{
			// Most expensive items
			$items_result = $smcFunc['db_query']('', '
				SELECT l.itemid, l.amount, s.name, s.image, s.status
				FROM {db_prefix}shop_log_buy AS l
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = l.itemid)
				WHERE l.sellerid <> 0 AND s.status = 1 
				ORDER BY l.amount DESC
				LIMIT 5',
				array(
				)
			);

			$max_num = 1;
			$context['shop_stats']['most_expensive'] = array();
			while ($row_items = $smcFunc['db_fetch_assoc']($items_result))
			{
				$context['shop_stats']['most_expensive'][] = array(
					'id' => $row_items['itemid'],
					'name' => $row_items['name'],
					'image' => Shop::ShopImageFormat($row_items['image']),
					'num' => $row_items['amount']
				);

				if ($max_num < $row_items['amount'])
					$max_num = $row_items['amount'];
			}
			$smcFunc['db_free_result']($items_result);

			foreach ($context['shop_stats']['most_expensive'] as $i => $bought)
			{
				$context['shop_stats']['most_expensive'][$i]['percent'] = round(($bought['num'] * 100) / $max_num);
				$context['shop_stats']['most_expensive'][$i]['num'] = comma_format($context['shop_stats']['most_expensive'][$i]['num']);
			}

			cache_put_data('shopStats_mostexp', $context['shop_stats']['most_expensive'], $modSettings['Shop_stats_refresh']);
		}

		return $context['shop_stats']['most_expensive'];
	}
}