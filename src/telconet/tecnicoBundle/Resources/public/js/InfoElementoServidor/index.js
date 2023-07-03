Ext.onReady(function() {   
    Ext.tip.QuickTipManager.init();

    var storeMarcas = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getMarcasElementosServidor,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreMarcaElemento', mapping:'nombreMarcaElemento'},
                {name:'idMarcaElemento', mapping:'idMarcaElemento'}
              ]
    });
    
    var storeModelos = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getModelosElementosPorMarca,
            extraParams: {
                idMarca: '',
                tipoElemento: 'SERVIDOR'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreModeloElemento', mapping:'nombreModeloElemento'},
                {name:'idModeloElemento', mapping:'idModeloElemento'}
              ]
    });

    var storeCantones = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getCantones,
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
    
    var storeJurisdicciones = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getJurisdicciones,
            reader: {
                type: 'json', 
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreJurisdiccion', mapping:'nombreJurisdiccion'},
                {name:'idJurisdiccion', mapping:'idJurisdiccion'}
              ]
    });
    
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getEncontrados,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                ipElemento: '',
                marcaElemento: '',
                modeloElemento: '',
                canton:'',
                jurisdiccion:'',
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idElemento',         mapping:'idElemento'},
                    {name:'nombreElemento',     mapping:'nombreElemento'},
                    {name:'ipElemento',         mapping:'ipElemento'},
                    {name:'cantonNombre',       mapping:'cantonNombre'},
                    {name:'jurisdiccionNombre', mapping:'jurisdiccionNombre'},
                    {name:'marcaElemento',      mapping:'marcaElemento'},
                    {name:'modeloElemento',     mapping:'modeloElemento'},
                    {name:'tipoElemento',       mapping:'tipoElemento'},
                    {name:'estado',             mapping:'estado'},
                    {name:'action1',            mapping:'action1'},
                    {name:'action2',            mapping:'action2'},
                    {name:'action3',            mapping:'action3'}
                  ],
//        autoLoad: true
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        viewConfig: { enableTextSelection: true },
        iconCls: 'icon-grid',
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'deleteAjax',
                        scope: this,
                        handler: function() {
                            var permiso = $("#ROLE_196-9");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if (!boolPermiso) {
                                alert("USTED NO TIENE PRIVILEGIOS PARA REALIZAR ESTA FUNCION!!!");
                            }
                            else {
                                eliminarAlgunos();
                            }
                        }
                    }
                ]}
        ],                  
        columns:[
                {
                  id: 'idElemento',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'ipElemento',
                  header: 'Ip Servidor',
                  xtype: 'templatecolumn', 
                  width: 300,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Ip:</span><span>{ipElemento}</span></br>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        '
                
                },
                {
                  header: 'Marca',
                  dataIndex: 'marcaElemento',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Tipo',
                  dataIndex: 'tipoElemento',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Modelo',
                  dataIndex: 'modeloElemento',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 90,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 300,                    
                    items: [
                        //SHOW SERVIDOR
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_196-6");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                
                                //alert(typeof permiso);
                                if(!boolPermiso){ 
                                    return 'button-grid-invisible';
                                }
                                else{
                                    return 'button-grid-show';
                                }
                            },
                            tooltip: 'Ver',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                window.location = ""+rec.get('idElemento')+"/showServidor";
                            }
                        },
                        //EDIT SERVIDOR
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_196-5");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                
                                //alert(typeof permiso);
                                if(!boolPermiso){ 
                                    return 'button-grid-invisible';
                                }
                                else{
                                    if (rec.get('action2') == "button-grid-invisible") 
                                        this.items[1].tooltip = '';
                                    else 
                                        this.items[1].tooltip = 'Editar';
                                }

                                return rec.get('action2');
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action2')!="button-grid-invisible")
                                    window.location = ""+rec.get('idElemento')+"/editServidor";
                            }
                        },
                        //DELETE SERVIDOR
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_196-8");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                
                                if(!boolPermiso){ 
                                    return 'button-grid-invisible';
                                }
                                else{
                                    if (rec.get('action3') == "button-grid-invisible") 
                                        this.items[2].tooltip = '';
                                    else 
                                        this.items[2].tooltip = 'Eliminar';
                                } 

                                return rec.get('action3');
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action3')!="button-grid-invisible"){
                                    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                        if(btn=='yes'){
                                            Ext.Ajax.request({
                                                url: deleteAjax,
                                                method: 'post',
                                                params: { param : rec.get('idElemento')},
                                                success: function(response){
                                                    var text = response.responseText;
                                                    store.load();
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
                            xtype: 'textfield',
                            id: 'txtIp',
                            fieldLabel: 'Ip',
                            value: '',
                            width: '200px'
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {
                            id: 'sltMarca',
                            fieldLabel: 'Marca',
                            xtype: 'combobox',
                            typeAhead: true,
                            displayField:'nombreMarcaElemento',
                            valueField: 'idMarcaElemento',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
                            hideTrigger: false,
                            store: storeMarcas,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combobox',
                            id: 'sltModelo',
                            fieldLabel: 'Modelo',
                            store: storeModelos,
                            displayField: 'nombreModeloElemento',
                            valueField: 'idModeloElemento',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
                            hideTrigger: false,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {
                            //xtype: 'combo',
                            id: 'sltCanton',
                            fieldLabel: 'Canton',
                            /*store: storeCantones,
                            displayField: 'nombre_canton',
                            valueField: 'id_canton',*/
                    
                            xtype: 'combobox',
                            typeAhead: true,
                            displayField:'nombre_canton',
                            valueField: 'id_canton',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
                            hideTrigger: false,
                            store: storeCantones,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combobox',
                            id: 'sltJurisdiccion',
                            fieldLabel: 'Jurisidiccion',
                            store: storeJurisdicciones,
                            displayField: 'nombreJurisdiccion',
                            valueField: 'idJurisdiccion',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
                            hideTrigger: false,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        //-------------------------------------
                        
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Modificado','Modificado'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        { width: '30%',border:false},
                        { width: '10%',border:false}, //final
                        
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
    store.load({
        callback:function(){        
            storeMarcas.load({
                callback: function(){
                    storeModelos.load({
                        callback: function(){
                            storeCantones.load({
                                callback: function(){
                                    storeJurisdicciones.load();                                  
                                }
                            });                                  
                        }
                    });                                  
                }
            });
        }
    });
});

function buscar(){
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,
        ipElemento:     Ext.getCmp('txtIp').value,
        marcaElemento:  Ext.getCmp('sltMarca').value,
        modeloElemento: Ext.getCmp('sltModelo').value,
        canton:         Ext.getCmp('sltCanton').value,
        jurisdiccion:   Ext.getCmp('sltJurisdiccion').value,
        estado:         Ext.getCmp('sltEstado').value
    }});
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    
    Ext.getCmp('txtIp').value="";
    Ext.getCmp('txtIp').setRawValue("");
    
    Ext.getCmp('sltMarca').value="";
    Ext.getCmp('sltMarca').setRawValue("");
    
    Ext.getCmp('sltModelo').value="";
    Ext.getCmp('sltModelo').setRawValue("");
    
    Ext.getCmp('sltCanton').value="";
    Ext.getCmp('sltCanton').setRawValue("");
    
    Ext.getCmp('sltJurisdiccion').value="";
    Ext.getCmp('sltJurisdiccion').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
        var estado = 0;
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            param = param + sm.getSelection()[i].data.idElemento;

            if (sm.getSelection()[i].data.estado == 'Eliminado')
            {
                estado = estado + 1;
            }
            if (i < (sm.getSelection().length - 1))
            {
                param = param + '|';
            }
        }      
        if (estado == 0)
        {
            Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        url: deleteAjax,
                        method: 'post',
                        params: {param: param},
                        success: function(response) {
                            var text = response.responseText;
                            store.load();
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            });
        }
        else
        {
            alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
        }
    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}   
