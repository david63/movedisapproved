<?php
/**
 *
 * @package Move Disapproved Posts
 * @copyright (c) 2020 david63
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace david63\movedisapproved\acp;

class movedisapproved_info
{
	public function module()
	{
		return [
			'filename'	=> '\david63\movedisapproved\acp\movedisapproved_module',
			'title' 	=> 'MOVE_DISAPPROVED',
			'modes' 	=> [
				'main' 	=> ['title' => 'MOVE_DISAPPROVED_MANAGE', 'auth' => 'ext_david63/movedisapproved && acl_a_forum', 'cat' => ['MOVE_DISAPPROVED']],
			],
		];
	}
}
