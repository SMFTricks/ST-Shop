<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: IncreaseTimeLoggedIn.php 113 2007-04-14 08:39:52Z daniel15              $ *
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

class item_IncreaseTimeLoggedIn extends itemTemplate
{
	function getItemDetails()
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
		if (empty($item_info[1]))
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
		if (empty($item_info[1]))
			$item_info[1] = 12;

		// Add the time to his current total
		$time = (int) ($user_info['total_time_logged_in'] + ($item_info[1] * 3600));

		// Update info
		updateMemberData($user_info['id'], array('total_time_logged_in' => (int) $time));
		
		return '<div class="infobox">' . sprintf($txt['Shop_itli_success'], $item_info[1]) . '</div>';
	}
}