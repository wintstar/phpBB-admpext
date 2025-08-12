<?php
/**
 *
 * Board Announcements extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2023 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace admpext\toolkit\controller;

use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

class acp_controller
{
	const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
	const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const NUMBER = '123456789';
	const SYMBOL = '!@#$%^&*=.';

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\filesystem The filesystem object */
	protected $filesystem;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $u_action;

	/** @var string */
	protected $extstore;

		/** @var string */
	protected $admpstore;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config
	 * @param language $language
	 * @param \phpbb\filesystem\filesystem $filesystem
	 * @param request $request
	 * @param template $template
	 * @param user $user
	 * @param $phpbb_root_path
	 * @param $php_ext
	 */
	public function __construct(\phpbb\config\config $config, language $language, \phpbb\filesystem\filesystem $filesystem, request $request, template $template, user $user, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->language = $language;
		$this->filesystem = $filesystem;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;

		/* !! Hidden directory .s and .s !!!! Point is is required !!!! */
		$this->extstore = $this->phpbb_root_path . 'ext/admpext/toolkit/.store/';
		$this->admpstore = $this->phpbb_root_path . 'admp/includes/.store/';

		$language->add_lang('toolkit_module_acp', 'admpext/toolkit');
	}

	/**
	 * Main ACP module
	 *
	 * @throws \Exception
	 */
	public function display()
	{
		$action = $this->request->variable('action', '');

		if ($this->user->data['user_type'] != USER_FOUNDER)
		{
			trigger_error('ACP_TOOLKIT_BOARD_FOUNDER_ONLY', E_USER_WARNING);
		}

		if (!file_exists($this->phpbb_root_path . 'admp'))
		{
			trigger_error('ACP_TOOLKIT_DIR_NOT_FOUND', E_USER_WARNING);
		}

		if (!file_exists($this->extstore))
		{
			$path = str_replace(DIRECTORY_SEPARATOR, '/', $this->extstore);
			$this->filesystem->mkdir($path, 493);

			$this->safe_dir($this->extstore);
		}

		if (!file_exists($this->admpstore))
		{
			$path = str_replace(DIRECTORY_SEPARATOR, '/', $this->admpstore);
			$this->filesystem->mkdir($path, 493);

			$this->safe_dir($this->admpstore);
		}

		if ($action === 'newtoken')
		{
			$this->delete_token();

			$this->config->set('admp_token_updated', time());

			redirect(append_sid("{$this->phpbb_root_path}adm/index.$this->php_ext", 'i=-admpext-toolkit-acp-toolkit_module&amp;mode=admp_security'));
		}

		$this->set_token_config();
		$token_view = $this->view_token();
		$token_check = $this->token_check();
		$board_dir = $this->token_board_dir();
		$admp_dir = $this->token_admp_dir();

		if (!empty($token_check['error']))
		{
			trigger_error($this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_CHECK'), 2) . adm_back_link($this->u_action . '&amp;action=newtoken'), E_USER_WARNING);
		}

		$u_toolkit_admp = $this->phpbb_root_path . 'admp/index.' . $this->php_ext;

		$this->template->assign_vars(array(
			'USERNAME'					=> $this->user->data['username'],
			'EMERGENCY_TOKEN'			=> (string) $token_view,
			'EMERGENCY_TOKEN_MSG'		=> (string) $token_check['message'],
			'EMERGENCY_TOKEN_ERROR'		=> (string) $token_check['error'],

			'BOARD_DIR_MSG'				=> implode('<br>', $board_dir['message']),
			'BOARD_DIR_ERROR'			=> implode('<br>', $board_dir['error']),
			'ADMP_DIR_MSG'				=> implode('<br>', $admp_dir['message']),
			'ADMP_DIR_ERROR'			=> implode('<br>', $admp_dir['error']),
			'ADMP_TOKEN_CREATED'		=> !empty($this->config['admp_token_created']) ? $this->user->format_date($this->config['admp_token_created']) : '',
			'ADMP_TOKEN_UPDATED'		=> !empty($this->config['admp_token_updated']) ? $this->user->format_date($this->config['admp_token_created']) :  $this->language->lang('ACP_TOOLKIT_TOKEN_NOT_UPDATED'),

			'U_TOOLKIT_ADMP'			=> $u_toolkit_admp,

			'S_TOOLKIT'					=> true,
			'S_BOARD_DIR_MSG'			=> ($board_dir['message']) ? true : false,
			'S_BOARD_DIR_ERROR'			=> ($board_dir['error']) ? true : false,
			'S_ADMP_DIR_MSG'			=> ($admp_dir['message']) ? true : false,
			'S_ADMP_DIR_ERROR'			=> ($admp_dir['error']) ? true : false,
			'S_EMERGENCY_TOKEN_MSG'		=> ($token_check['message']) ? true : false,
			'S_EMERGENCY_TOKEN_ERROR'	=> ($token_check['error']) ? true : false,
		));
	}

	private function get_token(): array
	{
		$filepath = $this->extstore;

		$json_data = array();
		foreach (glob("$filepath*.json") as $filename)
		{
			$path_parts = pathinfo($filename);
			$username = $this->decrypt($path_parts['basename']);

			if ($username == $this->user->data['username'])
			{
				$json_data[] = $this->read_json_data($filepath, $path_parts['filename']);
			}
		}

		$token_data = array();

		if (count($json_data) !=0)
		{
			foreach ($json_data as $row)
			{
				foreach ($row as $key => $value)
				{
					$token_data[$this->decrypt($key)] = $value;
				}
			}
		}

		return $token_data;
	}

	private function view_token()
	{
		$token_data = $this->get_token();

		$list = '';
		$list .= "". $this->language->lang('USERNAME').$this->language->lang('COLON') . "&nbsp;&nbsp;" . $token_data[$this->user->data['username']] ."<br>";
		$list .= "". $this->language->lang('PASSWORD').$this->language->lang('COLON') . "&nbsp;&nbsp;&emsp;&emsp;&emsp;&emsp;" . $token_data['password_token'] ."<br>";
		$list .= "". $this->language->lang('ACP_TOOLKIT_BOARD_TOKEN').$this->language->lang('COLON') . "&nbsp;&emsp;&emsp;" . $token_data['board_token_file'] ."<br>";

		return $list;
	}

	/**
	* set_token_config
	*
	* @return void
	*/
	private function set_token_config(): void
	{
		$user_set 	= false;

		$store = $this->admpstore;
		$extstore = $this->extstore;

		$this->config->set('admp_token_created', time());

		try
		{
			$this->filesystem->phpbb_chmod($store, \phpbb\filesystem\filesystem_interface::CHMOD_READ | \phpbb\filesystem\filesystem_interface::CHMOD_WRITE);
		}
		catch (\phpbb\filesystem\exception\filesystem_exception $e)
		{
			// Do nothing
		}

		try
		{
			$this->filesystem->phpbb_chmod($extstore, \phpbb\filesystem\filesystem_interface::CHMOD_READ | \phpbb\filesystem\filesystem_interface::CHMOD_WRITE);
		}
		catch (\phpbb\filesystem\exception\filesystem_exception $e)
		{
			// Do nothing
		}

		$json_data = array();
		foreach (glob("$extstore*.json") as $filename)
		{
			$path_parts = pathinfo($filename);
			$username = $this->decrypt($path_parts['basename']);

			if ($username == $this->user->data['username'])
			{
				$user_set = true;
			}
		}

		if ($user_set == false)
		{
			$u			= $this->encrypt($this->user->data['username'], true);
			$btk		= $this->encrypt('3jO8SL');
			$ptk		= $this->encrypt('6sF.WR');
			$ptkh		= $this->encrypt('f@OLE7');
			$pw			= $this->gen_rand_chars(16);

			$optionen = array(
				'cost' => 13,
			);

			$pwh = password_hash($pw, PASSWORD_BCRYPT, $optionen);

			$path = $this->extstore . $u . '.json';

			$set = array(
				$u		=> $this->gen_rand_chars(16),
				$btk	=> $this->create_board_token(),
				$ptk	=> $pw,
				$ptkh	=> $pwh,
			);

			$this->write_json_data($path, $set);
		}

		foreach (glob("$extstore*.json") as $cpyfile)
		{
			$path_parts = pathinfo($cpyfile);

			$username = $this->decrypt($path_parts['basename']);

			if ($username == $this->user->data['username'])
			{
				$to = $this->admpstore . $path_parts['basename'];

				if (!file_exists($to))
				{
					if (!$this->filesystem->copy($cpyfile, $to))
					{
						redirect($this->u_action);
					}

					try
					{
						$this->filesystem->phpbb_chmod($store, \phpbb\filesystem\filesystem_interface::CHMOD_READ);
					}
					catch (\phpbb\filesystem\exception\filesystem_exception $e)
					{
						// Do nothing
					}

					try
					{
						$this->filesystem->phpbb_chmod($extstore, \phpbb\filesystem\filesystem_interface::CHMOD_READ);
					}
					catch (\phpbb\filesystem\exception\filesystem_exception $e)
					{
						// Do nothing
					}

					redirect($this->u_action);
				}
			}
		}
	}

	private function create_board_token()
	{
		$token = '';
		$server_name = $this->request->server('SERVER_NAME');
		$secret_key = $this->gen_rand_chars(12);
		$key_name = $this->user->data['username'];

		/* Create a part of token using secretKey and other stuff */
		$token_generic = $secret_key.$server_name; // It can be 'stronger' of course

		/* Encoding token */
		$token = hash('sha256', $token_generic.$key_name);

		return $token;
	}

	private function delete_token()
	{
		$ext_filepath = $this->extstore;
		$admp_filepath = $this->admpstore;

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

	/**
	* encrypt
	*
	* @param  int $id
	* @param  bool $count
	* @return string
	*/
	private function encrypt(mixed $value, bool $hex = false): string
	{
		if ($hex)
		{
			$chars = $this->get_bytes_from_string(self::LOWERCASE.self::NUMBER.self::UPPERCASE.self::SYMBOL, 10);
			$value = base64_encode($value);
			$chars_one = $this->get_bytes_from_string(self::LOWERCASE.self::NUMBER.self::UPPERCASE.self::SYMBOL, 10);
		}
		else
		{
			$chars = $this->get_bytes_from_string(self::LOWERCASE.self::NUMBER.self::UPPERCASE.self::SYMBOL, 6);
			$chars_one = $this->get_bytes_from_string(self::LOWERCASE.self::NUMBER.self::UPPERCASE.self::SYMBOL, 6);
		}

		return sprintf('%s_%s_%s', $chars, $value, $chars_one);
	}

	/**
	 * decrypt
	 *
	 * @param  string $key
	 * @return int
	 */
	private function decrypt(mixed $key): string
	{
		$array = explode('_', $key);

		switch ($array[1])
		{
			case '3jO8SL':
				return 'board_token_file';
				break;

			case '6sF.WR':
				return 'password_token';
				break;

			case 'f@OLE7':
				return 'password_hash';
				break;

			default:
				return base64_decode($array[1]);
				break;
		}
	}

	private function token_check(): array
	{
		$filepath = $this->extstore;

		$message = '';
		$error = '';
		foreach (glob("$filepath*.json") as $filename)
		{
			$path_parts = pathinfo($filename);
			$username = $this->decrypt($path_parts['basename']);

			if ($username == $this->user->data['username'])
			{
				$extpath = $this->extstore . $path_parts['basename'];
				$admppath = $this->admpstore . $path_parts['basename'];

				if (file_exists($extpath))
				{
					@$extstore = file_get_contents($extpath);
					@$admpstore = file_get_contents($admppath);

					if ($admpstore == $extstore)
					{
						$message = $this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_CHECK'), 1);
					}
					else
					{
						$error = $this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_CHECK'), 2);
					}
				}
			}
		}

		return array('message' => $message, 'error' => $error);
	}

	private function token_board_dir(): array
	{
		$filepath = $this->extstore;

		$message = array();
		$error = array();
		foreach (glob("$filepath*.json") as $filename)
		{
			$path_parts = pathinfo($filename);
			$username = $this->decrypt($path_parts['basename']);

			if ($username == $this->user->data['username'])
			{
				$extpath = $filepath . $path_parts['basename'];
				$dir_perm = substr(sprintf('%o', fileperms($extpath)), -4);

				if (file_exists($extpath))
				{
					$message[] = $this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_DIR'), 1);

					if (basename($path_parts['dirname']) == '.store')
					{
						$message[] = $this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_HIDDEN'), 1);
					}
					else
					{
						$error[] = $this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_HIDDEN'), 2);
					}
				}
				else
				{
					$error[] = $this->language->lang(array('ACP_TOOLKIT_BOARD_TOKEN_DIR'), 2);
				}
			}
		}

		return array('message' => $message, 'error' => $error);
	}

	private function token_admp_dir(): array
	{
		$filepath = $this->admpstore;

		$message = array();
		$error = array();
		foreach (glob("$filepath*.json") as $filename)
		{
			$path_parts = pathinfo($filename);
			$username = $this->decrypt($path_parts['basename']);

			if ($username == $this->user->data['username'])
			{
				$admppath = $this->admpstore . $path_parts['basename'];
				$dir_perm = substr(sprintf('%o', fileperms($admppath)), -4);

				if (file_exists($admppath))
				{
					$message[] = $this->language->lang(array('ACP_TOOLKIT_ADMP_TOKEN_DIR'), 1);

					if (basename($path_parts['dirname']) == '.store')
					{
						$message[] = $this->language->lang(array('ACP_TOOLKIT_ADMP_TOKEN_HIDDEN'), 1);
					}
					else
					{
						$error[] = $this->language->lang(array('ACP_TOOLKIT_ADMP_TOKEN_HIDDEN'), 2);
					}
				}
				else
				{
					$error[] = $this->language->lang(array('ACP_TOOLKIT_ADMP_TOKEN_DIR'), 2);
				}
			}
		}

		return array('message' => $message, 'error' => $error);
	}

	/**
	* Generated random chars
	*
	*
	* @param  int $key
	* @return string
	*/
	protected function gen_rand_chars(int $key = 8): string
	{
		$output = '';
		$bytes = random_bytes($key);

		$output = bin2hex($bytes);

		return $output;
	}

	protected function get_bytes_from_string(string $key_space, int $length): string
	{
		$key_space_length = strlen($key_space);
		$string = '';
		for ($i = 0; $i < $length; $i++)
		{
			$string .= $key_space[random_int(0, $key_space_length - 1)];
		}
		return $string;
	}

	/**
	 * Write data in json file
	 *
	 * @param  mixed $path
	 * @param  mixed $data
	 */
	protected function write_json_data(string $path, array $data): void
	{
		$json_string = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		// Write in the file
		$fp = fopen($path, 'w');
		fwrite($fp, $json_string);
		fclose($fp);
	}

	/**
	 * Read json file
	 *
	 * @param  mixed $filename
	 * @return array
	 */
	protected function read_json_data(string $path, string $filename): array
	{
		$json_string = '';
		$json_data = [];
		$file = $path . $filename . '.json';

		if (file_exists($file)) {
			$json_string = file_get_contents($file);
			$json_data = json_decode($json_string, true);
		}

		return $json_data;
	}

	private function safe_dir($path)
	{
		if ($ht = @fopen($path . '.htaccess', 'wb'))
		{
			fwrite($ht, "# With Apache 2.4 the \"Order, Deny\" syntax has been deprecated and moved from\n");
			fwrite($ht, "# module mod_authz_host to a new module called mod_access_compat (which may be\n");
			fwrite($ht, "# disabled) and a new \"Require\" syntax has been introduced to mod_authz_core.\n");
			fwrite($ht, "# We could just conditionally provide both versions, but unfortunately Apache\n");
			fwrite($ht, "# does not explicitly tell us its version if the module mod_version is not\n");
			fwrite($ht, "# available. In this case, we check for the availability of module\n");
			fwrite($ht, "# mod_authz_core (which should be on 2.4 or higher only) as a best guess.\n");
			fwrite($ht, "<IfModule mod_version.c>\n");
			fwrite($ht, "\t<IfVersion < 2.4>\n");
			fwrite($ht, "\t\tOrder Allow,Deny\n");
			fwrite($ht, "\t\tDeny from All\n");
			fwrite($ht, "\t</IfVersion>\n");
			fwrite($ht, "\t<IfVersion >= 2.4>\n");
			fwrite($ht, "\t\tRequire all denied\n");
			fwrite($ht, "\t</IfVersion>\n");
			fwrite($ht, "</IfModule>\n");
			fwrite($ht, "<IfModule !mod_version.c>\n");
			fwrite($ht, "\t<IfModule !mod_authz_core.c>\n");
			fwrite($ht, "\t\tOrder Allow,Deny\n");
			fwrite($ht, "\t\tDeny from All\n");
			fwrite($ht, "\t</IfModule>\n");
			fwrite($ht, "\t<IfModule mod_authz_core.c>\n");
			fwrite($ht, "\t\tRequire all denied\n");
			fwrite($ht, "\t</IfModule>\n");
			fwrite($ht, "</IfModule>\n");
		}

		@fclose($ht);

		if ($in = @fopen($path . 'index.htm', 'wb'))
		{
			fwrite($in, "<html>\n");
			fwrite($in, "<head>\n");
			fwrite($in, "<title></title>\n");
			fwrite($in, "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n");
			fwrite($in, "</head>\n");
			fwrite($in, "\n");
			fwrite($in, "<body bgcolor=\"#FFFFFF\" text=\"#000000\">\n");
			fwrite($in, "\n");
			fwrite($in, "</body>\n");
			fwrite($in, "</html>");
		}

		@fclose($in);
	}

	/**
	 * Set page url
	 *
	 * @param	string	$u_action	Custom form action
	 * @return	void
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
