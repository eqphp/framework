<?php

class file{

    //读取文件内容
    static function read($file_name, $line = 0){
        $handle = fopen($file_name, 'r');
        $buffer = array();
        if ($handle) {
            while (!feof($handle)) {
                $buffer[] = fgets($handle);
            }
            fclose($handle);
        }

        if ($line === 0) {
            $key = 0;
        } else {
            if ($line <= count($buffer)) {
                $key = $line - 1;
            } else {
                $key = count($buffer)-2;
            }
        }
        return $key ? $buffer[$key] : $buffer;
    }

    //创建、写文件
    static function write($file_name, $string = '', $mode = 'w'){
        //file_put_contents($file_name, $string, FILE_APPEND);
        $open_file = fopen($file_name, $mode);
        flock($open_file, LOCK_EX);
        fwrite($open_file, $string);
        flock($open_file, LOCK_UN);
        fclose($open_file);
        return true;
    }

    //遍历目录（文件列表）directory;file,extension,base
    static function scan($directory, $name, &$data, $type = 'extension'){
        if (!is_dir($directory)) {
            return false;
        }
        $option = array(
            'base' => PATHINFO_BASENAME,
            'file' => PATHINFO_FILENAME,
            'extension' => PATHINFO_EXTENSION,
            );
        foreach (new DirectoryIterator($directory) as $file) {
            if ($file->isDot() || $file->isLink()) {
                continue;
            }
            if ($file->isFile()) {
                if (pathinfo($file->getFilename(), $option[$type]) === $name) {
                    $data[] = realpath($file->getPathname());
                }
            } else {
                self::scan($file->getPathname(), $name, $data, $type);
            }
        }
    }

    //类文件查找(mode:false->查询所有，true->由外向里(先父级目录，后子级目录)取第一个)
    static function search($directory, $class, &$file_name, $mode = false){
        if (!is_dir($directory)) {
            return false;
        }
        foreach (new DirectoryIterator($directory) as $file) {
            if ($file->isDot() || $file->isLink()) {
                continue;
            }
            if ($file->isFile()) {
                if (pathinfo($file->getFilename(), PATHINFO_FILENAME) === $class) {
                    $file_name[] = $file->getPathname();
                }
            } else {
                self::search($file->getPathname(), $class, $file_name);
            }
            if ($mode && $file_name) {
                return $file_name[0];
            }
        }
    }

    //修改文件、目录的权限/属主/所属分组
    //chmod-0775,0751,0421/chown-root,ftp,apache/chgrp-root,ftp,other
    static function modify($file_name, $mode, $value, $file_value = null){
        if (is_file($file_name)) {
            $value = is_null($file_value) ? $value : $file_value;
            return call_user_func($mode, $file_name, $value);
        }
        foreach (new DirectoryIterator($file_name) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($file->isDir()) {
                self::modify($file->getPathname(), $mode, $value);
            }
            call_user_func($mode, $file->getPathname(), $value);
        }
        return true;
    }

    //删除目录、文件
    static function delete($dir, $is_delete_self = true){
        $dir = trim($dir, '/');
        if (is_file($dir)) {
            return unlink($dir);
        }
        if (is_dir($dir)) {
            $source = opendir($dir);
            while ($file = readdir($source)) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $current_dir = $dir . '/' . $file;
                is_dir($current_dir) ? self::delete($current_dir) : unlink($current_dir);
            }
            closedir($source);
            $is_delete_self and rmdir($dir);
        }
        return true;
    }

    //创建目录
    static function folder($path, $mode = 0777){
        if (file_exists($path)) {
            return false;
        }
        self::folder(dirname($path));
        return mkdir($path, $mode);
    }

}