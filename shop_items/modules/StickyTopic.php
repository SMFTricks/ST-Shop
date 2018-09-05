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
		global $smcFunc, $user_info, $txt;

		$result = $smcFunc['db_query']('', '
			SELECT t.id_member_started, t.id_topic, t.is_sticky, t.id_first_msg, m.id_msg, m.subject
			FROM {db_prefix}topics AS t
			INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
			WHERE t.id_member_started = {int:member} AND t.is_sticky = 0',
			array(
				'member' => $user_info['id'],
			)
		);

		// What if the user has 0 topics?
		if (empty($smcFunc['db_num_rows']($result)))
			$returnStr = '<div class="errorbox">'. $txt['Shop_st_notopics'] . '</div>';
		else
		{
			$returnStr = $txt['Shop_st_choose_topic'] . '<br />
			<select name="stickyTopic">';
			while ($row = $smcFunc['db_fetch_assoc']($result))
				$returnStr .= '<option value="' . $row['id_topic'] . '">' . $row['subject'] . '</option>';
			$returnStr .= '</select><br />';
		}
		$smcFunc['db_free_result']($result);

		// Return the list of topics or an error
		return $returnStr;
	}

	function onUse()
	{
		global $user_info, $smcFunc, $txt;
		
		if (!isset($_REQUEST['stickyTopic']) || empty($_REQUEST['stickyTopic'])) 
			fatal_error($txt['Shop_st_error']);

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
		$smcFunc['db_free_result']($result);

		$topic_subject = $row['subject'];
		$topic_id = $_REQUEST['stickyTopic'];

		// That topic wasn't on the list...
		if (empty($row))
			fatal_error($txt['Shop_st_topic_notexists'], false);
		// That topic is not yours although it was...
		elseif ($row['id_member_started'] != $user_info['id'])
			fatal_error($txt['Shop_st_topic_notown'], false);
		
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET is_sticky = 1
			WHERE id_topic = {int:id_topic}',
			array(
				'id_topic' => $_REQUEST['stickyTopic'],
			)
		);
							 
		return '<div class="infobox">' . sprintf($txt['Shop_st_success'], $topic_id, $topic_subject) .'</div>';
	}
}