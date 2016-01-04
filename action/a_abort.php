<?php

    class a_abort{

        //静态类(yes)
        private $static_class;

        static function error(){
            smarty()->display('abort/error');
        }

        static function auth(){
            smarty()->display('abort/401');
        }

        static function forbid(){
            smarty()->display('abort/403');
        }

        static function tidy(){
            smarty()->display('abort/500');
        }








    }