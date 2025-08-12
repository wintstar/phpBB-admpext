<?php
/**
 *
 * @package phpBB Extension - AdminPlus Ext
 * @version 1.0.0
 * @author St. Frank <webdesign@stephan-frank.de> https://www.stephan-frank.de
 * @copyright (c) 2024 St.Frank
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
// Some characters you may want to copy&paste:
// ’ » “ ” …

$lang = array_merge($lang, array(
	// Module
	'ACP_TOOLKIT_TOKEN'					=> 'AdminPlus Token',
	'ACP_TOOLKIT_ADMP_INDEX'			=> 'Adminplus overview',
	'ACP_TOOLKIT_TOKEN_EXPLAIN'			=> 'Safety token is generated here for the founder. These are required to login in the AdminPlus (ADMP) if the database connection does not exist or is damaged.<br>
		!!! Copy these safety tokens and save them in a safe place !!!',
	'ACP_TOOLKIT_BOARD_FOUNDER_ONLY'	=> 'Only founders have access to the AdminPlus token.',
	'ACP_TOOLKIT_DIR_NOT_FOUND'			=> 'AdminPlus does not exist!',
	'ACP_TOOLKIT_TOKEN_CREATED'			=> 'phpBB Token created',
	'ACP_TOOLKIT_TOKEN_UPDATED'			=> 'phpBB Token updated',
	'ACP_TOOLKIT_TOKEN_NOT_UPDATED'		=> 'Never yet',
	'ACP_TOOLKIT_BOARD_TOKEN'			=> 'phpBB Token',
	'ACP_TOOLKIT_BOARD_DIR'				=> 'phpBB token directory',
	'ACP_TOOLKIT_ADMP_DIR'				=> 'AdminPlus token directory',
	'ACP_TOOLKIT_TOKEN_DIRS'			=> 'Token directories',
	'ACP_TOOLKIT_STORE_TOKEN'			=> 'Copy this emergency login data',
	'ACP_TOOLKIT_EMERGENCY'				=> 'Emergency login data',
	'ACP_TOOLKIT_EMERGENCY_CHECK'		=> 'Emergency token',
	'ACP_TOOLKIT_COPY_FAIL'				=> 'Copying the token file into the "%s" directory failed. Please check whether the directory can be described or available.',
	'ACP_TOOLKIT_NO_PARAM_DIR'			=> 'Directory "%s" was not found. Please whether the directory can be described or available.',

	'ACP_TOOLKIT_BOARD_TOKEN_CHECK'		=> array(
		1 =>	'Token are identical',
		2 =>	'Token are not identical',
	),

	'ACP_TOOLKIT_BOARD_TOKEN_HIDDEN'	=> array(
		1 =>	'Is not visible. Correct!',
		2 =>	'Is visible. Incorrect! Directory must not be visible. Therefore, a point must be before the directory name.',
	),

	'ACP_TOOLKIT_BOARD_TOKEN_DIR'		=> array(
		1 =>	'Is available',
		2 =>	'Is not available',
	),

	'ACP_TOOLKIT_ADMP_TOKEN_HIDDEN'		=> array(
		1 =>	'Is not visible. Correct!',
		2 =>	'Is visible. Incorrect! Directory must not be visible. Therefore, a point must be before the directory name.',
	),

	'ACP_TOOLKIT_ADMP_TOKEN_DIR'		=> array(
		1 =>	'Is available',
		2 =>	'Is not available',
	),
));
