<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Player_model extends CI_Model {
    private static $db;
	private $info;

	function __construct() {
		parent::__construct();
		$this->info = Array();

		self::$db = &get_instance()->db;
	}

	public static function count() {
		return self::$db->count_all("tanks_player");
	}

	public static function checkPos($board, $x, $y) {
		$query = self::$db->query('SELECT * FROM tanks_player WHERE board = ? and pos_x = ? and pos_y = ? and dead_time is NULL LIMIT 1', Array($board, $x, $y));

		if ($query->num_rows() == 1) {
			return true;
		} else {
			return false;
		}
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
		if (!in_array($key, Array('user','board'))) {
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
		if (isset($this->info['user'])) {
			$query = $this->db->query('UPDATE tanks_player SET life = ?, power = ?, dead_time = ?, pos_x = ?, pos_y = ? WHERE user = ? and board = ?', Array(
				$this->get('life'),
				$this->get('power'),
				$this->get('dead_time'),
				$this->get('pos_x'),
				$this->get('pos_y'),
				$this->get('user'),
				$this->get('board'),
			));
		} else {
			return false;
		}
	}

	public function add() {
		$query = $this->db->query('INSERT INTO tanks_player (user, board) VALUES (?,?)', Array(
			$this->get('user'),
			$this->get('board'),
		));
		$id = $this->db->insert_id();
		$this->setBoard($id);
		return $id;
	}

	public function delete() {
		$query = $this->db->query('DELETE FROM tanks_player WHERE user = ? and board = ?', Array(
			$this->get('user'),
			$this->get('board'),
		));
	}

	public function setPlayer($id, $board) {
		$this->info['user'] = $id;
		$this->info['board'] = $board;
		$query = $this->db->query('SELECT * FROM tanks_player WHERE user = ? and board = ? LIMIT 1', Array($id, $board));

		if ($query->num_rows() == 1) {
			$this->info = $query->row_array();
			return true;
		} else {
			return false;
		}
	}

	public function move($x, $y) {
		if (!isset($this->info['user'])) return 0;

		$this->set('pos_x', $this->get('pos_x')+$x);
		$this->set('pos_y', $this->get('pos_y')+$y);
		$this->update();
		return 1;
	}

	public function attack($damage = 1) {
		if (!isset($this->info['user'])) return 0;

		$this->set('life', $this->get('life')-$damage);
		if ($this->get('life') <= 0) $this->set('dead_time', date('Y-m-d H:i:s'));
		$this->update();

		$query = $this->db->query('DELETE FROM tanks_vote WHERE DATE(vote_time) = CURDATE() AND target = ?', Array($this->get('user')));
		return 1;
	}

	public function empower($power = 1) {
		if (!isset($this->info['user'])) return 0;

		$this->set('power', $this->get('power')+$power);
		$this->update();
		return 1;
	}

	public function vote($target) {
		$query = $this->db->query('DELETE FROM tanks_vote WHERE DATE(vote_time) = CURDATE() AND player = ?', Array($this->get('user')));
		$query = $this->db->query('INSERT INTO tanks_vote (board, player, target) VALUES (?,?,?)', Array($this->get('board'), $this->get('user'), $target));
	}

	public function getVote() {
		$query = $this->db->query('SELECT * FROM tanks_vote WHERE player = ? AND board = ? AND DATE(vote_time) = CURDATE() LIMIT 1', Array($this->get('user'), $this->get('board')));

		if ($query->num_rows() == 1) {
			$output = $query->row_array();
			return $output['target'];
		} else {
			return false;
		}
	}
}
