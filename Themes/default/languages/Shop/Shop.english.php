<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global $scripturl, $modSettings;

// Main
$txt['Shop'] = 'Shop';
$txt['Shop_main_button'] = 'Shop';
$txt['Shop_main_home'] = 'Shop - Home';
$txt['Shop_admin_button'] = 'Shop Admin';
$txt['Shop_main_home'] = 'Home';
$txt['Shop_main_buy'] = 'Buy Items';
$txt['Shop_main_inventory'] = 'Inventory';
$txt['Shop_main_stats'] = 'Stats';
$txt['Shop_main_gift'] = 'Send A Gift';
$txt['Shop_main_bank'] = 'Bank';
$txt['Shop_main_trade'] = 'Trade Center';
$txt['Shop_main_games'] = 'Games Room';
$txt['Shop_main_yourinventory'] = 'Your Inventory';

// Shop Home
$txt['Shop_user_info'] = 'User information';
$txt['Shop_welcome_to'] = '%s Shop';
$txt['Shop_welcome_text'] = '<strong>Welcome to the Shop, %1$s.</strong><br /> Here you can buy items with the %2$s you get from posting on the forum, trade items at the trade center, put your money save in the bank, browse your inventory and other users inventory.';
$txt['Shop_money_pocket'] = 'Pocket';
$txt['Shop_money_bank'] = 'Bank';
$txt['Shop_user_avatar'] = 'Avatar';
$txt['Shop_user_name'] = 'Member';
$txt['Shop_user_count'] = 'Count';

// Notifications and alerts
$txt['alert_group_shop'] = 'Shop alerts';
$txt['alert_shop_usercredits'] = 'When I receive ' . (!empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits') . ' from someone';
$txt['alert_shop_useritems'] = 'When I receive a shop gift from someone';
$txt['alert_shop_usertraded'] = 'When someone purchased an item from my trades list';
$txt['alert_shop_usermodule_steal'] = 'When someone steals from my pocket';
$txt['alert_shop_credits'] = '{member_link} has sent you {amount}';
$txt['alert_shop_items'] = '{member_link} has sent you a gift';
$txt['alert_shop_traded'] = '{member_link} has purchased your item {item_name} listed on the Trade Center';
$txt['Shop_notification_sold_from'] = 'Forum Shop';

// Shop buy
$txt['Shop_buy_date'] = 'Newest';
$txt['Shop_buy_something'] = 'Please choose something to buy!';
$txt['Shop_buy_soldout'] = '<strong>Sold out!</strong>';
$txt['Shop_buy_purchased'] = 'You have successfully purchased the selected item. <br />You now have %s in your pocket.';
$txt['Shop_buy_item_who'] = 'Who owns \'%s\'';
$txt['Shop_buy_item_who_nobody'] = 'Nobody owns the item \'%s\' at the moment.';
$txt['Shop_buy_item_who_this'] = 'Who owns this item';
$txt['Shop_buy_notenough'] = 'You don\'t have<br/>enough ' . (!empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits');
$txt['Shop_whohas_desc'] = 'On this page you can see a list of every user that own the item <i>%s</i> and how many they have.';

// Posting
$txt['Shop_posting_credits_pocket'] = !empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits';
$txt['Shop_posting_credits_bank'] = 'Bank ' . (!empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits');
$txt['Shop_posting_credits_pocket2'] = !empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits' . ' in pocket';
$txt['Shop_posting_credits_bank2'] = !empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits' . ' in bank';
$txt['Shop_posting_inventory'] = 'Shop inventory';
$txt['Shop_posting_inventory_all'] = 'View All';

// Items
$txt['Shop_item_image'] = 'Item image';
$txt['Shop_item_image_select'] = 'Select image';
$txt['Shop_item_name'] = 'Name';
$txt['Shop_item_id'] = 'Item ID';
$txt['Shop_item_member'] = 'Member';
$txt['Shop_items_sort'] = 'Sort by:';
$txt['Shop_item_description'] = 'Description';
$txt['Shop_item_stock'] = 'Stock';
$txt['Shop_item_limit'] = 'Limit';
$txt['Shop_item_price'] = 'Price';
$txt['Shop_item_free'] = 'Free';
$txt['Shop_item_not_usable'] = 'This item is not usable';
$txt['Shop_item_category'] = 'Category';
$txt['Shop_item_details'] = 'Details';
$txt['Shop_item_category_select'] = 'Select the category';
$txt['Shop_item_ascending'] = 'Ascending';
$txt['Shop_item_descending'] = 'Descending';
$txt['Shop_item_uncategorized'] = 'Uncategorized';
$txt['Shop_item_buy'] = 'Buy item';
$txt['Shop_item_trade'] = 'Trade';
$txt['Shop_item_trade_go'] = 'Trade item';
$txt['Shop_item_trade_desc'] = 'If you are sure that you want to put this item for sale on the trade center, set the price below.';
$txt['Shop_item_useit'] = 'Use item';
$txt['Shop_item_using'] = 'Using the item %s';
$txt['Shop_item_use'] = 'Usable';
$txt['Shop_item_notusable'] = 'Not usable';
$txt['Shop_item_used_success'] = 'The item %s was successfully used.';

// Categories
$txt['Shop_categories'] = 'Categories';
$txt['Shop_categories_all'] = 'All categories';
$txt['Shop_category_image'] = 'Category Image';

// Inventory
$txt['Shop_inventory_view'] = 'View inventory';
$txt['Shop_inventory_use_item'] = 'Use the %s item';
$txt['Shop_inventory_use_confirm'] = 'You are about to use this item. If there\'s anything else to fill, do it below, and then click the use button.';
$txt['Shop_item_fav'] = 'Fav';
$txt['Shop_inventory_hide'] = 'Hide my inventory on posts and profile';
$txt['Shop_inventory_search'] = 'Search users';
$txt['Shop_inventory_search_i'] = 'Search inventory';
$txt['Shop_inventory_myinventory'] = 'My inventory';
$txt['Shop_inventory_member_name'] = 'Member name:';
$txt['Shop_inventory_member_desc'] = 'Type the name of the user you want to search.';
$txt['Shop_inventory_member_find'] = 'Find members';
$txt['Shop_inventory_viewing_who'] = 'Viewing %s\'s inventory';
$txt['Shop_item_date'] = 'Date';
$txt['Shop_item_traded'] = 'The item was successfully added to the Trade Center.<br /> You will receive a personal message when someone buys it. You can also remove it from the Trade Center whenever you want.';
$txt['Shop_inventory_purchased'] = 'Purchased on: %s';

// Bank
$txt['Shop_bank_welcome'] = 'Welcome to the Shop Bank';
$txt['Shop_bank_desc'] = 'Use the bank to safely store your %1$s and avoid losing them. All %1$s stored in the bank, gain or lose an interest at a rate of %2$d%3% per day.';
$txt['Shop_bank_youhave'] = 'You currently have %1$s in your pocket and %2$s in the bank.';
$txt['Shop_bank_action'] = 'Would you like to deposit or withdraw?';
$txt['Shop_bank_deposit'] = 'Deposit';
$txt['Shop_bank_withdraw'] = 'Widthdraw';
$txt['Shop_bank_amount'] = 'Amount';
$txt['Shop_bank_notype'] = 'You need to choice a type of transaction.';
$txt['Shop_bank_noamount_not_negative'] = 'You need to set an amount to deposit or withdraw and it cannot be negative';
$txt['Shop_bank_notenough_pocket'] = 'You don\'t have enough %s in your pocket.';
$txt['Shop_bank_notbt_deposit'] = 'Your amount has to be between the minimum deposit and the maximum deposit.';
$txt['Shop_bank_notmin_deposit'] = 'Your amount has to be a minimum deposit of %d';
$txt['Shop_bank_notmax_deposit'] = 'Your amount has to be a maximum deposit of %d';
$txt['Shop_bank_deposit_successfull'] = 'The deposit was successful.';
$txt['Shop_bank_notenough_bank'] = 'You don\'t have enough %s in the bank.';
$txt['Shop_bank_notbt_withdrawal'] = 'Your amount has to be between the minimum withdrawal and the maximum withdrawal.';
$txt['Shop_bank_notmin_withdrawal'] = 'Your amount has to be a minimum withdrawal of %d';
$txt['Shop_bank_notmax_withdrawal'] = 'Your amount has to be a maximum withdrawal of %d';
$txt['Shop_bank_withdraw_successfull'] = 'The withdrawal was successful.';

// Trade center
$txt['Shop_trade_main'] = 'Trade center';
$txt['Shop_trade_welcome'] = 'Welcome to the Trade center';
$txt['Shop_trade_list'] = 'Trade list';
$txt['Shop_trade_list_desc'] = 'Here you will find every item that users have put for sale in the Trade Center.<br>If you want to find items from a specific user you can use the search tool located below.';
$txt['Shop_trade_desc'] = '<strong>Welcome to the Trade Center, %1$s.</strong><br /> Here you can trade and buy items from other users, check on your current listed items and find out more about the more active users in terms of sales, profit and purchases.';
$txt['Shop_trade_remove_item'] = 'Remove from Trade';
$txt['Shop_trade_removed'] = 'Your item was succesfully removed from the trade center.<br> You can now find it back in your inventory.';
$txt['Shop_trade_notification_sold_subject'] = 'Your item on trade was sold successfully.';
$txt['Shop_trade_notification_sold_message1'] = 'Congratulations!' . "\n" . '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has purchased your item \'%3$s\' for [i]%4$s[/i]. All the %5$s from the purchase have been added to your pocket.' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
$txt['Shop_trade_notification_sold_message2'] = 'Congratulations!' . "\n" . '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has purchased your item \'%3$s\' for [i]%4$s[/i], but has been taken away a fee of [i]%5$s[/i]. The other [i]%6$s[/i] from the purchase have been added to your pocket.' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
$txt['Shop_trade_myprofile'] = 'My items';
$txt['Shop_trade_myprofile_desc'] = 'This is the list of items <strong>you</strong> currently have in the Trade Center. You can remove them also if you want.';
$txt['Shop_trade_profile'] = 'Items from %s';
$txt['Shop_trade_profile_desc'] = 'You are currently viewing the list of <strong>%s\'s</strong> for the items this user has in the Trade Center. You can specifically buy items from him.';
$txt['Shop_trade_mytrades_actions'] = 'Actions';
$txt['Shop_trade_log'] = 'Trade log';
$txt['Shop_trade_log_desc'] = 'Find out about the items that you have bought and those that you have sold.';
$txt['Shop_trade_cost'] = 'Trade cost:';
$txt['Shop_trade_cost_desc'] = 'Set the price for your item.';
$txt['Shop_trade_mytrades'] = 'View trade list';


// Gift
$txt['Shop_gift_member_find'] = 'Type the name of the user you want to send a gift.';
$txt['Shop_gift_send_item'] = 'Send item';
$txt['Shop_gift_send_money'] = 'Send %s';
$txt['Shop_gift_amount'] = 'Amount to send';
$txt['Shop_gift_item_select'] = 'Select an item';
$txt['Shop_gift_item_sent'] = 'The item was sent successfully.';
$txt['Shop_gift_money_sent'] = 'The ' . (!empty($modSettings['Shop_credits_suffix']) ? $modSettings['Shop_credits_suffix'] : 'Credits') . ' were sent successfully.';
$txt['Shop_gift_message'] = 'Message to send to the member';
$txt['Shop_gift_message_desc'] = 'User will still receive a notification, but you can leave the box blank.';
$txt['Shop_gift_notification_subject'] = 'You have received a gift.';
$txt['Shop_gift_notification_message1'] = '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has sent you an item! He has gifted you the item \'%3$s\'.' . "\n" . 'If they left an additional message, will be shown below.' . "\n\n" . '%4$s' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
$txt['Shop_gift_notification_message2'] = '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has sent you %3$s! He has gifted you [i]%4$s[/i].' . "\n" . 'You now have %5$s' . "\n" . 'If they left an additional message, will be shown below.' . "\n\n" . '%6$s' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';

// Stats
$txt['Shop_stats_desc'] = 'Welcome to the Stats site, here you can keep track of the most relevant information about the Shop';
$txt['Shop_stats_most_bought'] = 'Most bought';
$txt['Shop_stats_most_traded'] = 'Top traded';
$txt['Shop_stats_top_cats'] = 'Best categories';
$txt['Shop_stats_top_buyers'] = 'Top buyers';
$txt['Shop_stats_top_inventories'] = 'Top inventories';
$txt['Shop_stats_top_gifts_sent'] = 'Gifts sent';
$txt['Shop_stats_top_gifts_received'] = 'Gifts received';
$txt['Shop_stats_top_money_sent'] = 'Money sent';
$txt['Shop_stats_top_money_received'] = 'Money received';
$txt['Shop_stats_traders'] = 'Best Traders';
$txt['Shop_stats_sellers'] = 'Best Sellers';
$txt['Shop_stats_richest_bank'] = 'Richest bank';
$txt['Shop_stats_richest_pocket'] = 'Richest pocket';
$txt['Shop_stats_last_added'] = 'Last items added';
$txt['Shop_stats_last_bought'] = 'Last items bought or traded';
$txt['Shop_stats_top_profit'] = 'Top profit';
$txt['Shop_stats_top_spent'] = 'Top spent';
$txt['Shop_stats_most_expensive'] = 'Most expensive';

// Games Room
$txt['Shop_games_welcome'] = 'Welcome to the Games Room';
$txt['Shop_games_welcome_desc'] = 'Here you can play some games and win some money, or lose it... Good luck and enjoy!<br />You currently have %d days left to play in the Games Room.';
$txt['Shop_games_setting1'] = 'Days until games room pass expires:';
$txt['Shop_games_success'] = 'Successfully set games room pass to expire in %d days!';
$txt['Shop_games_invalidpass'] = 'Your pass for the games room has expired, please buy a new pass at the shop!';
$txt['Shop_games_daysleft'] = ' days left';
$txt['Shop_games_listof'] = 'List of games available';
$txt['Shop_games_playgame'] = 'Play the game!';
$txt['Shop_games_letsplay'] = 'Let\'s play ';
$txt['Shop_games_slots'] = 'Slots';
$txt['Shop_games_lucky2'] = 'Lucky2';
$txt['Shop_games_number'] = 'Number Slots';
$txt['Shop_games_pairs'] = 'Pairs';
$txt['Shop_games_dice'] = 'Dice';
$txt['Shop_games_bet'] = 'Bet';
$txt['Shop_games_seven'] = 'Seven';
$txt['Shop_games_blackjack'] = 'Blackjack';
$txt['Shop_games_payouts'] = 'Payouts table';
$txt['Shop_games_loser'] = 'Sorry, you lost %s';
$txt['Shop_games_winner'] = 'Congratulations, you won %s';
$txt['Shop_games_spin'] = 'Spin the wheel';
$txt['Shop_games_roll'] = 'Roll the dice';
$txt['Shop_games_again'] = 'Try again!';
$txt['Shop_games_youhave'] = 'You have: ';
$txt['Shop_games_slots_desc'] = 'On this game you have to try to get exactly the same three images. Good luck!';
$txt['Shop_games_lucky2_desc'] = 'Here you just have to roll the dice and try to get a <strong>two</strong>. Good luck!';
$txt['Shop_games_number_desc'] = 'This game it\'s very similar to Slots, the difference is that here you have only numbers, and better chances to win. Good luck!';
$txt['Shop_games_number_complete'] = 'Three numbers: ';
$txt['Shop_games_number_firsttwo'] = 'First two numbers: ';
$txt['Shop_games_number_secondtwo'] = 'Last two numbers: ';
$txt['Shop_games_number_firstlast'] = 'First and last numbers: ';
$txt['Shop_games_pairs_desc'] = 'This game is quite hard, you need to get the exact pair of cards, and there are 52. Good luck!';
$txt['Shop_games_dice_desc'] = 'On this game you need to get the same numbers in both dices, let\'s roll the dice. Good luck!';

// Shop who
$txt['whoallow_shopinfo'] = 'Managing the Shop in the admin center';
$txt['whoallow_shopsettings'] = 'Managing the Shop settings';
$txt['whoallow_shopitems'] = 'Managing the Shop items';
$txt['whoadmin_shopmodules'] = 'Managing the Shop modules';
$txt['whoallow_shopcategories'] = 'Managing the Shop categories';
$txt['whoallow_shopgames'] = 'Managing the Shop games';
$txt['whoallow_shopinventory'] = 'Managing the Shop inventories and stock';
$txt['whoallow_shoplogs'] = 'Managing the Shop logs';
$txt['whoallow_shop'] = 'Viewing the <a href="' . $scripturl . '?action=shop">Forum Shop</a>';
$txt['whoallow_shop_buy'] = 'Buying items in the <a href="' . $scripturl . '?action=shop;sa=buy">Forum Shop</a>';
$txt['whoallow_shop_gift'] = 'Sending a <a href="' . $scripturl . '?action=shop;sa=gift">Gift</a> through the <a href="' . $scripturl . '?action=shop">Forum Shop</a>';
$txt['whoallow_shop_sendmoney'] = 'Sending <a href="' . $scripturl . '?action=shop;sa=sendmoney">%s</a> through the <a href="' . $scripturl . '?action=shop">Forum Shop</a>';
$txt['whoallow_shop_owninventory'] = 'Viewing his own Shop Inventory';
$txt['whoallow_shop_inventory'] = 'Viewing %1$s <a href="' . $scripturl . '?action=shop;sa=viewinventory;u=%2$d">Inventory</a>';
$txt['whoallow_shop_search'] = 'Searching for someone\'s <a href="' . $scripturl . '?action=shop;sa=search">Inventory</a>';
$txt['whoallow_shop_bank'] = 'Managing his money in the <a href="' . $scripturl . '?action=shop;sa=bank">Shop Bank</a>';
$txt['whoallow_shop_trade'] = 'Viewing the <a href="' . $scripturl . '?action=shop;sa=trade">Trade Center</a>';
$txt['whoallow_shop_tradelist'] = 'Viewing the items available for sale in the <a href="' . $scripturl . '?action=shop;sa=tradelist">Trade Center</a>';
$txt['whoallow_shop_owntrades'] = 'Viewing his own trade list <a href="' . $scripturl . '?action=shop;sa=trade">Trade Center</a>';
$txt['whoallow_shop_othertrades'] = 'Viewing %1$s\'s <a href="' . $scripturl . '?action=shop;sa=mytrades;u=%2$d">trade list</a> in the <a href="' . $scripturl . '?action=shop;sa=tradelist">Trade Center</a>';
$txt['whoallow_shop_tradelog'] = 'Viewing his trade log in the <a href="' . $scripturl . '?action=shop;sa=trade">Trade Center</a>';
$txt['whoallow_shop_stats'] = 'Viewing the <a href="' . $scripturl . '?action=shop;sa=stats">Shop Stats</a>';
$txt['whoallow_shop_games'] = 'Playing in the <a href="' . $scripturl . '?action=shop;sa=games">Games Room</a>';
$txt['whoallow_shop_games_slots'] = 'Playing <a href="'. $scripturl. '?action=shop;sa=games;play=slots">Slots</a> in the <a href="' . $scripturl . '?action=shop;sa=games">Games Room</a>';
$txt['whoallow_shop_games_lucky2'] = 'Playing <a href="'. $scripturl. '?action=shop;sa=games;play=lucky2">Lucky2</a> in the <a href="' . $scripturl . '?action=shop;sa=games">Games Room</a>';
$txt['whoallow_shop_games_number'] = 'Playing <a href="'. $scripturl. '?action=shop;sa=games;play=number">Number Slots</a> in the <a href="' . $scripturl . '?action=shop;sa=games">Games Room</a>';
$txt['whoallow_shop_games_pairs'] = 'Playing <a href="'. $scripturl. '?action=shop;sa=games;play=pairs">Pairs</a> in the <a href="' . $scripturl . '?action=shop;sa=games">Games Room</a>';
$txt['whoallow_shop_games_dice'] = 'Rolling the <a href="'. $scripturl. '?action=shop;sa=games;play=dice">Dice</a> in the <a href="' . $scripturl . '?action=shop;sa=games">Games Room</a>';