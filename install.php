<?php

/**
 * @package ST Shop
 * @version 2.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2018, Diego Andrés
 * @license http://www.mozilla.org/MPL/MPL-1.1.html
 */

	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
		require_once(dirname(__FILE__) . '/SSI.php');

	elseif (!defined('SMF'))
		exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

	global $smcFunc, $context;

	db_extend('packages');

	if (empty($context['uninstalling']))
	{
		// Scheduled Task
		$smcFunc['db_insert'](
			'ignore',
			'{db_prefix}scheduled_tasks',
			array(
				'time_offset' => 'int',
				'time_regularity' => 'int',
				'time_unit' => 'string',
				'disabled' => 'int',
				'task' => 'string',
				'callable' => 'string',
			),
			array(
				array(
					'0',
					'1',
					'd',
					'0',
					'bank_interest',
					'Shop::scheduled_shopBank',
				),
			),
			array('')
		);

		// Shop items
		$tables[] = array(
			'table_name' => '{db_prefix}shop_items',
			'columns' => array(
				array(
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'name',
					'type' => 'varchar',
					'size' => 50,
					'null' => false,
				),	
				array(
					'name' => 'image',
					'type' => 'tinytext',
					'null' => true,
				),
				array(
					'name' => 'description',
					'type' => 'varchar',
					'size' => 255,
					'null' => false,
				),
				array(
					'name' => 'price',
					'type' => 'int',
					'null' => false,
					'default' => 0,
				),
				array(
					'name' => 'count',
					'type' => 'smallint',
					'null' => false,
					'default' => 0,
				),
				array(
					'name' => 'module',
					'type' => 'tinyint',
					'null' => false,
					'size' => 10,
					'default' => 0,
				),
				array(
					'name' => 'function',
					'type' => 'tinytext',
					'null' => true,
				),
				array(
					'name' => 'info1',
					'type' => 'int',
					'null' => true,
				),
				array(
					'name' => 'info2',
					'type' => 'int',
					'null' => true,
				),
				array(
					'name' => 'info3',
					'type' => 'int',
					'null' => true,
				),
				array(
					'name' => 'info4',
					'type' => 'int',
					'null' => true,
				),
				array(
					'name' => 'input_needed',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'can_use_item',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'delete_after_use',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'catid',
					'type' => 'int',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'status',
					'type' => 'smallint',
					'default' => 1,
					'null' => false,
				),
				array(
					'name' => 'itemlimit',
					'type' => 'int',
					'default' => 0,
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('itemid'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		// Shop modules
		$tables[] = array(
			'table_name' => '{db_prefix}shop_modules',
			'columns' => array(
				array(
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'name',
					'type' => 'varchar',
					'size' => 50,
					'null' => false,
				),	
				array(
					'name' => 'description',
					'type' => 'varchar',
					'size' => 255,
					'null' => false,
				),
				array(
					'name' => 'price',
					'type' => 'int',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'author',
					'type' => 'varchar',
					'size' => 80,
					'null' => false,
				),
				array(
					'name' => 'email',
					'type' => 'varchar',
					'size' => 255,
					'null' => false,
				),
				array(
					'name' => 'require_input',
					'type' => 'tinyint',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'can_use',
					'type' => 'tinyint',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'editable_input',
					'type' => 'tinyint',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'web',
					'type' => 'varchar',
					'size' => 255,
					'null' => false,
				),
				array(
					'name' => 'file',
					'type' => 'tinytext',
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		// User inventory
		$tables[] = array(
			'table_name' => '{db_prefix}shop_inventory',
			'columns' => array(
				array(
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'userid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),	
				array(
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
				),
				array(
					'name' => 'trading',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'tradecost',
					'type' => 'int',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'date',
					'type' => 'int',
					'null' => false,
				),
				array(
					'name' => 'tradedate',
					'type' => 'int',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'fav',
					'type' => 'int',
					'default' => 0,
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		// Shop Categories
		$tables[] = array(
			'table_name' => '{db_prefix}shop_categories',
			'columns' => array(
				array(
					'name' => 'catid',
					'type' => 'smallint',
					'size' => 5,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'name',
					'type' => 'varchar',
					'size' => 50,
					'null' => false,
				),	
				array(
					'name' => 'image',
					'type' => 'tinytext',
					'null' => true,
					'null' => false,
				),
				array(
					'name' => 'description',
					'type' => 'varchar',
					'size' => 255,
					'null' => false,
				),
				array(
					'name' => 'status',
					'type' => 'smallint',
					'default' => 1,
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('catid'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		// Shop logs
		$tables[] = array(
			'table_name' => '{db_prefix}shop_log_buy',
			'columns' => array(
				array(
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'invid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'userid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'sellerid',
					'type' => 'int',
					'size' => 10,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'fee',
					'type' => 'int',
					'size' => 10,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'date',
					'type' => 'int',
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		$tables[] = array(
			'table_name' => '{db_prefix}shop_log_gift',
			'columns' => array(
				array(
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'userid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'receiver',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'invid',
					'type' => 'int',
					'size' => 10,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'message',
					'type' => 'varchar',
					'size' => 255,
					'null' => false,
				),
				array(
					'name' => 'is_admin',
					'type' => 'tinyint',
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'date',
					'type' => 'int',
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		$tables[] = array(
			'table_name' => '{db_prefix}shop_log_bank',
			'columns' => array(
				array(
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'userid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'fee',
					'type' => 'int',
					'size' => 10,
					'default' => 0,
					'null' => false,
				),
				array(
					'name' => 'type',
					'type' => 'smallint',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'date',
					'type' => 'int',
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);
		$tables[] = array(
			'table_name' => '{db_prefix}shop_log_games',
			'columns' => array(
				array(
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'null' => false,
				),
				array(
					'name' => 'userid',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'null' => false,
				),
				array(
					'name' => 'game',
					'type' => 'tinytext',
					'null' => false,
				),
				array(
					'name' => 'date',
					'type' => 'int',
					'null' => false,
				),
			),
			'indexes' => array(
				array(
					'type' => 'primary',
					'columns' => array('id'),
				),
			),
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => array(),
		);

		// Installing
		foreach ($tables as $table)
			$smcFunc['db_create_table']($table['table_name'], $table['columns'], $table['indexes'], $table['parameters'], $table['if_exists'], $table['error']);


		// Add some columns for board options
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			array(
				'name' => 'Shop_credits_count',
				'type' => 'tinyint',
				'default' => 1,
			)
		);
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			array(
				'name' => 'Shop_credits_topic',
				'type' => 'int',
				'default' => 0,
			)
		);
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			array(
				'name' => 'Shop_credits_post',
				'type' => 'int',
				'default' => 0,
			)
		);
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			array(
				'name' => 'Shop_credits_bonus',
				'type' => 'tinyint',
				'default' => 0,
			)
		);

		// Add a column for money
		$smcFunc['db_add_column'](
			'{db_prefix}members', 
			array(
				'name' => 'shopMoney',
				'type' => 'int',
				'default' => 0,
			)
		);
		// Add a column for banked money
		$smcFunc['db_add_column'](
			'{db_prefix}members', 
			array(
				'name' => 'shopBank',
				'type' => 'int',
				'default' => 0,
			)
		);
		// Add a column for hide inventory
		$smcFunc['db_add_column'](
			'{db_prefix}members',
			array(
				'name' => 'shopInventory_hide',
				'type' => 'int',
				'default' => 0,
			)
		);
		// Add a column for games pass
		$smcFunc['db_add_column'](
			'{db_prefix}members', 
			array(
				'name' => 'gamesPass',
				'type' => 'int',
				'default' => 0,
			)
		);

		// Check for any categories, or create a 'default' category
		$categories = $smcFunc['db_query']('', '
			SELECT catid
			FROM {db_prefix}shop_categories',
			array()
		);
		$has_categories = $smcFunc['db_num_rows']($categories);
		$smcFunc['db_free_result']($categories);
		// Default category 
		if ($has_categories == 0)
		{
			$smcFunc['db_insert'](
				'ignore',
				'{db_prefix}shop_categories',
				// Fields
				array(
					'name' => 'string',
					'image' => 'string',
					'description' => 'string',
				),
				// Values
				array(
					// Default category
					array(
						'Default',
						'Boardarrow.gif',
						'This is the default category'
					),
				),
				array()
			);
		}

		// Check if there are items, if not, proceed
		$items = $smcFunc['db_query']('', '
			SELECT itemid
			FROM {db_prefix}shop_items',
			array()
		);
		$has_items = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);
		// Default items 
		if ($has_items == 0)
		{
			$smcFunc['db_insert'](
				'ignore',
				'{db_prefix}shop_items',
				// Fields
				array(
					'name' => 'string',
					'image' => 'string',
					'description' => 'string',
					'price' => 'int',
					'count' => 'int',
					'function' => 'string',
					'catid' => 'int',
					'status' => 'int',
				),
				// Values
				array(
					// Sample item
					array(
						'name' => 'Default item',
						'image' => 'Bear.gif',
						'description' => 'The very first item of your shop',
						'price' => 75,
						'count' => 50,
						'function' => 'Default',
						'catid' => 1,
						'status' => 1,
					),
				),
				array()
			);
		}

		// Let's add some modules shall we
		$modules = $smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}shop_modules',
			array()
		);
		$has_modules = $smcFunc['db_num_rows']($modules);
		$smcFunc['db_free_result']($modules);
		// Default items 
		if ($has_modules == 0)
		{
			$smcFunc['db_insert'](
				'ignore',
				'{db_prefix}shop_modules',
				// Fields
				array(
					'name' => 'string',
					'description' => 'string',
					'price' => 'int',
					'author' => 'string',
					'email' => 'string',
					'require_input' => 'int',
					'editable_input' => 'int',
					'can_use' => 'int',
					'web' => 'string',
					'file' => 'string',
				),
				// Values
				array(
					array(
						'name' => 'Add xxx to Post Count',
						'description' => 'Increase your Post Count by xxx!',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'AddToPostCount',
					),
					array(
						'name' => 'Change Display Name',
						'description' => 'Change your display name',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'ChangeDisplayName',
					),
					array(
						'name' => 'Random Money (between xxx and xxx)',
						'description' => 'Get a random amount of money, between xxx and xxx!',
						'price' => 75,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'RandomMoney',
					),
					array(
						'name' => 'Steal Credits',
						'description' => 'Try to steal credits from another member!',
						'price' => 50,
						'author' => 'Diego Andrés',
						'email' => 'admin@smftricks.com',
						'require_input' => 1,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://www.smftricks.com/',
						'file' => 'Steal',
					),
					array(
						'name' => 'Decrease Posts by xxx',
						'description' => 'Decrease <i>Someone else\'s</i> post count by xxx!!',
						'price' => 200,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'DecreasePost',
					),
					array(
						'name' => 'Games Room Pass xxx days',
						'description' => 'Allows access to Games Room for xxx days',
						'price' => 50,
						'author' => 'wdm2005',
						'email' => 'wdm2005@blueyonder.co.uk',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://sleepy-arcade.ath.cx/',
						'file' => 'GamesPass',
					),
					array(
						'name' => 'Increase Total Time by xxx',
						'description' => 'Increase your total time logged in by xxx (default is 12 hours)',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'IncreaseTimeLoggedIn',
					),
					array(
						'name' => 'Sticky Topic',
						'description' => 'Make any one of your topics a sticky!',
						'price' => 400,
						'author' => 'Diego Andrés',
						'email' => 'admin@smftricks.com',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use' => 1,
						'web' => 'http://www.smftricks.com/',
						'file' => 'StickyTopic',
					),
					array(
						'name' => 'Change User Title',
						'description' => 'Change your user title',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'ChangeUserTitle',
					),
					array(
						'name' => 'Change Username',
						'description' => 'Change your Username!',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use' => 1,
						'web' => 'http://www.dansoftaustralia.net/',
						'file' => 'ChangeUsername',
					),
					array(
						'name' => 'Change Other\'s Title',
						'description' => 'Change someone else\'s title',
						'price' => 200,
						'author' => 'Diego Andrés',
						'email' => 'admin@smftricks.com',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use' => 1,
						'web' => 'http://www.smftricks.com/',
						'file' => 'ChangeOtherTitle',
					),
				),
				array()
			);

		}
	}

	// So... looking for something new
	$hooks = array(
		'integrate_pre_include' => '$sourcedir/Shop/Subs-Shop.php',
		'integrate_pre_load' => 'Shop::initialize',
	);

	foreach ($hooks as $hook => $function)
		add_integration_function($hook, $function, true);