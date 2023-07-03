/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var boolPanel = true;
function Seguimiento() {

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
     * @version 1.0 01-03-2020
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
     * @version 1.0 01-03-2020
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
     * @version 1.0 01-03-2020
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
     * @version 1.0 01-03-2020
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
     * @version 1.0 01-03-2020
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
     * @version 1.0 01-03-2020
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
     * @version 1.0 01-03-2020
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
     * obtenerInformacionSeguimiento, Obtiene el detalle del cada estado
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-03-2020
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
        var entidadSolicitudSeguimiento = new Seguimiento();
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
                                    id: strCodigoEstacion+"Tooltip"+intIdDetalleSolicitudCab,  
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
     * @version 1.0 01-03-2020
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

/**
 * dibujar, Crea y dibuja el table con la información del seguimiento.
 * @author David León <mdleon@telconet.ec>
 * @version 1.0 01-03-2020
 * @since 1.0
 * 
 * @param intIdServicio
 */
function dibujar (intIdServicio)
{
    /**
     * change, Muestra en colores las celdas segun su tiempo de ejecución
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 01-03-2020
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
                text: 'No Data Found',
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
        Ext.getCmp('panel2').doLayout();
    }
});


}