<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board extends MY_Controller {
	public function __construct() {
		parent::__construct();
		header('Content-Type: application/json');

		if ($this->input->get('player')) {
			if (!$this->user_model->checkPerm(24)) {
				if ($this->input->get('player') != $this->user_model->get('id')) {
					echo json_encode(Array(
						'success' => false,
						'message' => 'You cannot play as other player.'
					), JSON_PRETTY_PRINT);
					exit();
				}
			}
		}
	}

	public function move() {
		$this->load->model('Player_model', 'player');
		$this->load->model('Board_model', 'board');

		$dir = Array(
			'u' => Array(
				'x' => 0,
				'y' => -1,
			),
			'd' => Array(
				'x' => 0,
				'y' => 1,
			),
			'l' => Array(
				'x' => -1,
				'y' => 0,
			),
			'r' => Array(
				'x' => 1,
				'y' => 0,
			),
		);

		if (!in_array($this->input->get('dir'), Array('u','d','l','r'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Invalid direction.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->isGamingMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot play while not in gaming mode.'
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

		if (
			($this->player->get('pos_x') >= $this->board->get('size')) or
			($this->player->get('pos_y') >= $this->board->get('size')) or
			($this->player->get('pos_x') < 0) or
			($this->player->get('pos_y') < 0)
		) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot go outside the board.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		$this->player->update();
		$this->player->empower(-1);

		$this->load->model('Logbook_model', 'logbook');
		$action_id = $this->logbook->log($this->input->get('board'), 'move', $this->input->get('player'), null, $this->input->get('dir'));

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successful move.',
			'action' => $action_id,
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function buyLife() {
		$this->load->model('Player_model', 'player');
		$this->load->model('Board_model', 'board');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->isGamingMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot play while not in gaming mode.'
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

		if ($this->player->get('power') < 5) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not enough power.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		$this->player->empower(-5);
		$this->player->attack(-1);

		$this->load->model('Logbook_model', 'logbook');
		$action_id = $this->logbook->log($this->input->get('board'), 'buy_life', $this->input->get('player'), null, null);

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successfully bought.',
			'action' => $action_id,
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function attack() {
		$this->load->model('Board_model', 'board');
		$this->load->model('Player_model', 'player');
		$this->load->model('Player_model', 'target');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->isGamingMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot play while not in gaming mode.'
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
		$action_id = $this->logbook->log($this->input->get('board'), 'attack', $this->input->get('player'), $this->input->get('target'), null);
		if ($this->target->get('dead_time')) {
			$this->logbook->log($this->input->get('board'), 'death', $this->input->get('player'), null, null);
		}

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successful attack.',
			'action' => $action_id,
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function empower() {
		$this->load->model('Board_model', 'board');
		$this->load->model('Player_model', 'player');
		$this->load->model('Player_model', 'target');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->isGamingMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot play while not in gaming mode.'
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
		$action_id = $this->logbook->log($this->input->get('board'), 'empower', $this->input->get('player'), $this->input->get('target'), null);

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successful empower.',
			'action' => $action_id,
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

		for ($i = 0; $i < count($query); $i++) {
			$this->board->setBoard($query[$i]['id']);
			$query[$i]['players'] = $this->board->getPlayers();
		}

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
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		session_write_close();
		echo "retry: 5000\n\n";

		$this->load->model('Logbook_model', 'logbook');
		$this->load->model('Board_model', 'board');
		$this->load->model('Logbook_model', 'logbook');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo "data: ";
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			));
			echo "\n\n";
			return 0;
		}

		if ($this->logbook->getList($this->input->get('board'), 0) === false) {
			$this->logbook->log($this->input->get('board'), 'open', null, null, null);
		}

		$last_id = $this->input->get('action');
		while (1) {
			$start = microtime(true);
			$updates = $this->logbook->getList($this->input->get('board'), $last_id);
			if ($last_id >= $updates[count($updates)-1]['id']) {
				echo ": heartbeat\n\n";
				while (ob_get_level() > 0) {
					ob_end_flush();
				}
				flush();
				usleep(250000);
				continue;
			}

			echo "data: ";
			echo json_encode(Array(
				'success' => true,
				'handshake' => !$last_id,
				'time_spent' => microtime(true)-$start,
				'updates' => $updates,
			));
			echo "\n\n";

			if (is_array($updates)) $last_id = $updates[count($updates)-1]['id'];

			while (ob_get_level() > 0) {
				ob_end_flush();
			}
			flush();
			sleep(1);
		}
	}

	public function vote() {
		$this->load->model('Board_model', 'board');
		$this->load->model('Player_model', 'player');
		$this->load->model('Player_model', 'target');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->isGamingMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot play while not in gaming mode.'
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
		if (!$this->target->setPlayer($this->input->get('target'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Target player not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->player->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact when alive.'
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

		$this->player->vote($this->input->get('target'));

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successfully voted.',
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function getMyVote() {
		$this->load->model('Board_model', 'board');
		$this->load->model('Player_model', 'player');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->isGamingMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot play while not in gaming mode.'
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

		if (!$this->player->get('dead_time')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot interact when alive.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successfully gotten vote.',
			'vote' => $this->player->getVote(),
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function daily() {
		if (!in_array($_SERVER['REMOTE_ADDR'], Array('50.116.87.195','127.0.0.1','::1'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Only root is allowed to perform this action.',
				'ip' => $_SERVER['REMOTE_ADDR'],
			), JSON_PRETTY_PRINT);
			return 0;
		}
		$this->load->model('Board_model', 'board');
		$this->board->dailyEmpower();

		$this->load->model('Logbook_model', 'logbook');
		foreach ($this->board->getList() as $key => $value) {
			if (!empty($value['end_time'])) continue;
			$this->logbook->log($value['id'], 'daily_power', null, null, null);
		}

		foreach ($this->board->runVote() as $key => $value) {
			$this->logbook->log($value['board'], 'vote_power', $value['player'], null, null);
		}

		echo json_encode(Array(
			'success' => true,
			'message' => 'Successfully empowered everyone.',
		), JSON_PRETTY_PRINT);
		return 1;
	}

	public function startGame() {
		$this->load->model('Board_model', 'board');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			));
			return 0;
		}

		if (!empty($this->board->get('size'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board is already started.'
			));
			return 0;
		}

		if ($this->board->getPlayers()) {
			$this->board->startGame();
			$this->logbook->log($this->board->get('id'), 'start', null, null, null);
			echo json_encode(Array(
				'success' => true,
				'message' => 'Board started!',
			));
			return 1;
		} else {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board has no players.'
			));
			return 0;
		}
	}

	public function join() {
		$this->load->model('Board_model', 'board');
		$this->load->model('Player_model', 'player');

		if ($this->input->get('player') == 0) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Auth required.',
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			));
			return 0;
		}

		// Board is not in joining mode
		if (!$this->board->isJoiningMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot join that game.'
			));
			return 0;
		}

		if ($this->player->setPlayer($this->input->get('player'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Player already in game.',
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ($this->board->joinPlayer($this->input->get('player'))) {
			$this->load->model('Logbook_model', 'logbook');
			$this->logbook->log($this->input->get('board'), 'join', $this->input->get('player'), null, null);

			echo json_encode(Array(
				'success' => true,
				'message' => 'Successfully joined.',
			), JSON_PRETTY_PRINT);
			return 1;
		} else {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Could not join game.',
			), JSON_PRETTY_PRINT);
			return 0;
		}
	}

	public function leave() {
		$this->load->model('Board_model', 'board');
		$this->load->model('Player_model', 'player');

		if ($this->input->get('player') == 0) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Auth required.',
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			));
			return 0;
		}

		// Board is not in joining mode
		if (!$this->board->isJoiningMode()) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Cannot leave that game.'
			));
			return 0;
		}

		if (!$this->player->setPlayer($this->input->get('player'), $this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Player not in game.',
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ($this->board->leavePlayer($this->input->get('player'))) {
			$this->load->model('Logbook_model', 'logbook');
			$this->logbook->log($this->input->get('board'), 'leave', $this->input->get('player'), null, null);

			echo json_encode(Array(
				'success' => true,
				'message' => 'Successfully left.',
			), JSON_PRETTY_PRINT);
			return 1;
		} else {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Could not leave game.',
			), JSON_PRETTY_PRINT);
			return 0;
		}
	}

	public function getPlayerList() {
		$this->load->model('Board_model', 'board');

		if (!$this->board->setBoard($this->input->get('board'))) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Board not found.'
			));
			return 0;
		}

		echo json_encode(Array(
			'success' => true,
			'message' => 'Board found.',
			'players' => $this->board->getPlayers(),
		));
		return 1;
	}
}
