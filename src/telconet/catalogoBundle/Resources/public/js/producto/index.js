/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){
                
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
        var txtDescripcion = Ext.create('Ext.form.TextField', {
            xtype: 'textfield',
            fieldLabel: 'Descripcion',
            store: states,
            id:'txtDescripcion',
            name: 'txtDescripcion',
            value:'',
            width: 325
            
	    });
        //CREAMOS DATA STORE PARA LOS NOMBRES TECNICOS
        Ext.define('modelNombresTecnicos', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'nombre', type: 'string'}              
            ]
        });	
        var states = Ext.create('Ext.data.Store', {
            autoLoad: false,
            model: "modelNombresTecnicos",
            proxy: {
                type: 'ajax',
                url : url_nombres_tecnicos,
                reader: {
                    type: 'json',
                    root: 'nombres'
                        }
                    }
        });	        
	    var cmbNombreTecnico = Ext.create('Ext.form.ComboBox', {
            xtype: 'combobox',
            fieldLabel: 'Nombre Tecnico',
            store: states,
            queryMode: 'local',
            id:'idNombreTecnico',
            name: 'idNombreTecnico',
            valueField:'nombre',
            displayField:'nombre',		  
            width: 325,
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            listeners: {
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            if(states.getCount()==0)
                            {
                                states.removeAll();
                                states.load();
                            }
                        }
                    }			
                }
	    });
        
        //CREAMOS DATA STORE PARA LOS GRUPOS
        Ext.define('modelGrupo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idGrupo', type: 'string'},
                {name: 'descripcion',  type: 'string'}
            ]
        });
        var grupo = Ext.create('Ext.data.Store', {
            autoLoad: false,
            model: "modelGrupo",
            proxy: {
                type: 'ajax',
                url : url_grupo,
                reader: {
                    type: 'json',
                    root: 'strGrupo'
                        }
                    }
        });	        
	    var cmbGrupo = Ext.create('Ext.form.ComboBox', {
            xtype: 'combobox',
            fieldLabel: 'Grupo',
            store: grupo,
            queryMode: 'local',
            id:'idGrupo',
            name: 'idGrupo',
            valueField:'descripcion',
            displayField:'descripcion',		  
            width: 325,
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            listeners: {
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            if(grupo.getCount()==0)
                            {
                                grupo.removeAll();
                                grupo.load();
                            }
                        }
                    }			
                }
	    });
        
        //CREAMOS DATA STORE PARA LOS ESTADOS
        Ext.define('modelEstado', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idestado', type: 'string'},
                {name: 'codigo',  type: 'string'},
                {name: 'descripcion',  type: 'string'}                    
            ]
        });	
        var estado_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url : url_estados,
            reader: {
                type: 'json',
                root: 'estados'
                    }
                }
        });	
        var estado_cmb = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: estado_store,
            labelAlign : 'left',
            id:'idestado',
            name: 'idestado',
            valueField:'descripcion',
            displayField:'descripcion',
            fieldLabel: 'Estado',
            width: 325,
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,	
					
            listeners: {
                    select:
                    function(e) {
                        estado_id = Ext.getCmp('idestado').getValue();
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){
                            if(estado_store.getCount()==0)
                            {
                                estado_store.removeAll();
                                estado_store.load();
                            }
                        }
                    }			
                }
            });


                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'idproducto', type: 'string'},
							{name:'codigo', type: 'string'},
                            {name:'nombreTecnico', type: 'string'},
                            {name:'descripcion', type: 'string'},
                            {name:'tipo', type: 'string'},
                            {name:'instalacion', type: 'string'},
                            {name:'strGrupo', type: 'string'},
                            {name:'strSubgrupo', type: 'string'},
                            {name:'funcionPrecio', type: 'string'},
                            {name:'funcionCosto', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'},
                            {name:'linkEditar', type: 'string'},
                            {name:'linkEliminar', type: 'string'},
                            {name:'strRequiereComisionar', type: 'string'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store_grid,
                        reader: {
                            type: 'json',
                            root: 'productos',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombreTecnico:'',descripcion:'',strGrupo:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
                            store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
                            store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                            store.getProxy().extraParams.nombreTecnico= Ext.getCmp('idNombreTecnico').getValue();  
                            store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();
                            store.getProxy().extraParams.descripcion= Ext.getCmp('txtDescripcion').getValue();
                            store.getProxy().extraParams.strGrupo= Ext.getCmp('idGrupo').getValue();
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 10}});    



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
                    width:980,
                    height:275,
                    collapsible:false,
                    title: '',
                    selModel: sm,                  
                    renderTo: Ext.get('lista_prospectos'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando productos {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            }
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Codigo',
                        width: 70,
                        dataIndex: 'codigo'
                    },{
                        text: 'Descripcion',
                        width: 160,
                        dataIndex: 'descripcion'
                    },{
                        text: 'Nombre Tecnico',
                        width: 100,
                        dataIndex: 'nombreTecnico'
                    },{
                        text: 'Tipo',
                        dataIndex: 'tipo',
                        align: 'center',
                        width: 60
                    },{
                        text: 'Instalacion',
                        dataIndex: 'instalacion',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Grupo',
                        dataIndex: 'strGrupo',
                        align: 'right',
                        width: 90			
                    },{
                        text: 'SubGrupo',
                        dataIndex: 'strSubgrupo',
                        align: 'right',
                        width: 90			
                    },{
                        text: 'F. Precio',
                        dataIndex: 'funcionPrecio',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'F. Costo',
                        dataIndex: 'funcionCosto',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'F. Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        width: 100			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'center',
                        flex: 50
                    },{
                        text: 'Acciones',
                        width: 150,
                        renderer: renderAcciones
                    }]
                });

                var objPermiso = $("#ROLE_41-9");
                var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
                if (boolPermiso) {
                    listView.addDocked({xtype: 'toolbar',
                                        dock: 'top',
                                        align: '->',
                                        items: [
                                            //tbfill -> alinea los items siguientes a la derecha
                                            { xtype: 'tbfill' },
                                            {
                                            iconCls: 'icon_delete',
                                            text: 'Eliminar',
                                            disabled: false,
                                            itemId: 'delete',
                                            scope: this,
                                            handler: function(){eliminarAlgunos();}
                                           }]
                    
                    
                    });
                } 
                else 
                {
                    listView.addDocked({xtype: 'toolbar',
                                        dock: 'top',
                                        align: '->'
                    });
                }

    function renderAcciones(value, p, record) 
    {
        var iconos          = '';
        var strIdsProductos = '';
        var objPermiso      = null;
        var boolPermiso     = false;
        // Ingreo de Plantilla de Comisionistas, solo si posee ROL, si es empresa TN, si el Producto esta marcado que requiere comisionar
        // Y solo para Productos en estado Activo, Pendiente, Inactivo.
        objPermiso  = $("#ROLE_41-5237");
        boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso && strPrefijoEmpresa == 'TN' && record.data.strRequiereComisionar =='SI' && 
            (record.data.estado == 'Activo' || record.data.estado == 'Pendiente' || record.data.estado == 'Inactivo')) 
        {           
             iconos = iconos + '<b><a href="#" onClick="comisionPlantilla(' + record.data.idproducto + ', \'' + strIdsProductos + 
                              '\')" title="Ingreso de Plantilla de Comisiones" class="button-grid-agregarPlantillaComision"/></a></b>';
        }
        
        objPermiso = $("#ROLE_41-5297");
        boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso && strPrefijoEmpresa == 'TN' && record.data.strRequiereComisionar == 'SI')
        {
            iconos = iconos + '<b><a href="#" onClick="verLogsPlantillaComision(' + record.data.idproducto + ')" \n\
                                           title="Ver Historial de Plantilla de Comisiones" class="button-grid-logs"/></a></b>';
        }
                    
        iconos     = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';       
         
        objPermiso  = $("#ROLE_41-9");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) 
        {
            iconos = iconos + '<b><a href="#" onClick="eliminar(\'' + record.data.linkEliminar
                     + '\')" title="Eliminar" class="button-grid-delete"></a></b>';
        }
        
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
                    columns: 2,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: false,
                width: 980,
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
                                txtDescripcion,
                                {html:"&nbsp;",border:false,width:50},
                                cmbNombreTecnico,
                                estado_cmb,
                                DTFechaDesde,
                                DTFechaHasta,
                                cmbGrupo,
                                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
    });

    function Buscar()
    {
        /*if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
        {
                if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
                {
                   Ext.Msg.show({
                   title:'Error en Busqueda',
                   msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                   buttons: Ext.Msg.OK,
                   animEl: 'elId',
                   icon: Ext.MessageBox.ERROR
                        });		 

                }
                else
                {*/
                        store.load({params: {start: 0, limit: 10}});
                /*}
        }
        else
        {

                Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor Ingrese criterios de fecha.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
                     });
        }*/
    }

    function eliminar(direccion)
    {
        //alert(direccion);
        Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: direccion,
                    method: 'post',
                    success: function(response){
                        var text = response.responseText;
                        store.load();
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }
    
    function eliminarAlgunos()
    {
		var param = '';
		if(sm.getSelection().length > 0)
		{
		  var estado = 0;
		  for(var i=0 ;  i < sm.getSelection().length ; ++i)
		  {
			param = param + sm.getSelection()[i].data.idproducto;

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
			Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: url_eliminar,
						method: 'post',
						params: { param : param},
						success: function(response){
							var text = response.responseText;
							store.load();
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
			alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
		  }
		}
		else
		{
		  alert('Seleccione por lo menos un registro de la lista');
		}
	}

    function limpiar()
    {
        Ext.getCmp('fechaDesde').setRawValue("");
        Ext.getCmp('fechaHasta').setRawValue("");
        Ext.getCmp('idestado').setRawValue("");
        Ext.getCmp('txtDescripcion').setRawValue("");
        Ext.getCmp('idGrupo').setRawValue("");
        Ext.getCmp('idNombreTecnico').setRawValue("");
    }

function verLogsPlantillaComision(data)
{
    var storeHistorial = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_gridLogsPlantillaComision,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                intIdProducto: data
            }
        },
        fields:
            [
                {name: 'strUsrCreacion', mapping: 'strUsrCreacion'},
                {name: 'strFeCreacion', mapping: 'strFeCreacion'},
                {name: 'strIpCreacion', mapping: 'strIpCreacion'},
                {name: 'strGrupoRol', mapping: 'strGrupoRol'},
                {name: 'strEstado', mapping: 'strEstado'},                
                {name: 'strObservacion', mapping: 'strObservacion'}                
            ]
    });

    Ext.define('HistorialPlantilla', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strUsrCreacion', mapping: 'strUsrCreacion'},
            {name: 'strFeCreacion', mapping: 'strFeCreacion'},
            {name: 'strIpCreacion', mapping: 'strIpCreacion'},
            {name: 'strGrupoRol', mapping: 'strGrupoRol'},
            {name: 'strEstado', mapping: 'strEstado'},            
            {name: 'strObservacion', mapping: 'strObservacion'}            
        ]
    });

    //Grid Historial de Plantilla de comisionistas
    gridHistorialPlantilla = Ext.create('Ext.grid.Panel',
        {
            id: 'gridHistorialPlantilla',
            store: storeHistorial,
            columnLines: true,
            listeners:
                {
                    viewready: function(grid)
                    {
                        var view = grid.view;

                        grid.mon(view,
                            {
                                uievent: function(type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
                                    grid.recordIndex = recordIndex;
                                }
                            });

                        grid.tip = Ext.create('Ext.tip.ToolTip',
                            {
                                target: view.el,
                                delegate: '.x-grid-cell',
                                trackMouse: true,
                                autoHide: false,
                                renderTo: Ext.getBody(),
                                listeners:
                                    {
                                        beforeshow: function(tip)
                                        {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                            {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                                if (header.dataIndex != null)
                                                {
                                                    var trigger = tip.triggerElement,
                                                        parent = tip.triggerElement.parentElement,
                                                        columnTitle = view.getHeaderByCell(trigger).text,
                                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                                    {
                                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

                                                        if (columnText)
                                                        {
                                                            tip.update(columnText);
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
                                        }
                                    }
                            });

                        grid.tip.on('show', function()
                        {
                            var timeout;

                            grid.tip.getEl().on('mouseout', function()
                            {
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function()
                            {
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    }
                },
            columns:
                [
                    {
                        header: 'Usuario Creación',
                        dataIndex: 'strUsrCreacion',
                        width: 100,
                        sortable: true
                    }, {
                        header: 'Fecha Creación',
                        dataIndex: 'strFeCreacion',
                        width: 120
                    },
                    {
                        header: 'Ip Creación',
                        dataIndex: 'strIpCreacion',
                        width: 100
                    },
                    {
                        header: 'Grupo Rol',
                        dataIndex: 'strGrupoRol',
                        width: 150
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'strEstado',
                        width: 100
                    },                    
                    {
                        header: 'Observación',
                        dataIndex: 'strObservacion',
                        width: 350
                    }
                ],
            viewConfig:
                {
                    stripeRows: true,
                    enableTextSelection: true
                },
            frame: true,
            height: 300
        });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 950
                },
                items: [
                    gridHistorialPlantilla

                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Historial de la Plantilla de Comisionistas',
        modal: true,
        width: 1000,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}
