<?php
/**
 *
 * @package phpBB Extension - AdminPlus Tooken
 * @version 1.0.0
 * @author St. Frank <webdesign@stephan-frank.de> https://www.stephan-frank.de
 * @copyright (c) 2024 St.Frank
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
*/

namespace admpext\toolkit\acp;

class toolkit_info
{
	function module()
	{
		return array(
			'filename'	=> '\admpext\toolkit\acp\toolkit_module',
			'title'		=> 'ACP_TOOLKIT_TOKEN',
			'modes'		=> array(
				'admp_security'	 => array(
					'title'  => 'ACP_TOOLKIT_TOKEN',
					'auth'   => 'ext_admpext/toolkit && acl_a_server',
					'cat'    => array('ACP_SERVER_CONFIGURATION'),
				),
			),
		);
	}
}
