Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
    
    //store que obtiene los tipos de elementos
    var storeTipoElemento = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : getTiposElementosBackbone,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreTipoElemento', mapping:'nombreTipoElemento'},
                {name:'idTipoElemento', mapping:'idTipoElemento'}
              ]
    });
    
    //store que obtiene los elementos
    var storeElementos = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : getElementosPorTipo,
            extraParams: {
                idServicio: '',
                nombreElemento: this.nombreElemento,
                tipoElemento: ''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombreElemento', mapping:'nombreElemento'},
              {name:'ipElemento', mapping:'ip'}
            ]
    });
    
    //store que obtiene la data final (relacion elemento)
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 400000,
            type: 'ajax',
            url : 'getEncontrados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                elementoIdA: '',
                elementoIdB:''
            }
        },
        fields:
            [
                {name:'idRelacionElemento'  , mapping:'idRelacionElemento'  },
                {name:'elementoIdA'         , mapping:'elementoIdA'         },
                {name:'nombreElementoA'     , mapping:'nombreElementoA'     },
                {name:'tipoElementoA'       , mapping:'tipoElementoA'       },
                {name:'elementoIdB'         , mapping:'elementoIdB'         },
                {name:'nombreElementoB'     , mapping:'nombreElementoB'     },
                {name:'tipoElementoB'       , mapping:'tipoElementoB'       },
                {name:'estado'              , mapping:'estado'              },
                {name:'action3'             , mapping:'action3'             }
            ]
    });
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    //grid de datos
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
                    id: 'idRelacionElemento',
                    header: 'idRelacionElemento',
                    dataIndex: 'idRelacionElemento',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'relacionElemento',
                    header: 'Relacion Elemento',
                    xtype: 'templatecolumn', 
                    width: 600,
                    tpl: '<span class="bold">Elemento Contenedor:</span></br>\n\\n\
                          <span class="box-detalle">{nombreElementoA}</span>\n\
                          <span class="bold">Tipo Elemento: </span><span>{tipoElementoA}</span></br>\n\\n\\n\
                          <span class="bold">Elemento Contenido:</span></br>\n\\n\
                          <span class="box-detalle">{nombreElementoB}</span>\n\
                          <span class="bold">Tipo Elemento: </span><span>{tipoElementoB}</span></br>\n\
                          '                
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
                    
                    items: [
                        
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_263-9");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if(!boolPermiso){ 
                                    return 'button-grid-invisible';
                                }
                                else{
                                    if (rec.get('action3') == "button-grid-invisible") 
                                        tooltip = '';
                                    else 
                                        tooltip = 'Eliminar';
                                }
                                

                                return rec.get('action3');
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action3')!="button-grid-invisible")
                                    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                        if(btn=='yes'){
                                            Ext.Ajax.request({
                                                url: eliminarRelacionElemento,
                                                timeout: 3000000,
                                                method: 'post',
                                                params: { param : rec.get('idRelacionElemento')},
                                                success: function(response){
                                                    var text = response.responseText;
                                                    if(text == "No existe la entidad"){
                                                        Ext.Msg.alert('Mensaje','No se puede eliminar el registro, <br>\n\
                                                                                puesto que no existe registro!', function(btn){
                                                            if(btn=='ok'){;
                                                                store.load();
                                                            }
                                                        });
                                                    }
                                                    else{
                                                        Ext.Msg.alert('Mensaje','Se elimino registro!', function(btn){
                                                            if(btn=='ok'){;
                                                                store.load();
                                                            }
                                                        });
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
    
    //filtro de busqueda
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
                        { width: '10%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltTipoContenedor',
                            fieldLabel: 'Tipo Contenedor:',
                            store: storeTipoElemento,
                            displayField: 'nombreTipoElemento',
                            valueField: 'nombreTipoElemento',
                            loadingText: 'Buscando ...',
                            emptyText: 'Ingrese tipo Elemento..',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%',
                            listeners: {
                                select: function(combo){
                                    storeElementos.proxy.extraParams = {tipoElemento: combo.getValue(), idServicio: ''};
                                    storeElementos.load({params: {}});
                                }
                            }
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltTipoContenido',
                            fieldLabel: 'Tipo Contenido:',
                            store: storeTipoElemento,
                            displayField: 'nombreTipoElemento',
                            valueField: 'nombreTipoElemento',
                            loadingText: 'Buscando ...',
                            emptyText: 'Ingrese tipo Elemento..',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%',
                            listeners: {
                                select: function(combo){
                                    storeElementos.proxy.extraParams = {tipoElemento: combo.getValue(), idServicio: ''};
                                    storeElementos.load({params: {}});
                                }
                            }
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                
                        { width: '10%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltElementoContenedor',
                            fieldLabel: 'Elemento Contenedor:',
                            store: storeElementos,
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: "remote",
                            lazyRender: true,
                            forceSelection: true,
                            emptyText: 'Ingrese nombre Elemento..',
                            minChars: 3, 
                            typeAhead: true,
                            triggerAction: 'all',
                            selectOnTab: true,
                            width: '30%'
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltElementoContenido',
                            fieldLabel: 'Elemento Contenido:',
                            store: storeElementos,
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: "remote",
                            lazyRender: true,
                            forceSelection: true,
                            emptyText: 'Ingrese nombre Elemento..',
                            minChars: 3, 
                            typeAhead: true,
                            triggerAction: 'all',
                            selectOnTab: true,
                            width: '30%'
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                        
                        ],	
        renderTo: 'filtro'
    });
});

function buscar(){
    store.getProxy().extraParams.elementoIdA = Ext.getCmp('sltElementoContenedor').getValue();
    store.getProxy().extraParams.elementoIdB = Ext.getCmp('sltElementoContenido').getValue();

    store.load();
}

function limpiar(){
    Ext.getCmp('sltTipoContenedor').value="";
    Ext.getCmp('sltTipoContenedor').setRawValue("");
    
    Ext.getCmp('sltTipoContenido').value="";
    Ext.getCmp('sltTipoContenido').setRawValue("");
    
    Ext.getCmp('sltElementoContenedor').value="";
    Ext.getCmp('sltElementoContenedor').setRawValue("");
    
    Ext.getCmp('sltElementoContenido').value="";
    Ext.getCmp('sltElementoContenido').setRawValue("");
}