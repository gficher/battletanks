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

	<link rel="stylesheet" href="<?=base_url()?>assets/css/board.css">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
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

		for ($i=0; $i < $size; $i++) {
			echo "<div class=\"row\">";
			for ($j=0; $j < $size; $j++) {
				echo "<div class=\"col\">";

				if (($i == 0) and ($j == 0)) echo "<div class=\"user\" style=\"background-image: url(/assets/img/me_pic.jpg); border-color: yellow;\" title=\"gficher\"></div>";
				if (($i == 4) and ($j == 9)) echo "<div class=\"user\" style=\"background-image: url(/assets/img/bagatini.jpg);\" title=\"Bagatini\"></div>";
				if (($i == 7) and ($j == 19)) echo "<div class=\"user\" style=\"background-image: url(/assets/img/priscila.jpg);\" title=\"Priscila\"></div>";
				if (($i == 18) and ($j == 13)) echo "<div class=\"user\" style=\"background-image: url(/assets/img/giovanna.jpg);\" title=\"Giovanna\"></div>";

				echo "</div>";
			}
			echo "</div>";
		}
		?>
	</div>
</body>
</html>
