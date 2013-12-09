<?php

class Odpcp {

    public static $genericControllerClass = "GenericCtr";
    public static $controllerFilepath = "../controller";
    public static $modelFilepath = "../model";
    
   
    public static function tabPanelEdit($array,$panelid) {
        
        $tabLabel = array();
        
        $html = "";
        $htmltabs = "";
        $masks = array();// like "array(array('fieldid'=>'','mask'=>''));"
        
        foreach($array as $keyT => $tab){
            
            $keyTab = isset($tab['id'])?$tab['id']:$keyT;
            $fieldLabel =array();
            $fieldsnullable = array();
            $aFldValidation = array();
            $htmlRows = '';
            $htmlDef = '';
            
            $rows = isset($tab['rows'])?$tab['rows']:array();
            $rows['default'] = array();
            
            //Set  tab label
            $tabLabel[$keyT] = array('label'=>$tab['label'],'id'=>$keyTab);

            //###########################################################
            //  COMEÇA CARREGAR DADOS
            //#############################################################
            for($i=0; $i<= sizeof($tab['data']); $i++){
                
                $fields=array();
                
                //for each field of tab
                for($keyField=0; $keyField< sizeof($tab['fields']); $keyField++){
                    $field = $tab['fields'][$keyField];
//                foreach($tab['fields'] as $keyField => $field){
                    
                    $value= (!$i)?null:$tab['data'][$i-1][$field['name']];
                    $disabled = !$i?0:1;
                    
                    $id    = isset($field['id'])?$tab['id'].'_'.$field['id']:$tab['id'].'_'.$keyField;
                    //$name  = 'a_'.$tab['id'].'_'.$field['name'];
                    $name  = $field['name'];
                    $label = isset($field['label'])?$field['label']:"";
                    $type  = isset($field['type'])?$field['type']:"text";
                    $width = isset($field['width'])?"style='width: ".$field['width'].";'":"";
                    $maxlength = isset($field['maxlength'])?"maxlength='".$field['maxlength']."'":"";
                    $onchange = isset($field['onchange'])?"onchange=".$field['onchange']."":"";
                    $hdisabled = $disabled?"disabled='disabled'":"";
                    //Required fields should has the odpRequired class
                    $class = (isset($field['mask'])?"odpInput ".$name:"odpInput").(isset($field['required'])&& in_array($field['required'],array('1',1,true,'true')) ? ' odpRequired':'');
                    $htmlfield = "";
                    
                    //Set field masks into array
                    if(isset($field['mask'])){
                        array_push($masks, array('fieldid'=>$name, 'mask'=>$field['mask']));
                    }
                    
                    //Verify field's type
                    if($type == "text"){
                        //Set the Header field
                        $htmlfield = "<input id='$id' name='$name' class='$class' $maxlength $onchange style='width: 100%;' type='text' value='$value' $hdisabled>";
                        
                    }elseif($type == "hidden"){
                        $htmlfield = "<input id='$id' name='$name' class='$class' $maxlength $onchange style='width: 100%;' type='$type' value='$value' $hdisabled>";
                        
                    }elseif($type == "checkbox"){
                        $htmlfield = "<input id='$id' name='$name' class='$class' $onchange style='width: 100%;' type='$type' ".($value?"checked='checked'":"")." $hdisabled>";
                        
                    }elseif($type == "select"){
                        $loadType = isset($field['options']['load'])?'L':'F';
                        
                        if($i>0){
                            if(isset($field['options']['dynamicparameter'])){
                                foreach($field['options']['dynamicparameter'] as $keyDefPar => $columnToLoad){
                                    $field['options']['by'][$keyDefPar] = $tab['data'][($i-1)][$columnToLoad];
                                }
                            }
                        }else{
                            unset($field['options']['by']);
                            unset($field['options']['dynamicparameter']);
                        }
                        
                        $properties = array('name'=>$name, 'id'=>$id, 'class'=>$class, 'readonly'=>$disabled);
                        if(isset($field['onchange']))
                            $properties['onchange']= $field['onchange'];
                        
                        $htmlfield = Odpcp::select($loadType,$field['options'],$value,null,$properties);
                    }//End of field type verification
                    
                    if(!$i){
                        array_push($fieldLabel, "<td $width><span>$label</span></td>");
                    }
                    //Set id into array, together with the first field
/*                    if(!$keyField){
                        $htmlfield = "<input type='hidden' name='id' value='".$tab['data'][$i-1]['id']."'>".$htmlfield;
                    }*/
                    
                    //Set html field into array
                    array_push($fields,$htmlfield);
                
                }//End of field

                $htmlfield =  $i?"<td class='$panelid-$keyTab-fld'><input type='hidden' name='id' value='".$tab['data'][$i-1]['id']."'></td>":"<td class='$panelid-$keyTab-fld'><input type='hidden' name='id' value=''></td>";
                foreach($fields as $column){
                    $htmlfield = $htmlfield."<td class='$panelid-$keyTab-fld'>".$column."</td>";
                }
                
                $htmlIco = "<td style='width: 20px' class='odpImgSave'><input type='button' title='Salvar' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."save.gif\");' onclick='".$panelid.$keyTab."SaveLine($(this).closest(\"tr\"));'></td>
                            <td style='width: 20px' class='odpImgEdit'><input type='button' title='Editar' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."edit.gif\");' onclick='".$panelid.$keyTab."EditLine($(this).closest(\"tr\"));'></td>
                            <td style='width: 20px' class='odpImgDelete'><input type='button' title='Excluir' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."delete.gif\");' onclick='".$panelid.$keyTab."RemoveLine($(this).closest(\"tr\"));'></td>";
                
                if($i){ //Line with data
                    $htmlRows = $htmlRows."<tr class='$panelid-$keyTab-row odpSaved'>".$htmlfield.$htmlIco."</tr>";
                }else{ //Default empty line
                    $htmlDef = $htmlfield.$htmlIco;
                }
            }
            
            //#########################################################
            // TERMINA DE CARREGAR DADOS
            //########################################################
            
            //Convert arrays values to html
            $htmllabel = "<td></td>"; //Empty column to compensate id's hidden column
            foreach($fieldLabel as $column){
                $htmllabel = $htmllabel.$column;
            }
            
/*            $htmlvalidation = "";
            foreach($aFldValidation as $column){
                $htmlvalidation = $htmlvalidation.'if('.$column[0].'){alert("'.$column[1].'");return;}';
            }*/
            
            $htmlmask = "";
            foreach($masks as $column){
                $htmlmask = $htmlmask.'$(".'.$column['fieldid'].'").mask("'.$column['mask'].'");';
            }
            $htmlmask = sizeof($masks)?'jQuery(function($){'.$htmlmask.' });':"";
            
            //panel
            $htmltabs = $htmltabs. 
            "<div id='$panelid-$keyTab' name='$panelid-$keyTab' class='odpMIPanel' style='display: ".(!$keyT?"block":"none").";'>
                <input type='hidden' name='odpMIPid' value='$keyTab'>
                <div>
                    <div class='odpITPhp'>
                    <input type='hidden' id='$panelid-$keyTab-nextLine' value='0'>
                    <div><a href='javascript:void(0);' class='odpTabSel' onclick='".$panelid.$keyTab."NewLine();'>Novo</a></div>
                    
                        <table style='display: none;'>
                            <tr id='$panelid-$keyTab-defaultLine'>
                                $htmlDef
                            </tr>
                        </table>
                        
                        <table cellpadding='0' cellspacing='0px' border='0' id='$panelid-$keyTab-svLine'>
                            <tr style='font-weight: bold;'>
                                $htmllabel
                                $htmlRows
                            </tr>
                        </table>

                        <script type='text/javascript'>
                            //".$panelid. $keyTab ."NewLine();
                            
                            $('.odpSaved').each(function(i){
                                ".$panelid.$keyTab."ChangeLineStatus($(this), 'S');
                            });
                            
                            function ".$panelid.$keyTab."NewLine(){
                                newLine = $(\"#$panelid-$keyTab-defaultLine\").clone();
                                newLine.removeAttr('id');
                                newLine.addClass('$panelid-$keyTab-row');
                                newLine.addClass('odpNew');
                                newLine.find('[id^=\"".$tab['id']."\"]').each(function(i){
                                    $(this).attr('id',$(this).attr('id')+'_'+(parseInt($('#$panelid-$keyTab-nextLine').val())+1));
                                });
                                $('#$panelid-$keyTab-nextLine').val(parseInt($('#$panelid-$keyTab-nextLine').val())+1);
                                
                                ".$panelid.$keyTab."ChangeLineStatus(newLine, 'N');
                                
                                $(\"#$panelid-$keyTab-svLine\").append(newLine);
                            }
                            
                            function ".$panelid.$keyTab."RemoveLine(line){
                                jConfirm('Tem certeza que quer excluir?', 'Exclusão de linha', function(r) {
                                    if(r == false){return;}
                                    
                                    if(line.hasClass('odpNew')){
                                        line.remove();
                                    }else{
                                        $.post('aSubmitter.php', {a: 'remove', e: '".ucfirst($keyTab)."', id: line.find('input[name=\"id\"]').val()}, function(data){
                                               
                                            var result= null;
                                            eval('result = '+ data);
                                        
                                            msg = '';
                                            for(i=0; i<result.messages.length; i++){
                                                msg = msg+ result.messages[i];
                                            }
                                            
                                            if(result.status == 'S'){
                                                line.remove();
                                            }else if(result.status == 'E'){
                                                odpErrorMessageShow(msg);
                                            }else{
                                                odpErrorMessageShow('Ocorreu um erro inesperado. Entre em contato com o nosso suporte');
                                            }
                                        });
                                    }
                                }); 
                            }
                            
                            function ".$panelid.$keyTab."EditLine(line){

                                line.removeClass('odpSaved').addClass('odpEdited');
                                line.find('.$panelid-$keyTab-fld').each(function(i){
                                    $(this).children().removeAttr('disabled');
                                });
                                ".$panelid.$keyTab."ChangeLineStatus(line, 'E');
                            }
                            
                            function ".$panelid.$keyTab."SaveLine(line){
                            
                                var vHasError = false;
                                line.find('.odpRequired').each(function(i){
                                    if($(this).is('input')){
                                        if($(this).val().length==0){
                                            vHasError = true;
                                        }
                                    }else if($(this).is('select')){
                                        if($(this).val()=='0'){
                                            vHasError = true;
                                        }
                                    }
                                });
                                if(vHasError){
                                    line.addClass('odpInputError');
                                    odpErrorMessageShow('Campos obrigatórios não foram preenchidos');
                                    return;
                                }else{
                                    line.removeClass('odpInputError');
                                }
                                
                                var vJson = 'f: \"p".ucfirst($tab['id'])."\"';
                                
                                line.find('.$panelid-$keyTab-fld').each(function(i){
                                    var vName = $(this).children().attr('name');//.split('_');
                                    //vName = vName[2];
                                    var vValue = $(this).children().val();
                                    
                                    if(vName.length>0){
                                        vJson = vJson + ','+ vName+ ': \"' + vValue+'\"';
                                    }
                                });
                                vJson = '{ '+vJson+' }';
                                eval('vJson = '+vJson );
                                
                                $.post('aSubmitter.php', vJson, function(data){
                                       
                                    var result= null;
                                    eval('result = '+ data);
                                
                                    msg = '';
                                    for(i=0; i<result.messages.length; i++){
                                        msg = msg+ result.messages[i];
                                    }
                                    
                                    if(result.status == 'S'){
                                        if(line.hasClass('odpNew')){
                                            line.find('input[name=\"id\"]').val(result.id);
                                        }
                                        line.removeClass('odpNew').removeClass('odpEdited').addClass('odpSaved');
                                        line.find('.$panelid-$keyTab-fld').each(function(i){
                                            if($(this).children().attr('name').length>0){
                                                $(this).children().attr(\"disabled\",\"disabled\");
                                            }
                                        });
                                        ".$panelid.$keyTab."ChangeLineStatus(line, 'S');
                                    }else if(result.status == 'E'){
                                        odpErrorMessageShow(msg);
                                    }else{
                                        odpErrorMessageShow('Ocorreu um erro inesperado. Entre em contato com o nosso suporte');
                                    }
                                });
                            }
                            
                            function ".$panelid.$keyTab."ChangeLineStatus(line, status){
                            
                                if(status == 'E'){// Edited
                                    line.find('.odpImgDelete').show();
                                    line.find('.odpImgSave').show();
                                    line.find('.odpImgEdit').hide();
                                }else if (status == 'S'){// Saved
                                    line.find('.odpImgDelete').show();
                                    line.find('.odpImgSave').hide();
                                    line.find('.odpImgEdit').show();
                                }else if (status == 'N'){// New
                                    line.find('.odpImgDelete').show();
                                    line.find('.odpImgSave').show();
                                    line.find('.odpImgEdit').hide();
                                }
                            }
                        </script>
                    </div>
                </div>
            </div>";
        }//End of tab
        
        //odpItbPanHSel
        //odpItbPanHNSel
        $htmlHeader = "";
        foreach ($tabLabel as $key => $tlabel){
            $htmlHeader = $htmlHeader . 
            "<a href='javascript:void(0);' id='".$panelid.$tlabel['id']."lbl' class='".(!$key?"odpTabSel":"odpTabNSel")."' 
                onclick='jQuery(this).closest(\"div\").children(\"a\").each(".
                            "function(index) {".
                                "$(this).removeClass(\"odpTabSel\").addClass(\"odpTabNSel\");".
                            "});".
                          "jQuery(this).removeClass(\"odpTabNSel\").addClass(\"odpTabSel\");".
                          $panelid."Show(\"".$tlabel['id']."\");'".
             ">".$tlabel['label']."</a>";
        }
        
        $htmlHeader = "<div>".$htmlHeader.
        "<script type='text/javascript'>".
        
            "function ".$panelid."Show(i$panelid){".
            "$('div[id=\"$panelid\"]').children().each(
                            function(index) {
                                if($(this).is('#$panelid-'+i$panelid)){
                                    $(this).show();
                                }else{
                                    $(this).hide();
                                }
                            });".
            "}".
        "</script>".
        "</div>";
        
        return $htmlHeader."<div id='$panelid'>".$htmltabs."</div>"."<script type='text/javascript'> $htmlmask</script>";
    }
    
    public static function tabPanelInsert2($array,$panelid) {
        
        $tabLabel = array();
        
        $html = "";
        $htmltabs = "";
        $masks = array();// like "array(array('fieldid'=>'','mask'=>''));"
        
        foreach($array as $keyT => $tab){
            
            $keyTab = isset($tab['id'])?$tab['id']:$keyT;
            $fieldLabel =array();
            $fieldsnullable = array();
            $aFldValidation = array();
            $htmlRows = '';
            $htmlDef = '';
            $fields=array();
            
            $rows = isset($tab['rows'])?$tab['rows']:array();
            $rows['default'] = array();
            
            //Set  tab label
            $tabLabel[$keyT] = array('label'=>$tab['label'],'id'=>$keyTab);

            //for each field of tab
            foreach($tab['fields'] as $keyField => $field){
                
                $id    = isset($field['id'])?$tab['id'].'_'.$field['id']:$tab['id'].'_'.$keyField;
                $name  = 'a_'.$tab['id'].'_'.$field['name'];
                $label = isset($field['label'])?$field['label']:"";
                $type  = isset($field['type'])?$field['type']:"text";
                $width = isset($field['width'])?"style='width: ".$field['width'].";'":"";
                $maxlength = isset($field['maxlength'])?"maxlength='".$field['maxlength']."'":"";
                $onchange = isset($field['onchange'])?"onchange=".$field['onchange']."":"";
                //Required fields should has the odpRequired class
                $class = (isset($field['mask'])?"odpInput ".$name:"odpInput").(isset($field['required'])&& in_array($field['required'],array('1',1,true,'true')) ? ' odpRequired':'');
                
                $htmlfield = "";
                
                //Set field masks into array
                if(isset($field['mask'])){
                    array_push($masks, array('fieldid'=>$name, 'mask'=>$field['mask']));
                }
                
                //Verify field's type
                if($type == "text"){
                    //Set the Header field
                    $htmlfield = "<input id='$id' name='$name' class='$class' $maxlength $onchange style='width: 100%; 'type='text' >";
                    
                }elseif($type == "hidden"){
                    $htmlfield = "<input id='$id' name='$name' class='$class' $maxlength $onchange style='width: 100%;' type='$type'>";
                    
                }elseif($type == "checkbox"){
                    $htmlfield = "<input id='$id' name='$name' class='$class' $onchange style='width: 100%;' type='$type'>";
                    
                }elseif($type == "select"){
                    $loadType = isset($field['options']['load'])?'L':'F';

 /*                   if(isset($field['options']['defineparameter'])){
                        foreach($field['options']['defineparameter'] as $keyDefPar => $columnToLoad){
                            $field['options']['by'][$keyDefPar] = $aFieldValues[$columnToLoad];
                        } 
                    }*/
                    
                    $properties = array('name'=>$name, 'id'=>$id, 'class'=>$class);
                    if(isset($field['onchange']))
                        $properties['onchange']= $field['onchange'];
                    
                    $htmlfield = Odpcp::select($loadType,$field['options'],null,null,$properties);                                                            
                }//End of field type verification
                
                array_push($fieldLabel, "<td $width><span>$label</span></td>");
                
                //Set html field into array
                array_push($fields,$htmlfield);
            
            }//End of field
            
            $htmlfield = "";
            foreach($fields as $column){
                $htmlfield = $htmlfield."<td class='$panelid-$keyTab-fld'>".$column."</td>";
            }
            
            $htmlIco = "<td style='width: 20px'><input type='button' title='Excluir' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."delete.gif\");' onclick='".$panelid.$keyTab."RemoveLine($(this).closest(\"tr\"));'></td>";
            $htmlDef = $htmlfield.$htmlIco;
            
            //Convert arrays values to html
            $htmllabel = "";
            foreach($fieldLabel as $column){
                $htmllabel = $htmllabel.$column;
            }
            
/*            $htmlvalidation = "";
            foreach($aFldValidation as $column){
                $htmlvalidation = $htmlvalidation.'if('.$column[0].'){alert("'.$column[1].'");return;}';
            }*/
            
            $htmlmask = "";
            foreach($masks as $column){
                $htmlmask = $htmlmask.'$(".'.$column['fieldid'].'").mask("'.$column['mask'].'");';
            }
            $htmlmask = sizeof($masks)?'jQuery(function($){'.$htmlmask.' });':"";
            
            //panel
            $htmltabs = $htmltabs. 
            "<div id='$panelid-$keyTab' name='$panelid-$keyTab' class='odpMIPanel' style='display: ".(!$keyT?"block":"none").";'>
                <div>
                    <div class='odpITPhp'>
                    <input type='hidden' id='$panelid-$keyTab-nextLine' value='0'>
                    <div><a href='javascript:void(0);' class='odpTabSel' onclick='".$panelid.$keyTab."NewLine();'>Novo</a></div>
                    
                        <table style='display: none;'>
                            <tr id='$panelid-$keyTab-defaultLine'>
                                $htmlDef
                            </tr>
                        </table>
                        
                        <table cellpadding='0' cellspacing='0px' border='0' id='$panelid-$keyTab-svLine'>
                            <tr style='font-weight: bold;'>
                                $htmllabel
                            </tr>
                        </table>

                        <script type='text/javascript'>
                            ".$panelid. $keyTab ."NewLine();
                            ".$panelid. $keyTab ."NewLine();
                            ".$panelid. $keyTab ."NewLine();
                            ".$panelid. $keyTab ."NewLine();
                            ".$panelid. $keyTab ."NewLine();
                            
                            function ".$panelid.$keyTab."NewLine(){
                                newLine = $(\"#$panelid-$keyTab-defaultLine\").clone();
                                newLine.removeAttr('id');
                                newLine.addClass('$panelid-$keyTab-row');
                                newLine.find('[id^=\"".$tab['id']."\"]').each(function(i){
                                    $(this).attr('id',$(this).attr('id')+'_'+(parseInt($('#$panelid-$keyTab-nextLine').val())+1));
                                });
                                $('#$panelid-$keyTab-nextLine').val(parseInt($('#$panelid-$keyTab-nextLine').val())+1);
                                
                                $(\"#$panelid-$keyTab-svLine\").append(newLine);
                            }
                            
                            function ".$panelid.$keyTab."RemoveLine(line){
                                jConfirm('Tem certeza que quer excluir?', 'Exclusão de linha', function(r) {
                                    if(r == false){return;}
                                    line.remove();
                                }); 
                            }
                            
                            function ".$panelid.$keyTab."SaveLine(line){
                            
                                var vJson = 'f: \"save".ucfirst($tab['id'])."\"';
                                
                                line.children().each(function(i){
                                    var vName = $(this).children().attr('name');
                                    var vValue = $(this).children().val();
                                    
                                    if(vName.length>0){
                                        vJson = vJson + ','+ vName+ ': \"' + vValue+'\"';
                                    }
                                });
                                vJson = '{ '+vJson+' }';
                                eval('vJson = '+vJson );
                                
                                $.post('aSubmitter.php', vJson, function(data){
                                       
                                    line.children().each(function(i){
                                        if($(this).children().attr('name').length>0){
                                            $(this).children().removeClass(\"odpInputNotSaved\").removeClass(\"odpNew\").removeClass(\"odpEdit\").addClass(\"odpInputRO\");
                                            $(this).children().attr(\"disabled\",\"disabled\");
                                        }
                                    });
                                });
                            
                            }
                        </script>
                    </div>
                </div>
            </div>";
        }//End of tab
        
        //odpItbPanHSel
        //odpItbPanHNSel
        $htmlHeader = "";
        foreach ($tabLabel as $key => $tlabel){
            $htmlHeader = $htmlHeader . 
            "<a href='javascript:void(0);' id='".$panelid.$tlabel['id']."lbl' class='".(!$key?"odpTabSel":"odpTabNSel")."' 
                onclick='jQuery(this).closest(\"div\").children(\"a\").each(".
                            "function(index) {".
                                "$(this).removeClass(\"odpTabSel\").addClass(\"odpTabNSel\");".
                            "});".
                          "jQuery(this).removeClass(\"odpTabNSel\").addClass(\"odpTabSel\");".
                          $panelid."Show(\"".$tlabel['id']."\");'".
             ">".$tlabel['label']."</a>";
        }
        
        $htmlHeader = "<div>".$htmlHeader.
        "<script type='text/javascript'>".
        
            "function ".$panelid."Show(i$panelid){".
            "$('div[id=\"$panelid\"]').children().each(
                            function(index) {
                                if($(this).is('#$panelid-'+i$panelid)){
                                    $(this).show();
                                }else{
                                    $(this).hide();
                                }
                            });".
            "}".
        "</script>".
        "</div>";
        
        return $htmlHeader."<div id='$panelid'>".$htmltabs."</div>"."<script type='text/javascript'> $htmlmask</script>";
    }
    
    /*
      
      array(  --Panel
            
            array(    --Tab
                   'label' =>'Telefones (exemplo)';
                   'fields'=>array( 
                                     array(  --field text
                                           'id'      =>'name', //will be concatenated with panelid and tabid. Ex: wq-1-name 
                                           'label'   =>'Nome',
                                           'type'    =>'text',  //text, checkbox, select
                                           'width'   =>'10%',
                                           'nullable'=> 'false',
                                           'maxlength' => 5,
                                           'onchange' => 'alert();', //javascript
                                           'mask' => '' //jquery.mask format 
                                           ),
                                     array(  --field select
                                           'label'   =>'UF',
                                           'type'    =>'select',
                                           'width'   =>'10%',
                                           'nullable'=>'false',
                                           /## Options pode ser carregado de dois modos##/
                                           'options'=>array(
                                                             array('value'=>'10','label'=>'MG'),
                                                             array('value'=>'11','label'=>'SP')
                                                            )
                                           ou
                                           'options'=>array( 'load' => 'State', //obrigatório
                                                             'by'  => array('field1'=>'valor1','id_cidade'=>'23'),  //opcional
                                                             'value' => 'id',  //property of created object to get html option property 'value' 
                                                             'label' => 'nameCity' //property of created object to get html option property 'label'
                                                            )
                                           /## Fim options ##/
                                           )
                                   )
                  )
            ),
            array()--Tab2
      
      */
    public static function tabPanel($array,$panelid) {
        
        $tabLabel = array();
        
        $html = "";
        $htmltabs = "";
        $masks = array();// like "array(array('fieldid'=>'','mask'=>''));"
        
        foreach($array as $keyTab => $tab){
            
            $fieldLabel =array();
            $fieldsnullable = array();
            $aFldValidation = array();
            $htmlRows = '';
            $htmlDef = '';
            
            $rows = isset($tab['rows'])?$tab['rows']:array();
            $rows['default'] = array();
            
            //Set  tab label
            array_push($tabLabel, $tab['label']);

            foreach($rows as $keyRow => $row){
                
                $fields = array();
                $aFieldValues = array();
                
                //for each field of tab
                foreach($tab['fields'] as $keyField => $field){
                    
                    $value = (!strcmp($keyRow, 'default'))?null:$row[$keyField];
                    array_push($aFieldValues,$value);
                    
                    $id    = isset($field['id'])?$panelid.'-'.$keyTab.'-'.$field['id']:$panelid."-".$keyTab."-".$keyField;
                    $name  = 'a_'.$tab['id'].'_'.$field['name'];
                    $label = isset($field['label'])?$field['label']:"";
                    $type  = isset($field['type'])?$field['type']:"text";
                    $width = isset($field['width'])?"style='width: ".$field['width'].";'":"";
                    $maxlength = isset($field['maxlength'])?"maxlength='".$field['maxlength']."'":"";
                    $onchange = isset($field['onchange'])?"onchange=".$field['onchange']."":"";
                    $readonly = (!is_null($value))?"disabled='disabled'":"";
                    $class = (!is_null($value))?"odpInputRO":"odpInputNotSaved odpNew"; 
                    
                    $htmlfield = "";
                    
                    //Set field masks into array
                    if(isset($field['mask'])){
                        array_push($masks, array('fieldid'=>$name, 'mask'=>$field['mask']));
                    }
                    
                    //Verify field's type
                    if($type == "text"){
                        //Set the Header field
                        $hvalue = is_null($value)?"":"value='$value'";
                        $htmlfield = "<input id='".$id."' name='$name' class='$class' type='text' $maxlength $onchange $hvalue $readonly style='width: 100%;'>";
                        
                    }elseif($type == "hidden"){
                        $hvalue = is_null($value)?"":"value='$value'";
                        $htmlfield = "<input name='$name' class='$class' type='$type' $maxlength $onchange $hvalue $readonly style='width: 100%;'>";
                        
                    }elseif($type == "checkbox"){
                        $hvalue = is_null($value)?"":"value='$value'";
                        $htmlfield = "<input name='$name' class='$class' type='$type' $onchange $hvalue $readonly style='width: 100%;'>";
                        
                    }elseif($type == "select"){
                        $loadType = isset($field['options']['load'])?'L':'F';
    
                        if(isset($field['options']['defineparameter'])){
                            foreach($field['options']['defineparameter'] as $keyDefPar => $columnToLoad){
                                $field['options']['by'][$keyDefPar] = $aFieldValues[$columnToLoad];
                            } 
                        }
                        
                        $htmlfield = Odpcp::select($loadType,$field['options'],$value,isset($field['onchange'])?$field['onchange']:null,$name,null,(!is_null($value)),$class);                                                            
                    }//End of field type verification
                    
                    if(!strcmp($keyRow, 'default')){
                        //Set html label into array
                        array_push($fieldLabel, "<td $width><span>$label</span></td>");
                    }
                    
                    //Set html field into array
                    array_push($fields,$htmlfield);
                
                }//End of field
                
                $htmlfield = "";
                foreach($fields as $column){
                    $htmlfield = $htmlfield."<td>".$column."</td>";
                }
                
                $htmlIco = "<td style='width: 20px'><input type='button' title='Salvar' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."save.gif\");' onclick='".$panelid.$keyTab."SaveLine($(this).closest(\"tr\"));'></td>".
                           "<td style='width: 20px'><input type='button' title='Editar' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."edit.gif\");' onclick='".$panelid.$keyTab."EditLine($(this).closest(\"tr\"));'></td>".
                           "<td style='width: 20px'><input type='button' title='Excluir' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."delete.gif\");' onclick='".$panelid.$keyTab."RemoveLine($(this).closest(\"tr\"));'></td>";
                //width='100%' 
                if(!strcmp($keyRow, 'default')){
                    $htmlDef = $htmlfield.$htmlIco;
                }else{
                    $htmlRows = $htmlRows."<tr class='odpITPsvLine'>".$htmlfield.$htmlIco."</tr>";
                }
            }//End of rows
            
            //Convert arrays values to html
            $htmllabel = "";
            foreach($fieldLabel as $column){
                $htmllabel = $htmllabel.$column;
            }
            

            
/*            $htmlvalidation = "";
            foreach($aFldValidation as $column){
                $htmlvalidation = $htmlvalidation.'if('.$column[0].'){alert("'.$column[1].'");return;}';
            }*/
            
            $htmlmask = "";
            foreach($masks as $column){
               // select[id|="mTabPn-0-city"]
                $htmlmask = $htmlmask.'$("input[name$=\''.$column['fieldid'].'\']").each(function(i){$("#"+$(this).attr("id")).mask("'.$column['mask'].'");});';
            }
            $htmlmask = sizeof($masks)?'jQuery(function($){'.$htmlmask.' });':"";
            
            //panel
            $htmltabs = $htmltabs. 
            "<div id='$panelid-$keyTab' name='$panelid-$keyTab' class='odpMIPanel' style='display: ".(!$keyTab?"block":"none").";'>
                <div>
                    <div class='odpITPhp'>
                        <div><a href='javascript:void(0);' class='odpTabSel' onclick='".$panelid.$keyTab."NewLine();'>Novo</a></div>
                    
                        <table style='display: none;'>
                            <tr id='$panelid-$keyTab-defaultLine'>
                                $htmlDef
                            </tr>
                        </table>
                        
                        <table cellpadding='0' cellspacing='0px' border='0' id='$panelid-$keyTab-svLine'>
                            <tr style='font-weight: bold;'>
                                $htmllabel
                            </tr>
                            $htmlRows
                        </table>

                        <script type='text/javascript'>
                            function ".$panelid.$keyTab."NewLine(){
                                $(\"#$panelid-$keyTab-svLine\").append(\"<tr class='odpITPnwLine'>\"+$(\"#$panelid-$keyTab-defaultLine\").html()+\"</tr>\");
                                loadMasks();
                            }
                            
                            function ".$panelid.$keyTab."EditLine(line){
                                newelement = \"<tr><td style='width: 20px'><input type='button' class='odpButtonImg' style='background-image: url(\"+'\"".CT_FL_EXT_IMAGES."delete.gif\"'+\");' onclick='$(this).closest(\"+'\"tr\"'+\").remove();'></td></tr>\";
                                $(\"#$panelid-$keyTab-svLine\").append(newelement);
                            }
                            
                            function ".$panelid.$keyTab."RemoveLine(line){
                            
                                if(line.find('input[name=\"a_".$tab['id']."_id\"]').hasClass('odpInputNotSaved odpNew')){
                                    line.remove();
                                    return;
                                }
                            
                                jConfirm('Tem certeza que quer excluir?', 'Confirmation Dialog', function(r) {
                                    if(r == false){return;}
                                    
                                    var vJson = { f: \"remove".ucfirst($tab['id'])."\",
                                                  a_".$tab['id']."_id: line.find('input[name=\"a_".$tab['id']."_id\"]').val()
                                                };
                                    
                                    $.post('aSubmitter.php', vJson, function(data){
                                        if(data.status){
                                            line.remove();
                                        }else{
                                            odpSoftMsgShow(data.message);
                                        }
                                    });
                                }); 
                            }
                            
                            function ".$panelid.$keyTab."SaveLine(line){
                            
                                var vJson = 'f: \"save".ucfirst($tab['id'])."\"';
                                
                                line.children().each(function(i){
                                    var vName = $(this).children().attr('name');
                                    var vValue = $(this).children().val();
                                    
                                    if(vName.length>0){
                                        vJson = vJson + ','+ vName+ ': \"' + vValue+'\"';
                                    }
                                });
                                vJson = '{ '+vJson+' }';
                                eval('vJson = '+vJson );
                                
                                $.post('aSubmitter.php', vJson, function(data){
                                       
                                    line.children().each(function(i){
                                        if($(this).children().attr('name').length>0){
                                            $(this).children().removeClass(\"odpInputNotSaved\").removeClass(\"odpNew\").removeClass(\"odpEdit\").addClass(\"odpInputRO\");
                                            $(this).children().attr(\"disabled\",\"disabled\");
                                        }
                                    });
                                });
                            
                            }
                        </script>
                    </div>
                </div>
            </div>";
        }//End of tab
        
        //odpItbPanHSel
        //odpItbPanHNSel
        $htmlHeader = "";
        foreach ($tabLabel as $key => $tlabel){
            $htmlHeader = $htmlHeader . 
            "<a href='javascript:void(0);' id='".$panelid.$key."lbl' class='".(!$key?"odpTabSel":"odpTabNSel")."' 
                onclick='jQuery(this).closest(\"div\").children(\"a\").each(".
                            "function(index) {".
                                "$(this).removeClass(\"odpTabSel\").addClass(\"odpTabNSel\");".
                            "});".
                          "jQuery(this).removeClass(\"odpTabNSel\").addClass(\"odpTabSel\");".
                          $panelid."Show($key);'".
             ">$tlabel</a>";
        }
        
        $htmlHeader = "<div>".$htmlHeader.
        "<script type='text/javascript'>".
            "loadMasks();".
        
            "function loadMasks(){".
                $htmlmask.
            "}".
        
            "function ".$panelid."Show(i$panelid){".
                
            "$('div[id|=\"$panelid\"]').each(
                            function(index) {
                                if($(this).is('#$panelid-'+i$panelid)){
                                    $(this).show();
                                }else{
                                    $(this).hide();
                                }
                            });".
        
            "}".
        "</script>".
        "</div>";
        
        return $htmlHeader.$htmltabs;
    }
    
    /*
     * $type = F(fixed) / L(load)
     * 
     * Examples:
     * $type = F, $options = array(array('value'=>'1','label'=>'MG'),array('value'=>'2','label'=>'SP'))
     * 
     * $type = L, $options = array('load'=>'State',                        //mandatory
     *                             'by'=>'array('id_city'=>'23')',         //optional
     *                             'value' => 'id',                        //mandatory = property of created object to get html option property 'value' 
                                   'label' => 'nameCity'                   //mandatory = property of created object to get html option property 'label'
     *                            )
     * 
     */
    public static function select($type='F',$options=array(), $selected=0,$nullable=false, $properties){ 
        
        $onchange= isset($properties['onchange'])?$properties['onchange']:null;
        $readonly = isset($properties['readonly'])?$properties['readonly']:false;
        $class = isset($properties['class'])?$properties['class']:'odpInput';
        $name = isset($properties['name'])?$properties['name']:null;
        $id = isset($properties['id'])?$properties['id']:null;
        $width = isset($properties['width'])?$properties['width']:'100%';
        $style = isset($properties['style'])?$properties['style']:'';
        
        $htmlSelOptions ="<option".($selected?" ":" selected='selected' ")."value='0'>Selecione uma opção</option>";
        
        if($type=='F'){
            foreach($options as $opt){
                $htmlSelOptions = $htmlSelOptions. "<option".($selected==$opt['value']?" selected='selected' ":" ")."value='".$opt['value']."'>".$opt['label']."</option>";
            }
        }elseif($type=='L' && isset($options['by'])){
            //Import Generic controller
            require_once Odpcp::$controllerFilepath.'/'.Odpcp::$genericControllerClass.'.php';
            //Import required class
            eval('require_once "'.Odpcp::$modelFilepath.'/'.$options['load'].'.php";');
      
            $generic=null;
            eval('$generic = new '.Odpcp::$genericControllerClass.'();');
            
            $listByObject = null;
            
            eval('$listByObject= new '.$options['load'].'();');
            
            foreach($options['by'] as $key => $data){
                if(is_null($data)){
                    $options['by'][$key] = 0;
                }
            }
            
            //Get list of objects
            $selOptionList = $generic->listByParameters($listByObject, $options['by']);
            
        }else{
            //Import Generic controller
            require_once Odpcp::$controllerFilepath.'/'.Odpcp::$genericControllerClass.'.php';
            //Import required class
            eval('require_once "'.Odpcp::$modelFilepath.'/'.$options['load'].'.php";');
            
            $generic=null;
            eval('$generic = new '.Odpcp::$genericControllerClass.'();');
            $listByObject = null;
            
            eval('$listByObject= new '.$options['load'].'();');
            
            $selOptionList = $generic->listAll($listByObject);
        }
        
        //Convert object list into html options 
        if($type=='L'){
            foreach ($selOptionList as $optObj){
                $optObjValue = null;
                $optObjLabel = null;
                
                //Load Option Value
                eval('$optObjValue = $optObj->get'.ucfirst($options['value']).'();');
                if(is_object($optObjValue)){
                    $optObjValue = $optObjValue->getId();
                }
                //Load Option Label
                eval('$optObjLabel = $optObj->get'.ucfirst($options['label']).'();');
                if(is_object($optObjLabel)){
                    $optObjLabel = $optObjLabel->getId();
                }
                
                $htmlSelOptions = $htmlSelOptions. "<option".($selected==$optObjValue?" selected='selected' ":" ")."value='$optObjValue'>$optObjLabel</option>";
            } 
        }
        
        if(!empty($onchange)){
            $onchange='onchange='.$onchange;
        }
     
        $xpto = "<select name='$name' id='$id' class='$class' ".($readonly?"disabled='disabled'":"")." $onchange "."style='".(!is_null($width)?"width: $width;":"").$style."'>$htmlSelOptions</select>";
//        echo $xpto;
        return $xpto;    
    }
        
    /*
      
      array(  --Panel
            
            array(    --Tab
                   'label' =>'Telefones (exemplo)';
                   'fields'=>array( 
                                     array(  --field text
                                           'id'      =>'name', //will be concatenated with panelid and tabid. Ex: wq-1-name 
                                           'label'   =>'Nome',
                                           'type'    =>'text',  //text, checkbox, select
                                           'width'   =>'10%',
                                           'nullable'=> 'false',
                                           'maxlength' => 5,
                                           'onchange' => 'alert();', //javascript
                                           'mask' => '' //jquery.mask format 
                                           ),
                                     array(  --field select
                                           'label'   =>'UF',
                                           'type'    =>'select',
                                           'width'   =>'10%',
                                           'nullable'=>'false',
                                           /## Options pode ser carregado de dois modos##/
                                           'options'=>array(
                                                             array('value'=>'10','label'=>'MG'),
                                                             array('value'=>'11','label'=>'SP')
                                                            )
                                           ou
                                           'options'=>array( 'load' => 'State', //obrigatório
                                                             'by'  => array(array('field1'=>'valor1'),  //opcional
                                                                            array('id_cidade'=>'23')),
                                                             'value' => 'id',  //property of created object to get html option property 'value' 
                                                             'label' => 'nameCity' //property of created object to get html option property 'label'
                                                            )
                                           /## Fim options ##/
                                           )
                                   )
                  )
            ),
            array()--Tab2
      
      */
    public static function tabPanelInsert($array,$panelid) {
 
        $tabLabel = array();
        
        $html = "";
        $htmltabs = "";
        $masks = array();// like "array(array('fieldid'=>'','mask'=>''));"
        
        foreach($array as $keyTab => $tab){
            
            $fields = array();
            $fieldLabel =array();
            $fieldsnullable = array();
            $newLineFields = array();
            $aFldValidation = array();
            
            //Set  tab label
            array_push($tabLabel, $tab['label']);

            //for each field of tab
            foreach($tab['fields'] as $keyField => $field){
                
                $id    = isset($field['id'])?$panelid.'-'.$keyTab.'-'.$field['id']:$panelid."-".$keyTab."-".$keyField;
                $name  = 'a_'.$tab['id'].'_'.$field['name'];
                $label = isset($field['label'])?$field['label']:"";
                $type  = isset($field['type'])?$field['type']:"text";
                $width = isset($field['width'])?"style='width: ".$field['width'].";'":"";
                $maxlength = isset($field['maxlength'])?"maxlength='".$field['maxlength']."'":"";
                $onchange = isset($field['onchange'])?"onchange='".$field['onchange']."'":"";
                
                $htmlfield = "";
                
                //Set field masks into array
                if(isset($field['mask'])){
                    array_push($masks, array('fieldid'=>$id, 'mask'=>$field['mask']));
                }
                
                //Verify field's type
                if($type == "text"){
                    //Set the Header field
                    $htmlfield = "<td $width><input id='$id' name='$name' class='cadastroText' type='$type' $maxlength $onchange ".(!strpos($field['width'],'%')?$width:"style='width: 100%;'")."></td>";
                    //Set the field would be created when user click at add
                    array_push($newLineFields,'<td '.$width.'><input name=\'odpnewline\' class=\'odpTextboxRO\' readonly=\'readonly\' style=\'width: 100%;\' type=\'text\' value=\'"+$("#'.$id.'").val()+"\'></td>');
                    //Set the validations
                    if(!isset($field['nullable'])){
                        array_push($aFldValidation, array('$("#'.$id.'").val().length == 0','Campo '.$field['label'].' deve ser informado'));
                    }
                    
                }elseif($type == "hidden"){
                    $htmlfield = "<td $width><input id='$id' name='$name' class='cadastroText' type='$type' $maxlength $onchange ".(isset($field['width'])?$width:"style='width: 100%;'")."></td>";
                    array_push($newLineFields,'<td '.$width.'><input name=\'odpnewline\' class=\'odpTextboxRO\' readonly=\'readonly\' style=\'width: 100%;\' type=\'hidden\' value=\'"+$("#'.$id.'").val()+"\'></td>');
                    //Set the validations
                    if(!isset($field['nullable'])){
                        array_push($aFldValidation, array('$("#'.$id.'").val().length == 0','Campo '.$field['label'].' deve ser informado'));
                    }
                    
                }elseif($type == "checkbox"){
                    $htmlfield = "<td $width><input id='$id' name='$name' class='cadastroText' type='$type' $maxlength $onchange ".(!strpos($field['width'],'%')?$width:"style='width: 100%;'")."></td>";
                    array_push($newLineFields,'<td '.$width.'><input name=\'odpnewline\' class=\'odpTextboxRO\' readonly=\'readonly\' style=\'width: 100%;\' type=\'text\' value=\'"+$("#'.$id.'").is(":checked")?"Sim":"Não")+"\'></td>');
                    
                }elseif($type == "select"){
                    $loadType = isset($field['options']['load'])?'L':'F';
//($type='F',$options=array(), $selected=0, $onchange=null, $name=null,$nullable=false, $readonly=false, $class='odpInputRO')                    
                    $htmlfield = Odpcp::select($loadType,$field['options'],0,isset($field['onchange'])?$field['onchange']:null,$name,null,null,'odpInput',$id,(isset($field['width'])?(!strpos($field['width'],'%')?$field['width']:'100%'):null));                                                            
                    $htmlfield = "<td $width>".$htmlfield."</td>";
                }//End of field type verification
                
                //Set html label into array 
                array_push($fieldLabel, "<td $width><span>$label</span></td>");
                
                //Set html field into array
                array_push($fields,$htmlfield);
            
            }//End of field
            
            //Convert arrays values to html
            $htmllabel = "";
            foreach($fieldLabel as $column){
                $htmllabel = $htmllabel.$column;
            }
            
            $htmlfield = "";
            foreach($fields as $column){
                $htmlfield = $htmlfield.$column;
            }
            
            $htmlnewline = "";
            foreach($newLineFields as $column){
                $htmlnewline = $htmlnewline.$column;
            }
            
            $htmlvalidation = "";
            foreach($aFldValidation as $column){
                $htmlvalidation = $htmlvalidation.'if('.$column[0].'){alert("'.$column[1].'");return;}';
            }
            
            $htmlmask = "";
            foreach($masks as $column){
                $htmlmask = $htmlmask.'$("#'.$column['fieldid'].'").mask("'.$column['mask'].'");';
            }
            $htmlmask = sizeof($masks)?'jQuery(function($){'.$htmlmask.' });':"";
            
            //panel
            $htmltabs = $htmltabs. 
            "<div id='$panelid-$keyTab' name='$panelid-$keyTab' class='odpMIPanel' style='display: ".(!$keyTab?"block":"none").";'>
                <div>
                    <div class='odpITPhp'>
                        <table cellpadding='0' cellspacing='1px' border='0'>
                            <tr style='font-weight: bold;'>
                                $htmllabel
                                <td></td>
                            </tr>
                            <tr id='$panelid-$keyTab-defaultLine'>
                                $htmlfield
                                <td style='width: 20px'><input type='button' class='odpButtonImg' style='background-image: url(\"".CT_FL_EXT_IMAGES."save.gif\");' onclick='".$panelid.$keyTab."SaveLine();'></td>
                            </tr>
                        </table>

                        <script type='text/javascript'>
                            function ".$panelid.$keyTab."SaveLine(){
                            
                                $htmlvalidation
                                newelement = $('#$panelid-$keyTab-defaultLine').clone();
                                newelement.html('');
                                newelement.removeAttr('id');
                                
                                $('#$panelid-$keyTab-defaultLine').children().each(function(i){
        
                                    child = $(this).children();
                                    vField = null;

                                    if(child.attr('name').length > 0){
                                        if(child.is('select')){
                                            vField = $(this).clone();
                                            vField.children().val(child.val());
                                        }else{
                                            vField = $(this).clone();
                                        }
                                        vField.children().attr('disabled','disabled');
                                        vField.children().removeAttr('id');
                                        
                                        newelement.append(vField);
                                    }
                                });

                                $('#$panelid-$keyTab-svLine').append(newelement);
                                //newelement = \"<tr>$htmlnewline<td style='width: 20px'><input type='button' class='odpButtonImg' style='background-image: url(\"+'\"".CT_FL_EXT_IMAGES."delete.gif\"'+\");' onclick='$(this).closest(\"+'\"tr\"'+\").remove();'></td></tr>\";
                                //$(\"#$panelid-$keyTab-svLine\").append(newelement);
                            }
                        </script>
                    </div>
                    <div class='odpITPhp'>
                        <table id='$panelid-$keyTab-svLine' cellpadding='0' cellspacing='1px' border='0' style='width: 100%'>
                        </table>
                    </div>
                </div>
            </div>";
            
        }//End of tab
        
        //odpItbPanHSel
        //odpItbPanHNSel
        $htmlHeader = "";
        foreach ($tabLabel as $key => $tlabel){
            $htmlHeader = $htmlHeader . 
            "<a href='javascript:void(0);' id='".$panelid.$key."lbl' class='".(!$key?"odpTabSel":"odpTabNSel")."' 
                onclick='jQuery(this).closest(\"div\").children(\"a\").each(".
                            "function(index) {".
                                "$(this).removeClass(\"odpTabSel\").addClass(\"odpTabNSel\");".
                            "});".
                          "jQuery(this).removeClass(\"odpTabNSel\").addClass(\"odpTabSel\");".
                          $panelid."Show($key);'".
             ">$tlabel</a>";
        }
        
        $htmlHeader = "<div>".$htmlHeader.
        "<script type='text/javascript'>".
            $htmlmask.
        
            "function ".$panelid."Show(i$panelid){".
                
            "$('div[id|=\"$panelid\"]').each(
                            function(index) {
                                if($(this).is('#$panelid-'+i$panelid)){
                                    $(this).show();
                                }else{
                                    $(this).hide();
                                }
                            });".
        
            "}".
        "</script>".
        "</div>";
        
        return $htmlHeader.$htmltabs;
    }
    
}

?>