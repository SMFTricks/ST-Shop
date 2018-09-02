<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: DecreasePost.php 113 2007-04-14 08:39:52Z daniel15                      $ *
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

class item_DecreasePost extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Decrease Posts by xxx';
		$this->desc = 'Decrease <i>Someone else\'s</i> post count by xxx!!';
		$this->price = 200;
		
		$this->require_input = true;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}
	
	function getAddInput()
	{
		global $item_info;
		
		if ($item_info[1] == 0) $item_info[1] = 100;
		return '
			<dl class="settings">
				<dt>
					'.Shop::text('dp_setting1').'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
			</dl>';
	}

	function getUseInput()
	{
		global $context, $scripturl;
		return Shop::text('inventory_member_name') . '&nbsp;<input class="input_text" type="text" id="username" name="username" size="50" />
				<a href="'.$scripturl.'?action=findmember;input=username;sesc='.$context['session_id'].'" onclick="return reqWin(this.href, 350, 400);">
					<span class="generic_icons assist"></span> '.Shop::text('inventory_member_find'). '
				</a><br />';
	}

	function onUse()
	{
		global $smcFunc, $item_info;
		
		if ($item_info[1] == 0) $item_info[1] = 100;

		$result = $smcFunc['db_query']('', '
			SELECT id_member, posts, real_name
				FROM {db_prefix}members
				WHERE real_name = {string:user}',
			array(
				'user' => $_REQUEST['username'],
			)
		);

		// If user doesn't exist
		if ($smcFunc['db_num_rows']($result) == 0)
			fatal_error(Shop::text('user_unable_tofind'));

		$row = $smcFunc['db_fetch_assoc']($result);
			
		// This code from PersonalMessage.php. It trims the " characters off the membername posted, 
		// and then puts all names into an array
		$_REQUEST['username'] = strtr($_REQUEST['username'], array('\\"' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['username'], $matches);
		$userArray = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['username']))));
		
		// We only want the first memberName found
		$user = $userArray[0];
		
		$final_value = $row['posts'] - $item_info[1];
        updateMemberData($row['id_member'], array('posts' => $final_value));

        return '<div class="infobox">' . sprintf(Shop::text('dp_success'), $user, $item_info[1]) . '</div>';
	
	}

}

?>
