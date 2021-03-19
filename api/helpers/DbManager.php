<?php
class DbManager{
    private static function make_connection(){
        global $host, $user, $passwd, $db;
        $mysqli = mysqli_connect($host, $user, $passwd, $db);
        return $mysqli;
    }
    
    static function make_querry($query){
        $mysqli =  self::make_connection();
    
        $res = $mysqli->query($query);
        if(!$res) return false;
        $arr = $res->fetch_all(MYSQLI_ASSOC);
        
        // close connection
        $mysqli->close();
    
        return $arr;
    }
    
    static function make_safe_querry($query, $types = null, $params = null) {
        $mysqli = self::make_connection();
    
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param($types, ...$params);
    
        // if execute not succeed
        if(!$stmt->execute()) return false;

        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // close statement
        $stmt->close();
    
        // close connection
        $mysqli->close();

        return $result;
    }
    
    static function make_no_result_querry($query){
        $mysqli =  self::make_connection();
    
        $res = $mysqli->query($query);
        
        // close connection
        $mysqli->close();
    
        return $res;
    }
    
    static function make_insert_id_querry($query){
        $mysqli = self::make_connection();
        $mysqli->query($query);
        
        // get insert id
        $result = mysqli_insert_id($mysqli);
    
        // close connection
        $mysqli->close();
    
        return $result;
    }
    
    static function make_safe_insert_id_querry(
            $query, $types = null, $params = null){
        $mysqli = self::make_connection();
    
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param($types, ...$params);
    
        // if execute not succeed
        if(!$stmt->execute()) return false;
    
        // get insert id
        $result =  mysqli_insert_id($mysqli);
    
        // close statement
        $stmt->close();
    
        // close connection
        $mysqli->close();
    
        return $result;
    }
}
?>