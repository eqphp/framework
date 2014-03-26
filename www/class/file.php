<?php
class file{

    //创建、写文件
    static function save($file,$info='',$mode='w'){
        $open_file=fopen($file,$mode);
        flock($open_file,LOCK_EX);
        fwrite($open_file,$info);
        flock($open_file,LOCK_UN);
        fclose($open_file);
        return true;
    }

    //遍历目录（文件列表）
    static function scan($dir,$ext_name=true,&$data=array()){
        if (!is_dir($dir)) return false;
        $dir_tree=array();

        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDot()) continue;

            if ($file->isDir()) {
                array_push($dir_tree,self::scan($file->getPathname(),$ext_name,$data));
            } else {

                if ($ext_name === true) {
                    $data[]=realpath($file->getPathname());
                }

                //php_version>=5.3.6,$file->getExtension()
                if (pathinfo($file->getFilename(),PATHINFO_EXTENSION) === $ext_name) {
                    $data[]=realpath($file->getPathname());
                }

                $parent_dir=substr($file->getPath(),strrpos(realpath($file->getPath()),'\\')+1);
                array_push($dir_tree,'<strong>'.$parent_dir.' </strong>'.$file->getFilename());
            }

        }

        return $dir_tree;
    }

    //修改文件、目录的权限/属主/所属分组
    // chmod-0775,0751,0421/chown-root,ftp,apache/chgrp-root,ftp,other
    static function mod($file_name,$mode,$value){
        if (is_file($file_name)) {
            return call_user_func($mode,$file_name,$value);
        }

        foreach (new DirectoryIterator($file_name) as $file) {
            if ($file->isDot()) continue;
            if ($file->isDir()) {
                self::mod($file->getPathname(),$mode,$value);
            }
            call_user_func($mode,$file->getPathname(),$value);
        }

        return true;
    }

    //删除目录、文件
    static function del($dir,$is_del_self=true){
        $dir=trim($dir,'/');

        if (is_file($dir)) return unlink($dir);

        if (is_dir($dir)) {
            $dir_res=opendir($dir);
            while ($now_file=readdir($dir_res)) {
                if ($now_file == '.' || $now_file == '..') continue;
                $tem_dir=$dir.'/'.$now_file;
                is_dir($tem_dir) ? self::del($tem_dir) : unlink($tem_dir);
            }

            closedir($dir_res);
            $is_del_self && rmdir($dir);
        }

        return true;
    }

    //创建目录
    static function folder($path,$mode=0777){
        if (file_exists($path)) return false;
        self::folder(dirname($path));
        return mkdir($path,$mode);
    }


    //file类方法帮助提示
    static function tip(){
        $info='<br><font color="green">';
        $info.='1、扫描目录：scan($dir,$ext_name=true,&$data)<br>';
        $info.='2、写存文件：save($file,$info="",$mode="w")<br>';
        $info.='3、修改目录/文件：mod($file_name,$mode,$value)<br>';
        $info.='4、删除目录/文件：del($dir,$is_del_self=true)<br>';
        $info.='5、创建目录：folder($path,$mode=0775)</font><br>';
        return $info;
    }


}