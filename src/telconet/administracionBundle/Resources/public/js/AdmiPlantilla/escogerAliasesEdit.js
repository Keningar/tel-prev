function verAliases(idPlantilla)
{
    Ext.define('AliasGestionadoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_alias',       mapping:'id_alias'},
            {name:'valor',          mapping:'valor'},
            {name:'empresa',        mapping:'empresa'},		             
            {name:'jurisdiccion',   mapping:'jurisdiccion'},
            {name: 'esCC'},
            {name:'estado',         mapping:'estado'},
            {name:'accion'}
        ]
    });
    smAliasCreados = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
                selectionchange: function(sm, selections) {
                    gridAliasCreados.down('#agregarAliasCreadoButton').setDisabled(selections.length === 0);
                }
            }
    });
    
    smAliasActuales = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                selectionchange: function(sm, selections) {
                    gridAliasActuales.down('#removeAliasActualButton').setDisabled(selections.length === 0);
                }
            }
    });
    
    var filtroPanelAliasActuales = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, 
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 470,
        title: 'Filtros',
        buttons: [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function() {
                            buscarAliasActual();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function() {
                            limpiarAliasActual();
                        }
                    }
        ],
        items: [
                {width: '1%', border: false},
                {
                    xtype: 'textfield',
                    id: 'txtCorreoAliasActual',
                    fieldLabel: 'Correo',
                    value: '',
                    labelWidth: '7',
                    width: '80%',
                    style: 'margin: 5px'
                }
        ]
    });
    
    var filtroPanelAliasCreados = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,   
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 470,
        title: 'Filtros',
        buttons: [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function() {
                            buscarAliasCreado();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function() {
                            limpiarAliasCreado();
                        }
                    }
        ],
        items: [
                {width: '1%', border: false},
                {
                    xtype: 'textfield',
                    id: 'txtCorreoAliasCreado',
                    fieldLabel: 'Correo',
                    value: '',
                    labelWidth: '7',
                    width: '80%',
                    style: 'margin: 5px'
                }
        ]
    });
    
    storeAliasCreados = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: urlGridAlias,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo',
                start:'',
                limit:''
            }
        },
        fields:
        [				
            {name:'id_alias',       mapping:'id_alias'},
            {name:'valor',          mapping:'valor'},
            {name:'estado',         mapping:'estado'},
            {name:'empresa',        mapping:'empresa'},			             
            {name:'jurisdiccion',   mapping:'jurisdiccion'},
            {name: 'esCC'}
        ],
        autoLoad: true
    });         
        
    gridAliasCreados = Ext.create('Ext.grid.Panel', {
        id: 'gridAliasCreados',
        width: 470,
        height: 330,
        store: storeAliasCreados,
        selModel: smAliasCreados,
        title: 'Alias Creados',
        viewConfig: {
            enableTextSelection: true,
            id: 'gvAliasCreados',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns: [
                    {
                        id: 'id_alias_creado',
                        header: 'IdAlias',
                        dataIndex: 'id_alias',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'valor_alias_creado',
                        header: 'Valor',
                        dataIndex: 'valor',
                        width: 200,
                        sortable: true
                    },
                    {
                        header: 'Empresa',
                        dataIndex: 'empresa',
                        width: 150,
                        sortable: true
                    },
                    {
                        header: 'Jurisdicción',
                        dataIndex: 'jurisdiccion',
                        width: 80,
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 80,
                        sortable: true
                    }
        ],
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'agregarAliasCreadoButton',
                        text: 'Agregar',
                        tooltip: 'Agregar el alias seleccionado',
                        iconCls: 'add',
                        disabled: true,
                        handler: function() {
                            agregarAlias();
                        }
                    }]
        }]
    });

    storeAliasActuales = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: urlGetPlantillaAlias,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                id: idPlantilla,
                estado: 'Activo',
                start:'',
                limit:''
            }
        },
        fields:
        [				
            {name:'id_alias',       mapping:'id_alias'},
            {name:'valor',          mapping:'valor'},
            {name:'estado',         mapping:'estado'},
            {name:'empresa',        mapping:'empresa'},			             
            {name:'jurisdiccion',   mapping:'jurisdiccion'},
            {name: 'esCC'},
            {name: 'accion'}
        ],
        autoLoad: true
    });
    
    var cellEditingAliasActuales = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    
    gridAliasActuales = Ext.create('Ext.grid.Panel', {
        id: 'gridAliasActuales',
        width: 470,
        height: 330,
        store: storeAliasActuales,       
        selModel: smAliasActuales,
        plugins: [cellEditingAliasActuales],
        columns: [
                    {
                        id: 'id_alias_actual',
                        header: 'IdAlias',
                        dataIndex: 'id_alias',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'valor_alias_actual',
                        header: 'Valor',
                        dataIndex: 'valor',
                        width: 200,
                        sortable: true
                    },
                    {
                        header: 'Es Copia?',
                        dataIndex: 'esCC',
                        width: 60,
                        editor: new Ext.form.field.ComboBox({
                            typeAhead: true,
                            triggerAction: 'all',
                            selectOnTab: true,
                            store: [
                                ['SI', 'SI'],
                                ['NO', 'NO']
                            ],
                            lazyRender: true,
                            listClass: 'x-combo-list-small'
                        })
                    },
                    {
                        header: 'Empresa',
                        dataIndex: 'empresa',
                        width: 150,
                        sortable: true
                    },
                    {
                        header: 'Jurisdicción',
                        dataIndex: 'jurisdiccion',
                        width: 80,
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 80,
                        sortable: true
                    },
                    {
                        header: 'Acción',
                        hidden: true,
                        hideable: false,
                        dataIndex: 'accion'
                    }
        ],
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gvAliasActuales',
            trackOver: true,
            stripeRows: true,
            loadMask: true,
            getRowClass: function(record, index) {                            
                if (record.get("accion") == "Insertar") 
                {
                    return "x-grid-row-news";
                } 
            }
        },
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    { xtype: 'tbfill' },
                    {
                        itemId: 'removeAliasActualButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el alias seleccionado',
                        iconCls: 'remove',
                        scope: this,
                        disabled: true,
                        handler: function()
                        {
                           eliminarAlias(gridAliasActuales);
                        }
                    }
                ]
            }
        ],
        title: 'Alias asignados a la Plantilla',
        frame: true
    });  
        
    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults: {
           labelAlign: 'left',
           msgTarget: 'side'
        },
        defaults: {
           margins: '0 0 10 0'
        },
        items: [
                {
                    xtype: 'fieldset',
                    title: '',
                    defaultType: 'textfield',
                    width: '100%',
                    items:
                    [
                        {
                            xtype: 'fieldset',
                            title: '',                       
                            width: '100%',
                            items: 
                            [
                                 {
                                     layout: 'table',
                                     border: false,
                                     items: 
                                     [
                                         filtroPanelAliasCreados,
                                         {
                                             width: 100,
                                             layout: 'form',
                                             border: false,
                                             items: 
                                             [
                                                 {
                                                     xtype: 'displayfield'
                                                 }
                                             ]
                                         },
                                         filtroPanelAliasActuales
                                     ]
                                 },
                                 {
                                     layout: 'table',
                                     border: false,
                                     items: 
                                     [
                                         gridAliasCreados,
                                         {
                                             width: 100,
                                             layout: 'form',
                                             border: false,
                                             items: 
                                             [
                                                 {
                                                     xtype: 'displayfield'
                                                 }
                                             ]
                                         },
                                         gridAliasActuales
                                     ]
                                 },
                                 {
                                     layout: 'table',
                                     border: false,
                                     items: 
                                     [
                                         {
                                             width: 100,
                                             layout: 'form',
                                             border: false,
                                             items: 
                                             [
                                                 {
                                                     xtype: 'displayfield'
                                                 }
                                             ]
                                         }
                                     ]
                                 }
                            ]
                        }
                     ]
                 }
        ]
    });
    
    
	btnGuardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function() 
        {
            aliasGestionados = obtenerAliasGestionados();
            winAliases.destroy();
        }
    });        	
	
	btnCancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() 
        {
            winAliases.destroy();
        }
	});  
	
	winAliases = Ext.create('Ext.window.Window', {
			title: 'Alias Agregados a esta Plantilla',
			modal: true,
			width: 1150,
			height: 550,
			resizable: true,
			layout: 'fit',
			items: [formPanel],
			buttonAlign: 'center',
			buttons:[btnGuardar ,btnCancelar]
	}).show();       
}

function eliminarSeleccion(datosSelect)
{
	for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
	{
		  datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
	}
}	

function existeAsignacion(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var perfil = grid.getStore().getAt(i).get('id_alias');
        if (perfil === myRecord.get('id_alias'))
        {
            existe = true;
            break;
        }
    }
    return existe;
}


function agregarAlias()
{
    if (smAliasCreados.getSelection().length > 0)
    {
        for (var i = 0; i < smAliasCreados.getSelection().length; ++i)
        {
            var r = Ext.create('AliasGestionadoModel', {
                id_alias:       smAliasCreados.getSelection()[i].get('id_alias'),
                valor:          smAliasCreados.getSelection()[i].get('valor'),
                esCC:           'NO',
                empresa:        smAliasCreados.getSelection()[i].get('empresa'),
                jurisdiccion:   smAliasCreados.getSelection()[i].get('jurisdiccion'),
                estado:         smAliasCreados.getSelection()[i].get('estado'),
                accion:         'Insertar'
                
            });
            if (!existeAsignacion(r, gridAliasActuales))
            {
                storeAliasActuales.insert(0, r);
            }
            else
            {
                Ext.Msg.alert('Alerta ', 'Algún o algunos de los alias escogidos ya se encuentran en el panel de Alias Asociados a esta plantilla!');
            }
        }
    }
    else
    {
        Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un alias de los alias creados!')
    }
}

function eliminarAlias(datosSelect)
{
    var xRowSelMod = datosSelect.getSelectionModel().getSelection();
    for (var i = 0; i < xRowSelMod.length; i++)
    {
        var RowSel = xRowSelMod[i];
        datosSelect.getStore().remove(RowSel);
    }
}


function obtenerAliasGestionados()
{
    var array_alias_gestionados             = new Object();
    array_alias_gestionados['registros']    = new Array();
    var array_data                          = new Array();
    var totalGestionados                    = 0;

    if(storeAliasActuales.getNewRecords().length > 0)
    {
        var registrosNuevos=storeAliasActuales.getNewRecords();
        Ext.each(registrosNuevos,function(record,index)
        {
            array_data.push(record.data);
        });
        totalGestionados=totalGestionados+storeAliasActuales.getNewRecords().length;
    }

    if(storeAliasActuales.getUpdatedRecords().length > 0)
    {
        var registrosActualizados=storeAliasActuales.getUpdatedRecords();
        Ext.each(registrosActualizados,function(record,index)
        {
            record.set('accion', 'Editar');
            array_data.push(record.data);
        });
        totalGestionados=totalGestionados+storeAliasActuales.getUpdatedRecords().length;
    }

    if(storeAliasActuales.getRemovedRecords().length > 0)
    {
        var registrosEliminados=storeAliasActuales.getRemovedRecords();
        Ext.each(registrosEliminados,function(record,index)
        {
            record.set('accion', 'Eliminar');
            array_data.push(record.data);
        });
        totalGestionados=totalGestionados+storeAliasActuales.getRemovedRecords().length;
    }
    array_alias_gestionados['total']        = totalGestionados;
    array_alias_gestionados['registros']    = array_data;

    param = Ext.JSON.encode(array_alias_gestionados);
    return param;
}

function buscarAliasCreado()
{
    storeAliasCreados.getProxy().extraParams.nombre = Ext.getCmp('txtCorreoAliasCreado').value;
    storeAliasCreados.load();
}
function limpiarAliasCreado()
{
    Ext.getCmp('txtCorreoAliasCreado').value="";
    Ext.getCmp('txtCorreoAliasCreado').setRawValue("");

    storeAliasCreados.getProxy().extraParams.nombre = Ext.getCmp('txtCorreoAliasCreado').value;
    storeAliasCreados.load();    
}

function buscarAliasActual()
{
    storeAliasActuales.getProxy().extraParams.nombre = Ext.getCmp('txtCorreoAliasActual').value;
    storeAliasActuales.load();
}
function limpiarAliasActual()
{
    Ext.getCmp('txtCorreoAliasActual').value="";
    Ext.getCmp('txtCorreoAliasActual').setRawValue("");

    storeAliasActuales.getProxy().extraParams.nombre = Ext.getCmp('txtCorreoAliasActual').value;
    storeAliasActuales.load();    
}