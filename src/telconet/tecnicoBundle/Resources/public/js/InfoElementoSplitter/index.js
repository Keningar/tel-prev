Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();
    
    
    storeSpliterPrimario = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 400000, type: 'ajax',
            url: getSplitterPorNivel,
            extraParams: {
                nivelSplitter: '1'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'elemento', mapping: 'elemento'},
                {name: 'id_elemento', mapping: 'id_elemento'}
            ],
    });
    
    storeInterfacesElemento = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 400000, type: 'ajax',
            url: getInterfacesElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreInterface', mapping: 'nombreInterface'},
                {name: 'idInterface', mapping: 'idInterface'}
            ],

    });
    
    
    
    var storeMarcas = new Ext.data.Store({ 
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosSplitter',
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
    
    storeModelos = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosPorMarca',
            extraParams: {
                idMarca: '',
                tipoElemento: 'SPLITTER'
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
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/general/admi_canton/getCantones',
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
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_jurisdiccion/getJurisdicciones',
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
    
    storeElementoContenedor = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../../elemento/splitter/buscarElementoContenedor',
            reader: {
                type: 'json', 
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idElemento', mapping:'id_elemento'},
                {name:'nombreElemento', mapping:'nombre_elemento'}
              ]
    });

    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEncontradosSplitter',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento: '',
                modeloElemento: '',
                canton:'',
                jurisdiccion:'',
                contenidoEn: '',
                elementoContenedor:'',
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idElemento', mapping:'idElemento'},
                    {name:'nombreElemento', mapping:'nombreElemento'},
                    {name:'nombreElementoNodo', mapping:'nombreElementoNodo'},
                    {name:'ipElemento', mapping:'ipElemento'},
                    {name:'cantonNombre', mapping:'cantonNombre'},
                    {name:'jurisdiccionNombre', mapping:'jurisdiccionNombre'},
                    {name:'switchTelconet', mapping:'switchTelconet'},
                    {name:'puertoSwitch', mapping:'puertoSwitch'},
                    {name:'marcaElemento', mapping:'marcaElemento'},
                    {name:'modeloElemento', mapping:'modeloElemento'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'},
                    {name:'nivel', mapping:'nivel'},
                    {name:'clonado', mapping:'clonado'}
                  ],
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
                  id: 'ipElemento',
                  header: 'Splitter',
                  xtype: 'templatecolumn', 
                  width: 340,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <span class="bold">ElementoContenedor:</span><span>{nombreElementoNodo}</span></br>\n\
                        <tpl if="switchTelconet!=\'N/A\'">\n\
                            <!--<span class="bold">Switch:</span>{switchTelconet}</br>--> \n\
                            <!--<span class="bold">Puerto:</span>{puertoSwitch}-->\n\
                        </tpl>'
                
                },
                {
                  header: 'Marca',
                  dataIndex: 'marcaElemento',
                  width: 80,
                  sortable: true
                },
                {
                  header: 'Modelo',
                  dataIndex: 'modeloElemento',
                  width: 90,
                  sortable: true
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
                    width: 450,
//                    xtype: 'container',
                    
                    items: [
                        
                        {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-show'
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = ""+rec.get('idElemento')+"/showSplitter";
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_233-4");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                boolPermiso = true;
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
                                

                                return rec.get('action2')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action2')!="button-grid-invisible")
                                    window.location = ""+rec.get('idElemento')+"/editSplitter";
                                    //alert(rec.get('nombre'));
                                }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_233-8");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                //alert(typeof permiso);
                                if(!boolPermiso){ 
                                    return 'button-grid-invisible';
                                }
                                else{
                                    if (rec.get('action3') == "button-grid-invisible") 
                                        this.items[2].tooltip = '';
                                    else 
                                        this.items[2].tooltip = 'Eliminar';
                                }
                                

                                return rec.get('action3')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action3')!="button-grid-invisible")
                                    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                        if(btn=='yes'){
                                            Ext.Ajax.request({
                                                url: "deleteAjaxSplitter",
                                                timeout: 3000000,
                                                method: 'post',
                                                params: { param : rec.get('idElemento')},
                                                success: function(response){
                                                    
                                                    var text = response.responseText;
                                                    if(text == "SERVICIOS ACTIVOS"){
                                                        Ext.Msg.alert('Mensaje','NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN <BR> '+
                                                                      'SERVICIOS ACTIVOS, FAVOR REVISAR!', function(btn)
                                                                      {
                                                                            if(btn=='ok')
                                                                            {;
                                                                                store.load();
                                                                            }
                                                                      });
                                                    }
                                                    else if(text == "ENLACES ACTIVOS")
                                                    {
                                                        Ext.Msg.alert('Mensaje',
                                                                      'NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN <BR> '+
                                                                      'ENLACES ACTIVOS, FAVOR REVISAR!', function(btn){
                                                            if(btn=='ok'){;
                                                                store.load();
                                                            }
                                                        });
                                                    }
                                                    else
                                                    {
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
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    var permiso = $("#ROLE_233-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                    if(!boolPermiso){ 
                                        return 'button-grid-invisible';
                                    }
                                    else{
                                        return 'button-grid-administrarPuertos';
                                    }
                                    
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Administrar Puertos',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    administrarPuertos(grid.getStore().getAt(rowIndex).data);                                    
                                }
                            }
                        },
                                                {
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado" && rec.get('nivel') == '2' && rec.get('clonado') == null) {
                                    var permiso = $("#ROLE_272-2097");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                    if(!boolPermiso){ 
                                        return 'button-grid-invisible';
                                    }
                                    else
                                    {
                                        return 'button-grid-agregarScript';
                                    }
                                    
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Clonar elemento',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    clonarElemento(grid.getStore().getAt(rowIndex).data);                                    
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
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {
                            id: 'sltMarca',
                            fieldLabel: 'Marca',
                            xtype: 'combobox',
                            store: storeMarcas,
                            displayField:'nombreMarcaElemento',
                            valueField: 'idMarcaElemento',
                            loadingText: 'Buscando ...',
                            queryMode: 'local',
                            listClass: 'x-combo-list-small',
                            listeners: {
                                select: function(combo){
                                    cargarModelos(combo.getValue());
                                }
                            },//cierre listener
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
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combobox',
                            id: 'sltCanton',
                            fieldLabel: 'Canton',
                            
                            displayField:'nombre_canton',
                            valueField: 'id_canton',
                            loadingText: 'Buscando ...',
                            store: storeCantones,
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
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
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        //-------------------------------------
                        
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Contenido En',
                            id: 'sltContenidoEn',
                            value:'',
                            store: [
                                ['',''],
                                ['NODO','NODO'],
                                ['CAJA DISPERSION','CAJA DISPERSION']
                            ],
                            width: '30%',
                            listeners: {
                                select: function(combo){
                                    cargarElementosContenedores(combo.getValue());
                                }
                            },//cierre listener
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combobox',
                            id: 'sltElementoContenedor',
                            fieldLabel: 'Elemento Contenedor',
                            store: storeElementoContenedor,
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
    store.load({
        callback:function(){        
            storeMarcas.load({
                // store loading is asynchronous, use a load listener or callback to handle results
                callback: function(){
                    storeModelos.load({
                        callback: function(){
                            storeCantones.load({
                                callback: function(){
                                    storeJurisdicciones.load({
                                        callback: function(){
                                                                             
                                        }
                                    });                                  
                                }
                            });                                  
                        }
                    });                                  
                }
            });
        }
    });
    
});

function cargarModelos(idParam){
    storeModelos.proxy.extraParams = {idMarca: idParam, tipoElemento:'OLT', limite:100};
    storeModelos.load({params: {}});
}

function cargarElementosContenedores(idParam){
    storeElementoContenedor.proxy.extraParams = {tipoElemento: idParam};
    storeElementoContenedor.load({params: {}});
}

function buscar(){
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarca').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModelo').value;
    store.getProxy().extraParams.canton = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.jurisdiccion = Ext.getCmp('sltJurisdiccion').value;
    store.getProxy().extraParams.contenidoEn = Ext.getCmp('sltContenidoEn').value;
    store.getProxy().extraParams.elementoContenedor = Ext.getCmp('sltElementoContenedor').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
    
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
    
    Ext.getCmp('sltNodo').value="";
    Ext.getCmp('sltNodo').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
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
                    url: "deleteAjaxCaja",
                    timeout: 3000000,
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
                        else if(text=="SPLITTER ACTIVOS"){
                            Ext.Msg.alert('Mensaje','Uno o mas de los elementos aun posee elementos activos, <br> Favor revisar!', function(btn){
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

function administrarPuertos(data){
    Ext.define('estados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'opcion', type: 'string'},
            {name: 'valor',  type: 'string'}
        ]
    });
    
    comboEstados = new Ext.data.Store({ 
        model: 'estados',
        data : [
            {opcion:'Libre'     , valor:'not connect'},
            {opcion:'Ocupado'   , valor:'connected'},
            {opcion:'Dañado'    , valor:'err-disabled'},
            {opcion:'Inactivo'  , valor:'disabled'},
            {opcion:'Reservado' , valor:'reserved'},
            {opcion:'Factible'  , valor:'Factible'}
        ]
    });

    var comboInterfaces = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../interface_elemento/getInterfacesElemento',
            extraParams: {idElemento: data.idElemento, tipo:'SPLITTER'},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idInterfaceElemento', mapping:'idInterfaceElemento'},
                {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'},
                {name:'estado', mapping:'estado'},
                {name:'login', mapping:'login'}
              ]
    });
    
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridAdministracionPuertos.getView().refresh();
            }
        }
    });
    
    gridAdministracionPuertos = Ext.create('Ext.grid.Panel', {
        id:'gridAdministracionPuertos',
        store: comboInterfaces,
        columnLines: true,
        columns: [{
            id: 'idInterfaceElemento',
            header: 'idInterfaceElemento',
            dataIndex: 'idInterfaceElemento',
            hidden: true,
            hideable: false
        },{
            id: 'nombreInterfaceElemento',
            header: 'Interface Elemento',
            dataIndex: 'nombreInterfaceElemento',
            width: 150,
            hidden: false,
            hideable: false
        }, {
            id: 'estado',
            header: 'Estado',
            dataIndex: 'estado',
            width: 150,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                for (var i = 0;i< comboEstados.data.items.length;i++)
                {
                    if (comboEstados.data.items[i].data.valor == record.data.estado)
                    {
                        if(comboEstados.data.items[i].data.valor == "not connect"){
                            record.data.estado = "Libre";
                            break;
                        }
                        else if(comboEstados.data.items[i].data.valor == "connected"){
                            record.data.estado = "Ocupado";
                            break;
                        }
                        else if(comboEstados.data.items[i].data.valor == "err-disabled"){
                            record.data.estado = "Dañado";
                            break;
                        }
                        else if(comboEstados.data.items[i].data.valor == "disabled"){
                            record.data.estado = "Inactivo";
                            break;
                        }
                        else if(comboEstados.data.items[i].data.valor == "Factible"){
                            record.data.estado = "Factible";
                            break;
                        }
                        else if(comboEstados.data.items[i].data.valor == "reserved"){
                            record.data.estado = "Reservado";
                            break;
                        }
                    }
                }
                
                return record.data.estado;
            },
            editor: {   
                xtype: 'combobox',
                displayField:'opcion',
                valueField: 'valor',
                loadingText: 'Buscando ...',
                store: comboEstados,
                listClass: 'x-combo-list-small',
                queryMode: 'local'
            }
        },
        {
            id: 'login',
            header: 'Login',
            dataIndex: 'login',
            width: 450,
            hidden: false,
            hideable: false
        }
        ],
        
    
        
        viewConfig:{
            stripeRows:true,
            enableTextSelection: true
        },

        

        width: 660,
        height: 250,
        frame: true,
        plugins: [cellEditing]
        
        
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
                    columns: 1
                },
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:20px'
                },

                items: [
                    //hidden json
                    {
                        xtype: 'hidden',
                        id:'jsonInterfaces',
                        name: 'jsonInterfaces',
                        fieldLabel: '',
                        displayField: '',
                        value: '',
                        readOnly: true,
                        width: '30%'
                                    
                    },//cierre hidden
                    
                    //elemento
                    {
                        xtype: 'fieldset',
                        title: 'Informacion del Elemento',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 300,
                            height: 20
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

                                    {
                                        xtype: 'textfield',
                                        id:'elemento',
                                        name: 'elemento',
                                        fieldLabel: 'Elemento',
                                        displayField: data.nombreElemento,
                                        value: data.nombreElemento,
                                        readOnly: true,
                                        width: '200%'
                                    }

                                    //---------------------------------------

                                ]//cierre del container table
                            }                


                        ]//cierre del fieldset
                    },//cierre informacion ont
                    
                    {
                        xtype: 'fieldset',
                        title: 'Puertos',
                        defaultType: 'textfield',
            //                checkboxToggle: true,
            //                collapsed: true,
                        defaults: {
                            width: 500,
                            height: 200
                        },
                        items: [
                            
                            gridAdministracionPuertos

                        ]
                    },//cierre interfaces cpe
                ],//cierre items
                buttons: [{
                    text: 'Actualizar',
                    formBind: true,
                    handler: function(){

                            obtenerInterfaces();
                            var interfaces = Ext.getCmp('jsonInterfaces').getRawValue();
                            Ext.get(formPanel.getId()).mask('Guardando datos');
                            Ext.Ajax.request({
                                url: "administrarPuertos",
                                method: 'post',
                                timeout: 10000,
                                params: { 
                                    idElemento: data.idElemento,
                                    interfaces: interfaces
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    var respuesta = response.responseText.split("|");
                                    var status = respuesta[0];
                                    var mensaje = respuesta[1];
                                    
                                    if(status=="OK")
                                    {
                                        Ext.Msg.alert('Mensaje',mensaje, function(btn){
                                            if(btn=='ok'){
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje ',mensaje );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
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
        title: 'Administracion de Puertos',
        modal: true,
        width: 700,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
        
}


function clonarElemento(data) {

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
            columns: 1
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:20px'
        },
        items: [
            //elemento
            {
                xtype: 'fieldset',
                title: 'Informacion del Elemento',
                defaultType: 'textfield',
                defaults: {
                    width: 400,
                    height: 20
                },
                items: [
                    {
                        xtype: 'textfield',
                        id: 'nombreSpliterNuevo',
                        name: 'nombreSpliterNuevo',
                        fieldLabel: 'Nombre Elemento',
                        readOnly: false,
                        allowBlank:false

                    },
                    {
                        xtype: 'combobox',
                        allowBlank:false,
                        id: 'splitterPrimario',
                        name: 'splitterPrimario',
                        fieldLabel: 'Splitter L1/ODF',
                        valueField: 'id_elemento',
                        displayField: 'elemento',
                        loadingText: 'Buscando ...',
                        store: storeSpliterPrimario,
                        listClass: 'x-combo-list-small',
                        queryMode: 'remote',
                        listeners: {
                            select: {
                                fn: function(comp, record, index) {
                                    if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;")
                                        comp.setValue(null);
                                    storeInterfacesElemento.proxy.extraParams = {idElemento: comp.getValue(), estado:'not connect', tipoInterface: 'OUT%' };
                                    storeInterfacesElemento.load({params: {}});
                                }
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        allowBlank:false,
                        id: 'puertoSplitter',
                        name: 'puertoSplitter',
                        fieldLabel: 'OUT L1',
                        valueField: 'idInterface',
                        displayField: 'nombreInterface',
                        loadingText: 'Buscando ...',
                        store: storeInterfacesElemento,
                        listClass: 'x-combo-list-small',
                        queryMode: 'local',
                        listeners: {
                            select: {
                                fn: function(comp, record, index) {
                                    Ext.Ajax.request({
                                        url: getInterfaceEnalceOlt,
                                        method: 'get',
                                        timeout: 10000,
                                        params: {
                                            idInterface: comp.getValue()
                                        },
                                        success: function(response) {
                                            var json = Ext.JSON.decode(response.responseText);
                                            if (json.status == "ERROR"){
                                                Ext.getCmp('splitterPrimario').value="";
                                                Ext.getCmp('splitterPrimario').setRawValue("");
                                                Ext.getCmp('puertoSplitter').value="";
                                                Ext.getCmp('puertoSplitter').setRawValue("");
                                                
                                                Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                                    if (btn == 'ok') {
                                                    }
                                                });    
                                                
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                    //---------------------------------------
                ]//cierre del container table
            }, //cierre informacion ont
        ], //cierre items
        buttons: [{
                text: 'Clonar',
                formBind: true,
                handler: function() {

                    if (true) {

                        var nombreSpliterNuevo = Ext.getCmp('nombreSpliterNuevo').getValue();
                        var idSplitterPrimario = Ext.getCmp('splitterPrimario').getValue();
                        var idPuertoSplitter = Ext.getCmp('puertoSplitter').getValue();
                                    
                        Ext.get(formPanel.getId()).mask('Clonando');
                        Ext.Ajax.request({
                            url: clonarSplitter,
                            method: 'post',
                            timeout: 10000,
                            params: {
                                idElemento: data.idElemento,
                                nombreSpliterNuevo: nombreSpliterNuevo,
                                idPuertoSplitter: idPuertoSplitter
                            },
                            success: function(response) {
                                var json = Ext.JSON.decode(response.responseText);

                                    Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                            win.destroy();
                                        }
                                    });
                                
                            },
                            failure: function(result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                    else {
                        Ext.Msg.alert("Failed", "Favor Revise los campos", function(btn) {
                            if (btn == 'ok') {
                            }
                        });
                    }

                }
            }, {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Clonación de splitter',
        modal: true,
        width: 450,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();

}

function obtenerInterfaces(){
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridAdministracionPuertos.getStore().getCount();
  array_relaciones['interfaces'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridAdministracionPuertos.getStore().getCount(); i++)
  {
  	array_data.push(gridAdministracionPuertos.getStore().getAt(i).data);
  }
  array_relaciones['interfaces'] = array_data;
  Ext.getCmp('jsonInterfaces').setValue(Ext.JSON.encode(array_relaciones));
}
