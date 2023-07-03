/**
 * Script que presenta la pantalla de consulta de procesos masivos
 * 
 * @author John Vera         <javera@telconet.ec>
 * @version 1.0 04-12-2014
 * @author modificado John Vera         <javera@telconet.ec>
 * @version 1.1 27-11-2015
 * 
 */
Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    store = new Ext.data.Store
        ({
            pageSize: 10,
            total: 'total',
            proxy:
                {
                    timeout: 400000,
                    type: 'ajax',
                    url: 'getConsulta',
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            login: '',
                            estado: '',
                            idElemento: ''

                        }
                },
            fields:
                [
                    {name: 'idServicio', mapping: 'idServicio'},
                    {name: 'login', mapping: 'login'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'},
                    {name: 'estadoServicio', mapping: 'estadoServicio'},
                    {name: 'description', mapping: 'description'},
                    {name: 'cn', mapping: 'cn'},
                    {name: 'sn', mapping: 'sn'},
                    {name: 'tnEmpresa', mapping: 'tnEmpresa'},
                    {name: 'tnClientId', mapping: 'tnClientId'},
                    {name: 'tnClientClass', mapping: 'tnClientClass'},
                    {name: 'tnStatus', mapping: 'tnStatus'},
                    {name: 'tnPolicy', mapping: 'tnPolicy'},
                    {name: 'macAddress', mapping: 'macAddress'},
                    {name: 'packageID', mapping: 'packageID'}
                ]
        });


    //store que obtiene los elementos
    var storeElementos = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : getElementosPorTipo,
            extraParams: {
                idServicio: '',
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
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombreElemento', mapping:'nombreElemento'},
              {name:'ipElemento', mapping:'ip'}
            ]
    });

    var pluginExpanded = true;

    grid = Ext.create('Ext.grid.Panel',
        {
            width: '98%',
            height: 350,
            store: store,
            loadMask: true,
            frame: false,
            viewConfig: {enableTextSelection: true},
            iconCls: 'icon-grid',
            columns:
                [
                    {
                        header: 'No Servicio',
                        dataIndex: 'idServicio',
                        width: '5%',
                        sortable: true
                    },
                    {
                        header: 'Login',
                        dataIndex: 'login',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Elemento',
                        dataIndex: 'nombreElemento',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Estado Servicio',
                        dataIndex: 'estadoServicio',
                        width: '5%',
                        sortable: true
                    },
                    {
                        header: 'Descripcion',
                        dataIndex: 'description',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'CN',
                        dataIndex: 'cn',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'SN',
                        dataIndex: 'sn',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Empresa',
                        dataIndex: 'tnEmpresa',
                        width: '5%',
                        sortable: true
                    },
                    {
                        header: 'Client Id',
                        dataIndex: 'tnClientId',
                        width: '5%',
                        sortable: true
                    },
                                        {
                        header: 'Client Class',
                        dataIndex: 'tnClientClass',
                        width: '5%',
                        sortable: true
                    },
                                        {
                        header: 'Status',
                        dataIndex: 'tnStatus',
                        width: '3%',
                        sortable: true
                    },
                                        {
                        header: 'Policy',
                        dataIndex: 'tnPolicy',
                        width: '10%',
                        sortable: true
                    },
                                        {
                        header: 'Mac Address',
                        dataIndex: 'macAddress',
                        width: '10%',
                        sortable: true
                    }
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

    var filterPanel = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 5,
                align: 'stretch'
            },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: false,
            width: '98%',
            title: 'Criterios de busqueda',
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
                        handler: function() {
                            limpiar();
                        }
                    },
                    {
                        text: 'Excel',
                        iconCls: "icon_exportar",
                        handler: function() {
                            exportarExce();
                        }
                    }
                ],
            items:
                [
                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'login',
                        fieldLabel: 'Login',
                        value: '',
                        width: '30%'
                    },
                    {width: '20%', border: false},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado ',
                        id: 'estado',
                        value: '',
                        store: [
                            ['Activo', 'Activo'],
                            ['In-Corte', 'In-Corte'],
                            ['EnPruebas', 'EnPruebas'],
                            ['Inactivo', 'Inactivo'],
                            ['EnVerificacion', 'EnVerificacion'],
                            ['Pre-servicio', 'Pre-servicio'],
                            ['Reubicado', 'Reubicado'],
                            ['PreAsignacionInfoTecnica', 'PreAsignacionInfoTecnica'],
                            ['Replanificada', 'Replanificada'],
                            ['Trasladado', 'Trasladado'],
                            ['PrePlanificada', 'PrePlanificada'],
                            ['Planificada', 'Planificada'],
                            ['PreFactibilidad', 'PreFactibilidad']
                            

                        ],
                        width: '30%'
                    },
                    {width: '10%', border: false},
                    //inicio del siguiente bloque
                    {width: '10%', border: false},
                    {
                        xtype: 'combobox',
                        id: 'sltElementoContenedor',
                        fieldLabel: 'Elemento:',
                        store: storeElementos,
                        displayField: 'nombreElemento',
                        valueField: 'idElemento',
                        loadingText: 'Buscando ...',
                        listClass: 'x-combo-list-small',
                        queryMode: "remote",
                        lazyRender: true,
                        forceSelection: true,
                        emptyText: 'Ingrese nombre Elemento..',
                        minChars: 3, 
                        typeAhead: true,
                        selectOnTab: true,
                        width: '30%'
                    },
                    {width: '10%', border: false},
                    //nueva fila
                ],
            renderTo: 'filtro'
        });

    //store.load();


});


/**
 * Funcion que ejecuta la busqueda de los datos
 * 
 * @author John Vera         <javera@telconet.ec>
 * @version 1.0 04-12-2014
 * 
 */
function buscar()
{

    if(Ext.getCmp('estado').value=='' && Ext.getCmp('login').value=='' && 
        (Ext.getCmp('sltElementoContenedor').value==null||Ext.getCmp('sltElementoContenedor').value==''))
    {
        alert('Ingrese al menos un filtro');
    }
    
    store.getProxy().extraParams.estado = Ext.getCmp('estado').value;
    store.getProxy().extraParams.login = Ext.getCmp('login').value;
    store.getProxy().extraParams.idElemento = Ext.getCmp('sltElementoContenedor').value;

    store.load();
}

function exportarExce()
{

    if(Ext.getCmp('sltElementoContenedor').value==null || Ext.getCmp('sltElementoContenedor').value=='')
    {
        alert('Favor, seleccione el elemento.');
        return false;
    }

    Ext.MessageBox.confirm(
        'Exportar Excel',
        '¿ Generar reporte?',
        function(btn) {
            if (btn === 'yes') {
                window.location = exportarExcel + '?idElemento=' + Ext.getCmp('sltElementoContenedor').value;
            }
        });

}

/**
 * Funcion que limpia los controles de los 
 * filtros.
 * @author John Vera         <javera@telconet.ec>
 * @version 1.0 04-12-2014
 * 
 */
function limpiar()
{
    Ext.getCmp('estado').value = "";
    Ext.getCmp('estado').setRawValue("");

    Ext.getCmp('sltElementoContenedor').value = "";
    Ext.getCmp('sltElementoContenedor').setRawValue("");

    Ext.getCmp('login').value = "";
    Ext.getCmp('login').setRawValue("");
}
