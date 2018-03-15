<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\WeixinModel;
use Home\Model\KeywordModel;
class WXController extends Controller {
	//公众号操作
	public function index(){
		$echoStr = $_GET["echostr"];
		if($this->checkSignature()&&isset($echoStr)){
			echo $echoStr;
			exit;
		}else{
			$this->responseMsg($GLOBALS["HTTP_RAW_POST_DATA"]);
		}
	}
	
	//解析公众号操作的数据
	public function responseMsg($postStr)
	{
		if (!empty($postStr)){
			libxml_disable_entity_loader(true);
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername=$postObj->ToUserName;		
			$keyword = trim($postObj->Content);
			//下面变量均为添加 zq
			$msgType=$postObj->MsgType;
			$event=$postObj->Event;			
			$openid=''.trim($fromUsername);//要变成字符串才能保存
			//cookie('openid',$openid);//放到缓存中
			$weixin= new WeixinModel();
			$weixin->get_wx($openid);			
			switch($msgType){
				case "event":
					if($event=="subscribe"){
						$this->return_text($fromUsername,$toUsername,"欢迎关注强哥的公众号~");
					}
					if($event=="CLICK"){
						//暂无
					}
					if($event=="LOCATION"){
						//暂无
					}
					break;
				case "text":
					$keywords=new KeywordModel();
					$key=$keywords->find_keyword($keyword);//查找关键字对应返回的类型
					$type=$key['return_type'];//类型
					$id=$key['return_id'];//id					
					if($type=='0'){
						$keyword=$keywords->find_text($id);
						$this->return_text($fromUsername,$toUsername,$keyword);
					}
					if($type=='1'){
						$aaa=explode("|", $id);
						if(count($aaa)=='1'){
							$keyword_s=$keywords->find_graphic($id);//查找单条图文
							$keyword=array($keyword_s);
						}else{
							$keyword=$keywords->find_graphics($aaa);//查找多条图文							
						}	
						$this->return_graphic($fromUsername,$toUsername,$keyword);
					}
					$this->return_text($fromUsername,$toUsername,$keyword);
					break;
				default:
					$this->return_text($fromUsername,$toUsername,"暂无此功能");
			}
		}else {
			echo "请在微信中操作~";
			exit;
		}
	}
	//验证签名
	private function checkSignature()
	{
		if (!defined("TOKEN")) {
			throw new Exception('TOKEN is not defined!');
		}	
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];	
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
	
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	public function test(){
		$keywords=new KeywordModel();
		$key=$keywords->find_keyword("测试");//查找关键字对应返回的类型
		$type=$key['return_type'];//类型
		$id=$key['return_id'];//id
		if($type=='0'){
			$keyword=$keywords->find_text($id);
			$this->return_text($keyword);
		}
		if($type=='1'){	
			$aaa=explode("|", $id);
			if(count($aaa)=='1'){
				$keyword=$keywords->find_graphic($id);//查找单条图文
			}else{
				$keyword=$keywords->find_graphics($aaa);//查找多条图文
			}					
			$this->return_graphic($keyword);
		}
	}
	//回复文本
	public function return_text($fromUsername,$toUsername,$msg){
		$time=time();
		$xml="<xml>
		<ToUserName><![CDATA[$fromUsername]]></ToUserName>
		<FromUserName><![CDATA[$toUsername]]></FromUserName>
		<CreateTime>$time</CreateTime>
		<MsgType><![CDATA[text]]></MsgType>
		<Content><![CDATA[$msg]]></Content>
		</xml>";
		echo $xml;die;
	}
	/**
	 * /回复图文
	 * @param unknown $fromUsername
	 * @param unknown $toUsername
	 * @param unknown $data
	 * @param string $single 默认是单个图文
	 */
	public function return_graphic($fromUsername,$toUsername,$data){		
		$count=count($data);
		$time=time();
		$xml="<xml>
		<ToUserName><![CDATA[$fromUsername]]></ToUserName>
		<FromUserName><![CDATA[$toUsername]]></FromUserName>
		<CreateTime>$time</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>$count</ArticleCount>
		<Articles>";		
		foreach ($data as $key=>$value){
			$title=$value['title'];//标题
			$description=$value['description'];//简述
			$picurl=$value['picurl'];//图片链接
			$url=$value['url'];//链接地址
			$flag=$value['flag'];//是否添加openid
			if($flag){
				$url.="/openid/".$toUser."/type/weixin";
			}
				$xml.="<item>
						<Title><![CDATA[$title]]></Title>
				<Description><![CDATA[$description]]></Description>
				<PicUrl><![CDATA[$picurl]]></PicUrl>
				<Url><![CDATA[$url]]></Url>
				</item>";
		}
		$xml.="</Articles>
		</xml>";
		echo $xml;die;
	}
}