<?php

/*
***************************************************************************

	DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	Version 5, 2021
	Copyright (C) 2021 Christian Becher | phaziz.com <phaziz@gmail.com>

	Everyone is permitted to copy and distribute verbatim or modified
	copies of this license document, and changing it is allowed as long
	as the name is changed.

	DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
	TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
	0. YOU JUST DO WHAT THE FUCK YOU WANT TO!

	+++ Visit https://github.com/phaziz +++

***************************************************************************
*/

	/*
	* App-Route's Definition's
	*/

	$ROUTES_ARRAY = [
		[
			'name' => 'Startseite',
			'url' => '/mr-render/',
			'tpl' => 'startseite.php'
		],
		[
			'name' => 'Info',
			'url' => '/mr-render/info/',
			'tpl' => 'info.php'
		],
		[
			'name' => 'Consulting',
			'url' => '/mr-render/consulting/',
			'tpl' => 'consulting.php'
		],
		[
			'name' => 'Support',
			'url' => '/mr-render/support/',
			'tpl' => 'support.php'
		],
		[
			'name' => 'Kontakt',
			'url' => '/mr-render/kontakt/',
			'tpl' => 'kontakt.php'
		]
	];