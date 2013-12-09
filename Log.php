<?php

class Log {
    
    function __construct() 
    {
        //conexao
        $this->con = mysql_connect(CT_DB_HOST,CT_DB_USER,CT_DB_PASSWORD);
        mysql_select_db(CT_DB_DATABASE,$this->con);
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
    }
        
    public static function insert($who, $info1=null, $info2=null, $info3=null){
        $con = mysql_connect(CT_DB_HOST,CT_DB_USER,CT_DB_PASSWORD);
        mysql_select_db(CT_DB_DATABASE,$con);
    	
        //Replace characters "'" 
        $who   = str_replace("'",'&quot;',$who);
        $info1 = str_replace("'",'&quot;',$info1);
        $info2 = str_replace("'",'&quot;',$info2);
        $info3 = str_replace("'",'&quot;',$info3);
        
        $sql = sprintf("INSERT INTO ".Database::$object['Log']." (who, info1, info2, info3) 
                    VALUES ('%s','%s','%s','%s')", $who, $info1, $info2, $info3);
        $ret = mysql_query($sql);
        return $ret;
    }
    
    public static function info($who, $info1=null, $info2=null, $info3=null){
        $con = mysql_connect(CT_DB_HOST,CT_DB_USER,CT_DB_PASSWORD);
        mysql_select_db(CT_DB_DATABASE,$con);
        
        //Replace characters "'" 
        $who   = str_replace("'",'&quot;',$who);
        $info1 = str_replace("'",'&quot;',$info1);
        $info2 = str_replace("'",'&quot;',$info2);
        $info3 = str_replace("'",'&quot;',$info3);
        
        $sql = sprintf("INSERT INTO ".Database::$object['Log']." (who, info1, info2, info3) 
                    VALUES ('%s','%s','%s','%s')", $who, $info1, $info2, $info3);
        $ret = mysql_query($sql);
        return $ret;
    }
    
    public static function error($who, $info1, $info2=null, $info3=null, $level='L'){
        $con = mysql_connect(CT_DB_HOST,CT_DB_USER,CT_DB_PASSWORD);
        mysql_select_db(CT_DB_DATABASE,$con);
        
        //Replace characters "'" 
        $who   = str_replace("'",'&quot;',$who);
        $info1 = str_replace("'",'&quot;',$info1);
        $info2 = str_replace("'",'&quot;',$info2);
        $info3 = str_replace("'",'&quot;',$info3);
        
        if(!in_array($level, array('L','M','H','C'))) {
        	$level = 'L';
        }
        
        $sql = sprintf("INSERT INTO ".Database::$object['Log']." (who, info1, info2, info3) 
                    VALUES ('%s','%s','%s','%s')", $who, $info1, $info2, $info3);
        
        $ret = mysql_query($sql);
        return $ret;
    }
        
}

?>