/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.o
 */
function realizarCambioMac(data)
{
    Ext.get(gridServicios.getId()).mask('Consultando Información de Mac...');
    Ext.Ajax.request({
        url: getInformacionMacServicio,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response) {
            Ext.get(gridServicios.getId()).unmask();

            var datosInterface = Ext.JSON.decode(response.responseText);

            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side',
                    width: 320
                },
                items: [{
                        items: [
                            //informacion de backbone
                            {
                                xtype: 'fieldset',
                                title: 'Información de Mac del Cliente',
                                defaultType: 'textfield',
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 5,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                name: 'elemento',
                                                fieldLabel: '<b>Cpe Cliente</b>',
                                                displayField: datosInterface.elemento,
                                                value: datosInterface.elemento,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            //---------------------------------------------
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'interface',
                                                fieldLabel: '<b>Interface</b>',
                                                displayField: datosInterface.interface,
                                                value: datosInterface.interface,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'tipoEnlace',
                                                fieldLabel: '<b>Tipo Enlace</b>',
                                                displayField: datosInterface.tipoEnlace,
                                                value: datosInterface.tipoEnlace,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'mac',
                                                fieldLabel: '<b>MAC Anterior</b>',
                                                displayField: datosInterface.mac,
                                                value: datosInterface.mac,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            //---------------------------------------------
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'macNuevo',
                                                name: 'macNuevo',
                                                fieldLabel: '<b>MAC Nueva</b>',
                                                displayField: '',
                                                value: '',                                               
                                                width: 300
                                            }
                                        ]
                                    }

                                ]
                            }
                        ]
                    }],
                buttons: [{
                        text: 'Ejecutar',
                        formBind: true,
                        handler: function() {
                            
                            //Validar la mac
                            var mac = Ext.getCmp('macNuevo').getValue();

                            if(!(mac === 'NO EXISTE ELEMENTO'))
                            {
                                if(!mac.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                {
                                    Ext.Msg.alert('Mensaje ', 'Formato de Mac Incorrecto (xxxx.xxxx.xxxx), Favor Revisar');
                                    return;
                                }                               
                            }             
                            
                            Ext.MessageBox.wait('Actualizando, Por favor espere..');
                            Ext.Ajax.request({
                                url: urlActualizarMacServicio,
                                method: 'post',
                                timeout: 300000,
                                params: {
                                    idServicio : data.idServicio,
                                    idInterface: datosInterface.idInterface,
                                    macAnterior: datosInterface.mac,
                                    macNueva   : mac
                                },
                                success: function(response) {
                                    Ext.MessageBox.hide();
                                    win.hide();

                                    var respuesta = Ext.JSON.decode(response.responseText);

                                    Ext.Msg.alert('Mensaje', respuesta.mensaje, function(btn) {
                                        if (btn === 'ok') {
                                            store.load();
                                            win.destroy();
                                        }
                                    });

                                },
                                failure: function(result)
                                {
                                    Ext.MessageBox.hide();
                                    win.hide();
                                    Ext.Msg.alert('Error', result.statusText, function(btn) {
                                        if (btn == 'ok') {
                                            win.destroy();
                                        }
                                    });
                                }
                            });                            
                        }
                    }, {
                        text: 'Cerrar',
                        handler: function() {
                            win.destroy();
                        }
                    }]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Actualizar Mac del Servicio',
                modal: true,
                width: 350,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

        }//cierre Success
        ,
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error', result.statusText, function(btn) {
                if (btn == 'ok') {

                }
            });
        }
    }); 
}

/**
     * Función que se encarga de crear la tabla de Consultas de Enlaces del Servicio"
     * 
     * @author  Rafael Vera <rsvera@telconet.ec>
     * @version 2.0 15-06-2023 - Se agrego un select para realizar un filtrado en la tabla de Enlaces del Servicio
     *  
     * @param  integer data
     */
function consultarEnlacesServicio(data)
{    
    var storeEnlacesServicio = new Ext.data.Store({  
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getEnlacesServicio,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio,
                estado: 'Activo'
            }
        },
        fields:
            [
              {name:'idElementoInicial',      mapping:'idElementoInicial'},
              {name:'nombreElementoInicial',  mapping:'nombreElementoInicial'},
              {name:'nombreInterfaceInicial', mapping:'nombreInterfaceInicial'},
              {name:'estadoInterfaceInicial', mapping:'estadoInterfaceInicial'},
              {name:'idElementoFinal',        mapping:'idElementoFinal'},
              {name:'nombreElementoFinal',    mapping:'nombreElementoFinal'},
              {name:'nombreInterfaceFinal',   mapping:'nombreInterfaceFinal'},
              {name:'estadoInterfaceFinal',   mapping:'estadoInterfaceFinal'},
              {name:'idInterfaceInicial',     mapping:'idInterfaceInicial'},
              {name:'idInterfaceFinal',       mapping:'idInterfaceFinal'},
              {name:'estadoEnlace',           mapping:'estadoEnlace'},
              {name:'tipoElemento',           mapping:'tipoElemento'},
              {name:'usrCreacionEnlace',      mapping:'usrCreacionEnlace'},
              {name:'mac',                    mapping:'mac'}
            ]
    });    
    //select filtro 
    var storeFiltroEnlaces = Ext.create('Ext.data.Store', {
        fields: ['value', 'text'],
        data: [
          { value: 'Todos', text: 'Todos' },
          { value: 'Activo', text: 'Activo' },
          { value: 'Eliminado', text: 'Eliminado'}
        ]
      });
      var selectFiltro = Ext.create('Ext.form.ComboBox', {
        fieldLabel: 'Estado Enlace',
        store: storeFiltroEnlaces,
        valueField: 'value',
        displayField: 'text',
        queryMode: 'local',
        editable: false,
        id: 'selectFiltro',
        value: 'Activo',
        listeners: {
          select: function(datos, record) {
            var selectedValue = datos.getValue();
            Ext.create('Ext.data.Store', {
              autoLoad: true,
              proxy: {
                type: 'ajax',
                url: getEnlacesServicio,
                reader: {
                  type: 'json',
                  totalProperty: 'total',
                  root: 'encontrados'
                },
                extraParams: {
                  idServicio: data.idServicio,
                  estado: selectedValue
                }
              },
              fields: [
                { name: 'idElementoInicial', mapping: 'idElementoInicial' },
                { name: 'nombreElementoInicial', mapping: 'nombreElementoInicial' },
                { name: 'nombreInterfaceInicial', mapping: 'nombreInterfaceInicial' },
                { name: 'estadoInterfaceInicial', mapping: 'estadoInterfaceInicial' },
                { name: 'idElementoFinal', mapping: 'idElementoFinal' },
                { name: 'nombreElementoFinal', mapping: 'nombreElementoFinal' },
                { name: 'nombreInterfaceFinal', mapping: 'nombreInterfaceFinal' },
                { name: 'estadoInterfaceFinal', mapping: 'estadoInterfaceFinal' },
                { name: 'idInterfaceInicial', mapping: 'idInterfaceInicial' },
                { name: 'idInterfaceFinal', mapping: 'idInterfaceFinal' },
                { name: 'estadoEnlace', mapping: 'estadoEnlace' },
                { name: 'tipoElemento', mapping: 'tipoElemento' },
                { name: 'usrCreacionEnlace', mapping: 'usrCreacionEnlace' },
                { name: 'mac', mapping: 'mac' }
              ],
              listeners: {
                load: function(store, records) {
                  var grid = Ext.getCmp('gridEnlacesServicio');
                  var nuevosDatos = [];
                  store.each(function(record) {
                    nuevosDatos.push(record.getData());
                  });
                  grid.getStore().removeAll();
                  grid.getStore().loadData(nuevosDatos);
                }
              }
            });
          }
        }
      });
      
      
    //grid de usuarios
    gridEnlacesServicio = Ext.create('Ext.grid.Panel', {
        id:'gridEnlacesServicio',
        store: storeEnlacesServicio,
        columnLines: true,
        selectOnFocus:true,
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
                                            if(columnText.indexOf("Eliminado") != -1)
                                            {
                                                if(columnDataIndex === 'estadoEnlace' )
                                                {
                                                    tip.update(columnText + view.getRecord(parent).get('usrCreacionEnlace').toString());
                                                }
                                            }
                                            else
                                            {
                                                if(columnDataIndex === 'nombreElementoFinal')
                                                {
                                                    tip.update(columnText + view.getRecord(parent).get('mac').toString());
                                                }
                                                else
                                                {
                                                    tip.update(columnText);
                                                }
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
        columns: 
        [
            {                
                header: 'Elemento Inicio',
                dataIndex: 'nombreElementoInicial',
                width: 370              
            },
            {
                header: 'Puerto Inicial',
                dataIndex: 'nombreInterfaceInicial',
                width: 80
            },
            {
                header: 'Estado Puerto',
                dataIndex: 'estadoInterfaceInicial',
                width: 80
            },
            {
                header: 'Elemento Fin',
                dataIndex: 'nombreElementoFinal',
                width: 370
            },
            {
                header: 'Puerto Final',
                dataIndex: 'nombreInterfaceFinal',
                width: 80
            },
            {
                header: 'Estado Puerto',
                dataIndex: 'estadoInterfaceFinal',
                width: 80
            },
            {
                header: 'Estado Enlace',
                dataIndex: 'estadoEnlace',
                width: 80
            },
            {
                header: 'Tipo',
                dataIndex: 'tipoElemento',
                width: 170
            }
        ],
        viewConfig:
        {
            stripeRows:true,
            enableTextSelection: true
        },
        frame: true,
        height: 380        
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
                width: 1350
            },
            items: [
                selectFiltro,
                gridEnlacesServicio

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Enlaces del Servicio',
        modal: true,
        width: 1400,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();

    
}

