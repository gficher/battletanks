<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    public function __construct() {
		parent::__construct();
		if ($id = $this->session->user_id) {
			$this->user_model->setUser($id);
		} else if ($cookie = get_cookie('user_session')) {
			if (!$this->user_model->remember($cookie)) {
				set_cookie('user_session', '', 1);
			} else {
                $this->session->set_userdata('user_id', (int)$this->user_model->get('id'));
            }
		}
	}
}
