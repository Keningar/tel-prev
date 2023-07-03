Ext.require
([
    'Ext.form.*',
    'Ext.tip.QuickTipManager'
]);

var arrayExtJsCmpMask  = [];
var arrayStores        = [];
var arrayGraficoPastel = [];

Ext.QuickTips.init();
Ext.onReady(function() 
{    
    Ext.apply(Ext.form.field.VTypes,
    {
        daterange: function(val, field)
        {
            var date = field.parseDate(val);

            if (!date)
            {
                return false;
            }

            if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) 
            {
                var start = field.up('form').down('#' + field.startDateField);
                start.setMaxValue(date);
                start.setMinValue(Ext.Date.add(date, Ext.Date.DAY, -15));
                start.validate();
                this.dateRangeMax = date;
            }
            else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime())))
            {
                var end = field.up('form').down('#' + field.endDateField);
                end.setMinValue(date);
                end.setMaxValue(Ext.Date.add(date, Ext.Date.DAY, +15));
                end.validate();
                this.dateRangeMin = date;
            }

            return true;
        }
    });
    
    Ext.tip.QuickTipManager.init();
    
    store = new Ext.data.Store
    ({ 
        pageSize: 15,
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url : strUrlGridInstalaciones,
            timeout: 1200000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'nombreDepartamento'  , mapping:'nombreDepartamento'},
            {name:'numeroInstalaciones' , mapping:'numeroInstalaciones'},
            {name:'numeroEncuestas'     , mapping:'numeroEncuestas'},
            {name:'numeroActasEntrega'  , mapping:'numeroActasEntrega'},
            {name:'numeroImagenes'      , mapping:'numeroImagenes'}
        ],
        autoLoad: true
    });
											 
    var pluginExpanded = true;

    
    grid = Ext.create('Ext.grid.Panel',
    {
        width: 900,
        height: intHeight,
        store: store,
        loadMask: true,
        renderTo: 'gridInstalacionesFinalizadas',
        frame: false,
        layout:'fit',
        autoScroll: true,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar',
            enableTextSelection: true,
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        listeners: 
        {
            beforerender: function (cmp, eOpts)
            {
                cmp.columns[0].setHeight(30);
            }
        },
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
                  id: 'nombreDepartamento',
                  header: 'Departamento',
                  dataIndex: 'nombreDepartamento',
                  width: 475,
                  sortable: true			
            },
            {
                  id: 'numeroInstalaciones',
                  header: 'Instalaciones<br/>Finalizadas',
                  dataIndex: 'numeroInstalaciones',
                  align: 'center',
                  width: 100,
                  sortable: true
            },
            {
                  id: 'numeroEncuestas',
                  header: 'Encuestas',
                  dataIndex: 'numeroEncuestas',
                  align: 'center',
                  width: 100,
                  sortable: true
            },
            {
                  id: 'numeroActasEntrega',
                  header: 'Actas de<br/>Entrega',
                  dataIndex: 'numeroActasEntrega',
                  align: 'center',
                  width: 100,
                  sortable: true
            },
            {
                  id: 'numeroImagenes',
                  header: 'Imágenes',
                  dataIndex: 'numeroImagenes',
                  align: 'center',
                  width: 100,
                  sortable: true
            }
        ]
    }); 
    
    
    //****************************************************************
    //                    Combos para Filtros de Busqueda
    //****************************************************************
    //Campo Fecha Desde General
    var dateFechaDesde = new Ext.form.DateField
        ({
            id: 'dateFechaDesde',
            fieldLabel: 'Fecha Desde',
            labelAlign: 'left',
            xtype: 'datefield',
            minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -1),
            format: 'd-m-Y',
            width: 300,
            editable: false,
            name: 'dateFechaDesde',
            vtype: 'daterange',
            endDateField: 'dateFechaHasta'
        });
    
    //Campo Fecha Hasta General
    var dateFechaHasta = new Ext.form.DateField
        ({
            id: 'dateFechaHasta',
            fieldLabel: 'Fecha Hasta',
            labelAlign: 'left',
            xtype: 'datefield',
            format: 'd-m-Y',
            width: 300,
            editable: false,
            name: 'dateFechaHasta',
            vtype: 'daterange',
            startDateField: 'dateFechaDesde'
        });
   

    
    var filterPanel = Ext.create('Ext.form.Panel',
    {
        bodyPadding: 7,
        border:false,        
        buttonAlign: 'center',
        layout: 
        {
            type:'table',
            columns: 5
        },
        bodyStyle: 
        {
            background: '#fff'
        },  
        collapsible : true,
        collapsed: true,
        width: 900,
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
            {html:"&nbsp;",border:false,width:75},		
            dateFechaDesde,
            {html:"&nbsp;",border:false,width:100},
            dateFechaHasta,
            {html:"&nbsp;",border:false,width:75}
        ],	
        renderTo: 'filtroInstalacionesFinalizadas'
    }); 
    
    
    Ext.define('dataInstalaciones',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name:'name',  type: 'string',  mapping:'name'},
            {name:'value', type: 'int',     mapping:'value'}
        ]
    });
    
    /*
     * Gráfico Pastel Instalaciones de una Ciudad
     */
    for( var i = 0; i < arrayCantones.length; i++)
    {
        arrayStores[i] = Ext.create('Ext.data.JsonStore',
        {
            model: 'dataInstalaciones',
            proxy:
            {
                type: 'ajax',
                url: strUrlGetInstalaciones,
                timeout: 1200000,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    canton: arrayCantones[i]
                }
            },
            listeners:
            {
                load: function(store)
                {  
                    var strCanton = store['proxy']['extraParams'].canton;

                    if( store.totalCount == 0 )
                    {
                        Ext.Msg.alert('Atención', 'No se encontraron instalaciones ingresadas para este mes en '+strCanton);
                    }

                    Ext.getCmp('chartPastelInstalaciones'+strCanton).redraw();

                    hideMask('pastel'+strCanton);
                }
            },
            autoLoad: true
        });//arrayStores[i]
        
        arrayGraficoPastel[i] = Ext.create('widget.panel',
        {
            width: 370,
            height: 350,
            title: 'Instalaciones del Mes - '+arrayCantones[i],
            renderTo: 'gridPastelInstalaciones'+arrayCantones[i],
            layout: 'fit',
            items:
            {
                xtype: 'chart',
                id: 'chartPastelInstalaciones'+arrayCantones[i],
                animate: true,
                store: arrayStores[i],
                legend:
                {
                    position: 'right'
                },
                insetPadding: 5,
                theme: 'Base:gradients',
                series:
                [{
                    type: 'pie',
                    field: 'value',
                    id: 'seriePastel',
                    showInLegend: true,
                    donut: false,
                    tips: 
                    {
                        trackMouse: true,
                        width: 110,
                        height: 58,
                        renderer: function(store, storeItem)
                        {
                            var total = 0;
                            
                            store['store'].each(function(rec) 
                            {
                                total += (rec.get('value') * 1);
                            });

                            var porcentaje = Math.round((storeItem['storeItem'].get('value') * 100) / total);
                            this.setTitle( storeItem['storeItem'].get('name') + ': ' + porcentaje 
                                           + '%<br/>('+storeItem['storeItem'].get('value')+' instalaciones)');
                        }
                    },
                    highlight: 
                    {
                        segment:
                        {
                            margin: 20
                        }
                    },
                    label:
                    {
                        field: 'name',
                        display: 'rotate',
                        contrast: true,
                        font: '12px Arial'
                    }
                }]
            },
            dockedItems: 
            [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: 
                [      
                    { xtype: 'tbfill' },
                    {
                        id: i+'',
                        text: 'Actualizar',
                        handler: function(store)
                        {  
                            doMask(Ext.getCmp('chartPastelInstalaciones'+arrayCantones[store.id]), 'pastel'+arrayCantones[store.id]);
                            arrayStores[store.id].load();
                        }
                    }
                ]
            }],
            listeners:
            {
                afterrender:
                {
                    fn: function()
                    {
                        doMask(Ext.getCmp('chartPastelInstalaciones'+arrayCantones[i]), 'pastel'+arrayCantones[i]);
                    }
                }
            }
        });//arrayGraficoPastel[i]
        
    }//for( var i = 0; i < arrayCantones.length; i++)
});


function doMask(chart, name)
{
    arrayExtJsCmpMask[name] = new Ext.LoadMask(chart, { msg: 'Cargando...' });
    arrayExtJsCmpMask[name].show();
}


function hideMask(name)
{
    arrayExtJsCmpMask[name].hide();
}


function buscar()
{  
    if ( Ext.getCmp('dateFechaDesde').getValue() !== null ||
         Ext.getCmp('dateFechaHasta').getValue() !== null )
    {
        if (Ext.getCmp('dateFechaDesde').getValue() > Ext.getCmp('dateFechaHasta').getValue())
        {
            Ext.Msg.show
            ({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
        else
        {
            store.loadData([],false);

            store.proxy.extraParams = 
            {
                fechaDesde : Ext.getCmp('dateFechaDesde').value ? Ext.getCmp('dateFechaDesde').value : '', 
                fechaHasta : Ext.getCmp('dateFechaHasta').value ? Ext.getCmp('dateFechaHasta').value : '', 
            };
            
            store.load();
        }
    }
    else
    {
        Ext.Msg.show
        ({
            title:'Error en Busqueda',
            msg: 'Por favor elija un rango de fechas para realizar la búsqueda.',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
    }
}

function limpiar()
{
    Ext.getCmp('dateFechaDesde').setValue(null);
    Ext.getCmp('dateFechaHasta').setValue(null);
    Ext.getCmp('dateFechaDesde').setMaxValue(Ext.Date.add(new Date(), Ext.Date.YEAR, +1));
    Ext.getCmp('dateFechaDesde').setMinValue(Ext.Date.add(new Date(), Ext.Date.YEAR, -1));
    Ext.getCmp('dateFechaHasta').setMaxValue(Ext.Date.add(new Date(), Ext.Date.YEAR, +1));
    Ext.getCmp('dateFechaHasta').setMinValue(Ext.Date.add(new Date(), Ext.Date.YEAR, -1));
		
    store.loadData([],false);
    
    store.proxy.extraParams = 
    {
        fechaDesde : Ext.getCmp('dateFechaDesde').value ? Ext.getCmp('dateFechaDesde').value : '', 
        fechaHasta : Ext.getCmp('dateFechaHasta').value ? Ext.getCmp('dateFechaHasta').value : '', 
    };

    store.load();
}