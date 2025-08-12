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

namespace admpext\toolkit\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_data()
	{
		return array(
			// Add the ACP module to Board Configuration
			array('module.add', array(
				'acp',
				'ACP_SERVER_CONFIGURATION',
				array(
					'module_basename'	=> '\admpext\toolkit\acp\toolkit_module',
					'modes'				=> array('admp_security'),
				),
			)),
			array('config.add', array('admp_token_created', 0)),
			array('config.add', array('admp_token_updated', 0)),
			array('custom', array(array($this, 'admp_token_delete'))),
		);
	}

	public function admp_token_delete()
	{
		global $phpbb_root_path;

		$ext_filepath = $phpbb_root_path . 'ext/admpext/toolkit/.store';
		$admp_filepath = $phpbb_root_path . 'admp/includes/.store';

		if (file_exists($ext_filepath))
		{
			foreach (glob("$ext_filepath*.json") as $filename)
			{
				unlink($filename);
			}
		}

		if (file_exists($admp_filepath))
		{
			foreach (glob("$admp_filepath*.json") as $filename)
			{
				unlink($filename);
			}
		}
	}
}
