<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: RandomMoney.php 113 2007-04-14 08:39:52Z daniel15                       $ *
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

class item_RandomMoney extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Random Money (between xxx and xxx)';
		$this->desc = 'Get a random amount of money, between xxx and xxx!';
		$this->price = 75;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}

	function getAddInput()
	{
		global $item_info, $txt;

		// By default -190 and 190
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = -190;
		if (empty($item_info[2]) || !isset($item_info[2]))
			$item_info[2] = 190;

		$info = '
			<dl class="settings">
				<dt>
					'.$txt['Shop_rm_setting1'].'
				</dt>
				<dd>
					<input class="input_text" type="number" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
				<dt>
					'.$txt['Shop_rm_setting2'].'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info2" name="info2" value="' . $item_info[2] . '" />
				</dd>
			</dl>';

		return $info;
	}

	function onUse()
	{
		global $user_info, $item_info, $txt;

		// If an amount was not defined by the admin, assume defaults
		if (!isset($item_info[1]) || empty($item_info[1]))
			$item_info[1] = -190;

		if (!isset($item_info[2]) || empty($item_info[2]))
			$item_info[2] = 190;

		$amount = mt_rand($item_info[1], $item_info[2]);

		// By default we are always adding the money to their pocket.
		$final_value = $user_info['shopMoney'] + $amount;

		// Did he lose money?
		if ($amount < 0)
		{
			// If the user has enough money to pay for it out of their pocket
			if ($user_info['shopMoney'] >= ($amount*(-1)))
			{
				updateMemberData($user_info['id'], array('shopMoney' => $final_value));
				$info_result = '<div class="errorbox">' . sprintf($txt['Shop_rm_lost_pocket'], Shop::formatCash(abs($amount))) . '</div>';
			}
			// Remove it from the bank then!
			else
			{
				$final_value = $user_info['shopBank'] + $amount;
				updateMemberData($user_info['id'], array('shopBank' => $final_value));
				$info_result = '<div class="errorbox">' . sprintf($txt['Shop_rm_lost_bank'], Shop::formatCash(abs($amount))) . '</div>';
			}

		}
		// Congratulations! You won some money! :D
		else
		{
			updateMemberData($user_info['id'], array('shopMoney' => $final_value));
			$info_result = '<div class="infobox">' . sprintf($txt['Shop_rm_success'], Shop::formatCash($amount)) . '</div>';

		}
		// Return the final message
		return $info_result;
	}
}