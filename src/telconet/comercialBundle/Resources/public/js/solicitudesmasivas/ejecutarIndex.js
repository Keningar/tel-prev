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
        valueField: 'strIdProducto',
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
        width: 1280,
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
            {name: 'intTotalDetalles', type: 'int'},
            {name: 'intTotalDetallesAprobados', type: 'int'},
            {name: 'intTotalDetallesCPAprobados', type: 'int'},
            {name: 'intTotalDetallesRadioAprobados', type: 'int'},
            {name: 'intTotalDetallesIpccl2Aprobados', type: 'int'},
            {name: 'intTotalDetallesEnProceso', type: 'int'},
            {name: 'intTotalDetallesFinalizado', type: 'int'},
            {name: 'intTotalDetallesCPNA', type: 'int'},
            {name: 'intTotalDetallesRadioNA', type: 'int'},
            {name: 'intTotalDetallesIpccl2NA', type: 'int'},
            {name: 'intTotalDetallesRechazadas', type: 'int'},
            {name: 'intTotalDetallesFallos', type: 'int'}
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
                fechaDesdePlanif: "",
                fechaHastaPlanif: "",
                fechaDesdeIngOrd: "",
                fechaHastaIngOrd: "",
                txtLogin: "",
                txtCodigo: "",
                txtDescripcionPunto: "",
                txtVendedor: "",
                txtCiudad: "",
                cboTipoSolicitud: "",
                txtTipoSolicitud: "",
                cboEstados: "",
                cboProductos:"",
                cboOficinas: "",
                txtClienteIdentificacion:"",
                boolMasivas: "",
                boolReqArchivo: "",
                boolReqAprobPrecio: "",
                boolReqAprobRadio: "",
                boolReqAprobIpccl2: "",
                strEstadoDetalles: "",
                strEstadoAprobPrecio: "",
                strEstadoAprobRadio: "",
                strEstadoAprobIpccl2: "",
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
                storeSolicitudesMasivas.getProxy().extraParams.txtCiudad= txtCiudad.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboTipoSolicitud= cboTipoSolicitud.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtTipoSolicitud= cboTipoSolicitud.getRawValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboEstados= cboEstados.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboProductos=cboProductos.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.cboOficinas= cboOficinas.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.txtClienteIdentificacion=  txtClienteIdentificacion.getValue();
                storeSolicitudesMasivas.getProxy().extraParams.boolMasivas= true;
                storeSolicitudesMasivas.getProxy().extraParams.boolReqArchivo= true;
                storeSolicitudesMasivas.getProxy().extraParams.boolReqAprobPrecio= true;
                storeSolicitudesMasivas.getProxy().extraParams.boolReqAprobRadio= true;
                storeSolicitudesMasivas.getProxy().extraParams.boolReqAprobIpccl2= true;
                storeSolicitudesMasivas.getProxy().extraParams.strEstadoDetalles= 'Aprobada,EnProceso,Fallo';
                storeSolicitudesMasivas.getProxy().extraParams.strEstadoAprobPrecio= 'Aprobada,N/A';
                storeSolicitudesMasivas.getProxy().extraParams.strEstadoAprobRadio= 'Aprobada,N/A';
                storeSolicitudesMasivas.getProxy().extraParams.strEstadoAprobIpccl2= 'Aprobada,N/A';
            }
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
                width: 60,
                sortable: true,
                align: 'center',
                renderer :entidadSolicitudMasiva.styleBold
              },
              {
                id: 'strCliente',
                header: 'Cliente',
                dataIndex: 'strCliente',
                width: 140,
                sortable: true
              },
              {
                id: 'strProducto',
                header: 'Producto',
                dataIndex: 'strProducto',
                width: 80,
                sortable: true
              },
              {
                id: 'strTipoSolicitud',
                header: 'Tipo Solicitud',
                dataIndex: 'strTipoSolicitud',
                width: 90,
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
                id: 'intTotalDetalles',
                header: '# Det.',
                dataIndex: 'intTotalDetalles',
                width: 50,
                sortable: true,
                align: 'center'
              },
              {
                id: 'intTotalDetallesAprobados',
                header: 'Det. Aprobado',
                dataIndex: 'intTotalDetallesAprobados',
                width: 80,
                sortable: true,
                align: 'center'
              },   
              {
                id: 'intTotalDetallesCPAprobados',
                header: 'Det. Aprobado<br>Camb. Precio',
                dataIndex: 'intTotalDetallesCPAprobados',
                width: 80,
                sortable: true,
                align: 'center'
              },
              {
                id: 'intTotalDetallesRadioAprobados',
                header: 'Det. Aprobado<br>Radio',
                dataIndex: 'intTotalDetallesRadioAprobados',
                width: 80,
                sortable: true,
                align: 'center'
              },
              {
                id: 'intTotalDetallesIpccl2Aprobados',
                header: 'Det. Aprobado<br>IPCCL2',
                dataIndex: 'intTotalDetallesIpccl2Aprobados',
                width: 80,
                sortable: true,
                align: 'center'
              },
              {
                id: 'intTotalDetallesEnProceso',
                header: 'Det. EnProceso',
                dataIndex: 'intTotalDetallesEnProceso',
                width: 80,
                sortable: true,
                align: 'center'
              },
              {
                id: 'intTotalDetallesFinalizado',
                header: 'Det. Finalizado',
                dataIndex: 'intTotalDetallesFinalizado',
                width: 80,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strUsrCreacion',
                header: 'Creado por',
                dataIndex: 'strUsrCreacion',
                width: 80,
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
                hidden: false,
                renderer : entidadSolicitudMasiva.estadoChange
              },
              {
                  xtype: 'actioncolumn',
                  header: 'Acciones',
                  flex: 1,
                  items: [
                    {
                        getClass: function(v, meta, rec) {
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
                            var permiso = $("#ROLE_349-6");
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
                            var permiso = $("#ROLE_349-4037");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                            
                            if (boolPermiso && rec.get('strEstado') === "Pendiente") {
                                
                                var intTotalDetallesAprobados = Number(rec.get('intTotalDetallesAprobados'));
                                var intTotalDetallesFallos = Number(rec.get('intTotalDetallesFallos'));
                                
                                var intTotalDetallesCPNA = Number(rec.get('intTotalDetallesCPNA'));
                                var intTotalDetallesCPAprobados = Number(rec.get('intTotalDetallesCPAprobados'));
                                
                                var intTotalDetallesRadioNA = Number(rec.get('intTotalDetallesRadioNA'));
                                var intTotalDetallesRadioAprobados = Number(rec.get('intTotalDetallesRadioAprobados'));
                                
                                var intTotalDetallesIpccl2NA = Number(rec.get('intTotalDetallesIpccl2NA'));
                                var intTotalDetallesIpccl2Aprobados = Number(rec.get('intTotalDetallesIpccl2Aprobados'));                                
                                
                                boolPermiso = (intTotalDetallesAprobados === intTotalDetallesCPNA && intTotalDetallesAprobados === intTotalDetallesRadioNA && intTotalDetallesAprobados === intTotalDetallesIpccl2NA);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados === intTotalDetallesCPAprobados && intTotalDetallesAprobados === intTotalDetallesRadioAprobados && intTotalDetallesAprobados === intTotalDetallesIpccl2Aprobados);
                                boolPermiso = boolPermiso || ((intTotalDetallesAprobados === intTotalDetallesCPNA && intTotalDetallesAprobados === intTotalDetallesRadioNA) && intTotalDetallesIpccl2Aprobados > 0);
                                boolPermiso = boolPermiso || ((intTotalDetallesAprobados === intTotalDetallesCPNA && intTotalDetallesAprobados === intTotalDetallesIpccl2NA) && intTotalDetallesRadioAprobados > 0);
                                boolPermiso = boolPermiso || ((intTotalDetallesAprobados === intTotalDetallesIpccl2NA && intTotalDetallesAprobados === intTotalDetallesRadioNA) && intTotalDetallesCPAprobados > 0);
                                boolPermiso = boolPermiso || ((intTotalDetallesAprobados === intTotalDetallesCPAprobados && intTotalDetallesAprobados === intTotalDetallesRadioNA) && intTotalDetallesIpccl2Aprobados > 0);
                                boolPermiso = boolPermiso || ((intTotalDetallesAprobados === intTotalDetallesCPNA && intTotalDetallesAprobados === intTotalDetallesIpccl2Aprobados) && intTotalDetallesRadioAprobados > 0);
                                boolPermiso = boolPermiso || ((intTotalDetallesAprobados === intTotalDetallesIpccl2Aprobados && intTotalDetallesAprobados === intTotalDetallesRadioNA) && intTotalDetallesCPAprobados > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados <= intTotalDetallesIpccl2Aprobados && intTotalDetallesRadioNA >0 && intTotalDetallesCPNA > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados <= intTotalDetallesCPAprobados && intTotalDetallesRadioNA >0 && intTotalDetallesIpccl2NA > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados <= intTotalDetallesRadioAprobados && intTotalDetallesCPNA >0 && intTotalDetallesIpccl2NA > 0);        
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesIpccl2Aprobados && intTotalDetallesRadioNA >0 && intTotalDetallesCPNA > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesCPAprobados && intTotalDetallesRadioNA >0 && intTotalDetallesIpccl2NA > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesRadioAprobados && intTotalDetallesCPNA >0 && intTotalDetallesIpccl2NA > 0);                                
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesIpccl2Aprobados && intTotalDetallesRadioAprobados > 0 && intTotalDetallesCPNA > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesCPAprobados && intTotalDetallesRadioAprobados > 0 && intTotalDetallesIpccl2NA > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesRadioAprobados && intTotalDetallesCPAprobados > 0 && intTotalDetallesIpccl2NA > 0);                                
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesIpccl2Aprobados && intTotalDetallesRadioNA > 0 && intTotalDetallesCPAprobados > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesCPAprobados && intTotalDetallesRadioNA > 0 && intTotalDetallesIpccl2Aprobados > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesRadioAprobados && intTotalDetallesCPNA > 0 && intTotalDetallesIpccl2Aprobados > 0);                                
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesIpccl2Aprobados && intTotalDetallesRadioAprobados > 0 && intTotalDetallesCPAprobados > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesCPAprobados && intTotalDetallesRadioAprobados > 0 && intTotalDetallesIpccl2Aprobados > 0);
                                boolPermiso = boolPermiso || (intTotalDetallesAprobados >= intTotalDetallesRadioAprobados && intTotalDetallesCPAprobados > 0 && intTotalDetallesIpccl2Aprobados > 0);
                                
                                boolPermiso = (boolPermiso && intTotalDetallesAprobados > 0) || intTotalDetallesFallos > 0;

                                if(rec.get('strTipoSolicitud').search("CANCELACION") !== -1  && rec.get('intTotalDetallesAprobados') > 0){
                                    boolPermiso = true;
                                }
                                if (boolPermiso) {
                                   tooltip = 'Ejecutar Solicitud Solicitud';
                                   return 'button-grid-Tuerca';
                                } else {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Ejecutar Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            window.location = ""+rec.get('intIdSolicitud')+"/ejecutar";
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