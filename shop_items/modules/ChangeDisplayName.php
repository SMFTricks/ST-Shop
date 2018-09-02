<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: ChangeDisplayName.php 113 2007-04-14 08:39:52Z daniel15                 $ *
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

class item_ChangeDisplayName extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Change Display Name';
		$this->desc = 'Change your display name!';
		$this->price = 50;
		
		$this->require_input = true;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}

	function getAddInput()
	{
		global $item_info, $txt;
		loadLanguage('Shop');
		if ($item_info[1] == 0) $item_info[1] = 5;
		return '
			<dl class="settings">
				<dt>
					'. $txt['Shop_cdn_setting1'].'
				</dt>
				<dd>
					<input class="input_text" type="text" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
			</dl>';
	}

	function getUseInput()
	{
		global $item_info;

		// Use length of 5 as default
		if (!isset($item_info[1]) || $item_info[1] == 0) $item_info[1] = 5;
		
		return Shop::text('cdn_new_display_name').'&nbsp;<input class="input_text" type="text" id="newDisplayName" name="newDisplayName" size="50" /><br />
				<span class="smalltext">' . sprintf(Shop::text('cdn_new_display_name_desc'), $item_info[1]) . '</span><br />';
	}

	function onUse()
	{
		global $user_info, $item_info;
		
		// Use a length of 5 as default
		if (!isset($item_info[1]) || $item_info[1] == 0) $item_info[1] = 5;

		if (strlen($_REQUEST['newDisplayName']) < $item_info[1]) 
			fatal_error(sprintf(Shop::text('cdn_error'), $item_info[1]));

		updateMemberData($user_info['id'], array('real_name' => $_REQUEST['newDisplayName']));
		return '<div class="infobox">' . sprintf(Shop::text('cdn_success'), $_REQUEST['newDisplayName']) . '</div>';
	}

}

?>
