/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function verArchivosDigitales(contrato, empresaPrefijo = null) {

    var isAuditorSenior = (typeof grantedAuditorSenior === 'undefined') ? false : (grantedAuditorSenior ? true : false);
 
    var store = new Ext.data.Store({
        id: "verArchivosDigitalesStore",
        total: "total",
        pageSize: (empresaPrefijo === "MD" || empresaPrefijo === "EN" )? Number.MAX_SAFE_INTEGER : 10,
        autoLoad: (empresaPrefijo !== "MD" && empresaPrefijo !== "EN"  ),
        proxy: {
            type: "ajax",
            url: url_showDocumentosContrato,
            timeout: 120000,
            reader: {
                type: "json",
                totalProperty: "total",
                root: "logs",
            },
        },
        fields: [
            { name: "id", mapping: "id" },
            { name: "ubicacionLogicaDocumento", mapping: "ubicacionLogicaDocumento" },
            { name: "tipoDocumentoGeneral", mapping: "tipoDocumentoGeneral" },
            { name: "feCreacion", mapping: "feCreacion" },
            { name: "usrCreacion", mapping: "usrCreacion" },
            { name: "linkVerDocumento", mapping: "linkVerDocumento" },
            { name: "tipoContrato", mapping: "tipoContrato" },
            { name: "origen", mapping: "origen" },
            { name: "login", mapping: "login" },
            { name: "codEmpresa", mapping: "codEmpresa" },
            { name: "tipoDoc", mapping: "tipoDoc" },
            { name: "estado", mapping: "estado" },
        ],
        listeners: {
            load: function (storeGrid, records) {
                var cbxPuntosContrato = Ext.getCmp("idCbxPuntosContrato");

                if (!Ext.isEmpty(cbxPuntosContrato)) {
                    cbxPuntosContrato.enable();
                }

                var storeLogin = Ext.data.StoreManager.get("idStorePuntosContrato");

                if (!Ext.isEmpty(storeLogin)) {
                    var loginSesion = !Ext.isEmpty(
                        storeLogin.getProxy().getReader().rawData
                    )
                        ? storeLogin.getProxy().getReader().rawData["loginsesion"]
                        : "";
                    loginSesion = loginSesion.trim();

                    if (
                        !Ext.isEmpty(loginSesion) &&
                        storeLogin.find("login", loginSesion) >= 0 &&
                        storeGrid.find("login", loginSesion) >= 0
                    ) {
                        cbxPuntosContrato.setValue(loginSesion);

                        storeGrid.clearFilter(false);
                        storeGrid.filter(function (record) {
                            return (
                                !Ext.isEmpty(record.get("login")) &&
                                cbxPuntosContrato.getValue().trim() ===
                                record.get("login").trim()
                            );
                        });
                    } else {
                        cbxPuntosContrato.setValue(cbxPuntosContrato.valueDefault);
                        storeGrid.clearFilter(false);
                    }
                }
            },
        },
    });
    var gridArchivosDigitalesContrato = Ext.create("Ext.grid.Panel", {
        id: "gridArchivosDigitalesContrato",
        store: store,
        timeout: 60000,
        dockedItems: [
            {
                xtype: "toolbar",
                dock: "top",
                align: "->",
                items: [
                    (empresaPrefijo === "MD" || empresaPrefijo === "EN")
                        ? {
                            xtype: "combobox",
                            id: "idCbxPuntosContrato",
                            fieldLabel: "Puntos del cliente",
                            fieldSeparator: "",
                            displayField: "login",
                            valueField: "login",
                            style: "font-weight:bold;",
                            width: 350,
                            margin: "5 0 5 0",
                            listClass: "x-combo-list-small",
                            labelAlign: "left",
                            labelWidth: 120,
                            queryMode: "local",
                            emptyText: "Cargando...",
                            disabled: true,
                            anyMatch: true,
                            allowBlank: true,
                            editable: true,
                            typeAhead: true,
                            transform: "stateSelect",
                            enableKeyEvents: true,
                            filterAnyMatch: true,
                            filterIgnoreCase: true,
                            filterOnSelector: customFilter,
                            forceSelection: true,
                            selectOnFocus: true,
                            triggerAction: "all",
                            valueDefault: " -- Todos los puntos -- ",
                            store: Ext.create("Ext.data.Store", {
                                model: Ext.define("ModelVendedor", {
                                    extend: "Ext.data.Model",
                                    fields: [
                                        { name: "idPunto", type: "string", mapping: "idPunto" },
                                        { name: "login", type: "string", mapping: "login" },
                                        { name: "nombre", type: "string", mapping: "nombre" },
                                        { name: "estado", type: "string", mapping: "estado" },
                                        {
                                            name: "direccion",
                                            type: "string",
                                            mapping: "direccion",
                                            convert: function (value, record) {
                                                return value.toLowerCase();
                                            },
                                        },
                                    ],
                                }),
                                id: "idStorePuntosContrato",
                                autoLoad: true,
                                proxy: {
                                    type: "ajax",
                                    url: url_obtenerPuntosContrato,
                                    timeout: 60000,
                                    reader: {
                                        type: "json",
                                        root: "registros",
                                        totalProperty: "total",
                                        messageProperty: "loginsesion",
                                    },
                                    extraParams: {
                                        intIdContrato: contrato,
                                    },
                                    simpleSortMode: true,
                                },
                                listeners: {
                                    load: function (storeLogin, records) {
                                        var cbxPuntosContrato = Ext.getCmp("idCbxPuntosContrato");

                                        if (!Ext.isEmpty(cbxPuntosContrato)) {
                                            cbxPuntosContrato.emptyText =
                                                "Seleccione un punto para filtrar...";
                                            cbxPuntosContrato.applyEmptyText();
                                        }

                                        storeLogin.insert(0, {
                                            idPunto: "",
                                            login: cbxPuntosContrato.valueDefault,
                                        });

                                        var storeGrid = Ext.data.StoreManager.get(
                                            "verArchivosDigitalesStore"
                                        );

                                        if (!Ext.isEmpty(storeGrid)) {
                                            storeGrid.load();
                                        }
                                    },
                                },
                            }),
                            listeners: {
                                scope: this,
                                beforequery: function (record) {
                                    record.query = new RegExp(record.query, "i");
                                    record.forceAll = true;
                                },
                                select: function (combobox, records, eOpts) {
                                    if (combobox.getValue() === combobox.valueDefault) {
                                        store.clearFilter(false);
                                    } else {
                                        store.clearFilter(false);
                                        store.filter(function (record) {
                                            return (
                                                !Ext.isEmpty(record.get("login")) &&
                                                combobox.getValue().trim() ===
                                                record.get("login").trim()
                                            );
                                        }, this);
                                    }
                                },
                            },
                        }
                        : { xtype: "tbspacer", height: 0, hidden: true },
                    { xtype: "tbfill" },
                ],
            },
        ],
        columns: [
            new Ext.grid.RowNumberer({
                header: "#",
                flex: 0,
                align: "center",
                dataIndex: "index",
            }),
            {
                id: "id",
                header: "id",
                dataIndex: "id",
                hidden: true,
                hideable: false,
            },
            {
                header: "Archivo Digital",
                dataIndex: "ubicacionLogicaDocumento",
                width: 300,
            },
            {
                header: "Tipo Documento",
                dataIndex: "tipoDocumentoGeneral",
                width: 150,
            },
            {
                header: "Tipo Doc",
                dataIndex: "tipoDoc",
                width: 100,
                hidden:(empresaPrefijo !== "MD" && empresaPrefijo !== "EN")
            },
            {
                header: "Estado",
                dataIndex: "estado",
                width: 100,
                hidden:!isAuditorSenior
            },
            {
                header: "Fecha de Creacion",
                dataIndex: "feCreacion",
                width: 130,
                sortable: true,
            },            
            {
                header: "Creado por",
                dataIndex: "usrCreacion",
                width: 100,
                sortable: true,
            },
            {
                text: "Acciones",
                width: 80,
                renderer: renderAcciones,
            },
        ],
        bbar: Ext.create("Ext.PagingToolbar", {
            store: store,
            displayInfo: true,
            displayMsg: "Mostrando {0} - {1} de {2}",
            emptyMsg: "No hay datos que mostrar.",
        }),
    });

    gridArchivosDigitalesContrato.headerCt.insert(3, {
        header: "Tipo Contrato",
        dataIndex: "tipoContrato",
        width: 120,
    });

    gridArchivosDigitalesContrato.headerCt.insert(4, {
        header: "Origen",
        dataIndex: "origen",
        width: 60,
    });

    gridArchivosDigitalesContrato.headerCt.insert(5, {
        header: "Login Punto",
        dataIndex: "login",
        width: 150,
    });

    function renderAcciones(value, p, record) {
        var iconos = "";
        let idDoc = record.data.id;
        let urlLogic = record.data.ubicacionLogicaDocumento;
        let extDoc = urlLogic.split(".");
        let link = extDoc[1].toLowerCase() !== "pdf" ? record.data.linkVerDocumento : "";

        if (typeof grantedDescargarDocumentoPersonal !== 'undefined' && typeof grantedVerDocumentoPersonal !== 'undefined') {
            if (grantedDescargarDocumentoPersonal == "1") {
                let iconView = (
                    `<b>
                        <a
                            onClick="funcionesVisualizar('${idDoc}', '${extDoc[1]}', '${link}')"
                            title="Ver Archivo Digital"
                            class="button-grid-show"
                        ></a>
                    </b>`
                );
                iconos = iconos + iconView;

                let iconDown = (
                    `<b>
                        <a
                            onClick="descargarArchivo('${idDoc}')"
                            title="Descargar Archivo Digital"
                            class="button-grid-verParametrosIniciales"
                        ></a>
                    </b>`
                );
                iconos = iconos + iconDown;
            } else if (grantedVerDocumentoPersonal == "1") {
                let iconView = (
                    `<b>
                        <a
                            onClick="openModal('${idDoc}', '${extDoc[1]}', '${link}')"
                            title="Ver Archivo Digital"
                            class="button-grid-show"
                        ></a>
                    </b>`
                );
                iconos = iconos + iconView;
            } else {
                iconos =
                    iconos +
                    '<b><a href="' +
                    record.data.linkVerDocumento +
                    '" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';
            }
        } else {
            iconos =
                iconos +
                '<b><a href="' +
                record.data.linkVerDocumento +
                '" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';
        }

        return Ext.String.format(iconos, value, "1", "nada");
    }
    var pop = Ext.create("Ext.window.Window", {
        title: "Contrato: " + contrato,
        height: 520,
        width: 1130,
        modal: true,
        layout: {
            type: "fit",
            align: "stretch",
            pack: "start",
        },
        floating: true,
        shadow: true,
        shadowOffset: 20,
        items: [gridArchivosDigitalesContrato],
    });

    pop.show();

    function customFilter(rec, filter, displayField) {
        var value = rec.get(displayField);

        if (this.filterIgnoreCase) {
            value = value.toLocaleUpperCase();
        }

        if (this.filterIgnoreCase) {
            filter = filter.toLocaleUpperCase();
        }

        if (Ext.isEmpty(filter)) return true;

        var objOpts;
        var objRegex;

        if (this.filterAnyMatch && this.filterWordStart) {
            objOpts = this.filterIgnoreCase ? "i" : "";
            objRegex = new RegExp(
                "(^|[\\s\\.!?;\"'\\(\\)\\[\\]\\{\\}])" + Ext.escapeRe(filter),
                objOpts
            );
            return objRegex.test(value);
        } else if (this.filterAnyMatch) {
            objOpts = this.filterIgnoreCase ? "i" : "";
            objRegex = new RegExp(Ext.escapeRe(filter), objOpts);
            return objRegex.test(value);
        } else {
            objOpts = this.filterIgnoreCase ? "i" : "";
            objRegex = new RegExp("^s*" + Ext.escapeRe(filter), objOpts);
            return objRegex.test(value);
        }
    }
}

function funcionesVisualizar(idDoc, extDoc, link) {
    openModal(idDoc, extDoc, link);

}


function postGuardaHistorial(accion, idDoc) {
    Ext.Ajax.request(
        {
            url: url_guardarLog,
            method: 'post',
            params: {
                accion: accion,
                idDoc
            }
        });
}

function openModal(idDoc, extDoc, link) {
    let html = "";
    if (link) {
        html = `<div style="display:flex; ">  <img height="500px" width="auto" src="${link}" style="margin-left:auto; margin-right:auto;"> </div>`;
    } else {
        html = `<iframe id="pdf-js-viewer" 
                src="/./public/js/pdfjs/web/viewer.html?file=/soporte/gestion_documentos/${idDoc}/descargarDocumento" 
                title="webviewer" frameborder="0" width="885" height="515"></iframe>`;
    }

    postGuardaHistorial("VISUALIZAR", idDoc);

    Ext.create("Ext.window.Window", {
        title: "Documento Digital: ",
        height: 550,
        width: 900,
        modal: true,
        scroll: true,
        layout: {
            type: "fit",
            align: "stretch",
            pack: "start",
        },
        floating: true,
        shadow: true,
        shadowOffset: 20,
        items: [
            {
                xtype: "component",
                html: html,
            },
        ],
    }).show();
}

function descargarArchivo(idDoc)
  {
    window.location = "/soporte/gestion_documentos/"+idDoc+"/descargarDocumento";
    postGuardaHistorial("VISUALIZAR/IMPRIMIR", idDoc);
  }
