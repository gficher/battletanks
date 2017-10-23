<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lang extends MY_Controller {
	public function __construct() {
		parent::__construct();
		header('Content-Type: application/json');
	}

    public function index() {
        $lang['pt-br']['boardlist_learn_more'] = "Você pode aprender mais sobre o jogo <a href=\"https://archive.gficher.com/battletanks.pdf\">aqui</a>.";
        $lang['pt-br']['boardlist_open_time'] = "Data de abertura";
        $lang['pt-br']['boardlist_start_time'] = "Data de início";
        $lang['pt-br']['boardlist_status'] = "Estado";
        $lang['pt-br']['boardlist_players'] = "Jogadores";
        $lang['pt-br']['boardlist_view'] = "Ver";
        $lang['pt-br']['boardlist_join'] = "Entrar";
        $lang['pt-br']['boardlist_planned'] = "Planejado";
        $lang['pt-br']['boardlist_open_join'] = "Aberto para entrar";
        $lang['pt-br']['boardlist_cancelled'] = "Cancelado";
        $lang['pt-br']['boardlist_in_progress'] = "Em progresso";
        $lang['pt-br']['boardlist_ended'] = "Finalizado";
        $lang['pt-br']['menu_login_msg'] = "Você deve usar sua conta <a href=\"https://gficher.com\" target=\"_blank\">gficher.com</a> para entrar. Se você ainda não tem crie uma <a href=\"https://gficher.com/user/register\" target=\"_blank\">aqui</a>.<br>Por favor note que é muito importante ter uma foto de perfil!";
        $lang['pt-br']['board_game'] = "Jogo";
        $lang['pt-br']['board_logbook'] = "Histórico";
        $lang['pt-br']['board_players'] = "Jogadores";
        $lang['pt-br']['board_name'] = "Nome";
        $lang['pt-br']['board_life'] = "Vida";
        $lang['pt-br']['board_power'] = "Poder";
        $lang['pt-br']['board_join'] = "Entrar no jogo";
        $lang['pt-br']['board_joined'] = "Você entrou no jogo!";
        $lang['pt-br']['board_leave'] = "Sair do jogo";
        $lang['pt-br']['board_left'] = "Você saiu do jogo!";
        $lang['pt-br']['board_failed_join'] = "Erro ao entrar!";
        $lang['pt-br']['board_failed_leave'] = "Erro ao sair!";
        $lang['pt-br']['logbook_moved'] = "moveu";
        $lang['pt-br']['logbook_right'] = "para a direita";
        $lang['pt-br']['logbook_left'] = "para a esquerda";
        $lang['pt-br']['logbook_up'] = "para cima";
        $lang['pt-br']['logbook_down'] = "para baixo";
        $lang['pt-br']['logbook_bought_life'] = "comprou uma vida";
        $lang['pt-br']['logbook_attacked'] = "atacou";
        $lang['pt-br']['logbook_empowered'] = "delegou";
        $lang['pt-br']['logbook_died'] = "morreu graciosamente";
        $lang['pt-br']['logbook_daily_power'] = "Poder diário!";
        $lang['pt-br']['logbook_joined_game'] = "se inscreveu";
        $lang['pt-br']['logbook_left_game'] = "saiu";
        $lang['pt-br']['logbook_the_board'] = "no jogo";
        $lang['pt-br']['logbook_open_players'] = "Aberto para jogadores!";
        $lang['pt-br']['logbook_started'] = "O jogo começou!";
        $lang['pt-br']['logbook_won'] = "ganhou o jogo!";
        $lang['pt-br']['logbook_vote_power'] = "recebeu um poder por voto!";
        $lang['pt-br']['menu_by_gficher'] = "por <a href=\"https://gficher.com/\">gficher</a>";
        $lang['pt-br']['menu_board_list'] = "Lista de jogos";
        $lang['pt-br']['menu_my_account'] = "Minha conta";
        $lang['pt-br']['menu_edit_profile'] = "Editar perfil";
        $lang['pt-br']['menu_logout'] = "Sair";
        $lang['pt-br']['menu_login'] = "Entrar";
        $lang['pt-br']['menu_close'] = "Fechar";

        $lang['en']['boardlist_learn_more'] = "You can learn more about the game <a href=\"https://archive.gficher.com/battletanks.pdf\">here</a>.";
        $lang['en']['boardlist_open_time'] = "Open time";
        $lang['en']['boardlist_start_time'] = "Start time";
        $lang['en']['boardlist_status'] = "Status";
        $lang['en']['boardlist_players'] = "Players";
        $lang['en']['boardlist_view'] = "See";
        $lang['en']['boardlist_join'] = "Join";
        $lang['en']['boardlist_planned'] = "Planned";
        $lang['en']['boardlist_open_join'] = "Open to join";
        $lang['en']['boardlist_cancelled'] = "Cancelled";
        $lang['en']['boardlist_in_progress'] = "In progress";
        $lang['en']['boardlist_ended'] = "Ended";
        $lang['en']['menu_login_msg'] = "You should use your <a href=\"https://gficher.com\" target=\"_blank\">gficher.com</a> account to log in. If you do not have it create one <a href=\"https://gficher.com/user/register\" target=\"_blank\">here</a>.<br>Plase note that it's very important to have a profile picture!";
        $lang['en']['board_game'] = "Game";
        $lang['en']['board_logbook'] = "Logbook";
        $lang['en']['board_players'] = "Players";
        $lang['en']['board_name'] = "Name";
        $lang['en']['board_life'] = "Life";
        $lang['en']['board_power'] = "Power";
        $lang['en']['board_join'] = "Join game";
        $lang['en']['board_joined'] = "You have joined the game!";
        $lang['en']['board_leave'] = "Leave game";
        $lang['en']['board_left'] = "You have left the game";
        $lang['en']['board_failed_join'] = "Failed to join!";
        $lang['en']['board_failed_leave'] = "Failed to leave!";
        $lang['en']['logbook_moved'] = "moved";
        $lang['en']['logbook_right'] = "right";
        $lang['en']['logbook_left'] = "left";
        $lang['en']['logbook_up'] = "up";
        $lang['en']['logbook_down'] = "down";
        $lang['en']['logbook_bought_life'] = "bought a life";
        $lang['en']['logbook_attacked'] = "attacked";
        $lang['en']['logbook_empowered'] = "empowered";
        $lang['en']['logbook_died'] = "died gracefully";
        $lang['en']['logbook_daily_power'] = "Daily power!";
        $lang['en']['logbook_joined_game'] = "joined";
        $lang['en']['logbook_left_game'] = "left";
        $lang['en']['logbook_the_board'] = "the game";
        $lang['en']['logbook_open_players'] = "Open to players!";
        $lang['en']['logbook_started'] = "The game has started!";
        $lang['en']['logbook_won'] = "won the game!";
        $lang['en']['logbook_vote_power'] = "received a power by vote!";
        $lang['en']['menu_by_gficher'] = "by <a href=\"https://gficher.com/\">gficher</a>";
        $lang['en']['menu_board_list'] = "Game list";
        $lang['en']['menu_my_account'] = "My account";
        $lang['en']['menu_edit_profile'] = "Edit profile";
        $lang['en']['menu_logout'] = "Logout";
        $lang['en']['menu_login'] = "Sign in";
        $lang['en']['menu_close'] = "Close";

        if (array_key_exists($this->input->get('lang'), $lang)) {
            echo json_encode(Array(
    			'success' => true,
    			'lang' => $lang[$this->input->get('lang')],
    		), JSON_PRETTY_PRINT);
    		return 1;
        } else {
            echo json_encode(Array(
    			'success' => false,
    			'lang' => $lang['en'],
    		), JSON_PRETTY_PRINT);
    		return 1;
        }
    }
}
