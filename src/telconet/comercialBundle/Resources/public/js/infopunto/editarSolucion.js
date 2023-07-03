
var solucionEditada               = 0;
var nombreSolucionEditada         = '';
var arrayProductosNormales        = [];
var arrayProductosPreferenciales  = [];
var arrayTipoSoluciones           = [];
var arrayTipoSolucionesExistentes = [];
var esMultiCaracteristica         = false;
var contieneTipoSolucion          = false;
var esTipoSolucionPreferencial    = false;
//Informacion generica del producto
var nombreProducto                = '';
var tipoProductoConfigurado       = '';
var tipoSubSolucion               = '';
//Edicion de servicios
var arrayInformacionServicio      = [];
var boolEsEdicion                 = false;
var idServicioEditado             = 0;
//VARIABLE DE REFERENCIA QUE INDICA QUE NO ENCONTRAMOS EN LA EDICIÓN DE LA SOLUCIÓN.
var boolEsEditarSolucion          = true;
var intIdServicio                 = null;

Ext.onReady(function() 
{
    Ext.tip.QuickTipManager.init();
    
    var jsonSoluciones = JSON.parse(arraySoluciones);
    
    Ext.define('solucionesModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'numeroSolucion', type: 'string'},
            {name: 'nombreSolucion', type: 'string'},
            {name: 'totalSolucion',  type: 'string'}
        ]
    }); 
                
    storeSoluciones = new Ext.data.Store({
        pageSize: 5,
        autoDestroy: true,
        model: 'solucionesModel',
        proxy: {
            type: 'memory'
        }
    });
    
    $.each(jsonSoluciones,function(i , item)
    {
        var recordParamDet = Ext.create('solucionesModel', {
            numeroSolucion: item.numeroSolucion,
            nombreSolucion: item.nombreSolucion,
            totalSolucion : item.totalSolucion
        });

        storeSoluciones.insert(i, recordParamDet);
    });
                
    gridSoluciones = Ext.create('Ext.grid.Panel', {
        width: 760,
        id:'gridSoluciones',
        height: 173,
        title:'<b>Soluciones creadas por Punto</b>',
        store: storeSoluciones,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',    
        renderTo:'content-soluciones',
        columns: [
            {
                header   : '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
                xtype    : 'rownumberer',
                align    : 'center',
                width    :  25,
                sortable :  false,
                hideable :  false
            },
            {
                id: 'numeroSolucion',
                header: '<b>Número Solución</b>',
                dataIndex: 'numeroSolucion',
                width: 120,
                renderer: function(val)
                {                    
                    return '<i class="fa fa-shopping-bag" aria-hidden="true"></i>&nbsp;\n\
                            <label style="color:#4D793E;"><b>Sol. # '+val+'</b></label>';
                }
            },
            {
                id: 'nombreSolucion',
                header: '<b>Nombre de Solución</b>',
                dataIndex: 'nombreSolucion',
                width: 350
            },
            {
                id: 'totalSolucion',
                header: '<b>Total Solución</b>',
                dataIndex: 'totalSolucion',
                width: 120,
                renderer: function(val)
                {                    
                    return '<i class="fa fa-dollar" aria-hidden="true"></i>&nbsp;\n\
                            <label style="color:#4D793E;"><b>'+val+'</b></label>';
                }
            },
            {
                xtype    : 'actioncolumn',
                header   : '<b>Acciones</b>',
                width    :  120,
                align    : 'center',
                sortable :  false,
                hideable :  false,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-editarDireccion';
                        },
                        tooltip: 'Actualizar Nombre de Solución',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            editarNombreSolucion(grid.getStore().getAt(rowIndex).data);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-show';
                        },
                        tooltip: 'Visualizar Productos',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            nuevaSolucion = false;
                            limpiarArrays();
                            solucionEditada       = grid.getStore().getAt(rowIndex).data.numeroSolucion;
                            nombreSolucionEditada = grid.getStore().getAt(rowIndex).data.nombreSolucion;
                            $("#lbl-txt-solucion").text('# '+solucionEditada);
                            storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                            storeDetalle.removeAll();
                            storeDetalle.load({params: {}});
                            Ext.getCmp('btnAgregarProducto').setDisabled(false);
                        }
                    }
                ]
            }
        ]
    });

    Ext.define('detalleProductosModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idServicio', mapping: 'idServicio'},
            {name: 'idProducto', mapping: 'idProducto'},
            {name: 'descripcion',mapping: 'descripcion'},
            {name: 'estado',     mapping: 'estado'},
            {name: 'precio',     mapping: 'precio'},
            {name: 'tipoSolucion', mapping: 'tipoSolucion'},
            {name: 'segmento',     mapping: 'segmento'},
            {name: 'contieneCarcateristica', mapping: 'contieneCarcateristica'},
            {name: 'esCore'        , mapping: 'esCore'},
            {name: 'esPreferencial', mapping: 'esPreferencial'},
            {name: 'secuencial', mapping: 'secuencial'},
            {name: 'solicitud', mapping: 'solicitud'},
            {name: 'coresReferentes', mapping: 'coresReferentes'},
            {name: 'feCreacion', mapping: 'feCreacion'}
        ]
    });

    storeDetalle = new Ext.data.Store({
        autoLoad :  false,
        pageSize :  14,
        total    : 'total',
        model    : 'detalleProductosModel',
        proxy: {
            timeout :  999999999,
            type    : 'ajax',
            url     :  urlGetDetallesPorSolucion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'arrayResultado'
            },
            actionMethods: {
                create: 'GET', read: 'GET', update: 'GET', destroy: 'GET'
            },
            extraParams: {
                numeroSolucion: ''
            }
        },
        listeners: {
            beforeload : function() {
                Ext.get('servicios').mask('Espere por favor...');
            },
            load: function(data,j,k) {
                Ext.get('servicios').unmask();

                var boolExiste = (typeof data.getProxy().getReader().rawData === 'undefined') ? false :
                    (typeof data.getProxy().getReader().rawData.arrayMaquinasVirtualesPorPool === 'undefined') ? false : true;

                if (boolExiste) {
                    var arrayMaquinas = data.getProxy().getReader().rawData.arrayMaquinasVirtualesPorPool;
                    obtenerMaquinasVirtuales(arrayMaquinas);
                }
            }
        }
    });

    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock  : 'top',
        align : '->',
        id    : 'tlbAgregar',
        items :
        [
            {
                iconCls : 'icon_add',
                text    : 'Agregar Producto',
                id      : 'btnAgregarProducto',
                scope   :  this,
                handler: function()
                {
                    nuevaSolucion = false;
                    boolEsEdicion = false;
                    agregarServicio();
                }
            },
            {
                iconCls : 'iconSave',
                text    : 'Guardar Solución',
                id      : 'btnEditarSolucion',
                scope   : this,
                handler: function()
                {
                    nuevaSolucion = false;
                    showResumen();
                }
            }
        ]
	});

    //grid de detalles
    gridDetalle = Ext.create('Ext.grid.Panel', {
        width       :  1150,
        id          : 'gridDetalle',
        height      :  300,
        store       :  storeDetalle,
        loadMask    :  true,
        renderTo    : 'content-productos-solucion',
        frame       :  false,
        dockedItems : [toolbar],
        viewConfig  : {
            emptyText: 'No hay datos para mostrar',
            getRowClass: function(record, index) {
                var estado = record.get('estado');
                if (estado === 'PorAgregar') {
                    return 'grisTextGrid';
                } else {
                    return 'blackTextGrid';
                }
            }
        } ,
        columns: [
            {
                header   : '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
                xtype    : 'rownumberer',
                align    : 'center',
                width    :  25,
                sortable :  false,
                hideable :  false
            },
            {
                id: 'idServicio',
                header: 'idServicio',
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'idProducto',
                header: 'idProducto',
                dataIndex: 'idProducto',
                hidden: true,
                hideable: false
            },
            {
                id        : 'esCore',
                header    : 'esCore',
                dataIndex : 'esCore',
                hidden    :  true,
                hideable  :  false
            },
            {
                id        : 'esPreferencial',
                header    : 'esPreferencial',
                dataIndex : 'esPreferencial',
                hidden    :  true,
                hideable  :  false
            },
            {
                id        : 'segmento',
                dataIndex : 'segmento',
                align     : 'center',
                width     : 30,
                sortable  : false,
                hideable  : false,
                renderer  : function(val)
                {
                    if (!Ext.isEmpty(val)) {
                        return '<i class="fa fa-square" aria-hidden="true" style="color:'+val+'"></i>';
                    }
                }
            },
            {
                id: 'tipoSubSolucion',
                header: '<b>Tipo de Solución</b>',
                dataIndex: 'tipoSolucion',
                width: 200,
                sortable: true
            },
            {
                id: 'descripcion',
                header: '<b>Descripción</b>',
                dataIndex: 'descripcion',
                width: 250,
                sortable: true
            },
            {
                id: 'coresReferentes',
                header: '<b>Cores Relacionados</b>',
                dataIndex: 'coresReferentes',
                width: 200,
                sortable: true,
                renderer: function(val)
                {                  
                    if(!Ext.isEmpty(val))
                    {
                        var array = val.split("|");
                    
                        var html = '<ul>';

                        $.each(array, function(i,item)
                        {
                            if(!Ext.isEmpty(item))
                            {
                                html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>'; 
                            }
                        });

                        html += '</ul>';
                        return html;
                    }
                }
            },
            {
                id        : 'precio',
                header    : '<b>Precio</b>',
                dataIndex : 'precio',
                width     :  100,
                sortable  :  false,
                renderer  : function(val) {
                    return '<i class="fa fa-dollar" aria-hidden="true"></i>&nbsp;\n\
                            <label style="color:#4D793E;"><b>'+val+'</b></label>';
                }
            },
            {
                id        : 'estado',
                header    : '<b>Estado</b>',
                dataIndex : 'estado',
                width     : 125,
                sortable  : true,
                renderer  : function(val)
                {
                    if (val === 'PorAgregar') {
                        return '<b style="color:green;">'+val+'</b>';
                    } else {
                        return val;
                    }
                }
            },
            {
                id        : 'secuencial',
                header    : 'secuencial',
                dataIndex : 'secuencial',
                hidden    :  true,
                hideable  :  false
            },
            {
                id        : 'feCreacion',
                header    : '<b>Fe. Creación</b>',
                dataIndex : 'feCreacion',
                align     : 'center',
                width     :  110,
                sortable  :  false
            },
            {
                xtype    : 'actioncolumn',
                header   : '<b>Acciones</b>',
                width    :  105,
                sortable :  false,
                hideable :  false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if(rec.get('estado') === 'Pre-servicio' || 
                               rec.get('estado') === 'PreFactibilidad' ||
                               rec.get('estado') === 'FactibilidadEnProceso' ||
                               rec.get('estado') === 'PreAsignacionInfoTecnica' ||
                               rec.get('estado') === 'Pendiente')
                            {
                                return 'button-grid-edit';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                            
                        },
                        tooltip: 'Editar Servicio',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            nuevaSolucion = false;
                            for (var i = 0; i < gridDetalle.getStore().getCount(); i++)
                            {
                                var idServicio = gridDetalle.getStore().getAt(i).data.idServicio;

                                if (idServicio === '0')
                                {
                                   Ext.MessageBox.show({
                                        title      : 'Alerta',
                                        msg        : 'Estimado usuario por favor guardar los servicios en estado '+
                                                     '<b style="color:green;">PorAgregar</b>.',
                                        closable   : false,
                                        multiline  : false,
                                        icon       : Ext.Msg.WARNING,
                                        buttons    : Ext.Msg.YES,
                                        buttonText : {yes: 'Cerrar'}
                                    });
                                    return;
                                }
                            }
                            editarServicioSolucion(grid.getStore().getAt(rowIndex));
                        }
                    },
                    {
                        getClass: function(v, meta, rec)
                        {
                            if ( (rec.get('estado') === 'Pendiente'     ||
                                 rec.get('estado') === 'Pre-servicio'   ||
                                 rec.get('estado') === 'PreFactibilidad'||
                                 rec.get('estado') === 'Factible'       ||
                                 rec.get('estado') === 'PrePlanificada' ||
                                 rec.get('estado') === 'Rechazado'      ||
                                 rec.get('estado') === 'FactibilidadEnProceso'     ||
                                 rec.get('estado') === 'PreAsignacionInfoTecnica'  ||
                                 rec.get('estado') === 'PorAgregar')  &&
                                 Ext.isEmpty(rec.get('contieneCarcateristica'))
                               )
                            {
                                return 'button-grid-delete';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        },
                        tooltip: 'Eliminar Servicio',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            eliminarProducto(grid.getStore().getAt(rowIndex));
                        }
                    }
                ]
            }
        ],
        listeners:
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
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

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;
                    
                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        }
    });  
    
    Ext.getCmp('btnAgregarProducto').setDisabled(true);
    Ext.getCmp('btnEditarSolucion').setDisabled(true);
    
    //configurar ventana de configuracion de productos
    $("#content-conf-producto").dialog({
        autoOpen: false,
        modal: true,
        height:"auto",
        width:'auto',
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "blind",
            duration: 250
        },
        buttons: [
            {
                id: "button-configurar",
                text: "Agregar Producto",
                disabled: true,
                click: function() 
                {
                    validarFormulario();
                    
                    if(arraySolucion.length > 0)
                    {
                        var boolEsNuevoSubTipo = true;

                        $.each(arrayTipoSolucionesExistentes,function(i,item){
                            if(tipoSubSolucion === item['tipoSolucion'])
                            {
                                boolEsNuevoSubTipo = false;
                                return false;
                            }
                        });

                        //Si existe un nuevo tipo de solucion o se agrega un preferencial nuevo se configura la relacion
                        //core y preferencial
                        if(boolEsNuevoSubTipo || esTipoSolucionPreferencial)
                        {
                            //Realizar la relacion entre preferenciales y cores
                            mostrarVentanaRelacionCorePreferencial();
                        }
                        else
                        {
                            $(this).dialog("close");
                            agregarProductoGrid();
                        }
                    }
                }
            },
            {
                id: "button-cerrar",
                text: "Cerrar",
                click: function() {
                                        
                    arraySolucion = arraySolucion.filter(function(elem){        
                        return elem.secuencial !== identificadorProducto; 
                    });

                    intTotalPrecioUnitarioDC = 0;
                    valorAntProcesador       = 0;
                    valorAntMemoria          = 0;
                    valorAntStorage          = 0;
                    $(this).dialog("close");

                    validarGridRegistrosNuevos();
                }
            }]        
    });

    //Modal para guardar nuevos servicios a una solución.
    $("#content-resumen").dialog({
        autoOpen :  false,
        modal    :  true,
        height   : 'auto',
        width    : 'auto',
        show: {
            effect: "blind",
            duration: 250
        },
        hide: {
            effect: "blind",
            duration: 250
        },
        buttons: [
            {
                id: "button-confirmar",
                click: function() 
                {
                    ajaxEditarSolucion();
                }
            },
            {
                id: "button-cerrar-resumen",
                click: function() 
                {
                    //Borrara segun el secuencial
                    $(this).dialog("close");
                    $("#button-confirmar").find('i').remove();
                    $("#button-cerrar-resumen").find('i').remove();
                }
            }
        ]
    }); 

    $("#content-relacion-subtiposolucion").dialog({
        autoOpen : false,
        modal    : true,
        height   : 'auto',
        width    : 'auto',
        show: {
            effect   : "blind",
            duration : 250
        },
        hide: {
            effect   : "blind",
            duration : 250
        },
        buttons: [
            {
                id    : "button-confirmar-relacion",
                text  : "Agregar Producto",
                click : function() 
                {
                    relacionarCoreYPreferencial();
                }
            },
            {
                id    : "button-cerrar-relacion",
                text  : "Cerrar",
                click : function() 
                {
                    arraySolucion = arraySolucion.filter(function(elem){
                        return elem.secuencial !== identificadorProducto; 
                    });

                    arrayMaquinasVirtuales = arrayMaquinasVirtuales.filter(function(elem){
                        return elem.secuencial !== identificadorProducto; 
                    });

                    arrayInformacion = [];
                    $(this).dialog("close");
                }
            }]
    });
   
    $("#content-editar-producto").dialog({
        autoOpen: false,
        modal: true,
        height:"auto",
        width:'auto',
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "blind",
            duration: 250
        },
        buttons: [
            {
                id: "button-editar",
                text: "Editar Servicio",
                click: function() 
                {
                    validarFormulario();
                               
                    if(arraySolucion.length > 0)
                    {
                        ajaxEditarServicioSolucion();
                    }
                }
            },
            {
                id: "button-cerrar",
                text: "Cerrar",
                click: function() 
                {
                    arraySolucion = [];
                    $(this).dialog("close");   
                    
                    limpiarPanel();
                }
            }]        
    });
});

function agregarServicio()
{
    var grupoSeleccionado = '';
    var contadorRepetidos = 0;
    arrayTipoSolucionesExistentes = [];
    
    for (var i = 0; i < gridDetalle.getStore().getCount(); i++)
    {
        contadorRepetidos  = 0;
        var esPreferencial = gridDetalle.getStore().getAt(i).data.esPreferencial;
        var tipoSolucion   = gridDetalle.getStore().getAt(i).data.tipoSolucion;
        var descripcion    = gridDetalle.getStore().getAt(i).data.descripcion;

        var json               = {};
        json['tipoSolucion']   = tipoSolucion;
        json['descripcion']    = descripcion;
        json['esPreferencial'] = esPreferencial;
        json['idServicio']     = gridDetalle.getStore().getAt(i).data.idServicio;
        json['esCore']         = gridDetalle.getStore().getAt(i).data.esCore;

        $.each(arrayTipoSolucionesExistentes,function(i, item){
            if(esPreferencial === 'N')
            {
                if (item['tipoSolucion'] === tipoSolucion) {
                    contadorRepetidos ++;
                }
            }
        });

        if (contadorRepetidos === 0) {
            arrayTipoSolucionesExistentes.push(json);
        }
    }

    $.ajax({
        type   : "POST",
        url    : urlGetGrupoSubgrupo,
        data   : 
        {
          'tipo'     : 'GRUPO',
          'grupo'    : '',
          'subgrupo' : ''
        },
        beforeSend: function() 
        {            
            Ext.get(document.body).mask('Cargando Información de Grupo de Productos');
        },
        complete: function() 
        {
            Ext.get(document.body).unmask();
        },
        success: function(data)
        {            
            Ext.define('productosModel', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idProducto',           type: 'integer'},
                    {name: 'descripcionProducto',  type: 'string'},
                    {name: 'tipo',  type: 'string'},
                ]
            });  
            
            storeProductosNormales = Ext.create('Ext.data.Store', {
                pageSize: 5,                
                autoDestroy: true,
                model: 'productosModel',
                proxy: {
                    type: 'memory'
                }
            });  
            
            //Grid de Productos Normales
            var gridProductosNormales = Ext.create('Ext.grid.Panel', {
                width: 420,
                title:'Listado de Productos',
                id:'gridProductosNormales',
                height: 200,                
                store: storeProductosNormales,
                loadMask: true,
                frame: false,        
                viewConfig: {enableTextSelection: true, emptyText:'No hay Productos para mostrar'},
                columns: [
                    {
                        id: 'idProductoNormal',
                        dataIndex: 'idProducto',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'idTipo',
                        dataIndex: 'tipo',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'descripcionProductoNormal',      
                        header: '<b>Descripción Producto</b>',
                        dataIndex: 'descripcionProducto',
                        width: 350,
                        sortable: true                        
                    },
                    {
                        xtype: 'actioncolumn',
                        header: '<i align="center" class="fa fa-cogs" aria-hidden="true"></i>',
                        width: 35,
                        items: 
                        [
                            {
                                getClass: function(v, meta, rec) 
                                {                            
                                    return 'button-grid-seleccionar';
                                },
                                tooltip: 'Configurar Producto',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    nombreProducto          = grid.getStore().getAt(rowIndex).data.descripcionProducto;
                                    tipoProductoConfigurado = grid.getStore().getAt(rowIndex).data.tipo;
                                        
                                    if(contieneTipoSolucion)
                                    {
                                        if(Ext.isEmpty(Ext.getCmp('cmbAgrupacion').getValue()))
                                        {
                                            Ext.Msg.alert('Atención', 'Debe escoger un Tipo de Solución para configurar el Producto');
                                        }
                                        else
                                        {
                                            //Configurar Producto
                                            accion = 'agregar';
                                            configurarProductos(grid.getStore().getAt(rowIndex).data.idProducto,
                                                                nombreProducto, accion, null, false);
                                        }
                                    }
                                    else
                                    {
                                        //Configurar Producto
                                        configurarProductos(grid.getStore().getAt(rowIndex).data.idProducto,
                                                            nombreProducto, accion, null, false);
                                    }
                                }
                            }
                        ]
                    }
                ]
            });  
 
            var storeGrupos = new Ext.data.Store({
                fields: ['grupo','grupo'],
                data: data.arrayRespuestaGenerica
            });
            
            var htmlDivisor = Ext.create('Ext.Component', {
                html: '<div class="secHead"><label style="text-align:left;">\n\
                       <b><i class="fa fa-tags" aria-hidden="true"></i>&nbsp;</b><label id="lbl-info">Consulta de productos...</label></div>',
                padding: 1,
                layout: 'anchor'
            });

            var formPanelAgregarServicio = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                BodyPadding: 2,
                width:'auto',
                height:'auto',
                layout: {
                    type: 'table',
                    columns: 2
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                frame: true,
                items: 
                [
                    {
                        colspan: 1,
                        rowspan: 2,
                        xtype: 'panel',
                        width:450,
                        height:400,
                        title: 'Información de Productos',
                        defaults: { 
                            height: 75
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: 
                                [
                                    {
                                        xtype:           'combobox',
                                        name:            'cmbGrupoProductos',
                                        id:              'cmbGrupoProductos',
                                        fieldLabel:      '<b>Grupo</b>',
                                        displayField:    'grupo',
                                        valueField:      'grupo',
                                        store:           storeGrupos,
                                        editable:        false,
                                        width:           400,
                                        listeners: {
                                            select: function(combo) 
                                            { 
                                                Ext.getCmp('cmbSubGrupoProductos').setDisabled(true);
                                                Ext.getCmp('cmbAgrupacion').setDisabled(true);  
                                                
                                                Ext.getCmp('cmbAgrupacion').setValue("");  
                                                Ext.getCmp('cmbAgrupacion').setRawValue("");  
                                                Ext.getCmp('cmbSubGrupoProductos').setValue("");  
                                                Ext.getCmp('cmbSubGrupoProductos').setRawValue("");
                                                
                                                gridProductosNormales.getStore().removeAll();
                                                
                                                grupoSeleccionado = combo.getValue();
                                                //Obtener subgrupo de productos
                                                getProductos('SUBGRUPO',combo.getValue(),'');
                                            }
                                        }
                                    },   
                                    {
                                        xtype:           'combobox',
                                        name:            'cmbSubGrupoProductos',
                                        id:              'cmbSubGrupoProductos',
                                        fieldLabel:      '<b>SubGrupo</b>',
                                        displayField:    'subgrupo',
                                        valueField:      'subgrupo',
                                        editable:        false,
                                        disabled:        true,
                                        width:           400,
                                        listeners: {
                                            select: function(combo) 
                                            {
                                                Ext.getCmp('cmbAgrupacion').setValue("");  
                                                Ext.getCmp('cmbAgrupacion').setRawValue("");
                                                
                                                gridProductosNormales.getStore().removeAll();
                                                
                                                //Obtener subgrupo de productos
                                                getProductos('PRODUCTO',grupoSeleccionado,combo.getValue());
                                            }
                                        }
                                    },
                                    {
                                        xtype:           'combobox',
                                        name:            'cmbAgrupacion',
                                        id:              'cmbAgrupacion',
                                        fieldLabel:      '<b>Tipo Solución</b>',
                                        displayField:    'subSolucion',
                                        valueField:      'subSolucion',
                                        editable:        false,
                                        disabled:        true,
                                        width:           400,
                                        listeners: {
                                            select: function(combo) 
                                            {
                                                var arrayGrid   = [];
                                                tipoSubSolucion = combo.getValue();
                                                
                                                $.each(arrayTipoSoluciones, function(i, item) 
                                                {
                                                    if(item.subSolucion === combo.getValue())
                                                    {
                                                        if(item.tipo === 'P')
                                                        {
                                                            esTipoSolucionPreferencial = true;
                                                            arrayGrid = arrayProductosPreferenciales;
                                                            $("#lbl-info").html("Configuración de Productos \n\
                                                                                 <b style='color:green;'>Preferenciales</b>");
                                                        }
                                                        else
                                                        {
                                                            esTipoSolucionPreferencial = false;
                                                            arrayGrid = arrayProductosNormales;
                                                            $("#lbl-info").html("Configuración de Productos <b>Normales</b>");
                                                        }
                                                        return false;
                                                    }
                                                });
                                                
                                                gridProductosNormales.getStore().removeAll();
                                                
                                                $.each(arrayGrid, function(i, item) 
                                                {
                                                    var recordParamDet = Ext.create('productosModel', {
                                                        idProducto           : item.idProducto,
                                                        descripcionProducto  : item.descripcionProducto,
                                                        tipo                 : item.tipo
                                                    });

                                                    storeProductosNormales.insert(i, recordParamDet);
                                                });
                                            }
                                        }
                                    },
                                    htmlDivisor,
                                    gridProductosNormales
                                ]
                            }
                        ]
                    }
                ],
                buttons: [
                    {
                        text: 'Cerrar',
                        handler: function() {
                            winAgregarServicio.close();
                            winAgregarServicio.destroy();
                        }
                    }
                ]});

            winAgregarServicio = Ext.widget('window', {
                id:'winAgregarServicio',
                title: 'Agregar Nuevo Servicio a la Solución <b style="color:green;"># '+solucionEditada+'</b>',
                layout: 'fit',
                resizable: true,
                modal: true,
                closable: true,
                width:'auto',
                items: [formPanelAgregarServicio]
            });

            winAgregarServicio.show();
        }
    }); 
}

function getProductos(tipo,grupo,subgrupo)
{
    var msg = '';
    
    if(tipo === 'SUBGRUPO')
    {
        msg = 'Cargando Información de Sub-Grupo de Productos';
    }
    else
    {
        msg = 'Cargando Información de de Productos';
    }
    
    $.ajax({
        type   : "POST",
        url    : urlGetGrupoSubgrupo,
        data   : 
        {
          'tipo'     : tipo,
          'grupo'    : grupo,
          'subgrupo' : subgrupo
        },
        beforeSend: function() 
        {            
            Ext.get('winAgregarServicio').mask(msg);
        },
        complete: function() 
        {
            Ext.get('winAgregarServicio').unmask();
        },
        success: function(data)
        {            
            if(tipo === 'SUBGRUPO')
            {
                Ext.getCmp('cmbSubGrupoProductos').setDisabled(false);
                
                var storeSubGrupos = new Ext.data.Store({
                    fields: ['subgrupo','subgrupo'],
                    data: data.arrayRespuestaGenerica
                });
                
                Ext.getCmp('cmbSubGrupoProductos').bindStore(storeSubGrupos);
                
                if(data.arraySubSolucion.length > 0)
                {
                    arrayTipoSoluciones  = data.arraySubSolucion;
                    contieneTipoSolucion = true;
                    
                    Ext.getCmp('cmbAgrupacion').setDisabled(false);
                    
                    var storeSubTipoSolucion = new Ext.data.Store({
                        fields: ['subSolucion','tipo'],
                        data: data.arraySubSolucion
                    });
                    
                    Ext.getCmp('cmbAgrupacion').bindStore(storeSubTipoSolucion);
                }
                else
                {
                    contieneTipoSolucion = false;
                    arrayTipoSoluciones  = [];
                }
            }
            else//PRODUCTO
            {
                arrayProductosNormales = data.arrayRespuestaGenerica;
                
                $.each(arrayProductosNormales, function(i, item) 
                {
                    var recordParamDet = Ext.create('productosModel', {
                        idProducto           : item.idProducto,
                        descripcionProducto  : item.descripcionProducto
                    });

                    storeProductosNormales.insert(i, recordParamDet);
                });
                
                $("#lbl-info").html("Configuración de Productos <b>Normales</b>");

                if (data.arrayProductosReferencia.length > 0) {
                    arrayProductosPreferenciales = data.arrayProductosReferencia;
                } else {
                    arrayProductosPreferenciales = data.arrayRespuestaGenerica;
                }
            }
        }
    });
}

function configurarProductos(idProducto, producto, accion, idServicio, esEdicionProducto)
{
    //ALMACENAMOS EL ID DEL SERVICIO A CONFIGURAR SEA POR NUEVO O EDICION
    //PARA EVITAR ESTAR ENVIANDO A CADA MÉTODO POR PÁRAMETRO EL VALOR.
    intIdServicio    = idServicio;
    arrayInformacion = [];
    storageTotal     = 0;
    memoriaTotal     = 0;
    procesadorTotal  = 0;
    storageUsado     = 0;
    memoriaUsado     = 0;
    procesadorUsado  = 0;

    $("#button-configurar").attr('disabled', true);
    $("#button-editar").attr('disabled', true);
    $.ajax({
        type   : "POST",
        url    : urlGetCaracteristicas,
        data   : 
        {
          'idPunto'            : idPunto,
          'producto'           : idProducto,
          'verCaracteristicas' : true,
          'esGrupo'            : 'S',
          'esbusiness'         : true
        },
        beforeSend: function() 
        {
            if (accion === 'agregar') {
                Ext.get("winAgregarServicio").mask("Cargando información del Producto");
            } else {
                Ext.MessageBox.show({
                    msg: 'Cargando Formulario para edición',
                    progressText: 'Cargando...',
                    width:300,
                    wait:true,
                    waitConfig: {interval:100}
                });
            }
        },
        success: function(data)
        { 
            if (accion === 'agregar') {
                Ext.get("winAgregarServicio").unmask();
                winAgregarServicio.close();
                htmlContent = "#content-conf-producto";
                $(htmlContent).empty();
            } else {
                Ext.MessageBox.hide();
                htmlContent = "#content-editar-producto";
                $(htmlContent).empty();
            }

            esMultiCaracteristica = data.esMultiCaracteristica;
            gridMaquinasVirtuales = null;
            var htmlFormulario    = data.div; //Formulario de caracteristicas de cada producto.
            jsonFrecuenciaFact    = JSON.parse(frecuencia);

            var cmbFrecuencia     = '<select name="frecuencia-bs" id="frecuencia-bs"><option value="">Seleccione</option>';
            $.each(jsonFrecuenciaFact, function(k,item){
                cmbFrecuencia += "<option value='"+item.valor1+"'>"+item.valor2+"</option>";
            });
            cmbFrecuencia += "</select>";

            var trFrecuencia = "<div>"+cmbFrecuencia+"</div>";
            var trProducto   = "<input id='hd_id_producto' type='hidden' value='"+idProducto+"' />";
            var trProductName= "<input id='hd_nombre_producto' type='hidden' value='"+producto+"' />";

            //Limpiar informacion anterior para reenderizar en limpio el panel

            var divHeader     = "<div style= 'margin-top: 0px; padding-top: 0px;' class='container' id ='divHeader'></div>";
            var divPrincipal  = "<table id='tbl_htmlFormaulario'>"+htmlFormulario+"</table>";
            var divSecundario = "<div  id ='divSec'> </div>";

            var contentHtmlProgressBar = Ext.create('Ext.Component', {
                html:   '<div style = "padding-left: 15px; padding-right: 10px;padding-top:15%;">'+
                            '<table style="width:100%;">'+
                                '<tr>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-storage" class="ui-progressbar" align="center">'+
                                            '<div id="progressbar-storage-label" class="progress-label">Storage</div>'+
                                        '</div>'+    
                                    '</td>' +
                                '</tr><tr><td>&nbsp;</td></tr>' +
                                '<tr>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-memoria" class="ui-progressbar"  align="center">'+
                                            '<div id="progressbar-memoria-label" class="progress-label">Storage</div>'+
                                        '</div>'+    
                                    '</td>' +
                                '</tr><tr><td>&nbsp;</td></tr>' +
                                '<tr>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-procesador" class="ui-progressbar" align="center">'+
                                            '<div id="progressbar-procesador-label" class="progress-label">Storage</div>'+
                                        '</div>'+
                                    '</td>' +

                        '</tr>' +
                        '</div>'
            });

            var componentDivHeader = Ext.create('Ext.Component', {
                html: divHeader
            });
            
            var componentTrProducto = Ext.create('Ext.Component', {
                html: trProducto
            });
            
            var componentTrProductName = Ext.create('Ext.Component', {
                html: trProductName
            });
            
            var componentTrHtmlDivPrinci= Ext.create('Ext.Component', {
                html: divPrincipal
            }); 
            
            var componentTrHtmlDivSecundario = Ext.create('Ext.Component', {
                html: divSecundario
            });

            arrayRecursosHosting   = [];
            var gridRecursos       = null;
            var boolEsPoolCompleto = false;

            if (esMultiCaracteristica)
            {
                storageTotal          = 0;
                memoriaTotal          = 0;
                procesadorTotal       = 0;
                storageUsado          = 0;
                memoriaUsado          = 0;
                procesadorUsado       = 0;
                gridRecursos          = renderizarConfiguracionMultiCaractetistica(data);
                boolEsPoolCompleto    = (data.esPoolCompleto === 'SI' && !data.esLicencia);
                gridMaquinasVirtuales = renderizarGridMaquinasVirtuales(data);
            }

            var formCrearMV = Ext.create('Ext.form.Panel', {
                buttonAlign : 'center',
                id          : 'panelAgregarMv',
                BodyPadding : 10,
                width       : 1000,
                autoScroll  : true,
                height      : 600,
                frame       : true,
                items:
                [
                    {
                        xtype  : 'fieldset',
                        title  :'<b>Información General</b>',
                        layout : {
                            columns :  5,
                            type    : 'table'
                        },
                        items :
                        [ 
                            componentDivHeader,
                            componentTrProducto,
                            componentTrProductName
                        ]
                    },
                    //Configuración de características múltiples
                    {
                        xtype  : 'fieldset',
                        id     : 'fs_gridRecursos',
                        layout : 'fit',
                        collapsible: false,
                        items  : [gridRecursos]
                    },
                    //Configuración de máquinas virtuales
                    {
                        xtype : 'fieldset',
                        id    : 'fs_gridMaquinasVirtuales',
                        collapsible: false,
                        layout: {
                            type: 'table',
                            columns: 3
                        },
                        items:
                            [
                                //Grid máquinas virtuales
                                {
                                    xtype  : 'fieldset',
                                    height :  300,
                                    title  : '<b>Gestión de Máquinas Virtuales</b>',
                                    items  : [gridMaquinasVirtuales]
                                },
                                Ext.create('Ext.Component', {
                                    html: '&nbsp;'
                                }),
                                {
                                    xtype  : 'fieldset',
                                    height :  300,
                                    width  :  400,
                                    title  : '<b>Resumen de Recursos configurados</b>',
                                    items  : [contentHtmlProgressBar]
                                }
                            ]
                    },
                    {
                        xtype  : 'fieldset',
                        layout : 'fit',
                        title  : '<b>Resumen de Precios</b>',
                        collapsible: false,
                        items : [componentTrHtmlDivPrinci]
                    },
                    {
                        xtype  :'fieldset',
                        layout : 'fit',
                        hidden : true,
                        collapsible: false,
                        items : [componentTrHtmlDivSecundario]
                    },
                    {
                        xtype : 'hidden',
                        name  : 'txtInfoRecursos',
                        id    : 'txtInfoRecursos',
                        value : ''
                    }
                ],
                buttons:
                [
                    {
                        text    : '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;'+(accion == 'agregar'?'Agregar':'Editar'),
                        id      : 'botonEditarSolucion',
                        handler : function()
                        {
                            //Si no eres edición de servicio, generamos el secuencial que es
                            //como referencia del servicio que se esta agregando.
                            if (accion == 'agregar')
                            {
                                identificadorProducto = identificadorProducto + 1;
                                secuencial            = identificadorProducto;
                            }
                            else
                            {
                                secuencial = intIdServicio;
                            }

                            if (data.esLicencia)
                            {
                                var maquinasMensaje       = '';
                                var arrayMaquinasInvolved = [];
                                var arrayMaquinasSinSO    = [];
                                arrayMaquinasInvolved     = searchMaquinasInvolved(gridRecursos.getStore().data.items);
                                arrayMaquinasSinSO        = verificarSOMaquina(arrayMaquinasInvolved);
                                deleteLicenciasToArrayInformacion(gridRecursos.getStore().data.items);

                                if (arrayMaquinasSinSO.length > 0)
                                {
                                    $.each(arrayMaquinasSinSO, function(index, value){
                                        maquinasMensaje = maquinasMensaje  + value + ', ';
                                    });

                                    Ext.Msg.alert('Error', 'Las siguientes máquinas: '+ maquinasMensaje + 'están sin licencias');
                                    return false;
                                }

                                addLicenciasToArrayInformacion(gridRecursos.getStore().data.items);
                            }

                            //Validar los datos.
                            var boolFormValido = validarFormulario(winCrearMV,esEdicionProducto,idServicio,data.esCore);

                            if (boolFormValido && accion == 'agregar' && arraySolucion.length > 0)
                            {
                                //Si el producto es core o preferencial, se configura la relación core/preferencial.
                                if (data.esCore === 'S' || esTipoSolucionPreferencial) {
                                    mostrarVentanaRelacionCorePreferencial();
                                } else {
                                    agregarProductoGrid();
                                }
                            }

                            if (boolFormValido)
                            {
                                winCrearMV.close();
                                winCrearMV.destroy();
                            }
                            else
                            {
                                identificadorProducto = identificadorProducto - 1;
                                secuencial            = identificadorProducto;
                            }
                        }
                    },
                    {
                        text    : '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                        id      : 'botonCerrarSolucion',
                        handler : function()
                        {
                            if (accion == 'editar') {
                                Ext.MessageBox.show({
                                    title      : 'Alerta',
                                    msg        : 'Estimado usuario si cierra la ventana, se perderán todo los cambios realizados.',
                                    closable   :  false,
                                    multiline  :  false,
                                    icon       :  Ext.Msg.WARNING,
                                    buttons    :  Ext.Msg.YESNO,
                                    buttonText : {yes: 'Si', no: 'No'},
                                    fn: function (buttonValue) {
                                        if (buttonValue === "yes") {
                                            limpiarArrays();
                                            storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                                            storeDetalle.removeAll();
                                            storeDetalle.load({params: {}});
                                            Ext.getCmp('btnAgregarProducto').setDisabled(false);
                                            winCrearMV.destroy();
                                            winCrearMV.close();
                                        }
                                    }
                                });
                            } else {
                                winCrearMV.destroy();
                                winCrearMV.close();
                            }
                        }
                    }
                ]
            });

            var winCrearMV = Ext.widget('window', {
                id        : 'winCrearMV',
                title     : 'Configuración del Producto',
                layout    : 'fit',
                resizable : true,
                modal     : true,
                //closable  : false,
                width     : '500',
                items     : [formCrearMV]
            });

            Ext.getCmp('fs_gridRecursos').setVisible(esMultiCaracteristica)
            Ext.getCmp('fs_gridMaquinasVirtuales').setVisible(boolEsPoolCompleto);

            winCrearMV.show(); 

            //Inicializador del css
            $('#tbl_htmlFormaulario').find('input[type=text],select').each(function() 
            {
                $(this).addClass("form-control");
                $(this).css("width","50%");
                $(this).css('margin-top', '10px');
                $(this).css('padding-left', '0px');
            });
            
            $('#textarea').css('margin-left', '-5px');

            if (data.esLicencia)
            {
                $("#cantidad").prop('disabled', true);

            }

            $("#textarea").addClass("form-control");
            $("#textarea").css("width","50%");

            var Frecuencia = attachDiv($(trFrecuencia).find('#frecuencia-bs'), 'Frecuencia de Facturación');

            $("#divHeader").append(Frecuencia);
            $('#frecuencia-bs').addClass('form-control');

            Ext.getCmp('botonEditarSolucion').setDisabled(true);
            esEdicionProducto = false;

            if (!esMultiCaracteristica) {
                //Actualizar formulario para escenarios donde no exista un selector de configuraciones de producto   
                actualizaDescripcion(true);
            }

            if (accion === 'editar') {
                idServicioEditado = idServicio;
                ajaxGetInformacionServicioSolucion(idServicio,idProducto,data);
            } else {
                getComisionistas(idProducto,accion);
                initProgressBar();
            }
        }
    });
}

function actualizaTotal () 
{
    var precioNegociacion = $("#precio_venta").val();
    var cantidad          = $("#cantidad").val(); 
    document.getElementById('precio_total').value         =  precioNegociacion * cantidad;
}


function actualizaDescripcion(textInput) 
{
    var cantidad                 = $("#cantidad").val();
    var funcion_precio           = $("#funcion_precio").val();
    var cantidad_caracteristicas = $("#cantidad_caracteristicas").val();
    var caracteristicas          = 'caracteristicas_';
    var caracteristica_nombre    = 'caracteristica_nombre_';
    var producto_caracteristica  = 'producto_caracteristica_';
    var descripcion_producto     = $("#hd_nombre_producto").val();

    var esIntenetLite            = (descripcion_producto === "INTERNET SMALL BUSINESS" ?  true : false);
    
    var precio_unitario             = 0;
    var precio_total                = 0;      
    var caracteristicas_n           = "";
    var caracteristica_nombre_n     = "";
    var producto_caracteristica_n   = ""; 
    var valor_caract                = new Array();
    var nombre_caract               = new Array();
    var prod_caract                 = new Array();
    
    //escenario solo para pool de recursos de cloud IAAS
    
    if(textInput || cantidad_caracteristicas>=1)
    {
        for (var x = 0; x < cantidad_caracteristicas; x++)
        { 
            var muestraGrupoNegocioDescProd = true;
            caracteristicas_n         = caracteristicas + x;            
            caracteristica_nombre_n   = caracteristica_nombre + x;
            producto_caracteristica_n = producto_caracteristica + x;            
            valor_caract[x]           = eval(caracteristicas_n).value;                          
            if(valor_caract[x]==null || valor_caract[x]=='')
            {                
                return false;
            }            
            nombre_caract[x]          = eval(caracteristica_nombre_n).value;

            if(esIntenetLite && nombre_caract[x] == '[Grupo Negocio]')
            {
                muestraGrupoNegocioDescProd = false;
            }

            if(muestraGrupoNegocioDescProd)
            {
                descripcion_producto      += ' '+valor_caract[x];
            }

            prod_caract[x]            = eval(producto_caracteristica_n).value;
        } 

        for (var x = 0; x < nombre_caract.length; x++)
        {
            funcion_precio = replaceAll(funcion_precio, nombre_caract[x], valor_caract[x]);            
        }

        try
        {
            precio_unitario = eval(funcion_precio);
            if(isNaN(precio_unitario))
            {
                throw null;
            }
        }
        catch (err)
        {
            Ext.Msg.alert('Función precio mal definida, No se puede procesar este servicio');
        }
    }    
    
    if(!isNaN(precio_unitario))
    {
        precio_total  = (precio_unitario * cantidad);
    }
    else
    {
        precio_unitario = "";
        
        Ext.Msg.alert('Atención', 'Los valores ingresados no cumplen la función precio, favor verificar');
    }

    if(document.getElementById('descripcion_producto'))
    {
        document.getElementById('descripcion_producto').value = descripcion_producto;
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').value = precio_unitario;
    }

    if(document.getElementById('precio_unitario'))
    {
        document.getElementById('precio_unitario').value = precio_unitario;
    }

    if(document.getElementById('precio_total'))
    {
        document.getElementById('precio_total').value = precio_total;
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').disabled = false;
    }
}

function showResumen()
{
    var html = '';
    
    html+="<br/>El siguiente producto será agregado a la Solución <b style='color:green;'> #"+solucionEditada+"</b><br/><br/>";
    
    html+="<ol id='resumen-list'>";
    
    $.each(arraySolucion, function(k,item){
       html+='<li><i class="fa fa-angle-double-right" aria-hidden="true" style="color:#1c94c4;"></i>&nbsp;'+item.descripcion+'</li>';
    });
    
    html+="</ol><br/>";
    
    $("#content-resumen").html(html);
    
    //Se agrega iconos a los botones del modal panel
    $("#button-confirmar").html('<i class="fa fa-check" aria-hidden="true"></i>&nbsp;Aceptar');
    $("#button-cerrar-resumen").html('<i class="fa fa-times" aria-hidden="true"></i>&nbsp;Cancelar');
    
    $("#content-resumen").dialog("open"); 
}

function mostrarVentanaRelacionCorePreferencial()
{
    $("#content-relacion-subtiposolucion").html("");
    var htmlTbl = "<table id='tbl-relaciones' class='ui-widget ui-widget-content'><thead><tr class='ui-widget-header'><th></th>";
    
    //Si es nuevo preferencial muestro para que se relacione ese nuevo preferencial con el CORE
    if(esTipoSolucionPreferencial)
    {
        //obtener los CORE existentes
        $.each(arrayTipoSolucionesExistentes,function(i,item){
           if(item['esPreferencial'] !== 'S') 
           {
               htmlTbl += "<th>"+item['tipoSolucion']+"</th>";
           }
        });
        htmlTbl += "</tr></thead><tbody>";
        htmlTbl += "<tr><td id='"+identificadorProducto+"'>"+nombreProducto+"</td>";
        
        $.each(arrayTipoSolucionesExistentes,function(i, item)
        {
            if(item['esPreferencial'] !== 'S') 
            {
                htmlTbl += "<td align='center'><input type='checkbox' name='"+item['tipoSolucion']+"'/></td>";
            }            
        });
        
        htmlTbl+= "</tr>";
    }
    else// si es nuevo core presento todos los preferenciales para relacionar con el nuevo core
    {
        htmlTbl += "<th>"+tipoSubSolucion+"</th>";
        htmlTbl += "</tr></thead><tbody>";
        
        $.each(arrayTipoSolucionesExistentes,function(i, item)
        {
            if(item['esPreferencial'] === 'S' && item['esCore'] !== 'S')
            {
                htmlTbl += "<tr><td id='"+item['idServicio']+"'>"+item['descripcion']+"</td>";              
                htmlTbl += "<td align='center'><input type='checkbox' id='"+identificadorProducto+"' name='"+item['tipoSolucion']+"'/></td>";
                htmlTbl+= "</tr>";
            }
        });
    }
    
    htmlTbl += "</tbody></table>";

    $("#content-relacion-subtiposolucion").html(htmlTbl);

    $("#content-relacion-subtiposolucion").dialog("open");
}

function relacionarCoreYPreferencial()
{
    var arrayRefChecked         = [];
    var arrayIdsRefConfigurados = [];        
    
    $("#tbl-relaciones").find("tr").each(function()
        {
            $(this).find("td").each(function()
            {
                var id = $(this).attr("id");

                if(!Ext.isEmpty(id))
                {
                    arrayIdsRefConfigurados.push(id);
                }
            });
        });    
    
    if(esTipoSolucionPreferencial)
    {
        $("#tbl-relaciones").find("input[type=checkbox]").each(function()
        {
            if($(this).is(':checked'))
            {
                var idRef = $(this).parent().parent().children().first().attr("id");
                var tipo  = $(this).attr("name");
                arrayRefChecked.push(idRef);
                
                $.each(arraySolucion, function(i, item)
                {
                    if(parseInt(item['secuencial']) === parseInt(idRef))
                    {
                        arraySolucion[i]['tipoSubSolucionReferencial'] = arraySolucion[i]['tipoSubSolucionReferencial'] + tipo + "|";
                    }
                });
            }
        });
    }
    else//SE AGREGAR CORE
    {                
        $("#tbl-relaciones").find("input[type=checkbox]").each(function()
        {
            if($(this).is(':checked'))
            {
                var id    = $(this).attr("id");//id secuencial de referencia
                var idRef = $(this).parent().parent().children().first().attr("id");//id servicios Preferencial existente
                arrayRefChecked.push(idRef);
                
                $.each(arraySolucion, function(i, item)
                {
                    if(parseInt(item['secuencial']) === parseInt(id))
                    {
                        var referencia = idRef + "@" + tipoSubSolucion;
                        arraySolucion[i]['informacionPorEdicion'] = arraySolucion[i]['informacionPorEdicion'] + referencia + "|";
                    }
                });
            }
        });
    }
    
    var cantServiciosPreferenciales = arrayIdsRefConfigurados.length;
    var contRepetidos               = 0;
    
    $.each(arrayIdsRefConfigurados, function(i, item){
        $.each(arrayRefChecked, function (j, itemChk){
           if(item === itemChk)
           {
               contRepetidos++;
               return false;
           }
        });
    });
    
    if(esTipoSolucionPreferencial && cantServiciosPreferenciales !== contRepetidos)
    {
        Ext.Msg.alert('Alerta', "Cada producto Preferencial debe al menos estar configurado en una Sub-Solución");
        return false;
    }
    else if(contRepetidos == 0)
    {
        Ext.Msg.alert('Alerta', "Cada producto Preferencial debe al menos estar configurado en una Sub-Solución");
        return false;
    }
    else
    {
        agregarProductoGrid();
    }            
}

function agregarProductoGrid()
{
    var cantidadRegistros = gridDetalle.getStore().getCount();
    
    //Agregar servicio al grid en estado "PorAgregar"
    $.each(arraySolucion, function(i, item)
    {
        if(parseInt(item['secuencial']) === parseInt(identificadorProducto))
        {
            var recordParamDet = Ext.create('detalleProductosModel', {
                idServicio              : '0',
                idProducto              : item['codigo'],
                descripcion             : item['descripcion'],
                estado                  : 'PorAgregar',
                precio                  : item['precio_total'],
                tipoSolucion            : item['tipoSubSolucion'],
                esCore                  : item['esCore'],
                segmento                : '',
                contieneCarcateristica  : '',
                esPreferencial          : item['tipoProducto']==='N'?'N':'S',
                secuencial              : item['secuencial']
            });

            storeDetalle.insert(cantidadRegistros++, recordParamDet);
            
            validarGridRegistrosNuevos();
        }
    });
    
    $("#content-conf-producto").dialog("close");
    $("#content-relacion-subtiposolucion").dialog("close");
}

//FUNCIÓN EN CARGADA DE CREAR SERVICIOS A UNA SOLUCIÓN EXISTENTE.
function ajaxEditarSolucion()
{
    var arrayMaquinasVirtualesNuevas = [];
    arrayMaquinasVirtualesNuevas = arrayMaquinasVirtuales.filter(mvs => mvs.esNuevo);

    $("#content-resumen").dialog("close");
    $.ajax({
        type   : "POST",
        url    : urlGuardarSolucion,
        timeout: 600000,
        data   :
        {
          'data'              :  Ext.JSON.encode(arraySolucion),
          'tipoSolucion'      : 'S',
          'nombreSolucion'    :  nombreSolucionEditada,
          'idPunto'           :  idPunto,
          'tipoOrden'         : 'N',
          'numeroSolucion'    :  solucionEditada,
          'maquinasVirtuales' :  Ext.JSON.encode(arrayMaquinasVirtualesNuevas)
        },
        beforeSend: function()
        {
            Ext.MessageBox.show({
                msg   : 'Agregando Productos a la Solución',
                width : 300,
                wait  : true,
                waitConfig: {interval:200}
            });
        },
        success: function(data)
        {
            if(data.status === 'OK')
            {
                Ext.Msg.alert('Mensaje', "Solución actualizada correctamente", function(btn) {

                    if (btn == 'ok')
                    {
                        gridSoluciones.getStore().removeAll();

                        $.each(data.arrayInfo,function(i , item)
                        {
                            var recordParamDet = Ext.create('solucionesModel', {
                                numeroSolucion: item.numeroSolucion,
                                nombreSolucion: item.nombreSolucion,
                                totalSolucion : item.totalSolucion
                            });

                            storeSoluciones.insert(i, recordParamDet);
                        });

                        limpiarArrays();
                        ajaxConsultarSoluciones(solucionEditada);
                        storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                        storeDetalle.load({params: {}});
                        Ext.getCmp('btnEditarSolucion').setDisabled(true);
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error', data.mensaje);
            }
        }
    });
}

function eliminarProducto(raw)
{
    var nombreProducto = raw.data.descripcion;
    var idServicio     = raw.data.idServicio;
    var secuencial     = raw.data.secuencial;

    Ext.Msg.alert('Mensaje', "Seguro que desea Eliminar el Producto <b>"+nombreProducto+"</b> de la Solución ?", function(btn) {
        if (btn == 'ok')
        {
            //Eliminar servicio del grid 
            if(idServicio === '0')
            {
                storeDetalle.remove(raw);
                storeDetalle.sync();

                arraySolucion = arraySolucion.filter(function(elem){
                    return elem.secuencial !== secuencial;
                });

                validarGridRegistrosNuevos();
            }
            else//realizar eliminacion de servicio creado
            {
                ajaxEliminarServicio(raw);
            }
        }
    });
}

function ajaxEliminarServicio(raw)
{
    //Eliminar servicio del grid ( ya creado dentro de la solucion )
    $.ajax({
        type    : 'POST',
        url     :  urlEliminaServicioSolucion,
        timeout :  900000,
        data    :
        {
          'idServicio'  : raw.data.idServicio,
          'idSolicitud' : !Ext.isEmpty(raw.data.solicitud)?raw.data.solicitud:'',
          'idPunto'     : idPunto
        },
        beforeSend: function() 
        {
            Ext.MessageBox.show({
                msg   : 'Eliminado Servicio de la Solución',
                width : 300,
                wait  : true,
                waitConfig: {interval:200}
            });
        },
        success: function(data)
        {
            if (data.status === 'OK')
            {
                var html = '';

                if (data.arrayServiciosEliminados.length > 0)
                {
                    html += '<br><br>Los siguientes Servicios fueron eliminados por acción realizada en '+
                            '<b>'+raw.data.descripcion+'</b>';
                    html += '<br><ul>';
                    $.each(data.arrayServiciosEliminados, function(i, item)
                    {
                        html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>';
                    });
                    html += '</ul>';
                }
                if(data.arrayServiciosLigados.length > 0)
                {
                    html += '<br><br>Por la acción realizada Los siguientes servicios fueron desenlazados pero no eliminados:';
                    html += '<br><ul>';
                    $.each(data.arrayServiciosLigados, function(i, item)
                    {
                        html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>';
                    });
                    html += '</ul>';
                }

                Ext.Msg.alert('Mensaje', "Servicio Eliminado correctamente"+html, function(btn)
                {
                    if (btn == 'ok') 
                    {
                        gridSoluciones.getStore().removeAll();

                        $.each(data.arrayInfo,function(i , item)
                        {
                            var recordParamDet = Ext.create('solucionesModel', {
                                numeroSolucion : item.numeroSolucion,
                                nombreSolucion : item.nombreSolucion,
                                totalSolucion  : item.totalSolucion
                            });

                            storeSoluciones.insert(i, recordParamDet);
                        });

                        limpiarArrays();
                        storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                        storeDetalle.removeAll();
                        storeDetalle.load({params: {}});
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error', data.mensaje);
            }
        }
    });
}

function editarNombreSolucion(data)
{
    //Plugin para transformar los textos a mayusculas.
    Ext.define('App.plugin.UpperTextField', {
        extend : 'Ext.AbstractPlugin',
        alias  : 'plugin.uppertextfield',
        init   : function (cmp) {
            Ext.apply(cmp, {
                fieldStyle: (cmp.fieldStyle ? cmp.fieldStyle + ';' : '') + 'text-transform:uppercase',
                getValue: function() {
                    var val = cmp.__proto__.getValue.apply(cmp, arguments);
                    return val && val.toUpperCase ? val.toUpperCase() : val;
                }
            });
        }
    });

    //Panel de edición de nombre de solución.
    var formPanelEditar = Ext.create('Ext.form.Panel', {
        bodyPadding   : 12,
        frame         : false,
        fieldDefaults : {
            labelWidth :  80,
            labelAlign : 'left'
        },
        items:
        [
            {
                xtype : 'fieldset',
                items :
                [
                    {
                        xtype      : 'textfield',
                        id         : 'txtNombreSolucion',
                        name       : 'txtNombreSolucion',
                        fieldLabel : 'Actual',
                        value      :  data.nombreSolucion,
                        readOnly   :  true,
                        width      :  420
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'txtEditarNombreSolucion',
                        name       : 'txtEditarNombreSolucion',
                        fieldLabel : 'Nuevo',
                        width      :  420,
                        plugins    : ['uppertextfield']
                    }
                ]
            }
        ]
    });

    //Contenedor de botones.
    var buttons = Ext.create('Ext.Container', {
        items: [
            {
                xtype : 'button',
                width :  80,
                style : 'margin-right: 8px;',
                text  : '<label style="color:green;"><i class="fa fa-save" aria-hidden="true"></i></label>'+
                        '&nbsp;<b>Guardar</b>',
                handler: function()
                {
                    ajaxEditarNombreSolucion(data.numeroSolucion);
                }
            },
            {
                xtype : 'button',
                width :  80,
                style : 'margin-right: 8px;',
                text  : '<label style="color:red;"><i class="fa fa-close" aria-hidden="true"></i></label>'+
                        '&nbsp;<b>Cerrar</b>',
                handler: function() {
                    winEditarNombreSolucion.close();
                    winEditarNombreSolucion.destroy();
                }
            }
        ]
    });

    //Win para editar nombre de solución.
    winEditarNombreSolucion = Ext.widget('window', {
        id          : 'winEditarNombreSolucion',
        title       : 'Actualizar Nombre de Solución',
        modal       :  true,
        closable    :  false,
        resizable   :  false,
        layout      : 'fit',
        width       : 'auto',
        items       : [formPanelEditar],
        buttonAlign : 'center',
        buttons     : [buttons]
    }).show();
}

function ajaxEditarNombreSolucion(numeroSolucion)
{
    var actualNombreSolucion = Ext.getCmp('txtNombreSolucion').getValue();
    var nuevoNombreSolucion  = Ext.getCmp('txtEditarNombreSolucion').getValue();

    if (Ext.isEmpty(nuevoNombreSolucion))
    {
        Ext.Msg.alert('Alerta', 'Debe ingresar el nombre de la Solución.');
        return;
    }

    $.ajax({
        type    : "POST",
        url     :  urlEditarNombreSolucion,
        timeout :  600000,
        data    :
        {
          'idPunto'              : idPunto,
          'numeroSolucion'       : numeroSolucion,
          'actualNombreSolucion' : actualNombreSolucion,
          'nuevoNombreSolucion'  : nuevoNombreSolucion
        },
        beforeSend: function()
        {
            Ext.MessageBox.show({
                msg        : 'Actualizando Nombre de Solución',
                closable   :  false,
                width      :  300,
                wait       :  true,
                waitConfig : {interval:200}
             });
        },
        success: function(data)
        {
            if (data.status !== 'OK') {
                Ext.Msg.alert('Error', data.mensaje);
                return;
            }

            winEditarNombreSolucion.close();
            winEditarNombreSolucion.destroy();

            Ext.MessageBox.show({
                title      : 'Mensaje',
                msg        : 'Nombre de solución actualizado correctamente..',
                closable   :  false,
                multiline  :  false,
                icon       :  Ext.Msg.INFO,
                buttons    :  Ext.Msg.YES,
                buttonText : {yes : 'Cerrar'},
                fn : function (buttonValue)
                {
                    if (buttonValue === "yes") {

                        gridSoluciones.getStore().removeAll();

                        $.each(data.arrayInfo,function(i,item)
                        {
                            var recordParamDet = Ext.create('solucionesModel', {
                                numeroSolucion : item.numeroSolucion,
                                nombreSolucion : item.nombreSolucion,
                                totalSolucion  : item.totalSolucion
                            });

                            storeSoluciones.insert(i, recordParamDet);
                        });
                    }
                }
            });
        }
    });
}

function ajaxGetInformacionServicioSolucion(idServicio,idProducto,arrayData)
{
    $.ajax({
        type  : "POST",
        url   : urlGetInformacionServicio,
        async : false,
        data  :
        {
          'idServicio' : idServicio
        },
        beforeSend: function() 
        {
            Ext.MessageBox.show({
                msg   : 'Cargando información del Producto',
                progressText: 'Cargando...',
                width : 300,
                wait  : true,
                waitConfig: {interval:200}
            });
        },
        success: function(data)
        {
            Ext.MessageBox.hide();
            arrayInformacionServicio = data;

            if (data.arrayInfoBasica.length > 0)
            {
                var arrayInfo = data.arrayInfoBasica[0];
                
                tipoProductoConfigurado = arrayInfo.esPreferencial;

                //frecuencias
                $('#frecuencia-bs').val(arrayInfo.frecuencia);
                
                $("#cantidad").val(arrayInfo.cantidad);
                
                $("#descripcion_producto").val(arrayInfo.descripcion);
                
                //Valores
                $("#precio_unitario").val(arrayInfo.precioFormula);
                
                document.getElementById('precio_venta').disabled = false;
                
                if(!Ext.isEmpty(arrayInfo.precioInstalacion))
                {
                    $("#precio_instalacion").val(arrayInfo.precioInstalacion);
                }
                
                //Valores
                if(!Ext.isEmpty(arrayInfo.idSolicitud))
                {
                    var precio = arrayInfo.precioVenta - parseFloat(arrayInfo.descuento);
                    $("#precio_venta").val(precio);
                    $("#precio_total").val(parseFloat(precio)*parseInt(arrayInfo.cantidad));
                }
                else
                {
                    $("#precio_venta").val(arrayInfo.precioVenta);
                    $("#precio_total").val(parseFloat(arrayInfo.precioVenta)*parseInt(arrayInfo.cantidad));
                }

                if(!Ext.isEmpty(arrayInfo.ultimaMilla))
                {
                    $("#ultimaMillaIdProd").val(arrayInfo.ultimaMilla);
                    
                    if($("#tipoSolucion").length !== 0)
                    {
                        var text = $("#ultimaMillaIdProd option:selected" ).text();
                        
                        if(text === 'UTP')
                        {
                            $("#tipoSolucion").val('HOUSING');
                            //$("#tipoSolucion").selectmenu("refresh");
                        }
                        else
                        {
                            $("#tipoSolucion").val('HOSTING');
                            //$("#tipoSolucion").selectmenu("refresh");
                        }
                    }
                }

                //Cargar las caracteristicas
                renderizarInformacionServicio(data.arrayCaracteristicas,
                                              arrayInfo.esMultipleCaracteristica,
                                              arrayData);

                jsonInformacion = arrayMaquinasVirtuales.filter(maquina => maquina.secuencial == idServicioEditado);

                if (jsonInformacion[0]) {
                    arrayInformacion = jsonInformacion[0].maquinasVirtuales;
                    llenarGridMaquinasVirtuales(jsonInformacion[0].maquinasVirtuales,
                                                storeMaquinasVirtualesCaracteristicas,
                                                rowMaquinasVirtuales);
                } else {
                    //En caso que no se tenga maquinas virtuales, inicializamos el initProgressBar
                    initProgressBar(true);
                }

                //Cargar comisionistas despues de cargar los datos para setear la informacion guardada de vendedores
                getComisionistas(idProducto,'editar');
            }
            else
            {
                Ext.Msg.alert('Atención', 'No se pudo obtener la Información ligada al Servicio, notificar a Sistemas');
            }
        }
    });
}

//FUNCIÓN QUE PERMITE INICIALIZAR LA PANTALLA DE EDICIÓN DE LA SOLUCIÓN.
function editarServicioSolucion(raw)
{
    var nombreProducto = raw.data.descripcion;
    var idServicio     = raw.data.idServicio;
    var idProducto     = raw.data.idProducto;
    accion             = 'editar';
    esEdicionProducto  = true;
    configurarProductos(idProducto, nombreProducto, accion, idServicio, esEdicionProducto);
}

//FUNCIÓN QUE PERMITE LA ACTUALIZACIÓN DE UN SERVICIO DE UNA SOLUCIÓN.
function ajaxEditarServicioSolucion()
{
    //Obtenemos solo las maquinas virtuales nuevas, por motivos
    //que las existentes no pueden ser editadas.
    var arrayMaquinasVirtualesNuevas = arrayInformacion.filter(maquina => maquina.esNuevo);

    $.ajax({
        type : 'post',
        url  :  urlEditarServicioSolucion,
        data :
        {
          'idServicio'               : idServicioEditado,
          'numeroSolucion'           : solucionEditada,
          'arrayMaquinasVirtuales'   : Ext.JSON.encode(arrayMaquinasVirtualesNuevas),
          'data'                     : Ext.JSON.encode(arraySolucion),
          'arrayLicenciasEditadas'   : Ext.JSON.encode(arrayLicenciasEditadas),
          'arrayLicenciasEliminadas' : Ext.JSON.encode(arrayLicenciasEliminadas)
        },
        beforeSend: function()
        {
            Ext.Msg.show({
                msg   : 'Editando Información del Servicio..',
                width :  300,
                wait  :  true,
                waitConfig : {interval:200}
            });
        },
        success: function(data)
        {
            Ext.Msg.hide();
            $("#content-editar-producto").dialog("close");
            var status = data.status === 'OK';

            Ext.Msg.show({
                title      :  status ? 'Mensaje' : 'Error',
                msg        :  data.mensaje,
                closable   :  false,
                multiline  :  false,
                icon       :  status ? Ext.Msg.INFO : Ext.Msg.ERROR,
                buttons    :  Ext.Msg.YES,
                buttonText : {yes : 'Cerrar'},
                fn : function (buttonValue) {
                    if (buttonValue === "yes") {
                        limpiarArrays();
                        limpiarPanel();
                        storeDetalle.proxy.extraParams = {numeroSolucion: solucionEditada};
                        storeDetalle.removeAll();
                        storeDetalle.load({params: {}});
                    }
                }
            });
        }
    });
}

function ajaxConsultarMaquinasVirtuales()
{   
    //Buscar Maquinas Virtuales
    var objJson = []; 
    Ext.Ajax.request({
            url: urlGetDetallesPorSolucion,
            method: 'get',
            timeout:600000,
            params: 
            { 
                idServicio      : idServicioEditado,
                tipoInformacion : 'MAQUINAS-VIRTUALES'
            },
            success: function(response)
            {
                objJson = Ext.JSON.decode(response.responseText);
                arrayMaquinasVirtuales = [];
                if(objJson.length > 0)
                    {
                        var jsonMaquinasVirtuales = {};
                        jsonMaquinasVirtuales['secuencial']        = idServicioEditado;
                        jsonMaquinasVirtuales['maquinasVirtuales'] = objJson;  
                        arrayMaquinasVirtuales.push(jsonMaquinasVirtuales);
                    }
                return objJson; 
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        });
}