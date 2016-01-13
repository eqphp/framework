<?php
//rely on: logger
class mg{

	protected $connection,$database,$collection;
	
	//获取mongo对象-集合
	function __construct($collection='',$database=null){
		try{
			$server=config('server','mongo');
			if (phpversion('Mongo') >= 1.3) {
				$this->connection=new MongoClient($server);
			} else {
				$this->connection=new Mongo($server);
			}
			if (is_null($database)) {
				$database=preg_replace('/^mongodb:\/\/.*\//','',$server);
			}
			$this->database=$this->connection->$database;
			$collection and $this->collection=$this->database->$collection;
		} catch(Exception $e) {
			throw new Exception($e->getMessage(),105);
		}
	}

	function __get($name){
		if (in_array($name,array('connection','database','collection'))) {
			return $this->{$name};
		}
		throw new Exception('forbid visit mongo class property: '.$name,106);
	}

	//创建文档
	function post($data,$method='batchInsert',$option=array()){
		$method=in_array($method,array('batchInsert','insert','save')) ? $method : 'insert';
		$fun=$method !== 'save' ? 'insert' : 'save';
		logger::mongo("db.{$this->collection}.{$fun}(".json_encode($data).','.json_encode($option).')',false);
		return $this->collection->$method($data,$option);
	}

	//修改文档
	function patch($condition,$data,$is_all=true,$is_insert=false){
		$option=array('upsert'=>(bool)$is_insert,'multiple'=>(bool)$is_all);
		logger::mongo("db.{$this->collection}.update(".json_encode($condition).','.json_encode($data).','.json_encode($option).')',false);
		return $this->collection->update($condition,$data,$option);
	}

	//删除文档
	function delete($condition,$is_one=false){
		$option=array('justOne'=>(bool)$is_one);
		logger::mongo("db.{$this->collection}.remove(".json_encode($condition).','.json_encode($option).')',false);
		return $this->collection->remove($condition,$option);
	}

	//获取文档的一个节点
	function node($node,$condition=array(),$sort=array()){
		$field=array($node=>1,'_id'=>0);
		if ($sort && is_array($sort)) {
			$sort=self::process_sort($sort);
			$this->record_query_history($field,$condition,$sort,1);
			$result=$this->collection->find($condition,$field)->sort($sort)->limit(1);
			$result=iterator_to_array($result);
			return array_get($result[0],$node);
		}
		$this->record_query_history($field,$condition,null,1);
		$result=$this->collection->findOne($condition,$field);
		return array_get($result,$node);
	}

	//获取一个文档
	function document($field,$condition=array(),$sort=array()){
		$field=self::format_file_node($field);
		if ($sort && is_array($sort)) {
			$sort=self::process_sort($sort);
			$this->record_query_history($field,$condition,$sort,1);
			$result=$this->collection->find($condition,$field)->sort($sort)->limit(1);
			$result=iterator_to_array($result);
			return $result[0];
		}
		$this->record_query_history($field,$condition,null,1);
		return $this->collection->findOne($condition,$field);
	}
	
	//获取一批文档
	function batch($field='',$condition=array(),$sort=array(),$limit=0,$skip=0){
		$field=self::format_file_node($field);
		$result=$this->collection->find($condition,$field);
		if ($sort && is_array($sort)) {
			$sort=self::process_sort($sort);
			$result=$result->sort($sort);
		}
		if ($limit >= 1) $result=$result->limit($limit);
		if ($skip >= 1) $result=$result->skip($skip);
		$this->record_query_history($field,$condition,$sort,$limit,$skip);
		return iterator_to_array($result);
	}

	//分页查询
	function page($field='',$condition=array(),$sort=array(),$page,$page_size=20){
		$document_amount=$this->collection->count($condition);
		logger::mongo("db.{$this->collection}.count(".json_encode($condition).')');
		if ($document_amount < 1) return array(0,1,array());

		$page_count=ceil($document_amount/$page_size);
		$page=max(1,$page);
		$page=($page > $page_count) ? $page_count : $page;
		$offset=($page_size > $document_amount) ? 0 : (($page-1)*$page_size);

		$field=self::format_file_node($field);
		$result=$this->collection->find($condition,$field);
		if ($sort && is_array($sort)) {
			$sort=self::process_sort($sort);
			$result=$result->sort($sort);
		}
		if ($page_size >= 1) $result=$result->limit($page_size);
		if ($offset >= 1) $result=$result->skip($offset);
		$this->record_query_history($field,$condition,$sort,$page_size,$offset);
		return array($document_amount,$page_count,iterator_to_array($result));
	}

	//统计查询
	function tally(){
		//TODO

	}
	
	//记录查询操作日志
	private function record_query_history($field,$condition,$sort=array(),$limit=0,$skip=0){
		list($condition,$field)=array(json_encode($condition),json_encode($field));
		$command="db.{$this->collection}.find({$condition},{$field})";
		if ($sort && is_array($sort)) $command.='.sort('.json_encode($sort).')';
		if ($limit >= 1) $command.=".limit({$limit})";
		if ($skip >= 1) $command.=".skip({$skip})";
		logger::mongo($command);
	}

	//处理排序数据
	static function process_sort($data=array()){
		$buffer=array();
		foreach ($data as $node=>$value) {
			if ($value === -1 || $value === false || strtolower($value) === 'desc') {
				$buffer[$node]=-1;
			}
			if ($value === 1 || $value === true || strtolower($value) === 'asc') {
				$buffer[$node]=1;
			}
		}
		return $buffer;
	}
	
	//格式化查询节点（字段）
	static public function format_file_node($field){
		$data=array();
		if ($field && $field !== '*') {
			$node=explode(',',$field);
			$dest=array_fill(0,count($node),1);
			$data=array_combine($node,$dest);
		}
		if ($field !== '*') {
			$data['_id']=(int)(strpos($field,'_id') !== false);
		}

		return $data;
	}
	
}