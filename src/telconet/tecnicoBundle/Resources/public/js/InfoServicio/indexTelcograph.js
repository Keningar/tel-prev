function reenviarCredencialesTg(data){
    Ext.Msg.alert('Mensaje','Está seguro que desea reenviar las credenciales de acceso del portal de monitoreo Telcograf?', function(btn){
        if(btn==='ok'){
            Ext.get("grid").mask('Enviando información...');
            Ext.Ajax.request({
                url: urlReenviarCredencialesTelcograf,
                method: 'post',
                timeout: 400000,
                params: {
                    strRucTg: data.strRucTg,
                    strIpServicio: data.ipServicio
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var objData    = Ext.JSON.decode(response.responseText);
                    var strStatus  = objData.strStatus;
                    var strMensaje = objData.strMensaje;
                    if(strStatus === "OK"){
                        store.load();
                        Ext.Msg.alert('Mensaje','Se enviaron las credenciales correctamente!');
                    }
                    else{
                        Ext.Msg.alert('Mensaje ',strMensaje );
                    }
                },
                failure: function()
                {
                    Ext.get("grid").unmask();
                }   
            });
        }
    });
}

function reintentoMonitoreoTg(data){

    Ext.MessageBox.show({
        title      : 'Mensaje',
        msg        : '¿Está seguro que desea reintentar la creación del monitoreo Telcograf?',
        closable   : false,
        multiline  : false,
        icon       : Ext.Msg.QUESTION,
        buttons    : Ext.Msg.YESNO,
        buttonText : {yes: 'Si', no: 'No'},
        fn: function (buttonValue)
        {
            if(buttonValue === 'yes')
            {
                Ext.get("grid").mask('Ejecutando proceso...');
                Ext.Ajax.request({
                    url     : urlReintentoCreacionTelcograf,
                    method  : 'post',
                    timeout : 400000,
                    params: {
                        intIdServicio: data.idServicio
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        var objData    = Ext.JSON.decode(response.responseText);
                        var strStatus  = objData.strStatus;
                        var strMensaje = objData.strMensaje;
                        if(strStatus === "OK") {
                            store.load();
                            Ext.Msg.alert('Mensaje','Se ejecutó el proceso correctamente!');
                        } else {
                            Ext.Msg.alert('Error ',strMensaje );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}

function validarEmail(valor) {
    var emailValido = true;
    var emailRegex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if (!emailRegex.test(valor)){
        emailValido = false;
    }
    return emailValido;
}

function cambiarPassTg(data){

    //Store de contactos del punto.
    var storeContactosPunto = new Ext.data.Store({
        total    : 'total',
        pageSize : 200,
        autoLoad : true,
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    : urlGetTipoContactoPunto,
            reader : {
                type          : 'json',
                totalProperty : 'total',
                root          : 'result'
            },
            extraParams : {
                'idPersonaEmpresaRol' : data.idPersonaEmpresaRol
            }
        },
        fields :
        [
            {name : 'contacto'     , mapping : 'contacto'},
            {name : 'tipoContacto' , mapping : 'tipoContacto'}
        ]
    });

    //Grid de contactos del punto.
    var gridContactosPunto = Ext.create('Ext.grid.Panel', {
        id         : 'gridContactosPunto',
        title      : '',
        width      : 380,
        height     : 270,
        store      : storeContactosPunto,
        loadMask   : true,
        frame      : false,
        selModel   : new Ext.selection.CheckboxModel(),
        columns    :
        [
            {
                header : '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
                xtype  : 'rownumberer',
                width  : 25
            },
            {
                header    : '<b>Tipo Contacto</b>',
                dataIndex : 'tipoContacto',
                width     : 160,
                sortable  : false,
                hideable  : false
            },{
                header    : '<b>Contacto</b>',
                dataIndex : 'contacto',
                width     : 160,
                sortable  : false,
                hideable  : false
            }
        ],
        viewConfig : {
            enableTextSelection : true,
            stripeRows          : true,
            deferEmptyText      : false,
            emptyText           : 'Sin datos para mostrar'
        },
        listeners  : {
            itemdblclick: function( view, record, item, index, eventobj, obj){
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title    :'Copiar texto',
                    msg      : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    closable : false,
                    buttons  : Ext.Msg.OK,
                    icon     : Ext.Msg.INFO
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });
                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target     : view.el,
                    delegate   : '.x-grid-cell',
                    trackMouse : true,
                    renderTo   : Ext.getBody(),
                    listeners: {
                        beforeshow: function (tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        }
    });

    //Panel de contactos del punto.
    var panelContactos = Ext.create('Ext.form.Panel', {
        id            :'panelContactos',
        bodyPadding   : 5,
        height        : true,
        waitMsgTarget : true,
        fieldDefaults :
        {
            labelAlign : 'left',
            labelWidth : 90,
            msgTarget  : 'side'
        },
        items :
        [
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'+
                           '<b style="color:blue";>Correo Electrónico Adicional Para Reenvío (Soporte)</b>',
                hidden   : emailAdicionalTelcograf ? false : true,
                defaults : { width: 370 },
                items    :
                [{ id : 'idEmailAdicional' , xtype : 'textfield' }]
            },
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'+
                           '<b style="color:blue";>Selección de Contactos</b>',
                defaults : { width: 380 },
                items    : [gridContactosPunto]
            }
        ]
    });

    //Ventana
    var winContactosPunto = Ext.widget('window', {
        id          : 'winContactosPunto',
        title       : "Cambio de Contraseña",
        layout      : 'fit',
        resizable   : false,
        modal       : true,
        closable    : false,
        width       : 'auto',
        items       : [panelContactos],
        buttonAlign : 'right',
        buttons     :
        [
            {
                text    : '<label style="color:green;"><i class="fa fa-refresh" aria-hidden="true"></i></label>'+
                          '&nbsp;<b>Procesar</b>',
                handler : function() {

                    var destinatarios  = [];
                    var arraySelection = gridContactosPunto.getSelectionModel().getSelection();

                    var emailAdic = typeof Ext.getCmp('idEmailAdicional').value === 'undefined' ?
                        '' : Ext.getCmp('idEmailAdicional').value;

                    if (!Ext.isEmpty(emailAdic) && !validarEmail(emailAdic)) {
                        Ext.MessageBox.show({
                            closable :  false, multiline : false,
                            title    : 'Alerta', msg : 'Correo electrónico adicional inválido..!!',
                            buttons  :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING
                        });
                        return;
                    }

                    if (!Ext.isEmpty(emailAdic)) {
                        destinatarios.push(emailAdic);
                    }

                    if (arraySelection.length < 1 && Ext.isEmpty(emailAdic)) {
                        Ext.MessageBox.show({
                            closable :  false, multiline : false,
                            title    : 'Alerta', msg : 'Seleccione al menos un Correo Electrónico..!!',
                            buttons  :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING
                        });
                        return;
                    }

                    $.each(arraySelection, function(i, item) {
                        if (destinatarios.indexOf(item.data.contacto) === -1) {
                            destinatarios.push(item.data.contacto);
                        }
                    });

                    Ext.MessageBox.show({
                        title      : 'Mensaje',
                        msg        : '¿Está seguro que desea generar una nueva contraseña de <br/>'+
                                     'acceso al portal de monitoreo Telcograf?',
                        closable   : false,
                        multiline  : false,
                        icon       : Ext.Msg.QUESTION,
                        buttons    : Ext.Msg.YESNO,
                        buttonText : {yes: 'Si', no: 'No'},
                        fn: function (buttonValue)
                        {
                            if(buttonValue === 'yes')
                            {
                                Ext.MessageBox.wait('Proceso Ejecutándose...');
                                Ext.Ajax.request({
                                    timeout : 400000,
                                    url     : urlCambiarPassTelcograf,
                                    method  : 'post',
                                    params  : {
                                       'intIdPersonaRol' : data.idPersonaEmpresaRol,
                                       'strRucTg'        : data.strRucTg,
                                       'strIpServicio'   : data.ipServicio,
                                       'destinatarios'   : destinatarios.toString(),
                                       'idServicio'      : data.idServicio
                                    },
                                    success: function(response) {
                                        var objData = Ext.JSON.decode(response.responseText);
                                        var status  = objData.strStatus === 'OK' ? true : false;
                                        var mensaje = objData.strMensaje;
                                        var titulo  = status ? 'Mensaje' : 'Error';
                                        Ext.MessageBox.show({
                                            closable   : false  , multiline  : false,
                                            msg        : mensaje, title      : titulo,
                                            icon       : status ? Ext.Msg.INFO      : Ext.MessageBox.ERROR,
                                            buttons    : status ? Ext.MessageBox.OK : Ext.MessageBox.CANCEL,
                                            buttonText : status ? {ok: 'Cerrar'}    : {cancel: 'Cerrar.'},
                                            fn: function (buttonValue) {
                                                if (buttonValue === 'ok') {
                                                    store.load();
                                                    winContactosPunto.close();
                                                    winContactosPunto.destroy();
                                                }
                                            }
                                        });
                                    },
                                    failure: function (result) {
                                        winContactosPunto.close();
                                        winContactosPunto.destroy();
                                        Ext.get("grid").unmask();
                                        Ext.MessageBox.show({
                                            title      : 'Alerta',
                                            msg        : result.statusText,
                                            buttons    : Ext.MessageBox.OK,
                                            icon       : Ext.MessageBox.ERROR,
                                            closable   : false,
                                            multiline  : false,
                                            buttonText : {ok: 'Cerrar'}
                                        });
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                text    : '<label style="color:red;"><i class="fa fa-remove" aria-hidden="true"></i></label>'+
                          '&nbsp;<b>Cerrar</b>',
                handler : function() {
                    winContactosPunto.close();
                    winContactosPunto.destroy();
                }
            }
        ]
    }).show();
}

function cambiarUsuarioTg(data){

    //Store de contactos del punto.
    var storeContactosPunto = new Ext.data.Store({
        total    : 'total',
        pageSize : 200,
        autoLoad : true,
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    : urlGetTipoContactoPunto,
            reader : {
                type          : 'json',
                totalProperty : 'total',
                root          : 'result'
            },
            extraParams : {
                'idPersonaEmpresaRol' : data.idPersonaEmpresaRol
            }
        },
        fields :
        [
            {name : 'contacto'     , mapping : 'contacto'},
            {name : 'tipoContacto' , mapping : 'tipoContacto'},
            {name : 'usado'        , mapping : 'usado'}
        ]
    });

    var objData = null;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading....');
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
        method : 'POST',
        url    : urlGetContactoActualTg,
        async  : false,
        params : {
            'idPersonaEmpresaRol' : data.idPersonaEmpresaRol
        },
        success: function(response) {
            objData = Ext.JSON.decode(response.responseText);
        },
        failure: function(result) {
            Ext.MessageBox.show({
                title      : 'Error',
                msg        : result.statusText + '.<br/>Si el problema persiste comunique a Sistemas.!!',
                buttons    : Ext.MessageBox.OK,
                icon       : Ext.MessageBox.ERROR,
                closable   : false,
                multiline  : false,
                buttonText : {ok : 'Cerrar'}
            });
        }
    });

    var gridContactosPuntoNU = Ext.create('Ext.grid.Panel', {
        id         : 'gridContactosPunto',
        title      : '',
        width      : 450,
        height     : 270,
        store      : storeContactosPunto,
        loadMask   : true,
        selModel   : new Ext.selection.CheckboxModel(),
        frame      : false,
        columns    :
        [
            {
                header : '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
                xtype  : 'rownumberer',
                width  : 25
            },
            {
                header    : '<b>Tipo Contacto</b>',
                dataIndex : 'tipoContacto',
                width     : 160,
                sortable  : false,
                hideable  : false
            },
            {
                header    : '<b>Contacto</b>',
                dataIndex : 'contacto',
                width     : 160,
                sortable  : false,
                hideable  : false
            },
            {
                header    : '<b>Ocupado</b>',
                dataIndex : 'usado',
                width     : 70,
                sortable  : false,
                hideable  : false
            }
        ],
        viewConfig : {
            enableTextSelection : true,
            stripeRows          : true,
            deferEmptyText      : false,
            emptyText           : 'Sin datos para mostrar'
        },
        listeners  : {
            itemdblclick: function( view, record, item, index, eventobj, obj){
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title    :'Copiar texto',
                    msg      : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    closable : false,
                    buttons  : Ext.Msg.OK,
                    icon     : Ext.Msg.INFO
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });
                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target     : view.el,
                    delegate   : '.x-grid-cell',
                    trackMouse : true,
                    renderTo   : Ext.getBody(),
                    listeners: {
                        beforeshow: function (tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        }
    });

    //Panel de contactos del punto.
    var panelContactos = Ext.create('Ext.form.Panel', {
        id            :'panelContactos',
        bodyPadding   : 5,
        height        : true,
        waitMsgTarget : true,
        fieldDefaults :
        {
            labelAlign : 'left',
            labelWidth : 150,
            msgTarget  : 'side'
        },
        items :
        [
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'+
                           '<b style="color:blue";>Correo Electrónico Actual del Portal</b>',
                hidden   : emailAdicionalTelcograf ? false : true,
                defaults : { width: 450 },
                items    :
                [{ id      : 'idEmailActual' ,
                   xtype   : 'textfield',
                   readOnly: true,
                   value   : objData.contacto }]
            },
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'+
                           '<b style="color:blue";>Seleccione un Contacto</b>',
                defaults : { width: 450 },
                items    : [gridContactosPuntoNU]
            }
        ]
    });

    //Ventana
    var winContactosPunto = Ext.widget('window', {
        id          : 'winContactosPunto',
        title       : "Cambio de usuario",
        layout      : 'fit',
        resizable   : false,
        modal       : true,
        closable    : false,
        width       : 'auto',
        items       : [panelContactos],
        buttonAlign : 'right',
        buttons     :
        [
            {
                text    : '<label style="color:green;"><i class="fa fa-refresh" aria-hidden="true"></i></label>'+
                          '&nbsp;<b>Procesar</b>',
                handler : function() {

                    var destinatarios  = [];
                    var arraySelection = gridContactosPuntoNU.getSelectionModel().getSelection();

                    var emailActual = typeof Ext.getCmp('idEmailActual').value === 'undefined' ?
                        '' : Ext.getCmp('idEmailActual').value;

                    if (arraySelection.length != 1) {
                        Ext.MessageBox.show({
                            closable :  false, multiline : false,
                            title    : 'Alerta', msg : 'Seleccione al menos un Correo Electrónico..!!',
                            buttons  :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING
                        });
                        return;
                    }

                    $.each(arraySelection, function(i, item) {
                        if (destinatarios.indexOf(item.data.contacto) === -1) {
                            destinatarios.push(item.data.contacto);
                        }
                    });

                    Ext.MessageBox.show({
                        title      : 'Mensaje',
                        msg        : '¿Está seguro que desea cambiar el usuario al <br/>'+
                                     'acceso del portal de monitoreo Telcograf?',
                        closable   : false,
                        multiline  : false,
                        icon       : Ext.Msg.QUESTION,
                        buttons    : Ext.Msg.YESNO,
                        buttonText : {yes: 'Si', no: 'No'},
                        fn: function (buttonValue)
                        {
                            if(buttonValue === 'yes')
                            {
                                Ext.MessageBox.wait('Proceso Ejecutándose...');
                                Ext.Ajax.request({
                                    timeout : 400000,
                                    url     : urlCambiarUserTelcograf,
                                    method  : 'post',
                                    params  : {
                                       'intIdPersonaRol' : data.idPersonaEmpresaRol,
                                       'strRucTg'        : data.strRucTg,
                                       'strIpServicio'   : data.ipServicio,
                                       'destinatarios'   : destinatarios,
                                       'idServicio'      : data.idServicio,
                                       'emailActual'     : emailActual
                                    },
                                    success: function(response) {
                                        var objData = Ext.JSON.decode(response.responseText);
                                        var status  = objData.strStatus === 'OK' ? true : false;
                                        var mensaje = objData.strMensaje;
                                        var titulo  = status ? 'Mensaje' : 'Error';
                                        Ext.MessageBox.show({
                                            closable   : false  , multiline  : false,
                                            msg        : mensaje, title      : titulo,
                                            icon       : status ? Ext.Msg.INFO      : Ext.MessageBox.ERROR,
                                            buttons    : status ? Ext.MessageBox.OK : Ext.MessageBox.CANCEL,
                                            buttonText : status ? {ok: 'Cerrar'}    : {cancel: 'Cerrar.'},
                                            fn: function (buttonValue) {
                                                if (buttonValue === 'ok') {
                                                    store.load();
                                                    winContactosPunto.close();
                                                    winContactosPunto.destroy();
                                                }
                                            }
                                        });
                                    },
                                    failure: function (result) {
                                        winContactosPunto.close();
                                        winContactosPunto.destroy();
                                        Ext.get("grid").unmask();
                                        Ext.MessageBox.show({
                                            title      : 'Alerta',
                                            msg        : result.statusText,
                                            buttons    : Ext.MessageBox.OK,
                                            icon       : Ext.MessageBox.ERROR,
                                            closable   : false,
                                            multiline  : false,
                                            buttonText : {ok: 'Cerrar'}
                                        });
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                text    : '<label style="color:red;"><i class="fa fa-remove" aria-hidden="true"></i></label>'+
                          '&nbsp;<b>Cerrar</b>',
                handler : function() {
                    winContactosPunto.close();
                    winContactosPunto.destroy();
                }
            }
        ]
    }).show();
}

function verInfoTelcograf(data){

    var objData = null;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading....');
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
        method : 'POST',
        url    : urlInformacionTelcograf,
        async  : false,
        params : {
            'idPersonaEmpresaRol' : data.idPersonaEmpresaRol
        },
        success: function(response) {
            objData = Ext.JSON.decode(response.responseText);
        },
        failure: function(result) {
            Ext.MessageBox.show({
                title      : 'Error',
                msg        : result.statusText + '.<br/>Si el problema persiste comunique a Sistemas.!!',
                buttons    : Ext.MessageBox.OK,
                icon       : Ext.MessageBox.ERROR,
                closable   : false,
                multiline  : false,
                buttonText : {ok : 'Cerrar'}
            });
        }
    });

    if (!objData.status) {
        Ext.MessageBox.show({
            title      : 'Error',
            msg        : objData.message,
            buttons    : Ext.MessageBox.OK,
            icon       : Ext.MessageBox.ERROR,
            closable   : false,
            multiline  : false,
            buttonText : {ok : 'Cerrar'}
        });
        return;
    }

    var formPanelInfoTg = Ext.create('Ext.form.Panel', {
        id            : 'formPanelInfoTg',
        bodyPadding   : 5,
        height        : true,
        waitMsgTarget : true,
        fieldDefaults :
        {
            labelAlign : 'left',
            labelWidth : 120,
            msgTarget  : 'side'
        },
        items:
        [
            {
                xtype    : 'fieldset',
                title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue">Datos</b>',
                defaults : { width: 450 },
                items :
                [
                    {
                        xtype      : 'textfield',
                        id         : 'urlPortal',
                        fieldLabel : '<b>Url Portal</b>',
                        value      : !Ext.isEmpty(objData.urlPortal) ? objData.urlPortal : 'Sin datos',
                        readOnly   : true
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'usrPortal',
                        fieldLabel : '<b>User Portal</b>',
                        value      : !Ext.isEmpty(objData.usrPortal) ? objData.usrPortal : 'Sin datos',
                        readOnly   : true
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'ipServidorZabbix',
                        fieldLabel : '<b>Ip Servidor Zabbix</b>',
                        value      : !Ext.isEmpty(objData.ipServidorZabbix) ? objData.ipServidorZabbix : 'Sin datos',
                        readOnly   : true
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'organizacion',
                        fieldLabel : '<b>Organización</b>',
                        value      : !Ext.isEmpty(objData.organizacion) ? objData.organizacion : 'Sin datos',
                        readOnly   : true
                    }
                ]
            }
        ]
    });

    var winInfoTg = Ext.create('Ext.window.Window', {
        id          : 'winInfoTg',
        title       : 'Información Técnica Telcograf',
        layout      : 'fit',
        resizable   :  false,
        modal       :  true,
        closable    :  false,
        width       : 'auto',
        items       : [formPanelInfoTg],
        buttonAlign : 'right',
        buttons     :
        [
            {
                text    : '<label style="color:red;"><i class="fa fa-remove" aria-hidden="true"></i></label>'+
                          '&nbsp;<b>Cerrar</b>',
                handler : function() {
                    winInfoTg.close();
                    winInfoTg.destroy();
                }
            }
        ]
    }).show();
}

function crearMonitoreoTg(data) {

    Ext.MessageBox.show({
        title      : 'Mensaje',
        msg        : '¿Está seguro que desea crear el monitoreo Telcograf?',
        closable   : false,
        multiline  : false,
        icon       : Ext.Msg.QUESTION,
        buttons    : Ext.Msg.YESNO,
        buttonText : {yes: 'Si', no: 'No'},
        fn: function (buttonValue)
        {
            if(buttonValue === 'yes')
            {
                Ext.MessageBox.wait('Proceso Ejecutándose...');

                Ext.Ajax.request({
                    method : 'post',
                    timeout: 400000,
                    url    : urlCrearMonitoreoTelcograf,
                    params: {
                        'intIdServicio'          : data.idServicio,
                        'strValidarFacturacion'  : 'NO'
                    },
                    success: function(response) {
                        var objData = Ext.JSON.decode(response.responseText);
                        var status  = objData.strStatus === 'OK' ? true : false;
                        var mensaje = objData.strMensaje;
                        var titulo  = status ? 'Mensaje' : 'Error';
                        Ext.MessageBox.show({
                            closable   : false  , multiline  : false,
                            msg        : mensaje, title      : titulo,
                            icon       : status ? Ext.Msg.INFO      : Ext.MessageBox.ERROR,
                            buttons    : status ? Ext.MessageBox.OK : Ext.MessageBox.CANCEL,
                            buttonText : status ? {ok: 'Cerrar'}    : {cancel: 'Cerrar.'},
                            fn: function (buttonValue) {
                                if (buttonValue === 'ok' || buttonValue === 'cancel') {
                                    store.load();
                                }
                            }
                        });
                    },
                    failure: function (result) {
                        Ext.MessageBox.show({
                            title      : 'Alerta',
                            msg        : result.statusText,
                            buttons    : Ext.MessageBox.OK,
                            icon       : Ext.MessageBox.ERROR,
                            closable   : false,
                            multiline  : false,
                            buttonText : {ok: 'Cerrar'}
                        });
                    }
                });
            }
        }
    });
}

function restablecerDatosLdapTg(data) {

    Ext.MessageBox.show({
        title      : 'Mensaje',
        msg        : '¿Está seguro de restablecer los datos del Ldap para el monitoreo Telcograf?',
        closable   :  false,
        multiline  :  false,
        icon       :  Ext.Msg.QUESTION,
        buttons    :  Ext.Msg.YESNO,
        buttonText :  {yes: 'Si', no: 'No'},
        fn         :  function (buttonValue)
        {
            if(buttonValue === 'yes')
            {
                Ext.MessageBox.wait('Proceso Ejecutándose...');
                Ext.Ajax.request({
                    method : 'post',
                    timeout: 400000,
                    url    : urlRestablecerDatosLdapTg,
                    params: {
                        'intIdPersonaRol' : data.idPersonaEmpresaRol
                    },
                    success: function(response) {
                        var objData = Ext.JSON.decode(response.responseText);
                        var status  = objData.strStatus === 'OK' ? true : false;
                        var mensaje = objData.strMensaje;
                        var titulo  = status ? 'Mensaje' : 'Error';
                        Ext.MessageBox.show({
                            closable   : false  , multiline  : false,
                            msg        : mensaje, title      : titulo,
                            icon       : status ? Ext.Msg.INFO      : Ext.MessageBox.ERROR,
                            buttons    : status ? Ext.MessageBox.OK : Ext.MessageBox.CANCEL,
                            buttonText : status ? {ok: 'Cerrar'}    : {cancel: 'Cerrar.'},
                            fn: function (buttonValue) {
                                if (buttonValue === 'ok' || buttonValue === 'cancel') {
                                    store.load();
                                }
                            }
                        });
                    },
                    failure: function (result) {
                        Ext.MessageBox.show({
                            title      : 'Alerta',
                            msg        :  result.statusText,
                            buttons    :  Ext.MessageBox.OK,
                            icon       :  Ext.MessageBox.ERROR,
                            closable   :  false,
                            multiline  :  false,
                            buttonText :  {ok: 'Cerrar'}
                        });
                    }
                });
            }
        }
    });
}