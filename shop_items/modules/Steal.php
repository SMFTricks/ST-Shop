<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-08-04 11:56:24 +0200 (za, 04 aug 2007)                           $ *
* $Id:: Steal.php 125 2007-08-04 09:56:24Z daniel15                             $ *
* Software by:                DanSoft Australia (http://www.dansoftaustralia.net/)*
* Copyright 2005-2007 by:     DanSoft Australia (http://www.dansoftaustralia.net/)*
* Support, News, Updates at:  http://www.dansoftaustralia.net/                    *
*                                                                                 *
* Forum software by:          Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006-2007 by:     Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version of the license can always be found at                        *
* http://www.simplemachines.org.                                                  *
**********************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

class item_Steal extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Diego Andr&eacute;s';
		$this->authorWeb = 'https://www.smftricks.com/';
		$this->authorEmail = 'admin@smftricks.com';

		$this->name = 'Steal Credits';
		$this->desc = 'Try to steal credits from another member!';
		$this->price = 50;

		$this->require_input = true;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}

	function getAddInput()
	{
		global $item_info, $txt;

		// By default 40
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 40;

		// By default disabled
		if (empty($item_info[2]) || !isset($item_info[2]))
			$item_info[2] = 0;
		// By default disabled
		if (empty($item_info[3]) || !isset($item_info[3]))
			$item_info[3] = 0;

		$info = '
			<dl class="settings">
				<dt>
					'. $txt['Shop_steal_setting1']. '<br/>
					<span class="smalltext">'.$txt['Shop_steal_setting1_desc'].'</span>
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" max="100" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
				<dt>
					'. $txt['Shop_steal_setting2']. '<br/>
					<span class="smalltext">'.$txt['Shop_steal_setting2_desc'].'</span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" id="info2" name="info2" value="1"'. ($item_info[2] == 1 ? ' checked' : ''). ' />
				</dd>
				<dt>
					'. $txt['Shop_steal_setting3']. '<br/>
					<span class="smalltext">'.$txt['Shop_steal_setting3_desc'].'</span>
				</dt>
				<dd>
					<input class="input_check" type="checkbox" id="info3" name="info3" value="1"'. ($item_info[3] == 1 ? ' checked' : ''). ' />
				</dd>
			</dl>';

		return $info;
	}

	function getUseInput()
	{
		global $context, $txt;

		$search =
			$txt['Shop_steal_from']. '
			&nbsp;<input type="text" name="stealfrom" id="stealfrom" />
			<div id="membernameItemContainer"></div>
			<span class="smalltext">'. $txt['Shop_inventory_member_find']. '</span>
			<br /><br />
			<script>
				var oAddMemberSuggest = new smc_AutoSuggest({
					sSelf: \'oAddMemberSuggest\',
					sSessionId: \''. $context['session_id']. '\',
					sSessionVar: \''. $context['session_var']. '\',
					sSuggestId: \'to_suggest\',
					sControlId: \'stealfrom\',
					sSearchType: \'member\',
					sPostName: \'memberid\',
					sURLMask: \'action=profile;u=%item_id%\',
					sTextDeleteItem: \''. $txt['autosuggest_delete_item']. '\',
					sItemListContainerId: \'membernameItemContainer\'
				});
			</script>';

		return $search;
	}

	function onUse()
	{
		global $user_info, $item_info, $smcFunc, $txt, $settings;

		// By default 40
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 40;

		// By default enabled
		if (empty($item_info[2]) || !isset($item_info[2]))
			$item_info[2] = 0;

		// Check some inputs
		if (!isset($_REQUEST['stealfrom']) || empty($_REQUEST['stealfrom'])) 
			fatal_error($txt['Shop_user_unable_tofind'], false);

		// Get a random number between 0 and 100
		$prob = mt_rand(0, 100);

		// If successful
		if ($prob <= $item_info[1])
		{
			$member_query = array();
			$member_parameters = array();

			// Get the member name...
			$_REQUEST['stealfrom'] = strtr($smcFunc['htmlspecialchars']($_REQUEST['stealfrom'], ENT_QUOTES), array('&quot;' => '"'));
			preg_match_all('~"([^"]+)"~', $_REQUEST['stealfrom'], $matches);
			$member_name = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['stealfrom']))));

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
					SELECT id_member, posts, shopMoney, shopBank, real_name
					FROM {db_prefix}members
					WHERE (' . implode(' OR ', $member_query) . ')
					LIMIT 1',
					$member_parameters
				);
				$row = $smcFunc['db_fetch_assoc']($request);
					$memID = $row['id_member'];
					$pcount = $row['posts'];
					$memCash = $row['shopMoney'];
					$memName = $row['real_name'];
				$smcFunc['db_free_result']($request);
			}

			// Empty? Something went wrong
			if (empty($row))
				fatal_lang_error('not_a_user', false, 404);
			// Did we find an user?
			elseif (empty($memID))
				fatal_error($txt['Shop_user_unable_tofind'], false);
			// You cannot affect yourself
			elseif ($memID == $user_info['id'])
				fatal_error($txt['Shop_steal_error_yourself'], false);
			// That user's pocket is empty!
			elseif (empty($memCash))
				fatal_error($txt['Shop_steal_error_zero'], false);

			// Get random amount between 0 and amount of money stealee has
			$steal_amount = mt_rand(1, $memCash);

			// Steal from him!
			$final_value1 = $memCash - $steal_amount;
			updateMemberData($memID, array('shopMoney' => $final_value1));

			//...and give to stealer (robber)
			$final_value2 = $user_info['shopMoney'] + $steal_amount;
			updateMemberData($user_info['id'], array('shopMoney' => $final_value2));

			$extra_items = array(
				'item_icon' => $settings['images_url'] . '/icons/shop/steal.png',
				'amount' => Shop::formatCash($steal_amount),
			);

			// Now we are going to tell the user how much he got
			if ($steal_amount < 200)
				$info_result = '<div class="infobox">' . sprintf($txt['Shop_steal_success1'], Shop::formatCash($steal_amount), $memName) . '</div>';
			// If it was less than 200, the user was not very lucky
			else
				$info_result = '<div class="infobox">' . sprintf($txt['Shop_steal_success2'], Shop::formatCash($steal_amount), $memName) . '</div>';

			// Send PM
			if (!empty($item_info[2]))
				self::stealPM($memID, $steal_amount, $final_value1);
			// Send Alert
			if (!empty($item_info[3]))
				Shop::deployAlert($memID, 'module_steal', $user_info['id'], '?action=shop', $extra_items, false);
		}
		else
			$info_result = '<div class="errorbox">' . $txt['Shop_steal_error'] . '</div>';

		// Return the message
		return $info_result;
	}

	function stealPM($memID, $steal_amount, $current_money)
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
			'to' => array($memID),
			'bcc' => array()
		);
		// The message subject
		$subject = $txt['Shop_steal_notification_robbed'];

		// The actual message
		$message = sprintf($txt['Shop_steal_notification_pm'], $user_info['id'], $user_info['name'], Shop::formatCash($steal_amount), Shop::formatCash($current_money));

		// We need this file
		require_once($sourcedir . '/Subs-Post.php');
		// Send the PM
		sendpm($pmto, $subject, $message, false, $pmfrom);
	}
}