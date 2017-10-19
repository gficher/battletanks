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
			$row['picture'] = (empty($row['picture'])) ? 'default-user.png' : $row['picture'];
			$return[] = $row;
		}

		if (count($return)) {
			return $return;
		} else {
			return false;
		}
	}

	public function getAlivePlayers() {
		$return = Array();
		$board = $this->get('id');

		$query = self::$db->query("
		SELECT p.*, u.username, u.picture FROM tanks_player p
		LEFT JOIN users u ON p.user = u.id
		WHERE board = $board AND dead_time is NULL
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

	public function end() {
		$query = $this->db->query('SELECT p.*, u.username, u.picture FROM tanks_player p
		LEFT JOIN users u ON p.user = u.id
		WHERE board = ? AND dead_time is NULL', Array(
			$this->get('id'),
		));

		if ($query->num_rows() == 1) {
			$winner = $query->row_array()['user'];
			$query = $this->db->query('UPDATE tanks_board SET end_time = NOW() WHERE board = ?', Array(
				$this->get('id'),
			));
			return $winner;
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
		SET p.power = p.power+1 WHERE p.dead_time is NULL AND b.end_time is NULL AND start_time < NOW()
		');
	}

	public function runVote() {
		$output = Array();
		$winners = Array();
		$query = self::$db->query('SELECT COUNT(*) as count,board,target,DATE(vote_time) FROM tanks_vote WHERE DATE(vote_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY board,target,DATE(vote_time) ORDER BY board ASC, COUNT(*) DESC');

		foreach ($query->result_array() as $row) {
			$winners[$row['board']][] = $row;
		}

		foreach ($winners as $key => $value) {
			$bigger = 0;
			for ($i = 0; $i < count($value); $i++) {
				if ($bigger == 0) {
					$bigger = $value[$i]['count'];
				} else {
					if ($value[$i]['count'] < $bigger) {
						unset($winners[$key][$i]);
					}
				}
			}
			if (count($winners[$key]) >= 1) {
				$output[] = Array(
					'board' => $key,
					'player' => $winners[$key][rand(0, count($winners[$key])-1)]['target'],
				);

				$query = $this->db->query('
				UPDATE tanks_player p
				LEFT JOIN tanks_board b ON p.board = b.id
				SET p.power = p.power+1 WHERE p.dead_time is NULL AND b.end_time is NULL AND start_time < NOW() AND p.user = ? AND p.board = ?
				', Array(
					$winners[$key][rand(0, count($winners[$key])-1)]['target'],
					$key,
				));
			}

		}
		return $output;
	}

	public static function roundUpToAny($n,$x=3) {
		return (ceil($n)%$x === 0) ? ceil($n) : round(($n+$x/2)/$x)*$x;
	}

	public function joinPlayer($id) {
		$players = Array();
		if ($this->getPlayers()) {
			foreach ($this->getPlayers() as $key => $value) {
				$players[] = $value['user'];
			}
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
	public function leavePlayer($id) {
		$players = Array();
		if ($this->getPlayers()) {
			foreach ($this->getPlayers() as $key => $value) {
				$players[] = $value['user'];
			}
		}

		if (in_array($id, $players)) {
			$query = $this->db->query('DELETE FROM tanks_player WHERE user = ? AND board = ?', Array(
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

	public function isJoiningMode() {
		if ((new DateTime() < new DateTime($this->get('start_time'))) and (new DateTime() > new DateTime($this->get('open_time'))))
		return 1;
		return 0;
	}

	public function isGamingMode() {
		if ((new DateTime() > new DateTime($this->get('start_time'))) and (empty($this->get('end_time'))))
		return 1;
		return 0;
	}

	public function isPlannedMode() {
		if ((new DateTime() < new DateTime($this->get('open_time'))) and (empty($this->get('end_time'))))
		return 1;
		return 0;
	}

	public function isClosedMode() {
		if (!empty($this->get('end_time')))
		return 1;
		if (($this->isGamingMode) and (count($this->getPlayers()) == 0))
		return 1;
		return 0;
	}
}
