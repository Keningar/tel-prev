/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * New Solicitudes Masivas
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
      
    Ext.define('modelServiciosSeleccionados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdServicio', type: 'int'},
            {name: 'intIdSolicitud', type: 'int'},
            {name: 'strDescripcionServicio', type: 'string'},
            {name: 'intIdPunto', type: 'int'},
            {name: 'strLogin', type: 'string'},
            {name: 'intIdProdructo', type: 'int'},
            {name: 'strdescripcionProducto', type: 'string'},
            {name: 'strPrecioVenta', type: 'string'},
            {name: 'strPrecioMinimo', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strDatosActuales', type: 'string'},
            {name: 'strDatosNuevos', type: 'string'},
            {name: 'strNivelAprobacion', type: 'string'},
            {name: 'strMensaje', type: 'string'}
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
    
    //Crea el grid que muestra la información obtenida desde el controlador de la cabera de parámetros.
    gridServiciosSeleccionados = Ext.create('Ext.grid.Panel', {
        id: 'gridServiciosSeleccionados',
        name: 'gridServiciosSeleccionados',
        title: 'Solicitudes por Servicios Seleccionados',
        tbar : tbar,
        store: storeServiciosSeleccionados,        
        height: 600,
        width: '100%',
        multiSelect: false,        
        viewConfig: {enableTextSelection: true, preserveScrollOnRefresh: true},
        features: [groupingServiciosSeleccionados],
        columns: [
            {
                text: 'Login',
                flex: 1,
                dataIndex: 'strLogin',
                hidden: true
            },
            {
                text: 'Código',
                dataIndex: 'intIdSolicitud',
                align: 'center',
                renderer : entidadSolicitudMasiva.styleBold,
                hidden: true
            },
            {
                text: 'Detalle de Servicio',
                flex: 1,
                dataIndex: 'strDescripcionServicio'
            },
            {
                text: 'Datos Actuales del Servicio',
                flex: 1,
                dataIndex: 'strDatosActuales'
            },
            {
                text: 'Datos Nuevos del Servicio',
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
                width: 90,
                align: 'center',                
                dataIndex: 'strEstado',
                renderer : entidadSolicitudMasiva.estadoChange
            },
            {
                text: 'Nivel de Aprobación / Estados',
                flex: 1,
                dataIndex: 'strNivelAprobacion'
            },
            {
                text: 'Mensaje',
                flex: 1,
                dataIndex: 'strMensaje'
            },
            {
                header: 'Observación',
                flex: 1,
                dataIndex: 'strObservacion'
            },
            {
                  xtype: 'actioncolumn',
                  header: 'Acciones',
                  align: 'center',
                  width: 60,
                  items: [
                    {
                        getClass: function(v, meta, rec) {
                                tooltip = 'Ver Historial';
                                return 'button-grid-logs';
                        },
                        tooltip: 'Ver Historial',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeServiciosSeleccionados.getAt(rowIndex);
                            var intIdSolicitud = rec.get('intIdSolicitud');
                            entidadSolicitudMasiva.showHistorialSolicitudesMasivas(intIdSolicitud);
                        }
                    }
                  ]
            }
        ],
        bbar: new Ext.PagingToolbar({
            store: storeServiciosSeleccionados,       
            displayInfo: true,
            prependButtons: true,
            displayMsg:entidadSolicitudMasiva.displayMsg,
            emptyMsg: entidadSolicitudMasiva.emptyMsg,            
            pageSize: entidadSolicitudMasiva.intPageSize
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



