<?php

return [
	'domain' => array_get($_ENV, 'OAUTH_DOMAIN'),

	'client' => [
		'id'     => array_get($_ENV, 'OAUTH_CLIENT_ID'),
		'secret' => array_get($_ENV, 'OAUTH_CLIENT_SECRET'),
	]
];
