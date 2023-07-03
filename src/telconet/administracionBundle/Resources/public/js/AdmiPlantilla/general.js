function verAliases(idPlantilla)
{

    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_alias', mapping:'id_alias'},
			{name:'valor', mapping:'valor'},
			{name:'estado', mapping:'estado'},
			{name:'empresa', mapping:'empresa'},			             
			{name:'jurisdiccion', mapping:'jurisdiccion'},
            {name: 'esCC'}
		],
        idProperty: 'id_alias'
    });
    
    store = new Ext.data.Store({
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: '/administracion/comunicacion/admi_alias/grid',
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
        autoLoad: true
    });         
    
    vacio =  Ext.create('Ext.Component', {
        html: '',
        width: 20,       
        layout: 'anchor',
        style: { color: '#000000' }
    });  
      
	htmlComponent =  Ext.create('Ext.Component', {           
            html: '<input type="button" value="Agregar >>>" class="button-crud" \n\
                   onClick="ingresarAsignacion()" style="width: 12em !important;"/>',            
            width: 135,            
            style: { color: '#000000' }
        });
        
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
	 
    gridAlias = Ext.create('Ext.grid.Panel', {
        id: 'gridAlias',
        width: 470,
        height: 300,
        store: store,
        selModel: sm,
        plugins: [{ptype: 'pagingselectpersist'}],
        title: 'Alias Creados',
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns: [
            {
                id: 'id_alias',
                header: 'IdAlias',
                dataIndex: 'id_alias',
                hidden: true,
                hideable: false
            },
            {
                id: 'valor',
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
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            displayInfo: true,
            store: store,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
    });

    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_alias', mapping: 'id_alias'},
            {name: 'valor', mapping: 'valor'},
            {name: 'esCC'}
        ]
    });

    storeAliasPlantilla = new Ext.data.Store({
        pageSize: 10,
        model: 'Asignacion',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: '/administracion/comunicacion/admi_plantilla/getPlantillaAlias',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                id: idPlantilla,
                estado: 'Activo'
            }
        },
        autoLoad: true
    });
    
    sm2 = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                selectionchange: function(sm, selections) {
                    gridAsignaciones.down('#removeButton').setDisabled(selections.length === 0);
                }
            }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    }); 

    gridAsignaciones = Ext.create('Ext.grid.Panel', {
        id: 'gridAsignaciones',
        width: 320,
        height: 300,
        store: storeAliasPlantilla,       
        selModel: sm2,
        plugins: [cellEditing,{ptype : 'pagingselectpersist'}],
        columns: [
            {
                id: 'id_alias',                
                dataIndex: 'id_alias',
                header: 'IdAlias',
                hidden: true,
                hideable: false
            },
            {
                id: 'valor',
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
            }
        ],
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el perfil seleccionado',
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridAsignaciones);
                        }
                    }]
            }],
        title: 'Alias asignados a la Plantilla',
        frame: true
    });
       
    var FiltroPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders        
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: false,
        collapsed: false,
        width: 470,
        title: 'Filtros',
        buttons: [
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
                handler: function() {
                    limpiar();
                }
            }
        ],
        items: [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtCorreoAlias',
                fieldLabel: 'Correo',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px'
            }
        ]
    }); 
        
	formPanel = Ext.create('Ext.form.Panel', {
        width: 1000,
        height: 400,
        BodyPadding: 10,
        layout: {
            type: 'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        items: [
            //-----------------------------
            {html: "&nbsp;", border: false, width: 20},
            {html: "&nbsp;", border: false, width: 200},            
            {html: "&nbsp;", border: false, width: 135},
            {html: "&nbsp;", border: false, width: 200},
            //-----------------------------
            {html: "&nbsp;", border: false, width: 20},            
            FiltroPanel,
            {html: "&nbsp;", border: false, width: 135},
            {html: "&nbsp;", border: false, width: 200},
            //-----------------------------
            vacio,
            gridAlias,            
            htmlComponent,
            gridAsignaciones
            //-----------------------------
        ]

    });
            
	btnGuardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function() 
        {            
            aliasIds = obtenerAsignaciones();
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
			width: 1000,
			height: 500,
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
            Ext.Msg.alert('Alerta ', 'Ya ingreso el Alias escogido');
            break;
        }
    }
    return existe;
}
function ingresarAsignacion()
{
    if (sm.getSelection().length > 0)
    {
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            var r = Ext.create('Asignacion', {
                id_alias: sm.getSelection()[i].get('id_alias'),
                valor: sm.getSelection()[i].get('valor'),
                esCC: 'NO'
            });
            if (!existeAsignacion(r, gridAsignaciones))
            {
                storeAliasPlantilla.insert(0, r);
            }
        }
    }
    else
    {
        alert('Seleccione por lo menos una accion de la lista');
    }
}
function obtenerAsignaciones()
{
    var array_asignaciones = new Object();
    
    array_asignaciones['total'] = gridAsignaciones.getStore().getCount();
    array_asignaciones['asignaciones'] = new Array();
    
    var array_data = new Array();
    
    for (var i = 0; i < gridAsignaciones.getStore().getCount(); i++)
    {
        array_data.push(gridAsignaciones.getStore().getAt(i).data);
    }

    array_asignaciones['asignaciones'] = array_data;    
    
    param = Ext.JSON.encode(array_asignaciones);
    
    return param;
}
/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar()
{
    store.getProxy().extraParams.nombre = Ext.getCmp('txtCorreoAlias').value;
    store.load();
}
function limpiar()
{
    Ext.getCmp('txtCorreoAlias').value="";
    Ext.getCmp('txtCorreoAlias').setRawValue("");
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtCorreoAlias').value;
    store.load();    
}