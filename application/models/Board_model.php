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

		if (count($return)) {
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

	public static function dailyEmpower() {
		$query = self::$db->query('
		UPDATE tanks_player p
		LEFT JOIN tanks_board b ON p.board = b.id
		SET p.power = p.power+1 WHERE p.dead_time is NULL and b.end_time is NULL
		');
	}

	public static function roundUpToAny($n,$x=3) {
		return (ceil($n)%$x === 0) ? ceil($n) : round(($n+$x/2)/$x)*$x;
	}

	public function joinPlayer($id) {
		$players = Array();
		foreach ($this->getPlayers() as $key => $value) {
			$players[] = $value['user'];
		}
		
		if (!in_array($id, $players)) {
			$query = $this->db->query('INSERT INTO tanks_player (user, board) VALUES (?,?)', Array(
				$id,
				$this->get('id'),
			));
			return true;
		} else {
			return false;
		}
	}

	public function startGame() {
		$players = $this->getPlayers();

		$width = $this->roundUpToAny((count($players)/4)*3)+1;

		$this->set('size', $width);
		$this->set('size', $width);
		$this->update();

		$posx = $posy = 0;
		$opx = 0; $opy = 0;

		$this->load->model('Player_model', 'player');
		foreach ($players as $key => $value) {
			$this->player->setPlayer($value['user'], $this->get('id'));
			$this->player->set('pos_x', $posx);
			$this->player->set('pos_y', $posy);
			$this->player->update();

			if (($posx == 0) and ($posy == 0)) {
				$opx = 0; $opy = 3;
			} else if (($posy == $width-1) and ($posx == 0)) {
				$opx = 3; $opy = 0;
			} else if (($posy == $width-1) and ($posx == $width-1)) {
				$opx = 0; $opy = -3;
			} else if (($posy == 0) and ($posx == $width-1)) {
				$opx = -3; $opy = 0;
			}

			$posx += $opx;
			$posy += $opy;
		}
	}
}
