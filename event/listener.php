<?php
/**
 *
 * @package Move Disapproved Posts
 * @copyright (c) 2020 david63
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace david63\movedisapproved\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use phpbb\config\config;
use phpbb\user;
use phpbb\log\log;
use david63\movedisapproved\controller\main_controller;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var config */
	protected $config;

	/** @var user */
	protected $user;

	/** @var log */
	protected $log;

	/** @var main_controller */
	protected $indexoutput;

	/**
	 * Constructor for listener
	 *
	 * @param config            $config             Config object
	 * @param user              $user               User object
	 * @param log               $log                Log object
	 * @param main_controller	$main_controller    Main controller
	 *
	 * @access public
	 */
	public function __construct(config $config, user $user, log $log, main_controller $main_controller)
	{
		$this->config			= $config;
		$this->user				= $user;
		$this->log				= $log;
		$this->main_controller	= $main_controller;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.disapprove_posts_after'		=> 'move_disapproved',
			'core.submit_post_modify_sql_data'	=> 'modify_post_data',
			'core.submit_post_end'				=> 'update_post_data',
		];
	}

	/**
	 * Process post if disapproved
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function move_disapproved($event)
	{
		if ($this->config['move_disapproved_forum'] > 0)
		{
			$this->main_controller->move_disapproved($event);
		}
		else
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MOVE_DISAPPROVED_FORUM');
		}
	}

	/**
	 * Modify the sql post data with disapproved details
	 *
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */
	public function modify_post_data($event)
	{
		$data = $event['data'];

		if (array_key_exists('original_poster_id', $data) && $this->config['move_disapproved_forum'] > 0)
		{
			$sql_data = $event['sql_data'];

			$sql_data['phpbb_posts']['poster_id'] 							= $data['original_poster_id'];
			$sql_data['phpbb_posts']['poster_ip'] 							= $data['original_poster_ip'];
			$sql_data['phpbb_posts']['sql']['poster_id'] 					= $data['original_poster_id'];
			$sql_data['phpbb_posts']['sql']['poster_ip'] 					= $data['original_poster_ip'];

			$sql_data['phpbb_topics']['poster_id'] 							= $data['original_poster_id'];
			$sql_data['phpbb_topics']['poster_ip']							= $data['original_poster_ip'];
			$sql_data['phpbb_topics']['sql']['topic_poster'] 				= $data['original_poster_id'];
			$sql_data['phpbb_topics']['sql']['topic_first_poster_name'] 	= $data['original_poster_name'];
			$sql_data['phpbb_topics']['sql']['topic_last_poster_name'] 		= $data['original_poster_name'];
			$sql_data['phpbb_topics']['sql']['topic_first_poster_colour'] 	= $data['original_poster_colour'];
			$sql_data['phpbb_topics']['sql']['topic_last_poster_colour'] 	= $data['original_poster_colour'];

			$event['sql_data'] = $sql_data;
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
		if (array_key_exists('original_poster_id', $event['data']) && $this->config['move_disapproved_forum'] > 0)
		{
			$this->main_controller->update_post_data($event);
		}
	}
}
