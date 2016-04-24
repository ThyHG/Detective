<html>
<head>
	<title>Admin Panel</title>

	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,600' rel='stylesheet' type='text/css'>
	<style>
		*{ font-family: "open sans"; }
		a{ color: #007396; text-decoration: none; font-weight: bold; }
		table{ font-size: 1.4em; margin: 80px auto; }
		table td{ padding-right: 20px; }
		table th{ margin: 0px; padding: 0px;}
		#offline{ color: #52614E; font-weight: bold; }
		#online{ color: #70D60B; font-weight: bold; }
		#notice{ text-align: center; }
	</style>
</head>

<body>
	<table>

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

	<table>
		
	    <tr>
			<th>Current settings</th>
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