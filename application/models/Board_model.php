<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board_model extends CI_Model {
	private static $db;
	private $info;

	function __construct() {
		parent::__construct();
		$this->info = Array();

		self::$db = &get_instance()->db;
	}

	public static function count() {
		return self::$db->count_all("tanks_board");
	}

	public function get($key = null) {
		if ($key) {
			if (array_key_exists($key, $this->info)) {
				return $this->info[$key];
			} else {
				return false;
			}
		} else {
			return $this->info;
		}
	}

	public function set($key, $value) {
		if (!in_array($key, Array('id'))) {
			$this->info[$key] = $value;
			return true;
		} else {
			return false;
		}
	}

	public function clear() {
		$this->info = Array();
	}

	public function update() {
		if (isset($this->info['id'])) {
			$query = $this->db->query('UPDATE tanks_board SET open_time = ?, start_time = ?, end_time = ?, size = ? WHERE id = ?', Array(
				$this->get('open_time'),
				$this->get('start_time'),
				$this->get('end_time'),
				$this->get('size'),
				$this->get('id'),
			));
		} else {
			return false;
		}
	}

	public function add() {
		$query = $this->db->query('INSERT INTO tanks_board (open_time, start_time) VALUES (?,?)', Array(
			$this->get('open_time'),
			$this->get('start_time'),
		));
		$id = $this->db->insert_id();
		$this->setBoard($id);
		return $id;
	}

	public function delete() {
		$query = $this->db->query('DELETE FROM tanks_board WHERE id = ?', Array(
			$this->get('id'),
		));
	}

	public function setBoard($id) {
		$this->info['id'] = $id;
		$query = $this->db->query('SELECT * FROM tanks_board WHERE id = ? LIMIT 1', Array($id));

		if ($query->num_rows() == 1) {
			$this->info = $query->row_array();
			return true;
		} else {
			return false;
		}
	}

	public function getPlayers() {
		$return = Array();
		$board = $this->get('id');

		$query = self::$db->query("
		SELECT p.*, u.username, u.picture FROM tanks_player p
		LEFT JOIN users u ON p.user = u.id
		WHERE board = $board
		");

		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		if ($return) {
			return $return;
		} else {
			return false;
		}
	}

	public static function getList($search = null, $fields = '*', $field_order = 'id', $order = 'asc', $limit = 999999999999, $start = 0) {
		$return = Array();

		(!empty($search)) ? $search_str = "WHERE id = $search" : $search_str = "";

		$query = self::$db->query('SELECT '.$fields.' FROM tanks_board '.$search_str.' ORDER BY '.$field_order.' '.$order.' LIMIT '.$start.','.$limit);
		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		if ($return) {
			return $return;
		} else {
			return false;
		}
	}

	public function dailyEmpower() {
		if (isset($this->info['id'])) {
			$query = $this->db->query('
			UPDATE tanks_player
			LEFT JOIN tanks_board b ON p.board = b.id
			SET p.power = p.power+1 WHERE p.dead_time is NULL and b.end_time is NULL
			');
		} else {
			return false;
		}
	}
}
