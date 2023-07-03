
var winAsignacionIndividualInsp;
var winAgregarAsignadoInspeccion;

var connProgramarInspecciones = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando programación, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

function showProgramarInspecciones(idSolicitudInspeccion, login, cliente, tipo, estadoSolicitud)
{
    var connObtenerInformacionInspeccion = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Consultando asignados, Por favor espere!!',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
    connObtenerInformacionInspeccion.request({
        url: urlGetInfoSolicitudInsp,
        method: 'post',
        params:
            {
                idSolicitud: idSolicitudInspeccion,
                descSolicitud: 'SOLICITUD INSPECCION',
                login: login
            },
        success: function(response) {

            Ext.getCmp('loginInsp').setValue(login);
            Ext.getCmp('clienteInsp').setValue(cliente);

            var text = Ext.decode(response.responseText);

            var objData = JSON.parse(text);

            var datos = objData.arrayResultado;

            var ciudadInspeccion = '';

            for (var ind = 0; ind < datos.length; ++ind)
            {
                if (datos[ind].descripcionCaract == 'NOMBRE_PROYECTO_INSPECCION')
                {
                    Ext.getCmp('propuesta').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'NUMERO_COTIZACION_INSPECCION')
                {
                    Ext.getCmp('cotizacion').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'LONGITUD_INSPECCION')
                {
                    Ext.getCmp('longitudInsp').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'LATITUD_INSPECCION')
                {
                    Ext.getCmp('latitudInsp').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'DIRECCION_INSPECCION')
                {
                    Ext.getCmp('direccionInsp').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'CIUDAD_INSPECCION')
                {
                    ciudadInspeccion = datos[ind].valorDetSolCaract+', ';
                }
                if (datos[ind].descripcionCaract == 'NOMBRES_CONTACTO_INSPECCION')
                {
                    Ext.getCmp('contactoInsp').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'TELEFONO_CONTACTO_INSPECCION')
                {
                    Ext.getCmp('telefonoInsp').setValue(datos[ind].valorDetSolCaract);
                }
                if (datos[ind].descripcionCaract == 'NOMBRE_CLIENTE_INSPECCION')
                {
                    Ext.getCmp('clienteInsp').setValue(datos[ind].valorDetSolCaract);
                }
            }
            Ext.getCmp('direccionInsp').setValue(ciudadInspeccion+Ext.getCmp('direccionInsp').getValue());

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

    Ext.define('detalleInspecciones', {
        extend: 'Ext.data.Model',
        fields: [
            { name: 'idAsignado', type: 'integer' },
            { name: 'nombreAsignado', type: 'string' },
            { name: 'tipoAsignado', type: 'string' },
            { name: 'estado', type: 'string' },
            { name: 'estadoTarea', type: 'string' },
            { name: 'observacion', type: 'string' },
            { name: 'numeroTarea', type: 'integer' },
            { name: 'fechaInicio', type: 'string' },
            { name: 'fechaFin', type: 'string' },
            { name: 'origen', type: 'string' },
            { name: 'idSolicitud', type: 'integer' },
            { name: 'idSolPlanif', type: 'integer' }
            ]
        });

    storeCuadrillasInspecciones = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        model: 'detalleInspecciones',
        proxy: {
            type: 'ajax',
            url: urlDetalleAsignadosSolInsp,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'inspecciones'
            },
            extraParams: 
            {
                idSolicitud: idSolicitudInspeccion
            }
        },
        fields:
            [               
                { name: 'idAsignado', mapping: 'idAsignado' },
                { name: 'nombreAsignado', mapping: 'nombreAsignado' },
                { name: 'tipoAsignado', mapping: 'tipoAsignado' },
                { name: 'estado', mapping: 'estado' },
                { name: 'estadoTarea', mapping: 'estadoTarea' },
                { name: 'observacion', mapping: 'observacion' },
                { name: 'numeroTarea', mapping: 'numeroTarea' },
                { name: 'fechaInicio', mapping: 'fechaInicio' },
                { name: 'fechaFin', mapping: 'fechaFin' },
                { name: 'origen', mapping: 'origen' },
                { name: 'idSolicitud', mapping: 'idSolicitud' },
                { name: 'idSolPlanif', mapping: 'idSolPlanif' }
            ]
    });

    gridInspecciones = Ext.create('Ext.grid.Panel', {
        width: 800,
        height: 230,
        store: storeCuadrillasInspecciones,
        dockedItems: [
            {
                xtype: 'toolbar',
                id: 'tbGridInsp',
                name: 'tbGridInsp',
                items: [{
                        text: 'Programar',
                        id: 'agregarInspeccion',
                        name: 'agregarInspeccion',
                        tooltip: 'Agrega un asignado',
                        iconCls: 'programarCls',
                        handler: function() {
                            var permiso = $("#programarInspeccion");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                Ext.Msg.show({
                                    title: 'Mensaje del sistema',
                                    msg: "No tiene permisos para realizar esta acción" ,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.ERROR
                                });
                            }
                            else
                            {
                                agregarAsignadoInspeccion("programar", null, login);
                            }
                            
                        }
                    }
                ]
            }

        ],
        loadMask: true,
        frame: false,
        columns: [
            {
                header: 'idSolicitud',
                dataIndex: 'idSolicitud',
                hidden: true
            },
            {
                header: 'idSolPlanif',
                dataIndex: 'idSolPlanif',
                hidden: true
            },
            {
                header: 'idAsignado',
                dataIndex: 'idAsignado',
                hidden: true
            },
            {
                header: 'tipoAsignado',
                dataIndex: 'tipoAsignado',
                hidden: true
            },
            {
                header: 'Asignado',
                dataIndex: 'nombreAsignado',
                hideable: false,
                width: 200
            },
            {
                header: 'Tarea',
                dataIndex: 'numeroTarea',
                hideable: false,
                width: 70
            },
            {
                header: 'Estado Tarea',
                dataIndex: 'estadoTarea',
                hideable: false,
                width: 90
            },
            {
                header: 'Observación',
                dataIndex: 'observacion',
                hideable: false,
                width: 90
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                hideable: false,
                hidden: true,
                width: 110
            },
            {
                header: 'Fecha Inicio',
                dataIndex: 'fechaInicio',
                hideable: false,
                width: 100
            },
            {
                header: 'Fecha Fin',
                dataIndex: 'fechaFin',
                hideable: false,
                width: 100
            },
            {
                header: '<i class="fa fa-cogs" aria-hidden="true"></i>',
                xtype: 'actioncolumn',
                width: 220,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if (rec.get('origen') == "bd")
                            {
                                return 'icon-invisible';
                            }
                            else
                            {
                                return 'button-grid-delete';
                            }
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            storeCuadrillasInspecciones.remove(grid.getStore().getAt(rowIndex));
                            console.log("borrar asignado");
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                                return 'button-grid-verDetalle';
                        },
                        tooltip: 'Ver Historial',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            showHistorialInspeccion(grid.getStore().getAt(rowIndex).data.idSolPlanif);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if( tipo == 'gestionar' && 
                            (rec.get('estado') == "Planificada" || rec.get('estado') == "AsignadoTarea"
                            || (rec.get('estado') == "Asignada" && prefijoEmpresa == "MD")  ))
                            {
                                return 'button-grid-Retime';
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Replanificar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso = $("#replanificarInspeccion");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                Ext.Msg.show({
                                    title: 'Mensaje del sistema',
                                    msg: "No tiene permisos para realizar esta acción" ,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.ERROR
                                });
                            }
                            else
                            {
                                agregarAsignadoInspeccion("replanificar",grid.getStore().getAt(rowIndex), login);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {

                            if( tipo == 'gestionar' && (rec.get('estado') == "Replanificada" || rec.get('estado') == "Detenido"))
                            {
                                return 'button-grid-Time2';
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Programar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso = $("#programarInspeccion");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                Ext.Msg.show({
                                    title: 'Mensaje del sistema',
                                    msg: "No tiene permisos para realizar esta acción" ,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.ERROR
                                });
                            }
                            else
                            {
                                agregarAsignadoInspeccion("programarExiste", grid.getStore().getAt(rowIndex), login);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if( tipo == 'gestionar'&& 
                            (rec.get('estado') == "PrePlanificada" ||
                            rec.get('estado') == "Planificada" ||
                            rec.get('estado') == "Replanificada" ||
                            rec.get('estado') == "AsignadoTarea" ||
                            ( rec.get('estado') != "Detenido" && rec.get('estado') != "Asignada" && prefijoEmpresa == "MD") ) )
                            {
                                return 'button-grid-Pausa';
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Detener',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso = $("#detenerInspeccion");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                Ext.Msg.show({
                                    title: 'Mensaje del sistema',
                                    msg: "No tiene permisos para realizar esta acción" ,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.ERROR
                                });
                            }
                            else
                            {
                                agregarAsignadoInspeccion("detener",grid.getStore().getAt(rowIndex), login);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {

                            if( tipo == 'gestionar'&& 
                            (rec.get('estado') == "PrePlanificada" ||
                            rec.get('estado') == "Planificada" ||
                            rec.get('estado') == "Replanificada" ||
                            rec.get('estado') == "Detenido" ||
                            rec.get('estado') == "AsignadoTarea" ||
                            rec.get('estado') == "Planificada" ||
                            (rec.get('estado') != "Asignada" && prefijoEmpresa == "MD") ) )
                            {
                                return 'button-grid-Anular';
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Anular',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso = $("#anularInspeccion");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                Ext.Msg.show({
                                    title: 'Mensaje del sistema',
                                    msg: "No tiene permisos para realizar esta acción" ,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.ERROR
                                });
                            }
                            else
                            {
                                agregarAsignadoInspeccion("anular",grid.getStore().getAt(rowIndex), login);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            if( tipo == 'gestionar'&& 
                            (rec.get('estado') == "PrePlanificada" ||
                            rec.get('estado') == "Planificada" ||
                            rec.get('estado') == "Replanificada" ||
                            rec.get('estado') == "Detenido" ||
                            rec.get('estado') == "AsignadoTarea" ||
                            rec.get('estado') == "Planificada" ||
                            (rec.get('estado') != "Asignada" && prefijoEmpresa == "MD") ) )
                            {
                                return 'button-grid-BigDelete';
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Rechazar',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var permiso = $("#rechazarInspeccion");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                Ext.Msg.show({
                                    title: 'Mensaje del sistema',
                                    msg: "No tiene permisos para realizar esta acción" ,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.ERROR
                                });
                            }
                            else
                            {
                                agregarAsignadoInspeccion("rechazar",grid.getStore().getAt(rowIndex), login);
                            }
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
            }
        },
        renderTo: Ext.getBody()
    });



    fieldSetDatosInspeccion1Insp =Ext.create('Ext.form.FieldSet',
    {
        xtype:'fieldset',
        columnWidth: 1,
        
        title: '',
        collapsible: false,
        defaultType: 'textfield',
        //defaults: {anchor: '100%'},
        layout: 'anchor',
        items :[
            {
                xtype: 'textfield',
                fieldLabel: 'Propuesta',
                readOnly:true,
                name: 'propuesta',
                id: 'propuesta',
                labelWidth: 45,
                width:'100%'
            },
            
            
            
            {
                xtype: 'textfield',
                fieldLabel: 'Dirección',
                name: 'direccionInsp',
                readOnly:true,
                id : 'direccionInsp',
                labelWidth: 45,
                width:'100%'
            },


            {
                xtype: 'textfield',
                fieldLabel: '',
                readOnly:true,
                name: 'cotizacion',
                fieldStyle:"visibility: hidden",
                id: 'cotizacion',
                labelWidth: 45,
                width:'100%'
            }
        ]
    });

    fieldSetDatosInspeccion2Insp = Ext.create('Ext.form.FieldSet',
    {
        xtype:'fieldset',
        columnWidth: 0.5,
        title: '',
        collapsible: false,
        defaultType: 'textfield',
        //defaults: {anchor: '100%'},
        layout: 'anchor',
        items :[
            {
                xtype: 'textfield',
                fieldLabel: 'Cliente',
                name: 'clienteInsp',
                readOnly:true,
                id: 'clienteInsp',
                labelWidth: 35,
                width:'100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Login',
                name: 'loginInsp',
                readOnly:true,
                id : 'loginInsp',
                labelWidth: 35,
                width:'100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Telefono',
                readOnly:true,
                name: 'telefonoInsp',
                id : 'telefonoInsp',
                labelWidth: 35,
                width:'100%'
            }
        ]
    });

    fieldSetDatosInspeccion3Insp = Ext.create('Ext.form.FieldSet',
    {
        xtype:'fieldset',
        columnWidth: 0.2,
        collapsible: false,
        defaultType: 'textfield',
        //defaults: {anchor: '100%'},
        layout: 'anchor',
        items :[
            {
                xtype: 'textfield',
                fieldLabel: 'Latitud',
                name: 'latitudInsp',
                id : 'latitudInsp',
                readOnly:true,
                labelWidth: 50,
                width:'60%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Longitud',
                name: 'longitudInsp',
                id : 'longitudInsp',
                readOnly:true,
                labelWidth: 50,
                width:'60%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Contacto',
                name: 'contactoInsp',
                id: 'contactoInsp',
                readOnly:true,
                labelWidth: 50,
                width:'60%'
            }
        ]
    });

    fieldSetDatosAsignadosInsp =Ext.create('Ext.form.FieldSet',
    {
        xtype:'fieldset',
        columnWidth: 2,
        //visible:false,
        title: 'Detalle Inspecciones',
        collapsible: false,
        colspan: 2,
        defaultType: 'textfield',
        defaults: {anchor: '100%'},
        layout: 'anchor',
        items :[
            gridInspecciones,
            {
                xtype: 'textfield',
                fieldLabel: 'Asignados',
                name: 'asignados',
                id: 'asignados',
                hidden: true,
                value:""
            }
        ]
    });

    //fieldSetDatosInfoClienteInsp = fieldSetDatosClienteInsp;
    formPanelAsignacionIndividualInsp = Ext.create('Ext.form.Panel', 
    {
        renderTo: Ext.getBody(),
        bodyPadding: 3,
        width: 920,
        url: '',

        layout: {
            type: 'table',
            columns: 1
        },
        items: [
            {
                xtype:'fieldset',
                title: 'Datos Inspección',
                
                columnWidth: 2,
                collapsible: false,
                defaultType: 'textfield',
                defaults: {anchor: '100%'},
                layout: 'anchor',
                items :[
                    {
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        border: false,
                        collapsible: false,
                        defaultType: 'textfield',
                        defaults: {anchor: '100%'},
                        layout: {
                            type: 'table',
                            columns: 4
                        },
                        items :[
                            fieldSetDatosInspeccion1Insp,
                            fieldSetDatosInspeccion2Insp,
                            fieldSetDatosInspeccion3Insp


                        ]
                    }
                ]
            },
            fieldSetDatosAsignadosInsp

        ],
        buttons: 
        [
            {
                text: 'Guardar',
                id: 'guardarProgramarInspeccion',
                name: 'guardarProgramarInspeccion',
                handler: function() {
                    document.getElementById("asignados").value = "";
                    for (var i = 0; i < gridInspecciones.getStore().getCount(); i++)
                    {
                        document.getElementById("asignados").value = document.getElementById("asignados").value + 
                        gridInspecciones.getStore().getAt(i).data.idAsignado.toString()+"*"+
                        gridInspecciones.getStore().getAt(i).data.tipoAsignado+"*"+
                        gridInspecciones.getStore().getAt(i).data.fechaInicio+"*"+
                        gridInspecciones.getStore().getAt(i).data.fechaFin+"*"+
                        gridInspecciones.getStore().getAt(i).data.origen+"*"+
                        idSolicitudInspeccion+"*"+
                        login+"*"+
                        ""+"*"+
                        gridInspecciones.getStore().getAt(i).data.observacion
                        +"|";
                    }

                    if (document.getElementById("asignados").value == "")
                    {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: 'No se puede programar las inspecciones, debe ingresar los asignados!',
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                    else
                    {
                        connProgramarInspecciones.request({
                            url: urlProgramarInspecciones,
                            method: 'post',
                            params:
                                {
                                    asignados: document.getElementById("asignados").value
                                },
                            success: function(response) {
                                    var text = response.responseText;
                                    var objResponse =  JSON.parse(text);
                                    if (objResponse.status == "ok")
                                    {
                                        winAsignacionIndividualInsp.close();
                                        winAsignacionIndividualInsp.destroy();
                                        var ven = Ext.Msg.show({
                                            title:'Mensaje del sistema',
                                            msg: objResponse.mensaje,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.Msg.INFO
                                        });
                                        Ext.defer(function() {
                                            ven.toFront();
                                        }, 50);
                                        store.load();  
                                    }
                                    else 
                                    {
                                        var mm = Ext.Msg.show({
                                                        title:'Mensaje del sistema',
                                                        msg: objResponse.mensaje,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.Msg.ERROR
                                                    });
                                        Ext.defer(function() {
                                            mm.toFront();
                                        }, 50);
                                    }
                            },
                            failure: function(result) {
                                console.log("Resultado");
                                console.log(result);
                                Ext.Msg.show({
                                    title: 'Error',
                                    msg: 'Ocurrio un error al ejecutar el proceso',
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });
                    }
                }
            },
            {
                text:'Cerrar',
                handler: function()
                {
                    winAsignacionIndividualInsp.close();
                    winAsignacionIndividualInsp.destroy();
                }
            }
        ]
    });

    //Ocultar botón de agregar inspección
    //if (tipo == "gestionar")
    if (estadoSolicitud != 'Finalizada' && estadoSolicitud != 'Anulada' && estadoSolicitud != 'Rechazada')
    {
        //Ext.getCmp('tbGridInsp').hide();
        //Ext.getCmp('guardarProgramarInspeccion').hide();
        Ext.getCmp('tbGridInsp').show();
        Ext.getCmp('guardarProgramarInspeccion').show();
    }
    //else if (tipo == "programar")
    else
    {
        Ext.getCmp('tbGridInsp').hide();
        Ext.getCmp('guardarProgramarInspeccion').hide();
        //Ext.getCmp('tbGridInsp').show();
        //Ext.getCmp('guardarProgramarInspeccion').show();
    }

    winAsignacionIndividualInsp = Ext.widget('window', {
        title: 'Formulario de Asignación de Inspección',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: false,
        height: 550,
        width: 950,
        items: [formPanelAsignacionIndividualInsp]
    });

    winAsignacionIndividualInsp.show();
}


function agregarAsignadoInspeccion(tipoGestion,data, loginInspeccion)
{
    var connGestionarInspeccion = new Ext.data.Connection({
        timeout: 120000,
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Grabando Proceso, Por favor espere!!',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    var tituloVentana = "Formulario de agregar Asignado a Inspección";
    var altoVentana   = 400;
    var anchoVentana  = 455;
    if (data != null)
    {
        var datos                            = data.data;
        var idSol                            = datos.idSolicitud;
        var idSolPlanif                      = datos.idSolPlanif;
        var txtInformacionFechaPlanificacion = Ext.create('Ext.Component', { html: "<br>" });
        var fechaPlanificacionInicio         = datos.fechaInicio;
        fechaPlanificacionInicio             = fechaPlanificacionInicio.split(" ");
        var horaInicio                       = fechaPlanificacionInicio[1];
        var fechaPlanificacionFin            = datos.fechaFin;
        fechaPlanificacionFin                = fechaPlanificacionFin.split(" ");
        var horaFin                          = fechaPlanificacionFin[1];
    }
    if (tipoGestion == "replanificar")
    {
        txtInformacionFechaPlanificacion = Ext.create('Ext.form.TextField', {
            fieldLabel: 'Fecha Hora Inicio - Fin',
            padding: '2 2 2 10',
            labelWidth: 160,
            value: fechaPlanificacionInicio[0] + " " + horaInicio + " - " + horaFin,
            allowBlank: false,
            readOnly: true,
            anchor: '100%',
        });
    }

    cmbMotivosRePlanifInsp = Ext.create('Ext.data.comboMotivosReplanificarInspeccion', {
        id: 'cmb_motivo_replanif_insp',
        name: 'cmb_motivo_replanif_insp',
        fieldLabel: '* Motivo',
        anchor:'100%',
        labelStyle: "color:red;"});

    cmbMotivosDetenerInsp = Ext.create('Ext.data.comboMotivosDetenerInspeccion', {
            id: 'cmb_motivo_detener_insp',
            name: 'cmb_motivo_detener_insp',
            fieldLabel: '* Motivo',
            anchor:'100%',
            labelStyle: "color:red;"});

    cmbMotivosRechazarInsp = Ext.create('Ext.data.comboMotivosRechazarInspeccion', {
        id: 'cmb_motivo_rechazar_insp',
        name: 'cmb_motivo_rechazar_insp',
        fieldLabel: '* Motivo',
        anchor:'100%',
        labelStyle: "color:red;"});

    cmbMotivosAnularInsp = Ext.create('Ext.data.comboMotivosAnularInspeccion', {
        id: 'cmb_motivo_anular_insp',
        name: 'cmb_motivo_anular_insp',
        fieldLabel: '* Motivo',
        anchor:'100%',
        labelStyle: "color:red;"});
    
        
    txtObservacionProgramarfInsp = Ext.create('Ext.form.field.TextArea',
    {
        fieldLabel: '* Observación',
        name: 'txt_observacion_programar_insp',
        id: 'txt_observacion_programar_insp',
        value: "",
        allowBlank: false,
        anchor:'100%',
        height: 50
    });


    txtObservacionReplanifInsp = Ext.create('Ext.form.field.TextArea',
    {
        fieldLabel: '* Observación',
        name: 'txt_observacion_replanif_insp',
        id: 'txt_observacion_replanif_insp',
        value: "",
        allowBlank: false,
        anchor:'100%',
        height: 50
    });
    txtObservacionDetenerInsp = Ext.create('Ext.form.field.TextArea',
    {
        fieldLabel: '* Observación',
        name: 'txt_observacion_detener_insp',
        id: 'txt_observacion_detener_insp',
        value: "",
        allowBlank: false,
        anchor:'100%',
        height: 50
    });
    txtObservacionRechazarInsp = Ext.create('Ext.form.field.TextArea',
    {
        fieldLabel: '* Observación',
        name: 'txt_observacion_rechazar_insp',
        id: 'txt_observacion_rechazar_insp',
        value: "",
        allowBlank: false,
        anchor:'100%',
        height: 50
    });
    txtObservacionAnularInsp = Ext.create('Ext.form.field.TextArea',
    {
        fieldLabel: '* Observación',
        name: 'txt_observacion_anular_insp',
        id: 'txt_observacion_anular_insp',
        value: "",
        allowBlank: false,
        anchor:'100%',
        height: 50
    });
    fieldSetReplanifInsp = Ext.create('Ext.form.FieldSet',
    {
        columnWidth: 0.5,
        colspan: 2,
        title: 'Datos Adicionales',
        collapsible: false,
        defaultType: 'textfield',
        defaults: {
            width: '400px'
        },
        layout: 'anchor',
        items :[
            cmbMotivosRePlanifInsp,
            txtObservacionReplanifInsp,
        ]
    });

    fieldSetDetenerInsp = Ext.create('Ext.form.FieldSet',
    {
        columnWidth: 0.5,
        colspan: 2,
        title: 'Datos Detener Inspección',
        collapsible: false,
        defaultType: 'textfield',
        defaults: {
            width: '400px'
        },
        layout: 'anchor',
        items :[
            cmbMotivosDetenerInsp,
            txtObservacionDetenerInsp,
        ]
    });

    fieldSetRechazarInsp = Ext.create('Ext.form.FieldSet',
    {
        columnWidth: 0.5,
        colspan: 2,
        title: 'Datos Rechazar Inspección',
        collapsible: false,
        defaultType: 'textfield',
        defaults: {
            width: '400px'
        },
        layout: 'anchor',
        items :[
            cmbMotivosRechazarInsp,
            txtObservacionRechazarInsp,
        ]
    });

    fieldSetAnularInsp = Ext.create('Ext.form.FieldSet',
    {
        columnWidth: 0.5,
        colspan: 2,
        title: 'Datos Anular Inspección',
        collapsible: false,
        defaultType: 'textfield',
        defaults: {
            width: '400px'
        },
        layout: 'anchor',
        items :[
            cmbMotivosAnularInsp,
            txtObservacionAnularInsp,
        ]
    });

    Ext.define('EmpleadosListInspeccion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_empleado', type: 'int'},
            {name: 'nombre_empleado', type: 'string'}
        ]
    });
    var storeEmpleadosInspeccion= Ext.create('Ext.data.Store', { 
          id: 'storeEmpleadosInspeccion', 
          model: 'EmpleadosListInspeccion', 
          autoLoad: false, 
         proxy: { 
           type: 'ajax',
            url : '../../planificar/asignar_responsable/getEmpleados',
           reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
          }
        });    
    comboEmpleadosInspeccion = new Ext.form.ComboBox({
        id: 'cmb_empleado_inspeccion',
        name: 'cmb_empleado_inspeccion',
        fieldLabel: "Empleados",
        anchor: '100%',
        queryMode: 'remote',
        width: 200,
        emptyText: 'Seleccione Empleado',
        store: storeEmpleadosInspeccion,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        layout: 'anchor',
        disabled: false,
        hidden: true,
        listeners: {
            select: function(combo) {
                Ext.getCmp('idPersonaAsignado').setValue(combo.getValue());
            }
        }
    });
    Ext.define('CuadrillasListInspeccion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_cuadrilla', type: 'int'},
            {name: 'nombre_cuadrilla', type: 'string'}
        ]
    });
    var storeCuadrillasInspeccion= Ext.create('Ext.data.Store', { 
          id: 'storeCuadrillasInspeccion', 
          model: 'CuadrillasListInspeccion', 
          autoLoad: false, 
          proxy: 
          { 
               type: 'ajax',
               url : '../../planificar/asignar_responsable/getCuadrillas',
               reader: {
                   type: 'json',
                   totalProperty: 'total',
                   root: 'encontrados'
               }
          }
    });   
    comboCuadrillasInspeccion = new Ext.form.ComboBox({
        id          : 'cmb_cuadrilla_inspeccion',
        name        : 'cmb_cuadrilla_inspeccion',
        fieldLabel  : "Cuadrilla",
        anchor      : '100%',
        queryMode   : 'remote',
        width       : 200,
        emptyText   : 'Seleccione Cuadrilla',
        store       : storeCuadrillasInspeccion,
        displayField: 'nombre_cuadrilla',
        valueField  : 'id_cuadrilla',
        layout      : 'anchor',
        disabled    : false,
        listeners   : {
            select: function(combo) {
                connAsignarResponsable2.request({
                    url: url_asignar_responsable,
                    method: 'post',
                    params:
                        {
                            cuadrillaId: combo.getValue()
                        },
                    success: function(response) {
                        var text = Ext.decode(response.responseText);

                        if (text.existeTablet == "S")
                        {
                            cuadrillaAsignada = "S";
                            Ext.getCmp('lider_cuadrilla_inspeccion').setValue(text.nombres);
                            Ext.getCmp('idPersonaAsignado').setValue(text.idPersona);
                            Ext.getCmp('idPerEmpRolAsignado').setValue(text.idPersonaEmpresaRol);
                        } 
                        else
                        {
                            var alerta = Ext.Msg.alert("Alerta", "La cuadrilla " + text.nombreCuadrilla + 
                                                       " no posee tablet asignada. Realice la asignación de \n\
                                                         tablet correspondiente o seleccione otra cuadrilla.");
                            Ext.defer(function() {
                                alerta.toFront();
                            }, 50);
                            cuadrillaAsignada = "N";
                            Ext.getCmp('cmb_cuadrilla_inspeccion').setValue("");
                            Ext.getCmp('lider_cuadrilla_inspeccion').setValue("");
                            Ext.getCmp('idPersonaAsignado').setValue("");
                            Ext.getCmp('idPerEmpRolAsignado').setValue("");
                            console.log("NO EXISTE TABLET");
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
        }
    });

    feIni = new Date();
    hoIni = "00:00";
    hoFin = "00:30";

    DTFechaProgramacionInspeccion = Ext.create('Ext.form.DateField', {
        id         : 'fecha_inspeccion',
        name       : 'fecha_inspeccion',
        fieldLabel : '* Fecha       ',
        minValue   : new Date(),
        format     : 'd-m-Y',
        value      : feIni,
        labelStyle : "color:red;",
        labelAlign : 'top',
        width      : 160,
        anchor     : '100%',
        padding    :  '2 10 2 1'
    });
    THoraInicioInspeccion = Ext.create('Ext.form.TimeField', {
        fieldLabel : '* Hora Inicio',
        format     : 'H:i',
        id         : 'ho_inicio_inspeccion',
        name       : 'ho_inicio_inspeccion',
        minValue   : '00:01 AM',
        maxValue   : '22:59 PM',
        labelAlign : 'top',
        width      : 100,
        increment  : 30,
        value      : hoIni,
        editable   : false,
        labelStyle : "color:red;",
        padding    :  '2 10 2 1',
        listeners: {
            select: {fn: function(valorTime, value) {

                    var valueEscogido           = valorTime.getValue();
                    var valueEscogido2          = new Date(valueEscogido);
                    var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 30);
                    var horaTotal               = new Date(valueEscogidoAumentMili);
                    
                    var h   = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                    var m   = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                    
                    var horasTotalFormat = h + ":" + m;

                    Ext.getCmp('ho_fin_inspeccion' + '').setMinValue(horaTotal);
                    $("input[name='ho_fin_inspeccion" + "" + "']'").val(horasTotalFormat);
                }}
        }
    });
    THoraFinInspeccion = Ext.create('Ext.form.TimeField', {
        fieldLabel : '* Hora Fin',
        format     : 'H:i',
        id         : 'ho_fin_inspeccion',
        name       : 'ho_fin_inspeccion',
        minValue   : '00:30 AM',
        maxValue   : '23:59 PM',
        labelAlign : 'top',
        width      : 100,
        increment  : 30,
        value      : hoFin,
        editable   : false,
        labelStyle : 'color:red;',
        padding    :  '2 10 2 1'
    });

    fieldSetDatosFecha = Ext.create('Ext.form.FieldSet',
    {
        columnWidth : 0.5,
        colspan     : 2,
        title       : 'Datos Fecha',
        collapsible : false,
        defaultType : 'textfield',
        defaults    : {
            width:'400px'
        },
        layout     : 'anchor',
        items      :[
            txtInformacionFechaPlanificacion,
            {
                xtype       :'fieldset',
                columnWidth : 1,
                border      : 0,
                title       : '',
                collapsible : false,
                defaultType : 'textfield',
                defaults    : {
                    width   : '100px'
                },
                layout      : 
                {
                    type   :'table',
                    columns: 3
                },
                items       :[
                    DTFechaProgramacionInspeccion,
                    THoraInicioInspeccion,
                    THoraFinInspeccion
                ]
            }
        ]
    });

    fieldSetOptionGroupAsignado = Ext.create('Ext.form.FieldSet',
    {
        columnWidth : 0.5,
        colspan     : 2,
        title       : '',
        collapsible : false,
        defaultType : 'textfield',
        defaults    : {
            width   : '400px'
        },
        layout      : 'anchor',
        items       :
        [
            {   
                xtype      : 'radiogroup',
                labelWidth : 100,
                fieldLabel : '<b>Tipo Asignado</b>',
                items      : 
                [
                    {
                        xtype: 'radiofield',
                        id: 'rdEmpleado',
                        name: 'tipoAsignado',
                        boxLabel: 'Empleado',
                        inputValue: 'Empleado'
                    },
                    {
                        xtype: 'radiofield',
                        id: 'rdCuadrilla',
                        name: 'tipoAsignado',
                        boxLabel: 'Cuadrilla',
                        inputValue: 'Cuadrilla',
                        checked: true
                    }
                ],
                listeners: {
                    change: function(field, newValue, oldValue) {
                        var value = newValue.tipoAsignado;
                        if (Ext.isArray(value)) {
                            return;
                        }
                        if (value == 'Empleado') 
                        {
                            Ext.getCmp('cmb_cuadrilla_inspeccion').hide();
                            Ext.getCmp('lider_cuadrilla_inspeccion').hide();
                            Ext.getCmp('cmb_empleado_inspeccion').show();
                            Ext.getCmp('escogido_tipo_asignacion_insp').setValue(value);
                        }
                        else if (value == 'Cuadrilla') 
                        {
                            Ext.getCmp('cmb_empleado_inspeccion').hide();
                            Ext.getCmp('cmb_cuadrilla_inspeccion').show();
                            Ext.getCmp('lider_cuadrilla_inspeccion').show();
                            Ext.getCmp('escogido_tipo_asignacion_insp').setValue(value);
                        }
                    }
                }
            },
        ]
    }); 

    fieldSetDatosAsignado = Ext.create('Ext.form.FieldSet',
    {
        columnWidth : 0.5,
        colspan     : 2,
        title       : 'Datos Asignado',
        collapsible : false,
        defaultType : 'textfield',
        defaults    : {
            width   : '400px'
        },
        layout      : 'anchor',
        items       :
        [
            comboEmpleadosInspeccion,
            {
                xtype: 'textfield',
                fieldLabel: 'Líder Cuadrilla',
                name: 'lider_cuadrilla_inspeccion',
                id:'lider_cuadrilla_inspeccion',
                width: 400
            },
            {
                xtype: 'displayfield',
                fieldLabel: 'Persona:',
                id: 'idPersonaAsignado',
                name: 'idPersonaAsignado',
                hidden: true,
                value: ""
            },
            {
                xtype: 'displayfield',
                fieldLabel: 'PersonaEmpresaRol:',
                id: 'idPerEmpRolAsignado',
                name: 'idPerEmpRolAsignado',
                hidden: true,
                value: ""
            },
            {
                xtype: 'displayfield',
                id: 'escogido_tipo_asignacion_insp',
                name: 'escogido_tipo_asignacion_insp',
                hidden: true,
                value: 'Cuadrilla'
            },    
            comboCuadrillasInspeccion,
            txtObservacionProgramarfInsp

        ]
    });
    if (tipoGestion == "programar")
    {
        tituloVentana = "Formulario de programar Inspección";

        fieldSetDatosFecha.setVisible(true);
        fieldSetOptionGroupAsignado.setVisible(true);
        fieldSetDatosAsignado.setVisible(true);
        fieldSetReplanifInsp.setVisible(false);
        fieldSetDetenerInsp.setVisible(false);
        fieldSetRechazarInsp.setVisible(false);
        fieldSetAnularInsp.setVisible(false);
    }
    else if (tipoGestion == "replanificar")
    {
        tituloVentana = "Formulario de replanificar Inspección";
        altoVentana   = 530;

        fieldSetDatosFecha.setVisible(true);
        fieldSetOptionGroupAsignado.setVisible(true);
        fieldSetDatosAsignado.setVisible(true);
        fieldSetReplanifInsp.setVisible(true);
        fieldSetDetenerInsp.setVisible(false);
        fieldSetRechazarInsp.setVisible(false);
        fieldSetAnularInsp.setVisible(false);
    }
    else if(tipoGestion == 'detener')
    {
        tituloVentana = "Formulario para Detener Inspección";
        altoVentana = 250;
        fieldSetDatosFecha.setVisible(false);
        fieldSetOptionGroupAsignado.setVisible(false);
        fieldSetDatosAsignado.setVisible(true);
        fieldSetReplanifInsp.setVisible(false);
        fieldSetDetenerInsp.setVisible(true);
        fieldSetRechazarInsp.setVisible(false);
        fieldSetAnularInsp.setVisible(false);
    }
    else if(tipoGestion == 'rechazar')
    {
        tituloVentana = "Formulario para Rechazar Inspección";
        altoVentana = 250;
        fieldSetDatosFecha.setVisible(false);
        fieldSetOptionGroupAsignado.setVisible(false);
        fieldSetDatosAsignado.setVisible(false);
        fieldSetReplanifInsp.setVisible(false);
        fieldSetDetenerInsp.setVisible(false);
        fieldSetRechazarInsp.setVisible(true);
        fieldSetAnularInsp.setVisible(false);
    }
    else if(tipoGestion == 'anular')
    {
        tituloVentana = "Formulario para Anular Inspección";
        altoVentana = 250;
        fieldSetDatosFecha.setVisible(false);
        fieldSetOptionGroupAsignado.setVisible(false);
        fieldSetDatosAsignado.setVisible(false);
        fieldSetReplanifInsp.setVisible(false);
        fieldSetDetenerInsp.setVisible(false);
        fieldSetRechazarInsp.setVisible(false);
        fieldSetAnularInsp.setVisible(true);
    }
    else if(tipoGestion == 'programarExiste')
    {
        tituloVentana = "Formulario para Programar Inspección";
        fieldSetDatosFecha.setVisible(true);
        fieldSetOptionGroupAsignado.setVisible(false);
        fieldSetDatosAsignado.setVisible(true);
        fieldSetReplanifInsp.setVisible(false);
        fieldSetDetenerInsp.setVisible(false);
        fieldSetRechazarInsp.setVisible(false);
        fieldSetAnularInsp.setVisible(false);
    }

    formPanelAgregarAsignadoInspeccion = Ext.create('Ext.form.Panel', {
        renderTo: Ext.getBody(),
        bodyPadding: 3,
        width: 400,
        layout: {
            type: 'table',
            columns: 2,
            width: '400px'
        },
        items: [
            fieldSetDatosFecha,
            fieldSetOptionGroupAsignado,
            fieldSetDatosAsignado,
            fieldSetReplanifInsp,
            fieldSetDetenerInsp,
            fieldSetRechazarInsp,
            fieldSetAnularInsp
        ],
        buttons: 
        [
            {
                text: 'Guardar',
                handler: function() 
                {
                    var id_asignado     = "";
                    var nombre_asignado = "";
                    var tipo_asignado   = "";
                    var estado          = "Pendiente";
                    var origen          = "nuevo";
                    var fecha           = Ext.getCmp("fecha_inspeccion").getValue();
                    var anio            = fecha.getFullYear();
                    var mes             = ((fecha.getMonth()+1) < 10 ? "0" + (fecha.getMonth()+1) : (fecha.getMonth()+1));
                    var dia             = (fecha.getDate() < 10 ? "0" + fecha.getDate() : fecha.getDate());
                    var horaTotal       = Ext.getCmp("ho_inicio_inspeccion").getValue();
                    var h               = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                    var m               = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                    var fecha_inicio    = anio + "-" + mes + "-" + dia + " " + h + ":" + m;
                    horaTotal           = Ext.getCmp("ho_fin_inspeccion").getValue();
                    h                   = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                    m                   = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                    var fecha_fin       = anio + "-" + mes + "-" + dia + " " + h + ":" + m;

                    if (Ext.getCmp("escogido_tipo_asignacion_insp").getValue() == 'Empleado')
                    {
                        id_asignado = Ext.getCmp("cmb_empleado_inspeccion").getValue();
                        nombre_asignado = Ext.getCmp("cmb_empleado_inspeccion").getRawValue();
                        tipo_asignado = "Empleado";
                    }
                    else if(Ext.getCmp("escogido_tipo_asignacion_insp").getValue() == 'Cuadrilla')
                    {
                        id_asignado = Ext.getCmp("cmb_cuadrilla_inspeccion").getValue();
                        nombre_asignado = Ext.getCmp("cmb_cuadrilla_inspeccion").getRawValue();
                        tipo_asignado = "Cuadrilla";
                    }

                    if (Ext.getCmp("fecha_inspeccion").getValue() != "" && 
                        Ext.getCmp("ho_inicio_inspeccion").getValue() != "" && 
                        Ext.getCmp("ho_fin_inspeccion").getValue() != "" &&
                        (
                        (tipoGestion == "replanificar" && Ext.getCmp("cmb_motivo_replanif_insp").getValue() != ""  &&
                         Ext.getCmp("txt_observacion_replanif_insp").getValue() != "") || 
                         tipoGestion == "programar" ) &&
                        (
                        (Ext.getCmp("escogido_tipo_asignacion_insp").getValue() == 'Empleado' && 
                        Ext.getCmp("cmb_empleado_inspeccion").getValue() != null &&
                        Ext.getCmp("cmb_empleado_inspeccion").getValue() != ""
                        ) ||
                        (Ext.getCmp("escogido_tipo_asignacion_insp").getValue() == 'Cuadrilla' && 
                        Ext.getCmp("cmb_cuadrilla_inspeccion").getValue() != null &&
                        Ext.getCmp("cmb_cuadrilla_inspeccion").getValue() != ""
                        )
                        )
                    )
                    {
                        if (tipoGestion == "programar")
                        {
                            var recordAsignadoInspeccion = Ext.create('detalleInspecciones', 
                            {
                                idAsignado     : id_asignado,
                                nombreAsignado   : nombre_asignado,
                                estado : estado,
                                fechaInicio : fecha_inicio,
                                fechaFin : fecha_fin,
                                origen : origen,
                                tipoAsignado : tipo_asignado,
                                observacion: Ext.getCmp("txt_observacion_programar_insp").getValue()  
                            });
                            storeCuadrillasInspecciones.insert(0, recordAsignadoInspeccion);
                            winAgregarAsignadoInspeccion.close();
                            winAgregarAsignadoInspeccion.destroy();
                        }
                        else if(tipoGestion == "replanificar")
                        {
                            connGestionarInspeccion.request({
                                url: urlReplanificarInspeccion,
                                method: 'post',
                                params:
                                    {
                                        idSol: idSol,
                                        idSolPlanif: idSolPlanif,
                                        idAsignado: id_asignado,
                                        tipoAsignado: tipo_asignado,
                                        fechaInicio: fecha_inicio,
                                        fechaFin: fecha_fin,
                                        login: loginInspeccion,
                                        obs: Ext.getCmp("txt_observacion_replanif_insp").getValue(),
                                        idMotivo: Ext.getCmp("cmb_motivo_replanif_insp").getValue()

                                    },
                                success: function(response) 
                                {
                                    var text = response.responseText;
                                    var objResponse =  JSON.parse(text);
                                    if (objResponse.status == "ok")
                                    {
                                        winAgregarAsignadoInspeccion.close();
                                        winAgregarAsignadoInspeccion.destroy();
                                        var ven = Ext.Msg.show({
                                            title:'Mensaje del sistema',
                                            msg: objResponse.mensaje,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.Msg.INFO
                                        });
                                        Ext.defer(function() {
                                            ven.toFront();
                                        }, 50);
                                        storeCuadrillasInspecciones.load();    
                                    }
                                    else 
                                    {
                                        var mm = Ext.Msg.show({
                                                        title:'Mensaje del sistema',
                                                        msg: objResponse.mensaje,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.Msg.ERROR
                                                    });
                                        Ext.defer(function() {
                                            mm.toFront();
                                        }, 50);
                                    }       
                                },
                                failure: function(result) 
                                {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        msg: 'Ocurrio un error al ejecutar la asignación de cuadrillas',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                    }
                    else if(tipoGestion == "programarExiste" &&
                    ( Ext.getCmp("fecha_inspeccion").getValue() != "" && 
                    Ext.getCmp("ho_inicio_inspeccion").getValue() != "" && 
                    Ext.getCmp("ho_fin_inspeccion").getValue() != "" &&
                    Ext.getCmp("cmb_cuadrilla_inspeccion").getValue() != null &&
                    Ext.getCmp("cmb_cuadrilla_inspeccion").getValue() != ""
                    ))
                    {
                        var asignados =  id_asignado + "*" + tipo_asignado + "*" + fecha_inicio + "*" + fecha_fin + "*" +
                                         origen + "*" + idSol + "*" + loginInspeccion + "*" + idSolPlanif + "|";

                        connProgramarInspecciones.request({
                            url    : urlProgramarInspecciones,
                            method : 'post',
                            params : { asignados: asignados },
                            success: function(response) 
                            {
                                    var text = response.responseText;
                                    var objResponse =  JSON.parse(text);
                                    if (objResponse.status == "ok")
                                    {
                                        winAgregarAsignadoInspeccion.close();
                                        winAgregarAsignadoInspeccion.destroy();
                                        var ven = Ext.Msg.show({
                                            title:'Mensaje del sistema',
                                            msg: objResponse.mensaje,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.Msg.INFO
                                        });
                                        Ext.defer(function() {
                                            ven.toFront();
                                        }, 50);
                                        storeCuadrillasInspecciones.load();
                                    }
                                    else 
                                    {
                                        var mm = Ext.Msg.show({
                                                        title:'Mensaje del sistema',
                                                        msg: objResponse.mensaje,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.Msg.ERROR
                                                    });
                                        Ext.defer(function() {
                                            mm.toFront();
                                        }, 50);
                                    }
                            },
                            failure: function(result) {
                                Ext.Msg.show({
                                    title: 'Error',
                                    msg: 'Ocurrio un error al ejecutar el proceso',
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });
                    }
                    else if (
                     tipoGestion == "detener" && 
                     Ext.getCmp("cmb_motivo_detener_insp").getValue() != ""  &&
                     Ext.getCmp("txt_observacion_detener_insp").getValue() != "")
                    {
                            connGestionarInspeccion.request(
                            {
                                url: urlGestionarInspeccion,
                                method: 'post',
                                params:
                                    {
                                        idSol:       idSol,
                                        idSolPlanif: idSolPlanif,
                                        login:       loginInspeccion,
                                        obs:         Ext.getCmp("txt_observacion_detener_insp").getValue(),
                                        idMotivo:    Ext.getCmp("cmb_motivo_detener_insp").getValue(),
                                        estado:      "Detenido"
                                    },
                                success: function(response) {
                                    var text = response.responseText;
                                    var objResponse =  JSON.parse(text);
                                    if (objResponse.status == "ok")
                                    {
                                        winAgregarAsignadoInspeccion.close();
                                        winAgregarAsignadoInspeccion.destroy();
                                        var ven = Ext.Msg.show({
                                            title:'Mensaje del sistema',
                                            msg: objResponse.mensaje,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.Msg.INFO
                                        });
                                        Ext.defer(function() {
                                            ven.toFront();
                                        }, 50);
                                        storeCuadrillasInspecciones.load();    
                                    }
                                    else 
                                    {
                                        var mm = Ext.Msg.show({
                                                        title:'Mensaje del sistema',
                                                        msg: objResponse.mensaje,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.Msg.ERROR
                                                    });
                                        Ext.defer(function() {
                                            mm.toFront();
                                        }, 50);
                                    }       
                                },
                                failure: function(result) {
                                    Ext.Msg.show({
                                        title: 'Error',
                                        msg: 'Ocurrio un error al ejecutar el proceso',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                    }
                    else if (
                        tipoGestion == "rechazar" && 
                        Ext.getCmp("cmb_motivo_rechazar_insp").getValue() != ""  &&
                        Ext.getCmp("txt_observacion_rechazar_insp").getValue() != "")
                    {
                               connGestionarInspeccion.request(
                               {
                                   url: urlGestionarInspeccion,
                                   method: 'post',
                                   params:
                                       {
                                           idSol:       idSol,
                                           idSolPlanif: idSolPlanif,
                                           login:       loginInspeccion,
                                           obs:         Ext.getCmp("txt_observacion_rechazar_insp").getValue(),
                                           idMotivo:    Ext.getCmp("cmb_motivo_rechazar_insp").getValue(),
                                           estado:      "Rechazada"
                                       },
                                   success: function(response) {
                                       var text = response.responseText;
                                       var objResponse =  JSON.parse(text);
                                       if (objResponse.status == "ok")
                                       {
                                           winAgregarAsignadoInspeccion.close();
                                           winAgregarAsignadoInspeccion.destroy();
                                           var ven = Ext.Msg.show({
                                               title:'Mensaje del sistema',
                                               msg: objResponse.mensaje,
                                               buttons: Ext.Msg.OK,
                                               icon: Ext.Msg.INFO
                                           });
                                           Ext.defer(function() {
                                               ven.toFront();
                                           }, 50);
                                           storeCuadrillasInspecciones.load();    
                                       }
                                       else 
                                       {
                                           var mm = Ext.Msg.show({
                                                           title:'Mensaje del sistema',
                                                           msg: objResponse.mensaje,
                                                           buttons: Ext.Msg.OK,
                                                           icon: Ext.Msg.ERROR
                                                       });
                                           Ext.defer(function() {
                                               mm.toFront();
                                           }, 50);
                                       }       
                                   },
                                   failure: function(result) {
                                       Ext.Msg.show({
                                           title: 'Error',
                                           msg: 'Ocurrio un error al ejecutar el proceso',
                                           buttons: Ext.Msg.OK,
                                           icon: Ext.MessageBox.ERROR
                                       });
                                   }
                               });
                    }
                    else if (
                        tipoGestion == "anular" && 
                        Ext.getCmp("cmb_motivo_anular_insp").getValue() != ""  &&
                        Ext.getCmp("txt_observacion_anular_insp").getValue() != "")
                    {
                               connGestionarInspeccion.request(
                               {
                                   url: urlGestionarInspeccion,
                                   method: 'post',
                                   params:
                                       {
                                           idSol:       idSol,
                                           idSolPlanif: idSolPlanif,
                                           login:       loginInspeccion,
                                           obs:         Ext.getCmp("txt_observacion_anular_insp").getValue(),
                                           idMotivo:    Ext.getCmp("cmb_motivo_anular_insp").getValue(),
                                           estado:      "Anulada"
                                       },
                                   success: function(response) {
                                       var text = response.responseText;
                                       var objResponse =  JSON.parse(text);
                                       if (objResponse.status == "ok")
                                       {
                                           winAgregarAsignadoInspeccion.close();
                                           winAgregarAsignadoInspeccion.destroy();
                                           var ven = Ext.Msg.show({
                                               title:'Mensaje del sistema',
                                               msg: objResponse.mensaje,
                                               buttons: Ext.Msg.OK,
                                               icon: Ext.Msg.INFO
                                           });
                                           Ext.defer(function() {
                                               ven.toFront();
                                           }, 50);
                                           storeCuadrillasInspecciones.load();    
                                       }
                                       else 
                                       {
                                           var mm = Ext.Msg.show({
                                                           title:'Mensaje del sistema',
                                                           msg: objResponse.mensaje,
                                                           buttons: Ext.Msg.OK,
                                                           icon: Ext.Msg.ERROR
                                                       });
                                           Ext.defer(function() {
                                               mm.toFront();
                                           }, 50);
                                       }       
                                   },
                                   failure: function(result) {
                                       Ext.Msg.show({
                                           title: 'Error',
                                           msg: 'Ocurrio un error al ejecutar el proceso',
                                           buttons: Ext.Msg.OK,
                                           icon: Ext.MessageBox.ERROR
                                       });
                                   }
                               });
                    }
                    else
                    {
                        var alerta = Ext.Msg.alert("Alerta", "Faltan datos por ingresar");
                        Ext.defer(function() { alerta.toFront(); }, 50);
                    }
                }
            },
            {
                text:'Cerrar',
                handler: function()
                {
                    winAgregarAsignadoInspeccion.close();
                    winAgregarAsignadoInspeccion.destroy();
                }
            }
        ]
    });

    winAgregarAsignadoInspeccion = Ext.widget('window', {
        title     : tituloVentana,
        layout    : 'fit',
        resizable : true,
        modal     : true,
        closable  : false,
        height    : altoVentana,
        width     : anchoVentana,
        items     : [formPanelAgregarAsignadoInspeccion]
    });

    winAgregarAsignadoInspeccion.show();
}

function showHistorialInspeccion(idSolPlanif)
{
    Ext.define('detalleHistorialInspecciones', {
        extend: 'Ext.data.Model',
        fields: [
            { name: 'idSolPlanifHist', type: 'integer' },
            { name: 'nombreAsignado',  type: 'string' },
            { name: 'observacion',     type: 'string' },
            { name: 'feCreacion',      type: 'string' },
            { name: 'usrCreacion',     type: 'string' },
            { name: 'estado',          type: 'string' }
            ]
    });

    storeHistorialInspecciones = new Ext.data.Store({
        total    : 'total',
        autoLoad : true,
        model    : 'detalleHistorialInspecciones',
        proxy    : 
        {
            type     : 'ajax',
            url      : urlHistorialAsignadosSolInsp,
            reader   : 
            {
                type          : 'json',
                totalProperty : 'total',
                root          : 'historialInsp'
            },
            extraParams: 
            {
                idSolPlanif: idSolPlanif
            }
        },
        fields:
            [               
                { name: 'idSolPlanifHist', mapping: 'idSolPlanifHist' },
                { name: 'nombreAsignado', mapping: 'nombreAsignado' },
                { name: 'observacion', mapping: 'observacion' },
                { name: 'feCreacion', mapping: 'feCreacion' },
                { name: 'usrCreacion', mapping: 'usrCreacion' },
                { name: 'estado', mapping: 'estado' }
            ]
    });

    gridHistorialInspecciones = Ext.create('Ext.grid.Panel', {
        width   : 800,
        height  : 280,
        store   : storeHistorialInspecciones,
        loadMask: true,
        frame   : false,
        columns : [
            {
                header: 'idSolPlanifHist',
                dataIndex: 'idSolPlanifHist',
                hidden: true
            },
            {
                header: 'Asignado',
                dataIndex: 'nombreAsignado',
                hideable: false,
                width: 250
            },
            {
                header: 'Observación',
                dataIndex: 'observacion',
                hideable: false,
                width: 300
            },
            {
                header: 'Fecha',
                dataIndex: 'feCreacion',
                hideable: false,
                width: 100
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                hideable: false,
                hidden: true,
                width: 110
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
            }
        },
        renderTo: Ext.getBody()
    });

    formPanelHistorialInspeccion = Ext.create('Ext.form.Panel', 
    {
        renderTo    : Ext.getBody(),
        bodyPadding : 3,
        width       : 800,
        url         : '',
        items: 
        [
            gridHistorialInspecciones
        ],

        buttons: 
        [
            {
                text:'Cerrar',
                handler: function()
                {
                    winHistorialInspeccion.close();
                    winHistorialInspeccion.destroy();
                }
            }
        ]
    });

    winHistorialInspeccion = Ext.widget('window', {
        title    : 'Historial de Inspección',
        layout   : 'fit',
        resizable: true,
        modal    : true,
        closable : false,
        height   : 350,
        width    : 850,
        items    : [formPanelHistorialInspeccion]
    });

    winHistorialInspeccion.show();
}

function showRechazarSolicitudInspeccion(rec)
{
    var idSolicitudInspeccion = rec.get('id_factibilidad');
    var connRechazarSolicitudInspeccion = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Grabando Proceso, Por favor espere!!',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
    cmbMotivosRechazarSolicitudInsp = Ext.create('Ext.data.comboMotivosRechazarSolInspeccion', {
        id: 'cmb_motivo_rechazar_solicitud_insp',
        name: 'cmb_motivo_rechazar_solicitud_insp',
        fieldLabel: '* Motivo',
        anchor:'100%',
        labelStyle: "color:red;"});

    txtObservacionRechazarSolicitudInsp = Ext.create('Ext.form.field.TextArea',
    {
        fieldLabel: '* Observación',
        name: 'txt_observacion_rechazar_solicitud_insp',
        id: 'txt_observacion_rechazar_solicitud_insp',
        value: "",
        allowBlank: false,
        anchor:'100%',
        height: 50
    });

    fieldSetRechazarSolicitudInsp = Ext.create('Ext.form.FieldSet',
    {
        columnWidth: 0.5,
        colspan: 2,
        title: 'Datos Rechazar Solicitud Inspección',
        collapsible: false,
        defaultType: 'textfield',
        defaults: {
            width: '400px'
        },
        layout: 'anchor',
        items :[
            cmbMotivosRechazarSolicitudInsp,
            txtObservacionRechazarSolicitudInsp,
        ]
    });


    formPanelRechazarSolicitudInspeccion = Ext.create('Ext.form.Panel', {
        renderTo: Ext.getBody(),
        bodyPadding: 3,
        width: 400,
        layout: {
            type: 'table',
            columns: 2,
            width: '400px'
        },
        items: [
            fieldSetRechazarSolicitudInsp
        ],
        buttons: 
        [
            {
                text: 'Guardar',
                handler: function() 
                {
                    if (
                        Ext.getCmp("cmb_motivo_rechazar_solicitud_insp").getValue() != ""  &&
                        Ext.getCmp("txt_observacion_rechazar_solicitud_insp").getValue() != "")
                    {
                        connRechazarSolicitudInspeccion.request(
                        {
                            url: urlRechazarSolicitudInspeccion,
                            method: 'post',
                            params:
                                {
                                    idSol:       idSolicitudInspeccion,
                                    obs:         Ext.getCmp("txt_observacion_rechazar_solicitud_insp").getValue(),
                                    idMotivo:    Ext.getCmp("cmb_motivo_rechazar_solicitud_insp").getValue()
                                },
                            success: function(response) {
                                var text = response.responseText;
                                var objResponse =  JSON.parse(text);
                                if (objResponse.status == "ok")
                                {
                                    winRechazarSolicitudInspeccion.close();
                                    winRechazarSolicitudInspeccion.destroy();
                                    var ven = Ext.Msg.show({
                                        title:'Mensaje del sistema',
                                        msg: objResponse.mensaje,
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.Msg.INFO
                                    });
                                    Ext.defer(function() {
                                        ven.toFront();
                                    }, 50);
                                    store.load();
                                }
                                else 
                                {
                                    var mm = Ext.Msg.show({
                                                    title:'Mensaje del sistema',
                                                    msg: objResponse.mensaje,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.Msg.ERROR
                                                });
                                    Ext.defer(function() {
                                        mm.toFront();
                                    }, 50);
                                }       
                            },
                            failure: function(result) {
                                Ext.Msg.show({
                                    title: 'Error',
                                    msg: 'Ocurrio un error al ejecutar el proceso',
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });
                    }
                    else
                    {
                        var alerta = Ext.Msg.alert("Alerta", "Faltan datos por ingresar");
                        Ext.defer(function() { alerta.toFront(); }, 50);
                    }
                }
            },
            {
                text:'Cerrar',
                handler: function()
                {
                    winRechazarSolicitudInspeccion.close();
                    winRechazarSolicitudInspeccion.destroy();
                }
            }
        ]
    });

    winRechazarSolicitudInspeccion = Ext.widget('window', {
        title     : 'Rechazar Solicitud Inspección',
        layout    : 'fit',
        resizable : true,
        modal     : true,
        closable  : false,
        height    : 250,
        width     : 450,
        items     : [formPanelRechazarSolicitudInspeccion]
    });

    winRechazarSolicitudInspeccion.show();
}



function showDocumentosSolicitudInspeccion(rec)
{
    var id_solicitud = rec.get('id_factibilidad');

    var storeSolicitudInspeccion = new Ext.data.Store({
        pageSize: 1000,
        autoLoad: true,
        model    : 'documentosSolicitudInspeccion',
        proxy: {
            type: 'ajax',
            url : urlDocumentosSolInsp,
            reader: {
                type         : 'json',
                totalProperty: 'total',
                root         : 'documentosInsp'
            },
            extraParams: {
                idSolicitud : id_solicitud
            }
        },
        fields:
            [
                {name:'idSolCaracteristica',    mapping:'idSolCaracteristica'},
                {name:'descripcionCaracteristica',       mapping:'descripcionCaracteristica'},
                {name:'nombreDocumento',        mapping:'nombreDocumento'},
                {name:'linkVerDocumento',       mapping:'linkVerDocumento'}
            ]
    });

    Ext.define('documentosSolicitudInspeccion', {
        extend: 'Ext.data.Model',
        fields: [
                { name: 'idSolCaracteristica', type: 'integer' },
                { name: 'descripcionCaracteristica',     type: 'string' },
                { name: 'nombreDocumento',  type: 'string' },
                { name: 'linkVerDocumento',     type: 'string' }
            ]
    });

    //grid de documentos por Caso
    gridDocumentosSolicitudInspeccion = Ext.create('Ext.grid.Panel', {
        store: storeSolicitudInspeccion,
        columnLines: true,
        columns: [{
            header   : 'Nombre Archivo',
            dataIndex: 'nombreDocumento',
            width    : 260
        },
        {
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 90,
            items:
            [
                {
                    iconCls: 'button-grid-show',
                    tooltip: 'Ver Archivo Digital',
                    handler: function(grid, rowIndex, colIndex) {
                        var rec         = storeSolicitudInspeccion.getAt(rowIndex);
                        verArchivoDigital(rec);
                    }
                }
            ]
        }
    ],
        viewConfig:{
            stripeRows:true,
            enableTextSelection: true
        },
        frame : true,
        height: 200
    });

    function verArchivoDigital(rec)
    {
        var rutaFisica = rec.get('linkVerDocumento');
        var posicion = rutaFisica.indexOf('/public')
        window.open(rutaFisica.substring(posicion,rutaFisica.length));
    }

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding  : 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget : 'side'
        },
        items: [

            {
                xtype      : 'fieldset',
                title      : '',
                defaultType: 'textfield',

                defaults: {
                    width: 360
                },
                items: [

                    gridDocumentosSolicitudInspeccion

                ]
            }
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title   : 'Documentos Cargados',
        modal   : true,
        width   : 400,
        closable: true,
        layout  : 'fit',
        items   : [formPanel]
    }).show();

}
