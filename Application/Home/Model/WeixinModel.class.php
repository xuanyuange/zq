<?php
namespace Home\Model;
use Think\Model;
class WeixinModel extends Model{	
	protected $tableName = 'wx_user';
	//查找微信用户
	public function find_user($where){
		return M("wx_user")->where($where)->find();
	}
	//添加微信用户
	public function add_user($data){		
		return M("wx_user")->add($data);
	}
	//记录微信号
	public function get_wx($fromUsername){
		$res=M('wx_user')->where(array('openid'=>$fromUsername))->find();
		if(!$res){
			$data['openid']=$fromUsername;
			$data['add_time']=date("Y-m-d H:i:s");
			$data['update_time']=date("Y-m-d H:i:s");
			M('wx_user')->add($data);
		}
	}
	
}