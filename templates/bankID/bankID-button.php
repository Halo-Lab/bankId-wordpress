<div>
    <a id="mbid_start" href="bankid:///?autostarttoken=<?php echo $tocen->autoStartToken; ?>&redirect=null">
        <img src="<?php echo plugins_url(); ?>/bankID-registration/assets/img/bankid.png">
        <span>Starta BankID på denna enhet</span>
    </a>
</div>
<div>
    <a id="mbid_sign" href="#">
        <img src="<?php echo plugins_url(); ?>/bankID-registration/assets/img/bankid.png">
        <span>Starta BankID på annan enhet</span>
    </a>
</div>

<input type="hidden" id="orderRef" value="<?php echo $tocen->orderRef; ?>" />
<input type="hidden" id="personal_number" value="<?php echo $personal_number; ?>" />
<input type="hidden" id="redirect_id" value="<?php echo $redirect_id; ?>" />
<input type="hidden" id="url_redirect" value="<?php echo $url_redirect; ?>" />
<div id="zignsec_text"></div>

<script >
    jQuery(document).ready(function($) {
        var myVar;
        $( '#mbid_start' ).click(function() {
            $('#zignsec_text').text('Försöker starta BankID-appen.');
            myVar = setTimeout(pollServerAPI, 0);
        });         
        $( '#mbid_sign' ).click(function() {
            $('#zignsec_text').html('<iframe class="zignsec" src="'+ $('#url_redirect').val() +'"></iframe>');
            myVar = setTimeout(pollServerAPIAnother, 0);
        });

        function pollServerAPI(){
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                data : {
                    action : 'getProgressStatus',
                    orderRef : $('#orderRef').val()
                },
                method : 'POST', 
                success : function( response ){ 

                    var json_response = JSON.parse(response);
                    console.log(json_response);
                    if(json_response.errors.length>0)
                    {       
                        console.log(json_response.errors);   
                    }
                    else if(json_response.progressStatus == 'COMPLETE')
                    {
                        clearTimeout(myVar);
                        console.log(json_response.LookupPersonAddress);
                        registerBankIdUser(json_response.LookupPersonAddress);          
                    }
                    else if(json_response.progressStatus == 'USER_SIGN')
                    {
                        myVar = setTimeout(pollServerAPI, 5000);
                        $('#zignsec_text').text('Skriv in din säkerhetskod i BankID-appen och välj Legitimera eller Skriv under.');                 
                    }   
                    else if(json_response.progressStatus == 'EXPIRED_TRANSACTION')
                    {
                        clearTimeout(myVar);
                        $('#zignsec_text').text('BankID-appen svarar inte. Kontrollera att den är startad och att du har internetanslutning.  Om du inte har något giltigt BankID kan du hämta ett hos din Bank. Försök sedan igen.');
                    }   
                    else if(json_response.progressStatus == 'CLIENT_ERR')
                    {
                        clearTimeout(myVar);
                        $('#zignsec_text').text('Internt tekniskt fel. Uppdatera BankID-appen och försök igen.');               
                    }                                                                   
                    else
                    {
                        myVar = setTimeout(pollServerAPI, 2000);
                    }               
                },
                error : function(error){
                    $('#zignsec_text').text('Söker efter BankID, det kan ta en liten stund… '); 
                    console.log(error);
                    myVar = setTimeout(pollServerAPI, 20000);
                }
            });     
        }

        function pollServerAPIAnother(){
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                data : {
                    action : 'getProgressStatusAnother',
                    redirect_id : $('#redirect_id').val()
                },
                method : 'POST', 
                success : function( response ){ 

                    var json_response = JSON.parse(response);
                    console.log(json_response.result.identity.state);
                    if(json_response.errors.length>0)
                    {       
                        console.log(json_response.errors);   
                    }
                    else if(json_response.result.identity.state == 'PENDING')
                    {
                        myVar = setTimeout(pollServerAPIAnother, 2000);
                    }
                    else if(json_response.result.identity.state == 'FINISHED')
                    {
                        console.log(json_response);
                        clearTimeout(myVar);
                        registerBankIdUser(json_response.LookupPersonAddress);
                    }   
                },
                error : function(error){
                    $('#zignsec_text').text('Söker efter BankID, det kan ta en liten stund… '); 
                    console.log(error);
                    myVar = setTimeout(pollServerAPI, 20000);
                }
            });     
        }

        function registerBankIdUser(user_info){               
            $.ajax({
                url : '/wp-admin/admin-ajax.php',
                data : {
                    action : 'registerBankIdUser',
                    user_info : user_info
                },
                method : 'POST', 
                success : function( response ){
                }
            });
        }
    });
</script>