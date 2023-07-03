function comboEmpleadoSafeCity(data) {

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
        hidden       :  false,
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

//Inicio Bloque de funciones para servicos safe city
function getDispositivosNodoSafeCity(data) {

    //Precargamos el json con los dispositivos que seran instalados.
    var arrayDispositivosNodo = [];
    if (!Ext.isEmpty(data.strJsonDipositivosNodo)) {
        arrayDispositivosNodo = Ext.JSON.decode(data.strJsonDipositivosNodo);
        if (Ext.isEmpty(arrayDispositivosNodo)) {
            arrayDispositivosNodo = [];
        }
    }

    var storeDispositivosNodoSC = new Ext.data.Store ({
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
                var grid = Ext.getCmp("gridDispositivosNodoSafeCity");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnRemover").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosNodoSafeCity");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnRemover").setDisabled(true);
                }
            }
        }
    });

    var gridDispositivosNodoSafeCity = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosNodoSafeCity',
        width    :  500,
        height   :  130,
        store    :  storeDispositivosNodoSC,
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
                    //1: Nodo
                    agregarDispositivosOntNodo(data,1);
                }
            },
            {
                id  : 'btnRemover',
                text: '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Remover Dipositivo',
                scope: this,
                disabled: true,
                handler: function() {
                    var arraySelection = Ext.getCmp("gridDispositivosNodoSafeCity").getSelectionModel().getSelection();
                    $.each(arraySelection, function(i, item) {
                        var index = storeDispositivosNodoSC.findBy(function (record) {
                            return record.data.serieElemento === item.data.serieElemento;
                        });
                        if (index >= 0) {
                            storeDispositivosNodoSC.removeAt(index);
                            Ext.Msg.alert('Mensaje ',`Elemento removido con éxito`);

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
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Modelo',
                dataIndex : 'modeloElemento',
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Descripción',
                dataIndex : 'descripcionElemento',
                width     :  180,
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

    return gridDispositivosNodoSafeCity;
}

function getDispositivosClienteSafeCity(data) {

    //Precargamos el json con los dispositivos que seran instalados.
    var arrayDispositivosCliente = [];
    if (!Ext.isEmpty(data.strJsonDipositivosNodo)) {
        arrayDispositivosCliente = Ext.JSON.decode(data.strJsonDipositivosNodo);
        if (Ext.isEmpty(arrayDispositivosCliente)) {
            arrayDispositivosCliente = [];
        }
    }

    var storeDispositivosClienteSC = new Ext.data.Store ({
        autoDestroy: true,
        data       : arrayDispositivosCliente,
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

    var smSeleccionb = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosClienteSafeCity");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnRemoverb").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosClienteSafeCity");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnRemoverb").setDisabled(true);
                }
            }
        }
    });

    var gridDispositivosClienteSafeCity = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosClienteSafeCity',
        width    :  500,
        height   :  130,
        store    :  storeDispositivosClienteSC,
        selModel :  smSeleccionb,
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
                id  : 'btnAgregarb',
                text: '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Agregar Dipositivo',
                scope: this,
                handler: function() {
                    //2: cliente
                    agregarDispositivosOntCliente(data,2);
                }
            },
            {
                id  : 'btnRemoverb',
                text: '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Remover Dipositivo',
                scope: this,
                disabled: true,
                handler: function() {
                    var arraySelection = Ext.getCmp("gridDispositivosClienteSafeCity").getSelectionModel().getSelection();
                    $.each(arraySelection, function(i, item) {
                        var index = storeDispositivosClienteSC.findBy(function (record) {
                            return record.data.serieElemento === item.data.serieElemento;
                        });
                        if (index >= 0) {
                            storeDispositivosClienteSC.removeAt(index);
                            Ext.Msg.alert('Mensaje ',`Elemento removido con éxito`);
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
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Modelo',
                dataIndex : 'modeloElemento',
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Descripción',
                dataIndex : 'descripcionElemento',
                width     :  180,
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

    return gridDispositivosClienteSafeCity;
}

function agregarDispositivosOntNodo(data,accion) {

    var stylebutton  = '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>&nbsp;&nbsp;Agregar';
    var idEmpleado   =  Ext.getCmp('comboFilterTecnico').getValue();
    var empleado     =  Ext.getCmp('comboFilterTecnico').getRawValue();
    var titulo       =  null;

    if (Ext.isEmpty(idEmpleado)) {
        Ext.Msg.show({
            title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
            buttonText: {cancel: 'Cerrar'}
        });
        return;
    }

    if (accion === 1) {
        titulo       = 'Nodo';
    }

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        allowDeselect: true,
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnAgregarSafeCity").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnAgregarSafeCity").setDisabled(true);
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
            url    :  url_equipoAdicionales,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'result',
                totalProperty: 'total'
            },
            extraParams: {
                'intIdPersona'    : idEmpleado,
                'intPerteneceElemento' : 0
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
            {name: 'feAsignacion'       , mapping: 'feAsignacion'},
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
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaDesde',
                fieldLabel: '<b>Desde:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
            },
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaHasta',
                fieldLabel: '<b>Hasta:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
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
                    var strFechaDesde = Ext.getCmp('fechaDesde').getRawValue()  ?  Ext.getCmp('fechaDesde').getRawValue() : "";
                    var strFechaHasta = Ext.getCmp('fechaHasta').getRawValue()  ?  Ext.getCmp('fechaHasta').getRawValue() : "";

                    storeEquiposAsignados.load({params:{
                        'intIdPersona'    : idEmpleado,
                        'strTipoElemento' : null,
                        'strTiposElementos' : null,
                        'strNumeroSerie'  : serie,
                        'strModelo'       : modelo,
                        'strDescripcion'  : descripcion,
                        'strFechaDesde'   : strFechaDesde,
                        'strFechaHasta'   : strFechaHasta
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
                    Ext.getCmp('fechaDesde').setValue('');
                    Ext.getCmp('fechaHasta').setValue('');
                    storeEquiposAsignados.load();
                }
            }
        ]
    });

    var gridDispositivosAsignadosTecnico = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosAsignadosTecnico',
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
            },
            {
                header    : '<b>Fecha Asignacion</b>',
                dataIndex : 'feAsignacion',
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
                items:[filterPanelTecnico,gridDispositivosAsignadosTecnico]
            }
        ]
    });

    var btnCancelarSafeCity = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winAgregarDispositivosNodoSC.close();
            winAgregarDispositivosNodoSC.destroy();
        }
    });

    var btnAgregarSafeCity = Ext.create('Ext.Button', {
        id      : 'idBtnAgregarSafeCity',
        text    :  stylebutton,
        disabled:  true,
        handler: function() {
            if (Ext.getCmp('gridDispositivosNodoSafeCity')){
                var storeDispositivosNodo = Ext.getCmp("gridDispositivosNodoSafeCity").getStore();
            }
            var arraySelection        = Ext.getCmp("gridDispositivosAsignadosTecnico").getSelectionModel().getSelection();
            if (arraySelection.length > 0)
            {
                $.each(arraySelection, function(i, item)
                {
                    var index = null;
                    var serieElemento = item.data.serieElemento;
                    if (Ext.getCmp('gridDispositivosNodoSafeCity')){
                        index = storeDispositivosNodo.findBy(function (record) {
                            return record.data.serieElemento === serieElemento;
                        });
                    }

                    if (accion === 1) {
                        if (index < 0) {
                            console.log("Verdadero " + index);
                            storeDispositivosNodo.add({
                                "idControl"           : item.data.idControl,
                                "idPersonaRol"        : item.data.idPersonaRol,
                                "serieElemento"       : serieElemento,
                                "modeloElemento"      : item.data.modeloElemento,
                                "descripcionElemento" : item.data.descripcionElemento,
                                "macElemento"         : item.data.macElemento});
                                Ext.Msg.alert('Mensaje ',`Elemento  agregado con éxito`);
                            }else{
                                Ext.Msg.alert('Mensaje ',`El elemento con serie ${serieElemento} ya fue agregado`);
                            }
                    }
                });

                winAgregarDispositivosNodoSC.close();
                winAgregarDispositivosNodoSC.destroy();
            }
        }
    });

    var winAgregarDispositivosNodoSC = new Ext.Window ({
        id          : 'winAgregarDispositivosNodoSC',
        title       : 'Filtro de Dispositivos <b style="color:green;">('+titulo+')</b>',
        layout      : 'fit',
        y           :  35,
        buttonAlign : 'center',
        resizable   :  false,
        modal       :  true,
        closable    :  false,
        items       :  [formPanel],
        buttons     :  [btnAgregarSafeCity,btnCancelarSafeCity]
    }).show();
}

function agregarDispositivosOntCliente(data,accion) {

    var stylebutton  = '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>&nbsp;&nbsp;Agregar';
    var idEmpleado   =  Ext.getCmp('comboFilterTecnico').getValue();
    var empleado     =  Ext.getCmp('comboFilterTecnico').getRawValue();
    var titulo       =  null;

    if (Ext.isEmpty(idEmpleado)) {
        Ext.Msg.show({
            title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
            buttonText: {cancel: 'Cerrar'}
        });
        return;
    }

    if (accion === 2) {
        titulo       = 'Cliente';
    }

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        allowDeselect: true,
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnAgregarClienteSafeCity").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnAgregarClienteSafeCity").setDisabled(true);
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
            url    :  url_equipoAdicionales,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'result',
                totalProperty: 'total'
            },
            extraParams: {
                'intIdPersona'          : idEmpleado,
                'intPerteneceElemento' : 1
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
            {name: 'feAsignacion'       , mapping: 'feAsignacion'},
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
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaDesde',
                fieldLabel: '<b>Desde:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
            },
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaHasta',
                fieldLabel: '<b>Hasta:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
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
                    var strFechaDesde = Ext.getCmp('fechaDesde').getRawValue()  ?  Ext.getCmp('fechaDesde').getRawValue() : "";
                    var strFechaHasta = Ext.getCmp('fechaHasta').getRawValue()  ?  Ext.getCmp('fechaHasta').getRawValue() : "";

                    storeEquiposAsignados.load({params:{
                        'intIdPersona'    : idEmpleado,
                        'strTipoElemento' : null,
                        'strTiposElementos' : null,
                        'strNumeroSerie'  : serie,
                        'strModelo'       : modelo,
                        'strDescripcion'  : descripcion,
                        'strFechaDesde'   : strFechaDesde,
                        'strFechaHasta'   : strFechaHasta
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
                    Ext.getCmp('fechaDesde').setValue('');
                    Ext.getCmp('fechaHasta').setValue('');
                    storeEquiposAsignados.load();
                }
            }
        ]
    });

    var gridDispositivosAsignadosTecnico = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosAsignadosTecnico',
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
            },
            {
                header    : '<b>Fecha Asignacion</b>',
                dataIndex : 'feAsignacion',
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
                items:[filterPanelTecnico,gridDispositivosAsignadosTecnico]
            }
        ]
    });

    var btnCancelarClienteSafeCity = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winAgregarDispositivosClienteSC.close();
            winAgregarDispositivosClienteSC.destroy();
        }
    });

    var btnAgregarClienteSafeCity = Ext.create('Ext.Button', {
        id      : 'idBtnAgregarClienteSafeCity',
        text    :  stylebutton,
        disabled:  true,
        handler: function() {
            if (Ext.getCmp('gridDispositivosClienteSafeCity')){
                var storeDispositivosCliente = Ext.getCmp("gridDispositivosClienteSafeCity").getStore();
            }
            var arraySelection        = Ext.getCmp("gridDispositivosAsignadosTecnico").getSelectionModel().getSelection();
            if (arraySelection.length > 0)
            {
                $.each(arraySelection, function(i, item)
                {
                    var index = null;
                    var serieElemento = item.data.serieElemento;
                    if (Ext.getCmp('gridDispositivosClienteSafeCity')){
                        index = storeDispositivosCliente.findBy(function (record) {
                            return record.data.serieElemento === serieElemento;
                        });
                    }

                    if (accion === 2) {
                        if (index < 0) {
                            storeDispositivosCliente.add({
                                "idControl"           : item.data.idControl,
                                "idPersonaRol"        : item.data.idPersonaRol,
                                "serieElemento"       : serieElemento,
                                "modeloElemento"      : item.data.modeloElemento,
                                "descripcionElemento" : item.data.descripcionElemento,
                                "macElemento"         : item.data.macElemento});
                            Ext.Msg.alert('Mensaje ',`Elemento agregado con éxito`);
                        }else{
                            Ext.Msg.alert('Mensaje ',`El elemento con serie ${serieElemento} ya fue agregado`);
                        }
                    }
                });

                winAgregarDispositivosClienteSC.close();
                winAgregarDispositivosClienteSC.destroy();
            }
        }
    });

    var winAgregarDispositivosClienteSC = new Ext.Window ({
        id          : 'winAgregarDispositivosClienteSC',
        title       : 'Filtro de Dispositivos <b style="color:green;">('+titulo+')</b>',
        layout      : 'fit',
        y           :  35,
        buttonAlign : 'center',
        resizable   :  false,
        modal       :  true,
        closable    :  false,
        items       :  [formPanel],
        buttons     :  [btnAgregarClienteSafeCity,btnCancelarClienteSafeCity]
    }).show();
}
//Fin Bloque de funciones para servicos safe city