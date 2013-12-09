<?php

class Validator {
	
    public static function validateAuthority($necessary=array(), $userAuthorities){ 
    	
    	$errors = array();
    	$hasError = false;
        
    	if(!empty($necessary)){ //If is necessary authorization
    		
    	    //User should be logged and authority should be setted
    		if(isset($userAuthorities) || !empty($userAuthorities)){
    			$hasAuth = array_intersect($userAuthorities,$necessary);
    			
    			$hasError = empty($hasAuth);
    		}else{
    		    $hasError = true;
    		}
    	}
    	
    	if($hasError){
    	    $errors['default'] = array($this->getErrorMessage(null, 'HASNT_AUTHORITY'));
    	}
    	
    	return $errors;
    }
    
	/* NOTE
	 * 
	 * For each PARAMETER expects:
	 * parameterName
	 * parameterValue
	 * 
	 * For each RULE expects:
	 * parameterName (varchar)
	 * parameterValidations (array)
	 * optional (boolean)
	 * 
	 * For each PARAMETER VALIDATION expects:
	 * validatorName
	 * validatorValue
	 * 
	 * Example:
	 * $parameters = array('i'=> 30, 'name' => 'joao')
	 * $rules      = array('i'=> array('nullable' => false, 'datatype' => 'string', 'maxlength' => 70))
	 * 
	 * Validators can be:
	 * nullable      : If field can be null (true/false)
	 * minlength     : The lowest number os characters the data should have (integer equals or bigger than zero)
	 * maxlength     : The largest number of characters the data should have (integer equals or bigger than zero)
	 * length        : The exact number of characters the data should have(integer equals or bigger than zero)
	 * datatype      : The type that the data should be (numeric, integer, float, string, bool, array)
	 * regexp        : A Regular Expression the data should respect
	 * biggerThan    : The bigger number data can be (numeric, float, integer)
	 * smallerThan   : The smaller number data can be (numeric, float, integer)
	 * inDB          : If id is in database ($rule='ObjectName',$value = id))
	 * atList        : If data is on specific list 
	 * */
    public static function validateParameters($parameters, $rules){ 
  	
    	$errors = array();
        
    	foreach ($rules as $key => $rule){
    		
    		//Verify if is nullable (if it's not setted, set as true)
    		$nullable = isset($rule['nullable'])?$rule['nullable']:true;
    		
    		if(isset($parameters[$key])){
                
    			$parameterErrors = array();
    			
    			foreach ($rule as $validatorKey => $validatorData){
    				
                    try{
	                    /* Call validate function 
	                     * Ex: 
	                     *  $validatorKey 'length' will call function validateLength()
	                     * */
 
                    	call_user_func_array('Validator::validate'.ucfirst($validatorKey),array($parameters[$key],$validatorData));
                    }catch(Exception $e){
    					array_push($parameterErrors, Validator::getErrorMessage($key, $e->getMessage()));
    				}
    			}
    			if(!empty($parameterErrors)){
    			   // $errors[$key] = $parameterErrors;
    			    array_push($errors,$parameterErrors);
    			}    
    		}else{
    			if(!$nullable){
    			    $errors[$key] = array(Validator::getErrorMessage($key, 'FIELD_REQUIRED'));
    			}
    		}
    		
    	}
    	
    	return $errors;
    }
    
    private static function getErrorMessage($field, $error){

        $messages = array(
                    'HASNT_AUTHORITY'    => sprintf('Seu usuário não possui previlégio para essa operação'),
                    'FIELD_REQUIRED'     => sprintf('O campo %s deve ser informado',ucfirst($field)),
                    'MAXLENGTH_EXCEEDED' => sprintf('O campo %s excedeu o tamanho máximo permitido',ucfirst($field)),
                    'MINLENGTH_EXCEEDED' => sprintf('O campo %s excedeu o tamanho mínimo permitido',ucfirst($field)),
                    'INVALID_TYPE'       => sprintf('O campo %s contem um valor inválido',ucfirst($field)),
                    'INVALID_DATATYPE'   => sprintf('O campo %s contem um tipo de dado inválido',ucfirst($field)),
                    'INVALID_PATTERN'    => sprintf('O campo %s contem um formato de dado incorreto',ucfirst($field)),
                    'TOO_BIG_DATETIME'   => sprintf('%s maior que o permitido',ucfirst($field)),
                    'TOO_BIG_NUMBER'     => sprintf('O campo %s contem um numero maior que o valor permitido',ucfirst($field)),
                    'TOO_LOW_NUMBER'     => sprintf('O campo %s contem um numero menor que o valor permitido',ucfirst($field)),
                    'TOO_LOW_DATETIME'   => sprintf('%s menor que o permitido',ucfirst($field)),
                    'IS_NOT_IN_DB'       => sprintf('%s informado não existe',ucfirst($field)),
                    'IS_NOT_AT_LIST'     => sprintf('O valor do campo %s não é válido',ucfirst($field)),
                    'DATA_NOT_VALIDATED' => sprintf('%s: dado não pode ser validado',ucfirst($field))
        );

        return $messages[$error];
    }
    
    private static function validateNullable($value, $rule){
        
    	if(!$rule){ //If null is not able
    		if((is_null($value) || $value == "")){
    			throw new Exception('FIELD_REQUIRED');
    		}
    	}
    	
    	return 1;
    }
    
    private static function validateMinlength($value, $rule){
        
    	if(is_string($value)){
    		if(strlen($value) < $rule){
    			throw new Exception('MINLENGTH_EXCEEDED');
    		}
    	}elseif(is_array($value)){
    	    if(sizeof($value) < $rule){
                throw new Exception('MINLENGTH_EXCEEDED');
            }
    	}else{
    		throw new Exception('INVALID_TYPE');
    	}
        
        return 1;
    }
    
    private static function validateMaxlength($value, $rule){
    	
        if(is_string($value)){
            if(strlen($value) > $rule){
                throw new Exception('MAXLENGTH_EXCEEDED');
            }
        }elseif(is_array($value)){
            if(sizeof($value) > $rule){
                throw new Exception('MAXLENGTH_EXCEEDED');
            }
        }else{
            throw new Exception('INVALID_TYPE');
        }
        
        return 1;
    }
    
    private static function validateLength($value, $rule){
    	
        if(is_string($value)){
            if(strlen($value) != $rule){
                throw new Exception('INVALID_LENGTH');
            }
        }elseif(is_array($value)){
            if(sizeof($value) != $rule){
                throw new Exception('INVALID_LENGTH');
            }
        }else{
            throw new Exception('INVALID_TYPE');
        }
        
        return 1;
    }
    
    private static function validateDatatype($value, $rule){
        $isOk = 1;

        if(is_null($value) || strlen($value)==0){
        	return 1;
        }
        
        //Rule should be 'numeric', 'integer', 'float', 'string', 'bool' or 'array'
        if($rule == 'date'){
            $isOk = (preg_match(CT_PT_DATE_BR,$value) && checkdate(substr($value,3,2),substr($value,0,2),substr($value,6,4)));
        }elseif($rule == 'datetime'){
    	    $isOk = (preg_match(CT_PT_DATETIME_BR,$value) && checkdate(substr($value,3,2),substr($value,0,2),substr($value,6,4)));
    	}elseif($rule == 'integer'){
            $isOk = is_numeric($value)?is_integer((int)$value):false;
        }elseif($rule == 'bool'){
            $isOk = in_array($value,array(true,false,'true','false','0','1',0,1));
    	}else{
        	$isOk = call_user_func('is_'.$rule, $value);
    	}
    	
    	if(!$isOk){
    	    throw new Exception('INVALID_DATATYPE');
    	}
    	
        return 1;
    }
   
    private static function validateRegexp($value, $rule){

        if(!preg_match($rule,$value)){
            throw new Exception('INVALID_PATTERN');
        }
        
        return 1;
    }
    
    private static function validateBiggerThan($value, $rule){
        
        if(is_numeric($value)){
            if($value < $rule){
                throw new Exception('TOO_LOW_NUMBER');
            }
        }elseif(preg_match(CT_PT_DATETIME_BR,$value) && checkdate(substr($value,3,2),substr($value,0,2),substr($value,6,4))){
            //Is a datetime in format DD/MM/YYYY H:MI:SS

            if(new DateTime(Util::formatDateBRLToSql($value)) <= new DateTime($rule)){
                throw new Exception('TOO_LOW_DATETIME');
            }
            
        }else{
            throw new Exception('DATA_NOT_VALIDATED');
        }
        
        return 1;
    }
    
    private static function validateSmallerThan($value, $rule){
        
        if(is_numeric($value)){
            if($value > $rule){
                throw new Exception('TOO_BIG_NUMBER');
            }
        }elseif(preg_match(CT_PT_DATETIME_BR,$value) && checkdate(substr($value,3,2),substr($value,0,2),substr($value,6,4))){
            //Is a datetime in format DD/MM/YY H:MI:SS
            
            if(new DateTime(Util::formatDateBRLToSql($value)) >= new DateTime($rule)){
                throw new Exception('TOO_BIG_DATETIME');
            }
        }else{
            throw new Exception('DATA_NOT_VALIDATED');
        }
        
        return 1;
    }
    
    private static function validateInDB($value, $rule){
        
        require_once(CT_FL_ROOT.'dao/GenericStaticDAO.php');
        $obj = null;
         eval('$obj= new '.$rule.'('.$value.');');
        
        if(!GenericStaticDAO::isInDB($obj)){
            throw new Exception('IS_NOT_IN_DB');
        }
        
        return 1;
    }
    
    private static function validateAtList($value, $rule){
        
        if(!in_array($value, $rule)){
            throw new Exception('IS_NOT_AT_LIST');
        }
        
        return 1;
    }
}

?>