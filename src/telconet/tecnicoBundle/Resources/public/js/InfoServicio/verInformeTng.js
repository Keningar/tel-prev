/**
 * Funcion que sirve para ver los datos ingresados en el formulario de Activacion de 
 * servicios tecnico de TNG
 * 
 * @author Jesús Banchen <jbanchen@telconet.ec>
 * @version 1.0 14-14-2019
 * @param data
 * @param idAccion
 * */
function verServicioTng(data, idAccion){

        Ext.Ajax.request({
             url: getHistorialServicio,
                       method: 'get',
                       timeout: 10000,
                       params: {
                               idServicio : data.idServicio,

                                },
                       success: function(response) {

                           var json = Ext.JSON.decode(response.responseText);
                           var contador =json['total'];
                           var cont="";
                           for(var int=0 ; int <contador;int++)
                           {
                               if(json['encontrados'][int].estado != 'Backlog')
                               {
                                   cont=int; 
                               }
                           }
                           Ext.getCmp('observacionActivarServicio').setValue(json['encontrados'][cont].observacion);
                       }
            });

        Ext.Ajax.request({
             url: getDatosCliente,
                       method: 'get',
                       timeout: 10000,
                       params: {
                                    idServicio : data.idServicio,
                                },
                       success: function(response) {
                          var json = Ext.JSON.decode(response.responseText);
                          Ext.getCmp('direccion').setValue(json['encontrados'][0].direccion);
                       }
            });

if(data.productoId != "" && data.idServicio != ""){
    Ext.get(gridServicios.getId()).mask('Cargando...');
        Ext.Ajax.request({
                url: verDatosConexionUpStreamTng,
                method: 'get',
                timeout: 10000,
                params: {
                        idServicio : data.idServicio,
                        idProducto : data.productoId
                         },
                success: function(response) {Ext.get(gridServicios.getId()).unmask();
                    var json = Ext.JSON.decode(response.responseText);
                    Ext.getCmp('codConexionUpStream').setValue(json['encontrados'][0].strValor);                
                    Ext.getCmp('equipoConexionUpstream').setValue(json['encontrados'][1].strValor);
                    Ext.getCmp('sidquipoUpstream').setValue(json['encontrados'][2].strValor);
                    Ext.getCmp('puerto').setValue(json['encontrados'][3].strValor);
                    Ext.getCmp('tipoTransporte').setValue(json['encontrados'][4].strValor);
                    Ext.getCmp('provTransUpstream').setValue(json['encontrados'][5].strValor);
                    Ext.getCmp('idServicioRedTransp').setValue(json['encontrados'][6].strValor);
                    Ext.getCmp('idenRedtransporte').setValue(json['encontrados'][7].strValor);
                    var dateFeUno = new Date(Ext.Date.parse(json['encontrados'][8].strValor, 'd-F-Y'));
                    Ext.getCmp('fecha').setValue(Ext.Date.format(dateFeUno, 'd-F-Y'));
                    Ext.getCmp('plazo').setValue(json['encontrados'][9].strValor);
                    var datefecDos = new Date(Ext.Date.parse(json['encontrados'][10].strValor, 'd-F-Y'));
                    Ext.getCmp('fVenceContrato').setValue(Ext.Date.format(datefecDos, 'd-F-Y'));
                    Ext.getCmp('cpeSerialNumber').setValue(json['encontrados'][11].strValor) ;
                    Ext.getCmp('connectorType').setValue(json['encontrados'][12].strValor);
                    Ext.getCmp('esquema').setValue(json['encontrados'][13].strValor);
                    Ext.getCmp('iPWanGateway').setValue(json['encontrados'][14].strValor) ;
                    Ext.getCmp('ipWanCPE').setValue(json['encontrados'][15].strValor);
                    Ext.getCmp('ipLanCPE').setValue(json['encontrados'][16].strValor);
                    Ext.getCmp('iPlanCliente').setValue(json['encontrados'][17].strValor);
                    Ext.getCmp('vRF').setValue(json['encontrados'][18].strValor);
                    Ext.getCmp('aSN').setValue(json['encontrados'][19].strValor) ;
                    Ext.getCmp('monitoreoExterno').setValue(json['encontrados'][20].strValor);
                } 
                });}

    var confirmarFormPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85, 
                    msgTarget: 'side',
                    bodyStyle: 'padding:2px'
                },
                layout: {
                    type: 'table',
                    labelAlign: 'center',
                    columns: 1
                },
                items: [
                    {
                        xtype: 'fieldset',
                        title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos del Servicio</b>',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 1000,
                            height: 80
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 4,
                                    align: 'stretch',
                                    tableAttrs: {
                                      style: {
                                      width: '100%'
                                              }
                                                  } 
                                },
                                items: [
                                    {
                                    xtype: 'textfield',
                                    name: 'servicio',
                                    fieldLabel: 'Login',
                                    value: data.login,
                                    width: '25%',
                                    colspan :1,
                                    readOnly: true
                                    },
                                    {
                                    xtype: 'textfield',
                                    name: 'producto',
                                    fieldLabel: 'Producto',
                                    value: data.nombreProducto,
                                    width: '25%',
                                    colspan :1,
                                    readOnly: true
                                    },                                  
                                    {
                                    xtype: 'textfield',
                                    fieldLabel: 'Capacidad 1',
                                    name: 'capacidad1',
                                    id: 'capacidad1',
                                    value: data.capacidadUno,
                                    width: '25%',
                                    colspan :1,
                                    readOnly: true
                                    },
                                    {
                                    xtype: 'textfield',
                                    fieldLabel: 'Capacidad 2',
                                    name: 'capacidad2',
                                    id: 'capacidad2',
                                    value: data.capacidadDos,
                                    width: '25%',
                                    colspan :1,
                                    readOnly: true
                                    },
                                    { fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                    xtype: 'textfield',
                                    name: 'estado',
                                    fieldLabel: 'Estado',
                                    displayField:data.estado,
                                    value: data.estado,
                                    colspan :1,
                                    width: '25%',
                                    readOnly: true
                                    },
                                    {
                                    xtype: 'textarea',
                                    name: 'direccion',
                                    id:'direccion',
                                    fieldLabel: 'Direccion',
                                    value:  '',
                                    width: 744,
                                    height: 45,
                                    colspan :3,
                                    readOnly: true
                                    }
                                ]
                            }

                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: '<i class="fa fa-th" aria-hidden="true"></i>&nbsp;<b>Datos Ingreso</b>',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 1000,
                            height: 370
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 4,
                                    align: 'stretch',
                                    tableAttrs: {
                                                style: {width: '100%'}
                                                  } 
                                },
                                items: [
                                      {   
                                     xtype: 'textfield',
                                     id : 'codConexionUpStream',
                                     name: 'codConexionUpStream',
                                     fieldLabel: 'Cod. Conexión UpStream',
                                     value: "",
                                     readOnly: true,
                                     width: '25%'
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'sidquipoUpstream',
                                     name: 'sidquipoUpstream',
                                     fieldLabel: 'SID Equipo de Conexion Upstream',
                                     value: "",
                                     readOnly: true,
                                     width: '25%'
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'equipoConexionUpstream',
                                     name: 'equipoConexionUpstream',
                                     fieldLabel: 'Equipo Conexión BackBone',
                                     value: '',
                                     readOnly: true,
                                     width: '25%'
                                   },
                                    {
                                     xtype: 'textfield',
                                     id : 'puerto',
                                     name: 'puerto',
                                     fieldLabel: 'Puerto',
                                     value: '',
                                     readOnly: true,
                                     width: '25%'
                                    },{ fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                     xtype: 'textfield',
                                     fieldLabel: 'Tipo Transporte UpStream',
                                     id: 'tipoTransporte',
                                     name: 'tipoTransporte',
                                     value:'',
                                     width: '25%',
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'provTransUpstream',
                                     name: 'provTransUpstream',
                                     fieldLabel: 'Proveedor Transporte Upstream',
                                     value: "",
                                     readOnly: true,
                                     width: '25%'
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'idServicioRedTransp',
                                     name: 'idServicioRedTransp',
                                     fieldLabel: 'Id de servicio en red Transporte',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'idenRedtransporte',
                                     name: 'idenRedtransporte',
                                     fieldLabel: 'Identificador en red transporte',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'fecha',
                                     name: 'fecha',
                                     fieldLabel: 'Fecha Instalación (RFS)',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'plazo',
                                     name: 'plazo',
                                     fieldLabel: 'Plazo(meses)',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'fVenceContrato',
                                     name: 'fVenceContrato',
                                     fieldLabel: 'Fecha vence Contrato',
                                     value:'',
                                     readOnly:true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'cpeSerialNumber',
                                     name: 'cpeSerialNumber',
                                     fieldLabel: 'CPE Serial Number',
                                     value: "",
                                     readOnly: true
                                    },{ fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                     xtype: 'textfield',
                                     id : 'connectorType',
                                     name: 'connectorType',
                                     fieldLabel: 'Connector Type',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'esquema',
                                     name: 'esquema',
                                     fieldLabel: 'Esquema',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'iPWanGateway',
                                     name: 'iPWanGateway',
                                     fieldLabel: 'IP Wan Gateway',
                                     value: "",
                                     readOnly: true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'ipWanCPE',
                                     name: 'ipWanCPE',
                                     fieldLabel: 'IP Wan CPE',
                                     value: "",
                                     readOnly: true
                                    },{ fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                     xtype: 'textfield',
                                     id : 'ipLanCPE',
                                     name: 'ipLanCPE',
                                     fieldLabel: 'IP Lan CPE',
                                     value: "",
                                     readOnly: true
                                    },
                                    { xtype: 'textfield',
                                     id : 'iPlanCliente',
                                     name: 'iPlanCliente',
                                     fieldLabel: 'IP LAN Cliente',
                                     value: "",
                                     readOnly: true
                                    },
                                    { xtype: 'textfield',
                                     id : 'vRF',
                                     name: 'vRF',
                                     fieldLabel: 'VRF',
                                     value: "",
                                     readOnly: true
                                    },
                                    { xtype: 'textfield',
                                     id : 'aSN',
                                     name: 'aSN',
                                     fieldLabel: 'ASN',
                                     value: "",
                                     readOnly: true
                                    },{ fieldLabel: '',colspan :4, height: 10, border: false},
                                    {
                                     xtype: 'textfield',
                                     fieldLabel: 'Monitoreo Externo  SI o NO',
                                     id: 'monitoreoExterno',
                                     name: 'monitoreoExterno',
                                     readOnly: true,
                                     value: ""
                                    },{ fieldLabel: '',colspan :4, height: 10, border: false},
                                    {
                                    id : 'observacionActivarServicio',
                                    xtype: 'textarea',
                                    name: 'observacionActivarServicio',
                                    fieldLabel: 'Observación',
                                    value: "",
                                    readOnly: true,
                                    colspan:2,
                                    width: 480
                                    }
                                ]
                            }
                        ]
                    }
                ],
                buttons: [{
                    text: 'Cancelar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });
    
            var win = Ext.create('Ext.window.Window', {
                title: 'Informe de datos de Confirmacio de Servicio',
                modal: true,
                width: 1040,
                closable: true,
                layout: 'fit',
                items: [confirmarFormPanel]
            }).show();
}

