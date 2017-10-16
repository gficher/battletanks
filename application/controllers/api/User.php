<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
	public function __construct() {
		parent::__construct();
		header('Content-Type: application/json');
	}

	public function login() {
		if (!$this->input->post('user')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Username required.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
		if (!$this->input->post('pass')) {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Password required.'
			), JSON_PRETTY_PRINT);
			return 0;
		}

		if ($id = $this->user_model->login($this->input->post('user'), $this->input->post('pass'))) {
			$this->user_model->setUser($id);
			$this->user_model->setLastLogin();

			$this->session->set_userdata('user_id', (int)$id);

			echo json_encode(Array(
				'success' => true,
				'message' => 'Logged in.',
				'user' => $id,
				'picture' => $this->user_model->getPicture(),
				'username' => $this->user_model->get('username'),
				'name' => $this->user_model->get('name'),
				'surname' => $this->user_model->get('surname'),
			), JSON_PRETTY_PRINT);
			return 1;
		} else {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Invalid credentials!'
			), JSON_PRETTY_PRINT);
			return 0;
		}
	}

	public function getAuth() {
		if ($this->user_model->isLoggedIn()) {
			echo json_encode(Array(
				'success' => true,
				'message' => 'Logged in.',
				'user' => $this->user_model->get('id'),
				'picture' => $this->user_model->getPicture(),
				'username' => $this->user_model->get('username'),
				'name' => $this->user_model->get('name'),
				'surname' => $this->user_model->get('surname'),
			), JSON_PRETTY_PRINT);
			return 1;
		} else {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not logged in.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
	}

	public function logout() {
		if ($this->user_model->isLoggedIn()) {
			if ($remember = get_cookie('user_session')) {
				set_cookie('user_session', '', 1);
				$this->user_model->deleteRemember($remember);
			}
			$this->session->unset_userdata('user_id');

			echo json_encode(Array(
				'success' => true,
				'message' => 'Logged out.',
			), JSON_PRETTY_PRINT);
			return 1;
		} else {
			echo json_encode(Array(
				'success' => false,
				'message' => 'Not logged in.'
			), JSON_PRETTY_PRINT);
			return 0;
		}
	}
}
