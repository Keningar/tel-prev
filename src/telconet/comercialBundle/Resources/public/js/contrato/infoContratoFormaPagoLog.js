Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;

Ext.onReady(function()
{
    var strAncho             = 950;
    var strAnchoNombreClt    = 275;
	
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325
    });

    TFNombre = new Ext.form.TextField({
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield',
        regex: /^[a-zA-Z ]?$/,
        regexText: 'Solo letras'        
    });
    TFApellido = new Ext.form.TextField({
        id: 'apellido',
        fieldLabel: 'Apellido',
        xtype: 'textfield',
        regex: /^[a-zA-Z ]?$/,
        regexText: 'Solo letras'        
        
    });			

    TFIdentificacion = new Ext.form.TextField({
        id: 'identificacion',
        fieldLabel: 'Identificacion',
        xtype: 'textfield',
        regex: /^[0-9]+(?:\.[0-9][0-9])?$/,
        regexText: 'Solo números'
    });    
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'id', type: 'int'},
            {name:'contratoId', type: 'int'},            
            {name:'personaId', type: 'int'},      
            {name:'nombre', type: 'string'},
            {name:'estado', type: 'string'},
            {name:'feCreacion', type: 'string'},
            {name:'usrCreacion', type: 'string'}
        ]
    }); 


    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: urlGridInfoContratoFormaPagoLog,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'infoContratoLogs',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', estado: '', nombre: '', apellido: '',identificacion: ''},
            simpleSortMode: true
        },
        listeners:
        {
            beforeload: function(store)
            {
                store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.nombre = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido = Ext.getCmp('apellido').getValue();
                store.getProxy().extraParams.identificacion = Ext.getCmp('identificacion').getValue();
            }
        }
    });

    listView = Ext.create('Ext.grid.Panel', 
    {
        width: strAncho,
        autoHeight: true,
        collapsible: false,
        title: '',
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: [
                //tbfill -> alinea los items siguientes a la derecha
                {xtype: 'tbfill'},
            ]
        }],
        renderTo: Ext.get('listaLogs'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Cliente',
                width: strAnchoNombreClt,
                dataIndex: 'nombre'
            },
            {
                text: 'Fecha Creacion',
                dataIndex: 'feCreacion',
                width: 100,
                renderer: function(value, metaData, record, colIndex, store, view) {
                    metaData.tdAttr = 'data-qtip="' + value + '"';
                    return value;
                }
            },
            {
                text: 'Usuario',
                dataIndex: 'usrCreacion',
                width: 100
            },            
            {
                text: 'Estado',
                dataIndex: 'estado',
                width: 100
            }
        ]
    });



    filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        defaults: 
        {
            bodyStyle: 'padding:10px'
        },
        collapsible: true,
        collapsed: true,
        width: strAncho,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: Limpiar
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
            TFIdentificacion,
            {html: "&nbsp;", border: false, width: 50},           
        ],
        renderTo: 'filtroLogs'
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
            store.loadData([],false);
            store.currentPage = 1;
            store.load();
        }
    }

    function Limpiar() 
    {
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('nombre').setValue('');
        Ext.getCmp('apellido').setValue('');
        Ext.getCmp('identificacion').setValue('');
        
        store.loadData([],false);
        store.currentPage = 1;
        store.load();
    }
});

