# SofPyon Framework( Ver. α of α )
MVC風なことができるシンプルなPHPフレームワーク(超未完成)

##このプログラムのレベル
 - __動かない！と思ったら .htaccess を忘れていた__
  - <a href="http://sofpyon.github.io/about_applevel.html" target="_blank">この表記について</a>

##特徴
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

##使い方

注) 以下、開発者の __願望が含まれている可能性があります__ 。

###0. ファイル構成
 - http://example.jp/
  - .htaccess ( Apache 以外の方はご自身で作成してください )
  - oss_framework.php
  - index.php ( 添付されています )
   - __model__
   - __view__
     - __template__
   - __controller__

###1. Controller を作る
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

###2. View を作る
```php
<?php
//view/index/index.php
PARAM0:
<?php echo $array_view['data']; ?>
```

###実行結果
http://example.jp/

###3. Model は？
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

## その他の使い方
oss_framework.php を解読すればわかるはずです。

## 注意
 - 本フレームワークは、現在、アルファのアルファとして提供しています。
 - 使用した際に生じた問題は、データの誤消去なども含めて保証できません。
 - 何かあれば、質問するなり、Pull RequestなりPull Requestなりしてください。

## ライセンスについて
MIT ライセンス

(詳細は、LICENSE.txt をご覧ください)
