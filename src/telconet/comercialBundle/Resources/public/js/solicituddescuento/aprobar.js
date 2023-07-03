Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 15;
            var store='';
            var motivo_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;
            var arrayParametros = [];

            Ext.onReady(function(){

            Ext.define('modelMotivo', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idMotivo', type: 'string'},
                    {name: 'descripcion',  type: 'string'},
                    {name: 'idRelacionSistema',  type: 'string'}                 
                ]
            });

            var motivo_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelMotivo",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_motivos,
		        reader: {
		            type: 'json',
		            root: 'motivos'
                        }
                    }
            });	
            var motivo_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: motivo_store,
                labelAlign : 'left',
                id:'idMotivo',
                name: 'idMotivo',
				valueField:'idMotivo',
                displayField:'descripcion',
                fieldLabel: 'Motivo Rechazo',
                labelAlign:'right',
				width: 400,
				triggerAction: 'all',
				selectOnFocus:true,
				lastQuery: '',
				mode: 'local',
				allowBlank: true,
				listeners: {
					select:
					function(e) {
						//alert(Ext.getCmp('idestado').getValue());
						motivo_id = Ext.getCmp('idMotivo').getValue();
						relacion_sistema_id=e.displayTplData[0].idRelacionSistema;
					},
					click: {
						element: 'el', //bind to the underlying el property on the panel
						fn: function(){ 
							motivo_id='';
							relacion_sistema_id='';
							motivo_store.removeAll();
							motivo_store.load();
						}
					}			
				}
            });                
                
                
                
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
            
            TFNombre = new Ext.form.TextField(
            {
                id: 'nombre',
                fieldLabel: 'Nombre',
                xtype: 'textfield'
            });
            TFApellido = new Ext.form.TextField(
            {
                id: 'apellido',
                fieldLabel: 'Apellido',
                xtype: 'textfield'
            });
            TFRazonSocial = new Ext.form.TextField(
            {
                id: 'razonSocial',
                fieldLabel: 'Razon Social',
                xtype: 'textfield'
            });
            
            TFUsuarioCreacion = new Ext.form.TextField(
            {
                id: 'usuarioCreacion',
                fieldLabel: 'Usuario Creación',
                xtype: 'textfield'
            });
            
            TFLogin = new Ext.form.TextField(
            {
                id: 'login',
                fieldLabel: 'Login',
                xtype: 'textfield'
            });
            
            
            
            
                
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'string'},
                            {name:'cliente', type: 'string'},
			    {name:'servicio', type: 'string'},
                            {name:'login', type: 'string'},
                            {name:'motivo', type: 'string'},
                            {name:'asesor', type: 'string'},
                            {name:'vOriginal', type: 'string'},
                            {name:'descuento', type: 'string'},
                            {name:'observacion', type: 'string'},
                            {name:'feCreacion', type: 'string'},
                            {name:'usrCreacion', type: 'string'},
                            {name:'linkVer', type: 'string'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store,
                        reader: {
                            type: 'json',
                            root: 'solicitudes',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',
                                     fechaHasta:'',
                                     nombre:'',
                                     apellido:'',
                                     razonSocial:'',
                                     usuarioCreacion:'',
                                     login:''
                                    },
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.fechaDesde      = Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta      = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.nombre          = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido        = Ext.getCmp('apellido').getValue();
                store.getProxy().extraParams.razonSocial     = Ext.getCmp('razonSocial').getValue();
                store.getProxy().extraParams.usuarioCreacion = Ext.getCmp('usuarioCreacion').getValue();
                store.getProxy().extraParams.login           = Ext.getCmp('login').getValue();
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 15}});    



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


                var listView = Ext.create('Ext.grid.Panel', {
                    width:1200,
                    autoHeight: true,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        motivo_cmb,
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
					, {
                                        iconCls: 'icon_check',
                                        text: 'Aprobar',
                                        disabled: false,
                                        itemId: 'aprobar',
                                        scope: this,
                                        handler: function(){aprobarAlgunos()}
                                    }, {
                                        iconCls: 'icon_delete',
                                        text: 'Rechazar',
                                        disabled: false,
                                        itemId: 'rechazar',
                                        scope: this,
                                        handler: function(){rechazarAlgunos()}
                                    }                                
                                ]}],                    
                    renderTo: Ext.get('lista_prospectos'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: [new Ext.grid.RowNumberer(),
                    {
                        text: 'Fecha Creacion',
                        dataIndex: 'feCreacion',
                        align: 'center',
                        width: 130			
                    },{
                        text: 'Cliente',
                        width: 170,
                        dataIndex: 'cliente',
                        align: 'center'
                    },{
                        text: 'Asesor',
                        width: 170,
                        dataIndex: 'asesor',
                        align: 'center'
                    },{
                        text: 'Login',
                        width: 100,
                        dataIndex: 'login',
                        align: 'center'
                    },                        
                    {
                        text: 'Servicio',
                        width: 160,
                        dataIndex: 'servicio',
                        align: 'center'
                    },{
                        text: 'Motivo',
                        width: 160,
                        dataIndex: 'motivo',
                        align: 'center'
                    },{
                        text: 'Valor Original',
                        dataIndex: 'vOriginal',
                        align: 'center',
                        width: 80			
                    },{
                        text: 'Descuento',
                        dataIndex: 'descuento',
                        align: 'center',
                        width: 90			
                    },{
                        text: 'Observacion Solicitud',
                        dataIndex: 'observacion',
                        align: 'center',
                        width: 300		
                    },{
                        text: 'Usuario Creacion',
                        dataIndex: 'usrCreacion',
                        align: 'center',
                        width: 100
                        //flex: 50			
                    },{
                        text: 'Acciones',
                        width: 60,
                        renderer: renderAcciones,
                        hidden: true
                    }]
                });            


            function renderAcciones(value, p, record) {
                    var iconos='';
                    var estadoIncidencia=true;
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';			
                    return Ext.String.format(
                                    iconos,
                        value,
                        '1',
                                    'nada'
                    );
            }

            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 4,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },  
                defaults: 
                {
                    // applied to each contained panel
                    bodyStyle: 'padding:10px'
                },
                collapsible : true,
                collapsed: true,
                width: 1200,
                title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Buscar',
                            //xtype: 'button',
                            iconCls: "icon_search",
                            handler: Buscar,
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function(){ limpiar();}
                        }
                        
                        ],                

                        items: [
                                DTFechaDesde,
                                {html: "&nbsp;", border: false, width: 50},
                                DTFechaHasta,
                                {html: "&nbsp;", border: false, width: 50},
                                TFNombre,
                                {html: "&nbsp;", border: false, width: 50},
                                TFApellido,
                                {html: "&nbsp;", border: false, width: 50},
                                TFRazonSocial,
                                {html: "&nbsp;", border: false, width: 50},
                                TFUsuarioCreacion,
                                {html: "&nbsp;", border: false, width: 50},
                                TFLogin,
                                {html: "&nbsp;", border: false, width: 50},  
                                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
});

function Buscar(){

				store.load({params: {start: 0, limit: 15}});
	
	}

function eliminaDuplicados(selection)
{
    var arreglo     = [];
    var obj         = {};
    var out         = [];
    for (var i = 0; i < selection.length; ++i)
    {
        arreglo[i]      = selection[i].data.id;
        obj[arreglo[i]] = 0;
    }
    for (i in obj) 
    {
        out.push(i);
    }
    return out;
}

function aprobarAlgunos()
{
    $('#mensaje_validaciones').addClass('campo-oculto')
    var param = '';
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
        if (estado == 0)
        {
            Ext.Msg.confirm('Alerta', 'Se aprobaran las solicitudes seleccionadas. Desea continuar?', function (btn) {
                if (btn == 'yes')
                {
                    Ext.MessageBox.wait("Aprobando solicitudes...");
                    
                    if (strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN')
                    {
                        Ext.Ajax.request({
                            url: url_validaSolDesc, 
                            method: 'post',
                            params: {param: param},
                            success: function (response) {
                                Ext.MessageBox.hide();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.strStatus;
                                var strMensaje = objData.strMensaje;
                                var strParamIdsDetSolicitud = objData.strParamIdsDetSolicitud;
                                if (strStatus == 'Solicitudes No Cumplen')
                                {
                                    $('#mensaje_validaciones').removeClass('campo-oculto').html(strMensaje);
                                }                                
                                if (strParamIdsDetSolicitud != '')
                                {
                                    Ext.Ajax.request({
                                        url: url_aprobar,
                                        method: 'post',
                                        params: {param: strParamIdsDetSolicitud},
                                        success: function (response) {
                                            Ext.MessageBox.hide();
                                            var text = response.responseText;
                                            store.load();
                                            Ext.Msg.alert('Alerta', 'Transacción exitosa. '+ text );
                                        },
                                        failure: function (result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                } 
                                else
                                {
                                    Ext.Msg.alert('Error ', 'No fue posible autorizar la(s) solicitud(es) seleccionada(s), cliente(s) \n\
                                                         ya posee beneficio.');
                                }
                            },
                            failure: function ()
                            {
                                Ext.Msg.alert('Error ', 'Hay un Error: ' + result.statusText);
                            }
                        });
                    }
                    else
                    {
                        Ext.Ajax.request({
                            url: url_aprobar,
                            method: 'post',
                            params: {param: param},
                            success: function (response) {
                                Ext.MessageBox.hide();
                                var text = response.responseText;
                                store.load();
                                Ext.Msg.alert('Alerta', 'Transacción exitosa. '+ text);
                            },
                            failure: function (result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                }                
            });

        } else
        {
            alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
        }
    } 
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}

function rechazarAlgunos(){
    $('#mensaje_validaciones').addClass('campo-oculto').html('');    
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
          
        if (Ext.getCmp('idMotivo').getValue()){  
        Ext.Msg.confirm('Alerta','Se rechazaran las solicitudes seleccionadas. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.MessageBox.wait("Rechazando solicitudes...");
                Ext.Ajax.request({
                    url: url_rechazar,
                    method: 'post',
                    params: { param : param, motivoId:motivo_id },
                    success: function(response){
                        Ext.MessageBox.hide();
                        var text = response.responseText;
                        store.load();
                        Ext.Msg.alert('Alerta','Transacción exitosa');
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
       }
       else
       {
        alert('Debe seleccionar un motivo para poder rechazar la(s) nota(s) de credito.');                
       } 
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
}


function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");  
    Ext.getCmp('nombre').setValue("");
    Ext.getCmp('apellido').setValue("");
    Ext.getCmp('razonSocial').setValue("");
    Ext.getCmp('usuarioCreacion').setValue("");
    Ext.getCmp('login').setValue("");
    $('#mensaje_validaciones').addClass('campo-oculto').html('');    
}
