<?php

return [
	'develop' => true,
	'tmp' => dirname(dirname(dirname(dirname(__DIR__)))).'/typescripts/tmp',

	'finder' => [
		'finder' => 'NaApri\ScriptAutoCompilerL4\ScriptFinder',
		'args' => [
			'directories' => [dirname(dirname(dirname(dirname(__DIR__)))).'/typescripts/src'],
			'extension' => '.ts',
			'notExtention' => '.d.ts',
			'depth' => 4
		]
	],	

	'compile' => [
		'command' => 'node /usr/local/share/npm/bin/tsc {FILE} --out {OUTPUT}',
	],

	'minify' => [
		'command' => 'node /usr/local/share/npm/bin/uglifyjs {FILE} --o {OUTPUT} -c -m',
	],

	'build' => [
		'output' => dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/public/js/app.js',
		'url' => 'js/app.js',
		'minify' => true,
	],
];
