<?php

/**
 * @package ST Shop
 * @version 4.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

	global $smcFunc, $context;

	db_extend('packages');

	if (empty($context['uninstalling']))
	{
		// Set the importer not completed by default
		$smcFunc['db_insert'](
			'replace',
			'{db_prefix}settings',
			[
				'variable' => 'string',
				'value' => 'int',
			],
			[
				[
					'Shop_importer_success',
					0,
				],
			],
			['variable']
		);

		// Enable the alert by default
		$smcFunc['db_insert'](
			'ignore',
			'{db_prefix}user_alerts_prefs',
			[
				'id_member' => 'int',
				'alert_pref' => 'string',
				'alert_value' => 'int',
			],
			[
				[
					0,
					'shop_usercredits',
					1,
				],
				[
					0,
					'shop_useritems',
					1,
				],
				[
					0,
					'shop_usertraded',
					1,
				],
				[
					0,
					'shop_module_steal',
					1,
				],
			],
			['id_task']
		);

		// Scheduled Task
		$smcFunc['db_insert'](
			'ignore',
			'{db_prefix}scheduled_tasks',
			[
				'time_offset' => 'int',
				'time_regularity' => 'int',
				'time_unit' => 'string',
				'disabled' => 'int',
				'task' => 'string',
				'callable' => 'string',
			],
			[
				[
					'0',
					'1',
					'd',
					'0',
					'shop_bank_interest',
					'Shop\Tasks\Scheduled::bank_interest#',
				],
			],
			['']
		);

		// Shop items
		$tables[] = [
			'table_name' => '{db_prefix}stshop_items',
			'columns' => [
				[
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'name',
					'type' => 'varchar',
					'size' => 50,
					'not_null' => true,
				],	
				[
					'name' => 'image',
					'type' => 'tinytext',
				],
				[
					'name' => 'description',
					'type' => 'varchar',
					'size' => 255,
					'not_null' => true,
				],
				[
					'name' => 'price',
					'type' => 'mediumint',
					'not_null' => true,
					'default' => 0,
					'unsigned' => true,
				],
				[
					'name' => 'stock',
					'type' => 'smallint',
					'not_null' => true,
					'default' => 0,
					'unsigned' => true,
				],
				[
					'name' => 'module',
					'type' => 'int',
					'not_null' => true,
					'size' => 10,
					'default' => 0,
					'unsigned' => true,
				],
				[
					'name' => 'info1',
					'type' => 'int',
				],
				[
					'name' => 'info2',
					'type' => 'int',
				],
				[
					'name' => 'info3',
					'type' => 'int',
				],
				[
					'name' => 'info4',
					'type' => 'int',
				],
				[
					'name' => 'input_needed',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'can_use_item',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'delete_after_use',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'catid',
					'type' => 'int',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'status',
					'type' => 'smallint',
					'default' => 1,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'itemlimit',
					'type' => 'int',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['itemid'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		// Shop modules
		$tables[] = [
			'table_name' => '{db_prefix}stshop_modules',
			'columns' => [
				[
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'name',
					'type' => 'varchar',
					'size' => 50,
					'not_null' => true,
				],	
				[
					'name' => 'description',
					'type' => 'varchar',
					'size' => 255,
					'not_null' => true,
				],
				[
					'name' => 'price',
					'type' => 'int',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'author',
					'type' => 'varchar',
					'size' => 80,
					'not_null' => true,
				],
				[
					'name' => 'email',
					'type' => 'varchar',
					'size' => 255,
					'not_null' => true,
				],
				[
					'name' => 'require_input',
					'type' => 'tinyint',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'can_use_item',
					'type' => 'tinyint',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'editable_input',
					'type' => 'tinyint',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'web',
					'type' => 'varchar',
					'size' => 255,
					'not_null' => true,
				],
				[
					'name' => 'file',
					'type' => 'tinytext',
					'not_null' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		// User inventory
		$tables[] = [
			'table_name' => '{db_prefix}stshop_inventory',
			'columns' => [
				[
					'name' => 'id',
					'type' => 'mediumint',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'userid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],	
				[
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
					'unsigned' => true,
				],
				[
					'name' => 'trading',
					'type' => 'tinyint',
					'size' => 1,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'tradecost',
					'type' => 'int',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'date',
					'type' => 'int',
					'size' => 11,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'tradedate',
					'type' => 'int',
					'size' => 11,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'fav',
					'type' => 'tinyint',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		// Shop Categories
		$tables[] = [
			'table_name' => '{db_prefix}stshop_categories',
			'columns' => [
				[
					'name' => 'catid',
					'type' => 'int',
					'size' => 5,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'name',
					'type' => 'varchar',
					'size' => 50,
					'not_null' => true,
				],	
				[
					'name' => 'image',
					'type' => 'tinytext',
					'not_null' => true,
				],
				[
					'name' => 'description',
					'type' => 'varchar',
					'size' => 255,
					'not_null' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['catid'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		// Shop logs
		$tables[] = [
			'table_name' => '{db_prefix}stshop_log_buy',
			'columns' => [
				[
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'itemid',
					'type' => 'int',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'invid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'userid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'sellerid',
					'type' => 'mediumint',
					'size' => 10,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'fee',
					'type' => 'int',
					'size' => 10,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'date',
					'type' => 'int',
					'not_null' => true,
					'size' => 11,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		$tables[] = [
			'table_name' => '{db_prefix}stshop_log_gift',
			'columns' => [
				[
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'userid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'receiver',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'itemid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'invid',
					'type' => 'mediumint',
					'size' => 10,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'message',
					'type' => 'varchar',
					'size' => 255,
					'not_null' => true,
				],
				[
					'name' => 'is_admin',
					'type' => 'tinyint',
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'date',
					'type' => 'int',
					'not_null' => true,
					'size' => 11,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		$tables[] = [
			'table_name' => '{db_prefix}stshop_log_bank',
			'columns' => [
				[
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'userid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'fee',
					'type' => 'mediumint',
					'size' => 10,
					'default' => 0,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'action',
					'type' => 'tinytext',
					'not_null' => true,
				],
				[
					'name' => 'type',
					'type' => 'smallint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'date',
					'type' => 'int',
					'not_null' => true,
					'size' => 11,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];
		$tables[] = [
			'table_name' => '{db_prefix}stshop_log_games',
			'columns' => [
				[
					'name' => 'id',
					'type' => 'int',
					'size' => 10,
					'auto' => true,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'userid',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'amount',
					'type' => 'int',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'game',
					'type' => 'tinytext',
					'not_null' => true,
				],
				[
					'name' => 'date',
					'type' => 'int',
					'not_null' => true,
					'size' => 11,
					'unsigned' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];

		// Content log
		$tables[] = [
			'table_name' => '{db_prefix}stshop_log_content',
			'columns' => [
				[
					'name' => 'id_msg',
					'type' => 'int',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'id_member',
					'type' => 'mediumint',
					'size' => 10,
					'not_null' => true,
					'unsigned' => true,
				],
				[
					'name' => 'content',
					'type' => 'VARCHAR',
					'size' => 25,
					'not_null' => true,
				],
			],
			'indexes' => [
				[
					'type' => 'primary',
					'columns' => ['id_msg', 'id_member','content'],
				],
			],
			'if_exists' => 'ignore',
			'error' => 'fatal',
			'parameters' => [],
		];

		// Installing
		foreach ($tables as $table)
			$smcFunc['db_create_table']($table['table_name'], $table['columns'], $table['indexes'], $table['parameters'], $table['if_exists'], $table['error']);


		// Add some columns for board options
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			[
				'name' => 'Shop_credits_count',
				'type' => 'tinyint',
				'default' => 1,
			]
		);
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			[
				'name' => 'Shop_credits_topic',
				'type' => 'int',
				'default' => 0,
			]
		);
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			[
				'name' => 'Shop_credits_post',
				'type' => 'int',
				'default' => 0,
			]
		);
		$smcFunc['db_add_column'](
			'{db_prefix}boards', 
			[
				'name' => 'Shop_credits_bonus',
				'type' => 'tinyint',
				'default' => 0,
			]
		);

		// Add a column for money
		$smcFunc['db_add_column'](
			'{db_prefix}members', 
			[
				'name' => 'shopMoney',
				'type' => 'mediumint',
				'default' => 0,
			]
		);
		// Add a column for banked money
		$smcFunc['db_add_column'](
			'{db_prefix}members', 
			[
				'name' => 'shopBank',
				'type' => 'bigint',
				'default' => 0,
			]
		);
		// Add a column for hide inventory
		$smcFunc['db_add_column'](
			'{db_prefix}members',
			[
				'name' => 'shopInventory_hide',
				'type' => 'int',
				'default' => 0,
			]
		);
		// Add a column for games pass
		$smcFunc['db_add_column'](
			'{db_prefix}members', 
			[
				'name' => 'gamesPass',
				'type' => 'int',
				'default' => 0,
			]
		);

		// Check for any categories, or create a 'default' category
		$categories = $smcFunc['db_query']('', '
			SELECT catid
			FROM {db_prefix}stshop_categories',
			[]
		);
		$has_categories = $smcFunc['db_num_rows']($categories);
		$smcFunc['db_free_result']($categories);
		// Default category 
		if ($has_categories == 0)
		{
			$smcFunc['db_insert'](
				'ignore',
				'{db_prefix}stshop_categories',
				[
					'name' => 'string',
					'image' => 'string',
					'description' => 'string',
				],
				[
					// Default category
					[
						'Default',
						'bookshelf.png',
						'This is the default category'
					],
				],
				[]
			);
		}

		// Check if there are items, if not, proceed
		$items = $smcFunc['db_query']('', '
			SELECT itemid
			FROM {db_prefix}stshop_items',
			[]
		);
		$has_items = $smcFunc['db_num_rows']($items);
		$smcFunc['db_free_result']($items);
		// Default items 
		if ($has_items == 0)
		{
			$smcFunc['db_insert'](
				'ignore',
				'{db_prefix}stshop_items',
				[
					'name' => 'string',
					'image' => 'string',
					'description' => 'string',
					'price' => 'int',
					'stock' => 'int',
					'catid' => 'int',
					'status' => 'int',
				],
				[
					// Sample item
					[
						'name' => 'Default item',
						'image' => 'bear.png',
						'description' => 'The very first item of your shop',
						'price' => 75,
						'stock' => 50,
						'catid' => 1,
						'status' => 1,
					],
				],
				[]
			);
		}

		// Let's add some modules shall we
		$modules = $smcFunc['db_query']('', '
			SELECT id
			FROM {db_prefix}stshop_modules',
			[]
		);
		$has_modules = $smcFunc['db_num_rows']($modules);
		$smcFunc['db_free_result']($modules);
		// Default items 
		if ($has_modules == 0)
		{
			$smcFunc['db_insert'](
				'ignore',
				'{db_prefix}stshop_modules',
				[
					'name' => 'string',
					'description' => 'string',
					'price' => 'int',
					'author' => 'string',
					'email' => 'string',
					'require_input' => 'int',
					'can_use_item' => 'int',
					'editable_input' => 'int',
					'web' => 'string',
					'file' => 'string',
				],
				[
					[
						'name' => 'Increase Post Count',
						'description' => 'Increase the post count by \'x\'',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'IncreasePostCount',
					],
					[
						'name' => 'Change Display Name',
						'description' => 'Change your display name',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'ChangeDisplayName',
					],
					[
						'name' => 'Random Money',
						'description' => 'Get a random amount of money betwen \'x\' and \'y\'',
						'price' => 75,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'RandomMoney',
					],
					[
						'name' => 'Steal Credits',
						'description' => 'Attempt to steal from another member',
						'price' => 50,
						'author' => 'Diego Andrés',
						'email' => 'admin@smftricks.com',
						'require_input' => 1,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://smftricks.com',
						'file' => 'Steal',
					],
					[
						'name' => 'Decrease Posts by xxx',
						'description' => 'Decrease <i>Someone else\'s</i> post count by xxx!!',
						'price' => 200,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'DecreasePost',
					],
					[
						'name' => 'Games Room Pass',
						'description' => 'Gives access to Games Room for \'x\' days',
						'price' => 50,
						'author' => 'Sleepy Arcade',
						'email' => 'wdm2005@blueyonder.co.uk',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://www.simplemachines.org/community/index.php?action=profile;u=84438',
						'file' => 'GamesPass',
					],
					[
						'name' => 'Increase Total Time logged In',
						'description' => 'Increase your total time logged in by \'x\' hours',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 0,
						'editable_input' => 1,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'IncreaseTimeLoggedIn',
					],
					[
						'name' => 'Sticky Topic',
						'description' => 'Make any one of your topics a sticky',
						'price' => 400,
						'author' => 'Diego Andrés',
						'email' => 'admin@smftricks.com',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use_item' => 1,
						'web' => 'https://smftricks.com',
						'file' => 'StickyTopic',
					],
					[
						'name' => 'Change User Title',
						'description' => 'Allows you to change your title',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'ChangeUserTitle',
					],
					[
						'name' => 'Change Username',
						'description' => 'Change your username',
						'price' => 50,
						'author' => 'Daniel15',
						'email' => 'dansoft@dansoftaustralia.net',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use_item' => 1,
						'web' => 'https://github.com/Daniel15',
						'file' => 'ChangeUsername',
					],
					[
						'name' => 'Change Someone Else\'s Title',
						'description' => 'Allows you to change someone else\'s title',
						'price' => 200,
						'author' => 'Diego Andrés',
						'email' => 'admin@smftricks.com',
						'require_input' => 1,
						'editable_input' => 0,
						'can_use_item' => 1,
						'web' => 'https://smftricks.com',
						'file' => 'ChangeOtherTitle',
					],
				],
				[]
			);

		}
	}