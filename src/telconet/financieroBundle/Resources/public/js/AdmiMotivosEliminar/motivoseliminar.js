Ext.onReady(function() {
    //array que contiene el título de las ventanas de mensajes.
    var arrayTituloMensajeBox = [];
    arrayTituloMensajeBox['100'] = 'Información';
    arrayTituloMensajeBox['001'] = 'Error';
    arrayTituloMensajeBox['000'] = 'Alerta';
    
    //Define un modelo para el store storeParametrosCab.
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
                    Ext.getCmp('strNombreParametro').setValue(recStore.get('strNombreParametro'));
                    Ext.getCmp('strModulo').setValue(recStore.get('strModulo'));
                    Ext.getCmp('strProceso').setValue(recStore.get('strProceso'));
                    Ext.getCmp('strEstado').setValue(recStore.get('strEstado'));
                    Ext.getCmp('strUsrCreacion').setValue(recStore.get('strUsrCreacion'));
                    Ext.getCmp('strFeCreacion').setValue(recStore.get('strFeCreacion'));
                    Ext.getCmp('strDescripcion').setValue(recStore.get('strDescripcion'));
                }
            }        
    });

    //Formulario estático que solo muestra la información de la cabecera de parámetros.
    formGetParametrosCab = Ext.create('Ext.form.Panel', {
        height: 300,
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
                name: 'strNombreParametro',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strModulo',
                xtype: 'displayfield',
                fieldLabel: 'Módulo',
                name: 'strModulo',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strProceso',
                xtype: 'displayfield',
                fieldLabel: 'Proceso',
                name: 'strProceso',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strEstado',
                xtype: 'displayfield',
                fieldLabel: 'Estado',
                name: 'strEstado',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strUsrCreacion',
                xtype: 'displayfield',
                fieldLabel: 'Usuario Creación',
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
                name: 'strDescripcion',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strValor1',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor 1:',
                name: 'strValor1',
                labelStyle: 'font-weight:bold;',
                value: 'EL MOTIVO GENERA NOTA DE CREDITO SI (S) O  NO (N)',
                textAlign: 'left'
            },
            {
                id: 'strValor2',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor 2:',
                name: 'strValor2',
                labelStyle: 'font-weight:bold;',
                value: 'MOTIVOS ELIMINAR ORDEN SERVICIO VENDEDOR',
                textAlign: 'left'
            }
        ],
      // Reset and Submit buttons
        buttons: [{
            text: 'Activar',
            formBind: true, //only enabled once the form is valid
            disabled: true,
            tooltip : 'Activar',         
            handler: function() {
              
              var recStore = storeParametrosCab.getAt(0); 
              Ext.MessageBox.confirm(
                  'Activar Par\u00e1metro Motivos de Elimanción de la Orden de Servicio. ',
                  '¿Est\u00e1 seguro de activar el par\u00e1metro Motivos de Elimanción de la Orden de Servicio?',
                  function(btn) {
                      if (btn === 'yes') {

                          Ext.MessageBox.show({
                              msg: 'Activando Motivos de Elimanción de la Orden de Servicio...',
                              title: 'Activando',
                              progressText: 'Activando Motivos de Elimanción de la Orden de Servicio...',
                              progress: true,
                              closable: false,
                              width: 300,
                              wait: true,
                              waitConfig: {interval: 200}
                          });

                          activarInactivarMotivosRechazo({urlActivarInactivar:urlActivarInactivar, 
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
        }, {
            text: 'Inactivar',
            formBind: true, //only enabled once the form is valid
            disabled: true,
            tooltip : 'Inactivar',         
            handler: function() {
              
              var recStore = storeParametrosCab.getAt(0); 
              Ext.MessageBox.confirm(
                  'Inactivar Par\u00e1metro',
                  '¿Est\u00e1 seguro de inactivar el par\u00e1metro Motivos de Elimanción de la Orden de Servicio?',
                  function(btn) {
                      if (btn === 'yes') {

                          Ext.MessageBox.show({
                              msg: 'Inactivando Motivos de Elimanción de la Orden de Servicio ...',
                              title: 'Inactivando',
                              progressText: 'Inactivando de Elimanción de la Orden de Servicio ...',
                              progress: true,
                              closable: false,
                              width: 300,
                              wait: true,
                              waitConfig: {interval: 200}
                          });
                          activarInactivarMotivosRechazo({urlActivarInactivar:urlActivarInactivar, 
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
        }],
      
        
    });    

    //Define un modelo para el store storeParametrosDet.
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
        pageSize: 6,
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
    
    //Crea el plugin para la edición de filas del grid gridParametrosDet
    rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText  : 'Guardar',
        cancelBtnText: 'Cancelar',      
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            beforeedit: function(editor, context) {
                var permiso = $("#ROLE_430-6");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                return true;
            },
            afteredit: function(roweditor, changes, record, rowIndex) {
                //Valida que los nuevos campos a ingresar no sean nulos.
                if ( '' !== changes.newValues.strDescripcionDet.trim() &&
                    ('' !== changes.newValues.strValor1.trim() || '' !== changes.newValues.strValor2.trim() ))
                {
                    //Valida que los campos nuevos sean distintos a los de la base para hacer la petición ajax al controlador.
                    if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() ||
                        changes.originalValues.strValor1.trim()         !== changes.newValues.strValor1.trim() )
                    {
                        /**La strActualizaSoloDescripcion variable define si se actualiza solo el campo
                         * descripcion de la fila o no. Cuando la variable strActualizaSoloDescripcion es enviada
                         * en request con 'NO' se validará del lado del controlador AdmiParametroCabController con el método
                         * validaParametroCab que no exista parámetro en la base con los mismos datos nuevos a ingresar.
                         */
                        var strActualizaSoloDescripcion = 'NO';
                        if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() &&
                            (changes.originalValues.strValor1.trim() === changes.newValues.strValor1.trim() ))
                        {
                            /**
                             * Cuando la variable strActualizaSoloDescripcion es enviada.
                             * en request con 'SI' no se validará del lado del controlador que ya exista el parámeetro.
                             */
                            strActualizaSoloDescripcion = 'SI';
                        }
                        
                        if ('S' !== changes.newValues.strValor1.trim() && ('N' !== changes.newValues.strValor1.trim()))
                        {
                            Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, debe ser N o S');
                            var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                            recStore.set('strValor1', changes.originalValues.strValor1);                           
                            return false;
                        }

                        Ext.MessageBox.show({
                            msg: 'Actualizando registro...',
                            title: 'Procesando',
                            progressText: 'Actualizando.',
                            progress: true,
                            closable: false,
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });

                        Ext.Ajax.request({
                            url: urlActualizaParametroDet,
                            method: 'POST',
                            timeout: 60000,
                            params: {
                                intIdParametroDet: changes.originalValues.intIdParametroDet,
                                strDescripcion: changes.newValues.strDescripcionDet.trim(),
                                strValor1: changes.newValues.strValor1.trim(),
                                strValor2: changes.newValues.strValor2.trim(),
                                strActualizaSoloDescripcion: strActualizaSoloDescripcion
                            },
                            success: function(response) {
                                var text = Ext.decode(response.responseText);
                                var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                /*Cuando el strStatus enviado en el Response es != 100 el registro vuelve a sus 
                                 * valores originales.
                                 */
                                if ("100" !== text.strStatus) {
                                    recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                    recStore.set('strValor1', changes.originalValues.strValor1);
                                    recStore.set('strValor2', changes.originalValues.strValor2);
                                }
                                
                                Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                storeParametrosDet.load();
                                storeParametrosHist.load();
                            },
                            failure: function(result) {
                                //Por alguna excepcion no controlada el registro vuelve a sus valores originales.
                                var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                recStore.set('strValor1', changes.originalValues.strValor1);
                                recStore.set('strValor2', changes.originalValues.strValor2);
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    } else
                    {
                        Ext.Msg.alert('Información', 'No hay cambios en el registro.');
                    }
                }//Validacion de Campos
                else {
                    Ext.Msg.alert('Alerta!',
                        'No puede insertar el regitro vacio, Debe ingresar una descripcion y al menos un valor.');
                }
            }
        }
    });
    
    // Crea el grid para mostrar el detalle de la cabecera de parámetros
    gridParametrosDet = Ext.create('Ext.grid.Panel', {
        title: 'Detalle de Parametros',
        store: storeParametrosDet,
        id: 'gridParametrosDet',
        cls: 'custom-grid',
        autoScroll: false,
        plugins: [rowEditing],
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {
                header: 'Descripción ',
                dataIndex: 'strDescripcionDet',
                width: 450,
                editor: {
                    allowBlank: false ,
                    maskRe: /([A-Za-z0-9\s\_\-]+)/i, 
                    regex: /^([A-Za-z0-9\s\_\-]+)$/
                }
            },
            {
                header: 'Valor 1',
                dataIndex: 'strValor1',
                width: 100,                
                editor: {
                    allowBlank: false,
                    maskRe: /^([A-Z])/ ,	
                    regex : /^([A-Z]){1,1}$/
                }
            },
            {header: 'Valor 2', dataIndex: 'strValor2', width: 100},
            {header: 'Estado', dataIndex: 'strEstado', width: 80},
            {header: 'Usr. Creación', dataIndex: 'strUsrCreacion', width: 100},
            {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 100}          
        ],
        height: 150,
        width: 1000,
        renderTo: 'ListadosDetalleParametros',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeParametrosDet,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
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
       width: '97%',
       height: '100%',
       items: [
           formGetParametrosCab
       ],
       renderTo: 'ListadosCabeceraParametros',
   });   
    
});

function activarInactivarMotivosRechazo(objParams, recStore, storeParametrosCab, storeParametrosDet, storeParametrosHist, arrayTituloMensajeBox)
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
      var text = Ext.decode(response.responseText);
      if ("100" === text.strStatus) {
        recStore.set('strEstado', objParams.strEstado);
      }
      Ext.Msg.alert(arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
      storeParametrosCab.load();
      storeParametrosDet.load();
      storeParametrosHist.load();
    },
    failure: function(result) {
      Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
    }
  });
}