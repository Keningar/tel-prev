/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var funciones = new funciones();

var boolPanel = true;
function seguimiento() {

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
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
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
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
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
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
     * @since 1.0
     * @param string  strValue valor a ser formateado
     * @return string cadena formateada con el tag <b>
     */
    this.styleBold = function (strValue) {
        return '<b>' + strValue + '</b>';
    }
    
    /**
     * tituloMensajeBox, devuelve un titulo a ser puesto en los mensajes de respuestas
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
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
     * storeLoad, Al ejecutar el Load de un store muestra una alerta por algun error inesperado
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
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
     * getElementoSeguimientoArray, Crea elementos para la barra de seguimiento
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
     * @since 1.0
     * 
     * @param intIdDetalleSolicitudCab
     * @param arrayEstacionesSM
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
                    id: arrayEstacionesSM[i][0]+"Flecha"+intIdDetalleSolicitudCab,
                    src: (arrayEstacionesSM[i][4]) ? '/public/images/go_next_inactivo.png' : '',
                    cls: (arrayEstacionesSM[i][4]) ? 'icon_seguimiento' : ''
                });
            }
            
             var elementoImage = Ext.create('Ext.Img', {
                id: arrayEstacionesSM[i][0]+"Imagen"+intIdDetalleSolicitudCab,
                src: '/public/images/' + arrayEstacionesSM[i][2],
                cls: 'icon_seguimiento'
            });
            
            var elementoImgPanel = Ext.create('Ext.panel.Panel', {
                id: arrayEstacionesSM[i][0]+"ImagenPanel"+intIdDetalleSolicitudCab,
                width: 40,
                height: 40,
                layout: { tdAttrs: {style: 'padding: 5px; border-radius: 5px;'} },
                items: [
                    elementoImage
                ],
                cls: 'imagenPanel_inactivo'
            });
            
            var elementoButton = Ext.create('Ext.button.Button', {
                id: arrayEstacionesSM[i][0]+"Button"+intIdDetalleSolicitudCab,
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
                id: arrayEstacionesSM[i][0]+"Panel"+intIdDetalleSolicitudCab,
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
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
     * @since 1.0
     * 
     * @param intIdDetalleSolicitudCab
     * @param idContenedor
     * @param idTable
     */
    this.initSeguimiento = function ( intIdServicio, idContenedor, idTable)
    {   
        var items = "";
        var contenedor = Ext.get(idContenedor);
        if(contenedor == null){
            return;
        }
        contenedor.update('');
        var table = Ext.get(idTable);
        if(table != null){
            table.update('');
        }
        Ext.Ajax.request({
            url: urlEjecutarSeguimiento,
            method: 'post',
            timeout: 90000,
            params: {
                objServicio: intIdServicio
            },
            success: function (response) {
                var estacionesServ = Ext.decode(response.responseText);
                if (estacionesServ!="" && estacionesServ !== undefined) {
                    items = entidadSolicitudSeguimiento.getElementoSeguimientoArray(intIdServicio, estacionesServ);
        
                    Ext.create('Ext.toolbar.Toolbar', {
                        id : 'toolbarSeguimiento'+intIdServicio,
                        renderTo: contenedor,
                        width   : '100%',
                        heigth: 300,
                        items: items,
                        cls: "seguimiento_content",

                    });
                    dibujarAcciones(intIdServicio);
                    entidadSolicitudSeguimiento.obtenerInformacionSeguimiento(intIdServicio, estacionesServ);   
                    dibujar(intIdServicio);
                }
            },
            failure: function (response)
            {
                var text = Ext.decode(response.responseText);
                entidadSolicitudSeguimiento.alert(entidadSolicitudSeguimiento.tituloMensajeBox(text.strStatus), text.strMessageStatus);
            }
        });
                        
        //FIN DE MODIFICACIÓN                
         
    }
    
    
    /**
     * obtenerInformacionSeguimiento, Obtiene el detalle de cada estado
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
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
        var entidadSolicitudSeguimiento = new seguimiento();
        Ext.Ajax.request({
            url: urlGetDetalleServicios,
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
                                 
                                 entidadSolicitudSeguimiento.activarPanelSeguimiento(strCodigoEstacion, intIdDetalleSolicitudCab, estado);
                                 
                                 var tooltips = [{
                                    id: strCodigoEstacion+"T"+intIdDetalleSolicitudCab,  
                                    title: strNombreEstacion,
                                    target: strCodigoEstacion+"Panel"+intIdDetalleSolicitudCab,
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
                entidadSolicitudSeguimiento.alert(this.tituloMensajeBox(text.strStatus), text.strMessageStatus);
            }
        });
    }
    
    /**
     * activarPanelSeguimiento, Activa la estacion y le da animacion a las imagenes
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
     * @since 1.0
     * 
     * @param idElemento
     * @param estado
     */
    this.activarPanelSeguimiento = function(idElemento, intIdDetalleSolicitudCab, estado){        
        var button = Ext.getCmp(idElemento+"Button"+intIdDetalleSolicitudCab);
        var imagenPanel = Ext.getCmp(idElemento+"ImagenPanel"+intIdDetalleSolicitudCab);
        var flecha = Ext.getCmp(idElemento+"Flecha"+intIdDetalleSolicitudCab);
                
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
            target: idElemento+"ImagenPanel"+intIdDetalleSolicitudCab,
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
            target: idElemento+"Button"+intIdDetalleSolicitudCab,
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
                target: idElemento+"Flecha"+intIdDetalleSolicitudCab,
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
    
}


var connTareas = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Cargando Tareas',
                    progressText: 'loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});
/**
 * dibujar, Crea y dibuja el table con la información del seguimiento.
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 05-01-2023
 * @since 1.0
 * 
 * @param intIdServicio
 */
function dibujar (intIdServicio)
{
    /**
     * change, Muestra en colores las celdas segun su tiempo de ejecución
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 05-01-2023
     * @since 1.0
     * 
     * @param val
     */
    function change(val,metadata,record) {
        if (val < record.data.tiempoEstimado) {
            return '<span style="color:green;">' + val + '</span>';
        } else  {
            return '<span style="color:red;">' + val + '</span>';
        }
    }
    
    storeSeguim = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        timeout: 90000,
        autoLoad : false,
        proxy: {
            type: 'ajax',
            url : urlSeguimientoHistorial,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
	    actionMethods: {
		create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
	    },
            extraParams: {
                objServicio: intIdServicio
            }
        },
        fields:
                [
                    {name:'servicioId', mapping:'servicioId'},
                    {name:'observacion', mapping:'observacion'},
                    {name:'departamento', mapping:'departamento'},
                    {name:'estado', mapping:'estado'},
                    {name:'usrCreacion', mapping:'usrCreacion'},
                    {name:'feCreacion', mapping:'feCreacion'},
                    {name:'feModificacion', mapping:'feModificacion'},
                    {name:'ipModificacion', mapping:'ipModificacion'},
                    {name:'tiempoEstimado', mapping:'tiempoEstimado'},
                    {name:'tiempoTranscurrido', mapping:'tiempoTranscurrido'},
                    {name:'diasTranscurrido', mapping:'diasTranscurrido'},
                    
                ],
    });
    storeSeguim.load({
    callback: function(records, operation, success) {
        var totalregistros=Ext.JSON.decode(operation.resultSet.total);
        if(totalregistros<=0)
        {
            Ext.get('getPanelSeguimiento'+intIdServicio).update('');
            boolPanel=true;
            Ext.create('Ext.form.Panel', {
            hight:10,
            margin: '1 0 0 2',
            bodyPadding: 2,
            renderTo: Ext.get('getPanelSeguimiento'+intIdServicio),
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [{
                xtype: 'label',
                forId: 'myFieldId',
                text: 'No Existe Seguimiento para el Producto',
                margin: '0 0 0 10'
            }]
        });
        }
        else
        {
        Ext.create('Ext.grid.Panel', {
            margin: '1 0 0 2',
            id:"gridSegumiento",
            height: 280,
            width:1500,
            store: storeSeguim,
        columns:[
                {
                  header: 'Observacion',
                  dataIndex: 'observacion',
                  width:300,
                  hideable: false
                },
                {
                  id: 'servicioId',
                  header: 'IdServicio',
                  dataIndex: 'servicioId',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Usuario',
                  dataIndex: 'usrCreacion',
                  width: 90,
                  hideable: false
                },
                {
                  header: 'Departamento',
                  dataIndex: 'departamento',
                  width: 120,
                  hideable: false
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  hideable: false
                },
                {
                  header: 'TiempoEstimado',
                  dataIndex: 'tiempoEstimado',
                  width: 100,
                  hideable: false
                },
                {
                  header: 'TiempoTranscurrido',
                  dataIndex: 'tiempoTranscurrido',
                  renderer : change,
                  width: 120,
                  hideable: false
                },
                {
                  header: 'DiasTranscurrido',
                  dataIndex: 'diasTranscurrido',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Fecha Creacion',
                  dataIndex: 'feCreacion',
                  width: 105,
                  sortable: true
                },
                {
                  header: 'Fecha Modificación',
                  dataIndex: 'feModificacion',
                  width: 115,
                  sortable: true
                },
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: storeSeguim,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: Ext.get('getPanelSeguimiento'+intIdServicio),
    });
    
        }
        //Ext.getCmp('panel2').doLayout();
    }
});


}

/**
 * dibujar, Crea y dibuja el table con la información del seguimiento.
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 05-01-2023
 * @since 1.0
 * 
 * @param intIdServicio
 */
function dibujarAcciones(intIdServicio)
{
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        timeout: 90000,
        autoLoad : false,
        proxy: {
            type: 'ajax',
            url : url_gridAcciones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
	    actionMethods: {
		create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
	    },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                ultimaMilla: '',
                estado: 'Todos',
                servicioId: intIdServicio
            }
        },
        fields:
                [
                {name:'descripcionSolicitud',              mapping:'descripcionSolicitud'},
                {name:'cliente',                    mapping:'cliente'},
                {name:'vendedor',                   mapping:'vendedor'},
                {name:'jurisdiccion',               mapping:'jurisdiccion'},
                {name:'ciudad',                     mapping:'ciudad'},
                {name:'direccion',                  mapping:'direccion'},
                {name:'ultimaMilla',                mapping:'ultimaMilla'},
                {name:'feSolPlanifica',             mapping:'feSolPlanifica'},
                {name:'fePlanificacion',            mapping:'fePlanificacion'},
                {name:'estado',                     mapping:'estado'},
                {name:'BoolProyectoPlan',           mapping:'BoolProyectoPlan'},
                {name: 'id_factibilidad',           mapping: 'id_factibilidad'},
                {name: 'id_servicio',               mapping: 'id_servicio'},
                {name: 'tipo_orden',                mapping: 'tipo_orden'},
                {name: 'id_punto',                  mapping: 'id_punto'},
                {name: 'estado_punto',              mapping: 'estado_punto'},
                {name: 'caja',                      mapping: 'caja'},
                {name: 'tercerizadora',             mapping: 'tercerizadora'},
                {name: 'id_orden_trabajo',          mapping: 'id_orden_trabajo'},
                {name: 'login2',                    mapping: 'login2'},
                {name: 'esRecontratacion',          mapping: 'esRecontratacion'},
                {name: 'producto',                  mapping: 'producto'},
                {name: 'coordenadas',               mapping: 'coordenadas'},
                {name: 'direccion',                 mapping: 'direccion'},
                {name: 'observacion',               mapping: 'observacion'},
                {name: 'telefonos',                 mapping: 'telefonos'},
                {name: 'ciudad',                    mapping: 'ciudad'},
                {name: 'jurisdiccion',              mapping: 'jurisdiccion'},
                {name: 'nombreSector',              mapping: 'nombreSector'},
                {name: 'ultimaMilla',               mapping: 'ultimaMilla'},
                {name: 'strMetraje',                mapping: 'strMetraje'},
                {name: 'strPrefijoEmpresa',         mapping: 'strPrefijoEmpresa'},
                {name: 'feSolicitaPlanificacion',   mapping: 'feSolicitaPlanificacion'},
                {name: 'fePlanificada',             mapping: 'fePlanificada'},
                {name: 'HoraIniPlanificada',        mapping: 'HoraIniPlanificada'},
                {name: 'HoraFinPlanificada',        mapping: 'HoraFinPlanificada'},
                {name: 'latitud',                   mapping: 'latitud'},
                {name: 'longitud',                  mapping: 'longitud'},
                {name: 'strTipoEnlace',             mapping: 'strTipoEnlace'},
                {name: 'estado',                    mapping: 'estado'},
                {name: 'tituloCoordinar',           mapping: 'tituloCoordinar'},
                {name: 'esSolucion',                mapping: 'esSolucion'},
                {name: 'precioFibra',               mapping: 'precioFibra'},
                {name: 'metrosDeDistancia',         mapping: 'metrosDeDistancia'},
                {name: 'nombreTecnico',             mapping: 'nombreTecnico'},
                {name: 'tipo_esquema',              mapping: 'tipo_esquema'},
                {name: 'idIntWifiSim',              mapping: 'idIntWifiSim'},
                {name: 'idIntCouSim',               mapping: 'idIntCouSim'},
                {name: 'arraySimultaneos',          mapping: 'arraySimultaneos'},
                {name: 'strTipoRed',                mapping: 'strTipoRed'},
                {name: 'action1',                   mapping: 'action1'},
                {name: 'action2',                   mapping: 'action2'},
                {name: 'action3',                   mapping: 'action3'},
                {name: 'action4',                   mapping: 'action4'},
                {name: 'action5',                   mapping: 'action5'},
                {name: 'action6',                   mapping: 'action6'},
                {name: 'action7',                   mapping: 'action7'},
                {name: 'action8',                   mapping: 'action8'},
                {name: 'action9',                   mapping: 'action9'},
                {name: 'action10',                   mapping: 'action10'},
                {name: 'action11',                   mapping: 'action11'},
                {name: 'action12',                   mapping: 'action12'},
                {name: 'action13',                   mapping: 'action13'},
                {name: 'action14',                   mapping: 'action14'},
                {name: 'action15',                   mapping: 'action15'},
                {name: 'action16',                   mapping: 'action16'},
                {name: 'action17',                   mapping: 'action17'},    
                {name: 'action18',                   mapping: 'action18'},    
                {name: 'action19',                   mapping: 'action19'},    
                {name: 'action20',                   mapping: 'action20'},    
                {name:'rutaCroquis',               mapping:'rutaCroquis'},   
                ],
    });
    

    
    store.load({
    callback: function(records, operation, success) {
        var totalregistros=Ext.JSON.decode(operation.resultSet.total);
        if(totalregistros<=0)
        {
            //Ext.get('getPanelEstatus'+intIdServicio).update('');
            boolPanel=true;
            Ext.create('Ext.form.Panel', {
            hight:100,
            margin: '1 0 0 2',
            bodyPadding: 2,
            renderTo: Ext.get('getPanelEstatus'+intIdServicio),
            layout: {
                type: 'hbox',
                align: 'middle'
            },
            items: [{
                xtype: 'label',
                forId: 'myFieldId',
                text: 'No Data Found',
                margin: '0 0 0 10'
            }]
        });
        }
        else
        {
        Ext.get('getPanelEstatus'+intIdServicio).update('');
        Ext.create('Ext.grid.Panel', {
            margin: '1 0 0 2',
            id:"gridAcciones",
            height: 80,
            width:1500,
            store: store,
        columns:[
                {
                  header: 'Tipo Solicitud',
                  dataIndex: 'descripcionSolicitud',
                  width:140,
                  hideable: false
                },
                {
                  header: 'ServicioId',
                  id: 'servicio',
                  dataIndex: 'id_servicio',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Cliente',
                  id: 'cliente',
                  dataIndex: 'cliente',
                  //hidden: true,
                  hideable: false
                },
                {
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 90,
                  hideable: false
                },
                {
                  header: 'Jurisdiccion',
                  dataIndex: 'jurisdiccion',
                  width: 120,
                  hideable: false
                },
                {
                  header: 'Ciudad',
                  dataIndex: 'ciudad',
                  width: 100,
                  hideable: false
                },
                {
                  header: 'Direccion',
                  dataIndex: 'direccion',
                  width: 180,
                  hideable: false
                },
                {
                  header: 'Ultima Milla',
                  dataIndex: 'ultimaMilla',
                  //renderer : change,
                  width: 120,
                  hideable: false
                },
                {
                  header: 'Fe Sol Planifica',
                  dataIndex: 'feSolPlanifica',
                  width: 100,
                  sortable: true
                },
                {
                    id: 'fePlanificada',
                    header: 'Fecha Planificacion',
                    dataIndex: 'fePlanificada',
                    width: 125,
                    renderer: function(value, p, r) {
                        return r.data['fePlanificada'] + ' ' + r.data['HoraIniPlanificada'];
                    }
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                },
                {
                  xtype: 'actioncolumn',
                  header: 'Acciones',
                  //dataIndex: 'estado',
                  width: 300,
                  items: [
                      {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action1') == "icon-invisible" || rec.get('BoolProyectoPlan') == true)
                            {
                                rec.data.action1 = "icon-invisible";
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Programar';
                            }
                            return rec.get('action1');
                        },
                        tooltip: 'Programar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            if (rec.get('action1') != "icon-invisible" || rec.get('BoolProyectoPlan') == false)
                            {
                                showProgramar(rec, 'local', 0);
                            } else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                        /*var permiso = $("#ROLE_137-104");
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                        if (!boolPermiso) {
                            rec.data.action2 = "icon-invisible";
                        }*/

                        if (rec.get('action2') == "icon-invisible")
                            this.items[1].tooltip = '';
                        else
                            this.items[1].tooltip = 'Replanificar';

                        return rec.get('action2');
                         },
                         tooltip: 'Replanificar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);

                            /*var permiso = $("#ROLE_137-103");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action2 = "icon-invisible";
                            }*/

                            if (rec.get('action2') != "icon-invisible")
                            {
                                /*var permiso = $("#ROLE_137-9711");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);*/
                                showRePlanificar(rec, 'local', true);

                            } else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        },
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_137-106");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }
                            if (rec.data.descripcionSolicitud == 'Solicitud Migracion')
                            {
                                rec.data.action3 = "icon-invisible";
                            }
                            if (rec.get('action3') == "icon-invisible")
                                this.items[2].tooltip = '';
                            else
                                this.items[2].tooltip = 'Detener';

                            return rec.get('action3')
                        },
                        tooltip: 'Detener',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);

                            var permiso = $("#ROLE_137-103");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }
                            if (rec.data.descripcionSolicitud == 'Solicitud Migracion')
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') != "icon-invisible")
                                showDetener_Coordinar(rec, 'local');
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    /*{
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-225");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action4 = "icon-invisible";
                                }

                                if (rec.get('action4') == "icon-invisible")
                                    this.items[3].tooltip = '';
                                else
                                    this.items[3].tooltip = 'Anular Orden';

                                return rec.get('action4')
                            },
                            tooltip: 'Anular Orden',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action4 = "icon-invisible";
                                }

                                if (rec.get('action4') != "icon-invisible")
                                    showAnularOrden_Coordinar(rec);
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-105");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action5 = "icon-invisible";
                                }

                                if (rec.get('action5') == "icon-invisible")
                                    this.items[4].tooltip = '';
                                else
                                    this.items[4].tooltip = 'Rechazar Orden';

                                return rec.get('action5')
                            },
                            tooltip: 'Rechazar Orden',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action5 = "icon-invisible";
                                }

                                if (rec.get('action5') != "icon-invisible")
                                    showRechazarOrden_Coordinar(rec);
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action10')
                            },
                            tooltip: 'Ingresar Seguimiento',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                if (rec.get('action10') != "icon-invisible")
                                {
                                    agregarSeguimiento(rec);
                                }
                                else
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'No tiene permisos para realizar esta accion',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                            }
                        },*/
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action6')
                            },
                            tooltip: 'Ver Mapa',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);

                                if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
                                    showViewGoogleMap(rec);
                                else
                                    Ext.Msg.alert('Error ', 'Las coordenadas son incorrectas');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action7')
                            },
                            tooltip: 'Ver Croquis',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);

                                if (rec.get("id_factibilidad") != "" && rec.get("rutaCroquis") != "")
                                    showVerCroquis(rec.get('id_factibilidad'), rec.get('rutaCroquis'));
                                else
                                    Ext.Msg.alert('Error ', 'Las ruta no existe');
                            }
                        },

                        {
                            getClass: function(v, meta, rec) {
                               /* var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action8') == "icon-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Asignar Responsable';

                                return rec.get('action8')
                            },
                            tooltip: 'Asignar Responsable',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);

                                if (rec.get('action8') != "icon-invisible")
                                {
                                    showProgramar(rec, 'local', 1);
                                }
                                //showAsignacionIndividual(rec, 'local', '0', false);
                                else
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'No tiene permisos para realizar esta accion',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                            }
                        },

                        {
                            getClass: function(v, meta, rec) {
                                /*var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action9') == "icon-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Asignar Responsable';

                                return rec.get('action9')
                            },
                            tooltip: 'Asignar Responsable',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);

                                /*var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action9') != "icon-invisible")
                                {
                                    showProgramar(rec, 'local', 1);
                                    store.load();
                                }
                                //showAsignacionIndividual(rec, 'local', '0', false);
                                else
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'No tiene permisos para realizar esta accion',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if (!rec.get('seguimiento'))
                                {
                                    this.items[10].tooltip = '';
                                }
                                else
                                {
                                    this.items[10].tooltip = 'Seguimiento';
                                    return rec.get('action13');
                                }
                            },
                            tooltip: 'Seguimiento',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                showSeguimiento(rec, 'local', 0);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action11')
                            },
                            tooltip: 'Planificar FWA',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                planificarServicio(rec);
                                store.load();
                            }
                        },
                        {
                            getClass: function (v, meta, rec) {
                                return rec.get('action12')
                            },
                            tooltip: 'Ver Tareas del cliente',
                            handler: function (grid, rowIndex, colIndex) 
                            {
                                var rec = grid.getStore().getAt(rowIndex);
                                verTareasClientes(rec.get('login2'));
                                
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if (!rec.get('pedidos'))
                                {
                                    this.items[13].tooltip = '';
                                }
                                else
                                {
                                    this.items[13].tooltip = 'Pedidos';
                                    return rec.get('action14');
                                }
                            },
                            tooltip: 'Pedidos',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                showPedidos(rec, 'local', 0);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.raw.action15
                            },
                            tooltip: 'Validador de Excedente de Material',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
	                            getValidadorExcedenteMaterial(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.raw.action16
                            },
                            tooltip: 'Consulta Archivos de Evidencia para Excedente de Materiales',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                getDocumento(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.raw.action20
                            },
                            tooltip: 'Ver Documentos',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.getStore().getAt(rowIndex);
                                presentarDocumentosCasos(rec);
                            }
                        }
                  ]
                }
            ],
            /*bbar: Ext.create('Ext.PagingToolbar', {
            store: storeAcciones,
            //displayInfo: true,
            //displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),*/
        renderTo: Ext.get('getPanelEstatus'+intIdServicio),
    });
    
        }
        //Ext.getCmp('panel2').doLayout();
    }
});
}

function showProgramar(rec, origen, opcion)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";
    var departamentos = "";
    var nombreTarea = "";
    if (!winAsignacionIndividual)
    {
        var id_servicio     = rec.get("id_servicio");
        var id_factibilidad = rec.get("id_factibilidad");
        var tipo_solicitud  = rec.get("descripcionSolicitud");
        var boolEsHousing   = (rec.get('nombreTecnico') === 'HOUSING' || rec.get('nombreTecnico') === 'HOSTING');
        var tienePersonalizacionOpcionesGridCoordinar = "NO";
        var nombreOpcionPersonalizadaGridCoordinar = 'PROGRAMAR-' + tipo_solicitud.toUpperCase() + '-' + rec.get('nombreTecnico');
        if (typeof rec.get('arrayPersonalizacionOpcionesGridCoordinar') !== 'undefined' 
            && rec.get('arrayPersonalizacionOpcionesGridCoordinar').hasOwnProperty(nombreOpcionPersonalizadaGridCoordinar))
        {
            tienePersonalizacionOpcionesGridCoordinar = "SI";
        }
        
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });
        
        panelInfoAdicionalSolCoordinar = 
        {
            id:'panelInfoAdicionalSolCoordinar',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            defaults:
            {
                width: '740px',
            },
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa == "MD" && opcion == 0)
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: id_factibilidad,
                                    strOpcionGestionSimultanea: 'PLANIFICAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }
                                    Ext.getCmp('panelInfoAdicionalSolCoordinar').add({
                                        title: 'Gestión Simultánea',
                                        xtype: 'fieldset',
                                        defaultType: 'textfield',
                                        style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                        layout: 'anchor',
                                        defaults:
                                            {
                                                border: false,
                                                frame: false,
                                                width: '740px'
                                            },
                                        items: [
                                            {
                                                xtype: 'panel',
                                                border: false,
                                                style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                defaults:
                                                {
                                                    width: '650px',
                                                },
                                                layout: {type: 'hbox', align: 'stretch'},
                                                items: [
                                                    {                                                                                              
                                                        xtype: 'textfield',
                                                        hidden: true,
                                                        id:'tieneGestionSimultanea',
                                                        value:'SI'
                                                    },
                                                    Ext.create('Ext.Component', {
                                                        style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                        html: "<div>Esta solicitud #"+ id_factibilidad + " coordinará de manera simultánea "
                                                              +strInfoNumSolicitudes+"."+"</div>",
                                                        layout: 'anchor'
                                                    }),
                                                    {
                                                        id: 'btnMasDetalleSolsSimultaneas',
                                                        xtype: 'button',
                                                        text: 'Ver Detalle',
                                                        handler: function(){
                                                            Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                            Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                            Ext.getCmp('gridSolsSimultaneas').show();
                                                        }
                                                    },
                                                    {
                                                        id: 'btnMenosDetalleSolsSimultaneas',
                                                        xtype: 'button',
                                                        text: 'Ocultar Detalle',
                                                        hidden: true,
                                                        handler: function(){
                                                            Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                            Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                            Ext.getCmp('gridSolsSimultaneas').hide();
                                                        }
                                                    }
                                                ]
                                            },
                                            Ext.create('Ext.grid.Panel', {
                                                id: 'gridSolsSimultaneas',
                                                store: storeSolsSimultaneas,
                                                style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; margin-bottom: 10px",
                                                columnLines: true,
                                                hidden: true,
                                                columns: 
                                                [
                                                    {
                                                        id: 'descripServicioSimultaneo',
                                                        header: 'Plan/Producto',
                                                        dataIndex: 'descripServicioSimultaneo',
                                                        width: 130,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'estadoServicioSimultaneo',
                                                        header: 'Estado servicio',
                                                        dataIndex: 'estadoServicioSimultaneo',
                                                        width: 130,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'idSolSimultanea',
                                                        header: '# Solicitud',
                                                        dataIndex: 'idSolSimultanea',
                                                        width: 100,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'descripTipoSolSimultanea',
                                                        header: 'Tipo Solicitud',
                                                        dataIndex: 'descripTipoSolSimultanea',
                                                        width: 180,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'estadoSolSimultanea',
                                                        header: 'Estado Solicitud',
                                                        dataIndex: 'estadoSolSimultanea',
                                                        width: 130,
                                                        sortable: true
                                                    }
                                                ],
                                                viewConfig: {
                                                    stripeRows: true
                                                },
                                                frame: true,
                                                defaults:
                                                {
                                                    width: '670px'
                                                }
                                            })
                                        ]
                                    });
                                }
                                else
                                {
                                    Ext.getCmp('panelInfoAdicionalSolCoordinar').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolCoordinar').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        Ext.getCmp('panelInfoAdicionalSolCoordinar').add(
                        {                                                                                              
                            xtype: 'textfield',
                            hidden: true,
                            id:'tieneGestionSimultanea',
                            value:'NO'
                        });
                        Ext.getCmp('panelInfoAdicionalSolCoordinar').doLayout();
                    }
                }
            }
        };
        
        itemTercerizadora = Ext.create('Ext.Component', {
            html: "<br>"
        });
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        }
        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
            title: "Manual",
            buttonAlign: 'center',
            BodyPadding: 10,
            width: 900,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            autoScroll:true,
            items:
                [
                    {
                        xtype: 'panel',
                        border: false,
                        layout: {type: 'hbox', align: 'stretch'},
                        items: [
                            {
                                xtype: 'fieldset',
                                id: 'client-data-fieldset',
                                title: 'Datos del Cliente',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 15px;",
                                layout: 'anchor',
                                defaults: {
                                    width: '350px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Cliente',
                                        name: 'info_cliente',
                                        id: 'info_cliente',
                                        value: rec.get("cliente"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Login',
                                        name: 'info_login',
                                        id: 'info_login',
                                        value: rec.get("login2"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Ciudad',
                                        name: 'info_ciudad',
                                        id: 'info_ciudad',
                                        value: rec.get("ciudad"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Direccion',
                                        name: 'info_direccion',
                                        id: 'info_direccion',
                                        value: rec.get("direccion"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Sector',
                                        name: 'info_nombreSector',
                                        id: 'info_nombreSector',
                                        value: rec.get("nombreSector"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Es Recontratacion',
                                        name: 'es_recontratacion',
                                        id: 'es_recontratacion',
                                        value: rec.get("esRecontratacion"),
                                        allowBlank: false,
                                        readOnly: true
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldset',
                                title: 'Datos del Servicio',
                                id:'service-data-fieldset',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 15px;",
                                defaults: {
                                    width: '350px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Servicio',
                                        name: 'info_servicio',
                                        id: 'info_servicio',
                                        value: rec.get("producto"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Tipo Orden',
                                        name: 'tipo_orden_servicio',
                                        id: 'tipo_orden_servicio',
                                        value: rec.get("tipo_orden"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Tipo Enlace',
                                        name: 'strTipoEnlace',
                                        id: 'strTipoEnlace',
                                        value: rec.get("strTipoEnlace"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    itemTercerizadora,
                                    {
                                        xtype: 'textarea',
                                        fieldLabel: 'Telefonos',
                                        name: 'telefonos_punto',
                                        id: 'telefonos_punto',
                                        value: rec.get("telefonos"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textarea',
                                        fieldLabel: 'Observacion',
                                        name: 'observacion_punto',
                                        id: 'observacion_punto',
                                        value: rec.get("observacion"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                ]
                            }
                        ]
                    },
                    panelInfoAdicionalSolCoordinar,
                    Vacio1
                ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var param = "";
                        var boolError = true;
                        var boolErrorTecnico = false;
                        var idPerTecnico = 0;
                        if (origen == "local")
                        {
                            id = rec.data.id_factibilidad;
                            param = rec.data.id_factibilidad;
                            id_servicio = rec.data.id_servicio;
                        } else if (origen == "otro" || origen == "otro2")
                        {
                            if (id == null || !id || id == 0 || id == "0" || id == "")
                            {
                                boolError = false;
                                Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
                            }
                        } else
                        {
                            boolError = false;
                            Ext.Msg.alert('Alerta', 'No hay opcion escogida');
                        }
                        if (boolErrorTecnico)
                        {
                            Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
                        } else
                        {
                            if (boolError)
                            {
                                /////////NUEVO PROGRAMAR/////////
                                    var array_data_caract ={};
                                    var informacion = [];
                                    var numeroRegistros = 0;
                                    for (var i = 0; i < gridServicios.getStore().getCount(); i++)
                                    {
                                        variable = gridServicios.getStore().getAt(i).data;
                                        numeroRegistros = numeroRegistros+1;
                                        for (var key in variable)
                                        {
                                            var valor = variable[key];

                                            array_data_caract[key] = valor;
                                        }

                                        informacion.push(array_data_caract);

                                        array_data_caract = {};
                                    }

                                        if (informacion.length > 0)
                                        {
                                            Ext.MessageBox.wait("Guardando servicio(s)...");

                                            if (totalTareas > numeroRegistros)
                                            {
                                                strMensaje = "Existen Departamentos sin Gestionar. Desea continuar?";

                                            }
                                            else
                                            {
                                                strMensaje = "Se asignará el responsable. Desea continuar?";
                                            }
                                            Ext.Msg.confirm('Alerta', strMensaje, function(btn) {
                                            if (btn == 'yes') {
                                                connAsignarResponsable.request({
                                                    url: "../../planificar/coordinarproyecto/programarProyecto",
                                                    method: 'post',
                                                    timeout: 450000,
                                                    params: {

                                                        origen: origen,
                                                        id: id,
                                                        id_servicio: id_servicio,
                                                        param: param,
                                                        listado: JSON.stringify(informacion),
                                                        idIntWifiSim: JSON.stringify(rec.data.idIntWifiSim),
                                                        idIntCouSim : JSON.stringify(rec.data.idIntCouSim),
                                                        arraySimultaneos: JSON.stringify(rec.data.arraySimultaneos),
                                                        esHal: 'N',

                                                        tienePersonalizacionOpcionesGridCoordinar: tienePersonalizacionOpcionesGridCoordinar
                                                    },
                                                    success: function(response) {
                                                        var text        = response.responseText;
                                                        var intPosicion = text.indexOf("Correctamente");

                                                        if (text == "Se asignaron la(s) Tarea(s) Correctamente." || text == "ok" ||
                                                            text == "Se coordinó la solicitud" || intPosicion !== -1)
                                                        {
                                                            cierraVentanaAsignacionIndividual();

                                                            if(text == "ok")
                                                            {
                                                                text = "Se asignaron la(s) Tarea(s) Correctamente.";
                                                            }
                                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                                if (btn == 'ok') {
                                                                    store.load();
                                                                }
                                                            });
                                                        } else {
                                                            var mm = Ext.Msg.alert('Alerta', text);
                                                            Ext.defer(function() {
                                                                mm.toFront();
                                                            }, 50);
                                                        }
                                                    },
                                                    failure: function(result) {
                                                        Ext.Msg.alert('Alerta', result.responseText);
                                                    }
                                                });
                                            }
                                        });
                                          
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta',"Agregar Planificación");
                                        $('button[type=submit]').attr('disabled', 'disabled');
                                    }
                            }
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ]
        });

        /*Si el servicio posee un id de Internet Wifi, significa que es instalacion Simultanea
        * y se le agregaría un campo para que PYL pueda notarlo*/
        if (rec.get('idIntWifiSim'))
        {
            Ext.getCmp('client-data-fieldset').add({
                xtype: 'textfield',
                cls:'animated bounceIn',
                fieldLabel: 'Instalación Simultánea',
                name: 'es_instalacionSimultanea',
                id: 'es_instalacionSimultanea',
                value: 'INTERNET WIFI → Total de AP\'s: «' + rec.get('idIntWifiSim').length + "»",
                allowBlank: true,
                readOnly: true
            });
        }

        /*Si el servicio posee arraySimultaneos, significa que es instalacion Simultanea
        * y se le agregaría un campo para que PYL pueda notarlo*/
        if (typeof rec.get('arraySimultaneos') !== 'undefined' &&
        rec.get('arraySimultaneos') >= 1)
        {
            Ext.getCmp('client-data-fieldset').add({
                xtype: 'textfield',
                cls:'animated bounceIn',
                fieldLabel: 'Instalación Simultánea',
                name: 'es_instalacionSimultanea',
                id: 'es_instalacionSimultanea',
                value: '« SI »',
                allowBlank: true,
                readOnly: true
            });
        }
        
        /*Si el servicio posee un id de COU LINEAS TELEFONIA FIJA, significa que es instalacion Simultanea
        * y se le agregaría un campo para que PYL pueda notarlo*/
        if (rec.get('idIntCouSim'))
        {
            Ext.getCmp('client-data-fieldset').add({
                xtype: 'textfield',
                cls:'animated bounceIn',
                fieldLabel: 'Instalación Simultánea',
                name: 'es_instalacionSimultanea',
                id: 'es_instalacionSimultanea',
                value: 'COU LINEAS TELEFONIA FIJA ',
                allowBlank: true,
                readOnly: true
            });
        }
        /* Funcion para agregar el label del tipo de red. */
        agregarLabelTipoRed(rec);

        combo_tecnicos = Ext.create('Ext.Component', {
            html: ""
        });
        if ((prefijoEmpresa == "TN" && (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")) 
            || (prefijoEmpresa == "TNP" && rec.get("ultimaMilla") == "FTTx" && tipo_solicitud == "Solicitud Planificacion"))
        {
            storeTecnicos = new Ext.data.Store
                ({
                    total: 'total',
                    pageSize: 25,
                    listeners: {
                    },
                    proxy:
                        {
                            type: 'ajax',
                            method: 'post',
                            url: '../../planificar/asignar_responsable/getTecnicos',
                            reader:
                                {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                            extraParams: {
                                query: '',
                                'tipo_esquema': rec.get("tipo_esquema")
                            },
                            actionMethods:
                                {
                                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                }
                        },
                    fields:
                        [
                            {name: 'id_tecnico', mapping: 'idPersonaEmpresaRol'},
                            {name: 'nombre_tecnico', mapping: 'info_adicional'},
                        ],
                    autoLoad: true
                });
                const strIngeniero = rec.get("tipo_esquema") && rec.get("tipo_esquema") == 1 ? 'RADIO' : 'IPCCL2';
            
            if (rec.get('producto') === 'Cableado Estructurado')
            {
                combo_tecnicos = new Ext.form.ComboBox({
                    id: 'cmb_tecnico',
                    name: 'cmb_tecnico',
                    fieldLabel: 'Ingeniero Activación',
                    anchor: '100%',
                    queryMode: 'remote',
                    emptyText: 'Seleccione Ingeniero Activación',
                    width: 350,
                    store: storeTecnicos,
                    displayField: 'nombre_tecnico',
                    valueField: 'id_tecnico',
                    layout: 'anchor',
                    disabled: false,
                    listeners:{
                                select: function(combo, records, eOpts){
                                    Ext.getCmp('hidden_nombre_tecnico').setValue(records[0].get('nombre_tecnico'));
                                }
                            }
                });
            }
            else
            {
                combo_tecnicos = new Ext.form.ComboBox({
                    id: 'cmb_tecnico',
                    name: 'cmb_tecnico',
                    fieldLabel: `Ingeniero ${strIngeniero}`,
                    anchor: '100%',
                    queryMode: 'remote',
                    emptyText: `Seleccione Ingeniero ${strIngeniero}`,
                    width: 350,
                    store: storeTecnicos,
                    displayField: 'nombre_tecnico',
                    valueField: 'id_tecnico',
                    layout: 'anchor',
                    disabled: false,
                    listeners:{
                                select: function(combo, records, eOpts){
                                    Ext.getCmp('hidden_nombre_tecnico').setValue(records[0].get('nombre_tecnico'));
                                }
                            }
                });
            }
            
        }

        /*Si el producto requiere trabajo por mas departamentos se hace la validación para que se visualicen las tareas por departamentos*/
        connTareas.request({
            method: 'POST',

            //url: "../../factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
            url: "../../planificar/coordinarproyecto/getTareasByProcesoAndTarea",
            params: {servicioId: id_servicio, id_solicitud: id_factibilidad, nombreTarea: 'todas', estado: 'Activo'},
            success: function(response) {
                var data = Ext.JSON.decode(response.responseText.trim());
                if (data)
                {
                    totalTareas = data.total;
                    if (totalTareas > 0)
                    {
                        var intIdDepartamento = null;//undefined
                        contadorDatos = 0;
                        tareasJS = data.encontrados;
                        for(i in tareasJS)
                        {
                            contadorDatos = contadorDatos+1;
                            if(contadorDatos<totalTareas)
                            {
                                departamentos += "{idTarea: '"+tareasJS[i]["idTarea"]+"' , nombreTarea: '"+tareasJS[i]["nombreTarea"]+"', idDepartamento: '"+tareasJS[i]["idDepartamento"]+"'},";
 
                            }
                            else
                            {
                                departamentos += "{idTarea: '"+tareasJS[i]["idTarea"]+"' , nombreTarea: '"+tareasJS[i]["nombreTarea"]+"', idDepartamento: '"+tareasJS[i]["idDepartamento"]+"'}";

                            }
                        }
                        
                        Ext.define('ListadoDetalleOrden',
                        {
                            extend: 'Ext.data.Model',
                            fields:
                            [
                                {name: 'departamento',               type: 'string'},
                                {name: 'departamento_id',            type: 'string'},
                                {name: 'empleado',                   type: 'string'},
                                {name: 'empleado_id',                type: 'string'},
                                {name: 'cuadrilla',                  type: 'string'},
                                {name: 'cuadrilla_id',               type: 'string'},
                                {name: 'ingeniero',                  type: 'char'},
                                {name: 'ingeniero_id',               type: 'string'},
                                {name: 'fecha',                      type: 'date'},
                                {name: 'hora_inicio',                type: 'date'},
                                {name: 'hora_fin',                   type: 'date'},
                                {name: 'observacion',                type: 'string'},
                                {name: 'tarea_id',                   type: 'string'},
                                {name: 'tarea_adicional',            type: 'boolean'},
                                {name: 'proceso_id',                 type: 'string'},
                                {name: 'paramResponsable',           type: 'string'},
                                {name: 'nombreProceso',              type: 'string'},
                                {name: 'nombreTarea',                type: 'string'},
                            ]
                        });

                    dataStoreServicios = Ext.create('Ext.data.Store',
                        {
                            autoDestroy: true,
                            model: 'ListadoDetalleOrden',
                            proxy:
                            {
                                type: 'memory',
                                reader:
                                {
                                    type: 'json',
                                    root: 'personaFormasContacto',
                                    totalProperty: 'total'
                                }
                            }
                        });
                        
                        gridServicios = Ext.create('Ext.grid.Panel',
                        {
                            id: 'gridServicios',
                            store: dataStoreServicios,
                            //renderTo: 'lista_informacion_pre_cargada',
                            width: 800,
                            height: 200,
                            title: 'Listado de planificación',
                            frame: true,
                            viewConfig:
                                {
                                    getRowClass: function(record, index)
                                    {
                                        if (record.get('hijo'))
                                        {
                                            return 'hijo';
                                        }
                                    }
                                },
                            selModel:
                                {
                                    selType: 'cellmodel'
                                },
                            //plugins: [cellEditing],
                            columns:
                                [
                                    new Ext.grid.RowNumberer(),
                                    {
                                        text: 'Departamento',
                                        width: 100,
                                        dataIndex: 'departamento',
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'IdEmpleado',
                                        width: 10,
                                        dataIndex: 'empleado_id',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },
                                    {
                                        text: 'Empleado',
                                        width: 180,
                                        dataIndex: 'empleado',
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'Cuadrilla',
                                        dataIndex: 'cuadrilla',
                                        align: 'center',
                                        width: 80,
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'Ing IPCCL2/Activación',
                                        dataIndex: 'ingeniero',
                                        align: 'center',
                                        width: 150,
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'Fecha',
                                        width: 100,
                                        dataIndex: 'fecha',
                                        xtype: 'datecolumn',
                                        format: 'Y-m-d',
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'Hora Inicio',
                                        width: 60,
                                        dataIndex: 'hora_inicio',
                                        xtype: 'datecolumn',
                                        format: 'H-i',
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'Hora Fin',
                                        width: 60,
                                        dataIndex: 'hora_fin',
                                        xtype: 'datecolumn',
                                        format: 'H-i',
                                        tdCls: 'x-change-cell'
                                    },
                                    {
                                        text: 'Observación',
                                        width: 130,
                                        dataIndex: 'observacion',
                                        tdCls: 'x-change-cell',
                                    },
                                    {
                                        text: 'IdTarea',
                                        width: 100,
                                        dataIndex: 'caractCodigoPromoIns',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },{
                                        text: 'TareaAdicional',
                                        width: 10,
                                        dataIndex: 'tarea_adicional',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },
                                    {
                                        text: 'ProcesoId',
                                        width: 10,
                                        dataIndex: 'proceso_id',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },
                                    {
                                        text: 'paramResponsable',
                                        width: 10,
                                        dataIndex: 'paramResponsable',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },
                                    {
                                        text: 'nombreProceso',
                                        width: 10,
                                        dataIndex: 'nombreProceso',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },
                                    {
                                        text: 'nombreTarea',
                                        width: 10,
                                        dataIndex: 'nombreTarea',
                                        tdCls: 'x-change-cell',
                                        hidden: true
                                    },
                                    {
                                        header: 'Acciones',
                                        xtype: 'actioncolumn',
                                        width: 100,
                                        sortable: true,

                                        items:
                                            [

                                            ]
                                    },
                                    {
                                        dataIndex: 'servicio',
                                        hidden: true
                                    }
                                ]
                        });
                        
                        ////////////////DEPARTAMENTOS//////////////////////   
                        eval("var Departamentos = Ext.create('Ext.data.Store', { " +
                            " fields: ['idTarea',  'nombreTarea', 'idDepartamento'], " +
                            " autoload: false ,"+
                            " data: [ " +
                            departamentos +
                            "  ]" +
                            " });    ");
                        
                        text_tarea = new Ext.form.ComboBox({
                            id: "cmb_Departamento",
                            name: "cmb_Departamento",
                            fieldLabel: "Departamento ",
                            width: 290,
                            emptyText: 'Seleccione Departamento',
                            store: eval(Departamentos),
                            displayField: 'nombreTarea',
                            valueField: 'idTarea',
                            listeners:{
                                select: function(combo, records, eOpts){
                                    Ext.getCmp('hidden_id_tarea').setValue(records[0].get('idTarea'));
                                    validaDepartamento();
                                    Ext.getCmp('hidden_nombre_tarea').setValue(records[0].get('nombreTarea'));
                                    intIdDepartamento = records[0].get('idDepartamento');
                                    if(intIdDepartamento !='undefined')
                                    {
                                        Ext.getCmp('cmb_cuadrilla_').clearValue();
                                        Ext.getCmp('cmb_cuadrilla_').getStore().load({
                                            params: {
                                                idDepartamento: intIdDepartamento
                                            }
                                        });
                                    }
                                    
                                }
                            }
                        });
                        
                        // **************** TAREAS ******************       
                        eval("var storeTareas = Ext.create('Ext.data.Store', { " +
                                "  id: 'storeTarea', " +
                                " fields: ['idTarea',  'nombreTarea'], " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/ajaxGetTareasByProceso'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                        combo_tareas = new Ext.form.ComboBox({
                            id: 'cmb_tarea',
                            name: 'cmb_tarea',
                            fieldLabel: "Tarea",
                            anchor: '100%',
                            queryMode: 'local',
                            width: 300,
                            emptyText: 'Seleccione Tarea',
                            store: eval("storeTareas"),
                            displayField: 'nombreTarea',
                            valueField: 'idTarea',
                            layout: 'anchor',
                            disabled: true,
                            visible: false,
                            listeners:{
                                select: function(combo, records, eOpts){
                                    Ext.getCmp('hidden_nombre_tarea2').setValue(records[0].get('nombreTarea'));
                                }
                            }
                        });
             
                        eval("var storeProcesos = Ext.create('Ext.data.Store', { " +
                                "  id: 'storeProceso', " +
                                " fields: ['id',  'nombreProceso'], " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getProcesos'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'registros'" +
                                "  }" +
                                "  }" +
                                " });    ");
                        combo_proceso = new Ext.form.ComboBox({
                            id: 'cmb_proceso',
                            name: 'cmb_proceso',
                            fieldLabel: "Proceso",
                            anchor: '100%',
                            queryMode: 'remote',
                            width: 300,
                            emptyText: 'Seleccione Proceso',
                            store: eval("storeProcesos"),
                            displayField: 'nombreProceso',
                            valueField: 'id',
                            layout: 'anchor',
                            disabled: true,
                            visible: false,
                            listeners:{
                                select: function(combo, records, eOpts){
                                    intIdProceso = records[0].get('id');
                                    Ext.getCmp('hidden_nombre_proceso').setValue(records[0].get('nombreProceso'));
                                    Ext.getCmp('cmb_tarea').clearValue();
                                    Ext.getCmp('cmb_tarea').getStore().load({
                                        params: {
                                            id: intIdProceso
                                        }
                                    });
                                }
                            }
                        });

                        strIniHtmlCheck = '<input type="checkbox" id="checkDepartamento" onclick="validaCheck()" value="Otros">&nbsp;Otros';
                        var check = Ext.create('Ext.Component', {
                                html: strIniHtmlCheck,
                                width: 100,
                                padding: 10,
                                hidden: boolEsHousing,
                                style: {color: '#000000'}
                            });
                            //******** hidden id tarea
                            var hidden_tarea_id = new Ext.form.Hidden({
                                id: 'hidden_id_tarea',
                                name: 'hidden_id_tarea',
                                value: 0
                            });
                            //******** hidden nombre tarea
                            var hidden_tarea_nombre = new Ext.form.Hidden({
                                id: 'hidden_nombre_tarea',
                                name: 'hidden_nombre_tarea',
                                value: ""
                            });
                            //******** hidden nombre proceso
                            var hidden_proceso_nombre = new Ext.form.Hidden({
                                id: 'hidden_nombre_proceso',
                                name: 'hidden_nombre_proceso',
                                value: ""
                            });
                            //******** hidden nombre tarea manual
                            var hidden_tarea_nombre2 = new Ext.form.Hidden({
                                id: 'hidden_nombre_tarea2',
                                name: 'hidden_nombre_tarea2',
                                value: ""
                            });
                            //******** hidden nombre empleado
                            var hidden_nombre_emp = new Ext.form.Hidden({
                                id: 'hidden_nombre_empleado',
                                name: 'hidden_nombre_empleado',
                                value: ""
                            });
                            
                            //******** hidden nombre cuadrilla
                            var hidden_nombre_cua = new Ext.form.Hidden({
                                id: 'hidden_nombre_cuadrilla',
                                name: 'hidden_nombre_cuadrilla',
                                value: ""
                            });
                            
                            //******** hidden nombre ingeniero
                            var hidden_nombre_tec = new Ext.form.Hidden({
                                id: 'hidden_nombre_tecnico',
                                name: 'hidden_nombre_tecnico',
                                value: ""
                            });
                            var extraParamsEmpleadosPorPerfil = "";
                        
                            //******* id del departamento
                            //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
                            var strIniHtml = '';
                            if (prefijoEmpresa == "TNP")
                            {
                                strIniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual('+ 
                                             ' this.value);" checked="" value="empleado" name="tipoResponsable_">&nbsp;Empleado' +
                                             '';
                            }
                            else
                            {
                                if (typeof rec.get('arrayPersonalizacionOpcionesGridCoordinar') !== 'undefined'
                                    && rec.get('arrayPersonalizacionOpcionesGridCoordinar').hasOwnProperty(nombreOpcionPersonalizadaGridCoordinar))
                                {
                                    var arrayInfoPersonalizacionPlanificar  = 
                                        rec.get('arrayPersonalizacionOpcionesGridCoordinar')[nombreOpcionPersonalizadaGridCoordinar].split("|");
                                    var arrayTipoAsignacionesPermitidas     = arrayInfoPersonalizacionPlanificar[0].split(";");
                                    var arrayPerfilesAsignacionesPermitidas = arrayInfoPersonalizacionPlanificar[1].split(";");
                                    var valueTipoAsignacionPermitida = "";
                                    var nombreTipoAsignacionPermitida = "";
                                    var contadorArrayTipoAsignacionesPermitidas = 0;
                                    var valueChecked = "";
                                    var arrayInfoTipoAsignacionPermitida = [];
                                    let lengthTipoAsignacionesPermitidas = arrayTipoAsignacionesPermitidas.length;
                                    for (let tipoAsignacionesPermitidas of arrayTipoAsignacionesPermitidas) {
                                        arrayInfoTipoAsignacionPermitida = tipoAsignacionesPermitidas.split("-");
                                        valueTipoAsignacionPermitida = arrayInfoTipoAsignacionPermitida[0];
                                        nombreTipoAsignacionPermitida = arrayInfoTipoAsignacionPermitida[1];
                                        if(valueTipoAsignacionPermitida === "empleado")
                                        {
                                            extraParamsEmpleadosPorPerfil = 
                                                " extraParams: "+
                                                " {" +
                                                "     aplicaFiltroEmpleadosXPerfil: 'SI', " +
                                                "     nombrePerfilEmpleadosXAsignar: '" +
                                                        arrayPerfilesAsignacionesPermitidas[contadorArrayTipoAsignacionesPermitidas]+"' "+
                                                " }, ";
                                        }
                                        contadorArrayTipoAsignacionesPermitidas++;
                                        if(contadorArrayTipoAsignacionesPermitidas === 1)
                                        {
                                           valueChecked = 'checked="" ';
                                        }
                                        else
                                        {
                                            valueChecked = "";
                                        }
                                        strIniHtml = strIniHtml 
                                           + '<input type="radio" '
                                           + 'onchange="cambiarTipoResponsable_Individual( this.value);" '+valueChecked
                                           + 'value="'+valueTipoAsignacionPermitida+'" name="tipoResponsable_">'
                                           + '&nbsp;'+nombreTipoAsignacionPermitida;
                                        if(contadorArrayTipoAsignacionesPermitidas !== lengthTipoAsignacionesPermitidas)
                                        {
                                            strIniHtml = strIniHtml + '&nbsp;&nbsp;';
                                        }
                                        arrayInfoTipoAsignacionPermitida = [];
                                    }                                    
                                }
                                else
                                {

                                    strIniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual( this.value);" checked="" value="empleado" name="tipoResponsable_" id="radioEmp">&nbsp;Empleado' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" onchange="cambiarTipoResponsable_Individual( this.value);" value="cuadrilla" name="tipoResponsable_" id="radioCua">&nbsp;Cuadrilla' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" onchange="cambiarTipoResponsable_Individual( this.value);" value="empresaExterna" name="tipoResponsable_" id="radioExt">&nbsp;Contratista' +
                                    '';
                                }
                            }

                            RadiosTiposResponsable = Ext.create('Ext.Component', {
                                html: strIniHtml,
                                width: 350,
                                padding: 10,
                                hidden: boolEsHousing,
                                style: {color: '#000000'}
                            });
                            // **************** EMPLEADOS ******************
                            Ext.define('EmpleadosList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empleado', type: 'int'},
                                    {name: 'nombre_empleado', type: 'string'}
                                ]
                            });                 
                            eval("var storeEmpleados_= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpleados_', " +
                                "  model: 'EmpleadosList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpleados'," +
                                extraParamsEmpleadosPorPerfil +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_empleados = new Ext.form.ComboBox({
                                id: 'cmb_empleado_',
                                name: 'cmb_empleado_',
                                fieldLabel: "Empleados",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Empleado',
                                store: eval("storeEmpleados_"),
                                displayField: 'nombre_empleado',
                                valueField: 'id_empleado',
                                layout: 'anchor',
                                disabled: false,
                                listeners: {
                                    select: function(combo, records, eOpts) {
                                        Ext.getCmp('hidden_nombre_empleado').setValue(records[0].get('nombre_empleado'));
                                    }
                                }
                            });
                            // ****************  EMPRESA EXTERNA  ******************
                            Ext.define('EmpresaExternaList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empresa_externa', type: 'int'},
                                    {name: 'nombre_empresa_externa', type: 'string'}
                                ]
                            });
                            eval("var storeEmpresaExterna_= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpresaExterna_', " +
                                "  model: 'EmpresaExternaList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  },actionMethods: { " +
                                " create: 'POST', read: 'POST', update: 'POST', destroy: 'POST' " +
                                " }, " +
                                "  }" +
                                " });    ");
                            combo_empresas_externas = new Ext.form.ComboBox({
                                id: 'cmb_empresa_externa_',
                                name: 'cmb_empresa_externa_',
                                fieldLabel: "Contratista",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Contratista',
                                store: eval("storeEmpresaExterna_"),
                                displayField: 'nombre_empresa_externa',
                                valueField: 'id_empresa_externa',
                                layout: 'anchor',
                                visible: !boolEsHousing,
                                disabled: true
                            });
                            // **************** CUADRILLAS ******************
                            eval("var storeCuadrillas_= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeCuadrillas_', " +
                                "  fields: ['id_cuadrilla',  'nombre_cuadrilla'], " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }," +
                                " extraParams: { " +
                                "   idDepartamento: " + intIdDepartamento +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_cuadrillas = new Ext.form.ComboBox({
                                id: 'cmb_cuadrilla_',
                                name: 'cmb_cuadrilla_',
                                fieldLabel: "Cuadrilla",
                                anchor: '100%',
                                queryMode: 'local',
                                width: 350,
                                emptyText: 'Seleccione Cuadrilla',
                                store: eval("storeCuadrillas_"),
                                displayField: 'nombre_cuadrilla',
                                valueField: 'id_cuadrilla',
                                //layout: 'anchor',
                                hidden: boolEsHousing,
                                disabled: true,
                                listeners: {
                                    select: function(combo, records, eOpts) {
                                        Ext.getCmp('hidden_nombre_cuadrilla').setValue(records[0].get('nombre_cuadrilla'));
                                        seteaLiderCuadrilla(combo.getId(), combo.getValue());
                                    }
                                }
                            });
                            Ext.getCmp('cmb_cuadrilla_').getStore().load();
                            //******** html vacio...
                            var iniHtmlVacio = '';
                            Vacio = Ext.create('Ext.Component', {
                                html: iniHtmlVacio,
                                width: 350,
                                padding: 8,
                                layout: 'anchor',
                                style: {color: '#000000'}
                            });
                            formPanel = Ext.create('Ext.form.Panel', {
                                //bodyPadding: 5,
                                waitMsgTarget: true,
                                id: 'panelLiderCuadrilla_',
                                name: 'panelLiderCuadrilla_',
                                height: 80,
                                width: 350,
                                hidden: boolEsHousing,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    msgTarget: 'side'
                                },
                                items:
                                    [
                                        {
                                            xtype: 'fieldset',
                                            title: 'Lider de Cuadrilla',
                                            bodyStyle: 'margin: 1px;',
                                            defaultType: 'textfield',
                                            items:
                                                [
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Persona:',
                                                        id: 'idPersona_',
                                                        name: 'idPersona_',
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Nombre:',
                                                        id: 'nombreLider_',
                                                        name: 'nombreLider_',
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'PersonaEmpresaRol:',
                                                        id: 'idPersonaEmpresaRol_',
                                                        name: 'idPersonaEmpresaRol_',
                                                        hidden: true,
                                                        value: ""
                                                    }
                                                ]
                                        }
                                    ]
                            });
                            feIni = new Date();
                            hoIni = "00:00";
                            hoFin = "00:30";
                            if (rec.get("fePlanificada") !== "")
                            {

                                var strFecha = rec.get("fePlanificada");
                                var dia = strFecha.substr(0, 2);
                                var mes = strFecha.substr(3, 2);
                                var anio = strFecha.substr(6, 4);

                                feIni = new Date(anio, mes - 1, dia);
                                hoIni = rec.get("HoraIniPlanificada");
                                hoFin = rec.get("HoraFinPlanificada");
                            }

                            DTFechaProgramacion = Ext.create('Ext.data.fecha', {
                                id: 'fechaProgramacion_',
                                name: 'fechaProgramacion_',
                                fieldLabel: '* Fecha',
                                minValue: new Date(),
                                value: feIni,
                                labelStyle: "color:red;"
                            });
                            THoraInicio = Ext.create('Ext.form.TimeField', {
                                fieldLabel: '* Hora Inicio',
                                format: 'H:i',
                                id: 'ho_inicio_value_',
                                name: 'ho_inicio_value_',
                                minValue: '00:01 AM',
                                maxValue: '22:59 PM',
                                increment: 30,
                                value: hoIni,
                                editable: false,
                                labelStyle: "color:red;",
                                listeners: {
                                    select: {fn: function(valorTime, value) {
                                            
                                            var strValor                = valorTime.getId();
                                            var valueEscogido           = valorTime.getValue();
                                            var valueEscogido2          = new Date(valueEscogido);
                                            var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 30);
                                            var horaTotal               = new Date(valueEscogidoAumentMili);
                                            
                                            var h   = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                                            var m   = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                                            
                                            var horasTotalFormat = h + ":" + m;
                                            
                                            var strValorI = strValor.substr(16,1)
                                            Ext.getCmp('ho_fin_value_' + strValorI).setMinValue(horaTotal);
                                            $("input[name='ho_fin_value_" + strValorI + "']'").val(horasTotalFormat);
                                        }}
                                }
                            });
                            THoraFin = Ext.create('Ext.form.TimeField', {
                                fieldLabel: '* Hora Fin',
                                format: 'H:i',
                                id: 'ho_fin_value_',
                                name: 'ho_fin_value_',
                                minValue: '00:30 AM',
                                maxValue: '23:59 PM',
                                increment: 30,
                                value: hoFin,
                                editable: false,
                                labelStyle: "color:red;"
                            });
                            var txtObservacionPlanf = Ext.create('Ext.form.TextArea',
                                {
                                    fieldLabel: '',
                                    name: 'txtObservacionPlanf_',
                                    id: 'txtObservacionPlanf_',
                                    value: rec.get("observacionOpcionPyl"),
                                    allowBlank: false,
                                    width: 300,
                                    height: 100,
                                    listeners:
                                        {
                                            blur: function(field)
                                            {
                                                observacionPlanF = field.getValue();
                                            }
                                        }
                                });
                                
                                var btbAgregar = Ext.create('Ext.Button',{
                                    text: 'Agregar',
                                    style: "font-weight:bold; margin-left: 120px;",
                                    handler: function(){
                                        agregarListado(origen,rec,tipo_solicitud);
                                    }
                                });
                            var container = Ext.create('Ext.container.Container',
                                {
                                    layout: {
                                        type: 'hbox'
                                    },
                                    width: 900,
                                    
                                    items: [
                                        {
                                            xtype: 'panel',
                                            border: false,
                                            layout: {type: 'hbox', align: 'stretch'},
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    defaultType: 'textfield',
                                                    style: "font-weight:bold; margin-bottom: 15px; border-right:none",
                                                    layout: 'anchor',
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: false,
                                                            frame: false
                                                        },
                                                    items: [DTFechaProgramacion,
                                                        THoraInicio,
                                                        THoraFin,
                                                        hidden_tarea_id,hidden_tarea_nombre,hidden_proceso_nombre,hidden_tarea_nombre2,hidden_nombre_emp,hidden_nombre_cua,hidden_nombre_tec,
                                                        check,
                                                        text_tarea,
                                                        RadiosTiposResponsable,
                                                        combo_empleados,
                                                        combo_cuadrillas,
                                                        combo_empresas_externas,
                                                        formPanel,
                                                        Vacio,
                                                        combo_tecnicos,
                                                        Vacio]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    style: "margin-bottom: 15px; border-left:none",
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: true,
                                                            frame: false

                                                        },
                                                    items: [
                                                        {html: "Observación de Planificación:", border: false, width: 325},
                                                        txtObservacionPlanf,
                                                        Vacio,
                                                        combo_proceso,
                                                        combo_tareas,
                                                        Vacio,
                                                        btbAgregar]
                                                }]
                                        }
                                ]
                                });
                            formPanelAsignacionIndividual.items.add(container);
                            combo_tecnicos.setVisible(false);
                            
                            var containerTable = Ext.create('Ext.container.Container',
                                {
                                    layout: {
                                        type: 'hbox'
                                    },
                                    width: 900,
                                    
                                    items: [
                                        {xtype: 'panel',
                                            border: false,
                                            region: 'south',
                                            width: 900,
                                            split: true,
                                            //layout: {type: 'hbox', align: 'stretch'},
                                            items: [
                                            gridServicios
                                            ]
                                        }
                                //fin panel
                                ]
                                });
                            formPanelAsignacionIndividual.items.add(containerTable);

                            if ((prefijoEmpresa == "TN" && (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")) 
                                || (prefijoEmpresa == "TNP" && rec.get("ultimaMilla") == "FTTx" && tipo_solicitud == "Solicitud Planificacion"))
                            {
                                combo_tecnicos.setVisible(true);
                            }
                            
                            if(rec.get("muestraIngL2") == "N")
                            {
                                combo_tecnicos.setVisible(false);
                            }    

                            Ext.getCmp('cmb_empleado_').setVisible(true);
                            Ext.getCmp('cmb_cuadrilla_').setVisible(false);
                            Ext.getCmp('cmb_empresa_externa_').setVisible(false);
                            Ext.getCmp('panelLiderCuadrilla_').setVisible(false);

                            //Para productos housing a coordinar no existe asignacion de responsable
                            if (boolEsHousing)
                            {
                                combo_tecnicos.setVisible(false);
                                Ext.getCmp('cmb_empleado_').setVisible(false);
                                Ext.getCmp('cmb_cuadrilla_').setVisible(false);
                                Ext.getCmp('cmb_empresa_externa_').setVisible(false);
                                Ext.getCmp('panelLiderCuadrilla_').setVisible(false);
                            }

                            container.doLayout();
                            formPanelAsignacionIndividual.doLayout();

                        /*var tabs = new Ext.TabPanel({
                            xtype     :'tabpanel',
                            activeTab : 0,
                            autoScroll: false,
                            layoutOnTabChange: true,
                            items: [formPanelAsignacionIndividual]
                        });*/

                        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Formulario Asignacion Individual',
                            layout: 'fit',
                            resizable: false,
                            modal: true,
                            closable: false,
                            items: ([formPanelAsignacionIndividual])
                        });

                        if (rec.get('producto') === 'WIFI Alquiler Equipos')
                        {
                            winAsignacionIndividual.on('afterrender', function() {
                                connGetAsignadosTarea.request({
                                    method: 'POST',
                                    url: "../coordinar/getAsignadosTarea",
                                    params: {
                                        servicioId: id_servicio,
                                        idSolicitud: id_factibilidad,
                                        idPunto: rec.get('id_punto')
                                    },
                                    success: function(response) {
                                        var data = Ext.JSON.decode(response.responseText.trim());
                                        let status = data.status ? data.status : null;
                                        
                                        if (status)
                                        {
                                            var messagebox=  Ext.MessageBox.show({
                                                title: 'Información Importante',
                                                msg: data.data,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.INFO
                                            });

                                            Ext.Function.defer(function () {
                                                messagebox.zIndexManager.bringToFront(messagebox);
                                            },100);

                                        }
                                    }
                                });
                            });
                        }
                        winAsignacionIndividual.show();
                    } else
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "No se han podido obtener tareas asociadas a este servicio. Por favor informe a Sistemas.",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                } else {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: "Ocurrio un Error en la Obtencion de las Tareas",
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            },
            failure: function(result) {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: result.responseText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });

    }

}
/**
 * Funcion que permite agregar un label con la informacion del tipo de red.
 *
 * @param {*} rec ➜ Representa el objeto del servicio.
 * @returns {*}
 */
function agregarLabelTipoRed(rec) {

    /*Si el elemento cuenta con tipo de red, significa que pertenece a GPON y
    mostrará un textField con la información del tipo de red.*/
    if (rec.get('strTipoRed'))
    {
        objFieldStyle = {
            'backgroundColor': '#F0F2F2',
            'backgrodunImage': 'none',
            'color': 'green'
        };

        Ext.getCmp('service-data-fieldset').add({
            xtype: 'textfield',
            fieldCls:'animated bounceIn details-disabled',
            fieldLabel: 'Tipo de red',
            name: 'tipo_red',
            id: 'tipo_red',
            value: typeof rec.get('strTipoRed') != undefined ? "«" + rec.get('strTipoRed') + "»" : '',
            allowBlank: true,
            readOnly: true,
            hidden: typeof rec.get('strPrefijoEmpresa') != undefined && rec.get('strPrefijoEmpresa') != "TN",
            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};
                color:${objFieldStyle.color};`
        });
    }
}

function cierraVentanaAsignacionIndividual() {
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}

function cambiarTipoResponsable_Individual(valor)
{
    if (valor == "empleado")
    {
        cuadrillaAsignada = "S";
        Ext.getCmp('panelLiderCuadrilla_' ).setVisible(false);
        Ext.getCmp('cmb_empleado_').setVisible(true);
        Ext.getCmp('cmb_cuadrilla_').setVisible(false);
        Ext.getCmp('cmb_empresa_externa_').setVisible(false);
        Ext.getCmp('cmb_empleado_').setDisabled(false);
        Ext.getCmp('cmb_cuadrilla_').setDisabled(true);
        Ext.getCmp('cmb_empresa_externa_').setDisabled(true);
    } else if (valor == "cuadrilla")
    {
        Ext.getCmp('panelLiderCuadrilla_').setVisible(true);
        Ext.getCmp('cmb_empleado_').setVisible(false);
        Ext.getCmp('cmb_cuadrilla_').setVisible(true);
        Ext.getCmp('cmb_empresa_externa_').setVisible(false);
        Ext.getCmp('cmb_empleado_').setDisabled(true);
        Ext.getCmp('cmb_cuadrilla_').setDisabled(false);
        Ext.getCmp('cmb_empresa_externa_').setDisabled(true);
    } else if (valor == "empresaExterna")
    {
        cuadrillaAsignada = "S";
        Ext.getCmp('panelLiderCuadrilla_').setVisible(false);
        Ext.getCmp('cmb_empleado_').setVisible(false);
        Ext.getCmp('cmb_cuadrilla_').setVisible(false);
        Ext.getCmp('cmb_empresa_externa_').setVisible(true);
        Ext.getCmp('cmb_empleado_').setDisabled(true);
        Ext.getCmp('cmb_cuadrilla_').setDisabled(true);
        Ext.getCmp('cmb_empresa_externa_').setDisabled(false);
    }
}

function cambiarTipoResponsable_IndividualR(valor)
{
    if (valor == "empleado")
    {
        cuadrillaAsignada = "S";
        Ext.getCmp('panelLiderCuadrillaR_' ).setVisible(false);
        Ext.getCmp('cmb_empleadoR_').setVisible(true);
        Ext.getCmp('cmb_cuadrillaR_').setVisible(false);
        Ext.getCmp('cmb_empresa_externaR_').setVisible(false);
        Ext.getCmp('cmb_empleadoR_').setDisabled(false);
        Ext.getCmp('cmb_cuadrillaR_').setDisabled(true);
        Ext.getCmp('cmb_empresa_externaR_').setDisabled(true);
    } else if (valor == "cuadrilla")
    {
        Ext.getCmp('panelLiderCuadrillaR_').setVisible(true);
        Ext.getCmp('cmb_empleadoR_').setVisible(false);
        Ext.getCmp('cmb_cuadrillaR_').setVisible(true);
        Ext.getCmp('cmb_empresa_externaR_').setVisible(false);
        Ext.getCmp('cmb_empleadoR_').setDisabled(true);
        Ext.getCmp('cmb_cuadrillaR_').setDisabled(false);
        Ext.getCmp('cmb_empresa_externaR_').setDisabled(true);
    } else if (valor == "empresaExterna")
    {
        cuadrillaAsignada = "S";
        Ext.getCmp('panelLiderCuadrillaR_').setVisible(false);
        Ext.getCmp('cmb_empleadoR_').setVisible(false);
        Ext.getCmp('cmb_cuadrillaR_').setVisible(false);
        Ext.getCmp('cmb_empresa_externaR_').setVisible(true);
        Ext.getCmp('cmb_empleadoR_').setDisabled(true);
        Ext.getCmp('cmb_cuadrillaR_').setDisabled(true);
        Ext.getCmp('cmb_empresa_externaR_').setDisabled(false);
    }
}


function validaCheck() {
  // Get the checkbox
  var checkBox = document.getElementById("checkDepartamento");
  // Get the output text
  if (checkBox.checked != true){
    Ext.getCmp('cmb_proceso').setDisabled(true);
    Ext.getCmp('cmb_tarea').setDisabled(true);
    Ext.getCmp('cmb_proceso').setVisible(false);
    Ext.getCmp('cmb_tarea').setVisible(false);
    Ext.getCmp('cmb_tecnico').setDisabled(false);
    Ext.getCmp('cmb_Departamento').setDisabled(false);  
    document.getElementsByName("tipoResponsable_").forEach(e => {
      e.disabled = false;
    });
    //radios[2].disabled = false;
  } else {
    document.getElementById("radioEmp").checked = true;
    cambiarTipoResponsable_Individual("empleado");
    document.getElementsByName("tipoResponsable_").forEach(e => {
      e.disabled = true;
    });
    Ext.getCmp('cmb_Departamento').setValue("");  
    Ext.getCmp('cmb_Departamento').setDisabled(true);  
    Ext.getCmp('cmb_proceso').setVisible(true);
    Ext.getCmp('cmb_tarea').setVisible(true);
    Ext.getCmp('cmb_proceso').setDisabled(false);
    Ext.getCmp('cmb_tarea').setDisabled(false);
    Ext.getCmp('cmb_tecnico').setDisabled(true);
  }
}
function validaDepartamento() {
  // Get the checkbox
  var tareaDepartamento = Ext.getCmp('hidden_id_tarea').value;
  // Get the output text
  if ( tareaDepartamento != 'undefined'){
    Ext.getCmp('cmb_proceso').setDisabled(true);
    Ext.getCmp('cmb_tarea').setDisabled(true);
    Ext.getCmp('cmb_proceso').setVisible(false);
    Ext.getCmp('cmb_tarea').setVisible(false);
    Ext.getCmp('cmb_tecnico').setDisabled(false);
    Ext.getCmp('cmb_tecnico').setVisible(true);
  } else {
    Ext.getCmp('cmb_proceso').setVisible(true);
    Ext.getCmp('cmb_tarea').setVisible(true);
    Ext.getCmp('cmb_proceso').setDisabled(false);
    Ext.getCmp('cmb_tarea').setDisabled(false);
    Ext.getCmp('cmb_tecnico').setDisabled(true);
    Ext.getCmp('cmb_tecnico').setVisible(false);
  }
}


function seteaLiderCuadrilla(id, cuadrilla)
{
    connAsignarResponsable2.request({
        url: url_asignar_responsable,
        method: 'post',
        params:
            {
                cuadrillaId: cuadrilla
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);
            if (text.existeTablet == "S")
            {
                cuadrillaAsignada = "S";
                Ext.getCmp('nombreLider_').setValue(text.nombres);
                Ext.getCmp('idPersona_').setValue(text.idPersona);
                Ext.getCmp('idPersonaEmpresaRol_').setValue(text.idPersonaEmpresaRol);
            } else
            {
                var alerta = Ext.Msg.alert("Alerta", "La cuadrilla " + text.nombreCuadrilla + " no posee tablet asignada. Realice la asignación de \n\
                                                     tablet correspondiente o seleccione otra cuadrilla.");
                Ext.defer(function() {
                    alerta.toFront();
                }, 50);
                cuadrillaAsignada = "N";
                Ext.getCmp('cmb_cuadrilla_').setValue("");
                Ext.getCmp('nombreLider_').setValue("");
                Ext.getCmp('idPersona_').setValue("");
                Ext.getCmp('idPersonaEmpresaRol_').setValue("");
            }
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}

var connAsignarResponsable2 = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Consultando el lider, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

function agregarListado(origen, rec, tipo_solicitud)
{
    var idTarea         = "";
    var nombreTarea     = "";
    var boolError       = true;
    var boolErrorTecnico = true;
    var idPerTecnico    = 0;
    if (origen == "local")
    {
        id = rec.data.id_factibilidad;

        if ((prefijoEmpresa == "TN" && (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion"))
            || (prefijoEmpresa == "TNP" && rec.get("ultimaMilla") == "FTTx" && tipo_solicitud == "Solicitud Planificacion"))
        {
            idPerTecnico = Ext.getCmp('cmb_tecnico').value;
            if (idPerTecnico)
            {
                boolErrorTecnico = false;
            }
            var tareaDepartamentoId = Ext.getCmp('hidden_id_tarea').value;
            if ( tareaDepartamentoId == 'undefined')
            {
                boolErrorTecnico = false;
            }
        }
    } else if (origen == "otro" || origen == "otro2")
    {
        if (id == null || !id || id == 0 || id == "0" || id == "")
        {
            boolError = false;
            Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
        }
    } else
    {
        boolError = false;
        Ext.Msg.alert('Alerta', 'No hay opcion escogida');
    }
    var checkBox = document.getElementById("checkDepartamento");
    tareaDepartamentoId = Ext.getCmp('hidden_id_tarea').value;
    // Get the output text
    if (boolErrorTecnico && (checkBox.checked != true && tareaDepartamentoId != 'undefined'))
    {
        Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
    } else
    {
        if (boolError)
        {
            var paramResponsables = '';
            var boolErrorTareas = false;
            var mensajeError = "";
            var banderaEscogido         = $("input[name='tipoResponsable_']:checked").val();
            var codigoEscogido          = "";
            var tituloError             = "";
            var idPersona               = "0";
            var idPersonaEmpresaRol     = "0";
            var strObservacion          = Ext.getCmp('txtObservacionPlanf_').value;
            var strFechaProgramacion    = Ext.getCmp('fechaProgramacion_').value;
            var strHoraInicio           = Ext.getCmp('ho_inicio_value_').value;
            var strHoraFin              = Ext.getCmp('ho_fin_value_').value;
            var comboDepartamento       = Ext.getCmp('cmb_Departamento').value;
            var nomDepartamento         = Ext.getCmp('hidden_nombre_tarea').value;
            var comboProceso            = "";
            var nombreProceso           = "";
            var comboTarea              = "";
            var nombreCuadrilla         = "";
            var idCuadrilla             = "";
            var nombreEmpleado          = "";
            var empleadoId              = "";
            var ingeniero_id            = Ext.getCmp('cmb_tecnico').value;
            var ingeniero               = "";
            var tareaAdicional          = false;
            //alert(nomDepartamento);
            if (comboDepartamento == "" || comboDepartamento == null)
            {
                tituloError = "Departamento no seleccionado.";
                mensajeError +=  tituloError + "<br>";
            }
            if (ingeniero_id == "" || ingeniero_id == null)
            {
                tituloError = "Ingeniero no seleccionado.";
                mensajeError +=  tituloError + "<br>";
            }
            else
            {
                ingeniero = Ext.getCmp('hidden_nombre_tecnico').value; 
            }
            
            if (banderaEscogido == "empleado")
            {
                tituloError    = "Empleado no seleccionado.";
                codigoEscogido = Ext.getCmp('cmb_empleado_').value;
                empleadoId     = Ext.getCmp('cmb_empleado_').value;
                nombreEmpleado = Ext.getCmp('hidden_nombre_empleado').value;
                if(codigoEscogido == "" || codigoEscogido == null)
                {
                    mensajeError +=  tituloError + "<br>";
                }
            }
            if (banderaEscogido == "cuadrilla")
            {
                tituloError     = "Cuadrilla no seleccionada.";
                codigoEscogido  = Ext.getCmp('cmb_cuadrilla_').value;
                idCuadrilla     = Ext.getCmp('cmb_cuadrilla_').value;
                empleadoId      = Ext.getCmp('idPersona_').value;
                nombreCuadrilla = Ext.getCmp('hidden_nombre_cuadrilla').value; 
                nombreEmpleado  = Ext.getCmp('nombreLider_').value;
                idPersona = Ext.getCmp('idPersona_').getValue();
                idPersonaEmpresaRol = Ext.getCmp('idPersonaEmpresaRol_').getValue();
                if(codigoEscogido == "" || codigoEscogido == null)
                {
                    mensajeError +=  tituloError + "<br>";
                }

            }
            if (banderaEscogido == "empresaExterna")
            {
                tituloError = "Contratista no seleccionado.";
                codigoEscogido = Ext.getCmp('cmb_empresa_externa_').value;
                empleadoId     = Ext.getCmp('cmb_empresa_externa_').value;
                if(codigoEscogido == "" || codigoEscogido == null)
                {
                    mensajeError +=  tituloError + "<br>";
                }
            }
            if (!strObservacion || strObservacion == "" || strObservacion == 0)
            {
                tituloError = "La Observación no fue ingresada, por favor ingrese.";
                boolErrorTareas = true;
                mensajeError +=  tituloError + "<br>";
            }

            if (!strFechaProgramacion || strFechaProgramacion == "" || strFechaProgramacion == 0)
            {
                tituloError = "La fecha de Programación no fue seleccionada, por favor seleccione.";
                boolErrorTareas = true;
                mensajeError +=  tituloError + "<br>";
            }

            if (!strHoraInicio || strHoraInicio == "" || strHoraInicio == 0)
            {
                tituloError = "La hora de inicio no fue seleccionada, por favor seleccione.";
                boolErrorTareas = true;
                mensajeError +=  tituloError + "<br>";
            }

            if (!strHoraFin || strHoraFin == "" || strHoraFin == 0)
            {
                tituloError = "La hora de inicio no fue seleccionada, por favor seleccione";
                boolErrorTareas = true;
                mensajeError += "Tarea:" + Ext.getCmp('hidden_nombre_tarea').getValue() + " -- Hora Fin: " + tituloError + "<br>";
            }

            checkBox = document.getElementById("checkDepartamento");
            tareaDepartamentoId = Ext.getCmp('hidden_id_tarea').value;
            // Get the output text
            if (checkBox.checked == true || tareaDepartamentoId == 'undefined'){
                if(checkBox.checked == true)
                {
                    nomDepartamento = "";
                }
                //nomDepartamento = "";
                mensajeError    = "";
                tituloError     = "";
                comboProceso = Ext.getCmp('cmb_proceso').value;
                if (comboProceso == "" || comboProceso == null)
                {
                    tituloError = "Proceso no seleccionado.";
                    boolErrorTareas = true;
                    mensajeError +=  tituloError + "<br>";
                }

                nombreProceso = Ext.getCmp('hidden_nombre_proceso').value;
                tareaAdicional = true;
                comboTarea = Ext.getCmp('cmb_tarea').value;
                if (comboTarea == "" || comboTarea == null)
                {
                    tituloError = "Tarea no seleccionada.";
                    boolErrorTareas = true;
                    mensajeError +=  tituloError + "<br>";
                }
                if (!strObservacion || strObservacion == "" || strObservacion == 0)
                {
                    tituloError = "La Observación no fue ingresada, por favor ingrese.";
                    boolErrorTareas = true;
                    mensajeError +=  tituloError + "<br>";
                }
                tituloError    = "Empleado no seleccionado.";
                codigoEscogido = Ext.getCmp('cmb_empleado_').value;
                empleadoId     = Ext.getCmp('cmb_empleado_').value;
                nombreEmpleado = Ext.getCmp('hidden_nombre_empleado').value;
                if(codigoEscogido == "" || codigoEscogido == null)
                {
                    mensajeError +=  tituloError + "<br>";
                }
                nombreTarea = Ext.getCmp('hidden_nombre_tarea2').value;
            }

            if (codigoEscogido && codigoEscogido != "")
            {
                paramResponsables = paramResponsables + +Ext.getCmp('hidden_id_tarea').getValue() + "@@" + banderaEscogido + "@@" + 
                    codigoEscogido + "@@" + idPersona + "@@" + idPersonaEmpresaRol;

                //alert(paramResponsables);
            } else
            {
                boolErrorTareas = true;
            }
            if(boolErrorTareas)
            {
                Ext.Msg.alert('Error', mensajeError, Ext.emptyFn);
            }

            var txtObservacion = Ext.getCmp('txtObservacionPlanf_').value;
            var fechaProgramacion = Ext.getCmp('fechaProgramacion_').value;
            var ho_inicio = Ext.getCmp('ho_inicio_value_').value;
            var ho_fin = Ext.getCmp('ho_fin_value_').value;



            var registro = 
            {
                'departamento':                       nomDepartamento, 
                'departamento_id':                    comboDepartamento, 
                'empleado':                           nombreEmpleado, 
                'empleado_id':                        empleadoId,   
                'cuadrilla':                          nombreCuadrilla,   
                'cuadrilla_id':                       idCuadrilla,   
                'ingeniero':                          ingeniero,   
                'ingeniero_id':                       ingeniero_id,   
                'fecha':                              fechaProgramacion,   
                'hora_inicio':                        ho_inicio,   
                'hora_fin':                           ho_fin,   
                'observacion':                        txtObservacion, 
                'tarea_id':                           comboDepartamento,  
                'nombreTarea':                        nombreTarea,  
                'tarea_adicional':                    tareaAdicional,
                'proceso_id':                         comboProceso,
                'nombreProceso':                      nombreProceso,
                'paramResponsable':                   paramResponsables
            }
            if(!boolErrorTareas)
            {
                var rec_plan = new ListadoDetalleOrden(registro);
                //comentamos la validación para que no se repita el departamento
                /*var existingRecord = dataStoreServicios.findBy(function(record){
                    return record.get('departamento') === nomDepartamento;
                });
                if(existingRecord !== -1){
                    //alert('Ya existe una tarea ha este departamento, favor revisar.');
                    Ext.Msg.alert('Error', 'Ya existe una tarea ha este departamento, Favor revisar.', Ext.emptyFn);
                }
                else
                {
                    dataStoreServicios.add(rec_plan);
                }*/
                dataStoreServicios.add(rec_plan);
            }
            
        }
    }
}


var connAsignarResponsable = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }

});

/////////////////////////////////////////REPLANIFICAR///////////////////////////////////////////////
var connCoordinar = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

function showRePlanificar(rec, origen, boolPermisoOpu)
{
    winRePlanificar = "";
    formRePlanificar = "";
    tituloCoordinar = rec.get("tituloCoordinar");
    var id_servicio = rec.get("id_servicio");
    var id_factibilidad = rec.get("id_factibilidad");
    var tipo_solicitud = rec.get("descripcionSolicitud");
    boolVisible = true;

    if (tituloCoordinar == '')
    {
        tituloCoordinar = 'Replanificar Instalación';
    }

    if (!winRePlanificar)
    {
        Ext.define('MotivosList', {
            extend: 'Ext.data.Model',
            fields: [
                 {name: 'id_motivo', mapping: 'intIdMotivo'},
                 {name: 'nombre_motivo', mapping: 'strMotivo'}
            ]
        });                 
        eval("var storeMotivos_= Ext.create('Ext.data.Store', { " +
            "  id: 'storeMotivos_', " +
            "  model: 'MotivosList', " +
            "  autoLoad: false, " +
            " proxy: { " +
            "   type: 'ajax'," +
            "    url : '../../planificar/coordinar/getMotivosReplanificacion'," +
            "   reader: {" +
            "        type: 'json'," +
            "       totalProperty: 'total'," +
            "        root: 'encontrados'" +
            "  }" +
            "  }" +
            " });    ");
        cmbMotivosRePlanificacion = new Ext.form.ComboBox({
            id: 'cmbMotivoRePlanificacion',
            name: 'cmbMotivoRePlanificacion',
            fieldLabel: "* Motivo",
            anchor: '100%',
            queryMode: 'remote',
            width: 300,
            emptyText: 'Seleccione Motivo',
            store: eval("storeMotivos_"),
            displayField: 'nombre_motivo',
            valueField: 'id_motivo',
            layout: 'anchor',
            labelStyle: "color:red;"
        });
        DTFechaProgramacion = new Ext.form.DateField({
            id: 'fechaProgramacion',
            fieldLabel: 'Fecha Actual',
            labelAlign: 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            editable: false,
            disabled: true,
            value: rec.get("fePlanificada")
                //anchor : '65%',
                //layout: 'anchor'
        });
        if (boolPermisoOpu && (prefijoEmpresa != "TNP") && 
            (rec.get("descripcionSolicitud") == "Solicitud Planificacion" ||
             rec.get("descripcionSolicitud") == "Solicitud De Instalacion Cableado Ethernet"))
        {
            DTFechaReplanificacion = Ext.create('Ext.Component', {
                html: "<br>"
            });
        } else
        {
            DTFechaReplanificacion = new Ext.form.DateField({
                id: 'fechaReplanificacion',
                fieldLabel: '* Fecha Replanificación',
                labelAlign: 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                editable: false,
                minValue: new Date(),
                value: new Date(),
                labelStyle: "color:red; visible:false",
                visible: boolVisible
            });
        }
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }
        
        var agregaItemInfoAdicional = "NO";
        var textoInfoAdicional      = "";
        if (rec.data.estado == "Asignada" && rec.get('producto') !== "WIFI Alquiler Equipos") {
            agregaItemInfoAdicional = "SI";
            textoInfoAdicional      = "Solicitud con estado Asignada. Se eliminarán todos los datos técnicos y se liberarán los "
                                      +"recursos de BackBone.";
        }
        
        panelInfoAdicionalSolReplanif = Ext.create('Ext.container.Container',
        {
            id:'panelInfoAdicionalSolReplanif',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            defaults:
            {
                width: '740px',
            },
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa === "MD")
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: id_factibilidad,
                                    strOpcionGestionSimultanea: 'REPLANIFICAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }
                                    
                                    if(agregaItemInfoAdicional === "SI")
                                    {
                                        Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                            Ext.create('Ext.Component', {
                                                width: '700px',
                                                html: '<div class="warningmessage">'+textoInfoAdicional+'</div>',
                                            })
                                        );
                                    }
                                    
                                    Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                        {
                                            title: 'Gestión Simultánea',
                                            xtype: 'fieldset',
                                            defaultType: 'textfield',
                                            style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                            layout: 'anchor',
                                            defaults:
                                                {
                                                    border: false,
                                                    frame: false,
                                                    width: '740px'
                                                },
                                            items: [
                                                {
                                                    xtype: 'panel',
                                                    border: false,
                                                    style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                    defaults:
                                                    {
                                                        width: '650px',
                                                    },
                                                    layout: {type: 'hbox', align: 'stretch'},
                                                    items: [
                                                        {                                                                                              
                                                            xtype: 'textfield',
                                                            hidden: true,
                                                            id:'tieneGestionSimultanea',
                                                            value:'SI'
                                                        },
                                                        Ext.create('Ext.Component', {
                                                            style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                            html: "<div>Esta solicitud #"+ id_factibilidad + " replanificará de manera simultánea "
                                                                  +strInfoNumSolicitudes+"."+"</div>",
                                                            layout: 'anchor'
                                                        }),
                                                        {
                                                            id: 'btnMasDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ver Detalle',
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('gridSolsSimultaneas').show();
                                                            }
                                                        },
                                                        {
                                                            id: 'btnMenosDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ocultar Detalle',
                                                            hidden: true,
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('gridSolsSimultaneas').hide();
                                                            }
                                                        }
                                                    ]
                                                },
                                                Ext.create('Ext.grid.Panel', {
                                                    id: 'gridSolsSimultaneas',
                                                    store: storeSolsSimultaneas,
                                                    style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; "
                                                           +"margin-bottom: 10px",
                                                    columnLines: true,
                                                    hidden: true,
                                                    columns: 
                                                    [
                                                        {
                                                            id: 'descripServicioSimultaneo',
                                                            header: 'Plan/Producto',
                                                            dataIndex: 'descripServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoServicioSimultaneo',
                                                            header: 'Estado servicio',
                                                            dataIndex: 'estadoServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'idSolSimultanea',
                                                            header: '# Solicitud',
                                                            dataIndex: 'idSolSimultanea',
                                                            width: 100,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'descripTipoSolSimultanea',
                                                            header: 'Tipo Solicitud',
                                                            dataIndex: 'descripTipoSolSimultanea',
                                                            width: 180,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoSolSimultanea',
                                                            header: 'Estado Solicitud',
                                                            dataIndex: 'estadoSolSimultanea',
                                                            width: 130,
                                                            sortable: true
                                                        }
                                                    ],
                                                    viewConfig: {
                                                        stripeRows: true
                                                    },
                                                    frame: true,
                                                    defaults:
                                                    {
                                                        width: '670px'
                                                    }
                                                })
                                            ]
                                        }
                                    );
                                }
                                else
                                {
                                    if(agregaItemInfoAdicional === "SI")
                                    {
                                        Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                            Ext.create('Ext.Component', {
                                                width: '700px',
                                                html: '<p style="" class="warningmessage">'+textoInfoAdicional+'</p>',
                                            })
                                        );
                                    }

                                    Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolReplanif').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        if(agregaItemInfoAdicional === "SI")
                        {
                            Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                Ext.create('Ext.Component', {
                                    width: '700px',
                                    html: '<p style="" class="warningmessage">'+textoInfoAdicional+'</p>',
                                })
                            );
                        }

                        Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                            {                                                                                              
                                xtype: 'textfield',
                                hidden: true,
                                id:'tieneGestionSimultanea',
                                value:'NO'
                            },
                        );
                        Ext.getCmp('panelInfoAdicionalSolReplanif').doLayout();
                    }
                }
            }
        });
        
//******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            // width: 600,
            // padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        formRePlanificar = Ext.create('Ext.form.Panel', {
            title: "Manual",
            buttonAlign: 'center',
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            layout: {
            },
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            layout: 'anchor',
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: rec.get("esRecontratacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Punto',
                            defaultType: 'textfield',
                            id: 'service-data-fieldset',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("productoServicio"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Enlace',
                                    name: 'tipoEnlace',
                                    id: 'tipoEnlace',
                                    value: rec.get("strTipoEnlace"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                itemTercerizadora,
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Telefonos',
                                    name: 'telefonos_punto',
                                    id: 'telefonos_punto',
                                    value: rec.get("telefonos"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion_punto',
                                    id: 'observacion_punto',
                                    value: rec.get("observacion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                            ]
                        }
                    ]
                },
                panelInfoAdicionalSolReplanif
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var txtObservacion  = Ext.getCmp('txtObservacionPlanf').value;
                        var cmbMotivo       = Ext.getCmp('cmbMotivoRePlanificacion').value;
                        var boolPerfilOpu   = true;
                        var id_factibilidad = rec.get("id_factibilidad");
                        if (prefijoEmpresa == "TNP" || !boolPermisoOpu || (boolPermisoOpu && 
                            (rec.get("descripcionSolicitud") != "Solicitud Planificacion" &&
                             rec.get("descripcionSolicitud") != "Solicitud De Instalacion Cableado Ethernet")))
                        {
                            boolPerfilOpu            = false;
                            var fechaReplanificacion = Ext.getCmp('fechaReplanificacion').value;
                            var ho_inicio            = Ext.getCmp('ho_inicio_value').value;
                            var ho_fin               = Ext.getCmp('ho_fin_value').value;
                            var boolError            = false;
                            var mensajeError = "";
                            if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'El id del Detalle Solicitud no existe.');
                            }
                            if (!fechaReplanificacion || fechaReplanificacion == "" || fechaReplanificacion == 0)
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'La fecha de Replanificación no fue seleccionada, por favor seleccione.');
                            }
                            if (!ho_inicio || ho_inicio == "" || ho_inicio == 0)
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'La hora de fin no fue seleccionada, por favor seleccione.');
                            }
                            if (!ho_fin || ho_fin == "" || ho_fin == 0)
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'La hora de fin no fue seleccionada, por favor seleccione.');
                            }
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            Ext.Msg.alert('Alerta', 'El motivo no fue escogido, por favor seleccione.');
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            Ext.Msg.alert('Alerta', 'La observacion no fue ingresada, por favor ingrese.');
                        }

                        var param = '';
                        var boolErrorTecnico = true;
                        var idPerTecnico = 0;
                        if (origen == "local")
                        {
                            id = rec.data.id_factibilidad;
                            param = rec.data.id_factibilidad;
                            if (prefijoEmpresa == "TN" 
                                && (rec.data.descripcionSolicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion"))
                            {
                                idPerTecnico = Ext.getCmp('cmb_tecnico').value;
                                if (idPerTecnico)
                                {
                                    boolErrorTecnico = false;
                                }
                            }
                        } else if (origen == "otro" || origen == "otro2")
                        {
                            if (id == null || !id || id == 0 || id == "0" || id == "")
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
                            }
                        } else
                        {
                            boolError = true;
                            Ext.Msg.alert('Alerta', 'No hay opcion escogida');
                        }

                        if (boolErrorTecnico)
                        {
                            //Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
                            boolError = true;
                        } else
                        {
                            if (!boolError)
                            {
                                var paramResponsables = '';
                                var boolErrorTareas = false;
                                //mensajeError = "";
                                for (i in tareasJS)
                                {
                                    var banderaEscogido = $("input[name='tipoResponsable_" + i + "']:checked").val();
                                    var codigoEscogido = "";
                                    var tituloError = "";
                                    var idPersona = "0";
                                    var idPersonaEmpresaRol = "0";
                                    if (banderaEscogido == "empleado")
                                    {
                                        tituloError = "Empleado ";
                                        codigoEscogido = Ext.getCmp('cmb_empleado_' + i).value;
                                    }
                                    if (banderaEscogido == "cuadrilla")
                                    {
                                        tituloError = "Cuadrilla";
                                        codigoEscogido = Ext.getCmp('cmb_cuadrilla_' + i).value;
                                        idPersona = Ext.getCmp('idPersona_' + i).getValue();
                                        idPersonaEmpresaRol = Ext.getCmp('idPersonaEmpresaRol_' + i).getValue();
                                    }
                                    if (banderaEscogido == "empresaExterna")
                                    {
                                        tituloError = "Contratista";
                                        codigoEscogido = Ext.getCmp('cmb_empresa_externa_' + i).value;
                                    }
                                    if (codigoEscogido && codigoEscogido != "")
                                    {
                                        paramResponsables = paramResponsables + +tareasJS[i]['idTarea'] + "@@" + banderaEscogido + "@@" + codigoEscogido + "@@" +
                                            idPersona + "@@" + idPersonaEmpresaRol;
                                        if (i < (tareasJS.length - 1))
                                        {
                                            paramResponsables = paramResponsables + '|';
                                        }
                                    } else
                                    {
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Combo: " + tituloError + "<br>";
                                    }
                                }

                            }
                        }
                        
                        if (!boolError && !boolErrorTareas)
                        {
                            connCoordinar.request({
                                url: "../../planificar/coordinar/replanificar",
                                method: 'post',
                                timeout: 450000,
                                params: {origen: origen,
                                    id: id_factibilidad,
                                    param: param,
                                    paramResponsables: paramResponsables,
                                    idPerTecnico: idPerTecnico,
                                    ho_inicio: ho_inicio,
                                    ho_fin: ho_fin,
                                    observacion: txtObservacion,
                                    id_motivo: cmbMotivo,
                                    fechaReplanificacion: fechaReplanificacion,
                                    boolPerfilOpu: boolPerfilOpu
                                },
                                success: function(response) {
                                    var text        = response.responseText;
                                    var intPosicion = text.indexOf("Correctamente");

                                    if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                        text == "Se replanifico la solicitud" || intPosicion !== -1)
                                    {
                                        cierraVentanaRePlanificar();
                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                var permiso1 = '{{ is_granted("ROLE_139-111") }}';
                                                var boolPermiso1 = (Ext.isEmpty(permiso1)) ? false : (permiso1 ? true : false);
                                                var permiso2 = '{{ is_granted("ROLE_139-112")  }}';
                                                var boolPermiso2 = (Ext.isEmpty(permiso2)) ? false : (permiso2 ? true : false);
                                                if (!boolPermiso1 || !boolPermiso2) {
                                                    showMenuAsignacion('otro', id_factibilidad, false);
                                                }
                                            }
                                        });
                                    } else {
                                        Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Alerta', result.responseText);
                                }
                            });
                        } else {
                            Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRePlanificar();
                    }
                }
            ]
        });

        /* Funcion para agregar el label del tipo de red. */
        agregarLabelTipoRed(rec);

        combo_tecnicos = Ext.create('Ext.Component', {
            html: ""
        })
        if (prefijoEmpresa == "TN")
        {
            if (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")
            {
                storeTecnicos = new Ext.data.Store
                    ({
                        total: 'total',
                        pageSize: 25,
                        listeners: {
                        },
                        proxy:
                            {
                                type: 'ajax',
                                method: 'post',
                                url: '../../planificar/asignar_responsable/getTecnicos',
                                reader:
                                    {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                extraParams: {
                                    query: '',
                                    'tipo_esquema': rec.get("tipo_esquema")
                                },
                                actionMethods:
                                    {
                                        create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                    }
                            },
                        fields:
                            [
                                {name: 'id_tecnico', mapping: 'idPersonaEmpresaRol'},
                                {name: 'nombre_tecnico', mapping: 'info_adicional'},
                            ],
                        autoLoad: true
                    });
                const strIngeniero = rec.get("tipo_esquema") && rec.get("tipo_esquema") == 1 ? 'RADIO' : 'IPCCL2';
                if (rec.get('producto') === 'Cableado Estructurado')
                {
                    combo_tecnicos = new Ext.form.ComboBox({
                        id: 'cmb_tecnico',
                        name: 'cmb_tecnico',
                        fieldLabel: `Ingeniero Activación`,
                        anchor: '100%',
                        queryMode: 'remote',
                        emptyText: `Seleccione Ingeniero Activación`,
                        width: 350,
                        store: storeTecnicos,
                        displayField: 'nombre_tecnico',
                        valueField: 'id_tecnico',
                        layout: 'anchor',
                        disabled: false
                    }); 
                }
                else
                {
                    combo_tecnicos = new Ext.form.ComboBox({
                        id: 'cmb_tecnico',
                        name: 'cmb_tecnico',
                        fieldLabel: `Ingeniero ${strIngeniero}`,
                        anchor: '100%',
                        queryMode: 'remote',
                        emptyText: `Seleccione Ingeniero ${strIngeniero}`,
                        width: 350,
                        store: storeTecnicos,
                        displayField: 'nombre_tecnico',
                        valueField: 'id_tecnico',
                        layout: 'anchor',
                        disabled: false
                    });
                }
            }
        }
        connTareas.request({
            method: 'POST',
            url: "../../factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
            params: {servicioId: id_servicio, id_solicitud: id_factibilidad, nombreTarea: 'todas', estado: 'Activo', accion: 'Replanificar'},
            success: function(response) {
                var data = Ext.JSON.decode(response.responseText.trim());
                if (data)
                {
                    totalTareas = data.total;
                    if (totalTareas > 0)
                    {
                        tareasJS = data.encontrados;
                        for (i in tareasJS)
                        {
                            //******** hidden id tarea
                            /*var hidden_tarea = new Ext.form.Hidden({
                                id: 'hidden_id_tarea_' + i,
                                name: 'hidden_id_tarea_' + i,
                                value: tareasJS[i]["idTarea"]
                            });
                            //******** text nombre tarea
                            var text_tarea = new Ext.form.Label({
                                forId: 'txt_nombre_tarea_' + i,
                                style: "font-weight:bold; font-size:14px; color:red; margin-bottom: 15px;",
                                layout: 'anchor',
                                text: tareasJS[i]["nombreTarea"]
                            });*/
                            //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
                            var strIniHtml = '';
                            if (prefijoEmpresa == "TNP")
                            {
                                strIniHtml  = '<input type="radio" onchange="cambiarTipoResponsable_IndividualR(this.value);" checked="" value="empleado" name="tipoResponsable">&nbsp;Empleado' +
                                              '';
                            }
                            else
                            {
                                strIniHtml = '<input id="radioEmp" type="radio" onchange="cambiarTipoResponsable_IndividualR( this.value);" checked="" value="empleado" name="tipoResponsable">&nbsp;Empleado' +
                                    '&nbsp;&nbsp;' +
                                    '<input id="radioCua" type="radio" onchange="cambiarTipoResponsable_IndividualR( this.value);" value="cuadrilla" name="tipoResponsable">&nbsp;Cuadrilla' +
                                    '&nbsp;&nbsp;' +
                                    '<input id="radioExt" type="radio" onchange="cambiarTipoResponsable_IndividualR( this.value);" value="empresaExterna" name="tipoResponsable">&nbsp;Contratista' +
                                    '';
                            }
                            RadiosTiposResponsable = Ext.create('Ext.Component', {
                                html: strIniHtml,
                                width: 350,
                                padding: 10,
                                style: {color: '#000000'}
                            });
                            // **************** EMPLEADOS ******************
                            Ext.define('EmpleadosList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empleado', type: 'int'},
                                    {name: 'nombre_empleado', type: 'string'}
                                ]
                            });
                            eval("var storeEmpleados_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpleados_" + i + "', " +
                                "  model: 'EmpleadosList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpleados'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_empleados = new Ext.form.ComboBox({
                                id: 'cmb_empleadoR_',
                                name: 'cmb_empleadoR_',
                                fieldLabel: "Empleados",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Empleado',
                                store: eval("storeEmpleados_" + i),
                                displayField: 'nombre_empleado',
                                valueField: 'id_empleado',
                                layout: 'anchor',
                                disabled: false
                            });
                            // ****************  EMPRESA EXTERNA  ******************
                            Ext.define('EmpresaExternaList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empresa_externa', type: 'int'},
                                    {name: 'nombre_empresa_externa', type: 'string'}
                                ]
                            });
                            eval("var storeEmpresaExterna_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpresaExterna_" + i + "', " +
                                "  model: 'EmpresaExternaList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  },actionMethods: { " +
                                " create: 'POST', read: 'POST', update: 'POST', destroy: 'POST' " +
                                " }, " +
                                "  }" +
                                " });    ");
                            combo_empresas_externas = new Ext.form.ComboBox({
                                id: 'cmb_empresa_externaR_',
                                name: 'cmb_empresa_externaR_',
                                fieldLabel: "Contratista",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Contratista',
                                store: eval("storeEmpresaExterna_" + i),
                                displayField: 'nombre_empresa_externa',
                                valueField: 'id_empresa_externa',
                                layout: 'anchor',
                                disabled: true
                            });
                            // **************** CUADRILLAS ******************
                            Ext.define('CuadrillasList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_cuadrilla', type: 'int'},
                                    {name: 'nombre_cuadrilla', type: 'string'}
                                ]
                            });
                            eval("var storeCuadrillas_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeCuadrillas_" + i + "', " +
                                "  model: 'CuadrillasList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_cuadrillas = new Ext.form.ComboBox({
                                id: 'cmb_cuadrillaR_',
                                name: 'cmb_cuadrillaR_',
                                fieldLabel: "Cuadrilla",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Cuadrilla',
                                store: eval("storeCuadrillas_" + i),
                                displayField: 'nombre_cuadrilla',
                                valueField: 'id_cuadrilla',
                                layout: 'anchor',
                                disabled: true,
                                listeners: {
                                    select: function(combo) {

                                        seteaLiderCuadrilla(combo.getId(), combo.getValue());
                                    }
                                }
                            });
                            //******** html vacio...
                            var iniHtmlVacio = '';
                            Vacio = Ext.create('Ext.Component', {
                                html: iniHtmlVacio,
                                width: 350,
                                padding: 8,
                                layout: 'anchor',
                                style: {color: '#000000'}
                            });
                            formPanel = Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                waitMsgTarget: true,
                                id: 'panelLiderCuadrillaR_',
                                name: 'panelLiderCuadrillaR_',
                                height: 80,
                                width: 350,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    msgTarget: 'side'
                                },
                                items:
                                    [
                                        {
                                            xtype: 'fieldset',
                                            title: 'Lider de Cuadrilla',
                                            defaultType: 'textfield',
                                            items:
                                                [
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Persona:',
                                                        id: 'idPersona_',
                                                        name: 'idPersona_',
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'PersonaEmpresaRol:',
                                                        id: 'idPersonaEmpresaRol_',
                                                        name: 'idPersonaEmpresaRol_',
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Nombre:',
                                                        id: 'nombreLider_',
                                                        name: 'nombreLider_',
                                                        value: ""
                                                    }
                                                ]
                                        }
                                    ]
                            });
                            if (boolPermisoOpu && prefijoEmpresa != "TNP" &&
                                (rec.get("descripcionSolicitud") == "Solicitud Planificacion" ||
                                rec.get("descripcionSolicitud") == "Solicitud De Instalacion Cableado Ethernet"))
                            {
                                THoraInicio = Ext.create('Ext.Component', {
                                    html: "<br>"
                                });
                                THoraFin = Ext.create('Ext.Component', {
                                    html: "<br>"
                                });
                            } else
                            {
                                THoraInicio = Ext.create('Ext.form.TimeField', {
                                    fieldLabel: '* Hora Inicio',
                                    format: 'H:i',
                                    id: 'ho_inicio_value',
                                    name: 'ho_inicio_value',
                                    minValue: '00:01 AM',
                                    maxValue: '22:59 PM',
                                    increment: 30,
                                    value: "00:00",
                                    editable: false,
                                    labelStyle: "color:red;",
                                    listeners: {
                                        select: {fn: function(valorTime, value) {
                                                var valueEscogido = valorTime.getValue();
                                                var valueEscogido2 = new Date(valueEscogido);
                                                var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 30);
                                                var horaTotal = new Date(valueEscogidoAumentMili);
                                                var h = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                                                var m = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                                                var horasTotalFormat = h + ":" + m;
                                                Ext.getCmp('ho_fin_value').setMinValue(horaTotal);
                                                $('input[name="ho_fin_value"]').val(horasTotalFormat);
                                            }}
                                    }
                                });
                                THoraFin = Ext.create('Ext.form.TimeField', {
                                    fieldLabel: '* Hora Fin',
                                    format: 'H:i',
                                    id: 'ho_fin_value',
                                    name: 'ho_fin_value',
                                    minValue: '00:30 AM',
                                    maxValue: '23:59 PM',
                                    increment: 30,
                                    value: "00:30",
                                    editable: false,
                                    labelStyle: "color:red;"
                                });
                            }
                            txtInformacion = Ext.create('Ext.form.TextField', {
                                fieldLabel: 'Fecha Hora Inicio - Fin',
                                value: rec.get("fePlanificada") + " " + rec.get("HoraIniPlanificada") + " - " + rec.get("HoraFinPlanificada"),
                                allowBlank: false,
                                readOnly: true,
                            });
                            var txtObservacionPlanf = Ext.create('Ext.form.TextArea',
                                {
                                    fieldLabel: '',
                                    name: 'txtObservacionPlanf',
                                    id: 'txtObservacionPlanf',
                                    value: rec.get("observacionAdicional"),
                                    allowBlank: false,
                                    width: 300,
                                    height: 150,
                                    listeners:
                                        {
                                            blur: function(field)
                                            {
                                                observacionPlanF = field.getValue();
                                            }
                                        }
                                });
                            var container = Ext.create('Ext.container.Container',
                                {
                                    layout: {
                                        type: 'hbox'
                                    },
                                    width: 700,
                                    items: [
                                        {
                                            xtype: 'panel',
                                            border: false,
                                            layout: {type: 'hbox', align: 'stretch'},
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    defaultType: 'textfield',
                                                    style: "font-weight:bold; margin-bottom: 15px; border-right:none",
                                                    layout: 'anchor',
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: false,
                                                            frame: false
                                                        },
                                                    items: [txtInformacion,
                                                        DTFechaReplanificacion,
                                                        THoraInicio,
                                                        THoraFin,
                                                        cmbMotivosRePlanificacion,
                                                        RadiosTiposResponsable,
                                                        combo_empleados,
                                                        combo_cuadrillas,
                                                        combo_empresas_externas,
                                                        formPanel,
                                                        Vacio,
                                                        combo_tecnicos,
                                                        Vacio]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    style: "margin-bottom: 15px; border-left:none",
                                                    defaults:
                                                        {
                                                            width: '400px',
                                                            border: false,
                                                            frame: false

                                                        },
                                                    items: [
                                                        {html: "Observación:", border: false, width: 325},
                                                        txtObservacionPlanf]
                                                }]
                                        }]
                                });
                            formRePlanificar.items.add(container);
                            formRePlanificar.doLayout();
                            
                            if(rec.get("muestraIngL2") == "N")
                            {
                                combo_tecnicos.setVisible(false);
                            } 
                            
                            Ext.getCmp('cmb_empleadoR_').setVisible(true);
                            Ext.getCmp('cmb_cuadrillaR_').setVisible(false);
                            Ext.getCmp('cmb_empresa_externaR_').setVisible(false);
                            Ext.getCmp('panelLiderCuadrillaR_').setVisible(false);

                            //formPanelHalPrincipal = crearFormPanelHal('replanificar', rec, origen, 0, boolPermisoOpu);    

                            winRePlanificar = Ext.widget('window', {
                                title: tituloCoordinar,
                                layout: 'fit',
                                resizable: false,
                                modal: true,
                                closabled: false,
                                items: (formRePlanificar)
                                    /*boolPermisoAsignarTareaHal ? 
                                    ( rec.get("strTareaEsHal") == 'S' ? [formPanelHalPrincipal] : [formRePlanificar] ) : [formRePlanificar])*/
                            });
                            if (rec.get('producto') === 'Cableado Estructurado')
                            {
                                break;
                            }
                        }

                        winRePlanificar.show();
                    }
                }
            }
        });
    }
}

function cierraVentanaRePlanificar() {
    winRePlanificar.close();
    winRePlanificar.destroy();
}

//////////////////////////////////////DOCUMENTOS/////////////////////////////////

function presentarDocumentosCasos(rec)
{
    var id_servicio        = rec.get('id_servicio');
    var cantidadDocumentos = 1;
    var connDocumentosCaso = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.MessageBox.show({
                       msg: 'Consultando documentos, Por favor espere!!',
                       progressText: 'Saving...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connDocumentosCaso.request({
        url: url_documentos_valida,
        method: 'post',
        params:
            {
                idServicio   : id_servicio
            },
        success: function(response){
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if(cantidadDocumentos > 0)
            {
                var storeDocumentosCaso = new Ext.data.Store({
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url : url_documentos,
                        reader: {
                            type         : 'json',
                            totalProperty: 'total',
                            root         : 'encontrados'
                        },
                        extraParams: {
                            idServicio   : id_servicio
                        }
                    },
                    fields:
                        [
                            {name:'idDocumento',              mapping:'idDocumento'},
                            {name:'ubicacionLogica',          mapping:'ubicacionLogica'},
                            {name:'feCreacion',               mapping:'feCreacion'},
                            //{name:'usrCreacion',              mapping:'usrCreacion'},
                            {name:'linkVerDocumento',         mapping:'linkVerDocumento'},
                            {name:'boolEliminarDocumento',    mapping:'boolEliminarDocumento'}
                        ]
                });

                Ext.define('DocumentosCaso', {
                    extend: 'Ext.data.Model',
                    fields: [
                          {name:'ubicacionLogica',  mapping:'ubicacionLogica'},
                          {name:'feCreacion',       mapping:'feCreacion'},
                          {name:'linkVerDocumento', mapping:'linkVerDocumento'}
                    ]
                });

                //grid de documentos por Caso
                gridDocumentosCaso = Ext.create('Ext.grid.Panel', {
                    id:'gridMaterialesPunto',
                    store: storeDocumentosCaso,
                    columnLines: true,
                    columns: [{
                        header   : 'Nombre Archivo',
                        dataIndex: 'ubicacionLogica',
                        width    : 260
                    },
                    {
                        header   : 'Fecha de Carga',
                        dataIndex: 'feCreacion',
                        width    : 120
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 100,
                        items:
                        [
                            {
                                iconCls: 'button-grid-show',
                                tooltip: 'Ver Archivo Digital',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec         = storeDocumentosCaso.getAt(rowIndex);
                                    verArchivoDigital(rec);
                                }
                            }
                        ]
                    },

                ],
                    viewConfig:{
                        stripeRows:true,
                        enableTextSelection: true
                    },
                    frame : true,
                    height: 200
                });

                function verArchivoDigital(rec)
                {
                    var rutaFisica = rec.get('linkVerDocumento');
                    var posicion = rutaFisica.indexOf('/public')
                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                }

                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding  : 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget : 'side'
                    },
                    items: [

                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',

                        defaults: {
                            width: 550
                        },
                        items: [

                            gridDocumentosCaso

                        ]
                    }
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title   : 'Documentos Cargados',
                    modal   : true,
                    width   : 580,
                    closable: true,
                    layout  : 'fit',
                    items   : [formPanel]
                }).show();
            }
            else{
                Ext.Msg.show({
                title  :'Mensaje',
                msg    : 'No tiene archivos adjuntos',
                buttons: Ext.Msg.OK,
                animEl : 'elId',
                });
            }

        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}
