/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();
Ext.onReady(function() {    
	Ext.tip.QuickTipManager.init();
        storePlantillas = new Ext.data.Store({ 
            pageSize: 10,
            total: 'total',
            proxy: {
                type: 'ajax',
                url : 'grid',
                timeout: 99999999,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'Todos',
                    login: '',
                    tipo: 'Notificacion Externa' //Se crea este parametro para poder filtrar la busqueda
                }
            },
            fields:
                      [
                        {name:'id_documento', mapping:'id_documento'},
                        {name:'nombre', mapping:'nombre'},
                        {name:'usuario', mapping:'usuario'},
                        {name:'estado', mapping:'estado'},
			{name:'tipo', mapping:'tipo'},
                        {name:'action1', mapping:'action1'},
                        {name:'action2', mapping:'action2'},
                        {name:'action3', mapping:'action3'}
                      ],
            autoLoad: true
        });
   
    var isEvent = true;
    
    sm = new Ext.selection.CheckboxModel(
        {
            checkOnly: true,
            showHeaderCheckbox: false,
            listeners:
                {
                    selectionchange: function(model, selection)
                    {
                        intSizeGrid = selection.length;
                        
                        if (isEvent && model.lastFocused != null)
                        {
                            if ('Eliminado' == model.lastFocused.get('estado'))
                            {
                                model.doDeselect(model.lastFocused, false);
                                model.lastFocused = null;
                                Ext.Msg.show(
                                    {
                                        title: 'Alerta',
                                        msg: 'Registro tiene estado "Eliminado", No puede ser seleccionado',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.WARNING
                                    });
                                intSizeGrid -= 1;
                            }
                        }
                        
                        intSizeGrid = intSizeGrid < 0 ? 0 : intSizeGrid;
                        gridPlantillas.down('#delete').setDisabled(intSizeGrid <= 0);
                        Ext.getCmp('labelSeleccion').setText(intSizeGrid + ' seleccionados');
                    }
                }
        });

    gridPlantillas = Ext.create('Ext.grid.Panel', {
        id: 'gridPlantillas',
        width: 800,
        height: 400,
        store: storePlantillas,
		viewConfig:
            {
                enableTextSelection: true,
                loadingText: '<b>Cargando Plantillas, Por favor espere...',
                emptyText: '<center><br/><b/>*** No se encontraron Plantillas ***',
                loadMask: true
            }, 
        loadMask: true,
        frame: false,
        selModel: sm,
        listeners:
        {
            sortchange: function()
            {
                gridPlantillas.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
            }
        },
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    {
                        iconCls: 'icon_add',
                        text: 'Seleccionar Activos',
                        itemId: 'select',
                        scope: this,
                        handler: function()
                        {
                            gridPlantillas.getStore().each(function(record)
                            {
                                isEvent = false;
                                if ('Eliminado' != record.get('estado'))
                                {
                                    gridPlantillas.getSelectionModel().select(record, true);
                                }
                            });

                            isEvent = true;
                        }
                    },
                    {
                        iconCls: 'icon_limpiar',
                        text: 'Desmarcar Seleccionados',
                        itemId: 'clear',
                        scope: this,
                        handler: function()
                        {
                            isEvent = false;
                            gridPlantillas.getSelectionModel().deselectAll(true);
                            isEvent = true;
                        }
                    },
                    {
                        id: 'labelSeleccion',
                        cls: 'greenTextGrid',
                        text: '0 seleccionados',
                        scope: this
                    },
                    { xtype: 'tbfill' }, // alinea los items siguientes a la derecha
                    /*{
                        iconCls: 'icon_exportar',
                        text: 'Exportar',
                        scope: this,
                        handler: function(){ exportarExcel();}
                    },*/
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        id: 'delete',
                        itemId: 'delete',
                        disabled:  true,
                        scope: this,
                        handler: function(){ eliminarAlgunos();}
                    }
                ]}
        ],                  
        columns:[
                {
                  id: 'id_documento',
                  header: 'No',
                  dataIndex: 'id_documento',
                  hideable: false,
                  hidden: true,
                  width: 100
                },
                {
                  id: 'nombre',
                  header: 'Nombre',
                  dataIndex: 'nombre',
                  width: 340
                },
                {
                  id: 'usuario',
                  header: 'Creador',
                  dataIndex: 'usuario',
                  width: 100
                },
                {
                  id: 'tipo',
                  header: 'Clase Documento',
                  dataIndex: 'tipo',
                  width: 150
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 80,
                  sortable: true,
                  renderer: function(estado)
                  {
                      return estado == 'Modificado' ? 'Activo' : estado;
                  }
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 100,
                    items: [{
                        getClass: function(v, meta, rec) {return rec.get('action1');},
                        tooltip: 'Ver Plantilla',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storePlantillas.getAt(rowIndex);			    
                            window.location = rec.get('id_documento')+"/show";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action2') == "icon-invisible") 
                                this.items[1].tooltip = '';
                            else 
                                this.items[1].tooltip = 'Editar';
                            
                            return rec.get('action2');
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storePlantillas.getAt(rowIndex);
                            if(rec.get('action2')!="icon-invisible")			        
                                window.location = rec.get('id_documento')+"/edit";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action3') == "icon-invisible") 
                                this.items[2].tooltip = '';
                            else 
                                this.items[2].tooltip = 'Eliminar';
                            
                            return rec.get('action3');
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storePlantillas.getAt(rowIndex);
                            if(rec.get('action3')!="icon-invisible")
                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                    if(btn=='yes'){
                                        Ext.Ajax.request({
                                            url: "deleteAjax",
                                            method: 'post',
                                            params: { param : rec.get('id_documento')},
                                            success: function(response){
                                                var text = response.responseText;
                                                storePlantillas.load();
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });    
                            }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: storePlantillas,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    
        
/**************************************************************************/
//			CREACION DE COMBO TIPO PLANTILLA
//                      	  USANDO AJAX
/**************************************************************************/
	comboTipoPlantillaStore_index = new Ext.data.Store({ 
		total: 'total',
		proxy: {
			type: 'ajax',
            timeout: 99999999,
			url: 'getTipoPlantilla',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				nombre: 'Notificacion Externa', //Busca todas las ocurrencias que pertenezcan a una plantilla
				estado: 'Activo'     //Estado Activo
			}
		},
		fields:
		[			
			{name:'id_clase_documento', mapping:'id_clase_documento'},
			{name:'nombre_clase_documento', mapping:'nombre_clase_documento'}
		]
	});
    
	comboTipoPlantilla_index = Ext.create('Ext.form.ComboBox', {
		id:'comboTipoPlantilla_index',
		name:'comboTipoPlantilla_index',
		store: comboTipoPlantillaStore_index,
		displayField: 'nombre_clase_documento',
		valueField: 'id_clase_documento',		
		width: 300,
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Tipo',	
        labelWidth: '5',
		queryMode: "remote",
		emptyText: ''
	});


    /************************************************************************************/
    //
    //			criterio de busqueda de plantillas creadas
    //
    /************************************************************************************/
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type:'table',
            columns: 4
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: false,
        width: 800,
        title: 'Criterios de búsqueda',
        buttons: 
            [
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
                    handler: function() 
                    {
                        Ext.getCmp('txtNombrePlantilla').setValue('');
                        Ext.getCmp('txtUsrCreacion').setValue('');
                    }
                }
            ],
        items:
            [

                {width: 60, border: false},
                comboTipoPlantilla_index,
                {width: 60, border: false},
                {
                    xtype: 'textfield',
                    id: 'txtUsrCreacion',
                    style: 'white-space: nowrap',
                    fieldLabel: 'Login Creación',
                    labelWidth: '9',
                    value: '',
                    width: 250
                },
                {width: 60, border: false},
                {
                    xtype: 'textfield',
                    id: 'txtNombrePlantilla',
                    fieldLabel: 'Nombre',
                    labelWidth: '5',
                    value: '',
                    width: 300
                },
                {width: 60, border: false},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Estado',
                    labelWidth: '9',
                    id: 'sltEstado',
                    value: 'Todos',
                    name: 'sltEstado',
                    store: [
                        ['Todos', 'Todos'],
                        ['Activo', 'Activo'],
                        ['Eliminado', 'Eliminado']
                    ],
                    width: 250
                }
            ],
        renderTo: 'filtro'
    });     
    
});

function eliminarAlgunos(){
    var arrayPlantillas = [];
    if(sm.getSelection().length > 0)
    {
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            arrayPlantillas.push(sm.getSelection()[i].data.id_documento);
        }
      
        Ext.Msg.confirm('Alerta','Se eliminarán los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "deleteAjax",
                    method: 'post',
                    params: { param : arrayPlantillas.join("|")},
                    success: function(response){
                        var text = response.responseText;
                        storePlantillas.load();
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }
}

function exportarExcel()
{
    window.open("exportarConsulta");
}

function buscar()
{
    if (isNaN(comboTipoPlantilla_index.getValue()) || comboTipoPlantilla_index.getValue() == null)
    {
        tipo = 'Notificacion Externa';
    }
    else
    {
        tipo = parseInt(comboTipoPlantilla_index.getValue());
    }

    storePlantillas.proxy.extraParams =
        {
            tipo: tipo,
            nombre: Ext.getCmp('txtNombrePlantilla').value,
            login: Ext.getCmp('txtUsrCreacion').value,
            estado: Ext.getCmp('sltEstado').value
        };

    storePlantillas.load();
}