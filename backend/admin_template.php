<html>
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>

<body>
	<table id="status">

		<tr>
			<td>Game status</td>
			<td id=<?=$game_running ? "online" : "offline"; ?> ><?=$game_running ? "online" : "offline"; ?></td>
		</tr>
		<tr>
			<td>Players</td>
			<td><?=$player_count ?></td>
		</tr>
		<tr>
			<td><a href="admin.php?start">Start game</td>
			<td><a href="admin.php?stop">Stop game</td>
		</tr>
		<tr>
			<td colspan="2" id="notice"><?=$notice ?></td>
		</tr>

	</table>

	<table id="status">
		
	    <tr>
			<td colspan="2">Current settings</td>
		</tr>
		<tr>
			<td>Questions per player</td>
			<td><?=$server->questions_per_player ?></td>
		</tr>
		
		<tr>
			<td>Cards per player</td>
			<td><?=$server->cards_per_player ?></td>
		</tr>

		<tr>
			<td>Facts per card</td>
			<td><?=$server->facts_per_card ?></td>
		</tr>
		
	</table>

</body>
</html>