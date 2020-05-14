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

class DecreasePost extends Helper\Module
{
	function _construct()
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
		global $item_info, $txt;

		// If it's empty, decrease 100 by default
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 100;

		$info = '
			<dl class="settings">
				<dt>
					'. $txt['Shop_dp_setting1']. '
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
			</dl>';

		return $info;
	}

	function getUseInput()
	{
		global $context, $txt;

		$search =
			$txt['Shop_inventory_member_name']. '
			&nbsp;<input type="text" name="username" id="username" />
			<div id="membernameItemContainer"></div>
			<span class="smalltext">'. $txt['Shop_dp_find_desc']. '</span>
			<br /><br />
			<script>
				var oAddMemberSuggest = new smc_AutoSuggest({
					sSelf: \'oAddMemberSuggest\',
					sSessionId: \''. $context['session_id']. '\',
					sSessionVar: \''. $context['session_var']. '\',
					sSuggestId: \'to_suggest\',
					sControlId: \'username\',
					sSearchType: \'member\',
					sPostName: \'memberid\',
					sURLMask: \'action=profile;u=%item_id%\',
					sTextDeleteItem: \''. $txt['autosuggest_delete_item']. '\',
					sItemListContainerId: \'membernameItemContainer\'
				});
			</script>';

		return $search;
	}

	function onUse()
	{
		global $smcFunc, $txt, $user_info, $item_info;

		// Set it to 100 by default
		if (empty($item_info[1]) || !isset($item_info[1]))
			$item_info[1] = 100;

		// Make sure we got an user
		if (!isset($_REQUEST['username']) || empty($_REQUEST['username']))
			fatal_error($txt['Shop_user_unable_tofind'], false);

		$member_query = array();
		$member_parameters = array();

		// Get the member name...
		$_REQUEST['username'] = strtr($smcFunc['htmlspecialchars']($_REQUEST['username'], ENT_QUOTES), array('&quot;' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST['username'], $matches);
		$member_name = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['username']))));

		foreach ($member_name as $index => $name)
		{
			$member_name[$index] = trim($smcFunc['strtolower']($member_name[$index]));

			if (strlen($member_name[$index]) == 0)
				unset($member_name[$index]);
		}
		// Construct the query
		if (!empty($member_name))
		{
			$member_query[] = 'LOWER(member_name) IN ({array_string:member_name})';
			$member_query[] = 'LOWER(real_name) IN ({array_string:member_name})';
			$member_parameters['member_name'] = $member_name;
		}
		if (!empty($member_query))
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_member, posts
				FROM {db_prefix}members
				WHERE (' . implode(' OR ', $member_query) . ')
				LIMIT 1',
				$member_parameters
			);
			$row = $smcFunc['db_fetch_assoc']($request);
				$memID = $row['id_member'];
				$pcount = $row['posts'];
			$smcFunc['db_free_result']($request);
		}

		// Empty? Something went wrong
		if (empty($row))
			fatal_lang_error('not_a_user', false, 404);
		// Did we find an user?
		elseif (empty($memID))
			fatal_error($txt['Shop_user_unable_tofind'], false);
		// You cannot affect yourself
		elseif ($memID == $user_info['id'])
			fatal_error($txt['Shop_dp_yourself'], false);

		// Update the information
		$final_value = $pcount - $item_info[1];
		updateMemberData($memID, array('posts' => $final_value));

		return '<div class="infobox">' . sprintf($txt['Shop_dp_success'], $_REQUEST['username'], $item_info[1]) . '</div>';
	}
}