<?php

class Security {
	
    public static function loadPermittedPages(){ 

        require_once(CT_FL_ROOT.'controller/UserCtr.php');
    
        $controller = new UserCtr(); 
        $_SESSION['permitted_pages'] = $controller->listPermittedPages(isset($_SESSION['username'])?$_SESSION['username']:null); 
    }
        
    /*
     * @author Thalys Santana
     * @param $page: Page that need authority 
              $redirectTo: Page to redirect the user if he hasn't the authority
     */
    public static function verifyPagePermission($requestedPage, $redirectTo='index.php'){

        if(!isset($_SESSION['permitted_pages'])){
            Security::loadPermittedPages();
        }
        
        if(!in_array($requestedPage, $_SESSION['permitted_pages'])){
            
            Util::setMainMessage(array('Desculpe, mas você não pode acessar essa página'));

            if(isset($_SESSION['currentPage'])){
                if(stristr($_SESSION['currentPage'],$requestedPage)){
                    header('Location: '.str_ireplace($_SESSION['currentPage'],$requestedPage,$redirectTo));
                }else{
                    header('Location: '.$_SESSION['currentPage']);
                }
            }else{
                header('Location: '.CT_FL_EXT_PUBLIC.$redirectTo);
            }
            exit();
        }
    }
}

?>