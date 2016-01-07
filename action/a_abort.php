<?php

    class a_abort{

        //静态类(yes)
        private $static_class;

        static function error(){
            http::send(500);
        }

        static function auth(){
            http::send(401);
        }

        static function forbid(){
            http::send(403);
        }

        static function tidy(){
            http::send(500);
        }








    }