
/* global Ext */

Ext.onReady(function()
{
    var storePlantilla = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    url: urlGridAdendum,
                    extraParams:
                    {
                        contratoId: idContrato
                    },
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idAdendum'      , mapping: 'idAdendum'}, 
                    {name: 'noAdendum'      , mapping: 'noAdendum'},
                    {name: 'tipo'           , mapping: 'tipo'},
                    {name: 'login'          , mapping: 'login'},
                    {name: 'cliente'        , mapping: 'cliente'},
                    {name: 'feFinAdendum'   , mapping: 'feFinAdendum'},
                    {name: 'linkArchivo'    , mapping: 'linkArchivo'},
                    {name: 'estado'         , mapping: 'estado'},
                    {name: 'action1'        , mapping: 'action1'},
                    {name: 'action2'        , mapping: 'action2'},
                    {name: 'action3'        , mapping: 'action3'}
                ],
            autoLoad: true
        });

     Ext.create('Ext.grid.Panel',
        {
            width: 920,
            height: 300,
            store: storePlantilla,
            viewConfig:
                {
                    enableTextSelection: true,
                    trackOver: true,
                    stripeRows: true,
                    loadMask: true
                },
            columns:
                [
                    {
                        header: 'No. adendum',
                        dataIndex: 'noAdendum',
                        width: 150,
                        sortable: true
                    },
                    {
                        header: 'Tipo',
                        dataIndex: 'tipo',
                        width: 60,
                        sortable: true
                    },
                    {
                        header: 'Login',
                        dataIndex: 'login',
                        width: 120,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Cliente',
                        dataIndex: 'cliente',
                        width: 240,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Fecha fin adendum',
                        dataIndex: 'feFinAdendum',
                        width: 110,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        text: 'Acciones',
                        width: 140,
                        renderer: renderAcciones,
                    }
                ],
            title: 'Adendum de puntos adicionales',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storePlantilla,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'grid'
        });

        function renderAcciones(value, p, record) {
            var iconos='';
            if(record.data.tipo == 'FISICO'){
                iconos=iconos+'<b><a href="#" onClick="verDocumentosDigitales(\''+idContrato+'\',\''+record.data.login +'\',\''+prefijoEmpresa+'\')" title="Ver" class="button-grid-show"></a></b>';
                iconos=iconos+'<b><a href="'+record.data.linkArchivo+'" onClick="" title="Agregar Archivos Digitales" class="button-grid-agregarArchivo"></a></b>';
                iconos=iconos+'<b><a href="#" onClick="reenviarDocumento(\''+record.data.noAdendum+'\',\''+noContrato+'\')" title="Reenviar documento" class="button-grid-agregarCorreo"></a></b>';
                if(record.data.estado == 'Pendiente') {
                    iconos=iconos+'<b><a href="#" onClick="notificarAutorizar(\''+noContrato+'\',\''+record.data.noAdendum+'\')" title="Notificar convertir a OT" class="button-grid-habilitar"></a></b>';
                }
            }

            return Ext.String.format(
                            iconos,
                            value,
                            '1',
                            'nada'
            );
        }

      Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
                {
                    type: 'table',
                    columns: 3,
                    align: 'center'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: true,
            width: 920,
            title: 'Criterios de busqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        {
                            buscar();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        {
                            limpiar();
                        }
                    }
                ],
            items:
                [
                    {
                        xtype: 'textfield',
                        id: 'txtDescripcion',
                        fieldLabel: 'No. de adendum:',
                        value: '',
                        width: 360
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'estado',
                        value: 'Todos',
                        store: [
                            ['', '-- Todos los Estados --'],
                            ['Activo', 'Activo'],
                            ['Pendiente', 'Pendiente'],
                            ['PorAutorizar', 'PorAutorizar']
                        ],
                        width: 360
                    }
                ],
            renderTo: 'filtro'
        });


    function buscar()
    {
        cargarFiltrosBusquedaAlStore();
        storePlantilla.load();
    }


    function limpiar()
    {
        Ext.getCmp('txtDescripcion').value = "";
        Ext.getCmp('txtDescripcion').setRawValue("");
        Ext.getCmp('estado').value = "Todos";
        Ext.getCmp('estado').setRawValue("-- Todos los Estados --");


        storePlantilla.loadData([], false);
        cargarFiltrosBusquedaAlStore();
        storePlantilla.currentPage = 1;
        storePlantilla.load();
    }


    function cargarFiltrosBusquedaAlStore()
    {
        storePlantilla.getProxy().extraParams.descripcion = Ext.getCmp('txtDescripcion').value;
        storePlantilla.getProxy().extraParams.estado = Ext.getCmp('estado').value;
    }
});

function reenviarDocumento(nAdendum,nContrato)
{      
  
    var connReenviarCorreo = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {						
                    Ext.MessageBox.show({
                        msg: 'Reenviando los documentos al cliente, Por favor espere!!',
                        progressText: 'Reenviando correo...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });				
                },
                scope: this
            }
        }
    });
    storeFormasContacto = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlGetCorreosCliente,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: ''
            },
            extraParams:
            {
                numeroContrato: nContrato,
                numeroAdendum: nAdendum
            },
        },
        fields:
		[
            {name:'valor', mapping:'valor'}
		]
    });
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 2
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'combobox',
                        defaults:
                            {
                                width: 250
                            },
                        items:
                            [
                                {
                                    xtype: 'combobox',
                                    id: 'comboCorreoEmpl',
                                    name: 'comboCorreoEmpl',
                                    fieldLabel: 'Seleccione correo:',                
                                    store: storeFormasContacto,
                                    displayField: 'valor',
                                    valueField: 'valor',
                                    queryMode: 'remote',
                                    emptyText: 'Seleccione',
                                    forceSelection: true
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Reenviar',
                        formBind: true,
                        handler: function ()
                        { var valor = Ext.getCmp('comboCorreoEmpl').value;
                            if (valor == "")
                            {
                                Ext.Msg.alert("Alerta", "Seleccione un Correo ");
                            }else{
                                connReenviarCorreo.request
                                 ({
                                    url: urlReenviarDocumentoContrato,
                                    method: 'post',
                                    waitMsg: 'Esperando Respuesta',
                                    params:
                                        {
                                           numeroContrato: nContrato,
                                           numeroAdendum: nAdendum,
                                           correoCliente: valor,
                                           tipoContrato: 'AP'
                                        },
                                    success: function(response)
                                    {
                                        var respuesta = Ext.JSON.decode(response.responseText);

                                        if (respuesta.strStatus == "OK")
                                        {
                                            Ext.Msg.alert('MENSAJE ', 'Se envió el  Adendum correctamente.');
                                        }
                                        else
                                        {                                            
                                            Ext.Msg.alert('Error', respuesta.strMensaje);
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                                winReenviarDoc.destroy();
                            }
                        }                          
                    },
                    {
                        text: 'Cancelar',
                        handler: function ()
                        {
                            winReenviarDoc.destroy();
                        }
                    }
                ]
        });

    var winReenviarDoc = Ext.create('Ext.window.Window',
        {
            title: 'Reenvio de documento',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}



function notificarAutorizar(numeroContrato, numeroAdendum) {
    Ext.Ajax.request({
        url: urlNotificarAutorizar,
        method: 'post',
        params: {
            numeroContrato: numeroContrato,
            numeroAdendum: numeroAdendum
        },
        success: function(response)
        {
            var respuesta = Ext.JSON.decode(response.responseText);
            if (respuesta.strStatus == "OK")
            {
                mostrarCorreosAutorizar(numeroContrato,numeroAdendum);
            }
            else
            {
                Ext.Msg.alert('Error', respuesta.message);
            }
        },  failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}


function mostrarCorreosAutorizar(numeroContrato,numeroAdendum){
    var conn = new Ext.data.Connection({
        listeners: {
        'beforerequest': {
            fn: function (con, opt) {
            Ext.get(document.body).mask('Consultando Correo...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function (con, res, opt) {
            Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function (con, res, opt) {
            Ext.get(document.body).unmask();
            },
            scope: this
        }
        }
    });

    conn.request({
        url: urlCorreoAutorizar,
        method: 'post',
        params: {
            idContrato: idContrato,
            idAdendum: numeroAdendum
        },
        success: function(response)
        {
            var respuesta = Ext.JSON.decode(response.responseText);
            if (respuesta.strStatus == "OK")
            {
                llenarCorreosAutorizar(respuesta.arrayCorreo, numeroContrato,numeroAdendum);
            } else {
                Ext.Msg.alert('Error', respuesta.strMensaje);
            }
        },  failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        } 
    });
}


function llenarCorreosAutorizar(data, numeroContrato,numeroAdendum){
    var connAutorizar = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {						
                    Ext.MessageBox.show({
                        msg: 'Enviando correo al departamento de Ade para su posterior aprobación, Por favor espere!!',
                        progressText: 'Enviando...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });				
                },
                scope: this
            }
        }
    });

    var storeCorreosAutorizar = Ext.create('Ext.data.Store', {
        fields: ['valor'],
        data: data
    });
    var formCorreoContrato = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        width: 350,
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },
        defaultType: 'textfield',
        items: [
            {
                xtype: 'combobox',
                fieldLabel: 'Correo:',
                name: 'correoContrato',
                id: 'correoContrato',
                store: storeCorreosAutorizar,
                displayField: 'valor',
                valueField: 'valor',
                queryMode: 'local',
                emptyText: 'Seleccione',
                forceSelection: true
            }
        ],
        buttons: [
            {
                text: 'Autorizar',
                formBind: true,
                handler: function()
                {
                    var valor = Ext.getCmp('correoContrato').value;
                    if (valor == "")
                    {
                        Ext.Msg.alert("Alerta", "Favor ingrese el correo del cliente!");
                    }
                    else
                    {
                        connAutorizar.request
                        ({
                            url: urlAutorizarContratoFisico,
                            method: 'post',
                            waitMsg: 'Esperando Respuesta',
                            timeout: 400000,
                            params:
                                {
                                    numeroContrato: numeroContrato,
                                    numeroAdendum: numeroAdendum,
                                    correo: valor
                                },
                            success: function(response)
                            {
                                var respuesta = Ext.JSON.decode(response.responseText);
                                if (respuesta.strStatus == "OK")
                                {
                                    Ext.Msg.alert('MENSAJE ', 'Se envió el correo al departamento de autorización de contrato.');
                                   // store.load({params: {start: 0, limit: 10}});
                                }
                                else
                                {
                                    Ext.Msg.alert('Error', respuesta.message, function(btn) {
                                        if (btn == 'ok') {
                                            mostrarCorreosAutorizar(numeroContrato,numeroAdendum);
                                        }
                                    });
                                }
                                winVisualizarCorreo.destroy();
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                winVisualizarCorreo.destroy();
                            }
                        });
                    }
                }
            }]
    });

    var winVisualizarCorreo = Ext.create('Ext.window.Window',
         {
             title: 'Seleccionar el correo del departamento de contratos',
             modal: true,
             width: 320,
             closable: true,
             layout: 'fit',
             items: [formCorreoContrato]
         }).show();
}

function verDocumentosDigitales(contrato,login,empresaPrefijo = null) {
    var store = new Ext.data.Store({
        id: "verArchivosDigitalesStore",
        total: "total",
        pageSize: empresaPrefijo === "MD" ? Number.MAX_SAFE_INTEGER : 10,
        autoLoad: empresaPrefijo !== "MD",
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
                        cbxPuntosContrato.setValue(login);
                        cbxPuntosContrato.enable();
                        storeGrid.clearFilter(false);
                        storeGrid.filter(function (record) {
                            return (
                                !Ext.isEmpty(record.get("login")) &&
                                cbxPuntosContrato.getValue().trim() ===
                                record.get("login").trim()
                            );
                        });
                    } else {
                        cbxPuntosContrato.setValue(login);
                        storeGrid.clearFilter(false);
                        cbxPuntosContrato.enable();
                    }
                }
                cbxPuntosContrato.setDisabled(true);
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
                    empresaPrefijo === "MD"
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
                            hideMode :'visibility',
                            queryMode: "local",
                            emptyText: "Cargando...",
                            disabled: true,
                            anyMatch: true,
                            allowBlank: true,
                            editable: false,
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
