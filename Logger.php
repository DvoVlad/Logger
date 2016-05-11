<?
header('Content-Type: text/html; charset= utf-8');
define(HOST,'localhost');
define(BASE,'logger');
define(USER,'root');
define(PASSWORD,'');
 class DB{
	 private static $db = null;
	 private static $host = null;
	 private static $user = null;
	 private static $password = null;
	 private static $base = null;
	 public static function getDB(){
			 if(!(self::$db)){
				if(isset(self::$host)&&isset(self::$user)&&isset(self::$password)&&isset(self::$base)){
					self::$db = new mysqli(self::$host,self::$user,self::$password,self::$base);
				if(self::$db->connect_errno){
					echo "Не удалось подключиться к MySQL: " . self::$db->connect_error;
				}
			}else{
				exit('Настройки БД не установлены!');
			}
		 }
		 return self::$db;
	 }
	 public static function setHost($host){
		self::$host = $host;
	 }
	 public static function setUser($user){
		self::$user = $user;
	 }
	 public static function setPassword($password){
		self::$password = $password;
	 }
	 public static function setBase($base){
		self::$base = $base;
	 }
	final private function __construct(){
	}
	final private function __clone(){}
}
class Logger{
	private static $filep = '/log.txt';
	private static $logtype = 'MySQL';
	public static function setFilePath($path){
		if(!empty($path)){
			self::$filep = $path;
		}
	}
	public static function setLogType($logtype){
		self::$logtype = $logtype;
	}
	public function saveMessage($mess){
     switch(self::$logtype){
      case 'MySQL':
      $db = DB::getDB();
      if($mess instanceof Exception){
        $mess = $mess->getMessage();
        $stmt = $db->query("INSERT INTO `logs`(`Message`)VALUES('".$db->real_escape_string($mess)."')");
      }elseif(is_array($mess)||is_object($mess)){
        $mess = serialize($mess);
        $stmt = $db->query("INSERT INTO `logs`(`Message`)VALUES('".$db->real_escape_string($mess)."')");
      }elseif(is_string($mess)){
        $stmt = $db->query("INSERT INTO `logs`(`Message`)VALUES('".$db->real_escape_string($mess)."')");
      }
      break;
      case 'FILE':
      if($mess instanceof Exception){
        $mess = $mess->getMessage().' Message time:'.date('Y-m-d H:i:s').'\r\n';
        file_put_contents($filep,$mess,FILE_APPEND);
      }elseif(is_array($mess)||is_object($mess)){
        print_r($mess,$logmess);
        $logmess = $logmess.' Message time:'.date('Y-m-d H:i:s').'\r\n';
        file_put_contents($filep,$logmess,FILE_APPEND);
      }elseif(is_string($mess)){
        $mess = $mess.' Message time:'.date('Y-m-d H:i:s').'\r\n';
        file_put_contents($filep,$mess,FILE_APPEND);
      }
      break;
      case 'stdout':
      if($mess instanceof Exception){
        $mess = $mess->getMessage().' Message time:'.date('Y-m-d H:i:s').'\r\n';
        echo $mess;
      }elseif(is_array($mess)||is_object($mess)){
        print_r($mess,$logmess);
        $logmess ='<pre>'.$logmess.'</pre> Message time:'.date('Y-m-d H:i:s').'\r\n';
        echo $logmess;
      }elseif(is_string($mess)){
        $mess = $mess.' Message time:'.date('Y-m-d H:i:s').'\r\n';
        echo $mess;
      }
      break;
     }
   }
}
DB::setHost(HOST);
DB::setUser(USER);
DB::setPassword(PASSWORD);
DB::setBase(BASE);
DB::getDB();
$mess="something";
$logger = new Logger;
$logger->saveMessage($mess);
?>
