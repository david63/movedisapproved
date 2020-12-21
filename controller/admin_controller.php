<?php
/**
 *
 * @package Move Disapproved Posts
 * @copyright (c) 2020 david63
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace david63\movedisapproved\controller;

use phpbb\config\config;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbb\language\language;
use phpbb\log\log;
use david63\movedisapproved\core\functions;

/**
 * Admin controller
 */
class admin_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \david63\announceonindex\core\functions */
	protected $functions;

	/** @var string */
	protected $ext_images_path;

	/** @var string Custom form action */
	protected $u_action;

	protected $to_forum;

	/**
	 * Constructor for admin controller
	 *
	 * @param \phpbb\config\config                       $config             Config object
	 * @param \phpbb\request\request                     $request            Request object
	 * @param \phpbb\template\template                   $template           Template object
	 * @param \phpbb\user                                $user               User object
	 * @param \phpbb\language\language                   $language           Language object
	 * @param \phpbb\log\log                             $log                Log object
	 * @param \david63\movedisapproved\core\functions    functions           Functions for the extension
	 * @param string                                     $ext_images_path    Path to this extension's images
	 *
	 * @return \david63\movedisapproved\controller\admin_controller
	 * @access public
	 */
	public function __construct(config $config, request $request, template $template, user $user, language $language, log $log, functions $functions, string $ext_images_path)
	{
		$this->config			= $config;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;
		$this->language			= $language;
		$this->log				= $log;
		$this->functions		= $functions;
		$this->ext_images_path	= $ext_images_path;
	}

	/**
	 * Display the options a user can configure for this extension
	 *
	 * @return null
	 * @access public
	 */
	public function display_options()
	{
		// Add the language files
		$this->language->add_lang(['acp_movedisapproved', 'acp_common'], $this->functions->get_ext_namespace());

		// Create a form key for preventing CSRF attacks
		$form_key = 'move_disapproved';
		add_form_key($form_key);

		$back = false;

		// Is the form being submitted
		if ($this->request->is_set_post('submit'))
		{
			// Is the submitted form is valid
			if (!check_form_key($form_key))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Let's do some error checking
			$this->to_forum = $this->request->variable('move_disapproved_forum', 0);

			// Check that both fora are > 0
			if ($this->to_forum == 0)
			{
				trigger_error($this->language->lang('FORUM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// If no errors, process the form data
			// Set the options the user configured
			$this->set_options();

			// Add option settings change action to the admin log
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'MOVE_DISAPPROVED_LOG');

			// Option settings have been updated and logged
			// Confirm this to the user and provide link back to previous page
			trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		// Template vars for header panel
		$version_data = $this->functions->version_check();

		// Are the PHP and phpBB versions valid for this extension?
		$valid = $this->functions->ext_requirements();

		$this->template->assign_vars([
			'DOWNLOAD' 			=> (array_key_exists('download', $version_data)) ? '<a class="download" href =' . $version_data['download'] . '>' . $this->language->lang('NEW_VERSION_LINK') . '</a>' : '',

			'EXT_IMAGE_PATH' 	=> $this->ext_images_path,

			'HEAD_TITLE' 		=> $this->language->lang('MOVE_DISAPPROVED'),
			'HEAD_DESCRIPTION'	=> $this->language->lang('MOVE_DISAPPROVED_EXPLAIN'),

			'NAMESPACE'			=> $this->functions->get_ext_namespace('twig'),

			'PHP_VALID'			=> $valid[0],
			'PHPBB_VALID'		=> $valid[1],

			'S_BACK' 			=> $back,
			'S_VERSION_CHECK' 	=> (array_key_exists('current', $version_data)) ? $version_data['current'] : false,

			'VERSION_NUMBER' 	=> $this->functions->get_meta('version'),
		]);

		$selected_forum = isset($this->config['move_disapproved_forum']) ? $this->config['move_disapproved_forum'] : 0;

		// Set output vars for display in the template
		$this->template->assign_vars([
			'MOVE_DISAPPROVED_FORUM' => make_forum_select($selected_forum, false, true, true),

			'U_ACTION' => $this->u_action,
		]);
	}

	/**
	 * Set the options a user can configure
	 *
	 * @return null
	 * @access protected
	 */
	protected function set_options()
	{
		$this->config->set('move_disapproved_forum', $this->to_forum);
	}

	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 * @return null
	 * @access public
	 */
	public function set_page_url($u_action)
	{
		return $this->u_action = $u_action;
	}
}
