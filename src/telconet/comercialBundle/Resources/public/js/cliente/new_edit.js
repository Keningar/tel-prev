Ext.require([
    '*'
]);
var seLimpio = false;
var storeResultadoPrefactibilidad;
var gridPrefactibilidad;
var arrayDatosCaja = new Array();
var datosResultadoConsulta;
var objTempDataPersona={}; 

flagIdentificacionCorrecta=1;
ocultarImputRepresentanteTN(); 
function validaIdentificacionEdit()
{
    currenIdentificacion=$(input).val();

        let tipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val() ; 
        $.ajax({
            type: "POST",
            data: { 
                identificacion: currenIdentificacion,
                tipoIdentificacion:tipoIdentificacion
            },
            url: url_valida_identificacion,
			beforeSend: function()
            {
				$('#img-valida-identificacion').attr("src",url_img_loader);
			},
			success: function(obj)
            {  
                var status = obj.strStatus;    
                if (status=='ERROR') { 
                    alertModal("Error: No se pudo validar la identificacion ingresada."); 
                }else {           
                    if (status== "NO") 
                        {
                            flagIdentificacionCorrecta = 1;
                            $('#img-valida-identificacion').attr("title","Identificacion disponible");
                            $('#img-valida-identificacion').attr("src",url_img_check);
                            habilitaCampos();
                        }
                        else
                        {
                            flagIdentificacionCorrecta = 0;
                            $('#img-valida-identificacion').attr("title","identificacion ya existe");
                            $('#img-valida-identificacion').attr("src",url_img_delete);
                            $(input).focus();
                            deshabilitaCampos();
                            alertModal("Identificacion ya existente. Los datos seran cargados en el formulario.");
                      } 
			   }
               representanteLegalRefresh(); 
			}
	});
}


/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @since 1.0
 * @version 1.1 28-06-2017 
 * Se agrega validación para el número de caracteres permitidos en la cédula panameña.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.2 11-11-2020 - Se muestra mensaje de validación con clientes que tienen deuda pendiente.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.3 11-05-2021 - Se muestra mensaje de validación que no permite
 *                           ingresar identificación de un cliente que pertenece a un distribuidor.
 *
 * @author Eduardo Vargas Perero <eevargas@telconet.ec>
 * @version 1.4 16-06-2023 - Se muestra mensaje de alerta para que el usuario
 *                           valide en el SRI la identificación(RUC) de un cliente antes de ser registrado.
 *
 */
function validaIdentificacion(isValidarIdentificacionTipo)
{ 
    objTempDataPersona={}; 
    yaTieneElRol = false;
    yaTieneElRolCliente = false;
    objConfRepresentLegal.isCordinador =true; 
    floatSaldoPendiente = 0;
    strDistribuidor     = 0; 
    var identificacionEsCorrecta = false;
    currenIdentificacion = $(input).val();
    if ($('#' + formname + '_tipoIdentificacion').val() !== 'Seleccione...' && $('#' + formname + '_tipoIdentificacion').val() !== '')
    {
        if (strNombrePais === 'PANAMA') {
            identificacionEsCorrecta = true;
        }
        if (strNombrePais === 'GUATEMALA' && $('#' + formname + '_tipoIdentificacion').val() === 'NIT' && currenIdentificacion === 'C/F') {
            identificacionEsCorrecta = true;
        }        
        if (/^[\w]+$/.test(currenIdentificacion) && ($('#' + formname + '_tipoIdentificacion').val() === 'PAS')) 
        {
            identificacionEsCorrecta = true;
        }
        if (/^\d+$/.test(currenIdentificacion) && ($('#' + formname + '_tipoIdentificacion').val() === 'RUC' || $('#' + formname + '_tipoIdentificacion').val() === 'CED'
                                               || $('#' + formname + '_tipoIdentificacion').val() === 'NIT'
                                               || $('#' + formname + '_tipoIdentificacion').val() === 'DPI'))
        {
            identificacionEsCorrecta = true;
        }
    }
 
    if (identificacionEsCorrecta === true) 
    {    
        ocultarDiv('diverrorident');
        let tipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val() ; 
        if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
        {
            let tempIdentificacion = $('#' + formname + '_identificacionCliente').val();
            let tempTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val();
            cleanFields(1); 
            $('#' + formname + '_identificacionCliente').val(tempIdentificacion);
            $('#' + formname + '_tipoIdentificacion').val(tempTipoIdentificacion);
             
        }
        $.ajax({
            type: "POST",
            data: { 
                identificacion: currenIdentificacion,
                tipoIdentificacion:tipoIdentificacion
            },
            url: url_valida_identificacion,
            beforeSend: function() 
            {
                $('#img-valida-identificacion').attr("src", url_img_loader);
            },
            success: function(obj) 
            {   var status = obj.strStatus;    
                if (status=='ERROR') { 

                    flagIdentificacionCorrecta = 0;

                    $("#diverrorident").html(obj.strMensaje);
                    mostrarDiv('diverrorident');
                    
                }else {         
                        if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
                        { 
                         ocultarDiv('forma_pago');
                         ocultarDiv('divroles');
                        }

                        if (status== "NO") 
                        {
                            flagIdentificacionCorrecta = 1;
                            $('#img-valida-identificacion').attr("title", "Identificacion disponible");
                            $('#img-valida-identificacion').attr("src", url_img_check);
                            habilitaCampos();
                            $("#" + formname + "_yaexiste").val('N'); 
                        } 
                        else
                        {  
                            obj = obj.objData||{};  
                            objTempDataPersona =  obj; 

                            if (obj.isRecomendado == true) 
                            {  
                                 //es data recomenda
                                 flagIdentificacionCorrecta = 1;
                                 $('#img-valida-identificacion').attr("title", "Identificacion disponible");
                                 $('#img-valida-identificacion').attr("src", url_img_check);
                                 habilitaCampos();
                                 $("#" + formname + "_yaexiste").val('N'); 

                              
                            }
                            else
                            {
                                 //existe en base 
                                 flagIdentificacionCorrecta = 0;
                                 $('#img-valida-identificacion').attr("title", "identificacion ya existe");
                                 $('#img-valida-identificacion').attr("src", url_img_delete); 
                                 $(input).focus(); 
                                 $("#" + formname + "_yaexiste").val('S'); 
                                 deshabilitaCampos();
                            }
 
                          
                            floatSaldoPendiente = obj.floatSaldoPendiente ? obj.floatSaldoPendiente:0;
                            strDistribuidor     = obj.strDistribuidor     ? obj.strDistribuidor:0;

                    
                            if(strDistribuidor!="")
                            {
                                var strMensajeDistModal = 'Identificación ingresada pertenece al cliente del distribuidor: <br><b>'+strDistribuidor+' </b></br>'+
                                                        'Por favor crear una solicitud de autorización.';
                                Ext.Msg.show({
                                    title: 'Error',
                                    msg: strMensajeDistModal,
                                    width : 420,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                                return;
                            }
                            //obtiene roles de la persona                          
                            var roles = obj.roles||"";
                            var presentaroles = '';
                            if (roles!="") { 
                                var arr_roles = roles.split("|");
                                for (var i = 0; i < arr_roles.length; i++) 
                                {
                                    if (rol == arr_roles[i]) 
                                    {
                                        yaTieneElRol = true;
                                        objConfRepresentLegal.isCordinador = false ;
                                    }
                                    if (arr_roles[i] == 'Cliente') 
                                    {
                                        yaTieneElRolCliente = true;
                                        objConfRepresentLegal.isCordinador  = false ;
                                    }
                                    if (i == (arr_roles.length - 1) && arr_roles[i])
                                    {
                                        presentaroles = arr_roles[i];
                                    }
                                    else 
                                    {
                                        if (arr_roles[i])
                                        {
                                            presentaroles = presentaroles + arr_roles[i] + ", ";
                                        }
                                    }
                                }
                            }
                           
                            if (presentaroles) 
                            {
                                $("#divroles").html("la persona ya tiene los siguientes roles en el sistema: " + presentaroles);
                                mostrarDiv('divroles');
                            }
                            if (yaTieneElRol) 
                            {
                                alertModal("Identificacion ya existente y tiene Ya el rol de " + rol + " en el sistema, Por favor ingrese otra Identificacion.");
                            }
                            else
                            {
                                if (yaTieneElRolCliente) 
                                {
                                    alertModal("Identificacion ya existente como un Cliente, Por favor ingrese otra Identificacion.");
                                } 
                                else
                                {
                                    console.log("Muerstrs jaja")
                                    if(floatSaldoPendiente > 100 && prefijoEmpresa == "TN")
                                    {
                                        var strMensajeDeudaModal = 'Esta persona o Empresa tiene una deuda de: $'+floatSaldoPendiente+' <br>'+
                                                                'Por favor crear un solicitud de reactivación.';
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: strMensajeDeudaModal,
                                            width : 420,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                        return;
                                    }
                                    if (obj.isRecomendado == true) 
                                    {  
                                         //es data recomenda
                                        alertModal("Identificacion encontrada en equifax  aun no esta registrada y no tiene rol " + rol + 
                                         ". Los datos seran cargados en el formulario para que pueda ingresarlo como " + rol + ".");
                                      
                                    }
                                    else
                                    {
                                         //existe en base 
                                        alertModal("Identificacion ya existente pero aun no tiene rol " + rol + 
                                         ". Los datos seran cargados en el formulario para que pueda ingresarlo como " + rol + ".");
                                    }

                                   
                                }
                            }
 
 
                            $("#" + formname + "_id").val(obj.id);
                            $("#" + formname + "_nombres").val(obj.nombres);
                            $("#" + formname + "_apellidos").val(obj.apellidos);
                            $("#" + formname + "_razonSocial").val(obj.razonSocial);
                            $("#" + formname + "_tipoIdentificacion").val(obj.tipoIdentificacion);
                            $("#" + formname + "_representanteLegal").val(obj.representanteLegal);

                            if (obj.tipoEmpresa) {
                                $("#" + formname + "_tipoEmpresa").val(obj.tipoEmpresa).change();  
                            } 
                            if (obj.nacionalidad) {
                                $("#" + formname + "_nacionalidad").val(obj.nacionalidad).change();   
                            } 
                            if (obj.genero) {
                                $("#" + formname + "_genero").val(obj.genero).change();   
                            } 
                            if (obj.tituloId) {                              
                               $("#" + formname + "_tituloId").val(obj.tituloId).change(); 
                            } 
                            if (obj.estadoCivil) {
                                $("#" + formname + "_estadoCivil").val(obj.estadoCivil).change();   
                            } 

                       
                            if (obj.tipoTributario)
                            {
                                $('#' + formname + '_tipoTributario').val(obj.tipoTributario).change();                                                      
                            }
                          
                            if (obj.origenIngresos)
                            {
                                $('#' + formname + '_origenIngresos').val(obj.origenIngresos).change();  
                            }
                           
                           
                            
                            if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
                            {
                                $("#" + formname + "_estadoLegal").val(obj.estadoLegal);  
                                $("#" + formname + "_fechaInicioCompania").val(obj.fechaInicioCompania);  
                                $("#" + formname + "_dataRecomendacion").val(obj.dataRecomendacion);  
     
                                Ext.ComponentQuery.query ('combobox[name='+formname + '_direccionTributaria]')[0].setValue(obj.direccionTributaria);
                            }else{
                                $("#" + formname + "_direccionTributaria").val(obj.direccionTributaria);
                            }

                            
                            
                            if(prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
                            {
                                $("#" + formname + "_idreferido").val(obj.referidoId);
                                $("#" + formname + "_referido").val(obj.referidoNombre); 
                               
                                //formas de pagos 
                                if (obj.formaPagoId) 
                                {
                                    $('#infopersonaempformapagotype_formaPagoId').val(obj.formaPagoId).change(); 

                                    if (obj.tipoCuentaId) 
                                    {     
                                        $('#infopersonaempformapagotype_tipoCuentaId').val(obj.tipoCuentaId).change();  
                                        if (obj.bancoTipoCuentaId)
                                        {
                                            banco = obj.bancoTipoCuentaId;   
                                        }                                             
                                    }
                                }
                                             
                                       

                                let  arrayRecomendoDirecciones          = obj.arrayRecomendoDirecciones||[];    
                                let  arrayRecomendoTelefonoFijo         = obj.arrayRecomendoTelefonoFijo||[];
                                let  arrayRecomendacionesTarjetaCredito =   obj.arrayRecomendacionesTarjetaCredito ||[];
                                let  arrayRecomendacionesIngresos       =   obj.arrayRecomendacionesIngresos ||[];


                                   
                                objStatesDireccionesRecomendadas.loadData(arrayRecomendoDirecciones); 
                                objStatesRecomendoTelefonoFijo.loadData(arrayRecomendoTelefonoFijo);  


                                let mapRecomendacionesIngresos = []; 
                                for (let index = 0; index < arrayRecomendacionesIngresos .length; index++) {
                                    const e= arrayRecomendacionesIngresos [index];
                                    let n= index+1;
                                    mapRecomendacionesIngresos.push({
                                        fieldLabel:  e.titulo,
                                        name: 'fieldIngresosRecomendada'+n, 
                                        margin: 5,
                                        readOnly: true,
                                        value: e.descripcion,
                                    });                            
                                }     
                                Ext.getCmp('idIngresoRecomendadoIngresos').removeAll(true);
                                Ext.getCmp('idIngresoRecomendadoIngresos').add(mapRecomendacionesIngresos);



                                let mapRecomendacionesTarjetaCredito = []; 
                                for (let index = 0; index < arrayRecomendacionesTarjetaCredito .length; index++) {
                                    const e= arrayRecomendacionesTarjetaCredito[index];
                                    let n= index+1;
                                    mapRecomendacionesTarjetaCredito.push({
                                        fieldLabel:  '',//e.titulo,
                                        name: 'fieldTarjetaRecomendada'+n, 
                                        margin: 5,
                                        readOnly: true,
                                        value: e.titulo,//e.descripcion,
                                    });                            
                                }     
                                Ext.getCmp('idIngresoRecomendadoTarjetaCredito').removeAll(true);
                                Ext.getCmp('idIngresoRecomendadoTarjetaCredito').add(mapRecomendacionesTarjetaCredito); 
                        

                            }
                            
                            $("#" + formname + "_id").val(obj.id);

                            var fechaNac1 = obj.fechaNacimiento;
                            if (fechaNac1) {
                                var  arrFechaNacimiento = fechaNac1.split(' ');
                                var fechaNac2 = arrFechaNacimiento[0];
                                var arrFechaN = fechaNac2.split('/');
                                $("#" + formname + "_fechaNacimiento_day").val(arrFechaN[0] * 1);
                                $("#" + formname + "_fechaNacimiento_month").val(arrFechaN[1] * 1);
                                $("#" + formname + "_fechaNacimiento_year").val(arrFechaN[2]);
                            }
         
                            esEmpresa();
                            storeFormaContactoPersona.load({params: {personaid: obj.id}});
                          
                            $("#" + formname + "_yaexiste").val('S');
                        }

                        if (isValidarIdentificacionTipo && typeof validarIdentificacionTipo == typeof Function)
                        {
                            validarIdentificacionTipo();
                        }
                }                
                representanteLegalRefresh(); 
            }
        });
    }
    else 
    {
        if ($('#' + formname + '_tipoIdentificacion').val() === 'Seleccione...' || $('#' + formname + '_tipoIdentificacion').val() === '') 
        {
            mostrarDiv('dividentificacion');
            $("#dividentificacion").html("Antes de ingresar identificación seleccione tipo de identificación");
        }
        else 
        {
            $("#diverrorident").html("Identificación es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales");
            mostrarDiv('diverrorident');
        }
        $(input).val("");
    }
}

function esEmpresa() 
{
    if ($('#' + formname + '_tipoEmpresa').val() == 'Publica' || $('#' + formname + '_tipoEmpresa').val() == 'Privada') 
    {
        ocultarDiv('div_datos_persona_1');
        ocultarDiv('div_datos_persona_2');
        mostrarDiv('div_razon_social');
        $('#' + formname + '_razonSocial').attr('required', 'required');
        $('#' + formname + '_representanteLegal').attr('required', 'required');
        $('label[for=' + formname + '_representanteLegal]').html('* Representante Legal:');
        $('label[for=' + formname + '_representanteLegal]').addClass('campo-obligatorio');
        $('#' + formname + '_nombres').removeAttr('required');
        $('#' + formname + '_apellidos').removeAttr('required');
        $('#' + formname + '_genero').removeAttr('required');
        $('#' + formname + '_tituloId').removeAttr('required');
        $('#' + formname + '_estadoCivil').removeAttr('required');
        $('#' + formname + '_nombres').val('');
        $('#' + formname + '_apellidos').val('');
        if (objTempDataPersona.nombres) {
            $('#' + formname + '_nombres').val(objTempDataPersona.nombres);
        }
        if (objTempDataPersona.apellidos) {
            $('#' + formname + '_apellidos').val(objTempDataPersona.apellidos);
        }
        $('#' + formname + '_fechaNacimiento_day').removeAttr('required');
        $('#' + formname + '_fechaNacimiento_month').removeAttr('required');
        $('#' + formname + '_fechaNacimiento_year').removeAttr('required');
    }
    else
    {
        mostrarDiv('div_datos_persona_1');
        mostrarDiv('div_datos_persona_2');
        ocultarDiv('div_razon_social');
        $('#' + formname + '_razonSocial').removeAttr('required');
        $('label[for=' + formname + '_representanteLegal]').removeClass('campo-obligatorio');
        $('label[for=' + formname + '_representanteLegal]').html('Representante Legal:');
        $('#' + formname + '_representanteLegal').removeAttr('required');
        $('#' + formname + '_nombres').attr('required', 'required');
        $('#' + formname + '_apellidos').attr('required', 'required');
        $('#' + formname + '_genero').attr('required', 'required');
        $('#' + formname + '_tituloId').attr('required', 'required');
        $('#' + formname + '_estadoCivil').attr('required');
        $('#' + formname + '_razonSocial').val('');
        if (objTempDataPersona.razonSocial) {
            $('#' + formname + '_razonSocial').val(objTempDataPersona.razonSocial);
        }
        $('#' + formname + '_fechaNacimiento_day').attr('required', 'required');
        $('#' + formname + '_fechaNacimiento_month').attr('required', 'required');
        $('#' + formname + '_fechaNacimiento_year').attr('required', 'required');
        $('label[for=' + formname + '_fechaNacimiento_day]').addClass('campo-obligatorio');
        $('label[for=' + formname + '_fechaNacimiento_month]').addClass('campo-obligatorio');
        $('label[for=' + formname + '_fechaNacimiento_year]').addClass('campo-obligatorio');
        $('label[for=' + formname + '_fechaNacimiento]').html('* Fecha Nacimiento:');
    }
   

} 

function deshabilitaCampos()
{
    $('#'+formname+'_nombres').attr('readonly','readonly');
    $('#'+formname+'_apellidos').attr('readonly','readonly');
    $('#'+formname+'_tipoEmpresa').attr('disabled','disabled');
    $('#'+formname+'_razonSocial').attr('readonly','readonly');
    $('#'+formname+'_tipoIdentificacion').attr('disabled','disabled');
    
    if(prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
    {
        if($("#"+formname+"_referido").val()!=='')
        {
            ocultarDiv("imgreferido");
        }
    }
}

function habilitaCampos()
{
    $('#'+formname+'_direccionTributaria').removeAttr('readonly');
    $('#'+formname+'_nombres').removeAttr('readonly');
    $('#'+formname+'_apellidos').removeAttr('readonly');				
    $('#'+formname+'_tipoEmpresa').removeAttr('disabled');
    $('#'+formname+'_razonSocial').removeAttr('readonly');
    $('#'+formname+'_tipoIdentificacion').removeAttr('disabled');
    $('#'+formname+'_tipoTributario').removeAttr('disabled');
    $('#'+formname+'_nacionalidad').removeAttr('disabled');
    $('#'+formname+'_representanteLegal').removeAttr('readonly');		
    $('#'+formname+'_genero').removeAttr('disabled');
    $('#'+formname+'_tituloId').removeAttr('disabled');
    $('#'+formname+'_estadoCivil').removeAttr('disabled');
    //cambios DINARDARP - se agrega campo origenes de ingresos
    $('#'+formname+'_origenIngresos').removeAttr('disabled');
    $('#'+formname+'_fechaNacimiento_day').removeAttr('disabled');				
    $('#'+formname+'_fechaNacimiento_month').removeAttr('disabled');				
    $('#'+formname+'_fechaNacimiento_year').removeAttr('disabled');

    if(prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
    {
        mostrarDiv("imgreferido");	
    }
}

function validaIdentificacionCorrecta()
{
	if(flagIdentificacionCorrecta==1)
    {
		return true;
	}
    else
    {
		alertModal("Identificacion ya existente. Favor Corregir para poder ingresar el Nuevo Cliente");
		$(input).focus();
		return false;
	}
}
 
Ext.onReady(function() 
{

    Ext.define('PersonaFormasContactoModel', 
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto', type: 'string'},
            {name: 'valor', type: 'string'}
        ]
    });

    Ext.define('FormasContactoModel', 
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    // create the Data Store
    storeFormaContactoPersona = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
            proxy: {
                type: 'ajax',
                // load remote data using HTTP
                url: url_formas_contacto_persona,
                reader:
                    {
                        type: 'json',
                        root: 'personaFormasContacto',
                        // records will have a 'plant' tag
                        totalProperty: 'total'
                    },
                extraParams:
                    {
                        personaid: ''
                    },
                simpleSortMode: true
            },
            listeners:
                {
                    beforeload: function(store)
                    {
                        store.getProxy().extraParams.personaid = personaid;
                    }
                }
        });

    var storeFormasContacto = Ext.create('Ext.data.Store', 
    {
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
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', 
    {
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
    grid =  {
        store: storeFormaContactoPersona,
        renderTo: Ext.get('lista_formas_contacto_grid'),
        width: 600,
        height: 300,
        title: '',
        columns:
            [
                {
                    text: 'Forma Contacto',
                    header: 'Forma Contacto',
                    dataIndex: 'formaContacto',
                    width: 150,
                    editor: new Ext.form.field.ComboBox({
                        typeAhead: true,
                        triggerAction: 'all',
                        selectOnTab: true,
                        id: 'id',
                        name: 'formaContacto',
                        valueField: 'descripcion',
                        displayField: 'descripcion',
                        store: storeFormasContacto,
                        lazyRender: true,
                        editable: false,
                        listClass: 'x-combo-list-small'
                    })
                },
                {
                    text: 'Valor',
                    dataIndex: 'valor',
                    width: 400,
                    align: 'right',
                    editor:
                        {
                            width: '80%',
                            xtype: 'textfield',
                            fieldStyle: 'text-transform: lowercase',
                            allowBlank: false,                                
                            listeners:{
                                          // Al salir del campo ponemos todo en minuscula
                                          blur: function(field, e) 
                                          {
                                               field.setValue(field.getValue().toLowerCase());
                                          }
                                      }                                
                        }
                },
                {
                    xtype: 'actioncolumn',
                    width: 45,
                    sortable: false,
                    items:
                        [
                            {
                                iconCls: "button-grid-delete",
                                tooltip: 'Borrar Forma Contacto',
                                handler: function(grid, rowIndex)
                                { 
                                    storeFormaContactoPersona.removeAt(rowIndex);
                                                                          
                                }
                            }
                        ]
                }
            ],
        selModel:
            {
                selType: 'cellmodel'
            },
        tbar: [
            {
                text: 'Agregar',
                handler: function()
                {
                    var boolError = false;
                    var indice = 0;
                    for (var i = 0; i < storeFormaContactoPersona.getCount(); i++)
                    {
                        variable = storeFormaContactoPersona.getAt(i).data;
                        boolError = trimAll(variable['formaContacto']) == '';

                        if (boolError)
                        {
                            break;
                        }
                        else
                        {
                            boolError = trimAll(variable['valor']) == '';
                            if (boolError)
                            {
                                indice = 1;
                                break;
                            }
                        }
                    }
                    if (!boolError)
                    {
                        var r = Ext.create('PersonaFormasContactoModel',
                            {
                                idPersonaFormaContacto: '',
                                formaContacto: '',
                                valor: ''
                            });
                        storeFormaContactoPersona.insert(0, r);
                    }
                    cellEditing.startEditByPosition({row: 0, column: indice});
                }
            }           
        ],
        plugins: [cellEditing]       
        
    };
      
    if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN'){
        grid.tbar.push({
            text: 'Agregar información recomendada',
            handler: function()
            {
                createModelInformacionRecomendada();                         
            }
        });  
    }
    grid  = Ext.create('Ext.grid.Panel', grid );
    storeFormaContactoPersona.load();
    
     let arrayConfigPanel= {
        height: 510,
        id:'myTabs',
        renderTo: 'my-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab1',
             id:'idTabDatosPrincipales', 
             title:'Datos Principales'},
          
        ]            
    };

   

    if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN'){  
        arrayConfigPanel.items.push({
            contentEl:'tab3', 
            id:'idTabRecomendaciones', 
            title:'Recomendaciones',
            listeners:{
            activate: function(tab){
                grid.view.refresh(); 
                Ext.getCmp('idTabContactoCliente').enable ();
             }
          }
        });       
    }


       arrayConfigPanel.items.push({
        contentEl:'tab2', 
        id:'idTabContactoCliente', 
        title:'Formas de contacto',
        listeners:{
           activate: function(tab){
               grid.view.refresh();
           }
         }
       }); 

          
       if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN'){ 
        arrayConfigPanel.items.push ({
          contentEl: 'tab4',
          id:'idTabRepresentanteLegal', 
          title: 'Representante Legal',
          hidden:true,
          listeners: {
            activate: function (tab) {
              createTabPanelRepresentanteLegal ();
            },
          },
        });
    

        arrayConfigPanel.items.push({
            contentEl:'tab5',
            id:'idTabpPrefactibilidad', 
            title:'Prefactibilidad'
        });
       }

    new Ext.TabPanel(arrayConfigPanel);
    
    
    storeFormaContactoPersona.on('load', function()
    {
        if (typeof formasDeContacto !== 'undefined' && formasDeContacto != '')
        {
            arrayFormasContacto = formasDeContacto.split(',');
            for (i = 0; i < arrayFormasContacto.length; i += 3)
            {
                var registro =
                    {
                        'idPersonaFormaContacto': arrayFormasContacto[i],
                        'formaContacto': arrayFormasContacto[i + 1],
                        'valor': arrayFormasContacto[i + 2]
                    };
                var rec = new PersonaFormasContactoModel(registro);
                storeFormaContactoPersona.add(rec);
            }
        }
        
        for(i = 0; i < storeFormaContactoPersona.data.length; i++)
        {
            if(storeFormaContactoPersona.data.items[i].data.formaContacto == "")
            {
                storeFormaContactoPersona.removeAt(i); 
            }
        }
        
    });
        
    cleanFields(0);

    //Combos punto, parroquia, canton,sector- PREFACTIBILIDAD
    Ext.define('ListModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', type:'int'},
            {name:'nombre', type:'string'}
        ]
    });

    storePtosCobertura = Ext.create('Ext.data.Store',
        {
            id: 'storePtosCobertura',
            autoLoad: true,
            model: 'ListModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_puntoscobertura,
                    reader:
                        {
                            type: 'json',
                            root: 'jurisdicciones'
                        }
                }
        });

    storeCantones = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: url_cantones,
                    reader:
                        {
                            type: 'json',
                            root: 'cantones'
                        }
                }
        });

    storeParroquia = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_parroquias,
                    reader:
                        {
                            type: 'json',
                            root: 'parroquias'
                        }
                }
        });

    storeSectores = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: "ListModel",
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_sectores,
                    reader:
                        {
                            type: 'json',
                            root: 'sectores'
                        }
                }
        });
        
    storePtosCobertura.on('load', function()
    {
        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
        if (typeof ptoCoberturaId !== typeof undefined && ptoCoberturaId != '')
        {
            rec = storePtosCobertura.findRecord('id', ptoCoberturaId);
            if(rec != null)
            {
                combo_ptoscobertura.select(parseInt(ptoCoberturaId), true);
                $('#infopuntoextratype_ptoCoberturaId').val(ptoCoberturaId);
                storeCantones.proxy.extraParams = {idjurisdiccion: ptoCoberturaId};
                storeCantones.load();
            }
        }
    });


    storeCantones.on('load', function()
    {
        if (typeof cantonId !== typeof undefined && cantonId != '')
        {
            rec = storeCantones.findRecord('id', cantonId);
            if(rec != null)
            {
                Ext.ComponentQuery.query('combobox[name=idcanton]')[0].setDisabled(false);
                combo_cantones.select(parseInt(cantonId), true);
                $('#infopuntoextratype_cantonId').val(cantonId);
                storeParroquia.proxy.extraParams = {idcanton: cantonId};
                storeParroquia.load();
            }
        }
    });

    storeParroquia.on('load', function()
    {
        if (typeof parroquia !== typeof undefined && parroquia != '')
        {
            rec = storeParroquia.findRecord('id', parroquia);
            if(rec != null)
            {
                Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                combo_parroquia.select(parseInt(parroquia), true);
                $('#infopuntoextratype_parroquiaId').val(parroquia);
                storeSectores.proxy.extraParams = {idparroquia: parroquia};
                storeSectores.load();
            }
        }
    });

    storeSectores.on('load', function()
    {
        if (typeof sectorId !== typeof undefined && sectorId != '')
        {
            rec = storeSectores.findRecord('id', sectorId);
            if(rec != null)
            {
                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);
                combo_sector.select(parseInt(sectorId), true);
                $('#infopuntoextratype_sectorId').val(sectorId);
            }
        }
    });

    combo_ptoscobertura = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            id: 'idptocobertura',
            name: 'idptocobertura',
            store: storePtosCobertura,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Pto Cobertura',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: '',
            width: 250,
            allowBlank: false,
            renderTo: 'combo_ptoscobertura',
            queryMode: 'local',
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].setDisabled(false);
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(true);
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                        $('#infopuntoextratype_sectorId').val('');
                        $('#infopuntoextratype_ptoCoberturaId').val(combo.getValue());
                        storeCantones.proxy.extraParams = {idjurisdiccion: combo.getValue()};
                        storeCantones.load();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                    }}
            }

        });
    
    var strLblCanton    = 'Seleccione Cant\u00F3n';
    var strLblParroquia = 'Seleccione Parroquia';
    var strLblSector    = 'Seleccione Sector';
    
    combo_cantones = new Ext.form.ComboBox({
            id: 'idcanton',
            name: 'idcanton',
            labelAlign : 'left',
            fieldLabel: '',
            anchor: '200%',
            disabled: true,
            width: 250,
            emptyText: strLblCanton,
            store: storeCantones,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            renderTo: 'combo_cantones',
            listeners:{
                select:{fn:function(combo, value) {
                   Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                    Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                    $('#infopuntoextratype_cantonId').val(combo.getValue());
                    storeParroquia.proxy.extraParams = {idcanton: combo.getValue()};
                    storeParroquia.load();
                    $('#canton').val(combo.getRawValue());
                    if($('#canton').val()=='GUAYAQUIL' || $('#canton').val()=='QUITO' ){
                                        $('#labelLatitud').addClass('campo-obligatorio');
                                                            $('#grados_la').attr('required','required');
                                                            $('#minutos_la').attr('required','required');
                                                            $('#segundos_la').attr('required','required');
                                                            $('#decimas_segundos_la').attr('required','required');
                                                            $('#latitud').attr('required','required');
                                                            $('#labelLongitud').addClass('campo-obligatorio');
                                                            $('#grados_lo').attr('required','required');
                                                            $('#minutos_lo').attr('required','required');
                                                            $('#segundos_lo').attr('required','required');
                                                            $('#decimas_segundos_lo').attr('required','required');
                                                            $('#longitud').attr('required','required');
                    }else
                    {
                                        $('#labelLatitud').removeClass('campo-obligatorio');
                                                            $('#labelLongitud').removeClass('campo-obligatorio');
                                                            $('#grados_la').removeAttr('required');
                                                            $('#minutos_la').removeAttr('required');
                                                            $('#segundos_la').removeAttr('required');
                                                            $('#decimas_segundos_la').removeAttr('required');
                                                            $('#latitud').removeAttr('required');
                                                            $('#grados_lo').removeAttr('required');
                                                            $('#minutos_lo').removeAttr('required');
                                                            $('#segundos_lo').removeAttr('required');
                                                            $('#decimas_segundos_lo').removeAttr('required');
                                                            $('#longitud').removeAttr('required');										
                    }	
					
					
                },
            beforeshow: function(picker)
                {
                    picker.minWidth = picker.up('combobox').getSize().width;
                }
            },
                change: {fn:function( combo, newValue, oldValue ){				
                }}
            }            
    });    
    
    combo_parroquia = new Ext.form.ComboBox(
        {
            name: 'idparroquia',
            id: 'idparroquia',
            labelAlign: 'left',
            fieldLabel: '',
            disabled: true,
            width: 250,
            emptyText: strLblParroquia,
            store: storeParroquia,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            renderTo: 'combo_parroquia',
            matchFieldWidth: true,
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {
                                if(strNombrePais !== 'GUATEMALA')
                                {
                                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);                                    
                                    storeSectores.proxy.extraParams = {idparroquia: combo.getValue()};
                                    storeSectores.load();                                    
                                }
                                $('#infopuntoextratype_parroquiaId').val(combo.getValue());

                            }
                        },
                    change:
                        {
                            fn: function(combo, newValue, oldValue)
                            {
                                if(strNombrePais !== 'GUATEMALA')
                                {
                                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                                }
                            }
                        }
                }
        });    
		
    combo_sector = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storeSectores,
                labelAlign : 'left',
                name: 'idsector',
		valueField:'id',
                displayField:'nombre',
                fieldLabel: '',
		width: 250,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: false,	
                emptyText: strLblSector,
                disabled: true,
            renderTo: 'combo_sector',                
		listeners: {
                select:{fn:function(combo, value) {
                    $('#infopuntoextratype_sectorId').val(combo.getValue());
                    ocultarDiv('div_errorsector');
                }},
                    click: {
                        element: 'el',
                        fn: function(){ 
                        }
                    }			
		}
            });
    //FIN COMBOS
    
    Ext.ComponentQuery.query('combobox[name=idptocobertura]')[0].setDisabled (true); 
    mostrarResultadosPrefactibilidad();
    if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN') {
       createSelectDireccionRecomendada();
       Ext.getCmp('idTabContactoCliente').disable(); 
       createTabPanelRecomendacion();   
  
    }

    //Mensaje de Prevención para validar manualmente el RUC desde el SRI
    const checkSri = document.querySelector('#check_sri');
    checkSri.addEventListener('change', async (e) => {
        if (!e.target.checked) return;
        alertModal('Por favor, valide el RUC en la página del SRI para continuar.');
    });

});

function mostrarResultadosPrefactibilidad()
{
    
    //Define Model
    Ext.define('ResultadoCajasPrefactibilidadModel', 
    {
        extend: 'Ext.data.Model',
        fields: [
                {name: 'idCaja', type: 'int'},
                {name: 'nombreCaja', type: 'string'},
                {name: 'nombreConector', type: 'string'},
                {name: 'distancia', type: 'float'},
                {name: 'numPuertosDisponibles', type: 'int'}
        ]
    });
        
        
     // Resultados Consulta
    datosResultadoConsulta= arrayDatosCaja;
        
    //Create the Data Store
    storeResultadoPrefactibilidad = Ext.create('Ext.data.Store',
        {
            model: 'ResultadoCajasPrefactibilidadModel',
            data: datosResultadoConsulta         
        });
        
    //Create Grid Prefactibilidad
    gridPrefactibilidad = Ext.create('Ext.grid.Panel',
        {
            id: 'gridPrefactibilidad',
            store: storeResultadoPrefactibilidad,
            renderTo: 'lista_consulta_prefactibilidad',
            autoHeight: true,
            width: 925,
            height: 150,
            defaults: 
                {
                    bodyStyle: 'padding:10px'
                },
                collapsed: false,
                title: 'Información de Factibilidad',
                columns:
                    [
                        new Ext.grid.RowNumberer(),
                        {
                            header: 'Splitter',
                            width: 325,
                            dataIndex: 'nombreConector'
                        
                        },
                        {
                            header: 'Caja',
                            width: 300,
                            dataIndex: 'nombreCaja'
                            
                        },
                        {
                            header: 'Distancia',
                            width: 150,
                            dataIndex: 'distancia'
                            
                        },
                        {
                            header: 'Num. de Puertos',
                            width: 150,
                            dataIndex: 'numPuertosDisponibles'
                            
                        }
                    ]
        }
    );

}

function showGridPrefactibilidad()
{
    datosResultadoConsulta=arrayDatosCaja;
    storeResultadoPrefactibilidad = Ext.create('Ext.data.Store',
        {
            model: 'ResultadoCajasPrefactibilidadModel',
            data: datosResultadoConsulta         
        });
    gridPrefactibilidad.reconfigure(storeResultadoPrefactibilidad,null);

}

function cleanGrid()
{
    storeResultadoPrefactibilidad.removeAll();
    gridPrefactibilidad.getStore().removeAll();
}

 

function trimAll(texto)
{
    return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
}

function validarContentidoFormaContacto()
{ 
     
    let campo = '#' + formname + '_formas_contacto'; 
    let array_data = new Array();
    let variable = '';
    let valoresVacios = false;

    for (let i = 0; i < grid.getStore().getCount(); i++)
    {
        variable = grid.getStore().getAt(i).data;
        for (let key in variable)
        {
            let valor = variable[key];
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
    {
        Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
        alertModal('No hay formas de contacto aun ingresadas.');
        $(campo).val('');   
        return false;
    }
    else
    {
        if (valoresVacios == true)
        {
            Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
            alertModal('Hay formas de contacto que tienen valor vacio, por favor corregir.');
            $(campo).val('');
            return false;
        }
    }
    return true; 
}

function cleanFields(inicio)
{    
    if(inicio == 1)
    {


        seLimpio = true;
        objTempDataPersona = {}; 
        $('#div_msg_error').html('');

        $('#' + formname + '_tipoIdentificacion').removeAttr('disabled');

        validateFormRequired(); 

        if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
        {
            $('#infopersonaempformapagotype_formaPagoId  option')[0].selected = true;              

            $('#' + formname + '_referido').val('');
            $('#' + formname + '_idreferido').val('');
            $('#' + formname + '_idperreferido').val('');

            Ext.ComponentQuery.query('combobox[name=idptocobertura]')[0].reset();
            Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
            Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
            Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
            $('#grados_la').val("");
            $('#minutos_la').val("");
            $('#segundos_la').val("");
            $('#decimas_segundos_la').val("");
            $('#latitud').val("T");
            $('#grados_lo').val("");
            $('#minutos_lo').val("");
            $('#segundos_lo').val("");
            $('#decimas_segundos_lo').val("");
            $('#longitud').val("T");
            ocultarDiv('lista_consulta_prefactibilidad');
            ocultarDiv('labelInfoPrefactibilidad');
            cleanGrid(); 

            ocultarDiv('forma_pago');
            ocultarDiv('divroles');
            Ext.getCmp('idIngresoRecomendadoIngresos').removeAll(true);
            Ext.getCmp('idIngresoRecomendadoTarjetaCredito').removeAll(true);
        }

        $('label[for=' + formname + '_numeroConadis]').hide();
        $('#' + formname + '_numeroConadis').hide();

        $('#' + formname + '_tipoIdentificacion option')[0].selected = true;
        $('#' + formname + '_tipoEmpresa option')[0].selected = true;
        $('#' + formname + '_tipoTributario option')[0].selected = true;
        $('#' + formname + '_nacionalidad option')[0].selected = true;
        $('#' + formname + '_genero option')[0].selected = true;
        $('#' + formname + '_tituloId option')[0].selected = true;

        $('#' + formname + '_fechaNacimiento_day option')[0].selected = true;
        $('#' + formname + '_fechaNacimiento_month option')[0].selected = true;
        $('#' + formname + '_fechaNacimiento_year option')[0].selected = true;

        $('#' + formname + '_estadoCivil option')[0].selected = true;

        $('#' + formname + '_identificacionCliente').val('');
        if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
        {
            Ext.ComponentQuery.query ('combobox[name='+formname + '_direccionTributaria]')[0].setValue('');
            $('#' + formname + '_fechaInicioCompania').val('');
            $('#' + formname + '_estadoLegal').val('');    
           
        }else{
            $('#' + formname + '_direccionTributaria').val('');
        }
     
        $('#' + formname + '_nombres').val('');
        $('#' + formname + '_apellidos').val('');
        $('#' + formname + '_razonSocial').val('');
        $('#' + formname + '_representanteLegal').val('');
        $('#' + formname + '_dataRecomendacion').val('');
        
        $('label[for=' + formname + '_origenIngresos]').hide();
        $('#' + formname + '_origenIngresos').hide();
 
        storeFormaContactoPersona.removeAll();
        validarTipoEmpresa();
        representanteLegalRefresh(); 
    }
}

 

function validateFormRequired()
{
    Ext.getCmp('myTabs').setActiveTab(0); 
    $('#' + formname + '_formas_contacto').prop('required', false);
    selectorRequired(formname + '_tipoIdentificacion', true);
    $('#' + formname + '_identificacionCliente').prop('required', true); 
    $('#' + formname + '_direccionTributaria').prop('required', true);  
    selectorRequired(formname + '_tipoTributario', true);

    if ($('#' + formname + '_tipoTributario').val() == 'NAT')
    {
        selectorRequired( formname + '_origenIngresos', true);
    }
    else
    {
        $('#' + formname + '_origenIngresos').prop('required', false);
    }
    
    if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
    {
        selectorRequired( 'infopersonaempformapagotype_formaPagoId', true);
    }

    if (prefijoEmpresa == 'TN')
    {
        selectorRequired(  formname + '_contribuyenteEspecial', true);
        selectorRequired(  formname + '_pagaIva', true);
        selectorRequired(  formname + '_esPrepago', true);
        selectorRequired(  formname + '_tieneCarnetConadis', true);
        
        if ($('#' + formname + '_tieneCarnetConadis').val() =='S') 
        {
            $('#' + formname + '_numeroConadis').prop('required', true);
        }

        selectorRequired(  formname + '_idOficinaFacturacion', true);
    }

    selectorRequired(formname + '_nacionalidad', true);

    if($('#' + formname + '_tipoEmpresa option:selected').index() != 0) // ES EMPRESA
    {
        $('#' + formname + '_razonSocial').prop('required', true);
        $('#' + formname + '_representanteLegal').prop('required', true);
        
        $('#' + formname + '_genero').prop('required', false);
        $('#' + formname + '_tituloId').prop('required', false);
        $('#' + formname + '_nombres').prop('required', false);
        $('#' + formname + '_apellidos').prop('required', false);
        
        $('#' + formname + '_genero option')[0].selected = true;
        $('#' + formname + '_tituloId option')[0].selected = true;
        $('#' + formname + '_nombres').val('');
        $('#' + formname + '_apellidos').val('');
        $('#' + formname + '_fechaNacimiento_day option')[0].selected = true;
        $('#' + formname + '_fechaNacimiento_month option')[0].selected = true;
        $('#' + formname + '_fechaNacimiento_year option')[0].selected = true;
        
        $('#' + formname + '_fechaNacimiento').prop('required', false);
        $('#' + formname + '_fechaNacimiento_day').prop('required', false);
        $('#' + formname + '_fechaNacimiento_month').prop('required', false);
        $('#' + formname + '_fechaNacimiento_year').prop('required', false);
    }
    else
    {

        if( (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN') &&  ($('#' + formname + '_tipoTributario').val() == 'JUR'))
        {
            selectorRequired(formname + '_tipoEmpresa', true);
        }

        selectorRequired(formname + '_genero', true);
        selectorRequired(formname + '_tituloId', true) ; 
        $('#' + formname + '_nombres').prop('required', true);
        $('#' + formname + '_apellidos').prop('required', true);

        $('#' + formname + '_razonSocial').prop('required', false);
        $('#' + formname + '_representanteLegal').prop('required', false);

        if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
        {
            $('#' + formname + '_fechaNacimiento').prop('required', true);
            $('#' + formname + '_fechaNacimiento_day').prop('required', true);
            $('#' + formname + '_fechaNacimiento_month').prop('required', true);
            $('#' + formname + '_fechaNacimiento_year').prop('required', true);
            $('#' + formname + '_fechaInicioCompania').val('');
            $('#' + formname + '_estadoLegal').val('');
        }
        
        $('#' + formname + '_tipoEmpresa option')[0].selected = true;
        $('#' + formname + '_razonSocial').val('');
        $('#' + formname + '_representanteLegal').val('');
        
    } 
    
     ocultarImputRepresentanteTN(); 
   
    
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
function validaFormasContacto(){
    var validacionFromasContacto = Utils.validaFormasContacto(grid);
    if (!validacionFromasContacto)
    {
        Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
    }
    
    return validacionFromasContacto;
}

/**
 * Documentación para la función 'validacionesForm'.
 * @author Version Original
 * @version 1.0
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.1 11-11-2020 - Se muestra mensaje de validación con clientes que tienen deuda pendiente.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.2 11-05-2021 - Se muestra mensaje de validación que no permite
 *                           ingresar identificación de un cliente que pertenece a un distribuidor.
 *
 */
function validacionesForm() 
{
    Ext.getCmp('myTabs').setActiveTab(0); 
    var intEdad = 0;
    var identificacionCliente = $('#' + formname + '_identificacionCliente').val();
    if(identificacionCliente.trim() === '' || identificacionCliente === null) 
    {
           alertModal('Identificación no válida. Favor verificar.');
            return false;
    }  

    if(validarContentidoFormaContacto())
    {  
        if(validaFormasContacto())
        {
            if(validarContentidoRepresentanteLegal())
            {
               
                    if (yaTieneElRol) {
                        alertModal('Esta persona o Empresa Ya esta ingresado como ' + rol);
                        return false;
                    } 
                    else
                    {
                        if (yaTieneElRolCliente) 
                        {
                        alertModal('Esta persona o Empresa Ya esta ingresado como Cliente con estado Activo o Pendiente');
                            return false;
                        }
                        else if(strDistribuidor != "")
                        {
                            var strMensajeDistModal = 'Identificación ingresada pertenece al cliente del distribuidor: <br><b>'+strDistribuidor+' </b></br>'+
                                                        'Por favor crear una solicitud de autorización.';

                            Ext.MessageBox.show({
                                modal : true,
                                title : 'Error',
                                msg   : strMensajeDistModal,
                                width : 420,
                                icon  : Ext.MessageBox.ERROR,
                                buttons : Ext.Msg.OK
                            });
                            return false;
                        }
                        else if(floatSaldoPendiente >100 && prefijoEmpresa == "TN")
                        {
                            var strMensajeDeudaModal = 'Esta persona o Empresa tiene una deuda de: $'+floatSaldoPendiente+' <br>'+
                                                    'Por favor crear un solicitud de reactivación.';
                            Ext.MessageBox.show({
                                modal : true,
                                title : 'Error',
                                msg   : strMensajeDeudaModal,
                                width : 420,
                                icon  : Ext.MessageBox.ERROR,
                                buttons : Ext.Msg.OK
                            });
                            return false;
                        }
                        else
                        {
                            if ($('#' + formname + '_tipoEmpresa').val() == '' &&
                                ($('#' + formname + '_fechaNacimiento_day').val() == '' ||
                                    $('#' + formname + '_fechaNacimiento_month').val() == '' ||
                                    $('#' + formname + '_fechaNacimiento_year').val() == ''))
                            {
                                if (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN')
                                {   Ext.getCmp('myTabs').setActiveTab(0);
                                    alertModal('La Fecha de Nacimiento es un campo obligatorio - No se puede guardar el Prospecto');                             
                                    return false;
                                }
                            }
                            else
                            { 
                                intEdad = validaFechaNacimientoNew($('#'+formname+'_fechaNacimiento_day').val(),
                                $('#'+formname+'_fechaNacimiento_month').val(),
                                $('#'+formname+'_fechaNacimiento_year').val());
                                if(intEdad<18)
                                {
                                alertModal('La Fecha de Nacimiento ingresada corresponde a un menor de edad - \n\
                                        No se puede guardar el Prospecto :'+ $('#'+formname+'_fechaNacimiento_year').val() + '-' +
                                $('#'+formname+'_fechaNacimiento_month').val() + '-' + 
                                $('#'+formname+'_fechaNacimiento_day').val());
                                    return false;     
                                }
                            }
                            habilitaCampos();
                            $('#'+formname+'_esPrepago').removeAttr('disabled');				
                            return true;
                        }
                    }
                }
                else
                {
                    return false;
                }   
                
            } 
            else
            {
                return false;
            }            
    } 
    else
    {
        return false;
    }                        
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
function validaFechaNacimientoNew(intDia, intMes, intAno)
{       
    var intAnoDiferencia = 0;
    var intMesDiferencia = 0;
    var intDiaDiferencia = 0;        
    var f = new Date();        
    intAnoDiferencia = f.getFullYear() - intAno;
    intMesDiferencia = f.getMonth() +1 - intMes;
    intDiaDiferencia = f.getDate() - intDia;
  
    if ((intDiaDiferencia < 0 && intMesDiferencia == 0) || intMesDiferencia < 0)
    {
        intAnoDiferencia--;
    }    
    return intAnoDiferencia;
}

/** 
 * Descripcion: Permite ingresar caracteres que conforman cédula Panameña
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 30-06-2017
 * @param {event} e
 * @return boolean
 * 
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.1 27-09-2017 Se elimina la restricción.
 */
function cedulaPanama(e) {
    if (strNombrePais == 'PANAMA') {
        key = e.keyCode || e.which;

        tecla = String.fromCharCode(key).toUpperCase();
        letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-";
        especiales = ["8", "37", "39", "46", "9"];
        tecla_especial = false;
        for (var i in especiales) {
            if (key == especiales[i]) {
                tecla_especial = true;
                break;
            }
        }
        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            return false;
        }
    } else {
        return true;
    }
    
}

var objStatesDireccionesRecomendadas = Ext.create('Ext.data.ArrayStore', {
    fields : ['value'],
    data:[]
});
var objStatesRecomendoTelefonoFijo = Ext.create('Ext.data.ArrayStore', {
    fields : ['value'],
    data:[]
});

function createSelectDireccionRecomendada() { 
    Ext.create('Ext.form.field.ComboBox', { 
        id: formname + '_direccionTributaria',      
        name :formname + '_direccionTributaria',       
        width: 250,
        emptyText: 'Seleccione o escriba una dirección', 
        store:  objStatesDireccionesRecomendadas   ,
        queryMode: 'local',
        displayField: 'value',
        valueField:  'value', 
        renderTo: 'combo_direccion_recomenda'        
    });
}

function createModelInformacionRecomendada() {

   var  win = Ext.create('Ext.form.Panel', {
        title: 'Agregar información recomendada',
        width: 500,
        height: 200, 
        floating: true,
        closable: true,
        modal: true,   
        bodyPadding: 25,
        buttonAlign: 'center',  
        layout:{
            type:'table',
            columns: 1,
            align: 'left'
        }, 
        items: [  
            {
                xtype: 'combobox',
                fieldLabel: strTelefonoFijo+':',
                id: 'comboTelefonoFijoRecom',
                name: 'comboTelefonoFijoRecom',
                store:  objStatesRecomendoTelefonoFijo  ,                   
                emptyText: 'Seleccione o escriba un telefono',           
                queryMode: 'local',
                displayField: 'value',
                valueField:  'value',  
                width: 450,
                typeAhead: true , 
                 
           }        
        ],   
        buttons: [ 
            {
                text: 'Agregar',
                handler: function() {
                    loadDataContactoRecomendado(); 
                    win.destroy();
                } 
            },
            {
                text: 'Cerrar',
                handler: function(){
                    win.destroy();
                }
            }
        ], 
    }).show();
 

}
var strTelefonoFijo ="Telefono Fijo"; 
function loadDataContactoRecomendado() {   
    
    
    var boolError = false; 
    for (var i = 0; i < storeFormaContactoPersona.getCount(); i++)
    {
        variable = storeFormaContactoPersona.getAt(i).data;
        boolError = trimAll(variable['formaContacto']) == '';

        if (boolError)
        {
            break;
        }
        else
        {
            boolError = trimAll(variable['valor']) == '';
            if (boolError)
            { 
                break;
            }
        }
    }
    if (!boolError)
    {
        let strValueTelefonoFijo= Ext.getCmp('comboTelefonoFijoRecom').getValue();
        var registro =
        {
            'idPersonaFormaContacto': 0,
            'formaContacto': strTelefonoFijo,
            'valor':  strValueTelefonoFijo,
        };
        var rec = new PersonaFormasContactoModel(registro);
        storeFormaContactoPersona.add(rec);  
    } 
    

}

 
 

function createTabPanelRecomendacion() { 

    Ext.create('Ext.form.Panel', {
            title: '',
            header:false, 
            renderTo: Ext.get('tab3'),        
            width: 870,           
            height: 500,   
            bodyPadding: 10, 
            border: false,
            margin: 5,
            padding: 0,
            autoScroll :true,
            layout: {
                type: 'table',
                columns: 2,
                pack: 'center',
                align: 'middle',
                autoScroll :true,
                tableAttrs: {
                    style: {
                        width: '100%',
                       // height: '100%'
                    }
                },
                tdAttrs: {
                    align: 'center',
                    valign: 'top', 
                    autoScroll :true,
                }
            },
            items: [
                {
                    
                xtype:'fieldset',
                id: 'idIngresoRecomendadoTarjetaCredito', 
                name: 'idIngresoRecomendadoTarjetaCredito', 
                columnWidth: 0.5,
                title: 'TARJETAS DE CRÉDITO RECOMENDADAS',
                collapsible: false,
                defaultType: 'textfield',
                defaults: {anchor: '100%'},
                layout: 'anchor',
                autoHeight:true,
                autoScroll :true,
                width: 400,
                margin: 3,
                padding: 3,
                items :[]
                },
                {
                xtype:'fieldset',
                id: 'idIngresoRecomendadoIngresos', 
                name: 'idIngresoRecomendadoIngresos', 
                columnWidth: 0.5,
                title: 'MÁS DETALLES',
                collapsible: false,
                defaultType: 'textfield',
                defaults: {anchor: '100%'},
                layout: 'anchor',
                autoHeight:true,
                autoScroll :true,
                width: 455,
                margin: 3,
                padding: 3,
                items :[]
                } 
            ]
    }).show();
 
    
}


/**
 * Documentación para la función 'consultaPrefactibilidad'
 * Recibe datos del microservicio "" y determina si el Prospecto se debe guardar 
 * o no en el Historal de Prefactibilidad
 * 
 * @author  Andrea Cardenas <ascardenas@telconet.ec>
 * @version 1.0 18-06-2021 
 * 
 */

function consultaPrefactibilidad()
{ 
    if(verificarRequeridosPrefactibilidad())
    {
        
        var dependeEdificio=$("#selectoption_dependedeedificio").val();
        if(dependeEdificio=="N"){
            dependeEdificio="NO";
        }else{
            dependeEdificio="SI";
        }

        console.log('Se consultará Cobertura');

        var arrayForm= {'identificacionCliente'     :$('#' + formname + '_identificacionCliente').val(),
                        'tipoIdentificacion'        :$('#' + formname + '_tipoIdentificacion').val(),
                        'nombres'                   :$('#' + formname + '_nombres').val(),
                        'apellidos'                 :$('#' + formname + '_apellidos').val(),
                        'razonSocial'               :$('#' + formname + '_razonSocial').val(),
                        'formas_contacto'           :$('#' + formname + '_formas_contacto').val(),
                        'latitud'                   :$("#infopuntoextratype_latitudFloat").val(),
                        'longitud'                  :$("#infopuntoextratype_longitudFloat").val(),
                        'ptoCoberturaId'            :$("#infopuntoextratype_ptoCoberturaId").val(),
                        'cantonId'                  :$("#infopuntoextratype_cantonId").val(),
                        'parroquia'                 :$("#infopuntoextratype_parroquiaId").val(),
                        'sectorId'                  :$("#infopuntoextratype_sectorId").val(),
                        'dep_edificio'              :dependeEdificio,
                        'yaexiste'                  :$('#' + formname + '_yaexiste').val(),
                        'id'                        :$('#' + formname + '_id').val()
                    } 
        
        Ext.MessageBox.wait ('Consultando Prefactibilidad..', 'Por favor espere');
        // CONSULTA WS PREFACTIBILIDAD
        $.ajax({
            type: "POST",
            data:{
                preclientetype: JSON.stringify(arrayForm)
            },
            url: url_consulta_prefactibilidad,
           
            success: function(response) 
            {
                Ext.MessageBox.hide ();
                var info = JSON.stringify(response);
                var result= JSON.parse(info);
                console.log(result);

                if (result['status'] == "OK") 
                {
                    console.log('Se realizó Consulta existosamente') ;
                    //Se toman los datos devueltos por el WS Prefactibilidad
                    var arrayDatos= result['data'];
                    var responseExisteCobertura=arrayDatos['existeCobertura'];
                    var responseExisteFactibilidad=arrayDatos['existeFactibilidad'];
                    var responseMensajeFactibilidad=arrayDatos['mensajeGestionaPreFactibilidad'];
                    arrayDatosCaja =arrayDatos['infoCajasConectores'];
                    

                    if (responseExisteCobertura=="NO"){
                        
                        Ext.Msg.alert('Mensaje', responseMensajeFactibilidad);
                        ocultarDiv('labelInfoPrefactibilidad');
                        ocultarDiv('lista_consulta_prefactibilidad');
                        
                    }else{
                        
                        if(responseExisteFactibilidad=="NO"){

                            mostrarDiv('labelInfoPrefactibilidad');
                            mostrarDiv( 'labelCoberturaSi');ocultarDiv( 'labelCoberturaNo');
                            mostrarDiv( 'labelFactibilidadNo');ocultarDiv( 'labelFactibilidadSi');
                            mostrarDiv('lista_consulta_prefactibilidad');
                            showGridPrefactibilidad();
                            Ext.Msg.alert('Mensaje', responseMensajeFactibilidad);
                                

                        }else{

                            mostrarDiv('labelInfoPrefactibilidad');
                            mostrarDiv( 'labelCoberturaSi');ocultarDiv( 'labelCoberturaNo');
                            mostrarDiv( 'labelFactibilidadSi');ocultarDiv( 'labelFactibilidadNo');
                            mostrarDiv('lista_consulta_prefactibilidad');
                            showGridPrefactibilidad();
                            Ext.Msg.alert('Mensaje', responseMensajeFactibilidad);
 
                        }
                    }


                }else {                    
                   alertModal(result['mensaje'] );
                }

            }
        

        });
      
    }
     

}

/**
 * Documentación para la función 'verificarRequeridosPrefactibilidad'
 * Determina que los datos básicos, las formas de contacto y las coordenadas
 * sean corrrectas.
 * 
 * @author  Andrea Cardenas <ascardenas@telconet.ec>
 * @version 1.0 18-06-2021 
 * 
 */

function verificarRequeridosPrefactibilidad()
{
    var tipoIdentificacion= $('#' + formname + '_tipoIdentificacion').val();
    var identificacionCliente = $('#' + formname + '_identificacionCliente').val();
    var nombresCliente = $('#' + formname + '_nombres').val();
    var apellidosCliente = $('#' + formname + '_apellidos').val();
    var tipoEmpresa = $('#' + formname + '_tipoEmpresa').val();
    var razonSocialCliente= $('#' + formname + '_razonSocial').val();
    var formasContactoCliente = $('#' + formname + '_formas_contacto');
    var ptoCoberturaId = $("#infopuntoextratype_ptoCoberturaId").val();
    var cantonId=$("#infopuntoextratype_cantonId").val();
    var parroquia=$("#infopuntoextratype_parroquiaId").val();
    var sectorId=$("#infopuntoextratype_sectorId").val();
    var datosRequeridosOk= false;
    var formasContactoOk = false;
    var coordenadasOk = false;
    var ubicacionOk = false;
    var requerimientosOk=false;
    var mensajeValidacion='Para consultar la prefactibilidad debe ingresar los datos básicos del prospecto: Identificación, Tipo de identificación, Nombre y Apellido, Correo electrónico y mínimo un número de teléfono';
    var mensajeValRUC='Para consultar la prefactibilidad debe ingresar los datos básicos del prospecto: Identificación, Tipo de identificación, Razón Social, Correo electrónico y mínimo un número de teléfono';
    
    $('#' + formname + '_formas_contacto').prop('required', true);
    $('#' + formname + '_tipoIdentificacion').prop('required', true);
    $('#' + formname + '_identificacionCliente').prop('required', true);
    $('#' + formname + '_nombres').prop('required', true);
    $('#' + formname + '_apellidos').prop('required', true);
    $('#' + formname + '_razonSocial').prop('required', true);

    
        if(tipoIdentificacion=="RUC" ){
            if( tipoEmpresa!= ''){
                if((razonSocialCliente.trim() === '' || razonSocialCliente=== null)||(identificacionCliente.trim() === '' || identificacionCliente === null)){
                   Ext.getCmp('myTabs').setActiveTab(0);
                   alertModal(mensajeValRUC);
                }else{
                    if(validaRazonSocial(razonSocialCliente)== false) {
                       alertModal('Existen errores en la Razón Social, por favor corregir.'); 
                    }else{
                        datosRequeridosOk= true;
                    }
                }
            }
            else
            {
                datosRequeridosOk=validaNombresApellidosVacios( nombresCliente, apellidosCliente,identificacionCliente, mensajeValidacion);
            }

        }else{
            datosRequeridosOk=validaNombresApellidosVacios( nombresCliente, apellidosCliente, identificacionCliente, mensajeValidacion);
        }
    

    if(datosRequeridosOk){
        
        formasContactoOk=validaFormasContactoPrefactibilidad (formasContactoCliente); 
        if(formasContactoOk)
        {
            if(ptoCoberturaId!="" && cantonId!="" && parroquia!="" && sectorId!="")
            {
                ubicacionOk=true;
                coordenadasOk=validacionesCoordenadas();   
            }else{
               alertModal('Para consultar la prefactibilidad debe completar los datos ubicación del punto: ' 
                + 'Punto de cobertura, Cantón, Parroquia, Sector y Coordenadas.');
            }
        }else{
            Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
        }
    }
    else
    {
            Ext.getCmp('myTabs').setActiveTab(0);
    }
    
    if(datosRequeridosOk==true && formasContactoOk==true && coordenadasOk==true && ubicacionOk==true)
    {
        requerimientosOk=true;
    }

    return requerimientosOk;

}


/**
 * Documentación para la función 'validaFormasContactoPrefactibilidad'
 * determina que las formas ingresadas sean al menos un correo y un numero de teléfono
 * 
 * @author  Andrea Cardenas <ascardenas@telconet.ec>
 * @version 1.0 18-06-2021 
 * 
 */
function validaFormasContactoPrefactibilidad (formasContactoCliente)
{
    var array_data = new Array();
    var array_telefonos = new Array();
    var array_correos = new Array();
    var variable = '';
    var formaContacto='';
    var valoresVacios = false;
    var existeCorreo= false; var fillCorreo=false;
    var existeTelefono= false; var fillTelefono=false;
    var telefonosOk = false;
    var correosOk = false;
    var formasContactoOk=false;


    for (var i = 0; i < grid.getStore().getCount(); i++)
    {
        variable = grid.getStore().getAt(i).data;
        fillCorreo=false;
        fillTelefono=false;
        for (var key in variable)
        {
            var valor = variable[key];
            if (key == 'valor' && valor == '')
            {
                valoresVacios = true;
            }
            
                if (key =='formaContacto')
                {
                    formaContacto = variable[key];
                    formaContacto = formaContacto.toUpperCase();
                    if (formaContacto.match(/^TELEFONO.*$/))
                    {
                        fillTelefono = true;
                        existeTelefono = true;
                    }
                    if (formaContacto.match(/^CORREO.*$/))
                    {
                        fillCorreo = true;
                        existeCorreo = true;
                    }
                }   
                
                if (fillTelefono)
                {
                    array_telefonos.push(valor);
                    array_data.push(valor);
                }
                if (fillCorreo)
                {
                    array_correos.push(valor);
                    array_data.push(valor);
                }
        }
    }

    formasContactoCliente.val(array_data);
    
    if (formasContactoCliente.val() == '')
    {
       alertModal('Para consultar la prefactibilidad debe ingresar los datos básicos del prospecto: Identificación, Tipo de identificación, Nombre, Apellido, Correo electrónico y mínimo un número de teléfono');
        formasContactoCliente.val('');
       
    }
    else
    { 
        if(existeCorreo == true && existeTelefono== true && valoresVacios == false)
        {
            console.log(array_telefonos);
            console.log(array_correos);

            for (i = 0; i < array_correos.length; i++)
            {
                if (i % 2 != 0)
                {
                    if(!validaCorreo(array_correos[i])){
                       Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
                       alertModal('Hay direcciones de correo con errores, por favor corregir.');
                    }else{
                        correosOk=true;
                    }
                }
            }
            
            for (i = 0; i < array_telefonos.length; i++)
            {
                if (i % 2 != 0)
                {
                    if(!validaTelefono(array_telefonos[i])){
                       Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
                       alertModal('Hay números de telefono con errores. Por favor corregir '); 
                    }else{
                        telefonosOk=true;
                    }
                }
            }

            if (telefonosOk==true && correosOk==true)
            {
                formasContactoOk= true;    
            }

        }else{
            
            if (valoresVacios == true)
             {
               Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
               alertModal('Existen formas de contacto sin valores, favor corregir');
             }else{

               Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabContactoCliente'));
               alertModal('Faltan formas de contacto');
             }

        }
    }

    return formasContactoOk;   
}

function validaNombresApellidosVacios( nombre, apellido, identificacion, mensaje){
    var datosRequeridosOk= false;
    if((nombre.trim() === ''||nombre===null) || (apellido.trim() === ''||apellido===null) || (identificacion.trim() === ''||identificacion===null)){
        Ext.getCmp('myTabs').setActiveTab(0);
       alertModal(mensaje);
    }else{
        if(validaNombres(nombre)== false||validaNombres(apellido)== false ) {
           alertModal('Existen errores en los nombres y apellidos por favor corregir.');
        }
        if(validaNombres(nombre)==true && validaNombres(apellido)==true ){
            datosRequeridosOk= true;
        }
    }

    return datosRequeridosOk;

}

function validaRazonSocial(razonSocial)
{
    var RegExPattern = /^[A-Z0-9 ]{5,100}$/;
    var result=false;
    if(razonSocial.match(RegExPattern) && razonSocial.value != ''){
        result=true;
    }
    return result;
} 

function validaNombres(nombres)
{
    var RegExPattern = /^[A-Z ]{5,50}$/;
    var result=false;
    if(nombres.match(RegExPattern) && nombres.value != ''){
        result=true;
    }
    return result;
} 

function validaTelefono(telefono)
{
    var RegExPattern = /^[0-9]{8,10}$/;
    var result=false;
    if (telefono.match(RegExPattern) && (telefono.value != '')){
        result=true;
    }
    return result;
    
}

function validaCorreo(correo)
{
    var RegExPattern = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    var result=false;
    if (correo.match(RegExPattern) && (correo.value!='')){
        result=true;
    }
    return result;
}


/**
 * Variables globales para implementacion de representante legal 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
var objConfRepresentLegal= {
    show:false, 
    isCordinador:false
}; 

/**
 * Oculta o muestra un tab representante legal, aplica para MD
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
 function representanteLegalShow() {
    let strTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val(); 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    let strTipoTributario     = $('#' + formname + '_tipoTributario').val(); 
    let objElement = Ext.getCmp('idTabRepresentanteLegal');  
    if ((prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN') &&  strTipoIdentificacion=='RUC' &&  strTipoTributario =='JUR' && strIdentificacion!='')
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
    
   if (objConfRepresentLegal.show== false ) {
    let tab4 = Ext.get ('tab4'); 
    let strTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val(); 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    tab4.mask ('Cargando gestor de representante Legal espere ....');
    let panel =  gestorRepresentanteLegal (tab4, strTipoIdentificacion , strIdentificacion, objConfRepresentLegal.isCordinador );
    panel.show ();
    tab4.unmask (); 
    objConfRepresentLegal.show= true;  
   } 

 }

/**
 * valida el si requiere representante legal y carga la data en input
 * 
 * @author  Jefferson Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 1-09-2022
 * 
 */
function validarContentidoRepresentanteLegal() { 
    let strTipoIdentificacion = $('#' + formname + '_tipoIdentificacion').val(); 
    let strIdentificacion     = $('#' + formname + '_identificacionCliente').val();  
    let strTipoTributario     = $('#' + formname + '_tipoTributario').val();  
    let isEmpresa = ($('#'+ formname + '_tipoEmpresa').val()=='Publica' || $('#'+ formname + '_tipoEmpresa').val()=='Privada'); 
    if (strTipoTributario =='JUR' && isEmpresa == false) {
        activeTab('idTabDatosPrincipalesCrs');
        alertModal('Seleccione un tipo de empresa');
        return false; 
    }else {
        representanteLegalShow();
        if (strTipoTributario !='JUR' && isEmpresa == true) {
            activeTab('idTabDatosPrincipalesCrs');
            alertModal('Tipo empresa requiere seleccionar tipo tributario juridico');
            return false; 
        }     

        if (objConfRepresentLegal.isCordinador && (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN') &&  strTipoIdentificacion=='RUC' &&  strTipoTributario =='JUR' && strIdentificacion!='')
        {   
            var representanteLegal = getDataRepresentanteLegal(); 
            if (representanteLegal.length == 0 ) {            
                Ext.getCmp('myTabs').setActiveTab( Ext.getCmp('idTabRepresentanteLegal'));  
                $('#' + formname + '_representante_legal').val('');     
                return false; 
            }else{
                $('#' + formname + '_representante_legal').val(JSON.stringify(representanteLegal)); 
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
 */
 function ocultarImputRepresentanteTN() {
    if((prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN') )
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
    Ext.getCmp('myTabs').getEl ().unmask (); 
    Ext.Msg.alert ('Alerta',strMensaje);
    
 }

 /**
 * Documentación para la funcion 'validarCaracterEspecial'
 * 
 * Función que permite validar los caracteres especiales de un campo.
 * 
 * @author  Kenth Encalada <kencalada@telconet.ec>
 * @version 1.0 23-06-2023
 * 
 */
 function validarCaracterEspecial(strCampoAValidar, strNombreCampo) {
    if (prefijoEmpresa == 'TN' && strCampoAValidar != '')
    {
        const patronCaracteresEspeciales = /[\'^£$%&*()}{@#~?><>,|=+¬\/"]/gi
        const strCampo = document.getElementById(strCampoAValidar)?.value ?? '';
        if (patronCaracteresEspeciales.test(strCampo)) 
        {
           alertModal(`El campo: '${strNombreCampo}' contenía caracteres inválidos, por lo que se procederá con el ajuste del campo de texto.`);
           document.getElementById(strCampoAValidar).value = strCampo.replace(patronCaracteresEspeciales, '').trim();
        }
    } 
 }