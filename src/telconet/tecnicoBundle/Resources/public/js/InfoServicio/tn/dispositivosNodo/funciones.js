function seleccionarDispositivo(data,accion) {
    var id = 'agregar-dispositivo-'+accion;
    var contentHtmlAD = Ext.create('Ext.Component', {
        html:'<div id="'+id+'" align="center" '+
             'title="Seleccionar Dispositivo" style="cursor:pointer;">&nbsp;&nbsp'+
             '<label style="color:#3a87ad;">'+
                '<i class="fa fa-plus-square fa-lg" aria-hidden="true" onclick="agregarDispositivos('+data+','+accion+');"></i>'+
             '</label></div>'
    });
    return contentHtmlAD;
}

function comboEmpleado(data) {

    var empleadoActual = null;

    var storeEmpleado = new Ext.data.Store ({
        autoLoad : false,
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    :  url_empleados,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'json'
            }
        },
        fields: [
            {name: 'id_empleado'    , mapping: 'id_empleado'},
            {name: 'nombre_empleado', mapping: 'nombre_empleado'}
        ]
    });

    var comboFilterTecnico = new Ext.form.ComboBox({
        id           : 'comboFilterTecnico',
        queryMode    : 'remote',
        fieldLabel   : 'Técnico Encargado',
        store        :  storeEmpleado,
        valueField   : 'id_empleado',
        displayField : 'nombre_empleado',
        labelWidth   :  120,
        width        :  400,
        hidden       :  true,
        listeners: {
            select: function (combo) {
                if (empleadoActual !== null && empleadoActual !== combo.getValue()) {
                    limpiarDispositivos();
                }
                empleadoActual = combo.getValue();
            }
        }
    });

    //Precargamos el técnico encargado.
    var objTecnico = null;
    if (!Ext.isEmpty(data.strJsonTecnico)) {
        objTecnico = Ext.JSON.decode(data.strJsonTecnico);
        if (!Ext.isEmpty(objTecnico) && objTecnico !== null) {
            empleadoActual = objTecnico.id_empleado;
            storeEmpleado.add({"id_empleado"     : objTecnico.id_empleado,
                               "nombre_empleado" : objTecnico.nombre_empleado});
            Ext.getCmp('comboFilterTecnico').setRawValue(objTecnico.nombre_empleado);
            Ext.getCmp('comboFilterTecnico').setValue(objTecnico.id_empleado);
        }
    }

    return comboFilterTecnico;
}

function limpiarDispositivos() {

    //CPE
    if (typeof Ext.getCmp('serieNuevoCpe') !== 'undefined') {
        Ext.getCmp('descripcionNuevoCpe').setValue = '';
        Ext.getCmp('descripcionNuevoCpe').setRawValue('');
        Ext.getCmp('serieNuevoCpe').setValue = '';
        Ext.getCmp('serieNuevoCpe').setRawValue('');
        Ext.getCmp('modeloNuevoCpe').setValue = '';
        Ext.getCmp('modeloNuevoCpe').setRawValue('');
        Ext.getCmp('macNuevoCpe').setValue = '';
        Ext.getCmp('macNuevoCpe').setRawValue('');
    }

    //TRANSCEIVER
    if (typeof Ext.getCmp('serieNuevoTransciever') !== 'undefined') {
        Ext.getCmp('serieNuevoTransciever').setValue = '';
        Ext.getCmp('serieNuevoTransciever').setRawValue('');
        Ext.getCmp('modeloNuevoTransciever').setValue = '';
        Ext.getCmp('modeloNuevoTransciever').setRawValue('');
        Ext.getCmp('descripcionNuevoTransciever').setValue = '';
        Ext.getCmp('descripcionNuevoTransciever').setRawValue('');
    }

    //RADIO
    if (typeof Ext.getCmp('serieNuevoRadio') !== 'undefined') {
        Ext.getCmp('descripcionNuevoRadio').setValue = '';
        Ext.getCmp('descripcionNuevoRadio').setRawValue('');
        Ext.getCmp('serieNuevoRadio').setValue = '';
        Ext.getCmp('serieNuevoRadio').setRawValue('');
        Ext.getCmp('modeloNuevoRadio').setValue = '';
        Ext.getCmp('modeloNuevoRadio').setRawValue('');
        Ext.getCmp('macNuevoRadio').setValue = '';
        Ext.getCmp('macNuevoRadio').setRawValue('');
    }

    //NODO
    if (typeof Ext.getCmp('gridDispositivosNodo') !== 'undefined') {
        Ext.getCmp("gridDispositivosNodo").getStore().removeAll();
    }
}

function dispositivosNodo(data) {

    //Precargamos el json con los dispositivos que seran instalados en el nodo.
    var arrayDispositivosNodo = [];
    if (!Ext.isEmpty(data.strJsonDipositivosNodo)) {
        arrayDispositivosNodo = Ext.JSON.decode(data.strJsonDipositivosNodo);
        if (Ext.isEmpty(arrayDispositivosNodo)) {
            arrayDispositivosNodo = [];
        }
    }

    var storeDispositivosNodo = new Ext.data.Store ({
        autoDestroy: true,
        data       : arrayDispositivosNodo,
        proxy      : {type: 'memory'},
        fields     : [
            'idPersonaRol',
            'idControl',
            'serieElemento',
            'modeloElemento',
            'tipoElemento',
            'descripcionElemento',
            'macElemento'
        ]
    });

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosNodo");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnRemover").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosNodo");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnRemover").setDisabled(true);
                }
            }
        }
    });

    var gridDispositivosNodo = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosNodo',
        width    :  650,
        height   :  114,
        store    :  storeDispositivosNodo,
        selModel :  smSeleccion,
        loadMask :  true,
        frame    :  false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var value    = record.data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        dockedItems: [{
            xtype : 'toolbar',
            dock  : 'top',
            align : '<-',
            items : [{
                id  : 'btnAgregar',
                text: '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Agregar Dipositivo',
                scope: this,
                handler: function() {
                    //3: Nodo
                    agregarDispositivos(data,3);
                }
            },
            {
                id  : 'btnRemover',
                text: '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Remover Dipositivo',
                scope: this,
                disabled: true,
                handler: function() {
                    var arraySelection = Ext.getCmp("gridDispositivosNodo").getSelectionModel().getSelection();
                    $.each(arraySelection, function(i, item) {
                        var index = storeDispositivosNodo.findBy(function (record) {
                            return record.data.serieElemento === item.data.serieElemento;
                        });
                        if (index >= 0) {
                            storeDispositivosNodo.removeAt(index);
                        }
                    });
                }
            }]
        }],
        columns:
        [
            {
                dataIndex : 'idControl',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                dataIndex : 'idPersonaRol',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'Serie',
                dataIndex : 'serieElemento',
                width     :  150,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Modelo',
                dataIndex : 'modeloElemento',
                width     :  150,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Descripción',
                dataIndex : 'descripcionElemento',
                width     :  210,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Mac',
                dataIndex : 'macElemento',
                width     :  100,
                sortable  :  false,
                hideable  :  false
            }
        ]
    });

    return gridDispositivosNodo;
}

function agregarDispositivos(data,accion) {

    var stylebutton  = '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>&nbsp;&nbsp;Agregar';
    var idEmpleado   =  Ext.getCmp('comboFilterTecnico').getValue();
    var empleado     =  Ext.getCmp('comboFilterTecnico').getRawValue();
    var titulo       =  null;
    var tipoElemento =  null;
    var strTiposElementos = null;

    if (Ext.isEmpty(idEmpleado)) {
        Ext.Msg.show({
            title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
            buttonText: {cancel: 'Cerrar'}
        });
        return;
    }

    if (accion === 1) {
        titulo       = 'Cpe';
        tipoElemento = 'CPE';
    } else if (accion === 2) {
        titulo       = 'Transceiver';
        tipoElemento = 'TRANSCEIVER';
    } else if (accion === 3) {
        titulo       = 'Nodo';
    } else if (accion === 4) {
        titulo       = 'Radio';
        tipoElemento = 'RADIO';
    } else if (accion === 0 && data != null) {
        titulo            = data.titulo;
        strTiposElementos = JSON.stringify(data.tiposElementos);
    } else {
        titulo       = '';
    }

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode         : titulo === 'Nodo' ? 'MULTI' : 'SINGLE',
        allowDeselect: true,
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosAsignados");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnAgregar").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosAsignados");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnAgregar").setDisabled(true);
                }
            }
        }
    });

    var storeEquiposAsignados = new Ext.data.Store ({
        autoLoad :  true,
        pageSize :  2000,
        total    : 'total',
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    :  url_equipoAsignados,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'result',
                totalProperty: 'total'
            },
            extraParams: {
                'intIdPersona'    : idEmpleado,
                'strTipoElemento' : tipoElemento,
                'strTiposElementos' : strTiposElementos
            }
        },
        fields: [
            {name: 'idPersona'          , mapping: 'idPersona'},
            {name: 'idPersonaRol'       , mapping: 'idPersonaRol'},
            {name: 'idControl'          , mapping: 'idControl'},
            {name: 'serieElemento'      , mapping: 'serieElemento'},
            {name: 'modeloElemento'     , mapping: 'modeloElemento'},
            {name: 'tipoElemento'       , mapping: 'tipoElemento'},
            {name: 'descripcionElemento', mapping: 'descripcionElemento'},
            {name: 'macElemento'        , mapping: 'macElemento'},
        ]
    });

    var filterPanelTecnico = Ext.create('Ext.panel.Panel', {
        buttonAlign : 'center',
        border      :  false,
        width       :  700,
        layout: {
            type    : 'table',
            align   : 'center',
            columns :  5
        },
        bodyStyle: {background: '#fff'},
        defaults : {bodyStyle: 'padding:15px'},
        items:
        [
            {width:'20%',border:false},
            {
                xtype      : 'displayfield',
                fieldLabel : '<b>Técnico</b>',
                value      :  empleado,
                allowBlank :  true,
                readOnly   :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltSerie',
                fieldLabel : '<b>Serie</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltModelo',
                fieldLabel : '<b>Modelo</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltDescripcion',
                fieldLabel : '<b>Descripción</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false}
        ],
        buttons: [
            {
                text   : 'Buscar',
                iconCls: 'icon_search',
                handler: function() {
                    var serie       = Ext.getCmp('fltSerie').getValue();
                    var modelo      = Ext.getCmp('fltModelo').getValue();
                    var descripcion = Ext.getCmp('fltDescripcion').getValue();
                    storeEquiposAsignados.load({params:{
                        'intIdPersona'    : idEmpleado,
                        'strTipoElemento' : tipoElemento,
                        'strTiposElementos' : strTiposElementos,
                        'strNumeroSerie'  : serie,
                        'strModelo'       : modelo,
                        'strDescripcion'  : descripcion
                    }});
                }
            },
            {
                text   : 'Limpiar',
                iconCls: 'icon_limpiar',
                handler:  function(){
                    Ext.getCmp('fltSerie').setValue('');
                    Ext.getCmp('fltModelo').setValue('');
                    Ext.getCmp('fltDescripcion').setValue('');
                    storeEquiposAsignados.load();
                }
            }
        ]
    });

    var gridDispositivosAsignados = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosAsignados',
        width    :  740,
        height   :  250,
        store    :  storeEquiposAsignados,
        selModel :  smSeleccion,
        loadMask :  true,
        frame    :  false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var value    = record.data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeEquiposAsignados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}'
        }),
        columns: [
            {
                header    : 'idControl',
                dataIndex : 'idControl',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'idPersonaRol',
                dataIndex : 'idPersonaRol',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'noArticulo',
                dataIndex : 'noArticulo',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieElemento',
                width     :  155,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Tipo Elemento</b>',
                dataIndex : 'tipoElemento',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  130,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Descripción</b>',
                dataIndex : 'descripcionElemento',
                width     :  255,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Mac</b>',
                dataIndex : 'macElemento',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            }
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        frame: false,
        defaults: {
            bodyStyle : 'padding:15px',
            height    :  400
        },
        items:
        [
            {
                xtype  : 'panel',
                title  : 'Filtro Técnico',
                layout : {
                    pack    : 'center',
                    type    : 'table',
                    columns :  1
                },
                items:[filterPanelTecnico,gridDispositivosAsignados]
            }
        ]
    });

    var btnCancelar = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winAgregarDispositivosNodo.close();
            winAgregarDispositivosNodo.destroy();
        }
    });

    var btnAgregar = Ext.create('Ext.Button', {
        id      : 'idBtnAgregar',
        text    :  stylebutton,
        disabled:  true,
        handler: function() {
            var isTieneGridNodo = false;
            if (Ext.getCmp('gridDispositivosNodo')){
                isTieneGridNodo = true;
                var storeDispositivosNodo = Ext.getCmp("gridDispositivosNodo").getStore();
            }
            var arraySelection        = Ext.getCmp("gridDispositivosAsignados").getSelectionModel().getSelection();
            if (arraySelection.length > 0)
            {
                $.each(arraySelection, function(i, item)
                {
                    var index = null;
                    var serieElemento = item.data.serieElemento;
                    if (Ext.getCmp('gridDispositivosNodo')){
                        index = storeDispositivosNodo.findBy(function (record) {
                            return record.data.serieElemento === serieElemento;
                        });
                    }

                    if (accion !== 3 && index >= 0 && isTieneGridNodo) {
                        Ext.Msg.show({
                            title: 'Alerta',
                            msg  : 'La serie <b>'+serieElemento+'</b> '+
                                   'ya se encuentra seleccionada en <b>Dispositivos en Nodo</b>.',
                            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }
                    if (accion === 0 && data != null) {
                        var indexExist = Ext.getCmp("gridDispositivosValid").getStore().findBy(function (record) {
                            return record.data.serieElemento === serieElemento;
                        });
                        if (indexExist >= 0) {
                            Ext.Msg.show({
                                title: 'Alerta',
                                msg  : 'La serie <b>'+serieElemento+'</b> ya se encuentra seleccionada.',
                                icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                buttonText: {cancel: 'Cerrar'}
                            });
                            return;
                        }
                    }

                    if (accion === 1) {
                        Ext.getCmp('descripcionNuevoCpe').setValue = '';
                        Ext.getCmp('descripcionNuevoCpe').setRawValue('');
                        Ext.getCmp('macNuevoCpe').setValue = '';
                        Ext.getCmp('macNuevoCpe').setRawValue('');
                        Ext.getCmp('modeloNuevoCpe').setValue = '';
                        Ext.getCmp('modeloNuevoCpe').setRawValue('');
                        Ext.getCmp('serieNuevoCpe').setValue = serieElemento;
                        Ext.getCmp('serieNuevoCpe').setRawValue(serieElemento);
                        Ext.getCmp('serieNuevoCpe').focus();
                        Ext.getCmp('serieNuevoCpe').blur();
                    } else if (accion === 2) {
                        Ext.getCmp('descripcionNuevoTransciever').setValue = '';
                        Ext.getCmp('descripcionNuevoTransciever').setRawValue('');
                        Ext.getCmp('modeloNuevoTransciever').setValue = '';
                        Ext.getCmp('modeloNuevoTransciever').setRawValue('');
                        Ext.getCmp('serieNuevoTransciever').setValue = serieElemento;
                        Ext.getCmp('serieNuevoTransciever').setRawValue(serieElemento);
                        Ext.getCmp('serieNuevoTransciever').focus();
                        Ext.getCmp('serieNuevoTransciever').blur();
                    } else if (accion === 4) {
                        Ext.getCmp('descripcionNuevoRadio').setValue = '';
                        Ext.getCmp('descripcionNuevoRadio').setRawValue('');
                        Ext.getCmp('macNuevoRadio').setValue = '';
                        Ext.getCmp('macNuevoRadio').setRawValue('');
                        Ext.getCmp('modeloNuevoRadio').setValue = '';
                        Ext.getCmp('modeloNuevoRadio').setRawValue('');
                        Ext.getCmp('serieNuevoRadio').setValue = serieElemento;
                        Ext.getCmp('serieNuevoRadio').setRawValue(serieElemento);
                        Ext.getCmp('serieNuevoRadio').focus();
                        Ext.getCmp('serieNuevoRadio').blur();
                    } else if (accion === 0 && data != null) {
                        Ext.getCmp(data.keySerie).setValue = serieElemento;
                        Ext.getCmp(data.keySerie).setRawValue(serieElemento);
                        Ext.getCmp(data.keySerie).focus();
                        Ext.getCmp(data.keySerie).blur();
                        Ext.getCmp(data.keyModelo).setValue = item.data.modeloElemento;
                        Ext.getCmp(data.keyModelo).setRawValue(item.data.modeloElemento);
                        Ext.getCmp(data.keyModelo).focus();
                        Ext.getCmp(data.keyModelo).blur();
                        Ext.getCmp(data.keyMac).setValue = item.data.macElemento;
                        Ext.getCmp(data.keyMac).setRawValue(item.data.macElemento);
                        Ext.getCmp(data.keyMac).focus();
                        Ext.getCmp(data.keyMac).blur();
                        Ext.getCmp("gridDispositivosValid").getStore().add({
                            "serieElemento"       : serieElemento,
                            "modeloElemento"      : item.data.modeloElemento});
                    } else {
                        var serieNuevoTransciever = Ext.getCmp('serieNuevoTransciever').getValue();
                        var serieNuevoCpe         = Ext.getCmp('serieNuevoCpe').getValue();

                        if (!Ext.isEmpty(serieElemento) && !Ext.isEmpty(serieNuevoTransciever) &&
                            serieElemento === serieNuevoTransciever) {
                            Ext.Msg.show({
                                title: 'Alerta',
                                msg  : 'La serie <b>'+serieElemento+'</b> '+
                                       'ya se encuentra seleccionada en el <b>Transciever Cliente</b>.',
                                icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                buttonText: {cancel: 'Cerrar'}
                            });
                            return;
                        }

                        if (!Ext.isEmpty(serieElemento) && !Ext.isEmpty(serieNuevoCpe) &&
                            serieElemento === serieNuevoCpe) {
                            Ext.Msg.show({
                                title: 'Alerta',
                                msg  : 'La serie <b>'+serieElemento+'</b> '+
                                       'ya se encuentra seleccionada en el <b>Cpe</b>.',
                                icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                buttonText: {cancel: 'Cerrar'}
                            });
                            return;
                        }

                        if (index < 0) {
                            storeDispositivosNodo.add({
                                "idControl"           : item.data.idControl,
                                "idPersonaRol"        : item.data.idPersonaRol,
                                "serieElemento"       : serieElemento,
                                "modeloElemento"      : item.data.modeloElemento,
                                "descripcionElemento" : item.data.descripcionElemento,
                                "macElemento"         : item.data.macElemento});
                        }
                    }
                });

                winAgregarDispositivosNodo.close();
                winAgregarDispositivosNodo.destroy();
            }
        }
    });

    var winAgregarDispositivosNodo = new Ext.Window ({
        id          : 'winAgregarDispositivosNodo',
        title       : 'Filtro de Dispositivos <b style="color:green;">('+titulo+')</b>',
        layout      : 'fit',
        y           :  35,
        buttonAlign : 'center',
        resizable   :  false,
        modal       :  true,
        closable    :  false,
        items       :  [formPanel],
        buttons     :  [btnAgregar,btnCancelar]
    }).show();
}
