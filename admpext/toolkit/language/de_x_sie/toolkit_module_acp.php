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
	'ACP_TOOLKIT_ADMP_INDEX'			=> 'AdminPlus Übersicht',
	'ACP_TOOLKIT_TOKEN_EXPLAIN'			=> 'Hier werden für den Gründer Sicherheits-Token generiert. Diese werden zum Anmelden im AdminPlus (ADMP) benötigt, wenn die Datenbank-Verbindung nicht besteht bzw . beschädigt ist.<br>
		!!! Kopieren Sie diese Sicherheits-Token und speichern diese an einem sicheren Ort !!!',
	'ACP_TOOLKIT_BOARD_FOUNDER_ONLY'	=> 'Nur Gründer haben Zugriff auf die AdminPlus Token.',
	'ACP_TOOLKIT_DIR_NOT_FOUND'			=> 'Das AdminPlus ist nicht vorhanden!',
	'ACP_TOOLKIT_TOKEN_CREATED'			=> 'phpBB Token erstellt',
	'ACP_TOOLKIT_TOKEN_UPDATED'			=> 'phpBB Token aktualisiert',
	'ACP_TOOLKIT_TOKEN_NOT_UPDATED'		=> 'Noch nie',
	'ACP_TOOLKIT_BOARD_TOKEN'			=> 'phpBB Token',
	'ACP_TOOLKIT_BOARD_DIR'				=> 'phpBB Token-Verzeichnis',
	'ACP_TOOLKIT_ADMP_DIR'				=> 'AdminPlus Token-Verzeichnis',
	'ACP_TOOLKIT_TOKEN_DIRS'			=> 'Token-Verzeichnisse',
	'ACP_TOOLKIT_STORE_TOKEN'			=> 'Kopieren Sie diese Notfall Anmelde-Daten',
	'ACP_TOOLKIT_EMERGENCY'				=> 'Notfall Anmelde-Daten',
	'ACP_TOOLKIT_EMERGENCY_CHECK'		=> 'Notfall Token',
	'ACP_TOOLKIT_COPY_FAIL'				=> 'Das kopieren der Token-Datei in das Verzeichnis „%s“ schlug fehl. Prüfen Sie bitte ob das Verzeichnis beschreibbar bzw vorhanden ist.',
	'ACP_TOOLKIT_NO_PARAM_DIR'			=> 'Verzeichnis „%s“ wurde nicht gefunden. Bitte prüfen Sie ob das Verzeichnis beschreibbar bzw vorhanden ist.',

	'ACP_TOOLKIT_BOARD_TOKEN_CHECK'		=> array(
		1 =>	'Token sind identisch',
		2 =>	'Token sind nicht identisch',
	),

	'ACP_TOOLKIT_BOARD_TOKEN_HIDDEN'	=> array(
		1 =>	'Ist nicht sichtbar. Richtig!',
		2 =>	'Ist sichtbar. Falsch! Verzeichnis darf nicht sichtbar sein. Deswegen muss vor dem Verzeichnisnamen ein Punkt sein.',
	),

	'ACP_TOOLKIT_BOARD_TOKEN_DIR'		=> array(
		1 =>	'Ist vorhanden',
		2 =>	'Ist nicht vorhanden',
	),

	'ACP_TOOLKIT_ADMP_TOKEN_HIDDEN'		=> array(
		1 =>	'Ist nicht sichtbar. Richtig!',
		2 =>	'Ist sichtbar. Falsch! Verzeichnis darf nicht sichtbar sein. Deswegen muss vor dem Verzeichnisnamen ein Punkt sein.',
	),

	'ACP_TOOLKIT_ADMP_TOKEN_DIR'		=> array(
		1 =>	'Ist vorhanden',
		2 =>	'Ist nicht vorhanden',
	),
));
