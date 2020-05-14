<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global $modSettings;

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
$txt['Shop_item_date'] = 'Date';
$txt['Shop_item_traded'] = 'The item was successfully added to the Trade Center.<br /> You will receive a personal message when someone buys it. You can also remove it from the Trade Center whenever you want.';