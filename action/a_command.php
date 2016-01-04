<?php

class a_command{


    function index(){
	// D:\soft\mongodb\bin\mongo.exe
	// D:\soft\mongodb\bin\mongod.exe --dbpath=D:\soft\mongodb\db --service

    }

	
	//清理目录、创建目录、修改权限
	function publish(){
		
		self::clear_directory();
		self::create_directory();
		self::modify_privilege();
		
		echo 'publish successful';
	
	}


	//模块创建、删除
    function group(){
		$action=url(2,'/^(create|delete)$/');
		$name=strtolower(url(3,'/^[a-z][a-z0-9_]{2,9}$/'));
		if ($action && $name) {			
			if ($action === 'create') {
				//检测模块是否已存在
				if (file_exists(PATH_ROOT.$name)) {
					throw new Exception('module already exist',188);
				}
				foreach (array('action','model','config','plugin') as $category) {
					file::folder(PATH_ROOT.$name.'/'.$category,0777);
				}

				//注册 更改config配置group-list				

			} else {
				file::delete(PATH_ROOT.$name);
			}
		}
		throw new Exception('param error',189);
    }
	
	
	//清理目录
	private function clear_directory(){

		//上传临时目录
		file::delete(FILE_TEMP,true);

		//编译、缓存数据、session
		$cache_folder_list=array('smarty/compile','compile','session');
		foreach ($cache_folder_list as $cache_folder) {
			file::delete(PATH_CACHE.$cache_folder,false);
		}

		//日志topic->curl,error,exception,mail,memcache,model,mysql,redis,server
		$log_folder_list=array('mail','mongo','run','sql','run','test','topic','trace','visit');
		foreach ($log_folder_list as $log_folder) {
			file::delete(PATH_LOG.$log_folder,false);
		}

	}
	
	//创建目录
	private function create_directory(){

		file::folder(FILE_TEMP,0777);

		$cache_list=array('smarty/compile/','compile/','db/ini/','db/js/','db/php/','db/txt/','db/xml/','session/');
		foreach ($cache_list as $path_cache) {
			file::folder(PATH_CACHE.$path_cache,0777);
		}

		$log_list=array('trace/','run/','topic/','sql/','mongo/','visit/','test/');
		foreach ($log_list as $path_log) {
			file::folder(PATH_LOG.$path_log,0777);
		}

	}
	
	//变更运行权限
	private function modify_privilege(){

		file::modify(PATH_ROOT,'chown','apache:apache');
		file::modify(PATH_ROOT,'chmod',0755,0644);

		file::modify(PATH_CACHE,'chmod',0777);
		file::modify(PATH_LOG,'chmod',0777);
		file::modify(PATH_DATA,'chmod',0777);

		file::modify(PATH_FILE,'chmod',0777);
		file::modify(FILE_STATIC,'chmod',0755,0644);

	}


}