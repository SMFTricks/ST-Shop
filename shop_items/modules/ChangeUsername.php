<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: ChangeUsername.php 113 2007-04-14 08:39:52Z daniel15                    $ *
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

class item_ChangeUsername extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Change Username';
		$this->desc = 'Change your Username!';
		$this->price = 50;
	}

	function getUseInput()
	{
		global $txt;

		$input =
			$txt['Shop_cu_new_username'].'&nbsp;<input class="input_text" type="text" id="newusername" name="newusername" size="60" /><br />
				<span class="smalltext">'.$txt['Shop_cu_new_username_desc'].'</span><br />';

		return $input;
	}

	function onUse()
	{
		global $user_info, $smcFunc, $context, $sourcedir, $item_info, $txt;

		$value = trim(preg_replace('~[\t\n\r \x0B\0' . ($context['utf8'] ? '\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}' : '\x00-\x08\x0B\x0C\x0E-\x19\xA0') . ']+~' . ($context['utf8'] ? 'u' : ''), ' ', $_REQUEST['newusername']));

		// Name can't be empty!
		if (trim($value) == '')
			fatal_error($txt['Shop_cu_error_empty'], false);
		// It's too long! :o
		elseif ($smcFunc['strlen']($value) > 25)
			fatal_error($txt['Shop_cdn_error_long'], false);
		// Why you want the same name?
		elseif ($user_info['username'] == $value)
			fatal_error($txt['Shop_cdn_error_same'], false);
		// Alright everything fine. But, is it a reserved name?
		elseif ($user_info['username'] != $value)
		{
			require_once($sourcedir . '/Subs-Members.php');
			if (isReservedName($value, $user_info['id']))
				fatal_error($txt['Shop_cdn_error_taken'], false);
		}

		// Update the information
		updateMemberData($user_info['id'], array('member_name' => $value));

		return '<div class="infobox">' . sprintf($txt['Shop_cu_success'], $value) . '</div>';
	}
}