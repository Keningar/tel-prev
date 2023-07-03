var pi = 3.14159265358979;

/* Ellipsoid model constants (actual values here are for WGS84) */
var sm_a = 6378137.0;
var sm_b = 6356752.314;
var sm_EccSquared = 6.69437999013e-03;

var UTMScaleFactor = 0.9996;


Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var storeMarcas = new Ext.data.Store({ 
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosCaja',
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
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosPorMarca',
            extraParams: {
                idMarca: '',
                tipoElemento: 'CAJA DISPERSION'
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
        pageSize: 100,
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

    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url : 'getEncontradosCaja',
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
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idElemento', mapping:'idElemento'},
                    {name:'nombreElemento', mapping:'nombreElemento'},
                    {name:'ipElemento', mapping:'ipElemento'},
                    {name:'cantonNombre', mapping:'cantonNombre'},
                    {name:'jurisdiccionNombre', mapping:'jurisdiccionNombre'},
                    {name:'nivel', mapping:'nivel'},
                    {name:'ubicadoEn', mapping:'ubicadoEn'},
                    {name:'switchTelconet', mapping:'switchTelconet'},
                    {name:'puertoSwitch', mapping:'puertoSwitch'},
                    {name:'marcaElemento', mapping:'marcaElemento'},
                    {name:'modeloElemento', mapping:'modeloElemento'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}
                  ],
//        autoLoad: true
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
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
                    //tbfill -> alinea los items siguientes a la derecha
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
                  header: 'Caja',
                  xtype: 'templatecolumn', 
                  width: 300,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <span class="bold">Nivel:</span><span>{nivel}</span></br>\n\\n\
                        <span class="bold">Ubicado En:</span><span>{ubicadoEn}</span></br>\n\
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
                    items: [{
                        getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_232-6");
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
                            window.location = ""+rec.get('idElemento')+"/showCaja";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_232-5");
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
                            
                            return rec.get('action2')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action2')!="button-grid-invisible")
                                window.location = ""+rec.get('idElemento')+"/editCaja";
                                //alert(rec.get('nombre'));
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                             var permiso = $("#ROLE_232-8");
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
                                            url: "deleteAjaxCaja",
                                            method: 'post',
                                            params: { param : rec.get('idElemento')},
                                            success: function(response){
                                                var text = response.responseText;
                                                if(text=="OK"){
                                                    store.load();
                                                }
                                                else{
                                                    Ext.Msg.alert('Error ','Error: Aun Existen Elementos Activos dentro de este Elemento' );
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
                                return 'button-grid-Gmaps'
                            },
                            tooltip: 'Ver Mapa',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                latlon = new Array(2);
                                if(rec.get("latitud")!=0 && rec.get("longitud")!=0){
//                                    UTMXYToLatLon (rec.get("longitud"), rec.get("latitud"), 17, true, latlon);
//                                    console.log("latitud"+RadToDeg (latlon[0]));
//                                    console.log("longitud"+RadToDeg (latlon[1]));
//                                    showVerMapa(RadToDeg (latlon[0]),RadToDeg (latlon[1]));
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
//                            triggerAction: 'all',
//                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
//                            hideTrigger: false,
                            store: storeMarcas,
//                            lazyRender: true,
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
//                            triggerAction: 'all',
//                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
//                            hideTrigger: false,
//                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
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
//                            triggerAction: 'all',
//                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
//                            hideTrigger: false,
                            store: storeCantones,
//                            lazyRender: true,
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
//                            triggerAction: 'all',
//                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
//                            hideTrigger: false,
//                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
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
    
});

function buscar(){
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,
        marcaElemento: Ext.getCmp('sltMarca').value,
        modeloElemento: Ext.getCmp('sltModelo').value,
        canton: Ext.getCmp('sltCanton').value,
        jurisdiccion: Ext.getCmp('sltJurisdiccion').value,
        estado: Ext.getCmp('sltEstado').value
    }});
}

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
        nombre: Ext.getCmp('txtNombre').value,
        estado: Ext.getCmp('sltMarca').value,
        estado: Ext.getCmp('sltModelo').value,
        estado: Ext.getCmp('sltCanton').value,
        estado: Ext.getCmp('sltJurisdiccion').value,
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
//        alert(param);
      }      
      if(estado == 0)
      {
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "deleteAjaxCaja",
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

    /*
    * DegToRad
    *
    * Converts degrees to radians.
    *
    */
    function DegToRad (deg)
    {
        return (deg / 180.0 * pi)
    }


    /*
    * RadToDeg
    *
    * Converts radians to degrees.
    *
    */
    function RadToDeg (rad)
    {
        return (rad / pi * 180.0)
    }




    /*
    * ArcLengthOfMeridian
    *
    * Computes the ellipsoidal distance from the equator to a point at a
    * given latitude.
    *
    * Reference: Hoffmann-Wellenhof, B., Lichtenegger, H., and Collins, J.,
    * GPS: Theory and Practice, 3rd ed.  New York: Springer-Verlag Wien, 1994.
    *
    * Inputs:
    *     phi - Latitude of the point, in radians.
    *
    * Globals:
    *     sm_a - Ellipsoid model major axis.
    *     sm_b - Ellipsoid model minor axis.
    *
    * Returns:
    *     The ellipsoidal distance of the point from the equator, in meters.
    *
    */
    function ArcLengthOfMeridian (phi)
    {
        var alpha, beta, gamma, delta, epsilon, n;
        var result;

        /* Precalculate n */
        n = (sm_a - sm_b) / (sm_a + sm_b);

        /* Precalculate alpha */
        alpha = ((sm_a + sm_b) / 2.0)
           * (1.0 + (Math.pow (n, 2.0) / 4.0) + (Math.pow (n, 4.0) / 64.0));

        /* Precalculate beta */
        beta = (-3.0 * n / 2.0) + (9.0 * Math.pow (n, 3.0) / 16.0)
           + (-3.0 * Math.pow (n, 5.0) / 32.0);

        /* Precalculate gamma */
        gamma = (15.0 * Math.pow (n, 2.0) / 16.0)
            + (-15.0 * Math.pow (n, 4.0) / 32.0);
    
        /* Precalculate delta */
        delta = (-35.0 * Math.pow (n, 3.0) / 48.0)
            + (105.0 * Math.pow (n, 5.0) / 256.0);
    
        /* Precalculate epsilon */
        epsilon = (315.0 * Math.pow (n, 4.0) / 512.0);
    
    /* Now calculate the sum of the series and return */
    result = alpha
        * (phi + (beta * Math.sin (2.0 * phi))
            + (gamma * Math.sin (4.0 * phi))
            + (delta * Math.sin (6.0 * phi))
            + (epsilon * Math.sin (8.0 * phi)));

    return result;
    }



    /*
    * UTMCentralMeridian
    *
    * Determines the central meridian for the given UTM zone.
    *
    * Inputs:
    *     zone - An integer value designating the UTM zone, range [1,60].
    *
    * Returns:
    *   The central meridian for the given UTM zone, in radians, or zero
    *   if the UTM zone parameter is outside the range [1,60].
    *   Range of the central meridian is the radian equivalent of [-177,+177].
    *
    */
    function UTMCentralMeridian (zone)
    {
        var cmeridian;

        cmeridian = DegToRad (-183.0 + (zone * 6.0));
    
        return cmeridian;
    }



    /*
    * FootpointLatitude
    *
    * Computes the footpoint latitude for use in converting transverse
    * Mercator coordinates to ellipsoidal coordinates.
    *
    * Reference: Hoffmann-Wellenhof, B., Lichtenegger, H., and Collins, J.,
    *   GPS: Theory and Practice, 3rd ed.  New York: Springer-Verlag Wien, 1994.
    *
    * Inputs:
    *   y - The UTM northing coordinate, in meters.
    *
    * Returns:
    *   The footpoint latitude, in radians.
    *
    */
    function FootpointLatitude (y)
    {
        var y_, alpha_, beta_, gamma_, delta_, epsilon_, n;
        var result;
        
        /* Precalculate n (Eq. 10.18) */
        n = (sm_a - sm_b) / (sm_a + sm_b);
        	
        /* Precalculate alpha_ (Eq. 10.22) */
        /* (Same as alpha in Eq. 10.17) */
        alpha_ = ((sm_a + sm_b) / 2.0)
            * (1 + (Math.pow (n, 2.0) / 4) + (Math.pow (n, 4.0) / 64));
        
        /* Precalculate y_ (Eq. 10.23) */
        y_ = y / alpha_;
        
        /* Precalculate beta_ (Eq. 10.22) */
        beta_ = (3.0 * n / 2.0) + (-27.0 * Math.pow (n, 3.0) / 32.0)
            + (269.0 * Math.pow (n, 5.0) / 512.0);
        
        /* Precalculate gamma_ (Eq. 10.22) */
        gamma_ = (21.0 * Math.pow (n, 2.0) / 16.0)
            + (-55.0 * Math.pow (n, 4.0) / 32.0);
        	
        /* Precalculate delta_ (Eq. 10.22) */
        delta_ = (151.0 * Math.pow (n, 3.0) / 96.0)
            + (-417.0 * Math.pow (n, 5.0) / 128.0);
        	
        /* Precalculate epsilon_ (Eq. 10.22) */
        epsilon_ = (1097.0 * Math.pow (n, 4.0) / 512.0);
        	
        /* Now calculate the sum of the series (Eq. 10.21) */
        result = y_ + (beta_ * Math.sin (2.0 * y_))
            + (gamma_ * Math.sin (4.0 * y_))
            + (delta_ * Math.sin (6.0 * y_))
            + (epsilon_ * Math.sin (8.0 * y_));
        
        return result;
    }



    /*
    * MapLatLonToXY
    *
    * Converts a latitude/longitude pair to x and y coordinates in the
    * Transverse Mercator projection.  Note that Transverse Mercator is not
    * the same as UTM; a scale factor is required to convert between them.
    *
    * Reference: Hoffmann-Wellenhof, B., Lichtenegger, H., and Collins, J.,
    * GPS: Theory and Practice, 3rd ed.  New York: Springer-Verlag Wien, 1994.
    *
    * Inputs:
    *    phi - Latitude of the point, in radians.
    *    lambda - Longitude of the point, in radians.
    *    lambda0 - Longitude of the central meridian to be used, in radians.
    *
    * Outputs:
    *    xy - A 2-element array containing the x and y coordinates
    *         of the computed point.
    *
    * Returns:
    *    The function does not return a value.
    *
    */
    function MapLatLonToXY (phi, lambda, lambda0, xy)
    {
        var N, nu2, ep2, t, t2, l;
        var l3coef, l4coef, l5coef, l6coef, l7coef, l8coef;
        var tmp;

        /* Precalculate ep2 */
        ep2 = (Math.pow (sm_a, 2.0) - Math.pow (sm_b, 2.0)) / Math.pow (sm_b, 2.0);
    
        /* Precalculate nu2 */
        nu2 = ep2 * Math.pow (Math.cos (phi), 2.0);
    
        /* Precalculate N */
        N = Math.pow (sm_a, 2.0) / (sm_b * Math.sqrt (1 + nu2));
    
        /* Precalculate t */
        t = Math.tan (phi);
        t2 = t * t;
        tmp = (t2 * t2 * t2) - Math.pow (t, 6.0);

        /* Precalculate l */
        l = lambda - lambda0;
    
        /* Precalculate coefficients for l**n in the equations below
           so a normal human being can read the expressions for easting
           and northing
           -- l**1 and l**2 have coefficients of 1.0 */
        l3coef = 1.0 - t2 + nu2;
    
        l4coef = 5.0 - t2 + 9 * nu2 + 4.0 * (nu2 * nu2);
    
        l5coef = 5.0 - 18.0 * t2 + (t2 * t2) + 14.0 * nu2
            - 58.0 * t2 * nu2;
    
        l6coef = 61.0 - 58.0 * t2 + (t2 * t2) + 270.0 * nu2
            - 330.0 * t2 * nu2;
    
        l7coef = 61.0 - 479.0 * t2 + 179.0 * (t2 * t2) - (t2 * t2 * t2);
    
        l8coef = 1385.0 - 3111.0 * t2 + 543.0 * (t2 * t2) - (t2 * t2 * t2);
    
        /* Calculate easting (x) */
        xy[0] = N * Math.cos (phi) * l
            + (N / 6.0 * Math.pow (Math.cos (phi), 3.0) * l3coef * Math.pow (l, 3.0))
            + (N / 120.0 * Math.pow (Math.cos (phi), 5.0) * l5coef * Math.pow (l, 5.0))
            + (N / 5040.0 * Math.pow (Math.cos (phi), 7.0) * l7coef * Math.pow (l, 7.0));
    
        /* Calculate northing (y) */
        xy[1] = ArcLengthOfMeridian (phi)
            + (t / 2.0 * N * Math.pow (Math.cos (phi), 2.0) * Math.pow (l, 2.0))
            + (t / 24.0 * N * Math.pow (Math.cos (phi), 4.0) * l4coef * Math.pow (l, 4.0))
            + (t / 720.0 * N * Math.pow (Math.cos (phi), 6.0) * l6coef * Math.pow (l, 6.0))
            + (t / 40320.0 * N * Math.pow (Math.cos (phi), 8.0) * l8coef * Math.pow (l, 8.0));
    
        return;
    }
    
    
    
    /*
    * MapXYToLatLon
    *
    * Converts x and y coordinates in the Transverse Mercator projection to
    * a latitude/longitude pair.  Note that Transverse Mercator is not
    * the same as UTM; a scale factor is required to convert between them.
    *
    * Reference: Hoffmann-Wellenhof, B., Lichtenegger, H., and Collins, J.,
    *   GPS: Theory and Practice, 3rd ed.  New York: Springer-Verlag Wien, 1994.
    *
    * Inputs:
    *   x - The easting of the point, in meters.
    *   y - The northing of the point, in meters.
    *   lambda0 - Longitude of the central meridian to be used, in radians.
    *
    * Outputs:
    *   philambda - A 2-element containing the latitude and longitude
    *               in radians.
    *
    * Returns:
    *   The function does not return a value.
    *
    * Remarks:
    *   The local variables Nf, nuf2, tf, and tf2 serve the same purpose as
    *   N, nu2, t, and t2 in MapLatLonToXY, but they are computed with respect
    *   to the footpoint latitude phif.
    *
    *   x1frac, x2frac, x2poly, x3poly, etc. are to enhance readability and
    *   to optimize computations.
    *
    */
    function MapXYToLatLon (x, y, lambda0, philambda)
    {
        var phif, Nf, Nfpow, nuf2, ep2, tf, tf2, tf4, cf;
        var x1frac, x2frac, x3frac, x4frac, x5frac, x6frac, x7frac, x8frac;
        var x2poly, x3poly, x4poly, x5poly, x6poly, x7poly, x8poly;
    	
        /* Get the value of phif, the footpoint latitude. */
        phif = FootpointLatitude (y);
        	
        /* Precalculate ep2 */
        ep2 = (Math.pow (sm_a, 2.0) - Math.pow (sm_b, 2.0))
              / Math.pow (sm_b, 2.0);
        	
        /* Precalculate cos (phif) */
        cf = Math.cos (phif);
        	
        /* Precalculate nuf2 */
        nuf2 = ep2 * Math.pow (cf, 2.0);
        	
        /* Precalculate Nf and initialize Nfpow */
        Nf = Math.pow (sm_a, 2.0) / (sm_b * Math.sqrt (1 + nuf2));
        Nfpow = Nf;
        	
        /* Precalculate tf */
        tf = Math.tan (phif);
        tf2 = tf * tf;
        tf4 = tf2 * tf2;
        
        /* Precalculate fractional coefficients for x**n in the equations
           below to simplify the expressions for latitude and longitude. */
        x1frac = 1.0 / (Nfpow * cf);
        
        Nfpow *= Nf;   /* now equals Nf**2) */
        x2frac = tf / (2.0 * Nfpow);
        
        Nfpow *= Nf;   /* now equals Nf**3) */
        x3frac = 1.0 / (6.0 * Nfpow * cf);
        
        Nfpow *= Nf;   /* now equals Nf**4) */
        x4frac = tf / (24.0 * Nfpow);
        
        Nfpow *= Nf;   /* now equals Nf**5) */
        x5frac = 1.0 / (120.0 * Nfpow * cf);
        
        Nfpow *= Nf;   /* now equals Nf**6) */
        x6frac = tf / (720.0 * Nfpow);
        
        Nfpow *= Nf;   /* now equals Nf**7) */
        x7frac = 1.0 / (5040.0 * Nfpow * cf);
        
        Nfpow *= Nf;   /* now equals Nf**8) */
        x8frac = tf / (40320.0 * Nfpow);
        
        /* Precalculate polynomial coefficients for x**n.
           -- x**1 does not have a polynomial coefficient. */
        x2poly = -1.0 - nuf2;
        
        x3poly = -1.0 - 2 * tf2 - nuf2;
        
        x4poly = 5.0 + 3.0 * tf2 + 6.0 * nuf2 - 6.0 * tf2 * nuf2
        	- 3.0 * (nuf2 *nuf2) - 9.0 * tf2 * (nuf2 * nuf2);
        
        x5poly = 5.0 + 28.0 * tf2 + 24.0 * tf4 + 6.0 * nuf2 + 8.0 * tf2 * nuf2;
        
        x6poly = -61.0 - 90.0 * tf2 - 45.0 * tf4 - 107.0 * nuf2
        	+ 162.0 * tf2 * nuf2;
        
        x7poly = -61.0 - 662.0 * tf2 - 1320.0 * tf4 - 720.0 * (tf4 * tf2);
        
        x8poly = 1385.0 + 3633.0 * tf2 + 4095.0 * tf4 + 1575 * (tf4 * tf2);
        
        
        /* Calculate latitude */
        philambda[0] = phif + x2frac * x2poly * (x * x)
        	+ x4frac * x4poly * Math.pow (x, 4.0)
        	+ x6frac * x6poly * Math.pow (x, 6.0)
        	+ x8frac * x8poly * Math.pow (x, 8.0);
        	
        /* Calculate longitude */
        philambda[1] = lambda0 + x1frac * x
        	+ x3frac * x3poly * Math.pow (x, 3.0)
        	+ x5frac * x5poly * Math.pow (x, 5.0)
        	+ x7frac * x7poly * Math.pow (x, 7.0);
        
        return;
    }




    /*
    * LatLonToUTMXY
    *
    * Converts a latitude/longitude pair to x and y coordinates in the
    * Universal Transverse Mercator projection.
    *
    * Inputs:
    *   lat - Latitude of the point, in radians.
    *   lon - Longitude of the point, in radians.
    *   zone - UTM zone to be used for calculating values for x and y.
    *          If zone is less than 1 or greater than 60, the routine
    *          will determine the appropriate zone from the value of lon.
    *
    * Outputs:
    *   xy - A 2-element array where the UTM x and y values will be stored.
    *
    * Returns:
    *   The UTM zone used for calculating the values of x and y.
    *
    */
    function LatLonToUTMXY (lat, lon, zone, xy)
    {
        MapLatLonToXY (lat, lon, UTMCentralMeridian (zone), xy);

        /* Adjust easting and northing for UTM system. */
        xy[0] = xy[0] * UTMScaleFactor + 500000.0;
        xy[1] = xy[1] * UTMScaleFactor;
        if (xy[1] < 0.0)
            xy[1] = xy[1] + 10000000.0;

        return zone;
    }
    
    
    
    /*
    * UTMXYToLatLon
    *
    * Converts x and y coordinates in the Universal Transverse Mercator
    * projection to a latitude/longitude pair.
    *
    * Inputs:
    *	x - The easting of the point, in meters.
    *	y - The northing of the point, in meters.
    *	zone - The UTM zone in which the point lies.
    *	southhemi - True if the point is in the southern hemisphere;
    *               false otherwise.
    *
    * Outputs:
    *	latlon - A 2-element array containing the latitude and
    *            longitude of the point, in radians.
    *
    * Returns:
    *	The function does not return a value.
    *
    */
    function UTMXYToLatLon (x, y, zone, southhemi, latlon)
    {
        var cmeridian;
        	
        x -= 500000.0;
        x /= UTMScaleFactor;
        	
        /* If in southern hemisphere, adjust y accordingly. */
        if (southhemi)
        y -= 10000000.0;
        		
        y /= UTMScaleFactor;
        
        cmeridian = UTMCentralMeridian (zone);
        MapXYToLatLon (x, y, cmeridian, latlon);
        	
        return;
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
//                width:600,
//                height:450,
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
//                width: 600,
//                height: 450,
//                minHeight: 380,
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
        //var latlng = new google.maps.LatLng(-2.176963, -79.883673);
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

        //google.maps.event.addListener(mapa, 'dblclick', function(event) {
        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng, 
            map: mapa
        });
        mapa.setZoom(17);
        //  dd2dms(event.latLng.lat(),event.latLng.lng());
        //});
    }
} 

function cierraVentanaMapa(){
    winVerMapa.close();
    winVerMapa.destroy();
    
}