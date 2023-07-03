Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;
var store = '';
var storeServicios = '';

Ext.onReady(function() {

    //Grid de Documentos o Contratos de Venta Externa
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'id', type: 'string'},
            {name: 'ubicacionLogicaDocumento', type: 'string'},
            {name: 'tipoDocumentoGeneral', type: 'string'},
            {name: 'feCreacion', type: 'string'},
            {name: 'usrCreacion', type: 'string'},
            {name: 'descripcion', type: 'string'},
            {name: 'linkVerDocumento', type: 'string'},
            {name: 'linkEliminarDocumento', type: 'string'}
        ]
    });

    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_gridContratoExternoDigital,
            reader: {
                type: 'json',
                root: 'logs',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        listeners: {
            load: function(store) {
                store.each(function(record) {
                });
            }
        }
    });

    store.load({params: {start: 0, limit: 10}});

    sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function(selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function(record) {
                });
            }
        }
    });

    var listView = Ext.create('Ext.grid.Panel', {
        width: 1200,
        height: 275,
        collapsible: false,
        title: '',
        selModel: sm,
        renderTo: Ext.get('listado'),
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando documentos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
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
                text: 'Id',
                width: 80,
                dataIndex: 'id'
            }, {
                text: 'Archivo Digital',
                width: 250,
                dataIndex: 'ubicacionLogicaDocumento'
            }, {
                text: 'Tipo Documento',
                dataIndex: 'tipoDocumentoGeneral',
                width: 120
            }, {
                text: 'Fecha de Creación',
                dataIndex: 'feCreacion',
                flex: 60,
            }, {
                text: 'Creado por',
                dataIndex: 'usrCreacion',
                flex: 60
            }, {
                text: 'Descripción',
                dataIndex: 'descripcion',
                flex: 200
            }, {
                text: 'Acciones',
                width: 80,
                renderer: renderAcciones,
            }]
    });


function renderAcciones(value, p, record)
{
    var iconos = '';
    iconos = iconos + '<b><a href="' + record.data.linkVerDocumento + '" \n\
                       onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';

    if (record.data.linkEliminarDocumento)
    {
        iconos = iconos + '<b><a href="#" onClick="eliminar(\'' + record.data.linkEliminarDocumento + '\')" title="Eliminar Archivo Digital" \n\
                          class="button-grid-delete"></a></b>';
    }
    return Ext.String.format(
                             iconos,
                             value,
                             '1',
                             'nada'
                            );
}
                      
    // Grid de Servicios
    Ext.define('ListaDetalleServModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'id', type: 'string'},
            {name: 'descripcion', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'precio', type: 'string'},
        ]
    });

    storeServicios = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleServModel',
        proxy: {
            type: 'ajax',
            url: url_gridServiciosVtaExterna,
            reader: {
                type: 'json',
                root: 'listado',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        listeners: {
            load: function(storeServicios) {
                storeServicios.each(function(record) {
                });
            }
        }
    });

    storeServicios.load();

    smServicios = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function(selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function(record) {
                });

            }
        }
    });

    var listViewServicios = Ext.create('Ext.grid.Panel', {
        width: 450,
        height: 200,
        collapsible: false,
        title: '',
        selModel: smServicios,
        renderTo: Ext.get('lista_servicios'),
        store: storeServicios,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Servicio',
                width: 150,
                dataIndex: 'descripcion'
            }, {
                text: 'Precio',
                width: 100,
                dataIndex: 'precio'
            }, {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                width: 100
            }]
    });

});// fin Ext.onReady


function eliminar(direccion)
{
    Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
        if (btn == 'yes') {
            Ext.Ajax.request({
                url: direccion,
                method: 'post',
                params: {strRol: strRol,
                    intIdPunto: intIdPunto},
                success: function(response) {
                    var text = response.responseText;
                    store.load();
                    storeServicios.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}


function mostrarWaitMsg()
{
    var param = '';
    flagServicosCorrecto = 1;
    flagArchivoCorrecto = 1;
    
    if(prefijoEmpresa === 'MD')
    {
        if (smServicios.getSelection().length > 0)
        {
            for (var i = 0; i < smServicios.getSelection().length; ++i)
            {
                param = param + smServicios.getSelection()[i].data.id;

                if (i < (smServicios.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            $('#infopuntotype_listadoServicios').val(param);

        }
        else
        {
            flagServicosCorrecto = 0;
            Ext.Msg.alert('Error ', 'Seleccione por lo menos un servicio de la lista');
        }
    }

    var archivo = $('#infodocumentotype_imagenes_0').val();

    if (archivo == '')
    {
        flagArchivoCorrecto = 0;
        Ext.Msg.alert('Error ', 'Ingrese al menos un documento digital');
    }
    if (flagServicosCorrecto == 1 && flagArchivoCorrecto == 1)
    {
        Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere');
        return true;
    }
    else
    {
        return false;
    }
}
