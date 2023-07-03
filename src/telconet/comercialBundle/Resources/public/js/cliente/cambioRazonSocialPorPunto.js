/******************************************************************
 * FUNCIONES USADAS PARA EL INGRESO DE LA INFORMACION DEL CLIENTE
 * (DATOS PRINCIPALES)
 * HACIA EL CUAL SE REALIZARA EL CAMBIO DE RAZON SOCIAL
 ******************************************************************/
Ext.require([
    '*'
]);

flagIdentificacionCorrecta = 1;
formname                   = "clientetype";
ocultarImputRepresentanteTN() ; 

if(prefijoEmpresa === 'TN')
{
    $('#clientetype_esPrepago').attr('disabled','disabled'); 
    $('#clientetype_esPrepago').val('S'); 
    $('#clientetype_pagaIva').val('S'); 
}
 function tieneCarnetConadis()
{
    if ($('#clientetype_tieneCarnetConadis').val() == 'S')
    {

        document.getElementById("clientetype_numeroConadis").required = true;
        $('#clientetype_numeroConadis').show();
        $('label[for=clientetype_numeroConadis]').show();
        $('#clientetype_numeroConadis').attr('maxlength', '15');
    }
    else
    {
        $('#clientetype_tieneCarnetConadis').val('N');
        $('#clientetype_numeroConadis').val('');
        $('#clientetype_numeroConadis').hide();
        $('label[for=clientetype_numeroConadis]').hide();
        $('#clientetype_numeroConadis').removeAttr('maxlength');
        $('#clientetype_numeroConadis').removeAttr('required');
        document.getElementById("clientetype_numeroConadis").required = false;

    }
}
function esEmpresa()
{
    $('#clientetype_fechaNacimiento_month').removeAttr('required');
    $('#clientetype_fechaNacimiento_day').removeAttr('required');
    $('#clientetype_fechaNacimiento_year').removeAttr('required');

    if ($('#clientetype_tipoEmpresa').val() == 'Publica' || $('#clientetype_tipoEmpresa').val() == 'Privada')
    {
        ocultarDiv('div_nombres');
        mostrarDiv('div_razon_social');
        $('#clientetype_razonSocial').attr('required', 'required');
        $('#clientetype_representanteLegal').attr('required', 'required');
        $('label[for=clientetype_representanteLegal]').html('* Representante Legal:');
        $('label[for=clientetype_representanteLegal]').addClass('campo-obligatorio');
        $('#clientetype_nombres').removeAttr('required');
        $('#clientetype_apellidos').removeAttr('required');
        $('#clientetype_genero').removeAttr('required');
        $('#clientetype_estadoCivil').removeAttr('required');
        $('#clientetype_fechaNacimiento').removeAttr('required');
        $('#clientetype_tituloId').removeAttr('required');
        $('#clientetype_origenIngresos').removeAttr('required');
    }
    else
    {
        mostrarDiv('div_nombres');
        ocultarDiv('div_razon_social');
        $('#clientetype_razonSocial').removeAttr('required');
        $('label[for=clientetype_representanteLegal]').removeClass('campo-obligatorio');
        $('label[for=clientetype_representanteLegal]').html('Representante Legal:');
        $('#clientetype_representanteLegal').removeAttr('required');
        $('#clientetype_nombres').attr('required', 'required');
        $('#clientetype_apellidos').attr('required', 'required');
        $('#clientetype_genero').attr('required', 'required');
        $('#clientetype_estadoCivil').attr('required', 'required');
        $('#clientetype_fechaNacimiento').attr('required', 'required');
        $('#clientetype_tituloId').attr('required', 'required');
        $('#clientetype_origenIngresos').attr('required', 'required');
    }
    representanteLegalShow(); 
}
 
/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.1 27-09-2017 Se llama al método que ya contiene la validación respectiva.
 * @since 1.0
 **/
function esRuc()
{    
    var intMaxLongitudIdentificacion = 0;
    var strTipoIdentificacion        = $('#clientetype_tipoIdentificacion').val();
    
    Ext.Ajax.request({
        url: url_getMaxLongitudIdentificacionAjax,
        method: 'POST',
        timeout: 99999,
        async: false,
        params: { strTipoIdentificacion : strTipoIdentificacion },
        success: function(response){

          var objRespuesta = Ext.JSON.decode(response.responseText);

          if(objRespuesta.intMaxLongitudIdentificacion > 0)
          {
              intMaxLongitudIdentificacion = objRespuesta.intMaxLongitudIdentificacion;
          }
          
          $('#clientetype_identificacionCliente').removeAttr('maxlength');
          $('#clientetype_identificacionCliente').attr('maxlength', intMaxLongitudIdentificacion);
          $('#clientetype_identificacionCliente').val('');
          representanteLegalShow(); 
        },
        failure: function(response)
        {
            Ext.Msg.alert('Error ','Error: ' + response.statusText);
        }
    });
}
                
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

function MostrarTabsContratoArchivosDig()
{
    var tabPanel = Ext.getCmp('myTabsCrs');
    tabPanel.child('#idTabDatosContratoCrs').tab.show();
    tabPanel.child('#idTabSubirAchivosCrs').tab.show();
    tabPanel.child('#idTabAdicionalCrs').tab.show();
    $('.divContrato').show();
    $('.divAdendum').hide();
    document.getElementById("procesoContrato").setAttribute("value", 'Contrato');
}

$('#CambioPago').change(function() {
    if(this.checked) {
        $("#infocontratotype_formaPagoId").removeAttr("disabled");  
        $("#infocontratoformapagotype_tipoCuentaId").removeAttr("disabled");                                                                              
        $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("disabled");
        $('#infocontratoformapagotype_anioVencimiento').removeAttr("disabled");
        $('#infocontratoformapagotype_mesVencimiento').removeAttr("disabled");
        $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr('readonly');
        $('#infocontratoformapagotype_titularCuenta').removeAttr('readonly');
        $('#infocontratoformapagotype_codigoVerificacion').removeAttr('readonly');	 
    }
    else
    {
        $("#infocontratotype_formaPagoId").attr("disabled","disabled");  
        $("#infocontratoformapagotype_tipoCuentaId").attr("disabled","disabled");                                                                              
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("disabled","disabled");
        $("#infocontratoformapagotype_anioVencimiento").attr("disabled","disabled");  
        $("#infocontratoformapagotype_mesVencimiento").attr("disabled","disabled"); 
        $('#infocontratoformapagotype_numeroCtaTarjeta').attr('readonly', 'readonly');
        $('#infocontratoformapagotype_titularCuenta').attr('readonly', 'readonly');
    }       
});

function MostrarTabsAdendum()
{
    var tabPanel = Ext.getCmp('myTabsCrs');
    tabPanel.child('#idTabDatosContratoCrs').title = 'Adendum';
    tabPanel.child('#idTabDatosContratoCrs').tab.show();
    tabPanel.child('#idTabSubirAchivosCrs').tab.show();
    tabPanel.child('#idTabAdicionalCrs').tab.hide();
    $('.divContrato').hide();
    $('.divAdendum').show();
    $('#CambioPago').hide();
    $('#divFormaContrato').hide();
    $('#infodocumentotype_imagenes_0').removeAttr('disabled');
    $('#infodocumentotype_tipos_0').removeAttr('disabled');
    document.getElementById("procesoContrato").setAttribute("value", 'Adendum');
}

function validaIdentificacion(isValidarIdentificacionTipo)
{    
    currenIdentificacion = $(input).val();
    yaTieneElRol         = false;
    esPreCliente         = false;
    $('#errormessage').addClass('campo-oculto').html("");
    $.ajax({
        type: "POST",
        data: "identificacion=" + currenIdentificacion,
        url: url_valida_identificacion,
        beforeSend: function()
        {
            $('#img-valida-identificacion').attr("src", url_img_loader);
        },
        success: function(msg)
        {
            if (msg != '')
            {   // Si no encontro registro de la Persona, se habilita para el ingreso de toda la informacion      
                var tipoIdentificacion = $("#" + formname + "_tipoIdentificacion").val();
                var tipoEmpresa = $("#" + formname + "_tipoEmpresa").val();
                var tipoTributario= $("#" + formname + "_tipoTributario").val();


                if (msg == "no")
                {
                    flagIdentificacionCorrecta = 1;
                    $('#img-valida-identificacion').attr("title", "Identificacion disponible");
                    $('#img-valida-identificacion').attr("src", url_img_check);                   
                    storeFormas.removeAll();
                    $("#" + formname + "_yaexiste").val('N');
                    ocultarDiv('divroles');
                    habilitaCampos();
                    gridFormasContacto.setDisabled(false);
                    limpiaCampos(); 
                    if (!Ext.isEmpty(Ext.String.trim(currenIdentificacion)))
                    {
                        MostrarTabsContratoArchivosDig();
                    }

                    $("#" + formname + "_tipoIdentificacion").val(tipoIdentificacion);
                    $("#" + formname + "_tipoEmpresa").val(tipoEmpresa);
                    $("#" + formname + "_tipoTributario").val( tipoTributario);
                    esEmpresa(); 

                    $('#forma_pago').addClass("campo-oculto");
                    $('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
                    $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
                    $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
                    $('#infocontratoformapagotype_titularCuenta').removeAttr("required"); 
                    $("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
                    $("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
                    $("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
                }
                else
                {
                    $(input).focus();
                    var obj              = JSON.parse(msg);
                    var antiguoIdCliente = $("#" + formname + "_antiguoIdCliente").val();
                    // Verifico que no se realice cambio de Razon social hacia el mismo cliente
                    if (antiguoIdCliente == obj[0].id)
                    {
                        activeTab('idTabContactoClienteCrs');
                        alertModal("Error en el Numero de Identificacion, No puede realizar Cambio de Razon Social hacia el mismo Cliente");
                   
                        limpiaCampos();
                        storeFormas.removeAll();
                        gridFormasContacto.setDisabled(true);
                        $("#" + formname + "_yaexiste").val('N');
                        deshabilitaCampos();
                        habilitaFormaPago();                                               
                        $("#" + formname + "_tipoIdentificacion").val(tipoIdentificacion);
                        $('#' + formname + '_tipoIdentificacion').removeAttr('disabled');
                        $('#' + formname + '_direccionTributaria').attr('readonly', 'readonly');

                        $("#" + formname + "_tipoIdentificacion").val(tipoIdentificacion);
                        $("#" + formname + "_tipoEmpresa").val(tipoEmpresa);
                        $("#" + formname + "_tipoTributario").val( tipoTributario);
                        esEmpresa(); 
                    }
                    else
                    {
                        flagIdentificacionCorrecta = 0;
                        $('#img-valida-identificacion').attr("title", "identificacion ya existe");
                        $('#img-valida-identificacion').attr("src", url_img_delete);
                        // Si encontro registro de la Persona, muestro Informacion de la Persona y la precargo en pantalla independiente 
                        // del Rol que posea
                        limpiaCampos();
                        $("#" + formname + "_nombres").val(obj[0].nombres);
                        $("#" + formname + "_apellidos").val(obj[0].apellidos);
                        $("#" + formname + "_razonSocial").val(obj[0].razonSocial);
                        $("#" + formname + "_tituloId").val(obj[0].tituloId);
                        $("#" + formname + "_tipoTributario").val(obj[0].tipoTributario);
                        $("#" + formname + "_tipoIdentificacion").val(obj[0].tipoIdentificacion);
                        $("#" + formname + "_tipoEmpresa").val(obj[0].tipoEmpresa);
                        $("#" + formname + "_tipoTributario").val(obj[0].tipoTributario);
                        $("#" + formname + "_representanteLegal").val(obj[0].representanteLegal);
                        $("#" + formname + "_nacionalidad").val(obj[0].nacionalidad);
                        $("#" + formname + "_genero").val(obj[0].genero);
                        $("#" + formname + "_direccionTributaria").val(obj[0].direccionTributaria);
                        $("#" + formname + "_estadoCivil").val(obj[0].estadoCivil);
                        $("#" + formname + "_origenIngresos").val(obj[0].origenIngresos);
                        $("#" + formname + "_id").val(obj[0].id);
                        var fechaNac1      = obj[0].fechaNacimiento;
                        arrFechaNacimiento = fechaNac1.split(' ');
                        var fechaNac2      = arrFechaNacimiento[0];
                        arrFechaN          = fechaNac2.split('/');
                        $("#" + formname + "_fechaNacimiento_day").val(arrFechaN[0] * 1);
                        $("#" + formname + "_fechaNacimiento_month").val(arrFechaN[1] * 1);
                        $("#" + formname + "_fechaNacimiento_year").val(arrFechaN[2]);
                        
                        //Se obtiene los Campos Nuevos a nivel de persona
                        $("#" + formname + "_origenIngresos").val(obj[0].origenIngresos);
                        $("#" + formname + "_numeroConadis").val(obj[0].numeroConadis);                            
                        $("#" + formname + "_contribuyenteEspecial").val(obj[0].contribuyenteEspecial);
                        $("#" + formname + "_pagaIva").val(obj[0].pagaIva);
                            
                        if ($("#" + formname + "_tieneCarnetConadis").val()=='S')
                        {   
                            $("#" + formname + "_numeroConadis").attr('required','required');
                            $("#" + formname + "_numeroConadis").show();
                            $('label[for=clientetype_numeroConadis]').show();
                        }
                        else if ($("#" + formname + "_tieneCarnetConadis").val()=='N')
                        {   
                            $("#" + formname + "_numeroConadis").hide();
                            $('label[for=clientetype_numeroConadis]').hide();
                         }     
                         //Se obtiene datos de la PersonaEmpresaRol                       
                         var objPersonaEmpresaRol   = obj[0].datosPersonaEmpresaRol;
                         var v_idOficinaFacturacion = '';
                         var v_esPrepago = '';
                         for (var i = 0; i < objPersonaEmpresaRol.length; i++)
                         {
                             if(objPersonaEmpresaRol[0].rol=='Cliente') 
                             {
                                 v_idOficinaFacturacion = objPersonaEmpresaRol[0].idOficinaFacturacion;
                                 v_esPrepago            = objPersonaEmpresaRol[0].esPrepago;
                             }
                         }
                         $("#" + formname + "_idOficinaFacturacion").val(v_idOficinaFacturacion);
                         $("#" + formname + "_esPrepago").val(v_esPrepago);
                                                
                         esEmpresa();

                        // Si encontro registro de la Persona, verifico que Roles posee                    
                        // obtiene roles de la persona
                        var roles         = obj[0].roles;
                        var presentaroles = '';
                        arr_roles         = roles.split("|");
                        for (var i = 0; i < arr_roles.length; i++)
                        {
                            if (rol == arr_roles[i]) 
                            {
                                yaTieneElRol = true; // Bandera posee Rol de Cliente
                            }
                            if (rolPre == arr_roles[i])
                            {
                                esPreCliente = true; // Bandera para marcar que es Pre-cliente
                            }
                            if (i == (arr_roles.length - 1) && arr_roles[i])
                                presentaroles = arr_roles[i];
                            else
                            {
                                if (arr_roles[i])
                                    presentaroles = presentaroles + arr_roles[i] + ", ";
                            }
                        }
                        if (presentaroles)
                        {
                            $("#divroles").html("La persona ya tiene los siguientes roles en el sistema: " + presentaroles);
                            mostrarDiv('divroles');
                        }

                        // Si la Persona posee registro con Rol de "Cliente" entonces Busco Contrato
                        if (yaTieneElRol)
                        {
               
                            // Busco Contrato                                                                       
                            $.ajax({
                                type: "POST",
                                data: "identificacion=" + currenIdentificacion,
                                url: url_valida_contrato_activo,
                                success: function(msg1) {
                                    if (msg1 == 'no')
                                    {
                                        alertModal("No existe contrato Activo para el cliente, los puntos no podran ser trasladados a este cliente");
                                        var tipoIdentificacion = $("#" + formname + "_tipoIdentificacion").val();
                                        limpiaCampos();
                                        storeFormas.removeAll();
                                        gridFormasContacto.setDisabled(true);
                                        $("#" + formname + "_yaexiste").val('N');
                                        deshabilitaCampos();
                                        habilitaFormaPago();                                        
                                        $("#" + formname + "_tipoIdentificacion").val(tipoIdentificacion);
                                        $('#' + formname + '_direccionTributaria').attr('readonly', 'readonly');
                                    }
                                    else
                                    {
                                        var objContrato = JSON.parse(msg1);
                                        if (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN')
                                        {
                                            MostrarTabsAdendum();
                                            //Cargo informacion del Contrato    
                                            tarjetaCompleta = objContrato[0].numeroCtaTarjeta;
                                            var tarjetaOculta   = "xxxxxxxxxx" + tarjetaCompleta.slice(-3);
                                            
                                            $('#infocontratoextratype_tipoContratoId').val(objContrato[0].tipoContratoId);
                                            $('#infocontratotype_formaPagoId').val(objContrato[0].formaPagoId);
                                            $('#infocontratotype_numeroContratoEmpPub').val(objContrato[0].numeroContratoEmpPub);
                                            $('#infocontratoformapagotype_tipoCuentaId').val(objContrato[0].tipoCuentaId);
                                            $('#infocontratoformapagotype_bancoTipoCuentaId').val(objContrato[0].bancoTipoCuentaId);
                                            $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaOculta);
                                            $('#infocontratoformapagotype_titularCuenta').val(objContrato[0].titularCuenta);
                                            $('#infocontratoformapagotype_mesVencimiento').val(objContrato[0].mesVencimiento);
                                            $('#infocontratoformapagotype_anioVencimiento').val(objContrato[0].anioVencimiento);
                                            $('#infocontratoformapagotype_codigoVerificacion').val(objContrato[0].codigoVerificacion);
                                            if (objContrato[0].formaPagoId == 3)
                                            {
                                                $('#tarjeta').removeClass("campo-oculto");
                                                $('#infocontratoformapagotype_mesVencimiento').removeAttr("readonly");
                                                $("#infocontratoformapagotype_mesVencimiento").attr("enabled", "enabled");
                                                $('#infocontratoformapagotype_mesVencimiento').removeAttr('disabled');
                                                $('#infocontratoformapagotype_anioVencimiento').removeAttr("readonly");
                                                $("#infocontratoformapagotype_anioVencimiento").attr("enabled", "enabled");
                                                $('#infocontratoformapagotype_anioVencimiento').removeAttr('disabled');
                                            }
                                            habilitaFormaPago();
                                        }
                                        activeTab('idTabDatosPrincipalesCrs');
                                        alertModal("Identificacion ya existente con contrato Activo, los puntos seran trasladados a este cliente.");
                                        storeFormas.removeAll();
                                        storeFormas.load({params: {personaid: obj[0].id}});
                                        $("#" + formname + "_yaexiste").val('S');
                                        deshabilitaCampos();
                                        gridFormasContacto.setDisabled(true);
                                    }
                                }
                            });
                        }// fin if (yaTieneElRol)
                        // Si la Persona no posee Rol de "Cliente" verifico si posee rol de Pre-Cliente o Empleado u otro Rol
                        else
                        {
                            activeTab('idTabDatosPrincipalesCrs');
                             // Si se tiene registro de la persona y posee Rol de "Pre-cliente", no debo permitir realizar 
                            // "Cambio de Razon Social por Punto" , deberian proceder a terminar el flujo hasta que sea "Cliente" o Anular el
                            // Pre-cliente para que ingrese como cliente nuevo de ser el caso.                    
                            if (esPreCliente)
                            {
                                alertModal("Identificacion ya existente posee rol de Pre-cliente, No puede realizar Cambio de Razon Social por Punto");
                                var tipoIdentificacion = $("#" + formname + "_tipoIdentificacion").val();
                                limpiaCampos();
                                storeFormas.removeAll();
                                gridFormasContacto.setDisabled(true);
                                $("#" + formname + "_yaexiste").val('N');
                                deshabilitaCampos();
                                habilitaFormaPago();   
                                $("#" + formname + "_tipoIdentificacion").val(tipoIdentificacion);
                                $('#' + formname + '_tipoIdentificacion').removeAttr('disabled');
                                $('#' + formname + '_direccionTributaria').attr('readonly', 'readonly');
                                $("#" + formname + "_tipoEmpresa").val(tipoEmpresa);
                                $("#" + formname + "_tipoTributario").val( tipoTributario);
                                esEmpresa(); 
                            }
                            else
                            {
                                // Si se tiene registro de la persona pero no posee Rol de "Cliente" ni de "Pre-cliente".
                                alertModal("Identificacion ya existente no posee contrato, Debe completar datos del Contrato y subir Archivo Digital");
                                var tipoIdentificacion = $("#" + formname + "_tipoIdentificacion").val();
                                $("#" + formname + "_tipoIdentificacion").val(tipoIdentificacion);
                                $("#" + formname + "_tipoEmpresa").val(tipoEmpresa);
                                $("#" + formname + "_tipoTributario").val( tipoTributario);
                                esEmpresa(); 
                                storeFormas.removeAll();
                                storeFormas.load({params: {personaid: obj[0].id}});
                                $("#" + formname + "_yaexiste").val('N');
                                habilitaCampos();
                                habilitaFormaPago();
                                MostrarTabsContratoArchivosDig();
                                gridFormasContacto.setDisabled(false);
                            }

                       
                            
                        }// fin else if (yaTieneElRol)
                    }// fin else if (antiguoIdCliente == obj[0].id)
                }// fin else if (msg == "no")

                representanteLegalRefresh(); 
            }// fin if (msg != '')
            else
            {   activeTab('idTabDatosPrincipalesCrs');
                alertModal("Error: No se pudo validar la identificacion ingresada.");
            }
            if (isValidarIdentificacionTipo && typeof validarIdentificacionTipo == typeof Function)
            {
                validarIdentificacionTipo();
            }
        }
    });
}
 
function deshabilitaCampos()
{
    if ($('#' + formname + '_direccionTributaria').val() != '')
    {
        $('#' + formname + '_direccionTributaria').attr('readonly', 'readonly');
    }

    $('#' + formname + '_nombres').attr('readonly', 'readonly');
    $('#' + formname + '_apellidos').attr('readonly', 'readonly');
    $('#' + formname + '_tipoEmpresa').attr('disabled', 'disabled');
    $('#' + formname + '_razonSocial').attr('readonly', 'readonly');
    $('#' + formname + '_representanteLegal').attr('disabled', 'disabled');

    $('#' + formname + '_tipoIdentificacion').attr('disabled', 'disabled');

    if ($('#' + formname + '_tipoTributario').val() != '')
    {
        $('#' + formname + '_tipoTributario').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_nacionalidad').val() != '')
    {
        $('#' + formname + '_nacionalidad').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_representanteLegal').val() != '')
    {
        $('#' + formname + '_representanteLegal').attr('readonly', 'readonly');
    }
    if ($('#' + formname + '_genero').val() != '')
    {
        $('#' + formname + '_genero').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_tituloId').val() != '')
    {
        $('#' + formname + '_tituloId').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_estadoCivil').val() != '')
    {
        $('#' + formname + '_estadoCivil').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_fechaNacimiento_day').val() != '')
    {
        $('#' + formname + '_fechaNacimiento_day').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_fechaNacimiento_month').val() != '')
    {
        $('#' + formname + '_fechaNacimiento_month').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_fechaNacimiento_year').val() != '')
    {
        $('#' + formname + '_fechaNacimiento_year').attr('disabled', 'disabled');
    } 
    //Campos Nuevos a nivel de Persona y PersonaEmpresaRol
    if($('#'+formname+'_origenIngresos').val()!='')					
	{
        $('#'+formname+'_origenIngresos').attr('disabled','disabled');
    }
    if($('#'+formname+'_tieneCarnetConadis').val()!='')					
	{
        $('#'+formname+'_tieneCarnetConadis').attr('disabled','disabled');
    }
    if($('#'+formname+'_numeroConadis').val()!='')					
	{
        $('#'+formname+'_numeroConadis').attr('readonly','readonly');
    }
    if($('#'+formname+'_contribuyenteEspecial').val()!='')					
	{
        $('#'+formname+'_contribuyenteEspecial').attr('disabled','disabled');
    }
    if($('#'+formname+'_pagaIva').val()!='')					
	{
        $('#'+formname+'_pagaIva').attr('disabled','disabled');
    }
    if($('#'+formname+'_idOficinaFacturacion').val()!='')					
	{
        $('#'+formname+'_idOficinaFacturacion').attr('disabled','disabled');
    }
    
    //Contrato
    $('#infocontratoextratype_tipoContratoId').attr('disabled', 'disabled');
    $('#infocontratotype_formaPagoId').attr('disabled', 'disabled');
    $('#infocontratotype_numeroContratoEmpPub').attr('readonly', 'readonly');
    $('#infocontratotype_valorAnticipo').attr('readonly', 'readonly');
    $('#infocontratoformapagotype_tipoCuentaId').attr('disabled', 'disabled');
    $('#infocontratoformapagotype_bancoTipoCuentaId').attr('disabled', 'disabled');    
    $('#infocontratoformapagotype_numeroCtaTarjeta').attr('readonly', 'readonly');
    $('#infocontratoformapagotype_titularCuenta').attr('readonly', 'readonly');
    $('#infocontratoformapagotype_mesVencimiento').attr('disabled', 'disabled');
    $('#infocontratoformapagotype_anioVencimiento').attr('disabled', 'disabled');
}

function limpiaCampos() 
{
   
    document.getElementById("datosRepresentanteLegal").setAttribute("value", "");
    $('#telefonoCliente').val('');
    $('#telefonoCliente option').remove();
    $('#telefonoCliente ').append("<option selected disabled>Seleccione...</option>");
    $('#' + formname + '_direccionTributaria').val('');
    $('#' + formname + '_nombres').val('');
    $('#' + formname + '_apellidos').val('');
    $('#' + formname + '_tipoEmpresa').val('');
    $('#' + formname + '_razonSocial').val('');
    $('#' + formname + '_tipoIdentificacion').val('');
    $('#' + formname + '_tipoTributario').val('');
    $('#' + formname + '_nacionalidad').val('');
    $('#' + formname + '_representanteLegal').val('');
    $('#' + formname + '_genero').val('');
    $('#' + formname + '_tituloId').val('');
    $('#' + formname + '_estadoCivil').val('');
    $('#' + formname + '_fechaNacimiento_day').val('');
    $('#' + formname + '_fechaNacimiento_month').val('');
    $('#' + formname + '_fechaNacimiento_year').val('');
    $("#" + formname + "_referido").val('');
    esEmpresa();
    //Contrato
    $('#infocontratoextratype_tipoContratoId').val('');
    $('#infocontratotype_formaPagoId').val('');
    $('#infocontratotype_numeroContratoEmpPub').val('');
    $('#infocontratotype_valorAnticipo').val('');
    $('#infocontratoformapagotype_tipoCuentaId').val('');
    $('#infocontratoformapagotype_bancoTipoCuentaId').val('');
    $('#infocontratoformapagotype_numeroCtaTarjeta').val('');
    $('#infocontratoformapagotype_titularCuenta').val('');
    $('#infocontratoformapagotype_mesVencimiento').val('');
    $('#infocontratoformapagotype_anioVencimiento').val('');
    $('#infocontratoformapagotype_codigoVerificacion').val('');
    $('#infodocumentotype_imagenes_0').val('');
    $('#infodocumentotype_tipos_0').val('');
    //Campos Nuevos a nivel de Persona y PersonaEmpresaRol
    $('#'+formname+'_tieneCarnetConadis').val('N');
    $('#'+formname+'_numeroConadis').val('');                
    $('#'+formname+'_contribuyenteEspecial').val('');
    $('#'+formname+'_pagaIva').val('');
    $('#'+formname+'_idOficinaFacturacion').val('');    
}

function habilitaCampos()
{
    $('#' + formname + '_direccionTributaria').removeAttr('readonly');
    $('#' + formname + '_nombres').removeAttr('readonly');
    $('#' + formname + '_apellidos').removeAttr('readonly');
    $('#' + formname + '_tipoEmpresa').removeAttr('disabled');
    $('#' + formname + '_razonSocial').removeAttr('readonly');
    $('#' + formname + '_tipoIdentificacion').removeAttr('disabled');
    $('#' + formname + '_tipoTributario').removeAttr('disabled');
    $('#' + formname + '_nacionalidad').removeAttr('disabled');
    $('#' + formname + '_representanteLegal').removeAttr('readonly');
    $('#' + formname + '_genero').removeAttr('disabled');
    $('#' + formname + '_tituloId').removeAttr('disabled');
    $('#' + formname + '_estadoCivil').removeAttr('disabled');
    $('#' + formname + '_fechaNacimiento_day').removeAttr('disabled');
    $('#' + formname + '_fechaNacimiento_month').removeAttr('disabled');
    $('#' + formname + '_fechaNacimiento_year').removeAttr('disabled');
    //Campos Nuevos a nivel de Persona y PersonaEmpresaRol
    $('#'+formname+'_origenIngresos').removeAttr('disabled');
    $('#'+formname+'_tieneCarnetConadis').removeAttr('disabled');
    $('#'+formname+'_numeroConadis').removeAttr('readonly');
    $('#'+formname+'_contribuyenteEspecial').removeAttr('disabled');
    $('#'+formname+'_pagaIva').removeAttr('disabled');
    $('#'+formname+'_idOficinaFacturacion').removeAttr('disabled');				
    
    //Contrato
    $('#infocontratoextratype_tipoContratoId').removeAttr('disabled');
    $('#infocontratotype_formaPagoId').removeAttr('disabled');
    $('#infocontratotype_numeroContratoEmpPub').removeAttr('readonly');
    $('#infocontratotype_valorAnticipo').removeAttr('readonly');
    $('#infocontratoformapagotype_tipoCuentaId').removeAttr('disabled');
    $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr('disabled');
    $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr('readonly');
    $('#infocontratoformapagotype_titularCuenta').removeAttr('readonly');
    $('#infocontratoformapagotype_mesVencimiento').removeAttr('disabled');
    $('#infocontratoformapagotype_anioVencimiento').removeAttr('disabled');
    $('#infocontratoformapagotype_codigoVerificacion').removeAttr('readonly');
    $('#infodocumentotype_imagenes_0').removeAttr('disabled');
    $('#infodocumentotype_tipos_0').removeAttr('disabled');
}

function habilitaFormaPago()
{
    var seleccion = $('#infocontratotype_formaPagoId').val();
    if (seleccion == 3)
    {
        $('#forma_pago').removeClass("campo-oculto");
        $('#infocontratoformapagotype_tipoCuentaId').attr("required", "required");
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("required", "required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').attr("required", "required");
        $('#infocontratoformapagotype_titularCuenta').attr("required", "required");
    }
    else {
        $('#forma_pago').addClass("campo-oculto");
        $('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
        $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
        $('#infocontratoformapagotype_titularCuenta').removeAttr("required");
    }
}

/*************************************************************************
 * FUNCIONES USADAS PARA EL INGRESO DE LAS FORMAS DE CONTACTO DEL CLIENTE
 * HACIA EL CUAL SE REALIZARA EL CAMBIO DE RAZON SOCIAL
 * FUNCIONES PARA VALIDACION DE FORMULARIO, VALIDACION DE DATOS Y GUARDAR
 *************************************************************************/
Ext.onReady(function(){

    $('#clientetype_tieneCarnetConadis select').val('N');
    $('#clientetype_numeroConadis').hide();
    $('label[for=clientetype_numeroConadis]').hide();

    $('#botonAutorizar').hide();
    $('#botonGuardar').hide();
    $('#botonPin').hide();
    $('#botonGuardarContrato').hide();

    $('#telefonoCliente').keydown(function(){
        $(this).val($(this).val().replace(/[^\d]/,''));
        $(this).keyup(function(){
            $(this).val($(this).val().replace(/[^\d]/,''));
        });
    });

    $('#telefonoCliente').keypress(function(e) {
        var tval = $('#telefonoCliente').val(),
            tlength = tval.length,
            set = 10,
            remain = parseInt(set - tlength);
        if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
            $('textarea').val((tval).substring(0, tlength - 1));
            return false;
        }
    });
    
    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'}
        ]
    });

    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    
    // create the Data Store
    storeFormas = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'PersonaFormasContactoModel',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: url_formas_contacto_persona,
            reader: {
                type: 'json',
                root: 'personaFormasContacto',
                // records will have a 'plant' tag
                totalProperty: 'total'
            },
            extraParams:{personaid:''},
            simpleSortMode: true               
        },
        listeners: {
            load: function(sender) {
                if (typeof sender.getProxy().getReader().rawData !== 'undefined' &&
                    typeof sender.getProxy().getReader().rawData.personaFormasContacto !== 'undefined' &&
                    !Ext.isEmpty(sender.getProxy().getReader().rawData.personaFormasContacto) &&
                    !Ext.isEmpty(sender.getProxy().getReader().rawData.personaFormasContacto[0]))
                {
                    $('#telefonoCliente').val('');
                    $('#telefonoCliente option').remove();
                    $('#telefonoCliente ').append("<option selected disabled>Seleccione...</option>");
                    var personaFormasContacto = sender.getProxy().getReader().rawData.personaFormasContacto;
                    Ext.each(personaFormasContacto, function(data) {
                        if (data.formaContacto.toString().indexOf('Telefono') >= 0){
                            $('#telefonoCliente').append("<option value='"+data.valor+"'>"+data.valor+"</option>");
                        }
                    });
                }
            },
            beforeload: function(store){
                store.getProxy().extraParams.personaid= personaid;
            }
        }
    });

    // create the Data Store
    var storeFormasContacto = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(i) {
                var grid = i.grid;
                if (typeof grid !== 'undefined' &&
                    typeof grid.store !== 'undefined' &&
                    typeof grid.store.data !== 'undefined' &&
                    typeof grid.store.data.items !== 'undefined' &&
                    !Ext.isEmpty(grid.store.data.items))
                {
                    $('#telefonoCliente').val('');
                    $('#telefonoCliente option').remove();
                    $('#telefonoCliente ').append("<option selected disabled>Seleccione...</option>");
                    var personaFormasContacto = grid.store.data.items;
                    Ext.each(personaFormasContacto, function(data) {
                        if (data.data.formaContacto.toString().indexOf('Telefono') >= 0 && !Ext.isEmpty(data.data.valor)){
                            $('#telefonoCliente').append("<option value='"+data.data.valor+"'>"+data.data.valor+"</option>");
                         }
                    });
                }
            }
        }
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridFormasContacto = Ext.create('Ext.grid.Panel', {
        store: storeFormas,
        columns: [ {
            text:'Forma Contacto',    
            header: 'Forma Contacto',
            dataIndex: 'formaContacto',
            width: 150,
            editor: new Ext.form.field.ComboBox({
                
                typeAhead: true,
                triggerAction: 'all',
                selectOnTab: true,
                id:'id',
                name: 'formaContacto',
		        valueField:'descripcion',
                displayField:'descripcion',                
                store: storeFormasContacto,
                lazyRender: true,
                listClass: 'x-combo-list-small'
            })
        }, {
            text: 'Valor',            
            dataIndex: 'valor',
            width: 400,
            align: 'right',
            editor: {
                width:'80%',
                xtype: 'textfield',
                allowBlank: false
            }
        },{
            xtype: 'actioncolumn',
            width:45,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar Forma Contacto',
                handler: function(grid, rowIndex, colIndex) {
                    storeFormas.removeAt(rowIndex);
                    if (typeof storeFormas.data !== 'undefined') {
                        $('#telefonoCliente').val('');
                        $('#telefonoCliente option').remove();
                        $('#telefonoCliente ').append("<option selected disabled>Seleccione...</option>");
                        if (typeof storeFormas.data.items !== 'undefined' && !Ext.isEmpty(storeFormas.data.items)) {
                            var personaFormasContacto = storeFormas.data.items;
                            Ext.each(personaFormasContacto, function(data) {
                                if (data.data.formaContacto.toString().indexOf('Telefono') >= 0 && !Ext.isEmpty(data.data.valor)){
                                    $('#telefonoCliente').append("<option value='"+data.data.valor+"'>"+data.data.valor+"</option>");
                                 }
                            });
                        }
                    }
                }
            }]
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_formas_contacto_grid'),
        width: 600,
        height: 300,
        title: '',        
        tbar: [{
            text: 'Agregar',
            handler : function(){
                // Create a model instance
                var r = Ext.create('PersonaFormasContactoModel', {
                    idPersonaFormaContacto: '',
                    formaContacto: '',
                    valor: ''
                });
                storeFormas.insert(0, r);
                cellEditing.startEditByPosition({row: 0, column: 0});
                
            }
        }],
        plugins: [cellEditing]
    });

    // manually trigger the data store load
    storeFormas.load();
    var tabs = new Ext.TabPanel({
        height: 550, 
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true, 
        id:'myTabsCrs',
        renderTo: 'my-tabs-crs',            
        items:[
            {contentEl:'tab1', id: 'idTabDatosPrincipalesCrs', title:'Datos Principales'},
            {contentEl:'tab2', id: 'idTabContactoClienteCrs', title:'Formas de contacto',listeners:{
                 activate: function(tab){
                         gridFormasContacto.view.refresh()
                               
                 }
                               
             }},
             {
               contentEl: 'tab6',
               id:'idTabRepresentanteLegalCrs', 
               title: 'Representante Legal',
               hidden:true,
               listeners: {
                 activate: function (tab) {
                   createTabPanelRepresentanteLegal ();
                 },
               },
             },
            {contentEl:'tab3', id: 'idTabDatosContratoCrs', title:'Datos del Contrato', hidden: true},
            {contentEl:'tab4', id: 'idTabSubirAchivosCrs', title:'Subir Archivos', hidden: true},
            {contentEl:'tab5', id: 'idTabAdicionalCrs', title:'Adicional', hidden: true}
       ]          

    }); 

});

function grabar(campo)
{
 
    let isTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val()  !='Seleccione...'; 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    let isTipoTributario  = $('#' + formname + '_tipoTributario').val()  !='Seleccione...'; 
    let strYaExiste = $("#" + formname + "_yaexiste").val();
    activeTab('idTabDatosPrincipalesCrs');
    if (!isTipoIdentificacion) { 
        alertModal('Seleccione el tipo de identificación.');        
    }else if (!isTipoTributario) { 
        alertModal('Seleccionar tipo tributario');
    }else if (strIdentificacion=="") { 
        alertModal('Ingrese una identificación.');     
    }else if ( strYaExiste == 'N')
    {
        var array_data    = new Array();
        var variable      = '';
        var valoresVacios = false;
        for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
        {
            variable = gridFormasContacto.getStore().getAt(i).data;
            for (var key in variable)
            {
                var valor = variable[key];
                if (key == 'valor' && valor == '')
                {
                    valoresVacios = true;
                }
                else
                {
                    array_data.push(valor);
                }
            }
        }
        $(campo).val(array_data);

        if (($(campo).val() == '0,,') || ($(campo).val() == ''))
        {   activeTab('idTabContactoClienteCrs');
            alertModal('No hay formas de contacto aun ingresadas.');
            $(campo).val('');
            return false; 
        }
        else 
        {
            if (valoresVacios == true)
            {    activeTab('idTabContactoClienteCrs');
                alertModal('Hay formas de contacto que tienen valor vacio, por favor corregir.');
                $(campo).val('');
                 return false; 
            }
        }

        //Valida formulario de Contrato
        if ($('#infocontratotype_formaPagoId').val() == 3)
        {
            validarFormularioContrato();
        }
        else
        { 
            var tipoContratoId     = $('#infocontratoextratype_tipoContratoId').val();
            var proceso            = $('#procesoContrato').val();
            var formaContrato      = $('#formaContrato').val();

            if ((formaContrato=="" || formaContrato == 'Selecione...') && (prefijoEmpresa=="MD" || prefijoEmpresa=="EN" )&& proceso != 'Adendum')
            {   activeTab('idTabDatosContratoCrs');
                alertModal("Seleccione la forma del Contrato");
            }
            else if ((tipoContratoId == "" || tipoContratoId <= 0) && proceso != 'Adendum')
            {   activeTab('idTabDatosContratoCrs');
                alertModal("Seleccione un Tipo de Contrato");
            }
            else
            {
                $("#infocontratotype_formaPagoId").attr("enabled", "enabled");
                $("#infocontratoformapagotype_tipoCuentaId").attr("enabled", "enabled");
                $('#infocontratoformapagotype_bancoTipoCuentaId').attr("enabled", "enabled");
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
                validacionesForm();
            }
        }
    }
    else
    {
        validacionesForm();
    }
}

/**
 * Permite validar las formas de contactos.
 *
 * @version 1.00
 * 
 * Se llama a validación de formas de contactos centralizada.
 *
 * @author Héctor Ortega <haortega@telconet.ec>
 * @version 1.01, 29/11/2016
 */
function validaFormasContacto()
{
    if ($("#" + formname + "_yaexiste").val() == 'N')
    {
        
        return Utils.validaFormasContacto(gridFormasContacto);
      
    }
    else
    {
        return true;
    }
}

/**
 * Se valida que tenga por lo menos un documento digital
 * para MD, para TN no hay restriccion.
 *
 * @author Ricardo Coello Quezada <rcoello@telconet.ec>
 * @version 1.0, 28/09/2017
 * 
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.1  se valida el número de imagenes a subir MD
 * @since 1.0
 * 
 * 
 * @author Joel Broncano <jbroncano@telconet.ec>
 * @version 1.2 Soporte EN
 * @since 19/04/2023
 * 
 */
function validaSubidaArchivosDig()
{
    var ingresoFoto   = 0;
    var formaContrato = $('#formaContrato').val();
    var strProceso    = $('#procesoContrato').val();

    let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li'); 
    for (let i = 0; i <= list.length - 1; i++) {
        let id = 'infodocumentotype_imagenes_'+i;
        let archivobase64 = document.getElementById(id).getAttribute('value'); 
        if (Boolean(archivobase64)) 
        {
            ingresoFoto++;
        }        
    } 

    if(ingresoFoto >= 1 && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' ) && formaContrato == 'Contrato Fisico')
    {    
        return true;
    }
     
    if(ingresoFoto >= 3 && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN') && formaContrato != 'Contrato Fisico')
    {   
          return true;
    }

    if((prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')&& formaContrato == 'Contrato Fisico' && strProceso == 'Adendum')
    {   
          return true;
    }

    if(prefijoEmpresa == 'TN' )
    {   
          return true;
    }
    
    activeTab('idTabSubirAchivosCrs'); 
    alert('Debe adjuntar los archivos digitales');
    return false;
}

function validacionesForm()
{ 
    var intEdad = 0;

    if (validaFormasContacto())
    {
        if(validaCorreoECDF())
        {
            var asignaciones = gridAsignaciones.getStore().getCount();       

            if (asignaciones == 0)
            {
                alertModal("No se han escogido los Login para el cambio de razon Social por Punto");
                return false;
            }
            
            if ($('#' + formname + '_tipoEmpresa').val() == '' &&
            ($('#' + formname + '_fechaNacimiento_day').val() == '' ||
                $('#' + formname + '_fechaNacimiento_month').val() == '' ||
                $('#' + formname + '_fechaNacimiento_year').val() == ''))
            {
                alert('La Fecha de Nacimiento es un campo obligatorio, No puede realizar Cambio de Razon Social por Punto');
                return false;
            }
            else
            {
                intEdad = validaFechaNacimiento($('#' + formname + '_fechaNacimiento_day').val(),
                                                $('#' + formname + '_fechaNacimiento_month').val(),
                                                $('#' + formname + '_fechaNacimiento_year').val());
                if (intEdad < 18)
                {
                    alert('La Fecha de Nacimiento ingresada corresponde a un menor de edad - \n\
                        No puede realizar Cambio de Razon Social por Punto :' + $('#' + formname + '_fechaNacimiento_year').val() + '-' +
                        $('#' + formname + '_fechaNacimiento_month').val() + '-' +
                        $('#' + formname + '_fechaNacimiento_day').val());
                    return false;
                }
            }
            
            habilitaCampos();
            $('#'+formname+'_esPrepago').removeAttr('disabled');			
            obtenerAsignaciones();

                if (validarContentidoRepresentanteLegal() )
                {
                    if (validaSubidaArchivosDig() )
                    {
                        var telefonoCliente = $('#telefonoCliente').val();
                        var formaContrato   = $('#formaContrato').val();
                        if ((Ext.isEmpty(telefonoCliente) || 
                            telefonoCliente.toString().indexOf('Seleccione') >= 0) && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN')
                            && formaContrato != 'Contrato Fisico'
                        )
                        {
                            activeTab('idTabDatosContratoCrs');
                            alertModal("Seleccione el número de teléfono para envío de PIN");
                            return false;
                        }                
                        
                        enviarInformacionProcesar();
                        return true;
                }    
           } 
       }    
    }
    else {
        activeTab('idTabContactoClienteCrs');
     }

  return false;
}

/**
 * Se llama al proceso de crear contrato.
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 * 
 * 
 * Soporte EN
 * @author Joel Broncano <jbroncano@telconet.ec>
 * @version 1.1 18/04/2023
 * 
 */
function aprobarClick()
{
    Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere');
    var form = $('#cambiorazonsocial_form');
    var formData = new FormData(form[0]);
    $.ajax({
        url: url_crearContrato,
        type: 'POST',
        data: formData,
        success: function(response) {
            var json = Ext.JSON.decode(response);
            Ext.MessageBox.hide();
            if( typeof tarjetaCompleta !== 'undefined' && tarjetaCompleta != "")
            {
                var tarjetaOculta   = "xxxxxxxxxx" + tarjetaCompleta.slice(-3);
                $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaOculta);
            }
            if(json.strStatus == 0)
            {
                var strProceso   = $('#procesoContrato').val();
                if(strProceso == 'Adendum' && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' ))
                {
                    Ext.Msg.alert("Mensaje", json.strMensaje, function(btn){
                        if(btn=='ok')
                        {
                            $('#botonAutorizar').show();
                            $('#botonPin').show();
                            $('#botonGuardar').hide();
                            $('#botonGuardarContrato').hide();
                        }
                    });
                } 
                else
                {
                    var strUrl = $('#url_redireccionaniento').val();
                    Ext.Msg.alert("Mensaje", json.strMensaje, function(btn){
                        if(btn=='ok')
                        {
                            window.location.href = strUrl;
                        }
                    });
                }
            }
            else
            {
                Ext.Msg.alert('Mensaje ',  json.strMensaje);  
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            Ext.MessageBox.hide();
            Ext.Msg.alert('Mensaje ', errorThrown);
        },
        failure: function(response){
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function readURL(input) {
    var reader = new FileReader();
    reader.readAsDataURL(input.files[0]);
    reader.onload = function (e) {
        input.setAttribute('value', e.target.result);
    }
}

/**
 * Se llama al proceso de crear contrato.
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 * 
 * @author Alex Gomez <algomez@telconet.ec>
 * @version 1.1 10/08/2022 Se agrega nuevo parametro para el paso de array de puntos por proceso de CRS
 * 
 * 
 * 
 * 
 * @author Joel Broncano <jbroncano@telconet.ec>
 * @version 1.2 19/04/2023 Soporte EN
 * 
 * 
 */
function enviarInformacionProcesar()
{
    var numeroCtaTarjeta = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
    if(numeroCtaTarjeta.includes("xxxxxxxxxx"))
    {
        $('#forma_pago').addClass("campo-oculto");
        $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaCompleta);
    }

    var url = document.getElementById("cambiorazonsocial_form").getAttribute('action'); 
        Ext.MessageBox.wait("Procesando el cambio de razón social por login...", 'Por favor espere');
        var form = $('#cambiorazonsocial_form');
        var formData = new FormData(form[0]);
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                var json = Ext.JSON.decode(response);
                var arrayPuntosCRS = Ext.JSON.encode(json.arrayPuntosCRS);

                Ext.MessageBox.hide();
                document.getElementById("puntoCliente").setAttribute("value", json.intPunto);
                document.getElementById("personaEmpresaRolId").setAttribute("value", json.intPersonaEmprRol);
                document.getElementById("url_redireccionaniento").setAttribute("value", json.strUrl);
                document.getElementById("arrayPuntosCRS").setAttribute("value", arrayPuntosCRS);
                var strProceso            = $('#procesoContrato').val();

                if( typeof tarjetaCompleta !== 'undefined' && tarjetaCompleta != "")
                {
                    $('#forma_pago').removeClass("campo-oculto");
                    var tarjetaOculta   = "xxxxxxxxxx" + tarjetaCompleta.slice(-3);
                    $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaOculta);
                }
                if(json.strStatus == 0)
                {
                    activeTab('idTabDatosPrincipalesCrs');
                    if(strProceso == 'Adendum' && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' ) && json.strContratoFisico != 1)
                    {
                        Ext.Msg.alert("Mensaje", json.strMensaje, function(btn){
                            if(btn=='ok')
                            {
                                $('#botonAutorizar').show();
                                $('#botonPin').show();
                                $('#botonCambiarCliente').hide();
                                $('#botonGuardarContrato').hide();
                            }
                        });
                    } 
                    else
                    {
                        Ext.Msg.alert("Mensaje", json.strMensaje, function(btn){
                            if(btn=='ok')
                            {
                                window.location.href = json.strUrl;
                            }
                        });
                    }
                }
                else
                {
                    if(json.strStatusCliente == 1)
                    {
                        $('#botonCambiarCliente').hide();
                        var btncancelar = Ext.create('Ext.Button', {
                            text: 'Cancelar',
                            cls: 'x-btn-rigth',
                            handler: function() {
                                winContratoFisico.destroy();
                                $('#botonGuardar').show();
                            }
                        }); 
                        
                        var btnguardar = Ext.create('Ext.Button', {
                            text: 'Ok',
                            cls: 'x-btn-rigth',
                            handler: function() {
                                winContratoFisico.destroy();
                                $('#botonGuardarContrato').show();
                                if(strProceso == 'Adendum')
                                {
                                    window.location.href = json.strUrl;
                                }
                                else
                                {
                                    crearContratoFisico();
                                }
                            }
                        }); 

                        var formPanel = Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                waitMsgTarget: true,
                                height: 180,
                                width: 500,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 140,
                                    msgTarget: 'side'
                                },
                    
                                items: [{
                                    xtype: 'fieldset',
                                    title: 'Información',
                                    defaultType: 'textfield',
                                    items: [
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Mensaje:',
                                            id: 'mensajeContrato',
                                            name: 'mensajeContrato',
                                            value: json.strMensaje
                                        }
                                    ]
                                }]
                        });

                        var winContratoFisico = Ext.create('Ext.window.Window', {
                                title: 'Mensaje',
                                modal: true,
                                width: 660,
                                height: 200,
                                resizable: false,
                                layout: 'fit',
                                items: [formPanel],
                                buttonAlign: 'center',
                                buttons:[btnguardar,btncancelar]
                        }).show(); 
                    }
                    else
                    {   activeTab('idTabDatosPrincipalesCrs');
                        alertModal( json.strMensaje);
                    }
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                activeTab('idTabDatosPrincipalesCrs');
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error ', errorThrown);
            },
            failure: function(response){
                activeTab('idTabDatosPrincipalesCrs');
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
            },
            cache: false,
            contentType: false,
            processData: false
        });
}

/**
 * Se llama al proceso de crea contrato fisico
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 03/03/2021
 */
function crearContratoFisico()
{
    var formContrato = $('#cambiorazonsocial_form');
    var formDataContrato = new FormData(formContrato[0]);
    Ext.MessageBox.wait("Guardando Contrato Fisico...", 'Por favor espere');
    $.ajax({
        url: url_crearContratoFisico,
        type: 'POST',
        data: formDataContrato,
        success: function(response) {
            Ext.MessageBox.hide();
            var jsonContrato = Ext.JSON.decode(response);
            activeTab('idTabDatosPrincipalesCrs');
            Ext.Msg.alert("Mensaje", jsonContrato.strMensaje, function(btn){
                if(btn=='ok')
                {
                    if(jsonContrato.strStatus  == 0)
                    {
                        window.location.href = jsonContrato.strUrl;
                    }
                }
            });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            activeTab('idTabDatosPrincipalesCrs');
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', errorThrown);
        },
        failure: function(response){
            activeTab('idTabDatosPrincipalesCrs');
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

/**
 * Se llama al proceso de autorizar contrato
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 * 
 * @author Alex Gomez <algomez@telconet.ec>
 * @version 1.1 10/08/2022 Se agrega nuevo parametro para el paso de array de puntos por proceso de CRS
 */
function autorizarContrato()
{
    var formPanelAutorizar = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 110,
        width: 400,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [
        {
            xtype: 'textfield',
            hideTrigger: true,
            id: 'pin',
            name:'pin',
            fieldLabel: 'Ingresar Pin:',
            value: '',
            width: 350
        },
    ]
    });  
    var btncancelar = Ext.create('Ext.Button', {
            text: 'Cancelar',
            cls: 'x-btn-rigth',
            handler: function() {
		        winAutorizarContrato.destroy();													
            }
    }); 
    var btnaceptar = Ext.create('Ext.Button', {
            text: 'Autorizar',
            cls: 'x-btn-left',
            handler: function() {
                var valorPin      = Ext.getCmp('pin').value;
                if(valorPin.trim() != '')
                {
                    winAutorizarContrato.destroy();
                    Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere');
                    var intPersonaEmpresaRolId = $('#personaEmpresaRolId').val();
                    var intPunto               = $('#puntoCliente').val();
                    var strProcesoContrato     = $('#procesoContrato').val(); 
                    var arrayPuntosCRS         = $("#arrayPuntosCRS").val();
                    Ext.MessageBox.wait("Autorizando Contrato...", 'Por favor espere');
                    $.ajax({
                        url: url_autorizarContrato,
                        type: 'POST',
                        data: {
                            "puntoCliente"        : intPunto,
                            "personaEmpresaRolId" : intPersonaEmpresaRolId,
                            "pin"                 : valorPin,
                            "tipo"                : strProcesoContrato,
                            "arrayPuntosCRS"      : arrayPuntosCRS,
                            "cambioRazonSocial"   : "S"
                        },
                        success: function(response) {
                            var json = Ext.JSON.decode(response);
                            Ext.MessageBox.hide();
                            activeTab('idTabDatosPrincipalesCrs');
                            if(json.strStatus == 0)
                            {
                                var strUrl = json.strUrl;
                             
                                Ext.Msg.alert("Mensaje", "Proceso realizado", function(btn){
                                    if(btn=='ok')
                                    {
                                        window.location.href = strUrl;
                                    }
                                });
                            }
                            else
                            {
                                alertModal( json.strMensaje);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) { 
                            activeTab('idTabDatosPrincipalesCrs');
                            Ext.MessageBox.hide();
                            Ext.Msg.alert('Mensaje ', errorThrown);
                        },
                        failure: function(response){
                            activeTab('idTabDatosPrincipalesCrs');
                            Ext.MessageBox.hide();
                            Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ', 'Ingrese el PIN');
                }
            }
    });   
    var winAutorizarContrato = Ext.create('Ext.window.Window', {
			title: 'Autorizar Contrato',
			modal: true,
			width: 410,
			height: 110,
			resizable: true,
			layout: 'fit',
			items: [formPanelAutorizar],
			buttonAlign: 'center',
			buttons:[btnaceptar,btncancelar]
    }).show();  
}

/**
 * Se llama al proceso de reenvío de Pin
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 */
function reenviarPin()
{
    var personaEmpresaRolId = $('#personaEmpresaRolId').val();
    var puntoCliente        = $('#puntoCliente').val();
    var telefonoCliente     = $('#telefonoCliente').val();
    Ext.MessageBox.wait("Reenviando Pin...", 'Por favor espere');
    $.ajax({
        url: url_reenvioPin,
        type: 'POST',
        data: {
            "puntoCliente"        : puntoCliente,
            "personaEmpresaRolId" : personaEmpresaRolId,
            "telefonoCliente"     : telefonoCliente
        },
        success: function(response) {
            activeTab('idTabDatosPrincipalesCrs');
            var json = Ext.JSON.decode(response);
            Ext.MessageBox.hide();
            alertModal( json.strMensaje);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            activeTab('idTabDatosPrincipalesCrs');
            Ext.MessageBox.hide();
            Ext.Msg.alert('Mensaje ', errorThrown);
        },
        failure: function(response){
            activeTab('idTabDatosPrincipalesCrs');
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
        }
    });
}

/** 
 * Descripcion: Metodo encargado de devolver edad en base a la fecha recibida
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 *
 * @param int intDia
 * @param int intMes
 * @param int intAno
 * 
 * @version 1.0 03-09-2015 
 * @return integer
 */
function validaFechaNacimiento(intDia, intMes, intAno)
{       
    var intAnoDiferencia = 0;
    var intMesDiferencia = 0;
    var intDiaDiferencia = 0;        
    var f                = new Date();        
    intAnoDiferencia     = f.getFullYear() - intAno;
    intMesDiferencia     = f.getMonth() +1 - intMes;
    intDiaDiferencia     = f.getDate() - intDia;
  
    if ((intDiaDiferencia < 0 && intMesDiferencia == 0) || intMesDiferencia < 0)
    {
        intAnoDiferencia--;
    }    
    return intAnoDiferencia;
}

/************************************************************************************
 *  FUNCIONES PARA LOS GRIDS USADOS PARA LA ASIGNACION DE PUNTOS(LOGINES) QUE VAN
 *  REALIZAR CAMBIO DE RAZON SOCIAL 
 * 
 ************************************************************************************/

function obtenerAsignaciones()
{
    Ext.get('puntos_asignados').dom.value = "";
    var array_asignaciones                = new Object();
    array_asignaciones['total']           = gridAsignaciones.getStore().getCount();
    array_asignaciones['asignaciones']    = new Array();
    var array_data                        = new Array();
    for (var i = 0; i < gridAsignaciones.getStore().getCount(); i++)
    {

        array_data.push(gridAsignaciones.getStore().getAt(i).data);
    }

    array_asignaciones['asignaciones']    = array_data;
    Ext.get('puntos_asignados').dom.value = Ext.JSON.encode(array_asignaciones);

}

function eliminarAsignacionPto()
{       
    var xRowSelMod = grid.getSelectionModel().getSelection();   
    for (var i = 0; i < xRowSelMod.length; i++)
    {
        var RowSel = xRowSelMod[i];        
        grid.getStore().remove(RowSel);
    }
}

function ingresarAsignacion()
{   
    if (sm.getSelection().length > 0)
    {   
        for (var i = 0; i < sm.getSelection().length; ++i)
        {   
            var r = Ext.create('Asignacion', {
                punto_id: '',
                idPto: sm.getSelection()[i].get('idPto'),
                login: sm.getSelection()[i].get('login')
            });            
            if (!existeAsignacion(r, gridAsignaciones))            
                storeAsignaciones.insert(0, r);            
        }
        eliminarAsignacionPto();
    }
    else
    {
        alert('Seleccione por lo menos una accion de la lista');
    }
}

function existeAsignacion(myRecord, grid)
{
    var existe = false;
    var num    = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var idPto = grid.getStore().getAt(i).get('idPto');
        if (idPto == myRecord.get('idPto'))
        {
            existe = true;            
            break;
        }
    }
    return existe;
}

function EliminarAsignacionesPtos()
{   
    for (var i = 0; i < gridAsignaciones.getStore().getCount(); i++)
    {
        for (var j = 0; j < grid.getStore().getCount(); j++)
        {
            if (gridAsignaciones.getStore().getAt(i).get('idPto') == grid.getStore().getAt(j).get('idPto'))
            {
                grid.getStore().remove(grid.getStore().getAt(j));
            }
        }

    }
}

function eliminarSeleccion(datosSelect)
{
    Ext.Msg.confirm('Alerta', 'Se eliminaran los logines asignados que han sido seleccionados. Desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            var xRowSelMod = datosSelect.getSelectionModel().getSelection();
            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];
                datosSelect.getStore().remove(RowSel);
            }
            storePuntos.load({
                callback: function() {
                    EliminarAsignacionesPtos();
                }
            });
        }
    });
}

    
Ext.onReady(function() {   

    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPto', mapping: 'idPto'},
            {name: 'login', mapping: 'login'},
            {name: 'Nombre Punto', mapping: 'nombrePunto'},
            {name: 'Direccion', mapping: 'direccionPunto'}
        ]
    });
    
    // Inicio de Puntos gridPuntos
    storePuntos = new Ext.data.Store({
        pageSize: 20,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_gridPtos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                p_login: '',
                p_nombrePunto: '',
                p_direccion : ''
            }
        },
        fields:
            [
                {name: 'idPto', mapping: 'idPto'},
                {name: 'login', mapping: 'login'},
                {name: 'nombrePunto', mapping: 'nombrePunto'},
                {name: 'direccionPunto', mapping: 'direccionPunto'}
            ],
        autoLoad: true
    });

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    grid = Ext.create('Ext.grid.Panel', {
        width: 500,
        height: 400,
        store: storePuntos,
        loadMask: true,
        selModel: sm,
        iconCls: 'icon-grid',
        // grid columns
        columns: [
            {
                header: 'PuntoId',
                dataIndex: 'idPto',
                hidden: true,
                hideable: false
            },
            {
                header: 'Login',
                dataIndex: 'login',
                width: 120,
                sortable: true
            },
            {
                header: 'Nombre Punto',
                dataIndex: 'nombrePunto',
                width: 150,
                sortable: true
            },
            {
                header: 'Direccion',
                dataIndex: 'direccionPunto',
                width: 350,
                sortable: true
            }
            
        ],
        title: 'Puntos',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePuntos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridPuntos'
    });
   // Fin de Puntos gridPuntos
    
   // Inicio de Asignaciones gridAsignaciones
   storeAsignaciones = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: 'gridAsignaciones',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'asignaciones'
            }
        },
        fields:
            [
                {name: 'idPto', mapping: 'idPto'},
                {name: 'login', mapping: 'login'}
            ]
    });
    
    sm2 = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    })

    gridAsignaciones = Ext.create('Ext.grid.Panel', {
        width: 320,
        height: 450,
        store: storeAsignaciones,
        loadMask: true,
        selModel: sm2,
        iconCls: 'icon-grid',
        // grid columns
        columns: [
            {
                header: 'PuntoId',
                dataIndex: 'idPto',
                hidden: true,
                hideable: false
            },
            {
                header: 'Login',
                dataIndex: 'login',
                width: 343,
                sortable: true
            }
        ],
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el punto seleccionado',
                        iconCls: 'remove',
                        scope: this,
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridAsignaciones);
                        }
                    }]
            }],
        title: 'Puntos a realizar cambio de razon social',
        frame: true,
        renderTo: 'gridAsignaciones'
    });
    // Fin de Asignaciones gridAsignaciones  

    // Filtros de Busqueda GridPuntos
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 1,         
        border: false,        
        buttonAlign: 'center',
        layout: {            
            type:'table',
            columns: 2,
            align: 'left',            
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 500,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
                
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }
        ],
        items: [           
            {
                xtype: 'textfield',
                id: 'txtLogin',
                fieldLabel: 'Login',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 2px'
            },
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombrePunto',
                fieldLabel: 'Nombre Punto',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 2px'
            },           
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtDireccion',
                fieldLabel: 'Direccion',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 2px'
            }           
        ],
        renderTo: 'filtroPuntos'
    });
    // Fin de Filtros de Busqueda GridPuntos
   
});

function buscar(tipo) 
{
    storePuntos.getProxy().extraParams.p_login       = Ext.getCmp('txtLogin').value;
    storePuntos.getProxy().extraParams.p_nombrePunto = Ext.getCmp('txtNombrePunto').value;
    storePuntos.getProxy().extraParams.p_direccion   = Ext.getCmp('txtDireccion').value;
    
    storePuntos.load({
        callback:function(){        
            EliminarAsignacionesPtos();
        }
    });
    
}

function limpiar(tipo)
{
    Ext.getCmp('txtLogin').value = "";
    Ext.getCmp('txtLogin').setRawValue("");
    storePuntos.getProxy().extraParams.p_login = Ext.getCmp('txtLogin').value;
    
    Ext.getCmp('txtNombrePunto').value = "";
    Ext.getCmp('txtNombrePunto').setRawValue("");
    storePuntos.getProxy().extraParams.p_nombrePunto = Ext.getCmp('txtNombrePunto').value;
    
    Ext.getCmp('txtDireccion').value = "";
    Ext.getCmp('txtDireccion').setRawValue("");
    storePuntos.getProxy().extraParams.p_direccion = Ext.getCmp('txtDireccion').value;
       
    storePuntos.load({
        callback:function(){        
            EliminarAsignacionesPtos();
        }
    });
}

/********************************************************************************
 * FUNCIONES USADAS PARA EL INGRESO DE LA INFORMACION DEL CONTRATO SI SE TRATA 
 * DE UN CAMBIO DE RAZON DE SOCIAL HACIA UN CLIENTE NUEVO
 ********************************************************************************/

$('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
$('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
$('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
$('#infocontratoformapagotype_titularCuenta').removeAttr("required");
$("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
$("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
$("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
 
function validarFormularioContrato()
{
    var tipoContratoId     = $('#infocontratoextratype_tipoContratoId').val();
    var numeroCtaTarjeta   = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
    var titularCuenta      = $('#infocontratoformapagotype_titularCuenta').val();
    var bancoTipoCuentaId  = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
    var anioVencimiento    = $('#infocontratoformapagotype_anioVencimiento').val();
    var mesVencimiento     = $('#infocontratoformapagotype_mesVencimiento').val();
    var codigoVerificacion = $('#infocontratoformapagotype_codigoVerificacion').val();
    var formaContrato      = $('#formaContrato').val();
    var proceso            = $('#procesoContrato').val();
    var verificacion       = true;
    mensajes               = "";
    mensajes_bin           = "";

    if ((formaContrato=="" || formaContrato == 'Selecione...') && (prefijoEmpresa=="MD" || prefijoEmpresa=="EN" ) && proceso != 'Adendum')
    {
        mensajes+='Seleccione forma de contrato <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html(""+mensajes+mensajes_bin+"");  
        verificacion=false;
    }

    if (tipoContratoId == "" || tipoContratoId <= 0)
    {
        mensajes    += 'Seleccione un Tipo de Contrato <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }

    if (numeroCtaTarjeta == "")
    {
        mensajes     += 'Ingrese el Numero de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }

    if (titularCuenta == "")
    {
        mensajes     += 'Ingrese el Titular de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }


    if (verificacion)
    {
        // Funcion para obtener si la forma de pago es Tarjeta o Cuenta Bancaria 
        $.ajax({
            type: "POST",
            data: "bancoTipoCuentaId=" + bancoTipoCuentaId,
            url: url_validarPorFormaPago,
            success: function(msg) {
                if (msg.msg == 'TARJETA')
                {
                    $('label[for=infocontratoformapagotype_mesVencimiento]').html('* Mes Vencimiento:');
                    $('label[for=infocontratoformapagotype_mesVencimiento]').addClass('campo-obligatorio');
                    $("#infocontratoformapagotype_mesVencimiento").attr('required', 'required');
                    $('label[for=infocontratoformapagotype_anioVencimiento]').html('* A&ntilde;o Vencimiento:');
                    $('label[for=infocontratoformapagotype_anioVencimiento]').addClass('campo-obligatorio');
                    $("#infocontratoformapagotype_anioVencimiento").attr('required', 'required');
                    $('label[for=infocontratoformapagotype_codigoVerificacion]').html('* Codigo Verificaci&oacute;n:');
                    $('label[for=infocontratoformapagotype_codigoVerificacion]').addClass('campo-obligatorio');
                    $("#infocontratoformapagotype_codigoVerificacion").attr('required', 'required');
                    if (anioVencimiento == "" || mesVencimiento == "")
                    {
                        mensajes     += 'Ingrese Anio y mes de Vencimiento de la tarjeta <br /> ';
                        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
                        verificacion = false;
                    }

                    if (codigoVerificacion == "")
                    {
                        mensajes    += 'Ingrese el codigo de verificacion de la tarjeta <br /> ';
                        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
                        verificacion = false;
                    }
                    if (verificacion)
                        validacionesForm();
                }
                else
                {
                    $("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
                    $("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
                    $("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
                    validacionesForm();
                }
            }
        });
    }
}
$("#infocontratoformapagotype_numeroCtaTarjeta").blur(function()
{
    var tipoCuentaId       = $('#infocontratoformapagotype_tipoCuentaId').val();
    var bancoTipoCuentaId  = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
    var numeroCtaTarjeta   = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
    var codigoVerificacion = $('#infocontratoformapagotype_codigoVerificacion').val();
    var formaPagoId        = $('#infocontratotype_formaPagoId').val();

    $.ajax({
        type: "POST",
        data: "tipoCuentaId=" + tipoCuentaId + "&bancoTipoCuentaId=" + bancoTipoCuentaId + "&numeroCtaTarjeta=" + numeroCtaTarjeta +
            "&codigoVerificacion=" + codigoVerificacion +"&formaPagoId=" + formaPagoId,
        url: url_validarNumeroTarjetaCta,
        timeout: 10000,
        success: function(msg)
        {
            if (msg.msg == 'ok')
            {
                mensajes_bin = "";
                var info     = JSON.stringify(msg.validaciones);
                var myArray  = JSON.parse(info);
                for (var i = 0; i < myArray.length; i++)
                {
                    var object    = myArray[i];
                    mensajes_bin += object.mensaje_validaciones + ' <br /> ';
                }
                $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            }
            else
            {
                mensajes_bin = "";
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
            }
        }
    });
});

$('#formaContrato').change(function() {
    
    $('#tipos-fields-list').empty();
    $('#imagenes-fields-list').empty();

    var formaContrato = $(this).val();
    let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li'); 
    imagenesCount = list.length;  
    tiposCount    = list.length;        
    var imagenesList = jQuery('#imagenes-fields-list');
    var tiposList    = jQuery('#tipos-fields-list');
    var i = 0;
    var name='__name__';

    // graba la plantilla prototipo
    var newWidget = imagenesList.attr('data-prototype');

    if(formaContrato == 'Contrato Digital')
    {
        // graba la plantilla prototipo
        for (var property in arrayDocumentosSubir) 
        {
            var object    = arrayDocumentosSubir[property];
            var contador  = imagenesCount+i;
            var newWidgetTipo =            
            ' <select required="required" id="infodocumentotype_tipos_' +contador+ '"'+
            ' class="campo-obligatorio-select" name="infodocumentotype[tipos][' +contador+ ']"> '+
            '   <option value="'+property+'" > '+object+'</option> '+
            ' </select>';

            var newWidgetImagen =
            '<input type="file" id="infodocumentotype_imagenes_' +contador+ '"'+
            'name="infodocumentotype[imagenes][' +contador+ ']" class="campo-obligatorio"'+
            'onchange="readURL(this)"></input>';

            // crea un nuevo elemento lista y lo añade a la lista
            var newLiImagen = jQuery('<li></li>').html(newWidgetImagen);
            newLiImagen.appendTo(jQuery('#imagenes-fields-list'));

            var newLiTipo = jQuery('<li></li>').html(newWidgetTipo);             
            newLiTipo.appendTo(jQuery('#tipos-fields-list')); 

            i++;          
        }
    }
    else
    {
        var newWidgetTipoFisico = tiposList.attr('data-prototype');            

        newWidget = newWidget.replace(name, imagenesCount); 
        newWidget = newWidget.replace(name, imagenesCount);           
        newWidgetTipoFisico = newWidgetTipoFisico.replace(name, tiposCount);           
        newWidgetTipoFisico = newWidgetTipoFisico.replace(name, tiposCount);
        newWidget = newWidget.replace('"campo-obligatorio"', '"campo-obligatorio" onchange="readURL(this)"');
        
        // crea un nuevo elemento lista y lo añade a la lista
        var newLiImagenFisico = jQuery('<li></li>').html(newWidget);
        newLiImagenFisico.appendTo(jQuery('#imagenes-fields-list'));

        var newLiTipoFisico = jQuery('<li></li>').html(newWidgetTipoFisico);             
        newLiTipoFisico.appendTo(jQuery('#tipos-fields-list'));
    }

});

$('#infocontratotype_formaPagoId').change(function()
{
    var seleccion = $('#infocontratotype_formaPagoId').val();
    if (seleccion == 3)
    {
        $('#forma_pago').removeClass("campo-oculto");
        $('#infocontratoformapagotype_tipoCuentaId').attr("required", "required");
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("required", "required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').attr("required", "required");
        $('#infocontratoformapagotype_titularCuenta').attr("required", "required");
    }
    else
    {
        $('#forma_pago').addClass("campo-oculto");
        $('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
        $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
        $('#infocontratoformapagotype_titularCuenta').removeAttr("required");
        $("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
        $("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
        $("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
    }
});

$('#infocontratoformapagotype_tipoCuentaId').change(function()
{
    var tipoCuenta = $('#infocontratoformapagotype_tipoCuentaId').val();
    $.ajax({
        type: "POST",
        data: "tipoCuenta=" + tipoCuenta,
        url:  url_listarBancosAsociados,
        success: function(msg)
        {
            if (msg.msg == 'ok')
            {
                // Para tipo de cuenta corriente y ahorro se ocultan campos código de verificación,fecha y mes de vencimiento.
                if(tipoCuenta == 1 || tipoCuenta == 2)
                {
                    $('#tarjeta').addClass("campo-oculto");
                }
                else
                {
                    $('#tarjeta').removeClass("campo-oculto");
                }                   
                // Debo poner el tamaño de la caja de texto N° tarjeta/cta
                document.getElementById("infocontratoformapagotype_bancoTipoCuentaId").innerHTML = msg.div;
                document.getElementById("infocontratoformapagotype_numeroCtaTarjeta").setAttribute("maxlength", msg.tam);
                if (msg.tam == "16")
                {
                    $("label[for='infocontratoformapagotype_mesVencimiento']").addClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_mesVencimiento']").html('* Mes Vencimiento:');
                    $("label[for='infocontratoformapagotype_anioVencimiento']").addClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_anioVencimiento']").html('* A&ntilde;o Vencimiento:');
                    $("label[for='infocontratoformapagotype_codigoVerificacion']").addClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_codigoVerificacion']").html('* Cod. Verificacion:');
                }
                else
                {
                    $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_mesVencimiento']").html('Mes Vencimiento:');
                    $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_anioVencimiento']").html('A&ntilde;o Vencimiento:');
                    $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_codigoVerificacion']").html('Cod. Verificacion:');
                }
            }
            else
            {
                document.getElementById("infocontratoformapagotype_bancoTipoCuentaId").innerHTML = msg.msg;
                $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
                $("label[for='infocontratoformapagotype_mesVencimiento']").html('Mes Vencimiento:');
                $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
                $("label[for='infocontratoformapagotype_anioVencimiento']").html('A&ntilde;o Vencimiento:');
                $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
                $("label[for='infocontratoformapagotype_codigoVerificacion']").html('Cod. Verificacion:');
            }
        }
    });
});
        
$("#infocontratotype_valorAnticipo").blur(function()
{
    if (validaAnticipo() || $("#infocontratotype_valorAnticipo").val() == "")
    {
        ocultarDiv('div_valor');
        return true;
    }
    else
    {
        mostrarDiv('div_valor');
        $('#div_valor').html('El valor del anticipo debe ser en formato decimal (Formato:9999.99)');
        $("#infocontratotype_valorAnticipo").val('');
    }
});          
function validaAnticipo()
{
    return /^\d+(\.\d+)?$/.test($("#infocontratotype_valorAnticipo").val());
}

jQuery(document).ready(function()
{
    jQuery('#agregar_imagen').click(function() 
    {
        let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li'); 
        imagenesCount = list.length;  
        tiposCount    = list.length;        
        var imagenesList = jQuery('#imagenes-fields-list');
        var tiposList    = jQuery('#tipos-fields-list');
        // graba la plantilla prototipo
        var newWidget = imagenesList.attr('data-prototype');
        var newWidgetTipo = tiposList.attr('data-prototype');            
        var name='__name__';
        newWidget = newWidget.replace(name, imagenesCount);            
        newWidgetTipo = newWidgetTipo.replace(name, tiposCount);
        newWidget = newWidget.replace(name, imagenesCount);
        newWidgetTipo = newWidgetTipo.replace(name, tiposCount);
        imagenesCount++;
        tiposCount++;
        // crea un nuevo elemento lista y lo añade a la lista
        var newLi = jQuery('<li></li>').html(newWidget);
        newLi.appendTo(jQuery('#imagenes-fields-list'));

        var newLi = jQuery('<li></li>').html(newWidgetTipo);             
        newLi.appendTo(jQuery('#tipos-fields-list'));
        
        return false;
    });
})

function validaTieneCorreo(e) {
  if (e.value === "SI") {
    $(".div_correo_electronico").show();
    $("#clientetype_correo_electronico").focus();
  } else {
    $(".div_correo_electronico").hide();
    $("#clientetype_correo_electronico").val("");
  }
}

function validaCorreoECDF() 
{
  let tieneCorreo = document.querySelector('input[name="clientetype[tieneCorreoElectronico]"]:checked');
 
  $('#mensaje_validaciones').addClass('campo-oculto').html("");
  if (tieneCorreo === null) return true;
  if (tieneCorreo.value === "SI")
  {
    let correoElectronico = document.getElementById("clientetype_correo_electronico");
    var RegExPattern = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    if (RegExPattern.test(correoElectronico.value) && (correoElectronico.value != ''))
    {
        Ext.Ajax.request({
          url: url_valida_correo_electronicoECDF,
          method: 'post',
          async: false,
          timeout: 400000,
          params: {
              correoElectronico: correoElectronico.value
          },
          success: function(response) 
          {
              if (response.responseText != 'NO EXISTENTE')
              {
                activeTab('idTabContactoClienteCrs');
                  if (response.responseText == 'EXISTENTE')
                  {
                  
                      $('#mensaje_validaciones').removeClass('campo-oculto')
                      .html("El correo electrónico ingresado ya fue usado en " +
                          "otra Suscripción del producto El Canal del Futbol, favor ingresar otro correo.");
                      $("#clientetype_correo_electronico").focus();                     
                  }
                  else if (response.responseText == 'ERROR')
                  {
                      $('#mensaje_validaciones').removeClass('campo-oculto')
                      .html("Se presentaron errores al validar el correo electrónico" +
                          " ingresado, favor notificar a Sistemas.");
                      $("#clientetype_correo_electronico").focus();
                  }
                  return  false;
              }
              else 
              {
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
                return  true; 
              }
              
          },
          failure: function()
          {   activeTab('idTabContactoClienteCrs');
              $('#mensaje_validaciones').removeClass('campo-oculto')
              .html("Se presentaron errores al validar el correo electrónico" +
                  " ingresado, favor notificar a Sistemas.");
                  return  false;
          }
      });
    }
    else
    {
        activeTab('idTabContactoClienteCrs');
        alertModal("Debes ingresar un nuevo correo electrónico válido");
        $("#clientetype_correo_electronico").focus();
        return  false;
    }
  } 
  return  true; 
}





/**
 * Variables globales para implementacion de representante legal 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
 var objConfRepresentLegal= {
    show:false, 
    isCordinador:false, 
}; 

/**
 * Oculta o muestra un tab representante legal, aplica para MD
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
  * Soporte EN
 * @author  Joel Broncano <jbroncano@telconet.ec>
 * @version 1.1 19-04-2023
 * 
 */
 function representanteLegalShow() {
    let strTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val(); 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    let strTipoTributario     = $('#' + formname + '_tipoTributario').val(); 
    let isEmpresa = ($('#'+ formname + '_tipoEmpresa').val()=='Publica' || $('#'+ formname + '_tipoEmpresa').val()=='Privada'); 
    let objElement = Ext.getCmp('idTabRepresentanteLegalCrs');  
    if ((prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN')&&  isEmpresa && strTipoIdentificacion=='RUC' &&  strTipoTributario =='JUR' && strIdentificacion!='')
    {  
        if (objElement) {
            objElement.tab.setVisible(true); 
        }

    }else{
         
        if (objElement) {
            objElement.tab.setVisible(false); 
        }
    }
    
 
 }
 
 /**
 * mustra o oculta el tab en el proximo evento click se refresca la pantalla
 * limpiando los representante agregads al grid, aplica para MD
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
 function representanteLegalRefresh() {
    objConfRepresentLegal.show=false;
    representanteLegalShow()
       
 }
 
 /**
 * renderiza gestor de representante legal en tab 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
function createTabPanelRepresentanteLegal () {
    objConfRepresentLegal.isCordinador = !yaTieneElRol; 
   if (objConfRepresentLegal.show== false ) {
    let tabRep = Ext.get ('tab6'); 
    let strTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val(); 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    tabRep.mask ('Cargando gestor de representante Legal espere ....');
   
    let panel =  gestorRepresentanteLegal (tabRep, strTipoIdentificacion , strIdentificacion, objConfRepresentLegal.isCordinador);
    panel.show ();
    tabRep.unmask (); 
    objConfRepresentLegal.show= true;  
   } 

 }

/**
 * valida el si requiere representante legal y carga la data en input
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 * 
 * @author  Joel Broncano <jbroncano@telconet.ec>
 * @version 1.1 19-04-2023
 * 
 */
function validarContentidoRepresentanteLegal() {   
    let strTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val(); 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    let strTipoTributario     = $('#' + formname + '_tipoTributario').val();  
    let isEmpresa = ($('#'+ formname + '_tipoEmpresa').val()=='Publica' || $('#'+ formname + '_tipoEmpresa').val()=='Privada'); 
    objConfRepresentLegal.isCordinador = !yaTieneElRol; 
    if (strTipoTributario =='JUR' && isEmpresa == false) {
        activeTab('idTabDatosPrincipalesCrs');
        alertModal('Seleccione un tipo de empresa');
        return false; 
    }else {
        if (strTipoTributario !='JUR' && isEmpresa == true) {
            activeTab('idTabDatosPrincipalesCrs');
            alertModal('Tipo empresa requiere seleccionar tipo tributario juridico');
            return false; 
        }
        
        if (objConfRepresentLegal.isCordinador && (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN') &&  strTipoIdentificacion=='RUC' &&  strTipoTributario =='JUR' && strIdentificacion!='')
        {   representanteLegalShow(); 
            var representanteLegal = getDataRepresentanteLegal();
            if (representanteLegal.length == 0 ) {            
                 activeTab('idTabRepresentanteLegalCrs');  
                document.getElementById("datosRepresentanteLegal").setAttribute("value", "");
                $("#" + formname + "_representanteLegal").val(''); 
                return false; 
            }else{ 
                document.getElementById("datosRepresentanteLegal").setAttribute("value", Ext.encode(representanteLegal));
                $("#" + formname + "_representanteLegal").val('SD'); 
                return true; 
            }
        } else{
            return true; 
        }
    }

   
}

/**
 * Valida que un selector sea requerido
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */

 function selectorRequired(identificador, status) {
    let element = $('#' + identificador+' option:selected')[0];  
    if (element.value=="Seleccione...") {
        element.value=''; 
    }
    $('#' + identificador).prop('required', status);   
 
 }
/**
 * mensaje de alerta en tab
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
* @author  Joel Broncano <jbroncano@telconet.ec>
 * @version 1.1 19-04-202
 * 
 * 
 */
 function ocultarImputRepresentanteTN() {
    if( prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN')
    {
        $('#' + formname + '_representanteLegal').prop('required', false);
        $('#' + formname + '_representanteLegal').val('SD');
        $('#' + formname + '_representanteLegal').prop('hidden', true);
        $('label[for=' + formname + '_representanteLegal]').prop('hidden', true);
    }
 }
/**
 * mensaje de alerta en tab
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
 function alertModal(strMensaje) {
    Ext.getCmp('myTabsCrs').getEl ().unmask (); 
    Ext.Msg.alert ('Alerta',strMensaje);
    
 }

 /**
 * cambiar de tab 
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
  function activeTab(strIdTab) {
    Ext.getCmp('myTabsCrs').setActiveTab( Ext.getCmp(strIdTab));
 }