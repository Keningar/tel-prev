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

Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();

    var storeMarcas = new Ext.data.Store({ 
        total: 'total',
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosDslam',
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
//        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosPorMarca',
            extraParams: {
                idMarca: '',
                tipoElemento: 'DSLAM'
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
            url : 'getEncontradosDslam',
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
                    {name:'action4', mapping:'action4'},
                    {name:'botonOperatividad', mapping:'botonOperatividad'},
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
                  id: 'idElemento',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'ipElemento',
                  header: 'Dslam',
                  xtype: 'templatecolumn', 
                  width: 240,
                  tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Ip:</span><span>{ipElemento}</span></br>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <span class="bold">Pop:</span><span>{nombreElementoPop}</span></br>\n\
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
                  width: 60,
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
                            window.location = ""+rec.get('idElemento')+"/showDslam";
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_149-826");
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
                                    window.location = ""+rec.get('idElemento')+"/editDslam";
                                    //alert(rec.get('nombre'));
                                }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_149-827");
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
                                                url: ""+rec.get('idElemento')+"/deleteDslam",
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
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3" || rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048" ){
                                    var permiso = $("#ROLE_149-826");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

                                    //alert(typeof permiso);
                                    if(!boolPermiso){ 

                                        return 'button-grid-invisible';
                                    }
                                    else{
                                        if (rec.get('action4') == "button-grid-invisible") 
                                            this.items[1].tooltip = '';
                                        else 
                                            this.items[1].tooltip = 'Actualizar Perfiles';
                                    }
                                    return rec.get('action4')
                                }
                            },
                                tooltip: 'Actualizar Perfiles Dslam',          
                                handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3" || rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048" ){
                                      if(rec.get('action4')!="button-grid-invisible")
                                          Ext.Msg.confirm('Alerta','Se actualizaran los perfiles del dslam . Desea continuar?', function(btn){
                                              if(btn=='yes'){
                                                  Ext.get(grid.getId()).mask();
                                                  Ext.Ajax.request({
                                                      url: ""+rec.get('idElemento')+"/actualizarPerfilesDslam",
                                                      method: 'post',
                                                      params: { param : rec.get('idElemento')},
                                                      success: function(response){

                                                         Ext.Msg.alert('Mensaje','Se actualizaron los perlies . ', function(btn){
                                                              store.load();

                                                         });
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
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    var permiso = $("#ROLE_149-828");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                    if(!boolPermiso){ 
                                        return 'button-grid-invisible';
                                    }
                                    else{
                                        if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                            return 'button-grid-administrarPuertos';
                                        }
                                        if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                            return 'button-grid-administrarPuertos';
                                        }
                                        if(rec.get('modeloElemento')=="6524"){
                                            return 'button-grid-administrarPuertos';
                                        }
                                        if(rec.get('modeloElemento')=="7224"){
                                            return 'button-grid-administrarPuertos';
                                        }
                                        if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                            return 'button-grid-administrarPuertos';
                                        }
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
                                    
                                    if(modelo=="A2024" || modelo=="A2048"){
                                        administrarPuertos(grid.getStore().getAt(rowIndex).data);
                                    }
                                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                        administrarPuertos(grid.getStore().getAt(rowIndex).data);
                                    }
                                    else if(modelo=="6524"){
                                        administrarPuertos(grid.getStore().getAt(rowIndex).data);
                                    }
                                    else if(modelo=="7224"){
                                        administrarPuertos(grid.getStore().getAt(rowIndex).data);
                                    }
                                    else if(modelo=="MEA1" || modelo=="MEA3"){
                                        administrarPuertos(grid.getStore().getAt(rowIndex).data);
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                    
                                }
                            }
                        },//SCRIPTS SIN VARIABLE
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verDslam';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verDslam';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verDslam';
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verDslam';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verDslam';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Configuracion Completa',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    
                                    if(modelo=="A2024" || modelo=="A2048"){
                                        verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionDslamA2024");
                                    }
                                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                        verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionDslamR1");
                                    }
                                    else if(modelo=="6524"){
                                        verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionDslam6524");
                                    }
                                    else if(modelo=="7224"){
                                        verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionDslam7224");
                                    }
                                    else if(modelo=="MEA1" || modelo=="MEA3"){
                                        verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionDslamMea");
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                    
                                }
                            }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verInterfaces';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verInterfaces';
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verInterfaces';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Todos los Puertos',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTodosPuertosDslamA2024");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTodosPuertosDslamR1");
                                        }
                                        else if(modelo=="7224"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTodosPuertosDslam7224");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verMacs';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacs';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacs';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacs';
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
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
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsDslamA2024");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsDslamMea");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD24A"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsDslamR1");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsDslam6524");
                                        }
                                        else if(modelo=="7224"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsDslam7224");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verRendimiento';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verRendimiento';
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verRendimiento';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Rendimiento',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarRendimientoDslamA2024");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarRendimientoDslamR1");
                                        }
                                        else if(modelo=="7224"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarRendimientoDslam7224");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verTemperatura';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verTemperatura';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verTemperatura';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Temperatura',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTemperaturaDslamA2024");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTemperaturaDslamR1");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTemperaturaDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verLogs';
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verLogs';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verLogs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Logs',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarLogsDslam6524");
                                        }
                                        else if(modelo=="7224"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarLogsDslam7224");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarLogsDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verInterface'; 
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verInterface';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Interface',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarInterfaceDslam7224");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarInterfaceDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },//SCRIPTS CON VARIABLE
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verInterfacesPorPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verInterfacesPorPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verInterfacesPorPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver VCI y Velocidad',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionInterfaceDslamA2024");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionInterfaceDslamR1");
                                        }
                                        else if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionInterfaceDslam7224");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verNombrePuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Nombre Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarNombrePuertoDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verVelocidadReal';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verVelocidadReal';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verVelocidadReal';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Velocidad Real',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadRealDslamA2024");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadRealDslam6524");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadRealDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verSenalLejos';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Niveles de Señal Extremo Lejano',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarNivelesSenalExtremoLejanoDslamA2024");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verSenalCerca';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Niveles de Señal Extremo Cercano',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarNivelesSenalExtremoCercanoDslamA2024");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verConfiguracionBridge';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Configuracion Bridge',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionBridgeDslamA2024");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verCircuitoVirtual';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Circuito Virtual',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCircuitoVirtualDslamA2024");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verContadores';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Contadores',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarContadoresDslamA2024");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verRendimientoPuertoDiario';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verRendimientoPuertoDiario';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Desempeño Puerto - Diario',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoDiarioDslamA2024");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoDiarioDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verRendimientoPuertoIntervalo';
                                    }
                                    else if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verRendimientoPuertoIntervalo';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Desempeño Puerto - Intervalo',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoIntervaloDslamA2024");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoIntervaloDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Macs del Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsPuertoDslam7224");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsPuertoDslam6524");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsPuertosDslamR1");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacPuertoDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verVelocidadSeteada';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verVelocidadSeteada';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Velocidad Seteada',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadSeteadaDslam7224");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadSeteadaDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMonitorearPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMonitorearPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Monitorear Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitorearPuertoDslam7224");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitorearPuertoDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Parametros Linea',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarParametrosLineaDslam7224");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Codificacion Linea',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCodificacionLineaDslam7224");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Crc',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCrcDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Senal Ruido',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarSenalRuidoDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Atenuacion',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarAtenuacionDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Codificacion',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCodificacionDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Restriccion Ip',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarRestriccionIpDslam6524");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Monitorear Puerto Data I',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitoreoPuertoDataIDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Monitorear Puerto Data II',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitoreoPuertoDataIIDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Status Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarStatusPuertoDslamR1");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarEstadoPuertoDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Codificacion',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCodificacionDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-interfaceVirtual1';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Interface Virtual eth1',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarInterfaceVirtualEth1DslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-interfaceVirtual2';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Interface Virtual eth2',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarInterfaceVirtualEth2DslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Puertos Conectados',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarPuertosConectadosDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Procesamiento',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarProcesamientoDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Disco',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarDiscoDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Memoria',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarMemoriaDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacs';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Ver Tiempo Actividad',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptDslam(grid.getStore().getAt(rowIndex).data, "mostrarTiempoActividadDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Cambiar Codificacion',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "cambiarCodificacionPuertoDslam7224");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "cambiarCodificacionPuertoDslam6524");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "cambiarCodificacionPuertoDslamR1");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Mostrar Configuracion Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    if(modelo=="MEA1" || modelo=="MEA3"){
                                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionPuertoDslamMea");
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                }
                            }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Mostrar Errores Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    if(modelo=="MEA1" || modelo=="MEA3"){
                                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarErroresPuertoDslamMea");
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                }
                            }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Mostrar Interface Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    if(modelo=="MEA1" || modelo=="MEA3"){
                                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarInterfacePuertoDslamMea");
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                }
                            }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Mostrar Vci Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    if(modelo=="MEA1" || modelo=="MEA3"){
                                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVciPuertoDslamMea");
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                }
                            }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Mostrar Tiempo Actividad Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                    if(modelo=="MEA1" || modelo=="MEA3"){
                                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarTiempoActividadPuertoDslamMea");
                                    }
                                    else{
                                        alert("No existe Accion para este Dslam");
                                    }
                                }
                            }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMacPuerto';
                                    } 
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Limpiar Contadores',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "limpiarContadoresPuertoDslam7224");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "limpiarContadoresPuertoDslamR1");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "limpiarContadoresPuertoDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },{
                            getClass: function(v, meta, rec) {
                                if (rec.get('estado') != "Eliminado") {
                                    if(rec.get('modeloElemento')=="7224"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="6524"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                    if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                                        return 'button-grid-verMacPuerto';
                                    }
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                                
                            },
                            tooltip: 'Resetear Puerto',
                            handler: function(grid, rowIndex, colIndex) {
                                var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                                    if(grid.getStore().getAt(rowIndex).data.estado!="Eliminado"){
                                        if(modelo=="7224"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslam7224");
                                        }
                                        else if(modelo=="6524"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslam6524");
                                        }
                                        else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslamR1");
                                        }
                                        else if(modelo=="A2024" || modelo=="A2048"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslamA20");
                                        }
                                        else if(modelo=="MEA1" || modelo=="MEA3"){
                                            verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslamMea");
                                        }
                                        else{
                                            alert("No existe Accion para este Dslam");
                                        }
                                    }
                                }
                        },
                        //DEJAR SIN OPERATIVIDAD EL OLT
                        {
                            getClass: function (v, meta, rec) {
                                strBtnOperativo = 'button-grid-invisible';
                                if (rec.get('botonOperatividad') != 'Eliminado')
                                {
                                    var permiso = $("#ROLE_149-827");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) 
                                    {
                                        strBtnOperativo = 'button-grid-invisible';
                                    }
                                    else 
                                    {
                                        strBtnOperativo = 'button-grid-cancelarCliente';
                                    }
                                }

                                return strBtnOperativo;
                            },
                            tooltip: 'Dejar sin Operatividad',
                            handler: function (grid, rowIndex, colIndex) 
                            {
                                if (grid.getStore().getAt(rowIndex).data.botonOperatividad == "SI")
                                {
                                    Ext.Msg.confirm('Alerta', 'Se dejará al Dslam sin Operativdad. Desea continuar?', function(btn) {
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

function cargarModelos(idParam){
    storeModelos.proxy.extraParams = {idMarca: idParam, tipoElemento:'DSLAM', limite:100};
    storeModelos.load({params: {}});
}

function buscar(){
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.ipElemento = Ext.getCmp('txtIp').value;
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarca').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModelo').value;
    store.getProxy().extraParams.canton = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.jurisdiccion = Ext.getCmp('sltJurisdiccion').value;
    store.getProxy().extraParams.popElemento = Ext.getCmp('sltPop').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
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

function actualizarPerfiles(){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Actualizar los perfiles?', function(btn){
        if(btn=='ok'){
            Ext.get(grid.getId()).mask('Actualizando Perfiles...');
            Ext.Ajax.request({
                url: "actualizarPerfiles",
                method: 'post',
                timeout: 3000000,
                success: function(response){
                        if(response.responseText == "OK"){
                            
                            Ext.Msg.alert('Mensaje','Se Actualizaron los Perfiles!', function(btn){
                                if(btn=='ok'){
                                    Ext.get(grid.getId()).unmask();
                                    //store.load();
                                    //win.destroy();
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
            {opcion:'Activo', valor:'not connect'},
            {opcion:'Online'  , valor:'connected'},
            {opcion:'Dañado', valor:'err-disabled'},
            {opcion:'Inactivo'   , valor:'disabled'},
        ]
    });

    var comboInterfaces = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : getInterfacesElemento,
            extraParams: {idElemento: data.idElemento, tipo:"DSLAM"},
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
                {name:'estado', mapping:'estado'}
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
            width: 220,
            hidden: false,
            hideable: false
        }, {
            id: 'estado',
            header: 'Estado',
            dataIndex: 'estado',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                for (var i = 0;i< comboEstados.data.items.length;i++)
                {
                    if (comboEstados.data.items[i].data.valor == record.data.estado)
                    {
                        console.log(comboEstados.data.items[i].data.valor);
                        console.log(comboInterfaces.data.items[i].data.valor);
                        if(comboEstados.data.items[i].data.valor == "not connect"){
                            record.data.estado = "Activo";
                            break;
                        }
                        else if(comboEstados.data.items[i].data.valor == "connected"){
                            record.data.estado = "Online";
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
        }
        ],
        
    
        
        viewConfig:{
            stripeRows:true
        },

        

        width: 500,
        height: 350,
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
                    columns: 2
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
                        fieldLabel: 'Dslam',
                        displayField: '',
                        value: '',
                        readOnly: true,
                        width: '30%'
                                    
                    },//cierre hidden
                    
                    {
                        xtype: 'fieldset',
                        title: 'Puertos',
                        defaultType: 'textfield',
                        defaults: {
                            width: 590,
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
                        
                        if(true){
                            obtenerInterfaces();
                            var interfaces = Ext.getCmp('jsonInterfaces').getRawValue();
                            Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts de Comprobacion!');
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
                                    if(response.responseText == "OK"){
                                        Ext.Msg.alert('Mensaje','Se Actualizaron los puertos', function(btn){
                                            if(btn=='ok'){
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ',response.responseText + 
                                                                 '<br> No se pudo Actualizar los puertos del Elemento!' );
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
                            Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                    if(btn=='ok'){
                                    }
                            });
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
                title: 'Administracion de Puertos',
                modal: true,
                width: 600,
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

//ejecuta los scripts sin variable para los dslams
function verScriptDslam(data, action) {
    console.log(data.modeloElemento);
    Ext.get(grid.getId()).mask('Loading...');
    Ext.Ajax.request({
        url: action,
        method: 'post',
        waitMsg: 'Esperando Respuesta del Dslam',
        timeout: 400000,
        params: {
            modelo: data.modeloElemento,
            idElemento: data.idElemento
        },
        success: function(response) {

            var variable = response.responseText.split("&");
            var resp = variable[0];
            var script = variable[1];

            if (script == "NO EXISTE RELACION TAREA - ACCION") {
                Ext.Msg.alert('Error ', 'No Existe la Relacion Tarea - Accion');
                Ext.get(grid.getId()).unmask();
            }
            else {
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
                                            id: 'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 60,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly: true
                                        }]
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Configuracion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 550,
                                height: 300
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
                                            id: 'mensaje',
                                            name: 'mensaje',
                                            fieldLabel: 'Configuracion',
                                            value: ejecucion.mensaje,
                                            cols: 60,
                                            rows: 19,
                                            anchor: '100%',
                                            readOnly: true
                                        }]
                                }
                            ]
                        }],
                    buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function() {
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
                    items: [formPanel]
                }).show();

            }//cierre else

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}

//ejecuta los script con variable (puerto) para los dslams
function verScriptVariableDslam(data, action) {
    var comboInterfaces = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: getInterfacesElemento,
            extraParams: {idElemento: data.idElemento},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
            ]
    });

    var str = -1;

    str = action.search("cambiarCodificacionPuerto");

    if (str != -1) {
        Ext.define('codificacion', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'opcion', type: 'string'},
                {name: 'valor', type: 'string'}
            ]
        });

        var mod1 = 0;
        var mod2 = 0;
        var mod3 = 0;

        mod1 = action.search("6524");
        mod2 = action.search("7224");
        mod3 = action.search("R1");

        if (mod1 != -1) { //6524
            comboCodificacion = new Ext.data.Store({
                model: 'codificacion',
                data: [
                    {opcion: 'G.DMT ONLY MODE', valor: '0'},
                    {opcion: 'G.LITE ONLY MODE', valor: '2'},
                    {opcion: 'T1.413 ONLY MODE', valor: '1'},
                    {opcion: 'AUTO SENSING MODE', valor: '3'}
                ]
            });
        }
        else if (mod2 != -1) { //7224
            comboCodificacion = new Ext.data.Store({
                model: 'codificacion',
                data: [
                    {opcion: 'ADSL2'    , valor: 'adsl2'},
                    {opcion: 'ADSL2+'   , valor: 'adsl2+'},
                    {opcion: 'DMT'      , valor: 'dmt'},
                    {opcion: 'MULTIMODE', valor: 'multimode'}
                ]
            });
        }
        else if (mod3 != -1) { //R1
            comboCodificacion = new Ext.data.Store({
                model: 'codificacion',
                data: [
                    {opcion: 'ADSL.BIS'     , valor: 'adsl.bis'},
                    {opcion: 'ADSL.BIS.PLUS', valor: 'adsl.bis.plus'},
                    {opcion: 'G.DMT'        , valor: 'g.dmt'},
                    {opcion: 'AUTO'         , valor: 'auto'}
                ]
            });
        }

        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: [{
                    xtype: 'fieldset',
                    title: 'Ver Interface',
                    defaultType: 'textfield',
                    defaults: {
                        width: 650
                    },
                    items: [
                        {
                            xtype: 'container',
                            layout: {
                                type: 'hbox',
                                pack: 'left'
                            },
                            items: [{
                                    xtype: 'combo',
                                    id: 'comboInterfaces',
                                    name: 'comboInterfaces',
                                    store: comboInterfaces,
                                    fieldLabel: 'Interfaces',
                                    displayField: 'nombreInterfaceElemento',
                                    valueField: 'nombreInterfaceElemento',
                                    queryMode: 'local'
                                }, {
                                    xtype: 'combo',
                                    id: 'comboCodificacion',
                                    name: 'comboCodificacion',
                                    store: comboCodificacion,
                                    fieldLabel: 'Codificacion',
                                    displayField: 'opcion',
                                    valueField: 'valor',
                                    queryMode: 'local'
                                }]
                        }

                    ]
                }],
            buttons: [{
                    text: 'Ejecutar',
                    formBind: true,
                    handler: function() {
                        if (true) {
                            Ext.get(grid.getId()).mask('Loading...');
                            Ext.Ajax.request({
                                url: action,
                                method: 'post',
                                waitMsg: 'Esperando Respuesta del Dslam',
                                timeout: 400000,
                                params: {modelo: data.modeloElemento,
                                    idElemento: data.idElemento,
                                    interfaceElemento: Ext.getCmp('comboInterfaces').value,
                                    codificacion: Ext.getCmp('comboCodificacion').value
                                },
                                success: function(response) {

                                    var variable = response.responseText.split("&");
                                    var resp = variable[0];
                                    var script = variable[1];

                                    //alert(resp);

                                    if (script == "NO EXISTE RELACION TAREA - ACCION") {
                                        Ext.Msg.alert('Error ', 'No Existe la Relacion Tarea - Accion');
                                        Ext.get(grid.getId()).unmask();
                                    }
                                    else if (response.responseText.indexOf("El host no es alcanzable a nivel de red") != -1) {
                                        Ext.Msg.alert('Error ', 'No se puede Conectar al Dslam <br>Favor revisar con el Departamento Tecnico');
                                        Ext.get(grid.getId()).unmask();
                                    }
                                    else {
                                        var ejecucion = Ext.JSON.decode(resp);
                                        Ext.get(grid.getId()).unmask();

                                        var formPanel1 = Ext.create('Ext.form.Panel', {
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
                                                                    id: 'script',
                                                                    name: 'script',
                                                                    fieldLabel: 'Script',
                                                                    value: script,
                                                                    cols: 60,
                                                                    rows: 3,
                                                                    anchor: '100%',
                                                                    readOnly: true
                                                                }]
                                                        }
                                                    ]
                                                },
                                                {
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
                                                                    id: 'mensaje',
                                                                    name: 'mensaje',
                                                                    fieldLabel: 'Configuracion',
                                                                    value: ejecucion.mensaje,
                                                                    cols: 60,
                                                                    rows: 19,
                                                                    anchor: '100%',
                                                                    readOnly: true
                                                                }]
                                                        }
                                                    ]
                                                }],
                                            buttons: [{
                                                    text: 'Cerrar',
                                                    formBind: true,
                                                    handler: function() {
                                                        win.destroy();
                                                    }
                                                }]
                                        });

                                        var win = Ext.create('Ext.window.Window', {
                                            title: 'Ver Configuracion Interface',
                                            modal: true,
                                            width: 630,
                                            height: 550,
                                            closable: false,
                                            layout: 'fit',
                                            items: [formPanel1]
                                        }).show();
                                    }//cierre else
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                            win.destroy();
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
            title: 'Ver Configuracion Interface',
            modal: true,
            width: 550,
            closable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
    }
    else {
        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: [{
                    xtype: 'fieldset',
                    title: 'Ver Interface',
                    defaultType: 'textfield',
                    defaults: {
                        width: 650
                    },
                    items: [
                        {
                            xtype: 'container',
                            layout: {
                                type: 'hbox',
                                pack: 'left'
                            },
                            items: [{
                                    xtype: 'combo',
                                    id: 'comboInterfaces',
                                    name: 'comboInterfaces',
                                    store: comboInterfaces,
                                    fieldLabel: 'Interfaces',
                                    displayField: 'nombreInterfaceElemento',
                                    valueField: 'nombreInterfaceElemento',
                                    queryMode: 'local'
                                }]
                        }

                    ]
                }],
            buttons: [{
                    text: 'Ejecutar',
                    formBind: true,
                    handler: function() {
                        if (true) {
                            Ext.get(grid.getId()).mask('Loading...');
                            Ext.Ajax.request({
                                url: action,
                                method: 'post',
                                waitMsg: 'Esperando Respuesta del Dslam',
                                timeout: 400000,
                                params: {modelo: data.modeloElemento,
                                    idElemento: data.idElemento,
                                    interfaceElemento: Ext.getCmp('comboInterfaces').value,
                                    codificacion: ""
                                },
                                success: function(response) {

                                    var variable = response.responseText.split("&");
                                    var resp = variable[0];
                                    var script = variable[1];

                                    if (script == "NO EXISTE RELACION TAREA - ACCION") {
                                        Ext.Msg.alert('Error ', 'No Existe la Relacion Tarea - Accion');
                                        Ext.get(grid.getId()).unmask();
                                    }
                                    else if (response.responseText.indexOf("El host no es alcanzable a nivel de red") != -1) {
                                        Ext.Msg.alert('Error ', 'No se puede Conectar al Dslam <br>Favor revisar con el Departamento Tecnico');
                                        Ext.get(grid.getId()).unmask();
                                    }
                                    else {
                                        var ejecucion = Ext.JSON.decode(resp);
                                        Ext.get(grid.getId()).unmask();

                                        var formPanel1 = Ext.create('Ext.form.Panel', {
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
                                                                    id: 'script',
                                                                    name: 'script',
                                                                    fieldLabel: 'Script',
                                                                    value: script,
                                                                    cols: 60,
                                                                    rows: 3,
                                                                    anchor: '100%',
                                                                    readOnly: true
                                                                }]
                                                        }
                                                    ]
                                                },
                                                {
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
                                                                    id: 'mensaje',
                                                                    name: 'mensaje',
                                                                    fieldLabel: 'Configuracion',
                                                                    value: ejecucion.mensaje,
                                                                    cols: 60,
                                                                    rows: 19,
                                                                    anchor: '100%',
                                                                    readOnly: true
                                                                }]
                                                        }
                                                    ]
                                                }],
                                            buttons: [{
                                                    text: 'Cerrar',
                                                    formBind: true,
                                                    handler: function() {
                                                        win.destroy();
                                                    }
                                                }]
                                        });

                                        var win = Ext.create('Ext.window.Window', {
                                            title: 'Ver Configuracion Interface',
                                            modal: true,
                                            width: 630,
                                            height: 550,
                                            closable: false,
                                            layout: 'fit',
                                            items: [formPanel1]
                                        }).show();
                                    }//cierre else
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                            win.destroy();
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
            title: 'Ver Configuracion Interface',
            modal: true,
            width: 350,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
    }




}
