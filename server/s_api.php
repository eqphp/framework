<?php

class s_api{

	const VERSION='1.0.150702';
	const TIME_OUT=25;

	protected $api,$url,$method;
	protected $request_data,$request_header;
	protected $response,$response_status,$response_code,$curl_error,$response_header,$response_body;


    //初始化查询参数
    function __construct($api=''){
		$this->build_api($api);
		$this->request_header=array(
		 	'User-Agent'=>self::get_user_agent(),
		 	'Content-Type'=>'application/json',
			'Authorization'=>session::get('Authorization'),
		);
    }
	
	//注册请求头信息
	function header($header=array()){
		if (isset($header['Auth'])) {
			$this->request_header['Authorization']=$header['Auth'];
		}
		if (isset($header['Content-Type'])) {
			$this->request_header['Content-Type']=$header['Content-Type'];
		}

		return $this;
	}

	function get($id='',$query=null){
		$this->method='GET';
		$this->build_url($id,$query);		
		return $this;
	}

	function post($data){
		$this->method='POST';
		$this->request_data=$data;
		return $this;
	}

	function put($id='',$version='',$data=array()){
		$this->method='PUT';
		$this->build_url($id);		
		$version and $this->request_header['If-Match']=$version;
		$this->request_data=$data;
		return $this;
	}

	function delete($id='',$version='',$query=null){
		$this->method='DELETE';
		$this->build_url($id,$query);
		$version and $this->request_header['If-Match']=$version;
		return $this;
	}

	function patch($id,$version='',$data=array(),$query=null){
		$this->method='PATCH';
		$this->build_url($id,$query);
		$version and $this->request_header['If-Match']=$version;
		$this->request_data=$data;
		return $this;
	}
	
	//返回响应结果
	function call($mode='response'){
		$this->execute_curl_request();
		if ($mode === 'debug') {
			debug::out($this);
		}
		$this->parse_code();

		if ($mode === 'body') {
			$this->parse_body();
			return $this->response_body;
		}
		if ($mode === 'header') {
			$this->parse_header();
			return $this->response_header;
		}
		if ($mode === 'response') {
			$this->parse_header();
			$this->parse_body();
			return array($this->response_header,$this->response_body);
		}
	}
	
	//构建API
	protected function build_api($api){
		$api=config('api.'.$api,'api');
		$service=strstr($api,'.',true);
		
		$uri=str_replace('.','/',ltrim($api,$service));		
		$service=config('service.'.$service,'api');

		$this->url=$this->api=$service.$uri;
	}

	//构建请求URL
	protected function build_url($id='',$condition=null){
		$id and $this->url.='/'.$id;
		if (is_array($condition) && $condition) {
			$this->url.='?'.http_build_query($condition);
		} elseif (is_string($condition)) {
			$this->url.=$condition;
		}
	}

	//构建请求头信息
	protected function build_header(){
		$request_header=array();
		foreach ($this->request_header as $header=>$value) {
			$request_header[]=$header.': '.$value;
		}
		$this->request_header=$request_header;
	}

	//执行CURL请求
	protected function execute_curl_request(){
		$ch=curl_init($this->url);
	
		//Restful请求方式
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$this->method);

		//Auth认证
		// if ($this->is_auth) {
			// curl_setopt($ch,CURLOPT_USERPWD,'name:password');
		// }

		//Timeout时效性
		if (self::TIME_OUT > 0) {
			curl_setopt($ch,CURLOPT_TIMEOUT,self::TIME_OUT);
		}

		//SSL证书
		if (strpos($this->url,'https') === 0) {
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT,'/var/data/cert/ssl_cert.pem');

			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY,'/var/data/cert/ssl_key.pem');

			curl_setopt($ch,CURLOPT_SSLKEYPASSWD,'fsdf$3#ert!!*dfdsf');
		}

		//body传输数据
		if ($this->request_data) {
			$post_data=json_encode($this->request_data);
			$this->request_header['Content-Length']=strlen($post_data);
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		}

		//header头信息
		$this->build_header();
		curl_setopt($ch,CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$this->request_header);

		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_MAXREDIRS,2);

		$this->response=curl_exec($ch);
		$this->response_status=curl_getinfo($ch);
		$this->curl_error=array('error'=>curl_errno($ch),'message'=>curl_error($ch));

		curl_close($ch);
	}

	//解析异常、错误码
	protected function parse_code(){
		if ($this->curl_error['error']) {
			throw new Exception($this->curl_error['message'],110);
		}

		$this->response_code=(int)$this->response_status['http_code'];
		if ($this->response_code) {
			if ($this->response_code >= 400) {
				$message=config($this->response_code,'http_status');
				throw new Exception($this->response_code.' - '.$message,111);
			}
			if ($this->response_code === 211) {
				throw new Exception('api server custom exception',112);
			}
			return true;
		}
		throw new Exception('api server response code abesnt',113);
	}

	//解析响应的header头信息
	protected function parse_header(){
		$this->response_header=substr($this->response,0,$this->response_status['header_size']);
		$lines=preg_split('/(\r|\n)+/',$this->response_header,-1,PREG_SPLIT_NO_EMPTY);
		array_shift($lines);
		$this->response_header=array();
		foreach ($lines as $line) {
			list($name,$value)=explode(':',$line,2);
			$this->response_header[strtolower(trim($name))]=trim($value);
		}
	}

	//解析响应的body信息
	protected function parse_body(){
		$this->response_body=substr($this->response,$this->response_status['header_size']);
		$this->response_body=json_decode($this->response_body,true);
	}

	//获取curl_restful版本声明
	static function get_user_agent(){
		$curl_version=curl_version();
		$user_agent='Httpful/'.self::VERSION.' (cURL/';
		if (isset($curl_version['version'])) {
			$user_agent.=$curl_version['version'];
		}
		$user_agent.=' PHP/'. PHP_VERSION . ' (' . PHP_OS . ') ';
		if (isset($_SESSION['SERVER_SOFTWARE'])) {
			$user_agent.=$_SESSION['SERVER_SOFTWARE'];
		}
		$user_agent.=')';
		return $user_agent;
	}
}