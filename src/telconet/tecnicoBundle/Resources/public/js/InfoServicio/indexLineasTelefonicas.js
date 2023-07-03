
var gridCorreos;
function cambioElementoTelefonia(data) {
    var storeModelosCpe = new Ext.data.Store({
        pageSize: 1000,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: getModelosElemento,
            extraParams: {
                tipo: 'CPE',
                forma: 'Empieza con',
                estado: "Activo"
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'modelo', mapping: 'modelo'},
                {name: 'codigo', mapping: 'codigo'}
            ]
    });

    var elementoClienteNuevo = {
        xtype: 'fieldset',
        title: 'Ingrese la serie del elemento',
        defaultType: 'textfield',
        defaults: {
            width: 500,
            height: 100
        },
        items: [{
                xtype: 'container',
                layout: {
                    type: 'table',
                    columns: 5,
                    align: 'stretch'
                },
                items: [
                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'serieCpe',
                        name: 'serieCpe',
                        fieldLabel: 'Serie Elemento',
                        displayField: "",
                        value: "",
                        width: '35%',
                        listeners: {
                            blur: function (serie) {
                                Ext.Ajax.request({
                                    url: buscarCpeNaf,
                                    method: 'post',
                                    params: {
                                        serieCpe: serie.getValue(),
                                        modeloElemento: '',
                                        estado: 'PI',
                                        bandera: 'ActivarServicio'
                                    },
                                    success: function (response) {
                                        var respuesta = response.responseText.split("|");
                                        var status = respuesta[0];
                                        var mensaje = respuesta[1].split(",");
                                        var descripcion = mensaje[0];
                                        var mac = mensaje[1];
                                        var modelo = mensaje[2];

                                        Ext.getCmp('descripcionCpe').setValue = '';
                                        Ext.getCmp('descripcionCpe').setRawValue('');
                                        Ext.getCmp('macCpe').setValue = '';
                                        Ext.getCmp('macCpe').setRawValue('');
                                        Ext.getCmp('modeloCpe').setValue = '';
                                        Ext.getCmp('modeloCpe').setRawValue('');

                                        if (status == "OK")
                                        {
                                            if (storeModelosCpe.find('modelo', modelo) == -1)
                                            {
                                                var strMsj = 'El Elemento con: <br>' +
                                                    'Modelo: <b>' + modelo + ' </b><br>' +
                                                    'Descripcion: <b>' + descripcion + ' </b><br>' +
                                                    'No corresponde a un CPE, <br>' +
                                                    'No podrá continuar con el proceso, Favor Revisar <br>';
                                                Ext.Msg.alert('Advertencia', strMsj);
                                            } else
                                            {
                                                Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                Ext.getCmp('descripcionCpe').setRawValue(descripcion);
                                                Ext.getCmp('macCpe').setValue = mac;
                                                Ext.getCmp('macCpe').setRawValue(mac);
                                                Ext.getCmp('modeloCpe').setValue = modelo;
                                                Ext.getCmp('modeloCpe').setRawValue(modelo);
                                            }
                                        } else
                                        {
                                            Ext.Msg.alert('Mensaje ', mensaje);
                                        }
                                    },
                                    failure: function (result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }
                        }
                    },
                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'modeloCpe',
                        name: 'modeloCpe',
                        fieldLabel: 'Modelo',
                        displayField: '',
                        valueField: '',
                        readOnly: true,
                        width: '35%'
                    },
                    {width: '10%', border: false},

                    //---------------------------------------

                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'macCpe',
                        name: 'macCpe',
                        fieldLabel: 'Mac',
                        displayField: "",
                        value: "",
                        width: '35%'
                    },
                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'descripcionCpe',
                        name: 'descripcionCpe',
                        fieldLabel: 'Descripcion',
                        readOnly: true,
                        displayField: '',
                        value: '',
                        width: '35%'
                    },
                    {
                        xtype: 'hidden',
                        id: 'mensaje',
                        name: 'mensaje',
                        displayField: "",
                        value: "",
                        width: '35%'
                    },
                    {width: '10%', border: false},
                ]
            }]
    };

    var formPanelElementoNuevo = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            elementoClienteNuevo
        ],
        buttons: [{
                text: 'Cambiar',
                formBind: true,
                handler: function () {
                    var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                    var macCpe = Ext.getCmp('macCpe').getValue();
                    var serieCpe = Ext.getCmp('serieCpe').getValue();
                    var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();

                    var validacion = false;
                    var flag = 0;
                    if (serieCpe == "" || macCpe == "") {
                        validacion = false;
                    } else {
                        validacion = true;
                    }

                    if (descripcionCpe == "NO HAY STOCK" || descripcionCpe == "NO EXISTE SERIAL" || descripcionCpe == "CPE NO ESTA EN ESTADO") {
                        validacion = false;
                        flag = 3;
                    }

                    if (validacion) {
                        Ext.get(formPanelElementoNuevo.getId()).mask('Cambiando Elemento del Cliente...');

                        Ext.Ajax.request({
                            url: url_gestionarLineasTelefonicas, 
                            method: 'post',
                            timeout: 1000000,
                            params: {
                                idServicio: data.idServicio,
                                modeloCpe: modeloCpe,
                                macCpe: macCpe,
                                serieCpe: serieCpe,
                                descripcionCpe: descripcionCpe,
                                idSolicitud: data.tieneSolicitudCambioCpe,
                                opcion: 'CAMBIAR_ELEMENTO'
                            },
                            success: function (response) {
                                Ext.get(formPanelElementoNuevo.getId()).unmask();
                                if (response.responseText == "OK") {
                                    Ext.Msg.alert('Mensaje', 'Se cambió el Elemento del Cliente', function (btn) {
                                        if (btn == 'ok') {
                                            win.destroy();
                                            store.load();
                                        }
                                    });
                                } else {
                                    Ext.Msg.alert('Mensaje ', response.responseText);
                                }
                            },
                            failure: function (result)
                            {
                                Ext.get(formPanelElementoNuevo.getId()).unmask();

                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });

                    } else {
                        if (flag == 3) {
                            Ext.Msg.alert("Validación", "Datos del elemento incorrectos, favor revisar", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
                        } else {
                            Ext.Msg.alert("Validación", "Favor revise los campos", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
                        }
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function () {
                    Ext.get(gridServicios.getId()).unmask();
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Cambiar Equipo del Cliente',
        modal: true,
        width: 600,
        closable: true,
        layout: 'fit',
        items: [formPanelElementoNuevo]
    }).show();
}
var storeLineasComercial;
function verLineasTelefonicas(idServicio, estado){    
   
    storeLineasComercial = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : url_verLineasTelefonicas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: idServicio
            }
        },
        fields:
            [
              {name:'idNumero', mapping:'idNumero'},  
              {name:'numero', mapping:'numero'},
              {name:'idDominio', mapping:'idDominio'},
              {name:'dominio', mapping:'dominio'},
              {name:'idClave', mapping:'idClave'},
              {name:'clave', mapping:'clave'},
              {name:'idNumeroCanales', mapping:'idNumeroCanales'},
              {name:'numeroCanales', mapping:'numeroCanales'},
              {name:'estado', mapping:'estado'}              
            ]
    });    
    
    //Define boton para crear un detalle de parámetro usado en el toolbar: toolbar
    var btnCrearParametro = Ext.create('Ext.button.Button', {
        text: 'Agregar Lineas',
        iconCls: 'icon_ingresarTrazabilidad',
        scope: this,
        handler: function () {
            
            var permiso = $("#ROLE_415-6043");                        
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

            if(boolPermiso)
            {
                if(estado == "Activo" )
                { 
                    nuevaLinea(idServicio);
                }
                else
                {
                    Ext.Msg.alert('Error', 'Solo puede agregar números con el servicio Activo.');                
                }
            }
            else
            {
                Ext.Msg.alert('Error', 'No tiene los privilegios.');                
            }
        
        }
    });
    
    toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                btnCrearParametro
            ]
    });    
    
    //grid de usuarios
    gridCorreos = Ext.create('Ext.grid.Panel', {
        id:'gridCorreos',
        store: storeLineasComercial,
        columnLines: true,
        dockedItems: [toolbar],
        columns: [
        {
            header: 'Numero',
            dataIndex: 'numero',
            width: 85,
            sortable: true
        },
        {
            header: 'Dominio',
            dataIndex: 'dominio',
            width: 100,
            sortable: true
        },
        {
            header: 'Clave',
            dataIndex: 'clave',
            width: 75,
            sortable: true
        },
        {
            header: 'Canales',
            dataIndex: 'numeroCanales',
            width: 55,
            sortable: true
        },
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 75
        },
        {
            xtype: 'actioncolumn',
            header: 'Accion',
            width: 110,
            items: [

                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_415-6039");
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                        
                        if(boolPermiso && rec.get('estado') == "Activo")
                        {
                            return 'button-grid-edit';    
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }

                    },
                    tooltip: 'Editar Linea',
                    handler: function(grid, rowIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            editarNumero(grid.getStore().getAt(rowIndex).data);
                        }
                        
                    }
                },          
                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_415-6041");
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

                        if(boolPermiso && rec.get('estado') == "Activo" )
                        { 
                            return 'button-grid-cambioCpe';
                        }
                        else
                        {
                            return 'button-grid-invisible';                            
                        }

                    },
                    tooltip: 'Cambiar Numero',
                    handler: function(grid, rowIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            cambiarNumero(grid.getStore().getAt(rowIndex).data, idServicio);
                        }
                        
                    }
                },
                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_415-6042");                      
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                        if(boolPermiso && rec.get('estado') == "Activo" )
                        { 
                            return 'button-grid-cancelarCliente';
                        }
                        else
                        {
                            return 'button-grid-invisible';                            
                        }

                    },
                    tooltip: 'Cancelar Línea',
                    handler: function(grid, rowIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado == "Activo"){
                            cancelarLinea(grid.getStore().getAt(rowIndex).data);
                        }                        
                    }
                }
            ]
        }
        ],
        viewConfig:{
            stripeRows:true
        },

        frame: true,
        height: 250
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
        
        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
            defaults: {
                width: 530
            },
            items: [

                gridCorreos

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
                store.reload();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Lineas Telefonicas',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cambiarNumero(data, idServicio)
{

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            
            {
                xtype: 'fieldset',
                title: 'Buscar',
                defaultType: 'textfield',
                defaults: {
                    width: 280
                },
                items: [
                    {
                        xtype: 'textfield',
                        id: 'numeroBusqueda',
                        name: 'numeroBusqueda',
                        fieldLabel: 'Numero ',
                        displayField: "",
                        value: "",
                        width: '30%'
                    },
                    {
                        xtype: 'combobox',
                        id: 'filtro',
                        name: 'filtro',
                        fieldLabel: 'Filtro',
                        store:
                            [
                                ['EMPIEZA', 'EMPIEZA'],
                                ['TERMINA', 'TERMINA'],
                                ['CONTIENE', 'CONTIENE']
                            ],
                        width: '30%'
                    },
                    {
                        xtype: 'button',
                        id: 'btnBuscar',
                        text: 'Consultar',
                        width: 260,
                        handler: function ()
                        {
                            
                            var filtro = Ext.getCmp('filtro').getValue();
                            var numeroBusqueda = Ext.getCmp('numeroBusqueda').getValue();
                            
                            if(numeroBusqueda && !filtro)
                            {
                                Ext.Msg.alert('Error', 'Debe seleccionar el filtro.');
                                return false;                                
                            }
                            
                            Ext.get(formPanel.getId()).mask('Procesando...');
                            
                            Ext.Ajax.request({
                                url: url_gestionarLineasTelefonicas,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    numeroBusqueda: numeroBusqueda,
                                    filtro: filtro,
                                    opcion: 'CONSULTAR_NUMERO',
                                    idServicio: idServicio
                                },
                                success: function (response) {
                                    Ext.get(formPanel.getId()).unmask();                                    
                                    
                                    Ext.getCmp('numeroNuevo').setValue = response.responseText;
                                    Ext.getCmp('numeroNuevo').setRawValue(response.responseText);
                                    
                                }

                            });
                        }
                    }
                ]//cierre del fieldset
            },
            {
                xtype: 'fieldset',
                title: 'Numeros',
                defaultType: 'textfield',
                defaults: {
                    width: 280
                },
                items: [

                    {
                        xtype: 'textfield',
                        id: 'numeroActual',
                        name: 'numeroActual',
                        fieldLabel: 'Actual ',
                        displayField: "",
                        value: data.numero,
                        readOnly: true,
                        width: '30%'
                    },
                    {
                        xtype: 'textfield',
                        id: 'numeroNuevo',
                        name: 'numeroNuevo',
                        fieldLabel: 'Nuevo ',
                        displayField: "",
                        style: { color: 'blue' },
                        value: "",
                        readOnly: true,
                        width: '30%'
                    }
                ]//cierre del fieldset
            }
        ],
        buttons: [{
                text: 'Cambiar',
                formBind: true,
                handler: function () {
                    var numeroNuevo = Ext.getCmp('numeroNuevo').getValue();

                    if (numeroNuevo.length > 0) {

                        Ext.Msg.alert('Mensaje', '¿Está seguro que desea cambiar el número?', function (btn) {                           
                            
                            if (btn == 'ok') {
                                
                                Ext.get(formPanel.getId()).mask('Procesando...');
                                
                                Ext.Ajax.request({
                                    url: url_gestionarLineasTelefonicas,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {
                                        numeroNuevo: numeroNuevo,
                                        opcion: 'CAMBIAR_NUMERO',
                                        idNumero: data.idNumero,
                                        idServicio:data.idServicio
                                    },
                                    success: function (response) {
                                        Ext.get(formPanel.getId()).unmask();

                                        if (response.responseText == "OK") {
                                            Ext.Msg.alert('Mensaje', 'Transacción Exitosa ', function (btn) {
                                                if (btn == 'ok') {
                                                    win.destroy();
                                                    storeLineasComercial.load();
                                                }
                                            });
                                        } else {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Mensaje', response.responseText);
                                        }
                                    }
                                });
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Atención', 'Consulte el nuevo número.');
                    }
                }
            }, {
                text: 'Cancelar',
                handler: function () {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Cambiar Numero',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cancelarLinea(data)
{
    Ext.Msg.alert('Mensaje','¿Está seguro que desea cancelar el número?', function(btn){
        if(btn=='ok'){
            Ext.get(gridCorreos.getId()).mask('Cancelando...');
            Ext.Ajax.request({
                url: url_gestionarLineasTelefonicas,
                method: 'post',
                timeout: 400000,
                params: { 
                    idNumero: data.idNumero,                    
                    opcion: 'CANCELAR_LINEA'
                },
                success: function(response){
                    Ext.get(gridCorreos.getId()).unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se canceló correctamente.', function(btn){
                            if(btn=='ok'){
                                
                                storeLineasComercial.load();
                            }
                        });
                    }
                    else{

                        Ext.Msg.alert('Mensaje',response.responseText); 

                    }

                }

            });
        }
    });
    
}

function editarNumero(data)
{
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [

        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',

            defaults: {
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'canales',
                    name: 'canales',
                    fieldLabel: 'Canales ',
                    displayField: data.numeroCanales,
                    value: data.numeroCanales,
                    allowBlank: false,
                    blankText:  "Clave no puede ser vacia",
                    width: '30%'
                }
            ]
        }
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function () {
                    var canales = Ext.getCmp('canales').getValue();
                    //si es el mismo canal no hago nada
                    if (data.numeroCanales != canales)
                    {
                        
                        Ext.Msg.alert('Mensaje', 'Esta seguro que desea editarlo?', function (btn) {                           
                            
                            if (btn == 'ok') {
                                
                                Ext.get(formPanel.getId()).mask('Procesando...');
                                
                                Ext.Ajax.request({
                                    url: url_gestionarLineasTelefonicas,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {
                                        canales: canales,
                                        idNumeroCanales: data.idNumeroCanales,
                                        opcion: 'EDITAR_LINEA',
                                        idNumero: data.idNumero,
                                        idServicio:data.idServicio
                                    },
                                    success: function (response) {
                                        Ext.get(formPanel.getId()).unmask();

                                        if (response.responseText == "OK") {
                                            Ext.Msg.alert('Mensaje', 'Transacción Exitosa ', function (btn) {
                                                if (btn == 'ok') {
                                                    win.destroy();
                                                    storeLineasComercial.load();
                                                }
                                            });
                                        } else {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Mensaje', response.responseText);
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Editar Linea',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function nuevaLinea(idServicio)
{
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [

        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',

            defaults: {
                width: 200
            },
            items: [
                {
                    xtype: 'numberfield',
                    id:'cantidad',
                    name: 'cantidad',
                    fieldLabel: 'Cantidad Lineas',
                    displayField: "",
                    allowBlank: false,
                    blankText:  "No puede ser vacio",
                    value: 1,
                    maxValue: 20,
                    minValue: 1,    
                    width: '30%'
                },
                {
                    xtype: 'numberfield',
                    id:'canales',
                    name: 'canales',
                    fieldLabel: 'Numero Canales ',
                    displayField: "",
                    value: 2,
                    maxValue: 20,
                    minValue: 2,
                    allowBlank: false,
                    blankText:  "No puede ser vacio",
                    width: '30%'
                }
            ]
        }
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function () {
                    
                    var cantidad = Ext.getCmp('cantidad').getValue();
                    var canales = Ext.getCmp('canales').getValue();

                            
                    Ext.get(formPanel.getId()).mask('Procesando...');
                    Ext.Ajax.request({
                        url: url_nuevasLineas,
                        method: 'post',
                        timeout: 400000,
                        params: {
                            idServicio: idServicio,
                            cantidad: cantidad,
                            canales: canales
                        },
                        success: function (response) {
                            Ext.get(formPanel.getId()).unmask();

                            if (response.responseText == "OK") {
                                Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function (btn) {
                                    if (btn == 'ok') {
                                        storeLineasComercial.load();
                                        win.destroy();
                                    }
                                    else
                                    {
                                        win.destroy();
                                    }
                                });
                            } else {
                                Ext.Msg.alert('Error', response.responseText);
                            }
                        }
                    });
                }
            },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Nuevas Linea Telefonica',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}


function solicitarFactibilidadTelefonia(idServicio)
{
    connFact.request({
        url: url_solicitarFactibilidadTelefonia,
        method: 'post',
        timeout: 400000,
        params: {idServicio: idServicio },
        success: function (response) {
            var text = response.responseText;

            if (text == 'OK')
            {
                Ext.Msg.alert('Mensaje', 'Transacción Existosa.', function (btn) {
                    if (btn == 'ok') {
                        store.load();
                    }
                });
            } else
            {
                Ext.Msg.alert('Mensaje', text, function (btn) {
                    if (btn == 'ok') {
                        store.load();
                    }
                });
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Alerta', result.responseText);
            store.load();
        }
    });
}

function cierraVentanaIngresoFactibilidad() {
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}
