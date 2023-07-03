

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
                tipoElemento: 'SWITCH',
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
                            var permiso = $("#ROLE_358-4517");
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
                             var permiso = $("#ROLE_358-4518");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                if (rec.get('estadoElemento')=='Activo' ||rec.get('estadoElemento')=='Pendiente')
                                {
                                    if(boolPermiso){ 
                                        return 'button-grid-BigDelete';
                                    }
                                    else{
                                        return 'icon-invisible';
                                    } 
                                }
                            
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                    if(btn=='yes'){
                                        Ext.Ajax.request({
                                            url: "deleteAjax",
                                            method: 'post',
                                            params: { param : rec.get('idElemento')},
                                            success: function(response){
                                                var text = response.responseText;
                                                if(text=="OK"){
                                                    Ext.Msg.alert('Mensaje', 'Transacción Exitosa.' );
                                                    store.load();
                                                }
                                                else{
                                                    Ext.Msg.alert('Error',text );
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error','Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                                
                                
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return 'button-grid-Gmaps'
                            },
                            tooltip: 'Ver Mapa',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
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
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '10%',border:false},
                        
                        //-------------------------------------
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
      }      
      if(estado == 0)
      {
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "deleteAjax",
                    method: 'post',
                    params: { param : param},
                    success: function(response){
                        var text = response.responseText;
                        if(text=="OK"){
                            Ext.Msg.alert('Mensaje', 'Transacción Exitosa.' );
                            store.load();
                        }
                        else{
                            Ext.Msg.alert('Error ',text);
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
          Ext.Msg.alert('Error ','No puedes volver a eliminar.');
      }
    }
    else
    {
      alert('Seleccione por lo menos un registro de la lista');
    }
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

        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng, 
            map: mapa
        });
        mapa.setZoom(17);

    }
} 

function cierraVentanaMapa(){
    winVerMapa.close();
    winVerMapa.destroy();
    
}