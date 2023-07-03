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
var itemsPerPage = 10;
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
        width: 235,
        editable: false
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'fechaHastaPlanif',
        name: 'fechaHastaPlanif',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 235,
        editable: false
    });	
    DTFechaDesdeIngOrd = new Ext.form.DateField({
        id: 'fechaDesdeIngOrd',
        name: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 235,
        editable: false
    });
    DTFechaHastaIngOrd = new Ext.form.DateField({
        id: 'fechaHastaIngOrd',
        name: 'fechaHastaIngOrd',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 235,
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
            width: 235
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
        width: 235
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
        value:'Pendiente',
        width: 230
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
        width: 230
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
            },
            extraParams: {               
                strIdCompuesto: 'N'
            }
        },
        listeners: {
            load: entidadSolicitudMasiva.storeLoad
        }
    });
    cboProductos = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeProductos,
        labelAlign: 'left',
        id: 'cboProductos',
        name: 'cboProductos',
        valueField: 'strIdProducto',
        displayField: 'strNombreProducto',
        fieldLabel: 'Producto',
        width: 235
    });
    
    //Crea un campo para el panel de busqueda objFilterPanel
    txtDescripcionPunto = Ext.create('Ext.form.Text',
        {
            id: 'txtDescripcionPunto',
            name: 'txtDescripcionPunto',
            fieldLabel: 'Descripción Punto',
            labelAlign: 'left',
            allowBlank: true,
            width: 250,
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
            width: 235,
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
            width: 235,
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
            width: 235,
            regex: entidadSolicitudMasiva.strIntegerRegex,
            maskRe: entidadSolicitudMasiva.strIntegerMask
        });
        
        
    fechaCreacionFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Fecha Creación Solicitud:</b>',
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
        width: 235
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
        width: 235
    });
    
    filtrosFieldSet = Ext.create('Ext.form.FieldSet', {
        colspan: 2,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 4,
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
            cboOficinas
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
        width: 1040,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    //Realiza la petición con los campos seteados en el panel de busqueda
                    storeSolicitudesMasivas.load({
                        params:
                            {
                                start: 0, limit: 10
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
            {name: 'intTotalDetallesAprobados', type: 'string'},
            {name: 'intTotalDetallesRechazadas', type: 'string'},
            {name: 'intTotalDetallesEnProceso', type: 'string'},
            {name: 'intTotalDetallesFinalizada', type: 'string'},
            {name: 'floatPorcentajeDescuento', type: 'string'},
            {name: 'strCargoAsignado', type: 'string'}
        ]
    });

     //Store que realiza la petición ajax para el grid: gridListaParametros
    storeSolicitudesMasivas = Ext.create('Ext.data.Store', {
        pageSize: itemsPerPage,
        model: 'modelListaSolicitudesMasivas',
        autoLoad: false,
        proxy: {
            timeout: 9000000,
            type: 'ajax',
            url: urlGetSolicitudesMasivas,
            timeout: entidadSolicitudMasiva.timeout,
            reader: {
                type: 'json',
                root: 'jsonSolicitudesMasivas',
                totalProperty: 'total'
            },
            extraParams: {               
                fechaDesdePlanif: "",
                fechaHastaPlanif: "",
                fechaDesdeIngOrd: "",
                fechaHastaIngOrd: "",
                txtLogin: "",
                txtCodigo: "",
                txtDescripcionPunto: "",
                txtVendedor: "",
                txtClienteIdentificacion: "",
                cboTipoSolicitud:"",
                cboEstados: "",
                cboProductos:"",
                cboOficinas: "",
                boolMasivas: "",
                boolVisualizar: "",
            },
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(storeSolicitudesMasivas)
            {
                storeSolicitudesMasivas.getProxy().extraParams.fechaDesdePlanif= DTFechaDesdePlanif.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.fechaHastaPlanif= DTFechaHastaPlanif.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.fechaDesdeIngOrd= DTFechaDesdeIngOrd.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.fechaHastaIngOrd= DTFechaHastaIngOrd.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtLogin= txtLogin.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtCodigo= txtCodigo.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtDescripcionPunto= txtDescripcionPunto.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtVendedor= txtVendedor.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtClienteIdentificacion=  txtClienteIdentificacion.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboTipoSolicitud= cboTipoSolicitud.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboEstados= cboEstados.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboProductos=cboProductos.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboOficinas= cboOficinas.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.boolMasivas= true;
                storeSolicitudesMasivas.getProxy().extraParams.boolVisualizar= "Permitido";
            },
            load: entidadSolicitudMasiva.storeLoad
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
                id: 'strArchivo',
                header: 'archivo',
                dataIndex: 'strArchivo',
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
                tdCls: 'fontSize10',
                width: 100,
                sortable: true
              },
              {
                id: 'strProducto',
                header: 'Producto',
                dataIndex: 'strProducto',
                tdCls: 'fontSize10',
                width: 75,
                sortable: true
              },
              {
                id: 'strTipoSolicitud',
                header: 'Tipo Solicitud',
                dataIndex: 'strTipoSolicitud',
                tdCls: 'fontSize10',
                width: 80,
                sortable: true
              },
              {
                id: 'strFeCreacion',
                header: 'F. Creación',
                dataIndex: 'strFeCreacion',
                tdCls: 'fontSize10',
                width: 85,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strFePlanificacion',
                header: 'F. Planificación',
                dataIndex: 'strFePlanificacion',
                tdCls: 'fontSize10',
                width: 85,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strFeEjecucion',
                header: 'F. Ejecucion',
                dataIndex: 'strFeEjecucion',
                tdCls: 'fontSize10',
                width: 85,
                sortable: true,
                align: 'center'
              },   
              {
                id: 'strFeRechazo',
                header: 'F. Rechazo',
                dataIndex: 'strFeRechazo',
                tdCls: 'fontSize10',
                width: 85,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strUsrCreacion',
                header: 'Creado por',
                dataIndex: 'strUsrCreacion',
                width: 65,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strEstado',
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 75,
                sortable: true,
                align: 'center',
                renderer : entidadSolicitudMasiva.estadoChange
              },
              {
                id: 'floatPorcentajeDescuento',
                header: '%',
                dataIndex: 'floatPorcentajeDescuento',
                width: 50,
                sortable: true,
                align: 'center'
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
                  width: 80,
                  items: [
                    {
                        getClass: function(v, meta, rec) {                            
                            if (rec.get('strArchivo')==='' && rec.get('strEstado')=='Pendiente' && rec.get('strUsrCreacion') == usrLoginActivo) {
                               return 'button-grid icon_subir';
                            } else {
                                return "icon-invisible";
                            }
                        },
                        tooltip: 'Subir Archivo',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            var intIdSolicitud = rec.get('intIdSolicitud');
                            
                            var storeSolicitudesMasivasParams = {
                                params:
                                    {
                                        fechaDesdePlanif: DTFechaDesdePlanif.getValue(),
                                        fechaHastaPlanif: DTFechaHastaPlanif.getValue(),        
                                        fechaDesdeIngOrd: DTFechaDesdeIngOrd.getValue(),
                                        fechaHastaIngOrd: DTFechaHastaIngOrd.getValue(),              
                                        txtLogin: txtLogin.getValue(),
                                        txtCodigo: txtCodigo.getValue(), 
                                        txtDescripcionPunto: txtDescripcionPunto.getValue(),
                                        txtVendedor: txtVendedor.getValue(),
                                        txtClienteIdentificacion:  txtClienteIdentificacion.getValue(),
                                        cboTipoSolicitud: cboTipoSolicitud.getValue(),
                                        cboEstados: cboEstados.getValue(),
                                        cboProductos:cboProductos.getValue(),
                                        cboOficinas: cboOficinas.getValue(),
                                        boolMasivas: true
                                    }
                            };                            
                            entidadSolicitudMasiva.subirArchivo(intIdSolicitud, storeSolicitudesMasivas, storeSolicitudesMasivasParams);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            if (rec.get('strArchivo')!=='') {
                                return 'button-grid icon_bajar';
                             }if (rec.get('strArchivo')==='' && rec.get('strEstado')!=='Eliminada' 
                                 && rec.get('strEstado')!=='Rechazada' && rec.get('strUsrCreacion') !== usrLoginActivo) {
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
                    },
                    {
                        getClass: function(v, meta, rec) {                            
                            if (rec.get('strArchivo')!=='' && rec.get('strEstado')!=='Finalizada' && rec.get('strUsrCreacion') === usrLoginActivo) {
                               return 'button-grid icon_subir_edit';
                            } else {
                                return "icon-invisible";
                            }
                        },
                        tooltip: 'Reemplazar Archivo',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            var intIdSolicitud = rec.get('intIdSolicitud');
                            var storeSolicitudesMasivasParams = {
                                params:
                                    {
                                        fechaDesdePlanif: DTFechaDesdePlanif.getValue(),
                                        fechaHastaPlanif: DTFechaHastaPlanif.getValue(),        
                                        fechaDesdeIngOrd: DTFechaDesdeIngOrd.getValue(),
                                        fechaHastaIngOrd: DTFechaHastaIngOrd.getValue(),              
                                        txtLogin: txtLogin.getValue(),
                                        txtCodigo: txtCodigo.getValue(), 
                                        txtDescripcionPunto: txtDescripcionPunto.getValue(),
                                        txtVendedor: txtVendedor.getValue(),
                                        txtClienteIdentificacion:  txtClienteIdentificacion.getValue(),
                                        cboTipoSolicitud: cboTipoSolicitud.getValue(),
                                        cboEstados: cboEstados.getValue(),
                                        cboProductos:cboProductos.getValue(),
                                        cboOficinas: cboOficinas.getValue(),
                                        boolMasivas: true
                                    }
                            };                            
                            entidadSolicitudMasiva.subirArchivo(intIdSolicitud, storeSolicitudesMasivas, storeSolicitudesMasivasParams);
                        }
                    }
                  ]
              },
              {
                  xtype: 'actioncolumn',
                  header: 'Acciones',
                  width: 115,
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
                            var permiso = $("#ROLE_346-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                   tooltip = 'Ver Solicitud';
                                   return 'button-grid-show';
                            }
                        },
                        tooltip: 'Ver Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            window.location = ""+rec.get('intIdSolicitud')+"/show";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_346-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {                         
                                if (rec.get('strEstado')=='Pendiente' && rec.get('intTotalDetallesAprobados') === '0' 
                                    && rec.get('intTotalDetallesRechazadas') === '0'
                                    && rec.get('intTotalDetallesEnProceso') === '0'
                                    && rec.get('intTotalDetallesFinalizada') === '0'
                                    && rec.get('strUsrCreacion') == usrLoginActivo) {
                                   tooltip = 'Eliminar Solicitud';
                                   return 'button-grid-delete';
                                } else{
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Eliminar Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            entidadSolicitudMasiva.showDeleteSolicitudesMasivas(rec);
                        }
                    }
                  ]
              }        
        ],
        height: 500,
        width: 1040,
        renderTo: 'listadoSolicitudesMasivas',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeSolicitudesMasivas,
            displayInfo: true,
            displayMsg: entidadSolicitudMasiva.displayMsg,
            emptyMsg: entidadSolicitudMasiva.emptyMsg
        })
    });

});