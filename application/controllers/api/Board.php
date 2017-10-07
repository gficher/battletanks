<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends MY_Controller {
	public function __construct() {
		parent::__construct();
		header('Content-Type: application/json');
	}

	public function move() {
		$this->load->model('Player_model', 'player');

		$dir = Array(
			'up' => Array(
				'x' => 0,
				'y' => -1,
			),
			'down' => Array(
				'x' => 0,
				'y' => 1,
			),
			'left' => Array(
				'x' => -1,
				'y' => 0,
			),
			'right' => Array(
				'x' => 1,
				'y' => 0,
			),
		);

		if (!in_array($this->input->get('dir'), Array('up','down','left','right'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Invalid direction.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->player->setPlayer($this->input->get('player'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Actionee player not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if ($this->player->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact when dead.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ($this->player->get('power') < 1) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not enough power.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		$this->load->model('Board_model', 'board');
		$this->board->setBoard($this->input->get('board'));

		$this->player->set('pos_x', $this->player->get('pos_x')+$dir[$this->input->get('dir')]['x']);
		$this->player->set('pos_y', $this->player->get('pos_y')+$dir[$this->input->get('dir')]['y']);

		if ($this->player->checkPos($this->input->get('board'), $this->player->get('pos_x'), $this->player->get('pos_y'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot go inside someone.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (($this->player->get('pos_x') >= $this->board->get('size')) or ($this->player->get('pos_y') >= $this->board->get('size'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot go outside the board.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		$this->player->update();
		$this->player->empower(-1);

		$this->load->model('Logbook_model', 'logbook');
		$this->logbook->log($this->input->get('board'), 'move', $this->input->get('player'), null, $this->input->get('dir'));

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successful move.',
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function attack() {
		$this->load->model('Player_model', 'player');
		$this->load->model('Player_model', 'target');

		if (!$this->player->setPlayer($this->input->get('player'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Actionee player not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if (!$this->target->setPlayer($this->input->get('target'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Target player not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ((abs($this->player->get('pos_x')-$this->target->get('pos_x')) > 2) or (abs($this->player->get('pos_y')-$this->target->get('pos_y')) > 2)) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not in range.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ($this->player->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact when dead.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if ($this->target->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact with dead people.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if ($this->player->get('power') < 1) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not enough power.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		$this->player->empower(-1);
		$this->target->attack();

		$this->load->model('Logbook_model', 'logbook');
		$this->logbook->log($this->input->get('board'), 'attack', $this->input->get('player'), $this->input->get('target'), null);

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successful attack.',
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function empower() {
		$this->load->model('Player_model', 'player');
		$this->load->model('Player_model', 'target');

		if (!$this->player->setPlayer($this->input->get('player'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Actionee player not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if (!$this->target->setPlayer($this->input->get('target'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Target player not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ((abs($this->player->get('pos_x')-$this->target->get('pos_x')) > 2) or (abs($this->player->get('pos_y')-$this->target->get('pos_y')) > 2)) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not in range.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ($this->player->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact when dead.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if ($this->target->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact with dead people.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if ($this->player->get('power') < 1) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not enough power.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		$this->player->empower(-1);
		$this->target->empower();

		$this->load->model('Logbook_model', 'logbook');
		$this->logbook->log($this->input->get('board'), 'empower', $this->input->get('player'), $this->input->get('target'), null);

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successful empower.'
		), JSON_PRETTY_PRINT);
		return 0;
	}

	public function getBoard() {
		$this->load->model('Board_model', 'board');
		if ($this->board->setBoard($this->input->get('id'))) {
			$output = $this->board->get();
			$output['success'] = true;
			$output['players'] = $this->board->getPlayers();
		} else {
			$output = Array(
				'success' => false,
				'msg' => 'Board not found.',
			);
		}


		echo json_encode($output, JSON_PRETTY_PRINT);
	}

	public function getBoards() {
		$per_page = $this->input->get('per_page') ? $this->input->get('per_page') : 100;
		$page = $this->input->get('page') ? $this->input->get('page') : 1;
		$order = ($this->input->get('order')) ? $this->input->get('order') : 'id';

		$this->load->model('Board_model', 'board');

		$outputTotal = count($this->board->getList());
		$query = $this->board->getList($this->input->get('search'), '*', $order, 'asc', $per_page, ($page-1)*$per_page);

		echo json_encode(Array(
			'pagination' => Array(
				'page' => $page,
				'per_page' => $per_page,
				'pages' => ceil($outputTotal/$per_page),
				'total_results' => $outputTotal,
			),
			'success' => true,
			'results' => $query,
		), JSON_PRETTY_PRINT);
	}

	public function getUpdates() {
		$this->load->model('Logbook_model', 'logbook');
		$this->load->model('Board_model', 'board');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if (!is_numeric($this->input->get('id'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Invalid ID.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		echo json_encode(Array(
			'success' => true,
			'actions' => $this->logbook->getList($this->input->get('board'), $this->input->get('id')),
		), JSON_PRETTY_PRINT);
	}

	public function getLastUpdate() {
		$this->load->model('Logbook_model', 'logbook');
		$this->load->model('Board_model', 'board');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		echo json_encode(Array(
			'success' => true,
			'last_update' => $this->logbook->getLastUpdateId($this->input->get('board')),
		), JSON_PRETTY_PRINT);
		return 0;
	}
}
