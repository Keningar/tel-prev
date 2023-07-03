function confirmarServicioIp(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Confirmar el Servicio de Ip Publica', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Confirmando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioIpPublicaBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se confirmó el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo confirmar el servicio!' );
                    }

                }

            });
        }
    });
}

function activarIpFijaMD(data,grid){
    var readOnlyValor = true;
    var macIpFijaValor = data.macIpFija;
    if (data.tieneIpFijaActiva) {
        readOnlyValor = false;
        macIpFijaValor = data.macIpFija;
    }
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 4,
        waitMsgTarget: true,
        buttonAlign: 'center',
        items: [

        {
            xtype: 'fieldset',
            title: 'Mac',
            items: [
                {
                    xtype: 'textfield',
                    id: 'macIpFijaAdicional',
                    name: 'macIpFijaAdicional',
                    fieldLabel: 'aaaa.bbbb.cccc',
                    displayField: macIpFijaValor,
                    value: macIpFijaValor,
                    readOnly: readOnlyValor
                }
            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Activar',
            formBind: true,
            handler: function() {
                var macIpFijaAdicional = Ext.getCmp('macIpFijaAdicional').getValue();
                var regex = /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;

                macIpFijaAdicional = macIpFijaAdicional.replace(/\s/g, '');

                if (macIpFijaAdicional == "" || !macIpFijaAdicional.match(regex)) {
                    Ext.Msg.alert('Error', 'Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                    return;
                }
                    
                Ext.Msg.alert('Mensaje', 'Esta seguro que desea Activar la(s) Ip(s) Fija(s) con la mac <b>"' + macIpFijaAdicional + '</b>"?', 
                function(btn) {
                    if (btn == 'ok') {
                        Ext.get(formPanel.getId()).mask('Activando Ip(s) Fija(s)...');
                        Ext.Ajax.request({
                            url: activarIpFija,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                idServicio: data.idServicio,
                                idServicioRef: data.idServicioRefIpFija,
                                macIpFija: macIpFijaAdicional,
                                tieneIpFijaActiva: data.tieneIpFijaActiva
                            },
                            success: function(response) {
                                Ext.get(formPanel.getId()).unmask();
                                if (response.responseText == "OK") {
                                    Ext.Msg.alert('Mensaje', 'Se Activo la Ip(s) Fija(s) con exito!', function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                            win.destroy();
                                        }
                                    });
                                }
                                else {
                                    Ext.Msg.alert('Error ', response.responseText);
                                }
                            },
                            failure: function(result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', result.statusText);
                            }
                        });
                    }
                });
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Datos de Activación',
        modal: true,
        width: 300,
        closable: true,
        items: [formPanel]
    }).show();
}

/**
 * Funcion que sirve para mostrar una pantalla para migrar una ip adicional
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 15-03-2015
 * */
function migrarIpHuawei(data){
    var readOnlyValor = true;
    var disabledIp    = false;
    var macIpFijaValor = data.macIpFija;
    if (data.tieneIpFijaActiva) {
        readOnlyValor  = false;
        macIpFijaValor = data.macIpFija;
    }
    
    if (data.ipReservada != null)
    {
        disabledIp = true;
    }
   
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 4,
        waitMsgTarget: true,
        buttonAlign: 'center',
        items: [

        {
            xtype: 'fieldset',
            title: 'Mac',
            items: [
                {
                    xtype: 'textfield',
                    id: 'ipFija',
                    name: 'ipFija',
                    fieldLabel: 'Ip Fija',
                    displayField: "",
                    value:data.ipReservada,
                    readOnly:disabledIp
                },
                {
                    xtype: 'textfield',
                    id: 'macIpFija',
                    name: 'macIpFija',
                    fieldLabel: 'aaaa.bbbb.cccc',
                    displayField: macIpFijaValor,
                    value: macIpFijaValor,
                    readOnly: readOnlyValor
                }
            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Migrar',
            formBind: true,
            handler: function() {
                var macIpFija = Ext.getCmp('macIpFija').getValue();
                var ipFija = Ext.getCmp('ipFija').getValue();
                var regex = /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
                var regexIp = /^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;

                macIpFija = macIpFija.replace(/\s/g, '');
                ipFija = ipFija.replace(/\s/g, '');

                if (macIpFija == "" || !macIpFija.match(regex)) {
                    Ext.Msg.alert('Error', 'Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                    return;
                }
                
                if(ipFija =="" || !ipFija.match(regexIp)){
                    Ext.Msg.alert('Error', 'Campo de Ip Fija incorrecto, favor ingrese una ip valida!');
                    return;
                }
                    
                Ext.Msg.alert('Mensaje', 'Esta seguro que desea Migrar la Ip Fija con la mac <b>"' + macIpFija + '</b>"?', function(btn) {
                    if (btn == 'ok') {
                        Ext.get(formPanel.getId()).mask('Migrando Ip Fija...');
                        Ext.Ajax.request({
                            url: migrarIpFijaBoton,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                idServicio: data.idServicio,
                                ipFija: ipFija,
                                macIpFija: macIpFija,
                                tieneIpFijaActiva: data.tieneIpFijaActiva,
                                ipReservada : data.ipReservada
                            },
                            success: function(response) {
                                Ext.get(formPanel.getId()).unmask();
                                if (response.responseText == "OK") {
                                    Ext.Msg.alert('Mensaje', 'Se Migro la Ip Fija con exito!', function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                            win.destroy();
                                        }
                                    });
                                }
                                else {
                                    Ext.Msg.alert('Error ', response.responseText);
                                }
                            },
                            failure: function(result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', result.statusText);
                            }
                        });
                    }
                });
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Migrar Ip Fija',
        modal: true,
        width: 300,
        closable: true,
        items: [formPanel]
    }).show();
}

function cambiarMacIpFijaMD(data,grid){
    var readOnlyValor = true;
    var macIpFijaValor = data.macIpFija;

    if(macIpFijaValor=="")
    {
        readOnlyValor = false;
    }
        
    var formPanel = Ext.create('Ext.form.Panel', 
    {
        bodyPadding: 4,
        waitMsgTarget: true,
                buttonAlign: 'center',
        items: 
        [
            {
                xtype: 'fieldset',
                title: 'Mac Actual',
                items: 
                [
                    {
                        xtype: 'textfield',
                        id:'macIpFijaActual',
                        name: 'macIpFijaActual',
                        fieldLabel: 'aaaa.bbbb.cccc',
                        displayField: macIpFijaValor,
                        value: macIpFijaValor,
                        readOnly : readOnlyValor
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Mac Nueva',
                items: 
                [
                    {
                        xtype: 'textfield',
                        id:'macIpFijaNueva',
                        name: 'macIpFijaNueva',
                        fieldLabel: 'aaaa.bbbb.cccc',
                        displayField: "",
                        value: ""
                    }
                ]
            }
        ],
        buttons: 
        [
            {
                text: 'Ejecutar',
                formBind: true,
                handler: function()
                {
                    var macIpFijaActual = Ext.getCmp('macIpFijaActual').getValue();
                    var macIpFijaNueva  = Ext.getCmp('macIpFijaNueva').getValue();

                    macIpFijaActual = macIpFijaActual.replace(/\s/g,'');
                    macIpFijaNueva  = macIpFijaNueva.replace(/\s/g,'');

                    var regex=/^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;

                    if(macIpFijaActual=="" || !macIpFijaActual.match(regex))
                    {
                        Ext.Msg.alert('Error','Formato de Mac Actual Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                        return;
                    }
                    if(macIpFijaNueva=="" || !macIpFijaNueva.match(regex))
                    {
                        Ext.Msg.alert('Error','Formato de Mac Nueva Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                        return;
                    }

                    Ext.Msg.alert('Mensaje','Esta seguro que desea Cambiar la Mac Actual:<b>"'+macIpFijaActual+'"</b> \n\
                                            con la Mac Nueva:<b>"'+macIpFijaNueva+'"</b>?', function(btn1)
                    {
                        if(btn1=='ok')
                        {
                            Ext.get(formPanel.getId()).mask('Cambiando Mac de Ip(s) Fija(s)...');
                            Ext.Ajax.request
                            ({
                                url: cambiarMacIpFija,
                                method: 'post',
                                timeout: 400000,
                                params: 
                                { 
                                    idServicio: data.idServicio,
                                    idServicioRef: data.idServicioRefIpFija,
                                    tieneIpFijaActiva: data.tieneIpFijaActiva,
                                    macIpFija: macIpFijaActual,
                                    macIpFijaNueva: macIpFijaNueva
                                },
                                success: function(response)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText == "OK")
                                    {
                                        Ext.Msg.alert('Mensaje','Se Cambió la Mac de la(s) Ip(s) Fija(s) con exito!', function(btn)
                                        {
                                            if(btn=='ok')
                                            {
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Error ',response.responseText );
                                    }
                                    win.destroy();
                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ',result.statusText);
                                }
                            }); 
                        }//if(btn=='ok')
                    });
                }//handler: function()
            },
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });

    var win = Ext.create('Ext.window.Window', 
    {
        title: 'Datos de Cambio de Mac',
        modal: true,
        width: 300,
        closable: true,
        items: [formPanel]
    }).show();
}

function cancelarIpFijaMD(data,idAccion){
    var readOnlyValor = true;
    var macIpFijaValor = data.macIpFija;
        
    if(macIpFijaValor=="")
    {
        readOnlyValor = false;
    }

    var storeMotivos = new Ext.data.Store({  
                pageSize: 50,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : getMotivos,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        accion: "cancelarCliente"
                    }
                },
                fields:
                    [
                      {name:'idMotivo', mapping:'idMotivo'},
                      {name:'nombreMotivo', mapping:'nombreMotivo'}
                    ]
            });
            
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 4,
        waitMsgTarget: true,
        buttonAlign: 'center',
        items: [

        {
            xtype: 'fieldset',
            title: 'Mac',
            items: [
                {
                    xtype: 'textfield',
                    id:'macIpFijaAdicional',
                    name: 'macIpFijaAdicional',
                    fieldLabel: 'aaaa.bbbb.cccc',
                    displayField: macIpFijaValor,
                    value: macIpFijaValor,
                    readOnly : readOnlyValor,
                },
            ]
        },//cierre interfaces cpe
        {
                
            xtype: 'fieldset',
            title: 'Motivo Cancelacion',
            defaultType: 'textfield',
            defaults: {
                width: 250
            },
            items: [

                {
                    xtype: 'container',
                    layout: {
                        type: 'table',
                        columns: 2,
                        align: 'stretch'
                    },
                    items: [

                        { width: '10%', border: false},
                        {
                            xtype: 'combo',
                            id:'comboMotivosIp',
                            name: 'comboMotivosIp',
                            store: storeMotivos,
                            fieldLabel: 'Motivo',
                            displayField: 'nombreMotivo',
                            valueField: 'idMotivo',
                            queryMode: 'local'
                        }
                    ]
                }
            ]
        },
        ],
        buttons: [{
            text: 'Ejecutar',
            formBind: true,
            handler: function(){
                var macIpFijaAdicional = Ext.getCmp('macIpFijaAdicional').getValue();
                var regex=/^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
                macIpFijaAdicional = macIpFijaAdicional.replace(/\s/g,'');

                if(macIpFijaAdicional=="" || !macIpFijaAdicional.match(regex)){
                    Ext.Msg.alert('Error','Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                    return;
                }
                var motivo = Ext.getCmp('comboMotivosIp').getValue();
                var validacion = false;
                   
                if(motivo!=null)
                {
                    validacion=true;
                }
                    
                if(validacion)
                {
                    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar la(s) Ip(s) Fija(s) con la mac <b>"'+macIpFijaAdicional+'"</b>?',
                        function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).mask('Cancelando Ip(s) Fija(s)...');
                                Ext.Ajax.request({
                                    url: cancelarIpFija,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio:    data.idServicio,
                                        idServicioRef: data.idServicioRefIpFija,
                                        macIpFija:     macIpFijaAdicional,
                                        motivo:        motivo,
                                        idAccion:      idAccion
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Canceló la(s) Ip(s) Fija(s) con exito!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{
                                                Ext.Msg.alert('Error ',response.responseText );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Error ',result.statusText);
                                    }
                                }); 
                            }
                        });
                }
                else
                {
                    Ext.Msg.alert("Advertencia","Favor Escoja un Motivo", function(btn){
                            if(btn=='ok'){
                            }
                    });
                }
            }
        },
        {
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Datos de Cancelacion',
        modal: true,
        width: 300,
        closable: true,
//         layout: 'fit',
        items: [formPanel]
    }).show();
}

function agregarIpPublica(data){
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
    //                checkboxToggle: true,
    //                collapsed: true,
            defaults: {
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'ip',
                    name: 'ip',
                    fieldLabel: 'IP',
                    displayField: "",
                    value: "",
                    width: '30%'
                },
                {
                    xtype: 'textfield',
                    id: 'mascara',
                    name: 'mascara',
                    fieldLabel: 'Mascara',
                    displayField: "",
                    value: "",
                    width: '30%'
                }
                ,
                {
                    xtype: 'textfield',
                    id: 'gateway',
                    name: 'gateway',
                    fieldLabel: 'Gateway',
                    displayField: "",
                    value: "",
                    width: '30%'
                }

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    var ip = Ext.getCmp('ip').getValue();
                    var mascara = Ext.getCmp('mascara').getValue();
                    var gateway = Ext.getCmp('gateway').getValue();
                    
                    var str = 0;
                    var str1 = 0;
                    var str2 = 0;
                    var str3 = 0;
                    
                    if(ip=="" || ip==" " || mascara=="" || mascara==" " || gateway=="" || gateway==" "){
                        str=-1;
                    }
                    
                    str1 = mascara.search(",");
                    str2 = gateway.search(",");
                    str3 = mascara.search("/");
                    
                    
                    if(str!=-1 && str1==-1 && str2==-1 && str3==-1){
                        Ext.Msg.alert('Mensaje','Esta seguro que desea agregar los datos al Servicio?', function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).mask('Agregando la ip...');
                                Ext.Ajax.request({
                                    url: agregarIpPublicaServicio,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        ip: ip,
                                        mascara: mascara,
                                        gateway: gateway
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Agrego la Ip Publica!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ','No se pudo agregar la ip al Servicio!' );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                        });
                    }
                    else{
                        alert("Favor revisar los campos!");
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
        title: 'Agregar Ip Publica',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function verInformacionIpPublica(data){
    storeIpPublica = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getIpPublica,
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
              {name:'tipoIp', mapping:'tipoIp'},
              {name:'estado', mapping:'estado'},
              {name:'id', mapping:'id'}
            ]
    });
    
    Ext.define('Ip Publica', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'ip', mapping:'ip'},
              {name:'mascara', mapping:'mascara'},
              {name:'gateway', mapping:'gateway'},
              {name:'tipoIp', mapping:'tipoIp'},
              {name:'estado', mapping:'estado'},
              {name:'id', mapping:'id'}
        ]
    });
    
    //grid de usuarios
    gridIpPublica = Ext.create('Ext.grid.Panel', {
        id:'gridIpPublica',
        store: storeIpPublica,
        columnLines: true,
        columns: [{
            //id: 'nombreDetalle',
            header: 'Ip',
            dataIndex: 'ip',
            width: 120,
            sortable: true
        },
        {
            //id: 'nombreDetalle',
            header: 'Mascara',
            dataIndex: 'mascara',
            width: 120,
            sortable: true
        },
        {
            //id: 'nombreDetalle',
            header: 'Gateway',
            dataIndex: 'gateway',
            width: 120,
            sortable: true
        },
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 60
        },
        {
            xtype: 'actioncolumn',
            header: 'Accion',
            width: 80,
            items: [
                {
                    getClass: function(v, meta, rec) {
                                                if(prefijoEmpresa=="TTCO"){
                                                        var permiso = $("#ROLE_151-837");
                                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                                        //alert(typeof permiso);
                                                        if(!boolPermiso){ 
                                                                return 'button-grid-invisible';
                                                        }
                                                        else{
                                                                if(rec.get('estado') == "Activo"){
                                                                        return 'button-grid-delete';
                                                                }
                                                                else{
                                                                        return 'button-grid-invisible';
                                                                }
                                                        }
                                                }
                    },
                    tooltip: 'Eliminar Ip Publica',
                    handler: function(grid, rowIndex, colIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            eliminarIpPublica(grid.getStore().getAt(rowIndex).data);
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
        height: 200
        //title: 'Historial del Servicio'
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
//                checkboxToggle: true,
//                collapsed: true,
            defaults: {
                width: 530
            },
            items: [

                gridIpPublica

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
                store.load();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Ip Publicas',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function eliminarIpPublica(data){
    Ext.Msg.alert('Mensaje','Esta seguro que desea eliminar la Ip Publica?', function(btn){
        if(btn=='ok'){
            Ext.get(gridIpPublica.getId()).mask('Eliminando Ip Publica...');
            Ext.Ajax.request({
                url: eliminarIpPublicaBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    spc: data.id
                },
                success: function(response){
                    Ext.get(gridIpPublica.getId()).unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se elimino la Ip Publica', function(btn){
                            if(btn=='ok'){
                                
                                storeIpPublica.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo eliminar la Ip Publica!' );
                    }

                }

            });
        }
    });
    
}

function cancelarServicioIpPublica(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Servicio de Ip?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: cancelarServicioIpPublicaBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion:   idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Cancelo el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo cancelar el servicio!' );
                    }

                }

            });
        }
    });
}

function cortarServicioIpPublica(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio de Ip Publica?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: cortarServicioIpPublicaBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Corto el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo Cortar el servicio!' );
                    }

                }

            });
        }
    });
}

function reconectarServicioIpPublica(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconectar el Servicio de Ip Publica?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: reconectarServicioIpPublicaBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Reconectar el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo reconectar el servicio!' );
                    }

                }

            });
        }
    });
}

function agregarMacAddres1(data){
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
    //                checkboxToggle: true,
    //                collapsed: true,
            defaults: {
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'mac',
                    name: 'mac',
                    fieldLabel: 'aaaa.bbbb.cccc',
                    displayField: "",
                    value: "",
                    width: '30%'
                }
            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    var mac = Ext.getCmp('mac').getValue();
                    //Validaciones de mac address
                    var regex=/^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
                    mac = mac.replace(/\s/g,'');
                    
                                                            
                    if(mac=="" || !mac.match(regex)){
                        Ext.Msg.alert('Error','Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                        return;
                    }

                    var validacionMacOnt;
                    if(mac.match("c8b3.73+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("0014.d1+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("000e.dc+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("d8eb.97+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("ccb2.55+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("84c9.b2+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("fc75.16+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("20aa.4b+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("c8d7.19+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("0026.5a+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("48f8.b3+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("b475.0e+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$"))
                    {
                        validacionMacOnt = "correcta";
                    }
                    else{
                        validacionMacOnt = "incorrecta";
                    }                    
                    

                    //if(str!=-1){
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Agregar la MAC Address?' , function(btn){
                            if(btn=='ok'){
                                                                
                                Ext.get(formPanel.getId()).mask('Agregando la Mac Address...');
                                Ext.Ajax.request({
                                    url: agregarMacAddress,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        idProducto: data.productoId,
                                        idEmpresa: data.idEmpresa,
                                        validacionMacOnt: validacionMacOnt,
                                        mac: mac
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        //jvera 06/05/2014 inicio
                                        Ext.Msg.alert('Mensaje ', response.responseText);
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Agregó correctamente!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje', response.responseText);
                                        }
                                       
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                        });
                    /*}
                    else{
                        alert("Favor revisar los campos!");
                    }*/
                }
            },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar Mac Address',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function agregarMacAddres(data){
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
    //                checkboxToggle: true,
    //                collapsed: true,
            defaults: {
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'mac',
                    name: 'mac',
                    fieldLabel: 'aaaa.bbbb.cccc',
                    displayField: "",
                    value: "",
                    width: '30%'
                }
            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    var mac = Ext.getCmp('mac').getValue();
                    //Validaciones de mac address
                    var regex=/^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
                    mac = mac.replace(/\s/g,'');
                    
                                                            
                    if(mac=="" || !mac.match(regex)){
                        Ext.Msg.alert('Error','Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)');
                        return;
                    }

                    var validacionMacOnt;
                    if(mac.match("c8b3.73+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("0014.d1+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("000e.dc+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("d8eb.97+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("ccb2.55+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("84c9.b2+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("fc75.16+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("20aa.4b+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("c8d7.19+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("0026.5a+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("48f8.b3+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                        validacionMacOnt = "correcta";
                    }
                    else if(mac.match("b475.0e+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$"))
                    {
                        validacionMacOnt = "correcta";
                    }
                    else{
                        validacionMacOnt = "incorrecta";
                    }                    
                    

                    //if(str!=-1){
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Agregar la MAC Address?' , function(btn){
                            if(btn=='ok'){
                                
                                console.log(data);
                                
                                Ext.get(formPanel.getId()).mask('Agregando la Mac Address...');
                                Ext.Ajax.request({
                                    url: agregarMacAddress,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        idProducto: data.productoId,
                                        idEmpresa: data.idEmpresa,
                                        validacionMacOnt: validacionMacOnt,
                                        mac: mac
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        //jvera 06/05/2014 inicio
                                        Ext.Msg.alert('Mensaje ', response.responseText);
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Agregó correctamente!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ', response.responseText);
                                        }
                                       
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                        });
                    /*}
                    else{
                        alert("Favor revisar los campos!");
                    }*/
                }
            },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar Mac Address',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}