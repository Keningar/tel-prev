
Ext.require([
    '*'
]);
var imagenesCount  = 0;
var tiposCount     = 0;
var strPrefEmpresa = document.getElementById('prefijoEmpresa').value;

if(strPrefEmpresa === 'MD' || strPrefEmpresa === 'EN')    
{    
    var strDecripcionFP = document.getElementById('strDecripcionFP').value;

    if(strDecripcionFP == 'DEBITO BANCARIO')
    {
        $('#addDocumentos').removeClass("campo-oculto");
    }       
}    
  
function grabar(campo)
{
    var strPrefijoEmpresa = document.getElementById('prefijoEmpresa').value;
    
    if(strPrefijoEmpresa==='MD' || strPrefijoEmpresa==='EN')
    {
        Ext.get('motivoId').dom.value   = motivos_cmb.getValue();        
        Ext.get('numeroActa').dom.value = Ext.getCmp('txtNumActa').value;
    }

    if ($('#infocontratotype_formaPagoId').val() == 3)
    {
        validarFormularioContratos();

    }
    else
    {
        $("#infocontratotype_formaPagoId").attr("enabled", "enabled");
        $("#infocontratoformapagotype_tipoCuentaId").attr("enabled", "enabled");
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("enabled", "enabled");
        $('#mensaje_validaciones').addClass('campo-oculto').html("");
    }
        
}

function habilitaFormaPago()
{
    var seleccion        = $('#infocontratotype_formaPagoId').val();
    
    if (seleccion == 3)
    {
        
        if($("#infocontratoformapagotype_tipoCuentaId").val()==1 || $("#infocontratoformapagotype_tipoCuentaId").val()==2)
        {           
            $('#tarjeta').addClass("campo-oculto");
        }
        else
        {
            $('#tarjeta').removeClass("campo-oculto");
        }      
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

function limpiaCampos() 
{
    //Contrato
    $('#infocontratoextratype_tipoContratoId').val('');
    //$('#infocontratotype_formaPagoId').val('');
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
}

function validacionesForm()
{
    var boolValida         = true;
    var seleccion          = $('#infocontratotype_formaPagoId').val();
    var strPrefijoEmpresa  = document.getElementById('prefijoEmpresa').value;
    
    if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN')    
    {
        var formaPagoActualId  = document.getElementById('formaPagoActualId').value;
        
        var motivoId  = motivos_cmb.getValue();

        if (seleccion === '3')
        {
            boolValida    = validaSubidaArchivosDig();
        }
        
        if (!boolValida)
        {
            Ext.Msg.alert('Error', 'Debe adjuntar al menos un archivo digital.');
        }

        if (motivoId === null || motivoId === '')
        {
            Ext.Msg.alert('Error', 'Favor verificar selecci\u00f3n de  motivo.');

            boolValida = false;
        }    
    }
    
    return boolValida;
}

/**
 * Se valida que tenga por lo menos un documento digital
 * 
 */
function validaSubidaArchivosDig()
{
    var archivosDigitales = $("#infodocumentotype_imagenes_0")[0].files;
    var contArchivoDigital= archivosDigitales.length;
    var boolRespuesta     = false;
     
    if (contArchivoDigital !== 0)
    {
        boolRespuesta     = true;
    }
    return boolRespuesta;
}

Ext.onReady(function() 
{
    var strPrefijoEmpresa = document.getElementById('prefijoEmpresa').value;
    
    if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN' )    
    {
        Ext.define('Detalle', 
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idPunto', mapping: 'idPunto'},
                {name: 'strlogin', mapping: 'strlogin'},
                {name: 'strOrigen', mapping: 'strOrigen'},
                {name: 'floatInstalacion', mapping: 'floatInstalacion'},
                {name: 'floatInstProm', mapping: 'floatInstProm'},    
                {name: 'floatValorInst', mapping: 'floatValorInst'},
                {name: 'floatValorInstCambio', mapping: 'floatValorInstCambio'}
            ]
        });

        storeMotivosEliminacion = Ext.create( 'Ext.data.Store',{
            total: 'total',
            proxy: {
                type: 'ajax',
                url: urlMotivoFactura,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            fields:
                [
                    {name: 'id_motivo', mapping: 'id_motivo'},
                    {name: 'nombre_motivo', mapping: 'nombre_motivo'}
                ],
            autoLoad: true
        });

        store_fac_detalle_forma_pago = new Ext.data.Store({
            pageSize: 10,
            total: 'total',
            proxy: {
                type: 'ajax',
                timeout: 900000,
                url: urlValorInstProMensuales,
                //url: grid_fac_detalle_forma_pago,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    contratoId: '',
                    motivoId: '',
                    formaPagoId: '',
                    tipoCuentaId:''
                }
            },
            fields:
                [
                    {name: 'idPunto', mapping: 'intIdPto'},
                    {name: 'strlogin', mapping: 'strLogin'},
                    {name: 'strOrigen', mapping: 'strOrigen'},
                    {name: 'floatInstalacion', mapping: 'floatValorInst'},
                    {name: 'floatInstProm', mapping: 'floatSubtotal'}
                ],
        });

        motivos_cmb = new  Ext.create('Ext.form.ComboBox', 
        {
            xtype: 'combobox',
            async: false,
            id: 'id',
            name:'motivos',
            fieldLabel: 'Motivos',
            hiddenName: 'motivos',
            emptyText: 'Seleccione el motivo...',
            store: storeMotivosEliminacion,
            displayField: 'nombre_motivo',
            valueField: 'id_motivo',
            width: 400,
            listeners:
            {         
                select: function(comp, record, index)
                {
                    gridDetalleFactura();
                }
            }
        });

        storeMotivosEliminacion.load({
            callback: function(records, operation, success) {
                if (success) 
                {
                    motivos_cmb.setValue(records [5].data.id_motivo);
                    gridDetalleFactura();
                    $('#fac_detalle_forma_pago').removeClass("campo-oculto");
                }
            }
        });    

        Ext.create('Ext.panel.Panel', {
            //bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 5,
                align: 'stretch'
            },  
            bodyStyle: {
                background: '#fff'
            },
            width: 850,
            items: [
                motivos_cmb,
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'txtNumActa',
                    fieldLabel: 'Numero Acta',
                    invalidText: 'Solo numeros',
                    maskRe: /[0-9]/,
                    maxLength: 13,                    
                    value: document.getElementById('numeroActa').value,
                    width: '50px'
                }
            ],
            renderTo: 'filtro'
        });        

        grid_fac_detalle_forma_pago = Ext.create('Ext.grid.Panel', {
            width: 750,
            height: 350,
            store: store_fac_detalle_forma_pago,
            loadMask: true,
            frame: false,
            //selModel: sm,
            viewConfig: {enableTextSelection: true},
            iconCls: 'icon-grid',
            columns: [
                {
                    header: 'Login',
                    dataIndex: 'strlogin',
                    width: 150,
                    sortable: true
                },
                {
                    header: 'Orígen',
                    dataIndex: 'strOrigen',
                    width: 150,
                    sortable: true
                },                
                {
                    header: 'Instalación',
                    dataIndex: 'floatInstalacion',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Subtotal',
                    dataIndex: 'floatInstProm',
                    width: 150,
                    sortable: true
                }
            ],
            title: 'Valores a Facturar',
            renderTo: 'grid_fac_detalle_forma_pago'
        });
    }

});
    
function validarFormularioContratos()
{
    var numeroCtaTarjeta   = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
    var titularCuenta      = $('#infocontratoformapagotype_titularCuenta').val();
    var bancoTipoCuentaId  = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
    var anioVencimiento    = $('#infocontratoformapagotype_anioVencimiento').val();
    var mesVencimiento     = $('#infocontratoformapagotype_mesVencimiento').val();
    var codigoVerificacion = $('#infocontratoformapagotype_codigoVerificacion').val();
    var prefijoEmpresa       = document.getElementById("prefijoEmpresa").value;    
    if(prefijoEmpresa === 'MD' || prefijoEmpresa === 'EN' )    
    {  
        var strNumCtaTarjEncrip  = document.getElementById("strNumCtaTarjEncrip").value;
    }
    var strNumCtaTarj      = document.getElementById("strNumCtaTarj").value; 
    var verificacion       = true;
    mensajes               = "";
    mensajes_bin           = "";

       

    if((prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN') && isNaN(numeroCtaTarjeta))
    {
        numeroCtaTarjeta = strNumCtaTarj;
    }     
    
    if (numeroCtaTarjeta == "")
    {
        mensajes     += 'Ingrese el Número de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }
    if(isNaN(numeroCtaTarjeta) && numeroCtaTarjeta !== strNumCtaTarjEncrip)
    {
 		mensajes+='Debe ingresar el número de cuenta/tarjeta de manera completa. <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html(""+mensajes+mensajes_bin+"");
        verificacion=false;       
    }
    if (titularCuenta == "")
    {
        mensajes     += 'Ingrese el Titular de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }
    
    if (verificacion)
    {
        // Función para obtener si la forma de pago es Tarjeta o Cuenta Bancaria 
        $.ajax({
            type: "POST",
            data: "bancoTipoCuentaId=" + bancoTipoCuentaId,
            url: url_validarPorFormaPago,
            success: function(msg) {
                var json = Ext.JSON.decode(msg.mensaje);
                if (msg.status == '206'){
                    
                    Ext.Msg.alert('Error - Cambio de forma de pago ', json.strMessageStatus);

                }
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
                        mensajes    += 'Ingrese el c\u00f3digo de verificaci\u00f3n de la tarjeta <br /> ';
                        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
                        verificacion = false;
                    }
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

function gridDetalleFactura()
{
    var boolDet           = false;
    var tipoCuentaId      = $('#infocontratoformapagotype_tipoCuentaId').val();
    var bancoTipoCuentaId = $('#infocontratoformapagotype_bancoTipoCuentaId').val();   
    var formaPagoId       = $('#infocontratotype_formaPagoId').val();
    var motivoId          = motivos_cmb.getValue();
    var descripcionMotivo = motivos_cmb.getRawValue();
    
    if(descripcionMotivo === 'Solicitado por el Cliente' || descripcionMotivo === 'Solicitado por Entidad Financiera')
    {
        boolDet = true;
    }

    if(boolDet)
    {
          
        if(descripcionMotivo === 'Solicitado por el Cliente' || descripcionMotivo === 'Solicitado por Entidad Financiera')
        {
            $('#fac_detalle_forma_pago').removeClass("campo-oculto");
            store_fac_detalle_forma_pago.getProxy().extraParams.motivoId          = motivoId;            
            store_fac_detalle_forma_pago.getProxy().extraParams.formaPagoId       = formaPagoId;
            store_fac_detalle_forma_pago.getProxy().extraParams.tipoCuentaId      = tipoCuentaId;
            store_fac_detalle_forma_pago.getProxy().extraParams.bancoTipoCuentaId = bancoTipoCuentaId;
            store_fac_detalle_forma_pago.load();
        }
        else
        {
            store_fac_detalle_forma_pago.removeAll();
            $('#fac_detalle_forma_pago').addClass("campo-oculto");
        }
    }
    else
    {
        store_fac_detalle_forma_pago.removeAll();
        $('#fac_detalle_forma_pago').addClass("campo-oculto");
    }

}

/********************************************************************************
 * FUNCIONES USADAS PARA EL INGRESO DE LA INFORMACION DEL CONTRATO SI SE TRATA 
 * DE UN CAMBIO DE FORMA DE PAGO
 ********************************************************************************/
var strPrefijoEmpresa = document.getElementById('prefijoEmpresa').value;
$('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
$('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
$('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
$('#infocontratoformapagotype_titularCuenta').removeAttr("required");
$("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
$("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
$("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN')    
{
    Ext.get('formaPagoActualId').dom.value = $('#infocontratotype_formaPagoId').val();
}
$('#infocontratotype_formaPagoId').change(function()
{
    var seleccion = $('#infocontratotype_formaPagoId').val();
    var strPrefijoEmpresa = document.getElementById('prefijoEmpresa').value;
    
    if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN')    
    {    
        var formaPagoActualId = document.getElementById('formaPagoActualId').value;
        if(formaPagoActualId !== seleccion)
        {
            limpiaCampos();
        }        
    }    

    if (seleccion == 3)
    {
        $('#forma_pago').removeClass("campo-oculto");
        $('#addDocumentos').removeClass("campo-oculto");
        $('#infocontratoformapagotype_tipoCuentaId').attr("required", "required");
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("required", "required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').attr("required", "required");
        $('#infocontratoformapagotype_titularCuenta').attr("required", "required");
    }
    else
    {
        //limpiaCampos();
        $('#forma_pago').addClass("campo-oculto");
        $('#tarjeta').addClass("campo-oculto");
        $('#addDocumentos').addClass("campo-oculto");
        $('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
        $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
        $('#infocontratoformapagotype_titularCuenta').removeAttr("required");
        $("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
        $("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
        $("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
    }
    
    if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN')    
    {    
        gridDetalleFactura();
    }

});

$("#infocontratoformapagotype_numeroCtaTarjeta").blur(function()
{
    var tipoCuentaId       = $('#infocontratoformapagotype_tipoCuentaId').val();
    var bancoTipoCuentaId  = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
    var numeroCtaTarjeta   = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
    var codigoVerificacion = ""; 
    var strNumCtaTarj      = document.getElementById("strNumCtaTarj").value; 
    var formaPagoId        = $('#infocontratotype_formaPagoId').val();
    
    if ($('#infocontratoformapagotype_codigoVerificacion').val() !== null)
    {
        codigoVerificacion = $('#infocontratoformapagotype_codigoVerificacion').val();
    }
    var prefijoEmpresa       = document.getElementById("prefijoEmpresa").value;
    var strNumCtaTarjEncrip  = document.getElementById("strNumCtaTarjEncrip").value;  
    var verificacion       = true;
    mensajes               = "";
    mensajes_bin           = "";
    
    if((prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' )&& isNaN(numeroCtaTarjeta))
    {
        numeroCtaTarjeta = strNumCtaTarj;
    }
    if (numeroCtaTarjeta == "")
    {
        mensajes     += 'Ingrese el Número de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }
    if(isNaN(numeroCtaTarjeta) && numeroCtaTarjeta !== strNumCtaTarjEncrip)
    {
 		mensajes+='Debe ingresar el número de cuenta/tarjeta de manera completa. <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html(""+mensajes+mensajes_bin+"");
        verificacion = false;       
    }
 
    if(verificacion)
    {
    $.ajax({
        type: "POST",
        data: "tipoCuentaId=" + tipoCuentaId + "&bancoTipoCuentaId=" + bancoTipoCuentaId + "&numeroCtaTarjeta=" + numeroCtaTarjeta + "&codigoVerificacion=" + codigoVerificacion +"&formaPagoId=" + formaPagoId,
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
                $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes_bin + "");
            }
            else
            {
                mensajes_bin = "";
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
            }
        }
    });
    }
});

//Función para mostrar los campos requeridos cuando la forma de Pago es diferente a Efectivo. 
$('#infocontratoformapagotype_tipoCuentaId').change(function()
{
    var strPrefijoEmpresa = document.getElementById('prefijoEmpresa').value;
    
    if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN')    
    {    
        gridDetalleFactura();
    }
    
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

                //Para que aparezca la primera vez el Banco guardado en base
                if(typeof primeraVez == 'undefined' && typeof intBancoTipoCuentaId != 'undefined' )
                {
                    $('#infocontratoformapagotype_bancoTipoCuentaId').val(intBancoTipoCuentaId.toString());
                    primeraVez=1;
                }

                if(typeof tipoCuentaBanco != 'undefined')
                {
                    $('#infocontratoformapagotype_bancoTipoCuentaId').val(tipoCuentaBanco);
                }

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


$('#infocontratoformapagotype_bancoTipoCuentaId').change(function()
{
    var strPrefijoEmpresa = document.getElementById('prefijoEmpresa').value;
    
    if(strPrefijoEmpresa === 'MD' || strPrefijoEmpresa === 'EN' )    
    {    
        gridDetalleFactura();
    }
});
//Función ejecuta al carga la pantalla
jQuery(document).ready(function()
{
    habilitaFormaPago();
    
    jQuery('#agregar_imagen').click(function() 
    {
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

        var newLin = jQuery('<li></li>').html(newWidgetTipo);             
        newLin.appendTo(jQuery('#tipos-fields-list'));       
        return false;
    });
    
    
});

Ext.onReady(function() 
{
    //Aparece al inicio de carga el tipoCuenta guardado
    if( typeof primeraVez == 'undefined' && typeof intIdTipoCuenta != 'undefined' )
    {
        $('#infocontratoformapagotype_tipoCuentaId').val(intIdTipoCuenta.toString()).trigger('change');
    }
});