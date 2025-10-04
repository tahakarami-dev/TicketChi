<?php

defined('ABSPATH') || exit('NO Access');

class TKM_SMS{
    public $username;
    public $password;
    
    public $phone;
    public $message;
    public $code;
    public $api;
    

    public function __construct($phone,$message,$code)
    {
        $this->username = tkm_settings('sms-username');
        $this->password = tkm_settings('sms-password');
        

        $this->phone =$phone;
        $this->message=$message;
        $this->code=$code;
        $this->api = tkm_settings('sms-api');


        
    }
}