
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
]);

var winSearch;

function showEdificios(idcanton){
    var idCanton = idcanton;
    storeEdificio = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: url_getEncontradosNodoCliente,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento: '',
                modeloElemento:'',
                feCreacion: '',
                canton: idCanton,
                jurisdiccion: '',
                estado: 'Activo',
                empresa: 'NO'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'canton', mapping: 'canton'},
                {name: 'modeloElemento', mapping: 'modeloElemento'},
                {name: 'estadoElemento', mapping: 'estadoElemento'},
                {name: 'direccion', mapping: 'direccion'},
                {name: 'tipoAdmin', mapping: 'tipoAdmin'}
            ],
        autoLoad: true
    });
    
 var storeModelo = new Ext.data.Store({
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_modelo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                tipoElemento: 'EDIFICACION',
            }
        },
        fields:
            [
                {name: 'descripcion', mapping: 'descripcion'},
                {name: 'id', mapping: 'id'}
            ]
    });    
    
    gridElementosBusq = Ext.create('Ext.grid.Panel', {
        width: 650,
        height: 294,
        store: storeEdificio,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',                  
        columns:[
                {
                  id: 'idElemento',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Nombre Elemento',
                  dataIndex: 'nombreElemento',
                  width: 160,
                  sortable: true
                },
                {
                  header: 'Direccion',
                  dataIndex: 'direccion',
                  width: 80,
                  sortable: true
                },
                {
                  header: 'Canton',
                  dataIndex: 'canton',
                  width: 90,
                  sortable: true
                },
                {
                  header: 'Tipo',
                  dataIndex: 'modeloElemento',
                  width: 80,
                  sortable: true
                },
                {
                  header: 'Administración',
                  dataIndex: 'tipoAdmin',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estadoElemento',
                  width: 60,
                  sortable: true
                },
                {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 45,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-seleccionar';
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex) {

                            if (Ext.getCmp("dependeEdificioDesc")) {
                                Ext.getCmp("dependeEdificioDesc").setValue(grid.getStore().getAt(rowIndex).data.nombreElemento);
                            }

                            if (Ext.getCmp("dependeEdificioId")) {
                                Ext.getCmp("dependeEdificioId").setValue(grid.getStore().getAt(rowIndex).data.idElemento);
                            }

                            if (Ext.getCmp("tipoEdificio")) {
                                Ext.getCmp("tipoEdificio").setValue(grid.getStore().getAt(rowIndex).data.modeloElemento);
                            }

                            winSearch.destroy();
                        }
                    }
                ]
            }
            ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeEdificio,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
    
    filterPanelElementosBusq = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 630,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarElemento();
                }
            }
        ], //cierre buttons              
        items: [
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '50%'
            },
            {width: '20%', border: false},
            {
                xtype: 'combobox',
                id: 'sltModelo',
                fieldLabel: 'Tipo Edificación',
                store: storeModelo,
                displayField: 'descripcion',
                valueField: 'id',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '10%', border: false},
            //-------------------------------------
            //-------------------------------------
            {width: '10%', border: false}, //inicio
            {
                xtype: 'textfield',
                id: 'txtDireccion',
                fieldLabel: 'Direccion',
                value: '',
                width: '50%'
            },
            {width: '20%', border: false}, //final                        
        ]//cierre items
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
                width: 630
            },
            items: [
                filterPanelElementosBusq,
                gridElementosBusq
            ]
        }
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                winSearch.destroy();
            }
        }]
    });

    winSearch = Ext.create('Ext.window.Window', {
        title: 'Edificación',
        modal: true,
        width: 680,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function buscarElemento(){

    storeEdificio.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    storeEdificio.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModelo').value;
    storeEdificio.getProxy().extraParams.direccion = Ext.getCmp('txtDireccion').value;
    storeEdificio.load();

}

function cierraVentana(){
    winSearch.close();  
}