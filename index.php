<?php

	/*

		BSD Zero Clause License
		
		Copyright (C) 2021 by phaziz <phaziz@gmail.com>
		
		Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby granted.

		THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED 
		WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT, 
		OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF 
		CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
		
		- YOU JUST DO WHAT THE FUCK YOU WANT TO! - 

		+++ Visit https://github.com/phaziz/MrRender +++
	*/

	require_once 'vendor/autoload.php';
	require_once './routes/routes.php';
	require_once './plugins/pluginRegistry.php';
	require_once 'MrRender.php';

	new \MrRender\MrRender($routesArray, $pluginsArray);