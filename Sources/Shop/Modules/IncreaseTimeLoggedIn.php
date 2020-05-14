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

class IncreaseTimeLoggedIn extends Helper\Module
{
	function _construct()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Increase Total Time by xxx';
		$this->desc = 'Increase your total time logged in by xxx (default is 12 hours)';
		$this->price = 50;
		
		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}
	
	function getAddInput()
	{
		global $item_info, $txt;

		// By default 12 hours
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 12;

		$info = '
			<dl class="settings">
				<dt>
					'.$txt['Shop_itli_setting1'].'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $item_info[1] . '" /> '.$txt['Shop_itli_hours'].'
				</dd>
			</dl>';

		return $info;
	}

	function onUse()
	{
		global $user_info, $item_info, $txt;

		// By default 12 hours
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 12;

		// Add the time to his current total
		$time = (int) ($user_info['total_time_logged_in'] + ($item_info[1] * 3600));

		// Update info
		updateMemberData($user_info['id'], array('total_time_logged_in' => (int) $time));
		
		return '<div class="infobox">' . sprintf($txt['Shop_itli_success'], $item_info[1]) . '</div>';
	}
}