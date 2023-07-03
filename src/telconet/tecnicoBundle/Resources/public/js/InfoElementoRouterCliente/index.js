var pi = 3.14159265358979;
var sm_a = 6378137.0;
var sm_b = 6356752.314;
var sm_EccSquared = 6.69437999013e-03;

var UTMScaleFactor = 0.9996;


Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var storeCantones = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        proxy: {
            type: 'ajax',
            url : url_getCantones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombre_canton', mapping:'nombre_canton'},
                {name:'id_canton', mapping:'id_canton'}
              ]
    });
    
    
     var storeModelo = new Ext.data.Store({
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_modelo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                tipoElemento: 'ROUTER',
            }
        },
        fields:
            [
                {name: 'descripcion', mapping: 'descripcion'},
                {name: 'id', mapping: 'id'}
            ]
    });
    
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url : url_getEncontrados,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento: '',
                modeloElemento: '',
                feCreacion: '',
                canton:'',
                jurisdiccion:'',
                estado: ''
            }
        },
        fields:
                  [
                    {name:'idElemento', mapping:'idElemento'},
                    {name:'nombreElemento', mapping:'nombreElemento'},
                    {name:'modeloElemento', mapping:'modeloElemento'},
                    {name:'canton', mapping:'canton'},
                    {name:'jurisdiccion', mapping:'jurisdiccion'},
                    {name:'ip', mapping:'ip'},
                    {name:'estadoElemento', mapping:'estadoElemento'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'feCreacion', mapping:'feCreacion'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'latitud', mapping:'latitud'}
                  ],
        autoLoad: true
    });
   
    var pluginExpanded = true;
    
    
    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 294,
        store: store,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
            
        columns:[
                {
                  id: 'idElemento',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'elemento',
                  header: 'Nombre',
                  xtype: 'templatecolumn', 
                  width: 300,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccion}</span></br>\n\
                        <span class="bold">Ubicado En:</span><span>{direccion}</span></br>\n\
                        <tpl>\n\
                        </tpl>'
                
                },
                {
                  header: 'Canton',
                  dataIndex: 'canton',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Fecha Creación',
                  dataIndex: 'feCreacion',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Tipo',
                  dataIndex: 'modeloElemento',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estadoElemento',
                  width: 100,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 155,
                    items: [{
                        getClass: function(v, meta, rec) {

                                    return 'button-grid-show';
                                
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = ""+rec.get('idElemento')+"/show";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_350-4058");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                if (rec.get('estadoElemento')=='Activo' ||rec.get('estadoElemento')=='Pendiente')
                                {
                                    if(boolPermiso){ 
                                        return 'button-grid-edit';
                                    }
                                    else{
                                        return 'icon-invisible';
                                    } 
                                }
                        },
                        tooltip: 'Editar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                                window.location = ""+rec.get('idElemento')+"/edit";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_350-4157");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                if (rec.get('estadoElemento')=='Activo' ||rec.get('estadoElemento')=='Pendiente')
                                {
                                    if(boolPermiso){ 
                                        return 'button-grid-verIpPublica';
                                    }
                                    else{
                                        return 'icon-invisible';
                                    } 
                                }
                        },
                        tooltip: 'Ver Ips',
                        handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                verInformacionIp(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return 'button-grid-Gmaps'
                            },
                            tooltip: 'Ver Mapa',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                latlon = new Array(2);
                                if(rec.get("latitud")!=0 && rec.get("longitud")!=0){
                                    showVerMapa(rec.get("latitud"),rec.get("longitud"));
                                }
                                else
                                    Ext.MessageBox.show({
                                       title: 'Error',
                                       msg: 'Las coordenadas son incorrectas',
                                       buttons: Ext.MessageBox.OK,
                                       icon: Ext.MessageBox.ERROR
                                    });
                            }
                        },
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 930,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: [
                        { width: '10%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '200px'
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'',
                            store: [
                                ['Activo','Activo'],
                                ['Pendiente','Pendiente'],
                                ['Rechazada','Rechazada'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {
                            //xtype: 'combo',
                            id: 'sltCanton',
                            fieldLabel: 'Canton',
                            xtype: 'combobox',
                            typeAhead: true,
                            displayField:'nombre_canton',
                            valueField: 'id_canton',
                            loadingText: 'Buscando ...',
                            store: storeCantones,
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },

                        { width: '20%',border:false}, //final                        
                        {
                            xtype: 'combobox',
                            id: 'sltModelo',
                            fieldLabel: 'Tipo',
                            store: storeModelo,
                            displayField: 'descripcion',
                            valueField: 'id',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        }
                        
                        
                        ],	
        renderTo: 'filtro'
    });

    storeCantones.load({
        callback: function() {
                               
        }
    });                                  
                             

    
});

function buscar(){
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,
        canton: Ext.getCmp('sltCanton').value,
        estado: Ext.getCmp('sltEstado').value,
        modeloElemento: Ext.getCmp('sltModelo').value

    }});
}

function limpiar(){
    Ext.getCmp('sltModelo').value="";
    Ext.getCmp('sltModelo').setRawValue("");
    
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");    
    
    Ext.getCmp('sltCanton').value="";
    Ext.getCmp('sltCanton').setRawValue("");
    
    Ext.getCmp('sltEstado').value="";
    Ext.getCmp('sltEstado').setRawValue("");
    store.load({params: {
        nombre: Ext.getCmp('txtNombre').value,
        canton: Ext.getCmp('sltCanton').value,
        estado: Ext.getCmp('sltEstado').value
    }});
}

 
/************************************************************************ */
/************************** VER MAPA ************************************ */
/************************************************************************ */
function showVerMapa(latitud,longitud){
    winVerMapa="";

    if(latitud!=0 && longitud!=0)
    {
        if (!winVerMapa)
        {
            formPanelMapa = Ext.create('Ext.form.Panel', {
                BodyPadding: 10,
                frame: true,
                items: [
                    {
                        html: "<div id='map_canvas' style='width:575px; height:450px'></div>"
                    }
                ]
            });

            winVerMapa = Ext.widget('window', {
                title: 'Mapa del Elemento',
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: true,
                items: [formPanelMapa]
            });
        }

        winVerMapa.show();
        muestraMapa(latitud, longitud);
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function muestraMapa(vlat,vlong){
    var mapa;
    var ciudad = "";
    var markerPto ;

    if((vlat)&&(vlong)){
        var latlng = new google.maps.LatLng(vlat,vlong);
        var myOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        if(mapa){
            mapa.setCenter(latlng);
        }else{
            mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        }

        if(ciudad=="gye")
            layerCiudad = 'http://157.100.3.122/Coberturas.kml';
        else
            layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng, 
            map: mapa
        });
        mapa.setZoom(17);

    }
} 

function verInformacionIp(rec){
    storeIps = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : url_getIps,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idElemento: rec.get('idElemento')
            }
        },
        fields:
            [
              {name:'idDetalleElemento', mapping:'idDetalleElemento'},
              {name:'estado', mapping:'estado'},
              {name:'ip', mapping:'ip'},
              {name:'vlan', mapping:'vlan'},
              {name:'idIp', mapping:'idIp'}
            ]
    });
    
    
    //grid de usuarios
    gridIps = Ext.create('Ext.grid.Panel', {
        id:'gridIps',
        store: storeIps,
        columnLines: true,
        columns: [{
            header: 'Ip',
            dataIndex: 'ip',
            width: 100,
            sortable: true
        },
        {
            header: 'Vlan',
            dataIndex: 'vlan',
            width: 60
        },        
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 60
        },
        {
            xtype: 'actioncolumn',
            header: 'Accion',
            width: 50,
            items: [
                {
                    getClass: function(v, meta, rec) {
                        return 'button-grid-delete'
                    },
                    tooltip: 'Eliminar Ip',
                    handler: function(grid, rowIndex, colIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            eliminarIp(grid.getStore().getAt(rowIndex).data);
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
                width: 300
            },
            items: [

                gridIps

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Nuevo',
                handler: function() {
                  agregarIp(rec);
                }
            },
            {
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                    store.reload();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Ips',
        modal: true,
        width: 350,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function eliminarIp(data){
    Ext.Msg.alert('Mensaje','Esta seguro que desea eliminar la Ip?', function(btn){
        if(btn=='ok'){
            Ext.get(gridIps.getId()).mask('Ejecutando...');
            Ext.Ajax.request({
                url: url_deleteIp,
                method: 'post',
                timeout: 400000,
                params: { 
                    idDetalleElemento: data.idDetalleElemento,
                    idIp: data.idIp
                },
                success: function(response){
                    Ext.get(gridIps.getId()).unmask();
                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Transacción Exitosa.', function(btn){
                            if (btn == 'ok') {
                                storeIps.load();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje',response.responseText)
                    }
                }

            });
        }
    });
    
}

function agregarIp(rec){
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
                    id:'ip',
                    name: 'ip',
                    fieldLabel: 'Ip',
                    displayField: "",
                    value: "",
                    width: '30%'
                },
                {
                    xtype: 'textfield',
                    id: 'vlan',
                    name: 'vlan',
                    fieldLabel: 'Vlan',
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
                handler: function() {
                    var ip = Ext.getCmp('ip').getValue();
                    var vlan = Ext.getCmp('vlan').getValue();
                    var validaciones = 1;
                    if (vlan == '' || ip == '')
                    {
                        validaciones = 0;
                    }
                    //validacion de clave 
                    var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                    if (!ip.match(ipformat)) {
                        Ext.Msg.alert('Mensaje', 'Favor ingrese la ip en formato correcto.', function(btn) {
                            if (btn == 'ok') {
                            }
                        });
                        return false;
                    }

                    if (validaciones == 0) {
                        Ext.Msg.alert('Mensaje', 'Ingrese los campos obligatorios *', function(btn) {
                            if (btn == 'ok') {
                            }
                        });
                        return false;
                    }

                    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Agregar la Ip al elemento?', function(btn) {
                        if (btn == 'ok') {
                            Ext.get(formPanel.getId()).mask('Ejecutando...');
                            Ext.Ajax.request({
                                url: url_agregarIp,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    idElemento: rec.get('idElemento'),
                                    ip: ip,
                                    vlan: vlan
                                },
                                success: function(response) {
                                    Ext.get(formPanel.getId()).unmask();
                                    if (response.responseText == "OK") {
                                        Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function(btn) {
                                            if (btn == 'ok') {
                                                storeIps.load();
                                                win.destroy();
                                            }
                                        });
                                    }
                                    else {

                                        Ext.Msg.alert('Mensaje ', response.responseText);
                                    }

                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }
                    });
                }
            }, {
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar Ip',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cierraVentanaMapa(){
    winVerMapa.close();
    winVerMapa.destroy();
    
}