<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$alphabet[-1] = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>BattleTanks</title>

	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/tooltipster.bundle.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/tooltipster/themes/tooltipster-sideTip-borderless.min.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/font-awesome.min.css" />
	<link rel="stylesheet" href="<?=base_url()?>assets/css/board.css">
</head>
<body>
	<h1 class="title"><img src="/assets/img/logo.png" alt="BattleTanks" title="BattleTanks"></h1>
	<p style="text-align: center">You can learn more about how it works <a href="https://archive.gficher.com/battletanks.pdf" target="_blank" title="BattleTanks Info">here</a>.</p>

	<div class="board"></div>

	<h2  class="title">Logbook</h2>
	<div class="log-box"></div>

	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/tooltipster.bundle.min.js"></script>
	<script>
	var tab_id = Math.floor((Math.random() * 10000000000) + 1);
	var me_id, board = 1, last_action = 0, listener;
	var updateURL = function() {
		return "/api/board/getUpdates?board="+board.toString()+"&id="+tab_id.toString()+"&action="+last_action.toString();
	}

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
				listener.close();
				createListener();
				//$(".log-box").find('.entry').remove();
			}
		}, false);
	}

	function addToLog(value) {
		switch (value.action) {
			case "move":
			switch (value.direction) {
				case "u":
				direction = "up";
				break;
				case "d":
				direction = "down";
				break;
				case "l":
				direction = "left";
				break;
				case "r":
				direction = "right";
				break;
				default:
				direction = "?";
			}
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-arrows\"></i> <b>"+value.player_username+"</b> moved <b>"+direction+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "buy_life":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-heart\"></i> <b>"+value.player_username+"</b> bought a life <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "attack":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-bomb\"></i> <b>"+value.player_username+"</b> attacked <b>"+value.target_username+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			case "empower":
			$(".log-box").prepend("<div class=\"entry\">\
			<i class=\"fa fa-fw fa-power-off\"></i> <b>"+value.player_username+"</b> empowered <b>"+value.target_username+"</b> <span class=\"time\">"+value.timestamp+"</span>\
			</div>");
			break;
			default:
			console.log('Handshake log error', value);
		}
	}

	function paintBoard(size) {
		$(".board > .row").remove();
		$(".board").css({
			width: 42*(size+1)+'px',
			height: 42*(size+1)+'px',
		});

		for (var i = 0; i <= size; i++) {
			$(".board").append("<div class=\"row\"></div>");
			for (var j = 0; j <= size; j++) {
				if ((i==0) || (j==0)) {
					out = (i == 0) ? j-1 : i-1;
					if (out == -1) out = '';

					$(".board > .row").last().append("<div class=\"col coord\">"+out+"</div>");
					continue;
				}

				$(".board > .row").last().append("<div class=\"col\" data-x=\""+(j-1)+"\" data-y=\""+(i-1)+"\"></div>");
			}
		}
	}

	function login(username, password) {
		$.post('/api/user/login', {
			'user': username,
			'pass': password,
		}).done(function(data) {
			console.log(data);
			if (data.success) {
				me_id = data.user;
				repaint();
			}
		}).fail(function(data) {
			console.log(data);
		});
	}

	function logout() {
		$.post('/api/user/logout').done(function(data) {
			console.log(data);
			if (data.success) {
				me_id = 0;
				repaint();
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
		show_arrows(me_id);
		paint_range(me_id);
	}

	function get_power(id) {
		return parseInt($(".player[data-id='"+id+"']").find('.status .power').html());
	}

	function use_power(id, ap = 1) {
		$(".player[data-id='"+id+"']").find('.status .power').html((get_power(id)-ap));
		show_arrows(me_id);
		paint_range(me_id);
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
			cx = $(".player[data-id='"+id+"']").closest('.col').attr('data-x');
			cy = $(".player[data-id='"+id+"']").closest('.col').attr('data-y');

			content = $(".player[data-id='"+id+"']").closest('.col').html();

			$(".player[data-id='"+id+"']").remove();
			$(".board > .row > .col[data-x='"+(parseInt(cx)+x)+"'][data-y='"+(parseInt(cy)+y)+"']").html(content);

			$(".player[data-id='"+id+"']").css({
				'left': '0',
				'top': '0',
			});

			use_power(id);
			if (id == me_id) {
				if (get_power(id) != 0) show_arrows(id);
				paint_range(id);
			}

			$("[data-id='"+id+"'] > .tooltip").tooltipster({
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
			});
		}
		return;
	}

	function empowerPlayer(id) {
		use_power(me_id);
		use_power(id, -1);
		return;
	}

	function show_arrows(id) {
		cx = $(".player[data-id='"+id+"']").closest('.col').attr('data-x');
		cy = $(".player[data-id='"+id+"']").closest('.col').attr('data-y');

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
				if ($(".board > .row > .col[data-x='"+(parseInt(cx)+i)+"'][data-y='"+(parseInt(cy)+j)+"'] > .player").length) continue;

				if (i==-1 && j ==0) dir = 'left';
				if (i==0 && j ==-1) dir = 'up';
				if (i==1 && j ==0) dir = 'right';
				if (i==-0 && j ==1) dir = 'down';

				$(".board > .row > .col[data-x='"+(parseInt(cx)+i)+"'][data-y='"+(parseInt(cy)+j)+"']").html("\
				<div class=\"move-arrow\" data-dir=\""+dir+"\">\
				<i class=\"fa fa-arrow-"+dir+"\"></i>\
				</div>");
			}
		}
	}

	function paint_range(id, range = 2) {
		cx = $(".player[data-id='"+id+"']").closest('.col').attr('data-x');
		cy = $(".player[data-id='"+id+"']").closest('.col').attr('data-y');

		$(".range").removeClass('range');
		$(".board > .row > .col > .player > .actions").remove();

		//if (get_power(id) == 0) return;

		for (var i = -range; i <= range; i++) {
			for (var j = -range; j <= range; j++) {
				if (i==j && i == 0) continue;
				if (parseInt(cx)+i < 0) continue;
				if (parseInt(cy)+j < 0) continue;
				if (parseInt(cx)+i > 19) continue;
				if (parseInt(cy)+j > 19) continue;

				$(".board > .row > .col[data-x='"+(parseInt(cx)+i)+"'][data-y='"+(parseInt(cy)+j)+"']").addClass('range');
			}
		}

		$(".board > .row > .col.range > .player").append("\
		<div class=\"actions\">\
		<div class=\"bomb\" title=\"Bomb\"><i class=\"fa fa-bomb\"></i></div>\
		<div class=\"empower\" title=\"Give Action Point\"><i class=\"fa fa-plus\"></i></div>\
		</div\
		");
	}

	function repaint() {
		$(".move-arrow").remove();
		$(".player[data-id] > .picture").css({
			'border-color': 'transparent',
		});
		$(".player[data-id='"+me_id+"'] > .picture").css({
			'border-color': 'yellow',
		});
		show_arrows(me_id);
		paint_range(me_id);
	}

	$(document).ready(function() {
		$.get('/api/board/getBoard', {
			id: board,
		}).done(function(data) {
			console.log('Board data received', data);
			if (data.success) {
				paintBoard(parseInt(data.size));

				$.each(data.players, function(key, value) {
					if (value.dead_time != null) return;

					value.pos_x = (parseInt(value.pos_x)+1).toString();
					value.pos_y = (parseInt(value.pos_y)+1).toString();

					$(".board > .row > .col[data-x='"+(value.pos_x-1)+"'][data-y='"+(value.pos_y-1)+"']").append("<div class=\"player\" data-id=\""+value.user+"\" data-name=\""+value.username+"\">\
					<div class=\"status\">\
					<div class=\"power\">"+value.power+"</div>\
					<div class=\"lives\">"+value.life+"</div>\
					</div>\
					<div class=\"picture tooltip\" style=\"background-image: url(https://gficher.com/profile_images/"+value.picture+");\" title=\""+value.username+"\"></div>\
					</div>");
				});

				$('.tooltip').tooltipster({
					theme: 'tooltipster-borderless',
					delay: 0,
				});

				$.post('/api/user/getAUth').done(function(data) {
					console.log('Auth info received', data);
					if (data.success) {
						me_id = data.user;
						repaint();
						createListener();
					}
				}).fail(function(data) {
					console.error(data);
				});
			}
		}).fail(function(data) {
			console.error(data);
		});


		$('body').on("click", '.board > .row > .col > .move-arrow', function() {
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

		$('body').on("click", '.board > .row > .col > .player > .actions > .bomb', function() {
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

		$('body').on("click", '.board > .row > .col > .player > .actions > .empower', function() {
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
	});
	</script>
</body>
</html>
