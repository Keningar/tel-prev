Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
    
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEncontradosSinUbicacion',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: ''
            }
        },
        fields:
                  [
                    {name:'idElemento', mapping:'idElemento'},
                    {name:'nombreElemento', mapping:'nombreElemento'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}
                  ]
//        autoLoad: true
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        viewConfig: { enableTextSelection: true },
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
                  id: 'nombreElemento',
                  header: 'Elemento',
                  xtype: 'templatecolumn', 
                  width: 640,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n'
                
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 60,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 150,
//                    xtype: 'container',
                    
                    items: [
                        //EDITAR ELEMENTO
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('action2') == "button-grid-invisible") 
                                        tooltip = '';
                                    else 
                                        tooltip = 'Editar';
                                return rec.get('action2')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action2')!="button-grid-invisible")
                                    editarElemento(rec.get('idElemento'));
                            }
                        },
                        //ELIMINAR ELEMENTO
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('action3') == "button-grid-invisible") 
                                    tooltip = '';
                                else 
                                    tooltip = 'Eliminar';

                                return rec.get('action3')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action3')!="button-grid-invisible")
                                    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                        if(btn=='yes'){
                                            Ext.get(grid.getId()).mask('Guardando datos');
                                            Ext.Ajax.request({
                                                url: ""+rec.get('idElemento')+"/deleteSinUbicacion",
                                                method: 'post',
                                                params: { param : rec.get('idElemento')},
                                                success: function(response){
                                                    var text = response.responseText;
                                                    if(text == "SERVICIOS ACTIVOS"){
                                                        Ext.Msg.alert('Mensaje','NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN <BR> SERVICIOS ACTIVOS, FAVOR REVISAR!', function(btn){
                                                            if(btn=='ok'){;
                                                                store.load();
                                                            }
                                                        });
                                                    }
                                                    else{
                                                        store.load();
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                }
                                        });
                                    }
                                });
                            }
                        }
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
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 1,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true, 
        collapsed: false,
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
                        
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '100%'
                        }
                        
                        //-------------------------------------
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
});

function editarElemento(data){
    storeCanton = new Ext.data.Store({ 
        pageSize: 100,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/general/admi_canton/getCantones',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idCanton', mapping:'id_canton'},
              {name:'nombreCanton', mapping:'nombre_canton'}
            ],
        autoLoad: true
    });
    
    storeParroquia = new Ext.data.Store({ 
        pageSize: 60,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/general/admi_parroquia/buscarParroquias',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idParroquia', mapping:'id_parroquia'},
              {name:'nombreParroquia', mapping:'nombre_parroquia'}
            ],
//        autoLoad: true
    });
    
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
                                xtype: 'combobox',
                                id: 'canton',
                                name: 'canton',
                                fieldLabel: 'Canton',
                                displayField: 'nombreCanton',
                                valueField:'idCanton',
                                loadingText: 'Buscando...',
                                width: '25%',
                                queryMode: "local",
                                store: storeCanton,
                                listeners: {
                                    select: function(combo){
                                        storeParroquia.proxy.extraParams = {idCanton: combo.getValue()};
                                        storeParroquia.load({params: {}});
                                    }
                                }
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'combobox',
                                id: 'parroquia',
                                name: 'parroquia',
                                fieldLabel: 'Parroquia',
                                displayField: 'nombreParroquia',
                                valueField:'idParroquia',
                                loadingText: 'Buscando...',
                                width: '25%',
                                queryMode: "local",
                                store: storeParroquia
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'longitud',
                                name: 'longitud',
                                fieldLabel: 'Longitud',
                                displayField: '',
                                value: '',
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'latitud',
                                name: 'latitud',
                                fieldLabel: 'Latitud',
                                displayField: '',
                                value: '',
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'altura',
                                name: 'altura',
                                fieldLabel: 'Altura SNM',
                                displayField: '',
                                value: '',
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            { width: '15%', border: false},
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                        ]
                    }

                ]
            }//cierre de la informacion servicio/producto
            
        ],
        buttons: [{
            text: 'Grabar',
            formBind: true,
            handler: function(){
                var canton = Ext.getCmp('canton').getValue();
                var parroquia = Ext.getCmp('parroquia').getValue();
                var longitud = Ext.getCmp('longitud').getValue();
                var latitud = Ext.getCmp('latitud').getValue();
                var altura = Ext.getCmp('altura').getValue();
                
                var validacion=true;
                if(canton=="" || parroquia=="" || longitud=="" || latitud=="" || altura==""){
                    validacion=false;
                }

                if(validacion){
                    Ext.get(formPanel.getId()).mask('Guardando datos');
                    Ext.Ajax.request({
                        url: guardarUbicacion,
                        method: 'post',
                        timeout: 400000,
                        params: { 
                            idElemento: data,
                            canton: canton,
                            parroquia: parroquia,
                            longitud: longitud,
                            latitud: latitud,
                            altura: altura
                        },
                        success: function(response){
                            Ext.get(formPanel.getId()).unmask();
                            if(response.responseText == "OK"){
                                Ext.Msg.alert('Mensaje','Se Guardaron los datos', function(btn){
                                    if(btn=='ok'){
                                        win.destroy();
                                        store.load();
                                    }
                                });
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
                    if(flag==1){
                        Ext.Msg.alert("Validacion","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }
                    else if(flag==2){
                        Ext.Msg.alert("Validacion","Macs no pueden ser iguales, favor revisar!", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }
                    else if(flag==3){
                        Ext.Msg.alert("Validacion","Datos del Ont incorrectos, favor revisar!", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }
                    else if(flag==4){
                        Ext.Msg.alert("Validacion","Datos del Wifi incorrectos, favor revisar!", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }
                    else{
                        Ext.Msg.alert("Validacion","Favor Revise los campos", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }

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
        title: 'Actualizar Informacion del Elemento',
        modal: true,
        width: 600,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function buscar(){
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
}
