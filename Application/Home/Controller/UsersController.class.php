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

	}
	/**
	* 验证注册
	*
	*/
	public function checkRegister(){

	}
	/**
	* 生成验证码图片
	*
	*/
	public function getVerify(){
		$verify = new \Think\Verify();
		$verify->entry();
	}

	

	
}