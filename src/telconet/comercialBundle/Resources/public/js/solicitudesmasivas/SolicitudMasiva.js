/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function SolicitudMasiva() {

    //tamaños de los store y tiempos de solicitudes ajax
    this.intPageSize = 25;
    this.timeout = 90000;

    //Expresiones Regulares validaciones y mascaras
    this.strCadenaRegex     = /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/;
    this.strCadenaMask      = /^([A-Za-z]+[A-Za-z0-9\_\-]*)$/;
    this.strIntegerRegex    = /^([0-9]+[0-9]*)$/;
    this.strIntegerMask     = /^([0-9]+[0-9]*)$/;
    this.strDecimalRegex    = /^\d+(\.\d{1,2})?$/;
    this.strDecimalMask     = /[\d\.]/;

    //footer paginator messages
    this.displayMsg = '{0} - {1} de {2}';
    this.emptyMsg = 'No hay datos que mostrar.';
    
    /**
     * alert, muestra un messagebox con un mensaje 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 22-05-2016
     * @since 1.0
     * @param string  strStatus titulo
     * @param string  strMessage mensaje
     */
    this.alert = function(strStatus, strMessage)
    {
        var msg = Ext.create('Ext.window.MessageBox');	
        msg.alert(strStatus, strMessage);
    }
    
    /**
     * wait, muestra un messagebox con un mensaje 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 22-05-2016
     * @since 1.0
     * @param string  strStatus titulo
     * @param string  strMessage mensaje
     */
    this.wait = function(strStatus, strMessage)
    {
        var msg = Ext.create('Ext.window.MessageBox');	
        msg.wait(strStatus, strMessage);
        return msg;
    }

    /**
     * styleBold, el valor en tag <b> para mostrarlo en negritas en la tabla
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param string  strValue valor a ser formateado
     * @return string cadena formateada con el tag <b>
     */
    this.styleBold = function (strValue) {
        return '<b>' + strValue + '</b>';
    }
    
    /**
     * styleDollar, el valor en tag <b> para mostrarlo en negritas en la tabla y con el signo de dolar
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param string  strValue valor a ser formateado
     * @return string cadena formateada con el tag <b> y el signo de dolar
     */
    this.styleDollar = function (strValue) {
        return '<b>$ ' + strValue + '</b>';
    }

    /**
     * estadoChange, el valor en tag <span> para mostrarlo con formato depende del valor pasado
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param string  strValue valor a ser formateado
     * @return string cadena formateada con el tag <span>
     */
    this.estadoChange = function (strValue) {
        var strColor = '';
        switch (strValue) {
            case 'Aprobada':
            case 'EnProceso':
                strColor = 'green';
                break;
            case 'Rechazada':
                strColor = 'red';
                break;
            case 'Eliminada':
                strColor = 'wine';
                break;
            default:
                strColor = "blue";
                break;
        }
        return '<span class="bold color-' + strColor + '">' + strValue + '</span>';
    }

    /**
     * tituloMensajeBox, devuelve un titulo a ser puesto en los mensajes de respuestas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param string  strValue valor del codigo retornado del controlador
     * @return string cadena con el titulo correspondiente
     */
    this.tituloMensajeBox = function (strValue) {
        var strTipo = '';
        switch (strValue) {
            case '100':
                strTipo = 'Información';
                break;
            case '001':
                strTipo = 'Error';
                break;
            case '000':
            default:
                strTipo = 'Alerta';
                break;
        }
        return strTipo;
    }

    /**
     * ArrayToStr, devuelve una cadena de caracteres concatenando los valores del array con un separador
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-04-2016
     * @since 1.0
     * @param string  strValue valor del codigo retornado del controlador
     * @return string cadena de valores concatenados con el separador
     */
    this.arrayToStr = function (arrayValores, separador) {
        var strCadena = "";
        for (var intForIndex = 0; intForIndex < arrayValores.length; intForIndex++) {
            strCadena = strCadena + arrayValores[intForIndex];
            if (intForIndex < (arrayValores.length - 1)) {
                strCadena += separador;
            }
        }
        return strCadena;
    }

    /**
     * removeItemsSelectedFromArray, quita los elementos seleccionados en el Objeto chkBoxModel del arrayElementos
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-04-2016
     * @since 1.0
     * @param Object  chkBoxModel coleccion de checkbox seleccionados de una tabla
     * @param array  arrayElementos arreglo de identificadores seleccionados
     */
    this.removeItemsSelectedFromArray = function (chkBoxModel, arrayElementos) {
        for (var i = 0; i < chkBoxModel.getSelection().length; i++) {
            var value = chkBoxModel.getSelection()[i].data.intIdServicio;
            if (arrayElementos !== null) {
                arrayElementos.splice(arrayElementos.indexOf(value), 1);
            }
        }
    }

    /**
     * itemsSelectedToArray, quita los elementos seleccionados en el Objeto chkBoxModel del arrayElementos
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 15-04-2016
     * @since 1.0
     * @param Object  chkBoxModel coleccion de checkbox seleccionados de una tabla
     * @param array  arrayElementos arreglo de identificadores seleccionados
     */
    this.itemsSelectedToArray = function (chkBoxModel, idElement) {
        var arrayResultado = new Array();
        //Itera los chkBox y concatena los ID Servicios en un solo string strIdServicios
        for (var intForIndex = 0; intForIndex < chkBoxModel.getSelection().length; intForIndex++) {
            arrayResultado[intForIndex] = chkBoxModel.getSelection()[intForIndex].data[idElement];
        }
        return arrayResultado;
    }
    
    /**
     * storeLoad, Al ejecutar el Load de un store muestra una alerta por algun error inesperado
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 21-04-2016
     * @since 1.0
     * @param Object  chkBoxModel coleccion de checkbox seleccionados de una tabla
     * @param array  arrayElementos arreglo de identificadores seleccionados
     */
    this.storeLoad = function (store) {
        var strMensajeError = store.getProxy().getReader().rawData.strMensajeError;
        if ( typeof strMensajeError !== "undefined" && strMensajeError !== "" && strMensajeError !== null) {
            this.alert('Error', strMensajeError);
        }
    }
    
    /**
     * downloadArchivo, descarga el archivo adjunto desde el servidor
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 21-04-2016
     * @since 1.0
     * @param string  strUrl direccion del archivo adjunto a descargar
     */
   this.downloadArchivo = function(strUrl) {
       
//       Ext.Ajax.request({ 
//            url : urlDownloadArchivo,
//            progressText: 'Descargando Archivo Adjunto...',
//            params: {strUrl: strUrl}, 
//            success: function ( resp, opt ) { }  
//        }); 
       
       if(strUrl !== ''){
           window.location = urlDownloadArchivo+'?strUrl=' + strUrl;
       }else{
           this.alert('Error', 'Documento Adjunto no existe!');
       }
   }


    // FUNCIONES EXTJS

    /**
     * crearToolTip, crea tooltips para cada celda de la tabla basada en el contenido de las mismas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param Object ObjGrid Tabla a la cual se desea poner tooltips en sus celdas
     */
    this.crearToolTip = function (ObjGrid) {
        var view = ObjGrid.view;
        // Registreo de la celda actual
        ObjGrid.mon(view, {
            uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                ObjGrid.cellIndex = cellIndex;
                ObjGrid.recordIndex = recordIndex;
            }
        });
        ObjGrid.tip = Ext.create('Ext.tip.ToolTip', {
            target: view.el,
            delegate: '.x-grid-cell',
            trackMouse: true,
            renderTo: Ext.getBody(),
            listeners: {
                beforeshow: function updateTipBody(tip) {
                    if (!Ext.isEmpty(ObjGrid.cellIndex) && ObjGrid.cellIndex !== -1) {
                        header = ObjGrid.headerCt.getGridColumns()[ObjGrid.cellIndex];
                        tip.update(ObjGrid.getStore().getAt(ObjGrid.recordIndex).get(header.dataIndex));
                    }
                }
            }
        });
    }

    /**
     * showHistorialSolicitudesMasivas, que crea una ventana emergente con el historial de una solicitud
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param integer intIdSolicitud identificador para del que se quiere el historial
     */
    this.showHistorialSolicitudesMasivas = function (intIdSolicitud)
    {
        var ventana = Ext.getCmp('windowHistorialSolicitud');
        if(ventana != null){
            ventana.close();
            ventana.destroy();
        }        
        
        //Define un modelo para el store storeSolicitudesMasivasHistorial
        Ext.define('modelListaSolicitudesMasivasHistorial', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdSolicitudHistorial', type: 'int'},
                {name: 'strFeCreacion', type: 'string'},
                {name: 'strUsrCreacion', type: 'string'},
                {name: 'intIdDetalleSolicitud', type: 'int'},
                {name: 'strObservacion', type: 'string'},
                {name: 'strFeIniPlan', type: 'string'},
                {name: 'strFeFinPlan', type: 'string'},
                {name: 'strEstado', type: 'string'},
                {name: 'intIdMotivo', type: 'int'},
                {name: 'strNombreMotivo', type: 'string'}
            ]
        });

        //Store que realiza la petición ajax para el grid: gridListaSolicitudesMasivasHistorial
        var storeSolicitudesMasivasHistorial = "";
        storeSolicitudesMasivasHistorial = Ext.create('Ext.data.Store', {
            pageSize: this.intPageSize,
            model: 'modelListaSolicitudesMasivasHistorial',
            autoLoad: true,
            proxy: {
                timeout: 60000,
                type: 'ajax',
                url: urlGetSolicitudesMasivasHistorial,
                timeout: this.timeout,
                reader: {
                    type: 'json',
                    root: 'jsonSolicitudesMasivasHistorial',
                    totalProperty: 'total'
                },
                extraParams: {
                    intIdSolicitudMasiva: intIdSolicitud
                },
                simpleSortMode: true
            }
        });

        //Crea el grid que muestra la información obtenida desde el controlador  del Historial de Solicitudes.
        var gridListaSolicitudesMasivasHistorial = Ext.create('Ext.grid.Panel', {
            store: storeSolicitudesMasivasHistorial,
            id: 'gridListaSolicitudesMasivasHistorial',
            viewConfig: {enableTextSelection: true},
            columns: [
                {
                    id: 'intIdSolicitudHistorialH',
                    header: 'IdSolicitudHistorial',
                    dataIndex: 'intIdSolicitudHistorial',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'intIdDetalleSolicitudH',
                    header: 'IdDetalleSolicitud',
                    dataIndex: 'intIdDetalleSolicitud',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'strFeCreacionH',
                    header: 'F. Creación',
                    dataIndex: 'strFeCreacion',
                    width: 120,
                    renderer: this.styleBold,
                    sortable: true
                },
                {
                    id: 'strUsrCreacionH',
                    header: 'Responsable',
                    dataIndex: 'strUsrCreacion',
                    width: 80,
                    sortable: true
                },
                {
                    id: 'strObservacionH',
                    header: 'Observacion',
                    dataIndex: 'strObservacion',
                    width: 210
                },
                {
                    id: 'strFeIniPlanH',
                    header: 'F. Plan. Inicial',
                    dataIndex: 'strFeIniPlan',
                    width: 120,
                    sortable: true
                },
                {
                    id: 'strFeFinPlanH',
                    header: 'F. Plan. Final',
                    dataIndex: 'strFeFinPlan',
                    width: 120,
                    sortable: true
                },
                {
                    id: 'strEstadoHistoricoH',
                    header: 'Estado',
                    dataIndex: 'strEstado',
                    width: 100,
                    renderer: this.estadoChange,
                    sortable: true
                },
                {
                    id: 'strNombreMotivoH',
                    header: 'Motivo',
                    dataIndex: 'strNombreMotivo',
                    width: 230
                }
            ],
            height: 400,
            width: 980,
            listeners: {
                viewready: this.crearToolTip
            },
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeSolicitudesMasivasHistorial,
                displayInfo: true,
                displayMsg: this.displayMsg,
                emptyMsg: this.emptyMsg
            })
        });

        var winHistorialSolicitudesMasivas = Ext.widget('window', {
            id : 'windowHistorialSolicitud',
            title: 'Historial de la Solicitud: ' + intIdSolicitud,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: true,
            items: [gridListaSolicitudesMasivasHistorial]
        });
        winHistorialSolicitudesMasivas.show();
    }

    /**
     * showDeleteSolicitudesMasivas, Elimina logicamente la solicitud masiva, sus detalles y caracteristicas
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param integer intIdSolicitud identificador para del que se quiere eliminar
     */
    this.showDeleteSolicitudesMasivas = function (rec) {
        var intIdSolicitud = rec.get('intIdSolicitud');
        var strTipoSolicitud = rec.get('strTipoSolicitud');
        var strEstado = rec.get('strEstado');
        Ext.Msg.confirm('Alerta', 'Se Eliminará la Solicitud Masiva de ' + strTipoSolicitud + ' con código: ' + intIdSolicitud + ' . Desea continuar?', function (btn) {
            if (btn == 'yes') {
                var entidadSolicitudMasiva = new SolicitudMasiva();
                if (strEstado == 'Pendiente') {                    
                    var box = entidadSolicitudMasiva.wait('Procesando...', 'Solicitudes Masivas');
                    Ext.Ajax.request({
                        url: urlDeleteSolicitudMasiva,
                        method: 'post',
                        params: {
                            intIdSolicitud: intIdSolicitud
                        },
                        success: function (response) {
                            var text = Ext.decode(response.responseText);
                            if ("100" === text.strStatus) {
                                storeSolicitudesMasivas.load();
                                box.destroy();
                            }
                            entidadSolicitudMasiva.alert(this.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                        },
                        failure: function (response)
                        {
                            var text = Ext.decode(response.responseText);
                            box.destroy();
                            entidadSolicitudMasiva.alert(this.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                        }
                    });
                } else {
                    entidadSolicitudMasiva.alert('Error - Solicitud (' + intIdSolicitud + ')', 'Solo se puede eliminar una solicitud en estado: Pendiente');
                }
            }
        });
    }
    
    /**
     * aprobarSolicitudes, muestra una ventana emergente que pide confirmacion para la aprobacion de las solicitudes detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 25-04-2016
     * @since 1.0
     */
    this.aprobarSolicitudes = function (intIdDetalleSolicitudCab, arraySolicitudesSeleccionadas, storeToReload)
    {
        
        var ventana = Ext.getCmp('windowAprobarSolicitud');
        if(ventana != null){
            ventana.close();
            ventana.destroy();
        } 

        var strIdSolicitudesSeleccionadas = entidadSolicitudMasiva.arrayToStr(arraySolicitudesSeleccionadas, ',');
        var strPregunta = "</br><b>Total de Solicitudes a Aprobar: </b>" + arraySolicitudesSeleccionadas.length;
        strPregunta += "</br></br><b>¿Seguro desea Aprobar la(s) Solicitud(es) marcada(s)?</b>";

        var panelContenido = Ext.create('Ext.panel.Panel', {
            border: false,
            layout: {
                tdAttrs: {style: 'padding: 5px;'},
                type: 'table',
                columns: 1,
                align: 'center'
            },
            items: [
                {id: 'boxDatos', xtype: 'box', border: false, autoEl: {cn: strPregunta}}
            ],
            buttons: [
                {
                    text: '<b>Aprobar</b>',
                    handler: function () {                       
                        var entidadSolicitudMasiva = new SolicitudMasiva();
                        var box = entidadSolicitudMasiva.wait('Procesando...', 'Solicitudes Masivas');                        
                        Ext.Ajax.request({
                            url: urlAprobarSolicitud,
                            method: 'post',
                            params: {
                                intIdDetalleSolicitudCab: intIdDetalleSolicitudCab,
                                strIdSolicitudesSeleccionadas: strIdSolicitudesSeleccionadas
                            },
                            success: function (response) {
                                var text = Ext.decode(response.responseText);
                                if ("100" === text.strStatus) {
                                    storeToReload.load();                                    
                                    arraySolicitudesSeleccionadas = new Array();                                                                       
                                    entidadSolicitudMasiva.initSeguimiento(intIdDetalleSolicitudCab, 'seguimiento_content');
                                }
                                winAprobarSolicitudes.close();
                                winAprobarSolicitudes.destroy(); 
                                box.destroy();
                                entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                            },
                            failure: function (response)
                            {
                                var text = Ext.decode(response.responseText);
                                entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                                winAprobarSolicitudes.close();
                                winAprobarSolicitudes.destroy();
                                box.destroy();
                            }
                        });
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function () {
                        winAprobarSolicitudes.close();
                        winAprobarSolicitudes.destroy();
                    }
                }
            ]
        });
        
        var winAprobarSolicitudes = "";
        winAprobarSolicitudes = Ext.widget('window', {
            id : 'windowAprobarSolicitud',
            title: 'Aprobar Solicitudes',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: true,
            items: [
                panelContenido
            ]
        });
        winAprobarSolicitudes.show();
    }

    /**
     * rechazarSolicitudes, muestra una ventana emergente que pide un motivo para 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 14-04-2016
     * @since 1.0
     * @param array arraySolicitudesSeleccionadas arreglo de identificadores de solicitudes a ser rechazadas
     */
    this.rechazarSolicitudes = function (intIdDetalleSolicitudCab, arraySolicitudesSeleccionadas, storeToReload)
    {

        var strIdSolicitudesSeleccionadas = entidadSolicitudMasiva.arrayToStr(arraySolicitudesSeleccionadas, ',');

        var strPregunta = "</br><b>Total de Solicitudes a Rechazar: </b>" + arraySolicitudesSeleccionadas.length;
        strPregunta += "</br></br><b>¿Seguro desea Rechazar la(s) Solicitud(es) marcada(s)?</b>";

        var txtAreaMotivo = Ext.create('Ext.form.field.TextArea',
            {
                id: 'txtAreaMotivo',
                name: 'txtAreaMotivo',
                fieldLabel: 'Escriba un Motivo:',
                labelAlign: 'top',
                allowBlank: true,
                width: '99%'
            });

        var panelContenido = Ext.create('Ext.panel.Panel', {
            border: false,
            layout: {
                tdAttrs: {style: 'padding: 5px;'},
                type: 'table',
                columns: 1,
                align: 'center'
            },
            items: [
                {id: 'boxDatos', xtype: 'box', border: false, autoEl: {cn: strPregunta}},
                txtAreaMotivo
            ],
            buttons: [
                {
                    text: '<b>Rechazar</b>',
                    handler: function () {
                        var entidadSolicitudMasiva = new SolicitudMasiva();
                        //Realiza la petición con los campos seteados en el panel de busqueda
                        if (txtAreaMotivo.getValue() === '') {
                            entidadSolicitudMasiva.alert('Alerta', 'Debe escribir un motivo válido.');
                        } else {                            
                            var box = entidadSolicitudMasiva.wait('Procesando...', 'Solicitudes Masivas');
                            Ext.Ajax.request({
                                url: urlRechazarSolicitud,
                                method: 'post',
                                params: {
                                    intIdDetalleSolicitudCab: intIdDetalleSolicitudCab,
                                    strIdSolicitudesSeleccionadas: strIdSolicitudesSeleccionadas,
                                    strMotivo: txtAreaMotivo.getValue()
                                },
                                success: function (response) {
                                    var text = Ext.decode(response.responseText);
                                    if ("100" === text.strStatus) {
                                        storeToReload.load();                                        
                                        arraySolicitudesSeleccionadas = new Array();
                                        winRechazarSolicitudes.destroy();
                                        box.destroy();
                                        entidadSolicitudMasiva.initSeguimiento(intIdDetalleSolicitudCab, 'seguimiento_content');
                                    }
                                    entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                                },
                                failure: function (response)
                                {
                                    var text = Ext.decode(response.responseText);
                                    entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                                    winRechazarSolicitudes.destroy();
                                    box.destroy();
                                }
                            });
                        }
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function () {
                        winRechazarSolicitudes.destroy();
                    }
                }
            ]
        });

        var winRechazarSolicitudes = Ext.widget('window', {
            title: 'Rechazar Solicitudes',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: true,
            items: [
                panelContenido
            ]
        });
        winRechazarSolicitudes.show();
    }

    this.subirArchivo = function (intIdDetalleSolicitud, store, parametros, txtArchivo) {
        var formPanel = Ext.create('Ext.form.Panel',
            {
                width: 500,
                frame: true,
                bodyPadding: '10 10 0',
                defaults: {
                    anchor: '100%',
                    allowBlank: false,
                    msgTarget: 'side',
                    labelWidth: 50
                },
                items: [
                    {
                        xtype: 'filefield',
                        id: 'form-file',
                        name: 'archivo',
                        fieldLabel: 'Archivo:',
                        emptyText: 'Seleccione una Archivo',
                        buttonText: 'Browse',
                        buttonConfig: {
                            iconCls: 'upload-icon'
                        }
                    }
                ],
                buttons: [{
                        text: 'Subir Documento Adjunto',
                        handler: function () {
                            var form = this.up('form').getForm();
                            if (form.isValid())
                            {
                                var entidadSolicitudMasiva = new SolicitudMasiva();
                                
                                form.submit({
                                    url: urlSubirArchivo,
                                    params: {
                                        intIdDetalleSolicitud: intIdDetalleSolicitud
                                    },
                                    waitMsg: 'Subiendo Documento Adjunto...',
                                    success: function (form, action)
                                    {
                                        if ("100" === action.result.respuesta.strStatus) {                                            
                                            if (store !== null) {                                            
                                                store.load(parametros);
                                            } else if(txtArchivo !== null)
                                            {
                                                if(action.result.respuesta.registros !=='')
                                                {
                                                    strRutaArchivo = action.result.respuesta.registros;
                                                    
                                                    var arrayArchivo = strRutaArchivo.split('/');                        
                                                    var nombreArchivo = arrayArchivo[arrayArchivo.length-1];
                                                    txtArchivo.setValue(nombreArchivo);
                                                    arrayParametrosCreacion['strRutaArchivo'] = strRutaArchivo;
                                                }
                                            }
                                            win.destroy();
                                        }
                                        entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(action.result.respuesta.strStatus), action.result.respuesta.strMessageStatus);                                       
                                    },
                                    failure: function (form, action)
                                    {
                                        entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(action.result.respuesta.strStatus), action.result.respuesta.strMessageStatus);
                                        win.destroy();
                                    }
                                });
                            }
                        }
                    }, {
                        text: 'Cancelar',
                        handler: function () {
                            this.up('form').getForm().reset();
                            win.destroy();
                        }
                    }]
            });

        var win = Ext.create('Ext.window.Window', {
            title: 'Subir Documento Adjunto',
            modal: true,
            width: 500,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
    }

    /**
     * ejecutarSolicitudes, muestra una ventana emergente que pide confirmacion para la ejecucion de las solicitudes detalle
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * @since 1.0
     */
    this.ejecutarSolicitudes = function (intIdDetalleSolicitudCab, arraySolicitudesSeleccionadas, storeToReload)
    {

        var strIdSolicitudesSeleccionadas = entidadSolicitudMasiva.arrayToStr(arraySolicitudesSeleccionadas, ',');
        var strPregunta = "</br><b>Total de Solicitudes a Ejecutar: </b>" + arraySolicitudesSeleccionadas.length;
        strPregunta += "</br></br><b>¿Seguro desea Ejecutar la(s) Solicitud(es) marcada(s)?</b>";

        var panelContenido = Ext.create('Ext.panel.Panel', {
            border: false,
            layout: {
                tdAttrs: {style: 'padding: 5px;'},
                type: 'table',
                columns: 1,
                align: 'center'
            },
            items: [
                {id: 'boxDatos', xtype: 'box', border: false, autoEl: {cn: strPregunta}}
            ],
            buttons: [
                {
                    text: '<b>Ejecutar</b>',
                    handler: function () {                       
                        var entidadSolicitudMasiva = new SolicitudMasiva();
                        var box = entidadSolicitudMasiva.wait('Por favor espere mientras se crea la solicitud...', 'Solicitudes Masivas');
                        Ext.Ajax.request({
                            url: urlEjecutarSolicitud,
                            method: 'post',
                            params: {
                                intIdDetalleSolicitudCab: intIdDetalleSolicitudCab,
                                strIdSolicitudesSeleccionadas: strIdSolicitudesSeleccionadas
                            },
                            success: function (response) {
                                var text = Ext.decode(response.responseText);
                                if ("100" === text.strStatus) {
                                    storeToReload.load();                                    
                                    arraySolicitudesSeleccionadas = new Array();
                                    winEjecutarSolicitudes.close();
                                    winEjecutarSolicitudes.destroy();
                                    box.destroy();
                                    entidadSolicitudMasiva.initSeguimiento(intIdDetalleSolicitudCab, 'seguimiento_content');
                                }
                                entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                            },
                            failure: function (response)
                            {
                                var text = Ext.decode(response.responseText);
                                entidadSolicitudMasiva.alert(entidadSolicitudMasiva.tituloMensajeBox(text.strStatus), text.strMessageStatus);
                                winEjecutarSolicitudes.close();
                                winEjecutarSolicitudes.destroy();
                                box.destroy();
                            }
                        });
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function () {
                        winEjecutarSolicitudes.close();
                        winEjecutarSolicitudes.destroy();
                    }
                }
            ]
        });

        var winEjecutarSolicitudes = "";
        winEjecutarSolicitudes = Ext.widget('window', {
            id : 'windowEjecutarSolicitud',
            title: 'Ejecutar Solicitudes',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: true,
            items: [
                panelContenido
            ]
        });
        winEjecutarSolicitudes.show();
    }
    
    /**
     * getElementoSeguimientoArray, Crea elementos para la barra de seguimiento
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * @since 1.0
     * 
     * @param intIdDetalleSolicitudCab
     * @param arrayEsgetElementoSeguimientoArraytacionesSM
     * 
     * @return arrayPaneles
     */    
    this.getElementoSeguimientoArray = function (intIdDetalleSolicitudCab, arrayEstacionesSM)
    {        
        var arrayPaneles = [];
        
        for(var i=0; i < arrayEstacionesSM.length ; i++){
            var siguienteImage = null;
            if(arrayEstacionesSM[i][4])
            {
                siguienteImage = Ext.create('Ext.Img', {
                    id: arrayEstacionesSM[i][0]+"Flecha",
                    src: (arrayEstacionesSM[i][4]) ? '/public/images/go_next_inactivo.png' : '',
                    cls: (arrayEstacionesSM[i][4]) ? 'icon_seguimiento' : ''
                });
            }
            
             var elementoImage = Ext.create('Ext.Img', {
                id: arrayEstacionesSM[i][0]+"Imagen",
                src: '/public/images/' + arrayEstacionesSM[i][2],
                cls: 'icon_seguimiento'
            });
            
            var elementoImgPanel = Ext.create('Ext.panel.Panel', {
                id: arrayEstacionesSM[i][0]+"ImagenPanel",
                width: 40,
                height: 40,
                layout: { tdAttrs: {style: 'padding: 5px; border-radius: 5px;'} },
                items: [
                    elementoImage
                ],
                cls: 'imagenPanel_inactivo'
            });
            
            var elementoButton = Ext.create('Ext.button.Button', {
                id: arrayEstacionesSM[i][0]+"Button",
                text: arrayEstacionesSM[i][1],
                cls: 'buttonSeguimiento_inactivo'
            });
            
            var items = [];
            items[0] = elementoImgPanel;
            items[1] = elementoButton;
            if(arrayEstacionesSM[i][4])
            {
                items[2] = siguienteImage;
            }
            
            var elementoPanel = Ext.create('Ext.panel.Panel', {
                id: arrayEstacionesSM[i][0]+"Panel",
                width: arrayEstacionesSM[i][3],
                layout: {
                    tdAttrs: {style: 'padding: 5px; border:none;'},
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items: items
            });
            
            arrayPaneles[i] = elementoPanel;
        }
        
        return arrayPaneles;
    }	
	
	/**
     * initSeguimiento, Muestra el seguimiento 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * @since 1.0
     * 
     * @param intIdDetalleSolicitudCab
     * @param idContenedor
     */
    this.initSeguimiento = function (intIdDetalleSolicitudCab, idContenedor)
    {   
        var contenedor = Ext.get(idContenedor);
        if(contenedor == null){
            return;
        }
        contenedor.update('');
        
        var estacionesSM = [
          ['solicitudMasiva','Solicitud Masiva','solicitudcorvent.png',175,true],
          ['archivo','Archivo Adjunto','exdown.png',175,true],
          ['autorizacion','Autorización','adm-clausulacontrato.png',160,true],
          ['autorizacionPrecio','Aut. Precio','profit.png',150,true],
          ['autorizacionRadio','Aut. Radio','radio.png',150,true],
          ['autorizacionIpccl2','Aut. IPCCL2','network.png',155,true],
          ['ejecucion','Ejecución','admini.png',145,true],
          ['finalizada','Finalizada','todo.png',120,false]
        ];        
        var items = this.getElementoSeguimientoArray(intIdDetalleSolicitudCab, estacionesSM);
        
        Ext.create('Ext.toolbar.Toolbar', {
            id : 'toolbarSeguimiento',
            renderTo: idContenedor,
            width   : '100%',
            heigth: 300,
            items: items
        });
        
        this.obtenerInformacionSeguimiento(intIdDetalleSolicitudCab, estacionesSM);    
    }
    
    /**
     * obtenerInformacionSeguimiento, Obtiene el detalle del cada estacion y si esta activa
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * @since 1.0
     * 
     * @param intIdDetalleSolicitudCab
     * @param arrayEstacionesSM
     */
    this.obtenerInformacionSeguimiento = function(intIdDetalleSolicitudCab, arrayEstacionesSM){
        var strCodigosEstaciones = "";
        for(var i=0; i < arrayEstacionesSM.length ; i++){
            strCodigosEstaciones +=  arrayEstacionesSM[i][0];
            if(i < (arrayEstacionesSM.length-1)){
                strCodigosEstaciones +=",";
            }
        }
        var entidadSolicitudMasiva = new SolicitudMasiva();
        Ext.Ajax.request({
            url: urlGetDetalleEstaciones,
            method: 'post',
            params: {
                intIdSolicitud: intIdDetalleSolicitudCab,
                strCodigosEstaciones: strCodigosEstaciones
            },
            success: function (response) {                
                var text = Ext.decode(response.responseText);
                if ("100" === text.strStatus) {
                    var respuesta =  Ext.decode(text.strMessageStatus);
                    if(respuesta != null)
                    {
                        for(var i=0; i < arrayEstacionesSM.length ; i++){
                             var strCodigoEstacion =  arrayEstacionesSM[i][0];
                             var strNombreEstacion =  arrayEstacionesSM[i][1];
                             if(respuesta[strCodigoEstacion] != null)
                             {
                                 var contenido = respuesta[strCodigoEstacion]['contenido'];
                                 var estado = respuesta[strCodigoEstacion]['estado'];
                                 
                                 entidadSolicitudMasiva.activarPanelSeguimiento(strCodigoEstacion, estado);
                                 
                                 var tooltips = [{
                                    id: strCodigoEstacion+"Tooltip",  
                                    title: strNombreEstacion,
                                    target: strCodigoEstacion+"Panel",
                                    cls: 'tooltips_seguimiento',
                                    trackMouse: true,
                                    anchor: "100%",
                                    html: contenido,
                                    autoHide: true
                                }];

                                Ext.each(tooltips, function(config) {
                                    Ext.create('Ext.tip.ToolTip', config);
                                });
                                
                             }
                         }
                    }
                }
            },
            failure: function (response)
            {
                var text = Ext.decode(response.responseText);
                entidadSolicitudMasiva.alert(this.tituloMensajeBox(text.strStatus), text.strMessageStatus);
            }
        });
    }
    
    /**
     * activarPanelSeguimiento, Activa la estacion y le da animacion a las imagenes
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 01-05-2016
     * @since 1.0
     * 
     * @param idElemento
     * @param estado
     */
    this.activarPanelSeguimiento = function(idElemento, estado){        
        var button = Ext.getCmp(idElemento+"Button");
        var imagenPanel = Ext.getCmp(idElemento+"ImagenPanel");
        var flecha = Ext.getCmp(idElemento+"Flecha");
                
        button.removeCls("buttonSeguimiento_activo");
        button.removeCls("buttonSeguimiento_inactivo");
        imagenPanel.removeCls("imagenPanel_activo");
        imagenPanel.removeCls("imagenPanel_inactivo");
        
        button.addCls("buttonSeguimiento_"+estado);
        imagenPanel.addCls("imagenPanel_"+estado);
                
        if( flecha !== null && flecha !== undefined)
        {
            if(flecha.getEl().dom.src !== null){
                flecha.getEl().dom.src = "/public/images/go_next_"+estado+".png";
            }
        }
        
        Ext.create('Ext.fx.Anim', {
            target: idElemento+"ImagenPanel",
            duration: 1000,
            easing:'bounceOut',
            from: {
                width: 0,
                height: 0,
                left: 25,
                top: 25,
                opacity:0
            },
            to: {
                width: 40,
                height: 40,
                left: 0,
                top: 0,
                opacity:1
            }
        });
        
        Ext.create('Ext.fx.Anim', {
            target: idElemento+"Button",
            duration: 1000,
            easing:'bounceOut',
            from: {
                opacity:0
            },
            to: {
                opacity:1
            }
        });
        
        if( flecha !== null && flecha !== undefined)
        {
            Ext.create('Ext.fx.Anim', {
                target: idElemento+"Flecha",
                duration: 1000,
                easing:'bounceOut',
                from: {
                    opacity:0
                },
                to: {
                    opacity:1
                }
            });
        }        
    }
    
    /**
     * downloadExcel, descarga el archivo excel desde el servidor
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 27-06-2016
     * @since 1.0
     * @param int  intIdSolicitudMasivaCab Identificador de la Solicitud Masiva Cabecera
     */
   this.downloadExcel = function(intIdSolicitudMasivaCab) {
       if(intIdSolicitudMasivaCab !== ''){
           window.location = urlDownloadExcel+'?id=' + intIdSolicitudMasivaCab;
       }else{
           this.alert('Error', 'La Solicitud Masiva no existe!');
       }
   }
   
   /**
     * removeItemsSelectedFromArrayData, quita los elementos seleccionados en el Objeto chkBoxModel del arrayElementos de Datos
     * verificando el id del servicio.
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 09-08-2016
     * @since 1.0
     * @param Object  chkBoxModel coleccion de checkbox seleccionados de una tabla
     * @param array  arrayElementos arreglo de identificadores seleccionados
     */
    this.removeItemsSelectedFromArrayData = function (chkBoxModel, arrayElementos) {
        for (var i = 0; i < chkBoxModel.getSelection().length; i++) {
            var value = chkBoxModel.getSelection()[i].data.intIdServicio;
            if (arrayElementos !== null) {
                var j=0;
                for(j = 0; j < arrayElementos.length; j++)
                {
                    if(arrayElementos[j].intIdDetalle == value)
                    {
                        break;
                    }
                }                
                arrayElementos.splice(j, 1);
            }
        }
    }
}

