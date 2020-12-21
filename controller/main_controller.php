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
use phpbb\user;
use phpbb\db\driver\driver_interface;

class main_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string PHP extension */
	protected $phpEx;

	/** @var string phpBB tables */
	protected $tables;

	/**
	 * Constructor for main controller
	 *
	 * @param \phpbb\config\config				$config		Config object
	 * @param \phpbb\user						$user		User object
	 * @param \phpbb\db\driver\driver_interface	$db			Db object
	 * @param string							$root_path	phpBB root path
	 * @param string							$php_ext	phpBB extension
	 * @param array	                            $tables		phpBB db tables
	 *
	 * @return \david63\movedisapproved\controller\main_controller
	 * @access public
	 */
	public function __construct(config $config, user $user, driver_interface $db, string $root_path, string $php_ext, array $tables)
	{
		$this->config		= $config;
		$this->user			= $user;
		$this->db  			= $db;
		$this->root_path	= $root_path;
		$this->phpEx		= $php_ext;
		$this->tables		= $tables;
	}

	/**
	 * Controller for movedisapproved
	 *
	 * @param string     $name
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function move_disapproved($event)
	{
		$topic_information = $event['topic_information'];

		if (!class_exists('parse_message'))
		{
			include($this->root_path . 'includes/message_parser.' . $this->phpEx);
		}

		$message_parser = new \parse_message();

		foreach ($topic_information as $topic => $topic_data)
		{
			$message_parser->message = $topic_data['post_text'];

			$data_ary = [
				'topic_title'				=> $topic_data['topic_title'],
				'topic_first_post_id'		=> $topic_data['topic_first_post_id'],
				'topic_last_post_id'		=> $topic_data['topic_last_post_id'],
				'topic_time_limit'			=> $topic_data['topic_time_limit'],
				'topic_attachment'			=> $topic_data['topic_attachment'],
				'post_id'					=> (int) $topic_data['post_id'],
				'topic_id'					=> (int) $topic_data['topic_id'],
				'forum_id'					=> (int) $this->config['move_disapproved_forum'],
				'icon_id'					=> (int) $topic_data['icon_id'],
				'poster_id'					=> (int) $topic_data['poster_id'],
				'enable_sig'				=> (bool) $topic_data['enable_sig'],
				'enable_bbcode'				=> (bool) $topic_data['enable_bbcode'],
				'enable_smilies'	   		=> (bool) $topic_data['enable_smilies'],
				'enable_urls'				=> true,
				'enable_indexing'			=> (bool) $topic_data['enable_indexing'],
				'message_md5'				=> (string) md5($message_parser->message),
				'post_checksum'				=> $topic_data['post_checksum'],
				'post_edit_reason'			=> $topic_data['post_edit_reason'],
				'post_edit_user'			=> $topic_data['post_edit_user'],
				'post_time'					=> $topic_data['post_time'],
				'notify'					=> false,
				'notify_set'				=> false,
				'poster_ip'					=> $topic_data['poster_ip'],
				'post_edit_locked'			=> (int) $topic_data['post_edit_locked'],
				'bbcode_bitfield'			=> $message_parser->bbcode_bitfield,
				'bbcode_uid'				=> $message_parser->bbcode_uid,
				'message'					=> $message_parser->message,
				'topic_status'				=> $topic_data['topic_status'],
				'topic_visibility'			=> true,
				'post_visibility'			=> true,
				'force_approved_state'		=> true,
				'original_poster_id'		=> (int) $topic_data['poster_id'],
				'original_poster_ip'		=> $topic_data['poster_ip'],
				'original_poster_name'		=> $topic_data['username'],
				'original_poster_colour'	=> $topic_data['user_colour'],
			];

			$poll_ary = [];

			submit_post('post', $topic_data['post_subject'], '', POST_NORMAL, $poll_ary, $data_ary);
		}
	}

	/**
	 * Update the post with the disapproved data
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function update_post_data($event)
	{
		$data = $event['data'];

		if ($this->config['move_disapproved_forum'] > 0 && array_key_exists('original_poster_id', $data))
		{
			// Update the topics table
			$sql_ary = [
				'topic_last_poster_name'	=> $data['original_poster_name'],
				'topic_last_poster_colour'	=> $data['original_poster_colour'],
			];

			$sql	= 'UPDATE ' . $this->tables['topics'] . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE topic_id = ' . $data['topic_id'] . '';
			$result	= $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			unset($sql_ary);

			// Update the forums table
			$sql_ary = [
				'forum_last_poster_name'	=> $data['original_poster_name'],
				'forum_last_poster_colour'	=> $data['original_poster_colour'],
			];

			$sql 	= 'UPDATE ' . $this->tables['forums'] . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE forum_id = ' . $data['forum_id'] . '';
			$result	= $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			unset($sql_ary);

			// Remove the current user from the topics posted table
			$sql = 'DELETE FROM ' . $this->tables['topics_posted'] . '
				WHERE topic_id	= ' . $data['topic_id'] . '
					AND user_id = ' . $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
	}
}
