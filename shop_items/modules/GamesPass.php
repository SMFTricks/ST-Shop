<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           ST Shop 0.7                                         *
* Software by:                SA Development (http://sleepy-arcade.ath.cx/)       *
* Copyright 2005-2018 by:     wdm2005 (https://github.com/SAMods)                 *
* Support, News, Updates at:  https://github.com/SAMods                           *
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

class item_GamesPass extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'wdm2005';
		$this->authorWeb = 'http://sleepy-arcade.ath.cx/';
		$this->authorEmail = 'wdm2005@blueyonder.co.uk';

		$this->name = 'Games Room Pass xxx days';
		$this->desc = 'Allows access to Games Room for xxx days';
		$this->price = 50;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}

	function getAddInput()
	{
		global $item_info, $txt;

		// By default 30 days
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 30;

		$info = '
			<dl class="settings">
				<dt>
					'.$txt['Shop_games_setting1'].'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
			</dl>';

		return $info;
	}

	function onUse()
	{
		global $user_info, $item_info, $txt, $context;

		// By default 30 days
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 30;

		// Get the time in seconds
		$days = 86400 * $item_info[1];

		// He still have access?
		if (!empty($user_info['gamesPass']) && $user_info['gamesPass'] > time())
			$time = $user_info['gamesPass'] + $days;
		// Expired.. Then calculate from this moment
		else
			$time = time() + $days;

		// Update the information
		updateMemberData($user_info['id'], array('gamesPass' => $time));

		// Give him the exact amount of days he now has
		$expires = $context['user']['gamedays'] + $item_info[1];
		
		return '<div class="infobox">' . sprintf($txt['Shop_games_success'], $expires) . '</div>';
	}
}