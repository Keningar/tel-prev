/**
 * Funcion que sirve para confirmar los servicios de TNG
 * 
 * @author Jesús Banchen <jbanchen@telconet.ec>
 * @version 1.0 28-03-2019
 * @param data
 * @param idAccion
 * */
function confirmarServicioTng(data, idAccion){
 
var strVariable="Seleccione una Conexion";
 
        Ext.Ajax.request({
        url: getHistorialServicio,
        method: 'get',
        timeout: 10000,
        params: {
            idServicio: data.idServicio,
        },
        success: function (response)
        {
            var json = Ext.JSON.decode(response.responseText);
            var c = json['total'];
            var cont = "";
            for (var int = 0; int < c; int++)
            {
                if (json['encontrados'][int].estado == 'Backlog')
                {
                    cont = int;
                }
            }
            Ext.getCmp('observacionAnterior').setVisible(true);
            var res=json['encontrados'][cont].observacion;
            var res1= replaceAllIsb(res,"<br>","\n");
            var res2= replaceAllIsb(res1,"<b>"," ");
            var res3= replaceAllIsb(res2,"</b>"," ");
           
            Ext.getCmp('observacionAnterior').setValue(res3);
        }
    });

        Ext.Ajax.request({
        url: getDatosCliente,
        method: 'get',
        timeout: 10000,
        params: {
            idServicio: data.idServicio,
        },
        success: function (response) {
            var json = Ext.JSON.decode(response.responseText);
            Ext.getCmp('direccion').setValue(json['encontrados'][0].direccion);
        }
    });

    if (data.productoId != "" && data.idServicio != "" && data.estado == "Backlog")
    {Ext.get(gridServicios.getId()).mask('Consultando Info Tecnica...');
        Ext.Ajax.request({
                url: verDatosConexionUpStreamTng,
                method: 'get',
                timeout: 10000,
                params: {
                        idServicio : data.idServicio,
                        idProducto : data.productoId
                         },
                success: function(response) {
                    Ext.get(gridServicios.getId()).unmask();
                    var json = Ext.JSON.decode(response.responseText);
                    Ext.getCmp('codConexionUpStream').setRawValue( json['encontrados'][0].strValor);
                    Ext.getCmp('equipoConexionUpstream').setValue(json['encontrados'][1].strValor);
                    Ext.getCmp('sidquipoUpstream').setValue(json['encontrados'][2].strValor);
                    Ext.getCmp('puerto').setValue(json['encontrados'][3].strValor);
                    Ext.getCmp('tipoTransporte').setValue(json['encontrados'][4].strValor);
                    Ext.getCmp('provTransUpstream').setValue(json['encontrados'][5].strValor);
                    Ext.getCmp('idServicioRedTransp').setValue(json['encontrados'][6].strValor);
                    Ext.getCmp('idenRedtransporte').setValue(json['encontrados'][7].strValor);
                    Ext.getCmp('fecha').setValue(json['encontrados'][8].strValor);
                    Ext.getCmp('plazo').setValue(json['encontrados'][9].strValor);
                    Ext.getCmp('fVenceContrato').setValue(json['encontrados'][10].strValor);
                    Ext.getCmp('cpeSerialNumber').setValue(json['encontrados'][11].strValor) ;
                    Ext.getCmp('connectorType').setValue(json['encontrados'][12].strValor);
                    Ext.getCmp('esquema').setValue(json['encontrados'][13].strValor);
                    Ext.getCmp('iPWanGateway').setValue(json['encontrados'][14].strValor) ;
                    Ext.getCmp('ipWanCPE').setValue(json['encontrados'][15].strValor);
                    Ext.getCmp('ipLanCPE').setValue(json['encontrados'][16].strValor);
                    Ext.getCmp('iPlanCliente').setValue(json['encontrados'][17].strValor);
                    Ext.getCmp('vRF').setValue(json['encontrados'][18].strValor);
                    Ext.getCmp('aSN').setValue(json['encontrados'][19].strValor) ;
                    if (json['encontrados'][20].strValor == "SI")
                    {
                        Ext.getCmp('monitoreoExterno').setRawValue(true);
                    } else
                    {
                        Ext.getCmp('monitoreoExterno').setRawValue(false);
                    }
                } 
                });
    }

    storeConexion = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: verCodConexionUpStreamTng,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                accion: "cancelarCliente"
            }
        },
        fields:
                [
                    {name: 'intIdParametro', mapping: 'intIdParametro'},
                    {name: 'strNombreParametro', mapping: 'strNombreParametro'}
                ]
    });

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
                                    align: 'stretch',
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
                                    allowBlank: false,
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
                                    allowBlank: false,
                                    width: '25%',
                                    colspan :1,
                                    readOnly: true
                                    },
                                    { fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                    xtype: 'textfield',
                                    name: 'estado',
                                    fieldLabel: 'Estado',
                                    value: data.estado,
                                    width: '25%',
                                    colspan :1,
                                    readOnly: true
                                    },
                                    {
                                    xtype: 'textarea',
                                    name: 'direccion',
                                    id:'direccion',
                                    fieldLabel: 'Dirección',
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
                                        xtype: 'combobox',
                                        fieldLabel: '<i class="fa fa-asterisk" aria-hidden="true"></i>&nbsp;Cod. Conexión UpStream',
                                        id : 'codConexionUpStream',
                                        name: 'codConexionUpStream',
                                        store: storeConexion,
                                        emptyText:  strVariable,
                                        displayField: 'strNombreParametro',
                                        valueField: 'intIdParametro',
                                        editable: false,
                                        queryMode: 'remote',
                                        labelStyle: 'font-weight:bold',
                                        width: '25%',
                                        required : true,
                                     
                                            listeners: {
                                               change: function(combo)
                                               {

                                               Ext.Ajax.request({
                                               url: verDetConexionUpStreamTng,
                                               method: 'get',
                                               timeout: 10000,
                                               params: {
                                                   idElemento: combo.getValue()
                                               },
                                               success: function(response) {
                                                  var json = Ext.JSON.decode(response.responseText);
                                                   Ext.getCmp('equipoConexionUpstream').setValue(json['encontrados'][0].strValor1);
                                                   Ext.getCmp('tipoTransporte').setValue(json['encontrados'][1].strValor1);
                                                   Ext.getCmp('provTransUpstream').setValue(json['encontrados'][2].strValor1);
                                               } 
                                               });
                                               }                                                                                                  
                                           }
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'sidquipoUpstream',
                                     name: 'sidquipoUpstream', 
                                     fieldLabel: 'SID Equipo de Conexión Upstream',
                                     value: "",
                                     readOnly: false,
                                     width: '25%',
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'equipoConexionUpstream',
                                     name: 'equipoConexionUpstream',
                                     fieldLabel: 'Equipo Conexión BackBone',
                                     value: '',
                                     readOnly: true,
                                     width: '25%',
                                     required : true
                                   },
                                    {
                                     xtype: 'textfield',
                                     id : 'puerto',
                                     name: 'puerto',
                                     fieldLabel: 'Puerto',
                                     value: '',
                                     readOnly: false,
                                     width: '25%',
                                     required : true
                                    },{ fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                     xtype: 'textfield',
                                     fieldLabel: 'Tipo Transporte UpStream',
                                     id: 'tipoTransporte',
                                     name: 'tipoTransporte',
                                     value:'',
                                     width: '25%',
                                     readOnly: true,
                                     editable: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'provTransUpstream',
                                     name: 'provTransUpstream',
                                     fieldLabel: 'Proveedor Transporte Upstream',
                                     value: "",
                                     readOnly: true,
                                     width: '25%',
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'idServicioRedTransp',
                                     name: 'idServicioRedTransp',
                                     fieldLabel: 'Id de servicio en red Transporte',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'idenRedtransporte',
                                     name: 'idenRedtransporte',
                                     fieldLabel: 'Identificador en red transporte',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'datefield',
                                     id: 'fecha',
                                     name:'fecha',
                                     fieldLabel: '<i class="fa fa-asterisk" aria-hidden="true"></i>&nbsp;Fecha Instalación (RFS)',
                                     format: 'd-F-Y',
                                     value:'',
                                     editable: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'plazo',
                                     name: 'plazo',
                                     fieldLabel: '<i class="fa fa-asterisk" aria-hidden="true"></i>&nbsp;Plazo (meses)',
                                     value: "",
                                     readOnly: false,
                                     maskRe:/[0-9.]/,
                                     listeners: {
                                        blur: function (plazo)
                                        {
                                            var fecha1 = Ext.getCmp('fecha').getRawValue();
                                            var reg = new RegExp('^[0-9]+$');
                                            reg.test(Ext.getCmp('plazo').getRawValue());

                                            if (fecha1 != "")
                                            {
                                                if (reg.test(Ext.getCmp('plazo').getRawValue()))
                                                {
                                                    var meses = Ext.getCmp('plazo').getRawValue();
                                                    var d = 30;
                                                    d = d * meses;

                                                    var fecha = Ext.getCmp('fecha').getRawValue();
                                                    var obj = fecha.split('-');
                                                    var mes12 = "";
                                                    var di12 = "";
                                                    var anio12 = "";
                                                    var res = "";

                                                    if (obj[1] == "January") 
                                                    {
                                                        mes12 = "01";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "February") 
                                                    {
                                                        mes12 = "02";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "March") 
                                                    {
                                                        mes12 = "03";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "April") 
                                                    {
                                                        mes12 = "04";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "May") 
                                                    {
                                                        mes12 = "05";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "June") 
                                                    {
                                                        mes12 = "06";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "July") 
                                                    {
                                                        mes12 = "07";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "August") 
                                                    {
                                                        mes12 = "08";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "September") 
                                                    {
                                                        mes12 = "09";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "October") 
                                                    {
                                                        mes12 = "10";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "November") 
                                                    {
                                                        mes12 = "11";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    if (obj[1] == "December") 
                                                    {
                                                        mes12 = "12";
                                                        di12 = obj[0];
                                                        anio12 = obj[2];
                                                        res = mes12 + "/" + di12 + "/" + anio12;
                                                    }
                                                    var date8 = new Date(res);

                                                    newDate18 = Ext.Date.add(date8, Ext.Date.DAY, +d);
                                                    Ext.getCmp('fVenceContrato').setRawValue(Ext.Date.format(newDate18, 'd-F-Y'));

                                                } else {
                                                    Ext.Msg.alert('Mensaje', 'Solo se permite numeros');
                                                }

                                            } else {
                                                Ext.Msg.alert('Mensaje', 'Se necesita que selecione la fecha de Instalación');
                                            }
                                        }
                                    }
                                    },
                                    {
                                     xtype: 'datefield',
                                     id : 'fVenceContrato',
                                     name: 'fVenceContrato',
                                     fieldLabel: '<i class="fa fa-asterisk" aria-hidden="true"></i>&nbsp;Fecha vence Contrato',
                                     format: 'd-F-Y',
                                     value:"",
                                     editable: false,
                                     disabled :true,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'cpeSerialNumber',
                                     name: 'cpeSerialNumber',
                                     fieldLabel: 'CPE Serial Number',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },{ fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                     xtype: 'textfield',
                                     id : 'connectorType',
                                     name: 'connectorType',
                                     fieldLabel: 'Connector Type',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'esquema',
                                     name: 'esquema',
                                     fieldLabel: 'Esquema',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'iPWanGateway',
                                     name: 'iPWanGateway',
                                     fieldLabel: 'IP Wan Gateway',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    {
                                     xtype: 'textfield',
                                     id : 'ipWanCPE',
                                     name: 'ipWanCPE',
                                     fieldLabel: 'IP Wan CPE',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },{ fieldLabel: '',colspan :4, height: 8, border: false},
                                    {
                                     xtype: 'textfield',
                                     id : 'ipLanCPE',
                                     name: 'ipLanCPE',
                                     fieldLabel: 'IP Lan CPE',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    { xtype: 'textfield',
                                     id : 'iPlanCliente',
                                     name: 'iPlanCliente',
                                     fieldLabel: 'IP LAN Cliente',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    { xtype: 'textfield',
                                     id : 'vRF',
                                     name: 'vRF',
                                     fieldLabel: 'VRF',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },
                                    { xtype: 'textfield',
                                     id : 'aSN',
                                     name: 'aSN',
                                     fieldLabel: 'ASN',
                                     value: "",
                                     readOnly: false,
                                     required : true
                                    },{ fieldLabel: '',colspan :4, height: 10, border: false},
                                    {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Monitoreo Externo ?',
                                    name: 'monitoreoExterno',
                                    id: 'monitoreoExterno',
                                    checked: false,
                                    inputValue: 'NO'
                                    },{ fieldLabel: '',colspan :4, height: 10, border: false},
                                    {
                                    id : 'observacionActivarServicio',
                                    xtype: 'textarea',
                                    name: 'observacionActivarServicio',
                                    fieldLabel: '<i class="fa fa-asterisk" aria-hidden="true"></i>&nbsp;Observación',
                                    value: "",
                                    readOnly: false,
                                    colspan:2,
                                    width: 480,
                                    required: true
                                    },
                                    {
                                    id : 'observacionAnterior',
                                    xtype: 'textarea',
                                    name: 'observacionAnterior',
                                    fieldLabel: 'Observación Anterior',
                                    value: "",
                                    readOnly: true,
                                    colspan:2,
                                    width: 480,
                                    required: true
                                    }
                                ]
                            }
                        ]
                    }
                ],
                buttons: [{
                    text: '<i class="fa fa-check-square" aria-hidden="true"></i>&nbsp;Confirmar',
                    formBind: true,
                    handler: function(){
                        var observacion = Ext.getCmp('observacionActivarServicio').getValue();
                        var codConexionUpStream1 = Ext.getCmp('codConexionUpStream').getRawValue();
                        var equipoConexion = Ext.getCmp('equipoConexionUpstream').getValue(); 
                        var sid = Ext.getCmp('sidquipoUpstream').getValue();
                        var puerto1 = Ext.getCmp('puerto').getValue();
                        var tipoTrans = Ext.getCmp('tipoTransporte').getValue();
                        var provTransUpst = Ext.getCmp('provTransUpstream').getValue();
                        var idServRedTransp = Ext.getCmp('idServicioRedTransp').getValue();
                        var IdenRedtransp = Ext.getCmp('idenRedtransporte').getValue();
                        var fechas = Ext.getCmp('fecha').getRawValue();
                        var plazom = Ext.getCmp('plazo').getValue();
                        var fVenceCont = Ext.getCmp('fVenceContrato').getRawValue();
                        var cpeSerNum = Ext.getCmp('cpeSerialNumber').getValue();
                        var connectorTyp = Ext.getCmp('connectorType').getValue();
                        var esquema1 = Ext.getCmp('esquema').getValue();
                        var iPWanGat = Ext.getCmp('iPWanGateway').getValue();
                        var ipWan_CPE = Ext.getCmp('ipWanCPE').getValue();
                        var ipLan_CPE = Ext.getCmp('ipLanCPE').getValue();
                        var iPlanCli = Ext.getCmp('iPlanCliente').getValue();
                        var v_r_f = Ext.getCmp('vRF').getValue();
                        var a_s_n = Ext.getCmp('aSN').getValue();
                        var monitoreoExt = Ext.getCmp('monitoreoExterno').getValue() ;
                        
                        var validacion=true;
                        var strControl="";
                        if( observacion =="" || codConexionUpStream1 == "" || fechas== "" || plazom == "" || fVenceCont=="") {
                            validacion=false;
                        }
                        if(monitoreoExt)
                        {
                          monitoreoExt="SI";
                        }else
                        {
                         monitoreoExt="NO";
                        }
                        if(validacion )
                        {
                            var activar = false;
                            Ext.MessageBox.confirm('Confirmacion ',
                            '¿Esta seguro de confirmar el servicio? <br> La información ingresada no podrá ser editada.', function (btn) {
                                if (btn == 'yes') {
                                    strControl = "Activar";
                                    activar = true;
                                } 
                        if(validacion && activar){
                            Ext.get(confirmarFormPanel.getId()).mask('Procesando...');
                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio                 : data.idServicio,
                                    idProducto                 : data.productoId,
                                    observacionActivarServicio : observacion,
                                    idAccion                   : idAccion,
                                    strNombreTecnico           : data.descripcionProducto,
                                    
                                    strEstadoTng               :  data.estado,
                                    strActivarControl          :  strControl,
                                    codConexionUpStream        :  codConexionUpStream1,
                                    equipoConexionUpstream     :  equipoConexion,
                                    sidquipoUpstream           :  sid,
                                    puerto                     :  puerto1,
                                    tipoTransporte             :  tipoTrans,
                                    provTransUpstream          :  provTransUpst,
                                    idServicioRedTransp        :  idServRedTransp,
                                    idenRedtransporte          :  IdenRedtransp,
                                    fecha                      :  fechas,
                                    plazo                      :  plazom,
                                    fVenceContrato             :  fVenceCont,
                                    cpeSerialNumber            :  cpeSerNum,
                                    connectorType              :  connectorTyp,
                                    esquema                    :  esquema1,
                                    iPWanGateway               :  iPWanGat,
                                    ipWanCPE                   :  ipWan_CPE,
                                    ipLanCPE                   :  ipLan_CPE,
                                    iPlanCliente               :  iPlanCli,
                                    vRF                        :  v_r_f,
                                    aSN                        :  a_s_n,
                                    monitoreoExterno           :  monitoreoExt
                                },
                                success: function(response){
                                    Ext.get(confirmarFormPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                                        win.destroy();
                                        Ext.Msg.alert('Mensaje','Se guardo la información exitosamente. ', function(btn){
                                            if(btn=='ok'){
                                                store.load();                                    
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ','Error:' + response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(confirmarFormPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            }); 
                        } }); }
                        else{
                           Ext.Msg.alert("Failed","Favor Revise los campos con [*] son obligatorios ", function(btn){
                                    if(btn=='ok'){
                                    }
                            });
                        }
                  }
                },{
                    text: '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Guardar',
                    formBind: true,
                    handler: function(){
                        var observacion = Ext.getCmp('observacionActivarServicio').getValue();
                        var codConexionUpStream1 = Ext.getCmp('codConexionUpStream').getRawValue();
                        var equipoConexion = Ext.getCmp('equipoConexionUpstream').getValue(); 
                        var sid = Ext.getCmp('sidquipoUpstream').getValue();
                        var puerto1 = Ext.getCmp('puerto').getValue();
                        var tipoTrans = Ext.getCmp('tipoTransporte').getValue();
                        var provTransUpst = Ext.getCmp('provTransUpstream').getValue();
                        var idServRedTransp = Ext.getCmp('idServicioRedTransp').getValue();
                        var IdenRedtransp = Ext.getCmp('idenRedtransporte').getValue();
                        var fechas = Ext.getCmp('fecha').getRawValue();
                        var plazom = Ext.getCmp('plazo').getValue();
                        var fVenceCont = Ext.getCmp('fVenceContrato').getRawValue();
                        var cpeSerNum = Ext.getCmp('cpeSerialNumber').getValue();
                        var connectorTyp = Ext.getCmp('connectorType').getValue();
                        var esquema1 = Ext.getCmp('esquema').getValue();
                        var iPWanGat = Ext.getCmp('iPWanGateway').getValue();
                        var ipWan_CPE = Ext.getCmp('ipWanCPE').getValue();
                        var ipLan_CPE = Ext.getCmp('ipLanCPE').getValue();
                        var iPlanCli = Ext.getCmp('iPlanCliente').getValue();
                        var v_r_f = Ext.getCmp('vRF').getValue();
                        var a_s_n = Ext.getCmp('aSN').getValue();
                        var monitoreoExt = Ext.getCmp('monitoreoExterno').getValue() ;
                        
                        var validacion=true;
                        var strControl="";
                        if( observacion =="" || codConexionUpStream1 == "" || fechas== "" || plazom == "" || fVenceCont=="") {
                            validacion=false;
                        }
                        if(monitoreoExt)
                        {
                          monitoreoExt="SI";
                        }else
                        {
                         monitoreoExt="NO";
                        }
                        if(validacion )
                        {
                            var activar = false;
                            Ext.MessageBox.confirm('Confirmacion ', '¿Desea guardar el servicio en modo de Prueba?', function (btn) {
                                if (btn == 'yes') {
                                    strControl = "Backlog";
                                    activar = true;
                                }
                        if(validacion && activar){
                            Ext.get(confirmarFormPanel.getId()).mask('Procesando...');
                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio                 : data.idServicio,
                                    idProducto                 : data.productoId,
                                    observacionActivarServicio : observacion,
                                    idAccion                   : idAccion,
                                    strNombreTecnico           : data.descripcionProducto,
                                    
                                    strEstadoTng               :  data.estado,
                                    strActivarControl          :  strControl,
                                    codConexionUpStream        :  codConexionUpStream1,
                                    equipoConexionUpstream     :  equipoConexion,
                                    sidquipoUpstream           :  sid,
                                    puerto                     :  puerto1,
                                    tipoTransporte             :  tipoTrans,
                                    provTransUpstream          :  provTransUpst,
                                    idServicioRedTransp        :  idServRedTransp,
                                    idenRedtransporte          :  IdenRedtransp,
                                    fecha                      :  fechas,
                                    plazo                      :  plazom,
                                    fVenceContrato             :  fVenceCont,
                                    cpeSerialNumber            :  cpeSerNum,
                                    connectorType              :  connectorTyp,
                                    esquema                    :  esquema1,
                                    iPWanGateway               :  iPWanGat,
                                    ipWanCPE                   :  ipWan_CPE,
                                    ipLanCPE                   :  ipLan_CPE,
                                    iPlanCliente               :  iPlanCli,
                                    vRF                        :  v_r_f,
                                    aSN                        :  a_s_n,
                                    monitoreoExterno           :  monitoreoExt
                                },
                                success: function(response){
                                    Ext.get(confirmarFormPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                                        win.destroy();
                                        Ext.Msg.alert('Mensaje','Se guardo la información exitosamente. ', function(btn){
                                            if(btn=='ok'){
                                                store.load();                                    
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ','Error:' + response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(confirmarFormPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            }); 
                        } }); }
                        else{
                           Ext.Msg.alert("Failed","Favor Revise los campos con [*] son obligatorios ", function(btn){
                                    if(btn=='ok'){
                                    }
                            });
                        }
                  }
                },{
                    text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cancelar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });
            if(data.estado !="Backlog")
            {
                Ext.getCmp('observacionAnterior').setVisible(false);
            }
            else
            {
                Ext.getCmp('observacionAnterior').setVisible(true);
            }

            var win = Ext.create('Ext.window.Window', {
                title: 'Confirmar Servicio',
                modal: true,
                width: 1040,
                closable: true,
                layout: 'fit',
                items: [confirmarFormPanel]
            }).show();
}

function replaceAllIsb( text, busca, reemplaza )
{
    while (text.toString().indexOf(busca) !== -1)
    text = text.toString().replace(busca,reemplaza);
    return text;
}