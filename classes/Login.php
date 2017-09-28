<?php

class Login {
    public static function isLoggedin() {
        if(isset($_COOKIE["SNID"])) {
            if(DB::query("SELECT User_ID FROM login_tokens WHERE Token = ?", array(md5($_COOKIE["SNID"])))) {
                return DB::query("SELECT User_ID FROM login_tokens WHERE Token = ?",array(md5($_COOKIE["SNID"])))[0]['User_ID'];
            }
            return FALSE;
        }
    }
}
