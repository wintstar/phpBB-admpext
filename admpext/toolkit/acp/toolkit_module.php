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

class toolkit_module
{
	public $page_title;
	public $tpl_name;

	/**
	 * Main ACP module
	 *
	 * @throws \Exception
	 */
	public function main()
	{
		global $phpbb_container;

		/** @var \admpext\toolkit\controller\acp_controller $acp_controller */
		$acp_controller = $phpbb_container->get('admpext.toolkit.acp.controller');

		// Make the $u_action url available in the admin controller
		$acp_controller->set_page_url($this->u_action);


		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'acp_toolkit';

		// Set the page title for our ACP page
		$this->page_title = 'ACP_TOOLKIT_TOKEN';

		$acp_controller->display();
	}
}
