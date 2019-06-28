<?php

/**
 * @package ST Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2018, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */


function template_Shop_mainHome()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	echo '
	<div class="roundframe flow_auto">
		<div id="basicinfo" style="float: right; text-align: center;">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['user']['name'], '
				</h3>
			</div>
			<div class="information">
				<a href="', $scripturl, '?action=profile"><img class="avatar" style="display: inline" src="', $context['user']['avatar']['href'], '" alt="" /></a><br />
				<strong>', $txt['Shop_money_pocket'], ':</strong> ', $modSettings['Shop_credits_prefix'], $context['user']['shopMoney'], ' ', $modSettings['Shop_credits_suffix'], '<br />
				<strong>', $txt['Shop_money_bank'], ':</strong> ', $modSettings['Shop_credits_prefix'], $context['user']['shopBank'], ' ', $modSettings['Shop_credits_suffix'], '<br />
				<strong>', $txt['Shop_shop_games'], ':</strong> ', $context['user']['gamedays'], $txt['Shop_games_daysleft'], '
			</div>
		</div>
		<div id="detailedinfo" style="float: left;">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['shop']['forum_welcome'], '
				</h3>
			</div>
			<div class="information">
				', $context['shop']['welcome'], '
			</div>';

	foreach ($context['home_stats'] as $block)
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
	}
			echo '
		</div>
	</div>';
}

function template_Shop_main_above()
{
	global $context, $txt, $scripturl, $boardurl, $modSettings;
	
		echo '
	<div class="roundframe">
		<div class="cat_bar">
			<h3 class="catbg">', $context['page_title'], '</h3>
		</div>';

	if (!empty($context['page_description']))
		echo '
		<div class="information">
			', $context['page_description'], '
		</div>';
}

function template_Shop_main_below(){
	echo '
	</div>';
}

function template_shop_stats()
{
	global $txt, $context, $settings;

	// Other type of stats for testing purposes
	foreach ($context['stats_blocks']['shop_i'] as $block)
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
							', $item['image'], ' &nbsp;', $item['name'], '
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

	// Common stats
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
							', $item['link'], '
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

function template_Shop_buyItem()
{
	global $context;

	echo '
		<div class="windowbg">
			', $context['shop']['item_bought'], '
		</div>';
}

function template_Shop_invTabs_above()
{
	global $context, $scripturl, $txt;

	echo '
		<div class="buttonlist floatleft">';
		
		foreach ($context['inventory_tabs'] as $action => $tab)
			echo '
				<a class="button', (isset($_REQUEST['sa']) ? (in_array($_REQUEST['sa'],$tab['action'])) ? ($_REQUEST['sa'] == 'inventory' && isset($_REQUEST['id']) ? '' : ' active') : ($_REQUEST['sa'] == 'inventory' && isset($_REQUEST['id']) ? ' active' : '') : (!isset($_REQUEST['sa']) && $_REQUEST['action'] == 'profile' && $action == 'inventory' ? ' active' : '')), '" href="' , $scripturl . '?action=shop;sa=', $tab['action'][0], '">', $tab['label'], '</a>';
	echo '
		</div>';
}

function template_Shop_invTabs_below(){}

function template_Shop_invSearch()
{
	global $context, $txt, $scripturl;

	echo '
		<div class="clear"></div>
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=shop;sa=search2">
				', $txt['Shop_inventory_member_name'], '
				&nbsp;<input class="input_text" type="text" name="membername" id="membername" />
				<div id="membernameItemContainer"></div>
				<span class="smalltext">', $txt['Shop_inventory_member_name_desc'], '</span>
				<br /><br />
				<input class="button_submit floatleft" type="submit" value="', $txt['search'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
		<script>
			var oAddMemberSuggest = new smc_AutoSuggest({
				sSelf: \'oAddMemberSuggest\',
				sSessionId: \'', $context['session_id'], '\',
				sSessionVar: \'', $context['session_var'], '\',
				sSuggestId: \'to_suggest\',
				sControlId: \'membername\',
				sSearchType: \'member\',
				sPostName: \'memberid\',
				sURLMask: \'action=profile;u=%item_id%\',
				sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
				sItemListContainerId: \'membernameItemContainer\'
			});
		</script>';
}

function template_Shop_invView()
{
	global $context, $txt, $scripturl, $boardurl, $settings;

	echo '
					<a href="'. $scripturl. '?action=shop;sa=profile;itemid='. $item['dist']. ';fav='. (($item['fav'] == 1) ? 0 : 1). ';'. $context['session_var'] .'='. $context['session_id'] .'">							
						<img src="'. $settings['default_images_url']. '/shop/'. (($item['fav'] == 1) ? 'star' : 'star-empty'). '.png" />
					</a>
				</td>
			</tr>';
}

function template_Shop_invTradeSet()
{
	global $context, $txt, $scripturl;

	echo '
		<div class="clear"></div>
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=shop;sa=invtrade2;id=', $_REQUEST['id'], '">
				<input type="hidden" name="trade" value="', $_REQUEST['id'], '" />
				', $txt['Shop_trade_cost'], '
				&nbsp;<input class="input_text" type="text" name="tradecost" id="tradecost" size="20" />
				<br />
				<span class="smalltext">', $txt['Shop_trade_cost_desc'], '</span>
				<br /><br />
				<input class="button_submit floatleft" type="submit" value="', $txt['Shop_item_trade_go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_Shop_invTradeItem()
{
	global $context;

	echo '
		<div class="windowbg">
			', $context['shop']['tradewhat'], '
		</div>';
}

function template_Shop_invUseitem()
{
	global $context, $txt, $scripturl, $item_info;

		echo '
		<div class="windowbg">
			<form method="post" action="', $scripturl,'?action=shop;sa=invused;id=', $_REQUEST['id'], '">
				', $txt['Shop_inventory_use_confirm'], '<br /><br />
				', $context['shop']['use']['input'], '<br />
				<input class="button_submit floatleft" type="submit" value="', $txt['Shop_item_useit'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_Shop_invUsed()
{
	global $context, $txt, $item_info;

		echo '
		<div class="windowbg">
			', sprintf($txt['Shop_item_used_success'], $context['shop']['use']['name']), '<br /><br />
			', $context['shop']['used']['input'], '<br />
		</div>';
}

function template_Shop_mainBank()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
		<div class="windowbg">';

		if (isset($_REQUEST['deposit']) || isset($_REQUEST['withdraw']))
		echo '
			<div class="infobox">', $context['Shop']['bank']['success'], '</div>';

		echo '
			', empty($context['bank']['wdFee']) ? '' : $context['bank']['wdFee'] . '<br />', '
			', empty($context['bank']['dpFee']) ? '' : $context['bank']['dpFee'] . '<br />', '
			', empty($context['bank']['wdMin']) ? '' : $context['bank']['wdMin'] . '<br />', '
			', empty($context['bank']['wdMax']) ? '' : $context['bank']['wdMax'] . '<br />', '
			', empty($context['bank']['dpMin']) ? '' : $context['bank']['dpMin'] . '<br />', '
			', empty($context['bank']['dpMax']) ? '' : $context['bank']['dpMax'] . '<br />', '
			<hr />
			<form method="post" action="', $scripturl,'?action=shop;sa=bank2">
				', $context['bank']['message'], '<br /><br />
				
				<input class="input_radio" type="radio" name="type" value="deposit" id="deposit" checked />
				<label for="deposit">', $txt['Shop_bank_deposit'], '</label>

				<input class="input_radio" type="radio" name="type" value="withdraw" id="withdraw" />
				<label for="withdraw">', $txt['Shop_bank_withdraw'], '</label><br /><br />

				', $txt['Shop_bank_amount'], ':&nbsp;
				', $modSettings['Shop_credits_prefix'], '&nbsp;<input class="input_text" type="number" min="1" name="amount" size="10" />&nbsp;', $modSettings['Shop_credits_suffix'], '

				<br /><br />
				<input class="button_submit floatleft" type="submit" value="', $txt['go'], '" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>';
}

function template_Shop_giftTabs_above()
{
	global $context, $scripturl;

	echo '
		<div class="buttonlist floatleft">';
		
		foreach ($context['gift_tabs'] as $action => $tab)
			echo '
				<a class="button', (in_array($_REQUEST['sa'],$tab['action'])) ? ' active' : '', '" href="' , $scripturl . '?action=shop;sa=', $tab['action'][0], '">', $tab['label'], '</a>';

	echo '
		</div>
		<div class="clear"></div>';
}

function template_Shop_giftTabs_below(){}

function template_Shop_mainGift()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
		<div class="windowbg">
		
			<form method="post" action="', $scripturl,'?action=shop;sa=gift2', (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'sendmoney' ? ';money' : ''), '">

				', $txt['Shop_inventory_member_name'], '
				&nbsp;<input type="text" name="membername" id="membername" value="', (isset($_REQUEST['membername']) ? $_REQUEST['membername'] : ''), '" />
				<div id="membernameItemContainer"></div>
				<span class="smalltext">', $txt['Shop_gift_member_find'], '</span>
				<br /><br />';

			// If we are sending money, we need a different input
			if (isset($_REQUEST['sa']) && ($_REQUEST['sa'] == 'sendmoney'))
			{
				echo '
				', $txt['Shop_gift_amount'], ':&nbsp;
				', $modSettings['Shop_credits_prefix'], '&nbsp;<input class="input_text" min="1" type="number" name="amount" size="10" />&nbsp;', $modSettings['Shop_credits_suffix'], '';
			}
			// Sending an item then...
			else
			{
				if (!empty($context['shop_user_items_list']) && !empty($context['shop']['view_inventory'])) {
					echo '
					', $txt['Shop_gift_item_select'], ':&nbsp;
					<select name="item" id="item">
						<optgroup label="', $txt['Shop_gift_item_select'], '">';
						// List the categories
						foreach ($context['shop_user_items_list'] as $item)
						echo '
							<option value="', $item['id'], '">', $item['name'], '</option>';
					echo '
						</optgroup>
					</select>';
				}
				elseif (empty($context['shop']['view_inventory']))
					echo '
					<strong>', $txt['cannot_shop_viewInventory'], '</strong>';
				else
					echo '
					<strong>', $txt['Shop_gift_no_items'], '</strong>';
			}
			echo '
				<br /><br />
				', $txt['Shop_gift_message'], ':&nbsp;
				<textarea name="message" id="message" cols="35" rows="2"></textarea>';

			if ((isset($_REQUEST['sa']) && ($_REQUEST['sa'] != 'sendmoney')) && !empty($context['shop_user_items_list']) || (isset($_REQUEST['sa']) && ($_REQUEST['sa'] == 'sendmoney')))
				echo '
				<br />
				<input class="button_submit floatleft" type="submit" value="', ($_REQUEST['sa'] == 'sendmoney') ? $context['shop']['send_money'] : $txt['Shop_gift_send_item'], '" />';
	echo '
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
			</form>
		</div>
		<script>
			var oAddMemberSuggest = new smc_AutoSuggest({
				sSelf: \'oAddMemberSuggest\',
				sSessionId: \'', $context['session_id'], '\',
				sSessionVar: \'', $context['session_var'], '\',
				sSuggestId: \'to_suggest\',
				sControlId: \'membername\',
				sSearchType: \'member\',
				sPostName: \'memberid\',
				sURLMask: \'action=profile;u=%item_id%\',
				sTextDeleteItem: \'', $txt['autosuggest_delete_item'], '\',
				sItemListContainerId: \'membernameItemContainer\'
			});
		</script>';
}

function template_Shop_giftSent()
{
	global $context;

	echo '
	<div class="windowbg">
		', $context['shop']['gift_sent'], '
	</div>';
}


function template_Shop_mainTrade()
{
	global $context, $txt, $scripturl, $modSettings, $settings;

	echo '
	<div class="clear"></div>
		<div class="windowbg">';

	foreach ($context['trade_stats'] as $block)
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
	}

	echo '
		</div>';
}

function template_Shop_mainTrade_above()
{
	global $context, $scripturl, $txt;

	echo '
	<div class="buttonlist floatleft">';
		
	foreach ($context['trade_tabs'] as $action => $tab)
	{
		if (!isset($tab['action']))
		{
			echo '
			<a class="button" href="', $tab['link'], '">', $tab['label'], '</a>';
			continue;
		}
		else
		echo '
			<a class="button', (in_array($_REQUEST['sa'],$tab['action'])) ? ' active' : '', '" href="' , $scripturl . '?action=shop;sa=', $tab['action'][0], '">', $tab['label'], '</a>';
	}
	echo '
	</div>';
}

function template_Shop_mainTrade_below(){}

function template_Shop_mainGames()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
	<div class="roundframe flow_auto">
		<div id="basicinfo" style="float: right; text-align: center;">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['user']['name'], '
				</h3>
			</div>
			<div class="information">
				<a href="', $scripturl, '?action=profile"><img class="avatar" style="display: inline" src="', $context['user']['avatar']['href'], '" alt="" /></a><br />
				<strong>', $txt['Shop_money_pocket'], ':</strong> ', $modSettings['Shop_credits_prefix'], $context['user']['shopMoney'], ' ', $modSettings['Shop_credits_suffix'], '<br />
				<strong>', $txt['Shop_money_bank'], ':</strong> ', $modSettings['Shop_credits_prefix'], $context['user']['shopBank'], ' ', $modSettings['Shop_credits_suffix'], '<br />
				<strong>', $txt['Shop_shop_games'], ':</strong> ', $context['user']['gamedays'], $txt['Shop_games_daysleft'], '
			</div>
		</div>
		<div id="detailedinfo" style="float: left;">
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['Shop_games_welcome'], '
				</h3>
			</div>
			<div class="information">
				', $context['page_description'], '
			</div>
			<div class="title_bar">
				<h4 class="titlebg">', $txt['Shop_games_listof'], '</h4>
			</div>';

	// The list of games!
	foreach ($context['shop']['games'] as $game => $type)
	{
		echo '
			<div class="windowbg stripes">
				<img class="floatleft" src="', $type['src'], '" style="border: 1px solid #333;" alt="', $type['name'], '" />
				&nbsp; <strong>', $type['name'], '</strong><br />
				&nbsp; <a href="', $scripturl, '?action=shop;sa=games;play=', $game, '">', $txt['Shop_games_playgame'], '</a>
			</div>';
	}

	echo '
		</div>
	</div>';
}

function template_Shop_gamesPlay_above()
{
	global $txt, $context, $modSettings;

	echo '
	<div class="roundframe flow_auto">
		<div id="basicinfo" style="float: right">
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['Shop_games_payouts'], '
				</h3>
			</div>
			<div class="information">';

	// Playing slots or lucky2 or pairs or dice
	if (isset($_REQUEST['play']) && ($_REQUEST['play'] == 'slots' || $_REQUEST['play'] == 'lucky2' || $_REQUEST['play'] == 'pairs' || $_REQUEST['play'] == 'dice'))
	{
		// The payouts table
		foreach ($context['game']['faces'] as $face => $payout)
		{
			if ($payout == 0)
				continue;

			// Slots
			if ($_REQUEST['play'] == 'slots')
				$repeat = 3;
			// Lucky2
			elseif ($_REQUEST['play'] == 'luck2')
				$repeat = 1;
			// Pairs and Dice
			elseif ($_REQUEST['play'] == 'pairs' || $_REQUEST['play'] == 'dice')
				$repeat = 2;
			// By default 1
			else
				$repeat = 1;

			// Display the payout table
			echo str_repeat('<img src="'. $context['game_images']['src'] . $face . '.png" alt="" style="'. (($_REQUEST['play'] == 'pairs') ? 'width: 55px; height: 65px;' : 'width: 25px; height: 25px;'). ' vertical-align: middle;" />', $repeat), '&nbsp; ', $modSettings['Shop_credits_prefix'], $payout, ' ', $modSettings['Shop_credits_suffix'], '<br /><hr />';
		}
	}

	// Playing numberslots
	if (isset($_REQUEST['play']) && ($_REQUEST['play'] == 'number'))
	{
		// The payouts table
		foreach ($context['game']['payout'] as $type => $payout)
		{
			if ($payout == 0)
				continue;
			echo '
				', $txt['Shop_games_number_'. $type], $payout, '
				<hr />
			';
		}
	}

	echo '
			</div>
		</div>
		<div id="detailedinfo" style="float: left;">';

}

function template_Shop_gamesPlay()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">
				', $context['game']['title'], '
			</h3>
		</div>
		<div class="information">
			', $context['page_description'], '
		</div>
		<div class="title_bar">
			<h4 class="titlebg">', $txt['Shop_games_letsplay'], $context['game']['title'], '</h4>
		</div>
		<div class="windowbg">';

	// Win/Lose message
	if (isset($_REQUEST['do']))
		// Winner
		if (isset($context['win']))
			echo '
				<div class="infobox">' . $context['win'] . '</div>';
		// Loser
		else
			echo '
				<div class="errorbox">' . $context['nowin'] . '</div>';

	// Type to set the wheels
	if (isset($_REQUEST['do']) && ($_REQUEST['play'] == 'slots' || $_REQUEST['play'] == 'lucky2' || $_REQUEST['play'] == 'pairs' || $_REQUEST['play'] == 'dice'))
	{
		echo '
			<img src="', $context['game_images']['src'], $context['game']['wheel1'], '.png" alt="" />';

		// Set next wheel
		if (isset($_REQUEST['do']) && $_REQUEST['play'] != 'lucky2')
		{
			echo '
			<img src="', $context['game_images']['src'], $context['game']['wheel2'], '.png" alt="" />';

			// Set the third wheel
			if (isset($_REQUEST['do']) && ($_REQUEST['play'] != 'pairs' && $_REQUEST['play'] != 'dice'))
				echo '
			<img src="', $context['game_images']['src'], $context['game']['wheel3'], '.png" alt="" />';
		}
	}
	// Just print this if the user decided to play numberslots!
	elseif (isset($_REQUEST['do']) && $_REQUEST['play'] == 'number')
		echo '
			<span class="largetext" style="padding-left: 10px;">| ', $context['game']['wheel1'], ' | ', $context['game']['wheel2'], ' | ', $context['game']['wheel3'], ' |</span>';

	echo '
			<br/>';
}

function template_Shop_gamesPlay_below()
{
	global $scripturl, $context, $txt;

	echo'
				<br />
				<form method="post" action="',$scripturl,'?action=shop;sa=games;play=', $_REQUEST['play'], ';do">
					<input class="button_submit floatleft" type="submit" value="', ((isset($_REQUEST['do']) && isset($_REQUEST['play'])) ? $txt['Shop_games_again'] : $context['spin']), '" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
				</form>	
				<br /><br />
				<div class="windowbg stripes">
					', $txt['Shop_games_youhave'], $context['user']['games']['real_money'],'
				</div>
			</div>
		</div>
	</div>';
}

function template_Shop_displayInventory($shop_inventory)
{
	global $txt, $modSettings, $scripturl;

	$title = sprintf($txt['Shop_inventory_viewing_who'], $shop_inventory['user']);
	$inventory = (($modSettings['Shop_inventory_placement'] == 0) ? '' : $txt['Shop_posting_inventory']. ':');

	// Profile title
	if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'profile'))
		$inventory .= '<strong>'. $txt['Shop_posting_inventory']. '</strong>:<br />';
	else
		$inventory .= '<br />';

	// Bring the items!
	foreach ($shop_inventory as $item)
	{
		if (isset($item['image']))
			$inventory .= $item['image'];
	}

	$inventory .= '<br /><a href="'. $scripturl. '?action=shop;sa=userinv;id='. $shop_inventory['userid']. '" onclick="return reqOverlayDiv(this.href, \''. $shop_inventory['user']. '\', \'icons/shop/top_inventories.png\');">'. $txt['Shop_posting_inventory_all']. '</a>';

	return $inventory;

}

function template_Shop_invBasic()
{
	global $context, $boardurl, $settings, $modSettings, $txt;

	echo '
<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
	<head>
		<meta charset="', $context['character_set'], '">
		<meta name="robots" content="noindex">
		<title>', $context['page_title'], '</title>
		<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css', $modSettings['browser_cache'] ,'">
		<script src="', $settings['default_theme_url'], '/scripts/script.js', $modSettings['browser_cache'] ,'"></script>
	</head>
	<body id="shop_inventory_popup">
		<div class="windowbg">';

	// We're going to list all the items...
	foreach ($context['shop_items_list'] as $item)
		if (isset($item['image']))
			echo $item['image'];

	echo '
			<br class="clear">
			<a href="javascript:self.close();">', $txt['close_window'], '</a>
		</div>
	</body>
</html>';
}

function template_Shop_above()
{
	global $context, $scripturl, $modSettings, $txt;

	// Check for avoid errors
	if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_maintenance']) && allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
		return false;

	echo '
		<div class="buttonlist floatright" style="margin-bottom: -5px;">';
		
		foreach ($context['shop_links'] as $action => $tab)
		{
			if ((!allowedTo($tab['permission']) && allowedTo('shop_canManage')) || (allowedTo($tab['permission'])) && !empty($modSettings[$tab['enable']]))
			echo '
				<a class="button', (isset($_REQUEST['sa']) && in_array($_REQUEST['sa'],$tab['action'])) ? ' active' : (!isset($_REQUEST['sa']) && $action == 'home' ? ' active' : ''), '" href="' , $scripturl . '?action=shop;sa=', $action, '">', $tab['label'], '</a>';
		}

	echo '
		</div>
		<div class="clear"></div>';

	if (!empty($modSettings['Shop_enable_maintenance']))
		echo '
		<div id="errors" class="errorbox" style="margin-top: 5px;">
			<dl>
				<dt>
					<strong id="error_serious">', $txt['Shop_currently_maintenance_warn'], '</strong>
				</dt>
				<dd id="error_list" class="error">
					', $txt['Shop_currently_maintenance_warn_desc'], '
				</dd>
			</dl>
		</div>';
}

function template_Shop_below()
{
	global $context, $modSettings;

	// Check for avoid errors
	if (!empty($modSettings['Shop_enable_shop']) && !empty($modSettings['Shop_enable_maintenance']) && allowedTo('shop_canAccess') && !allowedTo('shop_canManage'))
		return false;

	echo $context['shop']['copyright'];
}