<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

global  $scripturl;

// Modules
$txt['Shop_tab_modules'] = 'Modules';
$txt['Shop_tab_modules_desc'] = 'Here you can check all the shop modules (special items) available that were pre-included or uploaded.';
$txt['Shop_modules_uploadmodules'] = 'Upload modules';
$txt['Shop_modules_uploadmodules_desc'] = 'In this section you can easily upload new modules to your shop item modules folder.';
$txt['Shop_modules_delete'] = 'Delete modules';

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
$txt['Shop_steal_setting2'] = 'Send personal message:';
$txt['Shop_steal_setting2_desc'] = 'When a user gets robbed he will receive a pm with information about who robbed him and how much they stole from him.';
$txt['Shop_steal_setting3'] = 'Send alert:';
$txt['Shop_steal_setting3_desc'] = 'When a user gets robbed he will receive an alert with information about who robbed him and how much they stole from him.';
$txt['Shop_steal_from'] = 'Steal from:';
$txt['Shop_steal_success1'] = 'You successfully stole from %2$s, although you only got %1$s!';
$txt['Shop_steal_success2'] = 'You successfully stole %1$s from %2$s! It\'s their fault for not having the money in the bank!';
$txt['Shop_steal_error'] = 'Steal unsuccessful! Sorry, better luck next time.';
$txt['Shop_steal_error_zero'] = 'That user is not carrying money on their pocket.';
$txt['Shop_steal_error_yourself'] = 'What are you doing? You cannot steal from yourself.';
$txt['Shop_steal_notification_robbed'] = 'You just got robbed!';
$txt['Shop_steal_notification_pm'] = 'We are sorry to inform you that you just have been robbed!' . "\n" . '[url=' . $scripturl . '?action=profile;u=%1$d]%2$s[/url] has stripped you of [i]%3$s[/i] from your pocket. You now have [i]%4$s[/i] remaining in your pocket.' . "\n\n" . ' Next time put your money safe in the Shop Bank.' . "\n\n" . 'This is an automatic notification, have a good day.' . "\n" .'- Forum Shop';
$txt['alert_shop_module_steal'] = '{member_link} just robbed you {amount} from your pocket!';