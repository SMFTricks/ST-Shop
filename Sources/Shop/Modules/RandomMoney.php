<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Modules;

use Shop\Shop;
use Shop\Helper;

if (!defined('SMF'))
	die('Hacking attempt...');

class RandomMoney extends Helper\Module
{
	function _construct()
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