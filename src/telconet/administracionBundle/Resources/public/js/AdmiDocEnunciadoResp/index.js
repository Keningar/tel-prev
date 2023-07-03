var dataFormasContacto = [];

Ext.onReady(function () {
    dataFormasContacto = JSON.parse(strListFormaContacto.replace(/&quot;/g, '"'));
    inicializarTabs();
});


var global = {
    panel: null,
    panelTabVisualizarRespuestas: null,

}


/**
* Metodo renderiza tabs
* @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
* @version 1.0 07-10-2022
* @since 1.0
*/
function inicializarTabs() {

    global.panel = new Ext.TabPanel({

        id: 'myTabs',
        renderTo: 'my-tabs',
        activeTab: 0,

        height: 600,
        style: { width: '-webkit-fill-available' },

        fullscreen: true,
        plain: true,
        autoRender: true,
        autoShow: true,
        items: [
            {
                id: 'idTabVisualizarRespuestas',
                title: 'Respuestas Prospecto/Cliente',
                contentEl: 'tab1',
                listeners: {
                    activate: function (tab) {
                        if (tab.showOnParentShow != false) {
                            renderizarTabVisualizarRespuestas();
                        }
                    }
                }
            }
        ]
    });
}


/**
* Renderiza componetes en tab  Visualizar Respuestas
* @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
* @version 1.0 07-10-2022
* @since 1.0
*/
function renderizarTabVisualizarRespuestas() {
    let tab = Ext.get('tab1');
    let view = Ext.get('myTabs');

    let strParametros = JSON.stringify({ identificacion: strIdentificacionCliente });
    let dataConfigGridProcesos = {
        name: 'AdminVisualizarRespuestas',
        title: 'Visualizar respuestas políticas y cláusulas de Prospecto/Cliente',
        height: view.getHeight() - 240,
        autoScroll: true,
        view: view,
        autoLoad: strIdentificacionCliente ? true : false,
        pageSize: 50,
        ajax: {
            url: urlManagerAdminDocEnunciadoResp,
            reader: {
                type: 'json',
                root: 'data',
                message: 'message',
                statusProperty: 'status',
            },
            extraParams: {
                strMetodo: 'listRespuestas',
                strParametros: strParametros
            },
        },
        fields: [
            { name: 'idEnunciado', type: 'integer' },
            { name: 'idDocRespuesta', type: 'integer' },
            { name: 'idPersona', type: 'integer' },

            { name: 'codigoEnunciado', type: 'string' },
            { name: 'descripcionEnunciado', type: 'string' },

            { name: 'lista', type: 'string' },

            { name: 'identificacion', type: 'string' },
            { name: 'tipoIdentificacion', type: 'string' },
            { name: 'tipoTributario', type: 'string' },
            { name: 'tipoPersona', type: 'string' },
            { name: 'nombres', type: 'string' },
            { name: 'apellido', type: 'string' },

            { name: 'feCreacion', type: 'string' },

            { name: 'politica', type: 'string' },
            { name: 'clausula', type: 'string' },

            { name: 'valor', type: 'string' },

            { name: 'contactos', type: 'json' },
        ],

        columns: [

            {
                header: 'Estado',
                dataIndex: 'lista',
                flex: 1,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    let html = values == 'blanca' ? 'APROBADOS' : 'NO APROBADOS';
                    return '<b>' + html + '</b>';
                },

            },
            {
                header: 'Nombres',
                dataIndex: 'nombres',
                flex: 1,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            },
            {
                header: 'Apellidos',
                dataIndex: 'apellido',
                flex: 1,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            },
            {
                header: 'Tipo',
                dataIndex: 'tipoPersona',
                flex: 1,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            },
            {
                header: 'Politica',
                dataIndex: 'politica',
                flex: 2,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            },
            {
                header: 'Clausula',
                dataIndex: 'clausula',
                flex: 2,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            },

            {
                header: 'Respuesta',
                dataIndex: 'valor',
                flex: 1,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            },

            {
                header: 'Fecha y Hora',
                dataIndex: 'feCreacion',
                flex: 1,
                renderer: function (values, metaData, record) {
                    let data = record.data;
                    metaData.tdAttr = getColor(data);
                    return values;
                },
            }

        ],
        tbar: []

    }
    let itemGridVisualizar = generateGridManager(dataConfigGridProcesos);



    let objPanel = Ext.create('Ext.form.Panel', {
        renderTo: tab,
        height: view.getHeight() - 20,
        fullscreen: true,
        layout: 'anchor',
        items: [
            {
                title: 'Filtros de búsqueda',
                id: 'field-from-filtros',
                name: 'field-from-filtros',
                xtype: 'fieldset',
                layout: {
                    type: 'table',
                    columns: 4,
                    pack: 'center',
                    align: 'middle',
                    tableAttrs: {
                        style: {
                            width: '100%',
                        },
                    },
                    tdAttrs: {
                        align: 'center',
                        valign: 'top',
                    },
                },
                collapsible: false,
                collapsed: false,
                margin: 10,
                padding: 5,
                items: [
                    generateInput({
                        colspan: 1,
                        type: 'select',
                        name: 'tipoIdentificacion',
                        title: 'Selecionar Tipo Identificación',
                        required: true,
                        valueField: 'id',
                        displayField: 'nombre',
                        dataLocal: [
                            {
                                id: 'CED',
                                nombre: 'CEDULA'
                            },
                            {
                                id: 'RUC',
                                nombre: 'RUC'
                            },
                            {
                                id: 'PAS',
                                nombre: 'PASAPORTE',
                            }
                        ]
                    }),
                    generateInput({
                        colspan: 1,
                        type: 'text',
                        name: 'identificacion',
                        title: 'Identificacón',
                        value: strIdentificacionCliente
                    }),
                    generateInput({
                        colspan: 1,
                        type: 'select',
                        name: 'formaContacto',
                        title: 'Selecionar Forma Contacto',
                        required: true,
                        valueField: 'id',
                        displayField: 'nombre',
                        dataLocal: dataFormasContacto
                    }),
                    generateInput({
                        colspan: 1,
                        type: 'text',
                        name: 'contacto',
                        title: 'Contacto'
                    }),
                    generateInput({
                        colspan: 2,
                        type: 'text',
                        name: 'nombres',
                        title: 'Nombres'
                    }),
                    generateInput({
                        colspan: 2,
                        type: 'text',
                        name: 'apellidos',
                        title: 'Apellidos'
                    }),
                    generateInput({
                        colspan: 2,
                        type: 'select',
                        name: 'lista',
                        title: 'Selecionar tipo de estado',
                        required: true,
                        valueField: 'id',
                        displayField: 'nombre',
                        dataLocal: [
                            {
                                id: 'negra',
                                nombre: 'NO APROBADOS'
                            },
                            {
                                id: 'blanca',
                                nombre: 'APROBADOS'
                            }
                        ]
                    }),

                    {
                        colspan: 4,
                        layout: {
                            type: 'hbox',
                            pack: 'center',
                            align: 'middle',
                        },
                        items: [
                            {
                                xtype: 'button',
                                text: 'Limpiar',
                                tooltip: 'Limpiar',
                                iconCls: 'icon_limpiar',
                                handler: function () {
                                    filtroActionLimpiar({});
                                }
                            },
                            { xtype: 'tbspacer', width: 10 },
                            {


                                xtype: 'button',
                                text: 'Buscar',
                                tooltip: 'Buscar',
                                iconCls: 'icon_search',
                                handler: function () {
                                    filtroActionBuscar({});
                                }
                            }
                        ]


                    },

                ],

            },
            itemGridVisualizar
        ]
    });

    objPanel.show();

    global.panelTabVisualizarRespuestas = objPanel;

}





function getColor(data) {
    const { lista } = data;
    const configuration = {
        blanca: {
            fondo: "#f9f961",// "yellow",
            text: 'black',
        },
        negra: {
            fondo: "#f96161",//"red",
            text: 'white',
        }
    }
    let conf = lista.toUpperCase() == 'BLANCA' ?
        configuration['blanca'] :
        configuration['negra'];

    return 'style="background:' + conf.fondo + ';  color:' + conf.text + '; "';
}


function filtroActionLimpiar(data) {

    let elFormaContacto = Ext.getCmp('formaContacto');
    let elContacto = Ext.getCmp('contacto');

    let elTipoIdentificacion = Ext.getCmp('tipoIdentificacion');
    let elIdentificacion = Ext.getCmp('identificacion');

    let elNombres = Ext.getCmp('nombres');
    let elApellidos = Ext.getCmp('apellidos');

    let elLista = Ext.getCmp('lista');

    elFormaContacto.select([]);
    elContacto.setValue("");
    elTipoIdentificacion.select([]);
    elIdentificacion.setValue("");
    elNombres.setValue("");
    elApellidos.setValue("");
    elLista.select([]);
    let storeGrid = Ext.StoreMgr.lookup('idStoreAdminVisualizarRespuestas');
    storeGrid.removeAll();
}

function filtroActionBuscar(data) {

    let elFormaContacto = Ext.getCmp('formaContacto');
    let elContacto = Ext.getCmp('contacto');

    let elTipoIdentificacion = Ext.getCmp('tipoIdentificacion');
    let elIdentificacion = Ext.getCmp('identificacion');

    let elNombres = Ext.getCmp('nombres');
    let elApellidos = Ext.getCmp('apellidos');

    let elLista = Ext.getCmp('lista');

    let param = {
        detalleContacto: {
            formaContacto: elFormaContacto.value,
            contacto: elContacto.value,
        },
        tipoIdentificacion: elTipoIdentificacion.value,
        identificacion: elIdentificacion.value,
        nombres: elNombres.value,
        apellidos: elApellidos.value,
        lista: elLista.value,
    }

    let strMetodo = 'listRespuestas';
    let strParametros = JSON.stringify(param);

    //agregar data 
    let storeGrid = Ext.StoreMgr.lookup('idStoreAdminVisualizarRespuestas');
    storeGrid.load({
        params: { strMetodo, strParametros }
    });
}