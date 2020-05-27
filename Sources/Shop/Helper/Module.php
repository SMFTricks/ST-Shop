<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @author Daniel15 <dansoft@dansoftaustralia.net>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @copyright Copyright (c) 2005-2007, DanSoft Australia
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

namespace Shop\Helper;

if (!defined('SMF'))
	die('No direct access...');

class Module
{
	/* 
	 * All the below fields aren't in the item files themselves. They're just defaults
	 * The correct values are filled in in the getItemDetails() function inside the item
	 * These values are used as fallovers. If any of the variables aren't defined in the item,
	 * it will use the defaults from here.
	 */
	// The name of the item (can be changed by admin)
    var $name;
	// The description of the item (can be changed)
    var $desc;
	// The default price (can be changed)
    var $price;

	// The name of the author
	var $authorName;
	// The author's web address
	var $authorWeb;
	// The email address of the author
	var $authorEmail;

	// Whether the item requires additional input or not
	// SMFShop versions before 1.0 (I think) always accepted input, regardless
	// Because of this, this value is set to 'true' for backwards compatibility
    var $require_input; 
	// Whether the item is 'usable' (most items) or not (a rock :D)
    var $can_use_item;
	// Whether the item will be deleted from the user's inventory upon use
	// This only works in SMFShop New Version and later, and was always true
	// in previous versions
	var $delete_after_use;
	// Whether this item allows an admin to change values used by getAddInput()
	// when editing the item. To support this, the item must set the value
	// of the input field to $item_info[1], $item_info[2], etc.
	var $addInput_editable;

	// Some more vars for the info fields
	var $item_info = [
		1 => '',
		2 => '',
		3 => '', 
		4 => '',
	];

	// We want to load the language file for these special items and initialize some values
	// Here, you should fill in the $name, $desc and $price variables.
	function __construct()
	{
		// Modules have their own language file
		loadLanguage('Shop/Modules');

		$this->authorName = '';
		$this->authorWeb = '';
		$this->authorEmail = '';

		$this->name = '';
		$this->desc = '';
		$this->price = 0;
		$this->require_input = true; 

		$this->can_use_item = true;
		$this->delete_after_use = true;
		$this->addInput_editable = false;
	}
    
    /* This is called when person is adding this item to their SMFShop installation via the admin
     * panel. If you need any additional fields, then return them here
	 * Any inputs should be called info1, info2, info3 and info4, and have a value of $item_info[1]
	 * (so that when the admin is editing the item, it shows the previous value)
	 * See AddToPostCount.php for a working demo
	 */
    function getAddInput()
	{
		// Example:
		// return 'Some input that the admin fills in: <input type="text" name="info1" value="' . $item_info[1] . '" />';
	}

    /* 
	 * This is called when person tries to use item. This is used to get input for the item.
     * If item needs no input then just return false here (you don't even need this function
	 * if the can_use_item variable is set to false)
	 * Call the fields whatever you want, as they're passed straight to the onUse() function (via the $_POST array)
	 */
    function getUseInput()
	{
		/* 
		 * Example:
		 *
		 * If input needed:
		 * return 'Enter your name: <input type="text" name="name" />';
		 *
		 * If no input needed:
		 * return false;
		 */
	}

    /* This is called when item is actually used
	 * The input fields in the getUseInput() function are passed here in the $_POST array
	 * The admin fields in the getAddInput() function are passed in the $item_info array
	 */
    function onUse()
	{
		/* See the included items for an example
		 * For an example of an item that uses getAddInput(), see AddToPostCount.php
		 * For an example of an item that uses getUseInput(), see ChangeDisplayName.php 
		 */
	}
}