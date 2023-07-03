Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

var storeFormaPago   = null;
var connEsperaAccion = new Ext.data.Connection
    ({
        listeners:
        {
            'beforerequest':
            {
                fn: function()
                {
                    Ext.MessageBox.show
                    ({
                        msg: 'Actualizando forma de pago para facturación',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function()
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception':
            {
                fn: function()
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
        
Ext.onReady(function()
{
    new Ext.Panel
    ({
        id: 'paneltoolBarVip',
        renderTo: 'toolBarVip',
        baseCls:  'x-plain',
        dockedItems:
        [
            {
                xtype:   'toolbar',
                dock:    'top',
                baseCls: 'x-plain',
                items:
                [
                    {
                        xtype: 'button',
                        id: 'btnHistoralPunto',
                        cls: 'icon_cliente_log',
                        tooltip: '<b>Historial Punto',
                        handler: function()
                        {
                            verHistorialPunto();
                        }
                    }
                ]
            }
        ]
    });
    
    Ext.define('modelFormaPago', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'codigo_forma_pago',      type: 'string'},
            {name: 'descripcion_forma_pago', type: 'string'}
        ]
    });

    //Store Formas de Pago
    storeFormaPago = Ext.create('Ext.data.Store',
    {
        autoLoad: true,
        model: "modelFormaPago",
        proxy: 
        {
            type: 'ajax',
            url: strUrlGridFormaPago,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'encontrados'
            },
            extraParams:
            {
                estado: 'Activo'
            }
        }
    });
});

function verHistorialPunto()
{
    var dataStoreHistorialPunto = new Ext.data.Store
    ({
        autoLoad: true,
        total: 'total',
        proxy:
        {
            type: 'ajax',
            timeout: 600000,
            url: strUrlHistorialPunto,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
        [
            {name: 'detalle', mapping: 'detalle', type: 'string'},
            {name: 'accion', mapping: 'accion', type: 'string'},
            {name: 'usuario', mapping: 'usuario', type: 'string'},
            {name: 'fecha', mapping: 'fecha', type: 'string'}
        ]
    });

    var gridHistorialPunto = Ext.create('Ext.grid.Panel',
    {
        id: 'gridHistorialPunto',
        store: dataStoreHistorialPunto,
        width: 790,
        height: 300,
        collapsible: false,
        multiSelect: true,
        viewConfig: 
        {
            emptyText: '<br><center><b>No hay datos para mostrar',
            forceFit: true,
            stripeRows: true,
            enableTextSelection: true
        },
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        layout: 'fit',
        region: 'center',
        buttons:
        [
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ],
        columns:
        [
            {
                dataIndex: 'detalle',
                header: 'Observaci\xf3n',
                width: 500
            },    
            {
                dataIndex: 'accion',
                header: 'Acci\xf3n',
                width: 137
            },
            {
                dataIndex: 'usuario',
                header: 'Usuario',
                width: 100
            },
            {
                dataIndex: 'fecha',
                header: 'Fecha',
                width: 150
            }
        ]
    });

    Ext.create('Ext.form.Panel',
    {
        id: 'formHistorialPunto',
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults:
        {
            labelAlign: 'left',
            labelWidth: 125,
            msgTarget: 'side'
        },
        items:
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:{ width: 700 },
                layout:
                {
                    type: 'table',
                    columns: 4,
                    align: 'left'
                },
                items:[ gridHistorialPunto ]
            }
        ]
    });

    var win = Ext.create('Ext.window.Window',
    {
        title: 'Historial Punto',
        modal: true,
        width: 800,
        closable: true,
        layout: 'fit',
        items: [gridHistorialPunto]
    }).show();
}

function editarFormaPagoFacturacion()
{
    var formEditarFacturacion = Ext.create('Ext.form.Panel',
        {
            id: 'formEditar',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
            {
                labelAlign: 'left',
                labelWidth: 125,
                msgTarget: 'side'
            },
            buttons:
            [
                {
                    text: 'Guardar',
                    handler: function()
                    {
                        var strCodigoFormaPago = Ext.getCmp('cmbFormaPago').getValue();

                        if( !Ext.isEmpty(strCodigoFormaPago) )
                        {
                            connEsperaAccion.request
                            ({
                                url: strUrlActualizarFormaPagoFacturacion,
                                method: 'POST',
                                timeout: 9000000,
                                dataType: 'json',
                                params:
                                { 
                                    strCodigoFormaPago: strCodigoFormaPago,
                                    intIdPunto: intIdPunto
                                },
                                success: function(response)
                                {
                                    var obj = Ext.JSON.decode(response.responseText);

                                    if ( 'OK' === obj.strEstado )
                                    {
                                        $("#formaPagoPunto").html(obj.strDescripcionFormaPago);

                                        Ext.Msg.show
                                        ({
                                            title: 'Información',
                                            msg: 'Se actualizó la forma de pago del punto de manera exitosa',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.INFO
                                        });
                                        
                                        win3.destroy();
                                    }
                                    else
                                    {
                                        Ext.Msg.show(
                                        {
                                            title: 'Error',
                                            msg: obj.strError,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.MessageBox.hide();
                                    Ext.Msg.alert('Error', result.responseText);
                                }
                            });
                        }//( !Ext.isEmpty(strCodigoFormaPago) )
                        else
                        {
                            Ext.Msg.show(
                            {
                                title: 'Atención',
                                msg: 'Debe seleccionar una forma de pago',
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        win3.destroy();
                    }
                }
            ],
            items:
            [
                {
                    xtype: 'fieldset',
                    autoHeight: 400,
                    labelWidth: 70,
                    width: 395,
                    items:
                    [
                        {
                            type: 'combobox',
                            fieldLabel: 'Formas de Pago',
                            id: 'cmbFormaPago',
                            labelStyle: 'font-weight:bolder;',
                            xtype: 'combo',
                            hiddenName: 'rating',
                            store: storeFormaPago,
                            valueField: 'codigo_forma_pago',
                            displayField: 'descripcion_forma_pago',
                            triggerAction: 'all',
                            editable: false,
                            width: 300
                        }
                    ]
                }
            ]
        });

    var win3 = Ext.create('Ext.window.Window',
        {
            title: 'Actualizar Forma de Pago Facturación',
            modal: true,
            width: 400,
            closable: true,
            layout: 'fit',
            items: [formEditarFacturacion]
        }).show();
}

function actualizarCoordenadaPunto(idPunto, latitud, longitud)
{
    winMostrarMapa="";

    if(latitud!=0 && longitud!=0)
    {
        Ext.MessageBox.wait('Consultando datos. Favor espere..');
        Ext.Ajax.request({
            url: urlObtenerCoordenadaPunto,
            method: 'post',
            timeout: 400000,
            params:
                {
                    idPunto: idPunto
                },
            success: function(response)
            {
                Ext.MessageBox.hide();
                var datos = Ext.JSON.decode(response.responseText);
                if(datos.boolTieneTarea)
                {
                    formPanelMapa = Ext.create('Ext.form.Panel', {
                    BodyPadding: 10,
                    frame: true,
                    items: [
                    {
                        html: "<div id='mapa' style='width:630px; height:340px'></div>"
                    },
                    {
                        layout: {
                            type: 'table',
                            tdAttrs: {style: 'padding: 5px;'},
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            //---------------------------------------------
                            { width: '10%', border: false},
                            {
                                xtype: 'label',
                                cls: 'label-coordenada',
                                text: 'Coordenada Actual',
                                width: 280
                            },
                            { width: '15%', border: false},
                            { width: '15%', border: false},
                            { width: 10, border: false},
                            //---------------------------------------------
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'latitudActual',
                                name: 'latitudActual',
                                fieldLabel: 'Latitud',
                                readOnly: true,
                                value: latitud,
                                width: 280
                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'longitudActual',
                                name: 'longitudActual',
                                fieldLabel: 'Longitud',
                                readOnly: true,
                                value: longitud,
                                width: 280
                            },
                            { width: 10, border: false},
                            //---------------------------------------------
                            { width: '10%', border: false},
                            {
                                xtype: 'label',
                                cls: 'label-coordenada',
                                fieldStyle: "font-weight: bold",
                                text: 'Coordenada Sugerida',
                                width: 280
                            },
                            { width: '15%', border: false},
                            { width: '15%', border: false},
                            { width: 10, border: false},
                            //---------------------------------------------
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                hideTrigger: true,
                                id: 'latitudSugerida',
                                name: 'latitudSugerida',
                                fieldLabel: 'Latitud',
                                value: datos.strLatitud,
                                width: 280
                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                hideTrigger: true,
                                id: 'longitudSugerida',
                                name: 'longitudSugerida',
                                fieldLabel: 'Longitud',
                                value: datos.strLongitud,
                                width: 280
                            },
                            { width: 10, border: false}
                            //---------------------------------------------
                        ],
                        buttons: [
                            {
                                text: 'Guardar',
                                handler: function()
                                {
                                    var form = formPanelMapa.getForm();
                                    if (form.isValid())
                                    {
                                        var strLatitud  = Ext.getCmp('latitudSugerida').value;
                                        var strLongitud = Ext.getCmp('longitudSugerida').value;
                                        Ext.MessageBox.wait('Guardando datos...');
                                        Ext.Ajax.request({
                                            timeout: 900000,
                                            url: urlGuardarCoordenadasSugeridas,
                                            method: 'post',
                                            params:
                                            {
                                                idPunto       : idPunto,
                                                strLatitud    : strLatitud,
                                                strLongitud   : strLongitud
                                            },
                                            success: function(response) {
                                                Ext.MessageBox.hide();
                                                winMostrarMapa.destroy();
                                                var datosActualizar = Ext.JSON.decode(response.responseText);
                                                Ext.Msg.show({
                                                    title: 'Actualización de coordenadas',
                                                    msg: datosActualizar.strMensaje,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.INFORMATION,
                                                    fn: function(btn) {
                                                        if (btn == 'ok') {
                                                            location.reload();
                                                        }
                                                    }});
                                            },
                                            failure: function(response)
                                            {
                                                Ext.MessageBox.hide();
                                                winMostrarMapa.destroy();
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: response.responseText,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        });
                                    }
                                }
                            },
                            {
                                text: 'Cerrar',
                                handler: function(){
                                    winMostrarMapa.destroy();
                                }
                            }
                        ]
                    }
                ]
                });

                winMostrarMapa = Ext.widget('window', {
                    title: 'Actualizar coordenada del punto',
                    layout: 'fit',
                    resizable: false,
                    modal: true,
                    closable: true,
                    items: [formPanelMapa]
                });

                winMostrarMapa.show();
                mostrarMapa(latitud, longitud);
                } else {
                    Ext.Msg.alert('Actualización de coordenadas', 'No existe tarea para actualizar la coordenada del punto');
                }
            },
            failure:function()
            {
                Ext.MessageBox.hide();
            }
        });
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function mostrarMapa(vlat,vlong){
    var mapa;
    var ciudad = "";
    var markerPto ;
    var markerPto2 ;

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
            mapa = new google.maps.Map(document.getElementById("mapa"), myOptions);
        }

        if(ciudad=="gye")
            layerCiudad = 'http://157.100.3.122/Coberturas.kml';
        else
            layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng,
            map: mapa
        });
        mapa.setZoom(17);

        google.maps.event.addListener(mapa, 'dblclick', function(event) {

		  if(markerPto2)
		      markerPto2.setMap(null);

		     markerPto2 = new google.maps.Marker({
			position: event.latLng,
			map: mapa,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
		      });
		   mapa.setZoom(17);

           Ext.getCmp('latitudSugerida').setValue(event.latLng.lat());
           Ext.getCmp('longitudSugerida').setValue(event.latLng.lng());
		});
    }
}

function cierraVentanaMapa(){
    winMostrarMapa.close();
    winMostrarMapa.destroy();
}

  
function validateServiceCrsPorPunto(intIdPersonaRol,intIdPunto, href)
{
    Ext.MessageBox.wait('Verificando datos. Favor espere..');        
    Ext.Ajax.request({
        url: url_ValidateServiceCrsPorPunto,
        timeout: 1000000,
        method: 'POST',
        params: 
        {
            intIdPersonaRol,
            intIdPunto
        },
        success: function(response) 
        { 
            Ext.MessageBox.hide();
            var json = Ext.JSON.decode(response.responseText); 
            if (json.status == 'OK')
            {
                window.location.href=href; 
            } 
            else
            {                          

                Ext.Msg.show({
                    title: 'Alerta',
                    msg: json.message,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.WARNING
                    });
            }
            
        },
        failure: function(result) 
        {
            Ext.MessageBox.hide();  
            Ext.Msg.alert('Error', 'Error: ' + result.statusText);
        }
    });
}

function comprobarDeudasCliente(strUrl)
{
    if (!!permisoValidarDeudas)
    {
        Ext.get(document.body).mask('Comprodando deudas del cliente...');
        Ext.Ajax.request({
            url: url_compobar_deudas_cliente,
            method: 'post',
            timeout: 600000,
            success: function (response) {
                Ext.get(document.body).unmask();
                let jsonResponse = Ext.JSON.decode(response.responseText);
                if (jsonResponse.status == "OK") {
                    window.location = strUrl;
                    Ext.get(document.body).mask('Redirigiendo...');
                } else {
                    Ext.Msg.show({
                        title: 'Alerta!',
                        msg: jsonResponse.mensaje,
                        closable: false,
                        buttons: Ext.Msg.OK,
                        defaultTextHeight: 100,
                        multiline: false
                    });

                }

            },
            failure: function (result) {
                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                Ext.get(document.body).unmask();
            }
        });
    }
    else {
        window.location = strUrl;
    }
}