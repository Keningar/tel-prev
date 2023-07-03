Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage    = 10;
var gridSecure      = "";
var filterSecureCpe = "";

Ext.onReady(function() 
{
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		

    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        value: new Date,
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        value: new Date,
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325
    });     
    var modelSecure = Ext.define('SecureModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'login',             mapping: 'login' },
                { name: 'razonSocial',       mapping: 'razonSocial' },
                { name: 'producto',          mapping: 'producto' },
                { name: 'serie',             mapping: 'serie' },
                { name: 'fechaActivacion',   mapping: 'fechaActivacion' },
                { name: 'fechaCaducidad',    mapping: 'fechaCaducidad' },
                { name: 'cpeForti',          mapping: 'cpeForti' },
                { name: 'planSecure',        mapping: 'planSecure' }
            ]
        });
        
    store = Ext.create('Ext.data.Store', 
    {
        model: modelSecure,
        pageSize: itemsPerPage,
        autoLoad: true,       
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetSecureCpe,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: ''},
            simpleSortMode: true
        },
        listeners:
        {
            beforeload: function(store)
            {
                store.getProxy().extraParams.fechaDesde     = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta     = Ext.getCmp('fechaHasta').getValue();
            }
        }
    });

    gridSecure = Ext.create('Ext.grid.Panel',
        {
            width: 1300,
            height: 370,
            store: store,
            iconCls: 'icon-grid',
            dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        {xtype: 'tbfill'},
                        {
                            iconCls: 'icon_exportar',
                            text: 'Exportar a excel',
                            disabled: false,
                            itemId: 'exportar',
                            scope: this,
                            handler: function() {
                                generarReporte();
                            }
                        }
                    ]}],             
            viewConfig: 
            {
                enableTextSelection: true,
                id: 'grid_secure',
                trackOver: true,
                stripeRows: true,
                loadMask: true
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'Login',
                    dataIndex: 'login',
                    width: 200,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Razón Social',
                    dataIndex: 'razonSocial',
                    width: 300,
                    sortable: true
                },
                {
                    header: 'Producto',
                    dataIndex: 'producto',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Serie del Equipo',
                    dataIndex: 'serie',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Fecha de Activación',
                    dataIndex: 'fechaActivacion',
                    width: 200,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Fecha de Caducidad',
                    dataIndex: 'fechaCaducidad',
                    width: 110,
                    align: 'center',
                    sortable: true
                },  
                {
                    header: 'Equipo Cpe',
                    dataIndex: 'cpeForti',
                    width: 110,
                    align: 'center',
                    sortable: true
                },  
                {
                    header: 'Plan Secure Cpe',
                    dataIndex: 'planSecure',
                    width: 110,
                    align: 'center',
                    sortable: true
                }
            ],
            title: 'Reporte Secure Cpe',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: store,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridSecureCpe'
        });
        
        
        
        filterSecureCpe = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
            {
                type: 'table',
                columns: 4,
                align: 'center'
            },
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: true,
            collapsed: true,
            width: 1300,
            title: 'Criterios de b\u00fasqueda',
            buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    {
                        Buscar();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    {
                        Limpiar();
                    }
                }
            ],
            items: 
            [
                DTFechaDesde,
                {html: "&nbsp;", border: false, width: 50},
                DTFechaHasta,
                {html: "&nbsp;", border: false, width: 50},
            ],
            renderTo: 'filtroSecure'
        });


    function Buscar() 
    {
        if ((Ext.getCmp('fechaDesde').getValue() != null) && (Ext.getCmp('fechaHasta').getValue() != null))
        {
            if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });

            }
            else
            {
                store.loadData([],false);
                store.currentPage = 1;
                store.load();
            }
        }
        else
        {
            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Seleccione rango de fecha para generar reporte.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
            return false;
        }
    }


    function Limpiar() 
    {
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
    }
    
    
    
    /**
     * Función que envia como parametros los filtros necesarios para la generación del reporte.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 05-10-2016 
     */  
    function generarReporte()
    {
       
       Ext.MessageBox.wait('Generando Reporte. Favor espere..');
       Ext.Ajax.request(
           {
               timeout: 900000,
               url: urlGenerarReporteSecureCpe,
               params:
                   {
                       fechaDesde: Ext.getCmp('fechaDesde').getValue(),
                       fechaHasta: Ext.getCmp('fechaHasta').getValue()
                   },
               method: 'get',

               success: function(response) 
               {                 
                   Ext.Msg.alert('Mensaje','Reporte generado exitosamente..');
               },
               failure: function(result)
               {
                   Ext.Msg.alert('Error ', 'Error al generar reporte: ' + result.statusText);
               }
           });
    }    
});


