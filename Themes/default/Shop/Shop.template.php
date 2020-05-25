<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

use Shop\Helper\Format;

function template_shop_above()
{
	global $context, $scripturl, $modSettings, $txt;

	// Check for avoid errors
	if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_maintenance']) && allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
		return false;

	// Shop is in maintenance??
	if (!empty($modSettings['Shop_enable_maintenance']))
		echo '
		<div id="errors" class="errorbox" style="margin-bottom: -10px;">
			<dl>
				<dt>
					<strong id="error_serious">', $txt['Shop_currently_maintenance_warn'], '</strong>
				</dt>
				<dd id="error_list" class="error">
					', $txt['Shop_currently_maintenance_warn_desc'], '
				</dd>
			</dl>
		</div>
		<div class="clear"></div>';

	echo '
		<div class="buttonlist floatright">';
		
	foreach ($context['shop']['tabs'] as $action => $tab)
	{
		if ((!allowedTo($tab['permission']) && allowedTo('shop_canManage')) || (allowedTo($tab['permission'])) && !empty($modSettings[$tab['enable']]))
			echo '
			<a class="button', (isset($_REQUEST['sa']) && in_array($_REQUEST['sa'],$tab['action'])) ? ' active' : (!isset($_REQUEST['sa']) && $action == 'home' ? ' active' : ''), '" href="' , $scripturl . '?action=shop;sa=', $action, '">', $tab['label'], '</a>';
	}

	echo '
		</div>';

	// Wrap everything by default
	echo '
		<div class="clear"></div>
		<div class="cat_bar">
			<h3 class="catbg">
				', $context['page_title'], '
			</h3>
		</div>
		<div class="windowbg">';
}

function template_home()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	echo '
	<div id="basicinfo" style="float: right; text-align: center;">
		<div class="title_bar">
			<h3 class="titlebg">
				', $context['user']['name'], '
			</h3>
		</div>
		<div class="information">
			<a href="', $scripturl, '?action=profile"><img class="avatar" style="display: inline" src="', $context['user']['avatar']['href'], '" alt="" /></a><br />
			<strong>', $txt['Shop_money_pocket'], ':</strong> ', Format::cash($context['user']['shopMoney']), '<br />
			<strong>', $txt['Shop_money_bank'], ':</strong> ', Format::cash($context['user']['shopBank']), '<br />
			<strong>', $txt['Shop_main_games'], ':</strong> ', $context['user']['gamedays'], $txt['Shop_games_days'], '
		</div>
	</div>
	<div id="detailedinfo" style="float: left;">
		<div class="title_bar">
			<h3 class="titlebg">
				', $context['shop']['forum_welcome'], '
			</h3>
		</div>
		<div class="information">
			', $context['shop']['welcome'], '
		</div>';

	/*foreach ($context['home_stats'] as $block)
	{
		// Check if he has enough privileges to show him this information
		if (empty($block['enabled']))
			continue;

		echo '
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg">
						<img class="centericon" src="', $settings['default_images_url'], '/icons/shop/', $block['icon'], '" alt="" /> ', $block['label'], '
					</h4>
				</div>

					<dl class="stats" style="padding: 5px;">';

		foreach ($block['function'] as $item)
		{
			echo '
						<dt style="font-weight: normal;">
							', (!empty($item['image']) ? $item['image'] : $item['link']), '
						</dt>
						<dd class="statsbar', empty($item['image']) ? ' generic_bar righttext' : '', '">';

				if (!empty($item['percent']))
					echo '
							<div class="bar" style="width: ', $item['percent'], '%;"></div>';
				else
					echo '
							<div class="bar empty"></div>';
			echo '
							<span>', (!empty($item['image']) ? $item['name'] : $item['num']), '</span>
						</dd>';
		}

		echo '
					</dl>
			</div>';
	}*/
			echo '
	</div>';
}

function template_shop_stats()
{
	global $txt, $context, $settings;

	// Store Stats
	foreach ($context['stats_blocks']['shop'] as $block)
	{
		// Check if he has enough privileges to show him this information
		if (empty($block['enabled']))
			continue;

		echo '
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg">
						<img class="centericon" src="', $settings['default_images_url'], '/icons/shop/', $block['icon'], '" alt="" /> ', $block['label'], '
					</h4>
				</div>
					<dl class="stats">';

		foreach ($block['function'] as $item)
		{
				echo '
						<dt>
							', !empty($item['image']) ? $item['image'].' &nbsp;'.$item['name'] : $item['link'], '
						</dt>
						<dd class="statsbar generic_bar righttext">';

				if (!empty($item['percent']))
					echo '
							<div class="bar" style="width: ', $item['percent'], '%;"></div>';
				else
					echo '
							<div class="bar empty"></div>';
			echo '
							<span>', $item['num'], '</span>
						</dd>';
		}

		echo '
					</dl>
			</div>';
	}
}
function template_options_above()
{
	global $scripturl, $context, $txt;

	// Tabs??
	if (!empty($context['section_tabs']))
	{
		echo '
		<div class="buttonlist floatleft">';
		
		foreach ($context['section_tabs'] as $action => $tab)
			echo '
			<a class="button', (isset($_REQUEST['sa']) && in_array($_REQUEST['sa'],$tab['action']) && empty($tab['anchor'])) ? ' active' : '', '" href="' , $scripturl . '?action=shop;sa=', $action, '">', $tab['label'], '</a>';
		
		echo '
		</div>';
	}

	// Categories?
	if (!empty($context['shop_categories_list']))
	{
		echo '
		<form action="'. $scripturl. $context['form_url']. '" method="post" class="floatright" style="margin: 5px 0 10px">
			<select name="cat" id="cat">
				<optgroup label="'. $txt['Shop_categories']. '">
					<option value="-1"'. (!isset($_REQUEST['cat']) || $_REQUEST['cat'] == -1 ? ' selected="selected"' : ''). '>'. $txt['Shop_categories_all']. '</option>
					<option value="0"'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] == 0 ? ' selected="selected"' : ''). '>'. $txt['Shop_item_uncategorized']. '</option>';

			// List the categories if there are
			foreach ($context['shop_categories_list'] as $category)
				echo '
					<option value="'. $category['catid']. '"'. (isset($_REQUEST['cat']) && $_REQUEST['cat'] == $category['catid'] ? ' selected="selected"' : ''). '>'. $category['name']. '</option>';
			
				echo '
				</optgroup>
			</select>&nbsp;
			<input class="button" type="submit" value="'. $txt['go']. '" />
		</form>';
	}

	// Extra space when we have tabs
	if (!empty($context['section_tabs']))
		echo '
	<div class="clear"></div>';
}

function template_options_below() {}

function template_use()
{
	global $context, $txt, $scripturl;

		echo '
		<div class="roundframe">
			<form method="post" action="', $scripturl,'?action=shop;sa=invused">
				<input type="hidden" name="id" value="', $context['item']['id'], '">
				', $txt['Shop_inventory_use_confirm'], '<br /><br />
				', $context['shop']['use']['input'], '<br />
				<input class="button floatright" type="submit" value="', $txt['Shop_item_useit'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_invused()
{
	global $context, $txt;

		echo '
		<div class="windowbg">
			', sprintf($txt['Shop_item_used_success'], $context['item']['name']), '<br /><br />
			', $context['shop']['used']['input'], '<br />
		</div>';
}

function template_gift($message = true)
{
	global $context, $txt, $scripturl, $modSettings;

	// If we are sending money, we need a different input
	if ($_REQUEST['sa'] == 'sendmoney')
	{
		echo '
				<dt>
					', $txt['Shop_gift_amount'], ':
				</dt>
				<dd>
					', Format::cash('<input min="1" type="number" name="amount" size="10" />'), '
				</dd>';
	}
	// Sending an item then...
	else
	{
		// User needs permissions, or items...
		if (empty($context['shop']['view_inventory']) || empty($context['shop_user_items_list']))
		{
			// No message, it'd look better imo
			$message = false;

			echo '
			</dl>
				<div class="errorbox">', $txt[(empty($context['shop_user_items_list']) ? 'Shop_gift_no_items' : 'cannot_shop_viewInventory')], '</div>
			<dl class="settings">';
		}
		// Show the list of items
		else
		{
			echo '
				<dt>
					', $txt['Shop_gift_item_select'], ':
				</dt>
				<dd>
					<select name="item" id="item">
						<optgroup label="', $txt['Shop_gift_item_select'], '">';

						// List the categories
						foreach ($context['shop_user_items_list'] as $item)
						echo '
							<option value="', $item['id'], '">', $item['name'], '</option>';

					echo '
						</optgroup>
					</select>
				</dd>';
		}
	}
	// Setting up a message?
	if (!empty($message))
		echo '
				<dt>
					', $txt['Shop_gift_message'], ':<br/>
					<span class="smalltext">', $txt['Shop_gift_message_desc'], '</span>
				</dt>
				<dd>
					<textarea name="message" id="message" cols="35" rows="2"></textarea>
				</dd>';
}

function template_bank()
{
	global $context, $txt, $scripturl, $modSettings;

	// Welcome message
	echo '
		<div class="title_bar">
			<h4 class="titlebg">
				', $txt['Shop_bank_welcome'], '
			</h4>
		</div>
		<div class="information">
			', $context['page_description'], '
		</div>';

	if (isset($_REQUEST['deposit']) || isset($_REQUEST['withdrawal']))
		echo '
		<div class="infobox">', $context['Shop']['bank']['success'], '</div>';

	echo '
		<div class="roundframe">
			', empty($modSettings['Shop_bank_withdrawal_fee']) ? '' : '<strong>' . $txt['Shop_bank_withdrawal_fee'] . ':</strong> ' . Format::cash($modSettings['Shop_bank_withdrawal_fee']) . '<br />', '
			', empty($modSettings['Shop_bank_deposit_fee']) ? '' : '<strong>' . $txt['Shop_bank_deposit_fee'] . ':</strong> ' . Format::cash($modSettings['Shop_bank_deposit_fee']) . '<br />', '
			', empty($modSettings['Shop_bank_withdrawal_min']) ? '' : '<strong>' . $txt['Shop_bank_withdrawal_min'] . ':</strong> ' . Format::cash($modSettings['Shop_bank_withdrawal_min']) . '<br />', '
			', empty($modSettings['Shop_bank_withdrawal_max']) ? '' : '<strong>' . $txt['Shop_bank_withdrawal_max'] . ':</strong> ' . Format::cash($modSettings['Shop_bank_withdrawal_max']) . '<br />', '
			', empty($modSettings['Shop_bank_deposit_min']) ? '' : '<strong>' . $txt['Shop_bank_deposit_min'] . ':</strong> ' . Format::cash($modSettings['Shop_bank_deposit_min']) . '<br />', '
			', empty($modSettings['Shop_bank_deposit_max']) ? '' : '<strong>' . $txt['Shop_bank_deposit_max'] . ':</strong> ' . Format::cash($modSettings['Shop_bank_deposit_max']) . '<br />', '
			<hr />
			<form method="post" action="', $scripturl,'?action=shop;sa=bank2">
				<div class="windowbg">
					', $context['bank']['message'], '
				</div>
				<dl class="settings">
					<dt>
						', $txt['Shop_bank_action'], '
					</dt>
					<dd>
						<input class="input_radio" type="radio" name="type" value="deposit" id="deposit" checked /> <label for="deposit">', $txt['Shop_bank_deposit'], '</label><br />
						<input class="input_radio" type="radio" name="type" value="withdraw" id="withdraw" /> <label for="withdraw">', $txt['Shop_bank_withdraw'], '</label>
					</dd>
					<dt>
						', $txt['Shop_bank_amount'], ':
					</dt>
					<dd>
						', Format::cash('<input type="number" min="1" name="amount" />'), '
					</dd>
				</dl>
				<input class="button floatright" type="submit" value="', $txt['go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_set_trade()
{
	global $context, $txt, $scripturl;

	echo '
	<div class="roundframe">
		<form method="post" action="', $scripturl,'?action=shop;sa=invtrade2;id=', $_REQUEST['id'], '">
			<input type="hidden" name="trade" value="', $_REQUEST['id'], '" />
			<dl class="settings">
				<dt>
					', Format::image($context['shop_item']['image']), $context['shop_item']['name'], '
				</dt>
				<dd>
					', sprintf($txt['Shop_inventory_purchased'], timeformat($context['shop_item']['date'])), ' 
				</dd>
				<dt>
					', $txt['Shop_trade_cost'], '<br />
					<span class="smalltext">', $txt['Shop_trade_cost_desc'], '</span>
				</dt>
				<dd>
					<input type="number" name="tradecost" id="tradecost" min="1" />
				</dd>
			</dl>
			<input class="button floatright" type="submit" value="', $txt['go'], '" />
			<input class="button floatright" type="button" value="', $txt['Shop_no_goback2'], '" onclick="window.location=\'', $scripturl, '?action=shop;sa=inventory\'" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		</form>
	</div>';
}

function template_trade_above()
{
	global $context;

	echo '
	<div class="title_bar">
		<h3 class="titlebg">
			', $context['page_welcome'], '
		</h3>
	</div>
	<div class="information">
		', $context['page_description'], '
	</div>';
}

function template_trade()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	/*foreach ($context['trade_stats'] as $block)
	{
		// Check if he has enough privileges to show him this information
		if (empty($block['enabled']))
			continue;

		echo '
			<div class="half_content">
				<div class="title_bar">
					<h4 class="titlebg">
						<img class="centericon" src="', $settings['default_images_url'], '/icons/shop/', $block['icon'], '" alt="" /> ', $block['label'], '
					</h4>
				</div>

					<dl class="stats" style="padding: 5px;">';

		foreach ($block['function'] as $item)
		{
			echo '
						<dt style="font-weight: normal;">
							', (!empty($item['image']) ? $item['image'].' &nbsp;'.$item['name'] : $item['link']), '
						</dt>
						<dd class="statsbar generic_bar righttext">';

				if (!empty($item['percent']))
					echo '
							<div class="bar" style="width: ', $item['percent'], '%;"></div>';
				else
					echo '
							<div class="bar empty"></div>';
			echo '
							<span>', $item['num'], '</span>
						</dd>';
		}

		echo '
					</dl>
			</div>';
	}*/

}

function template_trade_below()
{
	global $context, $txt, $scripturl;

	if (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'tradelist')
	{
		echo '
		<br class="clear" />
		<a id="searchuser"></a>
		<div class="title_bar">
			<h4 class="titlebg">
				'. $txt['Shop_inventory_search']. '
			</h4>
		</div>
		<div class="information">
			<form method="post" action="'. $scripturl.'?action=shop;sa=tradesearch">
				<dl class="settings">
					<dt>
						'. $txt['Shop_inventory_member_name']. '
					</dt>
					<dd>
						<input type="text" name="membername" id="membername" />
						<div id="membernameItemContainer"></div>
					</dd>
				</dl>
				<input class="button floatright" type="submit" value="'. $txt['search']. '" />
				<input type="hidden" name="'. $context['session_var']. '" value="'. $context['session_id']. '">
			</form>
		</div>
		<script>
			var oAddMemberSuggest = new smc_AutoSuggest({
				sSelf: \'oAddMemberSuggest\',
				sSessionId: \''. $context['session_id']. '\',
				sSessionVar: \''. $context['session_var']. '\',
				sSuggestId: \'to_suggest\',
				sControlId: \'membername\',
				sSearchType: \'member\',
				sPostName: \'memberid\',
				sURLMask: \'action=profile;u=%item_id%\',
				sTextDeleteItem: \''. $txt['autosuggest_delete_item']. '\',
				sItemListContainerId: \'membernameItemContainer\'
			});
		</script>';
	}
}


function template_games()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
	<div id="basicinfo" style="float: right; text-align: center;">
		<div class="title_bar">
			<h3 class="titlebg">
				', $context['user']['name'], '
			</h3>
		</div>
		<div class="information">
			<a href="', $scripturl, '?action=profile"><img class="avatar" style="display: inline" src="', $context['user']['avatar']['href'], '" alt="" /></a><br />
			<strong>', $txt['Shop_money_pocket'], ':</strong> ', Format::cash($context['user']['shopMoney']), '<br />
			<strong>', $txt['Shop_money_bank'], ':</strong> ', Format::cash($context['user']['shopBank']), '<br />
			<strong>', $txt['Shop_main_games'], ':</strong> ', $context['user']['gamedays'], $txt['Shop_games_days'], '
		</div>
	</div>
	<div id="detailedinfo" style="float: left;">
		<div class="title_bar">
			<h3 class="titlebg">
				', $txt['Shop_games_welcome'], '
			</h3>
		</div>
		<div class="information">
			', $context['page_description'], '
		</div>
		<div class="title_bar">
			<h4 class="titlebg">', $txt['Shop_games_list'], '</h4>
		</div>
		<div class="roundframe">';

	// The list of games!
	foreach ($context['shop']['games'] as $game => $type)
	{
		echo '
			<div class="windowbg">
				<img class="floatleft" src="', $type['icon'], '" style="border: 1px solid #333;" alt="', $type['name'], '" />
				&nbsp; <strong>', $type['name'], '</strong><br />
				&nbsp; <a href="', $scripturl, '?action=shop;sa=games;play=', $game, '">', $txt['Shop_games_playgame'], '</a>
			</div>';
	}

	echo '
		</div>
	</div>';
}

function template_games_play_above()
{
	global $txt, $context, $modSettings;

	echo '
	<div id="basicinfo">
		<div class="title_bar">
			<h3 class="titlebg">
				', $txt['Shop_games_payouts'], '
			</h3>
		</div>
		<div class="information">';

	// Playing slots or lucky2 or pairs or dice
	if (!empty($context['shop_game_spin'][0]))
	{
		// The payouts table
		foreach ($context['game']['faces'] as $face => $payout)
		{
			// No payout for this one
			if (empty($modSettings['Shop_settings_' . $_REQUEST['play'] . '_' . $payout]))
				continue;

			// Display the payout table for others
			if (!empty($context['shop_game_spin'][1]))
				echo str_repeat('<img src="'. $context['shop_game_images'] . $face . '.png" alt="" style="width: 25px; height: 25px; vertical-align: middle;" />', $context['shop_game_spin'][1]), '&nbsp; ', Format::cash($modSettings['Shop_settings_' . $_REQUEST['play'] . '_' . $payout]), '<br /><hr />';
			else
				echo $txt['Shop_games_' . $_REQUEST['play'] . '_'. $type], $modSettings['Shop_settings_' . $_REQUEST['play'] . '_' . $payout];

		}
	}

	echo '
		</div>
	</div>
	<div id="detailedinfo">
		<div class="title_bar">
			<h3 class="titlebg">
				', $context['game']['title'], '
			</h3>
		</div>
		<div class="information">
			', $context['page_description'], '
		</div>
		<div class="cat_bar">
			<h3 class="catbg">', $txt['Shop_games_letsplay'], $context['game']['title'], '</h3>
		</div>
		<div class="roundframe">';

}

function template_games_play()
{
	global $context, $txt, $scripturl, $modSettings;

	// Win/Lose message
	if (isset($_REQUEST['do']))
	{
		echo '
			<div class="' . (!empty($context['game_result'][0]) ? 'infobox' : 'errorbox') . '">
				' . $context['game_result'][1] . '
			</div>';


		// Type to set the wheels
		if (!empty($context['shop_game_spin'][0]))
		{
			echo '
				<img src="', $context['shop_game_images'], $context['shop_game']['wheel'][1], '.png" alt="" />';

			// Set next wheel
			if (isset($context['shop_game']['wheel'][2]))
			{
				echo '
					<img src="', $context['shop_game_images'], $context['shop_game']['wheel'][2], '.png" alt="" />';

				// Set the third wheel
				if (isset($context['shop_game']['wheel'][3]))
					echo '
						<img src="', $context['shop_game_images'], $context['shop_game']['wheel'][3], '.png" alt="" />';
			}
		}
	
		// Just print this if the user decided to play numberslots!
		elseif (!empty($context['shop_game_number']))
			echo '
				<span class="largetext" style="padding-left: 10px;">| ', $context['shop_game']['wheel'][1], ' | ', $context['shop_game']['wheel'][2], ' | ', $context['shop_game']['wheel'][3], ' |</span>';
	}

	echo '
			<br/>';
}

function template_games_play_below()
{
	global $scripturl, $context, $txt;

	echo'
			<br />
			<form method="post" action="',$scripturl,'?action=shop;sa=games;play=', $_REQUEST['play'], ';do">
				<input class="button floatleft" type="submit" value="', $txt['Shop_games_' . ((isset($_REQUEST['do']) && isset($_REQUEST['play'])) ? 'again' : 'spin')], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>	
			<br /><br />
			<div class="information">
				', $txt['Shop_games_youhave'], $context['user']['games']['real_money'],'
			</div>
		</div>
	</div>';
}


function template_shop_below()
{
	global $context, $modSettings;

	// Close the format
	echo '
		</div>';

	// Check for avoid errors
	if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_maintenance']) && allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
		return false;

	echo $context['shop']['copyright'];
}