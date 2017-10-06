<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
	private static $db;
	private $info;

	const mail_Host  = 'smtp.gmail.com';
	const mail_Port = 587;
	const mail_SMTPSecure = 'tls';
	const mail_SMTPAuth = true;
	const mail_Username = "gficher.mailer@gmail.com";
	const mail_Password = "gMailer%123*$";
	const mail_from = "gficher.mailer@gmail.com";
	const mail_from_name = 'Equipe gficherDev';
	const mail_debug = 0;

	function __construct() {
		parent::__construct();
		$this->info = Array();

		$this->info['permissions'] = Array();
		$this->info['roles'] = Array();

		self::$db = &get_instance()->db;
	}

	public static function count() {
		return self::$db->count_all("users");
	}

	private static function hash_password($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	private static function strReplaceAssoc(array $replace, $subject) {
		return str_replace(array_keys($replace), array_values($replace), $subject);
	}

	public static function getId($username) {
		$query = self::$db->query('SELECT id FROM users WHERE id = ? OR username = ? OR email = ?', Array($username, $username, $username));
		$row = $query->row_array();

		if (!empty($row['id'])) {
			return $row['id'];
		} else {
			return false;
		}
	}

	public static function getList($search = null, $fields = '*', $field_order = 'id', $order = 'asc', $limit = 999999999999, $start = 0) {
		$return = Array();

		$search_str = 'WHERE name LIKE "'.$search.'%" or surname LIKE "'.$search.'%" or username LIKE "'.$search.'%" or email LIKE "'.$search.'%" or CONCAT(name, " ", surname) LIKE "'.$search.'%"';
		$query = self::$db->query('SELECT '.$fields.' FROM users '.$search_str.' ORDER BY '.$field_order.' '.$order.' LIMIT '.$start.','.$limit);
		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		if ($return) {
			return $return;
		} else {
			return false;
		}
	}

	public static function getRoles() {
		$return = Array();

		$query = self::$db->query('SELECT * FROM roles');
		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		return $return;
	}

	public static function login($username, $password) {
		$query = self::$db->query('SELECT id, password FROM users WHERE email = ? OR username = ? LIMIT 1', Array($username, $username));
		$row = $query->row_array();

		if (crypt($password, $row['password']) == $row['password']) {
			return $row['id'];
		} else {
			return false;
		}
	}

	public function setLastLogin() {
		$query = $this->db->query('UPDATE users SET last_login = NOW(), last_ip = ? WHERE id = ?', Array($_SERVER['REMOTE_ADDR'], $this->get('id')));
	}

	public function setUser($id) {
		$this->info['id'] = $id;
		$query = $this->db->query('SELECT * FROM users WHERE id = ? LIMIT 1', Array($id));

		if ($query->num_rows() == 1) {
			$this->info = $query->row_array();

			$query = $this->db->query('SELECT permission FROM role_perm WHERE role in (SELECT role FROM user_role WHERE user = ?)', Array($id));
			$this->info['permissions'] = Array();
			foreach ($query->result_array() as $row) {
				$this->info['permissions'][] = $row['permission'];
			}

			$query = $this->db->query('SELECT * FROM user_role WHERE user = ?', Array($id));
			$this->info['roles'] = Array();
			foreach ($query->result_array() as $row) {
				$this->info['roles'][] = $row['role'];
			}
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
		if (!in_array($key, Array('id', 'password'))) {
			$this->info[$key] = $value;
			return true;
		} else {
			return false;
		}
	}

	public function clear() {
		$this->info = Array();

		$this->info['permissions'] = Array();
		$this->info['roles'] = Array();
	}

	public function update() {
		if (isset($this->info['id'])) {
			$query = $this->db->query('UPDATE users SET name = ?, email = ?, email_checked = ?, picture = ?, phone = ?, birthday = ? WHERE id = ?', Array(
				$this->get('name'),
				$this->get('email'),
				$this->get('email_checked'),
				$this->get('picture'),
				$this->get('phone'),
				$this->get('birthday'),
				$this->get('id'),
			));
		} else {
			return false;
		}
	}

	public function add() {
		$query = $this->db->query('INSERT INTO users (name, surname, username, email) VALUES (?,?,?,?)', Array(
			$this->get('name'),
			$this->get('surname'),
			$this->get('username'),
			$this->get('email'),
		));
		$id = $this->db->insert_id();
		$this->setUser($id);
		return $id;
	}

	public function delete() {
		$query = $this->db->query('DELETE FROM users WHERE id = ?', Array(
			$this->get('id'),
		));
	}

	public function setRemember() {
		$rid = md5($this->info['id'].time());

		if (isset($this->info['id'])) {
			$this->db->insert('login_remember', Array(
				'id' => $rid,
				'user' => $this->info['id'],
			));
			return $rid;
		} else {
			return false;
		}
	}

	public function remember($id) {
		$query = $this->db->query('SELECT * FROM login_remember WHERE id = ?', Array($id));

		if ($query->num_rows() == 1) {
			$row = $query->row_array();
			$this->setUser($row['user']);
			return $row['id'];
		} else {
			return false;
		}
	}

	public function deleteRemember($id) {
		$query = $this->db->query('DELETE FROM login_remember WHERE id = ?', Array($id));
	}

	public function checkPerm($perm = null) {
		if ($perm) {
			if (is_array($perm)) {
				return (array_intersect($perm, $this->info['permissions'])) ? true : false;
			} else {
				return in_array($perm, $this->info['permissions']);
			}
		} else {
			return $this->isLoggedIn();
		}
	}

	public function changePass($newPass) {
		$query = $this->db->query('UPDATE users SET password = ? WHERE id = ?', Array($this->hash_password($newPass), $this->info['id']));
	}

	public function isLoggedIn() {
		return ((isset($this->info['id'])) and (!empty($this->info['id'])));
	}

	public function addRole($role) {
		$query = $this->db->query('INSERT INTO user_role (user, role) VALUES (?, ?)', Array($this->info['id'], $role));
	}

	public function removeRole($role = null) {
		if ($role) {
			$query = $this->db->query('DELETE FROM user_role WHERE user = ? AND role = ?', Array($this->info['id'], $role));
		} else {
			$query = $this->db->query('DELETE FROM user_role WHERE user = ?', Array($this->info['id']));
		}
	}

	public function sendMailVerificationToken() {
		$genToken = hash('sha256', uniqid(rand(), true).$this->get('id'));

		$query = $this->db->query('DELETE FROM email_token WHERE user = ?', Array($this->get('id')));
		$query = $this->db->query('INSERT INTO email_token (token, user) VALUES (?, ?)', Array($genToken, $this->get('id')));

		/* TEMPLATE */
		$template = 'mail_verification';

		$options = array();
		$options['{name}'] = $this->get('name');
		$options['{genToken}'] = $genToken;

		$html = file_get_contents(APPPATH.'../mail_templates/'.$template.'.html');
		$html = self::strReplaceAssoc($options, $html);

		/* MAILER */
		$mail = new PHPMailer();

		$mail->isSMTP();
		$mail->SMTPDebug = self::mail_debug;
		$mail->Debugoutput = 'html';

		$mail->CharSet = 'UTF-8';

		$mail->Host = self::mail_Host;
		$mail->Port = self::mail_Port;
		$mail->SMTPSecure = self::mail_SMTPSecure;
		$mail->SMTPAuth = self::mail_SMTPAuth;
		$mail->Username = self::mail_Username;
		$mail->Password = self::mail_Password;
		$mail->setFrom(self::mail_from, self::mail_from_name);
		$mail->addAddress($this->get('email'), $this->get('name').' '.$this->get('surname'));
		$mail->Subject = 'Confirmar conta de '.$this->get('username');

		$mail->msgHTML($html);

		$mail->AltBody = "Olá, {$this->get('name')}!\n\n
		Você recebeu esse email pois necessita confirmar sua conta em gficher.com\n\n
		Se você não requisitou a criação de uma conta associada a este endereço de email ignore essa mensagem ou continue o processo de criação para que este endereço fique indisponível no sistema.\n\n
		Entre em contato com nosso suporte a qualquer momento se precisar de ajuda.\n\n
		Para verificá-la entre em
		https://beta.gficher.com/user/checkmail/{$genToken}\n\n
		Atenciosamente,\n
		Equipe gficherDev";

		if ($mail->send()) {
			return true;
		} else {
			return false;
		}
	}

	public function sendMailWelcome($admin = false) {
		$genToken = hash('sha256', uniqid(rand(), true).$this->get('id'));

		if ($admin) {
			$query = $this->db->query('DELETE FROM password_token WHERE user = ?', Array($this->get('id')));
			$query = $this->db->query('INSERT INTO password_token (token, user) VALUES (?, ?)', Array($genToken, $this->get('id')));
		} else {
			$query = $this->db->query('DELETE FROM email_token WHERE user = ?', Array($this->get('id')));
			$query = $this->db->query('INSERT INTO email_token (token, user) VALUES (?, ?)', Array($genToken, $this->get('id')));
		}

		/* TEMPLATE */
		if ($admin) {
			$template = 'welcome_admin';
		} else {
			$template = 'welcome';
		}

		$options = array();
		$options['{name}'] = $this->get('name');
		$options['{surname}'] = $this->get('surname');
		$options['{username}'] = $this->get('username');
		$options['{email}'] = $this->get('email');
		$options['{genToken}'] = $genToken;

		$html = file_get_contents(APPPATH.'../mail_templates/'.$template.'.html');
		$html = self::strReplaceAssoc($options, $html);

		/* MAILER */
		$mail = new PHPMailer();

		$mail->isSMTP();
		$mail->SMTPDebug = self::mail_debug;
		$mail->Debugoutput = 'html';

		$mail->CharSet = 'UTF-8';

		$mail->Host = self::mail_Host;
		$mail->Port = self::mail_Port;
		$mail->SMTPSecure = self::mail_SMTPSecure;
		$mail->SMTPAuth = self::mail_SMTPAuth;
		$mail->Username = self::mail_Username;
		$mail->Password = self::mail_Password;
		$mail->setFrom(self::mail_from, self::mail_from_name);
		$mail->addAddress($this->get('email'), $this->get('name').' '.$this->get('surname'));
		$mail->Subject = 'Bem-vindo, '.$this->get('name').'!';

		$mail->msgHTML($html);

		if ($admin) {
			$mail->AltBody = "Olá, {$this->get('name')}!\n\n
			Bem-vindo à plataforma gficher.com!\n\n
			Um administrador criou uma conta associada a este endereço de email.\n\n
			Nome: {$this->get('name')} {$this->get('surname')}\n
			Usuário: {$this->get('username')}\n
			Email: {$this->get('email')}\n\n
			Agora só falta definir uma senha usando o link abaixo.\n\n
			Entre em contato com nosso suporte a qualquer momento se precisar de ajuda.\n\n
			Para definir uma senha, entre em: \n
			https://beta.gficher.com/user/forgotpass/{$genToken}
			Atenciosamente,\n
			Equipe gficherDev";
		} else {
			$mail->AltBody = "Olá, {$this->get('name')}!\n\n
			Bem-vindo à plataforma gficher.com!\n\n
			Ficamos felizes por ter você em nosso sistema ;)\n\n
			Se você não requisitou a criação de uma conta associada a este endereço de email, ignore essa mensagem ou continue o processo de criação para que este endereço fique indisponível no sistema.\n\n
			O último passo é verificar seu email clicando no link abaixo.\n\n
			Entre em contato com nosso suporte a qualquer momento se precisar de ajuda.\n\n
			Para confirmar seu email, entre em:\n
			https://beta.gficher.com/user/checkmail/{$genToken}\n\n
			Atenciosamente,\n
			Equipe gficherDev";
		}

		if ($mail->send()) {
			return true;
		} else {
			return false;
		}
	}

	public static function checkMailToken($token) {
		$query = self::$db->query('SELECT *, DATE(creation_date) as creation_date_only FROM email_token WHERE token = ?', Array($token));
		$row = $query->row_array();

		if (!empty($row)) {
			return $row;
		} else {
			return false;
		}
	}

	public static function deleteMailToken($token) {
		$query = self::$db->query('DELETE FROM email_token WHERE token = ?', Array($token));
	}

	public function sendPasswordChangeToken() {
		$genToken = hash('sha256', uniqid(rand(), true).$this->get('id'));

		$query = $this->db->query('DELETE FROM password_token WHERE user = ?', Array($this->get('id')));
		$query = $this->db->query('INSERT INTO password_token (token, user) VALUES (?, ?)', Array($genToken, $this->get('id')));

		/* TEMPLATE */
		$template = 'forgot_password';

		$options = array();
		$options['{name}'] = $this->get('name');
		$options['{genToken}'] = $genToken;

		$html = file_get_contents(APPPATH.'../mail_templates/'.$template.'.html');
		$html = self::strReplaceAssoc($options, $html);

		/* MAILER */
		$mail = new PHPMailer();

		$mail->isSMTP();
		$mail->SMTPDebug = self::mail_debug;
		$mail->Debugoutput = 'html';

		$mail->CharSet = 'UTF-8';

		$mail->Host = self::mail_Host;
		$mail->Port = self::mail_Port;
		$mail->SMTPSecure = self::mail_SMTPSecure;
		$mail->SMTPAuth = self::mail_SMTPAuth;
		$mail->Username = self::mail_Username;
		$mail->Password = self::mail_Password;
		$mail->setFrom(self::mail_from, self::mail_from_name);
		$mail->addAddress($this->get('email'), $this->get('name').' '.$this->get('surname'));
		$mail->Subject = 'Alterar senha de '.$this->get('username');

		$mail->msgHTML($html);

		$mail->AltBody = "Olá, {$this->get('name')}!\n\n
		Nosso sistema recebeu uma solicitação de alteração de senha através do menu \"Esqueci minha senha\"\n\n
		Se não foi você ignore este email, já que o sistema pode receber estas solicitações de qualquer usuário não autenticado.\n\n
		Entre em contato com nosso suporte a qualquer momento se precisar de ajuda.\n\n
		Para verificá-la entre em
		https://beta.gficher.com/user/forgotpass/{$genToken}\n\n
		Atenciosamente,\n
		Equipe gficherDev";

		if ($mail->send()) {
			return true;
		} else {
			return false;
		}
	}


	public static function checkPassowrdToken($token) {
		$query = self::$db->query('SELECT *, DATE(creation_date) as creation_date_only FROM password_token WHERE token = ?', Array($token));
		$row = $query->row_array();

		if (!empty($row)) {
			return $row;
		} else {
			return false;
		}
	}

	public static function deletePasswordToken($token) {
		$query = self::$db->query('DELETE FROM password_token WHERE token = ?', Array($token));
	}

	public function getPicture() {
		if (!empty($this->info['picture'])) {
			return $this->info['picture'];
		} else {
			return 'default-user.png';
		}
	}

	public function addWallet($value, $desc) {
		$query = $this->db->query('INSERT INTO transaction_history (user, value, description) VALUES (?, ?, ?)', Array($this->get('id'), $value, $desc));
		$query = $this->db->query('UPDATE users SET wallet = wallet + ? WHERE id = ?', Array($value, $this->get('id')));
	}

	public function getTransactions($field_order = 'id', $order = 'desc', $limit = 999999999999, $start = 0) {
		$return = Array();

		$query = $this->db->query('SELECT * FROM transaction_history WHERE user = ? '.'ORDER BY '.$field_order.' '.$order.' LIMIT '.$start.','.$limit, Array($this->get('id')));
		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		if ($return) {
			return $return;
		} else {
			return false;
		}
	}

	public function getNotifications() {
		$return = Array();
		$query = $this->db->query('SELECT * FROM notifications WHERE user = ?', Array($this->get('id')));
		foreach ($query->result_array() as $row) {
			$return[] = $row;
		}

		if ($return) {
			return $return;
		} else {
			return false;
		}
	}

	public function readNotifications() {
		$query = $this->db->query('UPDATE notifications SET seen = NOW() WHERE user = ? AND seen is NULL', Array($this->get('id')));
	}

	public function getUploadLimit() {
		if ($this->checkPerm(11)) {
			return -1; // No limit
		} else {
			return 1024*1024*200; // 200 MB
		}
	}

	public function getUploadSize() {
        $query = $this->db->query('SELECT SUM(size) AS used FROM files WHERE owner = ?', Array($this->get('id')));
		$output = $query->row_array();

        return $output['used'];
	}

	public function canUpload() {
		$limit = $this->getUploadLimit();

		if ($limit == -1) return true;

		if ($limit > $this->getUploadSize()) {
			return true;
		} else {
			return false;
		}
	}
}
