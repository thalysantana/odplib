<script type="text/javascript">

/*
 * ODPSC - Simple Crud Form Functions
 */
function odpscDefaultAction(){

    var vSwitch = odpscPageStatus; //Var odpscPageStatus should be setted at the SimpleCrudPage
    
    switch(vSwitch){  
    case 'S':
        odpscSubmitMain('s');
        break;
    case 'I':
        odpscSubmitMain('p');
        break;
    case 'E':
        odpscSubmitMain('p');
        break;
    case 'X':
        odpscChangePageStatus('S');
        break;
    case 'V':
        odpscChangePageStatus('S');
        break;
    }
}
  
function odpscChangePageStatus(pStatus){

    odpscPvPageStatus = odpscPageStatus;
    odpscPageStatus = pStatus;

    switch(pStatus){
        case 'S':
            $('.odpObjField').each(function(i) {
                $(this).removeAttr('disabled');
            });

            $('#mBtEdit').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtSave').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtCancel').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtSearch').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtInsert').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            
            $('#sc_pageStatusDesc').html('Pesquisa');
            $('#sc_pageStatus').val(pStatus);
            $('#sc_search_nav_panel').hide();

            $('#mBtSearch').removeAttr('onclick');
            $('#mBtSearch').unbind();
            $('#mBtSearch').click(function() {
                odpscSubmitMain('s');
            });
            odpscClearFields();
            
            break;
        case 'I':
            $('.odpObjField').each(function(i){
                $(this).removeAttr('disabled');
            });
            $('#id').attr('disabled','disabled');
            
            $('#mBtInsert').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtEdit').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtCancel').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtSearch').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtSave').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#sc_pageStatusDesc').html('Inserção');
            $('#sc_pageStatus').val(pStatus);
            $('#sc_search_nav_panel').hide();
            
            $('#mBtSearch').removeAttr('onclick');
            $('#mBtSearch').unbind();
            $('#mBtSearch').click(function() {
                odpscChangePageStatus('S');
            });
            odpscClearFields();
            
            break;
        case 'E':
            $('.odpObjField').each(function(i){
                $(this).removeAttr('disabled');
            });
            $('#id').attr('disabled','disabled');

            $('#mBtInsert').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtEdit').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtCancel').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtSearch').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtSave').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#sc_pageStatusDesc').html('Edição');
            $('#sc_pageStatus').val(pStatus);
            $('#sc_search_nav_panel').hide();
            
            $('#mBtSearch').removeAttr('onclick');
            $('#mBtSearch').unbind();
            $('#mBtSearch').click(function() {
                odpscChangePageStatus('S');
            });


            break;
        case 'V':
            $('#id').attr('disabled','disabled');

            $('#mBtSave').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtCancel').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtSearch').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtInsert').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtEdit').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#sc_pageStatusDesc').html('Salvo');
            $('#sc_pageStatus').val(pStatus);
            $('#sc_search_nav_panel').hide();
            
            $('#mBtSearch').removeAttr('onclick');
            $('#mBtSearch').unbind();
            $('#mBtSearch').click(function() {
                odpscChangePageStatus('S');
            });

            $('.odpObjField').each(function(i){
                $(this).attr('disabled', 'disabled');
            });
            
            break;
        case 'X':
            $('#id').attr('disabled','disabled');

            $('#mBtSave').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtCancel').hide();//attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
            $('#mBtSearch').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtInsert').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#mBtEdit').show();//removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
            $('#sc_pageStatusDesc').html('Consultado');
            $('#sc_pageStatus').val(pStatus);
            $('#sc_search_nav_panel').show();
            
            $('#mBtSearch').removeAttr('onclick');
            $('#mBtSearch').unbind();
            $('#mBtSearch').click(function() {
                odpscChangePageStatus('S');
            });

            $('.odpObjField').each(function(i){
                $(this).attr('disabled', 'disabled');
            });
            
            break;
    }
}

function odpscSubmitMain(action){

    //Verifica campos obrigatórios
    switch(action){//TODO
        case 'p':
            if($("#name").val() === ''){
                odpMainMessageShow('Preencha todos os campos obrigatórios',null);
                return;
            }
            break;
        case 's':
            if($("#name").val() === '' &&
               $("#id").val() === '' &&
               $("#state").val() === '0'){
                odpMainMessageShow('Preencha todos os campos obrigatórios',null);
                return;
            }
            break;
        default:
            odpMainMessageShow('Ação não conhecida',null);
            return;
    }

    var vJson = 'e: "'+ $('#sc_entity').val() +'", a: "' + action+'"';

    $('.odpObjField').each(function(i){
        vJson = vJson + ','+$(this).attr('id')+' : "'+ $(this).val()+'"';
    });
    
    vJson =  eval('vJson = {'+vJson+'};');
            
    //Submit
    $.post('aSubmitter.php', vJson, function(result){
        msg = '';
        for(i=0; i<result.messages.length; i++){
            msg = msg+ result.messages[i];
        }
        
        if(result.status === 'S'){
            if(action === 'p'){
                if(odpscPageStatus==='I'){
                   $('#id').val(result.output.id);
                   odpscChangePageStatus('V');
                }else if(odpscPageStatus==='E'){
                    $('.odpObjField').each(function(i){
                    	eval("odpscSearchResult[odpscSearchIndex]."+ $(this).attr('id')+ "= '"+$(this).val()+"';");
                    });
                    
                    odpscChangePageStatus('X');
                }                   
            }else if(action === 's'){
                try{
                    if(result.output.length > 0){
                        odpscSearchResult=result.output;
                        odpscSearchIndex=0;
                        
                        odpscShowSearchResult(odpscSearchIndex);
                        odpscChangePageStatus('X');
                    }else{
                        odpSoftMessage('Não retornou resultados');
                    }
                }catch(vEexception){
                    odpErrorMessageShow('Desculpe-nos, ocorreu um erro ao realizar.');
                    return;
                }
            }
            
        }else if(result.status === 'E'){
            odpErrorMessageShow(msg);
        }else{
            odpErrorMessageShow('Ocorreu um erro inesperado. Entre em contato com o nosso suporte');
        }
    },'json');
}

function odpscShowSearchResult(pIdx){
    $('.odpObjField').each(function(i){
            eval("$(this).val(odpscSearchResult[pIdx]."+ $(this).attr('id')+ ");");
    });

    $('#mDataFoundNum').html((pIdx+1)+' / '+odpscSearchResult.length);
    
    if(pIdx===0){
        $('#mBtShowBack').attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
    }else{
        $('#mBtShowBack').removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
    }
    if(pIdx===(odpscSearchResult.length-1)){
        $('#mBtShowNext').attr('disabled', 'disabled').removeClass('odpEnabledBut').addClass('odpDisabledBut');
    }else{
        $('#mBtShowNext').removeAttr('disabled').removeClass('odpDisabledBut').addClass('odpEnabledBut');
    }        
}

function odpscClearFields(){
    $('.odpObjField').each(function(i){
        if($(this).is('select')){
            $(this).val(0);
        }else if($(this).is('input')){
            $(this).val('');
        }
    });
}

</script>