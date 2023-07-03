Ext.require([
    '*'
]);


function validaServiciosXFormaPago(){ 
                  // alert('validaServiciosXFormaPago');
                    var persona_empresa_rol_id=$('#preclientetype_personaEmpresaRolId').val();	                    
                    var id_forma_pago=$('#infopersonaempformapagotype_formaPagoId').val();			
                    var tipoCuenta=$('#infopersonaempformapagotype_tipoCuentaId').val();			
                    var bancoTipoCuentaId=$('#infopersonaempformapagotype_bancoTipoCuentaId').val();			                    
		    
		    parametros="idFormaPago="+id_forma_pago+"&tipoCuenta="+tipoCuenta+"&bcoTipoCtaId="+bancoTipoCuentaId+"&persona_empresa_rol_id="+persona_empresa_rol_id;
		    
                 $.ajax({
                    type: "POST",
                    data: parametros,
                    url: url_planCondicionIncumplida,
                    success: function(msg){                        
                        if (msg.msg == 'Error')                             
                        {                                
                            $('#preclientetype_mensaje').val(msg.msg);                              
                                alert("Posee Servicios ingresados que no se comercializan con la Forma de Pago que desea modificar");
                            
                        }
                        else{  
                            if (msg.msg == 'Ok'){ 
                               $('#preclientetype_mensaje').val(msg.msg);  
                            }
                        }
                      }
                   });	                   
             }     
             
flagIdentificacionCorrecta=1;

function validaIdentificacionEdit(){
    currenIdentificacion=$(input).val();
	$.ajax({
			type: "POST",
			data: "identificacion=" + currenIdentificacion,
			url: url_valida_identificacion,
			beforeSend: function(){
				$('#img-valida-identificacion').attr("src",url_img_loader);
			},
			success: function(msg){
				if (msg != ''){
					if(msg=="no"){
						flagIdentificacionCorrecta = 1;
						$('#img-valida-identificacion').attr("title","Identificacion disponible");
						$('#img-valida-identificacion').attr("src",url_img_check);
						store.removeAll();
						limpiaCampos();
						habilitaCampos();
					}else
					{
						flagIdentificacionCorrecta = 0;
						$('#img-valida-identificacion').attr("title","identificacion ya existe");
						$('#img-valida-identificacion').attr("src",url_img_delete);
						$(input).focus();
						//console.log(msg);
						var obj=JSON.parse(msg);
						//console.log(obj);
							//esEmpresa();
							//store.removeAll();
							//store.load({params: {personaid: obj[0].id}});
							deshabilitaCampos();
						alert("Identificacion ya existente. Los datos seran cargados en el formulario.");
					}
				   
			   }
			   else
			   {
				   alert("Error: No se pudo validar la identificacion ingresada.");
			   }
			}
	});
}

function validaIdentificacion(isValidarIdentificacionTipo) {
    currenIdentificacion = $(input).val();
    $.ajax({
        type: "POST",
        data: "identificacion=" + currenIdentificacion,
        url: url_valida_identificacion,
        beforeSend: function() {
            $('#img-valida-identificacion').attr("src", url_img_loader);
        },
        success: function(msg) {
            if (msg != '') {
                if (msg == "no") {
                    flagIdentificacionCorrecta = 1;
                    $('#img-valida-identificacion').attr("title", "Identificacion disponible");
                    $('#img-valida-identificacion').attr("src", url_img_check);
                    store.removeAll();
                    limpiaCampos();
                    habilitaCampos();
                    $("#" + formname + "_yaexiste").val('N');
                } else
                {
                    flagIdentificacionCorrecta = 0;
                    $('#img-valida-identificacion').attr("title", "identificacion ya existe");
                    $('#img-valida-identificacion').attr("src", url_img_delete);
                    $(input).focus();
                    //console.log(msg);
                    var obj = JSON.parse(msg);
                    //console.log(obj);
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
                    //cambios DINARDARP - se agrega campo origenes de ingresos
                    $("#" + formname + "_origenIngresos").val(obj[0].origenIngresos);
                    $("#" + formname + "_idreferido").val(obj[0].referidoId);
                    $("#" + formname + "_referido").val(obj[0].referidoNombre);
                    $("#" + formname + "_id").val(obj[0].id);
                    var fechaNac1 = obj[0].fechaNacimiento;
                    arrFechaNacimiento = fechaNac1.split(' ');
                    var fechaNac2 = arrFechaNacimiento[0];
                    arrFechaN = fechaNac2.split('/');
                    $("#" + formname + "_fechaNacimiento_day").val(arrFechaN[0] * 1);
                    $("#" + formname + "_fechaNacimiento_month").val(arrFechaN[1] * 1);
                    $("#" + formname + "_fechaNacimiento_year").val(arrFechaN[2]);
                    esEmpresa();
                    store.removeAll();
                    store.load({params: {personaid: obj[0].id}});
                    deshabilitaCampos();
                    $("#" + formname + "_yaexiste").val('S');
                    //store.getProxy().extraParams.personaid= obj[0].id; 
                    //grid.getStore().getProxy().extraParams = {personaid: obj[0].id};
                    //console.log(obj);
                    alert("Identificacion ya existente. Los datos seran cargados en el formulario.");
                }

            }
            else
            {
                alert("Error: No se pudo validar la identificacion ingresada.");
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
    //Si es MD permito que el campo direccion tributaria siempre sea editable
    if (strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN')
    {
        $('#'+formname+'_direccionTributaria').removeAttr('readonly');
    }
    else
    {
        //Si es TN se mantiene la edicion del campo si existe direccion tributaria dieferente de vacio
        if ($('#' + formname + '_direccionTributaria').val() != '')
        {
            $('#' + formname + '_direccionTributaria').attr('readonly', 'readonly');
        }
    }

    $('#' + formname + '_nombres').attr('readonly', 'readonly');
    $('#' + formname + '_apellidos').attr('readonly', 'readonly');
    $('#' + formname + '_tipoEmpresa').attr('disabled', 'disabled');
    $('#' + formname + '_razonSocial').attr('readonly', 'readonly');

    $('#' + formname + '_tipoIdentificacion').attr('disabled', 'disabled');

    if ($('#' + formname + '_tipoTributario').val() != '')
    {
        $('#' + formname + '_tipoTributario').attr('disabled', 'disabled');
    }
    if ($('#' + formname + '_nacionalidad').val() != '')
    {
        $('#' + formname + '_nacionalidad').attr('disabled', 'disabled');
    }

    $('#' + formname + '_representanteLegal').attr('readonly', 'readonly');
    /*Validación para nuevo perfil Md_delegado_datos que permite editar campo genero 
    y origenIngreso del cliente*/
    var permiso = puedeEditarCampos;
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

    if(boolPermiso && (strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN') )
    {
        $('#' + formname + '_genero').removeAttr('disabled');
        $('#' + formname + '_origenIngresos').removeAttr('disabled');
    }
    else
    {
        if ($('#' + formname + '_genero').val() != '')
        {
            $('#' + formname + '_genero').attr('disabled', 'disabled');
        }
        //cambios DINARDARP - se agrega campo origenes de ingresos
        if ($('#' + formname + '_origenIngresos').val() != '')
        {
            $('#' + formname + '_origenIngresos').attr('disabled', 'disabled');
        }
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
    //if($("#"+formname+"_referido").val()!='')
    //{ocultarDiv("imgreferido");}	
}

function limpiaCampos() {
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
    //cambios DINARDARP - se agrega campo origenes de ingresos
    $('#' + formname + '_origenIngresos').val('');
    $('#' + formname + '_fechaNacimiento_day').val('');
    $('#' + formname + '_fechaNacimiento_month').val('');
    $('#' + formname + '_fechaNacimiento_year').val('');
    $("#" + formname + "_referido").val('');
    mostrarDiv("imgreferido");
    esEmpresa();
}

function habilitaCampos(){
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
				//mostrarDiv("imgreferido");	
}
function validaIdentificacionCorrecta(){
	if(flagIdentificacionCorrecta==1){
		return true;
	}else{
		alert("Identificacion ya existente. Favor Corregir para poder ingresar el Nuevo Cliente");
		$(input).focus();
		return false;
	}
}

Ext.onReady(function(){

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
    store = Ext.create('Ext.data.Store', {
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
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
    grid = Ext.create('Ext.grid.Panel', {
        store: store,
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
        },{
            xtype: 'actioncolumn',
            width:45,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar Forma Contacto',
                handler: function(grid, rowIndex, colIndex) {
                    store.removeAt(rowIndex); 
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
        //frame: true,
        tbar: [{
            text: 'Agregar',
            handler : function(){
                // Create a model instance
                var r = Ext.create('PersonaFormasContactoModel', {
                    idPersonaFormaContacto: '',
                    formaContacto: '',
                    valor: ''
                });
                store.insert(0, r);
                cellEditing.startEditByPosition({row: 0, column: 0});
                
            }
        }],
        plugins: [cellEditing]
    });

    // manually trigger the data store load
    store.load();
    var tabs = new Ext.TabPanel({
        height: 650,
        renderTo: 'my-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab1', title:'Datos Principales'},
             {contentEl:'tab2', title:'Formas de contacto',listeners:{
                  activate: function(tab){
                          grid.view.refresh()
                                
                  }
                                
              }}
        ]            
    });        
});

function grabar(campo)
{    
    var array_data = new Array();
    var variable = '';
    var valoresVacios = false;
    for (var i = 0; i < grid.getStore().getCount(); i++)
    {
        variable = grid.getStore().getAt(i).data;
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
    if (valoresVacios == true) 
    {
        alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');
        $(campo).val('');
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
function validaFormasContacto(){
    
    var validacionFormasContacto = Utils.validaFormasContacto(grid);
    if (validacionFormasContacto)
    {
        habilitaCampos();
    }
    return validacionFormasContacto;
}

function validacionesForm() 
{
    var bandera = true;    
    if ($('#preclientetype_prefijoEmpresa').val() == "MD")
    {
        if ($('#preclientetype_mensaje').val() == "Error")
        {
            if (confirm("Existen Servicios ingresados que no se comercializan con la Forma de Pago que desea modificar\n\
                        , Se procedera a Eliminar los Pre-Servicios"))
            {
                bandera = true;
            } 
            else
            {
                bandera = false;
            }
        } 
        else
        {
            bandera = true;
        }
    }
    if (validaFormasContacto())
    {
        if ($('#' + formname + '_tipoEmpresa').val() == '' &&
            ($('#' + formname + '_fechaNacimiento_day').val() == '' ||
                $('#' + formname + '_fechaNacimiento_month').val() == '' ||
                $('#' + formname + '_fechaNacimiento_year').val() == ''))
        {
            alert('La Fecha de Nacimiento es un campo obligatorio - No se puede guardar el Prospecto');
            return false;
        }
        else
        { 
            intEdad = validaFechaNacimientoEdit($('#'+formname+'_fechaNacimiento_day').val(),
            $('#'+formname+'_fechaNacimiento_month').val(),
            $('#'+formname+'_fechaNacimiento_year').val());
            if(intEdad<18)
            {
                alert('La Fecha de Nacimiento ingresada corresponde a un menor de edad - \n\
                       No se puede guardar el Prospecto :'+ $('#'+formname+'_fechaNacimiento_year').val() + '-' +
                       $('#'+formname+'_fechaNacimiento_month').val() + '-' + 
                       $('#'+formname+'_fechaNacimiento_day').val());
	            return false;     
            }
        }
        bandera = true;
    } 
    else
    {
        bandera = false;
    }
    if(bandera== true)
    {
        $('#'+formname+'_esPrepago').removeAttr('disabled');				
    }
    return bandera;
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
function validaFechaNacimientoEdit(intDia, intMes, intAno)
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

