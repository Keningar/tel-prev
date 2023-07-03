    Ext.require([
        '*',
        'Ext.tip.QuickTipManager',
            'Ext.window.MessageBox'
    ]);

    var itemsPerPage = 10;
    var store='';
    var motivo_id='';
    var relacion_sistema_id='';
    var tipo_solicitud_id='';
    var area_id='';
    var login_id='';
    var tipo_asignacion='';
    var pto_sucursal='';
    var idClienteSucursalSesion;

    Ext.onReady(function(){

        Ext.form.VTypes["valorVtypeVal"] =/(^\d{1,4}\.\d{1,2}$)|(^\d{1,4}$)/;		
        Ext.form.VTypes["valorVtype"]=function(v){
            return Ext.form.VTypes["valorVtypeVal"].test(v);
        }
        Ext.form.VTypes["valorVtypeText"]="Puede ingresar hasta 4 enteros y al menos 1 decimal o puede ingresar hasta 4 enteros sin decimales";
        Ext.form.VTypes["valorVtypeMask"]=/[\d\.]/;

        Ext.form.VTypes["porcentajeVtypeVal"] =/(^\d{1,3}\.\d{1,2}$)|(^\d{1,3}$)/;		
        Ext.form.VTypes["porcentajeVtype"]=function(v){
            return Ext.form.VTypes["porcentajeVtypeVal"].test(v);
        }
        Ext.form.VTypes["porcentajeVtypeText"]="Puede ingresar hasta 3 enteros y al menos 1 decimal o puede ingresar hasta 3 enteros sin decimales";
        Ext.form.VTypes["porcentajeVtypeMask"]=/[\d\.]/;

        /**
         * Documentación para funcion 'solicitarDescuento'.
         * Metodo que valida los valores para solicitud de desceunto
         *
         * @author amontero@telconet.ec
         * @version 1.0 08-07-2015
         * 
         * Se agrega combo tipo de descuento para la generacion de la
         * solicitud de descuento.
         * 
         * @author rcoello@telconet.ec
         * @version 1.1 04-05-2017
         *
         * @author hlozano@telconet.ec
         * @version 1.2 29-05-2018 Se validó que se escoja una opción de descuento 
         * obligatoriamente, al momento de realizar la solicitud de descuento.
         * 
         * Se valida que el campo Observación no este vacío al momento de crear la solicitud.
         * @author Douglas Natha <dnatha@telconet.ec>
         * @version 1.3 27-11-2019
         * @since 1.2 
         */   
        function solicitarDescuento()
        {
            var param                       = '';
            var banderaValorMayor           = false;
            var strDescuentoSeleccionado    = '';
            if((sm.getSelection().length > 0)&&((Ext.getCmp('radio_porcentaje').checked)||(Ext.getCmp('radio_precio').checked)))
            {   
                if( (Ext.getCmp('observacion').getValue()).length > 0 )
                {
                    strDescuentoSeleccionado = Ext.getCmp('cboTipoDescuento').getValue();
                    var estado               = 0;
                    for(var i=0 ;  i < sm.getSelection().length ; ++i)
                    {
                        if ((parseFloat(Ext.getCmp('valorPrecio').getValue()) > parseFloat(sm.getSelection()[i].data.precioVenta))
                        || (parseFloat(Ext.getCmp('valorPorcentaje').getValue()>100)))
                        {
                            banderaValorMayor=true;
                        }                        
                        param = param + sm.getSelection()[i].data.idServicio;
                        if(i < (sm.getSelection().length -1))
                        {                    
                            param = param + '|';
                        }
                    } 

                    if(strDescuentoSeleccionado)
                    {
                        if(motivo_id)
                        {
                            if (Ext.getCmp('radio_porcentaje').checked)
                            {  
                                if((Ext.getCmp('valorPorcentaje').getValue())
                                    &&(Ext.getCmp('valorPorcentaje').isValid())
                                    &&(Ext.getCmp('valorPorcentaje').getValue()<=100))
                                {    
                                    ejecutaEnvioSolicitud(param);
                                }    
                                else
                                {    
                                    Ext.Msg.alert('Alerta ',
                                    'Por favor ingresar valor de Porcentaje o verifique que este ingresado correctamente' 
                                    + ' y que el porcentaje sea menor o igual a 100.');
                                }    
                            }
                            else if(Ext.getCmp('radio_precio').checked)
                            {
                                if((Ext.getCmp('valorPrecio').getValue())&&(Ext.getCmp('valorPrecio').isValid()))
                                { 
                                    if(banderaValorMayor==true)
                                    {
                                        Ext.Msg.alert('Alerta ','El valor de descuento es mayor al valor del servicio');
                                    }
                                    else
                                    {    
                                        ejecutaEnvioSolicitud(param);
                                    }
                                }    
                                else
                                {     
                                    Ext.Msg.alert('Alerta ','Por favor ingresar el Valor o verifique que este ingresado correctamente');
                                }    
                            }
                        }
                        else
                        {
                            alert('Seleccione el Motivo de la solicitud');
                        }
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta ','Seleccione tipo descuento, obligatorio para generar la solicitud');
                    }
                }
                else
                {
                    alert('El campo Observaci\u00F3n no puede quedar vacío');
                }       
            }
            else
            {
              alert('Seleccione por lo menos un registro de la lista y Escoja una opci\u00F3n de descuento: Valor o Porcentaje');
            }
        }            

        /**
        * Documentación para funcion 'ejecutaEnvioSolicitud'.
        * Metodo que envia a generar la solicitud  de descuento
        * 
        * Se agrega nuevo parametro 'tipoDescuento' a la peticion ajax
        * 
        * @author rcoello@telconet.ec
        * @version 1.1 05-05-2017
        *
        */   
        function ejecutaEnvioSolicitud(param){
                    $('#mensaje_validaciones').addClass('campo-oculto').html('');    
                    var tipoValor       ='';
                    var valor           =''; 
                    var strTipoDescuento='';
                    
                   if(Ext.getCmp('valorPrecio').getValue()){
                       tipoValor    = 'valor';
                       valor        = Ext.getCmp('valorPrecio').getValue();
                       valor        = parseFloat(valor).toFixed(2);
                   }else{
                       if (Ext.getCmp('valorPorcentaje').getValue()){
                           tipoValor    = 'porcentaje';
                           valor        = Ext.getCmp('valorPorcentaje').getValue();
                           valor        = parseFloat(valor).toFixed(2);
                       }
                   }
                   
                    strTipoDescuento       = Ext.getCmp('cboTipoDescuento').getValue();

                    Ext.Msg.confirm('Alerta','Se solicitara descuento para los registros seleccionados. Desea continuar?', function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                url:    url_solicitar_descuento_ajax,
                                method: 'post',
                                params: { param : param, motivoId:motivo_id, rs: relacion_sistema_id, ts:tipo_solicitud_id, tValor:tipoValor, 
                                          v:valor, obs:TFObservacion.getValue(),tipoDescuento: strTipoDescuento},
                                success: function(response){                                  
                                    var objData = Ext.JSON.decode(response.responseText);
                                    var strStatus  = objData.strStatus;
                                    var strMensaje = objData.strMensaje;
                                    if (strStatus == 'OK')
                                    {
                                        Ext.Msg.alert('Ok', 'Transacción exitosa. '+ strMensaje );                                        
                                    }
                                    else
                                    {                                       
                                        $('#mensaje_validaciones').removeClass('campo-oculto').html(strMensaje);
                                    }                                      
                                    store.load();
                                    if(strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN')
                                    {
                                        LimpiarDescuento();
                                    }
                                    Ext.getCmp('idMotivo').setValue('');
                                    motivo_store.removeAll();
                                    motivo_store.load();
                                    TFObservacion.setValue('');
                                },
                                failure: function(response)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + response.responseText);
                                }
                            });
                        }
                    });                

        }

        
        function LimpiarDescuento()
        {

            Ext.getCmp('radio_precio').enable();
            Ext.getCmp('radio_porcentaje').enable();
            Ext.getCmp('radio_precio').setValue(false);
            Ext.getCmp('radio_porcentaje').setValue(false);
            TFPorcentaje.setValue('');
            TFPrecio.setValue('');
            Ext.getCmp('valorPorcentaje').enable();
            Ext.getCmp('valorPrecio').enable();
            TFPorcentaje.setVisible(false);
            TFPrecio.setVisible(false);
            TipoValor.setVisible(false);            
        }
        
        //Creamos Store para Tipo de Descuento
        Ext.define('modelTipoDescuento', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'strDisplayTipoDescuento', type: 'string'},
                {name: 'strValueTipoDescuento',   type: 'string'},
                {name: 'strValueSelected',        type: 'string'}
            ]
        });

        storeTipoDescuento = Ext.create('Ext.data.Store', {
            id: 'idStoreTipoDescuento',
            autoLoad: true,
            model: "modelTipoDescuento",
            proxy: {
                type: 'ajax',
                url: urlGetTipoDescuento,
                reader: {
                    type: 'json',
                    root: 'arrayTipoDescuento'
                },
                extraParams:
                       {
                           strTipoSolicitud: 'SOL_DCTO'
                       }
            },
            listeners: {

                load: function(storeCbxTipoDescuento, records, success) {

                    if(store.getCount() == 0)
                    {
                        Ext.Msg.alert('Alerta ', 'No hay descuento relacionado a la solicitud.');
                    }
                    else
                    {    
                        for (var i = 0; i < storeCbxTipoDescuento.data.items.length; i++)
                        {
                            if (!Ext.isEmpty(storeCbxTipoDescuento.data.items[i].data.strValueSelected))
                            {
                                Ext.getCmp('cboTipoDescuento').setValue( storeCbxTipoDescuento.data.items[i].data.strValueSelected );
                                break;
                            }
                        }
                    }
                }
            }
        });
        
        cboTipoDescuento = new Ext.form.ComboBox({
            id: 'cboTipoDescuento',
            name: 'cboTipoDescuento',
            xtype: 'combobox',
            editable: false,
            queryMode: 'local',
            store: storeTipoDescuento,
            labelAlign: 'left',
            valueField: 'strValueTipoDescuento',
            displayField: 'strDisplayTipoDescuento',
            fieldLabel: 'Tipo Descuento',
            width: 250,
            height: 30
        });    
        
        //CREAMOS DATA STORE PARA EMPLEADOS
        Ext.define('modelMotivo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idMotivo', type: 'string'},
                {name: 'descripcion',  type: 'string'},
                {name: 'idRelacionSistema',  type: 'string'},
                {name: 'idTipoSolicitud',  type: 'string'}                    
            ]
        });			
        
        var motivo_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelMotivo",
        proxy: {
            type: 'ajax',
            url : url_lista_motivos,
            reader: {
                type: 'json',
                root: 'motivos'
                    }
                }
        });	
        
        var motivo_cmb = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: motivo_store,
            labelAlign : 'left',
            id:'idMotivo',
            name: 'idMotivo',
            valueField:'idMotivo',
            displayField:'descripcion',
            fieldLabel: 'Motivo',
            labelAlign:'right',
            width: 325,
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: true,	

            listeners: {
                        select:
                        function(e) {       
                            motivo_id           = Ext.getCmp('idMotivo').getValue();
                            relacion_sistema_id = e.displayTplData[0].idRelacionSistema;
                            tipo_solicitud_id   = e.displayTplData[0].idTipoSolicitud;
                            $('#mensaje_validaciones').addClass('campo-oculto').html('');    
                            if(strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN')
                            {                               
                                sm.deselectAll();                               
                                LimpiarDescuento();
                                
                                motivo_desc = Ext.getCmp('idMotivo').getRawValue();                           
                                var strflujoMotivoAdultoMayor = flujoMotivoAdultoMayor(motivo_desc);
                                
                                if(motivo_desc == strMotivoDescDiscapacidad)
                                {
                                    TipoValor.setVisible(true);
                                    Ext.getCmp('radio_porcentaje').enable();
                                    Ext.getCmp('radio_porcentaje').setValue(true);
                                    Ext.getCmp('radio_precio').disable();
                                    TFPorcentaje.setVisible(true);
                                    TFPorcentaje.setValue(strPorcentajeDiscapacidad);  
                                    Ext.getCmp('valorPorcentaje').disable();
                                }  
                                else if (strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' 
                                           || strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_RESOLUCION_072021') 
                                {                                                                      
                                    LimpiarDescuento();
                                    mostrarModalActFeNacimiento(url_actFechaNacimiento);
                                    
                                }
                                else
                                {
                                    LimpiarDescuento();
                                    TipoValor.setVisible(true);                                 
                                }
                            }
                        },
                        click: {
                            element: 'el',
                            fn: function(){ 
                                motivo_id           = '';
                                relacion_sistema_id = '';
                                tipo_solicitud_id   = '';
                                Ext.getCmp('idMotivo').setValue('');                                                              
                                motivo_store.removeAll();
                                motivo_store.load();                                
                                $('#mensaje_validaciones').addClass('campo-oculto').html('');    
                                if(strPrefijoEmpresa == 'MD'|| strPrefijoEmpresa == 'EN')
                                {
                                    LimpiarDescuento();
                                }
                            }
                        }			
            }
            });

            TFPrecio = new Ext.form.TextField({
                    id: 'valorPrecio',
                    name: 'valorPrecio',
                    labelAlign:'right',
                    fieldLabel: 'Valor',
                    hidden:true,
                    xtype: 'textfield',
                    width: '170px',
                    vtype: 'valorVtype'
            });

            TFPorcentaje = new Ext.form.TextField({
                    id: 'valorPorcentaje',
                    name: 'valorPorcentaje',
                    labelAlign:'right',
                    fieldLabel: 'Porcentaje',
                    hidden:true,
                    xtype: 'textfield',
                    width: '170px',
                    vtype: 'porcentajeVtype'
            });


            TFObservacion = new Ext.form.field.TextArea({
                    xtype     : 'textareafield',
                    id        : 'observacion',
                    name      : 'observacion',
                    fieldLabel: 'Observacion',
                    cols     : 80,
                    rows     : 2,
                    maxLength: 200
                }); 

            TipoValor = new Ext.form.RadioGroup(
                {
                    xtype      : 'fieldcontainer',
                    defaultType: 'radiofield',
                     width: '170px',
                    defaults: {
                        flex: 1
                    },
                    layout: 'hbox',
                    items: [
                        {
                            boxLabel  : 'Porcentaje',
                            name      : 'tipoDescuento',
                            inputValue: 'v',
                            id        : 'radio_porcentaje',
                            listeners:{                    
                            change:
                            function(radio1, newValue, oldValue, eOpts) {
                                if (radio1.checked){
                                    TFPorcentaje.setVisible(true);
                                }
                                else
                                {
                                    TFPorcentaje.setVisible(false);
                                    TFPorcentaje.setValue('');
                                }
                            }
                        }
                        }, {
                            boxLabel  : 'Valor',
                            name      : 'tipoDescuento',
                            inputValue: 'p',
                            id        : 'radio_precio',
                            listeners:{                    
                            change:
                            function(radio2, newValue, oldValue, eOpts) {
                                if (radio2.checked){
                                    TFPrecio.setVisible(true);
                                }
                                else
                                {
                                    TFPrecio.setVisible(false);
                                    TFPrecio.setValue('');
                                }
                            }
                        }
                        }
                    ]
                }        
            );

                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'idServicio', type: 'int'},
                            {name:'tipo', type: 'string'},
                            {name:'idPunto', type: 'string'},
                            {name:'descripcionPunto', type: 'string'},
                            {name:'idProducto', type: 'string'},
                            {name:'descripcionProducto', type: 'string'},
                            {name:'cantidad', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'precioVenta', type: 'string'},                            
                            {name:'estado', type: 'string'},
                            {name:'yaFueSolicitada', type: 'string'},
                            {name:'strNombreProducto', type: 'string'},
                            {name:'strAplicaDesc', type: 'string'},
                            {name:'strAplicaDescDiscapacidadAdultoMayor', type: 'string'}                            
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_grid,
                        reader: {
                            type: 'json',
                            root: 'servicios',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombre:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store,id_punto_cliente){
                                store.getProxy().extraParams.idPuntoCliente= id_punto_cliente;
                        }
                    }
                });

                store.load({params: {start: 0, limit: 10}});    

                if(strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN')
                {
                    TipoValor.setVisible(false);
                }
                
                 var sm = new Ext.selection.CheckboxModel({
                    listeners:{
                       select: function( selectionModel, record, index, eOpts ){                              
                           motivo_desc = Ext.getCmp('idMotivo').getRawValue();                                                    
                           var strflujoMotivoAdultoMayor = '';
                           
                           if((strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN') && !motivo_id)
                           {
                               sm.deselect(index);
                               Ext.Msg.alert('Alerta','Debe seleccionar el motivo de la Solicitud');      
                           }
                           if(record.data.yaFueSolicitada == 'S'){
                                sm.deselect(index);
                                Ext.Msg.alert('Alerta','Ya fue solicitado descuento para el servicio: '+record.data.descripcionProducto);
                            }
                            
                            if(record.data.strAplicaDesc === 'NO'){
                                sm.deselect(index);
                                Ext.Msg.alert('Alerta','No aplica descuento para el servicio: '+record.data.descripcionProducto);
                            }
                            
                            if(strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN' )
                            {
                               strflujoMotivoAdultoMayor = flujoMotivoAdultoMayor(motivo_desc);  
                            }
                            
                            if((strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN') && record.data.yaFueSolicitada == 'N'
                                && (motivo_desc == strMotivoDescDiscapacidad || 
                                    (strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' 
                                      || strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
                                   ))
                            {
                                 if (record.data.strAplicaDescDiscapacidadAdultoMayor === 'NO')
                                 {
                                     //Servicio No Aplica para Descuento
                                     sm.deselect(index);
                                     Ext.Msg.alert('Alerta','No aplica descuento "'+motivo_desc+'" para el servicio: '
                                         +record.data.strNombreProducto);
                                     if (strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' 
                                          || strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
                                     {
                                         LimpiarDescuento();
                                     }                                   
                                 }
                                 else
                                 {   // Servicio Aplica para descuento
                                     if (strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' 
                                            || strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_RESOLUCION_072021') 
                                     {
                                         // Obtengo el valor de descuento para motivo de tercera edad                                                                                 
                                         Ext.MessageBox.wait('Calculando Descuento "'+motivo_desc+'"...');
                                         Ext.Ajax.request({
                                             url:    url_calculaDescAdultoMayor,
                                             method: 'post',
                                             params: { intIdServicio : record.data.idServicio, strFlujoAdultoMayor : strflujoMotivoAdultoMayor },
                                             success: function(response){
                                             
                                                 Ext.MessageBox.hide();
                                                 var objData           = Ext.JSON.decode(response.responseText);
                                                 var strStatus         = objData.strStatus;
                                                 var strMensaje        = objData.strMensaje;
                                                 var fltValorDescuentoAdultoMayor = objData.fltValorDescuentoAdultoMayor;
                                                 var strTipoCategoriaPlan         = objData.strTipoCategoriaPlan; 
                                                 var strParamCategPlanBasico      = objData.strParamCategPlanBasico;
                                                if (strStatus != 'OK')
                                                {
                                                    Ext.Msg.alert('Error ','' + strMensaje + '"'  + motivo_desc + '" para el servicio: '
                                                                   +record.data.strNombreProducto);
                                                }
                                                else
                                                {
                                                    if(strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_ADULTO_MAYOR' ) 
                                                    { 
                                                        LimpiarDescuento();
                                                        TipoValor.setVisible(true);
                                                        Ext.getCmp('radio_precio').enable();
                                                        Ext.getCmp('radio_precio').setValue(true);
                                                        Ext.getCmp('radio_porcentaje').disable();
                                                        TFPrecio.setVisible(true);
                                                        TFPrecio.setValue(fltValorDescuentoAdultoMayor);                                                    
                                                        Ext.getCmp('valorPrecio').disable();
                                                    }
                                                    else if(strflujoMotivoAdultoMayor === 'PROCESO_3ERA_EDAD_RESOLUCION_072021')
                                                    {
                                                        detValoresDescAdultoMayor(strTipoCategoriaPlan,fltValorDescuentoAdultoMayor,strParamCategPlanBasico);
                                                    }    
                                                    
                                                }
                                             },
                                             failure: function(response)
                                             {
                                                 Ext.Msg.alert('Error ','Error: ' + response.responseText);
                                             }
                                         });                                                                
                                         //Fin valor descuento Beneficio 3era Edad / Adulto Mayor      
                                     }
                                 }
                            }
                       } 
                    }
                });

                var opcionesPanel = Ext.create('Ext.panel.Panel', {
                    bodyPadding: 0,
                    border:true,
                    buttonAlign: 'center',
                    bodyStyle: {
                                background: '#fff'
                    },                     
                    defaults: {
                        bodyStyle: 'padding:10px'
                    },
                    width: 1000,
                    title: 'Opciones',
                    items: [
                           {
                            xtype: 'toolbar',
                            dock: 'top',
                            align: '->',
                            items: [
                                    cboTipoDescuento
                                    ]
                           },{
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        TipoValor,
                                        TFPrecio,
                                        TFPorcentaje,                                        
                                        { xtype: 'tbfill' },                                       
                                        motivo_cmb,                                                                                
                                        {
                                        iconCls: 'icon_solicitud',
                                        text: 'solicitar',
                                        disabled: false,
                                        itemId: 'delete',
                                        scope: this,
                                        handler: function(){ solicitarDescuento();}
                                        }
                                        ,{
                                        iconCls: 'fa fa-edit',
                                        text: 'Act. Fecha Nacimiento',
                                        disabled: false,
                                        itemId: 'actualizarFecha',
                                        scope: this,
                                        handler: function(){ mostrarModalActFeNacimiento(url_actFechaNacimiento);}
                                        }
                                    ]
                              }
                    ],
                    renderTo: 'filtro_servicios'
                });
                var observacionPanel = Ext.create('Ext.panel.Panel', {
                    bodyPadding: 7,
                    border:true,
                    bodyStyle: {
                                background: '#fff'
                    },                     
                    defaults: {
                        bodyStyle: 'padding:10px'
                    },
                    width: 1000,
                    title: '',
                    items: [
                      TFObservacion
                    ],	
                    renderTo: 'panel_observacion'
                });


                var listView = Ext.create('Ext.grid.Panel', {
                    width:1000,
                    height:350,
                    collapsible:false,
                    title: 'Servicios del punto cliente',
                    selModel: sm,                    
                    renderTo: Ext.get('lista_servicios'),
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando servicios {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            }
                    },
                    viewConfig: {
                          getRowClass: function(record, index) {
                              var c = record.get('yaFueSolicitada');
                              if (c == 'S') {
                                  return 'grisTextGrid';
                              } else{
                                  return 'blackTextGrid';
                              }
                          },
                          emptyText: 'No hay datos para mostrar'
                    } ,
                    columns: [new Ext.grid.RowNumberer(),  
                    {
                        text: 'Producto/Plan',
                        width: 150,
                        dataIndex: 'strNombreProducto'
                    },{
                        text: 'Descripcion',
                        width: 150,
                        dataIndex: 'descripcionProducto'
                    },{
                        text: 'Cantidad',
                        width: 115,
                        dataIndex: 'cantidad'
                    },{
                        text: 'Precio Venta',
                        dataIndex: 'precioVenta',
                        align: 'right',
                        width: 135			
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 60			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 40
                    }

                    ]
                });

        function Buscar(){
            if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
            {
                if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
                {
                   Ext.Msg.show({
                   title:'Error en Busqueda',
                   msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                   buttons: Ext.Msg.OK,
                   animEl: 'elId',
                   icon: Ext.MessageBox.ERROR
                    });		 

                }
                else
                {
                    store.load({params: {start: 0, limit: 10}});
                }
            }
            else
            {

                   Ext.Msg.show({
                   title:'Error en Busqueda',
                   msg: 'Por Favor Ingrese criterios de fecha.',
                   buttons: Ext.Msg.OK,
                   animEl: 'elId',
                   icon: Ext.MessageBox.ERROR
                    });
            }	
        }

        function Limpiar(){

            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idestado').setValue('');
            Ext.getCmp('nombre').setValue('');
        }
        
        function mostrarModalActFeNacimiento(url_accion)
        {
            $.ajax({
                url: url_accion,
                type: 'get',
                dataType: "html",
            success: function (response) {
                $('#modalActFechaNacimiento .modal-body').html(response);
                $('#modalActFechaNacimiento').modal({show: true});
            },
            error: function () {
                $('#modalActFechaNacimiento .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
                $('#modalActFechaNacimiento').modal('show');
            }
            });
        }
        
        function flujoMotivoAdultoMayor(strMotivo) {
             
            var strFlujoMotivo = "";
            
            $.ajax({
                url: urlFlujoMotivoAdultoMayor,
                method: 'get',
                async: false,
                data: {'strNombreMotivo':strMotivo},
                success: function (data) {
                    if (data.boolFlujoAdultoMayor == true)
                    {
                        strFlujoMotivo = data.strFlujoAdultoMayor;
                    }
                    else
                    {
                        strFlujoMotivo= "";
                    }
                },
                error: function () {
                    alert("Ocurrió un error al obtener el flujo adulto mayor en el parámetro.");
                }
            });

            return strFlujoMotivo;
        }
        
        function detValoresDescAdultoMayor(strTipoCategoriaPlan, fltValorDescuentoAdultoMayor, strParamCategPlanBasico){

            if(strTipoCategoriaPlan == strParamCategPlanBasico)
            {
                LimpiarDescuento();
                TipoValor.setVisible(true);
                Ext.getCmp('radio_porcentaje').enable();
                Ext.getCmp('radio_porcentaje').setValue(true);
                Ext.getCmp('radio_precio').disable();
                TFPorcentaje.setVisible(true);
                TFPorcentaje.setValue(fltValorDescuentoAdultoMayor);                                                    
                Ext.getCmp('valorPorcentaje').disable();
            }
            else
            {
                LimpiarDescuento();
                TipoValor.setVisible(true);
                Ext.getCmp('radio_precio').enable();
                Ext.getCmp('radio_precio').setValue(true);
                Ext.getCmp('radio_porcentaje').disable();
                TFPrecio.setVisible(true);
                TFPrecio.setValue(fltValorDescuentoAdultoMayor);                                                    
                Ext.getCmp('valorPrecio').disable();
            }
        }
        
});
