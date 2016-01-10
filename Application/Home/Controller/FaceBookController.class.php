<?php
namespace Home\Controller;
use Think\Controller;

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
		$this->display('default/facebook');
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