<?php
define("LILO_MODULE_DIR", 'modules');

define("LILO_MODULE_ALIAS", serialize(array(
	'ui'				=> '000_user_interface',
	'user'			=> '001_user_management',
	'asset'			=> '002_asset_management',
	'avatar'		=> '003_avatar_editor',
	'friend'		=> '004_friends',
	'message'		=> '005_messaging',	// shout, direct message, chat
	'quest'			=> '006_quest_engine',
	'report'		=> '007_statistic_reporting_service',
	'admin'			=> '008_administrator_page',
	'log'				=> '009_log',
	'server'		=> '010_game_server_management',
	'article'		=> '011_articles'
)));

?>
