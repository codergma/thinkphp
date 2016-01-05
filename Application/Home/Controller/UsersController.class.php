<?php
namespace Home\Controller;
use Think\Controller;
/**
* 
*/
class UsersController extends Controller
{
	
	
	public function test()
	{
		$this->display('default/test');
	}
	/**
	* 跳去登录页面
	*
	*/
	public function login(){
		//如果已经登录了则直接跳去首页
		$USER = session('CG_USER');
		if(!empty($USER)){
			$this->redirect("Users/index");
		}
		$this->display('default/login');
	}
	/**
	* 跳去注册页面
	*
	*/
	public function register(){
		$this->display('default/register');
	}
	/**
	* 验证登录
	*
	*/
	public function checkLogin(){
		$rv = array();
		$rv['status'] = 1;
		if(!$this->checkVerify())
		{
			$rv['status'] = -1;//验证码错误
			die(json_encode($rv));
		}

		$m = D('Users');
		$res = $m->checkLogin();

		if ($res['status'] == -1) {
			$rv['status'] = -2;//用户名或密码错误
			die(json_encode($rv));
		}

		die(json_encode($rv));
	}
	/**
	* 验证注册
	* @return json 1/-1/-2/..  成功;验证码错误;用户名/邮箱/密码为空;用户名已经存在;邮箱已经被注册
	*/
	public function checkRegister(){
		$rv = array();
		$rv['status'] = 1;

		if (!$this->checkVerify()) {
			$rv['status'] = -1;//验证码错误
			die(json_encode($rv));
		}

		$m = D('Users');
		$res = $m->checkRegister();
		if ($res['status'] < 0) {
			$rv['status'] = $res['status'];
			die(json_encode($rv));
		}

		die(json_encode($rv));
	}
	/**
	* 生成验证码图片
	*
	*/
	public function getVerify(){
		$verify = new \Think\Verify();
		$verify->entry();
	}
	/**
	* 验证码校验
	*
	*/
	public function checkVerify(){
		$verify =  new \Think\Verify();
		return $verify->check(I('captcha'));
	}

	

	
}