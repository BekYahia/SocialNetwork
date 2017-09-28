<?php

class DB {

    private static function con() {
        $dns        = "mysql:host=localhost;dbname=SocialNetwork;charset=utf8";
        $user       = "sn_admin";
        $password   = "5VznuC5Ht3wtESNm";
        $options    = array(
                PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES UTF8",
                PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION
        );

        return  new PDO($dns, $user, $password, $options);
    }

    public static function query($query, $params = array()) {
        $stmt = self::con()->prepare($query);
        $stmt->execute($params);

        if (explode(' ', $query)[0] == "SELECT") {
            return $stmt->fetchAll();
        }
    }

}
/*
== i'm sorry to putit here, just i wanna make sure it'll includeed in every page. hhhhh
*/
function title() {
    global $title;
    echo isset($title) ? $title : "SocialNetwork";
}
