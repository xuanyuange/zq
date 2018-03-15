<?php
namespace Home\Model;
use Think\Model;
class KeywordModel extends Model{	
	//protected $tableName = 'keyword';
	//查找关键字表信息
	public function find_keyword($keyword){
		return M("keyword")->where(array('keyword'=>array('like','%'.$keyword.'%'),'deleted'=>'0' ))->order("sort desc")->field('return_type,return_id')->find();
	}
	//查找文本表
	public function find_text($id){
		return M("keyword_text")->where(['id'=>$id,'deleted'=>0])->getField("content");
	}
	//查找图文表
	public function find_graphic($id){
		return M("keyword_graphic")->where(['id'=>$id,'deleted'=>0])->find();
	}
	//查找多个图文
	public function find_graphics($ids){
		$array=array();
		foreach($ids as $k=>$v){
			$array[]=M("keyword_graphic")->where(['id'=>$v,'deleted'=>0])->find();
		}
		return $array;
		//return M("keyword_graphic")->where(['id'=>$id,'deleted'=>0])->find();
	}
	
}