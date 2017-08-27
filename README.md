# SofPyon Framework( Ver. α of α )
MVC風なことができるシンプルなPHPフレームワーク(超未完成・なおかつエセ)

## このプログラムのレベル
 - __だいぶ実用的になった__
  - <a href="http://sofpyon.github.io/about_applevel.html" target="_blank">この表記について</a>

## 最低動作環境
 - __PHP 5.3 以降__
  - なるべく、最新で安定的なバージョンで利用されることをおすすめします。
  - PHP 5.3 で新たに実装された機能を利用しています。

## 特徴
 - __１ファイルフレームワーク__ です
 - O/Rマッピングを一切 __使用していません__
  - でも、それなりに簡単にDB操作できるはずです
 - コントローラやモデルなどの基底クラスは __自分で作ってください__
 - テンプレート使えます。ただし、テンプレートにView部分を表示させるには、 __`include $file_view;` させてください__
 - index コントローラの index アクションを作成すると、 __http://example.jp/ といったURLでアクセスできます__
  - index という名前は、`$config['index']` で __変更できます__
 - 404.php を作成すると、404ページになります
  - ただし、404.php には、MVCを __採用していません__
  - でも、404ファイルの位置は、 `$config['404_file']` で __変更できます__
 - そもそも、__開発者の思い通りに動いていない可能性がある__
  - PHPの学習が不十分な段階でフレームワークを作成したため
 - その他、設定項目は、 `$config` で変更できます

## 使い方

注) 以下、開発者の __願望が含まれている可能性があります__ 。

### 0. ファイル構成
 - http://example.jp/
  - .htaccess ( Apache 以外の方はご自身で作成してください )
  - oss_framework.php
  - index.php ( 添付されています )
   - __model__
   - __view__
     - __template__
   - __controller__

### 1. Controller を作る
```php
<?php
//controller/index_controller.php
class index_controller {

  public function __construct( $param ) {
    $this->param = $param;
  }

  public function index(){
    view( array( 'data' => $this->param[0] ) );
  }
}
```

### 2. View を作る
```php
<?php
//view/index/index.php
PARAM0:
<?php echo $array_view['data']; ?>
```

### 実行結果
http://example.jp/

### 3. Model は？
```php
<?php
//model/hoge_model.php
class hoge_model {

  public function __construct( $db ) {
    $this->db = $db;
  }

  public function get() {
    $sql = 'SELECT * FROM hoge_table WHERE col = :value';
    $conditions = array( ':value' => 13 );
    return $this->db->select( $sql, $conditions );
  }

}
```

```
//Controller
$model = model( 'hoge' );
$data = $model->get();
view( array( 'data' => $data ) );
```

## このフレームワークの趣旨について
このフレームワークは、いわゆる __オレオレフレームワーク__ です。面倒な書き方をしなくてもWebアプリが作れる、という程度を目指していますので、ほかのフレームワークのように機能は充実していません。
(強いて言うなら、htmlspecialchars と echo を同時に行う h 関数がこのフレームワークには存在します)

このフレームワークで、例えば、コントローラ名を省略して http://example.jp/action とアクセスされた場合、 index アクションの action メソッドが呼び出されるようにしたいという際は、ご自身で __oss_framework.php を編集していただくことになりますので__ よろしくお願いします。

なお、その際は、ぜひ、プルリクエストなどを行っていただけると幸いです。

## その他の使い方
oss_framework.php を解読すればわかるはずです。

## 注意
 - 本フレームワークは、現在、アルファのアルファとして提供しています。
 - 使用した際に生じた問題は、データの誤消去なども含めて保証できません。
 - 何かあれば、質問するなり、Pull RequestなりPull Requestなりしてください。

## ライセンスについて
MIT ライセンス

(詳細は、LICENSE.txt をご覧ください)
