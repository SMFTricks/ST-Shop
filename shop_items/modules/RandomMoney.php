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
		global $item_info;

		if ($item_info[1] == 0) $item_info[1] = '-190';
		if ($item_info[2] == 0) $item_info[2] = '190';

		return '
			<dl class="settings">
				<dt>
					'.Shop::text('rm_setting1').'
				</dt>
				<dd>
					<input class="input_text" type="number" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
				<dt>
					'.Shop::text('rm_setting2').'
				</dt>
				<dd>
					<input class="input_text" type="number" id="info2" name="info2" value="' . $item_info[2] . '" />
				</dd>
			</dl>';
    }

    function onUse()
	{
        global $smcFunc, $user_info, $item_info;

		// If an amount was not defined by the admin, assume defaults
        if (!isset($item_info[1]) || empty($item_info[1]))
            $item_info[1] = -190;

        if (!isset($item_info[2]) || empty($item_info[2]))
            $item_info[2] = 190;

        $amount = mt_rand($item_info[1], $item_info[2]);

		// Did we lose money?
		if ($amount < 0)
		{
			$result = $smcFunc['db_query']('', "
				SELECT shopMoney, shopBank
				FROM {db_prefix}members
				WHERE id_member = {int:id}",
				array(
					'id' => $user_info['id'],
				)
			);

			$row = $smcFunc['db_fetch_assoc']($result);

			$money = $row['shopMoney'] + $amount;

			// If the user has enough money to pay for it out of his/her pocket
			if ($money >= 0)
			{
				updateMemberData($user_info['id'], array('shopMoney' => $money));
				return '<div class="errorbox">' . sprintf(Shop::text('rm_lost_pocket'), ShopMainData::formatCash(abs($amount))) . '</div>';
			}

			// Do we need to get the bank money instead?
			else
			{
				$bank = $row['shopBank'] + $amount;
				updateMemberData($user_info['id'], array('shopBank' => $bank));
				return '<div class="errorbox">' . sprintf(Shop::text('rm_lost_bank'), ShopMainData::formatCash(abs($amount))) . '</div>';
			}

		}

		// Congratulations! You won some money! :D
		else
		{
			$money = $user_info['shopMoney'] + $amount;
			updateMemberData($user_info['id'], array('shopMoney' => $money));
			return '<div class="infobox">' . sprintf(Shop::text('rm_success'), ShopMainData::formatCash($amount)) . '</div>';

		}

    }

}

?>

