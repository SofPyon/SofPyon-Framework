<?php

/**
 *
 * オープンソース版 SofPyon Framework
 *
 **/

## 基本情報

 //// データベース定義 ////
 class db {

   const DB_NAME = ''; //DB名
   const HOST    = 'localhost'; //localhost など
   const UTF     = 'utf8'; //utf8 など
   const USER    = ''; //DBユーザー名
   const PASS    = ''; //DBパスワード

 }

 //// データベース接続 ////
 class dbConnect {

   //PDOを格納する
   private $pdo;

   //データベースに接続する関数
   function __construct( $host, $dbname, $utf, $dbuser, $dbpass ){
     $this->pdo = new PDO("mysql:host={$host}; dbname={$dbname}; charset={$utf}", $dbuser, $dbpass, array( PDO::ATTR_EMULATE_PREPARES => false ) );
     $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }

   //SELECT文を実行する(１件取得)
   //$sql, $condition = ""
   function selectSingle( $sql, $condition = array() ){
     //$sql = "select * from {テーブル} where {カラム} = :placeholder";
     //$condition = array(
     //  ":placeholder"   => $hoge,
     //);
     $statement = $this->pdo->prepare( $sql );
     $statement->execute( $condition );
     $result = $statement->fetch();
     $statement->closeCursor();
     return $result;
   }

   //SELECT文を実行する
   //$sql, $condition = ""
   function select( $sql, $condition = array() ){
     //$sql = "select * from {テーブル} where {カラム} = :placeholder";
     //$condition = array(
     //  ":placeholder"   => $hoge,
     //);
     $statement = $this->pdo->prepare( $sql );
     $statement->execute( $condition );
     $result = $statement->fetchAll();
     $statement->closeCursor();
     return $result;
   }

   //いろいろ文を実行する
   //$sql, $condition = ""
   function sql( $sql, $condition = array() ){
     //$sql = "update {テーブル名} set {カラム1} = :placeholder1, {カラム2} = :placeholder2 where {カラム3} = :placeholder3";
     //$condition = array(
     //  ":placeholder1"   => $hoge1,
     //  ":placeholder2"   => $hoge2,
     //  ":placeholder3"   => $hoge3,
     //);
     $statement = $this->pdo->prepare($sql);
     $result = $statement->execute( $condition );
     $statement->closeCursor();
     return $result;
   }

 }

 $db = new dbConnect( db::HOST, db::DB_NAME, db::UTF, db::USER, db::PASS );

 //// 各種設定(index.phpで上書きできます) ////
 if( !isset( $config ) || !is_array( $config ) )                      $config = array();

 //// 呼び出し元の設定 ////
 $associative_array = debug_backtrace();
 $root = dirname( $associative_array[0]["file"] );
 if( !isset( $config['root'] ) || $config['root'] == '' )             $config['root']       = $root;

 //// その他の設定 ////
 if( !isset( $config['model'] ) || $config['model'] == '' )           $config['model']      = $config['root']. '/model';
 if( !isset( $config['view'] ) || $config['view'] == '' )             $config['view']       = $config['root']. '/view';
   if( !isset( $config['template'] ) || $config['template'] == '' )   $config['template']   = $config['view']. '/template';
 if( !isset( $config['controller'] ) || $config['controller'] == '' ) $config['controller'] = $config['root']. '/controller';
 if( !isset( $config['404_file'] ) || $config['404_file'] == '' )     $config['404_file']   = $config['root']. '/404.php';

 //// 以下、どうでもいい設定項目 ////
 if( !isset( $config['index'] ) || $config['index'] == '' )           $config['index']      = 'index';
 if( !isset( $config['get_param'] ) || $config['get_param'] == '' )   $config['get_param']  = 'query';

## ルーティング

 //// 404 かどうか ////
 $is_404 = false;

 //// GETの内容を読み取る ////
 if( isset( $_GET[ $config['get_param'] ] ) ){
   //GET取得
   $get = $_GET[ $config['get_param'] ];
   //スラッシュで分割
   $param = explode("/", $get);
   //param1がないとき
   if( $param[0] == '' ){
     $param[0] = $config['index'];
     $param[1] = $config['index'];
   }
   //param2がないとき
   if( $param[1] == '' ){
     $param[1] = $config['index'];
   }
 }else{
   //paramを適当に
   $param = array();
   $param[0] = $config['index'];
   $param[1] = $config['index'];
 }

 //// PARAM0 と PARAM1 を用意 ////
 define( 'PARAM0', $param[0] );
 define( 'PARAM1', $param[1] );

 //// １つ目のパラメータ = コントローラ名 ////
    //コントローラ名
    $name_controller;

    //PARAM0が空欄でないかどうか
    if( PARAM0 != '' ){
      /* PARAM0が存在する  */
      //コントローラ名
      $name_controller = strtolower( PARAM0 ). '_controller';
      //ファイルは存在するか
      $file_controller = $config['controller']. '/'. $name_controller. '.php';
      if( file_exists( $file_controller ) ){
        /* コントローラファイルが存在する */

        //読み込む
        require_once $file_controller;
        //クラスが存在するか
        if( class_exists( $name_controller, false ) ){
          /* コントローラクラスが存在する */

          //インスタンス化
          $obj_controller = new $name_controller( $param );
          //メソッド読み出し可能かどうか( 2つ目のパラメータ = メソッド名 )
          if( PARAM1 == '' ){
            /* PARAM1 がそもそも空 */
            if( is_callable(array($obj_controller, $config['index'])) ){
              call_user_func(array($obj_controller, $config['index']));
            }
          }elseif( is_callable(array($obj_controller, PARAM1)) ){
            /* (可能)コントローラのメソッド(アクション)が呼び出し可能 */

            call_user_func(array($obj_controller, PARAM1));
          }else{
            /* (不可能)コントローラのメソッド(アクション)が呼び出し不可能 */

            $is_404 = true;
          }
        }else{
          /* コントローラクラスが存在しない */
          $is_404 = true;
        }
      }else{
        /* コントローラファイルが存在しない */

        ///indexコントローラのメソッドかどうか確認
        //コントローラ名
        $name_controller = $config['index']. '_controller';
        //ファイルは存在するか
        $file_controller = $config['controller']. '/'. $name_controller. '.php';
        if( file_exists( $file_controller ) ){
          /* indexコントローラファイルが存在する */

          //読み込む
          require_once $file_controller;
          //クラスが存在するか
          if( class_exists( $name_controller, false ) ){
            /* indexコントローラクラスが存在する */

            //インスタンス化
            $obj_controller = new $name_controller( $param );
            //メソッド読み出し可能かどうか( 1つ目のパラメータ = メソッド名 )
            if( is_callable(array($obj_controller, PARAM0)) ){
              /* indexコントローラメソッド可能 */
              call_user_func(array($obj_controller, PARAM0));
            }else{
              /* indexコントローラメソッド不可能 */
              $is_404 = true;
            }
          }else{
            /* indexコントローラクラスが存在しない */
            $is_404 = true;
          }
        }else{
          /* indexコントローラファイルが存在しない */
          $is_404 = true;
        }
      }
    }else{
      /* PARAM0 が空欄 */

      //コントローラ名
      $name_controller = $config['index']. '_controller';
      //ファイルは存在するか
      $file_controller = $config['controller']. '/'. $name_controller. '.php';
      if( file_exists( $file_controller ) ){
        /* indexコントローラ存在する */

        //読み込む
        require_once $file_controller;
        //クラスが存在するか
        if( class_exists( $name_controller, false ) ){
          /* indexクラス存在する */

          //インスタンス化
          $obj_controller = new $name_controller( $param );
          //メソッド読み出し可能かどうか( $config['index'] = メソッド名 )
          if( is_callable(array($obj_controller, $config['index']) ) ){
            /* indexメソッド読み出し可能 */
            call_user_func(array($obj_controller, $config['index']));
          }else{
            /* indexメソッド読み出し不可能 */
            $is_404 = true;
          }
        }else{
          /* indexクラス存在しない */
          $is_404 = true;
        }
      }else{
        /* indexコントローラ存在しない */

        $is_404 = true;
      }
    }

## 404の場合
if( $is_404 ){
  //404を返す
  header("HTTP/1.1 404 Not Found");
  //404表示ファイルが存在するか
  if( file_exists( $config['404_file'] ) ){
    //404ファイル表示
    include $config['404_file'];
  }else{
    //404ファイル存在しないので独自表示
    echo '<h1>404 Not Found</h1>';
  }
}

## モデル読み出し
/**
* モデル読み出し
*
* @param string $name モデル名
* @return [object|null] モデルオブジェクト
*/
 function model( $name ) {
   //config
   global $config;

   //DB接続クラス
   global $db;

   //モデル名
   $name_model = strtolower( $name ). '_model';

   //ファイルは存在するか
   $file_model = $config['model']. '/'. $name_model. '.php';
   if( file_exists( $file_model ) ){
     //読み込む
     require_once $file_model;
     //クラスが存在するか
     if( class_exists( $name_model, false ) ){
       //インスタンス化
       $obj_model = new $name_model( $db );
       return $obj_model;
     }else{
       //クラスが存在しない
       return null;
     }
   }else{
     //モデルファイルが存在しない
     return null;
   }
 }

## ビュー読み出し
/**
* ビュー読み出し
*
* @param array $array_view ビューに渡す設定
* @param string $template テンプレート名
* @param array $array_template テンプレートに渡す設定
* @param string $name ビュー名
* @param string $dir コントローラフォルダ名
* @return bool 正常にデータを echo できたかどうか
*/
 function view(
   $array_view     = array(),
   $template       = '',
   $array_template = array()
   $name           = PARAM1,
   $dir            = PARAM0,
 ) {
   //config
   global $config;

   //DB接続クラス
   global $db;

   //ビュー名
   $name_view = strtolower( $name );

   //ビューの入っているフォルダ名
   $dir_view = strtolower( $dir );

   //テンプレート名
   if( $template != '' ){
     $name_template = strtolower( $template );
     $file_template = $config['template']. '/'. $name_template. '.php';
   }else{
     $name_template = null;
   }

   //ビューファイルは存在するか
   $file_view = $config['view']. '/'. $dir_view. '/'. $name_view. '.php';
   if( file_exists( $file_view ) ){
     //viewする
     //テンプレートファイルが存在するか
     if( $name_template != null && file_exists( $file_template ) ){
       //テンプレート内で必ず、include $file_view; を実行すること
       include $file_template;
     }else{
       //テンプレートが無いとき、直接viewする
       include $file_view;
     }
   }else{
     //ビューファイルが存在しない
     return null;
   }
 }
