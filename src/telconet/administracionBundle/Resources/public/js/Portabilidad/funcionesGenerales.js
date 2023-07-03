 

Ext.onReady(function() { 
      
});



function validaCorreo(numCorreoAgregado)
{ 
    let correo = $('#clientetype_correo_electronico_'+numCorreoAgregado).val();
   
   
    $.ajax({
        type: "POST",
        data: { 
            correo: correo
        },
        url: url_valida_correo,
        beforeSend: function() 
        {
            $('#img-valida-correo-'+numCorreoAgregado).attr("src", url_img_loader);
        },
        
        success: function(response) 
        {   
            
            var obj = JSON.parse(response);
            
            var strMsjValidacion = obj.strMsjValidacion; 
              
            if (strMsjValidacion === 'OK') 
            {   
                $('#img-valida-correo-'+numCorreoAgregado).attr("title", "correo valido");
                $('#img-valida-correo-'+numCorreoAgregado).attr("src", url_img_check); 
                ocultarDiv('divcorreo_0');
                $("#divcorreo_0").html(); 
                              
            }else 
            {      
                mostrarDiv('divcorreo_0');
                $("#divcorreo_0").html(obj.strMsjObservacion); 
                $('#img-valida-correo-'+numCorreoAgregado).attr("title", "correo invalido");
                $('#img-valida-correo-'+numCorreoAgregado).attr("src", url_img_delete);
                $('#clientetype_correo_electronico_'+numCorreoAgregado).val('');
            }   
        }
    });
}

function validaCamposVacios(numCorreoAgregado)
{   
    var intNoExisteSeleccionados = 0;
    var strTipo_identificacion = '';
    var strIdentificacion = '';
    var strCorreo = '';
    var intCorreoLleno = 1;
    //Valida Datos Formulario
    strTipo_identificacion = $('#clientetype_tipoIdentificacion').val();
    strIdentificacion = $('#clientetype_identificacionCliente').val();

    for (let i = 0; i < numCorreoAgregado; i++) {
        strCorreo = $('#clientetype_correo_electronico_'+i).val();
        
        if(strCorreo.length > 1)
        {   
            intCorreoLleno ++;
        }
    }
    

    if(strTipo_identificacion === 'Seleccione...' && strIdentificacion.length === 0
    && strCorreo.length === 0 )
    {   
      Ext.Msg.alert('Error','Ingresar los datos solicitados');
      
      return false;
        
    }else if(strIdentificacion.length === 0)
    {
      Ext.Msg.alert('Error','Ingresar cedula solicitada');
     
      return false;
    }else if(intCorreoLleno === numCorreoAgregado)
    {
      Ext.Msg.alert('Error','Ingresar correo solicitado');
     
      return false;
    }
    if (strOpcion  === 'Portabilidad')
    {
        return true;
        
    }else
    {
        let storeAdmiPoliticaClausula = Ext.StoreMgr.lookup('idStoreAdminPoliticas_Clausulas');
        var countDataGrid = storeAdmiPoliticaClausula.getCount();

        if (countDataGrid > 0){
        for (var i = 0; i < countDataGrid; i++)
        { 
        var strValorRespuestaCmb = "valorRespuestaCmb-"+i;
        var strcmbValorRespAct = document.getElementById(strValorRespuestaCmb);
        var strValorRespAct = strcmbValorRespAct.options[strcmbValorRespAct.selectedIndex].text;
        
        if (strValorRespAct.length === 0)
        {
            intNoExisteSeleccionados ++;
           
        }
        }
        if(intNoExisteSeleccionados === countDataGrid )
        {   
            Ext.Msg.alert('Error','Selecionar al menos una respuesta de una politica o clausulas');
            
            return false;
            
        }
        }
    }

   
    return true;  
}

function obtenerCorreos(numCorreoAgregado)
{
    var strListaCorreos = '';
    var strCorreo = '';
    for (let i = 0; i < numCorreoAgregado; i++) {
        strCorreo = $('#clientetype_correo_electronico_'+i).val();
        
        if(strCorreo.length > 0)
        {   
            strListaCorreos += strCorreo+',';
        }
    }
    strListaCorreos = strListaCorreos.substring(0, strListaCorreos.length - 1);
    return strListaCorreos;
}

function limpiaCampos(numCorreoAgregado)
{    

    $('#clientetype_identificacionCliente').val('');
    $('#clientetype_tipoIdentificacion').val('Seleccione...');
    $('#clientetype_rol').val('');
    for (let i = 0; i < numCorreoAgregado; i++) {
        $('#clientetype_correo_electronico_'+i).val(''); 
    }
        location.reload();
}

function limpiaCamposCambiaTipoIden(numCorreoAgregado)
{    

    $('#clientetype_identificacionCliente').val('');
    for (let i = 0; i < numCorreoAgregado; i++) {
        $('#clientetype_correo_electronico_'+i).val(''); 
    }
}

