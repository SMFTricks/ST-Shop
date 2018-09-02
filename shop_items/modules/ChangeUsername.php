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
		return Shop::text('cu_new_username') .'&nbsp;<input class="input_text" type="text" id="newusername" name="newusername" size="50" /><br />
				<span class="smalltext">'.Shop::text('cu_new_username_desc').'</span><br />';
	}

	function onUse()
	{
		global $user_info;

		if (!isset($_REQUEST['newusername']) || $_REQUEST['newusername'] == ' ' || empty($_REQUEST['newusername']))
			fatal_error(Shop::text('cu_error'));

		updateMemberData($user_info['id'], array('member_name' => $_REQUEST['newusername']));
		return '<div class="infobox">' . sprintf(Shop::text('cu_success'), $_REQUEST['newusername']) . '</div>';
	}

}

?>
