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

    
    DTFechaDesdeIngOrd = new Ext.form.DateField({
        id: 'fechaDesdeIngOrd',
        name: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 280,
        editable: false
    });
    DTFechaHastaIngOrd = new Ext.form.DateField({
        id: 'fechaHastaIngOrd',
        name: 'fechaHastaIngOrd',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 280,
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
            width: 280,
            regex: entidadSolicitudMasiva.strCadenaRegex
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
        width: 280
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
        width: 280
    });
    
    //Crea un campo para el panel de busqueda objFilterPanel
    txtCiudad = Ext.create('Ext.form.Text',
        {
            id: 'txtCiudad',
            name: 'txtCiudad',
            fieldLabel: 'Ciudad',
            labelAlign: 'left',
            allowBlank: true,
            width: 280,
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
            width: 280,
            regex: entidadSolicitudMasiva.strIntegerRegex,
            maskRe: entidadSolicitudMasiva.strIntegerMask
        }); 
    
    //Crea un campo para el panel de busqueda objFilterPanel
    txtCodigoSolicitudMasiva = Ext.create('Ext.form.Text',
        {
            id: 'txtCodigoSolicitudMasiva',
            name: 'txtCodigoSolicitudMasiva',
            fieldLabel: '<b>Código Sol. Masiva</b>',
            labelAlign: 'left',
            allowBlank: true,
            width: 280,
            regex: entidadSolicitudMasiva.strIntegerRegex,
            maskRe: entidadSolicitudMasiva.strIntegerMask
        }); 
    
    fechaPlanificacionFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Fecha Creación Solicitud:</b>',
        flex: 1,
        width: 635,
        height: 70,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items: [
            DTFechaDesdeIngOrd,
            DTFechaHastaIngOrd,
        ]
    });
    
    txtClienteIdentificacion = Ext.create('Ext.form.Text',
    {
        id: 'txtClienteIdentificacion',
        name: 'txtClienteIdentificacion',
        fieldLabel: 'Cliente / Identificación',
        labelAlign: 'left',
        allowBlank: true,
        width: 280
    });
    
    filtrosSecundariosFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Filtros Secundarios:</b>',
        width: 635,
        height: 70,
        layout: {
            tdAttrs: {style: 'padding: 1px 5px; margin-top:0;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items: [
            txtClienteIdentificacion,
            txtLogin
        ]
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
        width: 280
    });
    
    filtrosFieldSet = Ext.create('Ext.form.FieldSet', {
        title: '<b>Filtros Principales:</b>',
        width: 635,
        height: 160,
        layout: {
            tdAttrs: {style: 'padding: 3px 5px;'},
            type: 'table',
            columns: 2,
            align: 'left'
        },
        items: [
            txtCodigo,
            txtCodigoSolicitudMasiva,
            cboProductos,           
            cboEstados,
            cboOficinas
        ]
    });
    
    filtrosPanel = Ext.create('Ext.panel.Panel', {        
        border: false,
        width: 635,
        height: 150,
        layout: {
            tdAttrs: {style: 'padding: 0px;'},
            type: 'table',
            columns: 1,
            align: 'left'
        },
        items: [            
            fechaPlanificacionFieldSet,
            filtrosSecundariosFieldSet
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
                                fechaDesdeIngOrd: DTFechaDesdeIngOrd.getValue(),
                                fechaHastaIngOrd: DTFechaHastaIngOrd.getValue(),              
                                txtLogin: txtLogin.getValue(),
                                txtCodigo: txtCodigo.getValue(),
                                txtCodigoSolicitudMasiva: txtCodigoSolicitudMasiva.getValue(), 
                                txtCiudad: txtCiudad.getValue(),              
                                cboEstados: cboEstados.getValue(),
                                cboProductos:cboProductos.getValue(),
                                txtClienteIdentificacion: txtClienteIdentificacion.getValue(),
                                cboOficinas: cboOficinas.getValue(),
                                boolMasivas: true
                            }
                    });
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {                    
                    DTFechaDesdeIngOrd.setValue(null);
                    DTFechaHastaIngOrd.setValue(null);                    
                    txtLogin.setValue('');
                    txtCodigo.setValue('');
                    txtClienteIdentificacion.setValue('');
                    txtCodigoSolicitudMasiva.setValue('');
                    txtCiudad.setValue('');                    
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
            filtrosFieldSet,
            filtrosPanel
        ],
        renderTo: 'filtro'
    });
    
    //Define un modelo para el store storeParametrosCab
    Ext.define('modelListaSolicitudesMasivas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdSolicitud', type: 'int'},
            {name: 'intIdSolicitudReferencia', type: 'int'},
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
            {name: 'strUsrCreacion', type: 'string'}
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
                fechaDesdeIngOrd: DTFechaDesdeIngOrd.getValue(),
                fechaHastaIngOrd: DTFechaHastaIngOrd.getValue(),              
                txtLogin: txtLogin.getValue(),
                txtCodigo: txtCodigo.getValue(),
                txtCodigoSolicitudMasiva: txtCodigoSolicitudMasiva.getValue(),
                txtCiudad: txtCiudad.getValue(),
                cboEstados: cboEstados.getValue(),
                cboProductos:cboProductos.getValue(),
                txtClienteIdentificacion: txtClienteIdentificacion.getValue(),
                cboOficinas: cboOficinas.getValue(),
                boolMasivas: true
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
                flex: 1,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strCodigoSolMasiva',
                header: 'Código Sol. Masiva',
                dataIndex: 'intIdSolicitudReferencia',
                flex: 1,
                sortable: true,
                align: 'center',
                renderer :entidadSolicitudMasiva.styleBold
              },
              {
                id: 'strCliente',
                header: 'Cliente',
                dataIndex: 'strCliente',
                flex: 1,
                sortable: true
              },
              {
                id: 'strProducto',
                header: 'Producto',
                dataIndex: 'strProducto',
                flex: 1,
                sortable: true
              },
              {
                id: 'strTipoSolicitud',
                header: 'Tipo Solicitud',
                dataIndex: 'strTipoSolicitud',
                flex: 1,
                sortable: true
              },
              {
                id: 'strFeCreacion',
                header: 'F. Creación',
                dataIndex: 'strFeCreacion',
                flex: 1,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strUsrCreacion',
                header: 'Creado por',
                dataIndex: 'strUsrCreacion',
                flex: 1,
                sortable: true,
                align: 'center'
              },
              {
                id: 'strEstado',
                header: 'Estado',
                dataIndex: 'strEstado',
                flex: 1,
                sortable: true,
                align: 'center',
                renderer : entidadSolicitudMasiva.estadoChange
              },
              {
                  xtype: 'actioncolumn',
                  header: 'Acciones',
                  align: 'center',
                  flex: 1,
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
                            var permiso_show = $("#ROLE_348-6");
                            var permiso_aprobar = $("#ROLE_348-163");
                            var permiso_rechazar = $("#ROLE_348-94");
                            var boolPermiso = (typeof permiso_aprobar === 'undefined' && typeof permiso_rechazar === 'undefined') ? false : (permiso_aprobar.val() == 1 || permiso_rechazar.val() == 1 ? true : false);
                            var boolPermisoShow = (typeof permiso_show === 'undefined') ? false : (permiso_show.val() == 1 ? true : false);
                            var icon = "icon-invisible";
                            if (boolPermiso) {
                                if (rec.get('strEstado')=='Pendiente') {
                                   tooltip = 'Aprobar/Rechazar Solicitud Descuento';
                                   icon = 'button-grid-Tuerca';
                                }         
                            }                            
                            if(boolPermisoShow){
                                if(rec.get('strEstado')=='Finalizada') {                                    
                                    tooltip = 'Ver Solicitud Descuento';
                                    icon = 'button-grid-show';                     
                                }
                            }
                            return icon;
                        },
                        tooltip: 'Solicitud Descuento Masiva',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeSolicitudesMasivas.getAt(rowIndex);
                            window.location = ""+rec.get('intIdSolicitud')+"/aprobarRechazarDescuento";
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