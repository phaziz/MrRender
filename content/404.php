<?php header("HTTP/1.0 404 Not Found"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{@ PageName @}</title>
	<link href="{@ CDNLink @}style.css" rel="stylesheet">
</head>
<body>
	<div class="container">
		<nav role="navigation">
			{@ Navigation @}
		</nav>
		<div>
			<h1>{@ PageName @}</h1>
			<p>Route not found: {@ Request @}</p>
			<p>Unique: {@ Unique @}</p>
		</div>
	</div>
</body>
</html>