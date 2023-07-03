Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;

Ext.onReady(function() 
{
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		

    TFNombre = new Ext.form.TextField({
        id: 'nombre',
        fieldLabel: 'Nombres',
        xtype: 'textfield'
    });
    TFApellido = new Ext.form.TextField({
        id: 'apellido',
        fieldLabel: 'Apellidos',
        xtype: 'textfield'
    });			
    TFRazonSocial = new Ext.form.TextField({
        id: 'razonSocial',
        fieldLabel:'Raz\u00f3n Social',
        xtype: 'textfield'
    });
    TFIdentificacion = new Ext.form.TextField({
        id: 'identificacion',
        fieldLabel: 'Identificaci\u00f3n',
        xtype: 'textfield'
    });   
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
    var modelPagos = Ext.define('PagosModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'vendedor',             mapping: 'vendedor' },
                { name: 'cliente',              mapping: 'cliente' },
                { name: 'login',                mapping: 'login' },
                { name: 'fechaPago',            mapping: 'fechaPago' },
                { name: 'numeroPago',           mapping: 'numeroPago' },
                { name: 'formaPago',            mapping: 'formaPago' },
                { name: 'estadoPago',           mapping: 'estadoPago' },
                { name: 'valorPago',            mapping: 'valorPago' },
                { name: 'codigoTipoDocumento',  mapping: 'codigoTipoDocumento' },
                { name: 'factura',              mapping: 'factura' },
                { name: 'fechaFactura',         mapping: 'fechaFactura' },
                { name: 'estado',               mapping: 'estado' }
         
                
            ]
        });
        
    store = Ext.create('Ext.data.Store', 
    {
        model: modelPagos,
        pageSize: itemsPerPage,
        autoLoad: true,       
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetPagosVendedor,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', nombre: '', apellido: '', razonSocial: '',identificacion: ''},
            simpleSortMode: true
        },
        listeners:
        {
            beforeload: function(store)
            {
                store.getProxy().extraParams.fechaDesde     = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta     = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.nombre         = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido       = Ext.getCmp('apellido').getValue();
                store.getProxy().extraParams.razonSocial    = Ext.getCmp('razonSocial').getValue();
                store.getProxy().extraParams.identificacion = Ext.getCmp('identificacion').getValue();
            }
        }
    });

    var gridPagos = Ext.create('Ext.grid.Panel',
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
                            text: 'Generar-Enviar CSV',
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
                id: 'grid_pago',
                trackOver: true,
                stripeRows: true,
                loadMask: true
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'Vendedor',
                    dataIndex: 'vendedor',
                    width: 200,
                    sortable: true
                },
                {
                    header: 'Cliente',
                    dataIndex: 'cliente',
                    width: 150,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Login',
                    dataIndex: 'login',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Fecha de Pago',
                    dataIndex: 'fechaPago',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Número de Pago',
                    dataIndex: 'numeroPago',
                    width: 100,
                    align: 'center',
                    sortable: true
                },  
                {
                    header: 'Forma de Pago',
                    dataIndex: 'formaPago',
                    width: 100,
                    align: 'center',
                    sortable: true
                },  
                {
                    header: 'Estado Pago',
                    dataIndex: 'estadoPago',
                    width: 100,
                    align: 'center',
                    sortable: true
                },   
                {
                    header: 'Valor Pago',
                    dataIndex: 'valorPago',
                    width: 100,
                    align: 'center',
                    sortable: true
                }, 
                {
                    header: 'Código tipo documento',
                    dataIndex: 'codigoTipoDocumento',
                    width: 100,
                    align: 'center',
                    sortable: true
                },   
                {
                    header: 'Factura',
                    dataIndex: 'factura',
                    width: 100,
                    align: 'center',
                    sortable: true
                },    
                {
                    header: 'Fecha Factura',
                    dataIndex: 'fechaFactura',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Estado Factura',
                    dataIndex: 'estado',
                    width: 100,
                    align: 'center',
                    sortable: true
                },                  

            ],
            title: 'Resumen de Pagos',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: store,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridPagosVendedor'
        });
        
        
        
        var filterPagosVendedor = Ext.create('Ext.panel.Panel',
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
                TFNombre,
                {html: "&nbsp;", border: false, width: 50},
                TFApellido,
                {html: "&nbsp;", border: false, width: 50},
                TFRazonSocial,
                {html: "&nbsp;", border: false, width: 50},
                TFIdentificacion,
                {html: "&nbsp;", border: false, width: 50},  
            ],
            renderTo: 'filtroPagos'
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
        Ext.getCmp('nombre').setValue('');
        Ext.getCmp('apellido').setValue('');
        Ext.getCmp('razonSocial').setValue('');
        Ext.getCmp('identificacion').setValue('');
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
               url: urlGenerarReportePagosVendedor,
               params:
                   {
                       fechaDesde: Ext.getCmp('fechaDesde').getValue(),
                       fechaHasta: Ext.getCmp('fechaHasta').getValue(),
                       nombre: Ext.getCmp('nombre').getValue(),
                       apellido: Ext.getCmp('apellido').getValue(),
                       identificacion: Ext.getCmp('identificacion').getValue(),
                       razonSocial: Ext.getCmp('razonSocial').getValue()
                   },
               method: 'get',

               success: function(response) 
               {                 
                   Ext.Msg.alert('Mensaje','Reporte generado y enviado exitosamente..');
               },
               failure: function(result)
               {
                   Ext.Msg.alert('Error ', 'Error al generar y enviar reporte: ' + result.statusText);
               }
           });
    }    
});


