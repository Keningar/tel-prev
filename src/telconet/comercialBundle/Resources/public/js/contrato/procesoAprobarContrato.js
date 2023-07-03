
function esEmpresa() {
    if ($('#procesoaprobarcontratotype_tipoEmpresa').val() == 'Publica' || $('#procesoaprobarcontratotype_tipoEmpresa').val() == 'Privada') {
        ocultarDiv('div_nombres');
        mostrarDiv('div_razon_social');
        $('#procesoaprobarcontratotype_razonSocial').attr('required', 'required');
        $('#procesoaprobarcontratotype_representanteLegal').attr('required', 'required');
        $('label[for=procesoaprobarcontratotype_representanteLegal]').html('* Representante Legal:');
        $('label[for=procesoaprobarcontratotype_representanteLegal]').addClass('campo-obligatorio');
        $('#procesoaprobarcontratotype_nombres').removeAttr('required');
        $('#procesoaprobarcontratotype_apellidos').removeAttr('required');
        $('#procesoaprobarcontratotype_genero').removeAttr('required');
        $('#procesoaprobarcontratotype_estadoCivil').removeAttr('required');
        //cambios DINARDARP - se agrega campo origenes de ingresos
        $('#procesoaprobarcontratotype_origenIngresos').removeAttr('required');
        $('#procesoaprobarcontratotype_fechaNacimiento_year').removeAttr('required');
        $('#procesoaprobarcontratotype_fechaNacimiento_month').removeAttr('required');
        $('#procesoaprobarcontratotype_fechaNacimiento_day').removeAttr('required');
        $('#procesoaprobarcontratotype_tituloId').removeAttr('required');
        $('#procesoaprobarcontratotype_nombres').val('');
        $('#procesoaprobarcontratotype_apellidos').val('');
        $('#procesoaprobarcontratotype_genero').val('');
        $('#procesoaprobarcontratotype_estadoCivil').val('');
        //cambios DINARDARP - se agrega campo origenes de ingresos
        $('#procesoaprobarcontratotype_origenIngresos').val('');
        $('#procesoaprobarcontratotype_fechaNacimiento_year').val('');
        $('#procesoaprobarcontratotype_fechaNacimiento_month').val('');
        $('#procesoaprobarcontratotype_fechaNacimiento_day').val('');
        $('#procesoaprobarcontratotype_tituloId').val('');
    }
    else
    {
        mostrarDiv('div_nombres');
        ocultarDiv('div_razon_social');
        $('#procesoaprobarcontratotype_razonSocial').removeAttr('required');
        $('label[for=procesoaprobarcontratotype_representanteLegal]').removeClass('campo-obligatorio');
        $('label[for=procesoaprobarcontratotype_representanteLegal]').html('Representante Legal:');
        $('#procesoaprobarcontratotype_representanteLegal').removeAttr('required');
        $('#procesoaprobarcontratotype_nombres').attr('required', 'required');
        $('#procesoaprobarcontratotype_apellidos').attr('required', 'required');
        $('#procesoaprobarcontratotype_genero').attr('required', 'required');
        $('#procesoaprobarcontratotype_estadoCivil').attr('required', 'required');
        //cambios DINARDARP - se agrega campo origenes de ingresos
        $('#procesoaprobarcontratotype_origenIngresos').attr('required', 'required');
        $('#procesoaprobarcontratotype_fechaNacimiento_year').attr('required', 'required');
        $('#procesoaprobarcontratotype_fechaNacimiento_month').attr('required', 'required');
        $('#procesoaprobarcontratotype_fechaNacimiento_day').attr('required', 'required');
        $('#procesoaprobarcontratotype_tituloId').attr('required', 'required');
        $('#procesoaprobarcontratotype_razonSocial').val('');
    }
    $('#procesoaprobarcontratotype_tipoEmpresa').attr('disabled', 'disabled');
    $('#procesoaprobarcontratotype_tipoIdentificacion').attr('disabled', 'disabled');
    $('#procesoaprobarcontratotype_esPrepago').attr('disabled', 'disabled');
    $('#procesoaprobarcontratotype_idOficinaFacturacion').attr('disabled', 'disabled');
    if ($('#procesoaprobarcontratotype_identificacionCliente').val() != '')
    {
        flagIdentificacionCorrecta = 1;
        $('#procesoaprobarcontratotype_identificacionCliente').attr('readonly', 'readonly');
    }

}

           function esRuc(){
                if ($('#procesoaprobarcontratotype_tipoIdentificacion').val()=='RUC'){
                     $('#procesoaprobarcontratotype_identificacionCliente').removeAttr('maxlength');
                     $('#procesoaprobarcontratotype_identificacionCliente').attr('maxlength','13');
                        //$('#preclientetype_identificacionCliente').val('');
                }else
                {
                    $('#procesoaprobarcontratotype_identificacionCliente').removeAttr('maxlength');
                    $('#procesoaprobarcontratotype_identificacionCliente').attr('maxlength','10');
                        //$('#preclientetype_identificacionCliente').val('');
                }
           }                
function mostrarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'block';    
}
function ocultarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'none';    
}
 
 function validaIdentificacion(){
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
					}
					if(msg=="si"){
						flagIdentificacionCorrecta = 0;
						$('#img-valida-identificacion').attr("title","identificacion ya existe");
						$('#img-valida-identificacion').attr("src",url_img_delete);
						$(input).focus();
						alert("Identificacion ya existente. Favor Corregir");
					}
				   
			   }
			   else
			   {
				   alert("Error: No se pudo validar la identificacion ingresada.");
			   }
			}
	});
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
 
$(document).ready(function(){  
     esEmpresa();      
     esRuc();
                    
     if ($('#procesoaprobarcontratotype_tieneCarnetConadis').val()=='S')
     {   
         $('#procesoaprobarcontratotype_numeroConadis').attr('required','required');
         $('#procesoaprobarcontratotype_numeroConadis').show();
         $('label[for=procesoaprobarcontratotype_numeroConadis]').show();
     }
     else if ($('#procesoaprobarcontratotype_tieneCarnetConadis').val()=='N')
     {   
         $('#pprocesoaprobarcontratotype_numeroConadis').hide();
         $('label[for=procesoaprobarcontratotype_numeroConadis]').hide();
     }          
});

 /*Ext.require([
    '*'
 ]);*/

Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;
            var dataStoreEntregables;

/***Ext.onReady****/
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
    
    // create the Data Store para formas de contacto por Persona
    var store = Ext.create('Ext.data.Store', {
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

    // create the Data Store para forma de contacto
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
            //header: 'Valor',
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
        height: 1100,
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
    
    //Listado de Servicios para convertir a Orden de trabajo
   //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
                
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'string'}, 
			    {name:'login', type: 'string'},
                            {name:'descripcion', type: 'string'},
                            {name:'cantidad', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'precio', type: 'string'},
                            ]
                }); 


                storeServices = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store,
                        reader: {
                            type: 'json',
                            root: 'listado',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(storeServices){
				storeServices.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				storeServices.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();
				storeServices.getProxy().extraParams.origen = strOrigenContrato;
                        },
                        load: function(storeServices){
                            storeServices.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                storeServices.load({params: {start: 0, limit: 10, origen: strOrigenContrato}});    



                sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                                    //arregloSeleccionados.push(record.data.idOsDet);
                    });			
                            //console.log(arregloSeleccionados);

                        }
                    }
                });
                
                //Grid del Listado de servicios Factibles a convertir a Orden de Trabajo
                var listView = Ext.create('Ext.grid.Panel', {
                    width:650,
                    height:200,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    /*dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',   
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
					, {
                                        iconCls: 'icon_aprobar',
                                        text: 'Aprobar',
                                        disabled: false,
                                        itemId: 'aprobar',
                                        scope: this,
                                        handler: function(){aprobarAlgunos()}
                                    }]
                            }],  */                  
                    renderTo: Ext.get('lista_servicios'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: storeServices,
                        displayInfo: true,
                        displayMsg: 'Mostrando servicios {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: storeServices,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                    {
                        text: 'Login',
                        width: 190,
                        dataIndex: 'login'
                    },{
                        text: 'Servicio',
                        width: 200,
                        dataIndex: 'descripcion'
                    },{
                        text: 'Cantidad',
                        width: 70,
                        dataIndex: 'cantidad'
                    },{
                        text: 'Precio',
                        width: 70,
                        dataIndex: 'precio'
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        width: 70			
                    }]
                });            
            //fin de Grid de Listado de servicios para convertir a orden de trabajo
  
    dataStoreEntregables = new Ext.data.Store({
        total: 'total',
        pageSize: 50,
        autoLoad: true,
        proxy:
            {
                type: 'ajax',
                timeout: 600000,
                url: url_showDocumentosEntregables,
                reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'entregables'
                    }
            },
        fields:
            [
                {name: 'order', type: 'int'},
                {name: 'idContrato', type: 'int'},
                {name: 'codEntregable', type: 'string'},
                {name: 'desEntregable', type: 'string'},
                {name: 'valEntregable', type: 'bool'}
            ],
        sorters: [{
                property: 'order',
                direction: 'ASC'
            }],
        listeners:
            {
                load: function(storeServices)
                {
                    var myEntregables = [];

                    dataStoreEntregables.each(function(record)
                    {
                        myEntregables.push(record.data);
                    }, this);
                    $('#documentosEntregables').val(Ext.encode(myEntregables));
                }
            }
    });
    
    
    Ext.create('Ext.grid.Panel',
        {
            bufferedRenderer: false,
            store: dataStoreEntregables,
            loadMask: true,
            frame: false,
            renderTo: 'documentos_entregables',
            forceFit: true,
            height: 150,
            split: true,
            region: 'north',
            viewConfig:
                {
                    enableTextSelection: true,
                    preserveScrollOnRefresh: true
                },
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        id: 'idContrato',
                        header: 'idContrato',
                        dataIndex: 'idContrato',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'codEntregable',
                        header: 'codEntregable',
                        dataIndex: 'codEntregable',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'desEntregable',
                        header: 'Documento',
                        dataIndex: 'desEntregable',
                        sortable: false,
                        width: 320
                    },
                    {
                        xtype: 'checkcolumn',
                        header: 'Entregó?',
                        dataIndex: 'valEntregable',
                        width: 60,
                        editor:
                            {
                                xtype: 'checkbox',
                                cls: 'x-grid-checkheader-editor'
                            },
                        stopSelection: false,
                        listeners:
                            {
                                checkchange: function()
                                {
                                    var myEntregables = [];

                                    dataStoreEntregables.each(function(record)
                                    {
                                        myEntregables.push(record.data);
                                    }, this);
                                    $('#documentosEntregables').val(Ext.encode(myEntregables));
                                }
                            }
                    }
                ]
        });

});/*** fin Ext.onReady****/

function grabar(campo)
{
    if (strOrigenContrato == 'WEB')
    {
        if ($('#infocontratotype_formaPagoId').val() == 3)
        {
            validarNumeroTarjetaCuenta();
            if (validarFormulario())
            {
                validacionesForm();
                document.forms[0].submit();
                Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere');
            }
        }
        else
        {
            validaGrabarFormasContacto();
        }
    }
    else
    {
        validaGrabarFormasContacto();
    }
}

function validaGrabarFormasContacto()
{
    var array_data = new Array();
    var variable = '';
    var valoresVacios = false;
    var datos = '';
    for (var i = 0; i < grid.getStore().getCount(); i++) {
        variable = grid.getStore().getAt(i).data;
        for (var key in variable) {
            var valor = variable[key];
            if (key == 'valor' && valor == '') {
                valoresVacios = true;
            } else {
                array_data.push(valor);
            }
        }
    }
    datos = array_data;
    if ((datos == '0,,') || (datos == '')) {
        alert('No hay formas de contacto aun ingresadas.');

    } else {
        if (valoresVacios == true) {
            alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');

        }
        else {
            if (typeof campo !== typeof undefined && campo !== null)
            {
                $(campo).val(array_data);
            }
            $('#procesoaprobarcontratotype_tipoEmpresa').removeAttr('disabled');
            $('#procesoaprobarcontratotype_tipoTributario').removeAttr('disabled');
            $('#procesoaprobarcontratotype_nacionalidad').removeAttr('disabled');
            $('#procesoaprobarcontratotype_tipoIdentificacion').removeAttr('disabled');
            $('#procesoaprobarcontratotype_genero').removeAttr('disabled');
            $('#procesoaprobarcontratotype_estadoCivil').removeAttr('disabled');
            $('#procesoaprobarcontratotype_tituloId').removeAttr('disabled');
            //cambios DINARDARP - se agrega campo origenes de ingresos
            $('#procesoaprobarcontratotype_origenIngresos').removeAttr('disabled');
            $('#procesoaprobarcontratotype_fechaNacimiento_month').removeAttr('disabled');
            $('#procesoaprobarcontratotype_fechaNacimiento_day').removeAttr('disabled');
            $('#procesoaprobarcontratotype_fechaNacimiento_year').removeAttr('disabled');
        }
    }

    $("#infocontratotype_formaPagoId").attr("enabled", "enabled");
    $("#infocontratoformapagotype_tipoCuentaId").attr("enabled", "enabled");
    $('#infocontratoformapagotype_bancoTipoCuentaId').attr("enabled", "enabled");
    $('button[type="submit"]').removeAttr('disabled');
    $('#mensaje_validaciones').addClass('campo-oculto').html("");
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
    
    return Utils.validaFormasContacto(grid);
}

var validarDocumentos = true;

function validacionesForm()
{
    //Valido que haya seleccionado al menos 1 servicio para convertir a orden de trabajo	        
    var param = '';
    flagServicosCorrecto = 1;
    if (sm.getSelection().length > 0)
    {
        var estado = 0;
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            param = param + sm.getSelection()[i].data.id;

            if (sm.getSelection()[i].data.estado == 'Eliminado')
            {
                estado = estado + 1;
            }
            if (i < (sm.getSelection().length - 1))
            {
                param = param + '|';
            }
        }
        $('#infocontratotype_listadoServicios').val(param);

        if (estado != 0)
        {
            Ext.Msg.alert('Error ', 'Por lo menos uno de los Servicios se encuentra en estado ELIMINADO');
            flagServicosCorrecto = 0;
        }
    }
    else
    {
        flagServicosCorrecto = 0;
        Ext.Msg.alert('Error ', 'Seleccione por lo menos un servicio Factible de la lista');
    }

    if (validaFormasContacto() && flagIdentificacionCorrecta == 1 && flagServicosCorrecto == 1)
    {
        $('#procesoaprobarcontratotype_tipoIdentificacion').removeAttr('disabled');
        $('#procesoaprobarcontratotype_tipoEmpresa').removeAttr('disabled');
        return true;
    }
    else
    {
        return false;
    }

}

function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");  
}

/*function aprobarAlgunos(){
var param = '';
if(sm.getSelection().length > 0)
{
  var estado = 0;
  for(var i=0 ;  i < sm.getSelection().length ; ++i)
  {
    param = param + sm.getSelection()[i].data.id;

    if(sm.getSelection()[i].data.estado == 'Eliminado')
    {
      estado = estado + 1;
    }
    if(i < (sm.getSelection().length -1))
    {
      param = param + '|';
    }
  }      
  if(estado == 0)
  {

	Ext.MessageBox.show({
		icon: Ext.Msg.INFO,
		width:500,
		height: 300,
		title:'Mensaje del Sistema',
		msg: 'La generacion de la Orden de Trabajo se realizo con exito.',
		buttonText: {yes: "Ok"},
		fn: function(btn){
			if(btn=='yes'){  
				Ext.Ajax.request({
					url: url_aprobar,
					method: 'post',
					params: { param : param},
					success: function(response){
						var text = response.responseText;
						storeServices.load();
					},
					failure: function(result)
					{
						Ext.Msg.alert('Error ','Error: ' + result.statusText);
					}
				});
			}
		}
    });

  }
  else
  {
    alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
  }
}
else
{
  alert('Seleccione por lo menos un registro de la lista');
}
}*/

function Buscar(){

   storeServices.load({params: {start: 0, limit: 10, origen: strOrigenContrato}});
	
}
	