            
    function mostrarDiv(div)
    {
        capa               = document.getElementById(div);
        capa.style.display = 'block';    
    }
    function ocultarDiv(div)
    {
        capa               = document.getElementById(div);
        capa.style.display = 'none';    
    }		 
    function mostrarWaitMsg()
    {
        Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere'); 
           
    }
            
    $('#formaPagoId').change(function()
    {   
	presentaDatosTarjeta();                                
    });

    function presentaDatosTarjeta()
    {               
        ocultarDiv('forma_pago');			
	$("#tipoCuentaId").removeAttr('required');
        ocultarDiv('tipo_de_cuenta');
        var info_formaPagoId = info_condicion_plan.formaPagoId.value;
        var formaPagoId      = info_formaPagoId.split("-");                
        if( formaPagoId[2] == 'DEB' )
        {			
            mostrarDiv('tipo_tarjeta_cuenta');                                                                                            										
        }
        else
        {
            ocultarDiv('tipo_tarjeta_cuenta');
                        limpiarDatosFormaPago();                     										
        }			               
    }
	    
    $("input:radio[@name='info']").change(function() 
    {
        ocultarDiv('tipo_de_cuenta'); 
        var tipo = $("input:radio[@name='info']:checked").val();
        $.ajax({
                type: "POST",
                data: "tipo=" + $("input:radio[@name='info']:checked").val(),                                
                url: url_listar_tarjetas_cuentas,
                    success: function(msg){
                        if ( msg.msg == 'ok' )
                        {        
                            document.getElementById("forma_pago_msg").innerHTML = "";
                            ocultarDiv('forma_pago_msg');			
                            
                            document.getElementById("tipoCuentaId").innerHTML = msg.div;    
                             mostrarDiv('forma_pago');			    
                             if( tipo == 'tarjeta' )
                             {
                                 $("#tipoCuentaId").attr('multiple','multiple');
                             }
                             else
                             {
                                 $("#tipoCuentaId").removeAttr('multiple'); 
                             }                            
                        }
                        else
                        {                           
                            document.getElementById("tipoCuentaId").innerHTML = msg.msg;
                            ocultarDiv('forma_pago');
                            document.getElementById("forma_pago_msg").innerHTML = msg.msg;
                            mostrarDiv('forma_pago_msg');			    
			    $("#tipoCuentaId").removeAttr('required');
                        }
                    }
            });
    });
    
    $('#tipoCuentaId').change(function()
    {	  
        var info_tipoCuentaId = info_condicion_plan.tipoCuentaId.value;
        var tipoCuentaId      = info_tipoCuentaId.split("-");
        obtieneBancos(tipoCuentaId[0]);
    });
			
    function obtieneBancos(tipoCuentaId)
    {               
        parametros="tipoCuenta=" + tipoCuentaId;
        $.ajax({
                    type: "POST",
                    data: parametros,
                    url:url_listar_bancos_asociados,
                    success: function(msg){
                        if (msg.msg == 'ok')
                        {   
                            document.getElementById("bancoTipoCuentaId").innerHTML=msg.div;
                            if(msg.es_banco == 'S'){
                               mostrarDiv('tipo_de_cuenta');  
                            }else{
                               ocultarDiv('tipo_de_cuenta'); 
                            }
                            
                        }
                        else
                            document.getElementById("bancoTipoCuentaId").innerHTML=msg.msg;
                    }
                });			
    }
			
			
    function limpiarDatosFormaPago()
    {
        $('#tipoCuentaId').val('');
        $('#bancoTipoCuentaId').val('');                                        
					
    }
			        
                 
    function agregar_detalle()
    {   
        var info_tipoNegocioId = info_condicion_plan.tipoNegocioId.value;
        var tipoNegocio        = info_tipoNegocioId.split("-");
        var info_formaPagoId   = info_condicion_plan.formaPagoId.value;
        var formaPago          = info_formaPagoId.split("-");
        var info_tipoCuentaId  = info_condicion_plan.tipoCuentaId.value;
        var tipoCuenta         = info_tipoCuentaId.split("-");           
        //banco
        var band_banco         = 0;           
        var imprime_b          = '';            
        var bancoTipoCuenta;
        var bandera_existe     = false;          
        var lista              = document.info_condicion_plan.bancoTipoCuentaId;           
	var opciones           = lista.options; 
        var contador           = 0;
	for ( i=0;i<opciones.length;i++ )
        {                  
	    if ( opciones[i].selected == true )
            {                                                
                var banco                                    = opciones[i].value;
                bancoTipoCuenta                              = banco.split("-")
	        band_banco                                   = 1;	                        
                informacion_controlador                      = {};
                informacion_controlador["idPlanCondic"]      = "";
                informacion_controlador["tipoNegocioId"]     = tipoNegocio[0];
                informacion_controlador["formaPagoId"]       = formaPago[0];
                informacion_controlador["tipoCuentaId"]      = tipoCuenta[0];
                informacion_controlador["bancoTipoCuentaId"] = bancoTipoCuenta[0];
                var pos                                      = buscarCondiciones(informacion,tipoNegocio[0],formaPago[0],tipoCuenta[0],bancoTipoCuenta[0]);  
                if( pos )
                {
                    bandera_existe = true;
                    if ( imprime_b == "" )
                    {	
                        imprime_b = bancoTipoCuenta[1]; 
                        contador  = 1;
                    }
		    else
                    {
                        imprime_b = imprime_b+','+bancoTipoCuenta[1]; 
                        contador  = contador+1;
                    }
                }
                else
                {
                    document.getElementById("div_valida_condicion").innerHTML = ""; 
                    displayResult(tipoNegocio[1],formaPago[1],tipoCuenta[1],bancoTipoCuenta[1]);
                    informacion.push(informacion_controlador);
                    document.getElementById("valores").value = JSON.stringify(informacion);
                 }  
	    }
	}
        if( bandera_existe )
        {
            if( contador == 1 )
            {
                    document.getElementById("div_valida_condicion").innerHTML = "Ya existe la condicion para el banco: " + imprime_b;
            }
            else
            {
                    document.getElementById("div_valida_condicion").innerHTML = "Ya existen las condiciones para los bancos: " + imprime_b;
            }
        }
                        
        // tarjeta           
        var band_tipocuenta = 0;
        var imprime_c       = '';
        bandera_existe      = false;
        if( band_banco == 0 )
        {
            var info_bancoTipoCuentaId = info_condicion_plan.bancoTipoCuentaId.value;
            var bancoTipoCuenta        = info_bancoTipoCuentaId.split("-");                                                    
            var lista                  = document.info_condicion_plan.tipoCuentaId;           
	    var opciones               = lista.options; 
            contador                   = 0;                                
	    for ( i=0;i<opciones.length;i++ ) 
            {             
	        if ( opciones[i].selected == true && opciones[i].value!='null' )
                {                                                
		    var tipocuenta                               = opciones[i].value;
                    tipoCuenta                                   = tipocuenta.split("-")
		    band_tipocuenta                              = 1;	                        
                    informacion_controlador                      = {};
                    informacion_controlador["idPlanCondic"]      = "";
                    informacion_controlador["tipoNegocioId"]     = tipoNegocio[0];
                    informacion_controlador["formaPagoId"]       = formaPago[0];
                    informacion_controlador["tipoCuentaId"]      = tipoCuenta[0];
                    informacion_controlador["bancoTipoCuentaId"] = 'null';
                    bancoTipoCuenta[0]                           = "null";
                    bancoTipoCuenta[1]                           = "";
                    var pos                                      = buscarCondiciones(informacion,tipoNegocio[0],formaPago[0],tipoCuenta[0],bancoTipoCuenta[0]);  
                    if( pos )
                    {
                        bandera_existe = true;
                        if ( imprime_c == "" )
                        {	
                            imprime_c = tipoCuenta[1];  
                            contador  = 1;
                        }
			else
                        {
                            imprime_c = imprime_c+','+tipoCuenta[1]; 
                            contador  = contador+1;}
                        }
                        else
                        {
                               document.getElementById("div_valida_condicion").innerHTML = ""; 
                               displayResult(tipoNegocio[1],formaPago[1],tipoCuenta[1],bancoTipoCuenta[1]);
                               informacion.push(informacion_controlador);
                               document.getElementById("valores").value = JSON.stringify(informacion);
                        }  
		}
            }
            if( bandera_existe )
            {
                 if( contador == 1 )
                 {
                     document.getElementById("div_valida_condicion").innerHTML = "Ya existe la condicion para la tarjeta: " + imprime_c;
                 }
                 else
                 {
                     document.getElementById("div_valida_condicion").innerHTML = "Ya existen las condiciones para las tarjetas: " + imprime_c;
                 }
            }
                 
        }  
        var imprime_d = '';  
        if( band_banco == 0 && band_tipocuenta == 0 )
        {                
            var info_bancoTipoCuentaId = info_condicion_plan.bancoTipoCuentaId.value;
            var bancoTipoCuenta        = info_bancoTipoCuentaId.split("-");              
            if( formaPago[0] == "null" )
            {
                formaPago[1] = "";
            }
            if( tipoCuenta[0] == "null" ) 
            {
                tipoCuenta[1] = "";
            }                           
            informacion_controlador                      = {};
            informacion_controlador["idPlanCondic"]      = "";
            informacion_controlador["tipoNegocioId"]     = tipoNegocio[0];
            informacion_controlador["formaPagoId"]       = formaPago[0];
            informacion_controlador["tipoCuentaId"]      = tipoCuenta[0];
            informacion_controlador["bancoTipoCuentaId"] = 'null';
            bancoTipoCuenta[0]                           = "null";
            bancoTipoCuenta[1]                           = "";
               
            var pos = buscarCondiciones(informacion,tipoNegocio[0],formaPago[0],tipoCuenta[0],bancoTipoCuenta[0]);  
            if( pos )
            {
               imprime_d = tipoNegocio[1];                    
               if( formaPago[1] )
               {
                   imprime_d = imprime_d + ' - '+ formaPago[1];
               }
               document.getElementById("div_valida_condicion").innerHTML="Ya existe ingresada la condicion para el plan : "+ imprime_d;
            }
            else
            {
                document.getElementById("div_valida_condicion").innerHTML=""; 
                displayResult(tipoNegocio[1],formaPago[1],tipoCuenta[1],bancoTipoCuenta[1]);
                informacion.push(informacion_controlador);
                document.getElementById("valores").value=JSON.stringify(informacion);
            }   
        }               
        limpiar_detalle();
    }
        
    function limpiar_detalle()
    {    
        $('#tipoNegocioId').val('');  
        $('#formaPagoId').val(''); 
        $('#tipoCuentaId').val('');
	    $('#bancoTipoCuentaId').val('');
           
        ocultarDiv('forma_pago');
        ocultarDiv('tipo_de_cuenta');
        ocultarDiv('tipo_tarjeta_cuenta');  
        uncheckRadio(this.info_condicion_plan.info,0);
        uncheckRadio(this.info_condicion_plan.info,1);
        document.getElementById("forma_pago_msg").innerHTML = "";
        ocultarDiv('forma_pago_msg');	
    }
    function uncheckRadio(rbutton,i)
    {
       if( rbutton[i].checked == true )
       {
           rbutton[i].checked = false;
       }
    }
    function displayResult(tipoNegocio,formaPago,tipoCuenta,bancoTipoCuenta)
    {
        var table       = document.getElementById("table-3");
        var largo       = table.rows.length;
        var row         = table.insertRow(largo);
        var cell1       = row.insertCell(0);
        var cell2       = row.insertCell(1);
        var cell3       = row.insertCell(2);
        var cell4       = row.insertCell(3);     
        var cell5       = row.insertCell(4);     
        cell1.innerHTML = tipoNegocio;
        cell2.innerHTML = formaPago;
        cell3.innerHTML = tipoCuenta;                
        cell4.innerHTML = bancoTipoCuenta;  
        cell5.innerHTML = "<button type='button' onclick='removeRow(this);' class='button-crud'>Eliminar</button>";                               
    }
            
    function buscarCondiciones(informacion,tipoNegocio,formaPago,tipoCuenta,bancoTipoCuenta)
    {                
        for( var i=0;i<informacion.length;i++ )
        {                               
            if(String(informacion[i].tipoNegocioId)     == String(tipoNegocio) && 
               String(informacion[i].formaPagoId)       == String(formaPago) && 
               String(informacion[i].tipoCuentaId)      == String(tipoCuenta) &&
               String(informacion[i].bancoTipoCuentaId) == String(bancoTipoCuenta))
            {                                
               return true;
            }
        }
        return false;
    }
    function removeRow(src)
    {             
        var oRow = src.parentElement.parentElement;    
        var i    = oRow.rowIndex;
        document.getElementById("table-3").deleteRow(i);  
        informacion.splice(i-1,1);                        
        document.getElementById("valores").value = JSON.stringify(informacion);            
    }
