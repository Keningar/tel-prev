/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.QuickTips.init();
Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
	store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: strUrlGridFirma,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: ''
			}
		},
		fields:
		[
                {name: 'idPersonaEmpresaRol',               mapping: 'idPersonaEmpresaRol'},
                {name: 'idPersona',                         mapping: 'idPersona'},
                {name: 'tipoIdentificacion',                mapping: 'tipoIdentificacion'},
                {name: 'identificacion',                    mapping: 'identificacion'},
                {name: 'nombres',                           mapping: 'nombres'},
                {name: 'apellidos',                         mapping: 'apellidos'},
                {name: 'nacionalidad',                      mapping: 'nacionalidad'},
                {name: 'direccion',                         mapping: 'direccion'},
                {name: 'idDepartamento',                    mapping: 'idDepartamento'},
                {name: 'nombreDepartamento',                mapping: 'nombreDepartamento'},
                {name: 'idCanton',                          mapping: 'idCanton'},
                {name: 'nombreCanton',                      mapping: 'nombreCanton'},
                {name: 'idDocumentoRelacion',               mapping: 'idDocumentoRelacion'},
                {name: 'idDocumento',                       mapping: 'idDocumento'},
                {name: 'ubicacionFisicaDocumento',          mapping: 'ubicacionFisicaDocumento'},
                {name: 'actionSubirDoc',                    mapping: 'actionSubirDoc'},
                {name: 'actionVerDoc',                      mapping: 'actionVerDoc'},
                {name: 'actionEliminarDoc',                 mapping: 'actionEliminarDoc'}
		],
		autoLoad: true
	});
   
    
    grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        height: 400,
        store: store,
        viewConfig: { enableTextSelection: true },  
        loadMask: true,
        frame: false,
        listeners:
        {
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
            },
            viewready: function (grid) {
                var view = grid.view;
                
                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
                                        columnText      = view.getRecord(parent).get(columnDataIndex).toString();

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
                        }
                    }
                });

            }                                    
        },
        columns:
		[		
			{
			  id: 'idPersonaEmpresaRol',
			  header: 'idPersonaEmpresaRol',
			  dataIndex: 'idPersonaEmpresaRol',
			  hidden: true,
			  hideable: false
			},	
			{
			  id: 'idPersona',
			  header: 'idPersona',
			  dataIndex: 'idPersona',
			  hidden: true,
			  hideable: false
			},
            {
			  id: 'idDocumentoRelacion',
			  header: 'idDocumentoRelacion',
			  dataIndex: 'idDocumentoRelacion',
			  hidden: true,
			  hideable: false
			},
            {
			  id: 'idDepartamento',
			  header: 'idDepartamento',
			  dataIndex: 'idDepartamento',
			  hidden: true,
			  hideable: false
			},
            {
			  id: 'nombreDepartamento',
			  header: 'Departamento',
			  dataIndex: 'nombreDepartamento',
			  hidden: false,
			  hideable: false,
              width: 120
			},
            
            {
			  id: 'tipoIdentificacion',
			  header: 'Tipo Identificación',
			  dataIndex: 'tipoIdentificacion',
			  hidden: false,
			  hideable: false
			},
            {
			  id: 'identificacion',
			  header: 'Identificación',
			  dataIndex: 'identificacion',
			  hidden: false,
			  hideable: false
			},	
			{
			  id: 'nombres',
			  header: 'Nombres',
			  dataIndex: 'nombres',
			  hidden: false,
			  hideable: false,
              width: 180
			},	
			{
			  id: 'apellidos',
			  header: 'Apellidos',
			  dataIndex: 'apellidos',
			  hidden: false,
			  hideable: false,
              width: 180
			},
            {
			  id: 'nacionalidad',
			  header: 'Nacionalidad',
			  dataIndex: 'nacionalidad',
			  hidden: false,
			  hideable: false
			},
            {
			  id: 'nombreCanton',
			  header: 'Cantón',
			  dataIndex: 'nombreCanton',
			  hidden: false,
			  hideable: false,
              width: 120
			},
            {
			  id: 'direccion',
			  header: 'Dirección',
			  dataIndex: 'direccion',
			  hidden: false,
			  hideable: false,
              width: 200
			},
			{
				xtype: 'actioncolumn',
				header: 'Acciones',
				width: 150,
				items: 
				[	
					{
                        getClass: function(v, meta, rec)
                        {
                            var permiso = $("#ROLE_338-3");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ rec.data.actionSubirDoc = "icon-invisible"; }

                            if (rec.data.actionSubirDoc == "icon-invisible")
                            {
                                this.items[0].tooltip = ''; 
                            }   
                            else
                            {
                                this.items[0].tooltip = 'Subir Firma';
                            }

                            return rec.get('actionSubirDoc');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_338-3");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ rec.data.actionSubirDoc = "icon-invisible"; }

                            if(rec.get('actionSubirDoc')!="icon-invisible")
                            {
                                subirFirma(rec);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                            }
                        }
					},
					{
                        getClass: function(v, meta, rec)
                        {
                            var permiso = $("#ROLE_338-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ rec.data.actionVerDoc = "icon-invisible"; }
                            if (rec.get('actionVerDoc') == "icon-invisible")
                                this.items[1].tooltip = '';
                            else
                                this.items[1].tooltip = 'Ver Firma';

                            return rec.get('actionVerDoc');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_338-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ rec.data.actionVerDoc = "icon-invisible"; }

                            if(rec.get('actionVerDoc')!="icon-invisible")
                            {
                                verFirma(rec);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                            }
                        }
					},
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = $("#ROLE_338-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ rec.data.actionEliminarDoc = "icon-invisible"; }

                            if (rec.get('actionEliminarDoc') == "icon-invisible")
                                this.items[2].tooltip = '';
                            else
                                this.items[2].tooltip = 'Eliminar Firma';

                            return rec.get('actionEliminarDoc');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_338-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ rec.data.actionEliminarDoc = "icon-invisible"; }

                            if(rec.get('actionEliminarDoc')!="icon-invisible")
                            {
                                eliminarFirma(rec);
                            } 
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                            }
                        }
					}
                ]
			}
		],
		bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    
    /***********************FILTROS***********************/    
    var storeCantones = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlCantonesEmpleados,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'strValue',  mapping: 'strValue'},
            {name: 'strNombre', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
            
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        collapsible : true,
        collapsed: true,
        width: '100%',
        title: 'Criterios de busqueda',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        defaults: 
        {
            bodyStyle: 'padding:10px'
        },


        buttons: [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function(){ buscar();}
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function(){ limpiar();}
                    }

                ],                
        items: 
                [	
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtNombres',
                        fieldLabel: 'Nombres',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:250},
                    {
                        xtype: 'textfield',
                        id: 'txtApellidos',
                        fieldLabel: 'Apellidos',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:100},


                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtIdentificacion',
                        fieldLabel: 'Identificacion',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:250},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Canton:',
                        id: 'cmbCanton',
                        name: 'cmbCanton',
                        store: storeCantones,
                        displayField: 'strNombre',
                        valueField: 'strValue',
                        queryMode: 'remote',
                        emptyText: 'Seleccione',
                        forceSelection: true
                    },
                    {html:"&nbsp;",border:false,width:100}
                ],	
                renderTo: 'filtro'
            });
	
});

/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.canton = Ext.getCmp('cmbCanton').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombres').value="";
    Ext.getCmp('txtNombres').setRawValue("");
    Ext.getCmp('txtApellidos').value="";
    Ext.getCmp('txtApellidos').setRawValue("");
    Ext.getCmp('txtIdentificacion').value="";
    Ext.getCmp('txtIdentificacion').setRawValue("");
    Ext.getCmp('cmbCanton').value="Todos";
    Ext.getCmp('cmbCanton').setRawValue("Todos");
    
    store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.canton = Ext.getCmp('cmbCanton').value;
    
    store.loadData([],false);
    store.currentPage = 1;
    store.load();
}


function subirFirma(rec)
{
    var idPersonaEmpresaRol=rec.get('idPersonaEmpresaRol');
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

    var formPanel = Ext.create('Ext.form.Panel',
    {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',
        
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },

        items: [
            {
                xtype: 'filefield',
                id: 'form-file',
                name: 'archivo',
                emptyText: 'Seleccione un Archivo',
                buttonText: 'Browse',
                accept: ['jpg', 'png'],
                allowBlank: false,
                buttonConfig: {
                    iconCls: 'upload-icon'
                },
                listeners: 
                {
                    validitychange : function(me) {
                          var indexofPeriod = me.getValue().lastIndexOf("."),
                              uploadedExtension = me.getValue().substr(indexofPeriod + 1, me.getValue().length - indexofPeriod);

                          if (!Ext.Array.contains(this.accept, uploadedExtension)){
                              me.setActiveError('Por favor sólo suba archivos con las siguientes extensiones :  ' + this.accept.join() + ' !');
                              Ext.MessageBox.show({
                                  title   : 'Error en el Tipo de Archivo',
                                  msg   : 'Por favor sólo suba archivos con las siguientes extensiones :  ' + this.accept.join() + ' !',
                                  buttons : Ext.Msg.OK,
                                  icon  : Ext.Msg.ERROR,
                                  fn: function(buttonId) {
                                      win.destroy();
                                  }
                              });
                              me.setRawValue(null);
                              
                          }
                      }
                }
            }
        ],
        
        buttons: 
        [
            {
            text: 'Subir',
            handler: function(){
                var form = this.up('form').getForm();
                if(form.isValid())
                {
                    form.submit({
                    url: strUrlSubirFirma,
                    params :{
                        idPersonaEmpresaRol    : idPersonaEmpresaRol
                    },
                    waitMsg: 'Procesando Archivo...',
                    success: function(fp, o)
                    {
                        Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                        if(btn=='ok')
                        {
                             win.destroy();
                             store.load();
                        }
                        });
                    },
                    failure: function(fp, o) {
                     Ext.Msg.alert("Alerta",o.result.respuesta);
                    }
                    });
                }
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                win.destroy();
            }
        }
    ]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Firma de '+rec.get('nombres')+' '+rec.get('apellidos'),
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function verFirma(rec)
{
    var rutaFisica = rec.get('ubicacionFisicaDocumento');
    var posicion = rutaFisica.indexOf('/public')
    window.open(rutaFisica.substring(posicion,rutaFisica.length));
}

function eliminarFirma(rec)
{
    Ext.Msg.confirm('Alerta','Se eliminará la firma de '+rec.get('nombres')+' '+rec.get('apellidos')+'. Desea continuar?', function(btn){
        if(btn=='yes'){
            Ext.Ajax.request({
                url: strUrlEliminarFirma,
                method: 'post',
                params: { idDocumento : rec.get('idDocumento')},
                success: function(response){
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