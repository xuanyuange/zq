<?php
namespace Home\Controller;
use Think\Controller;
use LaneWeChat;
use LaneWeChat\Core\Wechat;
use LaneWeChat\Autoloader;
class IndexController extends Controller {
    public function index(){
    	echo "10086";
    	//调用自动载入函数
		//AutoLoader::register();
		//初始化微信类
		//$wechat = new WeChat(WECHAT_TOKEN, TRUE);	
		//首次使用需要注视掉下面这1行（26行），并打开最后一行（29行）
		//echo $wechat->run();
		//首次使用需要打开下面这一行（29行），并且注释掉上面1行（26行）。本行用来验证URL
		//$wechat->checkSignature();
    }
}