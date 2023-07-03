Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();

    var connGrabandoDatos = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Grabando los datos, Por favor espere!!',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
    
    var storeMarcas = new Ext.data.Store({ 
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosRadio',
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
                tipoElemento: 'RADIO'
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
    
    var storePop = new Ext.data.Store({ 
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../elemento/pop/getEncontradosPop',
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento: '',
                canton: '',
                jurisdiccion: '',
                estado: 'Todos'
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
                {name:'nombreElemento', mapping:'nombreElemento'}
              ]
    });

    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEncontradosRadio',
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
                popElemento:'',
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idElemento', mapping:'idElemento'},
                    {name:'nombreElemento', mapping:'nombreElemento'},
                    {name:'nombreElementoPop', mapping:'nombreElementoPop'},
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
                    {name: 'botonOperatividad', mapping: 'botonOperatividad'}
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
                  header: 'Nombre Radio',
                  xtype: 'templatecolumn', 
                  width: 300,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Ip:</span><span>{ipElemento}</span></br>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <span class="bold">Nodo:</span><span>{nombreElementoPop}</span></br>\n\
                        <tpl if="switchTelconet!=\'N/A\'">\n\
                            <span class="bold">Elemento Inicio:</span>{switchTelconet}</br> \n\
                            <span class="bold">Puerto Inicio:</span>{puertoSwitch}\n\
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
                  header: 'Elemento Operativo',
                  dataIndex: 'botonOperatividad',
                  width: 130,
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
                    width: 170,
//                    xtype: 'container',
                    
                    items: [
                        
                        {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-show'
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = ""+rec.get('idElemento')+"/showRadio";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action2') == "button-grid-invisible") 
                                this.items[1].tooltip = '';
                            else 
                                this.items[1].tooltip = 'Editar';
                            
                            return rec.get('action2')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action2')!="button-grid-invisible")
                                window.location = ""+rec.get('idElemento')+"/editRadio";
                                //alert(rec.get('nombre'));
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('action3') == "button-grid-invisible") 
                                    this.items[2].tooltip = '';
                                else 
                                    this.items[2].tooltip = 'Eliminar';

                                return rec.get('action3')
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('action3')!="button-grid-invisible")
                                    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                        if(btn=='yes'){
                                            Ext.Ajax.request({
                                                url: ""+rec.get('idElemento')+"/deleteRadio",
                                                method: 'post',
                                                params: { param : rec.get('idElemento')},
                                                success: function(response){
                                                    var text = response.responseText;
                                                    if(text == "SERVICIOS ACTIVOS"){
                                                        Ext.Msg.alert(
                                                             'Mensaje',
                                                            'NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN <BR> SERVICIOS ACTIVOS, FAVOR REVISAR!', 
                                                            function(btn){
                                                                if(btn=='ok'){;
                                                                    store.load();
                                                                }
                                                            });
                                                    }
                                                    else{
                                                        if (text == "ENLACES ACTIVOS")
                                                        {
                                                            Ext.Msg.alert(
                                                                    'Mensaje',
                                                                    'NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN <BR>'+
                                                                    ' ENLACES ACTIVOS, FAVOR REVISAR!.',
                                                                    function(btn) {
                                                                        if (btn == 'ok') {
                                                                            store.load();
                                                                        }
                                                                    });
                                                        }
                                                        else
                                                        {
                                                            if (text == "PROBLEMAS TRANSACCION")
                                                            {
                                                                Ext.Msg.alert(
                                                                    'Mensaje',
                                                                    'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.',
                                                                    function(btn) {
                                                                        if (btn == 'ok') {
                                                                            store.load();
                                                                        }
                                                                    });
                                                            }
                                                            else
                                                            {
                                                                store.load();
                                                            }
                                                        }
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
                                    if(rec.get('modeloElemento')=="411AH" || rec.get('modeloElemento')=="433AH"){
                                        return 'button-grid-verMacs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Macs',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="411AH" || modelo=="433AH"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsRadio");
                                        }
                                        else{
                                            alert("No existe Accion para esta Radio");
                                        }
                                    }
                                }
                        },
                        //DEJAR SIN OPERATIVIDAD EL RADIO
                        {
                            getClass: function (v, meta, rec) {
                                strBtnOperativo = 'button-grid-invisible';
                                if (rec.get('botonOperatividad') == 'SI')
                                {
                                    var permiso = $("#ROLE_155-2777");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        strBtnOperativo = 'button-grid-invisible';
                                    }
                                    else {
                                        strBtnOperativo = 'button-grid-cancelarCliente';
                                    }
                                }

                                return strBtnOperativo;
                            },
                            tooltip: 'Dejar sin Operatividad',
                            handler: function (grid, rowIndex, colIndex) 
                            {
                                var objElemento = store.getAt(rowIndex);
                                if (objElemento.get('botonOperatividad') == "SI")
                                {
                                    Ext.Msg.confirm('Alerta', 'Se dejará radio sin Operatividad. Desea continuar?', function(btn) {
                                        if (btn == 'yes')
                                        {
                                            connGrabandoDatos.request({
                                                timeout: 900000,
                                                url: url_QuitarOperatividad,
                                                method: 'post',
                                                params: {idElemento: grid.getStore().getAt(rowIndex).data.idElemento},
                                                success: function(response) {

                                                    var text = response.responseText;

                                                    if (text == "PROBLEMAS TRANSACCION")
                                                    {
                                                        Ext.Msg.alert('Error ', 'Existieron problemas al realizar la transaccion, ' +
                                                            'favor notificar a sistemas');
                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Mensaje', "Operación realizada con exito", function(btn) {
                                                            if (btn == 'ok') {
                                                                store.load();
                                                            }
                                                        });
                                                    }
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
                                    Ext.Msg.alert('Mensaje ', 'Este elemento ya se encuentra marcado SIN OPERATIVIDAD.');
                                }
                            }
                        },
                        //DEJAR CON OPERATIVIDAD EL RADIO
                        {
                            getClass: function (v, meta, rec) {
                                strBtnOperativo = 'button-grid-invisible';
                                if (rec.get('botonOperatividad') == 'NO')
                                {
                                    var permiso = $("#ROLE_155-2777");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        strBtnOperativo = 'button-grid-invisible';
                                    }
                                    else {
                                        strBtnOperativo = 'button-grid-informacionTecnica';
                                    }
                                }

                                return strBtnOperativo;
                            },
                            tooltip: 'Dejar con Operatividad',
                            handler: function (grid, rowIndex, colIndex) 
                            {
                                var objElemento = store.getAt(rowIndex);
                                if (objElemento.get('botonOperatividad') == "NO")
                                {
                                    Ext.Msg.confirm('Alerta', 'Se dejará radio con Operatividad. Desea continuar?', function(btn) {
                                        if (btn == 'yes')
                                        {
                                            connGrabandoDatos.request({
                                                timeout: 900000,
                                                url: url_AgregarOperatividad,
                                                method: 'post',
                                                params: {idElemento: grid.getStore().getAt(rowIndex).data.idElemento},
                                                success: function(response) {

                                                    var text = response.responseText;

                                                    if (text == "PROBLEMAS TRANSACCION")
                                                    {
                                                        Ext.Msg.alert('Error ', 'Existieron problemas al realizar la transaccion, ' +
                                                            'favor notificar a sistemas');
                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Mensaje', "Operación realizada con exito", function(btn) {
                                                            if (btn == 'ok') {
                                                                store.load();
                                                            }
                                                        });
                                                    }
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
                                    Ext.Msg.alert('Mensaje ', 'Este elemento ya se encuentra marcado CON OPERATIVIDAD.');
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
                        {
                            xtype: 'combobox',
                            id: 'sltPop',
                            fieldLabel: 'Pop',
                            store: storePop,
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            loadingText: 'Buscando ...',
                            hideTrigger: false,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
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
                                            storePop.load({
                                                
                                            });                                  
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

function buscar(){
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,
        ipElemento: Ext.getCmp('txtIp').value,
        marcaElemento: Ext.getCmp('sltMarca').value,
        modeloElemento: Ext.getCmp('sltModelo').value,
        canton: Ext.getCmp('sltCanton').value,
        jurisdiccion: Ext.getCmp('sltJurisdiccion').value,
        popElemento: Ext.getCmp('sltPop').value,
        estado: Ext.getCmp('sltEstado').value
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
    
    Ext.getCmp('sltPop').value="";
    Ext.getCmp('sltPop').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,
        ipElemento: Ext.getCmp('txtIp').value,
        marcaElemento: Ext.getCmp('sltMarca').value,
        modeloElemento: Ext.getCmp('sltModelo').value,
        canton: Ext.getCmp('sltCanton').value,
        jurisdiccion: Ext.getCmp('sltJurisdiccion').value,
        popElemento: Ext.getCmp('sltPop').value,
        estado: Ext.getCmp('sltEstado').value
    }});
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
                    url: "radio/deleteAjaxRadio",
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

//ejecuta los scripts sin variable para los dslams
function verScriptDslam(data, action){
    console.log(data.modeloElemento);
    Ext.get(grid.getId()).mask('Loading...');
    Ext.Ajax.request({
        url: action,
        method: 'post',
        waitMsg: 'Esperando Respuesta de la Radio',
        timeout: 400000,
        params: { modelo: data.modeloElemento,
                  idElemento: data.idElemento
                },
        success: function(response){
            
                var variable = response.responseText.split("&");
                var resp = variable[0];
                var script = variable[1];
                
                if(script=="NO EXISTE RELACION TAREA - ACCION"){
                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                    Ext.get(grid.getId()).unmask();
                }
                else{
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(grid.getId()).unmask();
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
                                title: 'Script',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 70
                                },
                                items: [

                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 75,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    },

                                ]
                            },
        //                    {
        //                        xtype: 'component',
        //                        html: 'Comando: '+json.encontrados[0].script
        //                    }
                            ,{
                            xtype: 'fieldset',
                            title: 'Configuracion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 550,
                                height: 325
                            },
                            items: [

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'hbox',
                                        pack: 'left'
                                    },
                                    items: [{
                                        xtype: 'textareafield',
                                        id:'mensaje',
                                        name: 'mensaje',
                                        fieldLabel: 'Configuracion',
                                        value: ejecucion.mensaje,
                                        cols: 75,
                                        rows: 19,
                                        anchor: '100%',
                                        readOnly:true
                                    }]
                                },

                            ]
                        }],
                        buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function(){
                                win.destroy();
                            }
                        }]
                    });
                    
                    var win = Ext.create('Ext.window.Window', {
                        title: 'Ver Configuracion',
                        modal: true,
                        width: 630,
                        height: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                    
                }//cierre else
            
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });    
}