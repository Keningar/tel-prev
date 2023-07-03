Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
    
    var storeTipoElemento = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_tipo_elemento/getTiposElementosBackbone',
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
    
    var storeElementos = new Ext.data.Store({  
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : '../../../tecnico/clientes/getElementosPorTipo',
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
    
    var storeInterfacesElemento = new Ext.data.Store({  
        pageSize: 500,
//                autoLoad: true,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : '../../../tecnico/clientes/getInterfacesPorElemento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idInterface', mapping:'idInterface'},
              {name:'nombreInterface', mapping:'nombreInterface'}
            ]
    });
    
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
                idEnlace: '',
                interfaceElementoIniId: '',
                interfaceElementoIni: '',
                elementoIniNombre: '',
                interfaceElementoFinId:'',
                interfaceElementoFin:'',
                elementoFinNombre:'',
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idEnlace', mapping:'idEnlace'},
                    {name:'interfaceElementoIniId', mapping:'interfaceElementoIniId'},
                    {name:'interfaceElementoIni', mapping:'interfaceElementoIni'},
                    {name:'elementoIniNombre', mapping:'elementoIniNombre'},
                    {name:'interfaceElementoFinId', mapping:'interfaceElementoFinId'},
                    {name:'interfaceElementoFin', mapping:'interfaceElementoFin'},
                    {name:'elementoFinNombre', mapping:'elementoFinNombre'},
                    {name:'bufferColor', mapping:'bufferColor'},
                    {name:'bufferNumero', mapping:'bufferNumero'},
                    {name:'hiloColor', mapping:'hiloColor'},
                    {name:'hiloNumero', mapping:'hiloNumero'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}
                  ],
//         autoLoad: true
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
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'deleteAjax',
                        scope: this,
                        handler: function(){ 
                             var permiso = $("#ROLE_149-827");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                            
                            if(!boolPermiso){ 
                                alert("USTED NO TIENE PRIVILEGIOS PARA REALIZAR ESTA FUNCION!!!");
                            }
                            else{
                                eliminarAlgunos();
                            }
                            
                        
                        }
                    }
                ]}
        ],                  
        columns:[
                {
                  id: 'idEnlace',
                  header: 'idEnlace',
                  dataIndex: 'idEnlace',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'ipElemento',
                  header: 'Enlace',
                  xtype: 'templatecolumn', 
                  width: 600,
                  tpl: '<span class="bold">Elemento Inicio:</span></br>\n\\n\
                        <span class="box-detalle">{elementoIniNombre}</span>\n\
                        <span class="bold">Puerto Inicio: </span><span>{interfaceElementoIni}</span></br>\n\\n\\n\
                        <span class="bold">Elemento Fin:</span></br>\n\\n\
                        <span class="box-detalle">{elementoFinNombre}</span>\n\
                        <span class="bold">Puerto Fin: </span><span>{interfaceElementoFin}</span></br>\n\
                        <span class="bold">Buffer: </span><span>{bufferNumero}, {bufferColor}</span></br>\n\
                        <span class="bold">Hilo: </span><span>{hiloNumero}, {hiloColor}</span></br>\n\
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
//                    xtype: 'container',
                    
                    items: [
                        
                        {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-show'
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = ""+rec.get('idEnlace')+"/show";
                            }
                        },
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
                                            Ext.Ajax.request({
                                                url: "deleteAjax",
                                                timeout: 3000000,
                                                method: 'post',
                                                params: { param : rec.get('idEnlace')},
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
                            id: 'sltTipoIni',
                            fieldLabel: 'Tipo Inicio:',
                            store: storeTipoElemento,
                            displayField: 'nombreTipoElemento',
                            valueField: 'nombreTipoElemento',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%',
                            listeners: {
                                select: function(combo){
                                    storeElementos.proxy.extraParams = {
                                        tipoElemento: combo.getValue(),
                                        idServicio: ''
                                    };
                                    storeElementos.load({params: {}});
                                }
                            }
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltTipoFin',
                            fieldLabel: 'Tipo Fin:',
                            store: storeTipoElemento,
                            displayField: 'nombreTipoElemento',
                            valueField: 'nombreTipoElemento',
                            loadingText: 'Buscando ...',
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
                            id: 'sltElementoIni',
                            fieldLabel: 'Elemento Inicio:',
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
                            width: '30%',
                            listeners: {
                                select: function(combo){
                                    storeInterfacesElemento.proxy.extraParams = {
                                        idElemento: combo.getValue()
                                    };
                                    storeInterfacesElemento.load({params: {}});
                                }
                            }
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltElementoFin',
                            fieldLabel: 'Elemento Fin:',
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
                            width: '30%',
                            listeners: {
                                select: function(combo){
                                    storeInterfacesElemento.proxy.extraParams = {
                                        idElemento: combo.getValue()
                                    };
                                    storeInterfacesElemento.load({params: {}});
                                }
                            }
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                                            
                        { width: '10%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltInterfaceIni',
                            fieldLabel: 'Interface Inicio:',
                            store: storeInterfacesElemento,
                            displayField: 'nombreInterface',
                            valueField: 'idInterface',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            id: 'sltInterfaceFin',
                            fieldLabel: 'Interface Fin:',
                            store: storeInterfacesElemento,
                            displayField: 'nombreInterface',
                            valueField: 'idInterface',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '10%',border:false},
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
    
    
});

function buscar(){
    store.getProxy().extraParams.interfaceElementoIniId = Ext.getCmp('sltInterfaceIni').value;
    store.getProxy().extraParams.interfaceElementoFinId = Ext.getCmp('sltInterfaceFin').value;
    store.getProxy().extraParams.elementoIniNombre = Ext.getCmp('sltElementoIni').getRawValue();
    store.getProxy().extraParams.elementoFinNombre = Ext.getCmp('sltElementoFin').getRawValue();
    store.load();
    
//    store.load({params: {
//        nombreElemento: Ext.getCmp('txtNombre').value,
//        ipElemento: Ext.getCmp('txtIp').value,
//        marcaElemento: Ext.getCmp('sltMarca').value,
//        modeloElemento: Ext.getCmp('sltModelo').value,
//        canton: Ext.getCmp('sltCanton').value,
//        jurisdiccion: Ext.getCmp('sltJurisdiccion').value,
//        popElemento: Ext.getCmp('sltPop').value,
//        estado: Ext.getCmp('sltEstado').value
//    }});
}

function limpiar(){
    Ext.getCmp('sltInterfaceIni').value="";
    Ext.getCmp('sltInterfaceIni').setRawValue("");
    
    Ext.getCmp('sltInterfaceFin').value="";
    Ext.getCmp('sltInterfaceFin').setRawValue("");
    
    Ext.getCmp('sltElementoFin').value="";
    Ext.getCmp('sltElementoFin').setRawValue("");
    
    Ext.getCmp('sltElementoIni').value="";
    Ext.getCmp('sltElementoIni').setRawValue("");
    
    Ext.getCmp('sltTipoIni').value="";
    Ext.getCmp('sltTipoIni').setRawValue("");
    
    Ext.getCmp('sltTipoFin').value="";
    Ext.getCmp('sltTipoFin').setRawValue("");
    
}

function eliminarAlgunos(){
    Ext.get(grid.getId()).mask('Eliminando Elementos...');
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.idElemento;
        
        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
          estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
        {
          param = param + '|';
        }
//        alert(param);
      }      
      if(estado == 0)
      {
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "dslam/deleteAjaxDslam",
                    method: 'post',
                    params: { param : param},
                    success: function(response){
                        var text = response.responseText;
                        
                        if(text == "OK"){
                            Ext.Msg.alert('Mensaje','Se eliminaron los Elementos!', function(btn){
                                if(btn=='ok'){
                                    Ext.get(grid.getId()).unmask();
                                    store.load();
                                }
                            });
                        }
                        else if(text=="SERVICIOS ACTIVOS"){
                            Ext.Msg.alert('Mensaje','Uno o mas de los elementos aun posee servicios activos, <br> Favor revisar!', function(btn){
                                if(btn=='ok'){
                                    Ext.get(grid.getId()).unmask();
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
