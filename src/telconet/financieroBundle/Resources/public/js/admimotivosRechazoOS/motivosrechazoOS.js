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
                fieldLabel: 'Nombre parámetro',
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
                value: 'ID_MOTIVO REFERENTE A ADMI_MOTIVO',
                textAlign: 'left'
            },
            {
                id: 'strValor2',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor 2:',
                name: 'strValor2',
                labelStyle: 'font-weight:bold;',
                value: 'NOMBRE_MOTIVO REFERENTE A ADMI_MOTIVO',
                textAlign: 'left'
            }
        ],
      // Reset and Submit buttons.
        buttons: [
            
            {
            text: 'Activar',
            formBind: true, //only enabled once the form is valid.
            disabled: true,
            tooltip : 'Activar',         
            handler: function() {              
              var recStore = storeParametrosCab.getAt(0); 
              Ext.MessageBox.confirm(
                  'Activar Parametros Reingreso de Orden de Servicio? ',
                  '¿Está seguro de activar el parametro de Reingreso de OS Automatica',
                  function(btn) {
                      if (btn === 'yes') {

                          Ext.MessageBox.show({
                              msg: 'Activando Motivos de Rechazo OS...',
                              title: 'Activando',
                              progressText: 'Activando Motivos de Rechazo de OS ...',
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
        },
        {
            
            text: 'Inactivar',
            formBind: true, //only enabled once the form is valid.
            disabled: true,
            tooltip : 'Inactivar',         
            handler: function() {              
              var recStore = storeParametrosCab.getAt(0); 
              Ext.MessageBox.confirm(
                  'Inactivar Parametros Reingreso de Orden de Servicio? ',
                  '¿Está seguro de Inactivar el parametro de Reingreso de OS Automatica',
                  function(btn) {
                      if (btn === 'yes') {

                          Ext.MessageBox.show({
                              msg: 'Inactivando Motivos de Rechazo OS...',
                              title: 'Inactivando',
                              progressText: 'Inactivando Motivos de Rechazo de OS ...',
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
        pageSize: 10,
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
    
        //Define un modelo para el store storeParametrosHist.
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
        pageSize: 10,
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
    
    //Crea el plugin para la edición de filas del grid gridParametrosDet.
    
    
    // Crea el grid para mostrar el detalle de la cabecera de parámetros.
    gridParametrosDet = Ext.create('Ext.grid.Panel', {
        title: 'Detalle de Parametros',
        store: storeParametrosDet,
        id: 'gridParametrosDet',
        cls: 'custom-grid',
        autoScroll: false,

        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {
                header: 'Descripción ',
                dataIndex: 'strDescripcionDet',
                width: 450,
                editor: {
                    allowBlank: false,
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
                    regex : /^([A-Z]{1,1})$/                    
                }
            },

            {header: 'Valor 2', dataIndex: 'strValor2', width: 300},
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
                            showEditarCargoReactivacion(grid.getStore().getAt(rowIndex).data, rowIndex);
                        }
                    }                                
                ]
            }
        ],
        height: 270,
        width: '100%',
        renderTo: 'ListadosDetalleParametros',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeParametrosDet,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });     
    
    // Crea el grid para mostrar el historial de edición de parámetros.
    gridParametrosHist = Ext.create('Ext.grid.Panel', {
        title: 'Historial de Parametros',
        store: storeParametrosHist,
        id: 'gridParametrosHist',
        cls: 'custom-grid',
        autoScroll: false,
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroHist', hidden: true},
            {
                header: 'Descripcion',
                dataIndex: 'strDescripcionDet',
                width: 400
            },
            {
                header: 'Valor 1',
                dataIndex: 'strValor1',
                width: 150
            },
            {
                header: 'Valor 2',
                dataIndex: 'strValor2',
                width: 300
            },
            {
                header: 'Estado Anterior',
                dataIndex: 'strEstado',
                width: 200
            },
            {header: 'Usr. Modifica.', dataIndex: 'strUsrUltMod', width: 150},
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
    /**Panel que contiene el formulario de presentación de la cabecera de parámetro, el panel de búsqueda y el grid
    * usado en la ventana windowsParametrosDet.
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

//Función que muestra el cambio del estado Activo e Inactivo del parámetro Motivos Rechazo PYL.
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
            if ("100" === text.strStatus){
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

function showEditarCargoReactivacion(data,indice) 
{    
    Ext.onReady(function(){

    var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(indice);

    
    if(recStore.get('strEstado')=="Activo")
    {
        var recEstados = Ext.create('Ext.data.Store', {
            fields: ['id', 'name'],
            data : [
                {"id":"2", "name":"Inactivo"}
            ]
        });    
    }
    else
    {
        var recEstados = Ext.create('Ext.data.Store', {
            fields: ['id', 'name'],
            data : [
                {"id":"1", "name":"Activo"}
            ]
        });    
    }
    
    var objEstados = Ext.create('Ext.form.ComboBox', {
        id: 'strEstadoMotivo',
        fieldLabel: 'Cambiar a Nuevo Estado',
        store: recEstados,
        queryMode: 'local',
        displayField: 'name',
        valueField: 'name',
        emptyText:'Seleccione estado a cambiar'
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
                    fieldLabel: 'Motivo de Rechazo',
                    labelAlign : 'left',
                    labelWidth: 200,                  
                    id: 'intMotivoRechazo',
                    name: recStore.get('strValor1'),
                    value: recStore.get('strValor2')
                },
                objEstados
                
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
                    var strEstadoMotivo     = Ext.getCmp('strEstadoMotivo').getValue();
                    var intMotivoRechazo    = Ext.getCmp('intMotivoRechazo').getName();
                    var strFechaActivacion  = recStore.get('strValor2');
                    var strDia              = '';
                    var strMes              = '';
                    var strAnio             = '';                    
                  
                    if( null === strEstadoMotivo)
                    {                    
                      Ext.Msg.alert('Error ', 'No ha seleccionado el Motivo');
                      return false;
                    }
                    
                    
                    Ext.Msg.confirm('Alerta', '¿Está de acuerdo cambiar el estado del Motivo de Rechazo?', function(btn) {
                        if (btn == 'yes') 
                        {
                            Ext.Ajax.request({
                                url: urlActualizaEstadoParametroDet,
                                method: 'POST',
                                timeout: 99999,
                                params: {
                                    intIdParametroDet: recStore.get('intIdParametroDet'),
                                    intMotivoId: intMotivoRechazo,
                                    strEstadoMotivo: strEstadoMotivo
                                },
                                success: function(result) {
                                    storeParametrosDet.load();
                                    responseJson = JSON.parse(result.responseText);
                                    Ext.Msg.alert('Informacion', responseJson.strMessageStatus);
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
        title: 'Actualizar estado de Motivo Rechazo OS',
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
