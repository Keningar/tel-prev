    Ext.onReady(function() {   
    Ext.tip.QuickTipManager.init();


    
    var storeOlt = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getEncontradosOlt,
            extraParams: {
                tipoElemento: 'OLT'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreElemento', mapping:'nombreElemento'},
                {name:'idElemento', mapping:'idElemento'}
              ]
    });

    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getEncontrados,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                login: '',
                solicitud: '',
                elemento: '',
                estado: '',
            }
        },
        fields:
                  [ {name:'idDetalleSolicitud', mapping:'idDetalleSolicitud'},
                    {name:'login',         mapping:'login'},
                    {name:'solicitud',     mapping:'solicitud'},
                    {name:'feCreacion',    mapping:'feCreacion'},
                    {name:'observacion',   mapping:'observacion'},
                    {name:'estado',        mapping:'estado'},
                    {name:'elemento',      mapping:'elemento'}
                  ],
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: { enableTextSelection: true },
        iconCls: 'icon-grid',
            
        columns:[
                {
                  header: 'Tipo Solicitud',
                  dataIndex: 'solicitud',
                  width: 200,
                  sortable: true
                },
                {
                  header: 'Login',
                  dataIndex: 'login',
                  width: 150,
                  sortable: true
                },
                {
                  header: 'Olt',
                  dataIndex: 'elemento',
                  width: 130,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Fecha Creación',
                  dataIndex: 'feCreacion',
                  width: 115,
                  sortable: true
                },                
                {
                  header: 'Observación',
                  dataIndex: 'observacion',
                  width: 125,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 100,                    
                    items: [
                        {
                            getClass: function(v, meta, rec) {
      
                                return 'button-grid-show';

                            },
                            tooltip: 'Ver Solicitud Padre',
                            handler: function(grid, rowIndex, colIndex) {
                                verSolicitudPadre(grid.getStore().getAt(rowIndex).data);
                            
                            }
                        },
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
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, 
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 930,
        title: 'Criterios de busqueda',
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
                items: [
                       {
                            xtype: 'textfield',
                            id: 'login',
                            fieldLabel: 'Login',
                            value: '',
                            width: '30%'
                        },
                        { width: '20%',border:false},
                         //inicio
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Tipo Solicitud',
                            id: 'solicitud',
                            value:'',
                            store: [
                                ['SOLICITUD CAMBIO PLAN MASIVO','SOLICITUD CAMBIO PLAN MASIVO']
                            ],
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        { width: '30%',border:false},                        
                        {
                            xtype: 'combobox',
                            id: 'elemento',
                            fieldLabel: 'Elemento',
                            store: storeOlt,
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
                            hideTrigger: false,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '20%',border:false},
                         //inicio
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'estado',
                            value:'',
                            store: [
                                ['Pendiente','Pendiente'],
                                ['Finalizada','Finalizada'],
                                ['Fallo','Fallo'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        { width: '30%',border:false},
                        { width: '10%',border:false}, //final
                        
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
    store.load({
        callback:function(){ }
    });
});

function verSolicitudPadre(data)
{
    storeHistorialSolicitud = new Ext.data.Store
    ({
        pageSize: 50,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url : showSolicitudPadre,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                idDetalleSolicitud: data.idDetalleSolicitud
            }
        },
        fields:
        [
            { name:'idSolPadre', mapping:'idSolPadre' },
            { name:'tipoSolicitud', mapping:'tipoSolicitud' },
            { name:'fechaCrea', mapping:'fechaCrea' },
            { name:'usuarioCrea', mapping:'usuarioCrea' },
            { name:'estado', mapping:'estado' }
        ]
    });
    
    gridHistorico = Ext.create('Ext.grid.Panel', 
    {
        id:'gridHistorico',
        store: storeHistorialSolicitud,
        columnLines: true,
        columns: 
        [
            {
                header: '# Solicitud Padre',
                dataIndex: 'idSolPadre',
                width: 100,
                sortable: true
            },
            {
                header: 'Tipo Solicitud',
                dataIndex: 'tipoSolicitud',
                width: 245,
                sortable: true
            },
            {
                header: 'Fecha',
                dataIndex: 'fechaCrea',
                width: 120,
                sortable: true
            },
            {
                header: 'Usuario',
                dataIndex: 'usuarioCrea',
                width: 75
            },        
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 70
            }
        ],
        viewConfig:
        {
            stripeRows:true
        },
        frame: true,
        height: 200
    });
    
    var formPanel = Ext.create('Ext.form.Panel', 
    {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: 
        {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: 
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: 
                {
                    width: 660
                },
                items: 
                [
                    gridHistorico
                ]
            }
        ],
        buttons: 
        [
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });

    var win = Ext.create('Ext.window.Window', 
    {
        title: 'Solicitud Padre',
        modal: true,
        width: 700,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}//fin de funcion de historial de solicitud



function buscar(){
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.login = Ext.getCmp('login').value;
    store.getProxy().extraParams.solicitud = Ext.getCmp('solicitud').value;
    store.getProxy().extraParams.elemento = Ext.getCmp('elemento').value;
    store.getProxy().extraParams.estado = Ext.getCmp('estado').value;
    store.load();

}

function limpiar(){
    Ext.getCmp('login').value="";
    Ext.getCmp('login').setRawValue("");
    
    Ext.getCmp('solicitud').value="";
    Ext.getCmp('solicitud').setRawValue("");
    
    Ext.getCmp('elemento').value="";
    Ext.getCmp('elemento').setRawValue("");
    
    Ext.getCmp('estado').value="";
    Ext.getCmp('estado').setRawValue("");

}

