var dataRespuestaUnica = JSON.parse(strListRespuestaUnica.replace(/&quot;/g, '"'));

var permisos = {
    enunciado: {
        index: (isShowEnunciadoIndex ? true : false),
        new: (isShowEnunciadoNew ? true : false),
        create: (isShowEnunciadoCreate ? true : false),
        edit: (isShowEnunciadoEdit ? true : false),
        delete: (isShowEnunciadoDelete ? true : false),
    },
    proceso: {
        index: (isShowProcesoIndex ? true : false),
        new: (isShowProcesoNew ? true : false),
        create: (isShowProcesoCreate ? true : false),
        edit: (isShowProcesoEdit ? true : false),
        delete: (isShowProcesoDelete ? true : false),
    },
    documento: {
        index: (isShowDocumentoIndex ? true : false),
        new: (isShowDocumentoNew ? true : false),
        create: (isShowDocumentoCreate ? true : false),
        edit: (isShowDocumentoEdit ? true : false),
        delete: (isShowDocumentoDelete ? true : false),
    },
    respuesta: {
        index: (isShowRespuestaIndex ? true : false),
        new: (isShowRespuestaNew ? true : false),
        create: (isShowRespuestaCreate ? true : false),
        edit: (isShowRespuestaEdit ? true : false),
        delete: (isShowRespuestaDelete ? true : false),
    },
    adicionales: {
        ShowTagPlantilla: (isShowTagPlantilla ? true : false),
        ShowSelectorProceso: (isShowSelectorProceso ? true : false)
    }
}



Ext.onReady(function () {
    Ext.define('modalListProceso', {
        extend: 'Ext.data.Model',
        fields: [
            { name: 'idProceso', type: 'integer' },
            { name: 'nombre', type: 'string' }
        ]
    });
    inicializarTabs();
    let display = (permisos.proceso.index || permisos.documento.index || permisos.respuesta.index) ? "none" : "show";
    document.getElementById("msjAdmin").style.display = display;
});





var varSatic = {
    textTieneClausualaRelacionada: 'Tiene Cláusulas relacionadas',
    textContratoAdhesion: 'Contrato de adhesión',
    textDefaultTagPlantilla: '',
    textDefaultProceso: 'LinkDatosBancarios',
    textSelectValueRequired: 'Check único',
    respuestaSeleccionUnica: dataRespuestaUnica || [], //PARAMETRIZADO (SI, NO)

}
var global = {
    indexPolitica: 'poli-',
    indexClausula: 'clau-',
    panel: null,
    panelTabVisualizar: null,
    panelTabFormulario: null,
    panelTabAdministrar: null,
    openTabFormulario: true
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
        items: [{
            id: 'idTabVisualizar',
            title: 'Visualizar',
            contentEl: 'tab1',
            listeners: {
                activate: function (tab) {
                    if (tab.showOnParentShow != false) {
                        renderizarTabVisualizar();
                    }
                }
            }
        },
        {
            id: 'idTabFormulario',
            title: 'Crear/Actualizar',
            contentEl: 'tab2',
            hidden: !permisos.enunciado.create,
            listeners: {
                activate: function (tab) {
                    if (global.openTabFormulario && (tab.showOnParentShow != false || global.panelTabFormulario == null)) {
                        renderizarTabFormulzario({});
                    }

                }
            }
        },
        {
            id: 'idTabAdministrar',
            title: 'Administración',
            contentEl: 'tab3',
            listeners: {
                activate: function (tab) {
                    if (tab.showOnParentShow != false) {
                        renderizarTabAdministracion();
                    }
                }
            }
        }
        ]
    });
}


/**
 * Renderiza componetes en tab administracion
 * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 07-10-2022
 * @since 1.0
 */
function renderizarTabVisualizar() {
    let tab = Ext.get('tab1');
    let view = Ext.get('myTabs');
    let id_index = 'idEnunciado';
    let strParametros = JSON.stringify({});
    let dataConfigGridProcesos = {
        name: 'AdminPoliticaClausula',
        title: 'Gestionar Política y Cláusulas',
        height: view.getHeight() - 30,
        autoScroll: true,
        view: view,
        autoLoad: true,
        ajax: {
            url: urlManagerAdminDocEnunciado,
            reader: {
                type: 'json',
                root: 'data',
                message: 'message',
                statusProperty: 'status',
            },
            extraParams: {
                strMetodo: 'list',
                strParametros: strParametros
            },
        },
        fields: [
            { name: id_index, type: 'integer' },
            { name: 'codigo', type: 'string' },
            { name: 'nombre', type: 'string' },
            { name: 'descripcion', type: 'string' },
            { name: 'tagPlantilla', type: 'string' },
            { name: 'empresaCod', type: 'integer' },

            { name: 'clausulas', type: 'json' },
            { name: 'documentos', type: 'json' },
            { name: 'atributos', type: 'json' },
            { name: 'respuestas', type: 'json' },

            { name: 'usrCreacion', type: 'string' },
            { name: 'feCreacion', type: 'string' },
            { name: 'usrModificacion', type: 'string' },
            { name: 'feModificacion', type: 'string' }
        ],
        columns: [
            {
                dataIndex: id_index,
                hidden: true,
                hideable: false,
            },
            {
                header: 'Información Política',
                flex: 2,
                sortable: false,
                renderer: function (
                    values,
                    metaData,
                    record,
                    rowIndex,
                    colIndex,
                    store
                ) {

                    let raw = record.raw;
                    let documentos = raw.documentos || [];
                    let procesos = raw.procesos || [];
                    let cols = 40;
                    let rowsTitulo = getSizeRows(raw.nombre, cols);
                    let rowsDescripcion = getSizeRows(raw.descripcion, cols);
                    let html = '<ul>';
                    html += '<li><b>Código:</b><br/>&nbsp;' + (raw.codigo || '') + ' </li>';
                    html += '<li><b>Título:</b><br/><textarea rows="' + rowsTitulo + '" cols="' + cols + '"  style="text-align: justify;" disabled="true">' + (raw.nombre || '') + '</textarea> </li>';
                    html += '<li><b>Descripción:</b><br/><textarea rows="' + rowsDescripcion + '" cols="' + cols + '"  style="text-align: justify;" disabled="true">' + (raw.descripcion || '') + '</textarea> </li>';

                    if (permisos.adicionales.ShowTagPlantilla) {
                        html += '<li><b>Tag Plantilla:</b><br/>&nbsp;' + (raw.tagPlantilla || '') + ' </li>';
                    }

                    if (permisos.adicionales.ShowSelectorProceso) {
                        html += '<li><b>Procesos:</b></li>';
                        html += '<ul>';
                        procesos.forEach((el, i) => {
                            html += '<li>&nbsp;<b>' + (i + 1) + ':</b>' + (el.nombreProceso || '') + ' </li>'
                        });
                        html += ' </ul>';
                        html += ' </li>';
                    }

                    html += '<li><b>Aplica A:</b></li>';
                    html += '<ul>';
                    documentos.forEach((el, i) => {
                        html += '<li>&nbsp;<b>' + (i + 1) + ':</b>' + (el.nombreDocumento || '') + ' </li>'
                    });
                    html += ' </ul>';
                    html += ' </li>';
                    return html;

                },
            },
            {
                header: 'Configuración',
                dataIndex: 'atributos',
                flex: 3,
                sortable: false,
                renderer: function (
                    values,
                    metaData,
                    record,
                    rowIndex,
                    colIndex,
                    store
                ) {
                    let raw = record.raw;
                    let atributos = raw.atributos || [];
                    let clausulas = raw.clausulas || [];

                    let html = '<ul>';

                    //renderizar atributos
                    atributos.sort(function (a, b) {
                        var textA = a.idCabEnunciado;
                        var textB = b.idCabEnunciado;
                        return (textA > textB) ? -1 : (textA < textB) ? 1 : 0;
                    });

                    atributos.forEach(el => {
                        let valoresName = (el.valoresName || []);
                        let subHtml = '';
                        valoresName.forEach(val => { subHtml += '&nbsp;' + val + ', ' });
                        subHtml = subHtml.substr(0, subHtml.length - 2);
                        let cols = 60;
                        let rows = (subHtml.length / cols);
                        html += '<li><b>' + el.nombreCabEnunciado + ':</b><br/> ';
                        html += '<textarea rows="' + rows + '" cols="' + cols + '" style="text-align: justify;" disabled="true">' + subHtml + '</textarea>';
                        html += '</li>';
                    });
                    //renderizar clausulas
                    html += '<ul>'
                    html += '<li><b>Cláusulas Relacionadas</b><br/> ';
                    if (clausulas.length) {
                        clausulas.forEach((el, i) => {
                            html += '<li><b>' + (i + 1) + ':</b>' + (el.nombre || '') + ' </li>'
                        });
                    } else {
                        html += '<li><u>NO APLICA</u></li>';
                    }
                    html += ' </ul>'


                    html += ' </ul>'
                    return html;
                },
            },
            {
                header: 'Creacion',
                flex: 1,
                sortable: false,
                renderer: function (
                    values,
                    metaData,
                    record,
                    rowIndex,
                    colIndex,
                    store
                ) {
                    let data = record.data;
                    return '<ul><li><b>' + data.usrCreacion + '</b></li><li>' + data.feCreacion + ' </li></ul>';

                },
            },
            {
                header: 'Actualización',
                flex: 1,
                sortable: false,
                renderer: function (
                    values,
                    metaData,
                    record,
                    rowIndex,
                    colIndex,
                    store
                ) {
                    let data = record.data;
                    return '<ul><li><b>' + data.usrModificacion + '</b></li><li>' + data.feModificacion + ' </li></ul>';

                },
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                text: 'Acciones',
                align: 'center',
                flex: 1,
                sortable: false,
                items: function () {
                    let buttons = [];
                    if (permisos.enunciado.edit) {
                        buttons.push({
                            iconCls: 'button-grid-edit',
                            tooltip: 'Editar',
                            handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                let raw = data.raw;
                                let tab = Ext.getCmp('idTabFormulario');
                                global.openTabFormulario = false;
                                global.panel.setActiveTab(tab);

                                if (global.panelTabFormulario) {
                                    global.panelTabFormulario.destroy();
                                    global.panelTabFormulario = null;
                                }
                                renderizarTabFormulzario({
                                    dataEditar: {
                                        index: global.indexPolitica,
                                        datos: raw,
                                        rowIndex
                                    }
                                });
                            }
                        })
                    }
                    if (permisos.enunciado.delete) {
                        buttons.push({
                            iconCls: 'button-grid-delete',
                            tooltip: 'Eliminar',
                            handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                let store = data.store;
                                let raw = data.raw;
                                let rawNew = store.data.items[rowIndex].data;
                                let strParametros = JSON.stringify({ idEnunciado: rawNew.idEnunciado });
                                gridActionEliminarRegistro({
                                    raw,
                                    rawNew,
                                    id_index,
                                    view: view,
                                    url: urlManagerAdminDocEnunciado,
                                    parameter: { 'strMetodo': 'delete', 'strParametros': strParametros },
                                    store
                                });
                            }
                        })
                    }
                    return buttons;
                }()
            }
        ],
        tbar: [],
        tbar_refresh: true

    }

    if (permisos.enunciado.create) {
        dataConfigGridProcesos.tbar.push(
            new Ext.Button({
                text: 'Agregar Política y Cláusulas',
                iconCls: 'icon_add',
                border: true,
                handler: function () {
                    let tab = Ext.getCmp('idTabFormulario');
                    global.openTabFormulario = false;
                    global.panel.setActiveTab(tab);

                    if (global.panelTabFormulario) {
                        global.panelTabFormulario.destroy();
                        global.panelTabFormulario = null;
                    }
                    renderizarTabFormulzario({});
                }
            })
        )
    }
    let item = generateGridManager(dataConfigGridProcesos);

    let objPanel = Ext.create('Ext.form.Panel', {
        renderTo: tab,
        height: view.getHeight() - 25,
        fullscreen: true,
        layout: 'anchor',
        items: item
    });

    objPanel.show();

    global.panelTabVisualizar = objPanel;

}
/**
 * Renderiza componetes en tab administracion
 * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 07-10-2022
 * @since 1.0
 */
function renderizarTabFormulzario(data) {
    const { dataEditar } = data;
    let editar = dataEditar ? true : false;

    let form = {
        title: 'Crear nueva Política o Cláusulas',
        buttonUnoText: "Guardar",
        buttonUnoIcon: 'icon_save',

        buttonDosText: 'Limpiar',
        buttonDosIcon: 'icon_limpiar',

        buttonTresText: "Restaurar",
        buttonTresIcon: 'icon_refresh',
        buttonTresHidden: true,
    }

    if (editar) {
        form.title = 'Modificar Política o Cláusulas';
        form.buttonUnoText = 'Actualizar';
        form.buttonTresHidden = false;
    }





    let view = global.panel.getEl();
    let tab = Ext.get('tab2');
    const loadInputsPlantilla = (datos) => {       
        if (datos.length==0) {
            Ext.Msg.alert('Alerta ', 'No existe configuración de formulario plantilla.');
            myMask.destroy();
        } else {
            global.arrayAtributos = datos;
            let arrayItemsInputsPolitica = generateInputFormulario({
                index: global.indexPolitica,
                datos: global.arrayAtributos
            });

            var objPanel = Ext.create('Ext.form.Panel', {
                renderTo: tab,
                height: view.getHeight() - 25,
                autoScroll: true,
                fullscreen: true,
                title: form.title,
                items: [{

                    title: 'Política',
                    id: 'field-from-politica',
                    name: 'field-from-politica',
                    xtype: 'fieldset',
                    layout: {
                        type: 'table',
                        columns: 3,
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
                    collapsible: true,
                    collapsed: false,
                    margin: 10,
                    padding: 5,
                    items: arrayItemsInputsPolitica
                },
                {

                    title: 'Cláusulas relacionadas',
                    xtype: 'fieldset',
                    id: 'field-grid-clausula',
                    name: 'field-grid-clausula',
                    collapsible: true,
                    collapsed: true,
                    margin: 10,
                    padding: 5,
                    listeners: {
                        beforeexpand: function (e) {
                            if (e.items.length == 0) {
                                let dataConfigGridRepuesta = {
                                    name: 'GestorClausulas',
                                    title: 'Gestionar Cláusulas',
                                    view: e,
                                    fields: [
                                        { name: 'rowIndex', type: 'integer' },
                                        { name: 'idEnunciado', type: 'integer' },
                                        { name: 'codigo', type: 'string' },
                                        { name: 'nombre', type: 'string' },
                                        { name: 'descripcion', type: 'string' },
                                        { name: 'tagPlantilla', type: 'string' },
                                        { name: 'empresaCod', type: 'integer' },

                                        { name: 'clausulas', type: 'json' },
                                        { name: 'documentos', type: 'json' },
                                        { name: 'atributos', type: 'json' },
                                        { name: 'respuestas', type: 'json' },

                                        { name: 'usrCreacion', type: 'string' },
                                        { name: 'feCreacion', type: 'string' },
                                        { name: 'usrModificacion', type: 'string' },
                                        { name: 'feModificacion', type: 'string' }
                                    ],
                                    columns: [{
                                        dataIndex: 'idEnunciado',
                                        hidden: true,
                                        hideable: false,
                                    },

                                    {
                                        header: 'Información Cláusula',
                                        flex: 2,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {

                                            let raw = record.raw;
                                            let documentos = raw.documentos || [];
                                            let procesos = raw.procesos || [];
                                            let cols = 27;
                                            let rowsTitulo = getSizeRows(raw.nombre, cols);
                                            let rowsDescripcion = getSizeRows(raw.descripcion, cols);
                                            let html = '<ul>';
                                            html += '<li><b>Código:</b><br/>&nbsp;' + (raw.codigo || '') + ' </li>';
                                            html += '<li><b>Título:</b><br/><textarea rows="' + rowsTitulo + '" cols="' + cols + '"  style="text-align: justify;" disabled="true">' + (raw.nombre || '') + '</textarea> </li>';
                                            html += '<li><b>Descripción:</b><br/><textarea rows="' + rowsDescripcion + '" cols="' + cols + '"  style="text-align: justify;" disabled="true">' + (raw.descripcion || '') + '</textarea> </li>';


                                            if (permisos.adicionales.ShowTagPlantilla) {
                                                html += '<li><b>Tag Plantilla:</b><br/>&nbsp;' + (raw.tagPlantilla || '') + ' </li>';
                                            }

                                            if (permisos.adicionales.ShowSelectorProceso) {
                                                html += '<li><b>Procesos:</b></li>';
                                                html += '<ul>';
                                                procesos.forEach((el, i) => {
                                                    html += '<li>&nbsp;<b>' + (i + 1) + ':</b>' + (el.nombreProceso || '') + ' </li>'
                                                });
                                                html += ' </ul>';
                                                html += ' </li>';
                                            }

                                            html += '<li><b>Aplica A:</b></li>';
                                            html += '<ul>';
                                            documentos.forEach((el, i) => {
                                                html += '<li>&nbsp;<b>' + (i + 1) + ':</b>' + (el.nombreDocumento || '') + ' </li>'
                                            });
                                            html += ' </ul>';
                                            html += ' </li>';
                                            return html;

                                        },
                                    },
                                    {
                                        header: 'Configuración',
                                        dataIndex: 'atributos',
                                        flex: 3,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let raw = record.raw;
                                            let atributos = raw.atributos || []; 

                                            let html = '<ul>';

                                            //renderizar atributos
                                            atributos.sort(function (a, b) {
                                                var textA = a.idCabEnunciado;
                                                var textB = b.idCabEnunciado;
                                                return (textA > textB) ? -1 : (textA < textB) ? 1 : 0;
                                            });

                                            atributos.forEach(el => {
                                                let valoresName = (el.valoresName || []);
                                                let subHtml = '';
                                                valoresName.forEach(val => { subHtml += '&nbsp;' + val + ', ' });
                                                subHtml = subHtml.substr(0, subHtml.length - 2);
                                                let cols = 60;
                                                let rows = (subHtml.length / cols);
                                                html += '<li><b>' + el.nombreCabEnunciado + ':</b><br/> ';
                                                html += '<textarea rows="' + rows + '" cols="' + cols + '" style="text-align: justify;" disabled="true">' + subHtml + '</textarea>';
                                                html += '</li>';
                                            });


                                            html += ' </ul>'
                                            return html;
                                        },
                                    },

                                    {
                                        header: 'Creacion',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrCreacion + '</b></li><li>' + data.feCreacion + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Actualización',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrModificacion + '</b></li><li>' + data.feModificacion + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Acciones',
                                        xtype: 'actioncolumn',
                                        text: 'Acciones',
                                        align: 'center',
                                        flex: 1,
                                        sortable: false,
                                        items: [
                                            {
                                                iconCls: 'button-grid-edit',
                                                tooltip: 'Editar',
                                                handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                    let raw = data.raw;
                                                    formClausulaEditar({ view, raw, rowIndex });
                                                }
                                            },
                                            {
                                                iconCls: 'button-grid-delete',
                                                tooltip: 'Eliminar',
                                                handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                    let raw = data.raw;
                                                    let store = data.store;
                                                    formClausulaQuitar({ view, raw, rowIndex, store });
                                                }
                                            },
                                        ]
                                    },
                                    ],
                                    tbar: [
                                        new Ext.Button({
                                            text: 'Agregar Cláusula',
                                            iconCls: 'icon_add',
                                            border: true,
                                            handler: function () {
                                                modalFormClausula({ action: 'agregar' });
                                            }
                                        })
                                    ]

                                }
                                let item = generateGridManager(dataConfigGridRepuesta);
                                e.add(item);
                            }
                        }
                    },
                    items: []
                }
                ],
                buttonAlign: 'center',
                buttons: [{
                    iconCls: form.buttonDosIcon,
                    text: form.buttonDosText,
                    handler: function () {
                        if (global.panelTabFormulario) {
                            global.panelTabFormulario.destroy();
                            global.panelTabFormulario = null;
                        }
                        renderizarTabFormulzario({});
                    },
                },
                {
                    iconCls: form.buttonTresIcon,
                    text: form.buttonTresText,
                    hidden: form.buttonTresHidden,
                    handler: function () {
                        if (global.panelTabFormulario) {
                            global.panelTabFormulario.destroy();
                            global.panelTabFormulario = null;
                        }
                        renderizarTabFormulzario(data);
                    },
                },
                {
                    iconCls: form.buttonUnoIcon,
                    text: form.buttonUnoText,
                    handler: function () {
                        formSavePolitica();
                    },
                },
                ],
                listeners: {
                    afterrender: function (e) {
                        loadComportamiento({ index: global.indexPolitica, view, dataEditar });
                    }
                }
            });
            objPanel.show();
            global.panelTabFormulario = objPanel;
        } 

    }


    runAjax({
        title: 'Obteniendo formulario de plantilla',
        view: view,
        url: urlgetGlobalListData,
        metodo: 'POST',
        parameter: { 'strMetodo': 'listInputPlantilla', 'strParametros': '{}' },
        callBackSuccess: loadInputsPlantilla,
        callBackeError: null
    });





}
/**
 * Renderiza componetes en tab administracion
 * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 07-10-2022
 * @since 1.0
 */
function renderizarTabAdministracion() {


    let view = global.panel.getEl();
    let tab = Ext.get('tab3');

    let objPanel = Ext.create('Ext.form.Panel', {
        renderTo: tab,
        height: view.getHeight() - 25,
        autoScroll: true,
        title: 'Administracion',
        fullscreen: true,
        layout: {
            layout: 'vbox',
            pack: 'center',
            align: 'middle',
            autoScroll: true,
            tableAttrs: {
                style: {
                    width: '100%',
                },
            },
            tdAttrs: {
                align: 'center',
                valign: 'top',
                autoScroll: true,
            },
        },
        items: [
            {
                title: 'Gestión de Procesos',
                xtype: 'fieldset',
                columnWidth: 0.5,
                defaultType: 'textfield',
                defaults: { anchor: '100%' },
                layout: 'anchor',
                collapsible: true,
                collapsed: true,
                autoHeight: true,
                autoScroll: true,
                margin: 10,
                padding: 5,
                style: { 'margin-right': '-10px', },
                hidden: !permisos.proceso.index,
                listeners: {
                    beforeexpand: function (e) {
                        if (e.items.length == 0) {
                            let id_index = 'idProceso';
                            let strParametros = JSON.stringify({});
                            let dataConfigGridProcesos = {
                                name: 'AdminProcesos',
                                title: 'Gestionar Procesos',
                                view: e,
                                autoLoad: true,
                                pageSize: 10,
                                ajax: {
                                    url: urlManagerAdminProcesos,
                                    reader: {
                                        type: 'json',
                                        root: 'data',
                                        message: 'message',
                                        statusProperty: 'status',
                                    },
                                    extraParams: {
                                        strMetodo: 'list',
                                        strParametros: strParametros
                                    },
                                },
                                fields: [
                                    { name: id_index, type: 'integer' },
                                    { name: 'nombre', type: 'string' },
                                    { name: 'codigo', type: 'string' },
                                    { name: 'descripcion', type: 'string' },
                                    { name: 'empresaCod', type: 'integer' },
                                    { name: 'estado', type: 'string' },
                                    { name: 'usrCreacion', type: 'string' },
                                    { name: 'feCreacion', type: 'string' },
                                    { name: 'usrUltMod', type: 'string' },
                                    { name: 'feUltMod', type: 'string' }
                                ],
                                columns: [
                                    {
                                        dataIndex: id_index,
                                        hidden: true,
                                        hideable: false,
                                    },
                                    {
                                        header: 'Código',
                                        dataIndex: 'codigo',
                                        flex: 1
                                    },
                                    {
                                        header: 'Nombre',
                                        dataIndex: 'nombre',
                                        editor: 'textfield',
                                        flex: 2,
                                        filter: {
                                            type: 'string'
                                        }
                                    },
                                    {
                                        header: 'Descripción',
                                        dataIndex: 'descripcion',
                                        editor: 'textfield',
                                        flex: 3,
                                        filter: {
                                            type: 'string'
                                        }
                                    },
                                    {
                                        header: 'Creacion',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrCreacion + '</b></li><li>' + data.feCreacion + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Actualización',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrUltMod + '</b></li><li>' + data.feUltMod + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Acciones',
                                        xtype: 'actioncolumn',
                                        text: 'Acciones',
                                        align: 'center',
                                        flex: 1,
                                        sortable: false,
                                        items: function () {
                                            let buttons = [];
                                            if (permisos.proceso.edit) {
                                                buttons.push({
                                                    tooltip: 'Guardar',
                                                    hidden: permisos.proceso.edit,
                                                    getClass: function (v, metadata, r, rowIndex, colIndex, store) {
                                                        let raw = r.raw;
                                                        let rawNew = store.data.items[rowIndex].data;
                                                        let isNew = (Object.keys(raw).length == 0);
                                                        isNew = isNew ? isNew : verificarExistenCambios({ raw, rawNew });
                                                        return isNew ? "button-grid-add" : "button-grid-edit";
                                                    },
                                                    handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                        let store = data.store;
                                                        let raw = data.raw;
                                                        let rawNew = store.data.items[rowIndex].data;
                                                        let validaciones = [];

                                                        let strValNombre = validateText({ value: rawNew.nombre, name: 'Nombre', min: 5, max: 100 });
                                                        if (strValNombre) { validaciones.push(strValNombre); }

                                                        let strValDescripcion = validateText({ value: rawNew.descripcion, name: 'Descripción', min: 5, max: 200 });
                                                        if (strValDescripcion) { validaciones.push(strValDescripcion); }

                                                        if (validaciones.length == 0) {
                                                            gridActionSaveRegistro({
                                                                id_index,
                                                                view: view,
                                                                url: urlManagerAdminProcesos,
                                                                store,
                                                                raw,
                                                                rawNew
                                                            });
                                                        } else {
                                                            let msj = validaciones[0];
                                                            Ext.Msg.alert('Alerta ', msj);
                                                        }
                                                    }
                                                })
                                            }

                                            if (permisos.proceso.delete) {
                                                buttons.push({
                                                    iconCls: 'button-grid-delete',
                                                    tooltip: 'Eliminar',
                                                    handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                        let store = data.store;
                                                        let raw = data.raw;
                                                        let rawNew = store.data.items[rowIndex].data;
                                                        gridActionEliminarRegistro({
                                                            raw,
                                                            rawNew,
                                                            id_index,
                                                            view: view,
                                                            url: urlManagerAdminProcesos,
                                                            parameter: { 'strMetodo': 'delete', 'strParametros': JSON.stringify(rawNew) },
                                                            store
                                                        });
                                                    }
                                                })
                                            }
                                            return buttons;
                                        }()
                                    },


                                ],
                                tbar_add: permisos.proceso.create,
                                tbar_refresh: true,
                                cellEditing: permisos.proceso.edit,

                            }
                            let item = generateGridManager(dataConfigGridProcesos);
                            e.add(item);
                        }
                    }
                },
                items: [],
            },
            {
                title: 'Gestión de Documentos',
                xtype: 'fieldset',
                columnWidth: 0.5,
                defaultType: 'textfield',
                defaults: { anchor: '100%' },
                layout: 'anchor',
                collapsible: true,
                collapsed: true,
                autoHeight: true,
                autoScroll: true,
                margin: 10,
                padding: 5,
                style: { 'margin-right': '-10px', },
                hidden: !permisos.documento.index,
                listeners: {
                    beforeexpand: function (e) {

                        let storeProcesos = Ext.create('Ext.data.Store', {
                            autoLoad: true,
                            model: 'modalListProceso',
                            proxy: {
                                type: 'ajax',
                                url: urlManagerAdminProcesos,
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    message: 'message',
                                    statusProperty: 'status',
                                },
                                extraParams: {
                                    strMetodo: 'list'
                                },
                            }
                        });

                        if (e.items.length == 0) {
                            let id_index = 'idDocumento';
                            let strParametros = JSON.stringify({});
                            let dataConfigGridDocumentos = {
                                name: 'AdminDocumentos',
                                title: 'Gestionar Documentos',
                                view: e,
                                autoLoad: true,
                                pageSize: 10,
                                ajax: {
                                    url: urlManagerAdminDocumentos,
                                    reader: {
                                        type: 'json',
                                        root: 'data',
                                        message: 'message',
                                        statusProperty: 'status',
                                    },
                                    extraParams: {
                                        strMetodo: 'list',
                                        strParametros: strParametros
                                    },
                                },
                                fields: [
                                    { name: id_index, type: 'integer' },
                                    { name: 'procesoId', type: 'integer' },
                                    { name: 'nombre', type: 'string' },
                                    { name: 'codigo', type: 'string' },
                                    { name: 'descripcion', type: 'string' },
                                    { name: 'estado', type: 'string' },
                                    { name: 'usrCreacion', type: 'string' },
                                    { name: 'feCreacion', type: 'string' },
                                    { name: 'usrUltMod', type: 'string' },
                                    { name: 'feUltMod', type: 'string' },

                                ],
                                columns: [
                                    {
                                        dataIndex: id_index,
                                        hidden: true,
                                        hideable: false,
                                    },
                                    {
                                        header: 'Código',
                                        dataIndex: 'codigo',
                                        flex: 1
                                    },
                                    {
                                        header: 'Proceso',
                                        dataIndex: 'procesoId',
                                        flex: 1,
                                        editor: new Ext.form.ComboBox({
                                            xtype: 'combobox',
                                            labelAlign: 'left',
                                            valueField: 'idProceso',
                                            displayField: 'nombre',
                                            allowBlank: false,
                                            store: storeProcesos

                                        }),
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let items = storeProcesos.data.items;

                                            for (let index = 0; index < items.length; index++) {
                                                const el = items[index];
                                                let raw = el.raw;
                                                if (raw['idProceso'] == values || raw.nombre == values) {
                                                    return raw.nombre
                                                }
                                            }
                                            return ' '
                                        },
                                    },
                                    {
                                        header: 'Nombre',
                                        dataIndex: 'nombre',
                                        editor: 'textfield',
                                        flex: 2
                                    },
                                    {
                                        header: 'Descripción',
                                        dataIndex: 'descripcion',
                                        editor: 'textfield',
                                        flex: 3
                                    },
                                    {
                                        header: 'Creacion',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrCreacion + '</b></li><li>' + data.feCreacion + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Actualización',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrUltMod + '</b></li><li>' + data.feUltMod + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Acciones',
                                        xtype: 'actioncolumn',
                                        text: 'Acciones',
                                        align: 'center',
                                        flex: 1,
                                        sortable: false,
                                        items: function () {
                                            let buttons = [];
                                            if (permisos.documento.edit) {
                                                buttons.push(
                                                    {
                                                        tooltip: 'Guardar',
                                                        getClass: function (v, metadata, r, rowIndex, colIndex, store) {
                                                            let raw = r.raw;
                                                            let rawNew = store.data.items[rowIndex].data;
                                                            let isNew = (Object.keys(raw).length == 0);
                                                            isNew = isNew ? isNew : verificarExistenCambios({ raw, rawNew });
                                                            return isNew ? "button-grid-add" : "button-grid-edit";
                                                        },
                                                        handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                            let store = data.store;
                                                            let raw = data.raw;
                                                            let rawNew = store.data.items[rowIndex].data;
                                                            let validaciones = [];

                                                            if (rawNew.procesoId == 0) {
                                                                validaciones.push('El campo  de <b>Proceso</b> requiere seleccionar una opción');
                                                            }

                                                            let strValNombre = validateText({ value: rawNew.nombre, name: 'Nombre', min: 5, max: 100 });
                                                            if (strValNombre) { validaciones.push(strValNombre); }

                                                            let strValDescripcion = validateText({ value: rawNew.descripcion, name: 'Descripción', min: 5, max: 200 });
                                                            if (strValDescripcion) { validaciones.push(strValDescripcion); }



                                                            if (validaciones.length == 0) {
                                                                gridActionSaveRegistro({
                                                                    id_index,
                                                                    view: view,
                                                                    url: urlManagerAdminDocumentos,
                                                                    store,
                                                                    raw,
                                                                    rawNew
                                                                });
                                                            } else {
                                                                let msj = validaciones[0];
                                                                Ext.Msg.alert('Alerta ', msj);
                                                            }


                                                        }
                                                    }
                                                )
                                            }
                                            if (permisos.documento.delete) {
                                                buttons.push(
                                                    {
                                                        iconCls: 'button-grid-delete',
                                                        tooltip: 'Eliminar',
                                                        handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                            let store = data.store;
                                                            let raw = data.raw;
                                                            let rawNew = store.data.items[rowIndex].data;
                                                            gridActionEliminarRegistro({
                                                                raw,
                                                                rawNew,
                                                                id_index,
                                                                view: view,
                                                                url: urlManagerAdminDocumentos,
                                                                parameter: { 'strMetodo': 'delete', 'strParametros': JSON.stringify(rawNew) },
                                                                store
                                                            });
                                                        }
                                                    }
                                                )
                                            }

                                            return buttons;
                                        }()
                                    }
                                ],
                                tbar_add: permisos.documento.create,
                                tbar_refresh: true,
                                cellEditing: permisos.documento.edit,
                            }
                            let item = generateGridManager(dataConfigGridDocumentos);
                            e.add(item);
                        }
                    }
                },
                items: [],
            },
            {
                title: 'Gestión de Respuestas',
                xtype: 'fieldset',
                columnWidth: 0.5,
                defaultType: 'textfield',
                defaults: { anchor: '100%' },
                layout: 'anchor',
                collapsible: true,
                collapsed: true,
                autoHeight: true,
                autoScroll: true,
                margin: 10,
                padding: 5,
                style: { 'margin-right': '-10px', },
                hidden: !permisos.respuesta.index,
                listeners: {
                    beforeexpand: function (e) {
                        if (e.items.length == 0) {

                            let id_index = 'idRespuesta';
                            let strParametros = JSON.stringify({});
                            let dataConfigGridRepuesta = {
                                name: 'AdminRepuestas',
                                title: 'Gestionar Repuestas',
                                view: e,
                                autoLoad: true,
                                pageSize: 10,
                                ajax: {
                                    url: urlManagerAdminRespuestas,
                                    reader: {
                                        type: 'json',
                                        root: 'data',
                                        message: 'message',
                                        statusProperty: 'status',
                                    },
                                    extraParams: {
                                        strMetodo: 'list',
                                        strParametros: strParametros
                                    },
                                },
                                fields: [
                                    { name: id_index, type: 'integer' },
                                    { name: 'nombre', type: 'string' },
                                    { name: 'valor', type: 'string' },
                                    { name: 'empresaCod', type: 'integer' },
                                    { name: 'estado', type: 'string' },
                                    { name: 'usrCreacion', type: 'string' },
                                    { name: 'feCreacion', type: 'string' },
                                    { name: 'usrUltMod', type: 'string' },
                                    { name: 'feUltMod', type: 'string' },
                                ],
                                columns: [
                                    {
                                        dataIndex: id_index,
                                        hidden: true,
                                        hideable: false,
                                    },

                                    {
                                        header: 'Nombre',
                                        dataIndex: 'nombre',
                                        flex: 2,
                                        editor: 'textfield'
                                    },
                                    {
                                        header: 'Valor',
                                        dataIndex: 'valor',
                                        flex: 2,
                                        editor: 'textfield'
                                    },
                                    {
                                        header: 'Creacion',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrCreacion + '</b></li><li>' + data.feCreacion + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Actualización',
                                        flex: 1,
                                        sortable: false,
                                        renderer: function (
                                            values,
                                            metaData,
                                            record,
                                            rowIndex,
                                            colIndex,
                                            store
                                        ) {
                                            let data = record.data;
                                            return '<ul><li><b>' + data.usrUltMod + '</b></li><li>' + data.feUltMod + ' </li></ul>';

                                        },
                                    },
                                    {
                                        header: 'Acciones',
                                        xtype: 'actioncolumn',
                                        text: 'Acciones',
                                        align: 'center',
                                        flex: 1,
                                        sortable: false,
                                        items: function () {
                                            let buttons = [];

                                            if (permisos.respuesta.edit) {
                                                buttons.push({
                                                    tooltip: 'Guardar',
                                                    getClass: function (v, metadata, r, rowIndex, colIndex, store) {
                                                        let raw = r.raw;
                                                        let rawNew = store.data.items[rowIndex].data;
                                                        let isNew = (Object.keys(raw).length == 0);
                                                        isNew = isNew ? isNew : verificarExistenCambios({ raw, rawNew });
                                                        return isNew ? "button-grid-add" : "button-grid-edit";
                                                    },
                                                    handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                        let store = data.store;
                                                        let raw = data.raw;
                                                        let rawNew = store.data.items[rowIndex].data;
                                                        let validaciones = [];


                                                        let strValNombre = validateText({ value: rawNew.nombre, name: 'Nombre', min: 2, max: 100 });
                                                        if (strValNombre) { validaciones.push(strValNombre); }

                                                        let strValValor = validateText({ value: rawNew.valor, name: 'Valor', min: 2, max: 100 });
                                                        if (strValValor) { validaciones.push(strValValor); }

                                                        if (validaciones.length == 0) {
                                                            gridActionSaveRegistro({
                                                                id_index,
                                                                view: view,
                                                                url: urlManagerAdminRespuestas,
                                                                store,
                                                                raw,
                                                                rawNew
                                                            });
                                                        } else {
                                                            let msj = validaciones[0];
                                                            Ext.Msg.alert('Alerta ', msj);
                                                        }

                                                    }
                                                });
                                            }
                                            if (permisos.respuesta.delete) {
                                                buttons.push({
                                                    iconCls: 'button-grid-delete',
                                                    tooltip: 'Eliminar',
                                                    handler: function (grid, rowIndex, colIndex, but, tr, data) {
                                                        let store = data.store;
                                                        let raw = data.raw;
                                                        let rawNew = store.data.items[rowIndex].data;
                                                        gridActionEliminarRegistro({
                                                            raw,
                                                            rawNew,
                                                            id_index,
                                                            view: view,
                                                            url: urlManagerAdminRespuestas,
                                                            parameter: { 'strMetodo': 'delete', 'strParametros': JSON.stringify(rawNew) },
                                                            store
                                                        });
                                                    }
                                                });
                                            }
                                            return buttons;
                                        }()
                                    }
                                ],
                                tbar_add: permisos.respuesta.create,
                                tbar_refresh: true,
                                cellEditing: permisos.respuesta.edit,
                            }
                            let item = generateGridManager(dataConfigGridRepuesta);
                            e.add(item);
                        }
                    }
                },
                items: [],
            },
        ],
    });

    objPanel.show();

    global.panelTabAdministrar = objPanel;

}

/**
 * Agrega comportamiento a input generados 
 * @author Jefferson Alexy Carrillo <jacarrillo@telconet.ec>
 * @version 1.0 07-10-2022
 * @since 1.0
 */
function loadComportamiento(data) {
    const { index, view, dataEditar } = data;
    var myMask = new Ext.LoadMask(Ext.getBody(), {
        msg: 'Configurando formulario, Por favor espere!!',
        style: { marginTop: '50px' }
    });

    myMask.show();



    let fieldSelectorProceso = Ext.getCmp('field-' + index + 'selectorProceso');
    fieldSelectorProceso.setVisible(permisos.adicionales.ShowSelectorProceso);

    let fieldTagPlantilla = Ext.getCmp('field-' + index + 'tagPlantilla');
    fieldTagPlantilla.setVisible(permisos.adicionales.ShowTagPlantilla);

    let elTagPlantilla = Ext.getCmp(index + 'tagPlantilla');
    elTagPlantilla.setValue(varSatic.textDefaultTagPlantilla);

    let fieldVisualizaContrato = Ext.getCmp('field-' + index + 'visualizaContrato');
    fieldVisualizaContrato.setVisible(false);

    if (index == global.indexClausula) {
        let fieldMasOpciones = Ext.getCmp('field-' + index + 'masOpciones');
        fieldMasOpciones.setVisible(false);
    }

    if (index == global.indexPolitica) {
        let fieldGridClausulas = Ext.getCmp('field-grid-clausula');
        fieldGridClausulas.setVisible(false);
    }


    const loadClausulasRelacionadas = () => {
        let checkMasOpciones = Ext.getCmp(index + 'masOpciones');
        checkMasOpciones.on({
            'change': function (e) {

                let arrayChecked = e.getChecked();
                let checkboxRequired = varSatic.textTieneClausualaRelacionada;
                arrayChecked = arrayChecked.filter((e) => { return e.boxLabel.toUpperCase() == checkboxRequired.toUpperCase() });
                //OCULTAR INPUT

                let fieldLink = Ext.getCmp('field-' + index + 'LINK');
                let fieldDetalle = Ext.getCmp('field-' + index + 'DETALLE');
                let fieldCheckDocumentoAplica = Ext.getCmp('field-' + index + 'checkDocumentoAplica');
                let fieldCheckVisualizaContrato = Ext.getCmp('field-' + index + 'visualizaContrato');

                let fieldTipoSeleccionResp = Ext.getCmp('field-' + index + 'OR-TSR');
                let fieldSeleccionResp = Ext.getCmp('field-' + index + 'OR');
                let fieldSeleccionRespDefaul = Ext.getCmp('field-' + index + 'OR-MD');
                let fieldSeleccionRespPermiteFlujo = Ext.getCmp('field-' + index + 'OR-PCF');
                let fieldSeleccionRespListBlanca = Ext.getCmp('field-' + index + 'OR-LB');
                let fieldSeleccionRespListaNegra = Ext.getCmp('field-' + index + 'OR-LN');


                let valCheck = arrayChecked.length == 0;

                fieldLink.setVisible(valCheck);
                fieldDetalle.setVisible(valCheck);
                fieldCheckDocumentoAplica.setVisible(valCheck);
                //validar si contrato de aesion esta marcado
                fieldCheckVisualizaContrato.setVisible(valCheck);
                fieldTipoSeleccionResp.setVisible(valCheck);
                fieldSeleccionResp.setVisible(valCheck);
                fieldSeleccionRespDefaul.setVisible(valCheck);
                fieldSeleccionRespPermiteFlujo.setVisible(valCheck);
                fieldSeleccionRespListBlanca.setVisible(valCheck);
                fieldSeleccionRespListaNegra.setVisible(valCheck);



                var fieldGridClausulas = Ext.getCmp('field-grid-clausula');
                fieldGridClausulas.setVisible(!valCheck);
                if (!valCheck) {
                    fieldGridClausulas.expand();

                } else {
                    setTimeout(() => {
                        let checkDocumentoAplica = Ext.getCmp(index + 'checkDocumentoAplica');
                        loadCheckVisualizarContrato(checkDocumentoAplica);
                    }, 100);
                }

            }
        });

    }


    const loadSeletRespuesta = () => {
        let element = Ext.getCmp(index + 'OR');
        if (element) {
            element.on({
                'change': function (e) {

                    let arrayList = e.displayTplData;

                    //CARGAR DATA EN RESPUESTA POR DEFAULT
                    let selectRespuestaDefault = Ext.getCmp(index + 'OR-MD');
                    selectRespuestaDefault.store.loadData(arrayList);
                    selectRespuestaDefault.select([]);
                    //CARGAR DATA EN RESPUESTA CONTINUAR CON FLUJO

                    let selectRespuestaContinuaFlujo = Ext.getCmp(index + 'OR-PCF');
                    selectRespuestaContinuaFlujo.store.loadData(arrayList);
                    selectRespuestaContinuaFlujo.select([]);
                    //CARGAR DATA EN RESPUESTA LISTA BLANCA 
                    let selectRespuestaListaBlanca = Ext.getCmp(index + 'OR-LB');
                    selectRespuestaListaBlanca.store.loadData(arrayList);
                    selectRespuestaListaBlanca.select([]);

                    //CARGAR DATA EN RESPUESTA LISTA NEGRA 
                    let selectRespuestaListaNegra = Ext.getCmp(index + 'OR-LN');
                    selectRespuestaListaNegra.store.loadData(arrayList);
                    selectRespuestaListaNegra.select([]);
                }
            });
        }
    }

    const loadSeletRespuestaListaBlanca = () => {
        let element = Ext.getCmp(index + 'OR-LB');
        if (element) {
            element.on({
                'change': function (e) {
                    let arrayListBlanca = e.value;

                    let selectRespuesta = Ext.getCmp(index + 'OR');
                    let arrayListRespuesta = selectRespuesta.displayTplData;
                    let arrayList = arrayListRespuesta.filter((e) => {
                        return arrayListBlanca.indexOf(e.id) == -1;
                    });

                    //CARGAR DATA EN RESPUESTA LISTA NEGRA 
                    let selectRespuestaListaNegra = Ext.getCmp(index + 'OR-LN');
                    let tempDataSelct = selectRespuestaListaNegra.displayTplData;
                    selectRespuestaListaNegra.store.loadData(arrayList);

                    let records = [];
                    tempDataSelct.forEach(el => {
                        let record = selectRespuestaListaNegra.findRecordByDisplay(el.nombre);
                        if (record) { records.push(record); }
                    });

                    selectRespuestaListaNegra.select(records);

                }
            });
        }
    }

    const loadSeletRespuestaListaNegra = () => {
        let element = Ext.getCmp(index + 'OR-LN');
        if (element) {
            element.on({
                'change': function (e) {
                    let arrayListNegra = e.value;

                    let selectRespuesta = Ext.getCmp(index + 'OR');
                    let arrayListRespuesta = selectRespuesta.displayTplData;
                    let arrayList = arrayListRespuesta.filter((e) => {
                        return arrayListNegra.indexOf(e.id) == -1;
                    });

                    //CARGAR DATA EN RESPUESTA LISTA BLANCA
                    let selectRespuestaListaBlanca = Ext.getCmp(index + 'OR-LB');
                    let tempDataSelct = selectRespuestaListaBlanca.displayTplData;
                    selectRespuestaListaBlanca.store.loadData(arrayList);

                    let records = [];
                    tempDataSelct.forEach(el => {
                        let record = selectRespuestaListaBlanca.findRecordByDisplay(el.nombre);
                        if (record) { records.push(record); }
                    });

                    selectRespuestaListaBlanca.select(records);


                }
            });
        }
    }


    const loadSeletTipoRespuesta = () => {
        let element = Ext.getCmp(index + 'OR-TSR');
        if (element) {
            element.on({
                'change': function (e) {
                    let selectValue = e.rawValue;;
                    let fieldSeleccionResp = Ext.getCmp(index + 'OR');
                    let fieldSeleccionRespPermiteFlujo = Ext.getCmp(index + 'OR-PCF');

                    if (selectValue == varSatic.textSelectValueRequired) {
                        fieldSeleccionResp.disable();
                        //POR DEFAUT SE MARCA LinkDatosBancarios
                        let arraytValueDefault = varSatic.respuestaSeleccionUnica;
                        let records = [];
                        arraytValueDefault.forEach(el => {
                            let record = fieldSeleccionResp.findRecordByDisplay(el);
                            if (record) {
                                records.push(record);
                            }
                        });
                        if (records.length == 0) {
                            Ext.Msg.alert('Alerta ', 'Las respuesta ' + JSON.stringify(arraytValueDefault) + " no existen en el listado de respuestas permitidas.");
                        }
                        fieldSeleccionResp.select(records);

                        //RESPUESTA PARA CONTINUAR EL FLUJO
                        fieldSeleccionRespPermiteFlujo.disable();
                        fieldSeleccionRespPermiteFlujo.select(records);


                    } else {
                        fieldSeleccionResp.enable();
                        fieldSeleccionResp.select([]);
                        fieldSeleccionRespPermiteFlujo.enable();
                        fieldSeleccionRespPermiteFlujo.select([]);
                    }

                    let selectRespuestaDefault = Ext.getCmp(index + 'OR-MD');
                    selectRespuestaDefault.select([]);

                }
            });
        }
    }


    const loadCheckVisualizarContrato = (data) => {
        let arrayChecked = data.getChecked();
        let checkboxRequired = varSatic.textContratoAdhesion;
        arrayChecked = arrayChecked.filter((e) => { return e.boxLabel.toUpperCase() == checkboxRequired.toUpperCase() });

        let element = Ext.getCmp('field-' + index + 'visualizaContrato');
        if (element) {
            let checkVal = arrayChecked.length != 0;
            element.setVisible(checkVal);
        }
    }

    const loadCheckDocumentos = (data) => {
        //CARGAR DATA DE DOCUMENTOS
        let arrayDocumentos = data || [];
        if (arrayDocumentos.length==0) {
            Ext.Msg.alert('Alerta ', 'Se requiere ingresar al menos un documento en la administración.'); 
        } else {
            let element = Ext.getCmp(index + 'checkDocumentoAplica');
            if (element) {
                element.removeAll();
                arrayDocumentos.forEach(el => {
                    element.add({
                        id: index + 'checkDocumentoAplica' + el.idDocumento,
                        boxLabel: el.nombre,
                        inputValue: el.idDocumento
                    });
                });
                element.on({
                    'change': function (e) {
                        loadCheckVisualizarContrato(e);
                    }
                });
            }

            if (dataEditar) {
                setDataFormulario(dataEditar);
            }
        }
        myMask.destroy();

    }

    const loadSelectProcesos = (data) => {
        //CARGAR DATA DE PROCESOS

        let arrayList = (data || []).map((e) => { return { id: e.idProceso, nombre: e.nombre }; });
        let element = Ext.getCmp(index + 'selectorProceso');
        if (element) {
            element.store.loadData(arrayList);
            element.on({
                'change': function (e, newVal, oldVa, dddl) {
                    let idProceso = newVal;
                    runAjax({
                        title: 'Obteniendo tipo de documentos',
                        view: view,
                        url: urlManagerAdminDocumentos,
                        metodo: 'POST',
                        parameter: { 'strMetodo': 'list', 'strParametros': '{"procesoId":"' + idProceso + '"}' },
                        callBackSuccess: loadCheckDocumentos,
                        callBackeError: null
                    });

                },
            });


            //POR DEFAUT SE MARCA LinkDatosBancarios 
            let record = element.findRecordByDisplay(varSatic.textDefaultProceso);
            if(record){
                element.select(record);
            }else{
                myMask.destroy();
            }

        }

        //EN POLITICA SE OCULTA EL CAMPO ORDENAMIENTO Y EN CLAUSULA ES VISIBLE
        let fieldOrden = Ext.getCmp('field-' + index + 'ORDEN');
        let ordenVisible = (index == global.indexClausula);
        fieldOrden.setVisible(ordenVisible);

    }



    runAjax({
        title: 'Obteniendo tipo de procesos',
        view: view,
        url: urlManagerAdminProcesos,
        metodo: 'POST',
        parameter: { 'strMetodo': 'list', 'strParametros': '{}' },
        callBackSuccess: function (data) { 
            if (data.length==0) {
                Ext.Msg.alert('Alerta ', 'Se requiere ingresar al menos un proceso en la administración.');
                myMask.destroy();
            } else {
                loadSelectProcesos(data);
                setTimeout(() => {
                    loadClausulasRelacionadas();
                    loadSeletTipoRespuesta();
                    loadSeletRespuesta();
                    loadSeletRespuestaListaBlanca();
                    loadSeletRespuestaListaNegra();
                }, 100);  
            }         

            
        },
        callBackeError: null
    });

   

}




function gridActionEliminarRegistro(data) {
    const { view, url, parameter, store, raw, rawNew } = data;
    let title = rawNew.codigo || rawNew.nombre || "";
    let isNew = (Object.keys(raw).length == 0);
    if (isNew) {
        store.remove(store.data.items[0]);
    } else {
        Ext.Msg.confirm('Alerta', 'Seguro desea eliminar este registro ' + title, function (btn) {
            if (btn == 'yes') {
                runAjax({
                    title: 'Eliminado registro ' + title,
                    view: view,
                    url: url,
                    metodo: 'POST',
                    parameter: parameter,
                    callBackSuccess: function (data) {
                        store.load();
                    },
                    callBackeError: null
                });
            }
        });
    }


}

function gridActionSaveRegistro(data) {
    const { id_index, view, url, store, raw, rawNew } = data
    let title = rawNew.codigo || rawNew.nombre || "";
    let strTitle = 'Actualizando registro ' + title;
    let strMetodo = 'edit';

    if (!rawNew[id_index]) {
        strTitle = 'Agregando nuevo registro ' + title;
        strMetodo = 'create';
    }
    if (verificarExistenCambios({ raw, rawNew })) {
        let strParameter = { 'strMetodo': strMetodo, 'strParametros': JSON.stringify(rawNew) };
        runAjax({
            title: strTitle,
            view: view,
            url: url,
            metodo: 'POST',
            parameter: strParameter,
            callBackSuccess: function (data) {
                store.load();
            },
            callBackeError: null
        });
    } else {
        Ext.Msg.alert('Alerta ', 'No existen cambios para actualizar este registro, hacer doble clic en campo a modificar.');
    }


}

function getDataFormulario(data) {
    const { index } = data;

    let arrayAtributos = global.arrayAtributos;
    let elIdEnunciado = Ext.getCmp(index + 'idEnunciado');
    let elCodigo = Ext.getCmp(index + 'codigo');
    let elTagPlantilla = Ext.getCmp(index + 'tagPlantilla');
    let elTitulo = Ext.getCmp(index + 'titulo');
    let elDescripcion = Ext.getCmp(index + 'descripcion');
    let elMasOpciones = Ext.getCmp(index + 'masOpciones');
    let elCheckDocumentoAplica = Ext.getCmp(index + 'checkDocumentoAplica');
    let elVisualizaContrato = Ext.getCmp(index + 'visualizaContrato');
    let elRespuestas = Ext.getCmp(index + 'OR');


    let arrayChecked = elMasOpciones.getChecked();
    let checkboxRequired = varSatic.textTieneClausualaRelacionada;
    arrayChecked = arrayChecked.filter((e) => { return e.boxLabel.toUpperCase() == checkboxRequired.toUpperCase() });
    let tieneClausulas = arrayChecked.length != 0;

    let clausulas = [];
    let atributos = [];
    let documentos = [];
    let respuestas = [];
    let validaciones = [];
    //VALIDA INPUT ESTATICOS


    let strValTitulo = validateText({ element: elTitulo, name: 'Título', min: 5, max: 100 });
    if (strValTitulo) { validaciones.push(strValTitulo); }

    let strValDescripcion = validateText({ element: elDescripcion, name: 'Descripción', min: 5, max: 200 });
    if (strValDescripcion) { validaciones.push(strValDescripcion); }



    if (!tieneClausulas) {
        //AGREGA DOCUMENTOS
        (elCheckDocumentoAplica.getChecked()).forEach(el => {
            let visibleEnDocumento = 'N';
            let checkboxRequired = varSatic.textContratoAdhesion;
            if (el.boxLabel.toUpperCase() == checkboxRequired.toUpperCase()) {
                let items = elVisualizaContrato.items.items;
                let aplica = items.filter((e) => { return e.boxLabel.toUpperCase() == 'SI' });
                visibleEnDocumento = aplica[0].checked ? 'S' : 'N';
            }
            documentos.push({
                "idDocumento": el.inputValue,
                "nombreDocumento": el.boxLabel,
                "visibleEnDocumento": visibleEnDocumento
            })
        });

        if (documentos.length == 0) {
            validaciones.push("Se requiere seleccionar un documento");
        }




        //AGREGA ATRIBUTOS
        (arrayAtributos).forEach(el => {
            let isInputPoliOrden = (index == global.indexPolitica && el.codigo == 'ORDEN');
            if (!isInputPoliOrden) {
                let elAtributo = Ext.getCmp(index + el.codigo);
                let valores = [];
                let valoresName = [];
                let respVal = elAtributo.value;
                if (respVal) {
                    if (!Array.isArray(respVal)) {
                        let strValInput = validateText({ value: respVal, name: el.nombre, min: 0, max: 4000 });
                        if (strValInput) { validaciones.push(strValInput); }

                        valores.push(respVal);
                        valoresName.push(respVal);
                    } else {
                        valores = respVal;
                        valoresName = respVal;
                    }

                    if (elAtributo.displayTplData) {
                        valores = [];
                        valoresName = [];
                        let display = elAtributo.displayTplData || [];
                        for (let index = 0; index < display.length; index++) {
                            const item = display[index];
                            if (item.id) {
                                valores.push((item.id).toString());
                            }
                            if (item.nombre) {
                                valoresName.push((item.nombre).toString());
                            }

                        }
                    }
                }

                if (el.esRequerido == "S" && valoresName.length == 0) {
                    let msj = "El campo <b>" + el.nombre + "</b> es requerido";
                    validaciones.push(msj);
                }

                if (valoresName.length != 0) {
                    atributos.push({
                        "codigo": el.codigo,
                        "idCabEnunciado": el.idCabEnunciado,
                        "nombreCabEnunciado": el.nombre,
                        "datos": valores,
                        "valoresName": valoresName
                    })
                }
            }

        });

        (elRespuestas.displayTplData).forEach(el => {
            respuestas.push({
                "idRespuesta": el.id,
                "nombreRespuesta": el.nombre,
            })
        });


    }
    //AGREGA CLAUSULAS
    let storeGridManager = Ext.StoreMgr.lookup('idStoreGestorClausulas');
    if (tieneClausulas && storeGridManager) {
        clausulas = getDataJsonStore(storeGridManager);
        clausulas = clausulas.map((e) => {
            delete e['usrCreacion'];
            delete e['feCreacion'];
            delete e['usrUltMod'];
            delete e['feUltMod'];
            return e;
        });
        documentos = [];
        atributos = [];
        respuestas = [];
    }


    //EJECUTA VALIDACIONES
    if (tieneClausulas && clausulas.length == 0) {
        validaciones.push("Es necesario agregar al menos una cláusula");
    }

    if (validaciones.length != 0) {
        let msj = validaciones[0];
        Ext.Msg.alert('Alerta ', msj);
    }

    //ESTRUCTURA DATA
    let params = {
        "idEnunciado": elIdEnunciado.value,
        "codigo": elCodigo.value || "######",
        "nombre": elTitulo.value,
        "descripcion": elDescripcion.value,
        "tagPlantilla": elTagPlantilla.value,
        "tieneClausulas": tieneClausulas,
        "clausulas": clausulas,
        "documentos": documentos,
        "atributos": atributos,
        "respuestas": respuestas,
        "validaciones": validaciones
    }



    return params;
}

function setDataFormulario(data) {
    const { index, datos } = data;

    let elIdEnunciado = Ext.getCmp(index + 'idEnunciado');
    let elCodigo = Ext.getCmp(index + 'codigo');
    let elTagPlantilla = Ext.getCmp(index + 'tagPlantilla');
    let elTitulo = Ext.getCmp(index + 'titulo');
    let elDescripcion = Ext.getCmp(index + 'descripcion');

    let arrayAtributosForm = global.arrayAtributos || [];

    let atributos = datos.atributos || [];
    let clausulas = datos.clausulas || [];
    let documentos = datos.documentos || [];


    elIdEnunciado.setValue(datos.idEnunciado);
    elCodigo.setValue(datos.codigo);
    elTitulo.setValue(datos.nombre);
    elDescripcion.setValue(datos.descripcion);
    elTagPlantilla.setValue(datos.tagPlantilla);

    let tieneClausulas = clausulas.length == 0 ? false : true;
    if (!tieneClausulas) {
        documentos.forEach(el => {
            let elcheckDocApl = Ext.getCmp(index + 'checkDocumentoAplica' + el.idDocumento);
            if (elcheckDocApl) { elcheckDocApl.setValue(true); }
            if (el.nombreDocumento == varSatic.textContratoAdhesion) {
                let elcheckVisualCont = Ext.getCmp(index + 'visualizaContrato' + el.visibleEnDocumento);
                if (elcheckVisualCont) { elcheckVisualCont.setValue(true); }
            }
        });

        arrayAtributosForm.forEach((el, i) => {

            let valorAtributos = atributos.filter((atr) => {
                return atr.codigo == el.codigo;
            });

            if (valorAtributos.length != 0) {
                let valorItem = valorAtributos[0];
                let valoresName = valorItem.valoresName || [];

                let elAtributo = Ext.getCmp(index + el.codigo);
                if (elAtributo) {
                    switch (elAtributo.xtype) {
                        case 'combobox':
                            let record = [];
                            valoresName.forEach(val => {
                                let recordVal = elAtributo.findRecordByDisplay(val);
                                if (recordVal) {
                                    record = record.concat(recordVal);
                                }
                            });
                            elAtributo.select(record);

                            break;
                        default:
                            valoresName.forEach(val => {
                                elAtributo.setValue(val)
                            });
                            break;
                    }

                }
            }

        });
    }



    let elmasOpcionesTieneClausulas = Ext.getCmp(index + 'masOpcionesTieneClausulas')
    elmasOpcionesTieneClausulas.setValue(tieneClausulas);
    elmasOpcionesTieneClausulas.disable();


    if (tieneClausulas) {
        let fieldGridClausulas = Ext.getCmp('field-grid-clausula');
        fieldGridClausulas.setVisible(true);
        fieldGridClausulas.expand();

        setTimeout(() => {
            let store = Ext.StoreMgr.lookup('idStoreGestorClausulas');
            clausulas.forEach(clausula => {
                let model = Ext.create('modelGestorClausulas', clausula);
                store.insert(
                    store.getCount(),
                    model
                );
            });
            store.commitChanges();
        }, 600);

    }

}

function formSavePolitica() {
    let index = global.indexPolitica;
    let params = getDataFormulario({ index });
    if (params.validaciones.length == 0) {
        let strMetodo = "";
        let strTitle = "";

        if (params.idEnunciado) {
            strMetodo = 'edit';
            strTitle = 'Actualizando ';
            params = { "enunciado": params };
        } else {
            strMetodo = 'create';
            strTitle = 'Guardando ';
        }

        let strParametros = JSON.stringify(params);
        strTitle = strTitle + 'Política y cláusulas... ';

        runAjax({
            title: strTitle,
            view: global.panelTabFormulario,
            url: urlManagerAdminDocEnunciado,
            metodo: 'POST',
            parameter: { 'strMetodo': strMetodo, 'strParametros': strParametros },
            callBackSuccess: function (data) {
                if (global.panelTabFormulario) {
                    global.panelTabFormulario.destroy();
                    global.panelTabFormulario = null;
                    global.openTabFormulario = true;
                }

                Ext.Msg.confirm('Transación registrada exitosamente', 'Visualizar política registrada', function (btn) {
                    if (btn == 'yes') {
                        global.panel.setActiveTab(Ext.getCmp('idTabVisualizar'));
                        let store = Ext.StoreMgr.lookup('idStoreAdminPoliticaClausula');
                        store.load();
                    } else {
                        renderizarTabFormulzario({});
                    }
                });
            },
            callBackeError: null
        });
    }
}

function generateInputFormulario(data) {
    const { index, datos } = data;

    let arrayItemsInputsStatic = [
        generateInput({
            type: 'text',
            name: index + 'idEnunciado',
            title: 'ID Enunciado',
            required: true,
            hidden: true,
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'text',
            name: index + 'codigo',
            title: 'Código',
            required: true,
            hidden: true,
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'text',
            name: index + 'tagPlantilla',
            title: 'Tag Plantilla',
            required: true,
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'text',
            name: index + 'titulo',
            title: 'Título',
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'textarea',
            name: index + 'descripcion',
            title: 'Descripción',
            required: true,
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'checkbox',
            name: index + 'masOpciones',
            title: 'Más Opciones',
            required: true,
            valueField: 'id',
            displayField: 'nombre',
            dataLocal: [{
                idt: index + 'masOpcionesTieneClausulas',
                id: 1,
                nombre: varSatic.textTieneClausualaRelacionada,
                checked: false
            }],
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'select',
            name: index + 'selectorProceso',
            title: 'Selecionar Proceso',
            required: true,
            valueField: 'id',
            displayField: 'nombre',
            dataLocal: [],
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'checkbox',
            name: index + 'checkDocumentoAplica',
            title: 'Marcar Documento donde aplica formulario',
            required: true,
            valueField: 'id',
            displayField: 'nombre',
            dataLocal: [],
            defaults: { anchor: '100%' }
        }),
        generateInput({
            type: 'radiobox',
            name: index + 'visualizaContrato',
            title: 'Visualización de contrato digital',
            required: true,
            valueField: 'id',
            displayField: 'nombre',
            dataLocal: [{
                idt: index + 'visualizaContratoN',
                id: 'N',
                nombre: 'No',
                checked: true
            },
            {
                idt: index + 'visualizaContratoS',
                id: 'S',
                nombre: 'Si',
                checked: false
            }
            ],
            defaults: { anchor: '100%' }
        })
    ];

    for (let i = 0; i < datos.length; i++) {
        let element = datos[i];
        // ajustes para codigo pedir cambios en backend 

        element.type = element.tipo;
        element.name = index + element.codigo;
        element.title = element.nombre;
        element.defaults = { anchor: '100%' };

        if (element.tipo == 'select' || element.tipo == 'multiSelect') {
            element.required = true;
            element.valueField = 'id';
            element.displayField = 'nombre';
            element.dataLocal = element.datos;
        }

        element.required = element.esRequerido == "S" ? true : false;

        let objInput = generateInput(element);
        arrayItemsInputsStatic.push(objInput);
    }
    return arrayItemsInputsStatic;
}

function modalFormClausula(data) {
    const { action, rowIndex, raw } = data;

    let form = {
        title: 'Crear cláusula',
        buttonUnoText: 'Agregar',
        buttonDosText: 'Limpiar',
        buttonUnoIcon: 'icon_add',
        buttonDosIcon: 'icon_limpiar',
    }

    if (action == 'modificar') {
        form.title = 'Modificar cláusula ' + raw.codigo;
        form.buttonUnoText = 'Modificar';
        form.buttonDosText = 'Restaurar';
        form.buttonUnoIcon = 'icon_edit';
        form.buttonDosIcon = 'icon_refresh';

    }

    var modal = Ext.create('Ext.window.Window', {
        title: form.title,
        autoHeight: true,
        width: 1100,
        modal: true,
        layout: {
            type: 'fit',
            align: 'stretch',
            pack: 'start',
        },
        floating: true,
        shadow: true,
        shadowOffset: 10,
        buttonAlign: 'center',
        buttons: [{
            iconCls: 'icon_cerrar',
            text: 'Cerrar',
            handler: function () {
                modal.destroy();
            }
        },
        {
            iconCls: form.buttonDosIcon,
            text: form.buttonDosText,
            handler: function () {
                modal.destroy();
                modalFormClausula({ action, rowIndex, raw });
            }
        },

        {
            iconCls: form.buttonUnoIcon,
            text: form.buttonUnoText,
            handler: function () {
                formClausulaAgregar({ modal, action, rowIndex });
            }
        }
        ],

        listeners: {
            afterrender: function (e) {

                let arrayItemsInputsClausula = generateInputFormulario({
                    index: global.indexClausula,
                    datos: global.arrayAtributos
                });

                let objPanel = Ext.create('Ext.form.Panel', {
                    fullscreen: true,
                    layout: {
                        layout: 'vbox',
                        pack: 'center',
                        align: 'middle',
                        autoScroll: true,
                        tableAttrs: {
                            style: {
                                width: '100%',
                            },
                        },
                        tdAttrs: {
                            align: 'center',
                            valign: 'top',
                            autoScroll: true,
                        },
                    },

                    items: [{
                        title: 'Cláusula',
                        xtype: 'fieldset',
                        layout: {
                            type: 'table',
                            columns: 3,
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
                        collapsible: true,
                        collapsed: false,
                        margin: 10,
                        padding: 5,
                        items: arrayItemsInputsClausula
                    },],
                    listeners: {
                        afterrender: function (e) {
                            let dataEditar = null;
                            if (action == "modificar") {
                                dataEditar = { index: global.indexClausula, datos: raw, rowIndex };
                            }

                            loadComportamiento({ index: global.indexClausula, view: modal, dataEditar });

                        },

                    }
                });
                e.add(objPanel);
            },

        }

    });

    modal.show();


}

function formClausulaAgregar(data) {
    const { modal, action, rowIndex } = data;
    let index = global.indexClausula;
    let params = getDataFormulario({ index });
    if (params.validaciones.length == 0) {
        Ext.Msg.confirm('Alerta', 'Seguro desea ' + action + ' este registro ', function (btn) {
            if (btn == 'yes') {
                if (action == "modificar") {
                    modal.getEl().mask('Actualizando Cláusula!!');
                } else {
                    modal.getEl().mask('Agregando Cláusula!!');
                }

                let store = Ext.StoreMgr.lookup('idStoreGestorClausulas');
                let storeRespaldo = getDataJsonStore(store);
                store.removeAll();


                params.rowIndex = store.getCount();
                let model = Ext.create('modelGestorClausulas', params);
                store.insert(params.rowIndex, model);

                for (let index = 0; index < storeRespaldo.length; index++) {
                    const item = storeRespaldo[index];
                    if (index != rowIndex) {
                        item.rowIndex = store.getCount();
                        let modelGridManager = Ext.create('modelGestorClausulas', item);
                        store.insert(item.rowIndex, modelGridManager);
                    }
                }


                store.commitChanges();
                modal.destroy();
            }
        })
    }

}

function formClausulaQuitar(data) {
    const { view, raw, rowIndex, store } = data;


    Ext.Msg.confirm('Alerta', 'Seguro desea eliminar la cláusula ' + raw.nombre, function (btn) {
        if (btn == 'yes') {
            view.mask('Removiendo Cláusula!!');
            let storeRespaldo = getDataJsonStore(store);
            store.removeAll();
            for (let index = 0; index < storeRespaldo.length; index++) {
                const item = storeRespaldo[index];
                if (index != rowIndex) {
                    let modelGridManager = Ext.create('modelGestorClausulas', item);
                    store.insert(store.getCount(), modelGridManager);
                }
            }
            store.commitChanges();
            view.unmask();
        }
    });


}

function formClausulaEditar(data) {
    const { raw, rowIndex } = data;
    modalFormClausula({ action: 'modificar', rowIndex, raw });
}



function validateText(params) {
    const { value, element, name, min, max } = params;
    let strMsj = null;
    // max 1000 caracteres en base.
    let data = (element ? element.value : value) || '';
    if (data.length > max) {
        strMsj = "El campo de <b>" + name + "</b> requiere un máximo de <b>" + max + "</b> caracteres"
    }
    if (data.length < min) {
        strMsj = "El campo  de <b>" + name + "</b> requiere un mínimo de <b>" + min + "</b> caracteres"
    }
    return strMsj;
}

function verificarExistenCambios(params) {
    const { raw, rawNew } = params;
    let hayCambios = false;
    for (var key in rawNew) {
        let anterior = raw[key] || "";
        let actual = rawNew[key] || "";
        if (anterior != actual) {
            hayCambios = true;
        }
    }
    return hayCambios;
}

function getSizeRows(text, cols) {
    let val = (text || '').length / cols;

    if (val % 1 != 0) {
        val = Math.abs(Math.round((val))) + 1;
    }

    return val;
}