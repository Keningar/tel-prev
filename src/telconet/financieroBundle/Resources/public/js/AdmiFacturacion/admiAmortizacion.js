/*
 * Función para validar objetos JSON. 
 * @author Madeline Haz <mhaz@telconet.ec>
 * @version 1.0 04-07-2019 | Versión Inicial.
 *
 */
Ext.onReady(function(){
    var precioInst = 0;
    var precioEquipo = 0;
        
    Ext.create('Ext.tab.Panel',{
        height: 600,
        width : 1100,
        renderTo: 'tabs_admi',
        activeTab: 0,
        items:[
            {contentEl:'tab1',title:'Instalación'},                           
            {contentEl:'tab2', title:'Equipos',
                listeners:{ activate: function(tab){
                        storeParametrosDetEquipo.load();  
                        storeParametrosHistEquipo.load(); }}},
            {contentEl:'tab3', title:'Tabla de Amortización 24 meses',
                listeners:{ activate: function(tab){
                        storeParametrosAmortizacionInst.load();   
                        storeParametrosAmortizacionEquipo.load(); 
                }}},                                     
            {contentEl:'tab4', title:'Tabla de Amortización 36 meses',
             listeners:{ activate: function(tab){
                        storeAmortizacionInst.load();   
                        storeAmortizacionEquipo.load(); 
             }}},  
            ],
        defaults:{autoScroll:true},
                
    }); 
    //ADMINISTRACIÓN TAB INSTALACIÓN.  //CABECERA PARÁMETROS DE INSTALACIÓN.    
    Ext.create('Ext.Panel', {
            renderTo : 'CabeceraInstalacion',
            height: 300,
            width: 1000,
            bodyPadding: 10,        
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
                    value: 'ÚLTIMA MILLA DE LA INSTALACIÓN',
                    textAlign: 'left'
                },
                {
                    id: 'strValor2',
                    colspan: 3,
                    xtype: 'displayfield',
                    fieldLabel: 'Valor 2:',
                    name: 'strValor2',
                    labelStyle: 'font-weight:bold;',
                    value: 'TIPO DE INSTALACIÓN',
                    textAlign: 'left'
                },
                {
                    id: 'strValor3',
                    colspan: 3,
                    xtype: 'displayfield',
                    fieldLabel: 'Valor 3:',
                    name: 'strValor3',
                    labelStyle: 'font-weight:bold;',
                    value: 'PRECIO DE INSTALACIÓN AL 100% SEGÚN ÚLTIMA MILLA',
                    textAlign: 'left'
                },
                {
                    id: 'strValor4',
                    colspan: 3,
                    xtype: 'displayfield',
                    fieldLabel: 'Valor 4:',
                    name: 'strValor4',
                    labelStyle: 'font-weight:bold;',
                    value: 'PRECIO DE INSTALACIÓN AL 50% SEGÚN ÚLTIMA MILLA',
                    textAlign: 'left'
                }            
            ]                       
    }); 
    
   //Define un modelo para el store de la Cabecera para los parámetro de instalación.
    Ext.define('ListaParametrosCabModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParametro'    , type: 'int'},
            {name: 'strNombreParametro', type: 'string'},
            {name: 'strDescripcion'    , type: 'string'},
            {name: 'strModulo'         , type: 'string'},
            {name: 'strProceso'        , type: 'string'},
            {name: 'strEstado'         , type: 'string'},
            {name: 'strUsrCreacion'    , type: 'string'},
            {name: 'strFeCreacion'     , type: 'string'}
        ]
    }); 
  // store para cabecera de los parámetro de instalación.
    storeParametrosCabInst = Ext.create('Ext.data.Store',
    {
        pageSize: 1,
        model: 'ListaParametrosCabModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlGetstoreListaParamInst,
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
                    var recStore = storeParametrosCabInst.getAt(0);
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
    
//Define un modelo para el store que lista el detalle de los parámetros de instalación.
    Ext.define('ListaParametrosDetModel',
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParametroDet', type: 'int' },
            {name: 'intIdParametroCab', type: 'int' },
            {name: 'strDescripcionDet', type: 'string' },
            {name: 'strValor1'        , type: 'string' },
            {name: 'strValor2'        , type: 'string' },
            {name: 'strValor3'        , type: 'string' },
            {name: 'strValor4'        , type: 'string' },
            {name: 'strValor5'        , type: 'string' },
            {name: 'strValor6'        , type: 'string' },
            {name: 'strEstado'        , type: 'string' },
            {name: 'strUsrCreacion'   , type: 'string' },
            {name: 'strFeCreacion'    , type: 'string' }
        ]
    });

    // Crea un store para obtener el detalle de los parámetros de instalación con referencia a los datos de cabecera.    
    storeParametrosDet = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
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
    //Define un modelo para el store que lista el historial de los parámetros de instalación.
    Ext.define('ListaParametrosHistModel',
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParametroDet', type: 'int' },
            {name: 'intIdParametroCab', type: 'int' },
            {name: 'strDescripcionDet', type: 'string' },
            {name: 'strValor1'        , type: 'string' },
            {name: 'strValor2'        , type: 'string' },
            {name: 'strValor3'        , type: 'string' },
            {name: 'strValor4'        , type: 'string' },
            {name: 'strEstado'        , type: 'string' },
            {name: 'strUsrCreacion'   , type: 'string' },
            {name: 'strFeCreacion'    , type: 'string' },
            {name: 'strUsrUltMod'     , type: 'string' },
            {name: 'strFeUltMod'      , type: 'string' }
        ]
    });
    
    // Crea un store para obtener el historial de los parámetros de instalación con referencia a los datos de cabecera.    
    storeParametrosHist = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
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
    
    //SecCrea el plugin para la edición de filas del grid de detalles de Instalación.   
    rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText  : 'Guardar',
        cancelBtnText: 'Cancelar',      
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {  
            afteredit: function(roweditor, changes, record, rowIndex) {                
                //Valida que los nuevos campos a ingresar no sean nulos.
                if ( '' !== changes.newValues.strDescripcionDet.trim() &&
                    ('' !== changes.newValues.strValor3.trim() ) && ('' !== changes.newValues.strValor4.trim() ))
                {   
                    //Valida que los campos nuevos sean distintos a los de la base para hacer la petición ajax al controlador
                    if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() ||
                        changes.originalValues.strValor3.trim()         !== changes.newValues.strValor3.trim()         ||     
                        changes.originalValues.strValor4.trim()         !== changes.newValues.strValor4.trim())         
                    {
                        /**La strActualizaSoloDescripcion variable define si se actualiza solo el campo 
                         * descripción de la fila o no. Cuando la variable strActualizaSoloDescripcion es enviada 
                         * en request con 'NO' se validará del lado del controlador AdmiParametroCabController con el método 
                         * validaParametroCab que no exista parámetro en la base con los mismos datos nuevos a ingresar.
                         */
                        var strActualizaSoloDescripcion = 'NO';
                        if (changes.originalValues.strDescripcionDet.trim() !== changes.newValues.strDescripcionDet.trim() &&
                            (changes.originalValues.strValor3.trim() === changes.newValues.strValor3.trim()) &&
                            (changes.originalValues.strValor4.trim() === changes.newValues.strValor4.trim()))
                        {
                            /**
                             * Cuando la variable strActualizaSoloDescripcion es enviada 
                             * en request con 'SI' no se validará del lado del controlador que ya exista el parámetro.
                             */
                            strActualizaSoloDescripcion = 'SI';
                        }
                        var  intValor3 = parseInt(changes.newValues.strValor3.trim());
                        var  intValor4 = parseInt(changes.newValues.strValor4.trim());
                        if ( intValor3 > 1000 || intValor4 > 1000   )
                        {
                            Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, Debe ser Menor o Igual a 1000');
                            var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                            recStore.set('strValor3', changes.originalValues.strValor3);                            
                            recStore.set('strValor4', changes.originalValues.strValor4);                            
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
                                strValor3: changes.newValues.strValor3.trim(),
                                strValor4: changes.newValues.strValor4.trim(),
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
                                    recStore.set('strValor3', changes.originalValues.strValor3);                                    
                                    recStore.set('strValor4', changes.originalValues.strValor4);                                    
                                }
                                    Ext.Msg.alert('Información', text.strMessageStatus);
                                    storeParametrosDet.load();
                                    storeParametrosHist.load();
                            },
                            failure: function(result) {
                                //Por alguna excepción no controlada el registro vuelve a sus valores originales.
                                var recStore = Ext.getCmp('gridParametrosDet').getStore().getAt(changes.rowIdx);
                                recStore.set('strDescripcionDet', changes.originalValues.strDescripcionDet);
                                recStore.set('strValor3', changes.originalValues.strValor3);                                
                                recStore.set('strValor4', changes.originalValues.strValor4);                                
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    } else
                    {
                        Ext.Msg.alert('Información', 'No hay cambios en el registro.');
                    }
                }//Validación de Campos.
                else {
                    Ext.Msg.alert('Alerta!',
                        'No puede insertar el regitro vacío, Debe ingresar una descripción y al menos un valor.');
                }
            }
        }
    });    
    // Crea el grid para mostrar el detalle de la cabecera de los parámetros de instalación.
    gridParametrosDet = Ext.create('Ext.grid.Panel',{
        title: 'Detalle de Parámetros',
        store: storeParametrosDet,
        id: 'gridParametrosDet',
        cls: 'custom-grid',
        autoScroll: false,
        plugins: [rowEditing],
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {header: 'Descripción ',
             dataIndex: 'strDescripcionDet',
             width: 300,
             editor: {
                      allowBlank: false,
                      maskRe: /([A-Za-z0-9\s\_\-]+)/i, 
                      regex: /^([A-Za-z0-9\s\_\-]+)$/
             }
            },
            {header: 'Valor 1', dataIndex: 'strValor1', width: 100  },
            {header: 'Valor 2', dataIndex: 'strValor2', width: 150  },
            {
                header: 'Valor 3',
                dataIndex: 'strValor3',
                width: 100,                
                editor: {
                         allowBlank: false,
                         maskRe: /[0-9]/, 
                         regex: /^[1-9]+([0-9]+)?$/
                }
            },
            {
                header: 'Valor 4',
                dataIndex: 'strValor4',
                width: 100,                
                editor: {
                    allowBlank: false,
                    maskRe: /[0-9]/, 
                    regex: /^[1-9]+([0-9]+)?$/
                }
            },
            {header: 'Estado', dataIndex: 'strEstado', width: 80  },
            {header: 'Usr. Creación', dataIndex:'strUsrCreacion', width: 100 },
            {header: 'Fe. Creación', dataIndex: 'strFeCreacion', width: 100  }
        ],
        height: 270,
        width: 1000,
        renderTo: 'ListadosDetParamInstalacion',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeParametrosDet,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });         
    // Crea el grid para mostrar el historial de edición de los parámetros de instalación.
    gridParametrosHist = Ext.create('Ext.grid.Panel',
    {
        title: 'Historial de Par\u00e1metros',
        store: storeParametrosHist,
        id: 'gridParametrosHist',
        cls: 'custom-grid',
        autoScroll: false,
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroHist', hidden: true },
            {
                header: 'Detalle',
                dataIndex: 'strDescripcionDet',
                width: 500
            },
            {header: 'Usr. Modifica.', dataIndex: 'strUsrUltMod', width: 150 },
            {header: 'Fe. Modifica.', dataIndex: 'strFeUltMod', width: 150   }
        ],
        height: 270,
        width: 1000,
        renderTo: 'ListadoHistParamInstalacion',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeParametrosHist,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });   
//ADMINISTRACIÓN TAB EQUIPOS. // CABECERA PARÁMETROS EQUIPOS.
    Ext.create('Ext.Panel', {
        renderTo : 'CabeceraEquipos',
        height: 300,
        width: 1000,
        bodyPadding: 10,        
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 3,
            pack: 'center'
        },
        items: [
            {
                id: 'strParametro',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Nombre parámetro',
                name: 'strParametro',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strModule',
                xtype: 'displayfield',
                fieldLabel: 'Módulo',
                name: 'strModule',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strProcess',
                xtype: 'displayfield',
                fieldLabel: 'Proceso',
                name: 'strProcess',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strState',
                xtype: 'displayfield',
                fieldLabel: 'Estado',
                name: 'strState',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strUser',
                xtype: 'displayfield',
                fieldLabel: 'Usuario Creación',
                name: 'strUser',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strDate',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Fecha Creación',
                name: 'strDate',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'left'
            },
            {
                id: 'strDescription',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Descripción:',
                name: 'strDescription',
                labelStyle: 'font-weight:bold;',
                value: 'VALORES A FACTURAR EN EL RETIRO DE EQUIPOS POR SOPORTE',
                textAlign: 'left'
            },
            {
                id: 'strValor1Det',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor 1:',
                name: 'strValor1Det',
                labelStyle: 'font-weight:bold;',
                value: 'TECNOLOGÍA',
                textAlign: 'left'
            },
            {
                id: 'strValor2Det',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor 2:',
                name: 'strValor2Det',
                labelStyle: 'font-weight:bold;',
                value: 'FORMA PARTE DE CANCELACIÓN VOLUNTARIA SI (S) O  NO (N)',
                textAlign: 'left'
            },
            {
                id: 'strValor3Det',
                colspan: 3,
                xtype: 'displayfield',
                fieldLabel: 'Valor 3:',
                name: 'strValor3Det',
                labelStyle: 'font-weight:bold;',
                value: 'PRECIO EQUIPOS',
                textAlign: 'left'
            }            
        ]                       
    });    
//Define un modelo para el store de la Cabecera del parámetro Equipo.
    Ext.define('ParametrosCabModelEquipo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intParametroId', type: 'int'},
            {name: 'strParametro'  , type: 'string'},
            {name: 'strDescription', type: 'string'},
            {name: 'strModule'     , type: 'string'},
            {name: 'strProcess'    , type: 'string'},
            {name: 'strState'      , type: 'string'},
            {name: 'strUser'       , type: 'string'},
            {name: 'strDate'       , type: 'string'}
        ]
    }); 
// Store para el parámetro Equipo.
    storeParametrosCabEquipo = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
        model: 'ParametrosCabModelEquipo',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlGetstoreListaParamEquipo,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParametroCab',
                totalProperty: 'intTotalParam'
            },
            simpleSortMode: true
        },
        listeners: {
                load: function() {
                    var recStore = storeParametrosCabEquipo.getAt(0);
                    Ext.getCmp('strParametro').setValue(recStore.get('strParametro'));
                    Ext.getCmp('strDescription').setValue(recStore.get('strDescription'));
                    Ext.getCmp('strModule').setValue(recStore.get('strModule'));
                    Ext.getCmp('strProcess').setValue(recStore.get('strProcess'));
                    Ext.getCmp('strState').setValue(recStore.get('strState'));
                    Ext.getCmp('strUser').setValue(recStore.get('strUser'));
                    Ext.getCmp('strDate').setValue(recStore.get('strDate'));                    
            }
        }
    }); 
//Define un modelo para el store que lista el detalle del parámetro equipos.
    Ext.define('ParametrosDetModelEquipos',
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParamDet'    , type: 'int' },
            {name: 'intIdParamCab'    , type: 'int' },
            {name: 'strDescriptionDet', type: 'string' },
            {name: 'strValor1Det', type: 'string'      },
            {name: 'strValor2Det', type: 'string'      },
            {name: 'strValor3Det', type: 'string'      },
            {name: 'strValor4Det', type: 'string'      },
            {name: 'strValor5Det', type: 'string'      },
            {name: 'strValor6Det', type: 'string'      },
            {name: 'strStateDet' , type: 'string'      },
            {name: 'strUserDet'  , type: 'string'      },
            {name: 'strDateDet'  , type: 'string'      }
        ]
    });
 // Crea un store para obtener el detalle de los parámetros de instalación con referencia a los datos de cabecera.
    storeParametrosDetEquipo = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
        model: 'ParametrosDetModelEquipos',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreParametrosDetEquipo,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParamDetResult',
                totalProperty: 'intTotalParam'
            },
            simpleSortMode: true
        }
    });
    //Crea el plugin para la edición de filas del grid de detalles de Equipos.   
    rowEditingEquipos = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText  : 'Guardar',
        cancelBtnText: 'Cancelar',      
        clicksToMoveEditor: 1,
        autoCancel: false,        
        listeners: {       
            afteredit: function(roweditor, changes, record, rowIndex) {                
                //Valida que los nuevos campos a ingresar no sean nulos.
                if(('' !== changes.newValues.strValor5Det.trim() ) && ('' !== changes.newValues.strValor6Det.trim() ))
                {   
                    //Valida que los campos nuevos sean distintos a los de la base para hacer la petición ajax al controlador
                    if (changes.originalValues.strValor5Det.trim()      !== changes.newValues.strValor5Det.trim()      ||     
                        changes.originalValues.strValor6Det.trim()      !== changes.newValues.strValor6Det.trim())         
                    {                                       
                        //Valida que el valor ingresado no sea mayor a 10000.
                        var  intValor6 = parseInt(changes.newValues.strValor6Det.trim());
                        if ( intValor6 > 10000 )
                        {
                            Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, Debe ser Menor o Igual a 10000');
                            var recValor6 = Ext.getCmp('gridParametrosDetEquipos').getStore().getAt(changes.rowIdx);
                            recValor6.set('strValor6Det', changes.originalValues.strValor6Det);                                                                                 
                            return false;
                        }                        
                        //Valida que el valor ingresado sea N o S.
                        if ('S' !== changes.newValues.strValor5Det.trim() && ('N' !== changes.newValues.strValor5Det.trim()))
                        {
                            Ext.Msg.alert('Error ', 'Valor ingresado no v\u00e1lido, debe ser N o S');
                            var recValor5 = Ext.getCmp('gridParametrosDetEquipos').getStore().getAt(changes.rowIdx);
                            recValor5.set('strValor5Det', changes.originalValues.strValor5Det);                            
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
                            url: urlActualizaParamEquipoDet,
                            method: 'POST',
                            timeout: 60000,
                            params: {
                                intIdParametroDet: changes.originalValues.intIdParamDet,
                                strDescripcion: changes.newValues.strDescriptionDet.trim(),
                                strValor1: changes.newValues.strValor1Det.trim(),                                
                                strValor5: changes.newValues.strValor5Det.trim(),
                                strValor6: changes.newValues.strValor6Det.trim()                                
                            },
                            success: function(response) {
                                var text = Ext.decode(response.responseText);                                                               
                                var recGrid = Ext.getCmp('gridParametrosDetEquipos').getStore().getAt(changes.rowIdx);
                                /*Cuando el strStatus enviado en el Response es != 100 el registro vuelve a sus 
                                 * valores originales.
                                 */
                                if ("100" !== text.strStatus) {                                    
                                    recGrid.set('strValor5Det', changes.originalValues.strValor5Det);                                    
                                    recGrid.set('strValor6Det', changes.originalValues.strValor6Det);                                    
                                }
                                    Ext.Msg.alert('Información', text.strMessageStatus);
                                    storeParametrosDetEquipo.load();
                                    storeParametrosHistEquipo.load();
                                    Ext.Msg.alert('Información', 'Registro(s) Modificado(s) Correctamente.');
                            },
                            failure: function(result) {
                                //Por alguna excepción no controlada el registro vuelve a sus valores originales.
                                var recGridEq = Ext.getCmp('gridParametrosDetEquipos').getStore().getAt(changes.rowIdx);
                                    recGridEq.set('strValor5Det', changes.originalValues.strValor5Det);                                    
                                    recGridEq.set('strValor6Det', changes.originalValues.strValor6Det);                             
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    } else
                    {
                        Ext.Msg.alert('Información', 'No hay cambios en el registro.');
                    }
                }//Validación de Campos.
                else {
                    Ext.Msg.alert('Alerta!',
                        'No puede insertar el regitro vacío, Debe ingresar una descripción y al menos un valor.');
                }
            }
        }
    });
              
// Crea el grid para mostrar el detalle de la cabecera de los parámetros de Equipos.
    gridParametrosDetEquipos = Ext.create('Ext.grid.Panel',{
        title: 'Detalle de Parámetros',
        store: storeParametrosDetEquipo,
        id: 'gridParametrosDetEquipos',
        cls: 'custom-grid',
        autoScroll: false,
        plugins: [rowEditingEquipos],
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParamDet', hidden: true},
            {header: 'Descripción', dataIndex:'strDescriptionDet', width: 300 },
            {header: 'Valor 1', dataIndex: 'strValor1Det', width: 150  },
            {
                header: 'Valor 2',
                dataIndex: 'strValor5Det',
                width: 100,                
                editor: {
                    allowBlank: false,
                    maskRe: /^([A-Z])/ ,	
                    regex : /^([A-Z]{1,1})$/                    
                }
            },
            {
                header: 'Valor 3',
                dataIndex: 'strValor6Det',
                width: 100,                
                editor: {
                         allowBlank: false,
                         maskRe: /[0-9]/, 
                         regex: /^[1-9]+([0-9]+)?$/
                }
            },            
            {header: 'Estado', dataIndex: 'strStateDet', width: 100  },
            {header: 'Usr. Creación', dataIndex:'strUserDet', width: 100 },
            {header: 'Fe. Creación', dataIndex: 'strDateDet', width: 100  }
        ],
        height: 270,
        width: 1000,
        renderTo: 'ListadosDetParamEquipo',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeParametrosDetEquipo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });          
    storeParametrosDetEquipo.load();      
//Define un modelo para el store que lista el historial de los parámetros de Equipos.
    Ext.define('ListParametrosHistModel',
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdParamDet', type: 'int' },
            {name: 'intIdParamCab', type: 'int' },
            {name: 'strDescripDet', type: 'string' },
            {name: 'strValor1Hist', type: 'string' },
            {name: 'strValor2Hist', type: 'string' },
            {name: 'strValor3Hist', type: 'string' },
            {name: 'strValor4Hist', type: 'string' },
            {name: 'strValor5Hist', type: 'string' },
            {name: 'strValor6Hist', type: 'string' },
            {name: 'strStateHist' , type: 'string' },
            {name: 'strUserHist'  , type: 'string' },
            {name: 'strDateHist'  , type: 'string' },
            {name: 'strUsrUltModHist', type: 'string'   },
            {name: 'strFeUltModHist', type: 'string'    }
        ]
    });
    
    // Crea un store para obtener el historial de los parámetros de Equipos 
    // con referencia a los datos de cabecera.    
    storeParametrosHistEquipo = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
        model: 'ListParametrosHistModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetStoreParametrosHistEquipo,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParamHistResultEquipo',
                totalProperty: 'intTotalParam'
            }
        }
    });
        
    storeParametrosHistEquipo.load();

// Crea el grid para mostrar el historial de edición de los parámetros de instalación.
    gridParametrosHistEquipo = Ext.create('Ext.grid.Panel',
    {
        title: 'Historial de Par\u00e1metros',
        store: storeParametrosHistEquipo,
        id: 'gridParametrosHistEquipo',
        cls: 'custom-grid',
        autoScroll: false,
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroHist', hidden: true },
            {
                header: 'Detalle',
                dataIndex: 'strDescripDet',
                width: 500
            },
            {header: 'Usr. Modifica.', dataIndex: 'strUsrUltModHist', width: 150 },
            {header: 'Fe. Modifica.', dataIndex: 'strFeUltModHist', width: 150   }
        ],
        height: 270,
        width: 1000,
        renderTo: 'ListadoHistParamEquipo',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeParametrosHistEquipo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });    
//TABLAS DE AMORTIZACIÓN INSTALACIÓN 24 MESES.  
    storeParametrosAmortizacionInst = Ext.create('Ext.data.Store',
    {
        pageSize: 2,
        model: 'ListaParametrosDetModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetValoresAmortizacionInst24,
            timeout: 90000,
            reader: {
              type: 'json',
              root: 'jsonAdmiParametroDetResult',
              totalProperty: 'intTotalParametros'
            },
            simpleSortMode: true
        }
    });
    var intPermanenciaMin24 = 0;
    var intPermanenciaMin36 = 0;
    Ext.Ajax.request({
        url: urlGetPermanenciaMin,
        method: 'post',
        timeout: 99999,
        async: false,       
        success: function(response){
            var objRespuesta     = Ext.JSON.decode(response.responseText);
            intPermanenciaMin24  = parseInt(objRespuesta.intPerMin24);
            intPermanenciaMin36  = parseInt(objRespuesta.intPerMin36);
        },
        failure: function(response)
        {
            Ext.Msg.alert('Error ','Error: ' + response.statusText);
        }
    });

    gridTablaAmortizacion24 = Ext.create('Ext.grid.Panel',{
        title: 'AMORTIZACIÓN INSTALACIÓN 24 MESES',
        store: storeParametrosAmortizacionInst,
        id: 'gridTablaAmortizacion24',
        cls: 'custom-grid',
        autoScroll: false,        
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {header: 'Descripción', dataIndex:'strValor2', width: 150 },
            {header: 'Tecnología', dataIndex: 'strValor1', width: 100  },
            {header: 'Medio', dataIndex: 'strValor3', width: 50  },
            {header: 'Mes 1', dataIndex: 'strValor3', width: 50  },
            {header: 'Precio', dataIndex: 'strValor3', width: 50, renderer: function(record){precioInst = record; }, hidden:true },            
            {header:'Mes 2',width: 50,  renderer: function(record){return CalculoAmortizacion(1,intPermanenciaMin24)}},
            {header:'Mes 3',width: 50,  renderer: function(record){return CalculoAmortizacion(2,intPermanenciaMin24)}},
            {header:'Mes 4',width: 50,  renderer: function(record){return CalculoAmortizacion(3,intPermanenciaMin24)}},          
            {header:'Mes 5',width: 50,  renderer: function(record){return CalculoAmortizacion(4,intPermanenciaMin24)}},
            {header:'Mes 6',width: 50,  renderer: function(record){return CalculoAmortizacion(5,intPermanenciaMin24)}},
            {header:'Mes 7',width: 50,  renderer: function(record){return CalculoAmortizacion(6,intPermanenciaMin24)}},
            {header:'Mes 8',width: 50,  renderer: function(record){return CalculoAmortizacion(7,intPermanenciaMin24)}},
            {header:'Mes 9',width: 50,  renderer: function(record){return CalculoAmortizacion(8,intPermanenciaMin24)}},
            {header:'Mes 10',width: 50,  renderer: function(record){return CalculoAmortizacion(9,intPermanenciaMin24)}},
            {header:'Mes 11',width: 50,  renderer: function(record){return CalculoAmortizacion(10,intPermanenciaMin24)}},
            {header:'Mes 12',width: 50,  renderer: function(record){return CalculoAmortizacion(11,intPermanenciaMin24)}},
            {header:'Mes 13',width: 50,  renderer: function(record){return CalculoAmortizacion(12,intPermanenciaMin24)}},
            {header:'Mes 14',width: 50,  renderer: function(record){return CalculoAmortizacion(13,intPermanenciaMin24)}},
            {header:'Mes 15',width: 50,  renderer: function(record){return CalculoAmortizacion(14,intPermanenciaMin24)}},
            {header:'Mes 16',width: 50,  renderer: function(record){return CalculoAmortizacion(15,intPermanenciaMin24)}},
            {header:'Mes 17',width: 50,  renderer: function(record){return CalculoAmortizacion(16,intPermanenciaMin24)}},
            {header:'Mes 18',width: 50,  renderer: function(record){return CalculoAmortizacion(17,intPermanenciaMin24)}},
            {header:'Mes 19',width: 50,  renderer: function(record){return CalculoAmortizacion(18,intPermanenciaMin24)}},
            {header:'Mes 20',width: 50,  renderer: function(record){return CalculoAmortizacion(19,intPermanenciaMin24)}},
            {header:'Mes 21',width: 50,  renderer: function(record){return CalculoAmortizacion(20,intPermanenciaMin24)}},
            {header:'Mes 22',width: 50,  renderer: function(record){return CalculoAmortizacion(21,intPermanenciaMin24)}},
            {header:'Mes 23',width: 50,  renderer: function(record){return CalculoAmortizacion(22,intPermanenciaMin24)}},
            {header:'Mes 24',width: 50,  renderer: function(record){return CalculoAmortizacion(23,intPermanenciaMin24)}}
        ],
        height: 140,
        width: 1100,
        renderTo: 'TablaAmortizacion24Inst',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeParametrosAmortizacionInst,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        
    });

storeParametrosAmortizacionInst.load();      
   // TABLA DE AMORTIZACIÓN EQUIPOS 24 MESES.
    storeParametrosAmortizacionEquipo = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
        model: 'ParametrosDetModelEquipos',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetValoresAmortizacionEquipo24,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParamDetResult',
                totalProperty: 'intTotalParam'
            },
            simpleSortMode: true
        }
    });   
               
// Crea el grid para mostrar el detalle de la cabecera de los parámetros de Equipos.
    gridTablaAmortizacion24Equipo = Ext.create('Ext.grid.Panel',{
        title: 'AMORTIZACIÓN EQUIPOS 24 MESES',
        store: storeParametrosAmortizacionEquipo,
        id: 'gridTablaAmortizacion24Equipo',
        cls: 'custom-grid',
        autoScroll: false,        
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParamDet', hidden: true},
            {header: 'Descripción', dataIndex:'strDescriptionDet', width: 200 },
            {header: 'Tecnología', dataIndex: 'strValor1Det', width: 100  },
            {header: 'Precio', dataIndex: 'strValor6Det', width: 50  },
            {header: 'Mes 1', dataIndex: 'strValor6Det', width: 50  },
            {header: 'Precio', dataIndex: 'strValor6Det', width: 50, renderer: function(record){precioEquipo = record; }, hidden:true },            
            {header:'Mes 2',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(1,intPermanenciaMin24)}},
            {header:'Mes 3',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(2,intPermanenciaMin24)}},
            {header:'Mes 4',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(3,intPermanenciaMin24)}},          
            {header:'Mes 5',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(4,intPermanenciaMin24)}},
            {header:'Mes 6',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(5,intPermanenciaMin24)}},
            {header:'Mes 7',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(6,intPermanenciaMin24)}},
            {header:'Mes 8',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(7,intPermanenciaMin24)}},
            {header:'Mes 9',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(8,intPermanenciaMin24)}},
            {header:'Mes 10',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(9,intPermanenciaMin24)}},
            {header:'Mes 11',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(10,intPermanenciaMin24)}},
            {header:'Mes 12',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(11,intPermanenciaMin24)}},
            {header:'Mes 13',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(12,intPermanenciaMin24)}},
            {header:'Mes 14',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(13,intPermanenciaMin24)}},
            {header:'Mes 15',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(14,intPermanenciaMin24)}},
            {header:'Mes 16',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(15,intPermanenciaMin24)}},
            {header:'Mes 17',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(16,intPermanenciaMin24)}},
            {header:'Mes 18',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(17,intPermanenciaMin24)}},
            {header:'Mes 19',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(18,intPermanenciaMin24)}},
            {header:'Mes 20',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(19,intPermanenciaMin24)}},
            {header:'Mes 21',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(20,intPermanenciaMin24)}},
            {header:'Mes 22',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(21,intPermanenciaMin24)}},
            {header:'Mes 23',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(22,intPermanenciaMin24)}},
            {header:'Mes 24',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(23,intPermanenciaMin24)}}  
        ],
        height: 400,
        width: 1100,
        renderTo: 'TablaAmortizacion24Equipo',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeParametrosAmortizacionEquipo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        
    });

storeParametrosAmortizacionEquipo.load(); 
//TABLAS DE AMORTIZACIÓN INSTALACIÓN 36 MESES.  
    storeAmortizacionInst = Ext.create('Ext.data.Store',
    {
        pageSize: 2,
        model: 'ListaParametrosDetModel',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetValoresAmortizacionInst36,
            timeout: 90000,
            reader: {
              type: 'json',
              root: 'jsonAdmiParametroDetResult',
              totalProperty: 'intTotalParametros'
            },
            simpleSortMode: true
        }
    });                  

    gridTablaInstalacion36 = Ext.create('Ext.grid.Panel',{
        title: 'AMORTIZACIÓN INSTALACIÓN 36 MESES',
        store: storeAmortizacionInst,
        id: 'gridTablaInstalacion36',
        cls: 'custom-grid',
        autoScroll: false,        
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParametroDet', hidden: true},
            {header: 'Descripción', dataIndex:'strValor2', width: 150 },
            {header: 'Medio', dataIndex: 'strValor1', width: 100  },
            {header: 'Precio', dataIndex: 'strValor3', width: 50  },
            {header: 'Mes 1', dataIndex: 'strValor3', width: 50  },
            {header: 'Precio', dataIndex: 'strValor3', width: 50, renderer: function(record){precioInst = record; }, hidden:true },            
            {header:'Mes 2',width: 50,  renderer: function(record){return CalculoAmortizacion(1,intPermanenciaMin36)}},
            {header:'Mes 3',width: 50,  renderer: function(record){return CalculoAmortizacion(2,intPermanenciaMin36)}},
            {header:'Mes 4',width: 50,  renderer: function(record){return CalculoAmortizacion(3,intPermanenciaMin36)}},          
            {header:'Mes 5',width: 50,  renderer: function(record){return CalculoAmortizacion(4,intPermanenciaMin36)}},
            {header:'Mes 6',width: 50,  renderer: function(record){return CalculoAmortizacion(5,intPermanenciaMin36)}},
            {header:'Mes 7',width: 50,  renderer: function(record){return CalculoAmortizacion(6,intPermanenciaMin36)}},
            {header:'Mes 8',width: 50,  renderer: function(record){return CalculoAmortizacion(7,intPermanenciaMin36)}},
            {header:'Mes 9',width: 50,  renderer: function(record){return CalculoAmortizacion(8,intPermanenciaMin36)}},
            {header:'Mes 10',width: 50,  renderer: function(record){return CalculoAmortizacion(9,intPermanenciaMin36)}},
            {header:'Mes 11',width: 50,  renderer: function(record){return CalculoAmortizacion(10,intPermanenciaMin36)}},
            {header:'Mes 12',width: 50,  renderer: function(record){return CalculoAmortizacion(11,intPermanenciaMin36)}},
            {header:'Mes 13',width: 50,  renderer: function(record){return CalculoAmortizacion(12,intPermanenciaMin36)}},
            {header:'Mes 14',width: 50,  renderer: function(record){return CalculoAmortizacion(13,intPermanenciaMin36)}},
            {header:'Mes 15',width: 50,  renderer: function(record){return CalculoAmortizacion(14,intPermanenciaMin36)}},
            {header:'Mes 16',width: 50,  renderer: function(record){return CalculoAmortizacion(15,intPermanenciaMin36)}},
            {header:'Mes 17',width: 50,  renderer: function(record){return CalculoAmortizacion(16,intPermanenciaMin36)}},
            {header:'Mes 18',width: 50,  renderer: function(record){return CalculoAmortizacion(17,intPermanenciaMin36)}},
            {header:'Mes 19',width: 50,  renderer: function(record){return CalculoAmortizacion(18,intPermanenciaMin36)}},
            {header:'Mes 20',width: 50,  renderer: function(record){return CalculoAmortizacion(19,intPermanenciaMin36)}},
            {header:'Mes 21',width: 50,  renderer: function(record){return CalculoAmortizacion(20,intPermanenciaMin36)}},
            {header:'Mes 22',width: 50,  renderer: function(record){return CalculoAmortizacion(21,intPermanenciaMin36)}},
            {header:'Mes 23',width: 50,  renderer: function(record){return CalculoAmortizacion(22,intPermanenciaMin36)}},
            {header:'Mes 24',width: 50,  renderer: function(record){return CalculoAmortizacion(23,intPermanenciaMin36)}}, 
            {header:'Mes 25',width: 50,  renderer: function(record){return CalculoAmortizacion(24,intPermanenciaMin36)}}, 
            {header:'Mes 26',width: 50,  renderer: function(record){return CalculoAmortizacion(25,intPermanenciaMin36)}},
            {header:'Mes 27',width: 50,  renderer: function(record){return CalculoAmortizacion(26,intPermanenciaMin36)}},
            {header:'Mes 28',width: 50,  renderer: function(record){return CalculoAmortizacion(27,intPermanenciaMin36)}},
            {header:'Mes 29',width: 50,  renderer: function(record){return CalculoAmortizacion(28,intPermanenciaMin36)}},
            {header:'Mes 30',width: 50,  renderer: function(record){return CalculoAmortizacion(29,intPermanenciaMin36)}},
            {header:'Mes 31',width: 50,  renderer: function(record){return CalculoAmortizacion(30,intPermanenciaMin36)}},
            {header:'Mes 32',width: 50,  renderer: function(record){return CalculoAmortizacion(31,intPermanenciaMin36)}},
            {header:'Mes 33',width: 50,  renderer: function(record){return CalculoAmortizacion(32,intPermanenciaMin36)}},
            {header:'Mes 34',width: 50,  renderer: function(record){return CalculoAmortizacion(33,intPermanenciaMin36)}},
            {header:'Mes 35',width: 50,  renderer: function(record){return CalculoAmortizacion(34,intPermanenciaMin36)}},
            {header:'Mes 36',width: 50,  renderer: function(record){return CalculoAmortizacion(35,intPermanenciaMin36)}}            
            
        ],
        height: 140,
        width: 1100,
        renderTo: 'TablaAmortizacion36Inst',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeAmortizacionInst,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        
    });
storeAmortizacionInst.load();   

   // TABLA DE AMORTIZACIÓN EQUIPOS 36 MESES.
    storeAmortizacionEquipo = Ext.create('Ext.data.Store',
    {
        pageSize: 20,
        model: 'ParametrosDetModelEquipos',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlGetValoresAmortizacionEquipo36,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonAdmiParamDetResult',
                totalProperty: 'intTotalParam'
            },
            simpleSortMode: true
        }
    });   
               
// Crea el grid para mostrar el detale de la cabecera de los parámetros de Equipos.

    gridTablaEquipos36 = Ext.create('Ext.grid.Panel',{
        title: 'AMORTIZACIÓN EQUIPOS 36 MESES',
        store: storeAmortizacionEquipo,
        id: 'gridTablaEquipos36',
        cls: 'custom-grid',
        autoScroll: false,        
        dockedItems: [toolbar],
        columns: [
            {header: "ID", dataIndex: 'intIdParamDet', hidden: true},
            {header: 'Descripción', dataIndex:'strDescriptionDet', width: 200 },
            {header: 'Tecnologia', dataIndex: 'strValor1Det', width: 100  },
            {header: 'Precio', dataIndex: 'strValor6Det', width: 50  },
            {header: 'Mes 1', dataIndex: 'strValor6Det', width: 50  },
            {header: 'Precio', dataIndex: 'strValor6Det', width: 50, renderer: function(record){precioEquipo = record; }, hidden:true },            
            {header:'Mes 2',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(1,intPermanenciaMin36)}},
            {header:'Mes 3',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(2,intPermanenciaMin36)}},
            {header:'Mes 4',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(3,intPermanenciaMin36)}},          
            {header:'Mes 5',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(4,intPermanenciaMin36)}},
            {header:'Mes 6',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(5,intPermanenciaMin36)}},
            {header:'Mes 7',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(6,intPermanenciaMin36)}},
            {header:'Mes 8',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(7,intPermanenciaMin36)}},
            {header:'Mes 9',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(8,intPermanenciaMin36)}},
            {header:'Mes 10',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(9,intPermanenciaMin36)}},
            {header:'Mes 11',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(10,intPermanenciaMin36)}},
            {header:'Mes 12',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(11,intPermanenciaMin36)}},
            {header:'Mes 13',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(12,intPermanenciaMin36)}},
            {header:'Mes 14',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(13,intPermanenciaMin36)}},
            {header:'Mes 15',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(14,intPermanenciaMin36)}},
            {header:'Mes 16',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(15,intPermanenciaMin36)}},
            {header:'Mes 17',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(16,intPermanenciaMin36)}},
            {header:'Mes 18',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(17,intPermanenciaMin36)}},
            {header:'Mes 19',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(18,intPermanenciaMin36)}},
            {header:'Mes 20',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(19,intPermanenciaMin36)}},
            {header:'Mes 21',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(20,intPermanenciaMin36)}},
            {header:'Mes 22',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(21,intPermanenciaMin36)}},
            {header:'Mes 23',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(22,intPermanenciaMin36)}},
            {header:'Mes 24',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(23,intPermanenciaMin36)}}, 
            {header:'Mes 25',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(24,intPermanenciaMin36)}}, 
            {header:'Mes 26',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(25,intPermanenciaMin36)}},
            {header:'Mes 27',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(26,intPermanenciaMin36)}},
            {header:'Mes 28',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(27,intPermanenciaMin36)}},
            {header:'Mes 29',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(28,intPermanenciaMin36)}},
            {header:'Mes 30',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(29,intPermanenciaMin36)}},
            {header:'Mes 31',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(30,intPermanenciaMin36)}},
            {header:'Mes 32',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(31,intPermanenciaMin36)}},
            {header:'Mes 33',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(32,intPermanenciaMin36)}},
            {header:'Mes 34',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(33,intPermanenciaMin36)}},
            {header:'Mes 35',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(34,intPermanenciaMin36)}},
            {header:'Mes 36',width: 50,  renderer: function(record){return CalculoAmortizacionEquipo(35,intPermanenciaMin36)}}                        
        ],
        height: 400,
        width: 1100,
        renderTo: 'TablaAmortizacion36Equipo',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeAmortizacionEquipo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        
    });

storeAmortizacionEquipo.load(); 
// Calcula los valores amortizados por Instalación.
   function CalculoAmortizacion(intMes,intvigencia)
   {        
       var fltTotal = 0, precio = precioInst ;        
       fltTotal   = precio - (precio/intvigencia) * (intMes);    
       fltTotal = (fltTotal.toFixed(2));
       return fltTotal ;
    }        
// Calcula los valores amortizados por Equipo.
   function CalculoAmortizacionEquipo(intMes,intvigencia)
   {          
       var fltTotal = 0, precio = precioEquipo ;        
       fltTotal   = precio - (precio/intvigencia) * (intMes);    
       fltTotal = (fltTotal.toFixed(2));
       return fltTotal ;
    }       
 
});
