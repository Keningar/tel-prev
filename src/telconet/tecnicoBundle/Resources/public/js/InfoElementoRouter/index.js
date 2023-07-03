/**
 * Funcion que sirve para cargar la data en el grid de elementos-router
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 8-12-2015
 * */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var storeMarcas = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getMarcasElementosRouter,
            extraParams: {
                tipoElemento: 'ROUTER'
            },
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
            url : getModeloElementoPorMarca,
            extraParams: {
                idMarca: '',
                tipoElemento: 'ROUTER'
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
        pageSize: 100,
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
        pageSize: 100,
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
            timeout: 3000000,
            type: 'ajax',
            url : getElementoRouter,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento:  '',
                modeloElemento: '',
                canton:         '',
                jurisdiccion:   '',
                tipoElemento:   'ROUTER',
                estado:         'Todos'
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
                    {name:'longitud',           mapping:'longitud'},
                    {name:'latitud',            mapping:'latitud'},
                    {name:'estado',             mapping:'estado'},
                    {name:'action1',            mapping:'action1'},
                    {name:'action2',            mapping:'action2'},
                    {name:'action3',            mapping:'action3'}
                  ]
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
    
    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 294,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        iconCls: 'icon-grid',
        viewConfig: { enableTextSelection: true },
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
                        handler: function(){ eliminarAlgunos();}
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
                  id: 'elemento',
                  header: 'Router',
                  xtype: 'templatecolumn', 
                  width: 300,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <tpl>\n\
                        </tpl>'
                
                },
                {
                  header: 'Marca',
                  dataIndex: 'marcaElemento',
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
                  width: 100,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 155,
                    items: [
                        //SHOW ELEMENTO
                        {
                            getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_316-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

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
                                window.location = ""+rec.get('idElemento')+"/showRouter";
                            }
                        },
                        //EDITAR ELEMENTO
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_316-5");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

                                if(!boolPermiso){ 
                                    return 'button-grid-invisible';
                                }
                                else{
                                    if (rec.get('action2') == "button-grid-invisible") 
                                        this.items[1].tooltip = '';
                                    else 
                                        this.items[1].tooltip = 'Editar';
                                }

                                return 'button-grid-invisible'; //cambiar return rec.get('action2')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action2')!="button-grid-invisible"){
                                    window.location = ""+rec.get('idElemento')+"/editRouter";
                                }
                            }
                        },
                        //ELIMINAR ELEMENTO
                        {
                            getClass: function(v, meta, rec) {
                                 var permiso = $("#ROLE_316-8");
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

                                return 'button-grid-invisible'; //cambiar return rec.get('action3')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                return 'button-grid-invisible'; //eliminar return

                                if(rec.get('action3')!="button-grid-invisible")
                                {
                                    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                        if(btn=='yes'){
                                            Ext.Ajax.request({
                                                url: ajaxDeleteElementoRouter,
                                                method: 'post',
                                                params: { param : rec.get('idElemento')},
                                                success: function(response){
                                                    var text = response.responseText;
                                                    if(text=="OK"){
                                                        store.load();
                                                    }
                                                    else{
                                                        Ext.Msg.alert('Error ','Error: Aun Existen Elementos dentro de este Elemento' );
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
    
    //panel del filtro
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
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
                                ['Modificado','Modificado'],
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
                            typeAhead: true,
                            displayField:'nombreMarcaElemento',
                            valueField: 'idMarcaElemento',
                            loadingText: 'Buscando ...',
                            store: storeMarcas,
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
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
                        { width: '10%',border:false} //final
                        
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
    store.load({
        callback: function() {
            storeMarcas.load({
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

/**
 * Funcion que llena los parametros que se envian para buscar los switches
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 8-12-2015
 * */
function buscar()
{
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,
        marcaElemento:  Ext.getCmp('sltMarca').value,
        modeloElemento: Ext.getCmp('sltModelo').value,
        canton:         Ext.getCmp('sltCanton').value,
        jurisdiccion:   Ext.getCmp('sltJurisdiccion').value,
        tipoElemento:   'ROUTER',
        estado:         Ext.getCmp('sltEstado').value
    }});
}

/**
 * Funcion que limpia los parametros que se envian para buscar los switches
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 8-12-2015
 * */
function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    
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
    
    store.load({params: {
        nombre:         Ext.getCmp('txtNombre').value,
        marca:          Ext.getCmp('sltMarca').value,
        modelo:         Ext.getCmp('sltModelo').value,
        canton:         Ext.getCmp('sltCanton').value,
        jurisdiccion:   Ext.getCmp('sltJurisdiccion').value,
        estado:         Ext.getCmp('sltEstado').value
    }});
}

function eliminarAlgunos(){
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
                    url: ajaxDeleteElementoRouter,
                    method: 'post',
                    params: { param : param},
                    success: function(response){
                        var text = response.responseText;
                        if(text=="OK"){
                            store.load();
                        }
                        else{
                            Ext.Msg.alert('Error ','Error: Aun Existen Elementos dentro de este Elemento' );
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
