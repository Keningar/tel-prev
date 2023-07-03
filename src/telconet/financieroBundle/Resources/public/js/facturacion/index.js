/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * Index Facturas
 */
Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;
var strFormato = '';

Ext.onReady(function () {
    
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'F.Emisión Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'F.Emisión Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        //anchor : '65%',
        //layout: 'anchor'
    });

    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    
    var estado_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_store_estados,
            reader: {
                type: 'json',
                root: 'estados'
            }
        }
    });
    var estado_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
        fieldLabel: 'Estado',
        width: 325
    });
        var strMensaje;
        formatoFactura();
        if (strNombrePais==="PANAMA")
        {
            strMensaje = "Debe ingresar el numero de la factura con formato "+strFormato;
            Ext.form.VTypes["numeroFacturaVtypeVal"] = Utils.REGEX_NUM_FACTFIS;
        }
        else
        {
            strMensaje = "Debe ingresar el numero de la factura con formato "+strFormato;
            Ext.form.VTypes["numeroFacturaVtypeVal"] = Utils.REGEX_NUM_FACTURA;
        }
	Ext.form.VTypes["numeroFacturaVtype"]=function(v){
		return Ext.form.VTypes["numeroFacturaVtypeVal"].test(v);
	}
	Ext.form.VTypes["numeroFacturaVtypeText"]=strMensaje;

    TFNumeroFactura = new Ext.form.TextField({
            id         : 'numeroFactura',
            name       : 'numeroFactura',
            labelAlign : 'left',
            fieldLabel : 'Numero Factura',
            xtype      : 'textfield',
            width      : 325,
            vtype      : 'numeroFacturaVtype',
            emptyText  : strFormato
    });	
	
    Ext.define('PtosList', 
    {
        extend : 'Ext.data.Model',
        fields : 
        [
            {
                name : 'id_pto_cliente', 
                type : 'int'
            },
            {
                name : 'descripcion_pto', 
                type : 'string'
            }
        ]
    });    
    
    storePtos = Ext.create('Ext.data.Store', 
    {
        model : 'PtosList',
        proxy : 
        {
            type   : 'ajax',
            url    : url_lista_ptos,
            reader : 
            {
                type          : 'json',
                totalProperty : 'total',
                root          : 'listado'
            }
        }
    });
    puntos_cmb = new Ext.form.ComboBox(
    {
        xtype         : 'combobox',
        store         : storePtos,    
        labelAlign    : 'left',   
        id            : 'idpunto',        
        name          : 'idpunto',
        valueField    : 'id_pto_cliente',  
        displayField  : 'descripcion_pto',        
        fieldLabel    : 'Login',
        width         : 325,
        triggerAction : 'query',
        lastQuery     : '',
        mode          : 'local',
        minChars      : 8
    });
        
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'Numerofacturasri', type: 'string'},
            {name: 'Punto', type: 'string'},
            {name: 'Cliente', type: 'string'},
            {name: 'Esautomatica', type: 'string'},
            {name: 'Feemision', type: 'string'},
            {name: 'Total', type: 'string'},
            {name: 'Fecreacion', type: 'string'},
            {name: 'Estado', type: 'string'},
            {name: 'linkVer', type: 'string'},
            {name: 'linkEliminar', type: 'string'},
            {name: 'strLinkClonar', type: 'string'},
            {name: 'strDebePintarBotonClonar', type: 'string'},
            {name: 'id', type: 'int'},
            {name: 'strCodigoDocumento', type: 'string'},
            {name: 'intIdTipoDocumento', type: 'int'},
            {name: 'linkImprimirPanama', type: 'string'},
            {name: 'empresa', type: 'string'},
            {name: 'boolMensajesCompElectronico', type: 'bool'},
            {name: 'boolImpresoraPanama', type: 'bool'},
            {name: 'boolVerificaActualiza', type: 'bool'},
            {name: 'boolDocumentoPdf', type: 'bool'},
            {name: 'boolDocumentoXml', type: 'bool'},
            {name: 'boolSimularCompElec', type: 'bool'},
            {name: 'boolVerificaEnvioNotificacion', type: 'bool'},
            {name: 'strEsElectronica', type: 'string'},
            {name: 'strMsnErrorComprobante', type: 'string'},
            {name: 'negocio', type: 'string'},
            {name: 'Feautorizacion', type: 'string'},
        ]
    });

    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            timeout: 90000,
            url: url_store_grid,
            reader: {
                type: 'json',
                root: 'documentos',
                totalProperty: 'total'
            },
            extraParams: 
            {
                fechaDesde    : '', 
                fechaHasta    : '', 
                estado        : '', 
                numeroFactura : '', 
                puntoId       : ''
            },
            simpleSortMode: true
        },
        listeners: {
            beforeload: function (store) 
            {
                store.getProxy().extraParams.fechaDesde    = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta    = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.estado        = Ext.getCmp('idestado').getValue();
                store.getProxy().extraParams.numeroFactura = Ext.getCmp('numeroFactura').getValue();
                store.getProxy().extraParams.puntoId       = Ext.getCmp('idpunto').getValue();
            },
            load: function (store) {
                store.each(function (record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

    if(intIdPunto>0)
    {
        store.load({params: {start: 0, limit: 10}});
        puntos_cmb.setDisabled(true);
    }



    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function (record) {
                    //arregloSeleccionados.push(record.data.idOsDet);
                });
                //console.log(arregloSeleccionados);

            }
        }
    });


    var listView = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 275,
        collapsible: false,
        title: '',
        selModel: sm,
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        disabled: true,
                        itemId: 'delete',
                        scope: this,
                        handler: function () {
                            eliminarAlgunos();
                        }
                    }]}],
        renderTo: Ext.get('lista_prospectos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando documentos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
            itemdblclick: function (view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Id Factura',
                width: 30,
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'Doc.',
                width: 40,
                dataIndex: 'strCodigoDocumento'
            }, {
                text: 'No. factura SRI',
                width: 110,
                dataIndex: 'Numerofacturasri'
            }, {
                text: 'Pto cliente',
                width: 100,
                dataIndex: 'Punto'
            }, {
                text: 'Cliente',
                width: 100,
                dataIndex: 'Cliente'
            }, {
                text: 'Auto?',
                dataIndex: 'Esautomatica',
                align: 'right',
                width: 35
            }, {
                text: 'Elec?',
                dataIndex: 'strEsElectronica',
                align: 'right',
                width: 35
            }, {
                text: 'Estado',
                dataIndex: 'Estado',
                align: 'right',
                width: 60
            }, {
                text: 'F. Creacion',
                dataIndex: 'Fecreacion',
                align: 'right',
                width: 100
            }, {
                text: 'F. Emision',
                dataIndex: 'Feemision',
                align: 'right',
                width: 100
            }, {
                text: 'F. Autorizacion',
                dataIndex: 'Feautorizacion',
                align: 'right',
                width: 100
            }, {
                text: 'Total',
                dataIndex: 'Total',
                align: 'right',
                width: 70
            }, {
                text: 'Acciones',
                width: 230,
                renderer: renderAcciones,
            }]
    });


    /**
     * Documentación de renderAcciones
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 19-10-2017 Se agrega el botón para reajustarImpuestos.
     * @since 1.0
     */
    function renderAcciones(value, p, record) {
        

        var iconos = '';
        var estadoIncidencia = true;
        var cliente = '';
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
        
        //Valida el permiso de reajustarImpuestos
        var objPermiso  = $("#ROLE_67-5517");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if(boolPermiso && record.data.Estado ==='Rechazado')
        {
            iconos = iconos + '<b><a href="#" onClick="reajustarImpuestos('
                + record.data.id + ');" title="Reajustar impuestos" class="button-grid icon-porcentaje"></a></b>';
        }

        //Solo para los records activos
        if(record.data.Estado=='Activo' && puede_anular)
        {
            cliente = record.data.Cliente.replace(/['"]+/g, '');
            iconos=iconos+'<b><a href="#" onClick="showProcesar('+record.data.id+ ','+"'"+ cliente+"'"+')" title="Anular" class="button-grid-delete"></a></b>';
        }
        if (puede_editar_sri && record.data.empresa=='TTCO') {
            iconos = iconos + '<b><a href="#" onClick="showEditarNumeroSri(' + record.data.id + ')" title="Editar" class="button-grid-edit"></a></b>';
            iconos = iconos + '<b><a href="#" onClick="showEditarFeEmision(' + record.data.id + ')" ';
            iconos = iconos + 'title="Editar Fecha Emision" class="button-grid-edit"></a></b>';
        }

        //verifica que el comprobante tenga mensajes para mostrar la pantalla de logs
        if (record.data.boolMensajesCompElectronico && record.data.empresa != 'TNG') {
             iconos = iconos + '<b><a href="#"  onClick="getMensajesCompElectronico(' + record.data.id + ')" title="Mensaje Comprobante" ';
             iconos = iconos + 'class="button-grid-logs"></a></b>';
        }
        //alert('imprimirFacturaPanama:'.record.data.boolImpresoraPanama);
        //Impresora Fiscal
        if (record.data.boolImpresoraPanama) 
        {   
            iconos = iconos + '<b><a href="#"  onClick="imprimirFacturaPanama(' + record.data.id + ')" title="Imprimir Comprobante" ';
            iconos = iconos + 'class="button-grid-recibopago"></a></b>';
        }
        //VALIDA DESCARGA COMPROBANTES
        var objPermiso = $("#ROLE_67-1837");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) {
            if (record.data.boolVerificaEnvioNotificacion) {
                    iconos = iconos + '<b><a href="#"  ';
                    iconos = iconos + 'onClick="downloadComprobante(' + record.data.id;
                    iconos = iconos + ',\'' + record.data.Numerofacturasri + '\',  \'xml\', ' + record.data.boolDocumentoXml;
                    iconos = iconos + ',\'' + record.data.strMsnErrorComprobante + '\')" title="Descargar XML" class="button-grid-xml"></a></b>';
                    iconos = iconos + '<b><a href="#"  onClick="downloadComprobante(' + record.data.id;
                    iconos = iconos + ',\'' + record.data.Numerofacturasri + '\',  \'pdf\', ' + record.data.boolDocumentoPdf;
                    iconos = iconos + ',\'' + record.data.strMsnErrorComprobante + '\')" title="Descargar Documento PDF" ';
                    iconos = iconos + 'class="button-grid-pdf"></a></b>';
            }
            //permite mostrar un boton para simular un XML y descargarlo
            if (record.data.boolSimularCompElec && record.data.empresa != 'TNG') {
               
                       iconos = iconos + '<b><a href="#" title="Simular XML" onClick="simularXML(' + record.data.id + ', ' 
                                + record.data.intIdTipoDocumento + ', '
                                + record.data.boolSimularCompElec + ')" class="button-grid-black-xml"></a> ';
             
            }
        }
        //VALIDA ACTUALIZA COMPROBANTE
        var objPermiso = $("#ROLE_67-1778");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) {
            //verifica que comprobante pueda ser actualizado
            if (record.data.boolVerificaActualiza) {
                iconos = iconos + '<b><a href="#"  onClick="actualizaComprobanteElec(' + record.data.id + ', ' + record.data.intIdTipoDocumento;
                iconos = iconos + ')" title="Actualiza Comprobante" class="button-grid-cambioVelocidad"></a></b>';
            }
        }

        //VALIDA ENVIA NOTIFICACION COMPROBANTE
        var objPermiso = $("#ROLE_67-1777");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) {
            if (record.data.boolVerificaEnvioNotificacion) {
                iconos = iconos + '<b><a href="#"  onClick="envioNotificacionComprobante(' + record.data.id;
                iconos = iconos + ')" title="Envio de Notificacion" class="button-grid-mail"></a></b>';
            }
        }
//(record.data.Estado!='Pendiente' && record.data.Estado!='Activo') &&
         //verifica si es empresa Guatemala, para mostrar boton de factura electronica
        if ((record.data.Estado!='Cerrado' && record.data.Estado!='Activo') && record.data.empresa == 'TNG') 
        {
            iconos = iconos + '<b><a href="#"  onClick="getFacturacionElectronicaGt(' + record.data.id + ')" title="Factura Electronica" ';
            iconos = iconos + 'class="button-grid-agregarFacturacionElectronica"></a></b>';
        }

        //Valida el permiso de clonacion de facturacion
        if(record.data.strDebePintarBotonClonar=="S")
        {
            iconos = iconos + '<b><a href="' + record.data.strLinkClonar + '" onClick="" title="Clonar Factura" class="button-grid-clonar-factura"></a></b>';
        }

        return Ext.String.format(
            iconos,
            value,
            '1',
            'nada'
            );
    }

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4,
            align: 'right',
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1000,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                //xtype: 'button',
                iconCls: "icon_search",
                handler: Buscar,
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
                    limpiar();
                }
            }

        ],
        items: [
            DTFechaDesde,
            {html: "&nbsp;", border: false, width: 50},
            DTFechaHasta,
            {html: "&nbsp;", border: false, width: 50},
            puntos_cmb,            
            {html: "&nbsp;", border: false, width: 50},
            estado_cmb,
            {html: "&nbsp;", border: false, width: 50},
            TFNumeroFactura,
            {html: "&nbsp;", border: false, width: 50}
        ],
        renderTo: 'filtro_prospectos'
    });

    function eliminarAlgunos() {
        var param = '';
        if (sm.getSelection().length > 0)
        {
            var estado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.idOrden;

                if (sm.getSelection()[i].data.estado == 'Eliminado')
                {
                    estado = estado + 1;
                }
                if (i < (sm.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (estado == 0)
            {
                Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            url: "delete_ajax",
                            method: 'post',
                            params: {param: param},
                            success: function (response) {
                                var text = response.responseText;
                                store.load();
                            },
                            failure: function (result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                });

            }
            else
            {
                alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }

    Ext.define('modelMotivos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    store_motivos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelMotivos",
        proxy: {
            type: 'ajax',
            url: url_listar_motivos,
            reader: {
                type: 'json',
                root: 'documentos'
            }
        }
    });
    
     //Proceso de Anulacion - Creacion del Store departamentos - rcoelloq
    storeDepartamentos = Ext.create('Ext.data.Store', {
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetDepartamentos,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'id_departamento',     mapping: 'id_departamento'},
            {name: 'nombre_departamento', mapping: 'nombre_departamento'}
        ],
        autoLoad: true
    });
    
});

/**
 * Documentacion del metodo Buscar() 
 * Ejecuta la busqueda cuando el usuario hace click en boton Buscar
 * 
 * Actualizacion: Se agrega que si no tiene punto en sesion entonces valida que busque por fechas
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 16-08-2016
 * 
 * Actualizacion: Se modifica el metodo para obtener por variable el formato de la factura
 * @author Germán Valenzuela <gvalenzuela@telconet.ec>
 * @version 1.2 08-08-2017
 */
function Buscar()
{
    //Si no tiene punto en sesion entonces valida que busque por fechas, login o numero de factura
    if(intIdPunto <= 0)
    {    
        if( (Ext.getCmp('fechaDesde').getValue() != null) && (Ext.getCmp('fechaHasta').getValue() != null) )
        {
            if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
            {
                Ext.Msg.show({
                title   : 'Error en Búsqueda',
                msg     : 'Por Favor para realizar la búsqueda [Fecha Desde] debe ser menor a [Fecha Hasta].',
                buttons : Ext.Msg.OK,
                animEl  : 'elId',
                icon    : Ext.MessageBox.ERROR
                });
            }
            else
            {
                //Cuando no hay login en sesion solo se permite buscar en un rango de 30 dias
                dias = (((parseInt(Ext.getCmp('fechaHasta').getValue().getTime() - Ext.getCmp('fechaDesde').getValue().getTime())/1000)/60)/60)/24;
                if (dias > 30)
                {
                    Ext.Msg.show({
                    title   : 'Error en Búsqueda',
                    msg     : 'Solo se puede consultar en un rango de 30 días',
                    buttons : Ext.Msg.OK,
                    animEl  : 'elId',
                    icon    : Ext.MessageBox.ERROR
                    });                    
                }
                else
                {    
                    if (Ext.getCmp('numeroFactura').getValue() != "")
                    {  
                        validaCriterioFactura();
                    }
                    else
                    {
                        store.load({params: {start: 0, limit: 10}});
                    }
                }
            }
        }
        else
        {
            formatoFactura(); 
            if((Ext.getCmp('idpunto').getValue() == null) 
                && ((Ext.getCmp('numeroFactura').getValue() == "") 
                || !Ext.getCmp('numeroFactura').isValid()))
            {    
                Ext.Msg.show({
                title:'Error en Búsqueda',
                msg: 'Si no tiene login en sesión debe buscar por [fechas], [login] o [número de factura con formato '+strFormato+'].',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
                });
            }
            else
            {  
                validaCriterioFactura();
            }    
        }
    }
    else
    {
        if (Ext.getCmp('numeroFactura').getValue() != "")
        {  
            validaCriterioFactura();
        }
        else
        {
            store.load({params: {start: 0, limit: 10}});
        }
    }    
}

/**
 * Funcion para obtener el formato de la factura
 * 
 * @author Germán Valenzuela <gvalenzuela@telconet.ec>
 * @version 1.0 08-08-2017
 */
function formatoFactura()
{
    if (strNombrePais==='PANAMA')
    {
        strFormato = '0000000000000-00000000';
    }
    else
    {
        strFormato = '000-000-000000000';
    }  
}


/**
 * Documentacion del metodo validaCriterioFactura()
 * Valida si el campo factura tiene ingresado datos con el formato correcto
 * 
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 26-08-2016
 * 
 * @author Germán Valenzuela <gvalenzuela@telconet.ec>
 * @version 1.1 08-08-2017  : Se modifica el metodo para obtener por variable el formato de factura
 */
function validaCriterioFactura()
{  
    if (Ext.getCmp('numeroFactura').isValid())
    {    
        store.load({params: {start: 0, limit: 10}});
    }
    else
    {
        formatoFactura();
        Ext.Msg.show({
            title   : 'Error en Búsqueda',
            msg     : '[El número de factura debe tener formato '+strFormato+'].',
            buttons : Ext.Msg.OK,
            animEl  : 'elId',
            icon    : Ext.MessageBox.ERROR
        });
    }        
}


function eliminar(direccion)
{
    //alert(direccion);
    Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function (btn) {
        if (btn == 'yes') {
            Ext.Ajax.request({
                url: url_procesar,
                method: 'post',
                success: function (response) {
                    var text = response.responseText;
                    store.load();
                },
                failure: function (result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

/**
 * Documentacion del metodo limpiar()
 * quita el valor de los campos de criterio de busqueda
 * 
 * Actualizacion: Se agrega los campos de criterio numeroFactura y idpunto
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 16-08-2016
 */
function limpiar() 
{
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");
    Ext.getCmp('idestado').setRawValue("");
    Ext.getCmp('numeroFactura').setValue(null);     
    Ext.getCmp('idpunto').setRawValue("");
    Ext.getCmp('idpunto').setValue(null);   
}
/**
 * El metodo actualizaComprobanteElec envia el id documento
 * para que el comprobante xml sea actualizado.
 * @param {int} intIdDocumento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 */
function actualizaComprobanteElec(intIdDocumento, intTipoDocumentoId) {
    Ext.Ajax.request({
        url: url_updateCompElectronico,
        method: 'post',
        params: {intIdDocumento: intIdDocumento, intTipoDocumentoId: intTipoDocumentoId},
        success: function (response) {
            var text = Ext.decode(response.responseText);
            if (text.boolConfirmacion == true) {
                Ext.Msg.alert('Success', text.strMensaje);
            } else {
                Ext.Msg.alert('Alert', text.strMensaje);
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
    store.load();
}

/**
 * El metodo envioNotificacionComprobante envia el id documento
 * para que busque la clave de acceso del comprobante y envie una notificacion
 * @param {int} intIdDocumento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 09-04-2018 Se setea valor para tiempo de timeout. 
 */
function envioNotificacionComprobante(intIdDocumento) {
    Ext.Ajax.request({
        timeout: 400000,
        url: url_envianotificacion,
        method: 'post',
        params: {intIdDocumento: intIdDocumento},
        success: function (response) {
            var text = Ext.decode(response.responseText);
            if (text.boolStatus == true) {
                Ext.Msg.alert('Success', text.strMensaje);
            } else {
                Ext.Msg.alert('Alert', text.strMensaje);
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
    store.load();
}

/**
 * El metodo downloadComprobante
 * permite descargar un archivo pdf, xml, txt
 * @param {int}    intIdDocumento
 * @param {string} strNombre
 * @param {string} strExtension
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 */
function downloadComprobante(intIdDocumento, strNombre, strExtension, boolDocumentoExist, strMsnErrorComprobante) {
    if(boolDocumentoExist == true){
        window.location = url_downloadCompElectronico+'?strNombre=' + strNombre + '&strExtension=' + strExtension+'&intIdDocumento='+intIdDocumento;
    }else{
        if(strMsnErrorComprobante != ''){
            Ext.Msg.alert('Error', strMsnErrorComprobante);
        }else{
            Ext.Msg.alert('Alerta', 'El archivo se encuentra vacio');
        }
    }
}
/**
 * El metodo simularXML
 * permite descargar un archivo xml sin guardarlo en la base
 * @param {int}    intIdDocumento Recibe el id del documento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 28-06-2016
 */
function simularXML(intIdDocumento, intIdTipoDocumento, boolSimularCompElec) {
    if (true === boolSimularCompElec) {
        window.location = url_simularCompElectronico + '?intIdDocumento=' + intIdDocumento + '&intIdTipoDocumento=' + intIdTipoDocumento;
    } else {
        Ext.Msg.alert('Alerta', 'No se puede simular XML para este documento.');
    }
}
/**
 * El metodo getMensajesCompElectronico
 * Obtiene el grid de mensajes de los comprobantes electronicos
 * @param {int} intIdDocumento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 */
function getMensajesCompElectronico(intIdDocumento) {
    var storeMensajesCompElectronico = new Ext.data.Store({
        pageSize: 10,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_mensajesCompElectronico,
            reader: {
                type: 'json',
                totalProperty: 'intTotalMensajes',
                root: 'storeMensajesCompElectronico'
            },
            extraParams: {
                intIdDocumento: intIdDocumento
            }
        },
        fields:
            [
                {name: 'strTipo', mapping: 'tipo'},
                {name: 'strMensaje', mapping: 'mensaje'},
                {name: 'strInformacionAdicional', mapping: 'informacionAdicional'},
                {name: 'strfeCreacion', mapping: 'feCreacion'}
            ]
    });

    gridMensajesCompElectronico = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialServicio',
        store: storeMensajesCompElectronico,
        autoScroll: true,
        columnLines: true,
        columns: [{
                //id: 'nombreDetalle',
                header: 'Tipo',
                dataIndex: 'strTipo',
                width: 90,
                sortable: true
            }, {
                header: 'Mensaje',
                dataIndex: 'strMensaje',
                width: 250
            },
            {
                header: 'Informacion Adicional',
                dataIndex: 'strInformacionAdicional',
                width: 478
            },
            {
                header: 'Fecha de Creacion',
                dataIndex: 'strfeCreacion',
                width: 110
            }],
        viewConfig: {
            stripeRows: true
        },
        frame: true,
        height: 200,
        width: 955,
        listeners: {
            itemdblclick: function (view, record, item, index, eventobj, obj)
            {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid)
            {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners: {
                            beforeshow: function updateTipBody(tip) {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });

            }
        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeMensajesCompElectronico,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}'
        })
    });

    var frmMensajeComprobante = Ext.create('Ext.form.Panel', {
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
                    width: 955
                },
                items: [
                    gridMensajesCompElectronico
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function () {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Mensajes Comprobantes Electronicos',
        modal: true,
        width: 1000,
        height: 300,
        closable: true,
        resizable: false,
        items: [frmMensajeComprobante]
    }).show();
}


function imprimirFacturaPanama(id) {
    Ext.MessageBox.wait('Procesando');
    Ext.Ajax.request({
        url: url_apiInterfazPanama,
        method: 'post',
        params: {id: id},
        success: function (response) 
        {
            var text = Ext.decode(response.responseText);
            if (text.strCodError === 'OK') 
            {
                Ext.Msg.alert('Success', text.strMensaje);
            } else {
                Ext.Msg.alert('Error', text.strMensaje);
            }
        },
        failure: function (rec, op) {
            var json = Ext.JSON.decode(op.response.responseText);
            Ext.Msg.alert('Alerta ', json.mensaje);
        }
    });
}


function imprimirFactura(id) {
    Ext.Ajax.request({
        url: "imprimirFacturaUnitaria",
        method: 'post',
        params: {id: id},
        success: function (response) {
// 		      var json = Ext.JSON.decode(response.responseText);
// 		      
// 		      if(json.success == true)
// 		      {
// 			      Ext.Msg.alert('Mensaje ', json.mensaje);
// 			      cierraVentanaEnviarPlantilla();
// 		      }
// 		      else
// 		      {
// 			      Ext.Msg.alert('Alerta ',json.mensaje);
// 			      //store.load();
// 		      }
        },
        failure: function (rec, op) {
            var json = Ext.JSON.decode(op.response.responseText);
            Ext.Msg.alert('Alerta ', json.mensaje);
        }
    });

}


function verificarCheck(value)
{
    if (value == 'Cliente')
    {  
        Ext.getCmp('cmbDepartamentos').setDisabled(true);
    }

    if (value == 'Empresa')
    {   
        Ext.getCmp('cmbDepartamentos').setDisabled(false);
    }
}

function verificar_informacion(responsable, cliente)
{
    var strTipoResponsable    = '';
    var strClienteResponsable = '';
    var strEmpresaResponsable = '';
    var strDepartamento       = null;
    var arrayResponsable = new Array();
    
    if(responsable)
    {
        strTipoResponsable     = 'Cliente';
        strClienteResponsable  = cliente ;
        strEmpresaResponsable  = '';
      
    }    
    else
    {   
        strDepartamento = Ext.getCmp('cmbDepartamentos').getValue();
        
        //Recupero departamento de la empresa.
        if(Ext.isEmpty(strDepartamento)  || strDepartamento == "Seleccione departamento...")
        {
            Ext.Msg.alert("Atención", "Debe seleccionar un departamento responsable");
            return null;
        }
        else
        {
            strTipoResponsable     = 'Empresa';
            strClienteResponsable  = cliente ;
            strEmpresaResponsable  = Ext.getCmp('cmbDepartamentos').getValue();
        }    
    }
    
     arrayResponsable[0]    = strTipoResponsable;
     arrayResponsable[1]    = strClienteResponsable;
     arrayResponsable[2]    = strEmpresaResponsable;
    
     
     return arrayResponsable;
}

function showProcesar(idfactura, cliente) {
    winDetalle = "";
    var responsable;
    var cambioResponsable=false;
    if (!winDetalle) {
        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch',
                columns: 1
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            width: 350,
            url: url_procesar,
            items: [{
                        id: 'idMotivo',
                        xtype: 'combo',
                        name: 'motivos',
                        fieldLabel: 'Motivos (*)',
                        hiddenName: 'motivos',
                        emptyText: 'Seleccione el motivo...',
                        store: store_motivos,
                        displayField: 'descripcion',
                        valueField: 'id',
                        selectOnFocus: true,
                        mode: 'local',
                        typeAhead: true,
                        editable: false,
                        triggerAction: 'all',
                    }, {
                        xtype: 'hiddenfield',
                        name: 'idfactura',
                        value: idfactura
                    }, {
                        xtype: 'hiddenfield',
                        name:  'strTipoResponsable' ,
                        value: ''
                    }, {
                        xtype: 'hiddenfield',
                        name:  'strClienteResponsable',
                        value: cliente
                    }, {
                        xtype: 'hiddenfield',
                        name:  'strEmpresaResponsable',
                        value: ''
                    }, {
                        xtype:'label',
                        name: 'responsables',
                        text: 'Responsables',
                        cls:  'font-weight:bold;',
                        margins: '0 0 10 0'
                    }, {
                        layout: 'form',
                        xtype: 'radiogroup',
                        labelWidth: 200,
                        vertical: true,
                       
                        items: [
                            {boxLabel: 'Cliente', name: 'responsable', inputValue: 'Cliente', checked: true},
                            {boxLabel: 'Empresa', name: 'responsable', inputValue: 'Empresa'},
                            
                        ],
                        listeners: {
                            change: function(field, newValue, oldValue) {
                                cambioResponsable = true;
                                var value = newValue.responsable;
                                
                                if (Ext.isArray(value)) {
                                    return;
                                }

                                verificarCheck(value);
                                
                                if(value == 'Cliente')
                                {   
                                    responsable = true;
                                    
                                }
                                else if(value=='Empresa')
                                {   
                                    responsable = false;
                                }
                            }
                        }
                    }, 
                        {
                        xtype: 'combo',
                        store:  storeDepartamentos, 
                        id: 'cmbDepartamentos',
                        name: 'cmbDepartamentos',
                        valueField: 'nombre_departamento',
                        displayField: 'nombre_departamento',
                        emptyText: 'Seleccione departamento...',
                        fieldLabel: '',
                        width: 150,
                        triggerAction: 'all',
                        selectOnFocus: true,
                        lastQuery: '',
                        mode: 'local',
                        allowBlank: true,
                        disabled: true,
                        style : 'border-width: 0px;width: 211px; table-layout: fixed; left: 0px;margin: -1px;top: 182px;margin-left: 102px;margin-top: -39px;',
                    }], 
            buttons: [{
                    text: 'Cancel',
                    handler: function () {
                        this.up('form').getForm().reset();
                        winDetalle.destroy();
                    }
                }, {
                    text: 'Grabar',
                    handler: function () {
                        if (form.getForm().findField('motivos').value === null) {
                            Ext.Msg.alert("Mensaje del sistema", "Es obligatorio seleccionar el motivo.");
                            return false;
                        }
                        if (!cambioResponsable)
                            responsable = true;
                        
                        var arrayResp = new Array();
                        var tamArrayResp = 0;
                        arrayResp    = verificar_informacion(responsable, cliente);
                        tamArrayResp = arrayResp.length;
                        if(tamArrayResp > 0){
                            form.getForm().findField('strTipoResponsable').setValue(arrayResp[0]); 
                            form.getForm().findField('strClienteResponsable').setValue(arrayResp[1]);
                            form.getForm().findField('strEmpresaResponsable').setValue(arrayResp[2]) ;   
                        } 
                        else
                        {
                            return false;
                        }
                        
                        var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
                            form1.submit({
                                waitMsg: "Procesando",
                                success: function (form, action) {
                                    var data = Ext.JSON.decode(action.response.responseText);
                                    Ext.MessageBox.show({
                                        icon: Ext.Msg.INFO,
                                        width: 500,
                                        height: 300,
                                        title: 'Mensaje del Sistema',
                                        msg: data.mensaje,
                                        buttonText: {yes: "Ok"},
                                    });
                                    store.load();
                                },
                                failure: function (form, action) {
                                    var data = Ext.decode(action.response.responseText);
                                    Ext.MessageBox.show({
                                        icon: Ext.Msg.INFO,
                                        width: 500,
                                        height: 300,
                                        title: 'Mensaje del Sistema',
                                        msg: data.mensaje,
                                        buttonText: {yes: "Ok"},
                                    });
                                }

                            });
                            this.up('window').hide();
                            winDetalle.destroy();
                        }
                    }
                }]
        });

        winDetalle = Ext.widget('window', {
            title: 'Procesar Anulación',
            closeAction: 'hide',
            width: 350,
            height: 300,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }
    winDetalle.show();
}
/**
 * Documentación para reajustarImpuestos
 * Realiza la petición AJAX para ejecutar el paquete que reajusta los impuestos en la factura.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0 10-10-2017 Versión inicial
 * @param {int} intIdDocumento
 * @returns void
 */
function reajustarImpuestos(intIdDocumento)
{
    Ext.Msg.confirm('Alerta', '¿Desea reajustar el valor de los impuestos para la presente factura?', function (btn) {
        if (btn === 'yes') {
            Ext.MessageBox.wait("Actualizando los impuestos para la presente factura...");
            $.ajax({
                type: "POST",
                data: "intIdDocumento=" + intIdDocumento,
                url: url_reajustar_impuestos,
                success: function (strMensaje)
                {
                    if (strMensaje === '')
                    {
                        strMensaje = 'Se ha reajustado la factura correctamente.';
                    }
                    Ext.Msg.alert('Mensaje del sistema', strMensaje);
                },
                error: function ()
                {
                    Ext.Msg.alert('Error','Ha ocurrido un error al ejecutar el proceso.');
                }
            });
        }
    });
}

//Editar Numero Sri
function showEditarNumeroSri(idfactura) {
    winEditarNumeroSri = "";
    if (!winEditarNumeroSri) {

        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0',
            },
            url: url_editar_sri,
            items: [
                {
                    xtype: 'combo',
                    name: 'motivos',
                    fieldLabel: 'Motivos',
                    hiddenName: 'motivos',
                    emptyText: 'Seleccione el motivo...',
                    store: store_motivos, // end of Ext.data.SimpleStore
                    displayField: 'descripcion',
                    valueField: 'id',
                    selectOnFocus: true,
                    mode: 'local',
                    typeAhead: true,
                    editable: false,
                    triggerAction: 'all',
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Numero SRI',
                    name: 'txt_sri'
                }, {
                    xtype: 'hiddenfield',
                    name: 'idfactura',
                    value: idfactura
                }],
            buttons: [{
                    text: 'Cancel',
                    handler: function () {
                        this.up('form').getForm().reset();
                        this.up('window').hide();
                    }
                }, {
                    text: 'Grabar',
                    handler: function () {
                        var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
                            form1.submit({
                                waitMsg: "Procesando",
                                success: function (form1, action) {
                                    Ext.Msg.alert('Success', 'Se realizo el cambio, favor verificar');
                                    form1.reset();
                                    if (store) {
                                        store.load();
                                    }
                                },
                                failure: function (form1, action) {
                                    Ext.Msg.alert('Failed', 'Error al ingresar los datos, por favor comunicarse con el departamento de Sistemas');
                                }
                            });
                            this.up('window').hide();
                        }
                    }
                }]
        });

        winEditarNumeroSri = Ext.widget('window', {
            title: 'Editar Numero Sri',
            closeAction: 'hide',
            width: 350,
            height: 300,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });


        winEditarNumeroSri.show();
    }

}

////////////////
FechaEmision = new Ext.form.DateField({
    id: 'fechaEmision',
    fieldLabel: 'Fecha Emision',
    labelAlign: 'left',
    xtype: 'datefield',
    name: 'fechaEmision',
    format: 'Y-m-d',
    width: 325
        //anchor : '65%',
        //layout: 'anchor'
});

//Editar Fecha Emision
function showEditarFeEmision(idfactura) {
    winEditarFeEmision = "";


    if (!winEditarFeEmision) {

        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0',
            },
            url: url_editar_feEmision,
            items: [
                {
                    /*xtype: 'combo',
                     name:'motivosF',
                     id:  'motivosF',
                     fieldLabel: 'Motivos',
                     hiddenName: 'motivosF',
                     emptyText: 'Seleccione el motivo...',
                     store: store_motivos, // end of Ext.data.SimpleStore
                     displayField: 'descripcion',
                     valueField: 'idmotivo',
                     selectOnFocus: true,
                     mode: 'local',
                     typeAhead: true,
                     editable: false,
                     triggerAction: 'all',*/

                    xtype: 'combo',
                    name: 'motivos',
                    fieldLabel: 'Motivos',
                    hiddenName: 'motivos',
                    emptyText: 'Seleccione el motivo...',
                    store: store_motivos, // end of Ext.data.SimpleStore
                    displayField: 'descripcion',
                    valueField: 'id',
                    selectOnFocus: true,
                    mode: 'local',
                    typeAhead: true,
                    editable: false,
                    triggerAction: 'all',
                },
                FechaEmision

                    , {
                        xtype: 'hiddenfield',
                        name: 'idfactura',
                        value: idfactura
                    }],
            buttons: [{
                    text: 'Cancel',
                    handler: function () {
                        this.up('form').getForm().reset();
                        this.up('window').hide();
                    }
                }, {
                    text: 'Grabar',
                    handler: function () {
                        var form1 = this.up('form').getForm();
                        //console.log(FechaEmision);
                        //alert(Ext.getCmp('fechaEmision').getValue());
                        if (form1.isValid() && ((Ext.getCmp('fechaEmision').getValue()) !== "")) {
                            form1.submit({
                                waitMsg: "Procesando",
                                success: function (form1, action) {
                                    Ext.Msg.alert('Success', 'Se realizo el cambio, favor verificar');
                                    form1.reset();
                                    if (store) {
                                        store.load();
                                    }
                                },
                                failure: function (form1, action) {
                                    var resp = Ext.JSON.decode(action.response.responseText);
                                    var mensaje = resp.mensaje;
                                    Ext.Msg.alert('Failed', 'Error al ingresar los datos, asegurese de ingresar la fecha de Emision y un motivo ');

                                }
                            });
                            this.up('window').hide();
                        }
                    }
                }]
        });

        winEditarFeEmision = Ext.widget('window', {
            title: 'Editar Fecha Emision',
            closeAction: 'hide',
            width: 350,
            height: 300,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });


        winEditarFeEmision.show();
    }

}

/**
 * El metodo facturacionElectronicaGt envia el id documento
 * para que envia los datos para obtener quetzal
 * @param {int} intIdDocumento
 * @param {str} strTipoDocumento
 * @author Katherine Yager V.<akyager@telconet.ec>
 * @version 1.0 27-02-2019
 */
function getFacturacionElectronicaGt(intIdDocumento) {
    Ext.MessageBox.wait('Procesando');
    
    Ext.Ajax.request({
        timeout: 400000,
        url: url_facturaElectronicaGt,
        method: 'post',
        params: {intIdDocumento: intIdDocumento,strTipoDocumento: 'FAC'},
        success: function (response) {
            var text = Ext.decode(response.responseText);
            if (text.boolCodError)
            {
                Ext.Msg.alert('Alert', text.strMensaje);
            }
            else 
            {
                Ext.Msg.alert('Success', text.strMensaje);
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
    store.load();
}


