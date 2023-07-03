/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * New Solicitudes Masivas
 */

// Clase que tiene las funciones y objetos Generales
var entidadSolicitudSeguimiento = new seguimiento();
var listView;
var itemsPerPage = 10;
var store = '';

Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*',
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

// Define Company entity
// Null out built in convert functions for performance *because the raw data is known to be valid*
// Specifying defaultValue as undefined will also save code. *As long as there will always be values in the data, or the app tolerates undefined field values*
Ext.define('Company', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'company'},
        {name: 'price'},
        {name: 'change', },
        {name: 'pctChange', },
        {name: 'lastChange', type: 'date', dateFormat: 'n/j h:ia', defaultValue: undefined},
        {name: 'realChange', type: 'date', dateFormat: 'n/j h:ia', defaultValue: undefined}
    ],
    idProperty: 'company'
});

Ext.onReady(function () {
    Ext.QuickTips.init();
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });

    Ext.define('modelProductos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idProducto', type: 'int'},
            {name: 'descripcionProducto', type: 'string'}
        ]
    });


    $.ajax({
        url: url_estados_articulo_proyecto,
        method: 'GET',
        success: function (data) {
            $.each(data.estados, function (id, registro) {
                $("#strEstadoBuscar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Estados. Por favor comuníquese con el departamento de Sistemas.");
            $('#modalMensajes').modal({show: true});
        }
    });

    $("#buscar").click(function () {
        getProyectos();
    });

    this.getProyectos();

    $("#limpiar").click(function () {
        limpiarFormBuscar();
    });
});

function getProyectos()
{
    Ext.Ajax.request({
        url: urlGetProyectos,
        method: 'post',
        async: false,
        timeout: 400000,
        params: {
            strTipo: 'Proyecto',
            strRazonS: $("#strRazonS_buscar").val(),
            strProyecto: $("#strProyecto_buscar").val(),
            strLogin: $("#strNombre_buscar").val(),
            strEstado: $("#strEstadoBuscar").val(),
        },
        success: function (response)
        {
            var text = Ext.decode(response.responseText);

            if (text.strStatus == 'OK')
            {
                $('#plantillaProyecto').html(text.strPlantillaProyecto);
            } else
            {
                $('#plantillaProyecto').html('<div class="info-error" style="padding:5px!important; margin: 15px 0px!important;">' +
                    'Ocurrio un error, por favor contactar al departamento de sistemas.' +
                    '</div>');
            }

        },
        failure: function ()
        {
            $('#mensaje_validaciones').removeClass('campo-oculto')
                .html("Se presentaron errores al consultar los Proyectos" +
                    ", favor notificar a Sistemas.");
        }
    });
}

function grafica(objServicio)
{
    entidadSolicitudSeguimiento.initSeguimiento(objServicio, 'seguimiento_content' + "_" + objServicio, 'getPanelSeguimiento' + objServicio);
}

/*
 * Función que sirve para enviar al store los datos que se
 * llenaron en los parámetros de busqueda.
 */
function buscarServicios() {
    Ext.get('panel2').update();

    if (((Ext.getCmp('fechaDesde').getValue() !== null) && (Ext.getCmp('fechaHasta').getValue() !== null)) ||
        Ext.getCmp('sltProducto').getValue() !== null)
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

        } else
        {
            store.load({params: {start: 0, limit: 10}});
        }
    }
    store.getProxy().extraParams.producto = Ext.getCmp('sltProducto').getValue();
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').getValue();
    store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
    store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
    store.getProxy().timeout = 999999;
    store.load({params: {start: 0, limit: 10}});
}

function limpiar() {

    Ext.getCmp('fechaDesde').setValue('');
    Ext.getCmp('fechaHasta').setValue('');
    Ext.getCmp('sltProducto').setValue('');
    Ext.getCmp('sltEstado').setValue('Todos');

}

function diseñar(intPuntoId)
{
    Ext.define('ListaDetalleModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idServicio', type: 'string'},
                    {name: 'productoId', type: 'string'},
                    {name: 'nombreProdcuto', type: 'string'},
                    {name: 'estado', type: 'string'},
                    {name: 'login', type: 'string'},
                    {name: 'descripcion', type: 'string'},
                    {name: 'datos', type: 'string'},
                ]
        });

    store = Ext.create('Ext.data.JsonStore',
        {
            model: 'ListaDetalleModel',
            pageSize: itemsPerPage,
            proxy:
                {
                    type: 'ajax',
                    url: urlListarServicios,
                    timeout: 9000000,
                    reader:
                        {
                            type: 'json',
                            root: 'servicios',
                            totalProperty: 'total'
                        },
                    extraParams: {fechaDesde: '',
                        fechaHasta: '',
                        strEstado: '',
                        producto: '',
                        strPunto: intPuntoId},
                    simpleSortMode: true
                },
            listeners:
                {
                    /*beforeload: function(store) 
                     {
                     store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                     store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                     store.getProxy().extraParams.producto   = Ext.getCmp('sltProducto').getValue();
                     store.getProxy().extraParams.strEstado     = Ext.getCmp('sltEstado').getValue();
                     },*/
                    load: function (store)
                    {
                        store.each(function (record)
                        {
                            if (record.data.datos != '')
                            {
                                listView = Ext.create('Ext.panel.Panel', {
                                    title: 'Listado de Servicios',
                                    width: '100%',
                                    height: 580,
                                    layout: 'accordion',
                                    class: 'accordion',
                                    timeout: 9000000,
                                    store: store,
                                    defaults: {
                                        // applied to each contained panel
                                        bodyStyle: 'padding:5px'
                                    },
                                    layoutConfig: {
                                        // layout-specific configs go here
                                        titleCollapse: false,
                                        animate: true,
                                        activeOnTop: true,
                                        collapseFirst: true,
                                        renderHidden: true,
                                        multi: true,
                                        fill: false
                                    },
                                    items: [],
                                    listeners: {
                                        render: function (accordionPanel) {
                                            //var cardBody = $('<div></div>').addClass("#pedo").appendTo($('#panel2'));
                                            var responseText = record.data.datos,
                                                obj = Ext.decode(responseText),
                                                data = obj.data;

                                            accordionPanel.add(data);
                                        }
                                    },
                                    renderTo: Ext.get('panel' + intPuntoId).update()
                                });
                            }
                        });
                    }
                }
        });
    store.load({params: {start: 0, limit: 10}});

}

/**
 * Documentación para la función 'limpiarFormBuscar'.
 *
 * Función encargada de limpiar los campos.
 *
 */
function limpiarFormBuscar()
{
    $('#strRazonS_buscar').val("");
    $('#strProyecto_buscar').val("");
    $('#strNombre_buscar').val("");
    $('#strEstadoBuscar').val("");
}