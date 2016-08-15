<?php

class PageError
{
    public function __construct(){}

    /*
     * генерация 404 ошибки
     * */
    static public function error404(){
        echo "ошибка 404 вы указали не существующую страницу";
    }

    static  public function accessError()
    {
        echo "временно не доступен";
    }
}