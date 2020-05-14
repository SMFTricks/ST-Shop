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

class ChangeOtherTitle extends Helper\Module
{
	function _construct()
	{
		$this->authorName = 'Diego Andr&eacute;s';
		$this->authorWeb = 'https://www.smftricks.com/';
		$this->authorEmail ='admin@smftricks.com';

		$this->name = Shop::getText('noty_items');
		$this->desc = 'Change someone else\'s title';
		$this->price = 200;
		
		$this->require_input = true;
		$this->can_use_item = true;
	}

	function getUseInput()
	{
		global $context;

		return
			Shop::getText('inventory_member_name'). '
			&nbsp;<input type="text" name="username" id="username" />
			<div id="membernameItemContainer"></div>
			<span class="smalltext">'. Shop::getText('cot_find_desc'). '</span>
			<br /><br />
			'. Shop::getText('cot_title'). '
			&nbsp;<input class="input_text" type="text" name="newtitle" size="50" />
			<br />
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
					sTextDeleteItem: \''. Shop::getText('autosuggest_delete_item', false). '\',
					sItemListContainerId: \'membernameItemContainer\'
				});
			</script>';
	}

	function onUse()
	{
		global $smcFunc, $user_info;

		// Make sure we got an user
		if (!isset($_REQUEST['username']) || empty($_REQUEST['username']))
			fatal_error(Shop::getText('user_unable_tofind'), false);
		// Somehow we missed the title?
		elseif (!isset($_REQUEST['newtitle']))
			fatal_error(Shop::getText('cot_empty_title'), false);

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
				SELECT id_member
				FROM {db_prefix}members
				WHERE (' . implode(' OR ', $member_query) . ')
				LIMIT 1',
				$member_parameters
			);
			$row = $smcFunc['db_fetch_assoc']($request);
				$memID = $row['id_member'];
			$smcFunc['db_free_result']($request);
		}

		// Empty? Something went wrong
		if (empty($row))
			fatal_lang_error('not_a_user', false, 404);
		// Did we find an user?
		elseif (empty($memID))
			fatal_error(Shop::getText('user_unable_tofind'), false);
		// You cannot affect yourself
		elseif ($memID == $user_info['id'])
			fatal_error(Shop::getText('cot_notown_title'), false);

		// Update the information
		updateMemberData($memID, array('usertitle' => $_REQUEST['newtitle']));

		return '<div class="infobox">' . sprintf(Shop::getText('cot_success'), $_REQUEST['username'], $_REQUEST['newtitle']) . '</div>';
	}
}