
Ext.onReady(function(){
  
    Ext.tip.QuickTipManager.init();     
      
    Ext.create('Ext.form.Panel', {
        renderTo: 'fi-form',
        width: 500,
        frame: true,
        title: 'Formulario Subir Archivos',
        bodyPadding: '10 10 0',

        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },

        items: [{
            xtype: 'filefield',
            id: 'form-file',
            name: 'archivo',
            emptyText: 'Seleccione una Archivo',
            buttonText: 'Browse',
            buttonConfig: {
                iconCls: 'upload-icon'
            }
        },
		{
			xtype: 'hiddenfield',
			name: 'modulo',
			value: ''
		}],
		
        buttons: [{
            text: 'Procesar',
            handler: function(){
                var form = this.up('form').getForm();

				this.up('form').getForm().findField('modulo').setValue(
					                          document.getElementById("telconet_schemabundle_info_documentotype_modulo").value
							              );
				if (this.up('form').getForm().findField('modulo').getValue() != "Escoja el Modulo" && 
					this.up('form').getForm().findField('modulo').getValue() != "")
				{
					if(form.isValid())
					{
						form.submit({
							url: strUrlFileUpload,			
							waitMsg: 'Procesando Archivo...',
							success: function(fp, o) 
							{   
								Ext.Msg.alert("Alerta", "Archivo procesado correctamente");
								$("#msg").show();
								$('#msg').html("FILE    : "+o.result.fileName+"   SIZE       : "
								                                             +Math.round( (o.result.fileSize/1000)*100 )/100+" KB");
								$('#hiddenFile').val(o.result.filePath);
								$('#nameFile').val(o.result.fileName);			      
							},
							failure: function(fp, o) {
								Ext.Msg.alert("Error",o.result.respuesta);
							}
						});
					}
				}
				else
				{
					Ext.Msg.alert('Alerta ',"Debe escoger primero el módulo");
				}
			}
        },{
            text: 'Resetear',
            handler: function() {
	        reset();		
                this.up('form').getForm().reset();
		$("#hiddenFile").val("");
		$("#msg").empty();
		$("#msg").html("FILE : Ninguno  SIZE : Ninguno");
            }
        }]
    });
    
    /*** CLIENTES ***/
    
    storeClientes = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlGetClientes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
		[
			{name:'id_cliente', mapping:'id_cliente'},
			{name:'cliente', mapping:'cliente'}
		],
		autoLoad: false
    });
    
    comboCliente = new Ext.form.ComboBox({
        id: 'cmb_cliente',
        name: 'cmb_cliente',        
	renderTo:'login',
        emptyText: 'Seleccione Cliente',
        store: storeClientes,
        displayField: 'cliente',
        valueField: 'id_cliente',
        height:30,
	width: 200,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
      
    Ext.define('TipoElementoList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idTipoElemento', type:'int'},
            {name:'nombreTipoElemento', type:'string'}
        ]
    });
    
    storeTipoElementos = Ext.create('Ext.data.Store', {
          pageSize: 200,
	  model: 'TipoElementoList',
	  autoLoad: true,
	  proxy: 
	  {
		type: 'ajax',
		url : strUrlGetTipoElementos,
		reader: {
			type: 'json',
			totalProperty: 'total',
			root: 'encontrados'
		},
		extraParams: {
		    nombre: '',
		    estado: 'Activo'
		}
	  }
    });
    
    comboTipoElementos = new Ext.form.ComboBox({
	  id: 'cmb_tipoElemento',
	  name: 'cmb_tipoElemento',
	  fieldLabel: false,
	  anchor: '100%',
	  queryMode:'remote',
	  width: 200,	  
	  store:storeTipoElementos,
	  displayField: 'nombreTipoElemento',
	  valueField: 'idTipoElemento',
	  renderTo: 'tipoElemento',
	  listeners:{
		select: {
			fn:function(e)
			{
			      Ext.getCmp('cmb_modeloElementos').reset();
			      Ext.getCmp('cmb_elementos').reset();	
			      Ext.getCmp('cmb_modeloElementos').setDisabled(false);			      
			      storeModeloElementos.proxy.extraParams = { tipoElemento:e.getValue()};
			      storeModeloElementos.load();
			}
		  }	      
	  }
    });
         
    storeModeloElementos = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlGetModeloElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
		[
			{name:'idModeloElemento', mapping:'idModeloElemento'},
			{name:'nombreModeloElemento', mapping:'nombreModeloElemento'}			
		],
		autoLoad: false
    });
    
    comboModeloElementos = new Ext.form.ComboBox({
        id: 'cmb_modeloElementos',
        name: 'cmb_modeloElementos',        
	renderTo:'modeloElemento',
        emptyText: 'Seleccione Cliente',
        store: storeModeloElementos,
	emptyText: 'Seleccione Modelo elemento',
        displayField: 'nombreModeloElemento',
        valueField: 'idModeloElemento',        
	width: 200,                
	queryMode: "remote",
	emptyText: '',
	listeners:{
	      select: {
		      fn:function(e)
		      {
			    Ext.getCmp('cmb_elementos').reset();	
			    Ext.getCmp('cmb_elementos').setDisabled(false);			      
			    storeElemento.proxy.extraParams = { modeloElemento:e.getValue() , tipoElemento:Ext.getCmp('cmb_tipoElemento').getValue()};
			    storeElemento.load();
		      }
		}	      
	  }
    });
        
    storeElemento = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlGetElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
		[
			{name:'idElemento', mapping:'idElemento'},
			{name:'nombreElemento', mapping:'nombreElemento'}			
		],
		autoLoad: false
    });
    
    comboElementos = new Ext.form.ComboBox({
        id: 'cmb_elementos',
        name: 'cmb_elementos',        
	renderTo:'elemento',
        emptyText: 'Seleccione Cliente',
        store: storeElemento,
	emptyText: 'Seleccione Elemento',
        displayField: 'nombreElemento',
        valueField: 'idElemento',        
	width: 200,                
	queryMode: "remote",
	emptyText: ''	
    });
        
    storeTipoCasoTarea = Ext.create('Ext.data.Store', {
      
	  fields: ['opcion', 'valor'],
	  data: 
	  [{
	      "opcion": "Caso",
	      "valor": "C"
	      }, {
	      "opcion": "Tarea",
	      "valor": "T"
	      }		   
	  ]
    });

    comboTipoCasoTarea = new Ext.form.ComboBox({
        id: 'cmb_tipoCasoTarea',
        name: 'cmb_tipoCasoTarea',        
	renderTo:'tipoCasoTarea',        
        store: storeTipoCasoTarea,
	emptyText: 'Seleccione Tipo',
        displayField: 'opcion',
        valueField: 'valor',        
	width: 200,                
	queryMode: "remote",
	emptyText: ''	
    });
    
    Ext.getCmp('cmb_modeloElementos').setDisabled(true);
    Ext.getCmp('cmb_elementos').setDisabled(true);
    
    $("#msg").empty();
    $("#hiddenFile").val("");
    $("#msg").hide();
});

function reset()
{
        var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Procesando...');
		      },
		      scope: this
		  },
		  'requestcomplete': {
		      fn: function (con, res, opt) {
			  Ext.get(document.body).unmask();
		      },
		      scope: this
		  },
		  'requestexception': {
		      fn: function (con, res, opt) {
			  Ext.get(document.body).unmask();
		      },
		      scope: this
		  }
	      }
	}); 
	
	var filePath = $('#hiddenFile').val();	
	
	if(filePath != '' )
	{
	      conn.request({
		      method: 'POST',
		      params :{
			    filePath: filePath			
		      },
		      url: strUrlResetFile,
		      success: function(response){			      
			      var json = Ext.JSON.decode(response.responseText);
			      if(json.success)
			      {
				      Ext.Msg.alert('Mensaje','Archivo desagregado');
			      }
			      else
			      {
				      Ext.Msg.alert('Alerta ','Ha ocurrido un error, intente nuevamente');
			      }
		      },
		      failure: function(rec, op) {
			      var json = Ext.JSON.decode(op.response.responseText);
			      Ext.Msg.alert('Alerta ',json.mensaje);
		      }
	      });
	
	}else Ext.Msg.alert('Alerta ',"No existe archivo procesado");
    
}

function guardar()
{  
	var nombreDocumento    = document.getElementById("telconet_schemabundle_info_documentotype_nombreDocumento").value;
	var modulo             = document.getElementById("telconet_schemabundle_info_documentotype_modulo").value;
	var tipoDocumento      = document.getElementById("telconet_schemabundle_info_documentotype_tipoDocumentoGeneralId").value;
	var extensionDoc       = document.getElementById("telconet_schemabundle_info_documentotype_tipoDocumentoId").value;
	var hiddenFile         = document.getElementById("hiddenFile").value;
	var nameFile           = document.getElementById("nameFile").value;
	var strModuloAdicional = document.getElementById("strModuloAdicional").value;
			
	//Validaciones de campos requeridos
	if(nombreDocumento == '')
	{
	      Ext.Msg.alert("Alerta","Debe ingresar el nombre del documento");
	      return false;    
	}
	else if(modulo == 'Escoja el Modulo')
	{
	      Ext.Msg.alert("Alerta","Debe escoger el módulo de correspondencia");
	      return false;    
	}
	else if(tipoDocumento == 'Escoja tipo Documento')
	{
	      Ext.Msg.alert("Alerta","Debe escoger el Tipo de Documento");
	      return false;    
	}
	else if(extensionDoc == 'Escoja Extension')
	{
	      Ext.Msg.alert("Alerta","Debe escoger la extension del Documento");
	      return false;    
	}
	else if(hiddenFile == '')
	{
	      Ext.Msg.alert("Alerta","Debe escoger el documento a subir");
	      return false;    
	}
	
	var login           = Ext.getCmp('cmb_cliente').getValue();
	var numeroDocumento = document.getElementById("numeroDocumento").value;
	var descripcion     = document.getElementById("telconet_schemabundle_info_documentotype_mensaje").value;
	var tipoElemento    = Ext.getCmp('cmb_tipoElemento').getValue();
	var modeloElemento  = Ext.getCmp('cmb_modeloElementos').getValue();
	var elemento        = Ext.getCmp('cmb_elementos').getValue();		
	var tareaCaso       = document.getElementById("numeroTareaCaso").value;
	var tipoTareaCaso   = Ext.getCmp('cmb_tipoCasoTarea').getValue();
	
	var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Procesando...');
		      },
		      scope: this
		  },
		  'requestcomplete': {
		      fn: function (con, res, opt) {
			  Ext.get(document.body).unmask();
		      },
		      scope: this
		  },
		  'requestexception': {
		      fn: function (con, res, opt) {
			  Ext.get(document.body).unmask();
		      },
		      scope: this
		  }
	      }
	}); 
	
	conn.request({
		method: 'POST',
		params :{
		      nombreDocumento: nombreDocumento,
		      modulo: modulo,
		      tipoDocumento: tipoDocumento,  //Tipo documento id
	              extensionDoc:extensionDoc,     //Tipo documento general id		      
		      hiddenFile: hiddenFile,        //Ruta fisica Archivo
		      nameFile: nameFile,            //Nombre Archivo
		      login: login,
		      numeroDocumento: numeroDocumento,
		      tipoElemento:tipoElemento,
		      modeloElemento: modeloElemento,
		      elemento: elemento,
		      descripcion:descripcion,
		      tareaCaso: tareaCaso,
		      tipoTareaCaso:tipoTareaCaso,
                      strModuloAdicional: strModuloAdicional
		},
		url: strUrlCreate,
	        success: function(response)
		{							      
                    var json = Ext.JSON.decode(response.responseText);

                    if(json.success == true)
                    {			
                        window.location = json.strUrlShow;
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta ',json.mensaje);						
                    }
		}		
	});
  
  
}
