
var winVerMapa;
var valorAsignacion = "empleado";
var tipoRack           = 'S';//Tipo Standard de Rack ( Nodo )
var marca              = '';//variable que almacenará la marca seleccionada
var fechaSerieNueva = '';
var bandEdit           = 'NA';
var intClase           = '';
var intContenedor      = '';

var storePadre = new Ext.data.Store({
    total: 'total',
    proxy: {
        type: 'ajax',
        url : '../../../administracion/tecnico/admi_tipo_elemento/getPadresElementosNodos',
        reader: {
            type: 'json',
            root: 'encontrados',
            totalProperty: 'total'
        }
    },
    fields:[
        {name: 'nombrePadreElemento', mapping: 'nombrePadreElemento'},
        {name: 'idPadreElemento', mapping: 'idPadreElemento'}
    ]
});

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

Ext.onReady(function() 
{
    Ext.tip.QuickTipManager.init();
    Ext.util.Format.thousandSeparator = '.';
    Ext.util.Format.decimalSeparator  = ',';

    //Medidores
    storeTipoMedidor = new Ext.data.Store({ 
        total: 'total',  
        model: 'TipoMedidor',
        proxy: {
            type: 'ajax',
            url : url_tipoMedidor,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'             
            }
        },
        fields:
              [
                {name:'nombreTipoMedidor', mapping:'nombreTipoMedidor'},
                {name:'idTipoMedidor', mapping:'idTipoMedidor'}
              ]
    });
    
    var storeMedidorElectrico = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : url_medidorElectrico,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name:'valor1', mapping:'valor1'},
                {name:'valor1', mapping:'valor1'}
            ],
        autoLoad: false
    });
    
    storeClaseMedidor = new Ext.data.Store({ 
        total: 'total',        
        proxy: {
            type: 'ajax',
            url : url_claseMedidor,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'             
            }
        },
        fields:
              [
                {name:'nombreClaseMedidor', mapping:'nombreClaseMedidor'},
                {name:'idClaseMedidor', mapping:'idClaseMedidor'}
              ]
    });

    var storeMotivos = new Ext.data.Store({ 
        total: 'total',        
        proxy: {
            type: 'ajax',
            url : url_motivos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idMotivo', mapping:'idMotivo'},
                {name:'nombreMotivo', mapping:'nombreMotivo'}
              ]
    });
    
    var storeClaseNodo = new Ext.data.Store({ 
        total: 'total',        
        proxy: {
            type: 'ajax',
            url : url_claseNodos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idDetalle', mapping:'idDetalle'},
                {name:'nombreDetalle', mapping:'nombreDetalle'}
              ]
    });
    
    var storeProvincias = new Ext.data.Store({ 
        total: 'total',        
        proxy: {
            type: 'ajax',
            url : url_provincias,
            reader: {
                type: 'json', 
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id_provincia', mapping:'id_provincia'},
                {name:'nombre_provincia', mapping:'nombre_provincia'}
              ]
    });

    var storeCantones = new Ext.data.Store({ 
        total: 'total',        
        proxy: {
            type: 'ajax',
            url : url_cantones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombre_canton', mapping:'nombre_canton'},
                {name:'id_canton', mapping:'id_canton'}
              ]         
    });        
    
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : url_gridNodos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                provincia: '',
                canton:'',
                motivo:'',
                clase:'',
                estadoSolicitud:'Todos',
                estadoNodo: 'Todos'                
            }
        },
        fields:
                  [
                    {name:'idElemento', mapping:'idElemento'},
                    {name:'nombreElemento', mapping:'nombreElemento'},                    
                    {name:'direccion', mapping:'direccion'},
                    {name:'nombreCanton', mapping:'nombreCanton'},
                    {name:'nombreProvincia', mapping:'nombreProvincia'},
                    {name:'clase', mapping:'clase'},
                    {name:'tipoMedio', mapping:'tipoMedio'},                    
                    {name:'estado', mapping:'estado'},
                    {name:'estadoSolicitud', mapping:'estadoSolicitud'},
                    {name:'solicitante', mapping:'solicitante'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'estado', mapping:'estado'},
                    {name:'numeroMedidor', mapping:'numeroMedidor'},
                    {name:'idMedidor', mapping:'idMedidor'},
                    {name:'idClaseMedidor', mapping:'idClaseMedidor'},
                    {name:'idTipoMedidor', mapping:'idTipoMedidor'},
                    {name:'nombreClaseMedidor', mapping:'nombreClaseMedidor'},
                    {name:'nombreTipoMedidor', mapping:'nombreTipoMedidor'},
                    {name:'valor1', mapping:'valor1'},
                    {name:'valor', mapping:'valor'},
                    {name:'arrayMantenimientoNodo' , mapping:'arrayMantenimientoNodo'},
                    {name:'solicitudCambioElemento', mapping:'solicitudCambioElemento'}
                  ],
        autoLoad: true,
        listeners: {
            load: function(sender, node, records) 
            {
                storeTipoMedidor.load();
                storeMedidorElectrico.load();
                storeClaseMedidor.load();
                storeMotivos.load();
                storeProvincias.load();
                storeClaseNodo.load();
            }
        }
    });       
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
    
    grid = Ext.create('Ext.grid.Panel', {
        height: 600,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        viewConfig: {enableTextSelection: true},
        iconCls: 'icon-grid',
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [                    
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_exportar',
                        text: 'Exportar',
                        itemId: 'exportar',
                        scope: this,
                        handler: function() {
                            exportar();
                        }
                    },
                    {
                        iconCls: 'icon_subir',
                        text: 'Subir Elementos',
                        itemId: 'subir',
                        scope: this,
                        handler: function() {
                            subir();
                        }
                    }
                ]}
        ],
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'elemento',
                header: 'Nodo',
                xtype: 'templatecolumn',
                width: 300,
                tpl: '<span class="bold">Nombre : </span>\n\
                        <span class="box-detalle">{nombreElemento}</span></br>\n\\n\
                        <span class="bold">Valor : </span><span>$ {valor}</span></br></br>\n\
                        <span class="bold">Direccion : </span><span>{direccion}</span></br>\n\
                        <span class="bold">Provincia : </span><span>{nombreProvincia}</span></br>\n\
                        <span class="bold">Canton : </span><span>{nombreCanton}</span></br>\n\
                        </br>\n\\n\
                        <span class="bold">Estado Nodo : </span><span>{estado}</span></br>\n\\n\
                        </br>\n\\n\
                        <span class="bold">Solicitante : </span><span>{solicitante}</span></br></br>\n\
                        <tpl>\n\
                        </tpl>'
            },
            {
                header: 'Clase',
                dataIndex: 'clase',
                width: 100,
                sortable: true
            },
            {
                header: 'Tipo',
                xtype: 'templatecolumn',
                width: 100,
                 tpl: '{tipoMedio}\n\<tpl></tpl>\n'                                                
            },
            {
                header: 'Estado Nodo',
                dataIndex: 'estado',
                width: 100,
                sortable: true
            },
            {
                header: 'Estado Solicitud',
                dataIndex: 'estadoSolicitud',
                width: 120,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: '35%',
                items: [
                    //show nodo
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = $("#ROLE_154-6"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if(!boolPermiso)
                            {
                                return '';
                            }
                            else
                            {
                                return 'button-grid-show';
                            }
                        },
                        tooltip: 'Ver Nodo',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            Ext.MessageBox.wait("Cargando, por favor espere...");
                            var rec = store.getAt(rowIndex);
                            window.location = "" + rec.get('idElemento') + "/showNodo";
                        }
                    },
                    //editar nodo
                    {
                        getClass: function(v, meta, rec) 
                        {                            
                            var permiso = $("#ROLE_154-2199"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                                        
                            if (rec.data.estado === "Eliminado" || !boolPermiso || rec.get('estadoSolicitud') === "RechazadaTecnico" ||
                                rec.get('estadoSolicitud') === "Eliminada" || rec.get('estadoSolicitud') === "RechazadaLegal")
                            {                               
                                return '';
                            }
                            else
                            {                                
                                return 'button-grid-edit';
                            }                           
                        },
                        tooltip: 'Editar Nodo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            Ext.MessageBox.wait("Cargando, por favor espere...");
                            
                            var rec = store.getAt(rowIndex);
                            if (rec.get('estado') !== "Eliminado")
                            {
                                window.location = "" + rec.get('idElemento') + "/editNodo";
                            }
                        }
                    },
                    /*Agregar periodo de mantenimiento*/
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = $("#ROLE_154-7097");
                            let boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            let boolRadio   = rec.get('tipoMedio').toUpperCase().indexOf('RADIO');
                            let boolPeriodoMantenimiento = rec.get('arrayMantenimientoNodo').boolPoseePeriodo;
                            let boolEstado = rec.get('estado') === 'Activo' && rec.get('estadoSolicitud') === "Finalizada";

                            if(boolPermiso && (boolRadio !== -1 && !boolPeriodoMantenimiento && boolEstado))
                            {
                                return 'button-grid-add-maintenance-period';
                            }
                            else
                            {
                                return '';
                            }
                        },
                        tooltip: 'Mantenimiento Preventivo Torre',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            let rec = store.getAt(rowIndex).data;
                            ingresarPeriodoMantenimiento(rec);
                        }
                    },
                    //eliminar nodo
                    {
                        getClass: function(v, meta, rec) 
                        {                            
                            var permiso = $("#ROLE_154-2198"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                                                        
                            
                            if (rec.data.estado === "Eliminado" || !boolPermiso )
                            {                               
                                return '';
                            }
                            else
                            {                                
                                return 'button-grid-delete';
                            }                           
                        },
                        tooltip: 'Eliminar Nodo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            
                            Ext.Msg.confirm('Alerta','¿Esta Seguro de Eliminar el Elemento?', function(btn){
							
                            if(btn=='yes' && rec.get('estado') !== "Eliminado")
                            {
                                var conn = new Ext.data.Connection({
                                    listeners: {
                                        'beforerequest': {
                                            fn: function(con, opt) {
                                                Ext.get(document.body).mask('Eliminando Nodo...');
                                            },
                                            scope: this
                                        },
                                        'requestcomplete': {
                                            fn: function(con, res, opt) {
                                                Ext.get(document.body).unmask();
                                            },
                                            scope: this
                                        },
                                        'requestexception': {
                                            fn: function(con, res, opt) {
                                                Ext.get(document.body).unmask();
                                            },
                                            scope: this
                                        }
                                    }
                                });     
                                
                                conn.request({
                                    method: 'POST',
                                    params: {
                                        id: rec.get('idElemento')
                                    },
                                    url: url_deleteNodo,
                                    success: function(response)
                                    {
                                        var json = Ext.JSON.decode(response.responseText);

                                        Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                            if (btn == 'ok') {                                                
                                                store.load();
                                            }
                                        });
                                    },
                                    failure: function(rec, op) {
                                        var json = Ext.JSON.decode(op.response.responseText);
                                        Ext.Msg.alert('Alerta ', json.mensaje);
                                    }
                                });                                
                            }
                            });
                        }
                    }, 
                    //agregar elementos
                    {
                        getClass: function(v, meta, rec)
                        {
                            var permiso = $("#ROLE_154-6"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if(rec.data.estado === "Eliminado" || !boolPermiso)
                            {
                                return '';
                            }
                            else
                            {
                                return 'button-grid-plus';
                            }
                        },
                        tooltip: 'Agregar Elementos',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            ingresoElemento(grid.getStore().getAt(rowIndex).data);
                        }
                    },
                    //Cambiar informacion de Nombre de Nodo
                    {
                        getClass: function(v, meta, rec) 
                        {                            
                            var permiso = $("#ROLE_154-2199"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if (rec.data.estadoSolicitud === "Pendiente" || !boolPermiso ||  
                                (rec.data.estado === "Eliminado" || !boolPermiso || rec.get('estadoSolicitud') === "Finalizada" ) ||
                                  rec.get('estadoSolicitud') === "RechazadaTecnico" || rec.get('estadoSolicitud') === "Eliminada" ||
                                  rec.get('estadoSolicitud') === "RechazadaLegal")
                            {                               
                                return '';
                            }
                            else
                            {                                
                                return 'button-grid-editarDireccion';
                            }                           
                        },
                        tooltip: 'Editar Nombre Nodo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            if (rec.data.estadoSolicitud !== "Pendiente")                                
                            {
                                editarNombreNodo(rec);                                
                            }
                        }
                    },
                    //Cambiar informacion de Medidor
                    {
                        getClass: function(v, meta, rec) 
                        {                    
                            var permiso = $("#ROLE_154-2199"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if (rec.data.estadoSolicitud === "Pendiente" || rec.data.estadoSolicitud === "Anulada" || 
                                rec.data.estadoSolicitud ==='N/A' || !boolPermiso ||
                                rec.data.estado === "Eliminado" || !boolPermiso || rec.get('estadoSolicitud') === "Finalizada" ||
                                rec.get('estadoSolicitud') === "RechazadaTecnico" || rec.get('estadoSolicitud') === "RechazadaLegal" ||
                                rec.get('estadoSolicitud') === "Eliminada" || rec.get('estadoSolicitud') === "RechazadaLegal" )
                            {                               
                                return '';
                            }
                            else
                            {                                
                                return 'button-grid-verVelocidadReal';
                            }                           
                        },
                        tooltip: 'Editar Informacion Medidor',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            editarMedidor(rec);
                        }
                    },
                    //Ver Imagen Nodo button-grid-verImagenNodo
                    {
                        getClass: function(v, meta, rec) 
                        {                          
                            if(rec.get('estadoSolicitud') === "Eliminada" || rec.get('estadoSolicitud') === 'Anulada' ||
                               rec.get('estadoSolicitud') === "RechazadaLegal" || rec.get('estadoSolicitud') === 'RechazadaTecnico')
                            {
                                return '';
                            }
                            else
                            {
                                return 'button-grid-verImagenNodo';
                            }                               
                        },
                        tooltip: 'Editar Imagenes de Instalacion',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            
                            showImages(rec.data);
                            //Metodo que muestra las imagenes relacionadas
                        }
                    },
                    //Ver Mapa
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-Gmaps';
                        },
                        tooltip: 'Ver Mapa',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            if (rec.get("latitud") !== 0 && rec.get("longitud") !== 0)
                            {
                                showVerMapa(rec);
                            }
                            else
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Las coordenadas son incorrectas',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    },
                    //Ver Elementos Contenidos por el NODO
                    {
                        getClass: function(v, meta, rec)
                        {
                            if(rec.get('estadoSolicitud') !== "Finalizada" && rec.get('estadoSolicitud') !== "FirmadoContrato" &&
                               rec.get('estadoSolicitud') !== "AutorizadaLegal" && rec.get('estadoSolicitud') !== "AutorizadaTecnico")
                            {
                                return '';
                            }
                            else
                            {
                                return 'button-grid-inventario';
                            }
                        },
                        tooltip: 'Elementos Contenidos y Generación de Solicitud',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            verElementosContenidos(rec);
                        }
                    },
                    //Cambio de Elemento.
                    {
                        getClass: function(v, meta, rec)
                        {
                            var icono = 'button-grid-cambioCpe';
                            if (rec.data.estado === "Eliminado" || !rec.data.solicitudCambioElemento || !generarCambioElementoNodo) {
                                icono = 'button-grid-invisible';
                            }
                            return icono;
                        },
                        tooltip: 'Cambio de Elemento',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var data = store.getAt(rowIndex);
                            cambioElementoNodo(data);
                        }
                    },
                    //Migrar Nodo
                    {
                        getClass: function(v, meta, rec)
                        {
                            var icono = 'button-grid-migrarPseudoPe';
                            if (rec.data.estado === "Eliminado" || !migrarElementoNodo) {
                                icono = 'button-grid-invisible';
                            }
                            return icono;
                        },
                        tooltip: 'Migrar Nodo',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var data = store.getAt(rowIndex);
                            migrarNodo(data);
                        }
                    },
                    //Asignar Tarea a NODO
                    {
                        getClass: function(v, meta, rec) 
                        {                               
                            if(rec.get('estadoSolicitud') === "Pendiente" || rec.get('estadoSolicitud') === "RechazadaTecnico" || 
                               rec.get('estadoSolicitud') === "RechazadaLegal" || rec.get('estadoSolicitud') === "Eliminada" ||
                               rec.get('estadoSolicitud') === "Anulada")
                            {                               
                                return '';
                            }
                            else
                            {                                
                                return 'button-grid-agregarTareaNodo';
                            }                                                            
                        },
                        tooltip: 'Agregar Tarea',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var conn = new Ext.data.Connection({
                                    listeners: {
                                        'beforerequest': {
                                            fn: function(con, opt) {
                                                Ext.get(document.body).mask('Cargando Informacion de fecha y hora...');
                                            },
                                            scope: this
                                        },
                                        'requestcomplete': {
                                            fn: function(con, res, opt) {
                                                Ext.get(document.body).unmask();
                                            },
                                            scope: this
                                        },
                                        'requestexception': {                                                                                
                                            fn: function(con, res, opt) {
                                                Ext.get(document.body).unmask();
                                            },
                                            scope: this
                                        }
                                    }
                                });

                            conn.request({
                                method: 'POST',
                                url: url_obtenerFechaServer,
                                success: function(response)
                                {
                                    var json = Ext.JSON.decode(response.responseText);

                                    if (json.success)
                                    {
                                        var fechaFinArray = json.fechaActual.split("-");                                        
                                        var fechaActual = fechaFinArray[0] + "-" + fechaFinArray[1] + "-" + fechaFinArray[2];

                                        var rec = store.getAt(rowIndex); 
                                        agregarTareaNodo(rec,fechaActual,json.horaActual);                                        
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta ', json.error);
                                    }
                                }
                            });
                           
                        }
                    },
                    //Ver Tareas relacionadas al NODO
                    {
                        getClass: function(v, meta, rec) 
                        {                                                                
                            if(rec.get('estadoSolicitud') === "Pendiente" || rec.get('estadoSolicitud') === "RechazadaTecnico" || 
                               rec.get('estadoSolicitud') === "RechazadaLegal" || rec.get('estadoSolicitud') === "Eliminada" ||
                               rec.get('estadoSolicitud') === "Anulada") 
                            {
                                return '';
                            }
                            else
                            {
                                return 'button-grid-verTareaNodo';
                            }                               
                        },
                        tooltip: 'Ver Tareas',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            verTareaNodo(rec);
                        }
                    },
                    //Realizar Mantenimiento de Torre
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = $("#ROLE_154-6817"); 	
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            var boolRadio = rec.get('tipoMedio').toUpperCase().indexOf('RADIO');

                            if(rec.get('estadoSolicitud') === "Finalizada" && boolPermiso && boolRadio !== -1)
                            {
                                return 'button-grid-mantenimientoTorre';                              
                            }
                            else
                            {
                                return '';
                            }
                        },
                        tooltip: 'Mantenimiento Correctivo de Torre',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            Ext.MessageBox.show({
                                title: 'Confirmación',
                                msg: `<div>
                                      <i class="custom-icon-msg--question"></i>
                                      <div class='custom-text-msg'>
                                      <span>Se creará una tarea para registrar el Mantenimiento Correctivo de esta Torre.</span>
                                      <div style="margin-bottom:-.5rem;">¿Esta seguro(a) que desea guardar?</div>
                                      <br/><b>&#10140; Fecha: </b><b style="color: dimgray">${Ext.Date.format(new Date(), "d/m/Y")}</b>
                                      <br/><b>&#10140; Nodo: </b><b style="color: dimgray">${rec.get('nombreElemento')}</b>
                                      <br/>
                                      </div>
                                      </div>`,
                                buttons: Ext.Msg.YESNO,
                                buttonText: { yes: 'Si', no: 'No' },
                                fn: function (btnValue)
                                {
                                    if (btnValue === 'yes')
                                    {
                                        Ext.MessageBox.wait("Procesando...");
                                        Ext.Ajax.request({
                                            url: url_realizarMantenimiento,
                                            method: 'post',
                                            params: { idNodo: rec.get('idElemento') },
                                            success: function (response)
                                            {
                                                var text = response.responseText;
                                                if (text == 'OK')
                                                {
                                                    Ext.Msg.alert('Mensaje', 'Transacción exitosa');
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Error', text);
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.Msg.alert('Error', 'Error..: ' + result.statusText);
                                            }
                                        });

                                    }
                                }
                            });
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,        
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: [
                        { width: '10%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '200px',
                            listeners: {
                                specialkey: function (field, event) {
                                    if (event.getKey() == event.ENTER && field.inputEl.dom.value) {
                                        buscar();
                                    }
                                }
                            }
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtIdentificacion',
                            fieldLabel: 'Identificación',
                            value: '',
                            width: '200px'
                        },                       
                        { width: '10%',border:false},
                        
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {
                            id: 'sltMotivo',
                            fieldLabel: 'Motivo',
                            xtype: 'combobox',
                            typeAhead: true,
                            displayField:'nombreMotivo',
                            valueField: 'idMotivo',
                            queryMode: 'local',
                            loadingText: 'Buscando ...',
                            store: storeMotivos,
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combobox',
                            id: 'sltClaseNodo',
                            fieldLabel: 'Clase Nodo',
                            store: storeClaseNodo,
                            displayField: 'nombreDetalle',
                            valueField: 'idDetalle',
                            queryMode: 'local',
                            listClass: 'x-combo-list-small',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        //-------------------------------------
                    
                        { width: '10%',border:false}, //inicio
                        {                            
                            id: 'sltProvincia',
                            fieldLabel: 'Provincia',                            
                            xtype: 'combobox',
                            typeAhead: true,
                            displayField:'nombre_provincia',
                            valueField: 'id_provincia',
                            queryMode: 'local',
                            store: storeProvincias,
                            listClass: 'x-combo-list-small',
                            width: '30%',
                            listeners: 
                            {
                                select: function(combo)
                                {
                                    Ext.getCmp('sltCanton').reset();
                                    Ext.getCmp('sltCanton').setDisabled(false);
                                    
                                    storeCantones.proxy.extraParams = {idProvincia: combo.getValue()};
                                    storeCantones.load();
                                                  
                                }
                            }
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combobox',
                            id: 'sltCanton',
                            fieldLabel: 'Canton',
                            store: storeCantones,
                            displayField: 'nombre_canton',
                            valueField: 'id_canton',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        //-------------------------------------
                        
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado Nodo',
                            id: 'sltEstadoNodo',
                            value:'Activo',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Modificado','Modificado'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado Solicitud',
                            id: 'sltEstadoSolicitud',
                            value:'Todos',
                            store: [
                                ['Pendiente', 'Pendiente'],
                                ['AutorizadaTecnico', 'AutorizadaTecnico'],
                                ['AutorizadaLegal', 'AutorizadaLegal'],
                                ['FirmadoContrato', 'FirmadoContrato'],                            
                                ['Finalizada', 'Finalizada'],
                                ['Anulada', 'Anulada'],
                                ['Eliminada', 'Eliminada'],
                                ['RechazadaTecnico', 'RechazadaTecnico'],
                                ['RechazadaLegal', 'RechazadaLegal']
                            ],
                            width: '30%'
                        },
                        { width: '10%',border:false} //final
                        
                        //-------------------------------------                                               
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
     Ext.getCmp('sltCanton').setDisabled(true);
    
});

function buscar(){
    store.load({params: {
        nombreElemento: Ext.getCmp('txtNombre').value,  
        identificacion: Ext.getCmp('txtIdentificacion').value,  
        canton:         Ext.getCmp('sltCanton').value,
        provincia:      Ext.getCmp('sltProvincia').value,
        estadoNodo:     Ext.getCmp('sltEstadoNodo').value,
        estadoSolicitud:Ext.getCmp('sltEstadoSolicitud').value,
        motivo:         Ext.getCmp('sltMotivo').value,
        clase:          Ext.getCmp('sltClaseNodo').value        
    }});
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    
    Ext.getCmp('txtIdentificacion').value="";
    Ext.getCmp('txtIdentificacion').setRawValue("");
    
    Ext.getCmp('sltClaseNodo').value="";
    Ext.getCmp('sltClaseNodo').setRawValue("");
    
    Ext.getCmp('sltMotivo').value="";
    Ext.getCmp('sltMotivo').setRawValue("");
    
    Ext.getCmp('sltCanton').value="";
    Ext.getCmp('sltCanton').setRawValue("");
    
    Ext.getCmp('sltProvincia').value="";
    Ext.getCmp('sltProvincia').setRawValue("");
    
    Ext.getCmp('sltEstadoNodo').value="Todos";
    Ext.getCmp('sltEstadoNodo').setRawValue("Todos");
    
    Ext.getCmp('sltEstadoSolicitud').value="Todos";
    Ext.getCmp('sltEstadoSolicitud').setRawValue("Todos");        
        
    store.load({params: {
        nombreElemento: '',
        identificacion: '',
        provincia: '',
        canton:'',
        motivo:'',
        clase:'',
        estadoSolicitud:'Todos',
        estadoNodo: 'Todos'        
    }});

    Ext.getCmp('sltCanton').setDisabled(true);
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.idElemento;
        
        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
          estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
        {
          param = param + '|';
        }
//        alert(param);
      }      
      if(estado == 0)
      {
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "nodo/deleteAjaxDslam",
                    method: 'post',
                    params: { param : param},
                    success: function(response){
                        var text = response.responseText;
                        store.load();
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
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

/************************************************************************ */
/************************** VER MAPA ************************************ */
/************************************************************************ */
function showVerMapa(rec) {
    winVerMapa = "";

    if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
    {
        if (!winVerMapa)
        {
            formPanelMapa = Ext.create('Ext.form.Panel', {
                BodyPadding: 10,
                frame: true,
                items: [
                    {
                        html: "<div id='map_canvas' style='width:575px; height:450px'></div>"
                    }
                ]
            });

            winVerMapa = Ext.widget('window', {
                title: 'Mapa del Elemento',
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: true,
                items: [formPanelMapa]
            });
        }

        winVerMapa.show();
        muestraMapa(rec.get("latitud"), rec.get("longitud"));
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function muestraMapa(vlat,vlong){
    var mapa;
    var ciudad = "";
    var markerPto ;

    if((vlat)&&(vlong)){
        var latlng = new google.maps.LatLng(vlat,vlong);
        //var latlng = new google.maps.LatLng(-2.176963, -79.883673);
        var myOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        if(mapa){
            mapa.setCenter(latlng);
        }else{
            mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        }

        if(ciudad=="gye")
            layerCiudad = 'http://157.100.3.122/Coberturas.kml';
        else
            layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

        //google.maps.event.addListener(mapa, 'dblclick', function(event) {
        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng, 
            map: mapa
        });
        mapa.setZoom(17);
        //  dd2dms(event.latLng.lat(),event.latLng.lng());
        //});
    }
} 

function cierraVentanaMapa(){
    winVerMapa.close();
    winVerMapa.destroy();
    
}

function editarNombreNodo(data)
{

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Actualizando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function()
        {
            if (Ext.getCmp('txtNombreNodo').value !== '')
            {
                conn.request({
                    method: 'POST',
                    params: {
                        id: data.get('idElemento'),
                        nombre: Ext.getCmp('txtNombreNodo').value
                    },
                    url: url_editarNombreNodo,
                    success: function(response)
                    {
                        var json = Ext.JSON.decode(response.responseText);

                        Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                            if (btn == 'ok') {
                                win.destroy();
                                store.load();
                            }
                        });
                    },
                    failure: function(rec, op) {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert('Alerta ', json.mensaje);
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Advertencia', 'Debe ingresar el nombre del Nodo');
            }
        }
    });

    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            win.destroy();
        }
    });


    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            msgTarget: 'side'
        },
        items:
            [
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    width: 500,
                    items:
                        [
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Nodo:',
                                id: 'nombreNodo',
                                name: 'nombreNodo',
                                value: data.get('nombreElemento')
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Direccion:',
                                id: 'direccion',
                                name: 'direccion',
                                value: data.get('direccion')
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Provincia:',
                                id: 'provincia',
                                name: 'provincia',
                                value: data.get('nombreProvincia')
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Canton:',
                                id: 'canton',
                                name: 'canton',
                                value: data.get('nombreCanton')
                            },
                            {
                                xtype: 'textarea',
                                fieldLabel: 'Nombre:',
                                id: 'txtNombreNodo',
                                name: 'txtNombreNodo',
                                value: data.get('nombreElemento'),
                                rows: 3,
                                cols: 40
                            }
                        ]
                }
            ]
    });

    win = Ext.create('Ext.window.Window', {
        title: 'Actualizar Nombre Nodo',
        modal: true,
        width: 525,
        height: 300,
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons: [btnguardar, btncancelar]
    }).show();
}

function editarMedidor(data)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Actualizando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });        

    btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function()
        {
            if (Ext.getCmp('txtNumeroMedidor').value !== '')
            {
                conn.request({
                    mstoreTipoMedidorethod: 'POST',
                    params: {
                        id: data.get('idMedidor'),
                        numeroMedidor:      Ext.getCmp('txtNumeroMedidor').value,
                        tipoMedidor:        Ext.getCmp('cmbTipoMedidor').value,
                        medidorElectrico:   Ext.getCmp('cmbMedidorElectrico').value,
                        claseMedidor:       Ext.getCmp('cmbClaseMedidor').value
                    },
                    url: url_editarMedidor,
                    success: function(response)
                    {
                        var json = Ext.JSON.decode(response.responseText);

                        Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                            if (btn == 'ok') {
                                win.destroy();
                                store.load();
                            }
                        });
                    },
                    failure: function(rec, op) {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert('Alerta ', json.mensaje);
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Advertencia', 'Debe ingresar el numero del Medidor');
            }
        }
    });

    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            win.destroy();
        }
    });        

    comboClaseMedidor = new Ext.form.ComboBox({
        id: 'cmbClaseMedidor',
        fieldLabel: 'Clase Medidor:',
        xtype: 'combobox',
        typeAhead: true,
        displayField: 'nombreClaseMedidor',
        valueField: 'idClaseMedidor',
        triggerAction: 'all',
        selectOnFocus: true,
        loadingText: 'Buscando ...',
        hideTrigger: false,
        store: storeClaseMedidor,
        lazyRender: true,
        listClass: 'x-combo-list-small'    
    });
    
    comboTipoMedidor = new Ext.form.ComboBox({
        id: 'cmbTipoMedidor',
        fieldLabel: 'Tipo Medidor:',
        xtype: 'combobox',
        typeAhead: true,
        displayField: 'nombreTipoMedidor',
        valueField: 'idTipoMedidor',
        triggerAction: 'all',
        selectOnFocus: true,
        loadingText: 'Buscando ...',
        hideTrigger: false,
        store: storeTipoMedidor,
        lazyRender: true,
        listClass: 'x-combo-list-small'
    });
    
            
    Ext.getCmp('cmbTipoMedidor').setRawValue(data.get('nombreTipoMedidor'));
    Ext.getCmp('cmbTipoMedidor').setValue(data.get('idTipoMedidor'));
    Ext.getCmp('cmbMedidorElectrico').setRawValue(data.get('valor1'));
    Ext.getCmp('cmbMedidorElectrico').setValue(data.get('valor1'));
    Ext.getCmp('cmbClaseMedidor').setRawValue(data.get('nombreClaseMedidor'));
    Ext.getCmp('cmbClaseMedidor').setValue(data.get('idClaseMedidor'));

    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            msgTarget: 'side'
        },
        items:
            [
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    width: 500,
                    items:
                        [
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Nodo:',
                                id: 'nombreNodo',
                                name: 'nombreNodo',
                                value: data.get('nombreElemento')
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Direccion:',
                                id: 'direccion',
                                name: 'direccion',
                                value: data.get('direccion')
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Provincia:',
                                id: 'provincia',
                                name: 'provincia',
                                value: data.get('nombreProvincia')
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Canton:',
                                id: 'canton',
                                name: 'canton',
                                value: data.get('nombreCanton')
                            },
                            comboTipoMedidor,
                            comboClaseMedidor,                           
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Numero Medidor:',
                                id: 'txtNumeroMedidor',
                                name: 'txtNumeroMedidor',
                                value: data.get('numeroMedidor')                              
                            }
                        ]
                }
            ]
    });        

    win = Ext.create('Ext.window.Window', {
        title: 'Actualizar Informacion Medidor',
        modal: true,
        width: 525,
        height: 300,
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons: [btnguardar, btncancelar]
    }).show();

}

function verElementosContenidos(data)
{
    var boolInHabilitar = typeof crearSolicitudElementoNodo === 'undefined' || !crearSolicitudElementoNodo;

    var storeElementosPrincipales = new Ext.data.Store ({
        id       : 'storeElementosPrincipales',
        total    : 'total',
        autoLoad :  true,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    :  url_verContenidos,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams: {
                'idNodo'      :  data.get('idElemento'),
                'tipoElemento': 'NODO'
            }
        },
        fields: [
            {name: 'idElemento'       , mapping: 'idElemento'},
            {name: 'nombreElemento'   , mapping: 'nombreElemento'},
            {name: 'descripcionElemento' , mapping: 'descripcionElemento'},
            {name: 'modeloElemento'   , mapping: 'modeloElemento'},
            {name: 'tipoElemento'     , mapping: 'tipoElemento'},
            {name: 'serieFisica'      , mapping: 'serieFisica'},
            {name: 'existenElementos' , mapping: 'existenElementos'},
            {name: 'loginCliente'     , mapping: 'loginCliente'},
            {name: 'inhabilitar'      , mapping: 'inhabilitar'}
        ]
    });

    var storeElementosSecundarios = new Ext.data.Store ({
        id       : 'storeElementosSecundarios',
        total    : 'total',
        autoLoad :  false,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    :  url_verContenidos,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'idElemento'      , mapping: 'idElemento'},
            {name: 'nombreElemento'  , mapping: 'nombreElemento'},
            {name: 'modeloElemento'  , mapping: 'modeloElemento'},
            {name: 'tipoElemento'    , mapping: 'tipoElemento'},
            {name: 'serieFisica'     , mapping: 'serieFisica'},
            {name: 'idElemento2'     , mapping: 'idElemento2'},
            {name: 'nombreElemento2' , mapping: 'nombreElemento2'},
            {name: 'modeloElemento2' , mapping: 'modeloElemento2'},
            {name: 'tipoElemento2'   , mapping: 'tipoElemento2'},
            {name: 'serieFisica2'    , mapping: 'serieFisica2'},
            {name: 'loginCliente2'   , mapping: 'loginCliente2'},
            {name: 'inhabilitar2'    , mapping: 'inhabilitar2'}
        ]
    });

    var smElementosPrincipales = new Ext.selection.CheckboxModel({
        listeners: {
            select: function(selectionModel, record, index) {
                if(record.data.inhabilitar || record.data.loginCliente !== "") {
                    smElementosPrincipales.deselect(index);
                }
            }
        }
    });

    var smElementosSecundarios = new Ext.selection.CheckboxModel({
        listeners: {
            select: function(selectionModel, record, index) {
                if(record.data.inhabilitar2 || record.data.loginCliente2 !== "") {
                    smElementosSecundarios.deselect(index);
                }
            }
        }
    });

    var gridElementosPrincipales = Ext.create('Ext.grid.Panel', {
        id      : 'idGridElementosPrincipales',
        title   : 'Elementos Principales',
        width   :  650,
        height  :  350,
        store   :  storeElementosPrincipales,
        selModel:  smElementosPrincipales,
        loadMask:  true,
        frame   :  false,
        viewConfig: {
            loadingText: 'Cargando',
            emptyText  : 'No hay datos para mostrar',
            getRowClass: function(rec) {
                return rec.get('inhabilitar') ||
                       rec.get('loginCliente') !== "" ? 'grisTextGrid' : 'blackTextGrid';
            }
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto?",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view,{
                    uievent: function (type, view, cell, recordIndex, cellIndex, e){
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',{
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    autoHide  : false,
                    renderTo  : Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            var gridData = grid.getStore().getAt(grid.recordIndex).data;
                            if (gridData.inhabilitar) {
                                tip.update('<b style="color:blue;">Elemento con solicitud vigente.<br/>'+
                                           'Debe finalizar el proceso.</b>');
                            } else if (gridData.loginCliente !== "" ) {
                                tip.update('<b style="color:blue;">Este elemento pertenece al cliente.<br/>'+
                                           'Use la opción: Cambio de Modem Inmediato.</b>');
                            } else {
                                return false;
                            }
                        }
                    }
                });

                grid.tip.on('show', function(){
                    var timeout;
                    grid.tip.getEl().on('mouseout', function(){
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseout', function(){
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        columns:
        [
            {
                header    : 'idElemento',
                dataIndex : 'idElemento',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Nombre</b>',
                dataIndex : 'nombreElemento',
                width     :  250,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Descripcion</b>',
                dataIndex : 'descripcionElemento',
                width     :  200,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  100,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento',
                width     :  80,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieFisica',
                align     : 'center',
                width     :  120,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Cliente</b>',
                dataIndex : 'loginCliente',
                align     : 'center',
                width     :  120,
                hideable  :  false,
                sortable  :  false,
                renderer: function(val){
                    return '<label style="color:green;">'+val+'</label>';
                }
            },
            {
                xtype     : 'actioncolumn',
                header    : '<b>Acciones</b>',
                width     :  70,
                align     : 'center',
                hideable  :  false,
                sortable  :  false,
                items     :
                [
                    {
                        tooltip: 'Ver elementos contenidos',
                        getClass: function(v, meta, rec) {
                            return rec.get('existenElementos') &&
                                  !rec.get('inhabilitar') ? 'button-grid-show' : 'icon-invisible';
                        },
                        handler: function(grid, rowIndex) {
                            var gridData       =  grid.getStore().getAt(rowIndex).data;
                            var nombreElemento = '<b style="color:green;">'+gridData.nombreElemento+'</b>';
                            Ext.getCmp("idGridElementosSecundarios").setTitle("Elementos Contenidos - " +nombreElemento);
                            storeElementosSecundarios.removeAll();
                            storeElementosSecundarios.load({params:{
                                'idNodo'              : gridData.idElemento,
                                'idElementoPrincipal' : data.get('idElemento'),
                                'boolEsSecundario'    : true
                            }});
                        }
                    }
                ]
            }
        ]
    });

    var gridElementosSecundarios = Ext.create('Ext.grid.Panel', {
        id      : 'idGridElementosSecundarios',
        title   : 'Elementos Contenidos',
        width   :  600,
        height  :  350,
        store   :  storeElementosSecundarios,
        selModel:  smElementosSecundarios,
        loadMask:  true,
        frame   :  false,
        viewConfig: {
            loadingText: 'Cargando',
            emptyText  : 'No hay datos para mostrar',
            getRowClass: function(rec) {
                return rec.get('inhabilitar2') ||
                       rec.get('loginCliente2') !== "" ? 'grisTextGrid' : 'blackTextGrid';
            }
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto?",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view,{
                    uievent: function (type, view, cell, recordIndex, cellIndex, e){
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',{
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    autoHide  : false,
                    renderTo  : Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            var gridData = grid.getStore().getAt(grid.recordIndex).data;
                            if (gridData.inhabilitar2) {
                                tip.update('<b style="color:blue;">Elemento con solicitud vigente.<br/>'+
                                           'Debe finalizar el proceso.</b>');
                            } else if (gridData.loginCliente2 !== "" ) {
                                tip.update('<b style="color:blue;">Este elemento pertenece al cliente.<br/>'+
                                           'Use la opción: Cambio de Modem Inmediato.</b>');
                            } else {
                                return false;
                            }
                        }
                    }
                });

                grid.tip.on('show', function(){
                    var timeout;
                    grid.tip.getEl().on('mouseout', function(){
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseout', function(){
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        columns:
        [
            {
                header    : '<b>Id 1</b>',
                dataIndex : 'idElemento',
                align     : 'center',
                width     :  60,
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Descripción</b>',
                dataIndex : 'nombreElemento',
                align     : 'center',
                width     :  100,
                hideable  :  false,
                sortable  :  true
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                align     : 'center',
                width     :  100,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento',
                align     : 'center',
                width     :  100,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieFisica',
                align     : 'center',
                width     :  120,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Id 2</b>',
                dataIndex : 'idElemento2',
                align     : 'center',
                width     :  60,
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Elemento</b>',
                dataIndex : 'nombreElemento2',
                align     : 'center',
                width     :  100,
                hideable  :  false,
                sortable  :  true
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento2',
                align     : 'center',
                width     :  100,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento2',
                align     : 'center',
                width     :  80,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieFisica2',
                align     : 'center',
                width     :  120,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Cliente</b>',
                dataIndex : 'loginCliente2',
                align     : 'center',
                width     :  120,
                hideable  :  false,
                sortable  :  false,
                renderer: function(val){
                    return '<label style="color:green;">'+val+'</label>';
                }
            }
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        frame      : false,
        bodyPadding: 10,
        items:
        [
            {
                xtype: 'fieldset',
                title: '<b>Datos del Nodo</b>',
                layout: {
                    tdAttrs : {style: 'padding: 7px;'},
                    type    : 'table',
                    pack    : 'center',
                    columns :  3
                },
                defaults: {
                    width :'350px;'
                },
                items : [
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Nombre</b>',
                        value      :  data.get('nombreElemento'),
                        margin     : '0 15',
                        labelWidth :  80,
                        readOnly   :  true
                    },
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Estado</b>',
                        value      :  data.get('estado'),
                        margin     : '0 15',
                        labelWidth :  80,
                        readOnly   :  true
                    },
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Dirección</b>',
                        value      :  data.get('direccion'),
                        margin     : '0 15',
                        labelWidth :  80,
                        readOnly   :  true
                    },
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Solicitante</b>',
                        value      :  data.get('solicitante'),
                        margin     : '0 15',
                        labelWidth :  80,
                        readOnly   :  true
                    },
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Provincia</b>',
                        value      :  data.get('nombreProvincia'),
                        margin     : '0 15',
                        labelWidth :  80,
                        readOnly   :  true
                    },
                    {
                        xtype      : 'textfield',
                        fieldLabel : '<b>Cantón</b>',
                        value      :  data.get('nombreCanton'),
                        margin     : '0 15',
                        labelWidth :  80,
                        readOnly   :  true
                    }
                ]
            },
            {
                xtype : 'fieldset',
                layout: {
                    tdAttrs : {style: 'padding: 2px;'},
                    type    : 'table',
                    pack    : 'center',
                    columns :  3
                },
                items: [
                    gridElementosPrincipales,
                    {width: '10%', border: false},
                    gridElementosSecundarios
                ]
            }
        ]
    });

    var btnSolicitud = Ext.create('Ext.Button', {
        iconCls  : 'icon_solicitud',
        text     : 'Crear Solicitud',
        disabled :  boolInHabilitar,
        handler: function() {
            var arrayElementoFilter;
            var arrayElementos = [];
            var boolSeguir     = true;
            var mensaje        = '';
            var gridPrincipal  = Ext.getCmp("idGridElementosPrincipales");
            var gridSecundario = Ext.getCmp("idGridElementosSecundarios");
            var arrayDatosGridPrincipal  = gridPrincipal.getSelectionModel().getSelection();
            var arrayDatosGridSecundario = gridSecundario.getSelectionModel().getSelection();

            if (arrayDatosGridPrincipal.length <= 0 && arrayDatosGridSecundario.length <= 0) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor seleccione al menos un elemento!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            $.each(arrayDatosGridPrincipal, function(i, item) {
                arrayElementos.push({'idElemento'     :  item.data.idElemento,
                                     'nombreElemento' :  item.data.nombreElemento,
                                     'modeloElemento' :  item.data.modeloElemento,
                                     'tipoElemento'   :  item.data.tipoElemento,
                                     'serieElemento'  :  item.data.serieFisica,
                                     'esPrincipal'    : 'Si'});
            });

            $.each(arrayDatosGridSecundario, function(i, item) {

                if (item.data.tipoElemento === 'UDRACK' && Ext.isEmpty(item.data.idElemento2)) {
                    boolSeguir = false;
                    mensaje    = "Ha seleccionado un tipo de '"+item.data.tipoElemento+"' sin elemento asociado!";
                    return;
                }

                if (item.data.tipoElemento === 'UDRACK' && !Ext.isEmpty(item.data.idElemento2)) {

                    arrayElementoFilter = arrayElementos.filter(function(elemento){
                        return elemento.idElemento === item.data.idElemento2;
                    });

                    if (Ext.isEmpty(arrayElementoFilter)) {
                        arrayElementos.push({'idElemento'     :  item.data.idElemento2,
                                             'nombreElemento' :  item.data.nombreElemento2,
                                             'modeloElemento' :  item.data.modeloElemento2,
                                             'tipoElemento'   :  item.data.tipoElemento2,
                                             'serieElemento'  :  item.data.serieFisica2,
                                             'esPrincipal'    : 'No'});
                    }

                } else {
                     arrayElementos.push({'idElemento'     :  item.data.idElemento,
                                          'nombreElemento' :  item.data.nombreElemento,
                                          'modeloElemento' :  item.data.modeloElemento,
                                          'tipoElemento'   :  item.data.tipoElemento,
                                          'serieElemento'  :  item.data.serieFisica,
                                          'esPrincipal'    : 'No'});
                }
            });

            if (!boolSeguir) {
                Ext.Msg.show({
                    title: 'Alerta',msg: mensaje,
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            solicitudCambioEquipo({'data':data,'elementos':arrayElementos});
        }
    });

    var btncancelar = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        cls: 'x-btn-rigth',
        handler: function() {
            winElementosNodo.close();
            winElementosNodo.destroy();
        }
    });

    var winElementosNodo = new Ext.Window ({
        id         : 'winElementosNodo',
        title      : 'Elementos en NODO',
        layout     : 'fit',
        resizable  :  false,
        closable   :  false,
        modal      :  true,
        items      :  [formPanel],
        buttonAlign: 'center',
        buttons    :  [btnSolicitud,btncancelar]
    }).show();
}

function solicitudCambioEquipo(data)
{
    var fechaFinArray    = '';
    var fechaActual      = '';
    var horaActual       = '';
    var idMotivo         = '';
    var idTipoSolicitud  = '';
    var arrayElementos   = data.elementos;
    var objData          = data.data;
    var idElementoNodo   = objData.get('idElemento');
    
    Ext.Ajax.request({
        method: 'post',
        url   :  url_obtenerFechaServer,
        async :  false,
        success: function(response) {
            var json = Ext.JSON.decode(response.responseText);
            if (json.success) {
                fechaFinArray = json.fechaActual.split("-");
                fechaActual   = fechaFinArray[0] + "-" + fechaFinArray[1] + "-" + fechaFinArray[2];
                horaActual    = json.horaActual;
            }
        }
    });

    var storeElementos = new Ext.data.Store ({
        autoDestroy: true,
        proxy      : {type: 'memory'},
        fields     : ['idElemento'    ,'nombreElemento',
                      'modeloElemento','tipoElemento'  ,
                      'esPrincipal'   ,'serieElemento',
                      'serieOrigen'],
        data       : arrayElementos
    });

    var storeMotivo = new Ext.data.Store ({
        autoLoad: false,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    :  url_motivosEquipos,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'motivos',
                totalProperty: 'total'
            },
        },
        fields:
        [
            {name: 'idMotivo'        , mapping: 'idMotivo'},
            {name: 'descripcion'     , mapping: 'descripcion'},
            {name: 'idTipoSolicitud' , mapping: 'idTipoSolicitud'},
        ]
    });

    var comboTipoSolicitud = new Ext.form.ComboBox({
        id         : 'comboTipoSolicitud',
        store      :  [['solicitudCambioElemento','SOLICITUD CAMBIO ELEMENTO'],
                       ['solicitudRetiroElemento','SOLICITUD RETIRO ELEMENTO']],
        fieldLabel : 'Tipo de Solicitud',
        emptyText  : 'Seleccione el tipo',
        width      :  320,
        listeners: {
            select: function(combo) {
                Ext.getCmp('comboMotivo').setDisabled(false);
                Ext.getCmp('comboMotivo').setValue("");
                Ext.getCmp('comboMotivo').setRawValue("");
                storeMotivo.removeAll();
                storeMotivo.load({params:{'tipoSolicitud':combo.getValue()}});
            }
        }
    });

    var comboMotivo = new Ext.form.ComboBox({
        id           : 'comboMotivo',
        store        :  storeMotivo,
        fieldLabel   : 'Motivo',
        valueField   : 'idMotivo',
        displayField : 'descripcion',
        queryMode    : 'local',
        width        :  320,
        disabled     :  true,
        editable     :  false,
        listeners: {
            select: function(combo) {
                idMotivo        = combo.getValue();
                idTipoSolicitud = combo.displayTplData[0].idTipoSolicitud;
            }
        }
    });

    var textAreaObservacion = new Ext.form.field.TextArea({
        id        : 'textAreaObservacion',
        fieldLabel: 'Observación',
        cols      :  80,
        width     :  450,
        rows      :  2,
        maxLength :  200,
        plugins   : ['uppertextfield']
    });

    //psvelez
    var btnIngresarSerie = Ext.create('Ext.Button', {
        iconCls  : 'icon_solicitud',
        text     : 'Ingresar Números de Series',
        handler: function() {

            var arrayElementoSinSerie = [];
            var intNumElemSinSerie = 0;

            // recorro arreglos de elementos sin serie
            $.each(arrayElementos, function(i, item) {
                if (item.serieElemento == null || item.serieElemento == "")
                {
                    arrayElementoSinSerie.push({'idElemento'     :  item.idElemento,
                                        'nombreElemento' :  item.nombreElemento,
                                        'modeloElemento' :  item.modeloElemento,
                                        'tipoElemento'   :  item.tipoElemento,
                                        'serieElemento'  :  item.serieElemento,
                                        'serieOrigen'    :  ''
                                    });
                    intNumElemSinSerie = intNumElemSinSerie +1;
                }
            });
            if (arrayElementoSinSerie.length >0)
            {   
                obtenerSerieNueva();
                creaPanelSeriesElementos(arrayElementoSinSerie);  
            }
            else
            {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'No existen elementos sin número de serie!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }               
        }
    });

    //psvelez
    var itemComboBtn = Ext.form.FieldSet({
        xtype: 'fieldset',
        title: '',
        style: 'border: none;padding:0px',
        bodyStyle: 'padding:0px',
        layout: {
            type: 'table',
            columns: 3,
            pack: 'center'
        },
        items: [
            comboTipoSolicitud,
            {
                width: 40,
                layout: 'form',
                border: false,
                items: 
                [
                    {
                        xtype: 'displayfield'
                    }
                ]
            },
            btnIngresarSerie                        
        ]
    });

    var observacionPanel = Ext.create('Ext.panel.Panel', {
        title      : 'Datos de la Solicitud',
        border     :  true,
        width      :  690,
        bodyPadding:  7,
        bodyStyle  :  {background: '#fff'},
        defaults   :  {bodyStyle : 'padding:10px'},
        items      :  [itemComboBtn,comboMotivo,textAreaObservacion]
        //items      :  [comboTipoSolicitud,comboMotivo,textAreaObservacion]
    });

    var gridElementos = Ext.create('Ext.grid.Panel', {
        id         : 'idGridElementosSolicitud',
        title      : 'Elementos',
        width      :  690,
        height     :  310,
        collapsible:  false,
        store      :  storeElementos,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : 'Copiar texto?',
                    msg    : 'Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>'+value+'</b>',
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
                header    : 'idElemento',
                dataIndex : 'idElemento',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Nombre</b>',
                dataIndex : 'nombreElemento',
                width     :  270,
                sortable  :  true
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  100,
                sortable  :  true
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento',
                width     :  100,
                sortable  :  true
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieElemento',
                width     :  110,
                sortable  :  true
            },
            {
                header    : '<b>Principal</b>',
                dataIndex : 'esPrincipal',
                align     : 'center',
                width     :  80,
                sortable  :  true
            },
            {
                header    : '<b>Serie Origen</b>',
                dataIndex : 'serieOrigen',
                width     :  100,
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                xtype     : 'actioncolumn',
                header    : '<b>Acciones</b>',
                width     :  70,
                align     : 'center',
                sortable  :  false,
                hideable  :  false,
                items     :
                [
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-delete';
                        },
                        tooltip: 'Remover Elemento',
                        handler: function(grid, rowIndex) {
                            var gridData = grid.getStore().getAt(rowIndex).data;
                            var index = storeElementos.findBy(function (record) {
                                return record.data.idElemento === gridData.idElemento;
                            });
                            if (index >= 0) {
                                storeElementos.removeAt(index);
                            }
                        }
                    }
                ]
            }
        ]
    });

    var formPanelSolicitudCambioEquipo = Ext.create('Ext.form.Panel', {
        id         : 'formPanelSolicitudCambioEquipo',
        frame      :  false,
        bodyPadding:  3,
        height     :  520,
        items:
        [
            {
                xtype : 'fieldset',
                border:  false,
                layout: {
                    type   : 'table',
                    pack   : 'center',
                    columns:  2
                },
                items:
                [
                    {
                        xtype  : 'fieldset',
                        height :  490,
                        layout : {type:'table',pack:'center',columns:1},
                        items  : [observacionPanel,gridElementos]
                    },
                    {
                        xtype  : 'fieldset',
                        height :  490,
                        layout : {type:'table',pack:'center',columns:1},
                        items  : [agregarTareaNodo(objData,fechaActual,horaActual,true)]
                    }
                ]
            }
        ]
    });

    var btnCrearSolicitud = Ext.create('Ext.Button', {
        text: '<label style="color:green;"><i class="fa fa-floppy-o" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Generar Solicitud</b>',
        handler: function()
        {
            var arrayElementos     = [];
            var storeGridElementos = Ext.getCmp('idGridElementosSolicitud').getStore();
            var observacionSol     = Ext.getCmp('textAreaObservacion').getValue();
            var fechaEjecucion     = Ext.getCmp('fecha_ejecucion').value;
            var horaEjecucion      = Ext.getCmp('hora_ejecucion').value;
            var intElemtoSinSerie  = 0;

            $.each(storeGridElementos.data.items, function(i, item) {
                arrayElementos.push({'idElemento':item.data.idElemento, 
                                     'serieElemento':item.data.serieElemento, 
                                     'serieOrigen':item.data.serieOrigen});
            });

            if (Ext.isEmpty(arrayElementos) || arrayElementos.length < 1) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Debe tener al menos un elemento para generar la solicitud!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            intElemtoSinSerie  = 0;
            $.each(storeGridElementos.data.items, function(i, item) {
                
                if (item.data.serieElemento == null || item.data.serieElemento == "")
                {
                    intElemtoSinSerie = intElemtoSinSerie +1;                   
                }                
            });
        
            if (intElemtoSinSerie > 0)
            {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Debe ingresar las series de los elemento para generar la solicitud!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (Ext.isEmpty(idMotivo)) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor seleccione el motivo de la solicitud!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (Ext.isEmpty(observacionSol)) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por ingrese la observación de la solicitud!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (Ext.getCmp('cmbTarea').value === null || Ext.getCmp('cmbTarea').value === "") {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor seleccione la tarea!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (Ext.getCmp('comboEmpresa').getValue() === null || Ext.getCmp('comboDepartamento').getValue() === null ||
                Ext.getCmp('comboCiudad').getValue()  === null)
            {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor seleccione la  empresa, ciudad y departamento!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (!((Ext.getCmp('comboEmpleado')  && Ext.getCmp('comboEmpleado').value) ||
                  (Ext.getCmp('comboCuadrilla') && Ext.getCmp('comboCuadrilla').value   && valorAsignacion === "cuadrilla")))
            {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor escoja un empleado o cuadrilla!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            var comboEmpleado        = null;
            var valoresComboEmpleado = null;
            var refAsignadoId        = null;
            var personaEmpresaRol    = null;
            var refAsignadoNombre    = null;
            var asignadoId           = null;
            var asignadoNombre       = null;
            var observacionTar       = Ext.getCmp('observacionAsignacion').value;
            var prefijoEmpresa       = Ext.getCmp('comboEmpresa').value;
            var nombreEmpresa        = Ext.getCmp('comboEmpresa').rawValue;
            var indexDataCombo       = Ext.getCmp('comboEmpresa').store.findBy(function (rec) {
                                        return rec.data.valor  === prefijoEmpresa &&
                                               rec.data.opcion === nombreEmpresa;});
            var idEmpresa            = Ext.getCmp('comboEmpresa').store.getAt(indexDataCombo).data.idEmpresa;
            var nombreProceso        = Ext.getCmp('cmbProcesos').rawValue;
            var nombreTarea          = Ext.getCmp('cmbTarea').rawValue;

            if (valorAsignacion === "empleado")
            {
                comboEmpleado        =  Ext.getCmp('comboEmpleado').value;
                valoresComboEmpleado =  comboEmpleado.split("@@");
                refAsignadoId        =  valoresComboEmpleado[0];
                personaEmpresaRol    =  valoresComboEmpleado[1];
                refAsignadoNombre    =  Ext.getCmp('comboEmpleado').rawValue;
                asignadoId           =  Ext.getCmp('comboDepartamento').value;
                asignadoNombre       =  Ext.getCmp('comboDepartamento').rawValue;

            }
            else if (valorAsignacion === "cuadrilla")
            {
                refAsignadoId  = "0";
                asignadoId     =  Ext.getCmp('comboCuadrilla').value;
                asignadoNombre =  Ext.getCmp('comboCuadrilla').rawValue;
            }
            else
            {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor escoja un empleado o cuadrilla!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            //Datos para generar la solitiud.
            var datosSolicitud = {
                'idTipoSolicitud': idTipoSolicitud,
                'idMotivo'       : idMotivo,
                'observacion'    : observacionSol,
                'idElementoNodo' : idElementoNodo,
                'arrayElementos' : arrayElementos,
            };

            //Datos para generar la tarea.
            var datosTarea = {
                'idEmpresa'        : idEmpresa,
                'prefijoEmpresa'   : prefijoEmpresa,
                'nombreProceso'    : nombreProceso,
                'nombreTarea'      : nombreTarea,
                'personaEmpresaRol': personaEmpresaRol,
                'asignadoId'       : asignadoId,
                'asignadoNombre'   : asignadoNombre,
                'refAsignadoId'    : refAsignadoId,
                'refAsignadoNombre': refAsignadoNombre,
                'observacion'      : observacionTar,
                'fechaEjecucion'   : fechaEjecucion,
                'horaEjecucion'    : horaEjecucion,
                'tipoAsignado'     : valorAsignacion
            };

            Ext.Msg.show({
                title      : 'Mensaje',
                msg        : '¿Está seguro de generar el proceso de solicitud?',
                closable   :  false,
                multiline  :  false,
                icon       :  Ext.Msg.QUESTION,
                buttons    :  Ext.Msg.YESNO,
                buttonText :  {yes: 'Si', no: 'No'},
                fn         :  function (buttonValue)
                {
                    if(buttonValue === 'yes')
                    {
                        Ext.MessageBox.wait('Proceso Ejecutándose...');
                        Ext.Ajax.request({
                            method : 'post',
                            timeout:  240000,
                            url    :  url_crearSolicitudElementosNodo,
                            params: {
                                'jsonDatosSolicitud': Ext.JSON.encode(datosSolicitud),
                                'jsonDatosTarea'    : Ext.JSON.encode(datosTarea)
                            },
                            success: function(response) {
                                var objData = Ext.JSON.decode(response.responseText);
                                var status  = objData.status;
                                var mensaje = objData.mensaje;
                                var titulo  = status ? 'Mensaje' : 'Error';
                                Ext.Msg.show({
                                    closable   : false  , multiline : false,
                                    msg        : mensaje, title     : titulo,
                                    icon       : status ? Ext.Msg.INFO   : Ext.Msg.ERROR,
                                    buttons    : status ? Ext.Msg.OK     : Ext.Msg.CANCEL,
                                    buttonText : status ? {ok: 'Cerrar'} : {cancel: 'Cerrar.'},
                                    fn: function (buttonValue) {
                                        if (buttonValue === 'ok') {
                                            winCrearSolicitud.close();
                                            winCrearSolicitud.destroy();
                                            store.removeAll();
                                            store.load();
                                            Ext.getCmp('idGridElementosPrincipales').getStore().removeAll();
                                            Ext.getCmp('idGridElementosPrincipales').getStore().load();
                                            Ext.getCmp('idGridElementosSecundarios').getStore().removeAll();
                                        }
                                    }
                                });
                            },
                            failure: function (result) {
                                Ext.Msg.show({
                                    title      : 'Alerta',
                                    msg        :  result.statusText,
                                    buttons    :  Ext.Msg.OK,
                                    icon       :  Ext.Msg.ERROR,
                                    closable   :  false,
                                    multiline  :  false,
                                    buttonText :  {ok: 'Cerrar'}
                                });
                            }
                        });
                    }
                }
            });
        }
    });

    var btnCancelar = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winCrearSolicitud.close();
            winCrearSolicitud.destroy();
        }
    })

    var winCrearSolicitud = new Ext.Window ({
        id         : 'winCrearSolicitud',
        title      : 'Crear Solicitud',
        layout     : 'fit',
        buttonAlign: 'center',
        resizable  :  false,
        closable   :  false,
        modal      :  true,
        items      :  [formPanelSolicitudCambioEquipo],
        buttons    :  [btnCrearSolicitud,btnCancelar]
    }).show();
}

//psvelez
function creaPanelSeriesElementos(data)
{
    var storeElementosSeries = new Ext.data.Store ({
        autoDestroy: true,
        proxy      : {type: 'memory'},
        fields     : ['idElemento'    ,'nombreElemento',
                      'modeloElemento','tipoElemento'  ,
                      'esPrincipal'   ,'serieElemento'],
        data       : data
    });

    var gridElementosSerie = Ext.create('Ext.grid.Panel', {
        id         : 'idGridElementosSeries',
        title      : 'Elementos',
        width      :  600,
        height     :  250,
        collapsible:  false,
        store      :  storeElementosSeries,
        listeners: {
            //Se valida que serie no exista ingresada
            edit: function(e, context) {

                var strSerie = e.context.value;

                var conn = new Ext.data.Connection({
                    listeners: {
                        'beforerequest': {
                            fn: function (con, opt) {
                                Ext.get(document.body).mask('Validando serie ingresada...');
                            },
                            scope: this
                        },
                        'requestcomplete': {
                            fn: function (con, res, opt) {
                                Ext.get(document.body).unmask();
                            },
                            scope: this
                        },
                        'requestexception': {
                            fn: function (con, res, opt) {
                                Ext.get(document.body).unmask();
                            },
                            scope: this
                        }
                    }
                }); 
                
                
                conn.request({
                    url: url_validaElementoSerie,
                    method: 'post',
                    params: 
                        { 
                            strSerie : strSerie
                        },
                    success: function(response){		
            
                        if (response.responseText != "0")
                        {
                            Ext.Msg.show({
                                title: 'Alerta',msg: 'Numero de Serie  ' + strSerie + '  ya existe, verificar Serie ingresada',
                                icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                buttonText: {cancel: 'Cerrar'}
                            });
                            context.record.set('serieElemento', '');
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
            
            
        },
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
                header    : 'idElemento',
                dataIndex : 'idElemento',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Nombre</b>',
                dataIndex : 'nombreElemento',
                width     :  220,
                sortable  :  true
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  130,
                sortable  :  true
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento',
                width     :  80,
                sortable  :  true
            },
            {
                header    : '<b>Número de Serie</b>',
                dataIndex : 'serieElemento',
                width     :  100,
                sortable  :  true,
                editor    : 'textfield'
            }
        ],
        selType: 'cellmodel',
        plugins: [
            Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1
            })
        ]
    });

    var btnGuardarSerie = Ext.create('Ext.Button', {
        text: '<label style="color:green;"><i class="fa fa-floppy-o" aria-hidden="true"></i></label>'+
              '&nbsp;<b> Guardar Números de Series</b>',
        handler: function()
        {
            var arrayElementos     = [];
            var intCountAux = 0;
            var storeGridElementosSeries = Ext.getCmp('idGridElementosSeries').getStore();
            var storeGridElementosSolicitud = Ext.getCmp('idGridElementosSolicitud').getStore();
            

            // recorro grid elementos sin serie para generar series automaticas y setear origen
            $.each(storeGridElementosSeries.data.items, function(i, item) 
            {

                var intIdElemento = '';

                // si serie esta null se genera automaticamente
                if (item.data.serieElemento == null || item.data.serieElemento == "")
                {
                    
                    // idElemento + fecha actual
                    storeGridElementosSeries.data.items[i].data.serieElemento = item.data.idElemento +''+ fechaSerieNueva;
                    storeGridElementosSeries.data.items[i].data.serieOrigen = 'automatica';
                    
                }else 
                {
                    storeGridElementosSeries.data.items[i].data.serieOrigen = 'manual';
                }

                intIdElemento = item.data.idElemento;

                // recorro elementos para actualizar grid solicitud
                $.each(storeGridElementosSolicitud.data.items, function(j, item2) 
                {

                    if (intIdElemento == item2.data.idElemento)
                    {
                        
                        storeGridElementosSolicitud.data.items[j].data.serieElemento = storeGridElementosSeries.data.items[i].data.serieElemento;
                        storeGridElementosSolicitud.data.items[j].data.serieOrigen = storeGridElementosSeries.data.items[i].data.serieOrigen;

                        return;
                    }


                });


            });

            // actualizo grid con el store nuevo
            Ext.getCmp('idGridElementosSolicitud').getView().refresh();

            winIngresoNumSerie.destroy();
            
        }
    })

    var btnCancelar = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winIngresoNumSerie.destroy();
        }
    })

    var winIngresoNumSerie = new Ext.Window ({
        id         : 'winIngresoNumSerie',
        title      : 'Ingresar números de Series',
        layout     : 'fit',
        buttonAlign: 'center',
        resizable  :  false,
        closable   :  false,
        modal      :  true,
        items      :  [gridElementosSerie],
        buttons    :  [btnGuardarSerie,btnCancelar]
    }).show();

    function validateString(strCampo) {
        var boolCorrecto = false;
        if (/^([A-Za-z]+[A-Za-z0-9\_\-]*)$/.test(strCampo))
        {
            boolCorrecto = true;
        }
        return boolCorrecto;
    }
}


function obtenerSerieNueva(){

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    }); 
    
    
    conn.request({
        url: url_getElementoSerieAutomatica,
        method: 'post',
        params: 
            { 
                
            },
        success: function(response){			
            fechaSerieNueva = Ext.decode(response.responseText);
          
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


function cambioElementoNodo(data)
{
    var storeSolicitudes = new Ext.data.Store ({
        id      : 'storeSolicitudes',
        total   : 'total',
        autoLoad:  true,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    :  url_obtenerSolicitudesElementosNodo,
            timeout:  60000,
            reader: {
                type: 'json',
                totalProperty: 'total'
            },
            extraParams: {
                'IdElementoNodo':data.get('idElemento')
            }
        },
        fields: [
            {name: 'tipoSolicitud'    , mapping: 'tipoSolicitud'},
            {name: 'idSolicitud'      , mapping: 'idSolicitud'},
            {name: 'idSolicitudCarac' , mapping: 'idSolicitudCarac'},
            {name: 'idElementoNodo'   , mapping: 'idElementoNodo'},
            {name: 'idElemento'       , mapping: 'idElemento'},
            {name: 'feCreacion'       , mapping: 'feCreacion'},
            {name: 'numeroTarea'      , mapping: 'numeroTarea'},
            {name: 'nombreElemento'   , mapping: 'nombreElemento'},
            {name: 'serieElemento'    , mapping: 'serieElemento'},
            {name: 'modeloElemento'   , mapping: 'modeloElemento'},
            {name: 'tipoElemento'     , mapping: 'tipoElemento'},
        ]
    });

    var gridElementosPorSolicitud = new Ext.create('Ext.grid.Panel',{
        id    :'gridElementosPorSolicitud',
        store : storeSolicitudes,
        height: 200,
        frame : false,
        viewConfig:{
            loadingText: 'Cargando',
            emptyText  : 'No hay datos para mostrar',
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : 'Copiar texto?',
                    msg    : 'Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>'+value+'</b>',
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        columns:
        [
            new Ext.grid.RowNumberer(),
            {
                dataIndex : 'idSolicitud',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                dataIndex : 'idSolicitudCarac',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                dataIndex : 'idElementoNodo',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                dataIndex : 'idElemento',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Nombre Elemento</b>',
                dataIndex : 'nombreElemento',
                width     :  200,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Modelo Elemento</b>',
                dataIndex : 'modeloElemento',
                width     :  120,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Tipo Elemento</b>',
                dataIndex : 'tipoElemento',
                width     :  120,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie Elemento</b>',
                dataIndex : 'serieElemento',
                width     :  120,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Fecha Creación</b>',
                dataIndex : 'feCreacion',
                align     : 'center',
                width     :  130,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Número Tarea</b>',
                dataIndex : 'numeroTarea',
                align     : 'center',
                width     :  100,
                hideable  :  false,
                sortable  :  false,
                renderer: function(val){
                    return '<b style="color:green;">'+val+'</b>';
                }
            },
            {
                xtype : 'actioncolumn',
                header: '<b>Acciones</b>',
                align : 'center',
                width :  80,
                items:
                [
                    {
                        tooltip: 'Cambiar Elemento',
                        getClass: function() {
                            return 'button-grid-verDslam';
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var dataGrid       = grid.getStore().getAt(rowIndex).data;
                            var tipoElemento   = dataGrid.tipoElemento;
                            var idElementoNodo = dataGrid.idElementoNodo;
                            var idElemento     = dataGrid.idElemento;
                            var idSolicitud    = dataGrid.idSolicitud;
                            var numeroTarea    = dataGrid.numeroTarea;

                            var storeModelos = new Ext.data.Store({
                                pageSize: 100,
                                autoLoad: true,
                                proxy: {
                                    type  : 'ajax',
                                    method: 'post',
                                    url   :  getModelosElemento,
                                    extraParams: {
                                        'tipo'  :  tipoElemento,
                                        'forma' : "Empieza con",
                                        'estado': "Activo"
                                    },
                                    reader: {
                                        type         : 'json',
                                        totalProperty: 'total',
                                        root         : 'encontrados'
                                    }
                                },
                                fields:[
                                    {name:'modelo', mapping:'modelo'},
                                    {name:'codigo', mapping:'codigo'}
                                ]
                            });

                            var elementoNodo = {
                                xtype : 'fieldset',
                                id    : 'elementoNodo',
                                title : 'Elemento Nuevo',
                                layout: {
                                    tdAttrs : {style: 'padding: 2px;'},
                                    type    : 'table',
                                    pack    : 'center',
                                    columns :  2
                                },
                                defaults: {
                                    width : 300
                                },
                                items:
                                [
                                    {
                                        xtype       : 'textfield',
                                        id          : 'nombreNuevoElemento',
                                        fieldLabel  : 'Nombre',
                                        labelWidth  :  75,
                                        value       :  dataGrid.nombreElemento
                                    },
                                    {width:'20%',border:false},
                                    {
                                        xtype       : 'textfield',
                                        id          : 'serieNuevoElemento',
                                        fieldLabel  : 'Serie',
                                        labelWidth  :  75
                                    },
                                    {
                                        xtype       : 'combobox',
                                        id          : 'modeloNuevoElemento',
                                        fieldLabel  : 'Modelo',
                                        displayField: 'modelo',
                                        valueField  : 'modelo',
                                        queryMode   : 'local',
                                        emptyText   : 'Seleccione el Modelo',
                                        loadingText : 'Buscando...',
                                        labelWidth  :  75,
                                        margin      : '0 10',
                                        store       :  storeModelos,
                                        listeners:
                                        {
                                            blur: function(combo)
                                            {
                                                var serieNuevoElemento = Ext.getCmp('serieNuevoElemento').getValue();
                                                Ext.Ajax.request({
                                                    url   :  buscarCpeNaf,
                                                    method: 'post',
                                                    params: {
                                                        'modeloElemento':  combo.getValue(),
                                                        'serieCpe'      :  serieNuevoElemento,
                                                        'estado'        : 'PI',
                                                        'bandera'       : 'ActivarServicio'
                                                    },
                                                    success: function(response){
                                                        var respuesta   = response.responseText.split("|");
                                                        var status      = respuesta[0];
                                                        var mensaje     = respuesta[1].split(",");
                                                        var descripcion = mensaje[0];
                                                        var mac         = mensaje[1];

                                                        if (status === "OK") {
                                                            Ext.getCmp('descripcionNuevoElemento').setValue = descripcion;
                                                            Ext.getCmp('descripcionNuevoElemento').setRawValue(descripcion);
                                                            Ext.getCmp('macNuevoElemento').setValue = mac;
                                                            Ext.getCmp('macNuevoElemento').setRawValue(mac);
                                                        } else {
                                                            Ext.getCmp('descripcionNuevoElemento').setValue = status;
                                                            Ext.getCmp('descripcionNuevoElemento').setRawValue(status);
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                        }
                                                    },
                                                    failure: function(result){
                                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    {
                                        xtype       : 'textfield',
                                        id          : 'macNuevoElemento',
                                        fieldLabel  : 'Mac',
                                        labelWidth  :  75,
                                        readOnly    :  true
                                    },
                                    {
                                        xtype       : 'textfield',
                                        id          : 'descripcionNuevoElemento',
                                        fieldLabel  : 'Descripción',
                                        labelWidth  :  75,
                                        margin      : '0 10',
                                        readOnly    :  true
                                    }
                                ]
                            };

                            var formPanelElementoNuevo = Ext.create('Ext.form.Panel',{
                                frame      : false,
                                bodyPadding: 5,
                                items      : [elementoNodo],
                                buttons:
                                [
                                    {
                                        text: '<label style="color:green;"><i class="fa fa-floppy-o" aria-hidden="true"></i></label>'+
                                              '&nbsp;<b>Cambiar</b>',
                                        formBind: true,
                                        handler : function()
                                        {
                                            var nombreNuevoElemento      = Ext.getCmp('nombreNuevoElemento');
                                            var serieNuevoElemento       = Ext.getCmp('serieNuevoElemento');
                                            var modeloNuevoElemento      = Ext.getCmp('modeloNuevoElemento');
                                            var macNuevoElemento         = Ext.getCmp('macNuevoElemento');
                                            var descripcionNuevoElemento = Ext.getCmp('descripcionNuevoElemento');

                                            if (Ext.isEmpty(nombreNuevoElemento.getValue()))
                                            {
                                                Ext.Msg.show({
                                                    title     : 'Alerta',
                                                    msg       : 'Ingrese el nombre del elemento.',
                                                    icon      :  Ext.Msg.WARNING,
                                                    buttons   :  Ext.Msg.CANCEL,
                                                    buttonText: {cancel: 'Cerrar'}
                                                });
                                                return;
                                            }

                                            if (Ext.isEmpty(serieNuevoElemento.getValue()))
                                            {
                                                Ext.Msg.show({
                                                    title     : 'Alerta',
                                                    msg       : 'Ingrese el número de serie del elemento.',
                                                    icon      :  Ext.Msg.WARNING,
                                                    buttons   :  Ext.Msg.CANCEL,
                                                    buttonText: {cancel: 'Cerrar'}
                                                });
                                                return;
                                            }

                                            if (Ext.isEmpty(modeloNuevoElemento.getValue()))
                                            {
                                                Ext.Msg.show({
                                                    title     : 'Alerta',
                                                    msg       : 'Seleccione el modelo del elemento.',
                                                    icon      :  Ext.Msg.WARNING,
                                                    buttons   :  Ext.Msg.CANCEL,
                                                    buttonText: {cancel: 'Cerrar'}
                                                });
                                                return;
                                            }

                                            if (descripcionNuevoElemento.getValue() ===  ""                  ||
                                                descripcionNuevoElemento.getValue() ===  null                ||
                                                descripcionNuevoElemento.getValue() === "NO EXISTE ELEMENTO" ||
                                                descripcionNuevoElemento.getValue() === "NO HAY STOCK"       ||
                                                descripcionNuevoElemento.getValue() === "NO EXISTE SERIAL"   ||
                                                descripcionNuevoElemento.getValue() === "CPE NO ESTA EN ESTADO")
                                            {
                                                Ext.Msg.show({
                                                    title     : 'Alerta',
                                                    msg       : 'Datos del elemento incorrectos, favor revisar!',
                                                    icon      :  Ext.Msg.WARNING,
                                                    buttons   :  Ext.Msg.CANCEL,
                                                    buttonText: {cancel: 'Cerrar'}
                                                });
                                                return;
                                            }

                                            Ext.get(formPanelElementoNuevo.getId()).mask('Proceso Ejecutándose...');
                                            Ext.Ajax.request({
                                                url     :  url_cambiarElementoNodo,
                                                method  : 'post',
                                                timeout :  1000000,
                                                params: {
                                                    'idSolicitud'         : idSolicitud,
                                                    'numeroTarea'         : numeroTarea,
                                                    'idElementoNodo'      : idElementoNodo,
                                                    'idElemento'          : idElemento,
                                                    'nombreNuevoElemento' : nombreNuevoElemento.getValue(),
                                                    'serieNuevoElemento'  : serieNuevoElemento.getValue(),
                                                    'modeloNuevoElemento' : modeloNuevoElemento.getValue(),
                                                    'macNuevoElemento'    : macNuevoElemento.getValue(),
                                                    'tipoElemento'        : tipoElemento
                                                },
                                                success: function(response)
                                                {
                                                    Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                    var objData = Ext.JSON.decode(response.responseText);
                                                    var status  = objData.status;
                                                    var message = objData.message;
                                                    var titulo  = status ? 'Mensaje' : 'Error';
                                                    Ext.Msg.show({
                                                        closable   : false  , multiline : false,
                                                        msg        : message, title     : titulo,
                                                        icon       : status ? Ext.Msg.INFO   : Ext.Msg.ERROR,
                                                        buttons    : status ? Ext.Msg.OK     : Ext.Msg.CANCEL,
                                                        buttonText : status ? {ok: 'Cerrar'} : {cancel: 'Cerrar'},
                                                        fn: function (buttonValue) {
                                                            if (buttonValue === 'ok') {
                                                                winCambiarElemento.close();
                                                                winCambiarElemento.destroy();
                                                                storeSolicitudes.removeAll();
                                                                storeSolicitudes.load();
                                                                store.removeAll();
                                                                store.load();
                                                            }
                                                        }
                                                    });
                                                },
                                                failure: function (result) {
                                                    Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                    Ext.Msg.show({
                                                        title      : 'Alerta',
                                                        msg        :  result.statusText,
                                                        buttons    :  Ext.Msg.OK,
                                                        icon       :  Ext.Msg.ERROR,
                                                        closable   :  false,
                                                        multiline  :  false,
                                                        buttonText :  {ok: 'Cerrar'}
                                                    });
                                                }
                                            });
                                        }
                                    },
                                    {
                                        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
                                              '&nbsp;<b>Cancelar</b>',
                                        formBind: true,
                                        handler : function()
                                        {
                                            winCambiarElemento.close();
                                            winCambiarElemento.destroy();
                                        }
                                    }
                                ]
                            });

                            var winCambiarElemento = Ext.create('Ext.window.Window',{
                                title    : 'Cambiar Elemento',
                                layout   : 'fit',
                                resizable:  false,
                                closable :  false,
                                modal    :  true,
                                items    : [formPanelElementoNuevo]
                            }).show();
                        }
                    }
                ]
            }
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel',{
        frame      : false,
        bodyPadding: 2,
        items:[{
            xtype: 'fieldset',
            items: [gridElementosPorSolicitud]
        }]
    });

    var btnCerrar = Ext.create('Ext.Button',{
        cls : 'x-btn-rigth',
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winSolicitudes.close();
            winSolicitudes.destroy();
        }
    });

    var winSolicitudes = Ext.create('Ext.window.Window',{
        title    : 'Elementos Por Solicitud',
        layout   : 'fit',
        resizable:  false,
        closable :  false,
        modal    :  true,
        items    :  [formPanel],
        buttons  :  [btnCerrar]
    }).show();
}

function migrarNodo(data)
{
    var idElemento = data.get('idElemento');

    var btnMigrar = Ext.create('Ext.Button', {
        text: '<label style="color:green;"><i class="fa fa-floppy-o" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Migrar</b>',
        handler: function()
        {
            var arrayElementos      = [];
            var storeGrid           = Ext.getCmp('idGridElementosMigrar').getStore();
            var idElementoNuevo     = Ext.getCmp('comboNodoNuevo').getValue();
            var nombreElementoNuevo = Ext.getCmp('comboNodoNuevo').getRawValue();

            if (Ext.isEmpty(idElementoNuevo)) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'Por favor seleccione el nuevo nodo!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (idElementoNuevo === idElemento) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'El nodo seleccionado no puede ser igual al nodo a migrar!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            if (storeGrid.data.items.length < 1) {
                Ext.Msg.show({
                    title: 'Alerta',msg: 'No existen elementos a migrar!',
                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                    buttonText: {cancel: 'Cerrar'}
                });
                return;
            }

            $.each(storeGrid.data.items, function(i, item) {
                arrayElementos.push(item.data.idElemento);
            });

            Ext.Msg.show({
                title      : 'Mensaje',
                msg        : '¿Está seguro de migrar los elementos al nodo <b>'+nombreElementoNuevo+'</b>?',
                closable   :  false,
                multiline  :  false,
                icon       :  Ext.Msg.QUESTION,
                buttons    :  Ext.Msg.YESNO,
                buttonText :  {yes: 'Si', no: 'No'},
                fn         :  function (buttonValue)
                {
                    if(buttonValue === 'yes')
                    {
                        Ext.MessageBox.wait('Proceso Ejecutándose...');
                        Ext.Ajax.request({
                            method : 'post',
                            timeout: 240000,
                            url    : url_migracionNodo,
                            params: {
                                'intIdElementoActual' : idElemento,
                                'intIdElementoNuevo'  : idElementoNuevo,
                                'strJsonElementos'    : Ext.JSON.encode(arrayElementos)
                            },
                            success: function(response) {
                                var objData = Ext.JSON.decode(response.responseText);
                                var status  = objData.status;
                                var mensaje = objData.mensaje;
                                var titulo  = status ? 'Mensaje' : 'Error';
                                Ext.Msg.show({
                                    closable   : false  , multiline : false,
                                    msg        : mensaje, title     : titulo,
                                    icon       : status ? Ext.Msg.INFO   : Ext.Msg.ERROR,
                                    buttons    : status ? Ext.Msg.OK     : Ext.Msg.CANCEL,
                                    buttonText : status ? {ok: 'Cerrar'} : {cancel: 'Cerrar.'},
                                    fn: function (buttonValue) {
                                        if (buttonValue === 'ok') {
                                            storeElementosNodo.removeAll();
                                            storeElementosNodo.load();
                                            storeElementosMigrar.removeAll();
                                            store.removeAll();
                                            store.load();
                                        }
                                    }
                                });
                            },
                            failure: function (result) {
                                Ext.Msg.show({
                                    title      : 'Alerta',
                                    msg        :  result.statusText,
                                    buttons    :  Ext.Msg.OK,
                                    icon       :  Ext.Msg.ERROR,
                                    closable   :  false,
                                    multiline  :  false,
                                    buttonText :  {ok: 'Cerrar'}
                                });
                            }
                        });
                    }
                }
            });
        }
    });

    var btnCancelar = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winMigrarNodo.close();
            winMigrarNodo.destroy();
        }
    });

    var storeElementosNodo = new Ext.data.Store ({
        autoLoad :  true,
        total    : 'total',
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    :  url_verContenidos,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams: {
                'idNodo'         :  idElemento,
                'estadoRelacion' : 'Activo'
            }
        },
        fields: [
            {name: 'idElemento'       , mapping: 'idElemento'},
            {name: 'nombreElemento'   , mapping: 'nombreElemento'},
            {name: 'modeloElemento'   , mapping: 'modeloElemento'},
            {name: 'tipoElemento'     , mapping: 'tipoElemento'},
            {name: 'existenElementos' , mapping: 'existenElementos'},
            {name: 'loginCliente'     , mapping: 'loginCliente'},
            {name: 'inhabilitar'      , mapping: 'inhabilitar'}
        ]
    });

    var storeElementosMigrar = new Ext.data.Store ({
        fields : ['idElemento','nombreElemento','modeloElemento','tipoElemento'],
        proxy  : {type: 'memory'}
    });

    var btnAgregar = Ext.create('Ext.Button', {
        id   : 'idBtnAgregar',
        text : '<label style="color:blue;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>'+
               '&nbsp;<b>Agregar</b>&nbsp;'+
               '<label style="color:black;"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></label>',
        disabled : true,
        handler  : function() {
            var gridElementosNodo = Ext.getCmp("idGridElementosNodo");
            var arraySelection = gridElementosNodo.getSelectionModel().getSelection();
            $.each(arraySelection, function(i, item) {
                var index = storeElementosMigrar.findBy(function (record) {
                    return record.data.idElemento === item.data.idElemento;
                });
                if (index < 0) {
                    storeElementosMigrar.add({"idElemento"     : item.data.idElemento,
                                              "nombreElemento" : item.data.nombreElemento,
                                              "modeloElemento" : item.data.modeloElemento,
                                              "tipoElemento"   : item.data.tipoElemento});
                }
            });
        }
    });

    var btnEliminar = Ext.create('Ext.Button', {
        id  : 'idBtnEliminar',
        text: '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Remover</b>',
        disabled: true,
        handler: function() {
            var gridElementosMigrar = Ext.getCmp("idGridElementosMigrar");
            var arraySelection = gridElementosMigrar.getSelectionModel().getSelection();
            $.each(arraySelection, function(i, item) {
                var index = storeElementosMigrar.findBy(function (record) {
                    return record.data.idElemento === item.data.idElemento;
                });
                if (index >= 0) {
                    storeElementosMigrar.removeAt(index);
                }
            });
            Ext.getCmp("idBtnEliminar").setDisabled(true);
        }
    });

    var smElementosSeleccion = new Ext.selection.CheckboxModel({
        listeners: {
            select: function(selectionModel, record, index) {
                if(record.data.inhabilitar) {
                    smElementosSeleccion.deselect(index);
                }
                var grid = Ext.getCmp("idGridElementosNodo");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnAgregar").setDisabled(false);
                }
            },
            deselect: function(){
                var grid = Ext.getCmp("idGridElementosNodo");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnAgregar").setDisabled(true);
                }
            }
        }
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        title       : 'Elementos del nodo - '+data.get('nombreElemento'),
        buttonAlign : 'center',
        bodyPadding :  10,
        border      :  false,
        collapsible :  true,
        collapsed   :  true,
        width       :  550,
        layout: {
            type    : 'table',
            align   : 'left',
            columns :  5
        },
        defaults : {bodyStyle: 'padding:15px'},
        items: [
            {width:'10%',border:false},
            {
                xtype      : 'textfield',
                id         : 'filterNombreNodo',
                fieldLabel : 'Nodo',
                labelWidth :  80,
            },
            {width:'10%',border:false},
            {
                xtype      : 'textfield',
                id         : 'filterTipoNodo',
                fieldLabel : 'Tipo',
                labelWidth :  80,
            },
            {width:'10%',border:false}
        ],
        buttons: [
            {
                text   : 'Buscar',
                iconCls: 'icon_search',
                handler: function()
                {
                    var nombreElemento = Ext.getCmp('filterNombreNodo').getValue();
                    var tipoElemento   = Ext.getCmp('filterTipoNodo').getValue();

                    if (Ext.isEmpty(nombreElemento) && Ext.isEmpty(tipoElemento)){
                        Ext.Msg.show({
                            title: 'Alerta',msg: 'Debe ingresar un criterio de búsqueda!',
                            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    storeElementosNodo.removeAll();
                    storeElementosNodo.load({params:{
                        'idNodo'         :  idElemento,
                        'nombreElemento' :  nombreElemento,
                        'tipoElemento'   :  tipoElemento,
                        'estadoRelacion' : 'Activo'
                    }});
                }
            },
            {
                text   : 'Limpiar',
                iconCls: 'icon_limpiar',
                handler: function()
                {
                    Ext.getCmp('filterNombreNodo').setValue('');
                    Ext.getCmp('filterTipoNodo').setValue('');
                    storeElementosNodo.load({params:{'idNodo':idElemento}});
                }
            }
        ]
    });

    var gridElementosNodo = Ext.create('Ext.grid.Panel', {
        id       : 'idGridElementosNodo',
        width    :  550,
        height   :  315,
        store    :  storeElementosNodo,
        selModel :  smElementosSeleccion,
        loadMask :  true,
        frame    :  false,
        viewConfig: {
            loadingText: 'Cargando',
            emptyText  : 'No hay datos para mostrar',
            getRowClass: function(rec) {
                return rec.get('inhabilitar') ? 'grisTextGrid' : 'blackTextGrid';
            }
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : 'Copiar texto?',
                    msg    : 'Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>' + value + '</b>',
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view,{
                    uievent: function (type, view, cell, recordIndex, cellIndex, e){
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',{
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    autoHide  : false,
                    renderTo  : Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            var gridData = grid.getStore().getAt(grid.recordIndex).data;
                            if (gridData.inhabilitar) {
                                tip.update('<b style="color:blue;">Elemento con solicitud vigente.<br/>'+
                                           'Debe finalizar el proceso.</b>');
                            } else {
                                return false;
                            }
                        }
                    }
                });

                grid.tip.on('show', function(){
                    var timeout;
                    grid.tip.getEl().on('mouseout', function(){
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseout', function(){
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        dockedItems:
        [
            {
                xtype : 'toolbar',
                dock  : 'top',
                align : '->',
                items : [
                    {xtype: 'tbfill'},
                    btnAgregar
                ]
            }
        ],
        columns:
        [
            {
                header    : 'idElemento',
                dataIndex : 'idElemento',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Nombre</b>',
                dataIndex : 'nombreElemento',
                width     :  310,
                sortable  :  true
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  95,
                sortable  :  true
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento',
                width     :  95,
                sortable  :  true
            }
        ]
    });

    var smElementosAgregados = new Ext.selection.CheckboxModel({
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("idGridElementosMigrar");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnEliminar").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("idGridElementosMigrar");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnEliminar").setDisabled(true);
                }
            }
        }
    });

    var gridElementosMigrar = Ext.create('Ext.grid.Panel', {
        id       : 'idGridElementosMigrar',
        title    : 'Elementos a migrar',
        width    :  550,
        height   :  340,
        store    :  storeElementosMigrar,
        selModel :  smElementosAgregados,
        loadMask :  true,
        frame    :  false,
        dockedItems:
        [
            {
                xtype : 'toolbar',
                dock  : 'top',
                align : '->',
                items : [
                    {xtype: 'tbfill'},
                    btnEliminar
                ]
            }
        ],
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj);
                var data     = record.data;
                var value    = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : 'Copiar texto?',
                    msg    : 'Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>' + value + '</b>',
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        columns:
        [
            {
                header    : 'idElemento',
                dataIndex : 'idElemento',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Nombre</b>',
                dataIndex : 'nombreElemento',
                width     :  310,
                sortable  :  true
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  95,
                sortable  :  true
            },
            {
                header    : '<b>Tipo</b>',
                dataIndex : 'tipoElemento',
                width     :  95,
                sortable  :  true
            }
        ]
    });

    var storeNodoNuevo = new Ext.data.Store({
        total: 'total',
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    :  url_gridNodos,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams: {
                'estadoNodo':'Activo',
            }
        },
        fields: [
            {name:'idElemento'      , mapping:'idElemento'},
            {name:'nombreElemento'  , mapping:'nombreElemento'},
            {name:'estado'          , mapping:'estado'},
            {name:'nombreProvincia' , mapping:'nombreProvincia'},
            {name:'nombreCanton'    , mapping:'nombreCanton'},
            {name:'direccion'       , mapping:'direccion'}
        ]
    });

    var comboNodoNuevo = new Ext.form.ComboBox({
        id          : 'comboNodoNuevo',
        queryMode   : 'remote',
        store       :  storeNodoNuevo,
        fieldLabel  : '<b>Nodo</b>',
        valueField  : 'idElemento',
        displayField: 'nombreElemento',
        emptyText   : 'Seleccione el Nodo',
        margin      : '0 20',
        width       :  400,
        disabled    :  false,
        listeners: {
            select: function(combo, rec) {
                if (combo.getValue() === idElemento) {
                    Ext.getCmp('comboNodoNuevo').setValue('');
                    Ext.getCmp('comboNodoNuevo').setRawValue('');
                    Ext.getCmp('estadoNuevoNodo').setValue('');
                    Ext.getCmp('provinciaNuevoNodo').setValue('');
                    Ext.getCmp('cantonNuevoNodo').setValue('');
                    Ext.getCmp('direccionNuevoNodo').setValue('');
                    Ext.Msg.show({
                        title: 'Alerta',msg: 'El nodo seleccionado no puede ser igual al nodo a migrar!',
                        icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                        buttonText: {cancel: 'Cerrar'}
                    });
                    return;
                }
                var data = rec[0].raw;
                Ext.getCmp('estadoNuevoNodo').setValue(data.estado);
                Ext.getCmp('provinciaNuevoNodo').setValue(data.nombreProvincia);
                Ext.getCmp('cantonNuevoNodo').setValue(data.nombreCanton);
                Ext.getCmp('direccionNuevoNodo').setValue(data.direccion);
            }
        }
    });

    var formPanelElementos = Ext.create('Ext.form.Panel', {
        id          : 'idFormPanelElementos',
        frame       :  false,
        bodyPadding :  10,
        items:
        [
            {
                xtype : 'fieldset',
                layout: {
                    type    : 'table',
                    pack    : 'center',
                    columns :  2
                },
                items:
                [
                    {
                        xtype  : 'fieldset',
                        title  : '<b>Datos del nodo a migrar</b>',
                        height :  175,
                        layout : {
                            tdAttrs : {style: 'padding: 3px;'},
                            type    : 'table',
                            pack    : 'center',
                            columns :  1
                        },
                        defaults: {
                            width : '400px;'
                        },
                        items : [
                            {
                                xtype      : 'textfield',
                                fieldLabel : '<b>Nodo</b>',
                                value      :  data.get('nombreElemento'),
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                fieldLabel : '<b>Estado</b>',
                                value      :  data.get('estado'),
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                fieldLabel : '<b>Provincia</b>',
                                value      :  data.get('nombreProvincia'),
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                fieldLabel : '<b>Cantón</b>',
                                value      :  data.get('nombreCanton'),
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                fieldLabel : '<b>Dirección</b>',
                                value      :  data.get('direccion'),
                                margin     : '0 20',
                                readOnly   :  true
                            }
                        ]
                    },
                    {
                        xtype  : 'fieldset',
                        title  : '<b>Datos del nuevo nodo</b>',
                        height :  175,
                        layout : {
                            tdAttrs : {style: 'padding: 3px;'},
                            type    : 'table',
                            pack    : 'center',
                            columns :  1
                        },
                        defaults: {
                            width : '400px;'
                        },
                        items : [
                            comboNodoNuevo,
                            {
                                xtype      : 'textfield',
                                id         : 'estadoNuevoNodo',
                                fieldLabel : '<b>Estado</b>',
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                id         : 'provinciaNuevoNodo',
                                fieldLabel : '<b>Provincia</b>',
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                id         : 'cantonNuevoNodo',
                                fieldLabel : '<b>Cantón</b>',
                                margin     : '0 20',
                                readOnly   :  true
                            },
                            {
                                xtype      : 'textfield',
                                id         : 'direccionNuevoNodo',
                                fieldLabel : '<b>Dirección</b>',
                                margin     : '0 20',
                                readOnly   :  true
                            }
                        ]
                    },
                    {
                        xtype  : 'fieldset',
                        height :  380,
                        layout : {
                            type    : 'table',
                            pack    : 'center',
                            columns :  1
                        },
                        items:[
                            {
                                xtype  : 'fieldset',
                                border :  false,
                                layout : {
                                    type    : 'table',
                                    pack    : 'center',
                                    columns :  1
                                },
                                items:[filterPanel,gridElementosNodo]
                            }
                        ]
                    },
                    {
                        xtype  : 'fieldset',
                        height :  380,
                        layout : {
                            type    : 'table',
                            pack    : 'center',
                            columns :  1
                        },
                        items:[
                           {
                                xtype  : 'fieldset',
                                border :  false,
                                layout : {
                                    type    : 'table',
                                    pack    : 'center',
                                    columns :  1
                                },
                                items:[gridElementosMigrar]
                            }
                        ]
                    }
                ]
            }
        ]
    });

    var winMigrarNodo = new Ext.Window ({
        id         : 'idWinMigrarNodo',
        title      : 'Migración de Nodo',
        layout     : 'fit',
        buttonAlign: 'center',
        resizable  :  false,
        closable   :  false,
        modal      :  true,
        items      :  [formPanelElementos],
        buttons    :  [btnMigrar,btnCancelar]
    }).show();
}

function showImages(data)
{
    idElemento = data.idElemento;
    
    Ext.Loader.setConfig({enabled: true});
    Ext.Loader.setPath('Ext.chooser', '../../../bundles/tecnico/js/InfoElementoNodo');
    Ext.Loader.setPath('Ext.ux', '../../../public/js/ext-4.1.1/src/ux');

    Ext.require([
        'Ext.button.Button',
        'Ext.data.proxy.Ajax',
        'Ext.chooser.z_InfoPanel',
        'Ext.chooser.z_IconBrowser',
        'Ext.chooser.z_Window',
        'Ext.ux.DataView.Animated',
        'Ext.toolbar.Spacer'
    ]);
  
    win = Ext.create('Ext.chooser.z_Window');
    win.show();
    
    
}

function agregarTareaNodo(data,fecha,hora,solicitud)
{
    var valorIdDepartamento = '';
    var esSolicitud         = typeof solicitud !== 'undefined' && solicitud;

    storeProcesos = new Ext.data.Store({
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_procesos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'nombreProceso', mapping: 'nombreProceso'}
            ]

    });

    storeTareas = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_tareaProceso,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreProceso: 'TAREAS SOPORTE',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idTarea', mapping: 'idTarea'},
                {name: 'nombreTarea', mapping: 'nombreTarea'}
            ]
    });

    storeEmpleados = new Ext.data.Store({
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: url_empleadosDepartamento,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_empleado', mapping: 'id_empleado'},
                {name: 'nombre_empleado', mapping: 'nombre_empleado'}
            ]
    });

    storeEmpresas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresaPorSistema,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                app: 'TELCOS'
            }
        },
        fields:
        [
            {name: 'idEmpresa' , mapping: 'idEmpresa'},
            {name: 'opcion'    , mapping: 'nombre_empresa'},
            {name: 'valor'     , mapping: 'prefijo'}
        ]
    });

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getCiudadesXEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });

    storeDepartamentosCiudad = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getDepartamentosXEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_departamento', mapping: 'id_departamento'},
                {name: 'nombre_departamento', mapping: 'nombre_departamento'}
            ]
    });

    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_integrantesCuadrilla,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'idCuadrilla', mapping: 'idCuadrilla'},
                {name: 'nombre', mapping: 'nombre'}
            ],
        listeners: {
            load: function(store) {
                if (store.proxy.extraParams.origenD === "Departamento")
                {
                    document.getElementById('radio_e').disabled = false;
                    document.getElementById('radio_c').disabled = false;
                    document.getElementById('radio_co').disabled = false;
                    document.getElementById('radio_e').checked = false;
                    document.getElementById('radio_c').checked = false;
                    document.getElementById('radio_co').checked = false;
                    Ext.getCmp('comboCuadrilla').setDisabled(true);
                    Ext.getCmp('comboEmpleado').setDisabled(true);
                    Ext.getCmp('comboContratista').setDisabled(true);

                    storeCuadrillas.proxy.extraParams.origenD = '';
                }
            }

        }
    });

    storeContratista = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
            }
        },
        fields:
            [
                {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
            ]
    });

    var iniHtml = '<div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                      &nbsp;<input type="radio" onchange="setearCombo(1);" value="empleado" name="radioCuadrilla" id="radio_e" disabled>&nbsp;\n\
                      Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(2);" value="cuadrilla" name="radioCuadrilla" \n\
                      id="radio_c" disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(3);"\n\
                      value="contratista" name="radioCuadrilla" id="radio_co" disabled>&nbsp;Contratista</div>';

    RadiosTiposResponsable = Ext.create('Ext.Component', {
        html: iniHtml,
        width: 600,
        padding: 10,
        style: {color: '#000000'}});

    combo_empleados = new Ext.form.ComboBox({
        id: 'comboEmpleado',
        name: 'comboEmpleado',
        fieldLabel: "Empleado",
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        disabled: true,
        width: 380,
        listeners: {
            select: function(){							
                Ext.getCmp('comboCuadrilla').value = "";
                Ext.getCmp('comboCuadrilla').setRawValue("");
                Ext.getCmp('comboContratista').value = "";
                Ext.getCmp('comboContratista').setRawValue("");
            }
        }                  
    });

    var formPanelTareas = Ext.create('Ext.form.Panel', {
        title         : esSolicitud ? 'Asignar Tarea' : '',
        bodyPadding   : 7,
        border        : true,
        waitMsgTarget : true,
        fieldDefaults : {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items:
        [
            {
                xtype     : 'fieldset',
                border    : !esSolicitud,
                autoHeight: true,
                width     : 450,
                items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Procesos:',
                        id:'cmbProcesos',
                        store: storeProcesos,
                        displayField: 'nombreProceso',
                        valueField: 'id',
                        queryMode: "remote",
                        emptyText: '',  
                        width: 380,
                        listeners: {
                            select: function(combo, records, eOpts) 
                            {
                                storeTareas.proxy.extraParams = {id: combo.getValue()};
                                storeTareas.load();
                                Ext.getCmp('cmbTarea').setVisible(true);
                                Ext.getCmp('cmbTarea').setDisabled(false);

                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        id: 'cmbTarea',
                        store: storeTareas,
                        displayField: 'nombreTarea',
                        valueField: 'idTarea',
                        fieldLabel: 'Tarea:',
                        queryMode: "remote",
                        emptyText: '',
                        width: 380,
                        disabled: true
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Empresa:',
                        id: 'comboEmpresa',
                        name: 'comboEmpresa',
                        store: storeEmpresas,
                        displayField: 'opcion',
                        valueField: 'valor',
                        queryMode: "remote",
                        emptyText: '',
                        width: 380,
                        listeners: {
                            select: function(combo) {
                                Ext.getCmp('comboCiudad').reset();
                                Ext.getCmp('comboDepartamento').reset();
                                Ext.getCmp('comboEmpleado').reset();
                                Ext.getCmp('comboCiudad').setDisabled(false);
                                Ext.getCmp('comboDepartamento').setDisabled(true);
                                Ext.getCmp('comboEmpleado').setDisabled(true);
                                presentarCiudades(combo.getValue());
                            }
                        },
                        forceSelection: true
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Ciudad',
                        id: 'comboCiudad',
                        name: 'comboCiudad',
                        store: storeCiudades,
                        displayField: 'nombre_canton',
                        valueField: 'id_canton',
                        queryMode: "remote",
                        emptyText: '',
                        disabled: true,
                        width: 380,
                        listeners: {
                            select: function(combo) {
                                Ext.getCmp('comboDepartamento').reset();
                                Ext.getCmp('comboEmpleado').reset();
                                Ext.getCmp('comboDepartamento').setDisabled(false);
                                Ext.getCmp('comboEmpleado').setDisabled(true);
                                empresa = Ext.getCmp('comboEmpresa').getValue();
                                presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                            }
                        },
                        forceSelection: true
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Departamento',
                        id: 'comboDepartamento',
                        name: 'comboDepartamento',
                        store: storeDepartamentosCiudad,
                        displayField: 'nombre_departamento',
                        valueField: 'id_departamento',
                        queryMode: "remote",
                        emptyText: '',
                        disabled: true,
                        width: 380,
                        listeners: {
                            afterRender: function(combo) {
                                if (esSolicitud) {
                                    formPanelTareas.getEl().mask("Cargando datos...");
                                    if (typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim()) {
                                        storeEmpresas.load(function() {
                                            Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                            Ext.getCmp('comboCiudad').setDisabled(false);
                                            storeCiudades.proxy.extraParams.empresa = strPrefijoEmpresaSession;
                                            storeCiudades.load(function() {
                                                Ext.getCmp('comboDepartamento').setDisabled(false);
                                                if (typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim()) {
                                                    Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                    if (typeof strIdDepartamentoUsrSession !== 'undefined' &&
                                                        strIdDepartamentoUsrSession.trim()) {
                                                        storeDepartamentosCiudad.proxy.extraParams.id_canton = strIdCantonUsrSession;
                                                        storeDepartamentosCiudad.proxy.extraParams.empresa   = strPrefijoEmpresaSession;
                                                        storeDepartamentosCiudad.load(function() {
                                                            combo.setValue(Number(strIdDepartamentoUsrSession));
                                                            Ext.getCmp('comboEmpleado').setDisabled(true);
                                                            Ext.getCmp('comboCuadrilla').setDisabled(true);
                                                            Ext.getCmp('comboContratista').setDisabled(true);
                                                            presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession,
                                                                strIdCantonUsrSession,strPrefijoEmpresaSession);
                                                            presentarCuadrillasXDepartamento(strIdDepartamentoUsrSession);
                                                            presentarContratistas();
                                                            formPanelTareas.getEl().unmask();
                                                        });
                                                    } else {
                                                        formPanelTareas.getEl().unmask();
                                                    }
                                                } else {
                                                    formPanelTareas.getEl().unmask();
                                                }
                                            });
                                        });
                                    } else {
                                        formPanelTareas.getEl().unmask();
                                    }
                                }
                            },
                            select: function(combo) {
                                Ext.getCmp('comboEmpleado').reset();
                                Ext.getCmp('comboEmpleado').setDisabled(true);
                                Ext.getCmp('comboCuadrilla').value = "";
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                                Ext.getCmp('comboCuadrilla').setDisabled(true);
                                Ext.getCmp('comboContratista').value = "";
                                Ext.getCmp('comboContratista').setRawValue("");
                                Ext.getCmp('comboContratista').setDisabled(true);
                                empresa = Ext.getCmp('comboEmpresa').getValue();
                                canton = Ext.getCmp('comboCiudad').getValue();
                                presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, valorIdDepartamento, 'no');
                                presentarCuadrillasXDepartamento(Ext.getCmp('comboDepartamento').getValue());
                                presentarContratistas();
                            }
                        },
                        forceSelection: true
                    },
                    RadiosTiposResponsable,
                    {
                        xtype: 'combobox',
                        id: 'comboEmpleado',
                        name: 'comboEmpleado',
                        fieldLabel: "Empleado",
                        store: storeEmpleados,
                        displayField: 'nombre_empleado',
                        valueField: 'id_empleado',
                        queryMode: "remote",
                        emptyText: '',
                        disabled: true,
                        width: 380,
                        listeners: {
                            select: function() {
                                Ext.getCmp('comboCuadrilla').value = "";
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                                Ext.getCmp('comboContratista').value = "";
                                Ext.getCmp('comboContratista').setRawValue("");
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Cuadrilla',
                        id: 'comboCuadrilla',
                        name: 'comboCuadrilla',
                        store: storeCuadrillas,
                        displayField: 'nombre',
                        valueField: 'idCuadrilla',
                        queryMode: "remote",
                        emptyText: '',
                        disabled: true,
                        width: 380,
                        listeners: {
                            select: function() {
                                Ext.getCmp('comboEmpleado').value = "";
                                Ext.getCmp('comboEmpleado').setRawValue("");
                                Ext.getCmp('comboContratista').value = "";
                                Ext.getCmp('comboContratista').setRawValue("");
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Contratista',
                        id: 'comboContratista',
                        name: 'comboContratista',
                        store: storeContratista,
                        displayField: 'nombre_empresa_externa',
                        valueField: 'id_empresa_externa',
                        queryMode: "remote",
                        emptyText: '',
                        disabled: true,
                        width: 380,
                        listeners: {
                            select: function() {
                                Ext.getCmp('comboEmpleado').value = "";
                                Ext.getCmp('comboEmpleado').setRawValue("");
                                Ext.getCmp('comboCuadrilla').value = "";
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                            }
                        }

                    },
                    {
                        xtype: 'datefield',
                        fieldLabel: 'Fecha de Ejecucion:',
                        id: 'fecha_ejecucion',
                        name: 'fecha_ejecucion',
                        editable: false,
                        format: 'Y-m-d',
                        width: 380,
                        value: fecha,
                        minValue: fecha
                    },
                    {
                        xtype: 'timefield',
                        fieldLabel: 'Hora de Ejecucion:',
                        format: 'H:i',
                        id: 'hora_ejecucion',
                        name: 'hora_ejecucion',
                        minValue: '00:01',
                        maxValue: '23:59',
                        increment: 1,
                        editable: true,
                        value: hora,
                        width: 380
                    },
                    {
                        xtype     : 'textarea',
                        id        : 'observacionAsignacion',
                        fieldLabel: 'Observación',
                        name      : 'observacion',
                        rows      :  3,
                        allowBlank:  false,
                        width     :  380,
                        plugins   : ['uppertextfield']
                    }
                ]
            }
        ]
    });

    var btnCrearTarea= Ext.create('Ext.Button', {
        text: 'Agregar Tarea',
        handler: function()
        {
            if (Ext.getCmp('cmbTarea').value !== null && Ext.getCmp('cmbTarea').value !== "")
            {
                if (Ext.getCmp('comboEmpresa').getValue() !== null && Ext.getCmp('comboDepartamento').getValue() !== null &&
                    Ext.getCmp('comboCiudad').getValue() !== null)
                {

                    if ((Ext.getCmp('comboEmpleado') && Ext.getCmp('comboEmpleado').value) ||
                        (Ext.getCmp('comboCuadrilla') && Ext.getCmp('comboCuadrilla').value && valorAsignacion === "cuadrilla") ||
                        (Ext.getCmp('comboContratista') && Ext.getCmp('comboContratista').value && 
                         valorAsignacion === "contratista"))
                    {
                        personaEmpresaRol = null;
                        refAsignadoNombre = null;

                        if (valorAsignacion === "empleado")
                        {
                            var comboEmpleado        = Ext.getCmp('comboEmpleado').value;
                            var valoresComboEmpleado = comboEmpleado.split("@@");
                            refAsignadoId       = valoresComboEmpleado[0];
                            personaEmpresaRol   = valoresComboEmpleado[1];
                            refAsignadoNombre   = Ext.getCmp('comboEmpleado').rawValue;
                            asignadoId          = Ext.getCmp('comboDepartamento').value;
                            asignadoNombre      = Ext.getCmp('comboDepartamento').rawValue;
                            tipoAsignado        = "EMPLEADO";

                        }
                        else if (valorAsignacion === "cuadrilla")
                        {
                            refAsignadoId       = "0";
                            asignadoId          = Ext.getCmp('comboCuadrilla').value;
                            asignadoNombre      = Ext.getCmp('comboCuadrilla').rawValue;
                            tipoAsignado        = "CUADRILLA";
                        }
                        else
                        {
                            refAsignadoId       = "0";
                            asignadoId          = Ext.getCmp('comboContratista').value;
                            asignadoNombre      = Ext.getCmp('comboContratista').rawValue;
                            tipoAsignado        = "EMPRESAEXTERNA";
                        }

                        observacion     = Ext.getCmp('observacionAsignacion').value;
                        fechaEjecucion  = Ext.getCmp('fecha_ejecucion').value;
                        horaEjecucion   = Ext.getCmp('hora_ejecucion').value;

                        var conn = new Ext.data.Connection({
                            listeners: {
                                'beforerequest': {
                                    fn: function(con, opt) {
                                        Ext.getBody().mask('Creando Tarea al Nodo');
                                    },
                                    scope: this
                                },
                                'requestcomplete': {
                                    fn: function(con, res, opt) {
                                        Ext.getBody().unmask();
                                    },
                                    scope: this
                                },
                                'requestexception': {
                                    fn: function(con, res, opt) {
                                        Ext.getBody().unmask();
                                    },
                                    scope: this
                                }
                            }
                        });

                        conn.request({
                            method: 'POST',
                            params: {
                                idNodo           : data.get('idElemento'), 
                                nombreNodo       : data.get('nombreElemento'), 
                                idTarea          : Ext.getCmp('cmbTarea').value,
                                personaEmpresaRol: personaEmpresaRol,
                                asignadoId       : asignadoId,
                                nombreAsignado   : asignadoNombre,
                                refAsignadoId    : refAsignadoId,
                                refAsignadoNombre: refAsignadoNombre,
                                observacion      : observacion,
                                fechaEjecucion   : fechaEjecucion,
                                horaEjecucion    : horaEjecucion,
                                tipoAsignacion   : tipoAsignado,
                                empresaAsignacion: Ext.getCmp('comboEmpresa').value
                            },
                            url: url_crearTareaNodo,
                            success: function(response)
                            {
                                var json = Ext.JSON.decode(response.responseText);

                                Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                    if (btn == 'ok') {
                                        winAsignarTarea.destroy();
                                        store.load();
                                    }
                                });
                            },
                            failure: function(rec, op) {
                                var json = Ext.JSON.decode(op.response.responseText);
                                Ext.Msg.alert('Alerta ', json.mensaje);
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado, cuadrilla o contratista');
                    }
                }
                else
                {
                    Ext.Msg.alert('Alerta ', 'Campos incompletos, debe seleccionar Empresa,Ciudad y Departamento');
                }
            }
            else
            {
                Ext.Msg.alert('Alerta ', 'Debe escoger una Tarea a asignar');
            }
        }
    });

    var btnCancelar= Ext.create('Ext.Button', {
        text: 'Cancelar',
        handler: function() {
            winAsignarTarea.destroy();
        }
    });

    var winAsignarTarea = Ext.create('Ext.window.Window', {
        title   : 'Asignar Tarea a Nodo',
        modal   :  true,
        width   :  480,
        closable:  true,
        layout  : 'fit',
        items   : [formPanelTareas],
        buttons : [btnCrearTarea,btnCancelar]
    });

    if (esSolicitud) {
        return formPanelTareas;
    } else {
        winAsignarTarea.show();
    }
}

function verTareaNodo(data)
{
    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            winVerTarea.destroy();
        }
    });
    
    var store = new Ext.data.Store
        ({            
            total: 'total',
            autoLoad:true,
            proxy:
                {                    
                    type: 'ajax',
                    url: url_verTareaNodo,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            idNodo: data.get('idElemento')                           
                        }
                },
            fields:
                [
                    {name: 'idDetalle', mapping: 'idDetalle'},
                    {name: 'idComunicacion', mapping: 'idComunicacion'},
                    {name: 'feSolicitada', mapping: 'feSolicitada'},
                    {name: 'nombreTarea', mapping: 'nombreTarea'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'tipoAsignado', mapping: 'tipoAsignado'} ,
                    {name: 'asignadoNombre', mapping: 'asignadoNombre'} ,
                    {name: 'observacion', mapping: 'observacion'} ,
                    {name: 'refAsignadoNombre', mapping: 'refAsignadoNombre'}                    
                ]
        });
    
     grid = Ext.create('Ext.grid.Panel',
        {
            width: '100%',
            height: 250,
            store: store,
            loadMask: true,
            frame: false,  
            viewConfig: {enableTextSelection: true},
            columns:
                [
                    {
                        id: 'idDetale',
                        header: 'idDetale',
                        dataIndex: 'idDetale',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'idComunicacion',
                        header: '#Tarea',
                        dataIndex: 'idComunicacion',                        
                        width: '5%',
                    },
                    {
                        header: 'Nombre Tarea',
                        dataIndex: 'nombreTarea',
                        width: '25%',
                        sortable: true
                    },   
                    {
                        header: 'Observación',
                        dataIndex: 'observacion',
                        width: '25%',
                        sortable: true
                    },  
                    {
                        header: 'Asignado',
                        dataIndex: 'asignadoNombre',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Responsable',
                        dataIndex: 'refAsignadoNombre',
                        width: '25%',
                        sortable: true
                    },
                    {
                        header: 'Fecha Solicitada',
                        dataIndex: 'feSolicitada',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: '10%',
                        sortable: true
                    }
                ],
                listeners:
                {
                    viewready: function(grid)
                    {
                        var view = grid.view;

                        grid.mon(view,
                            {
                                uievent: function(type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
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

                                                if (header.dataIndex != null)
                                                {
                                                    var trigger = tip.triggerElement,
                                                        parent = tip.triggerElement.parentElement,
                                                        columnTitle = view.getHeaderByCell(trigger).text,
                                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                                    {
                                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

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
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function()
                            {
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    }
                }
        });
    
    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            msgTarget: 'side'
        },
        items:
            [
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    width: 1090,
                    items:
                        [grid]
                }
            ]
    });        

    winVerTarea = Ext.create('Ext.window.Window', {
        title: 'Tareas generadas para el NODO',
        modal: true,
        width: 1100,        
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons: [btncancelar]
    }).show();
}

function presentarCiudades(empresa) 
{
    storeCiudades.proxy.extraParams = {empresa: empresa};
    storeCiudades.load();
}

function presentarDepartamentosPorCiudad(id_canton, empresa) 
{
    storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
    storeDepartamentosCiudad.load();
}

function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento, esJefe) 
{
    storeEmpleados.proxy.extraParams = {id_canton: id_canton, empresa: empresa, id_departamento: id_departamento, 
                                        departamento_caso: valorIdDepartamento, es_jefe: esJefe};
    storeEmpleados.load();
}

function presentarCuadrillasXDepartamento(id_departamento) 
{
    storeCuadrillas.proxy.extraParams = {departamento: id_departamento, estado: 'Eliminado', origenD: 'Departamento', strOrigenP: 'Nodo'};
    storeCuadrillas.load();
}

function presentarContratistas() 
{
    storeContratista.proxy.extraParams = {};
    storeContratista.load();
}

function setearCombo(tipo)
{
    if(tipo == "1")    
    {        
        var myData_message = storeEmpleados.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeEmpleados.getProxy().getReader().jsonData.myMetaData.boolSuccess;
                
        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Mensaje ', myData_message);
            Ext.getCmp('comboEmpleado').setDisabled(true); 
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
        }
        else
        {
            if (storeEmpleados.getCount() <= 1 && myData_boolSuccess != "1") {
                Ext.Msg.alert('Mensaje ', "No existen empleados asignados para este departamento.");
                Ext.getCmp('comboEmpleado').setDisabled(true);  
                Ext.getCmp('comboCuadrilla').setDisabled(true);
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
            }
            else
            {
                Ext.getCmp('comboEmpleado').setDisabled(false);
                Ext.getCmp('comboCuadrilla').setDisabled(true);
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
                valorAsignacion = "empleado";  
            }
            
        }
    }
    else if (tipo == "2")
    {    
        var myData_message = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.boolSuccess;                 

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
        }                
        else
        {                
            Ext.getCmp('comboCuadrilla').setDisabled(false);
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
            valorAsignacion = "cuadrilla";
        }
    }
    else
    {
        var myData_message = storeContratista.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeContratista.getProxy().getReader().jsonData.myMetaData.boolSuccess;

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }
        else
        {
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(false);
            valorAsignacion = "contratista";
        }
    }
}

function exportar()
{
    nombreElemento = Ext.getCmp('txtNombre').value;      
    canton         = Ext.getCmp('sltCanton').value;
    provincia      = Ext.getCmp('sltProvincia').value;
    estadoNodo     = Ext.getCmp('sltEstadoNodo').value;
    estadoSolicitud= Ext.getCmp('sltEstadoSolicitud').value;
    motivo         = Ext.getCmp('sltMotivo').value;
    clase          = Ext.getCmp('sltClaseNodo').value;
    
    console.log(provincia);
    
    window.location = url_exportarNodos + '?nombreElemento=' + nombreElemento
            + '&motivo=' + motivo
            + '&clase=' + clase
            + '&provincia=' + provincia
            + '&canton=' + canton
            + '&estadoNodo=' + estadoNodo
            + '&estadoSolicitud=' + estadoSolicitud;
}

function subir()
{
    var formPanel =  Ext.widget('form', {    
        width: 400,
        bodyPadding: 10,
        items: [{
            xtype: 'filefield',
            name: 'archivoElemento',
            id: 'archivoElemento',
            fieldLabel: 'Archivo a cargar(*):',
            labelWidth: 120,
            anchor: '100%',
            buttonText: 'Seleccionar Archivo...'
        }],
        buttons: [{
            text: 'Guardar',
            handler: function () {
                var form = this.up('form').getForm();
                //Se valida mini formulario para ingreso de elementos masivos
                var archivoElementoComp = Ext.getCmp('archivoElemento').value;
                                                                                                                                                                                
                if(!archivoElementoComp)
                {
                    Ext.Msg.alert('Advertencia', 'Debe seleccionar el archivo a subir');
                }                                        
                else
                {   
                    var archivoFinal = archivoElementoComp.toLowerCase();
                    var ext = getFileExt(archivoFinal);
                    if (ext == "csv") 
                    {
                        form.submit({
                        url: url_ingresoMasivo,
                        waitMsg: 'Subiendo Archivo...',                             
                             success: function(rec,op)
                             {         
                                var json = Ext.JSON.decode(op.response.responseText);                       
                                Ext.Msg.alert('Mensaje ', json.mensaje);                                                                                           
                                win.destroy();
                             },
                             failure: function(rec,op) {
                                var json = Ext.JSON.decode(op.response.responseText);
                                Ext.Msg.alert('Alerta ', json.mensaje);
                             }
                        });
                    }else 
                    {
                        Ext.Msg.alert('Advertencia', 'Solo se aceptan archivos con extensión .csv');
                        Ext.getCmp('archivoElemento').value="";
                        Ext.getCmp('archivoElemento').setRawValue("");
                    }
                    
                } 
            }
            },
            {
                text: 'Salir',
                handler: function()
                {
                    win.destroy();
                }
            }]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Ingreso Masivo de Elementos',
            modal: true,
            width: 600,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function ingresoElemento(data)
{    
    var strNombreElementoInpt;
    var strDescripcionElementoInpt;

    var storeTipoElemento = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_tipo_elemento/getTiposElementosNodos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      
        },
        fields:
            [
                {name: 'valor1', mapping: 'valor1'},
                {name: 'valor1', mapping: 'valor1'}
            ]
    });
    
    var storeMarcas = new Ext.data.Store({
        total: 'total',
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosTipo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento'},
                {name: 'idMarcaElemento', mapping: 'idMarcaElemento'}
            ]
    });

    var storeModelos = new Ext.data.Store({
        total: 'total',
        autoLoad:false,
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosPorMarca',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'},
                {name: 'idModeloElemento', mapping: 'idModeloElemento'}
            ]
    });
    
    var store = new Ext.data.Store
        ({            
            total: 'total',
            autoLoad:true,
            proxy:
                {                    
                    type: 'ajax',
                    url: url_verContenedor,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            idNodo: data.idElemento                        
                        }
                },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'},
                    {name: 'modeloElemento', mapping: 'modeloElemento'},
                    {name: 'tipoElemento', mapping: 'tipoElemento'},
                    {name: 'serieFisica', mapping: 'serieFisica'},
                    {name: 'feCreacion', mapping: 'feCreacion'},
                    {name: 'usrCreacion', mapping: 'usrCreacion'},
                    {name: 'estado', mapping: 'estado'}                   
                ]
        });
    
    grid = Ext.create('Ext.grid.Panel',
    {
            width: '100%',
            height: 250,
            store: store,
            loadMask: true,
            frame: false, 
            id: 'gridElementos',
            name: 'gridElementos',                       
            columns:
                [
                    {
                        header: 'Nombre Elemento',
                        dataIndex: 'nombreElemento',
                        width: '20%',
                        sortable: true
                    },
                    {
                        header: 'Modelo',
                        dataIndex: 'modeloElemento',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Tipo',
                        dataIndex: 'tipoElemento',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Serie',
                        dataIndex: 'serieFisica',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Fecha Creación',
                        dataIndex: 'feCreacion',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Usuario',
                        dataIndex: 'usrCreacion',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: '8%',
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 75,
                        items: [
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var permiso = $("#ROLE_154-2198"); 	
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                                                        
                                
                                    if (rec.data.estado === "Eliminado" || !boolPermiso || rec.data.tipoElemento === "SWITCH" || rec.data.tipoElemento === "SPLITTER")
                                    {                               
                                        return '';
                                    }
                                    else
                                    {                                   
                                        return 'button-grid-edit';
                                    }  
                                },
                                tooltip: 'Editar Elemento',
                                handler: function(grid,rowIndex,colIndex)
                                {
                                    var rec = store.getAt(rowIndex);   
                                    var band = true;
                                    var intBand = verificarClase(rec.get('tipoElemento'));
                                
                                    var claseRetornada = retornarClase(rec.get('nombreElemento'),rec.get('tipoElemento'));
                                    
                                    if(intBand==1)
                                    {
                                        band=false;   
                                    }
                                
                                    var panelDatos = Ext.create('Ext.form.Panel',{
                                        bodyPadding: 1,
                                        waitMsgTarget: true,
                                        fieldDefaults: {
                                            labelAlign: 'left',
                                            labelWidth: 85,
                                            msgTarget: 'side',
                                            bodyStyle: 'padding:20px'
                                        },
                                        layout: {
                                            type: 'table',
                                            // The total column count must be specified here
                                            columns: 1
                                        },
                                        defaults: {
                                            // applied to each contained panel
                                            bodyStyle: 'padding:20px'
                                        },
                                        items: [
                                            {
                                                xtype       : 'fieldset',
                                                title       : 'Elemento',
                                                labelStyle  : 'font-weight:bold', 
                                                autoHeight  : true,
                                                width       : 375,
                                                items:
                                                [
                                                    {
                                                        xtype: 'combobox',
                                                        fieldLabel: '* Clase:',
                                                        id: 'cmbClaseElmt',
                                                        name: 'cmbClaseElmt',                                
                                                        emptyText: 'Seleccione clase',
                                                        labelStyle:'font-weight:bold',     
                                                        width:350,
                                                        value:claseRetornada,
                                                        editable:false,
                                                        store: [                                    
                                                            ['PRIMARIO','Primario'],
                                                            ['SECUNDARIO','Secundario']
                                                        ],
                                                        hidden: band
                                                    },
                                                    {
                                                        xtype      : 'textfield',
                                                        id         : 'txtNombreElemento',
                                                        fieldLabel : '* Nombre',
                                                        labelStyle:'font-weight:bold', 
                                                        value      : rec.get('nombreElemento'),
                                                        width      : 350
                                                    }
                                                ]
                                            }
                                        ],
                                        buttons: [
                                            {                
                                                    text: 'Confirmar',
                                                    formBind: true,
                                                    handler: function(){                        
                                                        var strNuevoNombre = Ext.getCmp('txtNombreElemento').value;
                                                        if (strNuevoNombre === "" || strNuevoNombre === null) {
                                                            Ext.Msg.alert('Alerta ','<b>No se puede registrar valores nulos..!!</b>');
                                                            return;
                                                        }
                                                        var srtClaseElemento = Ext.getCmp('cmbClaseElmt').value; 
                                
                                                        Ext.Ajax.request({
                                                            url   :  url_editarNombreElemento,
                                                            async :  false,
                                                            method: 'post',
                                                            timeout: 300000,
                                                            params: {
                                                                tipoElemento     : rec.get('tipoElemento'),
                                                                nombreElemento   : strNuevoNombre,
                                                                serieElemento    : rec.get('serieFisica'),
                                                                idElemento       : rec.get('idElemento'),
                                                                claseElemento    : srtClaseElemento
                                                            },
                                                            success: function(response) {
                                                                var text = response.responseText;
                                                                if(response.responseText === "OK")
                                                                {                                       
                                                                    Ext.Msg.alert('Mensaje','Ingreso Exitoso.');
                                                                    store.load();
                                                                    win.destroy();    
                                                                }
                                                                else
                                                                {
                                                                    Ext.Msg.alert('Error',text );                         
                                                                    win.destroy();                     
                                                                }
                                                            },
                                                            
                                                            failure: function(result) {
                                                                win.destroy();
                                                                Ext.Msg.show({
                                                                    title   : 'Error',
                                                                    msg     : result.statusText,
                                                                    buttons : Ext.Msg.OK,
                                                                    icon    : Ext.MessageBox.ERROR
                                                                });
                                                            }
                                                        
                                                        });
                                                    }                
                                            },
                                            {
                                                text: 'Cancelar',
                                                handler: function(){                    
                                                    win.destroy();
                                                }
                                            }]
                                    });
                                
                                    var win = Ext.create('Ext.window.Window', {
                                        title: 'Editar elemento',
                                        modal: true,
                                        width: 400,
                                        closable: true,
                                        layout: 'fit',
                                        items: [panelDatos]
                                    }).show();                                                         
                                }
                            },
                            //eliminar nodo
                            {
                                getClass: function(v, meta, rec) 
                                {                            
                                    var permiso = $("#ROLE_154-2198"); 	
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                                                        
                                
                                    if (rec.data.estado === "Eliminado" || !boolPermiso || rec.data.tipoElemento === "SWITCH" || rec.data.tipoElemento === "SPLITTER")
                                    {                               
                                        return '';
                                    }
                                    else
                                    {                                   
                                        return 'button-grid-delete';
                                    }                           
                                },
                                tooltip: 'Eliminar Elemento',
                             
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec = store.getAt(rowIndex);
                                    Ext.Msg.confirm('Alerta','¿Esta Seguro de Eliminar el Elemento?', function(btnCarga){
                                    if(btnCarga=='yes' && rec.get('estado') !== "Eliminado")
                                    {				
                                        var conn = new Ext.data.Connection({
                                            listeners: {
                                                'beforerequest': {
                                                    fn: function(con, opt) {
                                                        Ext.get(document.body).mask('Eliminando Elemento...');
                                                    },
                                                    scope: this
                                                },
                                                'requestcomplete': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                },
                                                'requestexception': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                }
                                            }
                                        });  
                                    
                                        conn.request({
                                            method: 'POST',
                                            params: {
                                                id: rec.get('idElemento')
                                            },
                                            url: url_deleteElemento,
                                            success: function(response){
                                                var json = Ext.JSON.decode(response.responseText);
                                                Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                                    if (btn === 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            },
                                            failure: function(rec, op) {
                                                var json = Ext.JSON.decode(op.response.responseText);
                                                Ext.Msg.alert('Alerta ', json.mensaje);
                                            }
                                        });                                
                                    }});
                                }
                            }                                                                                  
                        ]
                    }
                ]
        });
    
    var formPanel = Ext.create('Ext.form.Panel',
        {            
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            
            items:
                [
                 {
                    xtype: 'fieldset',
                    autoHeight: true,
                    width: 980,
                    layout: 'column',
                    items:
                        [{
                            columnWidth: .40,
                            border: false,
                            items: [
                                {
                                xtype: 'combobox',
                                fieldLabel: '* Tipo:',
                                id: 'cmbTipo',
                                name: 'cmbTipo',
                                store:storeTipoElemento,
                                displayField: 'valor1',
                                valueField: 'valor1',    
                                queryMode: "remote",
                                emptyText: 'Seleccione Tipo',
                                labelStyle:'font-weight:bold',
                                width:350,
                                value: '',
                                editable : false,                                
                                listeners: {
                                    select: {fn: function(combo, value) {                                             
                                            var cmbTipo = Ext.getCmp('cmbTipo').value;                                            
                                            Ext.getCmp('cmbContenedor').value="";
                                            Ext.getCmp('cmbContenedor').setRawValue("");                                            
                                            cargarContenedores(data.idElemento,cmbTipo);                                            
                                            var intBand = verificarClase(cmbTipo);
                                            if(intBand==1)
                                            {
                                                Ext.getCmp('cmbClaseElemento').value="";
                                                Ext.getCmp('cmbClaseElemento').setRawValue("");
                                                Ext.getCmp('cmbClaseElemento').setVisible(true);
                                                Ext.getCmp('cmbClaseElemento').setDisabled(true); 
                                                intClase = 1;
                                            }
                                            else
                                            {
                                                Ext.getCmp('cmbClaseElemento').value="";
                                                Ext.getCmp('cmbClaseElemento').setRawValue("");
                                                Ext.getCmp('cmbClaseElemento').setVisible(false);
                                                intClase=0;
                                            }  

                                            Ext.getCmp('descripcionElemento').setValue = "";
                                            Ext.getCmp('descripcionElemento').setRawValue("");                                            

                                            Ext.getCmp('nombreElemento').setValue = "";
                                            Ext.getCmp('nombreElemento').setRawValue("");                                            
                                            
                                            Ext.getCmp('btnGuardarElemento').setDisabled(true);                                                    

                                            Ext.getCmp('txtMarca').value="";
                                            Ext.getCmp('txtMarca').setRawValue("");                                            

                                            Ext.getCmp('txtModelo').value="";
                                            Ext.getCmp('txtModelo').setRawValue("");                                            

                                            Ext.getCmp('serieElemento').setValue = "";
                                            Ext.getCmp('serieElemento').setRawValue("");

                                        }}
                                 } 
                            },                            
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Serie:',
                                id: 'serieElemento',
                                labelStyle:'font-weight:bold',     
                                width:350,
                                maxLength: 50,
                                enforceMaxLength: true,
                                allowBlank: false,
                                blankText: 'Campo Obligatorio.',
                                value:'',
                                listeners:{
                                    blur:function(srtSerie){ 
                                        var txtSerieElemento = srtSerie.getValue();                                        
                                        //var txtSerieElemento = Ext.getCmp('serieElemento').getValue();
                                        var strTipo = Ext.getCmp('cmbTipo').value;   
                                        if(txtSerieElemento.length==50)
                                        {
                                            Ext.Msg.alert('Mensaje ', 'Se permite el ingreso de máximo 50 caracteres.');
                                        }
                                        else if((txtSerieElemento!='')&&(strTipo!=''))
                                        {
                                            Ext.Ajax.request({
                                                url   :  urlValidarNafTelcos,
                                                method: 'post',
                                                params: {
                                                    'serieCpe'      :  txtSerieElemento,
                                                    'modelo'        : '',
                                                    'estado'        : 'PI',                                                
                                                    'bandera'       : 'InstalacionNodo',
                                                    'tipo'          : strTipo
                                                },
                                                success: function(response){
                                                    var respuesta   = response.responseText.split("|");                                                                                               
                                                    var status      = respuesta[0];
                                                    var mensaje     = respuesta[1].split(",");                                                
                                                    var descripcion = mensaje[0];
                                                    var marca       = mensaje[4];
                                                    var modelo      = mensaje[2];
                                                    var idmarca     = mensaje[5];
                                                    var idmodelo    = mensaje[6];                                                                                                
    
                                                    if (status === "OK") {
                                                                                                                                           
                                                        Ext.getCmp('descripcionElemento').setValue = descripcion;
                                                        Ext.getCmp('descripcionElemento').setRawValue(descripcion);
                                                        Ext.getCmp('descripcionElemento').setDisabled(true);
                                                        strDescripcionElementoInpt=descripcion;
                                                       
                                                        Ext.getCmp('nombreElemento').setValue = '';
                                                        Ext.getCmp('nombreElemento').setRawValue('');
                                                        Ext.getCmp('nombreElemento').setDisabled(true);
    
                                                        Ext.getCmp('txtMarca').value=marca;
                                                        Ext.getCmp('txtMarca').setRawValue(marca);
                                                        Ext.getCmp('txtMarca').setDisabled(true);
    
                                                        Ext.getCmp('txtModelo').value=modelo;
                                                        Ext.getCmp('txtModelo').setRawValue(modelo);
                                                        Ext.getCmp('txtModelo').setDisabled(true);                                                                                                       
    
                                                        Ext.getCmp('txtIdMarca').value=idmarca;
                                                        Ext.getCmp('txtIdMarca').setRawValue(idmarca);
                                                        Ext.getCmp('txtIdModelo').value=idmodelo;
                                                        Ext.getCmp('txtIdModelo').setRawValue(idmodelo);
    
                                                        Ext.getCmp('cmbContenedor').setDisabled(false); 
                                                        Ext.getCmp('cmbClaseElemento').setDisabled(false); 
                                                                                                            
                                                        Ext.getCmp('btnGuardarElemento').setDisabled(false);

                                                        Ext.getCmp('cmbClaseElemento').setVisible(true);
                                                        Ext.getCmp('cmbClaseElemento').value="";
                                                        Ext.getCmp('cmbClaseElemento').setRawValue("");
    
                                                        if((intClase==0) && (intContenedor==0))
                                                        {
                                                            var strTipo = Ext.getCmp('cmbTipo').value;
                                                            var intIdNodo = data.idElemento;
                                                            var strClase = '';
                                                            var strBandContenedor = 'NODO';
                                                            var strNombreAut = generarNombreAutomatico(strTipo, intIdNodo,strClase,strBandContenedor);
                                                            Ext.getCmp('nombreElemento').setValue = strNombreAut;
                                                            Ext.getCmp('nombreElemento').setRawValue(strNombreAut);
                                                            strNombreElementoInpt=strNombreAut;
                                                        }
                                                    }
                                                    else 
                                                    {
                                                        limpiarElemento();
                                                        Ext.Msg.alert('Mensaje ', mensaje);
                                                    }
                                                },
                                                failure: function(result){
                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                }
                                            })
                                        }
                                        else if((txtSerieElemento=="")&&(strTipo==""))
                                        {
                                            Ext.Msg.alert('Mensaje ', 'Seleccione el tipo e ingrese la serie.');
                                        }else if(txtSerieElemento==""){
                                            Ext.Msg.alert('Mensaje ', 'Ingrese la serie del elemento.');
                                        }else if(strTipo==""){
                                            Ext.Msg.alert('Mensaje ', 'Ingrese el tipo del elemento.');
                                        }else{
                                            Ext.Msg.alert('Mensaje ', 'Error inesperado del sistema.');
                                        }                                        
                                    }
                                }
                            },                         
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Marca:',
                                id: 'txtMarca',
                                labelStyle:'font-weight:bold',     
                                width:350,
                                value:'',
                                disabled:true
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Id Marca:',
                                id: 'txtIdMarca',
                                labelStyle:'font-weight:bold',     
                                width:350,
                                value:'',
                                hidden: true                             
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Bandera Edicion:',
                                id: 'txtBandEdicion',
                                name: 'txtBandEdicion',
                                labelStyle:'font-weight:bold',     
                                width:350,
                                value:'',
                                hidden: true                             
                            }]
                    },    
                    {
                        columnWidth: .60,
                        border: false,
                        items: [
                            {
                                xtype: 'combobox',
                                fieldLabel: '* Contenedor:',
                                id: 'cmbContenedor',
                                name: 'cmbContenedor',
                                store:storeElementosContenedores,
                                displayField: 'nombrePadreElemento',
                                valueField: 'idPadreElemento',
                                queryMode: "remote",
                                emptyText: 'Seleccione Contenedor',
                                labelStyle:'font-weight:bold',     
                                width:375,
                                value:'',
                                disabled:false,
                                editable : false,
                                hidden: true,
                                listeners: {
                                    select:{fn: function(combo, value){                                                                            
                                        if (intClase==0)
                                        {
                                            var strTipo = Ext.getCmp('cmbTipo').value;
                                            var intIdNodo = data.idElemento;
                                            var strClase = '';
                                            var strBandContenedor = Ext.getCmp('cmbContenedor').value;
                                            var strNombreAut = generarNombreAutomatico(strTipo, intIdNodo,strClase,strBandContenedor);
                                            Ext.getCmp('nombreElemento').setValue = strNombreAut;
                                            Ext.getCmp('nombreElemento').setRawValue(strNombreAut);
                                            strNombreElementoInpt=strNombreAut;
                                        }                                        
                                    }}
                                }
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '* Clase:',
                                id: 'cmbClaseElemento',
                                name: 'cmbClaseElemento',                                
                                emptyText: 'Seleccione tipo',
                                labelStyle:'font-weight:bold',
                                editable : false,     
                                width:375,
                                value:'',
                                store: [                                    
                                    ['PRIMARIO','Primario'],
                                    ['SECUNDARIO','Secundario']
                                ],
                                disabled:false,
                                hidden: true,
                                listeners: {
                                    select:{fn: function(combo, value){
                                        var strTipo = Ext.getCmp('cmbTipo').value;
                                        var intIdNodo = data.idElemento;
                                        var strClase = Ext.getCmp('cmbClaseElemento').value;
                                        var strBandContenedor = 'NODO';
                                        if (Ext.getCmp('cmbContenedor').isVisible())
                                        {
                                            intIdNodo  = Ext.getCmp('cmbContenedor').value;
                                            strBandContenedor = strTipo;
                                        }
                                        var strNombreAut = generarNombreAutomatico(strTipo, intIdNodo,strClase,strBandContenedor);
                                        Ext.getCmp('nombreElemento').setValue = strNombreAut;
                                        Ext.getCmp('nombreElemento').setRawValue(strNombreAut);
                                        strNombreElementoInpt=strNombreAut;
                                    }}
                                }
                            },                            
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Nombre:',
                                id: 'nombreElemento',
                                labelStyle:'font-weight:bold',     
                                width:375,
                                value:'',
                                disabled:true
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Descripción:',
                                id: 'descripcionElemento',
                                labelStyle:'font-weight:bold',     
                                width:375,
                                value:'',
                                disabled:true
                            },                            
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Modelo:',
                                id: 'txtModelo',
                                labelStyle:'font-weight:bold',     
                                width:375,
                                value:'',
                                disabled:true
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: '* Id Modelo:',
                                id: 'txtIdModelo',
                                labelStyle:'font-weight:bold',     
                                width:200,
                                value:'',
                                hidden: true
                            }]
                        }]
                },
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    width: 870,
                    items:
                        [grid]
                }
                ],
                            
                buttons:
                [
                    {
                        id:'btnGuardarElemento',
                        name:'btnGuardarElemento',
                        disabled:true,
                        text: 'Guardar',
                                    handler: function()
                                    {                                                                                                                    
                                        //Se valida mini formulario para crear elementos
                                        var cmbTipo             = Ext.getCmp('cmbTipo').value;
                                        var nombreElemento      = strNombreElementoInpt;                                        
                                        var serieElemento       = Ext.getCmp('serieElemento').value;
                                        var cmbContenedor       = Ext.getCmp('cmbContenedor').value;
                                        var strClaseSel         = Ext.getCmp('cmbClaseElemento').value;
                                        var idElemento          = data.idElemento;

                                        console.log('nombre capturado: ',strNombreElementoInpt,' descripcion: ',strDescripcionElementoInpt);

                                        if(cmbTipo == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe escoger el tipo de Elemento');
                                        }                                            
                                        else if(validarPreInstalacion(serieElemento) != "OK")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Serie incorrecta');
                                        }
                                        else if(serieElemento == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe ingresar el número de serie del elemento');
                                        } 
                                        else if(nombreElemento == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe ingresar el nombre del elemento');
                                        }                                      
                                        else if (Ext.getCmp('cmbContenedor').isVisible() && cmbContenedor == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe escoger el contenedor del elemento');
                                        }
                                        else if (Ext.getCmp('cmbClaseElemento').isVisible() && strClaseSel == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe escoger la clase del elemento');
                                        } 
                                        else
                                        {                                            
                                            var conn = requestMask('Grabando datos...');
                                            if (Ext.getCmp('cmbContenedor').isVisible())
                                            {
                                                idElemento  = cmbContenedor;
                                            }
                                            conn.request({
                                                    url: url_ingresoElemento,
                                                    method: 'post',
                                                    timeout: 300000,
                                                    params:
                                                        {
                                                            tipoElemento        : cmbTipo,
                                                            nombreElemento      : nombreElemento,                                                            
                                                            serieElemento       : serieElemento,
                                                            descripcionElemento : strDescripcionElementoInpt,
                                                            marcaElemento       : Ext.getCmp('txtIdMarca').value,
                                                            modeloElemento      : Ext.getCmp('txtIdModelo').value,
                                                            idElemento          : idElemento,
                                                            claseElemento       : Ext.getCmp('cmbClaseElemento').value                                                                                                    
                                                        },
                                                    success: function(response)
                                                    {
                                                        var text = response.responseText;
                                                        if(text=="OK"){
                                                            Ext.Msg.alert('Mensaje', 'Ingreso Exitoso.' );
                                                            limpiarElemento();
                                                            store.load();
                                                        }
                                                        else{
                                                            Ext.Msg.alert('Error',text );
                                                            limpiarElemento();
                                                        }
                                                    },
                                                    failure: function(result) {
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });
                                        }
                                    
                                    }
                    },
                    {
                        text: 'Salir',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Nuevos Elementos - '+data.nombreElemento,
            modal: true,
            width: 900,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();     
}//fin de funcion 

function requestMask(msg)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.getBody().mask(msg);
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.getBody().unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.getBody().unmask();
                },
                scope: this
            }
        }
    });
    
    return conn;
}

function limpiarElemento()
{
    Ext.getCmp('nombreElemento').value="";
    Ext.getCmp('nombreElemento').setRawValue("");
    Ext.getCmp('nombreElemento').setDisabled(true);    
    
    Ext.getCmp('serieElemento').value="";
    Ext.getCmp('serieElemento').setRawValue("");   
    
    Ext.getCmp('descripcionElemento').value="";
    Ext.getCmp('descripcionElemento').setRawValue("");
    Ext.getCmp('descripcionElemento').setDisabled(true);    
    
    Ext.getCmp('cmbTipo').value="";
    Ext.getCmp('cmbTipo').setRawValue("");
    
    Ext.getCmp('txtModelo').value="";
    Ext.getCmp('txtModelo').setRawValue("");
    Ext.getCmp('txtModelo').setDisabled(true); 
    
    Ext.getCmp('txtMarca').value="";
    Ext.getCmp('txtMarca').setRawValue(""); 
    Ext.getCmp('txtMarca').setDisabled(true); 
    
    Ext.getCmp('cmbContenedor').setVisible(false);
    Ext.getCmp('cmbContenedor').value="";
    Ext.getCmp('cmbContenedor').setRawValue("");
    
    Ext.getCmp('txtIdModelo').value="";
    Ext.getCmp('txtIdModelo').setRawValue("");

    Ext.getCmp('txtIdMarca').value="";
    Ext.getCmp('txtIdMarca').setRawValue("");
    
    Ext.getCmp('btnGuardarElemento').setDisabled(true);

    Ext.getCmp('cmbClaseElemento').setVisible(false);
    Ext.getCmp('cmbClaseElemento').value="";
    Ext.getCmp('cmbClaseElemento').setRawValue("");
    
}

// Obtener extensión
function getFileExt(sPTF, bDot) 
{
    if (!bDot)
    {
        bDot = false;
    }
    return sPTF.substr(sPTF.lastIndexOf('.') + ((!bDot) ? 1 : 0));
}

// Obtener contenedores de elementos
function presentarElementosContenidos(elemento,idNodo) 
{
    var param = elemento;
    var id = idNodo;
    Ext.Ajax.request({
                    url: "../../../administracion/tecnico/admi_tipo_elemento/getNivelesElementos",
                    method: 'post',
                    params: { param : param, id : id },
                    success: function(response){
                        var text     = JSON.parse(response.responseText).nivel;
                        var registro = JSON.parse(response.responseText).contador;
                                                
                        if(text !== null){
                            if (registro == 0)
                            {
                                Ext.Msg.alert('Advertencia', 'No existe elemento contenedor para el elemento seleccionado'); 
                                Ext.getCmp('cmbContenedor').setVisible(false);
                            }
                            else
                            {
                                Ext.getCmp('cmbContenedor').setVisible(true);
                                //Se llena el combo con los contenedores del elemento
                                storePadre.proxy.extraParams = {id:id ,valor2: text};
                                storePadre.load({params: {}});
                            }
                            
                        }
                        else
                        {
                            Ext.getCmp('cmbContenedor').setVisible(false);
                        }
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
                
    return;            
}

/*Mostrar ventana para registrar el periodo de mantenimiento de nodos radio.*/
let ingresarPeriodoMantenimiento = (data) =>
{
    var storeCiclosMantenimiento = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url : url_getCicloMantenimientoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name:'displayField', mapping:'displayField'},
                {name:'valueField', mapping:'valueField'}
            ],
        autoLoad: true
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 7,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 95,
        },
        layout: {
            type: 'table',
            columns: 1
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Información del Nodo',
                defaultType: 'textfield',
                defaults: {
                    width: 585
                },
                items: [
                    {
                        xtype: 'container',
                        autoWidth: true,
                        layout: {
                            type: 'table',
                            columns: 2,
                        },
                        items: [
                            {
                                xtype: 'textfield',
                                name: 'nombreElemento',
                                fieldLabel: 'Nombre Elemento',
                                displayField: data.nombreElemento,
                                value: data.nombreElemento,
                                readOnly: true,
                                width: '50%',
                                fieldCls: 'details-disabled'
                            },
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                width: '50%',
                                fieldCls: 'details-disabled'
                            },
                            {
                                xtype: 'textfield',
                                name: 'clase',
                                fieldLabel: 'Clase',
                                value: Ext.util.Format.stripTags(data.clase),
                                readOnly: true,
                                width: '50%',
                                fieldCls: 'details-disabled'
                            },
                            {
                                xtype: 'textfield',
                                name: 'tipoMedio',
                                fieldLabel: 'Tipo Medio',
                                value: data.tipoMedio.replace('<br />', ', '),
                                readOnly: true,
                                width: '50%',
                                fieldCls: 'details-disabled'
                            },
                            {
                                xtype: 'textareafield',
                                name: 'direccion',
                                fieldLabel: 'Dirección',
                                labelWidth: 28,
                                value: data.direccion,
                                readOnly: true,
                                width: '93%',
                                colspan: 5,
                                fieldCls: 'details-disabled'
                            },
                        ]
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Fecha de Próximo Mantenimiento',
                defaultType: 'textfield',
                defaults: {
                     width: 585
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table'
                        },
                        items: [
                            {
                                xtype: 'datefield',
                                id: 'strFechaProxMan',
                                name: 'strFechaProxMan',
                                fieldLabel: '<b class="required-dot">*</b> Fecha',
                                emptyText: 'DD-MM-AAAA',
                                value: data.arrayMantenimientoNodo.strProxMant ?
                                    Ext.Date.parse(data.arrayMantenimientoNodo.strProxMant, "d/m/Y") :
                                    null,
                                minValue: new Date(),
                                format: 'd/m/Y',
                                allowBlank: false,
                                inputWidth: 150,
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Periodo de Mantenimiento',
                defaultType: 'textfield',
                defaults: {
                    width: 585
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 2,
                        },
                        items: [
                            {
                                id: 'intPeriodoMeses',
                                name: 'intPeriodoMeses1',
                                fieldLabel: '<b class="required-dot">*</b> Periodo',
                                xtype: 'combobox',
                                emptyText: 'Seleccione un ciclo...',
                                typeAhead: true,
                                displayField:'displayField',
                                valueField: 'valueField',
                                queryMode: 'local',
                                loadingText: 'Buscando ...',
                                store: storeCiclosMantenimiento,
                                editable: false,
                                allowBlank: false,
                            },
                            {
                                xtype: 'label',
                                text: '(meses)',
                                margin: '0 0 0 10'
                            }
                        ]
                    }
                ]
            }
        ],
        buttons: [{
            text: 'Guardar',
            formBind: true,
            handler: function ()
            {

                let intPeriodoMeses = Ext.getCmp('intPeriodoMeses').getValue();
                let strFechaProxMan = Ext.Date.format(Ext.getCmp('strFechaProxMan').getValue(), "d/m/Y");

                if ((parseInt(intPeriodoMeses) && intPeriodoMeses >= 1) && strFechaProxMan)
                {
                    Ext.MessageBox.show({
                        title: 'Confirmación',
                        msg: `<div>
                              <i class="custom-icon-msg--question"></i>
                              <div class='custom-text-msg'>
                              <div style="margin-bottom:-.5rem;">¿Esta seguro(a) que desea guardar?</div>
                              <br/><b>&#10140; Periodo: </b><b style="color: dimgray">${intPeriodoMeses} meses</b>
                              <br/><b>&#10140; Fecha: </b><b style="color: dimgray">${strFechaProxMan}</b>
                              <br/><b>&#10140; Nodo: </b><b style="color: dimgray">${data.nombreElemento}</b>
                              <br/>
                              </div>
                              </div>`,
                        buttons: Ext.Msg.YESNO,
                        buttonText: { yes: 'Si', no: 'No' },
                        fn: function (btnValue)
                        {
                            if (btnValue === 'yes')
                            {
                                Ext.MessageBox.wait("Guardando...");
                                Ext.Ajax.request({
                                    url: url_ingresaInfoMantenimiento,
                                    method: 'post',
                                    timeout: 300000,
                                    params: {
                                        idElemento: data.idElemento,
                                        intPeriodo: intPeriodoMeses,
                                        strFechaProxMan: strFechaProxMan
                                    },
                                    success: function (response)
                                    {
                                        let objJsonResponse = JSON.parse(response.responseText);

                                        if (objJsonResponse.status == "OK")
                                        {

                                            Ext.Msg.show({
                                                title: 'Mensaje',
                                                msg: `<div>
                                                        <i class="custom-icon-msg--ok"></i>
                                                        <span class="custom-text-msg">${objJsonResponse.msg}</span>
                                                      </div>`,
                                                buttons: Ext.Msg.OK,
                                                fn: function (btn)
                                                {
                                                    if (btn == 'ok')
                                                    {
                                                        store.load();
                                                        win.destroy();
                                                    }
                                                }
                                            });

                                        }
                                        else
                                        {
                                            Ext.Msg.show({
                                                title: 'Error',
                                                msg: `<div>
                                                        <i class="custom-icon-msg--error"></i>
                                                        <span class="custom-text-msg">${objJsonResponse.msg}</span>
                                                      </div>`,
                                                buttons: Ext.Msg.OK,
                                                fn: function (btn)
                                                {
                                                    if (btn == 'ok')
                                                    {
                                                        store.load();
                                                        win.destroy();
                                                    }
                                                }
                                            });
                                        }
                                    },
                                    failure: function (result)
                                    {
                                        win.destroy();
                                        Ext.Msg.alert('Error ', 'Error: Ha ocurrido un error interno, por favor notificar a sistemas.', function ()
                                        {
                                            store.load();
                                        });
                                    }
                                });
                            }
                        }
                    });

                } else
                {
                    return false;
                }

            }
        }, {
            text: 'Cancelar',
            handler: function ()
            {
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Registro Información Mantenimiento Preventivo Nodos - Radio',
        width: 635,
        modal: true,
        items: [formPanel]
    }).show();

};


// **************** EMPLEADOS ******************
Ext.define('EmpleadosList', {
    extend: 'Ext.data.Model',
    fields: [
        {name:'idPersona', type:'int'},
        {name:'nombreCompleto', type:'string'}
    ]
});           
eval("var storeEmpleados = Ext.create('Ext.data.Store', { "+
    "  id: 'storeEmpleados', "+
    "  model: 'EmpleadosList', "+
    "  autoLoad: false, "+
    " proxy: { "+
        "   type: 'ajax',"+
    "    url : '../../../planificacion/planificar/asignar_responsable/getTecnicos',"+
        "   reader: {"+
    "        type: 'json',"+
        "       totalProperty: 'total',"+
    "        root: 'encontrados'"+
        "  }"+
    "  }"+
" });    ");


 //-------------- CLIENTES -------------------
 storeClientes = new Ext.data.Store({ 
    total: 'total',
    proxy: {
        type : 'ajax',
        url  : '/soporte/tareas/getClientes',
        reader:
        {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        },
        extraParams: {
                nombre : '',
                estado : 'Activo'
            }
    },
    fields:
    [
        {name:'id_cliente', mapping:'id_cliente'},
        {name:'cliente'   , mapping:'cliente'}
    ],
    autoLoad: false
});



function editarNombreElelemento(rec)
{   
    var band = true;
    var intBand = verificarClase(rec.get('tipoElemento'));

    if(intBand==1)
    {
        band=false;   
    }

    var panelDatos = Ext.create('Ext.form.Panel',{
        bodyPadding: 1,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            // The total column count must be specified here
            columns: 1
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype       : 'fieldset',
                title       : 'Elemento',
                labelStyle  : 'font-weight:bold', 
                autoHeight  : true,
                width       : 430,
                items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: '* Clase:',
                        id: 'cmbClaseElmt',
                        name: 'cmbClaseElmt',                                
                        emptyText: 'Seleccione clase',
                        labelStyle:'font-weight:bold',     
                        width:300,
                        value:'',
                        store: [                                    
                            ['PRIMARIO','Primario'],
                            ['SECUNDARIO','Secundario']
                        ],
                        hidden: band
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'txtNombreElemento',
                        fieldLabel : '* Nombre',
                        labelStyle:'font-weight:bold', 
                        value      : rec.get('nombreElemento'),
                        width      : 300
                    }
                ]
            }
        ],
        buttons: [
            {                
                    text: 'Confirmar',
                    formBind: true,
                    handler: function(){                        
                        var strNuevoNombre = Ext.getCmp('txtNombreElemento').value;
                        if (strNuevoNombre === "" || strNuevoNombre === null) {
                            Ext.Msg.alert('Alerta ','<b>No se puede registrar valores nulos..!!</b>');
                            return;
                        }
                        var srtClaseElemento = Ext.getCmp('cmbClaseElmt').value; 

                        Ext.Ajax.request({
                            url   :  url_editarNombreElemento,
                            async :  false,
                            method: 'post',
                            timeout: 300000,
                            params: {
                                tipoElemento     : rec.get('tipoElemento'),
                                nombreElemento   : strNuevoNombre,
                                serieElemento    : rec.get('serieFisica'),
                                idElemento       : rec.get('idElemento'),
                                claseElemento    : srtClaseElemento
                            },
                            success: function(response) {
                                var text = response.responseText;
                                if(response.responseText === "OK")
                                {                                       
                                    Ext.Msg.alert('Mensaje','Ingreso Exitoso.');
                                    store.load();
                                    win.destroy();    
                                }
                                else
                                {
                                    Ext.Msg.alert('Error',text );                         
                                    win.destroy();                     
                                }
                            },
                            
                            failure: function(result) {
                                win.destroy();
                                Ext.Msg.show({
                                    title   : 'Error',
                                    msg     : result.statusText,
                                    buttons : Ext.Msg.OK,
                                    icon    : Ext.MessageBox.ERROR
                                });
                            }
                        
                        });
                    }                
            },
            {
                text: 'Cancelar',
                handler: function(){                    
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Confirmar Servicio',
        modal: true,
        width: 350,
        closable: true,
        layout: 'fit',
        items: [panelDatos]
    }).show();  
}

var storeElementosClases = new Ext.data.Store({
    total: 'total',
    autoLoad:true,
    proxy: {
        type: 'ajax',
        url: '../../../administracion/tecnico/admi_tipo_elemento/getElementosConClase',
        reader: {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        }
    },
    fields:
        [
            {name:'valor1', mapping:'valor1'},
            {name:'valor1', mapping:'valor1'}
        ]
});


function verificarClase(strTipoElemento)
{   
    console.log('tipo de elemento a verificar: ',strTipoElemento);
    var arrayElementosClases;
    var intResultado=0;
    Ext.Ajax.request({
        url   :  '../../../administracion/tecnico/admi_tipo_elemento/getElementosConClase',
        async :  false,
        method: 'get',
        params: {},
        success: function(response){
            var respuesta  = response.responseText;
            arrayElementosClases = JSON.parse(respuesta);                            
        },
        failure: function(result){
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });


    for (elemento of arrayElementosClases)
    {
        console.log(elemento.tipoElemento);
        if(elemento.tipoElemento===strTipoElemento)
        {
            intResultado=1;
        }
    }
    
    return intResultado;
}

function generarNombreAutomatico(strTipo,intIdNodo,strClase,strContenedor)
{
    var strNombreAutomatico;

    Ext.Ajax.request({
        url   :  urlGeneraNombreAutomatico,
        async :  false,
        method: 'get',
        params: {
            'strTipoElemento'       :strTipo,
            'intIdNodoContenedor'   :intIdNodo,
            'strClase'              :strClase,
            'strContenedor'         :strContenedor
        },
        success: function(response){
            var respuesta   = response.responseText.split("|");                                                                                               
            var status      = respuesta[0];
            var mensaje     = respuesta[1];
            
            if(status=='OK')
            {
                strNombreAutomatico=mensaje;
            }
            else
            {
                strNombreAutomatico='Error al generar el nombre';
            }
        },
        failure: function(result){
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });

    return strNombreAutomatico;
}

function cargarContenedores(intIdNodo, strTipoElemento)
{
    var tipoElemento = strTipoElemento;
    var id = intIdNodo;
  
    Ext.Ajax.request
    ({
        url: "../../../administracion/tecnico/admi_tipo_elemento/getContenedoresNodo",
        method: 'post',
        params: { tipoElemento : tipoElemento, idNodo : id },
        success: function(response){
            var text     = JSON.parse(response.responseText).status;
            var total     = JSON.parse(response.responseText).total;
            console.log(text,total);
            if(text === 'Ok'){ 
                Ext.getCmp('cmbContenedor').setVisible(true);   
                Ext.getCmp('cmbContenedor').setDisabled(true);             
                storeElementosContenedores.proxy.extraParams = {tipoElemento:tipoElemento, idNodo:id};
                storeElementosContenedores.load({params: {}});                
            }else if(text === 'NC'){
                Ext.getCmp('cmbContenedor').setVisible(false);
            }
            else{
                Ext.Msg.alert('Advertencia', 'No existe elemento contenedor para el elemento seleccionado'); 
                Ext.getCmp('cmbContenedor').setVisible(false);
            }
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });

    return;    
}

var storeElementosContenedores= new Ext.data.Store
({
    total: 'total',
    autoLoad:false,
    proxy: 
    {
        type: 'ajax',
        url: '../../../administracion/tecnico/admi_tipo_elemento/getContenedoresNodo',
        reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
    },
    fields:
        [
            {name: 'nombrePadreElemento', mapping: 'nombrePadreElemento'},
            {name: 'idPadreElemento', mapping: 'idPadreElemento'}
        ]
});

function validarPreInstalacion(strSerieInput)
{    
    var strRespuesta;
    Ext.Ajax.request({
        url   :  url_validarSeriePreInstalacion,
        async :  false,
        method: 'get',
        params: {
            'serieElemento'       :strSerieInput
        },
        success: function(response){
            if(response.responseText === "OK")
            {
                strRespuesta="OK";
            }
            else
            {
                strRespuesta="ERROR";
            }
        },
        failure: function(result){
            Ext.Msg.alert('Error ','Error Inesperado del sistema');
        }
    });
    
    return strRespuesta;
}

function retornarClase(strNombre,strTipoInpt)
{
    var strClaseRetorno; 
    var strTipo;

    Ext.Ajax.request({
        url   :  url_validarTipoElemento,
        async :  false,
        method: 'get',
        params: {
            'tipoElemento' :strTipoInpt
        },
        success: function(response){
            console.log('Tipo de elemento retornado: ',response.responseText);

            if(response.responseText === "NA")
            {
                strTipo='NA';
            }
            else
            {
                strTipo=response.responseText;
            }
        },
        failure: function(result){
            Ext.Msg.alert('Error ','Error Inesperado del sistema');
        }
    });

    if(strTipo!='NA')
    {
        var intInicio = strTipo.length+1;
        var intFin = strTipo.length+2;
        var strClase = strNombre.substring(intInicio, intFin);

        if(strClase.toUpperCase()==='A')
        {
            strClaseRetorno='Primario';
        }
        else if(strClase.toUpperCase()==='B')
        {
            strClaseRetorno='Secundario';
        }
        else
        {
            strClaseRetorno='';
        }
    }
    
    return strClaseRetorno;
}