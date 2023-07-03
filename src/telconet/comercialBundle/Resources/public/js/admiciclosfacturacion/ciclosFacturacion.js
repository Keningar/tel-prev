var itemsPerPage = 10;
Ext.onReady(function() {
    //array que contiene el titulo de las ventanas de mensajes
    var arrayTituloMensajeBox = [];
    arrayTituloMensajeBox['100'] = 'Información';
    arrayTituloMensajeBox['001'] = 'Error';
    arrayTituloMensajeBox['000'] = 'Alerta';
    
    formCiclosFacturacion = Ext.create('Ext.form.Panel', {
        height: 220 ,
        width: '100%',
        bodyPadding: 10,
        autoScroll: true,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 1,
            pack: 'center'
        },    
    items: [
        {
           xtype: 'textfield',
           name: 'strTxDescripcionCiclo',
           id: 'strTxDescripcionCiclo',
           fieldLabel: 'Descripci\u00F3n de Ciclo',
           displayField: '',          
           maxLength: 50,
           width: 300,  
           enforceMaxLength: true
        },                
        {
            xtype: 'numberfield',
            anchor: '100%',
            name: 'strTxCicloInicio',
            id: 'strTxCicloInicio',
            fieldLabel: 'Ciclo Inicio',
            value: 1,
            maxValue: 31,
            minValue: 1            
        },
        {
            xtype: 'numberfield',
            anchor: '100%',
            name: 'strTxCicloFin',
            id: 'strTxCicloFin',
            fieldLabel: 'Ciclo Fin',
            value: 31,
            maxValue: 31,
            minValue: 1
        },
        {
            fieldLabel: 'Ciclo Especial',
            type: 'combobox',
            id: 'strTxCicloEspecial',            
            width: 200,
            xtype: 'combo',
            hiddenName: 'rating',
            store: new Ext.data.SimpleStore(
                                            {
                                                data:
                                                    [
                                                        ['S', 'Sí'],
                                                        ['N', 'No']
                                                    ],
                                                fields: ['value', 'text']
                                            }),
            valueField: 'value',
            value: 'S',
            displayField: 'text',           
            triggerAction: 'all',
            editable: false,
            listeners:
                     {
                        change:
                               {
                                    fn: function(that, e, eOpts)
                                    {
                                        ciclo_especial =  this.value;
                                    }
                                }

                     }
        }
    ],
    buttons: [{
        text: 'Guardar',
        tooltip : 'Guardar', 
        handler: function() {             
              if((Ext.getCmp('strTxCicloInicio').getValue()<1 || Ext.getCmp('strTxCicloInicio').getValue()>31)
                  ||  (Ext.getCmp('strTxCicloFin').getValue()<1 ||  Ext.getCmp('strTxCicloFin').getValue()>31))
              {
                  var w = new Ext.Window({
                                height: 95, width: 525,
                                resizable: false,                                
                                title: 'Error',
                                modal: true,
                                html: "<img style=\"vertical-align:middle\" "+
                                      "src=\"/./public/images/stop.png\"> "+"<span>No es posible ingresar Ciclo. "+
                                      " Los rangos Permitidos son de [1-31] dias</span>"
                                });                               
                                w.show();                   
              }
              else
              {
              Ext.Ajax.request({
                       url : urlValidaCicloFacturacionAjax,
                       method : 'post',
                       params: {                                
                                 strTxCicloInicio: Ext.getCmp('strTxCicloInicio').getValue(),
                                 strTxCicloFin: Ext.getCmp('strTxCicloFin').getValue()                                
                               },
                       success : function(response)
                       {
                            var respuesta = false;
                            var json = Ext.JSON.decode(response.responseText);
                            
                            if(json.strPermiteIngreso == "SI")
                            {						      
                                respuesta = true;
                            }
                           
                            if(respuesta)
                            {
                                Ext.MessageBox.confirm(
                                    'Ingreso de Ciclo',
                                    '¿Est\u00e1 seguro de ingresar el nuevo Ciclo de Facturaci\u00F3n, \n\
                                      considerar que cualquier otro ciclo que se encuentre Activo ser\u00e1 Inactivado ?',
                                    function(btn) {
                                        if (btn === 'yes') {
                                            //Guardo Ciclo de Facturacion
                                            Ext.Ajax.request({
                                              url: urlGuardarCicloFacturacionAjax,
                                              method: 'POST',
                                              params: 
                                              {   
                                                  strTxDescripcionCiclo: Ext.getCmp('strTxDescripcionCiclo').getValue(),
                                                  strTxCicloInicio: Ext.getCmp('strTxCicloInicio').getValue(),
                                                  strTxCicloFin: Ext.getCmp('strTxCicloFin').getValue(),
                                                  strTxCicloEspecial: Ext.getCmp('strTxCicloEspecial').getValue()    
                                              },                                              
                                              success: function(response)
                                              {
                                                  var respuesta = response.responseText;

                                                  if (respuesta == "Error")
                                                  {
                                                      Ext.Msg.alert('Error ', 'Se presentaron problemas al guardar la información,' +
                                                      ' favor notificar a Sistemas');
                                                  }
                                                  else
                                                  {
                                                      Ext.Msg.alert('MENSAJE ', 'Se guardó la información correctamente.');
                                                      storeCiclosFacturacion.load({params: {start: 0, limit: 10}}); 
                                                      storeCiclosFacturacionHist.load({params: {start: 0, limit: 10}});    
                                                  }
                                              },    
                                              failure: function(result) {
                                                  Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                              }
                                            });         
                                            //
                                        }
                                    });
                            }
                            else
                            {
                                    if (json.boolExisteCicloFacturacion)
                                    {
                                        var w = new Ext.Window({
                                            height: 95, width: 525,
                                            resizable: false,
                                            title: 'Error',
                                            modal: true,
                                            html: "<img style=\"vertical-align:middle\" " +
                                                "src=\"/./public/images/stop.png\"> " + "<span>No es posible ingresar ciclo de facturaci\u00F3n, " +                                              
                                                "Ciclo seleccionado ya existe</span>"
                                        });

                                        w.show();
                                    }
                                    else
                                    {
                                        var w = new Ext.Window({
                                            height: 95, width: 525,
                                            resizable: false,
                                            title: 'Error',
                                            modal: true,
                                            html: "<img style=\"vertical-align:middle\" " +
                                                "src=\"/./public/images/stop.png\"> " + "<span>No es posible ingresar Ciclo " +
                                                "de [" + json.intNumDiasEntreCiclos + "] dias, </span>" +
                                                "Solo se permite ciclos [30 o 31] dias</span>"
                                        });

                                        w.show();
                                    }

                            }

                        },
                       failure: function(result) {
                           Ext.Msg.alert('Error ', 'Error: '+ result.statusText);
                       }
               });  
           }// fin else
        }
    }]
});

    //Define un modelo para el store storeCiclosFacturacion
    Ext.define('ListaCiclosFacturacionModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdCiclo', type: 'int'},            
            {name: 'strNombreCiclo', type: 'string'},
            {name: 'strCodigoCiclo', type: 'string'},
            {name: 'strCicloEspecial', type: 'string'},
            {name: 'strCicloInicio', type: 'string'},
            {name: 'strCicloFin', type: 'string'},
            {name: 'strObservacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},            
            {name: 'intClientes', type: 'int'},
            {name: 'strEstado', type: 'string'},
            {name: 'strLinkEliminarCiclo', type: 'string'},
            {name: 'strLinkActivarCiclo', type: 'string'}
        ]
    });

    // Crea un store para obtener los Ciclos de Facturacion
    storeCiclosFacturacion = Ext.create('Ext.data.Store', {
        pageSize: itemsPerPage,
        model: 'ListaCiclosFacturacionModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreCiclosFacturacion,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'arrayResultado',
                totalProperty: 'intTotal'
            },
            simpleSortMode: true
        }
    });
    
    storeCiclosFacturacion.load();

   //Define un modelo para el store storeCiclosFacturacionHist
    Ext.define('ListaCiclosFacturacionHistModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdCiclo', type: 'int'},            
            {name: 'strNombreCiclo', type: 'string'},
            {name: 'strCicloInicio', type: 'string'},
            {name: 'strCicloFin', type: 'string'},
            {name: 'strObservacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},            
            {name: 'strEstado', type: 'string'}
        ]
    });

    // Crea un store para obtener el Historico de los Ciclos de Facturacion
    storeCiclosFacturacionHist = Ext.create('Ext.data.Store', {
        pageSize: 6,
        model: 'ListaCiclosFacturacionHistModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreCiclosFacturacionHist,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'arrayResultadoHist',
                totalProperty: 'intTotalHist'
            }
        }
    });
    
    storeCiclosFacturacionHist.load();
    
    // Crea el grid para mostrar el detalle de los ciclos de Facturacion
    gridCiclosFacturacion = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Ciclos de Facturaci\u00F3n',
        store: storeCiclosFacturacion,
        id: 'gridCiclosFacturacion',
        cls: 'custom-grid',
        dockedItems: [toolbar],
        columns: [
            {header: "Id", dataIndex: 'intIdCiclo', hidden: true},
            {
                header: 'Nombre de Ciclo ',
                dataIndex: 'strNombreCiclo',
                width: 150
            },
            {
                header: 'Codigo Ciclo',
                dataIndex: 'strCodigoCiclo',
                width: 100
            },
            {
                header: 'Ciclo Especial',
                dataIndex: 'strCicloEspecial',
                width: 100
            },
            {
                header: 'Ciclo Inicio',
                dataIndex: 'strCicloInicio',
                width: 100
            },
            {
                header: 'Ciclo Fin',
                dataIndex: 'strCicloFin',
                width: 100
            },
            {
                header: 'Descripci\u00F3n',
                dataIndex: 'strObservacion',
                width: 200
            },
            {
                header: 'Fe. Creaci\u00F3n',
                dataIndex: 'strFeCreacion',
                width: 100
            },            
            {
                header: 'Usr. Creación',
                dataIndex: 'strUsrCreacion',
                width: 100
            },
            {
                header: '# Clientes',
                dataIndex: 'intClientes',
                width: 80
            },
            {
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 80
            },            
            {
                text: 'Acciones',
                width: 100,
                renderer: renderAcciones,
            }
        ],
        height: 300,
        width: '100%',
        renderTo: 'ListadoCiclosFacturacion',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeCiclosFacturacion,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    }); 
    
    function renderAcciones(value, p, record)
    {
        var iconos = '';
        iconos = iconos + '<b><a href="#" onClick="verDetalleClientes(\''+record.data.intIdCiclo+'\')" title="Ver Detalles Clientes" class="button-grid-show"></a></b>';
        if (record.data.strLinkActivarCiclo)
        {
          
            iconos = iconos + '<b><a href="#" onClick="activar(\'' + record.data.strLinkActivarCiclo + '\',\'' + record.data.intIdCiclo + '\',\'' + record.data.strNombreCiclo + 
                            '\')" \n\
                        title="Activar Ciclo de Facturación" \n\
                        class="button-grid-edit"></a></b>';                     
        }
        if (record.data.strLinkEliminarCiclo)
        {
          /*  iconos = iconos + '<b><a href="#" onClick="eliminar(\'' + record.data.strLinkEliminarCiclo + '\')" \n\
                          title="Eliminar Ciclo de Facturación" \n\
                          class="button-grid-delete"></a></b>';*/
            iconos = iconos + '<b><a href="#" onClick="eliminar(\'' + record.data.strLinkEliminarCiclo + '\',\'' + record.data.intIdCiclo + 
                            '\')" \n\
                        title="Eliminar Ciclo de Facturación" \n\
                        class="button-grid-delete"></a></b>';                      
        }
        return Ext.String.format(
            iconos,
            value,
            '1',
            'nada'
            );
    }             
    
    // Crea el grid para mostrar el historial los Ciclos de Facturacion
    gridCiclosFacturacionHist = Ext.create('Ext.grid.Panel', {
        title: 'Historial de Ciclos de Facturaci\u00F3n',
        store: storeCiclosFacturacionHist,
        id: 'gridCiclosFacturacionHist',
        cls: 'custom-grid',
        autoScroll: false,
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdCicloHistorial', hidden: true},
            {
                header: 'Nombre de Ciclo ',
                dataIndex: 'strNombreCiclo',
                width: 200
            },
            {
                header: 'Ciclo Inicio',
                dataIndex: 'strCicloInicio',
                width: 100
            },
            {
                header: 'Ciclo Fin',
                dataIndex: 'strCicloFin',
                width: 100
            },
            {
                header: 'Observaci\u00F3n',
                dataIndex: 'strObservacion',
                width: 400
            },
            {
                header: 'Fe. Creaci\u00F3n',
                dataIndex: 'strFeCreacion',
                width: 150
            },            
            {
                header: 'Usr. Creación',
                dataIndex: 'strUsrCreacion',
                width: 100
            },
            {
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 80
            }                         
        ],
        height: 300,
        width: '100%',
        renderTo: 'ListadoCiclosFacturacionHist',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeCiclosFacturacionHist,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    }); 
        
   // Panel que contiene el formulario para el ingreso de los Ciclos de Facturacion
   panelCiclosFacturacion = new Ext.Panel({
       width: '100%',
       height: '100%',
       items: [
           formCiclosFacturacion
       ],
       renderTo: 'IngresoCiclosFacturacion',
   });   
    
});

function eliminar(strDireccion,intIdCiclo)
{
    //Valido si el ciclo de Facturacion puede ser Eliminado.
    Ext.Ajax.request({
        url: urlValidaCiclosAEliminarAjax,
        method: 'post',
         params: {                                
                    intIdCiclo: intIdCiclo                   
                 },
        success: function(response)
        {
            var respuesta = false;
            var json = Ext.JSON.decode(response.responseText);

            if (json.strPermiteEliminar == "SI")
            {
                respuesta = true;
            }

            if (respuesta)
            {
                Ext.MessageBox.confirm(
                    'Eliminaci\u00F3n de Ciclo',
                    '¿Est\u00e1 seguro de Eliminar el Ciclo de Facturaci\u00F3n ?',
                    function(btn) {                        
                        if (btn === 'yes') {
                            //Elimino Ciclo de Facturacion
                            Ext.Ajax.request({
                                url: strDireccion,
                                method: 'POST',
                                success: function(response)
                                {
                                    var respuesta = response.responseText;

                                    if (respuesta == "Error")
                                    {
                                        Ext.Msg.alert('Error ', 'Se presentaron problemas al eliminar el ciclo de Facturación,' +
                                            ' favor notificar a Sistemas');
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('MENSAJE ', 'Se Eliminó el Ciclo de Facturación');
                                        storeCiclosFacturacion.load({params: {start: 0, limit: 10}});
                                        storeCiclosFacturacionHist.load({params: {start: 0, limit: 10}});
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }                       
                    });
            }
            else
            {
                var w = new Ext.Window({
                    height: 95, width: 525,
                    resizable: false,
                    title: 'Error',
                    modal: true,
                    html: "<img style=\"vertical-align:middle\" " +
                        "src=\"/./public/images/stop.png\"> " + "<span>No es posible eliminar el Ciclo Facturación, " +
                        "Existe ["+json.intCantidadClientesEnCiclo+"] clientes asignados al ciclo</span>"
                });

                w.show();

            }
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}
/**
     * funcion para activar el ciclo de facturacion.
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 17-03-2022
     * @since 1.0
     */
function activar(strDireccion,intIdCiclo,strNombreCiclo)
{//se obtiene los mensajes parametrizados en base, para el proceso de activacion de ciclo de facturacion.
    Ext.Ajax.request({
        url: urlMensajesValidacionAjax,
        method: 'post',
         params: {                                
                    intIdCiclo: intIdCiclo                   
                 },
        success: function(response)
        {
            var json = Ext.JSON.decode(response.responseText);
            Ext.MessageBox.confirm(
                'Activaci\u00F3n de Ciclo',
                json.strMensajeConfirmarActivacion.replace('%nombreCiclo%',strNombreCiclo),
                function(btn) {                        
                    if (btn === 'yes') {
                        //Elimino Ciclo de Facturacion
                        Ext.Ajax.request({
                            url: strDireccion,
                            method: 'POST',
                            success: function(response)
                            {
                                var respuesta = response.responseText;

                                if (respuesta == "Error")
                                {
                                    Ext.Msg.alert('Error ', json.strMensajeErrorActivacion.replace('%nombreCiclo%',strNombreCiclo)+
                                        ' favor notificar a Sistemas');
                                }
                                else
                                {
                                    Ext.Msg.alert('MENSAJE ', json.strMensajeConfirmacionActivacion.replace('%nombreCiclo%',strNombreCiclo));
                                    storeCiclosFacturacion.load({params: {start: 0, limit: 10}});
                                    storeCiclosFacturacionHist.load({params: {start: 0, limit: 10}});
                                }
                            },
                            failure: function(result) {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }                       
                });
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}

function verDetalleClientes(intIdCiclo)
{
    var storeDetalleClientes = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreCiclosClientes,
            reader: {
                type: 'json',
                totalProperty: 'intTotales',
                root: 'arrayEncontrados'
            },
            extraParams: {
                intIdCiclo: intIdCiclo
            }
        },
        fields:
            [
                {name: 'strEstados', mapping: 'strEstados'},
                {name: 'intPreClientes', mapping: 'intPreClientes'},
                {name: 'intClientes', mapping: 'intClientes'},
                {name: 'intTotal', mapping: 'intTotal'}
            ]
    });

    //Grid Detalle Clientes
    gridDetalleClientes = Ext.create('Ext.grid.Panel',
        {
            id: 'gridDetalleClientes',
            store: storeDetalleClientes,
            columnLines: true,
            listeners:
                {
                    viewready: function(grid)
                    {
                        var view = grid.view;

                        grid.mon(view,
                            {
                                uievent: function(type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
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

                                                if (header.dataIndex != null)
                                                {
                                                    var trigger = tip.triggerElement,
                                                        parent = tip.triggerElement.parentElement,
                                                        columnTitle = view.getHeaderByCell(trigger).text,
                                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                                    {
                                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

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
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function()
                            {
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    }
                },
            columns:
                [
                    {
                        header: 'Estados',
                        dataIndex: 'strEstados',
                        width: 100,
                        sortable: true
                    }, {
                        header: 'PreClientes',
                        dataIndex: 'intPreClientes',
                        width: 100
                    },
                    {
                        header: 'Clientes',
                        dataIndex: 'intClientes',
                        width: 100
                    },
                    {
                        header: 'Total',
                        dataIndex: 'intTotal',
                        width: 100
                    }
                ],
            viewConfig:
                {
                    stripeRows: true,
                    enableTextSelection: true
                },
            frame: true,
            height: 300
        });

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
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 1100
                },
                items: [
                    gridDetalleClientes
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Ver Detalle Clientes',
        modal: true,
        width: 1150,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

