<?php
namespace App;
require __DIR__.'/vendor/autoload.php';

use MySQLi;
use RAWebService\REST;

class API extends REST {
  public $data = "";
  const DB_SERVER = "localhost";
  const DB_USER = "usuario";
  const DB_PASSWORD = "contraseña";
  const DB = "simple_news";

  private $db = NULL;

  public function __construct(){
    parent::__construct();				// Init parent contructor
    $this->dbConnect();					// Initiate Database connection
  }

  /*
  *  Database connection
  */
  private function dbConnect(){
      $this->db = mysqli_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD,self::DB);
  }
  /*
  * Public method for access api.
  * This method dynmically call the method based on the query string
  *
  */
  public function processApi(){
    $func = strtolower(trim(str_replace("/","",$_REQUEST['rquestMethod'])));
    if((int)method_exists($this,$func) > 0)
    $this->$func();
    else
    $this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
  }
  private function getArticles(){

    if($this->get_request_method() != "GET"){
      $this->response('',406);
    }

    $sql = mysqli_query($this->db,"SET NAMES utf8;");

    $sql = mysqli_query($this->db,"SELECT * FROM article");
    $result = array();
    if($sql->num_rows > 0){
      while($rlt = mysqli_fetch_array($sql,MYSQLI_ASSOC)){
        $result[] = $rlt;
      }
    }

    $this->response($this->json($result), 200);

  }

  private function postArticles(){

    if($this->get_request_method() != "POST"){
      $this->response('',406);
    }

    $title = $this->_request['title'];
    $body = $this->_request['body'];

    $sql = mysqli_query($this->db,"SET NAMES utf8;");

    $sql = mysqli_query($this->db,"INSERT INTO article (title, body) VALUES('$title', '$body')");
    if($sql)
      $result = ['msg' => "Insertado!"];
    else
      $result = ['msg' => "Falló!"];

    $this->response($this->json($result), 200);

  }


  /*	Encode array into JSON
  */
  public function json($data){
    if(is_array($data)){
      return json_encode($data);
    }
  }
}


$api = new API;
$api->processApi();
