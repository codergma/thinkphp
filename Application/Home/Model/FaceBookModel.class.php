<?php
namespace Home\Model;
use Think\Model;
Vendor('phpQuery',VENDOR_PATH.'phpQuery');
Vendor('simple_html_dom',VENDOR_PATH.'simple_html_dom');


/**
* FaceBook
*/
class FaceBookModel extends Model
{
	public $base_url   = 'https://www.facebook.com/';
	public $email  = null;
	public $curl_opts = null;
	public $profile_href = null;

    public function __construct(){
    	$this->email = I('email');
    	if (!empty($this->email)) {
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
    	}
    	parent::__construct();
    }
	/**
	* 模拟登录
	* @param string 
	* @param string 
	* @return bool
*/
	public function login(){
		$pass	  = I('pass');
		if (empty($this->email) || empty($pass)) {
			return false;
		}

		$query = array(
			'lsd'=>'AVpYi9xw',
			'email'=>$this->email,
			'pass'=>$pass,
			'persistent'=>1,
			);
		foreach ($query as $key => $value) {
			$query[$key] = urlencode($value);
		}
		$query = http_build_query($query);

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

		$res = substr($content, 9,3);
		if ($res == '200') {
			$data = array();
			$data['email'] = $this->email;
			$data['pass']     = $pass;
			$data['login_status']   = -1;
            //$query = "INSERT INTO __PREFIX__face_book (email,pass,login_status) VALUES ('". $data['email']."','".$data['pass']."',".$data['login_status']. ") ON DUPLICATE KEY UPDATE login_status=".$data['login_status'];
  			$this->add($data,array(),true);
			return false;
		}else{
			$data = array();
			$data['email'] = $this->email;
			$data['pass']     = $pass;
			$data['login_status']   = 1;
            //$query = "INSERT INTO __PREFIX__face_book (email,pass,login_status) VALUES ('". $data['email']."','".$data['pass']."',".$data['login_status']. ") ON DUPLICATE KEY UPDATE login_status=".$data['login_status'];
  			$this->add($data,array(),true);

			return true;
		}
	}


	/**
	* 抓取用户profile_href
	* @return bool
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

		$data = array();
		$data['profile_href']  = $this->profile_href;
		$this->where('email = '.$this->email)->save($data);
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
	*
	*/
	public function catchFriendsNum(){
//		 $capt_opts = $this->curl_opts;
//		 $capt_opts[CURLOPT_URL] =  $this->profile_href;
//		 $ch = curl_init();
//		 curl_setopt_array($ch, $capt_opts);
//		 $content = curl_exec($ch);
//		 curl_close($ch);
        // $capt_opts = $this->curl_opts;
        // $capt_opts[CURLOPT_URL] =  "https://www.facebook.com/bin.liu.7923";
        // $ch = curl_init();
        // curl_setopt_array($ch, $capt_opts);
        // $content = curl_exec($ch);
        // curl_close($ch);
        // file_put_contents(CACHE_PATH.'request.html',$content);
		

		// $html = file_get_html('/home/liubin/Documents/thinkphp/Application/Runtime/Cache/request.html');
		// $out = '';
		// foreach($html->find('#globalContainer') as $e) 
		//    $out .= $e ;
		// return $out;

		$content = file_get_contents(CACHE_PATH.'request.html');
		\phpQuery::newDocumentHTML($content);
		return $friends_num = pq("#globalContainer")->html();

		// $dom = new \DOMDocument();
		// $dom->loadHTML($content);
		// $nodes = $dom->getElementsByTagName('a');
		// $out = '';
		// foreach ($nodes as $node) {
		//     echo $out .= $node->nodeValue;
		// }

		// return $out;
		
		//     return $book->nodeValue;


		$data = array();
		$data['friends_num']  = $friends_num;
		$this->where('email = '.$this->email)->save($data);
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
}