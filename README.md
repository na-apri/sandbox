# SimpleQueue

後でバッチ処理するようなQueueもどきなもの

## 必要そうなもの

	brew tap homebrew/dupes
	brew tap josegonzalez/homebrew-php
	brew install php54
	brew install composer

	brew install redis
	brew install php54-redis

インストールしたいプロジェクトのディレクトリに、
composer.jsonというファイル名で以下を記述。

```JSON
{
	"repositories" : [
		{
			"packagist": false,
			"type": "vcs",
			"url": "git@github.com:na-apri/sandbox.git"
		}
	],
    "require": {
		"SimpleQueue": "dev-master"
    }
}
```

ディレクトリへ移動してコンソールで、

	composer install

をすればインストールできます。

## サンプル

### 設定

	$queue = new \SimpleQueue\SimpleQueue([
		'connection' => [
			'config' => [
				'default' => [
					'host' => '127.0.0.1',
					'port' => '6379',
					'database' => '0',
				],
			],
			'task' => 'default',
			'worker' => 'default',
		],
		'key_prefix' => 'SAMPLE_',
	]);

### pushする

	// インスタンスメソッドを設定
	$queue->push([
		new Foo(), 'Method', ['p1', 'p2']
	]);

### 呼び出し
	// オートローダーなどの名前解決を行ったphpをコンソールとかで以下を呼び出すようにする。
	$queue->run();

