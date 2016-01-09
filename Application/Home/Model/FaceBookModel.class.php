<?php
namespace Home\Model;
use Think\Model;
Vendor('phpQuery',VENDOR_PATH.'phpQuery');


/**
* FaceBook
*/
class FaceBookModel extends Model
{
	public $base_url   = 'https://www.facebook.com/';
	public $username  = null;
	public $curl_opts = null;
	/**
	* 模拟登录
	* @param string 
	* @param string 
	* @return bool
	*/
	public function login(){
		$username = I('username');
		$pass	  = I('pass');
		if (empty($username) || empty($pass)) {
			return false;
		}

		$query = array(
			'lsd'=>'AVpYi9xw',
			'email'=>$username,
			'pass'=>$pass,
			'persistent'=>1,
			);
		foreach ($query as $key => $value) {
			$query[$key] = urlencode($value);
		}
		$query = http_build_query($query);

		// 必须带上这个cookie，facebook用来检测是否时通过浏览器登录的
		$cookie = 'fb_gate=https%3A%2F%2Fwww.facebook.com%2F; _js_reg_fb_ref=https%3A%2F%2Fwww.facebook.com%2F';
		$cookie_file = 	CACHE_PATH.'/Cookie/'.$username;
		$login_opts = array(
			CURLOPT_URL 		   => 'https://www.facebook.com/login.php?login_attempt=1&lwv=110',
			CURLOPT_POST		   => true,
			CURLOPT_POSTFIELDS	   => $query,
			CURLOPT_COOKIE   	   => $cookie,
			CURLOPT_USERAGENT	   => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
			CURLOPT_COOKIEJAR	   => $cookie_file,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_REFERER		   => 'https://www.facebook.com/',
			CURLOPT_AUTOREFERER	   => true,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER		   => 1,
			);

		$ch = curl_init();
		curl_setopt_array($ch,$login_opts);
		$content = curl_exec($ch);
		curl_close($ch);

		$res = substr($content, 9,3);
		if ($res == '200') {
			$data = array();
			$data['username'] = $username;
			$data['pass']     = $pass;
			$data['login_status']   = -1;
            //$query = "INSERT INTO __PREFIX__face_book (username,pass,login_status) VALUES ('". $data['username']."','".$data['pass']."',".$data['login_status']. ") ON DUPLICATE KEY UPDATE login_status=".$data['login_status'];
  			$this->add($data,array(),true);
			return false;
		}else{
			$data = array();
			$data['username'] = $username;
			$data['pass']     = $pass;
			$data['login_status']   = 1;
            //$query = "INSERT INTO __PREFIX__face_book (username,pass,login_status) VALUES ('". $data['username']."','".$data['pass']."',".$data['login_status']. ") ON DUPLICATE KEY UPDATE login_status=".$data['login_status'];
  			$this->add($data,array(),true);
  			//登录成功初始化成员变量
			$this->username    = $username;
			$this->curl_opts    = array(
				CURLOPT_COOKIEFILE     => $cookie_file, 
				CURLOPT_USERAGENT	   => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_REFERER		   => 'https://www.facebook.com/',
				CURLOPT_AUTOREFERER	   => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER		   => 1,
				);
			return true;
		}
	}

	/**
	* 添加好友
	* 
	*/
	public function addFriends(){

	}

	/**
	* 抓取用户关键信息
	*
	*/
	public function captUserInfo(){
//		$opts = $this->curl_opts;
//		$opts[CURLOPT_URL] =  $this->base_url;
//		$ch = curl_init();
//		curl_setopt_array($ch, $opts);
//		$content = curl_exec($ch);
//		curl_close($ch);

		//用户id,first_name,last_name
		\phpQuery::newDocumentFile();



	}
	
}