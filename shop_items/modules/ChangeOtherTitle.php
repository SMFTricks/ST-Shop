 <?php
/**********************************************************************************
* SA Shop item                                                                    *
***********************************************************************************
* SA Shop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SA Shop 2.0 (Build 12)                              *
* $Date:: 2014-12-28 10:39:52 +0200 (za, 28 dic 2014)                           $ *
* $Id:: ChangeOtherTitle.php 113 2014-12-28 08:39:52Z		                    $ *
* Software by:                Diego Andrés (http://www.smftricks.com/)            *
* Copyright 2014 by:     Diego Andrés (http://www.smftricks.com/)                 *
* Support, News, Updates at:  http://www.smftricks.com/                           *
*                                                                                 *
* Forum software by:          Simple Machines (http://www.simplemachines.org)     *
**********************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

class item_ChangeOtherTitle extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Diego Andr&eacute;s';
		$this->authorWeb = 'http://www.smftricks.com/';
		$this->authorEmail ='admin@smftricks.com';

		$this->name = 'Change Other\'s Title';
		$this->desc = 'Change someone else\'s title';
		$this->price = 200;
		
		$this->require_input = true;
		$this->can_use_item = true;
	}

	function getUseInput()
	{
		global $context, $scripturl;
		return Shop::text('inventory_member_name') . '&nbsp;<input class="input_text" type="text" id="username" name="username" size="50" />
				<a href="'.$scripturl.'?action=findmember;input=username;sesc='.$context['session_id'].'" onclick="return reqWin(this.href, 350, 400);">
					<span class="generic_icons assist"></span> '.Shop::text('inventory_member_find'). '
				</a><br /><br />
				'.Shop::text('cot_title').' <input class="input_text" type="text" name="newtitle" size="50" /><br />';
	}

	function onUse()
	{
		global $smcFunc;

		// The user actually exists?
		$result = $smcFunc['db_query']('', '
			SELECT real_name, id_member
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

		// Title should not be empty 
		if (empty($_REQUEST['newtitle']))
			fatal_error(Shop::text('cot_empty_title'));

		// This code from PersonalMessage.php. It trims the " characters off the membername posted, 
		// and then puts all names into an array
		$_REQUEST['username'] = strtr($_REQUEST['username'], array('\\"' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['username'], $matches);
		$userArray = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['username']))));
		
		// We only want the first memberName found
		$user = $userArray[0];
		
        updateMemberData($row['id_member'], array('usertitle' => $_REQUEST['newtitle']));
		
		return '<div class="infobox">' . sprintf(Shop::text('cot_success'), $user, $_REQUEST['newtitle']) . '</div>';
	}

}

?>
