<?php
namespace Home\Controller;
use Think\Controller;
require_once VENDOR_PATH.'FaceBook/autoload.php';

/**
* FaceBook 抓取类
*/
class FaceBookController extends Controller
{
	/**
	* 
	*
	*/
	public function facebook(){
		$fb = new \Facebook\Facebook([
		  'app_id' => '566699746818875',
		  'app_secret' => '8bef1e8c1f64d8b1c602ceece2e4da9b',
		  'default_graph_version' => 'v2.5',
		]);
		$helper = $fb->getRedirectLoginHelper();
		// $permissions = ['email', 'user_likes']; // optional
		$loginUrl = $helper->getLoginUrl('http://localhost/tp/index.php/Home/FaceBook/loginCallback', $permissions);

		$this->show('<a href="' . $loginUrl . '">Log in with Facebook!</a>');
	// $this->display('default/facebook');
	}
	public function loginCallback(){
		$fb = new \Facebook\Facebook([
		  'app_id' => '566699746818875',
		  'app_secret' => '8bef1e8c1f64d8b1c602ceece2e4da9b',
		  'default_graph_version' => 'v2.5',
		]);

		$helper = $fb->getRedirectLoginHelper();
		try {
		  $accessToken = $helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		if (isset($accessToken)) {
		  // Logged in!
		  $_SESSION['facebook_access_token'] = (string) $accessToken;

		  // Now you can redirect to another page and use the
		  // access token from $_SESSION['facebook_access_token']
		}
	}



	public function run(){
		$rv = array('status' => 1);
		$m = D('FaceBook');
/*
		// 1.登录
		$res = $m->login();
		if (!$res) {
			$rv['status'] = -1;
			$rv['msg']    = '登录失败';
			die(json_encode($rv));
		}

		// 2.抓取用户信息
		// header("Content-type:text/html;charset=utf-8");
		$m->captUserInfo();
*/
		// 3.确认好友请求
		$m->acceptFriend();
		// $res = $m->addFriends();

	}

}