/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * New Solicitudes Masivas
 */

// Clase que tiene las funciones y objetos Generales
var entidadSolicitudMasiva = new SolicitudMasiva();
var urlRechazarSolicitud = '';
var urlAprobarSolicitud = '';

Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

Ext.onReady(function() {    
   
   var arraySolicitudesSeleccionadas = new Array();
    
    Ext.define('modelServiciosSeleccionados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdSolicitud', type: 'int'},
            {name: 'intIdServicio', type: 'int'},
            {name: 'strDescripcionServicio', type: 'string'},
            {name: 'intIdPunto', type: 'int'},
            {name: 'strLogin', type: 'string'},
            {name: 'intIdProdructo', type: 'int'},
            {name: 'strdescripcionProducto', type: 'string'},
            {name: 'strPrecioVenta', type: 'string'},
            {name: 'strPrecioMinimo', type: 'string'},
            {name: 'strDatosActuales', type: 'string'},
            {name: 'strDatosNuevos', type: 'string'},
            {name: 'strNivelAprobacion', type: 'string'},
            {name: 'strMensaje', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strObservacion', type: 'string'},
            {name: 'strEstadoPrecio', type: 'string'},
            {name: 'strEstadoRadio', type: 'string'},
            {name: 'strEstadoIpccl2', type: 'string'}
        ]
    });
    
    storeEstados = Ext.create('Ext.data.Store', {
        fields : ['strIdEstado', 'strNombreEstado'],
        data   : [
            {strIdEstado : '', strNombreEstado: 'Todos'},
            {strIdEstado : 'Pendiente', strNombreEstado: 'Pendiente'},
            {strIdEstado : 'Aprobada',  strNombreEstado: 'Aprobada'},
            {strIdEstado : 'Rechazada', strNombreEstado: 'Rechazada'},
            {strIdEstado : 'EnProceso', strNombreEstado: 'EnProceso'},
            {strIdEstado : 'Finalizada',strNombreEstado: 'Finalizada'},
            {strIdEstado : 'Fallo',strNombreEstado: 'Fallo'},
            {strIdEstado : 'Eliminada', strNombreEstado: 'Eliminada'}
        ]
    });
    cboEstados = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeEstados,
        labelAlign: 'right',
        id: 'cboEstados',
        name: 'cboEstados',
        valueField: 'strIdEstado',
        displayField: 'strNombreEstado',
        fieldLabel: 'Estado',
        value:'',
        width: 200,
        listeners:
        {
            select: function()
            {
                storeServiciosSeleccionados.proxy.extraParams = { intIdDetalleSolicitudCab: intIdDetalleSolicitudCab, strEstado:cboEstados.getValue()};
                storeServiciosSeleccionados.load();
            }
        }
    });  
    //create the toolbar with the 2 plugins
    var tbar = Ext.create('Ext.toolbar.Toolbar', {
        items  : [{
            xtype: 'tbtext',
            text: 'Filtrar Por:',
            reorderable: false
        }, '-']
    });
    tbar.add(cboEstados);
    
    storeServiciosSeleccionados = Ext.create('Ext.data.Store', {
        autoLoad: true,
        storeId: 'storeServiciosSeleccionados',
        model: 'modelServiciosSeleccionados',
        groupField: 'strLogin',
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url: urlGetDetalleSolicitudDet,
            reader: {
                type: 'json',
                root: 'jsonServiciosSeleccionados'
            },
            extraParams:{
                intIdDetalleSolicitudCab: intIdDetalleSolicitudCab,
                strEstado:cboEstados.getValue()
            }
        }
    });    
    
    groupingServiciosSeleccionados = Ext.create('Ext.grid.feature.Grouping',{
        groupHeaderTpl: 'Punto: {name} ({rows.length} Servicio{[values.rows.length > 1 ? "s" : ""]})'
    });
    
    toolbarSolicitudesSeleccionadas = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
        [{xtype: 'tbfill'},
            {
                xtype: 'button',
                cls : 'scm-button',
                id: "btnAprobarSolicitud",
                iconCls: "icon_aprobar",
                text: '<span class="bold color-green">Aprobar Seleccionados</span>',
                disabled: true,
                scope: this,
                handler: function () {
                   if(tipoAprobacion == 'radio')
                   {
                        urlAprobarSolicitud = urlAprobarSolicitudRadio;
                   } else if(tipoAprobacion == 'ipccl2'){
                        urlAprobarSolicitud = urlAprobarSolicitudIpccl2;
                   }
                   arraySolicitudesSeleccionadas = entidadSolicitudMasiva.itemsSelectedToArray(chkBoxModelSolicitudesSeleccionadas,'intIdSolicitud');
                   entidadSolicitudMasiva.aprobarSolicitudes(intIdDetalleSolicitudCab, arraySolicitudesSeleccionadas, storeServiciosSeleccionados);
                }
            },
            {
                xtype: 'button',
                cls : 'scm-button',
                id: "btnRechazarSolicitud",
                iconCls: "icon_rechazar",
                text: '<span class="bold color-wine">Rechazar Seleccionados</span>',
                disabled: true,
                scope: this,
                handler: function () {
                   if(tipoAprobacion == 'radio'){
                        urlRechazarSolicitud = urlRechazarSolicitudRadio;
                   } else if(tipoAprobacion == 'ipccl2'){
                        urlRechazarSolicitud = urlRechazarSolicitudIpccl2;
                   }
                   arraySolicitudesSeleccionadas = entidadSolicitudMasiva.itemsSelectedToArray(chkBoxModelSolicitudesSeleccionadas,'intIdSolicitud');
                   entidadSolicitudMasiva.rechazarSolicitudes(intIdDetalleSolicitudCab, arraySolicitudesSeleccionadas, storeServiciosSeleccionados);
                }
            }
        ]
    });
    
    chkBoxModelSolicitudesSeleccionadas = new Ext.selection.CheckboxModel({
        checkOnly : true,
        renderer : function(val, meta, record, rowIndex, colIndex, store,view){
            
            var estadoPrecio = record.get('strEstadoPrecio');
            var estadoRadio = record.get('strEstadoRadio');
            var estadoIpccl2 = record.get('strEstadoIpccl2');                            
            var boolEstados = (estadoPrecio == 'N/A' || estadoPrecio == 'Aprobada');                            
            if(tipoAprobacion == 'radio')
            {
                boolEstados = boolEstados && estadoRadio == 'Pendiente';
            }else if(tipoAprobacion == 'ipccl2')
            {
                boolEstados = boolEstados && (estadoRadio == 'N/A' || estadoRadio == 'Aprobada') && estadoIpccl2 == 'Pendiente';
            }
            
            if(record.get('strEstado')==='Rechazada' || record.get('strEstado')!=='Aprobada'
                || record.get('strEstado')==='EnProceso' || record.get('strEstado')==='Finalizada' || record.get('strEstado')==='Fallo' || !boolEstados){
                return '';
            }else{
                var baseCSSPrefix = Ext.baseCSSPrefix;
                meta.tdCls = baseCSSPrefix + 'grid-cell-special ' + baseCSSPrefix + 'grid-cell-row-checker';
                return '<div class="' + baseCSSPrefix + 'grid-row-checker"> </div>';
            }
        },
        onHeaderClick: function(headerCt, header, e) {
            if (header.isCheckerHd) {
                e.stopEvent();
                var isChecked = header.el.hasCls(Ext.baseCSSPrefix + 'grid-hd-checker-on');
                if (isChecked) {
                    this.deselectAll(true);
                } else {
                    var view  = this.views[0];
                    var store = view.getStore();
                    var model = view.getSelectionModel();
                    var s = [];
                    store.queryBy(function(record) {
                        
                        var estadoPrecio = record.get('strEstadoPrecio');
                        var estadoRadio = record.get('strEstadoRadio');
                        var estadoIpccl2 = record.get('strEstadoIpccl2');                            
                        var boolEstados = (estadoPrecio == 'N/A' || estadoPrecio == 'Aprobada');                            
                        if(tipoAprobacion == 'radio')
                        {
                            boolEstados = boolEstados && estadoRadio == 'Pendiente';
                        }else if(tipoAprobacion == 'ipccl2')
                        {
                            boolEstados = boolEstados && (estadoRadio == 'N/A' || estadoRadio == 'Aprobada') && estadoIpccl2 == 'Pendiente';
                        }
                        
                        if(record.get('strEstado')==='Aprobada' && boolEstados){
                            s.push(record);
                        }
                   });
                   model.select(s);
                }
            }
        },
        listeners: {
            selectionchange: function (selectionModel, selected, options) {                
                Ext.each(selected, function (rec) {});
                gridServiciosSeleccionados.down('#btnAprobarSolicitud').setDisabled(selected.length == 0);
                gridServiciosSeleccionados.down('#btnRechazarSolicitud').setDisabled(selected.length == 0);
            }
        }
    });  
    
    //Crea el grid que muestra la informacion obtenida desde el controlador de la cabera de parámetros.
    gridServiciosSeleccionados = Ext.create('Ext.grid.Panel', {
        id: 'gridServiciosSeleccionados',
        name: 'gridServiciosSeleccionados',
        title: 'Solicitudes por Servicios Seleccionados',
        store: storeServiciosSeleccionados,        
        tbar : tbar,
        height: 600,
        width: '100%',
        multiSelect: false,
        selModel: chkBoxModelSolicitudesSeleccionadas,
        dockedItems: [toolbarSolicitudesSeleccionadas],
        viewConfig: {enableTextSelection: true, preserveScrollOnRefresh: true},
        features: [groupingServiciosSeleccionados],
        columns: [
            {
                header: 'Login',
                flex: 1,
                dataIndex: 'strLogin',
                hidden: true
            },
            {
                text: 'C&oacute;digo',
                dataIndex: 'intIdSolicitud',
                align: 'center',
                renderer :entidadSolicitudMasiva.styleBold,
                hidden: true
            },
            {
                header: 'Detalle de Servicio',
                flex: 1,
                dataIndex: 'strDescripcionServicio'
            },
            {
                header: 'Datos Actuales del Servicio',
                flex: 1,
                dataIndex: 'strDatosActuales'
            },
            {
                header: 'Datos Nuevos del Servicio',
                flex: 1,
                dataIndex: 'strDatosNuevos'
            },
            {
                text: 'Precio Mínimo',
                width: 80,
                align: 'center',
                dataIndex: 'strPrecioMinimo'
            },
            {
                header: 'Estado',
                width: 80,
                align: 'center',                
                dataIndex: 'strEstado',
                renderer : entidadSolicitudMasiva.estadoChange
            },
            {
                header: 'Nivel de Aprobaci&oacute;n/Estados',
                width: 150,
                dataIndex: 'strNivelAprobacion'
            },
            {
                header: 'Mensaje',
                width: 150,
                dataIndex: 'strMensaje'
            },
            {
                header: 'Observaci&oacute;n',
                flex: 1,
                dataIndex: 'strObservacion'
            },    
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                align: 'center',
                width: 100,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                                return 'button-grid-logs';
                        },
                        tooltip: 'Ver Historial',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeServiciosSeleccionados.getAt(rowIndex);
                            var intIdSolicitud = rec.get('intIdSolicitud');
                            entidadSolicitudMasiva.showHistorialSolicitudesMasivas(intIdSolicitud);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            
                            var estadoPrecio = rec.get('strEstadoPrecio');
                            var estadoRadio = rec.get('strEstadoRadio');
                            var estadoIpccl2 = rec.get('strEstadoIpccl2');                            
                            var boolEstados = (estadoPrecio == 'N/A' || estadoPrecio == 'Aprobada');                            
                            if(tipoAprobacion == 'radio')
                            {
                                boolEstados = boolEstados && estadoRadio == 'Pendiente';
                            }else if(tipoAprobacion == 'ipccl2')
                            {
                                boolEstados = boolEstados && (estadoRadio == 'N/A' || estadoRadio == 'Aprobada') && estadoIpccl2 == 'Pendiente';
                            }
                            
                            var permisoRadio = $("#ROLE_351-163");
                            var boolPermisoRadio = (typeof permisoRadio === 'undefined') ? false : (permisoRadio.val() == 1 ? true : false) && tipoAprobacion == 'radio';
                            
                            var permisoIpccl2 = $("#ROLE_352-163");
                            var boolPermisoIpccl2 = (typeof permisoIpccl2 === 'undefined') ? false : (permisoIpccl2.val() == 1 ? true : false) && tipoAprobacion == 'ipccl2';
                            
                            if ((boolPermisoRadio || boolPermisoIpccl2) && boolEstados && rec.get('strEstado') === 'Aprobada') {
                                   return 'button-grid icon_aprobar';
                            }
                        },
                        tooltip: 'Aprobar Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeServiciosSeleccionados.getAt(rowIndex);
                            var arraySeleccionado = new Array();
                            arraySeleccionado.push(rec.get('intIdSolicitud'));
                            
                            if(tipoAprobacion == 'radio'){
                                urlAprobarSolicitud = urlAprobarSolicitudRadio;
                            } else if(tipoAprobacion == 'ipccl2'){
                                urlAprobarSolicitud = urlAprobarSolicitudIpccl2;
                            }
                            
                            entidadSolicitudMasiva.aprobarSolicitudes(intIdDetalleSolicitudCab, arraySeleccionado, storeServiciosSeleccionados);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            
                            var estadoPrecio = rec.get('strEstadoPrecio');
                            var estadoRadio = rec.get('strEstadoRadio');
                            var estadoIpccl2 = rec.get('strEstadoIpccl2');                            
                            var boolEstados = (estadoPrecio == 'N/A' || estadoPrecio == 'Aprobada');                            
                            if(tipoAprobacion == 'radio')
                            {
                                boolEstados = boolEstados && estadoRadio == 'Pendiente';
                            }else if(tipoAprobacion == 'ipccl2')
                            {
                                boolEstados = boolEstados && (estadoRadio == 'N/A' || estadoRadio == 'Aprobada') && estadoIpccl2 == 'Pendiente';
                            }
                            
                            var permisoRadio = $("#ROLE_351-94");
                            var boolPermisoRadio = (typeof permisoRadio === 'undefined') ? false : (permisoRadio.val() == 1 ? true : false) && tipoAprobacion == 'radio';
                            
                            var permisoIpccl2 = $("#ROLE_352-94");
                            var boolPermisoIpccl2 = (typeof permisoIpccl2 === 'undefined') ? false : (permisoIpccl2.val() == 1 ? true : false) && tipoAprobacion == 'ipccl2';
                            
                            if ((boolPermisoRadio || boolPermisoIpccl2) && boolEstados && rec.get('strEstado') === 'Aprobada') {
                                   return 'button-grid icon_rechazar';
                            }
                        },
                        tooltip: 'Rechazar Solicitud',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeServiciosSeleccionados.getAt(rowIndex);
                            var arraySeleccionado = new Array();
                            arraySeleccionado.push(rec.get('intIdSolicitud'));
                            
                            if(tipoAprobacion == 'radio'){
                                urlRechazarSolicitud = urlRechazarSolicitudRadio;
                            } else if(tipoAprobacion == 'ipccl2'){
                                urlRechazarSolicitud = urlRechazarSolicitudIpccl2;
                            }
                            
                            entidadSolicitudMasiva.rechazarSolicitudes(intIdDetalleSolicitudCab, arraySeleccionado, storeServiciosSeleccionados);
                        }
                    }
                ]
            }
        ],
        bbar: new Ext.PagingToolbar({
            store: storeServiciosSeleccionados,
            pageSize: entidadSolicitudMasiva.intPageSize,
            displayInfo: true,            
            prependButtons: true,
            displayMsg:entidadSolicitudMasiva.displayMsg,
            emptyMsg: entidadSolicitudMasiva.emptyMsg
        })
    });
    
    objSolicitudMasivaFieldSet = Ext.create('Ext.form.FieldSet', {
        id: 'objSolicitudMasivaFieldSet',
        name: 'objSolicitudMasivaFieldSet',
        frame: true,
        width: 1280,
        items: [                
            gridServiciosSeleccionados
        ]
    });
    
    objContenedorPanel = Ext.create('Ext.panel.Panel', {
        id: 'objContenedorPanel',
        name: 'objContenedorPanel',
        border: false,
        buttonAlign: 'center',
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 4,
            align: 'left'
        },
        width: 1400,
        items: [                
            objSolicitudMasivaFieldSet
        ],
        renderTo: 'objContenedorPanel'
    });
    
    entidadSolicitudMasiva.initSeguimiento(intIdDetalleSolicitudCab, 'seguimiento_content');
});