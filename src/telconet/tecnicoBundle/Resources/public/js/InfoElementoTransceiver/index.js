Ext.onReady(function() 
{    
    Ext.tip.QuickTipManager.init();
    
    //store para los modelos
    storeModelos = new Ext.data.Store
    ({ 
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url : urlModelosBox,
            extraParams: 
            {
                idMarca: '',
                tipoElemento: 'TRANSCEIVER'
            },
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            { name:'nombreModeloElemento' , mapping:'nombreModeloElemento'},
            { name:'idModeloElemento'     , mapping:'nombreModeloElemento'}
        ]
    });

    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url : urlAjaxTransceiver,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                nombreElemento: '',
                modeloElemento: '',
                estado: 'Todos'
            }
        },
        fields:
        [
            { name:'idElemento'     , mapping:'secuencia'  },
            { name:'nombreElemento' , mapping:'descripcion'},
            { name:'serial'         , mapping:'numeroSerie'},
            { name:'modeloElemento' , mapping:'modelo'     },
            { name:'responsable'    , mapping:'responsable'},
            { name:'estadoNaf'      , mapping:'estado'     },
            { name:'fecha'          , mapping:'fecha'      },
            { name:'action1'        , mapping:'action1'    },
            { name:'action2'        , mapping:'action2'    },
            { name:'action3'        , mapping:'action3'    }
        ]
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', 
    {
        checkOnly: true
    })
    
    //se crea el grid para los datos
    grid = Ext.create('Ext.grid.Panel', 
    {
        width: '78%',
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        viewConfig: { enableTextSelection: true },
        iconCls: 'icon-grid',
        columns:
        [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreElemento',
                header: 'Elemento Transceiver',
                xtype: 'templatecolumn', 
                width: 300,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\\n'
            },
            {
              header: 'Serial',
                dataIndex: 'serial',
                width: 155,
                sortable: true
            },
            {
                header: 'Modelo',
                dataIndex: 'modeloElemento',
                width: 90,
                sortable: true
            },
            {
                header: 'Ultimo Estado Naf',
                dataIndex: 'estadoNaf',
                width: 130,
                sortable: true
            },
            {
                header: 'Fecha Creacion',
                dataIndex: 'fecha',
                width: 120,
                sortable: true
            },
            {
                header: 'Responsable Naf',
                dataIndex: 'responsable',
                width: 300,
                sortable: true
            },
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    //creando el panel para el filtro
    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7, 
        border:false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: { background: '#fff' },
        collapsible : true, 
        collapsed: false,
        width: '78%',
        title: 'Criterios de busqueda',
            buttons: 
            [
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
                { width: '10%',border:false},
                {
                    xtype: 'textfield',
                    id: 'txtSerial',
                    fieldLabel: 'Serial',
                    value: '',
                    width: '30%'
                },
                { width: '20%',border:false},
                {
                    xtype: 'combobox',
                    id: 'sltModelo',
                    fieldLabel: 'Modelo',
                    store: storeModelos,
                    displayField: 'nombreModeloElemento',
                    valueField: 'idModeloElemento',
                    loadingText: 'Buscando ...',
                    listClass: 'x-combo-list-small',
                    queryMode: 'local',
                    width: '30%'
                },
                { width: '10%',border:false},

                //-------------------------------------   
            ],	
        renderTo: 'filtro'
    }); 
    
    storeModelos.load
    ({
        callback: function()
        {
        }
    });       
    
});

/**
 * Funcion que ejecuta la busqueda
 * por los filtros 
 * */
function buscar()
{
    store.getProxy().extraParams.serial = Ext.getCmp('txtSerial').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModelo').value;
    store.load();
}

/**
 * Funcion que limpia los filtros
 * */
function limpiar()
{    
    Ext.getCmp('txtSerial').value="";
    Ext.getCmp('txtSerial').setRawValue("");
        
    Ext.getCmp('sltModelo').value="";
    Ext.getCmp('sltModelo').setRawValue("");
    
}
