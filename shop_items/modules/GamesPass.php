<?php


if (!defined('SMF'))
	die('Hacking attempt...');

class item_GamesPass extends itemTemplate
{
	// When this function is called, you should set all the item's
	// variables (see inside this example)
	function getItemDetails()
	{
		$this->authorName = 'wdm2005';
		$this->authorWeb = 'http://sleepy-arcade.ath.cx/';
		$this->authorEmail = 'wdm2005@blueyonder.co.uk';

		$this->name = 'Games Room Pass xxx days';
		$this->desc = 'Allows access to Games Room for xxx days';
		$this->price = 50;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}

	function getAddInput()
	{
		global $item_info;

		if ($item_info[1] == 0) $item_info[1] = 30;
		return '
			<dl class="settings">
				<dt>
					'.Shop::text('games_setting1').'
				</dt>
				<dd>
					<input class="input_text" type="number" min="1" id="info1" name="info1" value="' . $item_info[1] . '" />
				</dd>
			</dl>';
	}

	function onUse()
	{
		global $user_info, $item_info;

		$days = 86400 * $item_info[1];
		$time = time() + $days;
		
		updateMemberData($user_info['id'], array('gamesPass' =>  $time));
		
		return '<div class="infobox">' . sprintf(Shop::text('games_success'), $item_info[1]) . '</div>';
	}
}

?>

