<!doctype html>

<html>
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="css/style.css"/>
</head>

<body>
	<table id="status">

		<tr>
			<td>Game status</td>
			<td id=<?=file_exists("j.on") ? "online" : "offline"; ?> ><?=file_exists("j.on") ? "online" : "offline"; ?></td>
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

</body>
</html>