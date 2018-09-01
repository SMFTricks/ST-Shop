<?php

/**
 * @package SA Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

if (!defined('SMF'))
	die('No direct access...');

function Shop_mainBank()
{
	global $context, $scripturl, $modSettings, $user_info, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_bank']))
		fatal_error($txt['Shop_currently_disabled_bank'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canBank');

	// Set all the page stuff
	$context['page_title'] = $txt['Shop_main_button'] . ' - ' . $txt['Shop_shop_bank'];
	$context['template_layers'][] = 'Shop_main';
	$context['sub_template'] = 'Shop_mainBank';
	$context['page_description'] = sprintf($txt['Shop_bank_desc'], $modSettings['Shop_credits_suffix'], $modSettings['Shop_bank_interest']);
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=shop;sa=bank',
		'name' => $txt['Shop_shop_bank'],
	);

	// Withdrawal fee
	$context['bank']['wdFee'] = ($modSettings['Shop_bank_withdrawal_fee'] != 0 ? '<strong>' . $txt['Shop_bank_withdrawal_fee'] . ':</strong> ' . Shop::formatCash($modSettings['Shop_bank_withdrawal_fee']) : '');
	// Deposit fee
	$context['bank']['dpFee'] = ($modSettings['Shop_bank_deposit_fee'] != 0 ? '<strong>' . $txt['Shop_bank_deposit_fee'] . ':</strong> ' . Shop::formatCash($modSettings['Shop_bank_deposit_fee']) : '');
	// Minimum withdrawal
	$context['bank']['wdMin'] = ($modSettings['Shop_bank_withdrawal_min'] != 0 ? '<strong>' . $txt['Shop_bank_withdrawal_min'] . ':</strong> ' . Shop::formatCash($modSettings['Shop_bank_withdrawal_min']) : '');
	// Maximum withdrawal
	$context['bank']['wdMax'] = ($modSettings['Shop_bank_withdrawal_max'] != 0 ? '<strong>' . $txt['Shop_bank_withdrawal_max'] . ':</strong> ' . Shop::formatCash($modSettings['Shop_bank_withdrawal_max']) : '');
	// Minnimum deposit
	$context['bank']['dpMin'] = ($modSettings['Shop_bank_deposit_min'] != 0 ? '<strong>' . $txt['Shop_bank_deposit_min'] . ':</strong> ' . Shop::formatCash($modSettings['Shop_bank_deposit_min']) : '');
	// Maximum deposit
	$context['bank']['dpMax'] = ($modSettings['Shop_bank_deposit_max'] != 0 ? '<strong>' . $txt['Shop_bank_deposit_max'] . ':</strong> ' . Shop::formatCash($modSettings['Shop_bank_deposit_max']) : '');
	// Just a happy message... How many credits do you have and what would you like to do?
	$context['bank']['message'] = sprintf($txt['Shop_bank_youhave'], $modSettings['Shop_credits_prefix'], $user_info['shopMoney'], $modSettings['Shop_credits_suffix'], $user_info['shopBank']);

	// Deposit
	if (isset($_REQUEST['deposit']))
		$context['Shop']['bank']['success'] = $txt['Shop_bank_deposit_successfull'];
	// Withdraw
	elseif (isset($_REQUEST['withdraw']))
		$context['Shop']['bank']['success'] = $txt['Shop_bank_withdraw_successfull'];
}

function Shop_bankTrans()
{
	global $context, $smcFunc, $user_info, $modSettings, $txt;

	// What if the Inventories are disabled?
	if (empty($modSettings['Shop_enable_bank']))
		fatal_error($txt['Shop_currently_disabled_bank'], false);

	// Check if he is allowed to access this section
	if (!allowedTo('shop_canManage'))
		isAllowedTo('shop_canBank');

	// Check session
	checkSession();

	$amount = (int) $_REQUEST['amount'];

	// You cannot leave empty the amount field
	if (empty($amount) || ($amount <= 0))
		fatal_error($txt['Shop_bank_noamount_not_negative'], false);

	// No type? Something must be wrong
	elseif (!empty($amount) && !isset($_REQUEST['type']))
		fatal_error($txt['Shop_bank_notype'], false);

	// Deposit
	elseif (!empty($amount) && $_REQUEST['type'] == 'deposit')
	{
		// Check if we have an appropiate amount according with the max-min deposit
		if (!empty($modSettings['Shop_bank_deposit_min']) || !empty($modSettings['Shop_bank_deposit_max']))
		{	
			// Max and Min deposit
			if ((!empty($modSettings['Shop_bank_deposit_min']) && !empty($modSettings['Shop_bank_deposit_max'])) && (($amount < $modSettings['Shop_bank_deposit_min']) ||  ($amount > $modSettings['Shop_bank_deposit_max'])))
				fatal_error($txt['Shop_bank_notbt_deposit'], false);
			// Just making sure...
			// Min deposit
			elseif ((!empty($modSettings['Shop_bank_deposit_min']) && empty($modSettings['Shop_bank_deposit_max'])) && ($amount < $modSettings['Shop_bank_deposit_min']))
				fatal_lang_error('Shop_bank_notmin_deposit', false, array($modSettings['Shop_bank_deposit_min']));
			// Max deposit
			elseif ((empty($modSettings['Shop_bank_deposit_min']) && !empty($modSettings['Shop_bank_deposit_max'])) && ($amount > $modSettings['Shop_bank_deposit_max']))
				fatal_lang_error('Shop_bank_notmax_deposit', false, array($modSettings['Shop_bank_deposit_max']));
		}
		// Fee enabled? Let's see if he can afford both
		if (!empty($modSettings['Shop_bank_deposit_fee']))
			$deposit = (int) (($user_info['shopMoney'] - $amount) - $modSettings['Shop_bank_deposit_fee']);
		else
			$deposit = (int) ($user_info['shopMoney'] - $amount);

		// Okay, we want to check if he actually has enough in his pocket and also check for the fee
		if ($deposit < 0 && ($user_info['shopBank']) < $modSettings['Shop_bank_deposit_fee'])
			fatal_lang_error('Shop_bank_notenough_pocket', false, array($modSettings['Shop_credits_suffix']));
		// Alright, so he has enough for the deposit then
		elseif ($deposit >= 0 || $user_info['shopBank'] >= $modSettings['Shop_bank_deposit_fee'])
		{
			// Fee from his pocket
			if ($deposit >= 0)
				$dtype = 0;
			// Fee from his bank
			else
				$dtype = 1;

			// Add the amount to the user's bank
			Shop_logBank($user_info['id'], $amount, $modSettings['Shop_bank_deposit_fee'], $dtype);
			// Send the user to the next page
			redirectexit('action=shop;sa=bank;deposit');
		}
	}
	// Withdraw
	elseif (!empty($amount) && $_REQUEST['type'] == 'withdraw')
	{
		// Does the user has enough money to pay the withdrawal fee?
		$checkmoney = (int) ($user_info['shopMoney'] - $modSettings['Shop_bank_withdrawal_fee']);

		// Check if we have an appropiate amount according with the max-min withdraw
		if (!empty($modSettings['Shop_bank_withdrawal_min']) || !empty($modSettings['Shop_bank_withdrawal_max']))
		{	
			// Max and Min withdraw
			if ((!empty($modSettings['Shop_bank_withdrawal_min']) && !empty($modSettings['Shop_bank_withdrawal_max'])) && (($amount < $modSettings['Shop_bank_withdrawal_min']) ||  ($amount > $modSettings['Shop_bank_withdrawal_max'])))
				fatal_error($txt['Shop_bank_notbt_withdrawal'], false);
			// Min withdraw
			elseif ((!empty($modSettings['Shop_bank_withdrawal_min']) && empty($modSettings['Shop_bank_withdrawal_max'])) && ($amount < $modSettings['Shop_bank_withdrawal_min']))
				fatal_lang_error('Shop_bank_notmin_withdrawal', false, array($modSettings['Shop_bank_withdrawal_min']));
			// Max withdraw
			elseif ((empty($modSettings['Shop_bank_withdrawal_min']) && !empty($modSettings['Shop_bank_withdrawal_max'])) && ($amount > $modSettings['Shop_bank_withdrawal_max']))
				fatal_lang_error('Shop_bank_notmax_withdrawal', array($modSettings['Shop_bank_withdrawal_max']));
		}
		// Check if fee enabled
		if (!empty($modSettings['Shop_bank_withdrawal_fee']) && ($checkmoney < 0))
			$withdraw = (int) (($user_info['shopBank'] - $amount) - $modSettings['Shop_bank_withdrawal_fee']);
		else
			$withdraw = (int) ($user_info['shopBank'] - $amount);

		// Okay, let's see if we got enough money...
		if ($withdraw < 0)
			fatal_lang_error('Shop_bank_notenough_bank', false, array($modSettings['Shop_credits_suffix']));
		// Go ahead with the withdraw
		elseif ($withdraw >= 0)
		{
			if (!empty($modSettings['Shop_bank_withdrawal_fee']) && ($checkmoney < 0))
				$wtype = 3;
			else
				$wtype = 2;

			// Add the amount to the user's pocket
			Shop_logBank($user_info['id'], $amount, $modSettings['Shop_bank_withdrawal_fee'], $wtype);
			// Send the user to the next page
			redirectexit('action=shop;sa=bank;withdraw');
		}
	}
}