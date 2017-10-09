<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logbook_model extends CI_Model {
    private static $db;
	private $info;

	function __construct() {
		parent::__construct();
		$this->info = Array();

		self::$db = &get_instance()->db;
	}

	public static function count() {
		return self::$db->count_all("tanks_log");
	}

	public static function log($board, $action, $player, $target, $direction) {
		$query = self::$db->query('INSERT INTO tanks_log (board, action, player, target, direction) VALUES (?,?,?,?,?)', Array(
			$board,
			$action,
			$player,
			$target,
			$direction,
		));
		$id = self::$db->insert_id();
		return $id;
	}

	public static function getList($board, $start_id) {
		$return = Array();

		$start_id = empty($start_id) ? 0 : $start_id;

		$query = self::$db->query("SELECT l.*,
			p.user as player_user, up.username as player_username, p.pos_x as player_x, p.pos_y as player_y, p.life as player_life, p.power as player_power,
			t.user as target_user, ut.username as target_username, t.pos_x as target_x, t.pos_y as target_y, t.life as target_life, t.power as target_power
			FROM tanks_log l
			LEFT JOIN tanks_player p ON p.user = l.player AND p.board = l.board
			LEFT JOIN users up ON up.id = p.user
			LEFT JOIN tanks_player t ON t.user = l.target AND p.board = l.board
			LEFT JOIN users ut ON ut.id = t.user
			WHERE l.id > $start_id ORDER BY l.id asc");
		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		if ($return) {
			return $return;
		} else {
			return false;
		}
	}

	public static function getLastUpdateId($board) {
		$query = self::$db->query("SELECT id FROM tanks_log WHERE board = $board ORDER BY id desc LIMIT 1");
		if (isset($query->result_array()[0]['id'])) return $query->result_array()[0]['id'];
		return 0;
	}
}
