Ext.onReady(function() {
    //array que contiene el titulo de las ventanas de mensajes
    var arrayTituloMensajeBox = [];
    arrayTituloMensajeBox['100'] = 'Información';
    arrayTituloMensajeBox['001'] = 'Error';
    arrayTituloMensajeBox['000'] = 'Alerta';
    
    //Define un modelo para el store storeParametrosCab
    Ext.define('ListaParametrosCabModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParametro', type: 'int'},
            {name: 'strNombreParametro', type: 'string'},
            {name: 'strDescripcion', type: 'string'},
            {name: 'strModulo', type: 'string'},
            {name: 'strProceso', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });    
    
    storeParametrosCab = Ext.create('Ext.data.Store', {
        pageSize: 20,
        model: 'ListaParametrosCabModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlGetstoreListaParametros,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroCabResult',
                totalProperty: 'intTotalParametros'
            },
            simpleSortMode: true
        },
        listeners: {
                load: function() {
                    var recStore = storeParametrosCab.getAt(0);
                    var recStoreDet = storeParametrosDet.getAt(0);
                    Ext.getCmp('strNombreParametro').setValue(recStore.get('strNombreParametro'));
                    Ext.getCmp('strModulo').setValue(recStore.get('strModulo'));
                    Ext.getCmp('strProceso').setValue(recStore.get('strProceso'));
                    Ext.getCmp('strEstado').setValue(recStore.get('strEstado'));
                    Ext.getCmp('strUsrCreacion').setValue(recStore.get('strUsrCreacion'));
                    Ext.getCmp('strFeCreacion').setValue(recStore.get('strFeCreacion'));
                    Ext.getCmp('strDescripcion').setValue(recStore.get('strDescripcion'));
                    Ext.getCmp('strValor3').setValue(recStoreDet.get('strValor3'));
                }
            }        
    });    
    
    

    //Formulario estático que solo muestra la información de la cabecera de parametros.
    formGetParametrosCab = Ext.create('Ext.form.Panel', {
        height: 270,
        width: '100%',
        bodyPadding: 10,
        autoScroll: true,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 3,
            pack: 'center'
        },
        items: [
            {
                id: 'strNombreParametro',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Nombre Parametro',
                labelWidth: 200,
                name: 'strNombreParametro',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strModulo',
                xtype: 'displayfield',
                fieldLabel: 'Módulo',
                labelWidth: 200,
                name: 'strModulo',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strProceso',
                xtype: 'displayfield',
                fieldLabel: 'Proceso',
                labelWidth: 70,
                name: 'strProceso',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strEstado',
                xtype: 'displayfield',
                fieldLabel: 'Estado',
                labelWidth: 70,
                name: 'strEstado',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strUsrCreacion',
                xtype: 'displayfield',
                fieldLabel: 'Usuario Creación',
                labelWidth: 200,
                name: 'strUsrCreacion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strFeCreacion',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Fecha Creación',
                labelWidth: 100,
                name: 'strFeCreacion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strDescripcion',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Descripción:',
                labelWidth: 200,
                name: 'strDescripcion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strValor1',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor a Cobrar:',
                labelWidth: 200,
                name: 'strValor1',
                labelStyle: 'font-weight:bold;',
                value: 'Valor a cobrar al cliente por cargo de reactivaci\u00F3n de servicio.',
                textAlign: 'left'
            },
            {
                id: 'strValor2',
                colspan: 2,
                xtype: 'displayfield',
                fieldLabel: 'Fecha de Activaci\u00F3n de Servicios:',
                labelWidth: 200,
                name: 'strValor2',
                labelStyle: 'font-weight:bold;',
                value: 'Fecha de referencia de activaci\u00F3n del servicio ',
                textAlign: 'left'
            },
            {
                id: 'strValor3',
                xtype: 'checkboxfield',
                fieldLabel: 'Generar Cobro a Todos',
                labelStyle: 'font-weight:bold;',
                labelWidth: 150,
                name: 'strValor3',
                handler: function(){
                    var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(0);
                    if(!(recStore.get('strValor3') == '1' && this.checked)) {
                        var strCobrarTodos = '0';
                        var strMsjCobrarTodos = 'Desactivado';
                        if(this.checked){
                            strCobrarTodos = '1';
                            strMsjCobrarTodos = 'Activado';
                        }
                        Ext.Ajax.request({
                            url: urlActualizaParametroDet,
                            method: 'POST',
                            timeout: 99999,
                            params: {
                                intIdParametroDet: recStore.get('intIdParametroDet'),
                                strDescripcion: recStore.get('strDescripcionDet'),
                                strValor1: recStore.get('strValor1'),
                                strValor2: recStore.get('strValor2'),
                                strValor3: strCobrarTodos,
                                strValor4: recStore.get('strValor4'),
                                strActualizaSoloDescripcion: 'NO'
                            },
                            success: function() {
                                storeParametrosDet.load();
                                storeParametrosHist.load(); 
                                Ext.Msg.alert('Informaci\u00F3n', 'Generar Cobro por Reconexi\u00F3n a Todos se ha '+strMsjCobrarTodos+' correctamente.');                                   
                            },
                            failure: function(result) {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });  
                    }
                }
            }
        ],
      // Reset and Submit buttons
        buttons: [{
            text: 'Activar',
            formBind: true, //only enabled once the form is valid
            disabled: true,
            tooltip : 'Activar',         
            handler: function() {
              
              var recStore  = storeParametrosCab.getAt(0);

              Ext.Ajax.request({
                       url : urlValidarDisponibilidadOpcionPorHora,
                       method : 'post',
                       params: {
                                 opcionTelcos: 'CARGO_REACTIVACION_MD'
                               },
                       success : function(response)
                       {
                            var respuesta = false;
                            var json = Ext.JSON.decode(response.responseText);
                            
                            if(json.strPermiteAcceso == "SI")
                            {						      
                                respuesta = true;
                            }
                           
                            if(respuesta)
                            {
                                Ext.MessageBox.confirm(
                                    'Activar Par\u00e1metro',
                                    '¿Est\u00e1 seguro de activar el par\u00e1metro de Cargo por Reactivaci\u00F3n de Servicio ?',
                                    function(btn) {
                                        if (btn === 'yes') {

                                            activarInactivarCargoReactivacion({urlActivarInactivar:urlActivarInactivar, 
                                                                              intIdParametro: recStore.get('intIdParametro'), 
                                                                              strEstado: 'Activo'}, 
                                                                              recStore, 
                                                                              storeParametrosCab, 
                                                                              storeParametrosDet,
                                                                              storeParametrosHist,
                                                                              arrayTituloMensajeBox);
                                        }
                                    });
                            }
                            else
                            {
                                var w = new Ext.Window({
                                height: 95, width: 525,
                                resizable: false,
                                title: 'Informativo',
                                html: "<img style=\"vertical-align:middle\" "+
                                      "src=\"/./public/images/stop.png\"> "+"<span>Esta opción solo se "+
                                      "encuentra disponible desde las "+json.strHoraInicio+
                                      " hasta las "+json.strHoraFin+"</span>"
                                });

                                w.show();

                            }                           

                        },
                       failure: function(result) {
                           Ext.Msg.alert('Error ', 'Error: '+ result.statusText);
                       }
               });             
            }
        }, {
            text: 'Inactivar',
            formBind: true, //only enabled once the form is valid
            disabled: true,
            tooltip : 'Inactivar',         
            handler: function() {
              
              var recStore = storeParametrosCab.getAt(0); 
              
              Ext.Ajax.request({
                       url : urlValidarDisponibilidadOpcionPorHora,
                       method : 'post',
                       params: {
                                 opcionTelcos: 'CARGO_REACTIVACION_MD'
                               },
                       success : function(response)
                       {
                            var respuesta = false;
                            var json = Ext.JSON.decode(response.responseText);
                            
                            if(json.strPermiteAcceso == "SI")
                            {						      
                                respuesta = true;
                            }
                           
                            if(respuesta)
                            {
                                Ext.MessageBox.confirm(
                                    'Inactivar Par\u00e1metro',
                                    '¿Est\u00e1 seguro de inactivar el par\u00e1metro de Cargo por Reactivaci\u00F3n de Servicio ? ',
                                    function(btn) {
                                        if (btn === 'yes') {
                                            
                                            activarInactivarCargoReactivacion({urlActivarInactivar:urlActivarInactivar, 
                                                                              intIdParametro: recStore.get('intIdParametro'), 
                                                                              strEstado: 'Inactivo'}, 
                                                                              recStore, 
                                                                              storeParametrosCab, 
                                                                              storeParametrosDet,
                                                                              storeParametrosHist,
                                                                              arrayTituloMensajeBox);

                                        }
                                    });
                            }
                            else
                            {
                                var w = new Ext.Window({
                                height: 95, width: 525,
                                resizable: false,
                                title: 'Informativo',
                                html: "<img style=\"vertical-align:middle\" "+
                                      "src=\"/./public/images/stop.png\"> "+"<span>Esta opción solo se "+
                                      "encuentra disponible desde las "+json.strHoraInicio+
                                      " hasta las "+json.strHoraFin+"</span>"
                                });

                                w.show();

                            }                           

                        },
                       failure: function(result) {
                           Ext.Msg.alert('Error ', 'Error: '+ result.statusText);
                       }
               });               
            }
        }],
      
        
    });    

    //Define un modelo para el store storeParametrosDet
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });

    // Crea un store para obtener el detalle de los parámetros con la referencia de la cabecera de parámetros.
    storeParametrosDet = Ext.create('Ext.data.Store', {
        pageSize: 2,
        model: 'ListaParametrosDetModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreParametrosDet,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroDetResult',
                totalProperty: 'intTotalParametros'
            },
            simpleSortMode: true
        }
    });
    
    storeParametrosDet.load();

    //Define un modelo para el store storeParametrosHist
    Ext.define('ListaParametrosHistModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'strUsrUltMod', type: 'string'},
            {name: 'strFeUltMod', type: 'string'},            
        ]
    });

    // Crea un store para obtener el detalle de los parámetros con la referencia de la cabecera de parámetros.
    storeParametrosHist = Ext.create('Ext.data.Store', {
        pageSize: 6,
        model: 'ListaParametrosHistModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreParametrosHist,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroHistResult',
                totalProperty: 'intTotalParametros'
            }
        }
    });
    
    storeParametrosHist.load();
    
    // Crea el grid para mostrar el detalle de la cabecera de parámetros
    gridParametrosDet = Ext.create('Ext.grid.Panel', {
        title: 'Detalle de Parametros',
        store: storeParametrosDet,
        id: 'gridParametrosDet',
        cls: 'custom-grid',
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {
                header: 'Descripción ',
                dataIndex: 'strDescripcionDet',
                width: 500
            },
            {
                header: 'Valor a Cobrar',
                dataIndex: 'strValor1',
                width: 150
            },
            {
                header: 'Fecha Activaci\u00F3n de Servicios',
                dataIndex: 'strValor2',
                width: 200
            },
            {header: 'Estado', dataIndex: 'strEstado', width: 80},
            {header: 'Usr. Creación', dataIndex: 'strUsrCreacion', width: 100},
            {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 100},
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 160,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            this.items[0].tooltip = 'Editar';
                            return "button-grid-edit";
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {   
                            Ext.Ajax.request({
                                     url : urlValidarDisponibilidadOpcionPorHora,
                                     method : 'post',
                                     params: {
                                               opcionTelcos: 'CARGO_REACTIVACION_MD'
                                             },
                                     success : function(response)
                                     {
                                          var respuesta = false;
                                          var json = Ext.JSON.decode(response.responseText);

                                          if(json.strPermiteAcceso == "SI")
                                          {						      
                                              respuesta = true;
                                          }

                                          if(respuesta)
                                          {
                                              showEditarCargoReactivacion(grid.getStore().getAt(rowIndex).data);
                                          }
                                          else
                                          {
                                              var w = new Ext.Window({
                                              height: 95, width: 525,
                                              resizable: false,
                                              title: 'Informativo',
                                              html: "<img style=\"vertical-align:middle\" "+
                                                    "src=\"/./public/images/stop.png\"> "+"<span>Esta opción solo se "+
                                                    "encuentra disponible desde las "+json.strHoraInicio+
                                                    " hasta las "+json.strHoraFin+"</span>"
                                              });

                                              w.show();

                                          }                           

                                      },
                                     failure: function(result) {
                                         Ext.Msg.alert('Error ', 'Error: '+ result.statusText);
                                     }
                             });                            
                            
                        }
                    }                                
                ]
            }            
            
        ],
        height: 150,
        width: '100%',
        renderTo: 'ListadosDetalleParametros'
    }); 
    
    // Crea el grid para mostrar el historial de edición de parámetros
    gridParametrosHist = Ext.create('Ext.grid.Panel', {
        title: 'Historial de Par\u00e1metros',
        store: storeParametrosHist,
        id: 'gridParametrosHist',
        cls: 'custom-grid',
        autoScroll: false,
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroHist', hidden: true},
            {
                header: 'Detalle',
                dataIndex: 'strDescripcionDet',
                width: 800
            },
            {header: 'Usr. Modifica.', dataIndex: 'strUsrUltMod', width: 100},
            {header: 'Fe. Modifica.', dataIndex: 'strFeUltMod', width: 150}          
            
        ],
        height: 200,
        width: '100%',
        renderTo: 'ListadoHistorialParametros',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeParametrosHist,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    }); 
    

    /**Panel que contiene el formulario de presentación de la cabecera de parámetro, el panel de busqueda y el grid
    * usado en la ventana windowsParametrosDet
    */
   panelParametrosDet = new Ext.Panel({
       width: '100%',
       height: '100%',
       items: [
           formGetParametrosCab
       ],
       renderTo: 'ListadosCabeceraParametros',
   });   
    
});

function activarInactivarCargoReactivacion(objParams, recStore, storeParametrosCab, storeParametrosDet, storeParametrosHist, arrayTituloMensajeBox)
{
  Ext.Ajax.request({
    url: objParams.urlActivarInactivar,
    method: 'POST',
    timeout: 99999,
    params: {
      intIdParametro: objParams.intIdParametro,
      strEstado     : objParams.strEstado
    },
    success: function(response) {
        
      var objRespuesta = Ext.decode(response.responseText);       

      if ("100" === objRespuesta.strStatus) {
        recStore.set('strEstado', objParams.strEstado);
      }
      Ext.Msg.alert(arrayTituloMensajeBox[objRespuesta.strStatus], objRespuesta.strMessageStatus);
      storeParametrosCab.load();
      storeParametrosDet.load();
      storeParametrosHist.load();
    },
    failure: function(result) {
      Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
    }
  });
}

/**
 * Documentación para el método 'showEditarCargoReactivacion'.
 * Función que envia mediante post el id del parámetro y la información que será enviada
 * al controlador para la respectiva actualización.
 * 
 * @param mixed  data          
 *
 * @author Edgar Holguín <eholguín@telconet.ec>
 * @version 1.0 17-07-2017 
 */
function showEditarCargoReactivacion(data) 
{    
    Ext.onReady(function(){
        
    var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(0);
      
    var objDateFechaActivacion = new Ext.form.DateField({
        id: 'dateFechaActivacion',
        fieldLabel: 'Fecha de Activaci\u00F3n Nueva:',
        labelAlign: 'left',
        labelWidth: 200,
        xtype: 'datefield',
        editable:false,
        format: 'd/m/Y',
        width: 300,
        maxValue: new Date()
   });       
  
      var objTxtValorCargo = Ext.create('Ext.form.field.Text', {
        xtype: 'field',
        fieldLabel: 'Valor de Cargo por  Reactivaci\u00F3n $(0.00):',
        labelAlign : 'left',
        labelWidth: 200,
        id: 'strValorCargoEdit',
        name: 'strValorCargoEdit',
        allowBlank: false,
        regex : Utils.REGEX_PRECIO,                    
        value: recStore.get('strValor1')
    });  
    
    
    panelParametroDet = Ext.create('Ext.form.Panel', {
        title: '',
        renderTo: Ext.getBody(),
        bodyPadding: 5,
        width: 480,
        items:[
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 300
                },
                items: [                   
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Fecha de Activaci\u00F3n Actual:',
                    labelAlign : 'left',
                    labelWidth: 200,                  
                    id: 'strFechaActivacionEdit',
                    name: 'strFechaActivacionEdit',
                    value: recStore.get('strValor2')
                },
                objDateFechaActivacion,
                objTxtValorCargo
              ]
            }
        ],
        buttons:
        [
            {
                text: 'Guardar',
                name: 'guardarBtn',
                disabled: false,
                handler: function() 
                {
                    var dateFechaActivacion = Ext.getCmp('dateFechaActivacion').getValue();
                    var strCargoEdit        = Ext.getCmp('strValorCargoEdit').getValue();
                    var strFechaActivacion  = recStore.get('strValor2');
                    var strDia              = '';
                    var strMes              = '';
                    var strAnio             = '';                    
                   
                    if( null !== dateFechaActivacion)
                    {                    
                        strDia              = ("0" + dateFechaActivacion.getDate()).slice(-2);
                        strMes              = ("0" + (dateFechaActivacion.getMonth() + 1)).slice(-2)
                        strAnio             = dateFechaActivacion.getFullYear();
                        strFechaActivacion  = strDia+ "/" + strMes + "/" + strAnio; 
                    }
                   
                    if( null === dateFechaActivacion && (Ext.getCmp('strValorCargoEdit').getValue() == recStore.get('strValor1')))
                    {                    
                      Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, debe ser diferente al actual');
                      return false;
                    }
                    if(parseFloat(strCargoEdit) <= 0)
                    {
                      Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, debe ser mayor que 0');
                      return false;
                    }  
                    
                    if(!(Utils.REGEX_PRECIO.test(strCargoEdit)) || '' === strCargoEdit )
                    {
                      Ext.Msg.alert('Error ', 'Formato de precio no v\u00e1lido, el valor ingresado debe tener hasta 2 decimales Ej: (2.50)');
                      return false;
                    }                   
                  
                    Ext.Msg.confirm('Alerta', '¿Est\u00e1 seguro de actualizar par\u00e1metros de Cargo por Reactivaci\u00F3n ?', function(btn) {
                        if (btn == 'yes') 
                        {
                            Ext.Ajax.request({
                                url: urlActualizaParametroDet,
                                method: 'POST',
                                timeout: 99999,
                                params: {
                                    intIdParametroDet: recStore.get('intIdParametroDet'),
                                    strDescripcion: recStore.get('strDescripcionDet'),
                                    strValor1: strCargoEdit,
                                    strValor2: strFechaActivacion,  
                                    strValor3: recStore.get('strValor3'),
                                    strValor4: recStore.get('strValor4'),                                 
                                    strActualizaSoloDescripcion: 'NO'
                                },
                                success: function() {
                                    storeParametrosDet.load();
                                    Ext.Msg.alert('Informaci\u00F3n', 'Datos fueron actualizados correctamente.');
                                    ventanaEditarDatos.close();
                                    storeParametrosDet.load();
                                    storeParametrosHist.load();                                    
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });    
                        }
                    });                         

                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }
        ]
    });  

    ventanaEditarDatos = Ext.widget('window', {
        title: 'Actualizar Datos de Cargo por Reactivaci\u00F3n',
        closeAction: 'destroy',
        closable: true,
        width: 480,
        height: 200,
        minHeight: 200,
        autoScroll: true,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: panelParametroDet
    });

    ventanaEditarDatos.show();
 });    
}

