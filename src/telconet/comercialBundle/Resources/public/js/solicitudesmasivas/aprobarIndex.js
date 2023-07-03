/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * Index Solicitudes Masivas
 */

// Clase que tiene las funciones y objetos Generales
var entidadSolicitudMasiva = new SolicitudMasiva();

Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

Ext.onReady(function() {

    //Crea Campos de fechas para el panel de busqueda objFilterPanel
    DTFechaDesdePlanif = new Ext.form.DateField({
        id: 'fechaDesdePlanif',
        name: 'fechaDesdePlanif',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 260,
        editable: false
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'fechaHastaPlanif',
        name: 'fechaHastaPlanif',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 260,
        editable: false
    });	
    DTFechaDesdeIngOrd = new Ext.form.DateField({
        id: 'fechaDesdeIngOrd',
        name: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 260,
        editable: false
    });
    DTFechaHastaIngOrd = new Ext.form.DateField({
        id: 'fechaHastaIngOrd',
        name: 'fechaHastaIngOrd',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 260,
        editable: false
    });
    
   //Crea un campo para el panel de busqueda objFilterPanel
    txtLogin = Ext.create('Ext.form.Text',
        {
            id: 'txtLogin',
            name: 'txtLogin',
            fieldLabel: 'Login',
            labelAlign: 'left',
            allowBlank: true,
            width: 250,
            regex: entidadSolicitudMasiva.strCadenaRegex
        });
    
    //Creamos Store para Tipo de Solicitudes
    Ext.define('modelTipoSolicitud', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdTipoSolicitud', type: 'int'},
            {name: 'strNombreTipoSolicitud', type: 'string'}
        ]
    });
        
     storeTipoSolicitud = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelTipoSolicitud",
        proxy: {
            type: 'ajax',
            url: urlGetTipoSolicitud,
            reader: {
                type: 'json',
                root: 'jsonTipoSolicitud'
            }
        }
    });
    cboTipoSolicitud = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeTipoSolicitud,
        labelAlign: 'left',
        id: 'cboTipoSolicitud',
        name: 'cboTipoSolicitud',
        valueField: 'intIdTipoSolicitud',
        displayField: 'strNombreTipoSolicitud',
        fieldLabel: 'Tipo Solicitud',
        width: 250
    });
    
    //Creamos Store para Estados de Solicitudes Masivas
    Ext.define('modelEstados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strIdEstado', type: 'string'},
            {name: 'strNombreEstado', type: 'string'}
        ]
    });
        
     storeEstados = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelEstados",        
        proxy: {
            type: 'ajax',
            url: urlGetEstados,
            reader: {
                type: 'json',
                root: 'jsonEstados'
            }
        }
    });
    cboEstados = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeEstados,
        labelAlign: 'left',
        id: 'cboEstados',
        name: 'cboEstados',
        valueField: 'strIdEstado',
        displayField: 'strNombreEstado',
        fieldLabel: 'Estado',
        value: "Pendiente",
        width: 250
    });
    
    //Creamos Store para Estados de Solicitudes Masivas
    Ext.define('modelPlanes', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdPlan', type: 'int'},
            {name: 'strNombrePlan', type: 'string'}
        ]
    });
        
     storePlanes = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelPlanes",
        proxy: {
            type: 'ajax',
            url: urlGetPlanes,
            reader: {
                type: 'json',
                root: 'jsonPlanes'
            }
        }
    });
    cboPlanes = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storePlanes,
        labelAlign: 'left',
        id: 'cboPlanes',
        name: 'cboPlanes',
        valueField: 'intIdPlan',
        displayField: 'strNombrePlan',
        fieldLabel: 'Planes',
        width: 250
    });
    
    //Creamos Store para Estados de Solicitudes Masivas
    Ext.define('modelProductos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strIdProducto', type: 'string'},
            {name: 'strNombreProducto', type: 'string'}
        ]
    });
        
     storeProductos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelProductos",
        proxy: {
            type: 'ajax',
            url: urlGetProductos,
            reader: {
                type: 'json',
                root: 'jsonProductos'
            }
        },
         baseParams: {
            strIdCompuesto: 'N'
        }
    });
    cboProductos = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeProductos,
        labelAlign: 'left',
        id: 'cboProductos',
        name: 'cboProductos',
        valueField: 'intIdProducto',
        displayField: 'strNombreProducto',
        fieldLabel: 'Producto',
        width: 250
    });
    
    //Crea un campo para el panel de busqueda objFilterPanel
    txtDescripcionPunto = Ext.create('Ext.form.Text',
        {
            id: 'txtDescripcionPunto',
            name: 'txtDescripcionPunto',
            fieldLabel: 'Descripción Punto',
            labelAlign: 'left',
            allowBlank: true,
            width: 220,
            regex: entidadSolicitudMasiva.strCadenaRegex
        });
        
    //Crea un campo para el panel de busqueda objFilterPanel
    txtVendedor = Ext.create('Ext.form.Text',
        {
            id: 'txtVendedor',
            name: 'txtVendedor',
            fieldLabel: 'Vendedor',
            labelAlign: 'left',
            allowBlank: true,
            width: 220,
            regex: entidadSolicitudMasiva.strCadenaRegex
        });
        
    //Crea un campo para el panel de busqueda objFilterPanel
    txtCiudad = Ext.create('Ext.form.Text',
        {
            id: 'txtCiudad',
            name: 'txtCiudad',
            fieldLabel: 'Ciudad',
            labelAlign: 'left',
            allowBlank: true,
            width: 220,
            regex: entidadSolicitudMasiva.strCadenaRegex,
            maskRe: entidadSolicitudMasiva.strCadenaMask
        }); 
        
    //Crea un campo para el panel de busqueda objFilterPanel
    txtCodigo = Ext.create('Ext.form.Text',
        {
            id: 'txtCodigo',
            name: 'txtCodigo',
            fieldLabel: '<b>Código</b>',
            labelAlign: 'left',
            allowBlank: true,
            width: 220,
            regex: entidadSolicitudMasiva.strIntegerRegex,
            maskRe: entidadSolicitudMasiva.strIntegerMask
        });
    
    fechaCreacionFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Fecha Creación Solicitud:</b>',
        flex: 1,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items: [
            DTFechaDesdeIngOrd,
            DTFechaHastaIngOrd
        ]
    });        
    
    fechaPlanificacionFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Fecha Planificación Solicitud:</b>',
        flex: 1,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items: [
            DTFechaDesdePlanif,
            DTFechaHastaPlanif
        ]
    });
    
    txtClienteIdentificacion = Ext.create('Ext.form.Text',
    {
        id: 'txtClienteIdentificacion',
        name: 'txtClienteIdentificacion',
        fieldLabel: 'Cliente / Identificación',
        labelAlign: 'left',
        allowBlank: true,
        width: 220
    });
    
    //Creamos Store para Oficinas de Solicitudes Masivas
    Ext.define('modelOficinas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_jurisdiccion', type: 'int'},
            {name: 'nombre_jurisdiccion', type: 'string'}
        ]
    });
        
     storeOficinas = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelOficinas",
        proxy: {
            type: 'ajax',
            url: urlGetOficinas,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }, 
        listeners: {
            load: {
                fn: function(){
                    storeOficinas.add({id_jurisdiccion:'', nombre_jurisdiccion: 'Todos'});
                    storeOficinas.sort('nombre_jurisdiccion', 'ASC');
                }
            }
        }
    });
    
    cboOficinas = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeOficinas,
        labelAlign: 'left',
        id: 'cboOficinas',
        name: 'cboOficinas',
        valueField: 'id_jurisdiccion',
        displayField: 'nombre_jurisdiccion',
        fieldLabel: 'Oficina',
        width: 250
    });

    var strRolVerIsp = $("#ROLE_443-8637");
    var boolVerIsp   = (typeof strRolVerIsp === 'undefined') ? true : (strRolVerIsp.val() == 1 ? false : true);

    storeIsp = Ext.create('Ext.data.Store', {
        fields : ['strIdIsp', 'strIsp'],
        data   : [
            {strIdIsp : 'No', strIsp: 'No'},
            {strIdIsp : 'Si',  strIsp: 'Si'}
        ]
    });

    cboIsp = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeIsp,
        labelAlign: 'left',
        id: 'cboIsp',
        name: 'cboIsp',
        valueField: 'strIdIsp',
        displayField: 'strIsp',
        fieldLabel: 'Es Isp',
        value: "No",
        width: 250,
        hidden:boolVerIsp
    });

    filtrosFieldSet = Ext.create('Ext.form.FieldSet', {
        colspan: 2,
        width: 1270,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 5,
            align: 'left'
        },
        items: [
            txtCodigo,
            cboTipoSolicitud,
            cboEstados,
            cboProductos,        
            txtVendedor,
            txtClienteIdentificacion,
            txtLogin,
            cboOficinas,
            cboIsp
        ]
    });
        
    //Panel con los campos que se usan como filtros para buscar en el grid: gridListaParametros del store: storeParametrosCab
    objFilterPanel = Ext.create('Ext.panel.Panel', {
        border: false,
        buttonAlign: 'center',
        layout: {
            tdAttrs: {style: 'padding: 1px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        collapsible: true,
        collapsed: false,
        width: 1280,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    //Realiza la petición con los campos seteados en el panel de busqueda
                    storeSolicitudesMasivas.removeAll();
                    storeSolicitudesMasivas.currentPage = 1;
                    storeSolicitudesMasivas.load({
                        params:
                            {
                                fechaDesdePlanif: DTFechaDesdePlanif.getValue(),
                                fechaHastaPlanif: DTFechaHastaPlanif.getValue(),        
                                fechaDesdeIngOrd: DTFechaDesdeIngOrd.getValue(),
                                fechaHastaIngOrd: DTFechaHastaIngOrd.getValue(),              
                                txtLogin: txtLogin.getValue(),
                                txtCodigo: txtCodigo.getValue(), 
                                txtDescripcionPunto: txtDescripcionPunto.getValue(),
                                txtClienteIdentificacion:  txtClienteIdentificacion.getValue(),
                                txtVendedor: txtVendedor.getValue(),
                                txtCiudad: txtCiudad.getValue(),              
                                cboTipoSolicitud: cboTipoSolicitud.getValue(),
                                txtTipoSolicitud: cboTipoSolicitud.getRawValue(),
                                cboEstados: cboEstados.getValue(),
                                cboProductos:cboProductos.getValue(),
                                cboOficinas:cboOficinas.getValue(),
                                cboIsp:cboIsp.getValue(),
                                boolMasivas: true,                                
                                boolReqArchivo: true,
                                boolReqAprobPrecio: true,
                                boolReqAprobRadio: false,
                                boolReqAprobIpccl2: false,
                                strEstadoDetalles: 'Pendiente'
                            }
                    });
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {                    
                    DTFechaDesdePlanif.setValue(null);
                    DTFechaHastaPlanif.setValue(null);
                    DTFechaDesdeIngOrd.setValue(null);
                    DTFechaHastaIngOrd.setValue(null);                    
                    txtLogin.setValue('');
                    txtClienteIdentificacion.setValue('');
                    txtDescripcionPunto.setValue('');
                    txtVendedor.setValue('');
                    txtCodigo.setValue(''); 
                    txtCiudad.setValue('');                    
                    cboTipoSolicitud.value = null;
                    cboTipoSolicitud.setRawValue(null);
                    cboEstados.value = null;
                    cboEstados.setRawValue(null);
                    cboProductos.value = null;
                    cboProductos.setRawValue(null);
                    cboOficinas.value = null;
                    cboOficinas.setRawValue(null);
                }
            }

        ],
        items: [            
            fechaCreacionFieldSet,
            fechaPlanificacionFieldSet,
            filtrosFieldSet
        ],
        renderTo: 'filtro'
    });
    
    //Define un modelo para el store storeParametrosCab
    Ext.define('modelListaSolicitudesMasivas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdSolicitud', type: 'int'},
            {name: 'intIdServicio', type: 'int'},
            {name: 'intIdPunto', type: 'int'},
            {name: 'strCliente', type: 'string'},
            {name: 'strVendedor', type: 'string'},
            {name: 'strLogin', type: 'string'},
            {name: 'strProducto', type: 'string'},
            {name: 'strTipoOrden', type: 'string'},
            {name: 'strCiudad', type: 'string'},
            {name: 'strTipoSolicitud', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'strFePlanificacion', type: 'string'},
            {name: 'strFeEjecucion', type: 'string'},
            {name: 'strFeRechazo', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strArchivo', type: 'string'},
            {name: 'floatPorcentajeDescuento', type: 'string'},
            {name: 'strCargoAsignado', type: 'string'}
        ]
    });

     //Store que realiza la petición ajax para el grid: gridListaParametros
    storeSolicitudesMasivas = Ext.create('Ext.data.Store', {
        pageSize: entidadSolicitudMasiva.intPageSize,
        model: 'modelListaSolicitudesMasivas',
        autoLoad: false,
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url: urlGetSolicitudesMasivas,
            timeout: entidadSolicitudMasiva.timeout,
            reader: {
                type: 'json',
                root: 'jsonSolicitudesMasivas',
                totalProperty: 'total'
            },
            extraParams:{
                fechaDesdePlanif: DTFechaDesdePlanif.getValue(),
                fechaHastaPlanif: DTFechaHastaPlanif.getValue(),        
                fechaDesdeIngOrd: DTFechaDesdeIngOrd.getValue(),
                fechaHastaIngOrd: DTFechaHastaIngOrd.getValue(),              
                txtLogin: txtLogin.getValue(),
                txtCodigo: txtCodigo.getValue(), 
                txtDescripcionPunto: txtDescripcionPunto.getValue(),
                txtVendedor: txtVendedor.getValue(),
                txtCiudad: txtCiudad.getValue(),              
                cboTipoSolicitud: cboTipoSolicitud.getValue(),
                txtTipoSolicitud: cboTipoSolicitud.getRawValue(),
                txtClienteIdentificacion:  txtClienteIdentificacion.getValue(),
                cboEstados: cboEstados.getValue(),
                cboProductos:cboProductos.getValue(),
                cboOficinas:cboOficinas.getValue(),
                boolMasivas: true,
                boolReqArchivo: true,
                boolReqAprobPrecio: true,
                boolReqAprobRadio: false,
                boolReqAprobIpccl2: false,
                strEstadoDetalles: 'Pendiente'
            },
            simpleSortMode: true
        }
    });
    
     //Crea el grid que muestra la información obtenida desde el controlador  de la cabera de parámetros.
    gridListaSolicitudesMasivas = Ext.create('Ext.grid.Panel', {
        title: 'Listado de Solicitudes Masivas',
        store: storeSolicitudesMasivas,
        id: 'gridListaSolicitudesMasivas',
        viewConfig: { enableTextSelection: true },
        columns: [
             {
                id: 'intIdSolicitud',
                header: 'IdSolicitud',
                dataIndex: 'intIdSolicitud',
                hidden: true,
                hideable: false
              },
              {
                id: 'intIdServicio',
                header: 'IdServicio',
                dataIndex: 'intIdServicio',
                hidden: true,
                hideable: false
              },
              {
                id: 'strCodigo',
                header: 'Código',
                dataIndex: 'intIdSolicitud',
                width: 70,
                sortable: true,
                align: 'center',
                renderer :entidadSolicitudMasiva.styleBold
              },
              {
                id: 'strCliente',
                header: 'Cliente',
                dataIndex: 'strCliente',
                width: 190,
                sortable: true
              },
              {
                id: 'strProducto',
                header: 'Producto',
                dataIndex: 'strProducto',
                width: 150,
                sortable: true
              },
              {
                id: 'strTipoSolicitud',
                header: 'Tipo Solicitud',
                dataIndex: 'strTipoSolicitud',
                width: 100,
                sortable: true
              },
              {
                id: 'strFeCreacion',
                header: 'F. Creación',
                dataIndex: 'strFeCreacion',
                width: 100,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strFePlanificacion',
                header: 'F. Planificación',
                dataIndex: 'strFePlanificacion',
                width: 100,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strFeEjecucion',
                header: 'F. Ejecucion',
                dataIndex: 'strFeEjecucion',
                width: 100,
                sortable: true,
                align: 'center'
              },   
              {
                id: 'strFeRechazo',
                header: 'F. Rechazo',
                dataIndex: 'strFeRechazo',
                width: 100,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strUsrCreacion',
                header: 'Creado por',
                dataIndex: 'strUsrCreacion',
                width: 85,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strEstado',
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 80,
                sortable: true,
                align: 'center',
                renderer : entidadSolicitudMasiva.estadoChange
              },
              {
                id: 'floatPorcentajeDescuento',
                header: '%',
                dataIndex: 'floatPorcentajeDescuento',
                width: 55,
                sortable: true,
                align: 'center',
                renderer : entidadSolicitudMasiva.floatPorcentajeDescuento
              },
              {
                id: 'strCargoAsignado',
                header: 'Cargo asignado',
                dataIndex: 'strCargoAsignado',
                width: 95,
                sortable: true,
                align: 'center',
                renderer : entidadSolicitudMasiva.strCargoAsignado
              },
              {
                  xtype: 'actioncolumn',
                  header: 'Archivo',
                  align: 'center',
                  width: 60,
                  items: [
                    {
                        getClass: function(v, meta, rec) {
                                if (rec.get('strArchivo')!=='') {
                                    tooltip = 'Descargar Archivo';
                                    return 'button-grid icon_bajar';
                                 }if (rec.get('strArchivo')==='' && rec.get('strEstado')!=='Eliminada' && rec.get('strEstado')!=='Rechazada') {
                                    tooltip = 'Falta el Archivo';
                                    return "button-grid icon_alert";
                                } else {
                                    return "icon-invisible";
                                }  
                        },
                        tooltip: 'Descargar Archivo',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            var strUrl = rec.get('strArchivo');
                            if(strUrl!==''){
                                entidadSolicitudMasiva.downloadArchivo(strUrl);
                            }else{
                                Ext.Msg.alert('Alerta', 'El Documento aun no ha sido adjuntado, comuniquese con el responsable!');
                            }
                        }
                    }
                  ]
              },
              {
                  xtype: 'actioncolumn',
                  header: 'Acciones',
                  align: 'center',
                  width: 125,
                  items: [
                    {
                        getClass: function(v, meta, rec) {
                                tooltip = 'Ver Historial';
                                return 'button-grid-logs';
                        },
                        tooltip: 'Ver Historial',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            var intIdSolicitud = rec.get('intIdSolicitud');
                            entidadSolicitudMasiva.showHistorialSolicitudesMasivas(intIdSolicitud);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_347-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                   tooltip = 'Ver Solicitud';
                                   return 'button-grid-show';
                            }
                        },
                        tooltip: 'Ver Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            window.location = "/comercial/solicitud/solicitudesmasivas/"+rec.get('intIdSolicitud')+"/show";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso_aprobar = $("#ROLE_347-163");
                            var permiso_rechazar = $("#ROLE_347-94");
                            var boolPermiso = (typeof permiso_aprobar === 'undefined' && typeof permiso_rechazar === 'undefined') ? false : (permiso_aprobar.val() == 1 || permiso_rechazar.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('strEstado')=='Pendiente' && rec.get('strArchivo')!=='' ) {
                                   tooltip = 'Aprobar/Rechazar Solicitud Solicitud';
                                   return 'button-grid-Tuerca';
                                } else {
                                    return "icon-invisible";
                                }          
                            }
                        },
                        tooltip: 'Aprobar/Rechazar Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            window.location = ""+rec.get('intIdSolicitud')+"/aprobarRechazar";
                        }
                    }
                  ]
              }        
        ],
        height: 500,
        width: 1280,
        renderTo: 'listadoSolicitudesMasivas',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeSolicitudesMasivas,
            displayInfo: true,
            displayMsg: entidadSolicitudMasiva.displayMsg,
            emptyMsg: entidadSolicitudMasiva.emptyMsg
        })
    });   
});