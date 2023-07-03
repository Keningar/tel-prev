/* Funcion que sirve para mostrar la pantalla de confirmacion de
 * servicio y realizar la llamada ajax para poner en Activo
 * el servicio para la empresa TTCO
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param int   idAccion    id de accion de la credencial
 */    
function confirmarActivacion(data,idAccion){
    
    if(data.tipoOrden=='R'){
        Ext.Msg.alert('Mensaje','Se reasignaran los datos al nuevo servicio, <br>Desea Continuar?', function(btn){
            if(btn=='ok'){
                Ext.get("grid").mask();
                Ext.Ajax.request({
                    url: confirmarActivacionBoton,
                    method: 'post',
                    timeout: 400000,
                    params: { 
                        idServicio: data.idServicio,
                        idProducto: data.productoId,
                        perfil: data.perfilDslam,
                        login: data.login,
                        capacidad1: data.capacidadUno,
                        capacidad2: data.capacidadDos,
                        serieCpe: "",
                        codigoArticulo: "",
                        macCpe: "",
                        numPc: "",
                        ssid: "",
                        password: "password",
                        modoOperacion: "modoOperacion",
                        jsonCaracteristicas: "jsonCaracteristicas",
                        observacionCliente: "observacion",
                        idAccion: idAccion
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        if(response.responseText == "OK"){
            //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                            Ext.Msg.alert('Mensaje','Se Confirmo la Reubicacion!', function(btn){
                                if(btn=='ok'){
                                    store.load();
                                }
                            });
                        }
                        else if(response.responseText == "NO IP CPE"){
                            Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                        }
                        else if(response.responseText == "CANTIDAD CERO"){
                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                        }
                        else if(response.responseText == "NO EXISTE PRODUCTO"){
                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                        }
                        else if(response.responseText == "NO EXISTE CPE"){
                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                        }
                        else{
                            Ext.Msg.alert('Mensaje ','No se pudo confirmar la Activacion, problemas en la Ejecucion del Script!' );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }
    else if(data.tipoOrden=='T'){
        Ext.Msg.alert('Mensaje','Se trasladaran los datos al nuevo servicio, <br>Desea Continuar?', function(btn){
            if(btn=='ok'){
                Ext.get("grid").mask();
                Ext.Ajax.request({
                    url: confirmarActivacionBoton,
                    method: 'post',
                    timeout: 400000,
                    params: { 
                        idServicio: data.idServicio,
                        idProducto: data.productoId,
                        perfil: data.perfilDslam,
                        login: data.login,
                        capacidad1: data.capacidadUno,
                        capacidad2: data.capacidadDos,
                        serieCpe: "",
                        codigoArticulo: "",
                        macCpe: "",
                        numPc: "",
                        ssid: "",
                        password: "password",
                        modoOperacion: "modoOperacion",
                        jsonCaracteristicas: "jsonCaracteristicas",
                        observacionCliente: "observacion",
                        idAccion: idAccion
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        if(response.responseText == "OK"){
            //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                            Ext.Msg.alert('Mensaje','Se Confirmo el Traslado!', function(btn){
                                if(btn=='ok'){
                                    store.load();
                                }
                            });
                        }
                        else if(response.responseText == "NO IP CPE"){
                            Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                        }
                        else if(response.responseText == "CANTIDAD CERO"){
                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                        }
                        else if(response.responseText == "NO EXISTE PRODUCTO"){
                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                        }
                        else if(response.responseText == "NO EXISTE CPE"){
                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                        }
                        else{
                            Ext.Msg.alert('Mensaje ','No se pudo confirmar la Activacion, problemas en la Ejecucion del Script!' );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }
    else{
        Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosCliente,
            method: 'post',
            timeout: 400000,
            params: { 
                idServicio: data.idServicio
            },
            success: function(response){
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;
                
                //console.log(datos);

                var storeIpPublica = new Ext.data.Store({  
                    pageSize: 50,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url : getIpPublicas,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams: {
                            idServicio: data.idServicio
                        }
                    },
                    fields:
                        [
                          {name:'ip', mapping:'ip'},
                          {name:'mascara', mapping:'mascara'},
                          {name:'gateway', mapping:'gateway'},
                          {name:'tipo', mapping:'tipo'},
                          {name:'ipCpe', mapping:'ipCpe'}
                        ]
                });

                //-------------------------------------------------------------------------------------------
                Ext.define('tipoCaracteristica', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'tipo', type: 'string'}
                    ]
                });

                var comboCaracteristica = new Ext.data.Store({ 
                    model: 'tipoCaracteristica',
                    data : [
                        {tipo:'MONITOREO' },
                        {tipo:'WAN' },
                        {tipo:'LAN' }
                    ]
                });

                var comboModoOperacionCpe = new Ext.data.Store({ 
                    model: 'tipoCaracteristica',
                    data : [
                        {tipo:'ROUTER' },
                        {tipo:'NAT' },
                        {tipo:'BRIDGE' }
                    ]
                });

                var storeModelosCpe = new Ext.data.Store({  
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url : getModelosElemento,
                        extraParams: {
                            tipo:   'CPE',
                            forma:  'Empieza con',
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
                          {name:'modelo', mapping:'modelo'},
                          {name:'codigo', mapping:'codigo'}
                        ]
                });

                Ext.define('IpPublica', {
                    extend: 'Ext.data.Model',
                    fields: [
                          {name:'ip', mapping:'ip'},
                          {name:'mascara', mapping:'mascara'},
                          {name:'gateway', mapping:'gateway'},
                          {name:'tipo', mapping:'tipo'},
                          {name:'ipCpe', mapping:'ipCpe'}
                    ]
                });

                var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1,
                    listeners: {
                        edit: function(){
                            // refresh summaries
                            gridIpPublica.getView().refresh();
                        }
                    }
                });

                var selIpPublica = Ext.create('Ext.selection.CheckboxModel', {
                    listeners: {
                        selectionchange: function(sm, selections) {
                            gridIpPublica.down('#removeButton').setDisabled(selections.length == 0);
                        }
                    }
                });

                //grid de ips (publica, lan, wan, monitoreo)
                gridIpPublica = Ext.create('Ext.grid.Panel', {
                    id:'gridIpPublica',
                    store: storeIpPublica,
                    columnLines: true,
                    columns: [{
                        //id: 'nombreDetalle',
                        header: 'Tipo',
                        dataIndex: 'tipo',
                        width: 100,
                        sortable: true,
                        editor: {
                            //id:'searchTipo_cmp',
                            queryMode: 'local',
                            xtype: 'combobox',
                            displayField:'tipo',
                            valueField: 'tipo',
                            loadingText: 'Buscando...',
                            store: comboCaracteristica
                        }
                    },{
                        header: 'Ip',
                        dataIndex: 'ip',
                        width: 120,
                        editor: {
                            id:'ip',
                            name:'ip',
                            xtype: 'textfield',
                            valueField: ''
                        }
                    },
                    {
                        header: 'Mascara',
                        dataIndex: 'mascara',
                        width: 130,
                        editor: {
                            id:'mascara',
                            name:'mascara',
                            xtype: 'textfield',
                            valueField: ''
                        }
                    },
                    {
                        header: 'Gateway',
                        dataIndex: 'gateway',
                        width: 120,
                        editor: {
                            id:'gateway',
                            name:'gateway',
                            xtype: 'textfield',
                            valueField: ''
                        }
                    },
                    {
                        xtype: 'checkcolumn',
                        header: 'Ip CPE?',
                        dataIndex: 'ipCpe',
                        width: 80,
                        editor: {
                            xtype: 'checkbox',
                            cls: 'x-grid-checkheader-editor'
                        },
                        stopSelection: false
                    },
                    {
                        header: 'idIp',
                        dataIndex: 'ipId',
                        width: 120,
                        hidden: true,
                        hideable: false
                    },
                    ],
                    selModel: selIpPublica,
                    viewConfig:{
                        stripeRows:true
                    },

                    // inline buttons
                    dockedItems: [{
                        xtype: 'toolbar', 
                        items: [{
                            itemId: 'removeButton',
                            text:'Eliminar',
                            tooltip:'Elimina el item seleccionado',
                            iconCls:'remove',
                            disabled: true,
                            handler : function(){eliminarSeleccion(gridIpPublica);}
                        }, '-', {
                            text:'Agregar',
                            tooltip:'Agrega un item a la lista',
                            iconCls:'add',
                            handler : function(){
                                // Create a model instance
                                var r = Ext.create('IpPublica', { 
                                    ip: '',
                                    mascara: '',
                                    gateway: '',
                                    tipo: ''

                                });
                                if(!existeRecordIpPublica(r, gridIpPublica))
                                {
                                    storeIpPublica.insert(0, r);
                                    cellEditing.startEditByPosition({row: 0, column: 1});
                                }
                                else
                                {
                                  alert('Ya existe un registro vacio.');
                                }
                            }
                        }]
                    }],
                    frame: false,
                    height: 200,
                    //title: 'Caracteristicas del Cliente',
                    plugins: [cellEditing]
                });

                //-------------------------------------------------------------------------------------------

                var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85, 
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 2
                },
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del cliente
                    {
                        xtype: 'fieldset',
                        title: 'Informacion Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500,
                            height: 130
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'nombreCompleto',
                                        fieldLabel: 'Cliente',
                                        displayField: datos[0].nombreCompleto,
                                        value: datos[0].nombreCompleto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '30%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'tipoNegocio',
                                        fieldLabel: 'Tipo Negocio',
                                        displayField: datos[0].tipoNegocio,
                                        value: datos[0].tipoNegocio,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        name: 'direccion',
                                        fieldLabel: 'Direccion',
                                        displayField: datos[0].direccion,
                                        value: datos[0].direccion,
                                        readOnly: true,
                                        labelPad: -52,
                                        //html: '4,1', 
                                        colspan: 4,
                                        width: '98%'
                                    }

                                    //---------------------------------------------
                                ]
                            }

                        ]
                    },//cierre de la informacion del cliente

                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Informacion del Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500,
                            height: 130
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'plan',
                                        fieldLabel: 'Plan',
                                        displayField: data.nombrePlan,
                                        value: data.nombrePlan,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'capacidadUno',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: data.capacidadUno,
                                        value: data.capacidadUno,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'capacidadDos',
                                        fieldLabel: 'Capacidad Dos',
                                        displayField: data.capacidadDos,
                                        value: data.capacidadDos,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Int/Prom Uno',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'numeroPcCliente',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Int/Prom Dos',
                                        displayField: data.capacidadCuatro,
                                        value: data.capacidadCuatro,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'ultimaMilla',
                                        name: 'ultimaMilla',
                                        fieldLabel: 'Ultima Milla',
                                        displayField: data.ultimaMilla,
                                        value: data.ultimaMilla,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    { width: '10%', border: false}
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto

                    //ips, mascaras, gws (publica, monitoreo, wan, lan)
                    {
                        xtype: 'fieldset',
                        title: 'Ips del Cliente',
                        defaultType: 'textfield',
            //                checkboxToggle: true,
            //                collapsed: true,
                        defaults: { 
                            width: 590,
                            height: 130
                        },
                        items: [

                            gridIpPublica

                        ]
                    },//cierre ips del cliente

                    //informacion del cpe
                    {
                        xtype: 'fieldset',
                        title: 'Informacion del CPE',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500 
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: 'Serie CPE',
                                        displayField: "",
                                        value: "",
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: 'Modelo',
                                        displayField:'modelo',
                                        valueField: 'modelo',
                                        loadingText: 'Buscando...',
                                        store: storeModelosCpe,
                                        width: '35%',
                                        listeners: {
                                            blur: function(combo){
                                                Ext.Ajax.request({
                                                    url: buscarCpeNaf,
                                                    method: 'post',
            //                                        timeout: 400000,
                                                    params: { 
                                                        serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                        modeloElemento: combo.getValue(),
                                                        estado: 'PI',
                                                        bandera: 'ActivarServicio'
                                                    },
                                                    success: function(response){
                                                        var respuesta = response.responseText.split("|");
                                                        var status = respuesta[0];
                                                        var mensaje = respuesta[1];

                                                        if(status=="OK")
                                                        {
                                                            Ext.getCmp('descripcionCpe').setValue = mensaje;
                                                            Ext.getCmp('descripcionCpe').setRawValue(mensaje);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                            Ext.getCmp('descripcionCpe').setValue = status;
                                                            Ext.getCmp('descripcionCpe').setRawValue(status);
                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'hidden',
                                        id:'jsonCaracteristicas',
                                        name: 'jsonCaracteristicas',
                                        displayField: '',
                                        valueField: '',
                                        width: '10%'
                                    },

                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: 'Mac CPE',
                                        displayField: data.mac,
                                        value: data.mac,
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'descripcionCpe',
                                        name: 'descripcionCpe',
                                        fieldLabel: 'Descripcion CPE',
                                        displayField: "",
                                        value: "",
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------


                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'ssid',
                                        name: 'ssid',
                                        fieldLabel: 'SSID Cliente',
                                        displayField: "",
                                        value: "",
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'password',
                                        name: 'password',
                                        fieldLabel: 'Password',
                                        displayField: "",
                                        value: "",
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'numeroPc',
                                        name: 'numeroPc',
                                        fieldLabel: 'Numero PCs',
                                        displayField: "",
                                        value: "",
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modoOperacion',
                                        name: 'modoOperacion',
                                        fieldLabel: 'Modo Operacion',
                                        displayField:'tipo',
                                        valueField: 'tipo',
                                        loadingText: 'Buscando...',
                                        store: comboModoOperacionCpe,
                                        width: '35%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: 'Observacion',
                                        displayField: "",
                                        labelPad: -45,
                                        //html: '4,1', 
                                        colspan: 4,
                                        value: "",
                                        width: '87%'

                                    }
                                ]//cierre del container table
                            }                


                        ]//cierre del fieldset
                    }//cierre informacion cpe

                ],
                buttons: [{
                    text: 'Grabar',
                    formBind: true,
                    handler: function(){
                        obtenerDatosCaracteristicas();
                        var jsonCaracteristicas = Ext.getCmp('jsonCaracteristicas').getRawValue();
                        var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                        var modoOperacion = Ext.getCmp('modoOperacion').getValue();
                        var numPc = Ext.getCmp('numeroPc').getValue();
                        var serieCpe = Ext.getCmp('serieCpe').getValue();
                        var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();
                        var macCpe = Ext.getCmp('macCpe').getValue();
                        var ssid = Ext.getCmp('ssid').getValue();
                        var password = Ext.getCmp('password').getValue();
                        var observacion = Ext.getCmp('observacionCliente').getValue();

                        var validacion=false;
                        if(serieCpe=="" || macCpe==""){
                            validacion=false;
                        }
                        else{
                            validacion=true;
                        }

                        if(descripcionCpe=="NO HAY STOCK" || descripcionCpe=="NO EXISTE SERIAL" || descripcionCpe=="CPE NO ESTA EN ESTADO"){
                            validacion=false;
                        }

                        if(validacion){
                            Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts de Comprobacion!');


                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio: data.idServicio,
                                    idProducto: data.productoId,
                                    perfil: data.perfilDslam,
                                    login: data.login,
                                    capacidad1: data.capacidadUno,
                                    capacidad2: data.capacidadDos,
                                    serieCpe: serieCpe,
                                    codigoArticulo: modeloCpe,
                                    macCpe: macCpe,
                                    numPc: numPc,
                                    ssid: ssid,
                                    password: password,
                                    modoOperacion: modoOperacion,
                                    jsonCaracteristicas: jsonCaracteristicas,
                                    observacionCliente: observacion,
                                    idAccion: idAccion
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje','Se Confirmo la Activacion', function(btn){
                                            if(btn=='ok'){
                                                win.destroy();
                                                store.load();
                                            }
                                        });
                                    }
                                    else if(response.responseText == "NO IP CPE"){
                                        Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                                    }
                                    else if(response.responseText == "CANTIDAD CERO"){
                                        Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                    }
                                    else if(response.responseText == "NO EXISTE PRODUCTO"){
                                        Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                    }
                                    else if(response.responseText == "NO EXISTE CPE"){
                                        Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                    }
                                    else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                        Ext.Msg.alert('Mensaje ','CPE no esta en el estado Correcto, favor revisar!' );
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ',response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });

                        }
                        else{
                            Ext.Msg.alert("Failed","Favor Revise los campos. No pueden haber campos vacios", function(btn){
                                    if(btn=='ok'){
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
                title: 'Confirmar Servicio',
                modal: true,
                width: 1225,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

            }//cierre response
        }); 
    }
    
         
}

/* Funcion que sirve para mostrar la pantalla de confirmacion de
 * servicio y realizar la llamada ajax para poner en Activo
 * el servicio para la empresa MD
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param int   idAccion    id de accion de la credencial
 */
function confirmarActivacionMD(data,idAccion){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({ 
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            
            //-------------------------------------------------------------------------------------------
            Ext.define('tipoCaracteristica', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'tipo', type: 'string'}
                ]
            });
                
            var comboModoOperacionCpe = new Ext.data.Store({ 
                model: 'tipoCaracteristica',
                data : [
                    {tipo:'ROUTER' },
                    {tipo:'NAT' },
                    {tipo:'BRIDGE' }
                ]
            });

            var storeModelosCpe = new Ext.data.Store({  
                pageSize: 1000,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : getModelosElemento,
                    extraParams: {
                        tipo:   'CPE',
                        forma:  'Empieza con',
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
                      {name:'modelo', mapping:'modelo'},
                      {name:'codigo', mapping:'codigo'}
                    ]
            });
            
            var strNombrePlanProd           = data.nombrePlan;
            var strEtiquetaPlanProd         = 'Plan';
            var strEtiquetaPerfilVelocidad  = 'Perfil';
            var strNombreTxtPerfilVelocidad = 'perfil';
            var strValorPerfilVelocidad     = data.perfilDslam;
            if(datos[0].strEsInternetLite === 'SI')
            {
                strNombrePlanProd           = data.nombreProducto;
                strEtiquetaPlanProd         = 'Producto';
                strEtiquetaPerfilVelocidad  = 'Velocidad';
                strNombreTxtPerfilVelocidad = 'velocidad';
                strValorPerfilVelocidad     = data.velocidadISB + " MB";
            }
            
            //-------------------------------------------------------------------------------------------

            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85, 
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 2
                },
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Informacion del Servicio',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 540,
                            height: 130
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'plan',
                                        fieldLabel: strEtiquetaPlanProd,
                                        displayField: strNombrePlanProd,
                                        value: strNombrePlanProd,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'capacidadUno',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: data.capacidadUno,
                                        value: data.capacidadUno,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'capacidadDos',
                                        fieldLabel: 'Capacidad Dos',
                                        displayField: data.capacidadDos,
                                        value: data.capacidadDos,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Int/Prom Uno',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'numeroPcCliente',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Int/Prom Dos',
                                        displayField: data.capacidadCuatro,
                                        value: data.capacidadCuatro,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'ultimaMilla',
                                        name: 'ultimaMilla',
                                        fieldLabel: 'Ultima Milla',
                                        displayField: data.ultimaMilla,
                                        value: data.ultimaMilla,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: strNombreTxtPerfilVelocidad,
                                        name: strNombreTxtPerfilVelocidad,
                                        fieldLabel: strEtiquetaPerfilVelocidad,
                                        displayField: strEtiquetaPerfilVelocidad,
                                        value: strValorPerfilVelocidad,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    { width: '10%', border: false}
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto

                    //informacion de backbone
                    {
                        xtype: 'fieldset',
                        title: 'Informacion de backbone',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 540,
                            height: 130
                        },
                        items: [

                            //gridInfoBackbone

                           {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                   {
                                        xtype: 'textfield',
                                        name: 'Elemento',
                                        fieldLabel: 'Elemento',
                                        displayField: data.elementoNombre,
                                       value: data.elementoNombre,

                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'ipElemento',
                                        fieldLabel: 'Ip Elemento',
                                        displayField: data.ipElemento,
                                        value: data.ipElemento,
                                        //displayField: "",
                                        //value: "",
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'interfaceElemento',
                                        fieldLabel: 'Puerto Elemento',
                                        displayField: data.interfaceElementoNombre,
                                        value: data.interfaceElementoNombre,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'modeloELemento',
                                        fieldLabel: 'Modelo Elemento',
                                        displayField: data.modeloElemento,
                                        value: data.modeloElemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'splitterElemento',
                                        fieldLabel: 'Splitter Elemento',
                                        displayField: datos[0].nombreSplitter,
                                        value: datos[0].nombreSplitter,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'splitterInterfaceElemento',
                                        fieldLabel: 'Splitter Interface',
                                        displayField: datos[0].nombrePuertoSplitter,
                                        value: datos[0].nombrePuertoSplitter,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cajaElemento',
                                        fieldLabel: 'Caja Elemento',
                                        displayField: datos[0].nombreCaja,
                                        value: datos[0].nombreCaja,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    { width: '10%', border: false}

                                ]
                            }

                        ]
                    }//cierre de info de backbone


                ],
                buttons: [{
                    text: 'Confirmar',
                    formBind: true,
                    handler: function(){
                        validacion= true;
                        if(validacion){
                            Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts de Comprobacion!');


                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio: data.idServicio,
                                    idProducto: data.productoId,
                                    idAccion  : idAccion,
                                    strEsIsb  : datos[0].strEsInternetLite
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                                        Ext.Msg.alert('Mensaje','Se Confirmo el Servicio', function(btn){
                                            if(btn=='ok'){
                                                win.destroy();
                                                store.load();                                                                                                
                                            }
                                        });
                                    }
                                    else if(datos[0].strEsInternetLite === "SI")
                                    {
                                        var strRespuesta    = response.responseText;
                                        var arrayRespuesta  = strRespuesta.split("-");
                                        if(arrayRespuesta[0] === "OK")
                                        {
                                            Ext.Msg.alert('Mensaje',arrayRespuesta[1], function(btn){
                                                if(btn === 'ok'){
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje ', strRespuesta );
                                        }
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ',response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });

                        }
                        else{
                            Ext.Msg.alert("Failed","Favor Revise los campos. No pueden haber campos vacios", function(btn){
                                    if(btn=='ok'){
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
                title: 'Confirmar Servicio',
                modal: true,
                width: 1200,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

        }//cierre response
    });
}

/**
 * Funcion que sirve para confirmar los servicios de TN
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 07-04-2016
 * @param data
 * @param idAccion
 * */
function confirmarServicioTn(data, idAccion){
    
    //-------------------------------------------------------------------------------------------

    var confirmarFormPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85, 
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 2
                },
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Datos del Servicio',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 260,
                            height: 180 + +((data.strNGFNubePublica.trim() === 'NO APLICA' || data.strNGFNubePublica.trim().length === 0) ? 0 : 20)
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Servicio
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'servicio',
                                        fieldLabel: 'Servicio',
                                        value: data.login,
                                        readOnly: true
                                    },
                                    // Fin Campo Servicio
                                    //-------------------------------
                                    // Campo Estado
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'estado',
                                        fieldLabel: 'Estado',
                                        value: typeof data.estado !== 'undefined' ? data.estado : 'EnPruebas',
                                        readOnly: true
                                    },
                                    // Fin Campo Estado
                                    //-------------------------------
                                    // Campo Producto
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto,
                                        readOnly: true
                                    },
                                    // Fin Campo Producto
                                    //-------------------------------
                                    // Campo Observación
                                    { width: '10%', border: false},
                                    {
                                        id : 'observacionActivarServicio',
                                        xtype: 'textarea',
                                        name: 'observacionActivarServicio',
                                        fieldLabel: 'Observación',
                                        value: "",
                                        readOnly: false,
                                        required : true,
                                    },
                                    // Fin Campo Observación
                                    //-------------------------------
                                    // Campo Nube Publica
                                    {
                                        xtype: 'textfield',
                                        id: 'strNGFNubePublica',
                                        name: 'strNGFNubePublica',
                                        hidden: +(data.strNGFNubePublica.trim() === 'NO APLICA' || data.strNGFNubePublica.trim().length === 0),
                                        fieldLabel: 'Nube Pública',
                                        value: data.strNGFNubePublica,
                                        readOnly: true
                                    }
                                    // Fin Campo Nube Publica
                                    //-------------------------------
                                ]
                            }

                        ]
                    }//cierre de la informacion servicio/producto
                ],
                buttons: [{
                    text: 'Confirmar',
                    formBind: true,
                    handler: function(){
                        
                        var observacion = Ext.getCmp('observacionActivarServicio').getValue();
                       
                        var validacion=true;
                        if(observacion==""){
                            validacion=false;
                        }
                        
                        //Valida Caracteristicas Adicionales y si debe validar datos tecnicos  
                        if (data.arrayCaractAdicionalesServicios && data.boolVisualizarDatosTecnicos == "S")
                        {
                            data.arrayCaractAdicionalesServicios  .forEach((el, index) => {
                                let itemVal = Ext.getCmp(el['DESCRIPCION_CARACTERISTICA'].toLowerCase()).getValue();
                                data.arrayCaractAdicionalesServicios  [index]['FIELD_VALUE'] = itemVal;
                            });
                        }
                        
                        if(validacion){
                            Ext.get(confirmarFormPanel.getId()).mask('Confirmando servicio...');
                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio                 : data.idServicio,
                                    idProducto                 : data.productoId,
                                    observacionActivarServicio : observacion,
                                    idAccion                   : idAccion,
                                    strNombreTecnico           : data.descripcionProducto,
                                    arrayCaractAdicionales     : JSON.stringify(data.arrayCaractAdicionalesServicios)
                                },
                                success: function(response){
                                    Ext.get(confirmarFormPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                                        win.destroy();
                                        Ext.Msg.alert('Mensaje','Se confirmo el Servicio: '+data.login, function(btn){
                                            if(btn=='ok'){
                                                store.load();                                    
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ','Error:' + response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(confirmarFormPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            }); 
                        }
                        else{
                            Ext.Msg.alert("Failed","Favor Revise los campos. No pueden haber campos vacios", function(btn){
                                    if(btn=='ok'){
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
            
            //Valida Caracteristicas Adicionales y si debe mostrar datos tecnicos           
            if (data.arrayCaractAdicionalesServicios && data.boolVisualizarDatosTecnicos == "S")
            {
                //validamos si el producto debe ingresar datos adicionales
                confirmarFormPanel.add([
                    { width: '10%', border: false},
                        //informacion del servicio/producto
                        {
                            xtype: 'fieldset',
                            title: 'Ingreso de Datos Técnicos',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 260,
                                height: 250
                            },
                            items: [
                                {
                                    xtype: 'container',
                                    id: 'fs_infoCaracteristicasAdicionales',
                                    layout: {
                                        type: 'table',
                                        columns: 1,
                                        align: 'stretch'
                                    },
                                    items: [
                                    ]
                                }
                            ]
                        }//cierre de la informacion servicio/producto
                ]);
                
                data.arrayCaractAdicionalesServicios.forEach((el, index) => {
                    Ext.getCmp('fs_infoCaracteristicasAdicionales').insert(4 + index,
                        [
                            {
                                xtype: el['XTYPE'],
                                id: el['DESCRIPCION_CARACTERISTICA'].toLowerCase(),
                                name: el['DESCRIPCION_CARACTERISTICA'].toLowerCase(),
                                fieldLabel: titleCase(el['LABEL']),
                                displayField: "",
                                value: "",
                                allowBlank: false,
                                width: '25%'
                            }
                        ]
                    );
                });
            }
            
            var win = Ext.create('Ext.window.Window', {
                title: 'Confirmar Servicio',
                modal: true,
                width: 300,
                height: 300,
                closable: true,
                layout: 'fit',
                items: [confirmarFormPanel]
            }).show();
}


/**
 * Función que sirve para confirmar los servicios de seguridad logica de TN
 *
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 19-02-2019
 * @param data
 * @param idAccion
 * */
function confirmarServicioSeguridadLogica(data, idAccion)
{

    tieneNubePublica = esNGFirewall =  false
    // data.nombreProducto = 'otro'
    // data.strNGFNubePublica = 'NINGUNO'


    if(data.strNGFNubePublica.length > 0 && data.strNGFNubePublica !== 'NINGUNO')
    {
        tieneNubePublica =  '1';
    }


    if(data.nombreProducto === 'SECURITY NG FIREWALL')
    {
        esNGFirewall =  '1';
    }


    var idOnt = null;
    if(data.idOnt != "" && data.idOnt != null)
    {
        idOnt = data.idOnt;
    }
    var storeInterfacesPorEstadoYElementoOnt = new Ext.data.Store({
            pageSize: 100,
            proxy: {
                type: 'ajax',
                url : getInterfacesPoEstadoYElemento,
                extraParams: {
                    estadoInterface: "connected",
                    elementoId:       idOnt
                },
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                  {name:'idInterface', mapping:'nombreInterface'}
                ]
            });
    var panelSeguridadLogica = Ext.create('Ext.form.Panel', {
                bodyPadding: 1,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 1
                },
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Datos del Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 300,
                            height: 150 + (esNGFirewall ? 30 : 0),
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Servicio
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'servicioSeguridad',
                                        name: 'servicioSeguridad',
                                        fieldLabel: 'Servicio',
                                        value: data.login,
                                        readOnly: true
                                    },
                                    // Fin Campo Servicio
                                    //-------------------------------
                                    // Campo Estado
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'estadoSeguridad',
                                        name: 'estadoSeguridad',
                                        fieldLabel: 'Estado',
                                        value: 'EnPruebas',
                                        readOnly: true
                                    },
                                    // Fin Campo Estado
                                    //-------------------------------
                                    // Campo Producto
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'productoSeguridad',
                                        name: 'productoSeguridad',
                                        width: 300,
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto,
                                        readOnly: true
                                    },
                                    // Fin Campo Producto
                                    //-------------------------------
                                    // Campo Observación
                                    { width: '10%', border: false},
                                    {
                                        id : 'observacionSeguridad',
                                        xtype: 'textarea',
                                        name: 'observacionSeguridad',
                                        width: 300,
                                        fieldLabel: '* Observación',
                                        value: "",
                                        readOnly: false,
                                        required : true,
                                    },
                                    // Fin Campo Observación
                                    //-------------------------------
                                    // Campo Nube Publica
                                    {
                                        xtype: 'textfield',
                                        id: 'strNGFNubePublica',
                                        name: 'strNGFNubePublica',
                                        width: 300,
                                        hidden: !esNGFirewall, 
                                        fieldLabel: 'Nube Pública',
                                        value: data.strNGFNubePublica,
                                        readOnly: true
                                    }
                                    // Fin Campo Nube Publica
                                    //-------------------------------
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Ont',
                        hidden: data.esServicioRequeridoSafeCity != "S",
                        defaultType: 'textfield',
                        defaults: {
                            width: 300,
                            height: 80
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Ont
                                    { width: '10%', border: false},
                                    {
                                        xtype:          'textfield',
                                        id:             'nombreOnt',
                                        name:           'nombreOnt',
                                        fieldLabel:     'Nombre',
                                        displayField:   "",
                                        value:          data.nombreOnt,
                                        readOnly:       true,
                                        width:          300
                                    },
                                    { width: '20%', border: false},
                                    {
                                        xtype:          'textfield',
                                        id:             'serieOnt',
                                        name:           'serieOnt',
                                        fieldLabel:     'Serie',
                                        displayField:   "",
                                        value:          data.serieOnt,
                                        readOnly:       true,
                                        width:          300
                                    },
                                    { width: '20%', border: false},
                                    {
                                        queryMode:      'local',
                                        xtype:          'combobox',
                                        id:             'puertosOnt',
                                        name:           'puertosOnt',
                                        fieldLabel:     '* Puertos',
                                        displayField:   'idInterface',
                                        value:          '-Seleccione-',
                                        valueField:     'nombreInterface',
                                        store:          storeInterfacesPorEstadoYElementoOnt,
                                        required:       data.esServicioRequeridoSafeCity === "S",
                                        width:          300
                                    }
                                ]
                            }

                        ]
                    },//cierre de la informacion ont
                    {
                        xtype: 'fieldset',
                        title: 'Datos del Equipo',
                        defaultType: 'textfield',
                        hidden: (esNGFirewall && tieneNubePublica),
                        defaults: {
                            width: 260,
                            height: 195
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Servicio
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Propiedad equipo',
                                        id: 'propiedadEquipo',
                                        value: 'T',
                                        editable: false,
                                        forceSelection: true,
                                        store: [
                                            ['T', 'TELCONET'],
                                            ['C', 'CLIENTE']
                                        ],
                                        width: 300,
                                        listeners: {
                                            select: function(combo)
                                            {
                                                Ext.getCmp('serieEquipo').setValue = "";
                                                Ext.getCmp('descEquipo').setValue = "";
                                                Ext.getCmp('modeloEquipo').setValue = "";
                                                Ext.getCmp('macEquipo').setValue = "";
                                                Ext.getCmp('ipEquipo').setValue = "";
                                                Ext.getCmp('serieEquipo').setRawValue("");
                                                Ext.getCmp('descEquipo').setRawValue("");
                                                Ext.getCmp('modeloEquipo').setRawValue("");
                                                Ext.getCmp('macEquipo').setRawValue("");
                                                Ext.getCmp('ipEquipo').setRawValue("");

                                                if(combo.getRawValue() === "CLIENTE")
                                                {
                                                    Ext.getCmp('descEquipo').setReadOnly(false);
                                                    Ext.getCmp('modeloEquipo').setReadOnly(false);
                                                    Ext.getCmp('macEquipo').setReadOnly(false);
                                                }
                                                else
                                                {
                                                    Ext.getCmp('descEquipo').setReadOnly(true);
                                                    Ext.getCmp('modeloEquipo').setReadOnly(true);
                                                    Ext.getCmp('macEquipo').setReadOnly(true);
                                                }
                                            }
                                        }
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'serieEquipo',
                                        width: 300,
                                        id: 'serieEquipo',
                                        fieldLabel: '* Serie',
                                        value: '',
                                        readOnly: false,
                                        maxLength: 40,
                                        listeners: {
                                            blur: function(serie){

                                                if(Ext.getCmp('propiedadEquipo').getRawValue() === "TELCONET")
                                                {
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe:          serie.getValue(),
                                                            modeloElemento:    '',
                                                            estado:            'PI',
                                                            bandera:           'ActivarServicio',
                                                            //comprobarInterfaz: 'SI',
                                                            //tipoElemento:      'CPE',
                                                            idServicio:        data.idServicio
                                                        },
                                                        success: function(response){
                                                            var respuesta     = response.responseText.split("|");
                                                            var status        = respuesta[0];
                                                            var mensaje       = respuesta[1].split(",");
                                                            strEsCpeExistente = respuesta[2];
                                                            var descripcion = mensaje[0];
                                                            var macCpe      = mensaje[1];
                                                            var modeloCpe   = mensaje[2];

                                                            Ext.getCmp('descEquipo').setValue = '';
                                                            Ext.getCmp('descEquipo').setRawValue('');

                                                            Ext.getCmp('modeloEquipo').setValue = '';
                                                            Ext.getCmp('modeloEquipo').setRawValue('');

                                                            Ext.getCmp('macEquipo').setValue = '';
                                                            Ext.getCmp('macEquipo').setRawValue('');

                                                            if(status=="OK")
                                                            {
                                                                Ext.getCmp('descEquipo').setValue = descripcion;
                                                                Ext.getCmp('descEquipo').setRawValue(descripcion);

                                                                Ext.getCmp('modeloEquipo').setValue = modeloCpe;
                                                                Ext.getCmp('modeloEquipo').setRawValue(modeloCpe);

                                                                Ext.getCmp('macEquipo').setValue = macCpe;
                                                                Ext.getCmp('macEquipo').setRawValue(macCpe);
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        }//clear
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'descEquipo',
                                        name: 'descEquipo',
                                        width: 300,
                                        fieldLabel: '* Descripción',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 1500
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'modeloEquipo',
                                        name: 'modeloEquipo',
                                        width: 300,
                                        fieldLabel: '* Modelo',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 100
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'macEquipo',
                                        name: 'macEquipo',
                                        width: 300,
                                        fieldLabel: '* Mac',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 100
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'ipEquipo',
                                        name: 'ipEquipo',
                                        width: 300,
                                        fieldLabel: '* Ip',
                                        value: '',
                                        readOnly: false,
                                        validator: function(v) {
                                            let regexIPValida = /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/
                                            if(!(esNGFirewall && tieneNubePublica && permisoActivarServiciotNGF))
                                            {
                                                return  regexIPValida.test(v) ? true : 'Formato inválido';
                                            }
                                            else
                                            {
                                                return true;
                                            }
                                        },
                                        maxLength: 16
                                    },
                                    { width: '10%', border: false},
                                    {
                                        height: 10,
                                        layout: 'form',
                                        border: false,
                                        items:
                                        [
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'label',
                                        forId: 'labelCamposObligatorios',
                                        text: '* Campos obligatorios'
                                    }
                                ]
                            }

                        ]
                    },
                    // FIELDSET PARA PRODUCTOS NGFIREWALL
                    {
                        xtype: 'fieldset',
                        title: 'Datos del Equipo',
                        defaultType: 'textfield',
                        hidden: !(esNGFirewall && tieneNubePublica),
                        defaults: {
                            width: 260,
                            height: 150
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Servicio
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: '* Administración',
                                        id: 'administracionNGF',
                                        name: 'administracionNGF',
                                        displayField : 'desc',
                                        valueField: 'value',
                                        editable: false,
                                        forceSelection: true,
                                        mode: 'local',
                                        store: new Ext.data.Store({
                                            total: 'total',
                                            proxy: {
                                                type: 'ajax',
                                                url : getAdminParametersDet,
                                                timeout: 3000000,
                                                extraParams: {
                                                    nombreParametro: 'LISTA_ADMINISTRACION_PRODUCTO_NGFIREWALL',
                                                },
                                                reader: {
                                                    type: 'json',
                                                    totalProperty: 'total',
                                                    root: 'items'
                                                },
                                            },
                                            fields:['desc', 'value']
                                        }),
                                        width: 300,
                                        hidden: !(data.nombreProducto === 'SECURITY NG FIREWALL' && permisoActivarServiciotNGF) 
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'ipEquipoNGF',
                                        name: 'ipEquipoNGF',
                                        width: 300,
                                        fieldLabel: '* Ip/FQDN',
                                        value: '',
                                        readOnly: false,
                                        maxLength: 20,
                                        maxLengthText: 'El máximo de caracteres permitidos para este campo es 20'
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'puertoAdminWebNGF',
                                        name: 'puertoAdminWebNGF',
                                        width: 300,
                                        fieldLabel: '* Puerto administración Web',
                                        value: '',
                                        readOnly: false,
                                        validator: function(v) {
                                            if((esNGFirewall && tieneNubePublica && permisoActivarServiciotNGF))
                                            {
                                                return  /^[0-9]+$/.test(v)? true : 'Se permiten solo números';
                                            }
                                            else
                                            {
                                                return true;
                                            }
                                        },
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'serialNGF',
                                        name: 'serialNGF',
                                        width: 300,
                                        fieldLabel: 'Serial licencia',
                                        value: '',
                                        readOnly: false,
                                    },
                                    
                                    { width: '10%', border: false},
                                    {
                                        height: 10,
                                        layout: 'form',
                                        border: false,
                                        items:
                                        [
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'label',
                                        forId: 'labelCamposObligatorios',
                                        text: '* Campos obligatorios'
                                    }
                                ]
                            }

                        ]
                    }
                ],
                buttons: [{
                    text: 'Confirmar',
                    formBind: true,
                    handler: function(){

                        var observacion          = Ext.getCmp('observacionSeguridad').getValue().trim();
                        var strSerieEquipo       = Ext.getCmp('serieEquipo').getValue().trim();
                        var strDescripcionEquipo = Ext.getCmp('descEquipo').getValue().trim();
                        var strModeloEquipo      = Ext.getCmp('modeloEquipo').getValue().trim();
                        var strMacEquipo         = Ext.getCmp('macEquipo').getValue().trim();
                        var ipEquipo             = Ext.getCmp('ipEquipo').getValue().trim();
                        var puertosOnt           = Ext.getCmp('puertosOnt').getRawValue();
                        var serialNGF            = Ext.getCmp('serialNGF').getValue().trim();
                        var puertoAdminWebNGF    = Ext.getCmp('puertoAdminWebNGF').getValue().trim();
                        var administracionNGF    = Ext.getCmp('administracionNGF').getRawValue();
                        var propiedadEquipo      = Ext.getCmp('propiedadEquipo').getValue().trim();
                        var validacion=true;


                        //SE VALIDA SI ES PRODUCTO NGFIREWALL
                        if(esNGFirewall && permisoActivarServiciotNGF && tieneNubePublica)
                        {


                            strSerieEquipo       = 'ROUTER CLIENTE';
                            strDescripcionEquipo = 'ROUTER CLIENTE';
                            strModeloEquipo      = 'ROUTER CLIENTE';
                            strMacEquipo         = 'ROUTER CLIENTE';
                            propiedadEquipo      = 'C';
                            ipEquipo = Ext.getCmp('ipEquipoNGF').getValue().trim();

                            //SE VALIDAN LOS CAMPOS ADICIONALES CUANDO EL PRODUCTO ES NGFIREWALL
                            if(serialNGF == '' || puertoAdminWebNGF == '' || administracionNGF == '')
                            {
                                validacion=false;
                            }
                        }




                        if(observacion == "" || strDescripcionEquipo == "" || strModeloEquipo == "" || strMacEquipo == "" || strSerieEquipo == ""
                           || ipEquipo == "" || propiedadEquipo == "")
                        {
                            validacion=false;
                        }

                        if(data.esServicioRequeridoSafeCity === "S" && puertosOnt == "-Seleccione-")
                        {
                            validacion=false;
                        }


                        if(validacion){

                            Ext.get(panelSeguridadLogica.getId()).mask('Confirmando servicio...');
                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    idServicio                 : data.idServicio,
                                    idProducto                 : data.productoId,
                                    observacionActivarServicio : "<b>Observación:</b> "+observacion,
                                    serieEquipo                : strSerieEquipo,
                                    descEquipo                 : strDescripcionEquipo,
                                    modeloEquipo               : strModeloEquipo,
                                    macEquipo                  : strMacEquipo,
                                    ipEquipo                   : ipEquipo,
                                    idAccion                   : idAccion,
                                    strNombreTecnico           : data.descripcionProducto,
                                    registroEquipo             : data.registroEquipo,
                                    propiedadEquipo            : propiedadEquipo,
                                    esServicioRequeridoSafeCity : data.esServicioRequeridoSafeCity,
                                    idOnt                      : idOnt,
                                    puertosOnt                 : puertosOnt,
                                    serialNGF,
                                    puertoAdminWebNGF,
                                    administracionNGF,
                                    permisoActivarServiciotNGF,
                                    tieneNubePublica,
                                    strNGFNubePublica: data.strNGFNubePublica
                                },
                                success: function(response){
                                    Ext.get(panelSeguridadLogica.getId()).unmask();
                                    if(response.responseText == "OK"){
                                        win.destroy();
                                        Ext.Msg.alert('Mensaje','Se confirmó el servicio: '+data.login, function(btn){
                                            if(btn=='ok'){
                                                store.load();
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ',response.responseText);
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(panelSeguridadLogica.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });
                        }
                        else{
                            Ext.Msg.alert("Alerta","Favor ingresar campos obligatorios", function(btn){
                                    if(btn=='ok'){
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
                title: 'Confirmar Servicio',
                modal: true,
                width: 350,
                closable: true,
                layout: 'fit',
                items: [panelSeguridadLogica]
            }).show();

            if(data.esServicioRequeridoSafeCity === "S")
            {
                storeInterfacesPorEstadoYElementoOnt.load();
            }
}


/**
 * Funcion que sirve para confirmar los servicios Smart Space de TN
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 16-01-2017
 * @param data
 * @param idAccion
 * */
function confirmarServicioSmartSpaceTn(data, idAccion)
{
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });
                            
    storeCircuitosL1 = Ext.create('Ext.data.Store', {

        model: 'ListaParametrosDetModel',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: getSegmentosPrimerNivel,
            extraParams: {
            strNombreParametro: 'CIRCUITO_L1_SMART_SPACE',
            strBuscaCabecera:   'SI',
            strDescripcionDet:  'CIRCUITO_L1_SMART_SPACE'
            },
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroDetResult',
                totalProperty: 'intTotalParametros'
            }
        }
    });

    storeCircuitosL2 = Ext.create('Ext.data.Store', {
        model: 'ListaParametrosDetModel',
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url: getSegmentosPrimerNivel,
            extraParams: {
            strNombreParametro: 'CIRCUITO_L1_SMART_SPACE',
            strBuscaCabecera:   'SI'
            },
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroDetResult',
                totalProperty: 'intTotalParametros'
            }
        }
    });
                        
    var confirmarFormPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85, 
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    columns: 2
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Datos del Servicio',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 260,
                            height: 220
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'servicio',
                                        fieldLabel: 'Servicio',
                                        value: data.login,
                                        readOnly: true
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'estado',
                                        fieldLabel: 'Estado',
                                        value: data.estado,
                                        readOnly: true
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto,
                                        readOnly: true
                                    },
                                    { width: '10%', border: false},
                                    {
                                        id : 'observacionActivarServicio',
                                        xtype: 'textarea',
                                        name: 'observacionActivarServicio',
                                        fieldLabel: 'Observación',
                                        value: "",
                                        readOnly: false,
                                        required : true,
                                    },
                                    { width: '10%', border: false},
                                    {
                                        queryMode:      'local',
                                        xtype:          'combobox',
                                        id:             'circuitoL1',
                                        name:           'circuitoL1',
                                        fieldLabel:     'Circuito L1',
                                        displayField:   'strValor1',
                                        required:       true,
                                        editable:       false,
                                        valueField:     'intIdParametroDet',
                                        loadingText:    'Buscando...',
                                        store:          storeCircuitosL1,
                                        listeners: 
                                        {
                                            select: function(combo)
                                            {
                                                Ext.getCmp('circuitoL2').clearValue(); 
                                                Ext.getCmp('btnConfirmar').setDisabled(true);
                                                Ext.getCmp('circuitoL2').setDisabled(true);
                                                Ext.getCmp('circuitoL2').setRawValue("Cargando..."); 
                                                var circuitoL1 = combo.getRawValue();
                                                storeCircuitosL2.load({
                                                    params:
                                                    {
                                                        strDescripcionDet:  circuitoL1
                                                    },
                                                    callback:function()
                                                    {
                                                        if (storeCircuitosL2.getCount() > 0 )
                                                        {
                                                            Ext.getCmp('circuitoL2').clearValue(); 
                                                            Ext.getCmp('circuitoL2').setDisabled(false);
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('circuitoL2').clearValue(); 
                                                            Ext.getCmp('circuitoL2').setDisabled(true);
                                                        }
                                                        Ext.getCmp('btnConfirmar').setDisabled(false);
                                                    }
                                                });

                                            }
                                        }
                                        
                                       
                                    },
                                    { width: '10%', border: false},
                                    {
                                        queryMode:      'local',
                                        disabled:       true,
                                        xtype:          'combobox',
                                        id:             'circuitoL2',
                                        name:           'circuitoL2',
                                        fieldLabel:     'Circuito L2',
                                        displayField:   'strValor1',
                                        editable:       false,
                                        valueField:     'intIdParametroDet',
                                        loadingText:    'Buscando...',
                                        store:          storeCircuitosL2
                                    }
                                ]
                            }

                        ]
                    }//cierre de la informacion servicio/producto
                ],
                buttons: [{
                    id:   'btnConfirmar',
                    text: 'Confirmar',
                    formBind: true,
                    handler: function(){
                        
                        var observacion   = Ext.getCmp('observacionActivarServicio').getValue();
                        var strCircuitoL1 = Ext.getCmp('circuitoL1').getRawValue();
                        var strCircuitoL2 = Ext.getCmp('circuitoL2').getRawValue();
                        var validacion    = true;
                        if (observacion == "")
                        {
                            validacion=false;
                        }
                        if (Ext.isEmpty(strCircuitoL1))
                        {
                            validacion=false;
                        }
                        else
                        {
                            if (storeCircuitosL2.getCount() > 0)
                            {
                                if (Ext.isEmpty(strCircuitoL2))
                                {
                                    validacion=false;
                                }
                            }
                        }
                        
                        
                        if(validacion){
                            Ext.get(confirmarFormPanel.getId()).mask('Cargando...');
                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio                 : data.idServicio,
                                    idProducto                 : data.productoId,
                                    observacionActivarServicio : observacion,
                                    idAccion                   : idAccion,
                                    strEsSmartSpace            : "SI",
                                    strCircuitoL1              : strCircuitoL1,
                                    strCircuitoL2              : strCircuitoL2
                                    
                                },
                                success: function(response){
                                    Ext.get("grid").unmask();
                                    if(response.responseText == "OK"){
                                        win.destroy();
                                        Ext.Msg.alert('Mensaje','Se confirmo el Servicio: '+data.login, function(btn){
                                            if(btn=='ok'){
                                                store.load();                                    
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ','Error:' + response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get("grid").unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            }); 
                        }
                        else{
                            Ext.Msg.alert("Failed","Existen campos vacíos. Por favor revisar.", function(btn){
                                    if(btn=='ok'){
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
                title: 'Confirmar Servicio Smart Space',
                modal: true,
                width: 300,
                closable: true,
                layout: 'fit',
                items: [confirmarFormPanel]
            }).show();
}

/**
 * Funcion que sirve para confirmar la actuvacion del servicio
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 07-04-2016
 * @param data
 * @param idAccion
 * */
function confirmarActivacionTN(data,idAccion){
    Ext.Msg.alert('Mensaje','Se confirmará la activación del servicio, <br>Desea Continuar?', function(btn){
        if(btn==='ok'){
            Ext.get("grid").mask();
            Ext.Ajax.request({
                url: confirmarActivacionBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idProducto: data.productoId,
                    perfil: data.perfilDslam,
                    login: data.login,
                    capacidad1: data.capacidadUno,
                    capacidad2: data.capacidadDos,
                    serieCpe: "",
                    codigoArticulo: "",
                    macCpe: "",
                    numPc: "",
                    ssid: "",
                    password: "password",
                    modoOperacion: "modoOperacion",
                    jsonCaracteristicas: "jsonCaracteristicas",
                    observacionCliente: "observacion",
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText === "OK"){
                        Ext.Msg.alert('Mensaje','Se Confirmo la Activacion', function(btn){
                            if(btn === 'ok'){
                                win.destroy();
                                store.load();
                            }
                        });
                    }
                },
                failure: function(result)
                {
                    Ext.get("grid").unmask();
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });         
}

function titleCase(str) {
    return str.toLowerCase().split(' ').map(function(word) {
        return (word.charAt(0).toUpperCase() + word.slice(1));
    }).join(' ');
}
