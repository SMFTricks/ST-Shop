<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: IncreaseKarma.php 113 2007-04-14 08:39:52Z daniel15                     $ *
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

class item_IncreaseKarma extends itemTemplate
{
	function getItemDetails()
	{
		$this->authorName = 'Daniel15';
		$this->authorWeb = 'http://www.dansoftaustralia.net/';
		$this->authorEmail = 'dansoft@dansoftaustralia.net';

		$this->name = 'Increase Karma by xxx';
		$this->desc = 'Increase your Karma by xxx';
		$this->price = 100;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}
	
	// See 'AddToPostCount.php' for info on how this works
function getAddInput()
	{
		global $user_info, $item_info;
		if ($item_info[1] == 0) $item_info[1] = 5;
		return 'Amount to increase Karma by: <input type="text" name="info1" value="' . $item_info[1] . '" />';
	}

function onUse()
	{
		global $smcFunc, $context, $user_info, $item_info;
		
		$result = $smcFunc['db_query']('', '
	        SELECT karma_good
		    FROM {db_prefix}members
		    WHERE id_member = {string:name} 
		    ORDER BY id_member DESC
		    LIMIT 1',
			  array(
			  'name' => $context['user']['id'],

				  )
			  );
	     $row = $smcFunc['db_fetch_assoc']($result);
	     $karma = $row['karma_good'];
         $smcFunc['db_free_result']($result);
	
	     $context['karma'] = $karma;
		
		$final_karma =  $item_info[1] + $karma;
        updateMemberData($context['user']['id'], array('karma_good' => $final_karma));

		return 'Successfully increased your Karma by ' . $item_info[1] . '!';
	}
}

?>
