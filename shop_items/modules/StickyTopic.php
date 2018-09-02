<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: StickyTopic.php 113 2007-04-14 08:39:52Z daniel15                       $ *
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

class item_StickyTopic extends itemTemplate
{
 
    function getItemDetails()
	{
		$this->authorName = 'Diego Andr&eacute;s';
		$this->authorWeb = 'http://www.smftricks.com/';
		$this->authorEmail = 'admin@smftricks.com';

		$this->name = 'Sticky Topic';
		$this->desc = 'Make any one of your topics a sticky!';
		$this->price = 400;

		$this->require_input = true;
		$this->can_use_item = true;
    }

    function getUseInput()
	{
		global $smcFunc, $user_info;
		
		$returnStr = Shop::text('st_choose_topic') . '<br />
			<select name="stickyTopic">';

		$result = $smcFunc['db_query']('', '
			SELECT t.id_member_started, t.id_topic, t.is_sticky, t.id_first_msg, m.id_msg, m.subject
			FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			WHERE t.id_member_started = {int:member} AND t.is_sticky = 0',
			array(
				'member' => $user_info['id'],
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($result))
		{
			$returnStr .= '<option value="' . $row['id_topic'] . '">' . $row['subject'] . '</option>';
		}
			
		$returnStr .= '</select><br />';
		
		// What if the user has 0 topics?
		if ($smcFunc['db_num_rows']($result) == 0)
		{
			return '<div class="errorbox">' .Shop::text('st_notopics') . '</div>';
		}
		else
			return $returnStr;
    }

    function onUse()
	{
		global $user_info, $smcFunc, $scripturl;
		
		if (!isset($_REQUEST['stickyTopic'])) 
			fatal_error(Shop::text('st_error'));

		$_REQUEST['stickyTopic'] = (int) $_REQUEST['stickyTopic'];
		
		$result = $smcFunc['db_query']('', '
			SELECT t.id_topic, t.is_sticky, t.id_member_started, m.subject, t.id_first_msg, m.id_msg
			FROM {db_prefix}topics AS t
			LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			WHERE t.id_topic = {int:id_topic}',
			array(
				'id_topic' => $_REQUEST['stickyTopic'],
			)
		);

		$row = $smcFunc['db_fetch_assoc']($result);		
		
		if ($smcFunc['db_num_rows']($result) == 0)
			fatal_error(Shop::text('st_topic_notexists'));

		if ($row['id_member_started'] != $user_info['id'])
			fatal_error(Shop::text('st_topic_notown'));
		
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics			 
			SET is_sticky = 1
			WHERE id_topic = {int:id_topic}
			LIMIT 1',
			array(
				'id_topic' => $_REQUEST['stickyTopic'],
			)
		);

		$topic_subject = $row['subject'];
		$topic_id = $_REQUEST['stickyTopic'];
							 
        return '<div class="infobox">' . sprintf(Shop::text('st_success'), $topic_id, $topic_subject) .'</div>';
    }
}

?>
