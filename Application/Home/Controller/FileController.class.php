<?php
namespace Home\Controller;
use Think\Controller;

/**
* 文件服务器
*
*/
class FileController extends Controller
{
	
	function __construct() {
		parent::__construct();
	}

	public function download(){
		$file = RUNTIME_PATH.'Files'.'/中文.txt';
		// $file_name = basename($file); //basename()不支持中文
		$file_name = array_pop(explode( '/',$file));
		$encoded_filename = rawurlencode($file_name);
		header('Content-type: application/octet-stream');

		//处理中文文件名
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if (preg_match('/MSIE/i', $ua) || preg_match('/Trident/i', $ua)) {
			header('Content-Disposition: attachment; filename="'.$encoded_filename.'"');
		}else if (preg_match('/Firefox/i', $ua)) {
			header('Content-Disposition: attachment; filename*="utf8\'\''.$file_name.'"');
		}else{
			 header('Content-Disposition: attachment; filename="' . $file_name . '"');
		}
		/*
		header('Content-Length:'.filesize($file));
		readfile($file);
		虽然PHP的readfile尝试实现的尽量高效, 不占用PHP本身的内存, 但是实际上它还是需要采用MMAP(如果支持),
		或者是一个固定的buffer去循环读取文件, 直接输出. 输出的时候, 如果是Apache + PHP mod, 那么还需要发送到Apache的输出缓冲区.
		最后才发送给用户. 而对于Nginx + fpm如果他们分开部署的话, 那还会带来额外的网络IO.
		Apache的module mod_xsendfile, 让Apache直接发送这个文件给用户:
		*/

		// sudo apt-get install libapache2-mod-xsendfile
		// a2enmod xsendfile
		// 在apache2虚拟主机配置中开启， XSendFile On
		header("X-Sendfile: $file");
		exit();
	}
	
}