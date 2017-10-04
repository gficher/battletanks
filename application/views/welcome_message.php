<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
	<h1 class="title">BattleTanks</h1>
	<p>
		This game had its concept created by Halfbrick but then it was banned due to chaotic results.<br>
		The rules are simple: you win one action point a day and can use it whenever you want. You can spend it by moving through the board, giving it to a nearby player or by attacking a player.<br>
		Your main goal is to eliminate all other oponents and be the last one standing on the board.
	</p>

	<div class="board">
		<?php
		$size = 20;
		$range_size = 2;

		$players = Array(
			Array(
				'id' => 1,
				'name' => 'gficher',
				'power' => 2,
				'lives' => 3,
				'picture' => 'me_pic.jpg',
				'x' => 6,
				'y' => 3,
			),
			Array(
				'id' => 2,
				'name' => 'Bagatini',
				'power' => 1,
				'lives' => 3,
				'picture' => 'bagatini.jpg',
				'x' => 9,
				'y' => 4,
			),
			Array(
				'id' => 3,
				'name' => 'Priscila',
				'power' => 19,
				'lives' => 3,
				'picture' => 'priscila.jpg',
				'x' => 19,
				'y' => 7,
			),
			Array(
				'id' => 4,
				'name' => 'Giovanna',
				'power' => 7,
				'lives' => 3,
				'picture' => 'giovanna.jpg',
				'x' => 13,
				'y' => 18,
			),
		);

		for ($i=0; $i <= $size; $i++) {
			echo "<div class=\"row\">";
			for ($j=0; $j <= $size; $j++) {
				if (($i == 0) or ($j == 0)) {
					$out = ($i == 0) ? $j-1 : $i-1;
					if ($out == -1) $out = '';
					echo "<div class=\"col coord\">$out</div>";
					continue;
				}

				echo "<div class=\"col\" data-x=\"{$j}\" data-y=\"{$i}\">";

				foreach ($players as $value) {
					if (($value['x'] == $j) and ($value['y'] == $i)) {
						echo "
						<div class=\"player\" data-id=\"{$value['id']}\" data-name=\"{$value['name']}\">
						<div class=\"status\">
						<div class=\"power\">{$value['power']}</div>
						<div class=\"lives\">{$value['lives']}</div>
						</div>
						<div class=\"picture tooltip\" style=\"background-image: url(/assets/img/{$value['picture']});\" title=\"{$value['name']}\"></div>
						</div>
						";
					}
				}

				echo "</div>";
			}
			echo "</div>";
		}
		?>

	<h2>Logbook</h2>
		<div class="log-box">
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:32</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
			<div class="entry"><i class="fa fa-fw fa-bomb"></i> <b>gficher</b> bombed <b>Bagatini</b> <span class="time">03/10/2017 22:10:21</span></div>
		</div>
	</div>

	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/tooltipster.bundle.min.js"></script>
	<script>
	var me_id = 1;

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

	function movePlayer(id, x, y) {
		if (get_power(id) == 0) return;

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
			if (get_power(id) != 0) show_arrows(id);
			paint_range(id);

			$("[data-id='"+id+"'] > .tooltip").tooltipster({
				theme: 'tooltipster-borderless',
				delay: 0,
				content: $("[data-id='"+id+"']").attr('data-name'),
			});
		});
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

		if (get_power(id) == 0) return;

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
		<div class=\"give-ap\" title=\"Give Action Point\"><i class=\"fa fa-plus\"></i></div>\
		</div\
		");
	}

	$(document).ready(function() {
		$('.tooltip').tooltipster({
			theme: 'tooltipster-borderless',
			delay: 0,
		});

		$(".player[data-id='"+me_id+"'] > .picture").css({
			'border-color': 'yellow',
		});

		show_arrows(me_id);
		paint_range(me_id);

		$('.board > .row > .col').on("click", '.move-arrow', function() {
			dir = $(this).attr('data-dir');
			x = 0;
			y = 0;

			if (dir == 'up') {
				y--;
			} else if (dir == 'down') {
				y++;
			} else if (dir == 'left') {
				x--;
			} else if (dir == 'right') {
				x++;
			}

			$(".move-arrow").remove();
			movePlayer(me_id, x, y);
		});
	});
	</script>
</body>
</html>
