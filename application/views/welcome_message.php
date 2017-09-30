<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

		for ($i=0; $i < $size; $i++) {
			echo "<div class=\"row\">";
			for ($j=0; $j < $size; $j++) {
				((abs($i-3) <= $range_size) and (abs($j-7) <= $range_size)) ? $range = 'range' : $range = '';
				
				echo "<div class=\"col {$range}\">";

				if (($i == 3) and ($j == 7)) echo "
				<div class=\"player\">
				<div class=\"power\">10</div>
				<div class=\"picture tooltip\" style=\"background-image: url(/assets/img/me_pic.jpg); border-color: yellow;\" title=\"gficher\"></div>
				</div>
				";
				if (($i == 3) and ($j == 8)) echo "
				<div class=\"move-arrow\">
					<i class=\"fa fa-arrow-right\"></i>
				</div>
				";
				if (($i == 4) and ($j == 7)) echo "
				<div class=\"move-arrow\">
					<i class=\"fa fa-arrow-down\"></i>
				</div>
				";
				if (($i == 2) and ($j == 7)) echo "
				<div class=\"move-arrow\">
					<i class=\"fa fa-arrow-up\"></i>
				</div>
				";
				if (($i == 3) and ($j == 6)) echo "
				<div class=\"move-arrow\">
					<i class=\"fa fa-arrow-left\"></i>
				</div>
				";
				if (($i == 4) and ($j == 9)) echo "
				<div class=\"player\">
				<div class=\"power\">0</div>
				<div class=\"bomb\" title=\"Bomb Bagatini\"><i class=\"fa fa-bomb\"></i></div>
				<div class=\"picture tooltip\" style=\"background-image: url(/assets/img/bagatini.jpg);\" title=\"Bagatini\"></div>
				</div>
				";
				if (($i == 7) and ($j == 19)) echo "
				<div class=\"player\">
				<div class=\"power\">17</div>
				<div class=\"picture tooltip\" style=\"background-image: url(/assets/img/priscila.jpg);\" title=\"Priscila\"></div>
				</div>
				";
				if (($i == 18) and ($j == 13)) echo "
				<div class=\"player\">
				<div class=\"power\">2</div>
				<div class=\"picture tooltip\" style=\"background-image: url(/assets/img/giovanna.jpg);\" title=\"Giovanna\"></div>
				</div>
				";

				echo "</div>";
			}
			echo "</div>";
		}
		?>
	</div>

	<script type="text/javascript" src="<?=base_url()?>assets/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/tooltipster.bundle.min.js"></script>
	<script>
	$(document).ready(function() {
		$('.tooltip').tooltipster({
			theme: 'tooltipster-borderless',
			delay: 0,
		});
	});
	</script>
</body>
</html>
