<?php

/**
 * @package ST Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2014, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

global  $scripturl, $txt, $modSettings;

// Main
$txt['Shop'] = 'Shop';
$txt['Shop_main_button'] = 'Shop';
$txt['Shop_main_home'] = 'Shop - Home';
$txt['Shop_main'] = 'shop';

// Main admin
$txt['Shop_admin_button'] = 'Shop Admin';
$txt['Shop_live_news'] = 'Live from smftricks.com';
$txt['Shop_live_error'] = 'Couldn\'t connect to smftricks.com';
$txt['Shop_version'] = 'Shop version';
$txt['Shop_donate_title'] = 'Donate to the author';
$txt['Shop_tab_info'] = 'Shop Information';
$txt['Shop_tab_info_desc'] = 'Hello %s, welcome to your ST Shop Admin panel. From here you can edit the shop settings, add items, modules, categories and set the reward in the games room.<br> Additionally you can check the shop logs adn use the maintenance tools to restock items or send items/money to specific users, or remove items from their inventory.';
$txt['Shop_main_credits'] = 'Credits';

// Shop settings
$txt['Shop_settings_general'] = 'General';
$txt['Shop_settings_general_desc'] = 'In this section you can enable or disable the general shop features.';
$txt['Shop_tab_settings'] = 'Shop Settings';
$txt['Shop_enable_shop'] = 'Enable Shop?';
$txt['Shop_enable_shop_desc'] = 'If the shop is not enabled, the shop page won\'t be visible, but core features will still work normally.';
$txt['Shop_enable_games'] = 'Enable Games Room?';
$txt['Shop_enable_bank'] = 'Enable Bank?';
$txt['Shop_enable_gift'] = 'Enable Send Gifts and Credits?';
$txt['Shop_enable_trade'] = 'Enable Trade?';
$txt['Shop_enable_stats'] = 'Enable Stats?';
$txt['Shop_stats_refresh'] = 'Time for refreshing stats';
$txt['Shop_stats_refresh_desc'] = 'The time in seconds that the forum will take to refresh your shop stats stored in cache. This helps you to prevent overloads of data and help to load information faster. <br/>By default is set to <i>900</i> (15 mins).';
$txt['Shop_enable_maintenance'] = 'Enable Maintenance?';
$txt['Shop_enable_maintenance_desc'] = 'This will put the Shop in maintenance, only users who are allowed to manage the Shop have access.';

// Permissions
$txt['Shop_settings_permissions'] = 'Permissions';
$txt['Shop_settings_permissions_desc'] = 'In this section you can handle the permissions in a very easy way.';
$txt['permissiongroup_shop'] = 'Shop permissions';
$txt['permissionname_shop_canAccess'] = 'Access Forum Shop';
$txt['groups_shop_canAccess'] = 'Access Forum Shop';
$txt['permissionhelp_shop_canAccess'] = 'If the user can access the shop.';
$txt['permissionhelp_groups_shop_canAccess'] = 'If the user can access the shop.';
$txt['cannot_shop_canAccess'] = ' You\'re not allowed to access the forum shop.';
$txt['permissionname_shop_canBuy'] = 'Buy Items';
$txt['groups_shop_canBuy'] = 'Buy Items';
$txt['permissionhelp_shop_canBuy'] = 'If the user is allowed to buy items.';
$txt['cannot_shop_canBuy'] = ' You\'re not allowed to buy items.';
$txt['permissionname_shop_playGames'] = 'Access the Games Room';
$txt['groups_shop_playGames'] = 'Access the Games Room';
$txt['permissionhelp_shop_playGames'] = 'If the user can access to the Games Room.';
$txt['cannot_shop_playGames'] = ' You\'re not allowed to access to the Games Room.';
$txt['permissionname_shop_canTrade'] = 'Access the Trade Center';
$txt['groups_shop_canTrade'] = 'Access the Trade Center';
$txt['permissionhelp_shop_canTrade'] = 'If the user have access to the trade center in the shop.';
$txt['cannot_shop_canTrade'] = ' You\'re not allowed to access the trade center.';
$txt['permissionname_shop_canBank'] = 'Can use the Bank';
$txt['groups_shop_canBank'] = 'Can use the Bank';
$txt['permissionhelp_shop_canBank'] = 'If the user is allowed to access the shop bank.';
$txt['cannot_shop_canBank'] = ' You\'re not allowed to access the bank.';
$txt['permissionname_shop_canGift'] = 'Send Gifts';
$txt['groups_shop_canGift'] = 'Send Gifts';
$txt['permissionhelp_shop_canGift'] = 'If the user is allowed to send gifts and/or money';
$txt['cannot_shop_canGift'] = ' You\'re not allowed to send gifts.';
$txt['permissionname_shop_viewInventory'] = 'View Inventory';
$txt['groups_shop_viewInventory'] = 'View Inventory';
$txt['permissionhelp_shop_viewInventory'] = 'If the user is allowed to view their inventory and other users inventory.';
$txt['cannot_shop_viewInventory'] = ' You\'re not allowed to view inventories.';
$txt['permissionname_shop_viewStats'] = 'View Stats';
$txt['groups_shop_viewStats'] = 'View Stats';
$txt['permissionhelp_shop_viewStats'] = 'If the user is allowed to view the shop stats page.';
$txt['cannot_shop_viewStats'] = ' You\'re not allowed to view the shop stats.';
$txt['permissionname_shop_canManage'] = 'Manage the Shop';
$txt['groups_shop_canManage'] = 'Manage the Shop';
$txt['permissionhelp_shop_canManage'] = 'This permission defines if the user is allowed to manage items, categories and logs of the Shop.';
$txt['cannot_shop_canManage'] = ' You\'re not allowed to manage the shop.';

// Credits settings
$txt['Shop_settings_credits'] = 'Shop Credits';
$txt['Shop_settings_credits_desc'] = 'Here you will be able to set the credits settings for the shop.';
$txt['Shop_credits_register'] = 'Credits upon registration';
$txt['Shop_credits_topic'] = 'Credits received per new topic';
$txt['Shop_credits_post'] = 'Credits received per new post';
$txt['Shop_credits_word'] = 'Credits received per word';
$txt['Shop_credits_character'] = 'Credits received per character';
$txt['Shop_credits_limit'] = 'Limit of credits received in a post';
$txt['Shop_credits_limit_desc'] = 'If you have set an amount per word or characters, here you can set the limit of credits that the user can receive.';
$txt['Shop_bank_settings'] = 'Bank settings';
$txt['Shop_bank_interest'] = 'Bank interest';
$txt['Shop_bank_interest_desc'] = 'Set the percent the users will receive per day.';
$txt['Shop_bank_interest_yesterday'] = 'Only logged interest';
$txt['Shop_bank_interest_yesterday_desc'] = 'With this enabled, the user won\'t receive bank interest if he has not logged in during the last 24 hours.';
$txt['Shop_bank_withdrawal_fee'] = 'Withdrawal fee';
$txt['Shop_bank_deposit_fee'] = 'Deposit fee';
$txt['Shop_bank_max_min_desc'] = 'Set 0 for no limits.';
$txt['Shop_bank_withdrawal_min'] = 'Minimum withdrawal';
$txt['Shop_bank_withdrawal_max'] = 'Maximum withdrawal';
$txt['Shop_bank_deposit_min'] = 'Minimum deposit';
$txt['Shop_bank_deposit_max'] = 'Maximum deposit';
$txt['Shop_credits_general_settings'] = 'General settings';
$txt['Shop_credits_prefix'] = 'Credits prefix';
$txt['Shop_credits_prefix_desc'] = 'The prefix is going to appear BEFORE the amount of credits.';
$txt['Shop_credits_suffix'] = 'Credits suffix';
$txt['Shop_credits_suffix_desc'] = 'The suffix is going to appear AFTER the amount of credits.';
$txt['Shop_images_resize'] = 'Resize shop images';
$txt['Shop_images_resize_desc'] = 'You can resize the shop item images to a determined width and height. By default is 32px.';
$txt['Shop_images_width'] = 'Images width';
$txt['Shop_images_height'] = 'Images height';
$txt['Shop_items_perpage'] = 'How many items to display per page?';
$txt['Shop_items_perpage_desc'] = 'This will affect all sections where appear an items list';
$txt['Shop_items_trade_fee'] = 'Trade fee percent';
$txt['Shop_items_trade_fee_desc'] = 'This will take away from sellers, a percent of the sell of an item.';
$txt['Shop_credits_count'] = 'Enable Shop credits';
$txt['Shop_credits_count_desc'] = 'This settings will allow users to receive credits for topics and posts on this board.';
$txt['Shop_credits_enable_bonus'] = 'Enable bonus';
$txt['Shop_credits_enable_bonus_desc'] = 'This will enable in this board the extra credits per word/character.';
$txt['Shop_credits_custom_override'] = 'This will override the main settings for credits.';
$txt['Shop_credits_viewing_who'] = 'Viewing credits of %s';

// Profile
$txt['Shop_items_none_select'] = '[NONE]';
$txt['Shop_display_pocket']  = 'Display credits in pocket';
$txt['Shop_display_pocket_placement']  = 'Pocket credits placement (posts)';
$txt['Shop_display_pocket_placement_desc']  = 'If it\'s enabled in posts you can choose different placements.';
$txt['Shop_display_bank']  = 'Display credits in bank';
$txt['Shop_display_bank_placement']  = 'Bank credits placement (posts)';
$txt['Shop_display_bank_placement_desc']  = 'If it\'s enabled in posts you can choose different placements.';
$txt['Shop_display_post']  = 'Only in posts';
$txt['Shop_display_profile']  = 'Only in profile';
$txt['Shop_display_both']  = 'Both (profile and posts)';
$txt['Shop_inventory_enable']  = 'Enable mini-inventory';
$txt['Shop_inventory_items_num'] = 'Max of items to display';
$txt['Shop_inventory_placement']  = 'Items inventory placement (posts)';
$txt['Shop_inventory_allow_hide'] = 'Allow users to hide inventory';
$txt['Shop_inventory_allow_hide_desc'] = 'User will be able to hide their inventory on posts and profile.';
$txt['Shop_inventory_show_same_once'] = 'Show the same item only once';

// Admin Items
$txt['Shop_settings_profile'] = 'Posting and Profile';
$txt['Shop_settings_profile_desc'] = 'Here you can find all the settings related to the post display and forum profile.';
$txt['Shop_tab_inventory'] = 'Shop Inventory';
$txt['Shop_tab_inventory_desc'] = 'Here you can find user\'s inventory and delete items from his account.';
$txt['Shop_tab_credits'] = 'Edit credits';
$txt['Shop_tab_credits_desc'] = 'Here you can see the credits (bank and pocket) of someone else and modify their amount.';
$txt['Shop_tab_items'] = 'Shop Items';
$txt['Shop_tab_items_desc'] = 'In this section you can browse all the items added to the shop, edit them and add new items.';
$txt['Shop_items_add'] = 'Add item';
$txt['Shop_items_add_desc'] = 'Here you can specify either use a premade item (module) or to use the default non-usable item.';
$txt['Shop_items_add_desc2'] = 'You are adding the item \'%s\' to your shop. For support with this item, please email the author: %s &lt;<a href="mailto:%3$s?subject=%1$s item">%3$s</a>&gt;, visit their website at <a href="%4$s">%4$s</a>. You can also ask for support at <a href="http://smftricks.com" target="_blank">SMF Tricks</a>, home of ST Shop';
$txt['Shop_items_edit_desc'] = 'You are currently editing the the item \'%s\'.';
$txt['Shop_items_delete'] = 'Delete items';
$txt['Shop_items_edit'] = 'Edit item';
$txt['Shop_items_uploaditems'] = 'Upload items';
$txt['Shop_items_uploaditems_desc'] = 'In this section you can easily upload new items to your shop images folder.';
$txt['Shop_item_module'] = 'Module';
$txt['Shop_item_function'] = 'Function';
$txt['Shop_item_not_module'] = 'If you don\'t want to use a module, the item will be based on a \'Default\' item with no functions or features';
$txt['Shop_item_module_select'] = 'Select a module';
$txt['Shop_item_modify'] = 'Edit';
$txt['Shop_sure_delete'] = 'Are you sure you want to delete these items?';
$txt['Shop_item_delete_also'] = 'This will also delete those items from the user\'s inventory';
$txt['Shop_items_deleted'] = 'Selected items were deleted successfully.';
$txt['Shop_items_added'] = 'The item was added successfully.';
$txt['Shop_items_updated'] = 'The item was updated successfully.';
$txt['Shop_item_add'] = 'Go';
$txt['Shop_item_add_items'] = 'Add the item';
$txt['Shop_item_save_item'] = 'Save item';
$txt['Shop_want_module'] = 'Want to use a premade item (module)?';
$txt['Shop_item_status'] = 'Status';
$txt['Shop_item_enable'] = 'Enable item?';
$txt['Shop_item_description_match'] = 'Make sure to change the Name and Description above to reflect the values that are on this area.';
$txt['Shop_item_additional'] = 'Additional item inputs';
$txt['Shop_item_limit_desc'] = 'Set the limit of items that an user can buy/have/carry. Set 0 for no limit.<br /><i>This won\'t take effect on previous bought items (if you\'re editing the item).</i>';
$txt['Shop_item_notice'] = 'Images are stored in Sources/Shop/items/. Feel free to add more images!';
$txt['Shop_items_na'] = 'N/A';
$txt['Shop_item_delete_after'] = 'Delete item after use?';
$txt['Shop_item_remove_inventory'] = 'Remove items from inventory';

// Modules
$txt['Shop_tab_modules'] = 'Modules';
$txt['Shop_tab_modules_desc'] = 'Here you can check all the shop modules (special items) available that were pre-included or uploaded.';
$txt['Shop_modules_uploadmodules'] = 'Upload modules';
$txt['Shop_modules_uploadmodules_desc'] = 'In this section you can easily upload new modules to your shop item modules folder.';
$txt['Shop_modules_delete'] = 'Delete modules';

// Admin Categories
$txt['Shop_tab_categories'] = 'Categories';
$txt['Shop_tab_categories_desc'] = 'In this section you can find all the created categories, and also edit them an add new categories.';
$txt['Shop_categories_add'] = 'Add category';
$txt['Shop_categories_add_desc'] = 'You are adding a new category. Here you can specify the name, image and description for the new category.';
$txt['Shop_categories_edit_desc'] = 'You are currently editing the the category \'%s\'.';
$txt['Shop_categories_delete'] = 'Delete categories';
$txt['Shop_categories_edit'] = 'Edit category';
$txt['Shop_sure_delete_cat'] = 'Are you sure you want to delete these categories?';
$txt['Shop_cat_delete_also'] = 'Also delete all the items';
$txt['Shop_cat_delete_also_desc'] = 'This will completly remove the items that are in those categories. If not, the items will be set as \'uncategorized\'';
$txt['Shop_category_add_category'] = 'Add the category';
$txt['Shop_categories_deleted'] = 'Selected categories were deleted successfully.';
$txt['Shop_categories_added'] = 'The category was added successfully.';
$txt['Shop_categories_updated'] = 'The category was updated successfully.';
$txt['Shop_total_items'] = 'Total items';
$txt['Shop_category_save_category'] = 'Save category';

// Games settings
$txt['Shop_tab_games'] = 'Games Settings';
$txt['Shop_settings_slots_desc'] = 'Here you can define how much the user will win or lose in Slots';
$txt['Shop_games_slots_desc'] = 'In this game you have to try to get exactly the same three images. Good luck!';
$txt['Shop_settings_slots_losing'] = 'How much the user will lose';
$txt['Shop_settings_slots_7'] = '7\'s payout';
$txt['Shop_settings_slots_bell'] = 'Bells payout';
$txt['Shop_settings_slots_cherry'] = 'Cherries payout';
$txt['Shop_settings_slots_lemon'] = 'Lemons payout';
$txt['Shop_settings_slots_orange'] = 'Oranges payout';
$txt['Shop_settings_slots_plum'] = 'Plums payout';
$txt['Shop_settings_slots_dollar'] = 'Dollars payout';
$txt['Shop_settings_slots_melon'] = 'Melons payout';
$txt['Shop_settings_slots_grapes'] = 'Grapes payout';
$txt['Shop_settings_lucky2_desc'] = 'Here you can define how much the user will win or lose in Lucky2';
$txt['Shop_settings_lucky2_losing'] = 'How much the user will lose';
$txt['Shop_settings_lucky2_price'] = 'How much the user will win';
$txt['Shop_settings_number_desc'] = 'Here you can define how much the user will win or lose in Numberslots';
$txt['Shop_settings_number_losing'] = 'How much the user will lose';
$txt['Shop_settings_number_complete'] = 'Three numbers payout';
$txt['Shop_settings_number_firsttwo'] = 'First two numbers payout';
$txt['Shop_settings_number_secondtwo'] = 'Last two numbers payout';
$txt['Shop_settings_number_firstlast'] = 'First and last numbers payout';
$txt['Shop_settings_pairs_desc'] = 'Here you can define how much the user will win or lose in Pairs';
$txt['Shop_settings_pairs_losing'] = 'How much the user will lose';
$txt['Shop_settings_pairs_price'] = 'How much the user will win';
$txt['Shop_settings_pairs_clubs'] = 'Clubs';
$txt['Shop_settings_pairs_clubs_1'] = 'Ace of clubs';
$txt['Shop_settings_pairs_clubs_2'] = 'Two of clubs';
$txt['Shop_settings_pairs_clubs_3'] = 'Three of clubs';
$txt['Shop_settings_pairs_clubs_4'] = 'Four of clubs';
$txt['Shop_settings_pairs_clubs_5'] = 'Five of clubs';
$txt['Shop_settings_pairs_clubs_6'] = 'Six of clubs';
$txt['Shop_settings_pairs_clubs_7'] = 'Seven of clubs';
$txt['Shop_settings_pairs_clubs_8'] = 'Eight of clubs';
$txt['Shop_settings_pairs_clubs_9'] = 'Nine of clubs';
$txt['Shop_settings_pairs_clubs_10'] = 'Ten of clubs';
$txt['Shop_settings_pairs_clubs_11'] = 'Jack of clubs';
$txt['Shop_settings_pairs_clubs_12'] = 'Queen of clubs';
$txt['Shop_settings_pairs_clubs_13'] = 'King of clubs';
$txt['Shop_settings_pairs_diamonds'] = 'Diamonds';
$txt['Shop_settings_pairs_diam_1'] = 'Ace of diamonds';
$txt['Shop_settings_pairs_diam_2'] = 'Two of diamonds';
$txt['Shop_settings_pairs_diam_3'] = 'Three of diamonds';
$txt['Shop_settings_pairs_diam_4'] = 'Four of diamonds';
$txt['Shop_settings_pairs_diam_5'] = 'Five of diamonds';
$txt['Shop_settings_pairs_diam_6'] = 'Six of diamonds';
$txt['Shop_settings_pairs_diam_7'] = 'Seven of diamonds';
$txt['Shop_settings_pairs_diam_8'] = 'Eight of diamonds';
$txt['Shop_settings_pairs_diam_9'] = 'Nine of diamonds';
$txt['Shop_settings_pairs_diam_10'] = 'Ten of diamonds';
$txt['Shop_settings_pairs_diam_11'] = 'Jack of diamonds';
$txt['Shop_settings_pairs_diam_12'] = 'Queen of diamonds';
$txt['Shop_settings_pairs_diam_13'] = 'King of diamonds';
$txt['Shop_settings_pairs_hearts'] = 'Hearts';
$txt['Shop_settings_pairs_hearts_1'] = 'Ace of hearts';
$txt['Shop_settings_pairs_hearts_2'] = 'Two of hearts';
$txt['Shop_settings_pairs_hearts_3'] = 'Three of hearts';
$txt['Shop_settings_pairs_hearts_4'] = 'Four of hearts';
$txt['Shop_settings_pairs_hearts_5'] = 'Five of hearts';
$txt['Shop_settings_pairs_hearts_6'] = 'Six of hearts';
$txt['Shop_settings_pairs_hearts_7'] = 'Seven of hearts';
$txt['Shop_settings_pairs_hearts_8'] = 'Eight of hearts';
$txt['Shop_settings_pairs_hearts_9'] = 'Nine of hearts';
$txt['Shop_settings_pairs_hearts_10'] = 'Ten of hearts';
$txt['Shop_settings_pairs_hearts_11'] = 'Jack of hearts';
$txt['Shop_settings_pairs_hearts_12'] = 'Queen of hearts';
$txt['Shop_settings_pairs_hearts_13'] = 'King of hearts';
$txt['Shop_settings_pairs_spades'] = 'Spades';
$txt['Shop_settings_pairs_spades_1'] = 'Ace of spades';
$txt['Shop_settings_pairs_spades_2'] = 'Two of spades';
$txt['Shop_settings_pairs_spades_3'] = 'Three of spades';
$txt['Shop_settings_pairs_spades_4'] = 'Four of spades';
$txt['Shop_settings_pairs_spades_5'] = 'Five of spades';
$txt['Shop_settings_pairs_spades_6'] = 'Six of spades';
$txt['Shop_settings_pairs_spades_7'] = 'Seven of spades';
$txt['Shop_settings_pairs_spades_8'] = 'Eight of spades';
$txt['Shop_settings_pairs_spades_9'] = 'Nine of spades';
$txt['Shop_settings_pairs_spades_10'] = 'Ten of spades';
$txt['Shop_settings_pairs_spades_11'] = 'Jack of spades';
$txt['Shop_settings_pairs_spades_12'] = 'Queen of spades';
$txt['Shop_settings_pairs_spades_13'] = 'King of spades';
$txt['Shop_settings_dice_desc'] = 'Here you can define how much the user will win or lose in Dice';
$txt['Shop_settings_dice_losing'] = 'How much the user will lose';
$txt['Shop_settings_dice_1'] = 'Dice 1 payout';
$txt['Shop_settings_dice_2'] = 'Dice 2 payout';
$txt['Shop_settings_dice_3'] = 'Dice 3 payout';
$txt['Shop_settings_dice_4'] = 'Dice 4 payout';
$txt['Shop_settings_dice_5'] = 'Dice 5 payout';
$txt['Shop_settings_dice_6'] = 'Dice 6 payout';

// Logs
$txt['Shop_tab_logs'] = 'Shop logs';
$txt['Shop_tab_logs_desc'] = 'Here you can check all the logs, including sent money, trading, gifts, and more.';
$txt['Shop_logs_admin_money'] = 'Admin Money';
$txt['Shop_logs_admin_money_desc'] = 'Log for the sent %s by admin.';
$txt['Shop_logs_admin_items'] = 'Admin Items';
$txt['Shop_logs_admin_items_desc'] = 'Log for the sent/gifted items by admin.';
$txt['Shop_logs_money'] = 'Money Sent';
$txt['Shop_logs_money_desc'] = 'Log for the sent %s.';
$txt['Shop_logs_items'] = 'Items Sent';
$txt['Shop_logs_items_desc'] = 'Log for the sent or gifted items.';
$txt['Shop_logs_trade'] = 'Trade';
$txt['Shop_logs_trade_desc'] = 'Log for the items from trade center.';
$txt['Shop_logs_bank'] = 'Bank transactions';
$txt['Shop_logs_bank_desc'] = 'Log for bank transactions, deposits, withdraws.';
$txt['Shop_logs_buy'] = 'Bought items';
$txt['Shop_logs_buy_desc'] = 'Log for the bought items in the forum shop.';
$txt['Shop_logs_games'] = 'Games Room';
$txt['Shop_logs_games_desc'] = 'Log for the %s obtained in the gaming room.';
$txt['Shop_logs_games_type'] = 'Game';
$txt['Shop_logs_empty'] = 'There are no entries for this log';
$txt['Shop_logs_user_sending'] = 'Sending user';
$txt['Shop_logs_user_receiving'] = 'Receiving user';
$txt['Shop_logs_date'] = 'Date';
$txt['Shop_logs_amount'] = 'Amount';
$txt['Shop_logs_no_entries'] = 'There are no entries available';
$txt['Shop_logs_delete'] = 'Delete selected logs';
$txt['Shop_logs_buyer'] = 'Buyer';
$txt['Shop_logs_seller'] = 'Seller';
$txt['Shop_logs_user'] = 'User';
$txt['Shop_logs_fee'] = 'Fee';
$txt['Shop_logs_fee_type1'] = ' from pocket';
$txt['Shop_logs_fee_type2'] = ' from bank';
$txt['Shop_logs_transaction'] = 'Transaction';
$txt['Shop_logs_trans_withdraw'] = 'Withdraw';
$txt['Shop_logs_trans_deposit'] = 'Deposit';
$txt['Shop_logs_have_date'] = 'Has it since';
$txt['Shop_logs_updated'] = 'The selected entries were successfully deleted.';

// Admin inventory
$txt['Shop_inventory_groupcredits'] = 'Group credits';
$txt['Shop_inventory_groupcredits_desc'] = 'Here you can send some money to a very specific group.';
$txt['Shop_inventory_restock'] = 'Restock items';
$txt['Shop_inventory_restock_desc'] = 'Here you can reset the stock of specific items, or all of them.';
$txt['Shop_user_credits_updated'] = 'The credits of %s were successfully updated';
$txt['Shop_inventory_items_deleted'] = 'The selected items were successfully deleted from %s\'s profile';
$txt['Shop_groupcredits_sent'] = $modSettings['Shop_credits_suffix'] . ' were successfully sent';
$txt['Shop_membergroup'] = 'Membergroup';
$txt['Shop_groupcredits_action'] = 'Action:';
$txt['Shop_groupcredits_add'] = 'Add';
$txt['Shop_groupcredits_substract'] = 'Substract';
$txt['Shop_restock_successful'] = 'Selected items were restocked successfully';
$txt['Shop_restock_what'] = 'Items to restock';
$txt['Shop_restock_all'] = 'Restock all items';
$txt['Shop_restock_selected'] = 'Restock only selected items';
$txt['Shop_restock_select_items'] = 'Select the items you would like to restock';
$txt['Shop_restock_lessthan'] = 'Restock all items with a stock less or equal to';
$txt['Shop_restock_lessthan_desc'] = 'If you checked the option to restock only selected items, this option will be ignored.';
$txt['Shop_restock_amount'] = 'Amount to add to stock';
$txt['Shop_restock_amount_desc'] = 'It will sum that number with the current stock of the item(s)';
$txt['Shop_restock_error_noitems'] = 'You didn\'t select any items.';
$txt['Shop_inventory_usercredits'] = 'Send credits';
$txt['Shop_inventory_usercredits_desc'] = 'Send credits to specific members in the forum.';
$txt['Shop_inventory_members_desc'] = 'Here you can select the members you wish to send stuff to.';
$txt['Shop_inventory_useritems'] = 'Send items';
$txt['Shop_inventory_useritems_desc'] = 'Send items to specific members in the forum. <br> If any item has a specific carrying limit it will be ignored, but it will still lower the stock for the item on the number of members selected.';
$txt['Shop_inventory_useritems_noitems'] = 'There are no items in the shop';
$txt['Shop_inventory_useritems_nostock'] = 'No enough stock of this item for the number of selected users';
$txt['Shop_useritems_sent'] = 'Selected item was successfully sent';


// Files
$txt['Shop_file_already_exists'] = 'Sorry, that file already exists.';
$txt['Shop_file_error_type1'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
$txt['Shop_file_error_type2'] = 'Sorry, only PHP files are allowed.';
$txt['Shop_file_too_large'] = 'Sorry, your file is too large.';
$txt['Shop_item_upload_success'] = 'The item was uploaded successfully';
$txt['Shop_item_upload_error'] = 'There was an error and the item was not uploaded.';
$txt['Shop_module_upload_success'] = 'The module was uploaded successfully';
$txt['Shop_module_upload_error'] = 'There was an error and the module was not uploaded.';
$txt['Shop_module_delete'] = 'Are you sure you want to delete these modules?';
$txt['Shop_module_delete_also'] = 'This will also delete those modules from the modules directory. <br/> Existing items using this module will be turned into default and regular items (non-usable)';

// Errors
$txt['Shop_item_delete_error'] = 'Please choose something to delete!';
$txt['Shop_item_notfound'] = 'Unable to find an item';
$txt['Shop_module_notfound'] = 'Unable to find that module';
$txt['Shop_category_notfound'] = 'Unable to find a category';
$txt['Shop_cannot_open_items'] = 'Cannot open Sources/Shop/modules dir!';
$txt['Shop_cannot_open_images'] = 'Cannot open Sources/Shop/items dir!';
$txt['Shop_modules_only_admin'] = 'Only admins can upload modules';
$txt['Shop_modules_invalid'] = 'The uploaded file is not valid as a shop module, check that the file is correct or contact the author.';
$txt['Shop_no_goback'] = 'NO, go back!';
$txt['Shop_no_goback2'] = 'Cancel';
$txt['Shop_item_error'] = 'ERROR: Could not create instance of \'%s\' item!<br />';
$txt['Shop_no_items'] = 'There are no items added!';
$txt['Shop_inventory_no_items'] = 'You don\'t have any items!';
$txt['Shop_no_modules'] = 'There are no modules added!';
$txt['Shop_no_categories'] = 'There are no categories created!';
$txt['Shop_item_name_blank'] = 'You need to enter a name for the item!';
$txt['Shop_category_name_blank'] = 'You need to enter a name for the category!';
$txt['Shop_currently_disabled'] = 'Shop is currently disabled by admin! Please come back soon.';
$txt['Shop_currently_maintenance'] = 'Shop is in maintenance mode. Please come back later!';
$txt['Shop_currently_maintenance_warn'] = 'Shop is in Maintenance Mode. Only allowed users can currently access.';
$txt['Shop_currently_maintenance_warn_desc'] = 'You can turn off Maintenance Mode from the <a href="'. $scripturl. '?action=admin;area=shopsettings;sa=general">General Settings</a> area.';
$txt['Shop_not_allowedto_canAccess'] = 'Sorry, you are not allowed to access the Shop.';
$txt['Shop_item_notown'] = 'What are you doing? You don\'t own that item, you cannot trade it!';
$txt['Shop_item_notown_use'] = 'What are you doing? You don\'t own that item, you cannot use it!';
$txt['Shop_user_empty'] = 'Please type a member name.';
$txt['Shop_user_unable_tofind'] = 'Unable to find an user, please try again.';
$txt['Shop_usergroup_unable_tofind'] = 'Unable to find any members in the selected groups.';
$txt['Shop_item_notprice'] = 'You need to specify a price to the item.';
$txt['Shop_item_price_notnegative'] = 'Item price can not be zero or negative.';
$txt['Shop_item_alreadytraded'] = 'That item is already being traded.';
$txt['Shop_currently_disabled_bank'] = 'The bank is currently disabled.';
$txt['Shop_currently_disabled_trade'] = 'The trade center is currently disabled.';
$txt['Shop_currently_disabled_gift'] = 'The gifts are currently disabled.';
$txt['Shop_currently_disabled_stats'] = 'The stats are currently disabled.';
$txt['Shop_currently_disabled_games'] = 'The games room is currently disabled.';
$txt['Shop_item_notbuy_own'] = 'You cannot buy your own items... Duh!';
$txt['Shop_item_currently_traded'] = 'That item is currently on trading';
$txt['Shop_item_limit_reached'] = 'Sorry, you already reached the limit of this item in your inventory.';
$txt['Shop_module_notfound_admin'] = 'The module file for this item couldn\'t be found. All items using it have been disabled.<br>Please inform an admin, thanks.';
$txt['Shop_item_no_module'] = 'This item is missing it\'s module file. <br> Couldn\'t open the file: %s.php';
$txt['Shop_groupcredits_nogroups'] = 'No groups were selected for the action.';


// Shop main
$txt['Shop_shop_home'] = 'Home';
$txt['Shop_shop_buy'] = 'Buy Items';
$txt['Shop_shop_inventory'] = 'Inventory';
$txt['Shop_shop_stats'] = 'Stats';
$txt['Shop_shop_gift'] = 'Send A Gift';
$txt['Shop_shop_bank'] = 'Bank';
$txt['Shop_shop_trade'] = 'Trade Center';
$txt['Shop_shop_games'] = 'Games Room';
$txt['Shop_shop_yourinventory'] = 'Your Inventory';
$txt['Shop_user_info'] = 'User information';
$txt['Shop_welcome_to'] = '%s Shop';
$txt['Shop_welcome_text'] = '<strong>Welcome %1$s to the Shop.</strong><br /> Here, you can buy items with the %2$s you get from posting on the forum, trade them into the trade center, put your money save in the bank, browse your inventory and other users inventory, and also play at the games room.';
$txt['Shop_money_pocket'] = 'Pocket';
$txt['Shop_money_bank'] = 'Bank';
$txt['Shop_user_avatar'] = 'Avatar';
$txt['Shop_user_name'] = 'Member';
$txt['Shop_user_count'] = 'Count';

// Shop buy
$txt['Shop_buy_date'] = 'Newest';
$txt['Shop_item_buy_i'] = 'Buy item';
$txt['Shop_item_buy_i_ne'] = '<i>You don\'t have<br /> enough %s</i>';
$txt['Shop_buy_something'] = 'Please choose something to buy!';
$txt['Shop_buy_soldout'] = '<strong>Sold out!</strong>';
$txt['Shop_buy_item_nostock'] = 'Sorry, we don\'t have any remaining \'%s\' items in stock';
$txt['Shop_buy_item_notenough'] = 'You don\'t have enough %1$s to buy the item \'%2$s\'. You need %4$s%3$d %1$s more to buy that item.';
$txt['Shop_buy_item_bought'] = 'You have successfully bought the item \'%1$s\'. <br />You now have %2$s%3$d %4$s in your pocket.';
$txt['Shop_buy_item_bought_use'] = 'You have successfully bought the item \'%1$s\'. To use the item, please go to your inventory.<br />You now have %2$s%3$d %4$s in your pocket.';
$txt['Shop_buy_item_bought_error'] = 'You probably came here for a mistake, because you have not bought any item before coming to this section.';
$txt['Shop_buy_item_who'] = 'Who owns %s';
$txt['Shop_buy_item_who_nobody'] = 'Nobody owns the item \'%s\' at the moment.';
$txt['Shop_buy_item_who_this'] = 'Who owns this';
$txt['Shop_whohas_desc'] = 'On this page you can see a list of every user that own the item <i>%s</i> and how many they have.';

// Posting
$txt['Shop_posting_credits_pocket'] = $modSettings['Shop_credits_suffix'];
$txt['Shop_posting_credits_bank'] = 'Bank ' . $modSettings['Shop_credits_suffix'];
$txt['Shop_posting_credits_pocket2'] = ' ' . $modSettings['Shop_credits_suffix'];
$txt['Shop_posting_credits_bank2'] = ' ' . $modSettings['Shop_credits_suffix'] . ' in bank';
$txt['Shop_posting_inventory'] = 'Items inventory';
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
$txt['Shop_item_category_select'] = 'Select the category';
$txt['Shop_item_ascending'] = 'Ascending';
$txt['Shop_item_descending'] = 'Descending';
$txt['Shop_item_uncategorized'] = 'Uncategorized';
$txt['Shop_item_buy'] = 'Buy item';
$txt['Shop_item_trade'] = 'Trade';
$txt['Shop_item_trade_go'] = 'Trade item';
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
$txt['Shop_view_inventory'] = 'View inventory';
$txt['Shop_inventory_use_item'] = 'Use the %s item';
$txt['Shop_inventory_use_confirm'] = 'You are about to use this item. If there\'s anything else to fill, do it below, and then click the use button.';
$txt['Shop_item_fav'] = 'Fav';
$txt['Shop_inventory_hide'] = 'Hide my inventory on posts and profile';
$txt['Shop_inventory_search'] = 'Search users';
$txt['Shop_inventory_search_i'] = 'Search users inventory';
$txt['Shop_inventory_myinventory'] = 'My inventory';
$txt['Shop_inventory_member_name'] = 'Member name:';
$txt['Shop_inventory_member_name_desc'] = 'Type the name of the user you want to search.';
$txt['Shop_inventory_member_find'] = 'Find members';
$txt['Shop_inventory_viewing_who'] = 'Viewing %s\'s inventory';
$txt['Shop_item_date'] = 'Bought on';
$txt['Shop_item_traded'] = 'The item was successfully added to the Trade Center.<br /> You will receive a personal message when someone buys it. You can also remove it from the Trade Center whenever you want.';

// Bank
$txt['Shop_bank_desc'] = 'Welcome to the Bank. Here, you can safely store your %1$s to avoid losing them. All %1$s stored in the bank, gain or lose an interest at a rate of %2$d%3% per day.';
$txt['Shop_bank_youhave'] = 'You currently have %1$s%2$d %3$s in your pocket and %1$s%4$d %3$s in the bank. Would you like to deposit or withdraw?';
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
$txt['scheduled_task_bank_interest'] = 'Bank interest';
$txt['scheduled_task_desc_bank_interest'] = 'The magic task that will get money from nowhere.';

// Trade center
$txt['Shop_trade_main'] = 'Trade center';
$txt['Shop_trade_list'] = 'Trade list';
$txt['Shop_trade_list_desc'] = 'Here you will find every item that users have put for sale in the Trade Center.<br>If you want to find items from a specific user you can use the search tool located below.';
$txt['Shop_trade_desc'] = '<strong>Welcome %1$s to the Trade Center.</strong><br /> Here, you can trade and buy items from other users, check on your current items listed and find out more about the more active users in terms of sales, profit and purchases.';
$txt['Shop_item_remove_ftrade'] = 'Remove item from Trade';
$txt['Shop_item_trade_removed'] = 'Your item was succesfully removed from the trade center.<br> You can now find it back in your inventory.';
$txt['Shop_trade_notification_sold_from'] = 'Forum Shop';
$txt['Shop_trade_notification_sold_subject'] = 'Your item on trade was sold successfully.';
$txt['Shop_trade_notification_sold_message1'] = 'Congratulations!' . "\n" . '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has purchased your item \'%3$s\' for [i]%4$s[/i]. All the %5$s from the purchase have been added to your pocket.' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
$txt['Shop_trade_notification_sold_message2'] = 'Congratulations!' . "\n" . '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has purchased your item \'%3$s\' for [i]%4$s[/i], but has been taken away a fee of [i]%5$s[/i]. The other [i]%6$s[/i] from the purchase have been added to your pocket.' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
$txt['Shop_trade_myprofile'] = 'My items';
$txt['Shop_trade_myprofile_desc'] = 'This is the list of items <strong>you</strong> currently have in the Trade Center. You can remove them also if you want.';
$txt['Shop_trade_profile'] = 'Items from %s';
$txt['Shop_trade_profile_desc'] = 'You are currently viewing the list of <strong>%s\'s</strong> for the items he has in the Trade Center. You can specifically buy items from him.';
$txt['Shop_trade_mytrades_actions'] = 'Actions';
$txt['Shop_trade_log'] = 'Trade log';
$txt['Shop_trade_log_desc'] = 'Find out about the items that you have bought and those that you have sold.';
$txt['Shop_trade_cost'] = 'Trade cost:';
$txt['Shop_trade_cost_desc'] = 'Set the price for your item.';
$txt['Shop_view_mytrades'] = 'View trade list';


// Gift
$txt['Shop_gift_member_find'] = 'Type the name of the user you want to send a gift.';
$txt['Shop_gift_send_item'] = 'Send item';
$txt['Shop_gift_send_money'] = 'Send %s';
$txt['Shop_gift_amount'] = 'Amount to send';
$txt['Shop_gift_item_select'] = 'Select an item';
$txt['Shop_gift_no_items'] = 'You do not have any item to send';
$txt['Shop_gift_no_item_found'] = 'You did not select any item to send.';
$txt['Shop_gift_no_amount'] = 'You did not set any amount to send';
$txt['Shop_gift_unable_user'] = 'Unable to find an user.';
$txt['Shop_gift_item_sent'] = 'The item \'%s\' was sent successfully.';
$txt['Shop_gift_money_sent'] = 'The %1$s were sent successfully.<br />You now have %2$s%3$d %1$s in your pocket.';
$txt['Shop_gift_not_yourself'] = 'You cannot send gifts to yourself, come on don\'t be selfish.';
$txt['Shop_gift_not_enough_pocket'] = 'You do not have enough %s in your pocket for this gift.';
$txt['Shop_gift_not_negative_or_zero'] = 'The amount to send cannot be negative nor zero.';
$txt['Shop_gift_message'] = 'Message to send to the member';
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


// Add To Post Count
$txt['Shop_atpc_setting1'] = 'Amount to change post count by:';
$txt['Shop_atpc_success'] = 'Successfully added <strong>%d</strong> to post count!';
// Change Display Name
$txt['Shop_cdn_setting1'] = 'Minimum length of name:';
$txt['Shop_cdn_new_display_name'] = 'New Display Name:';
$txt['Shop_cdn_new_display_name_desc'] = 'Please choose a display name which is at least %d characters long.';
$txt['Shop_cdn_error_short'] = 'The display name you chose is not long enough! Please go back and choose a name which is at least %d characters long.';
$txt['Shop_cdn_error_long'] = 'The display name you chose is too long, try something shorter.';
$txt['Shop_cdn_error_empty'] = 'The display name you chose is invalid.';
$txt['Shop_cdn_error_taken'] = 'That display name is already taken.';
$txt['Shop_cdn_error_same'] = 'That display name is the same you already have.';
$txt['Shop_cdn_success'] = 'Successfully changed your display name to %s';
// Change Other Title and Change Title
$txt['Shop_cot_title'] = 'New title:';
$txt['Shop_cot_find_desc'] = 'Type the name of the user you want to change their title';
$txt['Shop_cot_empty_title'] = 'You need to enter a new title to use!';
$txt['Shop_cot_notown_title'] = 'You cannot use this item on yourself';
$txt['Shop_cot_success'] = 'Successfully changed <i>%1$s</i>\'s title to <strong>%2$s</strong>';
$txt['Shop_cot_own_success'] = 'Successfully changed title to <strong>%2$s</strong>';
// Change Username
$txt['Shop_cu_new_username'] = 'New Username:';
$txt['Shop_cu_new_username_desc'] = 'Please choose your new username.<br><strong>Be aware</strong> that this will change the username you use to access the forum and <strong>you will have to reset your password after using it</strong>.<br> It\'s recommended that you logout of the forum and reset your password right after.';
$txt['Shop_cu_error_short'] = 'The username you chose is not long enough!.';
$txt['Shop_cu_error_long'] = 'The username you chose is too long, try something shorter.';
$txt['Shop_cu_error_empty'] = 'The username you chose is invalid.';
$txt['Shop_cu_error_taken'] = 'That username is already taken.';
$txt['Shop_cu_error_same'] = 'That username is the same you already have.';
$txt['Shop_cu_success'] = 'Successfully changed your username to <strong>%s</strong>';
// Decrease Post
$txt['Shop_dp_setting1'] = 'Amount to decrease by:';
$txt['Shop_dp_find_desc'] = 'Type the name of the user you want to decrease their post count';
$txt['Shop_dp_yourself'] = 'You cannot use this item on yourself';
$txt['Shop_dp_success'] = 'Successfully decreased <i>%1$s</i>\'s posts by <strong>%2$d</strong>!';
// Increase Total Time Logged In
$txt['Shop_itli_setting1'] = 'Amount to increase total time by:';
$txt['Shop_itli_hours'] = 'Hours';
$txt['Shop_itli_success'] = 'Successfully added <strong>%d</strong> hours to total logged in time.';
// Random Money
$txt['Shop_rm_setting1'] = 'Minimum amount winnable:';
$txt['Shop_rm_setting2'] = 'Maximum amount winnable:';
$txt['Shop_rm_lost_pocket'] = 'You lost %s!';
$txt['Shop_rm_lost_bank'] = 'You lost %s! <br /><br /> However, you didn\'t have enough money in your pocket, so the money was taken from your bank instead!<br/><i>If you ended up with a negative value in the bank is time for you to rethink your economy</i>';
$txt['Shop_rm_success'] = 'Congratulations, you got %s!';
// Sticky Topic
$txt['Shop_st_choose_topic'] = 'Please choose which topic you would like to sticky';
$txt['Shop_st_error'] = 'No topic chosen!';
$txt['Shop_st_topic_notexists'] = 'That topic does not exist!';
$txt['Shop_st_topic_notown'] = 'That isn\'t your topic!';
$txt['Shop_st_notopics'] = 'You don\'t have any topic created! (Or any non-sticky topic)';
$txt['Shop_st_success'] = 'The topic <a href="'. $scripturl . '?topic=%1$d.0">%2$s</a> was successfully stickied!';
// Steal
$txt['Shop_steal_setting1'] = 'Probability of successful steal:';
$txt['Shop_steal_setting1_desc'] = 'For steal, user does NOT need to, and shouldn\'t know the probability! It\'s more fun this way :-)';
$txt['Shop_steal_setting2'] = 'Send notifications:';
$txt['Shop_steal_setting2_desc'] = 'When a user gets robbed he will receive a notification informing him who robbed him and how much they stole from him.';
$txt['Shop_steal_from'] = 'Steal from:';
$txt['Shop_steal_success1'] = 'You successfully stole from %2$s, although you only got %1$s!';
$txt['Shop_steal_success2'] = 'You successfully stole %1$s from %2$s! It\'s their fault for not having the money in the bank!';
$txt['Shop_steal_error'] = 'Steal unsuccessful! Sorry, better luck next time.';
$txt['Shop_steal_error_zero'] = 'That user is not carrying money on their pocket.';
$txt['Shop_steal_error_yourself'] = 'What are you doing? You cannot steal from yourself.';
$txt['Shop_steal_notification_robbed'] = 'You just got robbed!';
$txt['Shop_steal_notification_message'] = 'We are sorry to inform you that you just have been robbed!' . "\n" . '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has stripped you of [i]%3$s[/i] from your pocket. You now have [i]%4$s[/i] remaining in your pocket.' . "\n\n" . ' Next time put your money safe in the Shop Bank.' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
