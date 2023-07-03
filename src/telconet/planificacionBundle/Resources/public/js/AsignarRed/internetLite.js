/**
 * Función que sirve para mostrar la pantalla de asignación de recursos de red para Internet Small Business
 * 
 * @author Lizbeth Cruz <mlcruztelconet.ec>
 * @version 1.0 27-11-2017
 * 
 * @author Lizbeth Cruz <mlcruztelconet.ec>
 * @version 1.1 07-02-2019 Se agrega nombre técnico de las ips adicionales para un servicio TelcoHome para que cumplan el mismo flujo
 *                          que las ips adicionales para un Small Business
 * 
 * @author Pablo Pin <ppin@telconet.ec>
 * @versio 1.2 17-06-2019 - Se agrega la definición de la variable IdServicio para que pueda ser enviada como parametro,
 *                          en strUrlAjaxComboElementosByPadre, y controlar el estado "Restringido".  
 *                          
 * @author Antonio Ayala <afayala@telconet.ec>
 * @versio 1.3 19-02-2021 - Se agrega la definición de la variable tipoIp para saber si es Fija o Privada.                         
 * 
 */
 function showRecursoDeRedInternetLite(rec)
 {
     const idServicio = rec.data.id_servicio;
     winRecursoDeRed = "";
     formPanelRecursosDeRed = "";
 
     var strTipoRed = "";
     if (typeof rec.get('strTipoRed') !== "undefined"){
         strTipoRed = rec.get('strTipoRed');
     }
     var booleanTipoRedGpon = false;
     if (typeof rec.get('booleanTipoRedGpon') !== "undefined"){
         booleanTipoRedGpon = rec.get('booleanTipoRedGpon');
     }
 
     if (!winRecursoDeRed)
     {
         if (rec.data.nombreTecnico === 'IPSB')
         {
             boolCargaCmbs = false;
         } 
         else
         {
             //nroIp = rec.data.cantidadIp;
             boolCargaCmbs = true;
         }
         
         
         var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
         CamposRequeridos = Ext.create('Ext.Component', {
             html: iniHtmlCamposRequeridos,
             padding: 1,
             layout: 'anchor',
             style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
         });
 
         storeElementosSplitters = new Ext.data.Store({
             total: 'total',
             pageSize: 10000,
             autoLoad: boolCargaCmbs,
             listeners: {
                 load: function() {
                     if(rec.data.idSplitter )
                     {
                         Ext.getCmp("cmbSplitter").setValue(rec.data.idSplitter);
                     }
                 }
             },
             proxy: {
                 type: 'ajax',
                 url: strUrlAjaxComboElementosByPadre,
                 timeout: 120000,
                 reader: {
                     type: 'json',
                     totalProperty: 'total',
                     root: 'encontrados'
                 },
                 actionMethods: {
                     create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                 },
                 extraParams: {
                     popId: rec.data.idCaja,
                     elemento: 'SPLITTER',
                     idServicio: idServicio
                 }
             },
             fields:
                 [
                     {name: 'idElemento', mapping: 'idElemento'},
                     {name: 'nombreElemento', mapping: 'nombreElemento'}
                 ]
         });
 
         storeInterfacesBySplitter = new Ext.data.Store({
             autoLoad: boolCargaCmbs,
             total: 'total',
             pageSize: 10000,
             proxy: {
                 type: 'ajax',
                 url: 'getJsonInterfacesByElemento',
                 timeout: 120000,
                 reader: {
                     type: 'json',
                     totalProperty: 'total',
                     root: 'encontrados'
                 },
                 extraParams: {
                     idElemento: rec.get("idSplitter"),
                     interfaceSplitter: rec.data.interfaceSplitter
                 }
             },
             fields:
                 [
                     {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                     {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
                 ]
         });
 
 
         if (rec.data.tieneIp && rec.data.cantidadIp !== 0) {
             var nroIp = rec.data.cantidadIp ? rec.data.cantidadIp : 0;
             var plan = rec.data.idPlan ? rec.data.idPlan : 0;
             var storeIps = new Ext.data.Store({
                 id: 'idPoolStore',
                 total: 'total',
                 pageSize: 10,
                 autoLoad: true,
                 listeners: {
                     'load': function(store, records, successful) {
                         if (successful) {
                             if (store.getProxy().getReader().rawData.error) {
                                 Ext.Msg.show({
                                     title: 'Importante',
                                     msg: store.getProxy().getReader().rawData.error,
                                     width: 300,
                                     buttons: Ext.MessageBox.OK,
                                     icon: Ext.MessageBox.ERROR
                                 });
                             }
                             else if (store.getProxy().getReader().rawData.faltantes) {
                                 if (store.getProxy().getReader().rawData.faltantes !== 0) {
                                     Ext.Msg.show({
                                         title: 'Importante',
                                         msg: 'No se encontraron disponibles el número de ips requeridas. <br /> Ips faltantes: '
                                             + store.getProxy().getReader().rawData.faltantes + '<br /> Por favor solicitar a GEPON crear un '
                                             + 'nuevo pool de ip.',
                                         width: 300,
                                         buttons: Ext.MessageBox.OK,
                                         icon: Ext.MessageBox.ERROR
                                     });
                                 }
                             }
                         }
                     }
                 },
                 proxy: {
                     type: 'ajax',
                     url: nroIp + '/' + rec.data.elementoId + '/' + rec.data.id_servicio + '/' + rec.data.id_punto + '/' + rec.data.esPlan +
                         '/' + plan + '/' + rec.data.marcaOlt + '/getips',
                     timeout: 300000,
                     reader: {
                         type: 'json',
                         totalProperty: 'total',
                         root: 'ips',
                         messageProperty: 'message'
                     }
                 },
                 fields:
                     [
                         {name: 'ip', mapping: 'ip'},
                         {name: 'mascara', mapping: 'mascara'},
                         {name: 'gateway', mapping: 'gateway'},
                         {name: 'tipo', mapping: 'tipo'},
                         {name: 'scope', mapping: 'scope'}
                     ]
             });
 
 
             //grid de ips
             gridIps = Ext.create('Ext.grid.Panel', {
                 id: 'gridIps',
                 store: storeIps,
                 columnLines: true,
                 columns: [{
                         header: 'Tipo',
                         dataIndex: 'tipo',
                         width: 100,
                         sortable: true
                     }, {
                         header: 'Ip',
                         dataIndex: 'ip',
                         width: 150,
                         editor: {
                             id: 'ip',
                             name: 'ip',
                             xtype: 'textfield',
                             valueField: ''
                         }
                     },
                     {
                         header: 'Mascara',
                         dataIndex: 'mascara',
                         width: 150,
                         editor: {
                             id: 'mascara',
                             name: 'mascara',
                             xtype: 'textfield',
                             valueField: ''
                         }
                     },
                     {
                         header: 'Gateway',
                         dataIndex: 'gateway',
                         width: 150,
                         editor: {
                             id: 'gateway',
                             name: 'gateway',
                             xtype: 'textfield',
                             valueField: ''
                         }
                     },
                     {
                         header: 'Scope',
                         dataIndex: 'scope',
                         hidden: true,
                         hideable: false
                     }],
                 viewConfig: {
                     stripeRows: true
                 },
                 frame: true,
                 height: 200,
                 title: 'Ips del Cliente'
             });
 
 
         } else {
 
             gridIps = Ext.create('Ext.Component', {
                 html: "<br>"
             });
         }
 
         formPanelRecursosDeRed = Ext.create('Ext.form.Panel', {
             buttonAlign: 'center',
             BodyPadding: 10,
             bodyStyle: "background: white; padding:10px; border: 0px none;",
             frame: true,
             items: [
                 CamposRequeridos,
                 {
                     xtype: 'panel',
                     border: false,
                     layout: {type: 'hbox', align: 'stretch'},
                     items: [
                         {
                             xtype: 'fieldset',
                             title: 'Datos del Cliente',
                             defaultType: 'textfield',
                             style: "font-weight:bold; margin-bottom: 15px;",
                             layout: 'anchor',
                             defaults: {
                                 width: '350px'
                             },
                             items: [
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Cliente',
                                     name: 'info_cliente',
                                     id: 'info_cliente',
                                     value: rec.get("cliente"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Login',
                                     name: 'info_login',
                                     id: 'info_login',
                                     value: rec.get("login2"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Ciudad',
                                     name: 'info_ciudad',
                                     id: 'info_ciudad',
                                     value: rec.get("ciudad"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Direccion',
                                     name: 'info_direccion',
                                     id: 'info_direccion',
                                     value: rec.get("direccion"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Sector',
                                     name: 'info_nombreSector',
                                     id: 'info_nombreSector',
                                     value: rec.get("nombreSector"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Es Recontratacion',
                                     name: 'es_recontratacion',
                                     id: 'es_recontratacion',
                                     value: rec.get("esRecontratacion"),
                                     allowBlank: false,
                                     readOnly: true
                                 }
                             ]
                         },
                         {
                             xtype: 'fieldset',
                             title: 'Datos del Servicio',
                             defaultType: 'textfield',
                             style: "font-weight:bold; margin-bottom: 15px;",
                             defaults: {
                                 width: '350px'
                             },
                             items: [
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Tipo Orden',
                                     name: 'tipo_orden_servicio',
                                     id: 'tipo_orden_servicio',
                                     value: rec.get("tipo_orden"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Servicio',
                                     name: 'info_servicio',
                                     id: 'info_servicio',
                                     value: rec.get("producto"),
                                     allowBlank: false,
                                     readOnly: true,
                                     listeners: {
                                         render: function(c) {
                                             Ext.QuickTips.register({
                                                 target: c.getEl(),
                                                 text: rec.get("items_plan")
                                             });
                                         }
                                     }
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Capacidad Uno',
                                     name: 'capacidadUno',
                                     id: 'capacidadUno',
                                     displayField: rec.get("capacidad1"),
                                     value: rec.get("capacidad1"),
                                     hidden: !booleanTipoRedGpon,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Capacidad Dos',
                                     name: 'capacidadDos',
                                     id: 'capacidadDos',
                                     displayField: rec.get("capacidad2"),
                                     value: rec.get("capacidad2"),
                                     hidden: !booleanTipoRedGpon,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Tipo de Red',
                                     name: 'tipoRed',
                                     id: 'tipoRed',
                                     displayField: strTipoRed,
                                     value: strTipoRed,
                                     hidden: !booleanTipoRedGpon,
                                     readOnly: true
                                 }
                             ]
                         }
                     ]
                 },
                 {
                     xtype: 'fieldset',
                     title: 'Datos de Recursos de Red',
                     defaultType: 'textfield',
                     style: "font-weight:bold; margin-bottom: 15px;",
                     defaults: {
                         width: '350px'
                     },
                     items: [
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Ultima Milla',
                             name: 'txt_um',
                             id: 'txt_um',
                             value: rec.get("ultimaMilla"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Cantidad',
                             name: 'txtCantidadIp',
                             id: 'txtCantidadIp',
                             value: rec.get("cantidadIp"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'OLT',
                             name: 'txt_olt',
                             id: 'txt_olt',
                             value: rec.get("pop"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'MARCA OLT',
                             name: 'txt_marca_olt',
                             id: 'txt_marca_olt',
                             value: rec.get("marcaOlt"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Linea',
                             name: 'txt_linea',
                             id: 'txt_linea',
                             value: rec.get("intElemento"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Caja',
                             name: 'txt_caja',
                             width: 450,
                             id: 'txt_caja',
                             value: rec.get("caja"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'combobox',
                             id: 'cmbSplitter',
                             name: 'cmbSplitter',
                             fieldLabel: '* Splitter',
                             typeAhead: true,
                             allowBlank: false,
                             queryMode: "local",
                             triggerAction: 'all',
                             displayField: 'nombreElemento',
                             valueField: 'idElemento',
                             selectOnTab: true,
                             width: 450,
                             store: storeElementosSplitters,
                             lazyRender: true,
                             listClass: 'x-combo-list-small',
                             labelStyle: "color:red;",
                             listeners: {
                                 select: {fn: function(combo, value) {
                                         Ext.getCmp('cmbInterfaceSplitter').reset();
                                         storeInterfacesBySplitter.proxy.extraParams = {idElemento: combo.getValue()};
                                         storeInterfacesBySplitter.load({params: {}});
                                     }}
                             }
                         },
                         {
                             xtype: 'combobox',
                             id: 'cmbInterfaceSplitter',
                             name: 'cmbInterfaceSplitter',
                             fieldLabel: '* Interface',
                             width: 200,
                             typeAhead: true,
                             allowBlank: false,
                             queryMode: "local",
                             triggerAction: 'all',
                             displayField: 'nombreInterfaceElemento',
                             valueField: 'idInterfaceElemento',
                             selectOnTab: true,
                             store: storeInterfacesBySplitter,
                             listClass: 'x-combo-list-small',
                             emptyText: 'Seleccione',
                             labelStyle: "color:red;",
                         },
                         {
                             xtype: 'panel',
                             BodyPadding: 10,
                             bodyStyle: "background: white; padding:10px; border: 0px none;",
                             frame: true,
                             hidden: booleanTipoRedGpon,
                             items: [gridIps]
                         }
                     ]
                 }
             ],
             buttons: [
                 {
                     text: 'Guardar',
                     handler: function() {
                         var tipoSolicitud   = "";
                         var datosIps        = "";
                         var tipoIp          = rec.data.strTipoIp;
                         var idDetSolPlanif = rec.get("id_factibilidad");
                         if (rec.data.nombreTecnico == "IPSB") 
                         {
                             tipoSolicitud = "info técnica";
                         }
                         else
                         {
                             tipoSolicitud = "planificación";
                             var idSplitter          = Ext.getCmp('cmbSplitter').value;
                             if (!idSplitter || idSplitter === "" || idSplitter === 0)
                             {
                                 Ext.Msg.alert('Alerta', 'Por favor seleccione el splitter!');
                                 return;
                             }
                             var idInterfaceSplitter = Ext.getCmp('cmbInterfaceSplitter').value;
                             if (!idInterfaceSplitter || idInterfaceSplitter === "" || idInterfaceSplitter === 0)
                             {
                                 Ext.Msg.alert('Alerta', 'Por favor seleccione la interface!');
                                 return;
                             }
                         }
                         if (!idDetSolPlanif || idDetSolPlanif == "" || idDetSolPlanif == 0)
                         {
                             Ext.Msg.alert('Alerta', 'No existe una solicitud de '+tipoSolicitud+' asociada a este servicio');
                             return;
                         }
                         
                         if (rec.data.tieneIp) 
                         {
                             var datosIps = obtenerDatosIpsPublicasInternetLite(rec.data.cantidadIp, tipoIp);
 
                             if (datosIps === "") {
                                 Ext.Msg.alert('Alerta', 'El número de Ips obtenidas no coincide con el número de Ips requeridas: ' 
                                                         + rec.data.cantidadIp);
                                 return;
                             }
                         }
                         
                         connRecursoDeRed.request({
                             url: booleanTipoRedGpon ? strUrlGuardaRecursosRedDatosGpon : strUrlGuardaRecursosRedInternetLite,
                             timeout: 12000000,
                             method: 'post',
                             params: {
                                         idDetSolPlanif:         idDetSolPlanif, 
                                         idSplitter:             idSplitter, 
                                         idInterfaceSplitter:    idInterfaceSplitter,
                                         datosIps:               datosIps, 
                                         marcaOlt:               rec.get("marcaOlt"),
                                         nombreTecnico:          rec.data.nombreTecnico
                             },
                             success: function(response) {
                                 var datos = Ext.JSON.decode(response.responseText);
                                 cierraVentanaRecursoDeRedInternetLite();
                                 if (datos.status === "OK")
                                 {
                                     Ext.Msg.alert('Mensaje', datos.mensaje, function(btn) {
                                         if (btn == 'ok') {
                                             store.load();
                                         }
                                     });
                                 }
                                 else
                                 {
                                     Ext.Msg.alert('Error ', datos.mensaje);
                                 }
                             },
                             failure: function(result) {
                                 Ext.Msg.alert('Error ', result.responseText);
                             }
                         });
                     }
                 }
                 , {
                     text: 'Cerrar',
                     handler: function() {
                         cierraVentanaRecursoDeRedInternetLite();
                     }
                 }
             ]
         });
         
         Ext.getCmp('cmbInterfaceSplitter').setValue(rec.data.interfaceSplitter);
         if (rec.data.nombreTecnico == "IPSB") 
         {
             Ext.getCmp('txt_olt').setVisible(false);
             Ext.getCmp('txt_marca_olt').setVisible(false);
             Ext.getCmp('txt_linea').setVisible(false);
             Ext.getCmp('txt_caja').setVisible(false);
             Ext.getCmp('cmbSplitter').setVisible(false);
             Ext.getCmp('cmbInterfaceSplitter').setVisible(false);
         } 
         else 
         {
             Ext.getCmp('cmbSplitter').setVisible(true);
             Ext.getCmp('txtCantidadIp').setVisible(false);
         }
         winRecursoDeRed = Ext.widget('window', {
             title: 'Ingreso de Recursos de Red',
             layout: 'fit',
             resizable: false,
             modal: true,
             items: [formPanelRecursosDeRed]
         });
     }
     winRecursoDeRed.show();
 }
 
 function cierraVentanaRecursoDeRedInternetLite() {
     winRecursoDeRed.close();
     winRecursoDeRed.destroy();
 }
 
 
 function obtenerDatosIpsPublicasInternetLite(cantidad, tipo)
 {
     if (gridIps.getStore().getCount() >= 1) {
         var array_relaciones = new Object();
         array_relaciones['total'] = gridIps.getStore().getCount();
         array_relaciones['caracteristicas'] = new Array();
 
         var array_data = new Array();
         var numIps = 0;
 
         for (var i = 0; i < gridIps.getStore().getCount(); i++)
         {
             array_data.push(gridIps.getStore().getAt(i).data);
             if (gridIps.getStore().getAt(i).data.tipo == tipo)
                 numIps++;
         }
 
         if (numIps > cantidad || numIps < cantidad) {
             return "";
         }
 
         array_relaciones['caracteristicas'] = array_data;
         return Ext.JSON.encode(array_relaciones);
     } else {
         return "";
     }
 }
 
 /**
  * 
  * Función que mostrará la pantalla para la migración de recursos de red en servicios Small Business y TelcoHome
  * 
  * @author Lizbeth Cruz <mlcruz@telconet.ec>
  * @version 1.0 25-06-2019
  * 
  */
 function showRecursoDeRedMigracionProdsTn(rec)
 {
     const idServicio = rec.data.id_servicio;
     winRecursoDeRed = "";
     formPanelRecursosDeRed = "";
     if (!winRecursoDeRed)
     {
         //******** html campos requeridos...
         var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
         CamposRequeridos = Ext.create('Ext.Component', {
             html: iniHtmlCamposRequeridos,
             padding: 1,
             layout: 'anchor',
             style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
         });
 
         storeElementosSplitters = new Ext.data.Store({
             total: 'total',
             pageSize: 10000,
             autoLoad: true,
             listeners: {
                 load: function () {
                     if (rec.data.idSplitter)
                     {
                         Ext.getCmp("cmbSplitter").setValue(rec.data.idSplitter);
                     }
                 }
             },
             proxy: {
                 type: 'ajax',
                 url: strUrlAjaxComboElementosByPadre,
                 timeout: 120000,
                 reader: {
                     type: 'json',
                     totalProperty: 'total',
                     root: 'encontrados'
                 },
                 actionMethods: {
                     create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                 },
                 extraParams: {
                     popId: rec.data.idCaja,
                     elemento: 'SPLITTER',
                     idServicio: idServicio
                 }
             },
             fields:
                 [
                     {name: 'idElemento', mapping: 'idElemento'},
                     {name: 'nombreElemento', mapping: 'nombreElemento'}
                 ]
         });
 
 
         storeInterfacesBySplitter = new Ext.data.Store({
             autoLoad: true,
             total: 'total',
             pageSize: 10000,
             proxy: {
                 type: 'ajax',
                 url: 'getJsonInterfacesByElemento',
                 timeout: 120000,
                 reader: {
                     type: 'json',
                     totalProperty: 'total',
                     root: 'encontrados'
                 },
                 extraParams: {
                     idElemento: rec.get("idSplitter"),
                     interfaceSplitter: rec.data.interfaceSplitter,
                     estado: "reserved"
                 }
             },
             fields:
                 [
                     {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                     {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
                 ]
         });
 
         if (rec.data.tieneIp && rec.data.cantidadIp !== 0) {
             var nroIp = rec.data.cantidadIp ? rec.data.cantidadIp : 0;
             var plan = rec.data.idPlan ? rec.data.idPlan : 0;
             var storeIps = new Ext.data.Store({
                 id: 'idPoolStore',
                 total: 'total',
                 pageSize: 10,
                 autoLoad: true,
                 listeners: {
                     'load': function (store, records, successful) {
                         if (successful) {
                             if (store.getProxy().getReader().rawData.error) {
                                 Ext.Msg.show({
                                     title: 'Importante',
                                     msg: store.getProxy().getReader().rawData.error,
                                     width: 300,
                                     buttons: Ext.MessageBox.OK,
                                     icon: Ext.MessageBox.ERROR
                                 });
                             } else if (store.getProxy().getReader().rawData.faltantes
                                 && store.getProxy().getReader().rawData.faltantes !== 0) {
                                     Ext.Msg.show({
                                         title: 'Importante',
                                         msg: 'No se encontraron disponibles el número de ips requeridas. <br /> Ips faltantes: '
                                             + store.getProxy().getReader().rawData.faltantes
                                             + '<br /> Por favor solicitar a GEPON crear un nuevo pool de ip.',
                                         width: 300,
                                         buttons: Ext.MessageBox.OK,
                                         icon: Ext.MessageBox.ERROR
                                     });
                             }
                         }
                     }
                 },
                 proxy: {
                     type: 'ajax',
                     url: nroIp + '/' + rec.data.idPop + '/' + rec.data.id_servicio + '/' + rec.data.id_punto + '/' + rec.data.esPlan +
                         '/' + plan + '/' + 'MIGRACION' + '/getips',
                     timeout: 120000,
                     reader: {
                         type: 'json',
                         totalProperty: 'total',
                         root: 'ips',
                         messageProperty: 'message'
                     }
                 },
                 fields:
                     [
                         {name: 'ip', mapping: 'ip'},
                         {name: 'mascara', mapping: 'mascara'},
                         {name: 'gateway', mapping: 'gateway'},
                         {name: 'tipo', mapping: 'tipo'}
                     ]
             });
 
             //grid de ips
             gridIps = Ext.create('Ext.grid.Panel', {
                 id: 'gridIps',
                 store: storeIps,
                 columnLines: true,
                 columns: [{
                         header: 'Tipo',
                         dataIndex: 'tipo',
                         width: 100,
                         sortable: true
                     }, {
                         header: 'Ip',
                         dataIndex: 'ip',
                         width: 150,
                         editor: {
                             id: 'ip',
                             name: 'ip',
                             xtype: 'textfield',
                             valueField: ''
                         }
                     },
                     {
                         header: 'Mascara',
                         dataIndex: 'mascara',
                         width: 150,
                         editor: {
                             id: 'mascara',
                             name: 'mascara',
                             xtype: 'textfield',
                             valueField: ''
                         }
                     },
                     {
                         header: 'Gateway',
                         dataIndex: 'gateway',
                         width: 150,
                         editor: {
                             id: 'gateway',
                             name: 'gateway',
                             xtype: 'textfield',
                             valueField: ''
                         }
                     }],
                 viewConfig: {
                     stripeRows: true
                 },
                 frame: true,
                 height: 200,
                 title: 'Ips del Cliente'
             });
 
         } else {
             gridIps = Ext.create('Ext.Component', {
                 html: "<br>"
             });
         }
 
         Ext.define('Ips', {
             extend: 'Ext.data.Model',
             fields: [
                 {name: 'ip', mapping: 'ip'},
                 {name: 'tipo', mapping: 'tipo'}
             ]
         });
 
         var boolDeshabilitaGuardar = false;
         if (rec.get("marcaOlt") == 'TELLION')
         {
             boolDeshabilitaGuardar = true;
         }
 
         formPanelRecursosDeRed = Ext.create('Ext.form.Panel', {
             buttonAlign: 'center',
             BodyPadding: 10,
             bodyStyle: "background: white; padding:10px; border: 0px none;",
             frame: true,
             items: [
                 CamposRequeridos,
                 {
                     xtype: 'panel',
                     border: false,
                     layout: {type: 'hbox', align: 'stretch'},
                     items: [
                         {
                             xtype: 'fieldset',
                             title: 'Datos del Cliente',
                             defaultType: 'textfield',
                             style: "font-weight:bold; margin-bottom: 15px;",
                             layout: 'anchor',
                             defaults: {
                                 width: '350px'
                             },
                             items: [
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Cliente',
                                     name: 'info_cliente',
                                     id: 'info_cliente',
                                     value: rec.get("cliente"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Login',
                                     name: 'info_login',
                                     id: 'info_login',
                                     value: rec.get("login2"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Ciudad',
                                     name: 'info_ciudad',
                                     id: 'info_ciudad',
                                     value: rec.get("ciudad"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Direccion',
                                     name: 'info_direccion',
                                     id: 'info_direccion',
                                     value: rec.get("direccion"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Sector',
                                     name: 'info_nombreSector',
                                     id: 'info_nombreSector',
                                     value: rec.get("nombreSector"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Es Recontratacion',
                                     name: 'es_recontratacion',
                                     id: 'es_recontratacion',
                                     value: rec.get("esRecontratacion"),
                                     allowBlank: false,
                                     readOnly: true
                                 }
                             ]
                         },
                         {
                             xtype: 'fieldset',
                             title: 'Datos del Servicio',
                             defaultType: 'textfield',
                             style: "font-weight:bold; margin-bottom: 15px;",
                             defaults: {
                                 width: '350px'
                             },
                             items: [
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Tipo Orden',
                                     name: 'tipo_orden_servicio',
                                     id: 'tipo_orden_servicio',
                                     value: rec.get("tipo_orden"),
                                     allowBlank: false,
                                     readOnly: true
                                 },
                                 {
                                     xtype: 'textfield',
                                     fieldLabel: 'Servicio',
                                     name: 'info_servicio',
                                     id: 'info_servicio',
                                     value: rec.get("producto"),
                                     allowBlank: false,
                                     readOnly: true,
                                     listeners: {
                                         render: function (c) {
                                             Ext.QuickTips.register({
                                                 target: c.getEl(),
                                                 text: rec.get("items_plan")
                                             });
                                         }
                                     }
                                 }
                             ]
                         }
                     ]
                 },
                 {
                     xtype: 'fieldset',
                     title: 'Datos de Recursos de Red',
                     defaultType: 'textfield',
                     style: "font-weight:bold; margin-bottom: 15px;",
                     defaults: {
                         width: '350px'
                     },
                     items: [
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Ultima Milla',
                             name: 'txt_um',
                             id: 'txt_um',
                             value: rec.get("ultimaMilla"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Cantidad',
                             name: 'txtCantidadIp',
                             id: 'txtCantidadIp',
                             value: rec.get("cantidad"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'OLT',
                             name: 'txt_olt',
                             id: 'txt_olt',
                             value: rec.get("pop"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'MARCA OLT',
                             name: 'txt_marca_olt',
                             id: 'txt_marca_olt',
                             value: rec.get("marcaOlt"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Linea',
                             name: 'txt_linea',
                             id: 'txt_linea',
                             value: rec.get("intElemento"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'textfield',
                             fieldLabel: 'Caja',
                             name: 'txt_caja',
                             width: 450,
                             id: 'txt_caja',
                             value: rec.get("caja"),
                             allowBlank: false,
                             readOnly: true
                         },
                         {
                             xtype: 'combobox',
                             id: 'cmbSplitter',
                             name: 'cmbSplitter',
                             fieldLabel: '* Splitter',
                             typeAhead: true,
                             queryMode: "local",
                             triggerAction: 'all',
                             displayField: 'nombreElemento',
                             valueField: 'idElemento',
                             selectOnTab: true,
                             width: 450,
                             store: storeElementosSplitters,
                             lazyRender: true,
                             listClass: 'x-combo-list-small',
                             labelStyle: "color:red;",
                             listeners: {
                                 select: {fn: function (combo, value) {
                                         Ext.getCmp('cmbInterfaceSplitter').reset();
                                         storeInterfacesBySplitter.proxy.extraParams = {idElemento: combo.getValue(), estado: "reserved"};
                                         storeInterfacesBySplitter.load({params: {}});
                                     }}
                             }
                         },
                         {
                             xtype: 'combobox',
                             id: 'cmbInterfaceSplitter',
                             name: 'cmbInterfaceSplitter',
                             fieldLabel: '* Interface',
                             width: 200,
                             typeAhead: true,
                             allowBlank: false,
                             queryMode: "local",
                             triggerAction: 'all',
                             displayField: 'nombreInterfaceElemento',
                             valueField: 'idInterfaceElemento',
                             selectOnTab: true,
                             store: storeInterfacesBySplitter,
                             listClass: 'x-combo-list-small',
                             emptyText: 'Seleccione',
                             labelStyle: "color:red;",
                         },
                         {
                             xtype: 'panel',
                             BodyPadding: 10,
                             bodyStyle: "background: white; padding:10px; border: 0px none;",
                             frame: true,
                             items: [gridIps]
                         }
                     ]
                 }
             ],
             buttons: [
                 {
                     disabled: boolDeshabilitaGuardar,
                     text: 'Guardar',
                     handler: function () {
                         var datosIps = "";
                         var tipoIp         = rec.data.strTipoIp;
                         var idDetSolPlanif = rec.get("id_factibilidad");
 
                         var idSplitter = Ext.getCmp('cmbSplitter').value;
                         if (!idSplitter || idSplitter === "" || idSplitter === 0)
                         {
                             Ext.Msg.alert('Alerta', 'Por favor seleccione el splitter!');
                             return;
                         }
                         var idInterfaceSplitter = Ext.getCmp('cmbInterfaceSplitter').value;
                         if (!idInterfaceSplitter || idInterfaceSplitter === "" || idInterfaceSplitter === 0)
                         {
                             Ext.Msg.alert('Alerta', 'Por favor seleccione la interface!');
                             return;
                         }
 
                         if (!idDetSolPlanif || idDetSolPlanif == "" || idDetSolPlanif == 0)
                         {
                             Ext.Msg.alert('Alerta', 'No existe una solicitud de migración asociada a este servicio');
                             return;
                         }
 
                         if (rec.data.tieneIp)
                         {
                             datosIps = obtenerDatosIpsPublicasInternetLite(rec.data.cantidadIp, tipoIp);
                             if (datosIps === "")
                             {
                                 Ext.Msg.alert('Alerta', 'El número de Ips obtenidas no coincide con el número de Ips requeridas: '
                                     + rec.data.cantidadIp);
                                 return;
                             }
                         }
 
                         connRecursoDeRed.request({
                             url: strUrlGuardaRecursosRedInternetLite,
                             timeout: 120000,
                             method: 'post',
                             params: {
                                 idDetSolPlanif:                 idDetSolPlanif, 
                                 idSplitter:                     idSplitter, 
                                 idInterfaceSplitter:            idInterfaceSplitter,
                                 datosIps:                       datosIps, 
                                 marcaOlt:                       rec.get("marcaOlt"),
                                 nombreTecnico:                  rec.data.nombreTecnico,
                                 idOltNuevoMigracion:            rec.get("idPop"),
                                 idInterfaceOltNuevoMigracion:   rec.get("intElementoInterface")
                             },
                             success: function (response) {
                                 var datos = Ext.JSON.decode(response.responseText);
                                 cierraVentanaRecursoDeRedInternetLite();
                                 if (datos.status === "OK")
                                 {
                                     Ext.Msg.alert('Mensaje', datos.mensaje, function(btn) {
                                         if (btn == 'ok') {
                                             store.load();
                                         }
                                     });
                                 }
                                 else
                                 {
                                     Ext.Msg.alert('Error ', datos.mensaje);
                                 }
                             },
                             failure: function (result) {
                                 Ext.MessageBox.show({
                                     title: 'Error',
                                     msg: result.responseText,
                                     buttons: Ext.MessageBox.OK,
                                     icon: Ext.MessageBox.ERROR
                                 });
                             }
                         });
 
 
                     }
                 }
                 , {
                     text: 'Cerrar',
                     handler: function () {
                         cierraVentanaRecursoDeRedInternetLite();
                     }
                 }
             ]
         });
         Ext.getCmp('cmbInterfaceSplitter').setValue(rec.data.interfaceSplitter);
         Ext.getCmp('cmbSplitter').setDisabled(true);
         Ext.getCmp('txtCantidadIp').setVisible(false);
 
         winRecursoDeRed = Ext.widget('window', {
             title: 'Migración de Recursos de Red',
             layout: 'fit',
             resizable: false,
             modal: true,
             items: [formPanelRecursosDeRed]
         });
     }
 
     winRecursoDeRed.show();
     //Valida que la marca del olt, en caso de ser TELLION nuestra el mensaje por pantalla
     if ((rec.get("marcaOlt") == 'TELLION'))
     {
         Ext.Msg.show({
                         title: 'Importante',
                         msg: 'Enlace no corresponde a la migración (OLT es TELLION). Favor pedir a GIS que regularice.',
                         width: 300,
                         buttons: Ext.MessageBox.OK,
                         icon: Ext.MessageBox.ERROR
                     });
     }
 }
 