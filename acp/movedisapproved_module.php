<?php
/**
 *
 * @package Move Disapproved Posts
 * @copyright (c) 2020 david63
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace david63\movedisapproved\acp;

class movedisapproved_module
{
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name   = 'movedisapproved';
		$this->page_title = $phpbb_container->get('language')->lang('MOVE_DISAPPROVED');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('david63.movedisapproved.admin.controller');

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		$admin_controller->display_options();
	}
}
