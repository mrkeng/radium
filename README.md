# Welcome to Radium PHP Framework #

Radium は、 PHP の軽量アプリケーションフレームワークです。

PHP バージョン 5.3 以降で動作します。

## Features

- MVC のコーディングスタイル
- ActiveRecord スタイルの O/R マッピング
- PHP のテンプレートエンジンを使用しないピュア PHP なビュー作成
- URL からコントローラにマッピングする機能
- 高度なルーティング機能
- 多言語化ウェブアプリケーションの開発

### Support Database

- MongoDB
- MySQL (required php-activerecord)

## Quick Installation

radium のインストールで必要なのは以下の2点です。

1. radium を clone
2. ウェブサーバの DocumentRoot を設定

### 1. radium を clone

任意のディレクトリ（ここでは /home/radiumuser/radiumapp とします）に radium のファイル一式を clone します。

	$ cd /home/radiumuser
	$ git clone --recursive https://github.com/nariyu/radium radiumapp

### 2. DocumentRoot を設定

ここでは Apache Web Server を使った例で解説します。

httpd.conf 等で VirtualHost 設定をしてください。以下は radiumapp.example.com というドメインで動作する設定の例です:

	<VirtualHost *>
		ServerName radiumapp.example.com
		ServerAdmin info@radiumapp.example.com
		DocumentRoot /home/radiumuser/radiumapp
		<Directory /home/radiumuser/radiumapp>
			AllowOverride all
		</Directory>
	</VirtualHost>

以上でインストールは完了です。 apache を再起動して http://radiumapp.example.com/ にウェブブラウザでアクセスしてください。

## Routing

radium ではコントローラクラスを作成し、メソッドを記述するだけでフレンドリーな URL にマッピングします。

例えば:

	http://radiumapp.example.com/media/get_list

という URL にアクセスすると以下のような Media コントローラクラスの get_list メソッドが処理されます:

	namespace app\controllers;
	
	class MediaController extends radium\action\Controller
	{
		public function get_list()
		{
			// CODE HERE
		}
	}

また、メソッドに引数 12345 を与える場合は:

	http://radiumapp.example.com/media/show/12345

という URL にアクセスすると以下のように show メソッドに引数で渡すことができます。

	class MediaController extends radium\action\Controller
	{
		public function show($mediaId)
		{
			// $mediaId == 12345
		}
	}

radium ではルーティング用の設定ファイル（app/config/routes.php）に簡単な記述を追加するだけでデータベースのレコードを自動的に読み込んでコントローラに渡すルーティングが実現できます。

例えば:

	http://radiumapp.example.com/m/3ix8743he

という URL で alias プロパティが 3ix8743he のデータを参照する場合、以下のように app/config/routes.php に記述することで Media モデルを Media コントローラの show メソッドに渡すことができます:

	Router::connect('/m/{:Media:alias}', array('controller' => 'media', 'action' => 'show'));


## その他 ##

詳しくは <http://nariyu.github.com/radium> をご覧下さい。（これから少しずつ書きます）

その他わからないことがありましたら [@nariyu](http://twitter.com/nariyu) まで。