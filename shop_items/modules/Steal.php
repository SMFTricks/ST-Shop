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
		$this->authorWeb = 'http://www.smftricks.com/';
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
		global $item_info;

		if ($item_info[1] == 0) $item_info[1] = 40;

		return '
			<dl class="settings">
				<dt>
					'.Shop::text('steal_setting1').'<br />
					<span class="smalltext">'.Shop::text('steal_setting1_desc').'</span>
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" max="100" id="info1" name="info1" value="' . $item_info[1] . '" /> %
				</dd>
			</dl>';
	}

	function getUseInput()
	{
		global $context, $scripturl;

		return Shop::text('steal_from') . '&nbsp;<input class="input_text" type="text" id="stealfrom" name="stealfrom" size="50" />
				<a href="'.$scripturl.'?action=findmember;input=stealfrom;sesc='.$context['session_id'].'" onclick="return reqWin(this.href, 350, 400);">
					<span class="generic_icons assist"></span> '.Shop::text('inventory_member_find'). '
				</a><br />';
	}

	function onUse()
	{
		global $user_info, $item_info, $smcFunc;

		// Check some inputs
		if (!isset($_REQUEST['stealfrom']) || empty($_REQUEST['stealfrom'])) 
			fatal_error(Shop::text('user_unable_tofind'));

		// This code from PersonalMessage.php5. It trims the " characters off the membername posted, 
		// and then puts all names into an array
		$_REQUEST['stealfrom'] = strtr($_REQUEST['stealfrom'], array('\\"' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['stealfrom'], $matches);
		$userArray = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['stealfrom']))));

		// We only want the first memberName found
		$user = $userArray[0];

		// Get a random number between 0 and 100
		$try = mt_rand(0, 100);

		// If successful
		if ($try <= $item_info[1])
		{
			// Get stealee's (person we're stealing from) money count
			$result = $smcFunc['db_query']('', '
				SELECT shopMoney, real_name, id_member
				FROM {db_prefix}members 
				WHERE real_name = {string:name}', 
				array( 
					'name' => $user, 
				) 
			);

			// If user doesn't exist
			if ($smcFunc['db_num_rows']($result) == 0)
				fatal_error(Shop::text('user_unable_tofind'));

			// Wait, are you going to steal yourself? WTF
			if ($user_info['id'] == $row['id_member']) 
				fatal_error(Shop::text('steal_error_yourself'));

			$row = $smcFunc['db_fetch_assoc']($result);

			// Get random amount between 0 and amount of money stealee has
			$steal_amount = mt_rand(0, $row['shopMoney']);

			// We shouldn't get less than 0
			if ($steal_amount < 0)
				$steal_amount = 0;

			
			$stealee = $row['id_member'];
			$cash = $row['shopMoney'];

			$final_value1 = $cash - $steal_amount;
			updateMemberData($stealee, array('shopMoney' => $final_value1));

			//...and give to stealer (robber)
			$final_value2 = $user_info['shopMoney'] + $steal_amount;
			updateMemberData($user_info['id'], array('shopMoney' => $final_value2));

			// Now we are going to tell the user how much he got
			if ($steal_amount < 200)
				return '<div class="infobox">' . sprintf(Shop::text('steal_success1'), ShopMainData::formatCash($steal_amount)) . '</div>';
			// If it was less than 200, the user was not very lucky
			else
				return '<div class="infobox">' . sprintf(Shop::text('steal_success2'), ShopMainData::formatCash($steal_amount), $user) . '</div>';
		}
		else
		{
			return '<div class="errorbox">' . Shop::text('steal_error') . '</div>';
		}
	}
}

?>