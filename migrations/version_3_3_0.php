<?php
/**
 *
 * @package Move Disapproved Posts
 * @copyright (c) 2020 david63
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace david63\movedisapproved\migrations;

class version_3_3_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return [
			['config.add', ['move_disapproved_forum', '0']],

			// Add the ACP module
			['module.add', ['acp', 'ACP_CAT_DOT_MODS', 'MOVE_DISAPPROVED']],

			['module.add', [
				'acp', 'MOVE_DISAPPROVED', [
					'module_basename'	=> '\david63\movedisapproved\acp\movedisapproved_module',
					'modes' 			=> ['main'],
				],
			]],
		];
	}
}
