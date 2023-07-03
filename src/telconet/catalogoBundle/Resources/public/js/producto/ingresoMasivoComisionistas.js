
Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var itemsPerPage = 10;
var store = '';
var estado_id = '';

Ext.onReady(function() {
    
    Ext.tip.QuickTipManager.init();
    
    // Productos
    Ext.define('modelProducto', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'int'},
            {name: 'nombre', type: 'string'}
        ]
    });

    var productos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelProducto",
        proxy: {
            type: 'ajax',
            url: url_productos,
            reader: {
                type: 'json',
                root: 'nombres'
            }
        }
    });

     var cmbProducto = Ext.create('Ext.form.ComboBox', {
        xtype: 'combobox',
        fieldLabel: 'Producto',
        store: productos,
        queryMode: 'local',        
        id: 'idProducto',
        name: 'idProducto',
        valueField: 'id',
        displayField: 'nombre',        
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',        
        allowBlank: false,
        emptyText: 'Seleccione...',        
        listeners: {
            click: {
                element: 'el',
                fn: function() {
                    if (productos.getCount() == 0)
                    {
                        productos.removeAll();
                        productos.load();
                    }
                }
            }
        }
    });
    
    // Grupos
    Ext.define('modelGrupo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'nombre', type: 'string'}
        ]
    });

    var grupos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelGrupo",
        proxy: {
            type: 'ajax',
            url: url_grupos,
            reader: {
                type: 'json',
                root: 'nombres'
            }
        }
    });

     var cmbGrupo = Ext.create('Ext.form.ComboBox', {
        xtype: 'combobox',
        fieldLabel: 'Grupo',
        store: grupos,
        queryMode: 'local',        
        id: 'idGrupo',
        name: 'idGrupo',
        valueField: 'nombre',
        displayField: 'nombre',        
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',        
        allowBlank: false,
        emptyText: 'Seleccione...',        
        listeners: {
            click: {
                element: 'el',
                fn: function() {
                    if (grupos.getCount() == 0)
                    {
                        grupos.removeAll();
                        grupos.load();
                    }
                }
            }
        }
    });
    
    // Nombres Tecnicos
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
            url: url_nombres_tecnicos,
            reader: {
                type: 'json',
                root: 'nombres'
            }
        }
    });
    var cmbNombreTecnico = Ext.create('Ext.form.ComboBox', {
        xtype: 'combobox',
        fieldLabel: 'Nombre Técnico',
        store: states,
        queryMode: 'local',
        id: 'idNombreTecnico',
        name: 'idNombreTecnico',
        valueField: 'nombre',
        displayField: 'nombre',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: false,
        emptyText: 'Seleccione...',  
        listeners: {
            click: {
                element: 'el',
                fn: function() {
                    if (states.getCount() == 0)
                    {
                        states.removeAll();
                        states.load();
                    }
                }
            }
        }
    });

    // Estados
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    var estado_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_estados,
            reader: {
                type: 'json',
                root: 'estados'
            }
        }
    });
    var estado_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank:false,
        emptyText: 'Seleccione...',  
        listeners: {
            select:
                function(e) {
                    estado_id = Ext.getCmp('idestado').getValue();
                },
            click: {
                element: 'el',
                fn: function() {
                    if (estado_store.getCount() == 0)
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
        fields: 
        [
            {name: 'intIdProducto', type: 'string'},
            {name: 'strCodigo', type: 'string'},
            {name: 'strDescripcion', type: 'string'},
            {name: 'strNombreTecnico', type: 'string'},
            {name: 'strGrupo', type: 'string'},
            {name: 'strTipo', type: 'string'},
            {name: 'fltInstalacion', type: 'string'},
            {name: 'strFuncionPrecio', type: 'string'},
            {name: 'strFechaCreacion', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strRequiereComisionar', type: 'string'}
        ],
        idProperty: 'intIdProducto'
    });
    
     store = new Ext.data.Store({ 
        pageSize: itemsPerPage, 
        model: 'ListaDetalleModel',        
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_store_grid,
            reader: {
                type: 'json',
                root: 'productos',
                totalProperty: 'total'
            },
            extraParams: {intParamIdProducto:'', strParamGrupo: '', strParamNombreTecnico: '', strParamEstado: '' },
            simpleSortMode: true
        },
        autoLoad: true,       
    });
    var pluginExpanded = true;   
        
    var objPermiso = $("#ROLE_41-5257");
    var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
    
    var AsignacionMasivaPlantillaBtn = "";
    sm = "";    
    
    if (boolPermiso) 
    {
        sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
        })
        AsignacionMasivaPlantillaBtn = Ext.create('Ext.button.Button', {
            iconCls: 'icon_check',
            text: 'Asignar/Reemplazar Plantilla',
            scope   : this,
            handler: function(){ AsignacionMasivaPlantilla();}
          });
    }
   
    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [
                {
                    iconCls: 'icon_add',
                    text: 'Seleccionar Todos',
                    itemId: 'select',
                    scope: this,
                    handler: function() {
                        Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').selectAll()
                    }
                },
            {
                    iconCls: 'icon_limpiar',
                    text: 'Desmarque Todos',
                    itemId: 'clear',
                    scope: this,
                    handler: function() {
                        Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').clearPersistedSelection()
                    }
                },
                {xtype: 'tbfill'},
                AsignacionMasivaPlantillaBtn
            ]
    });
        
    var listView = Ext.create('Ext.grid.Panel', {
        id : 'listView',
        width: 980,
        height: 300,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: false
        },
        dockedItems: [ toolbar ],                       
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Código',
                width: 70,
                dataIndex: 'strCodigo'
            }, {
                text: 'Descripción',
                width: 160,
                dataIndex: 'strDescripcion'
            }, {
                text: 'Nombre Técnico',
                width: 100,
                dataIndex: 'strNombreTecnico'
            }, {
                text: 'Grupo',
                width: 100,
                dataIndex: 'strGrupo'    
            }, {
                text: 'Tipo',
                dataIndex: 'strTipo',
                align: 'center',
                width: 60
            }, {
                text: 'Instalación',
                dataIndex: 'fltInstalacion',
                align: 'right',
                width: 70
            }, {
                text: 'F. Precio',
                dataIndex: 'strFuncionPrecio',
                align: 'right',
                width: 70
             }, {
                text: 'F. Creación',
                dataIndex: 'strFechaCreacion',
                align: 'right',
                width: 100    
            }, {
                text: 'Requiere Comisionar',
                dataIndex: 'strRequiereComisionar',
                align: 'center',
                width: 120
            }, {
                text: 'Estado',
                dataIndex: 'strEstado',
                align: 'center',
                flex: 50
            }],          
           bbar: Ext.create('Ext.PagingToolbar', {       
           store: store,
           displayInfo: true,
           displayMsg: 'Mostrando productos {0} - {1} of {2}',
           emptyMsg: "No hay datos para mostrar"
           }),           
            renderTo:'listView'
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,        
        buttonAlign: 'center',        
        layout: {
            type: 'table',
            columns: 2,
            align: 'left',
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,        
        collapsed: true,
        width: 980,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',                
                iconCls: "icon_search",
                handler: Buscar,
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
            cmbProducto,
            cmbGrupo,               
            cmbNombreTecnico,
            estado_cmb,            
        ],
        renderTo: 'filtro_masivoplantilla'
    });

});

function Buscar()
{
    store.getProxy().extraParams.intParamIdProducto    = Ext.getCmp('idProducto').getValue();
    store.getProxy().extraParams.strParamGrupo         = Ext.getCmp('idGrupo').getValue();
    store.getProxy().extraParams.strParamNombreTecnico = Ext.getCmp('idNombreTecnico').getValue();
    store.getProxy().extraParams.strParamEstado        = Ext.getCmp('idestado').getValue();              
    store.load();
}

function limpiar()
{
    Ext.getCmp('idProducto').setRawValue("");
    Ext.getCmp('idGrupo').setRawValue("");
    Ext.getCmp('idNombreTecnico').setRawValue("");
    Ext.getCmp('idestado').setRawValue("");        
    store.load();
}

function AsignacionMasivaPlantilla()
{
    var intIdProducto = '';
    var param = '';
    var selection = Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').getPersistedSelection();

    if(selection.length > 0)
    {    
        for (var i = 0; i < selection.length; ++i)
        {
            param = param + selection[i].data.intIdProducto;

            if (i < (selection.length - 1))
            {
                param = param + '|';
            }
        }        
        Ext.Msg.confirm('Alerta', 'Se asignará Plantilla de Comisiones a todos los productos seleccionados. Desea continuar?', function(btn) {
            if (btn == 'yes')
            {
                comisionPlantilla(intIdProducto,param);              
            }
        });
    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}
