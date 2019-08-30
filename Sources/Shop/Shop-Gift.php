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

class ShopGift extends ShopHome
{
	public static function Main()
	{
		global $context, $smcFunc, $scripturl, $modSettings, $user_info, $memberContext, $txt;

		// What if the Inventories are disabled?
		if (empty($modSettings['Shop_enable_gift']))
			fatal_error($txt['Shop_currently_disabled_gift'], false);

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canGift');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_gift'];
		$context['template_layers'][] = 'Shop_main';
		$context['template_layers'][] = 'Shop_giftTabs';
		$context['sub_template'] = 'Shop_mainGift';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=gift',
			'name' => $txt['Shop_shop_gift'],
		);
		// Sub-menu tabs
		$context['gift_tabs'] = self::Tabs();
		// Can he view inventories?
		$context['shop']['view_inventory'] = allowedTo('shop_viewInventory');
		// Adding additional linktree
		$context['linktree'][] = array(
			'url' => (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'sendmoney' ? $scripturl . '?action=shop;sa=sendmoney' : $scripturl . '?action=shop;sa=sendgift'),
			'name' => (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'sendmoney' ? sprintf($txt['Shop_gift_send_money'], $modSettings['Shop_credits_suffix']) : $txt['Shop_gift_send_item']),
		);

		if (isset($_REQUEST['sa']) && ($_REQUEST['sa'] != 'sendmoney'))
			// Items list
			$context['shop_user_items_list'] = Shop::getUserItemsList($user_info['id']);

		// Send money string
		$context['shop']['send_money'] = sprintf($txt['Shop_gift_send_money'], $modSettings['Shop_credits_suffix']);

		// Do we have an ID already?, Let's find out the name of that user
		if (isset($_REQUEST['u']))
		{
			$userid = (int) $_REQUEST['u'];
			// Find out the member credits...
			$temp = loadMemberData($userid, false, 'profile');
			if (!empty($temp))
			{
				loadMemberContext($userid);
				$membername = $memberContext[$userid]['name'];
				$_REQUEST['membername'] = $membername;
			}
		}

		// Load suggest.js
		loadJavaScriptFile('suggest.js', array('default_theme' => true, 'defer' => false, 'minimize' => true), 'smf_suggest');
	}

	public static function Tabs()
	{
		global $context, $modSettings, $txt;

		$context['gift_tabs'] = array(
			'gift' => array(
				'action' => array('gift', 'senditem'),
				'label' => $txt['Shop_gift_send_item'],
			),
			'sendmoney' => array(
				'action' => array('sendmoney'),
				'label' => sprintf($txt['Shop_gift_send_money'], $modSettings['Shop_credits_suffix']),
			),
		);

		return $context['gift_tabs'];
	}

	public static function Send()
	{
		global $smcFunc, $context, $user_info, $modSettings, $scripturl, $txt, $settings;

		// What if the Inventories are disabled?
		if (empty($modSettings['Shop_enable_gift']))
				fatal_error($txt['Shop_currently_disabled_gift'], false);

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canGift');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_gift'];
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=gift',
			'name' => $txt['Shop_shop_gift'],
		);

		// Check session
		checkSession();

		// You cannot get here without an item
		if (!isset($_REQUEST['item']) && !isset($_REQUEST['money']))
			fatal_error($txt['Shop_gift_no_item_found'], false);
		// Or an amount if is sending money...
		elseif (!isset($_REQUEST['amount']) && isset($_REQUEST['money']))
			fatal_error($txt['Shop_gift_no_amount'], false);
		// Anyway, couldn't get so far if for any reason there is no member to send the items/money
		elseif (!isset($_REQUEST['membername']))
			fatal_error($txt['Shop_gift_unable_user'], false);

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
			$row = $smcFunc['db_fetch_assoc']($request);
				$memID = $row['id_member'];
			$smcFunc['db_free_result']($request);
		}

		// Empty? Something went wrong
		if (empty($row))
			fatal_lang_error('not_a_user', false, 404);
		// Did we find an user?
		if (empty($memID))
			fatal_error($txt['Shop_user_unable_tofind'], false);
		// You cannot gift yourself DUH!
		elseif ($memID == $user_info['id'])
			fatal_error($txt['Shop_gift_not_yourself'], false);

		// Did the user leave a message? Nice :)
		$message = $smcFunc['htmlspecialchars']($_REQUEST['message'], ENT_QUOTES);

		// Little array of info
		$extra_items = array();
		
		if (!isset($_REQUEST['money']) && isset($_REQUEST['item']))
		{
			$itemid = (int) $_REQUEST['item'];
			// Get the item's information
			$result = $smcFunc['db_query']('', '
				SELECT p.id, p.userid, p.trading, p.userid, s.status, p.itemid, s.name
					FROM {db_prefix}shop_inventory AS p
				LEFT JOIN {db_prefix}shop_items AS s ON (s.itemid = p.itemid)
				WHERE p.id = {int:id} AND p.trading = 0 AND p.userid = {int:userid}',
				array(
					'userid' => $user_info['id'],
					'id' => $itemid,
				)
			);
			$row = $smcFunc['db_fetch_assoc']($result);
			$smcFunc['db_free_result']($result);

			// Is that id actually valid?
			if (empty($row) || ($row['status'] == 0) || ($row['trading'] == 1))
				fatal_error($txt['Shop_item_notfound'], false);
			// Proceed
			else
			{
				// Add some info
				$extra_items['item_icon'] = $settings['images_url'] . '/icons/shop/top_gifts_r.png';
				// Send the gift and log the information
				parent::logGift($user_info['id'], $memID, $message, 0, $row['itemid'], $row['id']);
				// Send a PM to the user that its going to receive the item.
				self::sendPM('item', $memID, $row['name'], '', $message);
				// Send an alert
				if (!empty($modSettings['Shop_noty_items']))
					Shop::deployAlert($memID, 'items', $row['id'], '?action=shop;sa=inventory', $extra_items);
				// Let's get out of here and later we'll show a nice message
				redirectexit('action=shop;sa=gift3;id='. $row['id']);
			}
		}
		else
		{
			// Set the amount
			$amount = (int) $_REQUEST['amount'];
			// We need to find out the difference if there's not enough money
			$notenough = ($user_info['shopMoney'] - $amount);
			// Is that id actually valid?
			if ($notenough < 0)
				fatal_lang_error('Shop_gift_not_enough_pocket', false, array($modSettings['Shop_credits_suffix']));
			elseif ($amount <= 0)
				fatal_error($txt['Shop_gift_not_negative_or_zero'], false);
			// Proceed
			else
			{
				// Add some info
				$extra_items['item_icon'] = $settings['images_url'] . '/icons/shop/top_money_r.png';
				$extra_items['amount'] = Shop::formatCash($amount);
				// Send the gift and log the information
				parent::logGift($user_info['id'], $memID, $message, $amount);
				// Send a PM to the user that its going to receive the money.
				self::sendPM('money', $memID, '', $amount, $message);
				// Send an alert
				if (!empty($modSettings['Shop_noty_credits']))
					Shop::deployAlert($memID, 'credits', $user_info['id'], '?action=shop', $extra_items);
				// Let's get out of here and later we'll show a nice message
				redirectexit('action=shop;sa=gift3');
			}
		}
	}

	public static function Send2()
	{
		global $context, $smcFunc, $modSettings, $scripturl, $user_info, $txt;

		// What if the Gifts are disabled?
		if (empty($modSettings['Shop_enable_gift']))
			fatal_error($txt['Shop_currently_disabled_gift'], false);

		// Check if he is allowed to access this section
		if (!allowedTo('shop_canManage'))
			isAllowedTo('shop_canGift');

		// Set all the page stuff
		$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_gift'];
		$context['template_layers'][] = 'Shop_main';
		$context['template_layers'][] = 'Shop_giftTabs';
		$context['sub_template'] = 'Shop_giftSent';
		$context['linktree'][] = array(
			'url' => $scripturl . '?action=shop;sa=gift',
			'name' => $txt['Shop_shop_gift'],
		);
		// Sub-menu tabs
		$context['gift_tabs'] = self::Tabs();

		if (isset($_REQUEST['id']))
		{
			$itemid = (int) $_REQUEST['id'];

			// Get the item's information
			$result = $smcFunc['db_query']('', '
				SELECT p.id, p.itemid, s.name, s.status
					FROM {db_prefix}shop_inventory AS p
				LEFT JOIN {db_prefix}shop_items AS s ON (p.itemid = s.itemid)
				WHERE p.id = {int:id}',
				array(
					'id' => $itemid,
				)
			);
			$row = $smcFunc['db_fetch_assoc']($result);
			$smcFunc['db_free_result']($result);

			// That item is not currently enabled!
			if (!isset($_REQUEST['id']) || empty($row) || ($row['status'] == 0))
				fatal_error($txt['Shop_item_notfound'], false);

			// Let's display a nice message
			$context['shop']['gift_sent'] = sprintf($txt['Shop_gift_item_sent'], $row['name']);
		}
		// Well, we just need a different message here
		else
			$context['shop']['gift_sent'] = sprintf($txt['Shop_gift_money_sent'], $modSettings['Shop_credits_suffix'], $modSettings['Shop_credits_prefix'], $user_info['shopMoney']);
	}

	public static function sendPM($todo, $userid, $itemname, $amount, $message)
	{
		global $user_info, $sourcedir, $modSettings, $memberContext, $txt;

		// Who is sending the PM
		$pmfrom = array(
			'id' => 0,
			'name' => $txt['Shop_trade_notification_sold_from'],
			'username' => $txt['Shop_trade_notification_sold_from'],
		);
		// Who is receiving the PM		
		$pmto = array(
			'to' => array($userid),
			'bcc' => array()
		);
		// The message subject
		$subject = $txt['Shop_gift_notification_subject'];
		// Find out the member credits...
		$temp = loadMemberData($userid, false, 'profile');
		loadMemberContext($userid);
		$membermoney = $memberContext[$userid]['shopMoney'];

		// The actual message
		if ($todo == 'item')
			$body = sprintf($txt['Shop_gift_notification_message1'], $user_info['id'], $user_info['name'], $itemname, $message);
		elseif ($todo == 'money')
			$body = sprintf($txt['Shop_gift_notification_message2'], $user_info['id'], $user_info['name'], $modSettings['Shop_credits_suffix'], Shop::formatCash($amount), Shop::formatCash($membermoney), $message);

		// We need this file
		require_once($sourcedir . '/Subs-Post.php');
		// Send the PM
		sendpm($pmto, $subject, $body, false, $pmfrom);
	}
}