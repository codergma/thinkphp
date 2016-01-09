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
	public function run(){
		$rv = array('status' => 1);
		$m = D('FaceBook');

		// 1.登录
		$res = $m->login();
		if (!$res) {
			$rv['status'] = -1;
			$rv['msg']    = '登录失败';
			die(json_encode($rv));
		}

		// 2.抓取用户信息
		$m->captUserInfo();
		// $res = $m->addFriends();


	}

}