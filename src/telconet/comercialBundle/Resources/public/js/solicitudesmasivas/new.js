/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * New Solicitudes Masivas
 */

// Clase que tiene las funciones y objetos Generales
var entidadSolicitudMasiva          = new SolicitudMasiva();
var arrayParametrosCreacion         = new Array();
var arrayParametrosCreacionXDetalle = new Array();
var strRutaArchivo                  = '';

var connConsultarMora = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function (con, opt) {						
                Ext.MessageBox.show({
                   msg: 'Consultando Mora del cliente',
                   progressText: 'Saving...',
                   width:300,
                   wait:true,
                   waitConfig: {interval:200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function (con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function (con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});


function getFacturasAbiertasCliente()
{    
    connConsultarMora.request({
        url: urlGetMoraCliente,
        method: 'post',
        params: 
            { 
                cliente : ''
            },
        success: function(response){			
            var text = Ext.decode(response.responseText);
            
            if(text.strClienteMora == "S")
            {
                Ext.Msg.alert('Alerta', 'Cliente tiene facturas pendientes');
                cboProductos.setDisabled(true);
                cboUltimaMilla.setDisabled(true);
            }                        
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });  

} 


Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

Ext.onReady(function() {
    
    //Array con los Servicios Seleccionados por puntos para crear las solicitudes Detalle
    var arrayPuntosServiciosSeleccionados = new Array();
    
    //Crea Campos de fechas para el panel de busqueda objFilterPanel
    DTFechaPlanificada = new Ext.form.DateField({
        id: 'fechaPlanificada',
        name: 'fechaPlanificada',
        fieldLabel: 'Fecha Planificada',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 250,
        editable: false
    });
    
    //Crea un campo para el panel de busqueda objFilterPanel
    cboHora = Ext.create('Ext.form.field.Time', {
        id: 'cboHora',
        name: 'cboHora',
        fieldLabel: 'Hora',
        labelAlign: 'left',
        increment: 30,
        width: 250,
        minValue: '00:00',
        maxValue: '23:30'
    });
        
    
    //Creamos Store para Tipo de Solicitudes
    Ext.define('modelTipoSolicitud', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdTipoSolicitud', type: 'int'},
            {name: 'strNombreTipoSolicitud', type: 'string'}
        ]
    });
        
     storeTipoSolicitud = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelTipoSolicitud",
        proxy: {
            type: 'ajax',
            url: urlGetTipoSolicitud,
            reader: {
                type: 'json',
                root: 'jsonTipoSolicitud'
            }
        }
    });
    
    cboTipoSolicitud = new Ext.form.ComboBox({
        id: 'cboTipoSolicitud',
        name: 'cboTipoSolicitud',
        xtype: 'combobox',
        editable: false,
        store: storeTipoSolicitud,
        labelAlign: 'left',
        valueField: 'intIdTipoSolicitud',
        displayField: 'strNombreTipoSolicitud',
        fieldLabel: 'Tipo Solicitud',
        width: 320,
        height: 30,
        listeners:
        {
            select: function()
            {                
                var strTipoSolicitud = cboTipoSolicitud.getValue();
                limpiarTodo();
                cboTipoSolicitud.setValue(strTipoSolicitud);
                
                if(cboTipoSolicitud.getRawValue().search("DEMOS") !== -1)
                {                                        
                    getFacturasAbiertasCliente();

                    cboEsFacturable.show();
                    rdEstadosProd.hide();
                }                
                else if(cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") !== -1)
                {
                    cboProductos.setDisabled(false);
                    cboUltimaMilla.setDisabled(false);
                    cboEsFacturable.hide();
                    rdEstadosProd.show();
                    chkBoxObsoleto.enable();
                    txtDuracionDemo.hide();
                    txtDuracionDemo.setValue(null);
                }
                else 
                {
                    cboProductos.setDisabled(false);
                    cboUltimaMilla.setDisabled(false);                    
                    cboEsFacturable.hide();
                    rdEstadosProd.show();
                    chkBoxActivo.setValue(true);
                    chkBoxObsoleto.setValue(false);
                    chkBoxObsoleto.disable();
                    txtDuracionDemo.hide();
                    txtDuracionDemo.setValue(null);
                }
                            
                var box = entidadSolicitudMasiva.wait('Por favor espere mientras se cargan los producto...', 'Productos');
                var strEsConcentrador = (cboTipoSolicitud.getRawValue().search("CANCELACION") == -1) ? 'NO' : '';
                storeProductos.proxy.extraParams = { strIdCompuesto:'S', strTipoSolicitud: strTipoSolicitud, strEsConcentrador: strEsConcentrador };
                storeProductos.load(function(records, operation, success) {
                    box.hide();
                });

                activacionCamposSecundario();
            } 
        }
    });    
    
    
    storeEsFacturable = Ext.create('Ext.data.Store', {
        autoLoad: true,
        fields: ['intIdEsFacturable', 'strNombreEsFacturable'],
        data : [
            {"intIdEsFacturable":"SI", "strNombreEsFacturable":"SI"},
            {"intIdEsFacturable":"NO", "strNombreEsFacturable":"NO"}
        ]
    });   
    
    
    cboEsFacturable = new Ext.form.ComboBox({
        id: 'cboEsFacturable',
        name: 'cboEsFacturable',
        xtype: 'combobox',
        editable: false,
        store: storeEsFacturable,
        value: "SI",
        hidden:"true",
        labelAlign: 'left',
        valueField: 'intIdEsFacturable',
        displayField: 'strNombreEsFacturable',
        fieldLabel: 'Facturable', 
        width: 320,
        height: 30,
        listeners: 
        {
            select: function(combo)
            {
                if(cboProductos.getValue() != null)
                {
                    if(combo.getValue() == "NO")
                    {
                        txtPrecio.hide();
                        txtPrecio.setValue(null);
                    }
                    else
                    {
                        txtPrecio.show();
                    }
                }
            }
        }
    });   
    
    
    //Creamos Store para Estados de Solicitudes Masivas
    Ext.define('modelProductos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strIdProducto', type: 'string'},
            {name: 'strNombreProducto', type: 'string'},
            {name: 'strEsEnlace', type: 'string'},
            {name: 'strSoporteMasivo', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strNombreTecnico', type: 'string'},
            {name: 'intPrecio', type: 'float'}
        ]
    });
        
    storeProductos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelProductos",
        proxy: {
            timeout: 90000,
            type: 'ajax',
            url: urlGetProductos,
            reader: {
                type: 'json',
                root: 'jsonProductos'
            },
            extraParams: {
                strIdCompuesto: 'S', 
                strTipoSolicitud: cboTipoSolicitud.getValue(),
                strEsConcentrador: ((cboTipoSolicitud.getRawValue().search("CANCELACION") == -1) ? 'NO' : '')
            }
        }
    });
    
    cboProductos = new Ext.form.ComboBox({
        id: 'cboProductos',
        name: 'cboProductos',
        xtype: 'combobox',
        queryMode: 'local',
        store: storeProductos,
        labelAlign: 'left',
        valueField: 'strIdProducto',
        displayField: 'strNombreProducto',
        fieldLabel: 'Productos',
        style: 'font-weight:bold;',
        width: 320,
        height: 30,
        listeners:
        {
            select: function()
            {               
                activacionCamposSecundarioProducto();
            }
        }
    });
    
    //Creamos Store para Ultima Milla
    Ext.define('modelUltimaMilla', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idTipoMedio', type: 'string'},
            {name: 'nombreTipoMedio', type: 'string'}
        ]
    });
        
     storeUltimaMilla = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelUltimaMilla",     
        proxy: {            
            type: 'ajax',
            url: urlGetUltimaMilla,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        }, 
        listeners: {
            load: {
                fn: function(){
                    storeUltimaMilla.add({idTipoMedio:'', nombreTipoMedio: 'Todos'});
                    storeUltimaMilla.sort('idTipoMedio', 'ASC');
                }
            }
        }
    });
    
    cboUltimaMilla = new Ext.form.ComboBox({
        id: 'cboUltimaMilla',
        name: 'cboUltimaMilla',
        xtype: 'combobox',
        editable: false,
        store: storeUltimaMilla,
        labelAlign: 'left',
        valueField: 'idTipoMedio',
        displayField: 'nombreTipoMedio',
        fieldLabel: 'Ultima Milla',
        width: 320,
        height: 30
    });
    
    storeTipoEjecucion = Ext.create('Ext.data.Store', {
        autoLoad: true,
        fields: ['intIdTipoEjecucion', 'strNombreTipoEjecucion'],
        data : [
            {"intIdTipoEjecucion":"Normal", "strNombreTipoEjecucion":"Normal"}
        ]
    });

    cboTipoEjecucion = new Ext.form.ComboBox({
        id: 'cboTipoEjecucion',
        name: 'cboTipoEjecucion',
        xtype: 'combobox',
        editable: false,
        store: storeTipoEjecucion,
        labelAlign: 'left',
        valueField: 'intIdTipoEjecucion',
        displayField: 'strNombreTipoEjecucion',
        fieldLabel: 'Tipo de Ejecución',
        queryMode: 'local',
        width: 300,
        listeners:
        {
            select: function()
            {
                if(cboTipoEjecucion.getValue().search('Planificada') != -1){
                    DTFechaPlanificada.show();
                    cboHora.show();
                }else{
                    DTFechaPlanificada.hide();
                    cboHora.hide();
                }
            }
        }
    });
    
    function activacionCamposSecundario() {
        if(cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 ){
            cboMotivos.show();
            txtCapacidad1.hide();
            txtCapacidad2.hide();
            txtPrecio.hide();
            cboVelocidadesIsb.hide();
        } else {
            cboMotivos.hide();            
            activacionCamposSecundarioProducto();
        }
    }
    
    function activacionCamposSecundarioProducto() {
        txtPrecio.reset();
        txtPrecio.enable();
        var strEsISB = "NO";
        if(cboTipoSolicitud.getRawValue().search("CANCELACION") == -1){            
            if(cboProductos.getValue() != null){
              var recordProd = cboProductos.findRecord(cboProductos.valueField || cboProductos.displayField, cboProductos.getValue());
              var strNombreTecnico = recordProd.get('strNombreTecnico');
              if(strNombreTecnico === "INTERNET SMALL BUSINESS" || strNombreTecnico === "TELCOHOME")
              {
                  txtCapacidad1.hide();
                  txtCapacidad2.hide();
                  strEsISB = "SI";
                  cboVelocidadesIsb.setValue(null);
                  cboVelocidadesIsb.setRawValue(null);
                  var boxVelocidades = entidadSolicitudMasiva.wait( 'Por favor espere mientras se cargan las velocidades...', 
                                                                    'Velocidades');
                  storeVelocidadesIsb.proxy.extraParams = { 
                                                              intIdProducto: cboProductos.getValue().substring(0,cboProductos.getValue().search('-'))
                                                            };
                  
                  storeVelocidadesIsb.load(function(records, operation, success) {
                      boxVelocidades.hide();
                  });
              }
              else if(cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 || 
                  cboTipoSolicitud.getRawValue().search("DEMOS") != -1){
                    txtCapacidad1.setValue(null);
                    txtCapacidad2.setValue(null);
                     
                     if((cboTipoSolicitud.getRawValue() == "DEMOS"))
                     {
                         txtDuracionDemo.show();
                     }                     
                     

                    txtAreaDescripcion.setValue(null);
                    if(chkBoxActivo.getValue() == true) {
                        txtCapacidad1.show();
                        txtCapacidad2.show();
                    } else if(chkBoxObsoleto.getValue() == true) {
                        cboProductosObsoletos.show();                
                        var box = entidadSolicitudMasiva.wait('Por favor espere mientras se cargan los productos obsoletos...', 'Productos Obsoletos');
                        cboProductosObsoletos.setValue(null);
                        cboProductosObsoletos.setRawValue(null);
                                                                       
                        storeProductosObsoletos.proxy.extraParams = { 
                                                                        strIdCompuesto: 'N', 
                                                                        strEstado: 'Inactivo',
                                                                        intIdProductoSeleccionado: cboProductos.getValue(),
                                                                        strNombreTecnico: strNombreTecnico
                                                                    };
                        storeProductosObsoletos.load(function(records, operation, success) {
                            box.hide();
                        });
                    }
                } else {
                    txtCapacidad1.hide();
                    txtCapacidad2.hide();
                }
                txtPrecio.setValue(null);   
                txtPrecio.show();
                
                if((cboTipoSolicitud.getRawValue() == "DEMOS") && (cboEsFacturable.getRawValue() == "NO"))
                {
                    txtPrecio.hide();                       
                }                
            } 
        }

        if (strEsISB == "SI")
        {
          if(cboTipoSolicitud.getRawValue() != "CAMBIO PRECIO")
          {
              cboVelocidadesIsb.show();
          }
        }
        else
        {
            cboVelocidadesIsb.show();
            cboVelocidadesIsb.hide();
        }
        

    }
    
    //Creamos Store para Estados de Solicitudes Masivas
    Ext.define('modelMotivos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdMotivo', type: 'int'},
            {name: 'strNombreMotivo', type: 'string'}
        ]
    });
        
     storeMotivos = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelMotivos",
        proxy: {
            type: 'ajax',
            url: urlGetMotivos,
            reader: {
                type: 'json',
                root: 'jsonMotivos'
            }
        }
    });
    
    cboMotivos = new Ext.form.ComboBox({
        id: 'cboMotivos',        
        name: 'cboMotivos',
        xtype: 'combobox',
        queryMode: 'local',
        editable: true,
        store: storeMotivos,
        labelAlign: 'left',
        valueField: 'intIdMotivo',
        displayField: 'strNombreMotivo',
        fieldLabel: 'Motivos Cancelación',
        width: 300
    });
    
    storeVelocidadesIsb = new Ext.data.Store({
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: strUrlGetVelocidadesIsb,
            reader: {
                type: 'json',
                root: 'arrayRegistros'
            },
            extraParams:
                {
                    intIdProducto: 0
                }
        },
        fields:
            [
                {name: 'valor1', type: 'string'}
            ]
    });
    
    cboVelocidadesIsb = new Ext.form.ComboBox({
        id: 'comboVelocidadesIsb',        
        name: 'comboVelocidadesIsb',
        xtype: 'combobox',
        editable: false,
        store: storeVelocidadesIsb,
        valueField: 'valor1',
        displayField: 'valor1',
        fieldLabel: 'Velocidad(MB)',
        width: 295,
        labelWidth:'100px',
        listeners:
            {
                select: function()
                {
                    calcularPrecioCambioPlanIsb();
                }
            }
    });
    
    txtSolicitudMasiva = Ext.create('Ext.panel.Panel', {
        border: false,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table'
        }
      });
    
    objBotonesPanel = Ext.create('Ext.panel.Panel', {
        id: 'objBotonesPanel',
        name: 'objBotonesPanel',
        border: false,
        buttonAlign: 'right',
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 4,
            align: 'rigth'
        },
        width: '100%',
        items: [
            txtSolicitudMasiva
        ],
        buttons:[            
            { 
                id:         'btnCrearSolicitudMasiva',
                text:       'Crear Solicitud Masiva',
                iconCls:    'icon_solicitud',
                cls:        'margin-10',
                disabled:   true,
                handler: function() {                  
                    Ext.Msg.confirm('Alerta','Se creará una Solicitud Masiva con los Servicios seleccionados. Desea continuar?', function(btn){
                        if(btn=='yes'){
                            var strIdServicios = entidadSolicitudMasiva.arrayToStr(arrayPuntosServiciosSeleccionados, ',');
                            var strJsonDetalleCaract    = Ext.JSON.encode({arrayData: arrayParametrosCreacionXDetalle});                            
                            var box = entidadSolicitudMasiva.wait('Por favor espere mientras se crea la solicitud...', 'Solicitudes Masivas');
                            Ext.Ajax.request({
                                url: urlCreateSolicitudMasiva,
                                method: 'post',
                                params: {  
                                    intIdTipoSolicitud  : arrayParametrosCreacion['intIdTipoSolicitud'],
                                    strRutaArchivo      : arrayParametrosCreacion['strRutaArchivo'],
                                    strIdServicios      : strIdServicios,
                                    strJsonDetalleCaract: strJsonDetalleCaract,
                                    intTiempoSolDemo    : txtDuracionDemo.getValue(),
                                },
                                success: function(response)
                                {
                                    var text = Ext.decode(response.responseText);
                                    var msg  = "";
                                    
                                    if ("100" === text.strStatus)
                                    {
                                        if(text.total !== 0)
                                        {
                                            msg = "<br>El punto tiene los siguientes servicios en estado <b>Rechazado</b>. \n\
                                                  Por favor gestionar la anulación de los mismos con el \n\
                                                  asesor comercial para continuar con la cancelación :";
                                                  
                                            text.registros.forEach(function(entry) {
                                               msg = msg + "<br><label><b style='font-size: 17.5px;'>&#10551;</b>"+entry+"</label>";
                                            });
                                        }
                                        limpiarTodo();
                                    }                                    
                                    box.destroy();
                                    entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus+msg);
                                },
                                failure: function(response)
                                {
                                    var text = Ext.decode(response.responseText);
                                    entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                                    box.destroy();
                                }
                            });
                        }
                     });                      
                }
            }
        ]
    });
    
    txtDuracionDemo = Ext.create('Ext.form.Text',
    {
        id: 'txtDuracionDemo',
        name: 'txtDuracionDemo',
        fieldLabel: 'Dias',
        labelAlign: 'left',
        allowBlank: true,
        width: 250,
        maskRe: entidadSolicitudMasiva.strDecimalMask,
        regex: entidadSolicitudMasiva.strDecimalRegex,
        regexText: "<b>Error</b></br>Número Invalido.",
        validator: function(v) {
            return entidadSolicitudMasiva.strDecimalRegex.test(v)?true:"Número Invalido";
        }
    });      
    
    objEjecucionPanel = Ext.create('Ext.panel.Panel', {
        id: 'objEjecucionPanel',
        name: 'objEjecucionPanel',
        border: false,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items :[  
            txtDuracionDemo,
            DTFechaPlanificada,
            cboHora
        ]
    });
    
    txtCapacidad1 = Ext.create('Ext.form.Text',
    {
        id: 'txtCapacidad1',
        name: 'txtCapacidad1',
        fieldLabel: 'CAPACIDAD1',
        labelAlign: 'left',
        allowBlank: true,
        width: 250,
        maskRe: entidadSolicitudMasiva.strDecimalMask,
        regex: entidadSolicitudMasiva.strDecimalRegex,
        regexText: "<b>Error</b></br>Número Invalido.",
        validator: function(v) {
            return entidadSolicitudMasiva.strDecimalRegex.test(v)?true:"Número Invalido";
        }
    });
    
    txtCapacidad2 = Ext.create('Ext.form.Text',
    {
        id: 'txtCapacidad2',
        name: 'txtCapacidad2',
        fieldLabel: 'CAPACIDAD2',
        labelAlign: 'left',
        allowBlank: true,
        width: 250,
        maskRe: entidadSolicitudMasiva.strDecimalMask,
        regex: entidadSolicitudMasiva.strDecimalRegex,
        regexText: "<b>Error</b></br>Número Invalido.",
        validator: function(v) {
            return entidadSolicitudMasiva.strDecimalRegex.test(v)?true:"Número Invalido";
        }
    });
    
    txtPrecio = Ext.create('Ext.form.Text',
    {
        id: 'txtPrecio',
        name: 'txtPrecio',
        fieldLabel: 'Precio',
        labelAlign: 'left',
        allowBlank: true,
        width: 295,
        labelWidth:'100px',
        maskRe: entidadSolicitudMasiva.strDecimalMask,
        regex: entidadSolicitudMasiva.strDecimalRegex,
        regexText: "<b>Error</b></br>Número Invalido.",
        validator: function(v) {
            return entidadSolicitudMasiva.strDecimalRegex.test(v)?true:"Número Invalido";
        }
    });
    
    objCaracteristicasPanel = Ext.create('Ext.panel.Panel', {
        id: 'objCaracteristicasPanel',
        name: 'objCaracteristicasPanel',
        border: false,        
        height: 50,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 3,
            align: 'left'
        },
        items :[
            txtCapacidad1,
            txtCapacidad2
        ]
    });
   
    txtAreaDescripcion = Ext.create('Ext.form.field.TextArea',
    {
        id: 'txtAreaDescripcion',
        name: 'txtAreaDescripcion',
        fieldLabel: 'Descripción',
        labelAlign: 'top',
        allowBlank: true,
        width: '100%',
        height: 60
    });
    
    var chkBoxObsoleto = new Ext.form.Radio({
        boxLabel: 'Obsoleto',
        id: 'chkBoxObsoleto',
        name: 'grEstadosProd',
        inputValue: 'chkBoxObsoleto'
    });
    
    var chkBoxActivo = new Ext.form.Radio({
        boxLabel: 'Actual',
        id: 'chkBoxActivo',
        name: 'grEstadosProd',
        inputValue: 'chkBoxActivo',
        checked: true
    });
    
    var rbValueEstadosProd = 'chkBoxActivo';
    var rdEstadosProd = new Ext.form.RadioGroup({
        fieldLabel: 'Vigencia',        
        colspan: 2,
        columns: 3,
        width: 450,
        items: [chkBoxActivo, chkBoxObsoleto],
        listeners: {
            change: function(field, newValue, oldValue) {
                if(cboTipoSolicitud.getValue() != null)
                {
                    limpiarParcial();
                    rbValueEstadosProd = newValue.grEstadosProd;
                    switch (rbValueEstadosProd) {
                        case 'chkBoxObsoleto':
                            cboUltimaMilla.disable();
                            txtCapacidad1.setValue(null);
                            txtCapacidad1.hide();
                            txtCapacidad2.setValue(null);
                            txtCapacidad2.hide();
                            storeProductos.proxy.extraParams = {
                                                                    strIdCompuesto: 'N',
                                                                    strTipoSolicitud: cboTipoSolicitud.getValue(),
                                                                    strEstado: 'Inactivo'
                                                                };
                            break;
                        case 'chkBoxActivo':
                            cboProductosObsoletos.hide();
                            var strEsConcentrador = (cboTipoSolicitud.getRawValue().search("CANCELACION") == -1) ? 'NO' : '';
                            storeProductos.proxy.extraParams = {
                                                                    strIdCompuesto: 'S',
                                                                    strTipoSolicitud: cboTipoSolicitud.getValue(),
                                                                    strEsConcentrador: strEsConcentrador
                                                                };
                            break;
                    }

                    var box = entidadSolicitudMasiva.wait('Por favor espere mientras se cargan los producto...', 'Productos');
                    storeProductos.load(function(records, operation, success) {
                        box.hide();
                    });
                }
            }
        }
    });
    
    objFiltrosPanel = Ext.create('Ext.panel.Panel', {
        border: false,
        buttonAlign: 'center',
        layout: {
            tdAttrs: {style: 'padding: 1px;'},
            type: 'table',
            columns: 1,
            align: 'left'
        },
        width: '100%',
        buttons: [
            {
                id: 'buttonFiltrar',
                text: 'Filtrar',
                iconCls: "icon_search",
                handler: function() {
                    //Realiza la petición con los campos seteados en el panel de busqueda
                    if(validacionFiltros()){
                        var strNoIdServicios = entidadSolicitudMasiva.arrayToStr(arrayPuntosServiciosSeleccionados, ',');
                        var idProducto = '';
                        if(chkBoxActivo.getValue() == true){
                            idProducto = cboProductos.getValue().substring(0,cboProductos.getValue().search('-'));
                        } else {
                            idProducto = cboProductos.getValue();
                        }
                        var intIdUltimaMilla = cboUltimaMilla.getValue();
                        storePuntosServicios.proxy.extraParams = { 
                            intIdProducto: idProducto, 
                            strNoIdServicios: strNoIdServicios,
                            intIdUltimaMilla: intIdUltimaMilla,
                            strNombreSolicitud: cboTipoSolicitud.getRawValue(),
                            strEstado: (cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 ) ? 'Activo,In-Corte' : 'Activo',
                            boolCambioPlan: (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 ) ? true : false
                        };
                        storePuntosServicios.load();
                    }                    
                }
            },
            {
                text: 'Limpiar Todo',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiarTodo();
                }
            }
        ],
        items: [
            cboTipoSolicitud,     
            cboEsFacturable,  
            rdEstadosProd,
            cboProductos,                  
            cboUltimaMilla
        ]
    });
    
    storeProductosObsoletos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelProductos",
        proxy: {
            timeout: 90000,
            type: 'ajax',
            url: urlGetProductos,
            reader: {
                type: 'json',
                root: 'jsonProductos'
            },
            extraParams: {
                strIdCompuesto: 'N', 
                strTipoSolicitud: cboTipoSolicitud.getValue(), 
                strEstado: 'Inactivo',
                intIdProductoSeleccionado: cboProductos.getValue()
            }
        }
    });
    
    cboProductosObsoletos = new Ext.form.ComboBox({
        id: 'cboProductosObsoletos',
        name: 'cboProductosObsoletos',
        xtype: 'combobox',
        editable: false,
        store: storeProductosObsoletos,
        labelAlign: 'left',
        valueField: 'strIdProducto',
        displayField: 'strNombreProducto',
        fieldLabel: 'Productos Obsoletos',
        width: 320,
        height: 30,
        listeners:
        {
            select: function()
            {
                var recordProd = cboProductosObsoletos.findRecord(cboProductosObsoletos.valueField || cboProductosObsoletos.displayField, 
                                                                  cboProductosObsoletos.getValue());
                var intPrecio = recordProd.get('intPrecio');
                txtPrecio.setValue(null);
                txtPrecio.setValue(intPrecio);
            }
        }
    });
    
    objFiltrosFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Filtros:</b>',
        flex: 1,
        width: 360,
        height: 230,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 1,
            align: 'left'
        },
        items: [
            objFiltrosPanel
        ]
    });
    
    txtArchivo = Ext.create('Ext.form.Text',
    {
        id: 'txtArchivo',
        name: 'txtArchivo',
        labelAlign: 'top',
        fieldLabel: 'Archivo Adjunto',
        allowBlank: true,
        readOnly: true,
        width: 250
    });
    
    btnAdjuntarArchivo = Ext.create('Ext.Button', {
        text: ' ',
        iconCls: "icon_subir",
        height: 26,
        width: 26,
        style: { marginTop: '10px' },
        handler: function() {            
           entidadSolicitudMasiva.subirArchivo(null, null, null, txtArchivo);
        }
    });
    
    objArchivoPanel = Ext.create('Ext.panel.Panel', {
        id: 'objArchivoPanel',
        name: 'objArchivoPanel',
        border: false,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        width: 300,
        items: [
            txtArchivo,
            btnAdjuntarArchivo
        ]
    });
    
    objArchivoTextAreaPanel = Ext.create('Ext.panel.Panel', {
        id: 'objArchivoTextAreaPanel',
        name: 'objArchivoTextAreaPanel',
        border: false,
        colspan: 2,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        width: 850,
        items: [            
            txtAreaDescripcion,
            objArchivoPanel
        ]
    });
    
    objProdPrecioPanel = Ext.create('Ext.panel.Panel', {
        id: 'objProdPrecioPanel',
        name: 'objProdPrecioPanel',
        border: false,
        height: 50,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items :[
            cboMotivos,
            cboProductosObsoletos,
            cboVelocidadesIsb,
            txtPrecio
        ]
    });

    objNuevaSolMasivaFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Datos de la Nueva Solicitud Masiva:</b>',
        flex: 1,
        width: 1000,
        height: 230,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items: [
            cboTipoEjecucion,
            objEjecucionPanel,
            objProdPrecioPanel,            
            objCaracteristicasPanel,
            objArchivoTextAreaPanel            
        ]
    });
           
    objFilterPanel = Ext.create('Ext.panel.Panel', {
        id: 'objFilterPanel',
        name: 'objFilterPanel',
        buttonAlign: '->',
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        width: 1280,
        items: [
            objFiltrosFieldSet,
            objNuevaSolMasivaFieldSet
        ],
        renderTo: 'filtro'
    });
    
    function limpiarTodo() {
        arrayParametrosCreacionXDetalle = new Array();
        cboTipoSolicitud.setValue(null);
        cboTipoSolicitud.setRawValue(null);
        chkBoxActivo.setValue(true);
        chkBoxObsoleto.setValue(false);
        chkBoxObsoleto.disable();
        
        limpiarParcial();
        
        txtArchivo.setValue(null);
        strRutaArchivo = '';        
        storePuntosServicios.loadData([],false);
        storeServiciosSeleccionados.loadData([],false);
        arrayPuntosServiciosSeleccionados = new Array();
        txtSolicitudMasiva.remove(Ext.getCmp("boxDatos"));
        Ext.getCmp("btnCrearSolicitudMasiva").setDisabled(true);        
        // Ajuste de Tamaño de FieldSet y Grid
        var intHeightIni = objPutosServiciosFieldSet.getHeight();
        objPutosServiciosFieldSet.setHeight(objSolicitudMasivaFieldSet.getHeight());
        var diferencia = objPutosServiciosFieldSet.getHeight() - intHeightIni;
        gridPuntosServicios.setHeight(gridPuntosServicios.getHeight() + diferencia);        
        arrayParametrosCreacion = new Array();
    }
    
    function limpiarParcial() {
        cboTipoEjecucion.setValue(null);
        cboTipoEjecucion.setRawValue(null);
        cboProductos.setValue(null);
        cboProductos.setRawValue(null);
        cboUltimaMilla.setValue(null);
        cboUltimaMilla.setRawValue(null);
        DTFechaPlanificada.setValue(null);
        cboHora.setValue(null);
        cboHora.setRawValue(null);
        cboMotivos.setValue(null);
        cboMotivos.setRawValue(null);
        txtCapacidad1.setValue(null);
        txtCapacidad2.setValue(null);
        txtPrecio.setValue(null);
        txtDuracionDemo.setValue(null);
        txtAreaDescripcion.setValue(null);
        DTFechaPlanificada.hide();
        cboHora.hide();
        cboMotivos.hide();        
        txtCapacidad1.hide();
        txtCapacidad2.hide();
        txtPrecio.hide();
        txtArchivo.setValue(null);
        strRutaArchivo = '';
        
        cboProductosObsoletos.hide();
        cboProductosObsoletos.setValue(null);
        cboProductosObsoletos.setRawValue(null);
        
        
        cboVelocidadesIsb.setValue(null);
        cboVelocidadesIsb.setRawValue(null);
        cboVelocidadesIsb.hide();
    }
    
    function validacionFiltros(){
        flag = true;
        if(cboTipoSolicitud.getValue() == null) {
            flag = false;
            Ext.Msg.alert('Alerta', 'Debe seleccionar un Tipo de Solicitud.');
        } else if(cboProductos.getValue() == null) {
            flag = false;
            Ext.Msg.alert('Alerta', 'Debe seleccionar un producto.');
        }
        return flag;
    }
    
    function validacionCambiosCancelacion(){
        flag = true;
        
        if (cboTipoSolicitud.getRawValue() != null && cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 && cboMotivos.getValue() == null) {
            flag = false;
            Ext.Msg.alert('Alerta', 'Debe seleccionar un Motivo de Cancelación.');
        }
        
        if(cboTipoEjecucion.getValue() === null) {
            flag = false;
            Ext.Msg.alert('Alerta', 'Debe seleccionar un Tipo de Ejecución.');
        }
        
        if(flag && cboTipoSolicitud.getRawValue().search("CANCELACION") == -1){
            var recordProdBusq = cboProductos.findRecord(cboProductos.valueField || cboProductos.displayField, cboProductos.getValue());
            var strNombreTecnicoBusq = recordProdBusq.get('strNombreTecnico');
            if(cboProductos.getValue().search('SI') != -1 && cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1){
                if(strNombreTecnicoBusq != "INTERNET SMALL BUSINESS" && strNombreTecnicoBusq !="TELCOHOME")
                {
                    if(txtCapacidad1.getValue() == "") {
                        flag = false;
                        Ext.Msg.alert('Alerta', 'Debe ingresar una CAPACIDAD1 válida.');
                    } else if(txtCapacidad2.getValue() == "") {
                        flag = false;
                        Ext.Msg.alert('Alerta', 'Debe ingresar una CAPACIDAD2 válida.');
                    }
                }
            }

            if(cboTipoSolicitud.getRawValue().search("DEMOS") != -1)
            {
                if(txtDuracionDemo.getValue() == "")
                {
                    flag = false;
                    Ext.Msg.alert('Alerta', 'Debe ingresar la duración del Demo.');
                }
                else if(txtDuracionDemo.getRawValue() < 2 || txtDuracionDemo.getRawValue() > 15)
                {
                    flag = false;
                    Ext.Msg.alert('Alerta', 'El tiempo para el demo es mínimo 2 y máximo 15 dias.');                    
                }
            }

            if(chkBoxObsoleto.getValue() == true && cboProductosObsoletos.getValue() =="") {
                flag = false;
                Ext.Msg.alert('Alerta', 'Debe seleccionar un Producto Obsoleto al cual Cambiar.');
            }
            
            if(cboTipoSolicitud.getRawValue().search("DEMOS") != -1)
            {
                if(txtCapacidad1.getValue() == "")
                {
                    flag = false;
                    Ext.Msg.alert('Alerta', 'Debe ingresar la CAPACIDAD1 para el Demo.');                    
                }
                else if(txtCapacidad2.getValue() == "")
                {
                    flag = false;
                    Ext.Msg.alert('Alerta', 'Debe ingresar una CAPACIDAD2 para el Demo.');
                }                 
                                                
                if(cboEsFacturable.getRawValue() == "SI")
                {
                    if(txtPrecio.getValue() == "")
                    {
                        flag = false;
                        Ext.Msg.alert('Alerta', 'Debe ingresar un precio a facturar para el Demo.');
                    }
                }
            }
            else
            {
                if(txtPrecio.getValue() == "")
                {
                    flag = false;
                    Ext.Msg.alert('Alerta', 'Debe ingresar un precio válido.');
                }
                else if(!(Utils.REGEX_PRECIO.test(txtPrecio.getValue())))
                {                                           
                    Ext.Msg.alert('Error ', 'Formato de precio no v\u00e1lido, el valor ingresado debe tener hasta 2 decimales Ej: (2.50)');
                    flag = false;
                }

                if(strNombreTecnicoBusq == "INTERNET SMALL BUSINESS" || strNombreTecnicoBusq == "TELCOHOME")
                {
                    if(cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1)
                    {
                        if(cboVelocidadesIsb.getValue() == null || cboVelocidadesIsb.getValue() == "") {
                            flag = false;
                            Ext.Msg.alert('Alerta', 'Debe seleccionar una VELOCIDAD válida.');
                        }
                    }
                }
            }
        }

        if(flag && cboTipoEjecucion.getRawValue().search("Normal") == -1 ){
            if(DTFechaPlanificada.getRawValue() == "") {
                flag = false;
                Ext.Msg.alert('Alerta', 'Debe escoger una Fecha válida de planificación.');
            } else if(cboHora.getRawValue() == "") {
                flag = false;
                Ext.Msg.alert('Alerta', 'Debe escoger una Hora válida de planificación');
            }
        }
        
        if(flag && txtAreaDescripcion.getValue() == "") {
            flag = false;
            Ext.Msg.alert('Alerta', 'Debe ingresar una Descripción para la Solicitud Masiva.');
        }
        
        return flag;
    }
    
    chkBoxModelPuntoServicio = new Ext.selection.CheckboxModel({
        checkOnly : true,
        renderer : function(val, meta, record, rowIndex, colIndex, store,view){
           if(((record.get('boolTieneCPELimit') === false && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 || 
               cboTipoSolicitud.getRawValue().search("DEMOS") != -1)
               && record.get('boolEsEnlace') === true) || ((record.get('boolSeleccionable') === false 
               && record.get('boolTieneCapacidades') === false) && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 || 
               cboTipoSolicitud.getRawValue().search("DEMOS") != -1)) && chkBoxActivo.getValue() == true) || 
               (cboTipoSolicitud.getRawValue().search("DEMOS") != -1 && record.get('boolDemoActivo') === true))
            {
                return '';
            }else{
                var baseCSSPrefix = Ext.baseCSSPrefix;
                meta.tdCls = baseCSSPrefix + 'grid-cell-special ' + baseCSSPrefix + 'grid-cell-row-checker';
                return '<div class="' + baseCSSPrefix + 'grid-row-checker"> </div>';
            }
        },
        onHeaderClick: function(headerCt, header, e) {
            if (header.isCheckerHd) {
                e.stopEvent();
                var isChecked = header.el.hasCls(Ext.baseCSSPrefix + 'grid-hd-checker-on');
                if (isChecked) {
                    this.deselectAll(true);
                } else {
                    var view  = this.views[0];
                    var store = view.getStore();
                    var model = view.getSelectionModel();
                    var s = [];
                    store.queryBy(function(record) {
                        if(((record.get('boolSeleccionable') === true || record.get('boolTieneCapacidades') === true) 
                            && record.get('boolTieneCPELimit') === true
                            && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 || 
                            cboTipoSolicitud.getRawValue().search("DEMOS") != -1)) ||
                            (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") == -1 || 
                            cboTipoSolicitud.getRawValue().search("DEMOS") == -1) && chkBoxActivo.getValue() == true){
                            s.push(record);
                        }
                   });
                   model.select(s);
                }
            }
        },
        listeners: {
            selectionchange: function (selectionModel, selected, options) {                
                Ext.each(selected, function (rec) {});
                gridPuntosServicios.down('#btnAnadirServicios').setDisabled(selected.length == 0);
            }
        }
    });
    
    toolbarPuntosServicios = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            items:
                [{xtype: 'tbfill'},
                    {
                        xtype: 'button',
                        cls : 'scm-button',
                        id: "btnAnadirServicios",
                        iconCls: "icon_anadir",
                        text: '<span class="bold color-green">Añadir a Servicios Seleccionados</span>',
                        disabled: true,
                        scope: this,
                        handler: function (){                            
                            if(validacionCambiosCancelacion()){                             
                                var strIdServicios          = '';
                                var strDescripcionSolMasiva = '';
                                var strJsonDetalleCaract    = '';
                                //Valida que haya seleccionado servicios por punto, caso contrario muestra un mensaje de alerta
                                if (chkBoxModelPuntoServicio.getSelection().length > 0)
                                {
                                    var arraySeleccionadosNuevos = new Array();
                                    var intTotalArray = arrayPuntosServiciosSeleccionados.length;
                                    
                                    //Itera los chkBox y concatena los ID Servicios en un solo string strIdServicios
                                    for (var intForIndex = 0; intForIndex < chkBoxModelPuntoServicio.getSelection().length; intForIndex++) {
                                        arrayPuntosServiciosSeleccionados[intTotalArray + intForIndex] = chkBoxModelPuntoServicio.getSelection()[intForIndex].data.intIdServicio;
                                        arraySeleccionadosNuevos[intForIndex] = chkBoxModelPuntoServicio.getSelection()[intForIndex].data.intIdServicio;
                                    }
                                    
                                    var strIdServicios = entidadSolicitudMasiva.arrayToStr(arrayPuntosServiciosSeleccionados, ','); 
                                    
                                    //Realiza la petición con los campos seteados en el panel de busqueda
                                    var idProducto = '';
                                    if(chkBoxActivo.getValue() == true) {
                                        idProducto = cboProductos.getValue().substring(0,cboProductos.getValue().search('-'));
                                    } else {
                                        idProducto = cboProductos.getValue();
                                    }
                                    var intIdUltimaMilla = cboUltimaMilla.getValue();
                                    storePuntosServicios.proxy.extraParams = { 
                                        intIdProducto: idProducto, 
                                        strNoIdServicios: strIdServicios, 
                                        intIdUltimaMilla: intIdUltimaMilla,
                                        strNombreSolicitud: cboTipoSolicitud.getRawValue(),
                                        strEstado: (cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 ) ? 'Activo,In-Corte' : 'Activo',
                                        boolCambioPlan: (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 ) ? true : false
                                    };
                                    storePuntosServicios.load();
                                    
                                    strDescripcionSolMasiva = obtenerParametrosSolicitud(arraySeleccionadosNuevos);
                                    strJsonDetalleCaract    = Ext.JSON.encode({arrayData: arrayParametrosCreacionXDetalle});
                                        
                                    storeServiciosSeleccionados.proxy.extraParams = {
                                            strIdServicios: strIdServicios,
                                            intIdTipoSolicitud: cboTipoSolicitud.getValue(),
                                            strJsonDetalleCaract : strJsonDetalleCaract,
                                            strEstado: (cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 ) ? 'Activo,In-Corte' : 'Activo'
                                        };
                                    
                                    storeServiciosSeleccionados.load();
                                    
                                    txtSolicitudMasiva.remove(Ext.getCmp("boxDatos"));
                                    txtSolicitudMasiva.add({id:'boxDatos',xtype: 'box', border:false,autoEl: {cn: strDescripcionSolMasiva}});
                                    Ext.getCmp("btnCrearSolicitudMasiva").setDisabled(false);
                                    
                                    // Ajuste de Tamaño de FieldSet y Grid
                                    var intHeightIni = objPutosServiciosFieldSet.getHeight();
                                    objPutosServiciosFieldSet.setHeight(objSolicitudMasivaFieldSet.getHeight());
                                    var diferencia = objPutosServiciosFieldSet.getHeight() - intHeightIni;
                                    gridPuntosServicios.setHeight(gridPuntosServicios.getHeight() + diferencia);
                                    
                                }else{
                                    Ext.Msg.alert('Alerta', 'Debe Seleccionar al menos un Servicio.');
                                }
                            }
                        }
                    }
                ]
    });
    
    Ext.define('modelPuntosServicios', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdServicio',         type: 'int'},
            {name: 'strDescripcionServicio',type: 'string'},
            {name: 'intIdPunto',            type: 'int'},
            {name: 'strLogin',              type: 'string'},
            {name: 'intIdProdructo',        type: 'int'},
            {name: 'strdescripcionProducto',type: 'string'},
            {name: 'strPrecioVenta',        type: 'string'},
            {name: 'strestado',             type: 'string'},
            {name: 'strDatosActuales',      type: 'string'},
            {name: 'boolSeleccionable',     type: 'boolean'},
            {name: 'boolTieneCapacidades',  type: 'boolean'},
            {name: 'boolEsEnlace',          type: 'boolean'},
            {name: 'boolTieneCPELimit',     type: 'boolean'},
            {name: 'strLoginAux',           type: 'string'},
            {name: 'boolDemoActivo',        type: 'boolean'}
        ]
    });
    
    storePuntosServicios = Ext.create('Ext.data.Store', {
       // autoLoad: true,
        storeId: 'storePuntosServicios',
        model: 'modelPuntosServicios',
        groupField: 'strLogin',
        proxy: {
            timeout: 999999,
            type: 'ajax',
            url: urlGetPuntosServicios,
            reader: {
                type: 'json',
                root: 'jsonPuntosServicios'
            }
        }
    });
    
    groupingPuntosServicios = Ext.create('Ext.grid.feature.Grouping',{
        groupHeaderTpl: 'Punto: {name} ({rows.length} Servicio{[values.rows.length > 1 ? "s" : ""]})'
    });
    
    gridPuntosServicios = Ext.create('Ext.grid.Panel', {
        store: storePuntosServicios,
        height: 460,
        width: '100%',
        multiSelect: false,
        selModel: chkBoxModelPuntoServicio,
        viewConfig: {enableTextSelection: true, preserveScrollOnRefresh: true},
        dockedItems: [toolbarPuntosServicios],
        title: 'Puntos Sucursales por Servicios',
        features: [groupingPuntosServicios],
        columns: [{
            text: 'Login',
            flex: 1,
            dataIndex: 'intIdServicio',
            hidden: true
        },{
            text: 'Login',
            flex: 1,
            dataIndex: 'strLogin',
            hidden: true
        },{
            text: 'Detalle de Servicio',
            flex: 1,
            dataIndex: 'strDescripcionServicio',
            title:  'strDescripcionServicio',
            renderer: function(value, meta, record) {
                if((record.get('boolTieneCPELimit') === false && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 
                    || cboTipoSolicitud.getRawValue().search("DEMOS") != -1)
                    && record.get('boolEsEnlace') === true && chkBoxActivo.getValue() == true))
                {
                    meta.style = "background-color:#F5F6CE;";
                }
                else if((record.get('boolSeleccionable') === false && record.get('boolTieneCapacidades') === false) 
                        && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 || 
                        cboTipoSolicitud.getRawValue().search("DEMOS") != -1) && chkBoxActivo.getValue() == true){
                    meta.style = "background-color:#F6CED8;";
                }
                return value;
            }
        },{
            text: 'Datos Actuales del Servicio',
            flex: 1,
            dataIndex: 'strDatosActuales',
            renderer: function(value, meta, record) {
                if(record.get('boolTieneCPELimit') === false && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 
                    || cboTipoSolicitud.getRawValue().search("DEMOS") != -1)
                    && record.get('boolEsEnlace') === true && chkBoxActivo.getValue() == true)
                {
                    meta.style = "background-color:#F5F6CE;";
                }
                else if((record.get('boolSeleccionable') === false && record.get('boolTieneCapacidades') === false) 
                        && (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 
                        || cboTipoSolicitud.getRawValue().search("DEMOS") != -1) != -1 && chkBoxActivo.getValue() == true
                        || !record.get('strLoginAux'))
                {
                    meta.style = "background-color:#F6CED8;";
                }
                return value;
            }
        }],
        listeners: {            
            viewready: entidadSolicitudMasiva.crearToolTip
        },
        bbar: new Ext.PagingToolbar({
            store: storePuntosServicios,       
            displayInfo: true,
            prependButtons: true,
            emptyMsg: entidadSolicitudMasiva.emptyMsg,
            displayMsg: entidadSolicitudMasiva.displayMsg,
            pageSize: entidadSolicitudMasiva.intPageSize
        })
    });
    
    Ext.define('modelServiciosSeleccionados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdServicio', type: 'int'},
            {name: 'strDescripcionServicio', type: 'string'},
            {name: 'intIdPunto', type: 'int'},
            {name: 'strLogin', type: 'string'},
            {name: 'intIdProdructo', type: 'int'},
            {name: 'strdescripcionProducto', type: 'string'},
            {name: 'strPrecioVenta', type: 'string'},
            {name: 'strPrecioMinimo', type: 'string'},
            {name: 'strestado', type: 'string'},
            {name: 'strDatosActuales', type: 'string'},
            {name: 'strDatosNuevos', type: 'string'},
            {name: 'strNivelAprobacion', type: 'string'},
            {name: 'strMensaje', type: 'string'}
        ]
    });
    
    storeServiciosSeleccionados = Ext.create('Ext.data.Store', {
       // autoLoad: true,
        storeId: 'storeServiciosSeleccionados',
        model: 'modelServiciosSeleccionados',
        groupField: 'strLogin',
        proxy: {
            timeout: 999999,
            type: 'ajax',
            url: urlGetServiciosSeleccionados,
            reader: {
                type: 'json',
                root: 'jsonServiciosSeleccionados'
            }
        }
    });
    
    toolbarServiciosSeleccionados = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            items:
                [{xtype: 'tbfill'},
                    {
                        xtype: 'button',
                        cls : 'scm-button',
                        id: "btnQuitarServicios",
                        iconCls: "icon_remover",
                        text: '<span class="bold color-wine">Quitar de Servicios Seleccionados</span>',
                        disabled: true,
                        scope: this,
                        handler: function () {
                            var strJsonDetalleCaract = '';
                           if (chkBoxModelServiciosSeleccionados.getSelection().length > 0) {
                                entidadSolicitudMasiva.removeItemsSelectedFromArray(chkBoxModelServiciosSeleccionados,arrayPuntosServiciosSeleccionados);
                                entidadSolicitudMasiva.removeItemsSelectedFromArrayData(chkBoxModelServiciosSeleccionados,arrayParametrosCreacionXDetalle);
                            } else {
                                Ext.Msg.alert('Alerta', 'Debe Seleccionar al menos un Servicio a ser Descartado de la Solicitud Masiva.');
                            }
                            
                            if(arrayPuntosServiciosSeleccionados.length == 0) {
                                txtSolicitudMasiva.remove(Ext.getCmp("boxDatos"));
                                Ext.getCmp("btnCrearSolicitudMasiva").setDisabled(true);
                            }                            
                            var strNoIdServicios = entidadSolicitudMasiva.arrayToStr(arrayPuntosServiciosSeleccionados, ',');
                            var idProducto = '';
                            if(chkBoxActivo.getValue() == true) {
                                idProducto = cboProductos.getValue().substring(0,cboProductos.getValue().search('-'));
                            } else {
                                idProducto = cboProductos.getValue();
                            }
                            strNoIdServicios = (strNoIdServicios == "")?'-1':strNoIdServicios;                            
                            strJsonDetalleCaract    = Ext.JSON.encode({arrayData: arrayParametrosCreacionXDetalle});
                            storeServiciosSeleccionados.proxy.extraParams = {
                                strIdServicios: strNoIdServicios,
                                intIdTipoSolicitud: cboTipoSolicitud.getValue(),
                                strJsonDetalleCaract: strJsonDetalleCaract,
                                strEstado: (cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 ) ? 'Activo,In-Corte' : 'Activo'
                            };
                            storeServiciosSeleccionados.load();
                            var intIdUltimaMilla = cboUltimaMilla.getValue();
                            storePuntosServicios.proxy.extraParams = { 
                                intIdProducto: idProducto, 
                                strNoIdServicios: strNoIdServicios, 
                                intIdUltimaMilla: intIdUltimaMilla,
                                strNombreSolicitud: cboTipoSolicitud.getRawValue(),
                                strEstado: (cboTipoSolicitud.getRawValue().search("CANCELACION") != -1 ) ? 'Activo,In-Corte' : 'Activo',
                                boolCambioPlan: (cboTipoSolicitud.getRawValue().search("CAMBIO PLAN") != -1 ) ? true : false
                            };
                            storePuntosServicios.load();
                        }
                    }
                ]
    });
        
    chkBoxModelServiciosSeleccionados = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                Ext.each(selected, function (rec) {});
                gridServiciosSeleccionados.down('#btnQuitarServicios').setDisabled(selected.length == 0);
            }
        }
    });
    
    groupingServiciosSeleccionados = Ext.create('Ext.grid.feature.Grouping',{
        groupHeaderTpl: 'Punto: {name} ({rows.length} Servicio{[values.rows.length > 1 ? "s" : ""]})'
    });
    
    //Crea el grid que muestra la información obtenida desde el controlador de la cabera de parámetros.
    gridServiciosSeleccionados = Ext.create('Ext.grid.Panel', {
        id: 'gridServiciosSeleccionados',
        name: 'gridServiciosSeleccionados',
        title: 'Servicios Seleccionados',
        store: storeServiciosSeleccionados,        
        height: 460,
        width: '100%',
        multiSelect: false,
        selModel: chkBoxModelServiciosSeleccionados,
        viewConfig: {enableTextSelection: true, preserveScrollOnRefresh: true},
        features: [groupingServiciosSeleccionados],
        dockedItems: [toolbarServiciosSeleccionados],
        columns: [
            {
                text: 'Login',
                flex: 1,
                dataIndex: 'strLogin',
                hidden: true
            },
            {
                text: 'Detalle de Servicio',
                flex: 1,
                dataIndex: 'strDescripcionServicio'
            },
            {
                text: 'Datos Actuales del Servicio',
                flex: 1,
                dataIndex: 'strDatosActuales'
            },      
            {
                text: 'Datos Nuevos del Servicio',
                width: 150,
                dataIndex: 'strDatosNuevos'
            },
            {
                text: 'Precio Mínimo',
                width: 80,
                align: 'center',
                dataIndex: 'strPrecioMinimo'
            },
            {
                text: 'Nivel de Aprobación',
                width: 150,
                dataIndex: 'strNivelAprobacion'
            },
            {
                text: 'Mensaje',
                width: 200,
                dataIndex: 'strMensaje'
            }
        ],
        bbar: new Ext.PagingToolbar({
            store: storeServiciosSeleccionados,       
            displayInfo: true,
            prependButtons: true,
            emptyMsg: entidadSolicitudMasiva.emptyMsg,
            displayMsg: entidadSolicitudMasiva.displayMsg,
            pageSize: entidadSolicitudMasiva.intPageSize
        })
    });
    
    objPutosServiciosFieldSet = Ext.create('Ext.form.FieldSet', {
        id: 'objPutosServiciosFieldSet',
        name: 'objPutosServiciosFieldSet',
        title: '<b>Servicios a Cambiar / Cancelar: </b>',
        frame: true,
        width: 360,
        height: 680,
        items: [
            {                 
             xtype: 'box', 
             border:false,
             autoEl:
                {
                 cn: "<b>Seleccionar servicios a cambiar/cancelar:</b><br>Solo se puede realizar cambios sobre servcios Activos y cancelaciones \n\
                      sobre servicios Activos e In-Corte<br><br>"
                }
            },
            gridPuntosServicios,
            {                 
             xtype: 'box', 
             border:false,
             autoEl:
                {
                 cn: "<br><div class='smc-color-legend'><div class='title'>Advertencias:</div><div class='bcolor error'></div><div class='legend'>\n\
                      Servivio no seleccionable (Falta Información Técnica)</div><div class='smc-separator'></div><div class='bcolor warning'>\n\
                      </div><div class='legend'>Servivio no seleccionable (Falta BW Límites en el Modelo del CPE)</div></div>"
                }
            }
        ]
    });
    
    objSolicitudMasivaFieldSet = Ext.create('Ext.form.FieldSet', {
        id: 'objSolicitudMasivaFieldSet',
        name: 'objSolicitudMasivaFieldSet',
        title: '<b>Solicitud Masiva de Cambio / Cancelación: </b>',
        frame: true,
        width: 1000,
        height: 680,
        items: [
            objBotonesPanel,
            gridServiciosSeleccionados
        ]
    });
    
    objContenedorPanel = Ext.create('Ext.panel.Panel', {
        id: 'objContenedorPanel',
        name: 'objContenedorPanel',
        border: false,
        buttonAlign: 'center',
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 4,
            align: 'left'
        },
        width: 1400,
        items: [
            objPutosServiciosFieldSet,
            objSolicitudMasivaFieldSet
        ],
        renderTo: 'objContenedorPanel'
    });
    
    function obtenerParametrosSolicitud(arrayPuntosServiciosSeleccionados){
        var str = "";
        str = "<b>Tipo Solicitud: </b>"+cboTipoSolicitud.getRawValue();
        
        if(strRutaArchivo !== '' && txtArchivo.getValue() != '')
        {
            str += " <br><b>Archivo Adjunto: </b>"+ txtArchivo.getValue();
        }
        
        cargarArrayParametrosCreacion(arrayPuntosServiciosSeleccionados);
        return str;
    }
    
    function cargarArrayParametrosCreacion(arrayPuntosServiciosSeleccionados){

        var idProducto = '';
        if(chkBoxActivo.getValue() == true){
            idProducto = cboProductos.getValue().substring(0,cboProductos.getValue().search('-'));
        } else {
            idProducto = cboProductos.getValue();
        }
        
        if(arrayParametrosCreacion['intIdTipoSolicitud'] == null)
        {
            arrayParametrosCreacion['intIdTipoSolicitud'] = cboTipoSolicitud.getValue();
            arrayParametrosCreacion['strRutaArchivo']     = strRutaArchivo;
        }
                
        for(var i = 0; i < arrayPuntosServiciosSeleccionados.length; i++)
        {
            var intIdDetalle = arrayPuntosServiciosSeleccionados[i];
            if(intIdDetalle != null)
            {
                var objDetalle = {
                                    intIdDetalle: intIdDetalle,
                                    data : {
                                        strTipoEjecucion:       cboTipoEjecucion.getValue(),
                                        strFechaPlanificada:    DTFechaPlanificada.getRawValue(),
                                        strHora:                cboHora.getRawValue(),
                                        intIdMotivo:            cboMotivos.getValue(),
                                        intIdProducto:          idProducto,
                                        strTipoSolicitud:       cboTipoSolicitud.getRawValue(),
                                        strEsFacturable:        cboEsFacturable.getRawValue(),
                                        intDuracionDemo:        txtDuracionDemo.getRawValue(),
                                        intPrecio:              txtPrecio.getValue(),
                                        intCapacidad1:          txtCapacidad1.getValue(),
                                        intCapacidad2:          txtCapacidad2.getValue(),
                                        strDescripcion:         txtAreaDescripcion.getValue(),
                                        intIdUltimaMilla:       cboUltimaMilla.getValue(),
                                        intIdprodObsoleto:      (chkBoxObsoleto.getValue() ? cboProductosObsoletos.getValue() : '' ),
                                        intVelocidad:           cboVelocidadesIsb.getValue(),
                                    }
                                 };
                arrayParametrosCreacionXDetalle.push(objDetalle);
            }
        }
    }
    
    DTFechaPlanificada.hide();
    cboHora.hide();
    cboMotivos.hide();
    txtCapacidad1.hide();
    txtCapacidad2.hide();
    txtPrecio.hide();
    txtDuracionDemo.hide();    
    chkBoxObsoleto.disable();
    cboProductosObsoletos.hide();
    cboVelocidadesIsb.hide();
});


function calcularPrecioCambioPlanIsb()
{
    Ext.MessageBox.wait('Calculando precio. Por favor espere..');
    Ext.Ajax.request({
        url: strUrlGetCaractProdFuncionPrecio,
        method: 'post',
        timeout: 400000,
        params:
            {
                productoId: cboProductos.getValue().substring(0,cboProductos.getValue().search('-'))
            },
        success: function (response)
        {
            Ext.MessageBox.hide();

            var datosCaracts = Ext.JSON.decode(response.responseText);

            if (datosCaracts.strStatusGetInfo === "OK")
            {
                var respuestaPrecios = calcularValoresProductoIsb(datosCaracts);
                var precioProductoIsb = respuestaPrecios["precioISB"];
                Ext.getCmp('txtPrecio').setRawValue(precioProductoIsb.toFixed(2));
                Ext.getCmp('txtPrecio').setValue(precioProductoIsb.toFixed(2));
            } 
            else
            {
                Ext.Msg.alert('Error ', 'No se ha podido obtener la información correctamente');
            }
        },
        failure: function ()
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', 'Falló al obtener la información');
        }
    });
}