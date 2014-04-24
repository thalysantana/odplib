<?php

class Util {

	/*
	 *  $data['header'] = An array  
	 *  $data['cWidth'] = The width percentual of each column. The total of widths should be 100. Ex: ['20%','60%','20%']
	 *  $data[0,1,2,etc...] = Lines of table
	 */
	public static function printTable($data, $width='100%'){
		
		if (!array_key_exists('header',$data)){
			return array(0, 'Line "header" is missing.');
		}elseif (array_key_exists('cWidth',$data)){
			$x = 0;
			foreach ($data['cWidth'] as $w){
				$x = $x + $w;
			}
			
			if($x != 100){
				return array(0, "The total width is not 100%. It's $x%.");
			}
		}
		
		//Generate Header
		$columns = '';
		foreach($data['header'] as $key => $field){
			$columns = $columns.'<td width='.$data['cWidth'][$key].'>'.$field.'</td>';
		}
        $header = '<tr class="clAdmTabHeader">'.$columns.'</tr>';
		
        //Generate lines
        $lines = '';
		foreach ($data as $key => $line){
			
			if(is_numeric($key)){
				
				$columns = '';
				foreach ($line as $field){
					$columns = $columns.'<td>'.$field.'</td>';
				}
				$lines = $lines.'<tr class="clAdmLine'. $key % 2 .'">'.$columns.'</tr>';
			}
		}
		
		return "<table width='$width'>".$header.$lines."</table>";
	}
	
	/*
	 * @author Thalys Santana
	 * @param $currentPage: Page that need authority 
              $necessaryAuthorities: Array with the necessary authorities
              $redirectTo: Page to redirect the user if he hasn't the authority
	 */
    public static function verifyAuthority($currentPage, $necessaryAuthorities=array(), $redirectTo='index.php'){
        
    	if(!empty($necessaryAuthorities)){ //If is necessary authorization
	    	$errors = array();
    		
	    	//If username isn't setted on session, user has to log in
    		if(!isset($_SESSION['username'])){
    			array_push($errors,CT_MSG_E_REQUIRES_LOGIN);
    		}else{
    			//If $hasAuth is empty user hasn't the necessary authority
    			$hasAuth = array_intersect($_SESSION['authorities'],$necessaryAuthorities);
    			
    			if(empty($hasAuth)){
    				array_push($errors,CT_MSG_E_HAS_NOT_AUTHORITY);
    			}
    		}
    		
    		//If has errors
    		if (!empty($errors)){
    			
    			//Set error messages
    			$message = '';
    			foreach ($errors as $error){
    				$message = $message.'<div class="panelMsgText-error">'.CT_IC_ERROR.$error.'</div>';
    			}
    			$_SESSION['mainMessage'] = $message;
    			
    			//Redirect user
    			/* If the currentPage setted on session is the current page (that is requiring authority validate)
    			 * redirect to the page setted at $redirectTo
    			 */ 
    		    if(stristr($_SESSION['currentPage'],$currentPage)){
                    header('Location: '.str_ireplace($_SESSION['currentPage'],$currentPage,$redirectTo));
                }else{
                    header('Location: '.$_SESSION['currentPage']);
                }
                exit();
    		}
    		//User has necessary authority
    		
    	}
    	//Page doesn't need authority
    }
    
    public static function setMainMessage($error=array(), $success=array()){
        require_once('../config/constants.php');
    	
        $array = array();
    	
        if(is_array($error)){
        	foreach ($error as $msg){
                array_push($array,'<div class="panelMsgText-error">'.CT_IC_ERROR.$msg.'</div>');
            }
        }
        
        if(is_array($success)){
            foreach ($success as $msg){
                array_push($array,'<div class="panelMsgText-success">&nbsp;'.$msg.'</div>');
            }
        }
        
        //Mount the html
        $html = '';
        foreach ($array as $msg){
        	$html = $html.$msg;
        }
        
        $_SESSION['mainMessage'] = $html;
    }

    public static function formatDateBRLToSql($date){
        if(strlen($date) == 10){
        	//Expect '31/12/2010' to return '2010-12-31'
            return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2);
        }else{
            //Expect '31/12/2010 12:59:59' to return '2010-12-31 12:59:59'
            return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2).substr($date,10,9);
        }
    }

    public static function formatDateSqlToBRL($date){
        if(strlen($date) == 10){
            //Expect '2010-12-31' to return '31/12/2010'
            return substr($date,8,2).'/'.substr($date,5,2).'/'.substr($date,0,4);
        }else{
            //Expect '2010-12-31 12:59:59' to return '31/12/2010 12:59:59'
            return substr($date,8,2).'/'.substr($date,5,2).'/'.substr($date,0,4).substr($date,10,9);
        }

    }

    public static function formatValueSqlToBRL($float){
    	return 'R$ '.number_format($float,2,',','.');
    }
    
    public static function formatPhoneNumber($phone){
    
	    $phone = preg_replace("/[^0-9]/", "", $phone);
	 
	    if(strlen($phone) == 7)
	        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
	    elseif(strlen($phone) == 10)
	        return preg_replace("/([0-9]{2})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $phone);
	    else
	        return $phone;
    }
    
    public static function verifyDateDiff($startDate, $endDate, $diffAllowed){
    	
        $con = mysql_connect(CT_DB_HOST,CT_DB_USER,CT_DB_PASSWORD);
        mysql_select_db(CT_DB_DATABASE,$con);
   
        $sql = sprintf("SELECT TIMESTAMPDIFF(SECOND,CONVERT('%s',DATETIME),CONVERT('%s',DATETIME))<=%s is_valid
                        FROM DUAL", $startDate, $endDate, $diffAllowed+CT_TIME_TO_CHOOSE_DEVIATION);
        $ret = mysql_query($sql);

        if(mysql_num_rows($ret)){
            while ($row = mysql_fetch_array($ret)){
                return $row['is_valid'];
            }
        }
    }

    public static function removeAccent($str) 
    { 
      $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
      $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'); 
      return str_replace($a, $b, $str); 
    }

    public static function getFieldFromObjectList($array, $property, $keyOrValue = 'V'){
        $aux = array();
        foreach($array as $obj){
            if($keyOrValue == 'K'){
                eval('$aux[$obj->get'.ucwords($property).'()]=null;');
            }else{
               eval('array_push($aux,$obj->get'.ucwords($property).'());');
            }
        }
        return $aux;
    }
}

?>
