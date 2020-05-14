<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Modules;

use Shop\Shop;
use Shop\Helper;

if (!defined('SMF'))
	die('Hacking attempt...');

class StickyTopic extends Helper\Module
{
	function _construct()
	{
		$this->authorName = 'Diego Andr&eacute;s';
		$this->authorWeb = 'https://www.smftricks.com/';
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