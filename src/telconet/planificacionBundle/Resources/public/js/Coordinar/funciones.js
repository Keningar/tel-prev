/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
let entidadSolicitudSeguimiento = new Seguimiento();

var winMenuAsignacion;
var winAsignacion;
var winAsignacionIndividual;
var winRecursoDeRed;
var gridIpPublica;
var gridIpMonitoreo;
var tareasJS;
var cuadrillaAsignada = "S";
var seleccionaHal     = false;
var nIntentos         = 0;
var esHal             = 'N';
var tipoHal;


Ext.override(Ext.data.Connection, {

        timeout:45000

});
    
var connCoordinar = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});
var connTareas = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Cargando Tareas',
                    progressText: 'loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});
var connAsignarResponsable = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});
var connAsignarResponsable2 = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Consultando el lider, Por favor espere!!',
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

var connGetAsignadosTarea = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Cargando información importante',
                    progressText: 'Cargando...',
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

var connRecursoDeRed = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

var connPlanificarServicio = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});
/************************************************************************ */
/*********************** CAMBIAR TIPO RESPONSABLE *********************** */
/************************************************************************ */
function cambiarTipoResponsable(valor)
{
    if (valor == "empleado")
    {
        $('#tr_empleado').css("display", "table");
        $('#tr_cuadrilla').css("display", "none");
        $('#tr_empresa_externa').css("display", "none");
        storeEmpleados.load();
    } else if (valor == "cuadrilla")
    {
        $('#tr_empleado').css("display", "none");
        $('#tr_cuadrilla').css("display", "table");
        $('#tr_empresa_externa').css("display", "none");
        storeCuadrillas.load();
    } else if (valor == "empresaExterna")
    {
        $('#tr_empleado').css("display", "none");
        $('#tr_cuadrilla').css("display", "none");
        $('#tr_empresa_externa').css("display", "table");
        storeEmpresaExterna.load();
    }

}

function cambiarTipoResponsable_Individual(i, valor)
{
    if (valor == "empleado")
    {
        cuadrillaAsignada = "S";
        Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);
        Ext.getCmp('cmb_empleado_' + i).setVisible(true);
        Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
        Ext.getCmp('cmb_empleado_' + i).setDisabled(false);
        Ext.getCmp('cmb_cuadrilla_' + i).setDisabled(true);
        Ext.getCmp('cmb_empresa_externa_' + i).setDisabled(true);
    } else if (valor == "cuadrilla")
    {
        Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(true);
        Ext.getCmp('cmb_empleado_' + i).setVisible(false);
        Ext.getCmp('cmb_cuadrilla_' + i).setVisible(true);
        Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
        Ext.getCmp('cmb_empleado_' + i).setDisabled(true);
        Ext.getCmp('cmb_cuadrilla_' + i).setDisabled(false);
        Ext.getCmp('cmb_empresa_externa_' + i).setDisabled(true);
    } else if (valor == "empresaExterna")
    {
        cuadrillaAsignada = "S";
        Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);
        Ext.getCmp('cmb_empleado_' + i).setVisible(false);
        Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_' + i).setVisible(true);
        Ext.getCmp('cmb_empleado_' + i).setDisabled(true);
        Ext.getCmp('cmb_cuadrilla_' + i).setDisabled(true);
        Ext.getCmp('cmb_empresa_externa_' + i).setDisabled(false);
    }
}

function seteaLiderCuadrilla(id, cuadrilla)
{
    connAsignarResponsable2.request({
        url: url_asignar_responsable,
        method: 'post',
        params:
            {
                cuadrillaId: cuadrilla
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);
            if (text.existeTablet == "S")
            {
                cuadrillaAsignada = "S";
                Ext.getCmp('nombreLider_' + id.substring(14, 15)).setValue(text.nombres);
                Ext.getCmp('idPersona_' + id.substring(14, 15)).setValue(text.idPersona);
                Ext.getCmp('idPersonaEmpresaRol_' + id.substring(14, 15)).setValue(text.idPersonaEmpresaRol);
            } else
            {
                var alerta = Ext.Msg.alert("Alerta", "La cuadrilla " + text.nombreCuadrilla + " no posee tablet asignada. Realice la asignación de \n\
                                                     tablet correspondiente o seleccione otra cuadrilla.");
                Ext.defer(function() {
                    alerta.toFront();
                }, 50);
                cuadrillaAsignada = "N";
                Ext.getCmp('cmb_cuadrilla_' + id.substring(14, 15)).setValue("");
                Ext.getCmp('nombreLider_' + id.substring(14, 15)).setValue("");
                Ext.getCmp('idPersona_' + id.substring(14, 15)).setValue("");
                Ext.getCmp('idPersonaEmpresaRol_' + id.substring(14, 15)).setValue("");
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



/************************************************************************ */
/******************* GUARDAR ASIGNAR RESPONSABLE  *********************** */
/************************************************************************ */
function asignarResponsable(origen, id)
{
    var banderaEscogido = false;
    var codigoEscogido = "";
    var tituloError = "";
    var param = '';
    var boolError = false;
    if (origen == "local")
    {
        banderaEscogido = $("input[name='tipoResponsable']:checked").val();
        if (banderaEscogido == "empleado")
        {
            tituloError = "Empleado";
            codigoEscogido = Ext.getCmp('cmb_empleado').value;
        }
        if (banderaEscogido == "cuadrilla")
        {
            tituloError = "Cuadrilla";
            codigoEscogido = Ext.getCmp('cmb_cuadrilla').value;
        }
        if (banderaEscogido == "empresaExterna")
        {
            tituloError = "Contratista";
            codigoEscogido = Ext.getCmp('cmb_empresa_externa').value;
        }

        if (sm.getSelection().length > 0)
        {
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.id_factibilidad;
                if (i < (sm.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (param && param != "")
            {
                //NADA
            } else
            {
                boolError = false;
                Ext.Msg.alert('Alerta', 'No hay parametros parseados.');
            }

        } else
        {
            boolError = false;
            Ext.Msg.alert('Alerta', 'Seleccione por lo menos un registro de la lista');
        }
    } else if (origen == "otro" || origen == "otro2")
    {
        banderaEscogido = $("input[name='tipoResponsable_1']:checked").val();
        if (banderaEscogido == "empleado")
        {
            tituloError = "Empleado";
            codigoEscogido = Ext.getCmp('cmb_empleado_1').value;
        }
        if (banderaEscogido == "cuadrilla")
        {
            tituloError = "Cuadrilla";
            codigoEscogido = Ext.getCmp('cmb_cuadrilla_1').value;
        }
        if (banderaEscogido == "empresaExterna")
        {
            tituloError = "Contratista";
            codigoEscogido = Ext.getCmp('cmb_empresa_externa_1').value;
        }

        if (id == null || !id || id == 0 || id == "0" || id == "")
        {
            boolError = true;
            Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
        }
    } else
    {
        boolError = true;
        Ext.Msg.alert('Alerta', 'No hay opcion escogida');
    }

    if (!boolError)
    {
        if (codigoEscogido && codigoEscogido != "")
        {
            Ext.Msg.confirm('Alerta', 'Se asignará el responsable. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                    connAsignarResponsable.request({
                        url: "../../planificar/asignar_responsable/asignarAjax",
                        method: 'post',
                        params: {origen: origen, id: id, param: param, banderaEscogido: banderaEscogido, codigoEscogido: codigoEscogido},
                        success: function(response) {
                            var text = response.responseText;
                            if (text == "Se asigno la Tarea Correctamente.")
                            {
                                if (origen == "otro" || origen == "otro2") {
                                    cierraVentanaAsignacion();
                                }
                                Ext.Msg.alert('Mensaje', text, function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                        //showRecursosDeRed(id, "factibilidad");
                                    }
                                });
                            } else {
                                Ext.Msg.alert('Alerta', 'Error: ' + text);
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.alert('Alerta', result.responseText);
                        }
                    });
                }
            });
        } else
        {
            Ext.Msg.alert('Alerta', 'Por favor seleccione un valor del combo ' + tituloError);
        }
    }
}

function asignarResponsableIndividual(rec, origen, id, store)
{
}

/************************************************************************ */
/**************************** RECURSO DE RED ****************************** */
/************************************************************************ */
function showRecursoDeRed(rec, id, origen)
{
    winRecursoDeRed = "";
    formPanelRecursosDeRed = "";
    if (!winRecursoDeRed)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
//            width: 600,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });
        Ext.define('IpPublica', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ipPublica', mapping: 'ipPublica'}
            ]
        });
        Ext.define('IpMonitoreo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ipMonitoreo', mapping: 'ipMonitoreo'}
            ]
        });
        storeIpsPublicas = Ext.create('Ext.data.Store', {
            // destroy the store if the grid is destroyed
            // autoDestroy: true,
            // autoLoad: false,
            model: 'IpPublica',
            // proxy: {
            // type: 'ajax',
            // load remote data using HTTP
            // url: 'gridTecnologia',
            // specify a XmlReader (coincides with the XML format of the returned data)
            // reader: {
            // type: 'json',
            // totalProperty: 'total',
            // records will have a 'plant' tag
            // root: 'tecnologias'
            // }
            // }
        });
        storeIpsMonitoreo = Ext.create('Ext.data.Store', {
            // destroy the store if the grid is destroyed
            // autoDestroy: true,
            // autoLoad: false,
            model: 'IpMonitoreo',
            // proxy: {
            // type: 'ajax',
            // load remote data using HTTP
            // url: 'gridTecnologia',
            // specify a XmlReader (coincides with the XML format of the returned data)
            // reader: {
            // type: 'json',
            // totalProperty: 'total',
            // records will have a 'plant' tag
            // root: 'tecnologias'
            // }
            // }
        });
        var cellEditingIpPublica = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    // refresh summaries
                    // gridIpPublica.getView().refresh();
                    var rowIdx = object.rowIdx;
                    var currentIpPublica = object.value;
                    if (esIpValida(currentIpPublica)) {
                        if (!existeRecordIpPublica(rowIdx, currentIpPublica, gridIpPublica))
                        {
                            $('input[name="ipPublica_text"]').val('');
                            $('input[name="ipPublica_text"]').val(currentIpPublica);
                            // this.collapse();
                        } else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: "Ip ya existente. Por favor ingrese otra.",
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            eliminarSeleccion(gridIpPublica);
                        }
                    } else {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "Ingrese una Ip valida",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        eliminarSeleccion(gridIpPublica);
                    }
                }
            }
        });
        var selIpsPublicas = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIpPublica.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });
        var cellEditingIpMonitoreo = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    // refresh summaries
                    // gridIpMonitoreo.getView().refresh();
                    var rowIdx = object.rowIdx;
                    var currentIpMonitoreo = object.value;
                    if (esIpValida(currentIpMonitoreo)) {
                        if (!existeRecordIpMonitoreo(rowIdx, currentIpMonitoreo, gridIpMonitoreo))
                        {
                            $('input[name="ipMonitoreo_text"]').val('');
                            $('input[name="ipMonitoreo_text"]').val(currentIpMonitoreo);
                            // this.collapse();
                        } else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: "Ip ya existente. Por favor ingrese otra.",
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            eliminarSeleccion(gridIpMonitoreo);
                        }
                    } else {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "Ingrese una Ip valida",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        eliminarSeleccion(gridIpMonitoreo);
                    }
                }
            }
        });
        var selIpsMonitoreo = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIpMonitoreo.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });
        storeInterfacesByElemento = new Ext.data.Store({
            autoLoad: true,
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: 'getJsonInterfacesByElemento',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    idElemento: rec.get("elementoId")
                }
            },
            fields:
                [
                    {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                    {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
                ]
        });
        //grid Ip Publica
        gridIpPublica = Ext.create('Ext.grid.Panel', {
            id: '	gridIpPublica',
            store: storeIpsPublicas,
            columnLines: true,
            columns: [{
                    id: 'ipPublica',
                    header: 'Ip Publica',
                    dataIndex: 'ipPublica',
                    width: 290,
                    // sortable: true,
                    // renderer: function (value, metadata, record, rowIndex, colIndex, store){
                    // alert("aqui estoy");
                    // if (typeof(record.data.usuarioAccesoNombre) == "number")
                    // {

                    // record.data.usuarioAccesoId = record.data.usuarioAccesoNombre;
                    // for (var i = 0;i< storeUsuarios.data.items.length;i++)
                    // {
                    // if (storeUsuarios.data.items[i].data.idUsuarioAcceso == record.data.usuarioAccesoId)
                    // {
                    // record.data.usuarioAccesoNombre = storeUsuarios.data.items[i].data.nombreUsuarioAcceso;
                    // break;
                    // }
                    // }
                    // }
                    // return record.data.usuarioAccesoNombre;
                    // },
                    editor: {
                        id: 'ipPublica_text',
                        name: 'ipPublica_text',
                        xtype: 'textfield',
                        // typeAhead: true,
                        // displayField:'nombreUsuarioAcceso',
                        // valueField: 'idUsuarioAcceso',
                        // triggerAction: 'all',
                        // selectOnFocus: true,
                        // loadingText: 'Buscando ...',
                        // hideTrigger: false,
                        // store: storeUsuarios,
                        // lazyRender: true,
                        // listClass: 'x-combo-list-small',
                        // listeners: {
                        // edit: function(editor,textField){
                        // var currentIpPublica = textField.getValue();
                        // if(esIpValida(currentIpPublica)){
                        // if(!existeRecordIpPublica(currentIpPublica, gridIpPublica))
                        // {
                        // Ext.get('ipPublica_cmp').dom.value='';
                        // Ext.get('ipPublica_cmp').dom.value=currentIpPublica;
                        // this.collapse();

                        // }
                        // else
                        // {
                        // Ext.MessageBox.show({
                        // title: 'Error',
                        // msg: "Ip ya existente. Por favor ingrese otra.",
                        // buttons: Ext.MessageBox.OK,
                        // icon: Ext.MessageBox.ERROR
                        // });
                        // eliminarSeleccion(gridIpPublica);
                        // }
                        // }else{
                        // Ext.MessageBox.show({
                        // title: 'Error',
                        // msg: "Ingrese una Ip valida",
                        // buttons: Ext.MessageBox.OK,
                        // icon: Ext.MessageBox.ERROR
                        // });
                        // }
                        // }
                        // }
                    }
                }],
            selModel: selIpsPublicas,
            viewConfig: {
                stripeRows: true
            },
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            iconCls: 'add',
                            handler: function() {
                                if (!existeRecordIpPublica("", "", gridIpPublica))
                                {
                                    // Create a model instance
                                    var r = Ext.create('IpPublica', {
                                        ipPublica: ''
                                    });
                                    storeIpsPublicas.insert(0, r);
                                    // cellEditingIpPublica.startEditByPosition({row: 0, column: 1});
                                } else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ya existe un registro vacio para que sea llenado.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }, '-', {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridIpPublica);
                            }
                        }]
                }],
            // width: 425,
            // height: 200,
            frame: true,
            title: 'Ips Publicas',
            // renderTo: 'gridIpPublica',
            plugins: [cellEditingIpPublica]
        });
        function existeRecordIpPublica(rowIdx, ip, grid)
        {
            var existe = false;
            var num = grid.getStore().getCount();
            for (var i = 0; i < num; i++)
            {
                if (i != rowIdx) {
                    var ipPublica = grid.getStore().getAt(i).get('ipPublica');
                    if ((ipPublica == ip))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        //grid Ip Monitoreo
        gridIpMonitoreo = Ext.create('Ext.grid.Panel', {
            id: '	gridIpMonitoreo',
            store: storeIpsMonitoreo,
            columnLines: true,
            columns: [{
                    id: 'ipMonitoreo',
                    header: 'Ip Monitoreo',
                    dataIndex: 'ipMonitoreo',
                    width: 290,
                    // sortable: true,
                    // renderer: function (value, metadata, record, rowIndex, colIndex, store){
                    // alert("aqui estoy");
                    // if (typeof(record.data.usuarioAccesoNombre) == "number")
                    // {

                    // record.data.usuarioAccesoId = record.data.usuarioAccesoNombre;
                    // for (var i = 0;i< storeUsuarios.data.items.length;i++)
                    // {
                    // if (storeUsuarios.data.items[i].data.idUsuarioAcceso == record.data.usuarioAccesoId)
                    // {
                    // record.data.usuarioAccesoNombre = storeUsuarios.data.items[i].data.nombreUsuarioAcceso;
                    // break;
                    // }
                    // }
                    // }
                    // return record.data.usuarioAccesoNombre;
                    // },
                    editor: {
                        id: 'ipMonitoreo_cmp',
                        xtype: 'textfield',
                        // typeAhead: true,
                        // displayField:'nombreUsuarioAcceso',
                        // valueField: 'idUsuarioAcceso',
                        // triggerAction: 'all',
                        // selectOnFocus: true,
                        // loadingText: 'Buscando ...',
                        // hideTrigger: false,
                        // store: storeUsuarios,
                        // lazyRender: true,
                        // listClass: 'x-combo-list-small',
                        // listeners: {
                        // edit: function(textField){
                        // var currentIpMonitoreo = textField.getValue();
                        // if(esIpValida(currentIpMonitoreo)){
                        // if(!existeRecordIpMonitoreo(currentIpMonitoreo, gridIpMonitoreo))
                        // {
                        // Ext.get('ipMonitoreo_cmp').dom.value='';
                        // Ext.get('ipMonitoreo_cmp').dom.value=currentIpMonitoreo;
                        // this.collapse();

                        // }
                        // else
                        // {
                        // Ext.MessageBox.show({
                        // title: 'Error',
                        // msg: "Ip ya existente. Por favor ingrese otra.",
                        // buttons: Ext.MessageBox.OK,
                        // icon: Ext.MessageBox.ERROR
                        // });
                        // eliminarSeleccion(gridIpMonitoreo);
                        // }
                        // }else{
                        // Ext.MessageBox.show({
                        // title: 'Error',
                        // msg: "Ingrese una Ip valida",
                        // buttons: Ext.MessageBox.OK,
                        // icon: Ext.MessageBox.ERROR
                        // });
                        // }
                        // }
                        // }
                    }
                }],
            selModel: selIpsMonitoreo,
            viewConfig: {
                stripeRows: true
            },
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            iconCls: 'add',
                            handler: function() {
                                if (!existeRecordIpMonitoreo("", "", gridIpMonitoreo))
                                {
                                    // Create a model instance
                                    var r = Ext.create('IpMonitoreo', {
                                        ipPublica: ''
                                    });
                                    storeIpsMonitoreo.insert(0, r);
                                    // cellEditingIpPublica.startEditByPosition({row: 0, column: 1});
                                } else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ya existe un registro vacio para que sea llenado.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }, '-', {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridIpMonitoreo);
                            }
                        }]
                }],
            // width: 425,
            // height: 200,
            frame: true,
            title: 'Ips Monitoreo',
            // renderTo: 'gridIpPublica',
            plugins: [cellEditingIpMonitoreo]
        });
        function existeRecordIpMonitoreo(rowIdx, ip, grid)
        {
            var existe = false;
            var num = grid.getStore().getCount();
            for (var i = 0; i < num; i++)
            {
                if (i != rowIdx) {
                    var ipMonitoreo = grid.getStore().getAt(i).get('ipMonitoreo');
                    if ((ipMonitoreo == ip))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        function eliminarSeleccion(datosSelect)
        {
            for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
            {
                datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
            }
        }

        function obtenerIpsPublicas()
        {
            if (gridIpPublica.getStore().getCount() >= 1) {
                var ips = "";
                for (var i = 0; i < gridIpPublica.getStore().getCount(); i++)
                {
                    if (i == 0) {
                        ips = gridIpPublica.getStore().getAt(i).data.ipPublica;
                    } else {
                        ips = ips + "@" + gridIpPublica.getStore().getAt(i).data.ipPublica;
                    }
                }
                return ips;
            } else {
                return "";
            }
        }
        function obtenerIpsMonitoreo()
        {
            if (gridIpMonitoreo.getStore().getCount() >= 1) {
                var ips = "";
                for (var i = 0; i < gridIpMonitoreo.getStore().getCount(); i++)
                {
                    if (i == 0) {
                        ips = gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
                    } else {
                        ips = ips + "@" + gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
                    }
                }
                return ips;
            } else {
                return "";
            }
        }

        formPanelRecursosDeRed = Ext.create('Ext.form.Panel', {
//            width:600,
            // height:600,
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: 'Datos del Cliente',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    layout: 'anchor',
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Cliente',
                            name: 'info_cliente',
                            id: 'info_cliente',
                            value: rec.get("cliente"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Login',
                            name: 'info_login',
                            id: 'info_login',
                            value: rec.get("login2"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Jurisdiccion',
                            name: 'info_ciudad',
                            id: 'info_ciudad',
                            value: rec.get("jurisdiccion"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Coordenadas',
                            name: 'info_coordenadas',
                            id: 'info_coordenadas',
                            value: rec.get("coordenadas"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Direccion',
                            name: 'info_direccion',
                            id: 'info_direccion',
                            value: rec.get("direccion"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Sector',
                            name: 'info_nombreSector',
                            id: 'info_nombreSector',
                            value: rec.get("nombreSector"),
                            allowBlank: false,
                            readOnly: true
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos del Servicio',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Servicio',
                            name: 'info_servicio',
                            id: 'info_servicio',
                            value: rec.get("productoServicio"),
                            allowBlank: false,
                            readOnly: true
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de Recursos de Red',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Ultima Milla',
                            name: 'txt_um',
                            id: 'txt_um',
                            value: rec.get("ultimaMilla"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Pop',
                            name: 'txt_pop',
                            id: 'txt_pop',
                            value: rec.get("pop"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Dslam',
                            name: 'txt_dslam',
                            id: 'txt_dslam',
                            value: rec.get("dslam"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface',
                            name: 'cmb_Interface',
                            fieldLabel: '* Interface',
                            typeAhead: true,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreInterfaceElemento',
                            valueField: 'idInterfaceElemento',
                            selectOnTab: true,
                            store: storeInterfacesByElemento,
//                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione',
                            labelStyle: "color:red;",
//                            disabled: true,
//                            editable: false,
                            // listeners:{
//                                select:{fn:function(combo, value) {
//                                    Ext.Ajax.request({
//                                        url: "ajaxCargaElemento",
//                                        method: 'post',
//                                        params: { idElemento : combo.getValue()},
//                                        success: function(response){
//                                            var ContDisponibilidad = response.responseText;
//                                            $('input[name="puertos_disponibles"]').val(ContDisponibilidad);
//                                        },
//                                        failure: function(result)
//                                        {
//                                             Ext.MessageBox.show({
//                                                title: 'Error',
//                                                msg: result.statusText,
//                                                buttons: Ext.MessageBox.OK,
//                                                icon: Ext.MessageBox.ERROR
//                                             });
////                             Ext.Msg.alert('Alerta','Error: ' + result.statusText);
//                                        }
//                                    });
//                                }}
                            // }
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Ip Wan',
                            name: 'ip_wan',
                            id: 'ip_wan',
                            allowBlank: false,
                            emptyText: 'Ingrese una Ip',
                            labelStyle: "color:red;",
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Ip Lan',
                            name: 'ip_lan',
                            id: 'ip_lan',
                            allowBlank: false,
                            emptyText: 'Ingrese una Ip',
                            labelStyle: "color:red;",
                        }, {
                            xtype: 'panel',
                            BodyPadding: 10,
                            bodyStyle: "background: white; padding:10px; border: 0px none;",
                            frame: true,
                            items: [gridIpPublica]
                        }, {
                            xtype: 'panel',
                            BodyPadding: 10,
                            bodyStyle: "background: white; padding:10px; border: 0px none;",
                            frame: true,
                            items: [gridIpMonitoreo]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var ip_wan = $('input[name="ip_wan"]').val();
                        var ip_lan = $('input[name="ip_lan"]').val();
                        var id_interface = Ext.getCmp('cmb_Interface').value;
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        } else if (!id_interface || id_interface == "" || id_interface == 0)
                        {
                            boolError = true;
                            mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                        } else if (!esIpValida(ip_wan))
                        {
                            boolError = true;
                            mensajeError += "La Ip Wan es Invalida. Por favor ingrese nuevamente\n";
                        } else if (!esIpValida(ip_lan))
                        {
                            boolError = true;
                            mensajeError += "La Ip Lan es Invalida. Por favor ingrese nuevamente\n";
                        }

                        if (!boolError)
                        {
                            connRecursoDeRed.request({
                                url: "guardaRecursosDeRed",
                                method: 'post',
                                params: {id: id_factibilidad, interface_id: id_interface, ip_wan: ip_wan, ip_lan: ip_lan, ips_publicas: obtenerIpsPublicas(), ips_monitoreo: obtenerIpsMonitoreo()},
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se guardo correctamente los Recursos de Red")
                                    {
                                        cierraVentanaRecursoDeRed();
                                        Ext.MessageBox.show({
                                            title: 'Mensaje',
                                            msg: text,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.INFO
                                        });
                                    } else {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: text,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
//                                             Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
//                                                                                 Ext.Msg.alert('Alerta', result.responseText);
                                }
                            });
                        } else {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: mensajeError,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
//                            Ext.Msg.alert('Error' ,'Error: ' + mensajeError);
                        }
                    }
                }
//                {
//                    text: 'Cerrar',
//                    handler: function(){
//                        cierraVentanaRecursoDeRed();
//                    }
//                }
            ]
        });
        winRecursoDeRed = Ext.widget('window', {
            title: 'Ingreso de Recursos de Red',
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelRecursosDeRed]
        });
    }

    winRecursoDeRed.show();
}

function cierraVentanaRecursoDeRed() {
    winRecursoDeRed.close();
    winRecursoDeRed.destroy();
}

function esIpValida(ip) {
    var RegExPattern = /^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;
    ;
    if ((ip.match(RegExPattern)) && (ip != '')) {
        return true;
    } else {
        return false;
    }
}

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
function showMenuAsignacion(origen, id, panelAsignados)
{
    winMenuAsignacion = "";
    formPanelMenuAsignacion = "";
    if (!winMenuAsignacion)
    {
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 300,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });
        //******** HtmlDesc
        var iniHtml = 'Por favor escoja algun boton';
        HtmlDesc = Ext.create('Ext.Component', {
            html: iniHtml,
            width: 300,
            padding: 10,
            style: {color: '#000000'}
        });
        //******** html vacio...
        var iniHtmlVacio = '';
        Vacio = Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 300,
            padding: 8,
            layout: 'anchor',
            style: {color: '#000000'}
        });
        formPanelMenuAsignacion = Ext.create('Ext.form.Panel', {
            width: 380,
            height: 150,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, HtmlDesc, Vacio1, Vacio],
            buttons: [
                {
                    text: 'Asignacion Global',
                    handler: function() {
                        cierraVentanaMenuAsignacion();
                        showAsignacion(origen, id, panelAsignados);
                    }
                },
                {
                    text: 'Asignacion Individual',
                    handler: function() {
                        cierraVentanaMenuAsignacion();
                        showAsignacionIndividual(origen, id, panelAsignados);
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaMenuAsignacion();
                    }
                }
            ]
        });
        winMenuAsignacion = Ext.widget('window', {
            title: 'Menu Asignacion',
            width: 400,
            height: 170,
            minHeight: 170,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelMenuAsignacion]
        });
    }

    winMenuAsignacion.show();
}

function cierraVentanaMenuAsignacion() {
    winMenuAsignacion.close();
    winMenuAsignacion.destroy();
}

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
function showAsignacion(origen, id, panelAsignados)
{
    winAsignacion = "";
    formPanelAsignacion = "";
    if (!winAsignacion)
    {
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 600,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });
        var i = 1;
        //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
        var iniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
            '&nbsp;&nbsp;' +
            '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="cuadrilla" name="tipoResponsable_' + i + '">&nbsp;Cuadrilla' +
            '&nbsp;&nbsp;' +
            '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="empresaExterna" name="tipoResponsable_' + i + '">&nbsp;Contratista' +
            '';
        RadiosTiposResponsable = Ext.create('Ext.Component', {
            html: iniHtml,
            width: 600,
            padding: 10,
            style: {color: '#000000'}
        });
        // **************** EMPLEADOS ******************
        Ext.define('EmpleadosList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_empleado', type: 'int'},
                {name: 'nombre_empleado', type: 'string'}
            ]
        });
        eval("var storeEmpleados_" + i + "= Ext.create('Ext.data.Store', { " +
            "  id: 'storeEmpleados_" + i + "', " +
            "  model: 'EmpleadosList', " +
            "  autoLoad: false, " +
            " proxy: { " +
            "   type: 'ajax'," +
            "    url : '../../planificar/asignar_responsable/getEmpleados'," +
            "   reader: {" +
            "        type: 'json'," +
            "       totalProperty: 'total'," +
            "        root: 'encontrados'" +
            "  }" +
            "  }" +
            " });    ");
        combo_empleados = new Ext.form.ComboBox({
            id: 'cmb_empleado_' + i,
            name: 'cmb_empleado_' + i,
            fieldLabel: "Empleados",
            anchor: '100%',
            queryMode: 'remote',
            width: 350,
            emptyText: 'Seleccione Empleado',
            store: eval("storeEmpleados_" + i),
            displayField: 'nombre_empleado',
            valueField: 'id_empleado',
            layout: 'anchor',
            disabled: false
        });
        // ****************  EMPRESA EXTERNA  ******************
        Ext.define('EmpresaExternaList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_empresa_externa', type: 'int'},
                {name: 'nombre_empresa_externa', type: 'string'}
            ]
        });
        eval("var storeEmpresaExterna_" + i + "= Ext.create('Ext.data.Store', { " +
            "  id: 'storeEmpresaExterna_" + i + "', " +
            "  model: 'EmpresaExternaList', " +
            "  autoLoad: false, " +
            " proxy: { " +
            "   type: 'ajax'," +
            "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
            "   reader: {" +
            "        type: 'json'," +
            "       totalProperty: 'total'," +
            "        root: 'encontrados'" +
            "  },actionMethods: { " +
            " create: 'POST', read: 'POST', update: 'POST', destroy: 'POST' " +
            " }, " +
            "  }" +
            " });    ");
        combo_empresas_externas = new Ext.form.ComboBox({
            id: 'cmb_empresa_externa_' + i,
            name: 'cmb_empresa_externa_' + i,
            fieldLabel: "Contratista",
            anchor: '100%',
            queryMode: 'remote',
            width: 350,
            emptyText: 'Seleccione Contratista',
            store: eval("storeEmpresaExterna_" + i),
            displayField: 'nombre_empresa_externa',
            valueField: 'id_empresa_externa',
            layout: 'anchor',
            disabled: true
        });
        // **************** CUADRILLAS ******************
        Ext.define('CuadrillasList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_cuadrilla', type: 'int'},
                {name: 'nombre_cuadrilla', type: 'string'}
            ]
        });
        eval("var storeCuadrillas_" + i + "= Ext.create('Ext.data.Store', { " +
            "  id: 'storeCuadrillas_" + i + "', " +
            "  model: 'CuadrillasList', " +
            "  autoLoad: false, " +
            " proxy: { " +
            "   type: 'ajax'," +
            "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
            "   reader: {" +
            "        type: 'json'," +
            "       totalProperty: 'total'," +
            "        root: 'encontrados'" +
            "  }" +
            "  }" +
            " });    ");
        combo_cuadrillas = new Ext.form.ComboBox({
            id: 'cmb_cuadrilla_' + i,
            name: 'cmb_cuadrilla_' + i,
            fieldLabel: "Cuadrilla",
            anchor: '100%',
            queryMode: 'remote',
            width: 350,
            emptyText: 'Seleccione Cuadrilla',
            store: eval("storeCuadrillas_" + i),
            displayField: 'nombre_cuadrilla',
            valueField: 'id_cuadrilla',
            layout: 'anchor',
            disabled: true
        });
        //******** html vacio...
        var iniHtmlVacio = '';
        Vacio = Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 600,
            padding: 8,
            layout: 'anchor',
            style: {color: '#000000'}
        });
        formPanelAsignacion = Ext.create('Ext.form.Panel', {
            width: 700,
            height: 150,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, RadiosTiposResponsable, Vacio1, combo_empleados, combo_cuadrillas, combo_empresas_externas, Vacio],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        asignarResponsable(origen, id);
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacion();
                    }
                }
            ]
        });
        Ext.getCmp('cmb_empleado_' + i).setVisible(true);
        Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);

        winAsignacion = Ext.widget('window', {
            title: 'Formulario Asignacion',
            width: 740,
            height: 200,
            minHeight: 200,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelAsignacion]
        });
        /*
         if(origen == "otro2" && panelAsignados)
         {
         winAsignacion = Ext.widget('window', {
         title: 'Formulario Asignacion',
         width: 740,
         height:500,
         minHeight: 500,
         layout: 'fit',
         resizable: false,
         modal: true,
         closabled: false,
         items: [formPanelAsignacion]
         });
         }
         else
         {
         winAsignacion = Ext.widget('window', {
         title: 'Formulario Asignacion',
         width: 740,
         height:200,
         minHeight: 200,
         layout: 'fit',
         resizable: false,
         modal: true,
         closabled: false,
         items: [formPanelAsignacion]
         });
         }*/
    }

    winAsignacion.show();
}

function cierraVentanaAsignacion() {
    winAsignacion.close();
    winAsignacion.destroy();
}

/************************************************************************ */
/***************** ASIGNACION INDIVIDUAL RESPONSABLE ******************** */
/*opcion 0 es coordinar y asignar, opcion 1 es asignar responsable */
/************************************************************************ */

function ejecutaGestionSimultanea(objInfoGestionSimultanea)
{
    Ext.MessageBox.wait("Gestionando solicitudes simultáneas...");
    Ext.Ajax.request({
        url: strUrlEjecutaSolsGestionSimultanea,
        method: 'post',
        timeout: 450000,
        params: objInfoGestionSimultanea,
        success: function(response){
            Ext.MessageBox.hide();
            var objData     = Ext.JSON.decode(response.responseText);
            var strStatus   = objData.status;
            var strMensaje  = objData.mensaje;
            if(strStatus == "OK") {
                Ext.Msg.alert('Mensaje', strMensaje, function (btn) {
                    if (btn == 'ok') {
                        store.load();
                        if(objInfoGestionSimultanea.strOpcionGestionSimultanea === "REPLANIFICAR")
                        {
                            var permiso1 = '{{ is_granted("ROLE_139-111") }}';
                            var boolPermiso1 = (Ext.isEmpty(permiso1)) ? false : (permiso1 ? true : false);
                            var permiso2 = '{{ is_granted("ROLE_139-112")  }}';
                            var boolPermiso2 = (Ext.isEmpty(permiso2)) ? false : (permiso2 ? true : false);
                            if (!boolPermiso1 || !boolPermiso2) {
                                showMenuAsignacion('otro', objInfoGestionSimultanea.intIdSolGestionada, false);
                            }
                        }
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error ', strMensaje);
            }
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function showProgramar(rec, origen, opcion)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";
    if (!winAsignacionIndividual)
    {
        var id_servicio     = rec.get("id_servicio");
        var id_factibilidad = rec.get("id_factibilidad");
        var tipo_solicitud  = rec.get("descripcionSolicitud");
        var boolEsHousing   = (rec.get('nombreTecnico') === 'HOUSING' || rec.get('nombreTecnico') === 'HOSTING');
        var tienePersonalizacionOpcionesGridCoordinar = "NO";
        var nombreOpcionPersonalizadaGridCoordinar = 'PROGRAMAR-' + tipo_solicitud.toUpperCase() + '-' + rec.get('nombreTecnico');
        if (typeof rec.get('arrayPersonalizacionOpcionesGridCoordinar') !== 'undefined' 
            && rec.get('arrayPersonalizacionOpcionesGridCoordinar').hasOwnProperty(nombreOpcionPersonalizadaGridCoordinar))
        {
            tienePersonalizacionOpcionesGridCoordinar = "SI";
        }
        
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });
        
        panelInfoAdicionalSolCoordinar = 
        {
            id:'panelInfoAdicionalSolCoordinar',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            defaults:
            {
                width: '740px',
            },
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa == "MD" && opcion == 0)
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: id_factibilidad,
                                    strOpcionGestionSimultanea: 'PLANIFICAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }
                                    Ext.getCmp('panelInfoAdicionalSolCoordinar').add({
                                        title: 'Gestión Simultánea',
                                        xtype: 'fieldset',
                                        defaultType: 'textfield',
                                        style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                        layout: 'anchor',
                                        defaults:
                                            {
                                                border: false,
                                                frame: false,
                                                width: '740px'
                                            },
                                        items: [
                                            {
                                                xtype: 'panel',
                                                border: false,
                                                style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                defaults:
                                                {
                                                    width: '650px',
                                                },
                                                layout: {type: 'hbox', align: 'stretch'},
                                                items: [
                                                    {                                                                                              
                                                        xtype: 'textfield',
                                                        hidden: true,
                                                        id:'tieneGestionSimultanea',
                                                        value:'SI'
                                                    },
                                                    Ext.create('Ext.Component', {
                                                        style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                        html: "<div>Esta solicitud #"+ id_factibilidad + " coordinará de manera simultánea "
                                                              +strInfoNumSolicitudes+"."+"</div>",
                                                        layout: 'anchor'
                                                    }),
                                                    {
                                                        id: 'btnMasDetalleSolsSimultaneas',
                                                        xtype: 'button',
                                                        text: 'Ver Detalle',
                                                        handler: function(){
                                                            Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                            Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                            Ext.getCmp('gridSolsSimultaneas').show();
                                                        }
                                                    },
                                                    {
                                                        id: 'btnMenosDetalleSolsSimultaneas',
                                                        xtype: 'button',
                                                        text: 'Ocultar Detalle',
                                                        hidden: true,
                                                        handler: function(){
                                                            Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                            Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                            Ext.getCmp('gridSolsSimultaneas').hide();
                                                        }
                                                    }
                                                ]
                                            },
                                            Ext.create('Ext.grid.Panel', {
                                                id: 'gridSolsSimultaneas',
                                                store: storeSolsSimultaneas,
                                                style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; margin-bottom: 10px",
                                                columnLines: true,
                                                hidden: true,
                                                columns: 
                                                [
                                                    {
                                                        id: 'descripServicioSimultaneo',
                                                        header: 'Plan/Producto',
                                                        dataIndex: 'descripServicioSimultaneo',
                                                        width: 130,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'estadoServicioSimultaneo',
                                                        header: 'Estado servicio',
                                                        dataIndex: 'estadoServicioSimultaneo',
                                                        width: 130,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'idSolSimultanea',
                                                        header: '# Solicitud',
                                                        dataIndex: 'idSolSimultanea',
                                                        width: 100,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'descripTipoSolSimultanea',
                                                        header: 'Tipo Solicitud',
                                                        dataIndex: 'descripTipoSolSimultanea',
                                                        width: 180,
                                                        sortable: true
                                                    },
                                                    {
                                                        id: 'estadoSolSimultanea',
                                                        header: 'Estado Solicitud',
                                                        dataIndex: 'estadoSolSimultanea',
                                                        width: 130,
                                                        sortable: true
                                                    }
                                                ],
                                                viewConfig: {
                                                    stripeRows: true
                                                },
                                                frame: true,
                                                defaults:
                                                {
                                                    width: '670px'
                                                }
                                            })
                                        ]
                                    });
                                }
                                else
                                {
                                    Ext.getCmp('panelInfoAdicionalSolCoordinar').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolCoordinar').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        Ext.getCmp('panelInfoAdicionalSolCoordinar').add(
                        {                                                                                              
                            xtype: 'textfield',
                            hidden: true,
                            id:'tieneGestionSimultanea',
                            value:'NO'
                        });
                        Ext.getCmp('panelInfoAdicionalSolCoordinar').doLayout();
                    }
                }
            }
        };
        
        itemTercerizadora = Ext.create('Ext.Component', {
            html: "<br>"
        });
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        }
        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
            title: "Manual",
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            autoScroll:true,
            items:
                [
                    {
                        xtype: 'panel',
                        border: false,
                        layout: {type: 'hbox', align: 'stretch'},
                        items: [
                            {
                                xtype: 'fieldset',
                                id: 'client-data-fieldset',
                                title: 'Datos del Cliente',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 15px;",
                                layout: 'anchor',
                                defaults: {
                                    width: '350px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Cliente',
                                        name: 'info_cliente',
                                        id: 'info_cliente',
                                        value: rec.get("cliente"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Login',
                                        name: 'info_login',
                                        id: 'info_login',
                                        value: rec.get("login2"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Ciudad',
                                        name: 'info_ciudad',
                                        id: 'info_ciudad',
                                        value: rec.get("ciudad"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Direccion',
                                        name: 'info_direccion',
                                        id: 'info_direccion',
                                        value: rec.get("direccion"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Sector',
                                        name: 'info_nombreSector',
                                        id: 'info_nombreSector',
                                        value: rec.get("nombreSector"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Es Recontratacion',
                                        name: 'es_recontratacion',
                                        id: 'es_recontratacion',
                                        value: rec.get("esRecontratacion"),
                                        allowBlank: false,
                                        readOnly: true
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldset',
                                title: 'Datos del Servicio',
                                id:'service-data-fieldset',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 15px;",
                                defaults: {
                                    width: '350px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Servicio',
                                        name: 'info_servicio',
                                        id: 'info_servicio',
                                        value: rec.get("productoServicio"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Tipo Orden',
                                        name: 'tipo_orden_servicio',
                                        id: 'tipo_orden_servicio',
                                        value: rec.get("tipo_orden"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Tipo Enlace',
                                        name: 'strTipoEnlace',
                                        id: 'strTipoEnlace',
                                        value: rec.get("strTipoEnlace"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    itemTercerizadora,
                                    {
                                        xtype: 'textarea',
                                        fieldLabel: 'Telefonos',
                                        name: 'telefonos_punto',
                                        id: 'telefonos_punto',
                                        value: rec.get("telefonos"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textarea',
                                        fieldLabel: 'Observacion',
                                        name: 'observacion_punto',
                                        id: 'observacion_punto',
                                        value: rec.get("observacion"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                ]
                            }
                        ]
                    },
                    panelInfoAdicionalSolCoordinar,
                    Vacio1
                ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var param = '';
                        var boolError = true;
                        var boolErrorTecnico = false;
                        var idPerTecnico = 0;
                        if (origen == "local")
                        {
                            id = rec.data.id_factibilidad;
                            param = rec.data.id_factibilidad;
                            
                            if ((prefijoEmpresa == "TN" && (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion"))
                                || (prefijoEmpresa == "TNP" && rec.get("ultimaMilla") == "FTTx" && tipo_solicitud == "Solicitud Planificacion"))
                            {
                                idPerTecnico = Ext.getCmp('cmb_tecnico').value;
                                if (!idPerTecnico)
                                {
                                    boolErrorTecnico = false;
                                }
                            }
                        } else if (origen == "otro" || origen == "otro2")
                        {
                            if (id == null || !id || id == 0 || id == "0" || id == "")
                            {
                                boolError = false;
                                Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
                            }
                        } else
                        {
                            boolError = false;
                            Ext.Msg.alert('Alerta', 'No hay opcion escogida');
                        }



                        if (boolErrorTecnico)
                        {
                            Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
                        } else
                        {
                            if (boolError)
                            {
                                var paramResponsables = '';
                                var boolErrorTareas = false;
                                var mensajeError = "";
                                for (i in tareasJS)
                                {
                                    var banderaEscogido         = $("input[name='tipoResponsable_" + i + "']:checked").val();
                                    var codigoEscogido          = "";
                                    var tituloError             = "";
                                    var idPersona               = "0";
                                    var idPersonaEmpresaRol     = "0";
                                    var strObservacion          = Ext.getCmp('txtObservacionPlanf_' + i).value;
                                    var strFechaProgramacion    = Ext.getCmp('fechaProgramacion_' + i).value;
                                    var strHoraInicio           = Ext.getCmp('ho_inicio_value_' + i).value;
                                    var strHoraFin              = Ext.getCmp('ho_fin_value_' + i).value;
                                    
                                    if (banderaEscogido == "empleado")
                                    {
                                        tituloError = "Empleado ";
                                        codigoEscogido = Ext.getCmp('cmb_empleado_' + i).value;
                                    }
                                    if (banderaEscogido == "cuadrilla")
                                    {
                                        tituloError = "Cuadrilla";
                                        codigoEscogido = Ext.getCmp('cmb_cuadrilla_' + i).value;
                                        idPersona = Ext.getCmp('idPersona_' + i).getValue();
                                        idPersonaEmpresaRol = Ext.getCmp('idPersonaEmpresaRol_' + i).getValue();
                                    }
                                    if (banderaEscogido == "empresaExterna")
                                    {
                                        tituloError = "Contratista";
                                        codigoEscogido = Ext.getCmp('cmb_empresa_externa_' + i).value;
                                    }
                                    if (!strObservacion || strObservacion == "" || strObservacion == 0)
                                    {
                                        tituloError = "La observacion no fue ingresada, por favor ingrese.";
                                        boolErrorTareas = true;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Observación: " + tituloError + "<br>";
                                    }
                                    
                                    if (!strFechaProgramacion || strFechaProgramacion == "" || strFechaProgramacion == 0)
                                    {
                                        tituloError = "La fecha de Programacion no fue seleccionada, por favor seleccione.";
                                        boolErrorTareas = true;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Fecha Programación: " + tituloError + "<br>";
                                    }
                                    
                                    if (!strHoraInicio || strHoraInicio == "" || strHoraInicio == 0)
                                    {
                                        tituloError = "La hora de inicio no fue seleccionada, por favor seleccione";
                                        boolErrorTareas = true;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Hora Inicio: " + tituloError + "<br>";
                                    }
                                    
                                    if (!strHoraFin || strHoraFin == "" || strHoraFin == 0)
                                    {
                                        tituloError = "La hora de inicio no fue seleccionada, por favor seleccione";
                                        boolErrorTareas = true;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Hora Fin: " + tituloError + "<br>";
                                    }
                                    
                                    if (codigoEscogido && codigoEscogido != "")
                                    {
                                        paramResponsables = paramResponsables + +tareasJS[i]['idTarea'] + "@@" + banderaEscogido + "@@" + 
                                            codigoEscogido + "@@" + idPersona + "@@" + idPersonaEmpresaRol;
                                        if (i < (tareasJS.length - 1))
                                        {
                                            paramResponsables = paramResponsables + '|';
                                        }
                                    } else
                                    {
                                        boolErrorTareas = false;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Combo: " + tituloError + "<br>";
                                    }
                                }//FIN FOR
                                
                                var id_factibilidad = rec.get("id_factibilidad");
                                var id_factibilidad = rec.get("id_factibilidad");
                                var descripcionSolicitud = rec.get("descripcionSolicitud");
                                var boolError = false;
                                
                                var txtObservacion = Ext.getCmp('txtObservacionPlanf_0').value;
                                var fechaProgramacion = Ext.getCmp('fechaProgramacion_0').value;
                                var ho_inicio = Ext.getCmp('ho_inicio_value_0').value;
                                var ho_fin = Ext.getCmp('ho_fin_value_0').value;
                                
                                if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                                {
                                    boolError = true;
                                }
                                
                                if (!boolErrorTareas && !boolError)
                                {
                                    strMensaje = "Se asignará el responsable. Desea continuar?";
                                    if (paramResponsables == "")
                                    {
                                        strMensaje = "Se Coordinará la planificación. Desea continuar?";
                                    }

                                    Ext.Msg.confirm('Alerta', strMensaje, function(btn) {
                                        if (btn == 'yes') {
                                            connAsignarResponsable.request({
                                                url: "../../planificar/coordinar/programar",
                                                method: 'post',
                                                timeout: 450000,
                                                params: {
                                                    origen: origen,
                                                    id: id,
                                                    param: param,
                                                    paramResponsables: paramResponsables,
                                                    idPerTecnico: idPerTecnico,
                                                    fechaProgramacion: fechaProgramacion,
                                                    ho_inicio: ho_inicio,
                                                    ho_fin: ho_fin,
                                                    observacion: txtObservacion,
                                                    opcion: opcion,
                                                    idIntWifiSim: JSON.stringify(rec.data.idIntWifiSim),
                                                    idIntCouSim : JSON.stringify(rec.data.idIntCouSim),
                                                    arraySimultaneos: JSON.stringify(rec.data.arraySimultaneos),
                                                    esHal: 'N',
                                                    tienePersonalizacionOpcionesGridCoordinar: tienePersonalizacionOpcionesGridCoordinar
                                                },
                                                success: function(response) {
                                                    var text        = response.responseText;
                                                    var intPosicion = text.indexOf("Correctamente");

                                                    if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                                        text == "Se coordinó la solicitud" || intPosicion !== -1)
                                                    {
                                                        var tieneGestionSimultanea = Ext.getCmp('tieneGestionSimultanea').value;
                                                        cierraVentanaAsignacionIndividual();
                                                        if(prefijoEmpresa == "MD" && tieneGestionSimultanea === "SI" && opcion == 0)
                                                        {
                                                            var objInfoGestionSimultanea = {
                                                                strOpcionGestionSimultanea:         "PLANIFICAR",
                                                                intIdSolGestionada:                 id,
                                                                strOrigen:                          origen,
                                                                strParamResponsables:               paramResponsables,
                                                                strFechaProgramacion:               fechaProgramacion,
                                                                strFechaHoraInicioProgramacion:     ho_inicio,
                                                                strFechaHoraFinProgramacion:        ho_fin,
                                                                strMensajeEjecucionSolGestionada:   text
                                                            };
                                                            ejecutaGestionSimultanea(objInfoGestionSimultanea);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                                if (btn == 'ok') {
                                                                    store.load();
                                                                }
                                                            });
                                                        }
                                                    } else {
                                                        var mm = Ext.Msg.alert('Alerta', text);
                                                        Ext.defer(function() {
                                                            mm.toFront();
                                                        }, 50);
                                                    }
                                                },
                                                failure: function(result) {
                                                    Ext.Msg.alert('Alerta', result.responseText);
                                                }
                                            });
                                        }
                                    });
                                } else
                                {
                                    Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
                                }
                            }
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ]
        });

        /*Si el servicio posee un id de Internet Wifi, significa que es instalacion Simultanea
        * y se le agregaría un campo para que PYL pueda notarlo*/
        if (rec.get('idIntWifiSim'))
        {
            Ext.getCmp('client-data-fieldset').add({
                xtype: 'textfield',
                cls:'animated bounceIn',
                fieldLabel: 'Instalación Simultánea',
                name: 'es_instalacionSimultanea',
                id: 'es_instalacionSimultanea',
                value: 'INTERNET WIFI → Total de AP\'s: «' + rec.get('idIntWifiSim').length + "»",
                allowBlank: true,
                readOnly: true
            });
        }

        /*Si el servicio posee arraySimultaneos, significa que es instalacion Simultanea
        * y se le agregaría un campo para que PYL pueda notarlo*/
        if (typeof rec.get('arraySimultaneos') !== 'undefined' &&
        rec.get('arraySimultaneos') >= 1)
        {
            Ext.getCmp('client-data-fieldset').add({
                xtype: 'textfield',
                cls:'animated bounceIn',
                fieldLabel: 'Instalación Simultánea',
                name: 'es_instalacionSimultanea',
                id: 'es_instalacionSimultanea',
                value: '« SI »',
                allowBlank: true,
                readOnly: true
            });
        }
        
        /*Si el servicio posee un id de COU LINEAS TELEFONIA FIJA, significa que es instalacion Simultanea
        * y se le agregaría un campo para que PYL pueda notarlo*/
        if (rec.get('idIntCouSim'))
        {
            Ext.getCmp('client-data-fieldset').add({
                xtype: 'textfield',
                cls:'animated bounceIn',
                fieldLabel: 'Instalación Simultánea',
                name: 'es_instalacionSimultanea',
                id: 'es_instalacionSimultanea',
                value: 'COU LINEAS TELEFONIA FIJA ',
                allowBlank: true,
                readOnly: true
            });
        }
        /* Funcion para agregar el label del tipo de red. */
        agregarLabelTipoRed(rec);

        combo_tecnicos = Ext.create('Ext.Component', {
            html: ""
        });
        if ((prefijoEmpresa == "TN" && (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")) 
            || (prefijoEmpresa == "TNP" && rec.get("ultimaMilla") == "FTTx" && tipo_solicitud == "Solicitud Planificacion"))
        {
            storeTecnicos = new Ext.data.Store
                ({
                    total: 'total',
                    pageSize: 25,
                    listeners: {
                    },
                    proxy:
                        {
                            type: 'ajax',
                            method: 'post',
                            url: '../../planificar/asignar_responsable/getTecnicos',
                            reader:
                                {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                            extraParams: {
                                query: '',
                                'tipo_esquema': rec.get("tipo_esquema")
                            },
                            actionMethods:
                                {
                                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                }
                        },
                    fields:
                        [
                            {name: 'id_tecnico', mapping: 'idPersonaEmpresaRol'},
                            {name: 'nombre_tecnico', mapping: 'info_adicional'},
                        ],
                    autoLoad: true
                });
            const strIngeniero = rec.get("tipo_esquema") && rec.get("tipo_esquema") == 1 ? 'RADIO' : 'IPCCL2';
            
            if (rec.get('producto') === 'Cableado Estructurado')
            {
                combo_tecnicos = new Ext.form.ComboBox({
                    id: 'cmb_tecnico',
                    name: 'cmb_tecnico',
                    fieldLabel: `Ingeniero Activación`,
                    anchor: '100%',
                    queryMode: 'remote',
                    emptyText: `Seleccione Ingeniero Activación`,
                    width: 350,
                    store: storeTecnicos,
                    displayField: 'nombre_tecnico',
                    valueField: 'id_tecnico',
                    layout: 'anchor',
                    disabled: false
                });
            }
            else
            {
                combo_tecnicos = new Ext.form.ComboBox({
                    id: 'cmb_tecnico',
                    name: 'cmb_tecnico',
                    fieldLabel: `Ingeniero ${strIngeniero}`,
                    anchor: '100%',
                    queryMode: 'remote',
                    emptyText: `Seleccione Ingeniero ${strIngeniero}`,
                    width: 350,
                    store: storeTecnicos,
                    displayField: 'nombre_tecnico',
                    valueField: 'id_tecnico',
                    layout: 'anchor',
                    disabled: false
                });
            }
            
        }

        /*Si el producto requiere trabajo por mas departamentos se hace la validación para que se visualicen las tareas por departamentos*/
        connTareas.request({
            method: 'POST',
            url: "../../factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
            params: {servicioId: id_servicio, id_solicitud: id_factibilidad, nombreTarea: 'todas', estado: 'Activo'},
            success: function(response) {
                var data = Ext.JSON.decode(response.responseText.trim());
                if (data)
                {
                    totalTareas = data.total;
                    if (totalTareas > 0)
                    {
                        tareasJS = data.encontrados;
                        for (i in tareasJS)
                        {
                            //******** hidden id tarea
                            var hidden_tarea = new Ext.form.Hidden({
                                id: 'hidden_id_tarea_' + i,
                                name: 'hidden_id_tarea_' + i,
                                value: tareasJS[i]["idTarea"]
                            });
                            //******** text nombre tarea
                            var text_tarea = new Ext.form.Label({
                                forId: 'txt_nombre_tarea_' + i,
                                style: "font-weight:bold; font-size:14px; color:red; margin-bottom: 15px;",
                                layout: 'anchor',
                                hidden: boolEsHousing,
                                text: tareasJS[i]["nombreTarea"]
                            });
                            var extraParamsEmpleadosPorPerfil = "";
                            //******* id del departamento
                            var intIdDepartamento = tareasJS[i]["idDepartamento"]
                            //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
                            var strIniHtml = '';
                            if (prefijoEmpresa == "TNP")
                            {
                                strIniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i +
                                             ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                                             '';
                            }
                            else
                            {
                                if (typeof rec.get('arrayPersonalizacionOpcionesGridCoordinar') !== 'undefined'
                                    && rec.get('arrayPersonalizacionOpcionesGridCoordinar').hasOwnProperty(nombreOpcionPersonalizadaGridCoordinar))
                                {
                                    var arrayInfoPersonalizacionPlanificar  = 
                                        rec.get('arrayPersonalizacionOpcionesGridCoordinar')[nombreOpcionPersonalizadaGridCoordinar].split("|");
                                    var arrayTipoAsignacionesPermitidas     = arrayInfoPersonalizacionPlanificar[0].split(";");
                                    var arrayPerfilesAsignacionesPermitidas = arrayInfoPersonalizacionPlanificar[1].split(";");
                                    var valueTipoAsignacionPermitida = "";
                                    var nombreTipoAsignacionPermitida = "";
                                    var contadorArrayTipoAsignacionesPermitidas = 0;
                                    var valueChecked = "";
                                    var arrayInfoTipoAsignacionPermitida = [];
                                    let lengthTipoAsignacionesPermitidas = arrayTipoAsignacionesPermitidas.length;
                                    for (let tipoAsignacionesPermitidas of arrayTipoAsignacionesPermitidas) {
                                        arrayInfoTipoAsignacionPermitida = tipoAsignacionesPermitidas.split("-");
                                        valueTipoAsignacionPermitida = arrayInfoTipoAsignacionPermitida[0];
                                        nombreTipoAsignacionPermitida = arrayInfoTipoAsignacionPermitida[1];
                                        if(valueTipoAsignacionPermitida === "empleado")
                                        {
                                            extraParamsEmpleadosPorPerfil = 
                                                " extraParams: "+
                                                " {" +
                                                "     aplicaFiltroEmpleadosXPerfil: 'SI', " +
                                                "     nombrePerfilEmpleadosXAsignar: '" +
                                                        arrayPerfilesAsignacionesPermitidas[contadorArrayTipoAsignacionesPermitidas]+"' "+
                                                " }, ";
                                        }
                                        contadorArrayTipoAsignacionesPermitidas++;
                                        if(contadorArrayTipoAsignacionesPermitidas === 1)
                                        {
                                           valueChecked = 'checked="" ';
                                        }
                                        else
                                        {
                                            valueChecked = "";
                                        }
                                        strIniHtml = strIniHtml 
                                           + '<input type="radio" '
                                           + 'onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" '+valueChecked
                                           + 'value="'+valueTipoAsignacionPermitida+'" name="tipoResponsable_' + i + '">'
                                           + '&nbsp;'+nombreTipoAsignacionPermitida;
                                        if(contadorArrayTipoAsignacionesPermitidas !== lengthTipoAsignacionesPermitidas)
                                        {
                                            strIniHtml = strIniHtml + '&nbsp;&nbsp;';
                                        }
                                        arrayInfoTipoAsignacionPermitida = [];
                                    }                                    
                                }
                                else
                                {
                                    strIniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="cuadrilla" name="tipoResponsable_' + i + '">&nbsp;Cuadrilla' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="empresaExterna" name="tipoResponsable_' + i + '">&nbsp;Contratista' +
                                    '';
                                }
                            }
                            
                            RadiosTiposResponsable = Ext.create('Ext.Component', {
                                html: strIniHtml,
                                width: 350,
                                padding: 10,
                                hidden: boolEsHousing,
                                style: {color: '#000000'}
                            });
                            // **************** EMPLEADOS ******************
                            Ext.define('EmpleadosList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empleado', type: 'int'},
                                    {name: 'nombre_empleado', type: 'string'}
                                ]
                            });                 
                            eval("var storeEmpleados_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpleados_" + i + "', " +
                                "  model: 'EmpleadosList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpleados'," +
                                extraParamsEmpleadosPorPerfil +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_empleados = new Ext.form.ComboBox({
                                id: 'cmb_empleado_' + i,
                                name: 'cmb_empleado_' + i,
                                fieldLabel: "Empleados",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Empleado',
                                store: eval("storeEmpleados_" + i),
                                displayField: 'nombre_empleado',
                                valueField: 'id_empleado',
                                layout: 'anchor',
                                disabled: false
                            });
                            // ****************  EMPRESA EXTERNA  ******************
                            Ext.define('EmpresaExternaList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empresa_externa', type: 'int'},
                                    {name: 'nombre_empresa_externa', type: 'string'}
                                ]
                            });
                            eval("var storeEmpresaExterna_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpresaExterna_" + i + "', " +
                                "  model: 'EmpresaExternaList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  },actionMethods: { " +
                                " create: 'POST', read: 'POST', update: 'POST', destroy: 'POST' " +
                                " }, " +
                                "  }" +
                                " });    ");
                            combo_empresas_externas = new Ext.form.ComboBox({
                                id: 'cmb_empresa_externa_' + i,
                                name: 'cmb_empresa_externa_' + i,
                                fieldLabel: "Contratista",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Contratista',
                                store: eval("storeEmpresaExterna_" + i),
                                displayField: 'nombre_empresa_externa',
                                valueField: 'id_empresa_externa',
                                layout: 'anchor',
                                visible: !boolEsHousing,
                                disabled: true
                            });
                            // **************** CUADRILLAS ******************
                            Ext.define('CuadrillasList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_cuadrilla', type: 'int'},
                                    {name: 'nombre_cuadrilla', type: 'string'}
                                ]
                            });
                            eval("var storeCuadrillas_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeCuadrillas_" + i + "', " +
                                "  model: 'CuadrillasList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }," +
                                " extraParams: { " +
                                "   idDepartamento: " + intIdDepartamento +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_cuadrillas = new Ext.form.ComboBox({
                                id: 'cmb_cuadrilla_' + i,
                                name: 'cmb_cuadrilla_' + i,
                                fieldLabel: "Cuadrilla",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Cuadrilla',
                                store: eval("storeCuadrillas_" + i),
                                displayField: 'nombre_cuadrilla',
                                valueField: 'id_cuadrilla',
                                layout: 'anchor',
                                hidden: boolEsHousing,
                                disabled: true,
                                listeners: {
                                    select: function(combo) {

                                        seteaLiderCuadrilla(combo.getId(), combo.getValue());
                                    }
                                }
                            });
                            //******** html vacio...
                            var iniHtmlVacio = '';
                            Vacio = Ext.create('Ext.Component', {
                                html: iniHtmlVacio,
                                width: 350,
                                padding: 8,
                                layout: 'anchor',
                                style: {color: '#000000'}
                            });
                            formPanel = Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                waitMsgTarget: true,
                                id: 'panelLiderCuadrilla_' + i,
                                name: 'panelLiderCuadrilla_' + i,
                                height: 80,
                                width: 350,
                                hidden: boolEsHousing,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    msgTarget: 'side'
                                },
                                items:
                                    [
                                        {
                                            xtype: 'fieldset',
                                            title: 'Lider de Cuadrilla',
                                            defaultType: 'textfield',
                                            items:
                                                [
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Persona:',
                                                        id: 'idPersona_' + i,
                                                        name: 'idPersona_' + i,
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'PersonaEmpresaRol:',
                                                        id: 'idPersonaEmpresaRol_' + i,
                                                        name: 'idPersonaEmpresaRol_' + i,
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Nombre:',
                                                        id: 'nombreLider_' + i,
                                                        name: 'nombreLider_' + i,
                                                        value: ""
                                                    }
                                                ]
                                        }
                                    ]
                            });
                            feIni = new Date();
                            hoIni = "00:00";
                            hoFin = "00:30";
                            if (rec.get("fePlanificada") !== "")
                            {

                                var strFecha = rec.get("fePlanificada");
                                var dia = strFecha.substr(0, 2);
                                var mes = strFecha.substr(3, 2);
                                var anio = strFecha.substr(6, 4);

                                feIni = new Date(anio, mes - 1, dia);
                                hoIni = rec.get("HoraIniPlanificada");
                                hoFin = rec.get("HoraFinPlanificada");
                            }

                            DTFechaProgramacion = Ext.create('Ext.data.fecha', {
                                id: 'fechaProgramacion_' + i,
                                name: 'fechaProgramacion_' + i,
                                fieldLabel: '* Fecha',
                                minValue: new Date(),
                                value: feIni,
                                labelStyle: "color:red;"
                            });
                            THoraInicio = Ext.create('Ext.form.TimeField', {
                                fieldLabel: '* Hora Inicio',
                                format: 'H:i',
                                id: 'ho_inicio_value_' + i,
                                name: 'ho_inicio_value_' + i,
                                minValue: '00:01 AM',
                                maxValue: '22:59 PM',
                                increment: 30,
                                value: hoIni,
                                editable: false,
                                labelStyle: "color:red;",
                                listeners: {
                                    select: {fn: function(valorTime, value) {
                                            
                                            var strValor                = valorTime.getId();
                                            var valueEscogido           = valorTime.getValue();
                                            var valueEscogido2          = new Date(valueEscogido);
                                            var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 30);
                                            var horaTotal               = new Date(valueEscogidoAumentMili);
                                            
                                            var h   = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                                            var m   = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                                            
                                            var horasTotalFormat = h + ":" + m;
                                            
                                            var strValorI = strValor.substr(16,1)
                                            Ext.getCmp('ho_fin_value_' + strValorI).setMinValue(horaTotal);
                                            $("input[name='ho_fin_value_" + strValorI + "']'").val(horasTotalFormat);
                                        }}
                                }
                            });
                            THoraFin = Ext.create('Ext.form.TimeField', {
                                fieldLabel: '* Hora Fin',
                                format: 'H:i',
                                id: 'ho_fin_value_' + i,
                                name: 'ho_fin_value_' + i,
                                minValue: '00:30 AM',
                                maxValue: '23:59 PM',
                                increment: 30,
                                value: hoFin,
                                editable: false,
                                labelStyle: "color:red;"
                            });
                            var txtObservacionPlanf = Ext.create('Ext.form.TextArea',
                                {
                                    fieldLabel: '',
                                    name: 'txtObservacionPlanf_' + i,
                                    id: 'txtObservacionPlanf_' + i,
                                    value: rec.get("observacionOpcionPyl"),
                                    allowBlank: false,
                                    width: 300,
                                    height: 150,
                                    listeners:
                                        {
                                            blur: function(field)
                                            {
                                                observacionPlanF = field.getValue();
                                            }
                                        }
                                });
                            var container = Ext.create('Ext.container.Container',
                                {
                                    layout: {
                                        type: 'hbox'
                                    },
                                    width: 900,
                                    items: [
                                        {
                                            xtype: 'panel',
                                            border: false,
                                            layout: {type: 'hbox', align: 'stretch'},
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    defaultType: 'textfield',
                                                    style: "font-weight:bold; margin-bottom: 15px; border-right:none",
                                                    layout: 'anchor',
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: false,
                                                            frame: false
                                                        },
                                                    items: [DTFechaProgramacion,
                                                        THoraInicio,
                                                        THoraFin,
                                                        hidden_tarea,
                                                        text_tarea,
                                                        RadiosTiposResponsable,
                                                        combo_empleados,
                                                        combo_cuadrillas,
                                                        combo_empresas_externas,
                                                        formPanel,
                                                        Vacio,
                                                        combo_tecnicos,
                                                        Vacio]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    style: "margin-bottom: 15px; border-left:none",
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: true,
                                                            frame: false

                                                        },
                                                    items: [
                                                        {html: "Observación de Planificación:", border: false, width: 325},
                                                        txtObservacionPlanf]
                                                }]
                                        }]
                                });
                            formPanelAsignacionIndividual.items.add(container);
                            combo_tecnicos.setVisible(false);
                            if ((prefijoEmpresa == "TN" && (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")) 
                                || (prefijoEmpresa == "TNP" && rec.get("ultimaMilla") == "FTTx" && tipo_solicitud == "Solicitud Planificacion"))
                            {
                                combo_tecnicos.setVisible(true);
                            }
                            
                            if(rec.get("muestraIngL2") == "N")
                            {
                                combo_tecnicos.setVisible(false);
                            }    

                            Ext.getCmp('cmb_empleado_' + i).setVisible(true);
                            Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
                            Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
                            Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);

                            //Para productos housing a coordinar no existe asignacion de responsable
                            if (boolEsHousing)
                            {
                                combo_tecnicos.setVisible(false);
                                Ext.getCmp('cmb_empleado_' + i).setVisible(false);
                                Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
                                Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
                                Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);
                            }

                            container.doLayout();
                            formPanelAsignacionIndividual.doLayout();
                        }
                        //formPanelAsignacionIndividual.doLayout();
                        formPanelHalPrincipal = crearFormPanelHal('planificar', rec, origen, opcion, false);

                        var tabs = new Ext.TabPanel({
                            xtype     :'tabpanel',
                            activeTab : 0,
                            autoScroll: false,
                            layoutOnTabChange: true,
                            items: [formPanelAsignacionIndividual,formPanelHalPrincipal]
                        });

                        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Formulario Asignacion Individual',
                            layout: 'fit',
                            resizable: false,
                            modal: true,
                            closable: false,
                            items: (boolPermisoAsignarTareaHal ? [tabs] : [formPanelAsignacionIndividual])
                        });

                        if (rec.get('producto') === 'WIFI Alquiler Equipos')
                        {
                            winAsignacionIndividual.on('afterrender', function() {
                                connGetAsignadosTarea.request({
                                    method: 'POST',
                                    url: "../coordinar/getAsignadosTarea",
                                    params: {
                                        servicioId: id_servicio,
                                        idSolicitud: id_factibilidad,
                                        idPunto: rec.get('id_punto')
                                    },
                                    success: function(response) {
                                        var data = Ext.JSON.decode(response.responseText.trim());
                                        let status = data.status ? data.status : null;
                                        
                                        if (status)
                                        {
                                            var messagebox=  Ext.MessageBox.show({
                                                title: 'Información Importante',
                                                msg: data.data,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.INFO
                                            });

                                            Ext.Function.defer(function () {
                                                messagebox.zIndexManager.bringToFront(messagebox);
                                            },100);

                                        }
                                    }
                                });
                            });
                        }
                        winAsignacionIndividual.show();
                    } else
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "No se han podido obtener tareas asociadas a este servicio. Por favor informe a Sistemas.",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                } else {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: "Ocurrio un Error en la Obtencion de las Tareas",
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            },
            failure: function(result) {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: result.responseText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });

    }

}
/**
 * Funcion que permite agregar un label con la informacion del tipo de red.
 *
 * @param {*} rec ➜ Representa el objeto del servicio.
 * @returns {*}
 * 
 * @author Pablo Pin 
 * @version 1.0 02-10-2019 - Versión Inicial.
 * 
 */
function agregarLabelTipoRed(rec) {

    /*Si el elemento cuenta con tipo de red, significa que pertenece a GPON y
    mostrará un textField con la información del tipo de red.*/
    if (rec.get('strTipoRed'))
    {
        objFieldStyle = {
            'backgroundColor': '#F0F2F2',
            'backgrodunImage': 'none',
            'color': 'green'
        };

        Ext.getCmp('service-data-fieldset').add({
            xtype: 'textfield',
            fieldCls:'animated bounceIn details-disabled',
            fieldLabel: 'Tipo de red',
            name: 'tipo_red',
            id: 'tipo_red',
            value: typeof rec.get('strTipoRed') != undefined ? "«" + rec.get('strTipoRed') + "»" : '',
            allowBlank: true,
            readOnly: true,
            hidden: typeof rec.get('strPrefijoEmpresa') != undefined && rec.get('strPrefijoEmpresa') != "TN",
            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};
                color:${objFieldStyle.color};`
        });
    }
}

function cierraVentanaAsignacionIndividual() {
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}

function showRePlanificar(rec, origen, boolPermisoOpu)
{
    winRePlanificar = "";
    formRePlanificar = "";
    tituloCoordinar = rec.get("tituloCoordinar");
    var id_servicio = rec.get("id_servicio");
    var id_factibilidad = rec.get("id_factibilidad");
    var tipo_solicitud = rec.get("descripcionSolicitud");
    boolVisible = true;

    if (tituloCoordinar == '')
    {
        tituloCoordinar = 'Replanificar Instalación';
    }

    if (!winRePlanificar)
    {
        cmbMotivosRePlanificacion = Ext.create('Ext.data.comboMotivosRePlanificacion', {
            id: 'cmbMotivoRePlanificacion',
            name: 'cmbMotivoRePlanificacion',
            fieldLabel: '* Motivo',
            labelStyle: "color:red;"});
        DTFechaProgramacion = new Ext.form.DateField({
            id: 'fechaProgramacion',
            fieldLabel: 'Fecha Actual',
            labelAlign: 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            editable: false,
            disabled: true,
            value: rec.get("fePlanificada")
                //anchor : '65%',
                //layout: 'anchor'
        });
        if (boolPermisoOpu && (prefijoEmpresa != "TNP") && 
            (rec.get("descripcionSolicitud") == "Solicitud Planificacion" ||
             rec.get("descripcionSolicitud") == "Solicitud De Instalacion Cableado Ethernet"))
        {
            DTFechaReplanificacion = Ext.create('Ext.Component', {
                html: "<br>"
            });
        } else
        {
            DTFechaReplanificacion = new Ext.form.DateField({
                id: 'fechaReplanificacion',
                fieldLabel: '* Fecha Replanificación',
                labelAlign: 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                editable: false,
                minValue: new Date(),
                value: new Date(),
                labelStyle: "color:red; visible:false",
                visible: boolVisible

                    //anchor : '65%',
                    //layout: 'anchor'

            });
        }
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }
        
        var agregaItemInfoAdicional = "NO";
        var textoInfoAdicional      = "";
        if (rec.data.estado == "Asignada" && rec.get('producto') !== "WIFI Alquiler Equipos") {
            agregaItemInfoAdicional = "SI";
            textoInfoAdicional      = "Solicitud con estado Asignada. Se eliminarán todos los datos técnicos y se liberarán los "
                                      +"recursos de BackBone.";
        }
        
        panelInfoAdicionalSolReplanif = Ext.create('Ext.container.Container',
        {
            id:'panelInfoAdicionalSolReplanif',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            defaults:
            {
                width: '740px',
            },
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa === "MD")
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: id_factibilidad,
                                    strOpcionGestionSimultanea: 'REPLANIFICAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }
                                    
                                    if(agregaItemInfoAdicional === "SI")
                                    {
                                        Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                            Ext.create('Ext.Component', {
                                                width: '700px',
                                                html: '<div class="warningmessage">'+textoInfoAdicional+'</div>',
                                            })
                                        );
                                    }
                                    
                                    Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                        {
                                            title: 'Gestión Simultánea',
                                            xtype: 'fieldset',
                                            defaultType: 'textfield',
                                            style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                            layout: 'anchor',
                                            defaults:
                                                {
                                                    border: false,
                                                    frame: false,
                                                    width: '740px'
                                                },
                                            items: [
                                                {
                                                    xtype: 'panel',
                                                    border: false,
                                                    style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                    defaults:
                                                    {
                                                        width: '650px',
                                                    },
                                                    layout: {type: 'hbox', align: 'stretch'},
                                                    items: [
                                                        {                                                                                              
                                                            xtype: 'textfield',
                                                            hidden: true,
                                                            id:'tieneGestionSimultanea',
                                                            value:'SI'
                                                        },
                                                        Ext.create('Ext.Component', {
                                                            style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                            html: "<div>Esta solicitud #"+ id_factibilidad + " replanificará de manera simultánea "
                                                                  +strInfoNumSolicitudes+"."+"</div>",
                                                            layout: 'anchor'
                                                        }),
                                                        {
                                                            id: 'btnMasDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ver Detalle',
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('gridSolsSimultaneas').show();
                                                            }
                                                        },
                                                        {
                                                            id: 'btnMenosDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ocultar Detalle',
                                                            hidden: true,
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('gridSolsSimultaneas').hide();
                                                            }
                                                        }
                                                    ]
                                                },
                                                Ext.create('Ext.grid.Panel', {
                                                    id: 'gridSolsSimultaneas',
                                                    store: storeSolsSimultaneas,
                                                    style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; "
                                                           +"margin-bottom: 10px",
                                                    columnLines: true,
                                                    hidden: true,
                                                    columns: 
                                                    [
                                                        {
                                                            id: 'descripServicioSimultaneo',
                                                            header: 'Plan/Producto',
                                                            dataIndex: 'descripServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoServicioSimultaneo',
                                                            header: 'Estado servicio',
                                                            dataIndex: 'estadoServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'idSolSimultanea',
                                                            header: '# Solicitud',
                                                            dataIndex: 'idSolSimultanea',
                                                            width: 100,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'descripTipoSolSimultanea',
                                                            header: 'Tipo Solicitud',
                                                            dataIndex: 'descripTipoSolSimultanea',
                                                            width: 180,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoSolSimultanea',
                                                            header: 'Estado Solicitud',
                                                            dataIndex: 'estadoSolSimultanea',
                                                            width: 130,
                                                            sortable: true
                                                        }
                                                    ],
                                                    viewConfig: {
                                                        stripeRows: true
                                                    },
                                                    frame: true,
                                                    defaults:
                                                    {
                                                        width: '670px'
                                                    }
                                                })
                                            ]
                                        }
                                    );
                                }
                                else
                                {
                                    if(agregaItemInfoAdicional === "SI")
                                    {
                                        Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                            Ext.create('Ext.Component', {
                                                width: '700px',
                                                html: '<p style="" class="warningmessage">'+textoInfoAdicional+'</p>',
                                            })
                                        );
                                    }

                                    Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolReplanif').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        if(agregaItemInfoAdicional === "SI")
                        {
                            Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                                Ext.create('Ext.Component', {
                                    width: '700px',
                                    html: '<p style="" class="warningmessage">'+textoInfoAdicional+'</p>',
                                })
                            );
                        }

                        Ext.getCmp('panelInfoAdicionalSolReplanif').add(
                            {                                                                                              
                                xtype: 'textfield',
                                hidden: true,
                                id:'tieneGestionSimultanea',
                                value:'NO'
                            },
                        );
                        Ext.getCmp('panelInfoAdicionalSolReplanif').doLayout();
                    }
                }
            }
        });
        
//******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            // width: 600,
            // padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        formRePlanificar = Ext.create('Ext.form.Panel', {
            title: "Manual",
            buttonAlign: 'center',
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            layout: {
            },
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            layout: 'anchor',
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: rec.get("esRecontratacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Punto',
                            defaultType: 'textfield',
                            id: 'service-data-fieldset',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("productoServicio"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Enlace',
                                    name: 'tipoEnlace',
                                    id: 'tipoEnlace',
                                    value: rec.get("strTipoEnlace"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                itemTercerizadora,
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Telefonos',
                                    name: 'telefonos_punto',
                                    id: 'telefonos_punto',
                                    value: rec.get("telefonos"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion_punto',
                                    id: 'observacion_punto',
                                    value: rec.get("observacion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                            ]
                        }
                    ]
                },
                panelInfoAdicionalSolReplanif
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var txtObservacion  = Ext.getCmp('txtObservacionPlanf').value;
                        var cmbMotivo       = Ext.getCmp('cmbMotivoRePlanificacion').value;
                        var boolPerfilOpu   = true;
                        var id_factibilidad = rec.get("id_factibilidad");
                        if (prefijoEmpresa == "TNP" || !boolPermisoOpu || (boolPermisoOpu && 
                            (rec.get("descripcionSolicitud") != "Solicitud Planificacion" &&
                             rec.get("descripcionSolicitud") != "Solicitud De Instalacion Cableado Ethernet")))
                        {
                            boolPerfilOpu            = false;
                            var fechaReplanificacion = Ext.getCmp('fechaReplanificacion').value;
                            var ho_inicio            = Ext.getCmp('ho_inicio_value').value;
                            var ho_fin               = Ext.getCmp('ho_fin_value').value;
                            var boolError            = false;
                            var mensajeError = "";
                            if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                            {
                                boolError = true;
                                mensajeError += "El id del Detalle Solicitud no existe.\n";
                            }
                            if (!fechaReplanificacion || fechaReplanificacion == "" || fechaReplanificacion == 0)
                            {
                                boolError = true;
                                mensajeError += "La fecha de Replanificación no fue seleccionada, por favor seleccione.\n";
                            }
                            if (!ho_inicio || ho_inicio == "" || ho_inicio == 0)
                            {
                                boolError = true;
                                mensajeError += "La hora de inicio no fue seleccionada, por favor seleccione.\n";
                            }
                            if (!ho_fin || ho_fin == "" || ho_fin == 0)
                            {
                                boolError = true;
                                mensajeError += "La hora de fin no fue seleccionada, por favor seleccione.\n";
                            }
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }

                        var param = '';
                        var boolErrorTecnico = false;
                        var idPerTecnico = 0;
                        if (origen == "local")
                        {
                            id = rec.data.id_factibilidad;
                            param = rec.data.id_factibilidad;
                            if (prefijoEmpresa == "TN" 
                                && (rec.data.descripcionSolicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion"))
                            {
                                idPerTecnico = Ext.getCmp('cmb_tecnico').value;
                                if (!idPerTecnico)
                                {
                                    boolErrorTecnico = false;
                                }
                            }
                        } else if (origen == "otro" || origen == "otro2")
                        {
                            if (id == null || !id || id == 0 || id == "0" || id == "")
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
                            }
                        } else
                        {
                            boolError = true;
                            Ext.Msg.alert('Alerta', 'No hay opcion escogida');
                        }

                        if (boolErrorTecnico)
                        {
                            //Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
                            boolError = true;
                        } else
                        {
                            if (!boolError)
                            {
                                var paramResponsables = '';
                                var boolErrorTareas = false;
                                var mensajeError = "";
                                for (i in tareasJS)
                                {
                                    var banderaEscogido = $("input[name='tipoResponsable_" + i + "']:checked").val();
                                    var codigoEscogido = "";
                                    var tituloError = "";
                                    var idPersona = "0";
                                    var idPersonaEmpresaRol = "0";
                                    if (banderaEscogido == "empleado")
                                    {
                                        tituloError = "Empleado ";
                                        codigoEscogido = Ext.getCmp('cmb_empleado_' + i).value;
                                    }
                                    if (banderaEscogido == "cuadrilla")
                                    {
                                        tituloError = "Cuadrilla";
                                        codigoEscogido = Ext.getCmp('cmb_cuadrilla_' + i).value;
                                        idPersona = Ext.getCmp('idPersona_' + i).getValue();
                                        idPersonaEmpresaRol = Ext.getCmp('idPersonaEmpresaRol_' + i).getValue();
                                    }
                                    if (banderaEscogido == "empresaExterna")
                                    {
                                        tituloError = "Contratista";
                                        codigoEscogido = Ext.getCmp('cmb_empresa_externa_' + i).value;
                                    }
                                    if (codigoEscogido && codigoEscogido != "")
                                    {
                                        paramResponsables = paramResponsables + +tareasJS[i]['idTarea'] + "@@" + banderaEscogido + "@@" + codigoEscogido + "@@" +
                                            idPersona + "@@" + idPersonaEmpresaRol;
                                        if (i < (tareasJS.length - 1))
                                        {
                                            paramResponsables = paramResponsables + '|';
                                        }
                                    } else
                                    {
                                        boolErrorTareas = false;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Combo: " + tituloError + "<br>";
                                    }
                                }

                            }
                        }
                        
                        if (!boolError && !boolErrorTareas)
                        {
                            connCoordinar.request({
                                url: "replanificar",
                                method: 'post',
                                timeout: 450000,
                                params: {origen: origen,
                                    id: id_factibilidad,
                                    param: param,
                                    paramResponsables: paramResponsables,
                                    idPerTecnico: idPerTecnico,
                                    ho_inicio: ho_inicio,
                                    ho_fin: ho_fin,
                                    observacion: txtObservacion,
                                    id_motivo: cmbMotivo,
                                    fechaReplanificacion: fechaReplanificacion,
                                    boolPerfilOpu: boolPerfilOpu
                                },
                                success: function(response) {
                                    var text        = response.responseText;
                                    var intPosicion = text.indexOf("Correctamente");

                                    if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                        text == "Se replanifico la solicitud" || intPosicion !== -1)
                                    {
                                        var tieneGestionSimultanea = Ext.getCmp('tieneGestionSimultanea').value;
                                        cierraVentanaRePlanificar();
                                        if(prefijoEmpresa == "MD" && tieneGestionSimultanea === "SI")
                                        {   
                                            var objInfoGestionSimultanea = {
                                                strOpcionGestionSimultanea:         "REPLANIFICAR",
                                                intIdSolGestionada:                 id_factibilidad,
                                                strOrigen:                          origen,
                                                intIdMotivo:                        cmbMotivo,
                                                strBoolPerfilOpu:                   boolPerfilOpu,
                                                strFechaReplanificacion:            fechaReplanificacion,
                                                strFechaHoraInicioReplanificacion:  ho_inicio,
                                                strFechaHoraFinReplanificacion:     ho_fin,
                                                strParamResponsables:               paramResponsables,
                                                intIdPerTecnico:                    idPerTecnico,
                                                strMensajeEjecucionSolGestionada:   text
                                            };
                                            ejecutaGestionSimultanea(objInfoGestionSimultanea);
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                    var permiso1 = '{{ is_granted("ROLE_139-111") }}';
                                                    var boolPermiso1 = (Ext.isEmpty(permiso1)) ? false : (permiso1 ? true : false);
                                                    var permiso2 = '{{ is_granted("ROLE_139-112")  }}';
                                                    var boolPermiso2 = (Ext.isEmpty(permiso2)) ? false : (permiso2 ? true : false);
                                                    if (!boolPermiso1 || !boolPermiso2) {
                                                        showMenuAsignacion('otro', id_factibilidad, false);
                                                    }
                                                }
                                            });
                                        }
                                    } else {
                                        Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Alerta', result.responseText);
                                }
                            });
                        } else {
                            Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRePlanificar();
                    }
                }
            ]
        });

        /* Funcion para agregar el label del tipo de red. */
        agregarLabelTipoRed(rec);

        combo_tecnicos = Ext.create('Ext.Component', {
            html: ""
        })
        if (prefijoEmpresa == "TN")
        {
            if (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")
            {
                storeTecnicos = new Ext.data.Store
                    ({
                        total: 'total',
                        pageSize: 25,
                        listeners: {
                        },
                        proxy:
                            {
                                type: 'ajax',
                                method: 'post',
                                url: '../../planificar/asignar_responsable/getTecnicos',
                                reader:
                                    {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                extraParams: {
                                    query: '',
                                    'tipo_esquema': rec.get("tipo_esquema")
                                },
                                actionMethods:
                                    {
                                        create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                    }
                            },
                        fields:
                            [
                                {name: 'id_tecnico', mapping: 'idPersonaEmpresaRol'},
                                {name: 'nombre_tecnico', mapping: 'info_adicional'},
                            ],
                        autoLoad: true
                    });
                const strIngeniero = rec.get("tipo_esquema") && rec.get("tipo_esquema") == 1 ? 'RADIO' : 'IPCCL2';
                if (rec.get('producto') === 'Cableado Estructurado')
                {
                    combo_tecnicos = new Ext.form.ComboBox({
                        id: 'cmb_tecnico',
                        name: 'cmb_tecnico',
                        fieldLabel: `Ingeniero Activación`,
                        anchor: '100%',
                        queryMode: 'remote',
                        emptyText: `Seleccione Ingeniero Activación`,
                        width: 350,
                        store: storeTecnicos,
                        displayField: 'nombre_tecnico',
                        valueField: 'id_tecnico',
                        layout: 'anchor',
                        disabled: false
                    }); 
                }
                else
                {
                    combo_tecnicos = new Ext.form.ComboBox({
                        id: 'cmb_tecnico',
                        name: 'cmb_tecnico',
                        fieldLabel: `Ingeniero ${strIngeniero}`,
                        anchor: '100%',
                        queryMode: 'remote',
                        emptyText: `Seleccione Ingeniero ${strIngeniero}`,
                        width: 350,
                        store: storeTecnicos,
                        displayField: 'nombre_tecnico',
                        valueField: 'id_tecnico',
                        layout: 'anchor',
                        disabled: false
                    });
                }
            }
        }
        connTareas.request({
            method: 'POST',
            url: "../../factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
            params: {servicioId: id_servicio, id_solicitud: id_factibilidad, nombreTarea: 'todas', estado: 'Activo', accion: 'Replanificar'},
            success: function(response) {
                var data = Ext.JSON.decode(response.responseText.trim());
                if (data)
                {
                    totalTareas = data.total;
                    if (totalTareas > 0)
                    {
                        tareasJS = data.encontrados;
                        for (i in tareasJS)
                        {
                            //******** hidden id tarea
                            var hidden_tarea = new Ext.form.Hidden({
                                id: 'hidden_id_tarea_' + i,
                                name: 'hidden_id_tarea_' + i,
                                value: tareasJS[i]["idTarea"]
                            });
                            //******** text nombre tarea
                            var text_tarea = new Ext.form.Label({
                                forId: 'txt_nombre_tarea_' + i,
                                style: "font-weight:bold; font-size:14px; color:red; margin-bottom: 15px;",
                                layout: 'anchor',
                                text: tareasJS[i]["nombreTarea"]
                            });
                            //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
                            var strIniHtml = '';
                            if (prefijoEmpresa == "TNP")
                            {
                                strIniHtml  = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i +
                                              ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                                              '';
                            }
                            else
                            {
                                strIniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="cuadrilla" name="tipoResponsable_' + i + '">&nbsp;Cuadrilla' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="empresaExterna" name="tipoResponsable_' + i + '">&nbsp;Contratista' +
                                    '';
                            }
                            RadiosTiposResponsable = Ext.create('Ext.Component', {
                                html: strIniHtml,
                                width: 350,
                                padding: 10,
                                style: {color: '#000000'}
                            });
                            // **************** EMPLEADOS ******************
                            Ext.define('EmpleadosList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empleado', type: 'int'},
                                    {name: 'nombre_empleado', type: 'string'}
                                ]
                            });
                            eval("var storeEmpleados_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpleados_" + i + "', " +
                                "  model: 'EmpleadosList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpleados'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_empleados = new Ext.form.ComboBox({
                                id: 'cmb_empleado_' + i,
                                name: 'cmb_empleado_' + i,
                                fieldLabel: "Empleados",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Empleado',
                                store: eval("storeEmpleados_" + i),
                                displayField: 'nombre_empleado',
                                valueField: 'id_empleado',
                                layout: 'anchor',
                                disabled: false
                            });
                            // ****************  EMPRESA EXTERNA  ******************
                            Ext.define('EmpresaExternaList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_empresa_externa', type: 'int'},
                                    {name: 'nombre_empresa_externa', type: 'string'}
                                ]
                            });
                            eval("var storeEmpresaExterna_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpresaExterna_" + i + "', " +
                                "  model: 'EmpresaExternaList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  },actionMethods: { " +
                                " create: 'POST', read: 'POST', update: 'POST', destroy: 'POST' " +
                                " }, " +
                                "  }" +
                                " });    ");
                            combo_empresas_externas = new Ext.form.ComboBox({
                                id: 'cmb_empresa_externa_' + i,
                                name: 'cmb_empresa_externa_' + i,
                                fieldLabel: "Contratista",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Contratista',
                                store: eval("storeEmpresaExterna_" + i),
                                displayField: 'nombre_empresa_externa',
                                valueField: 'id_empresa_externa',
                                layout: 'anchor',
                                disabled: true
                            });
                            // **************** CUADRILLAS ******************
                            Ext.define('CuadrillasList', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_cuadrilla', type: 'int'},
                                    {name: 'nombre_cuadrilla', type: 'string'}
                                ]
                            });
                            eval("var storeCuadrillas_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeCuadrillas_" + i + "', " +
                                "  model: 'CuadrillasList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                            combo_cuadrillas = new Ext.form.ComboBox({
                                id: 'cmb_cuadrilla_' + i,
                                name: 'cmb_cuadrilla_' + i,
                                fieldLabel: "Cuadrilla",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Cuadrilla',
                                store: eval("storeCuadrillas_" + i),
                                displayField: 'nombre_cuadrilla',
                                valueField: 'id_cuadrilla',
                                layout: 'anchor',
                                disabled: true,
                                listeners: {
                                    select: function(combo) {

                                        seteaLiderCuadrilla(combo.getId(), combo.getValue());
                                    }
                                }
                            });
                            //******** html vacio...
                            var iniHtmlVacio = '';
                            Vacio = Ext.create('Ext.Component', {
                                html: iniHtmlVacio,
                                width: 350,
                                padding: 8,
                                layout: 'anchor',
                                style: {color: '#000000'}
                            });
                            formPanel = Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                waitMsgTarget: true,
                                id: 'panelLiderCuadrilla_' + i,
                                name: 'panelLiderCuadrilla_' + i,
                                height: 80,
                                width: 350,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    msgTarget: 'side'
                                },
                                items:
                                    [
                                        {
                                            xtype: 'fieldset',
                                            title: 'Lider de Cuadrilla',
                                            defaultType: 'textfield',
                                            items:
                                                [
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Persona:',
                                                        id: 'idPersona_' + i,
                                                        name: 'idPersona_' + i,
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'PersonaEmpresaRol:',
                                                        id: 'idPersonaEmpresaRol_' + i,
                                                        name: 'idPersonaEmpresaRol_' + i,
                                                        hidden: true,
                                                        value: ""
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        fieldLabel: 'Nombre:',
                                                        id: 'nombreLider_' + i,
                                                        name: 'nombreLider_' + i,
                                                        value: ""
                                                    }
                                                ]
                                        }
                                    ]
                            });
                            if (boolPermisoOpu && prefijoEmpresa != "TNP" &&
                                (rec.get("descripcionSolicitud") == "Solicitud Planificacion" ||
                                rec.get("descripcionSolicitud") == "Solicitud De Instalacion Cableado Ethernet"))
                            {
                                THoraInicio = Ext.create('Ext.Component', {
                                    html: "<br>"
                                });
                                THoraFin = Ext.create('Ext.Component', {
                                    html: "<br>"
                                });
                            } else
                            {
                                THoraInicio = Ext.create('Ext.form.TimeField', {
                                    fieldLabel: '* Hora Inicio',
                                    format: 'H:i',
                                    id: 'ho_inicio_value',
                                    name: 'ho_inicio_value',
                                    minValue: '00:01 AM',
                                    maxValue: '22:59 PM',
                                    increment: 30,
                                    value: "00:00",
                                    editable: false,
                                    labelStyle: "color:red;",
                                    listeners: {
                                        select: {fn: function(valorTime, value) {
                                                var valueEscogido = valorTime.getValue();
                                                var valueEscogido2 = new Date(valueEscogido);
                                                var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 30);
                                                var horaTotal = new Date(valueEscogidoAumentMili);
                                                var h = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                                                var m = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                                                var horasTotalFormat = h + ":" + m;
                                                Ext.getCmp('ho_fin_value').setMinValue(horaTotal);
                                                $('input[name="ho_fin_value"]').val(horasTotalFormat);
                                            }}
                                    }
                                });
                                THoraFin = Ext.create('Ext.form.TimeField', {
                                    fieldLabel: '* Hora Fin',
                                    format: 'H:i',
                                    id: 'ho_fin_value',
                                    name: 'ho_fin_value',
                                    minValue: '00:30 AM',
                                    maxValue: '23:59 PM',
                                    increment: 30,
                                    value: "00:30",
                                    editable: false,
                                    labelStyle: "color:red;"
                                });
                            }
                            txtInformacion = Ext.create('Ext.form.TextField', {
                                fieldLabel: 'Fecha Hora Inicio - Fin',
                                value: rec.get("fePlanificada") + " " + rec.get("HoraIniPlanificada") + " - " + rec.get("HoraFinPlanificada"),
                                allowBlank: false,
                                readOnly: true,
                            });
                            var txtObservacionPlanf = Ext.create('Ext.form.TextArea',
                                {
                                    fieldLabel: '',
                                    name: 'txtObservacionPlanf',
                                    id: 'txtObservacionPlanf',
                                    value: rec.get("observacionAdicional"),
                                    allowBlank: false,
                                    width: 300,
                                    height: 150,
                                    listeners:
                                        {
                                            blur: function(field)
                                            {
                                                observacionPlanF = field.getValue();
                                            }
                                        }
                                });
                            var container = Ext.create('Ext.container.Container',
                                {
                                    layout: {
                                        type: 'hbox'
                                    },
                                    width: 700,
                                    items: [
                                        {
                                            xtype: 'panel',
                                            border: false,
                                            layout: {type: 'hbox', align: 'stretch'},
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    defaultType: 'textfield',
                                                    style: "font-weight:bold; margin-bottom: 15px; border-right:none",
                                                    layout: 'anchor',
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: false,
                                                            frame: false
                                                        },
                                                    items: [txtInformacion,
                                                        DTFechaReplanificacion,
                                                        THoraInicio,
                                                        THoraFin,
                                                        cmbMotivosRePlanificacion,
                                                        RadiosTiposResponsable,
                                                        combo_empleados,
                                                        combo_cuadrillas,
                                                        combo_empresas_externas,
                                                        formPanel,
                                                        Vacio,
                                                        combo_tecnicos,
                                                        Vacio]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    style: "margin-bottom: 15px; border-left:none",
                                                    defaults:
                                                        {
                                                            width: '400px',
                                                            border: false,
                                                            frame: false

                                                        },
                                                    items: [
                                                        {html: "Observación:", border: false, width: 325},
                                                        txtObservacionPlanf]
                                                }]
                                        }]
                                });
                            formRePlanificar.items.add(container);
                            formRePlanificar.doLayout();
                            
                            if(rec.get("muestraIngL2") == "N")
                            {
                                combo_tecnicos.setVisible(false);
                            } 
                            
                            Ext.getCmp('cmb_empleado_' + i).setVisible(true);
                            Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
                            Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
                            Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);

                            formPanelHalPrincipal = crearFormPanelHal('replanificar', rec, origen, 0, boolPermisoOpu);    

                            winRePlanificar = Ext.widget('window', {
                                title: tituloCoordinar,
                                layout: 'fit',
                                resizable: false,
                                modal: true,
                                closabled: false,
                                items: (boolPermisoAsignarTareaHal ? 
                                    ( rec.get("strTareaEsHal") == 'S' ? [formPanelHalPrincipal] : [formRePlanificar] ) : [formRePlanificar])
                            });
                            if (rec.get('producto') === 'Cableado Estructurado')
                            {
                                break;
                            }
                        }

                        winRePlanificar.show();
                    }
                }
            }
        });
    }
}

function cierraVentanaRePlanificar() {
    winRePlanificar.close();
    winRePlanificar.destroy();
}

function showDetener_Coordinar(rec, origen)
{
    winDetener_Coordinar = "";
    formPanelDetener_Coordinar = "";
    if (!winDetener_Coordinar)
    {
        cmbMotivosDtenido = Ext.create('Ext.data.comboMotivosDetenido', {
            id: 'cmbMotivo',
            name: 'cmbMotivo'});
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }
//******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            // html: iniHtmlCamposRequeridos,
            // width: 600,
            // padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });
        
        
        panelInfoAdicionalSolDetener = Ext.create('Ext.container.Container',
        {
            id:'panelInfoAdicionalSolDetener',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa === "MD")
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: rec.get("id_factibilidad"),
                                    strOpcionGestionSimultanea: 'DETENER'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }

                                    Ext.getCmp('panelInfoAdicionalSolDetener').add(
                                        {
                                            title: 'Gestión Simultánea',
                                            xtype: 'fieldset',
                                            defaultType: 'textfield',
                                            style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                            layout: 'anchor',
                                            defaults:
                                                {
                                                    border: false,
                                                    frame: false
                                                },
                                            items: [
                                                {
                                                    xtype: 'panel',
                                                    border: false,
                                                    style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                    defaults:
                                                    {
                                                        width: '565px',
                                                    },
                                                    layout: {type: 'hbox', align: 'stretch'},
                                                    items: [
                                                        {                                                                                              
                                                            xtype: 'textfield',
                                                            hidden: true,
                                                            id:'tieneGestionSimultanea',
                                                            value:'SI'
                                                        },
                                                        Ext.create('Ext.Component', {
                                                            style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                            html: "<div>Esta solicitud #"+ rec.get("id_factibilidad") 
                                                                  + " detendrá de manera simultánea "
                                                                  +strInfoNumSolicitudes+"."+"</div>",
                                                            layout: 'anchor'
                                                        }),
                                                        {
                                                            id: 'btnMasDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ver Detalle',
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('gridSolsSimultaneas').show();
                                                            }
                                                        },
                                                        {
                                                            id: 'btnMenosDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ocultar Detalle',
                                                            hidden: true,
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('gridSolsSimultaneas').hide();
                                                            }
                                                        }
                                                    ]
                                                },
                                                Ext.create('Ext.grid.Panel', {
                                                    id: 'gridSolsSimultaneas',
                                                    store: storeSolsSimultaneas,
                                                    style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; "
                                                           +"margin-bottom: 10px",
                                                    columnLines: true,
                                                    hidden: true,
                                                    columns: 
                                                    [
                                                        {
                                                            id: 'descripServicioSimultaneo',
                                                            header: 'Plan/Producto',
                                                            dataIndex: 'descripServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoServicioSimultaneo',
                                                            header: 'Estado servicio',
                                                            dataIndex: 'estadoServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'idSolSimultanea',
                                                            header: '# Solicitud',
                                                            dataIndex: 'idSolSimultanea',
                                                            width: 100,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'descripTipoSolSimultanea',
                                                            header: 'Tipo Solicitud',
                                                            dataIndex: 'descripTipoSolSimultanea',
                                                            width: 180,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoSolSimultanea',
                                                            header: 'Estado Solicitud',
                                                            dataIndex: 'estadoSolSimultanea',
                                                            width: 130,
                                                            sortable: true
                                                        }
                                                    ],
                                                    viewConfig: {
                                                        stripeRows: true
                                                    },
                                                    frame: true,
                                                    defaults:
                                                    {
                                                        width: '565px'
                                                    }
                                                })
                                            ]
                                        }
                                    );
                                }
                                else
                                {
                                    Ext.getCmp('panelInfoAdicionalSolDetener').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolDetener').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        Ext.getCmp('panelInfoAdicionalSolDetener').add(
                            {                                                                                              
                                xtype: 'textfield',
                                hidden: true,
                                id:'tieneGestionSimultanea',
                                value:'NO'
                            },
                        );
                        Ext.getCmp('panelInfoAdicionalSolDetener').doLayout();
                    }
                }
            }
        });
        
        formPanelDetener_Coordinar = Ext.create('Ext.form.Panel', {
//             width:600,
//             height:800,
            buttonAlign: 'center',
            BodyPadding: 5,
            bodyStyle: "background: white; padding:5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 15px;",
                            layout: 'anchor',
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: rec.get("esRecontratacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Punto',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("productoServicio"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Enlace',
                                    name: 'tipoEnlace',
                                    id: 'tipoEnlace',
                                    value: rec.get("strTipoEnlace"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                itemTercerizadora,
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Telefonos',
                                    name: 'telefonos_punto',
                                    id: 'telefonos_punto',
                                    value: rec.get("telefonos"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion_punto',
                                    id: 'observacion_punto',
                                    value: rec.get("observacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        }
                    ]
                },
                panelInfoAdicionalSolDetener,
                {
                    xtype: 'fieldset',
                    title: 'Datos para Detener Proceso',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '500px'
                    },
                    items: [
                        {
                            xtype: 'textarea',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false,
                            labelStyle: "color:red;"
                        },
                        cmbMotivosDtenido,
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Detener',
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo = Ext.getCmp('cmbMotivo').value;
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }

                        var param = '';
                        var boolErrorTecnico = false;
                        var idPerTecnico = 0;
                        if (origen == "local")
                        {
                            id = rec.data.id_factibilidad;
                            param = rec.data.id_factibilidad;
                        } else if (origen == "otro" || origen == "otro2")
                        {
                            if (id == null || !id || id == 0 || id == "0" || id == "")
                            {
                                boolError = true;
                                Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
                            }
                        } else
                        {
                            boolError = true;
                            Ext.Msg.alert('Alerta', 'No hay opcion escogida');
                        }



                        if (boolErrorTecnico)
                        {
                            Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
                        } else
                        {
                            if (!boolError)
                            {
                                var paramResponsables = '';
                                var boolErrorTareas = false;
                                var mensajeError = "";
                                for (i in tareasJS)
                                {
                                    var banderaEscogido = $("input[name='tipoResponsable_" + i + "']:checked").val();
                                    var codigoEscogido = "";
                                    var tituloError = "";
                                    var idPersona = "0";
                                    var idPersonaEmpresaRol = "0";
                                    if (banderaEscogido == "empleado")
                                    {
                                        tituloError = "Empleado ";
                                        codigoEscogido = Ext.getCmp('cmb_empleado_' + i).value;
                                    }
                                    if (banderaEscogido == "cuadrilla")
                                    {
                                        tituloError = "Cuadrilla";
                                        codigoEscogido = Ext.getCmp('cmb_cuadrilla_' + i).value;
                                        idPersona = Ext.getCmp('idPersona_' + i).getValue();
                                        idPersonaEmpresaRol = Ext.getCmp('idPersonaEmpresaRol_' + i).getValue();
                                    }
                                    if (banderaEscogido == "empresaExterna")
                                    {
                                        tituloError = "Contratista";
                                        codigoEscogido = Ext.getCmp('cmb_empresa_externa_' + i).value;
                                    }


                                    if (codigoEscogido && codigoEscogido != "")
                                    {
                                        paramResponsables = paramResponsables + +tareasJS[i]['idTarea'] + "@@" + banderaEscogido + "@@" + codigoEscogido + "@@" +
                                            idPersona + "@@" + idPersonaEmpresaRol;
                                        if (i < (tareasJS.length - 1))
                                        {
                                            paramResponsables = paramResponsables + '|';
                                        }
                                    } else
                                    {
                                        boolErrorTareas = true;
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Combo: " + tituloError + "<br>";
                                    }
                                }

                            }
                        }

                        if (!boolError)
                        {
                            connCoordinar.request({
                                url: "detener",
                                method: 'post',
                                params: {paramResponsables: paramResponsables,
                                    id: id_factibilidad,
                                    id_motivo: cmbMotivo,
                                    observacion: txtObservacion},
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se detuvo la solicitud")
                                    {
                                        var tieneGestionSimultanea = Ext.getCmp('tieneGestionSimultanea').value;
                                        cierraVentanaDetener_Coordinar();
                                        if(prefijoEmpresa == "MD" && tieneGestionSimultanea === "SI")
                                        {
                                            var objInfoGestionSimultanea = {
                                                strOpcionGestionSimultanea:         "DETENER",
                                                intIdSolGestionada:                 id_factibilidad,
                                                intIdMotivo:                        cmbMotivo,
                                                strMensajeEjecucionSolGestionada:   text
                                            };
                                            ejecutaGestionSimultanea(objInfoGestionSimultanea);
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                }
                                            });
                                        }
                                    } else {
                                        Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Alerta', result.responseText);
                                }
                            });
                        } else {
                            Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaDetener_Coordinar();
                    }
                }
            ]
        });
        winDetener_Coordinar = Ext.widget('window', {
            title: 'Detener Orden de Servicio',
//             width: 570,
//             height:630,
//             minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelDetener_Coordinar]
        });
    }

    winDetener_Coordinar.show();
}

function cierraVentanaDetener_Coordinar() {
    winDetener_Coordinar.close();
    winDetener_Coordinar.destroy();
}

/* 
 * @version 1.0 No documentada
 * 
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.1 18-12-2019 | Se agrega validación para que aparezca mensaje de ejecución de Nc masiva.
 * 
*/
function showAnularOrden_Coordinar(rec)
{
    winAnularOrden_Coordinar = "";
    formPanelAnularOrden_Coordinar = "";
    if (!winAnularOrden_Coordinar)
        cmbMotivosAnulacion = Ext.create('Ext.data.comboMotivosAnulacion', {
            id: 'cmbMotivosAnulacion',
            name: 'cmbMotivosAnulacion',
            fieldLabel: '* Motivo',
            labelStyle: "color:red;"});
    {
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }
//******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            // width: 600,
            // padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });
        
        panelInfoAdicionalSolAnular = Ext.create('Ext.container.Container',
        {
            id:'panelInfoAdicionalSolAnular',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa === "MD")
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: rec.get("id_factibilidad"),
                                    strOpcionGestionSimultanea: 'ANULAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }

                                    Ext.getCmp('panelInfoAdicionalSolAnular').add(
                                        {
                                            title: 'Gestión Simultánea',
                                            xtype: 'fieldset',
                                            defaultType: 'textfield',
                                            style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                            layout: 'anchor',
                                            defaults:
                                                {
                                                    border: false,
                                                    frame: false
                                                },
                                            items: [
                                                {
                                                    xtype: 'panel',
                                                    border: false,
                                                    style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                    defaults:
                                                    {
                                                        width: '565px',
                                                    },
                                                    layout: {type: 'hbox', align: 'stretch'},
                                                    items: [
                                                        {                                                                                              
                                                            xtype: 'textfield',
                                                            hidden: true,
                                                            id:'tieneGestionSimultanea',
                                                            value:'SI'
                                                        },
                                                        Ext.create('Ext.Component', {
                                                            style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                            html: "<div>Esta solicitud #"+ rec.get("id_factibilidad") 
                                                                  + " anulará de manera simultánea "
                                                                  +strInfoNumSolicitudes+"."+"</div>",
                                                            layout: 'anchor'
                                                        }),
                                                        {
                                                            id: 'btnMasDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ver Detalle',
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('gridSolsSimultaneas').show();
                                                            }
                                                        },
                                                        {
                                                            id: 'btnMenosDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ocultar Detalle',
                                                            hidden: true,
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('gridSolsSimultaneas').hide();
                                                            }
                                                        }
                                                    ]
                                                },
                                                Ext.create('Ext.grid.Panel', {
                                                    id: 'gridSolsSimultaneas',
                                                    store: storeSolsSimultaneas,
                                                    style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; "
                                                           +"margin-bottom: 10px",
                                                    columnLines: true,
                                                    hidden: true,
                                                    columns: 
                                                    [
                                                        {
                                                            id: 'descripServicioSimultaneo',
                                                            header: 'Plan/Producto',
                                                            dataIndex: 'descripServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoServicioSimultaneo',
                                                            header: 'Estado servicio',
                                                            dataIndex: 'estadoServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'idSolSimultanea',
                                                            header: '# Solicitud',
                                                            dataIndex: 'idSolSimultanea',
                                                            width: 100,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'descripTipoSolSimultanea',
                                                            header: 'Tipo Solicitud',
                                                            dataIndex: 'descripTipoSolSimultanea',
                                                            width: 180,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoSolSimultanea',
                                                            header: 'Estado Solicitud',
                                                            dataIndex: 'estadoSolSimultanea',
                                                            width: 130,
                                                            sortable: true
                                                        }
                                                    ],
                                                    viewConfig: {
                                                        stripeRows: true
                                                    },
                                                    frame: true,
                                                    defaults:
                                                    {
                                                        width: '565px'
                                                    }
                                                })
                                            ]
                                        }
                                    );
                                }
                                else
                                {
                                    Ext.getCmp('panelInfoAdicionalSolAnular').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolAnular').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        Ext.getCmp('panelInfoAdicionalSolAnular').add(
                            {                                                                                              
                                xtype: 'textfield',
                                hidden: true,
                                id:'tieneGestionSimultanea',
                                value:'NO'
                            },
                        );
                        Ext.getCmp('panelInfoAdicionalSolAnular').doLayout();
                    }
                }
            }
        });
        
        formPanelAnularOrden_Coordinar = Ext.create('Ext.form.Panel', {
//             width:600,
//             height:800,
            buttonAlign: 'center',
            BodyPadding: 5,
            bodyStyle: "background: white; padding:5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            layout: 'anchor',
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: rec.get("esRecontratacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Punto',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("productoServicio"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Enlace',
                                    name: 'tipoEnlace',
                                    id: 'tipoEnlace',
                                    value: rec.get("strTipoEnlace"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                itemTercerizadora,
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Telefonos',
                                    name: 'telefonos_punto',
                                    id: 'telefonos_punto',
                                    value: rec.get("telefonos"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion_punto',
                                    id: 'observacion_punto',
                                    value: rec.get("observacion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                            ]
                        }
                    ]
                },
                panelInfoAdicionalSolAnular,
                {
                    xtype: 'fieldset',
                    title: 'Datos de la anulacion',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 10px;",
                    defaults: {
                        width: '500px'
                    },
                    items: [
                        {
                            xtype: 'textarea',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false,
                            labelStyle: "color:red;"
                        },
                        cmbMotivosAnulacion
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Anular',
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo = Ext.getCmp('cmbMotivosAnulacion').value;
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }


                        connCoordinar.request({
                         url: urlGetProcesoMasivoNC,
                        method: 'post',
                        timeout: 400000,
                        async: false,
                            success: function(response)
                            {

                                    if (response.responseText === 'NO')
                                    {
                                        boolError = true;
                                        mensajeError += "Se encuentra en proceso la Aprobación masiva de NC, espere unos minutos por favor y vuelva a intentar.\n";
                                    }

                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        })


                        if (!boolError)
                        {
                            connCoordinar.request({
                                url: "anular",
                                method: 'post',
                                params: {
                                    id: id_factibilidad,
                                    id_motivo: cmbMotivo,
                                    observacion: txtObservacion,
                                    /*Aquí se debe colocar si el servicio posee otro que depende de el por instalación simultanea.*/
                                    serviciosSimultaneos: rec.get('idIntWifiSim') ? JSON.stringify(rec.get('idIntWifiSim')) : null
                                },
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se anulo la solicitud")
                                    {
                                        var tieneGestionSimultanea = Ext.getCmp('tieneGestionSimultanea').value;
                                        cierraVentanaAnularOrden_Coordinar();
                                        if(prefijoEmpresa == "MD" && tieneGestionSimultanea === "SI")
                                        {
                                            var objInfoGestionSimultanea = {
                                                strOpcionGestionSimultanea:         "ANULAR",
                                                intIdSolGestionada:                 id_factibilidad,
                                                intIdMotivo:                        cmbMotivo,
                                                strMensajeEjecucionSolGestionada:   text
                                            };
                                            ejecutaGestionSimultanea(objInfoGestionSimultanea);
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                }
                                            });
                                        }
                                    } else {
                                        Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Alerta', result.responseText);
                                }
                            });
                        } 
                        else
                        {
                            cierraVentanaAnularOrden_Coordinar();                          
                            Ext.Msg.alert('Alerta', mensajeError);
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAnularOrden_Coordinar();
                    }
                }
            ]
        });
        winAnularOrden_Coordinar = Ext.widget('window', {
            title: 'Anulacion de Orden de Servicio',
//             width: 570,
//             height:630,
//             minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelAnularOrden_Coordinar]
        });
    }

    winAnularOrden_Coordinar.show();
}

function cierraVentanaAnularOrden_Coordinar() {
    winAnularOrden_Coordinar.close();
    winAnularOrden_Coordinar.destroy();
}

/************************************************************************ */
/************************* RECHAZAR ORDEN ******************************* */
/************************************************************************ */
function showRechazarOrden_Coordinar(rec)
{
    winRechazarOrden_Coordinar = "";
    formPanelRechazarOrden_Coordinar = "";
    if (!winRechazarOrden_Coordinar)
    {
        cmbMotivosRechazo = Ext.create('Ext.data.comboMotivosRechazo', {
            id: 'cmbMotivos',
            name: 'cmbMotivos',
            fieldLabel: '* Motivo',
            labelStyle: "color:red;"});
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

//******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            // width: 600,
            // padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });
        
        
        panelInfoAdicionalSolRechazar = Ext.create('Ext.container.Container',
        {
            id:'panelInfoAdicionalSolRechazar',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa === "MD")
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: rec.get("id_factibilidad"),
                                    strOpcionGestionSimultanea: 'RECHAZAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }

                                    Ext.getCmp('panelInfoAdicionalSolRechazar').add(
                                        {
                                            title: 'Gestión Simultánea',
                                            xtype: 'fieldset',
                                            defaultType: 'textfield',
                                            style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                            layout: 'anchor',
                                            defaults:
                                                {
                                                    border: false,
                                                    frame: false
                                                },
                                            items: [
                                                {
                                                    xtype: 'panel',
                                                    border: false,
                                                    style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                    defaults:
                                                    {
                                                        width: '565px',
                                                    },
                                                    layout: {type: 'hbox', align: 'stretch'},
                                                    items: [
                                                        {                                                                                              
                                                            xtype: 'textfield',
                                                            hidden: true,
                                                            id:'tieneGestionSimultanea',
                                                            value:'SI'
                                                        },
                                                        Ext.create('Ext.Component', {
                                                            style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                            html: "<div>Esta solicitud #"+ rec.get("id_factibilidad") 
                                                                  + " rechazará de manera simultánea "
                                                                  +strInfoNumSolicitudes+"."+"</div>",
                                                            layout: 'anchor'
                                                        }),
                                                        {
                                                            id: 'btnMasDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ver Detalle',
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('gridSolsSimultaneas').show();
                                                            }
                                                        },
                                                        {
                                                            id: 'btnMenosDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ocultar Detalle',
                                                            hidden: true,
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('gridSolsSimultaneas').hide();
                                                            }
                                                        }
                                                    ]
                                                },
                                                Ext.create('Ext.grid.Panel', {
                                                    id: 'gridSolsSimultaneas',
                                                    store: storeSolsSimultaneas,
                                                    style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; "
                                                           +"margin-bottom: 10px",
                                                    columnLines: true,
                                                    hidden: true,
                                                    columns: 
                                                    [
                                                        {
                                                            id: 'descripServicioSimultaneo',
                                                            header: 'Plan/Producto',
                                                            dataIndex: 'descripServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoServicioSimultaneo',
                                                            header: 'Estado servicio',
                                                            dataIndex: 'estadoServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'idSolSimultanea',
                                                            header: '# Solicitud',
                                                            dataIndex: 'idSolSimultanea',
                                                            width: 100,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'descripTipoSolSimultanea',
                                                            header: 'Tipo Solicitud',
                                                            dataIndex: 'descripTipoSolSimultanea',
                                                            width: 180,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoSolSimultanea',
                                                            header: 'Estado Solicitud',
                                                            dataIndex: 'estadoSolSimultanea',
                                                            width: 130,
                                                            sortable: true
                                                        }
                                                    ],
                                                    viewConfig: {
                                                        stripeRows: true
                                                    },
                                                    frame: true,
                                                    defaults:
                                                    {
                                                        width: '565px'
                                                    }
                                                })
                                            ]
                                        }
                                    );
                                }
                                else
                                {
                                    Ext.getCmp('panelInfoAdicionalSolRechazar').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolRechazar').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        Ext.getCmp('panelInfoAdicionalSolRechazar').add(
                            {                                                                                              
                                xtype: 'textfield',
                                hidden: true,
                                id:'tieneGestionSimultanea',
                                value:'NO'
                            },
                        );
                        Ext.getCmp('panelInfoAdicionalSolRechazar').doLayout();
                    }
                }
            }
        });
        
        formPanelRechazarOrden_Coordinar = Ext.create('Ext.form.Panel', {
//             width:600,
//             height:800,
            buttonAlign: 'center',
            BodyPadding: 5,
            bodyStyle: "background: white; padding:5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            layout: 'anchor',
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: rec.get("esRecontratacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Punto',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("productoServicio"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Enlace',
                                    name: 'tipoEnlace',
                                    id: 'tipoEnlace',
                                    value: rec.get("strTipoEnlace"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                itemTercerizadora,
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Telefonos',
                                    name: 'telefonos_punto',
                                    id: 'telefonos_punto',
                                    value: rec.get("telefonos"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion_punto',
                                    id: 'observacion_punto',
                                    value: rec.get("observacion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                            ]
                        }
                    ]
                },
                panelInfoAdicionalSolRechazar,
                {
                    xtype: 'fieldset',
                    title: 'Datos del Rechazo',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 10px;",
                    defaults: {
                        width: '500px'
                    },
                    items: [
                        {
                            xtype: 'textarea',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false,
                            labelStyle: "color:red;"
                        },
                        cmbMotivosRechazo
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Rechazar',
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo = Ext.getCmp('cmbMotivos').value;
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }

                        if (!boolError)
                        {
                            if (rec.get('esSolucion') === 'S')
                            {
                                $.ajax({
                                    type: "POST",
                                    url: urlAnularRechazarSol,
                                    timeout: 900000,
                                    data:
                                        {
                                            'idServicio' : rec.get("id_servicio"),
                                            'idSolicitud': id_factibilidad,
                                            'idMotivo'   : cmbMotivo,
                                            'observacion': txtObservacion,
                                            'accion'     : 'Rechazar',
                                            'origen'     : 'coordinacion'
                                        },
                                    beforeSend: function()
                                    {
                                        Ext.MessageBox.show({
                                            msg: 'Rechazando Servicio de la Solución',
                                            progressText: 'Rechazando...',
                                            width: 300,
                                            wait: true,
                                            waitConfig: {interval: 200}
                                        });
                                    },
                                    success: function(data)
                                    {
                                        if (data.status === 'OK')
                                        {
                                            cierraVentanaRechazarOrden_Coordinar();

                                            var html = '';

                                            if (data.arrayServiciosEliminados.length > 0)
                                            {
                                                html += '<br><br>Los siguientes Servicios fueron rechazados por acción realizada.';
                                                html += '<br><ul>';
                                                $.each(data.arrayServiciosEliminados, function(i, item)
                                                {
                                                    html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp' + item + '</li>';
                                                });
                                                html += '</ul>';
                                            }

                                            var text = "Servicio Rechazado correctamente" + html;

                                            Ext.Msg.alert('Mensaje', text, function(btn)
                                            {
                                                if (btn == 'ok')
                                                {
                                                    store.load();
                                                }
                                            });
                                        } else
                                        {
                                            Ext.Msg.alert('Error', data.mensaje);
                                        }
                                    }
                                });
                            } else
                            {
                                connCoordinar.request({
                                    url: "rechazar",
                                    method: 'post',
                                    params: {
                                        id: id_factibilidad,
                                        id_motivo: cmbMotivo,
                                        observacion: txtObservacion,
                                        /*Aquí se debe colocar si el servicio posee otro que depende de el por instalación simultanea.*/
                                        serviciosSimultaneos: rec.get('idIntWifiSim') ? JSON.stringify(rec.get('idIntWifiSim')) : null
                                    },
                                    success: function(response) {
                                        var text = response.responseText;
                                        if (text == "Se rechazo la solicitud")
                                        {
                                            var tieneGestionSimultanea = Ext.getCmp('tieneGestionSimultanea').value;
                                            cierraVentanaRechazarOrden_Coordinar();
                                            
                                            if(prefijoEmpresa == "MD" && tieneGestionSimultanea === "SI")
                                            {
                                                var objInfoGestionSimultanea = {
                                                    strOpcionGestionSimultanea:         "RECHAZAR",
                                                    intIdSolGestionada:                 id_factibilidad,
                                                    intIdMotivo:                        cmbMotivo,
                                                    strMensajeEjecucionSolGestionada:   text
                                                };
                                                ejecutaGestionSimultanea(objInfoGestionSimultanea);
                                            }
                                            else
                                            {
                                            
                                                Ext.Msg.alert('Mensaje', text, function(btn) {
                                                    if (btn == 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            }
                                        } else {
                                            cierraVentanaRechazarOrden_Coordinar();
                                            Ext.Msg.alert('Alerta', 'Error: ' + text);
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Alerta', result.responseText);
                                    }
                                });
                            }
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRechazarOrden_Coordinar();
                    }
                }
            ]
        });
        winRechazarOrden_Coordinar = Ext.widget('window', {
            title: 'Rechazo de Orden de Servicio',
//             width: 570,
//             height:630,
//             minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelRechazarOrden_Coordinar]
        });
    }

    winRechazarOrden_Coordinar.show();
}

function cierraVentanaRechazarOrden_Coordinar() {
    winRechazarOrden_Coordinar.close();
    winRechazarOrden_Coordinar.destroy();
}


function buscar() {
    var boolError = false;
    if ((Ext.getCmp('fechaDesdePlanif').getValue() != null) && (Ext.getCmp('fechaHastaPlanif').getValue() != null))
    {
        if (Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            boolError = true;

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Planificacion debe ser fecha menor a Fecha Hasta Planificacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }

    if ((Ext.getCmp('fechaDesdeIngOrd').getValue() != null) && (Ext.getCmp('fechaHastaIngOrd').getValue() != null))
    {
        if (Ext.getCmp('fechaDesdeIngOrd').getValue() > Ext.getCmp('fechaHastaIngOrd').getValue())
        {
            boolError = true;

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Ingreso Orden debe ser fecha menor a Fecha Hasta Ingreso Orden.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }
    if (!boolError)
    {
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
        store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
        store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
        store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('cmbCiudades').value;
        store.getProxy().extraParams.sector = Ext.getCmp('cmbSector').value;
        store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
        store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
        store.getProxy().extraParams.login = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.ultimaMilla = Ext.getCmp('cmbUltimaMilla').value;
        store.getProxy().extraParams.estadoPunto = Ext.getCmp('cmbEstadoPunto').value;
        store.load();
    }
}

function agregarSeguimiento(rec){
    
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get('ingreso_seguimiento').mask('Ingresando Seguimiento...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get('ingreso_seguimiento').unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get('ingreso_seguimiento').unmask();
                },
                scope: this
            }
        }
    });
    btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                var valorSeguimiento = Ext.getCmp('seguimiento').value;
                conn.request({
                    method: 'POST',
                    params :{
                        id: rec.data.id_factibilidad,
                        seguimiento: valorSeguimiento
                    },
                    url: 'ingresarSeguimiento',
                    success: function(response){
                        winSeguimiento.destroy();
                        var text = response.responseText;

                        Ext.Msg.alert('Mensaje', text, function(btn) {
                            if (btn == 'ok') {
                                store.load();
                            }
                        });
                    },
                    failure: function(result) {
                        Ext.Msg.alert('Alerta', result.responseText);
                    }
            });
            }
    });
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winSeguimiento.destroy();
            }
    });            
            
    formPanel2 = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 200,
            width: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 140,
                msgTarget: 'side'
            },

            items: [{
                xtype: 'fieldset',
                title: 'Información',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Tipo Solicitud:',
                        id: 'tipoSolicitud',
                        name: 'tipoSolicitud',
                        value: rec.data.descripcionSolicitud
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Historial:',
                        id: 'historial',
                        name: 'historial',
                        value: rec.data.observacion,
                        rows: 5,
                        cols: 50,
                        readOnly: true
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Seguimiento:',
                        id: 'seguimiento',
                        name: 'seguimiento',
                        rows: 5,
                        cols: 50
                    }
                ]
            }]
         });

    winSeguimiento = Ext.create('Ext.window.Window', {
            id: 'ingreso_seguimiento',
            title: 'Ingresar Seguimiento',
            modal: true,
            width: 660,
            height: 325,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show();     
}

function planificarServicio(rec)
{
    if (rec.data.id_servicio && rec.data.id_punto)
    {
        strMensaje = "Configurar el Servicio FWA. Desea continuar?";

        Ext.Msg.confirm('Alerta', strMensaje, function(btn) {
            if (btn == 'yes') {
                connPlanificarServicio.request({
                    url: "planificarFwa",
                    method: 'post',
                    params: {
                       idServicio   : rec.data.id_servicio,
                       idPunto      : rec.data.id_punto
                    },
                    success: function(response) {
                        var text = response.responseText;
                        if (!text == "No existe la característica CONCENTRADOR_FWA, este servicio debe estar previamente enlazado a un concentrador."
                            || !text =="El concentrador virtual FWA no esta activo aún ")
                        {
                            //cierraVentanaAsignacionIndividual();
                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                if (btn == 'ok') {
                                    store.load();
                                }
                            });
                        } else {
                            var mm = Ext.Msg.alert('Alerta', text);
                            Ext.defer(function() {
                                mm.toFront();
                            }, 50);
                        }
                    },
                    failure: function(result) {
                        Ext.Msg.alert('Alerta', result.responseText);
                    }
                });
            }
        });
    } else
    {
        Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
    }
}


function opcionesHal(tipo, idDetalleSolicitud, idDetalle, idComunicacion)
{
    if (tipo == 1)
    {
        storeIntervalosHal.getProxy().extraParams.nIntentos = 1;
        gridIntervalos.setVisible(false);
        gridHalDice.setVisible(true);
        formPanelHalPrincipal.doLayout();
    }
    else if (tipo == 2)
    {
        nIntentos = nIntentos + 1;
        storeIntervalosHal.getProxy().extraParams.nIntentos = nIntentos;
        gridIntervalos.setVisible(true);
        gridHalDice.setVisible(false);
        formPanelHalPrincipal.doLayout();
    }

    document.getElementById('divAtenderAntes').style.display = 'block';
    Ext.getCmp('nueva_sugerencia').setDisabled(true);
    tipoHal       = tipo;
    seleccionaHal = true;
    storeIntervalosHal.removeAll();
    storeIntervalosHal.getProxy().extraParams.idDetSolicitud = idDetalleSolicitud;
    storeIntervalosHal.getProxy().extraParams.idDetalle      = idDetalle;
    storeIntervalosHal.getProxy().extraParams.idComunicacion = idComunicacion;
    storeIntervalosHal.getProxy().extraParams.esInstalacion  = 'S';
    storeIntervalosHal.getProxy().extraParams.idCaso         = null;
    storeIntervalosHal.getProxy().extraParams.idHipotesis    = null;
    storeIntervalosHal.getProxy().extraParams.idAdmiTarea    = null;
    storeIntervalosHal.getProxy().extraParams.fechaSugerida  = null;
    storeIntervalosHal.getProxy().extraParams.horaSugerida   = null;
    storeIntervalosHal.getProxy().extraParams.tipoHal       = tipoHal;
    storeIntervalosHal.load();
    Ext.getCmp('fecha_sugerida').setValue(null);
    Ext.getCmp('hora_sugerida').setValue(null);
}

function eliminarSeleccionHal(selModelIntervalos,gridHalDice,tipoHal)
{
    if (tipoHal == 2)
    {
        for (var i = 0; i < selModelIntervalos.getSelection().length; ++i)
        {
            selModelIntervalos.getStore().remove(selModelIntervalos.getSelection()[i]);
        }
    }
    else
    {
        for (var ind = 0; ind < gridHalDice.getStore().data.items.length; ++ind)
        {
            gridHalDice.getStore().remove(gridHalDice.getStore().data.items[ind]);
        }
    }
}



function crearFormPanelHal(tipoAccion, rec, origen, opcion, boolPermisoOpu)
{
    var tipo_solicitud = rec.get("descripcionSolicitud");
    var tituloDatosAdicionalesHal = 'Adicionales';
    var componenteVacio = Ext.create('Ext.Component', {
        html: '',
        width: 200,
        padding: 8,
        layout: 'anchor',
        style: {color: '#000000'}
    });
   
    cmbMotivosRePlanificacion = componenteVacio;
    cmbTecnicos               = componenteVacio;

    if (tipoAccion === 'replanificar')
    {
        cmbMotivosRePlanificacion = Ext.create('Ext.data.comboMotivosRePlanificacion', {
            id: 'cmbMotivoRePlanificacionHal',
            name: 'cmbMotivoRePlanificacionHal',
            fieldLabel: '* Motivo',
            labelStyle: "color:red;"});        
    }

    if (prefijoEmpresa == "TN")
    {
        if (tipo_solicitud == "Solicitud Planificacion" || tipo_solicitud == "Solicitud Migracion")
        {
            storeTecnicos.getProxy().extraParams.tipo_esquema   = rec.get("tipo_esquema");

            const strIngeniero = rec.get("tipo_esquema") && rec.get("tipo_esquema") == 1 ? 'RADIO' : 'IPCCL2';

            cmbTecnicos = new Ext.form.ComboBox({
                id: 'cmbTecnicoHal',
                name: 'cmbTecnicoHal',
                fieldLabel: `Ingeniero ${strIngeniero}`,
                anchor: '100%',
                queryMode: 'remote',
                emptyText: `Seleccione Ingeniero ${strIngeniero}`,
                width: 350,
                store: storeTecnicos,
                displayField: 'nombre_tecnico',
                valueField: 'id_tecnico',
                layout: 'anchor',
                disabled: false
            });
        }
    }
    
    if(rec.get("muestraIngL2") == "N")
    { 
        cmbTecnicos.setVisible(false);
    } 

    
    itemTercerizadora = componenteVacio;

    if (rec.get("tercerizadora")) {
        itemTercerizadora = new Ext.form.TextField({
            xtype: 'textfield',
            fieldLabel: 'Tercerizadora',
            name: 'fieldtercerizadora',
            id: 'fieldtercerizadora',
            value: rec.get("tercerizadora"),
            allowBlank: false,
            readOnly: true
        });
    }

    var radbuttonHal = '<div align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
        '<input type="radio"'+
        ' onchange="opcionesHal(1,'+rec.get("id_factibilidad")+','+rec.get("intIdDetalle")+','+rec.get("intIdComunicacion")+');'+
        ' " value="halDice" name="radioCuadrilla" id="radio_a">&nbsp;'+
        ' Mejor Opci&oacuten&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio"'+
        ' onchange="opcionesHal(2,'+rec.get("id_factibilidad")+','+rec.get("intIdDetalle")+','+rec.get("intIdComunicacion")+');"'+
        ' value="halSugiere" name="radioCuadrilla" id="radio_b">&nbsp;Sugerencias&nbsp;&nbsp;&nbsp;&nbsp;</div>';

    var radioButtonAA = '<div align="left" id="divAtenderAntes" style="display:none;">'+
                        '<label><b>¿De existir disponibilidad, el cliente desea ser atendido antes de la fecha acordada?</b></label>&nbsp;&nbsp;'+
                        '<input type="checkbox" id="cboxAtenderAntes" name="cboxAtenderAntes" >'+
                        '</div>';

    /* Componente para los radio button */
    radioAtenderAntes = Ext.create('Ext.Component',
    {
        html    : radioButtonAA,
        width   : 600,
        padding : 10,
        style   : { color: '#000000' }
    });

    /* Componente para los radio button */
    radiosTiposHal =  Ext.create('Ext.Component',
    {
        html    : radbuttonHal,
        width   : 600,
        padding : 10,
        style   : { color: '#000000' }
    });

    FieldNotificacionHal = new Ext.form.field.Display(
    {
        xtype : 'displayfield',
        id    : 'notificacionHal',
        name  : 'notificacionHal'
    });

    /* Store que obtiene las sugerencias de hal */
    storeIntervalosHal = new Ext.data.Store(
    {
        pageSize : 1000,
        total    : 'total',
        async    : false,
        proxy:
        {
            type : 'ajax',
            url  : url_getIntervalosHal,
            reader:
            {
            type: 'json',
            totalProperty: 'total',
            root: 'intervalos'
            }
        },
        fields:
        [
            {name: 'idSugerencia'      , mapping: 'idSugerencia'},
            {name: 'fecha'             , mapping: 'fecha'},
            {name: 'horaIni'           , mapping: 'horaIni'},
            {name: 'fechaTexto'        , mapping: 'fechaTexto'},
            {name: 'segTiempoVigencia' , mapping: 'segTiempoVigencia'},
            {name: 'fechaVigencia'     , mapping: 'fechaVigencia'},
            {name: 'horaVigencia'      , mapping: 'horaVigencia'}
        ],
        listeners:
        {
            load: function(sender, node, records)
            {
                if (tipoHal === 2) {
                    Ext.getCmp('nueva_sugerencia').setDisabled(false);
                }
                var mensaje    = '';
                var boolExiste = (typeof sender.getProxy().getReader().rawData === 'undefined') ? false :
                            (typeof sender.getProxy().getReader().rawData.mensaje === 'undefined') ? false : true;
                Ext.getCmp('notificacionHal').setValue(null);
                if (boolExiste) {
                    mensaje = sender.getProxy().getReader().rawData.mensaje;
                    if (mensaje != null || mensaje != '') {
                        Ext.getCmp('notificacionHal').setValue(mensaje);
                    } 
                } 
                else 
                {
                    mensaje = '<b style="color:red";>Error interno, Comunique a Sistemas..!!</b>';
                    Ext.getCmp('notificacionHal').setValue(mensaje);
                }
                formPanelHalPrincipal.refresh;
            }
        }
    });

    /* Model para la seleccion de las sugerencias  */
    selModelIntervalos = Ext.create('Ext.selection.CheckboxModel',
    {
        mode: 'SINGLE'
    });

    /* Componente para fecha Sugerida por el cliente */
    FieldFechaSugerida = new Ext.form.field.Display(
    {
        xtype      : 'displayfield',
        fieldLabel : 'Fecha Solicitada',
        width      :  90,
        padding    : '6px'
    });

    /* Componente para fecha Sugerida por el cliente */
    DTFechaSugerida = new Ext.form.DateField(
    {
        id       : 'fecha_sugerida',
        name     : 'fecha_sugerida',
        xtype    : 'datefield',
        format   : 'Y-m-d',
        editable : false,
        //minValue : fechaActual,
        width    : 120
    });

    /* Componente para la hora Sugerida por el cliente */
    FieldHoraSugerida = new Ext.form.field.Display(
    {
        xtype      : 'displayfield',
        fieldLabel : 'Hora',
        width      : 32,
        padding    : '3px'
    });

    TMHoraSugerida = new Ext.form.TimeField(
    {
        xtype     : 'timefield',
        format    : 'H:i',
        id        : 'hora_sugerida',
        name      : 'hora_sugerida',
        minValue  : '00:00',
        maxValue  : '23:59',
        increment : 15,
        editable  : false,
        width     : 75
    });

    /* Grid de intervalos */
    gridIntervalos = Ext.create('Ext.grid.Panel',
    {
        width       : 650,
        height      : 240,
        collapsible : false,
        title       : 'Sugerencias',
        id          : 'gridIntervalos',
        selModel    : selModelIntervalos,
        store       : storeIntervalosHal,
        loadMask    : true,
        frame       : true,
        forceFit    : true,
        autoRender  : true,
        enableColumnResize :false,
        listeners:
        {
            itemdblclick: function( view, record, item, index, eventobj, obj ){
            var position = view.getPositionByEvent(eventobj),
            data = record.data,
            value = data[this.columns[position.column].dataIndex];
            Ext.Msg.show({
            title:'Copiar texto?',
            msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
            buttons: Ext.Msg.OK,
            icon: Ext.Msg.INFORMATION
            });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                    grid.cellIndex = cellIndex;
                    grid.recordIndex = recordIndex;
                }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
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
        dockedItems: 
        [
            {
                xtype : 'toolbar',
                dock  : 'top',
                align : '->',
                items : 
                [
                    FieldFechaSugerida,
                    DTFechaSugerida,
                    '-',
                    FieldHoraSugerida,
                    TMHoraSugerida,
                    { xtype: 'tbfill' },
                    {
                        text     : 'Nueva Sugerencia',
                        iconCls  : 'icon_aprobar',
                        disabled : true,
                        itemId   : 'automatica',
                        scope    : this,
                        id       : 'nueva_sugerencia',
                        name     : 'nueva_sugerencia',
                        handler: function()
                        {
                            var idDetalle = null;
                            var idComunicacion = null;
                            Ext.getCmp('nueva_sugerencia').setDisabled(true);
                            nIntentos = nIntentos + 1;
                            if (rec.get("intIdDetalle"))
                            {
                                idDetalle = rec.get("intIdDetalle");
                            }
                            if (rec.get("intIdComunicacion"))
                            {
                                idComunicacion = rec.get("intIdComunicacion");
                            }
                            storeIntervalosHal.getProxy().extraParams.idDetalle      = idDetalle;
                            storeIntervalosHal.getProxy().extraParams.idComunicacion = idComunicacion;                            
                            storeIntervalosHal.getProxy().extraParams.idCaso         = 0;
                            storeIntervalosHal.getProxy().extraParams.idHipotesis    = 0;
                            storeIntervalosHal.getProxy().extraParams.idAdmiTarea    = 0;
                            storeIntervalosHal.getProxy().extraParams.nIntentos      = nIntentos;
                            storeIntervalosHal.getProxy().extraParams.fechaSugerida  = Ext.getCmp('fecha_sugerida').value;
                            storeIntervalosHal.getProxy().extraParams.horaSugerida   = Ext.getCmp('hora_sugerida').value;
                            storeIntervalosHal.getProxy().extraParams.tipoHal        = tipoHal;
                            storeIntervalosHal.load();
                        }
                    }
                ]
            }
        ],
        viewConfig:
        {
            enableTextSelection: true,
            stripeRows: true,
            emptyText: 'Sin datos para mostrar, Por favor leer la Notificación HAL'
        },
        columnLines: true,
        columns:
        [
            {
                id: 'id_Sugerencia',
                header: "id_Sugerencia",
                dataIndex: 'idSugerencia',
                hidden: true,
                hideable: false
            },
            {
                id: 'fecha_disponible',
                header: 'Fecha Disponible',
                dataIndex: 'fecha',
                width: 90
            },
            {
                id: 'horaIni_disponible',
                header: 'Hora Inicio',
                dataIndex: 'horaIni',
                width: 60
            },
            {
                id: 'fechaTexto',
                header: 'Mensaje',
                dataIndex: 'fechaTexto',
                width: 310
            },
            {
                id: 'tiempo_reserva',
                header: 'Reserva (Seg)',
                dataIndex: 'segTiempoVigencia',
                width: 80
            },
            {
                id: 'hora_fin_reserva',
                header: 'Hora Fin Reserva',
                dataIndex: 'horaVigencia',
                width: 130,
                hidden: true
            },
            {
                id: 'fecha_reserva',
                dataIndex: 'fechaVigencia',
                hidden: true,
                hideable: false
            }
        ]
    });

    /* Inavilitamos el gid */
    gridIntervalos.setVisible(false);

    /* Grid hal dice */
    gridHalDice = Ext.create('Ext.grid.Panel',
    {
        title: 'Sugerencia de Hal',
        id: 'gridHalDice',
        width: 650,
        height: 100,
        autoRender:true,
        enableColumnResize :false,
        store: storeIntervalosHal,
        listeners:
        {
            itemdblclick: function( view, record, item, index, eventobj, obj ){
            var position = view.getPositionByEvent(eventobj),
            data = record.data,
            value = data[this.columns[position.column].dataIndex];
            Ext.Msg.show({
                title:'Copiar texto?',
                msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.INFORMATION
            });
            },
            viewready: function (grid) 
            {
                var view = grid.view;
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
        viewConfig:
        {
            enableTextSelection: true,
            stripeRows: true,
            emptyText: 'Sin datos para mostrar, Por favor leer la Notificación HAL'
        },
        loadMask: true,
        frame:true,
        forceFit:true,
        columns:
        [
            {
                id: 'id_Sugerencia_hal_dice',
                header: "id_Sugerencia",
                dataIndex: 'idSugerencia',
                hidden: true,
                hideable: false
            },
            {
                id: 'fecha_disponible_hal_dice',
                header: 'Fecha Disponible',
                dataIndex: 'fecha',
                width: 90
            },
            {
                id: 'horaIni_disponible_hal_dice',
                header: 'Hora Inicio',
                dataIndex: 'horaIni',
                width: 60
            },
            {
                id: 'fechaTexto_hal_dice',
                header: 'Mensaje',
                dataIndex: 'fechaTexto',
                width: 310
            },
            {
                id: 'tiempo_reserva_hal_dice',
                header: 'Reserva (Seg)',
                dataIndex: 'segTiempoVigencia',
                width: 80
            },
            {
                id: 'hora_fin_reserva_hal_dice',
                header: 'Hora Fin Reserva',
                dataIndex: 'horaVigencia',
                width: 130,
                hidden: true
            },
            {
                id: 'fecha_reserva_hal_dice',
                dataIndex: 'fechaVigencia',
                hidden: true,
                hideable: false
            }
        ]
    });

    gridHalDice.setVisible(false);

    formPanelHalDatosGenerales = Ext.create('Ext.form.Panel',
    {
        xtype: 'panel',
        border: false,
        layout: {type: 'vbox', align: 'left'},
        items: 
        [
            {
                xtype: 'fieldset',
                id: 'client-data-fieldset_hal',
                title: 'Datos del Cliente',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                layout: 'auto',
                defaults: {
                    width: '350px'
                },
                items: 
                [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Cliente',
                        name: 'info_cliente',
                        id: 'info_cliente_hal',
                        value: rec.get("cliente"),
                        allowBlank: false,
                        readOnly: true,
                        //width: 200,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Login',
                        name: 'info_login',
                        id: 'info_login_hal',
                        value: rec.get("login2"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Ciudad',
                        name: 'info_ciudad',
                        id: 'info_ciudad_hal',
                        value: rec.get("ciudad"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Direccion',
                        name: 'info_direccion',
                        id: 'info_direccion_hal',
                        value: rec.get("direccion"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Sector',
                        name: 'info_nombreSector',
                        id: 'info_nombreSector_hal',
                        value: rec.get("nombreSector"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Es Recontratacion',
                        name: 'es_recontratacion',
                        id: 'es_recontratacion_hal',
                        value: rec.get("esRecontratacion"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: 
                [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Servicio',
                        name: 'info_servicio',
                        id: 'info_servicio_hal',
                        value: rec.get("productoServicio"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Tipo Orden',
                        name: 'tipo_orden_servicio',
                        id: 'tipo_orden_servicio_hal',
                        value: rec.get("tipo_orden"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Tipo Enlace',
                        name: 'strTipoEnlace',
                        id: 'strTipoEnlace_hal',
                        value: rec.get("strTipoEnlace"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110
                    },
                    itemTercerizadora,
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Teléfonos',
                        name: 'telefonos_punto',
                        id: 'telefonos_punto_hal',
                        value: rec.get("telefonos"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110,
                        height: 60
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Observación',
                        name: 'observacion_punto',
                        id: 'observacion_punto_hal',
                        value: rec.get("observacion"),
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: 110,
                        height: 60

                    },
                ]
            },
            componenteVacio                                   
        ]
    });

    formPanelHalDatosHal = Ext.create('Ext.form.Panel',
        {
            xtype: 'panel',
            border: false,
            layout: {type: 'vbox', align: 'stretch'},
            items: 
            [
                {
                    xtype :'fieldset',
                    title :'&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue";>Notificación HAL</b>',
                    items :
                    [
                        FieldNotificacionHal
                    ]
                },
                {
                    xtype : 'fieldset',
                    title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Asignación de Tareas HAL</b>',
                    items :
                    [
                        radiosTiposHal,
                        gridHalDice,
                        gridIntervalos,
                        radioAtenderAntes
                    ]
                },
                {
                    xtype : 'fieldset',
                    title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos '+tituloDatosAdicionalesHal+'</b>',
                    items :
                    [
                        cmbTecnicos,
                        cmbMotivosRePlanificacion,
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Observación de Planificación',
                            name: 'txtObservacionPlanfHal',
                            id: 'txtObservacionPlanfHal',
                            value: rec.get("observacion"),
                            allowBlank: false,
                            height: 60,
                            anchor    : '100%',
                            listeners:
                                {
                                    blur: function(field)
                                    {
                                        observacionPlanF = field.getValue();
                                    }
                                }
                        }
    
                    ]
                }
            ]
        }
    );

    /* Panel principal para la comunicacion con hal */
    formPanelHalPrincipal = Ext.create('Ext.form.Panel',
    {
        title: "HAL",
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults:
        {
            labelAlign: 'left',
            labelWidth: 200,
            msgTarget: 'side'
        },
        layout: {type: 'hbox', align: 'stretch'},
        items:
        [
            formPanelHalDatosGenerales,
            formPanelHalDatosHal
        ],
        buttons:
        [
            {
                text: 'Guardar',
                handler: function() 
                {
                    if (tipoAccion === 'planificar')
                    {
                        grabarProgramarHal(rec, prefijoEmpresa, origen, opcion);
                    }
                    else if(tipoAccion === 'replanificar')
                    {
                        grabarReplanificarHal(rec, prefijoEmpresa, origen, boolPermisoOpu);
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function()
                {
                    nIntentos     = 0;
                    seleccionaHal = false;
                    Ext.getCmp('fecha_sugerida').setValue(null);
                    Ext.getCmp('hora_sugerida').setValue(null);
                    if (tipoAccion === 'planificar' )
                    {
                        winAsignacionIndividual.destroy();
                    }
                    else if(tipoAccion === 'replanificar' )
                    {
                        winRePlanificar.destroy();
                    }

                    //Notificar a HAL al presionar botón Cerrar                    
                    if (gridHalDice.getStore().data.items.length > 0)
                    {
                        var idSugerencias = '';
                        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
                        {
                            idSugerencias = idSugerencias + gridHalDice.getStore().data.items[i].data.idSugerencia+'|';
                        }

                        Ext.Ajax.request
                        ({
                            url    :  urlNotificarCancelarHal,
                            method : 'post',
                            params :
                            {
                                idSugerencia  : idSugerencias
                            }
                        });
                    }
                }
            }
        ]
    });

    return formPanelHalPrincipal;
}

function grabarProgramarHal(rec, prefijoEmpresa, origen, opcion)
{
    var param            = '';
    var boolError        = true;
    var boolErrorTecnico = false;
    var idPerTecnico     = 0;
    esHal                = 'S';
    id                   = rec.data.id_factibilidad;
    param                = rec.data.id_factibilidad;

    if ((prefijoEmpresa == "TN" 
        && (rec.data.descripcionSolicitud == "Solicitud Planificacion" || rec.data.descripcionSolicitud == "Solicitud Migracion"))
        || (prefijoEmpresa == "TNP" 
        && rec.get("ultimaMilla") == "FTTx" && rec.data.descripcionSolicitud == "Solicitud Planificacion"))
    {
        idPerTecnico = Ext.getCmp('cmbTecnicoHal').value;
        if (!idPerTecnico && (rec.get("muestraIngL2") == "S")) 
        {
            boolErrorTecnico = true;
        }
    }

    if (boolErrorTecnico)
    {
        Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
    } 
    else
    {
        if (boolError)
        {
            var txtObservacion = Ext.getCmp('txtObservacionPlanfHal').value;
            var id_factibilidad = rec.get("id_factibilidad");
            var atenderAntes = "N";
            var idSugerencia;
            var fechaVigencia;
            boolError = false;

            if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
            {
                boolError = true;
            }
            if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
            {
                boolError = true;
            }

            if(document.getElementById('cboxAtenderAntes').checked)
            {
                atenderAntes = "S";
            }

            if (!seleccionaHal)
            {
                Ext.Msg.alert("Alerta","Debe escoger una opción de Hal...!!");
                return;
            }

            if (tipoHal == 1)
            {
                if (gridHalDice.getStore().data.items.length < 1)
                {
                    Ext.Msg.alert("Alerta","No se obtuvieron sugerencias de hal...!!");
                    return;
                }

                for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
                {
                    idSugerencia  = gridHalDice.getStore().data.items[i].data.idSugerencia;
                    fecha         = gridHalDice.getStore().data.items[i].data.fecha;
                    hora          = gridHalDice.getStore().data.items[i].data.horaIni;
                    fechaVigencia = gridHalDice.getStore().data.items[i].data.fechaVigencia;
                }
            }
            else
            {
                if (selModelIntervalos.getSelection().length < 1)
                {
                    Ext.Msg.alert("Alerta","Debe escoger una fecha...!!");
                    return;
                }

                for (var ind = 0; ind < selModelIntervalos.getSelection().length; ++ind)
                {
                    idSugerencia  = selModelIntervalos.getSelection()[ind].data.idSugerencia;
                    fecha         = selModelIntervalos.getSelection()[ind].data.fecha;
                    hora          = selModelIntervalos.getSelection()[ind].data.horaIni;
                    fechaVigencia = selModelIntervalos.getSelection()[ind].data.fechaVigencia;
                }
            }

            if (!boolError)
            {
                strMensaje = "Se asignará el responsable. Desea continuar?";
                Ext.Msg.confirm('Alerta', strMensaje, function(btn) {
                    if (btn == 'yes') {
                        connAsignarResponsable.request({
                            url: "../../planificar/coordinar/programar",
                            method: 'post',
                            timeout: 3000000,
                            params: {
                                origen        : origen,
                                id            : id,
                                param         : param,
                                idPerTecnico  : idPerTecnico,
                                observacion   : txtObservacion,
                                opcion        : opcion,
                                idIntWifiSim  : JSON.stringify(rec.data.idIntWifiSim),
                                idIntCouSim   : JSON.stringify(rec.data.idIntCouSim),
                                idSugerencia  : idSugerencia,
                                fechaVigencia : fechaVigencia,
                                atenderAntes  : atenderAntes,
                                esHal         : esHal
                            },
                            success: function(response) {
                                var text        = response.responseText;
                                var intPosicion = text.indexOf("Correctamente");

                                if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                    text == "Se coordinó la solicitud" || intPosicion !== -1)
                                {
                                    cierraVentanaAsignacionIndividual();
                                    Ext.Msg.alert('Mensaje', text, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                } else {
                                    var mm = Ext.Msg.show({
                                                title:'Mensaje del sistema',
                                                msg: text,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.Msg.ERROR
                                             });
                                       Ext.defer(function()
                                       {
                                           mm.toFront();
                                       }, 50);
        
                                }
                            },
                            failure: function(result) {
                                Ext.Msg.alert('Alerta', result.responseText);
                            }
                        });
                    }
                });
            } 
            else
            {
                Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
            }
        }
    }
}

function grabarReplanificarHal(rec, prefijoEmpresa, origen, boolPermisoOpu)
{
    var txtObservacion  = Ext.getCmp('txtObservacionPlanfHal').value;
    var cmbMotivo       = Ext.getCmp('cmbMotivoRePlanificacionHal').value;
    var boolPerfilOpu   = true;
    var id_factibilidad = rec.get("id_factibilidad");
    var idDetalle       = rec.get("intIdDetalle");
    var atenderAntes    = "N";
    esHal               = 'S';
    var idSugerencia;
    var fechaVigencia;

    if (prefijoEmpresa == "TNP" || !boolPermisoOpu || (boolPermisoOpu && rec.get("descripcionSolicitud") != "Solicitud Planificacion"))
    {
        boolPerfilOpu    = false;
        var boolError    = false;
        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
        {
            boolError = true;
        }
    }
    if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
    {
        boolError = true;
    }
    if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
    {
        boolError = true;
    }


    var param            = '';
    var boolErrorTecnico = false;
    var idPerTecnico     = 0;
    id                   = rec.data.id_factibilidad;
    param                = rec.data.id_factibilidad;
    if (prefijoEmpresa == "TN" 
        && (rec.data.descripcionSolicitud == "Solicitud Planificacion" || rec.data.descripcionSolicitud == "Solicitud Migracion"))
    {
        idPerTecnico = Ext.getCmp('cmbTecnicoHal').value;
        if (!idPerTecnico)
        {
            boolErrorTecnico = true;
        }
    }

    if(document.getElementById('cboxAtenderAntes').checked)
    {
        atenderAntes = "S";
    }

    if (!seleccionaHal)
    {
        Ext.Msg.alert("Alerta","Debe escoger una opción de Hal...!!");
        return;
    }

    if (tipoHal == 1)
    {
        if (gridHalDice.getStore().data.items.length < 1)
        {
            Ext.Msg.alert("Alerta","No se obtuvieron sugerencias de hal...!!");
            return;
        }

        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
        {
            idSugerencia  = gridHalDice.getStore().data.items[i].data.idSugerencia;
            fecha         = gridHalDice.getStore().data.items[i].data.fecha;
            hora          = gridHalDice.getStore().data.items[i].data.horaIni;
            fechaVigencia = gridHalDice.getStore().data.items[i].data.fechaVigencia;
        }
    }
    else
    {
        if (selModelIntervalos.getSelection().length < 1)
        {
            Ext.Msg.alert("Alerta","Debe escoger una fecha...!!");
            return;
        }

        for (var ind = 0; ind < selModelIntervalos.getSelection().length; ++ind)
        {
            idSugerencia  = selModelIntervalos.getSelection()[ind].data.idSugerencia;
            fecha         = selModelIntervalos.getSelection()[ind].data.fecha;
            hora          = selModelIntervalos.getSelection()[ind].data.horaIni;
            fechaVigencia = selModelIntervalos.getSelection()[ind].data.fechaVigencia;
        }
    }

    if (boolErrorTecnico)
    {
        Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
    }
    else
    {
        if (!boolError)
        {
            strMensaje = "Se asignará el responsable. Desea continuar?";

            Ext.Msg.confirm('Alerta', strMensaje, function(btn) 
            {
                if (btn == 'yes') 
                {
                    connCoordinar.request({
                        url    : "replanificar",
                        method : 'post',
                        timeout: 450000,
                        params : {
                            origen        : origen,
                            id            : id_factibilidad,
                            idDetalle     : idDetalle,
                            param         : param,
                            idPerTecnico  : idPerTecnico,
                            observacion   : txtObservacion,
                            id_motivo     : cmbMotivo,
                            boolPerfilOpu : boolPerfilOpu,
                            idSugerencia  : idSugerencia,
                            fechaVigencia : fechaVigencia,
                            atenderAntes  : atenderAntes,
                            esHal         : esHal
                        },
                        success: function(response) {
                            var text        = response.responseText;
                            var intPosicion = text.indexOf("Correctamente");

                            if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                text == "Se replanificó la solicitud" || intPosicion !== -1)
                            {
                                cierraVentanaRePlanificar();
                                Ext.Msg.alert('Mensaje', text, function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                    }
                                });
                            }
                            else 
                            {
                               var mm = Ext.Msg.show({
                                            title:'Mensaje del sistema',
                                            msg: text,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.Msg.ERROR
                                        });
                               Ext.defer(function() {
                                   mm.toFront();
                               }, 50);
                            }

                        },
                        failure: function(result) {
                            Ext.Msg.alert('Alerta', result.responseText);
                        }
                    });
                }
            });
        } 
        else 
        {
            Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
        }
    }
}

    /*Función mejorada que muestra la pantalla del Validador de Excedente de Materiales */

    function validadorExcedenteMaterial(rec)
    {
    var intIdServicio                   = rec.data.id_servicio;
    var strModulo                       = rec.raw.strModulo;
    var strValorMetraje                 = rec.data.strMetraje;
    var strSolExcedenteMaterial         = rec.raw.solExcedenteMaterial;
    var intMetrosDeDistancia            = rec.raw.metrosDeDistancia ;
    var intPrecioFibra                  = rec.raw.precioFibra;
    var floatValorCaractOCivil          = rec.raw.floatValorCaractOCivil;
    var floatValorCaractOtrosMateriales = rec.raw.floatValorCaractOtrosMateriales;
    var floatValorCaractCancPorCli      = rec.raw.floatValorCaractCancPorCli;
    var floatValorCaractAsumeCli        = rec.raw.floatValorCaractAsumeCli;
    var floatValorCaractAsumeEmpresa    = rec.raw.floatValorCaractAsumeEmpresa;
    var floatSubtotalOtrosClientes      = parseFloat(floatValorCaractOCivil) + parseFloat(floatValorCaractOtrosMateriales);
    
    var strBotonModulo                = '';
    var winValidadorExcedente         = "";
    var resultado1                    = 0;
    var PrecioObraCivil               = 0;
    var PrecioOtrosMate               = 0;
    var suma                          = 0;

    if (!strValorMetraje)
    {
       Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos, no existe metraje');
    }

    if(strModulo=='PLANIFICACION')
    {
        strBotonModulo = 'Enviar a comercial';
    }
    else if(strModulo=='COMERCIAL')
    {
        strBotonModulo = 'Validar';
    }
    
    var formPanelCreacionTarea = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding: 15px; border: 0px none;",
        height: 500,
        width: 350,
        frame: true,
        items: [

            //Resumen del cliente (muestra el numero de la solicitud y el estado)
            { width: '10%', border: false },
            {
                xtype: 'label',
                forId: 'lbl_InfoSolExcedente',
                style: "font-weight:bold; color:blue;",
                text: strSolExcedenteMaterial + '\n ',
                margin: '0 0 30 0'
            },
            //-------------------PROYECTOS/CLIENTES EXCEPCIÒN-------------  
            { width: '10%', border: false },
            {
                xtype: 'panel',
                border: false,
                frame: true,
                layout: { type: 'hbox', align: 'stretch' },
                hidden : true,
                items: [
                    {
                        xtype: 'label',
                        forId: 'lbl_clientes_excepcion',
                        text: 'PROYECTOS/CLIENTES EXCEPCIÒN :',
                        margin: '0 0 0 15'
                    }
                ]
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Modulo:',
                name: 'txt_Modulo',
                id: 'txt_Modulo',
                value: strModulo,
                allowBlank: false,
                readOnly: true,
                style: "width:75%",
                hidden:true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Valor Predeterminado (metros):',
                name: 'txt_ValorPredeterminado',
                id: 'txt_ValorPredeterminado',
                value: '0',
                allowBlank: false,
                readOnly: true,
                style: "width:75%",
                hidden : true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Metraje Inspecciòn FO:',
                name: 'txt_MetrajeInpeccion',
                id: 'txt_MetrajeInpeccion',
                value: '0',
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                hidden : true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Diferencia de FO (metros):',
                name: 'txt_DiferenciaDeFibra',
                id: 'txt_DiferenciaDeFibra',
                value: '0',
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                hidden : true
            },
            {
                xtype: 'textfield',
                fieldLabel: '<b> Sub Total: </b>',
                name: 'txt_SubTotalProyectos',
                id: 'txt_SubTotalProyectos',
                value: '0',
                allowBlank: false,
                readOnly: true,
                style: "width:75%",
                hidden : true
            },
            // -----------OTROS CLIENTES-------------    
            { width: '10%', border: false },
            {
                xtype: 'panel',
                border: false,
                frame: true,
                layout: { type: 'hbox', align: 'stretch' },
                items: [
                    {
                        xtype: 'label',
                        forId: 'lbl_OtrosClientes',
                        text: 'OTROS CLIENTES :',
                        margin: '0 0 0 15'
                    }
                ]
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Fibra (metros)',
                name: 'txt_FibraMetros',
                id: 'txt_FibraMetros',
                value: parseFloat(strValorMetraje),
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {
                            Ext.getCmp('txt_PrecioFibra').setValue
                            (
                                //le da el valor a al precio de fibra
                                parseFloat(resultado1 = Ext.getCmp("txt_FibraMetros").getValue() > parseFloat(intMetrosDeDistancia) ?
                                    (Ext.getCmp("txt_FibraMetros").getValue() - parseFloat(intMetrosDeDistancia)) * parseFloat(intPrecioFibra) : 0)
                            );
                            
                            parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                            parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                            parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate));
                            parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma));
                            parseFloat(Ext.getCmp('txt_Total').setValue(suma))
                            parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Precio Fibra',
                name: 'txt_PrecioFibra',
                id: 'txt_PrecioFibra',
                value: (parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia)?
                        (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia)) * parseFloat(intPrecioFibra) : 0),
                allowBlank: false,
                readOnly: true,
                style: "width:75%"
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Precio Obra Civil',
                name: 'txt_PrecioObraCivil',
                id: 'txt_PrecioObraCivil',
                value: parseFloat(floatValorCaractOCivil),
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {
                            parseFloat(resultado1 = Ext.getCmp("txt_PrecioFibra").value);
                            parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                            parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                            parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                            parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                            parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                            //Resetear los copago
                            parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Precio Otros Materiales',
                name: 'txt_PrecioOtrosMate',
                id: 'txt_PrecioOtrosMate',
                value: parseFloat(floatValorCaractOtrosMateriales),
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {
                            parseFloat(resultado1 = Ext.getCmp("txt_PrecioFibra").value);
                            parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                            parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                            parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                            parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                            parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                            //Resetear los copago
                            parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                        }
                    }
                }
            },
            {
                xtype: 'numberfield',
                fieldLabel: '<b> Sub Total: </b>',
                name: 'txt_SubTotalOtrosClientes',
                id: 'txt_SubTotalOtrosClientes',
                allowBlank: false,
                value: (parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia) ?
                             ( (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia) ) * parseFloat(intPrecioFibra) 
                                     + parseFloat(floatSubtotalOtrosClientes)   )
                                  : floatSubtotalOtrosClientes),
                readOnly: true,
                style: "width:75%",
                maskRe: /[0-9.]/
            },
            // -----------COPAGOS-------------    
            { width: '10%', border: false },
            {
                xtype: 'panel',
                border: false,
                frame: true,
                layout: { type: 'hbox', align: 'stretch' },
                items: [
                    {
                        xtype: 'label',
                        forId: 'lbl_copagos',
                        text: 'COPAGOS :',
                        margin: '0 0 0 15'
                    }
                ]
            },
            {
                xtype: 'numberfield',
                fieldLabel: '% Cancelado por el cliente:',
                name: 'txt_CanceladoPorCliente',
                id: 'txt_CanceladoPorCliente',
                value: floatValorCaractCancPorCli,
                allowBlank: false,
                readOnly : strModulo == "PLANIFICACION" ? true : false,
                style: "width:75%",
                maskRe: /[0-9.]/,
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {

                            parseFloat(SubTotalOtrosClientes = Ext.getCmp("txt_SubTotalOtrosClientes").value);
                            parseFloat(PorcentajeCanceladoPorCliente = Ext.getCmp('txt_CanceladoPorCliente').value);
                            parseFloat(AsumeCliente = Ext.getCmp('txt_AsumeCliente').value);
                            parseFloat(CalculoAsumeCliente = ((SubTotalOtrosClientes * PorcentajeCanceladoPorCliente) / 100));
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(CalculoAsumeCliente));
                            parseFloat(CalculoAsumeEmpresa = SubTotalOtrosClientes - CalculoAsumeCliente);
                            parseFloat(Ext.getCmp('txt_Total').setValue(CalculoAsumeEmpresa));
                            parseFloat((Ext.getCmp('txt_AsumeEmpresa').setValue(CalculoAsumeEmpresa)) )
                        }
                    }
                }
            },
            {
                xtype: 'numberfield',
                fieldLabel: 'Asume el cliente:',
                name: 'txt_AsumeCliente',
                id: 'txt_AsumeCliente',
                value: parseFloat(floatValorCaractAsumeCli),
                allowBlank: false,
                readOnly : strModulo == "PLANIFICACION" ? true : false,
                style: "width:75%",
                maskRe: /[0-9.]/
            },
            {
                xtype: 'numberfield',
                fieldLabel: 'Asume la empresa:',
                name: 'txt_AsumeEmpresa',
                id: 'txt_AsumeEmpresa',
                value: parseFloat(floatValorCaractAsumeEmpresa),
                allowBlank: false,
                readOnly : strModulo == "PLANIFICACION" ? true : false,
                style: "width:75%",
                maskRe: /[0-9.]/
            },
            {
                xtype: 'numberfield',
                fieldLabel: '<b>Total:</b>',
                name: 'txt_Total',
                id: 'txt_Total',
                value: (floatValorCaractCancPorCli >0 ? floatValorCaractAsumeEmpresa:
                    parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia) ?
                             ( (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia) ) * parseFloat(intPrecioFibra) 
                                     + parseFloat(floatSubtotalOtrosClientes)   )
                                  : floatSubtotalOtrosClientes),
                style: "width:75%",
                maskRe: /[0-9.]/,
                readOnly : strModulo == "PLANIFICACION" ? true : false
            },        
            // -----------OBSERVACIÒN-------------
            { width: '10%', border: false },
            {
                xtype: 'label',
                forId: 'lbl_observacion',
                text: 'Observación :',
                margin: '0 0 0 15'
            },
            {
                xtype: 'textareafield',
                hideLabel: true,
                name: 'txt_Observacion',
                id: 'txt_Observacion',
                value: " ",
                width: 315,
                heigth: 200,
                readOnly: false
            },
            { width: '10%', border: false },
        ],
        buttons: [
            {
                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;' + strBotonModulo,
                handler: function () {
                    $.ajax({
                        url: urlValidadorMaterial,
                        type: "POST",
                        timeout: 600000,
                        data:
                        {
                            intIdServicio: intIdServicio,
                            precioMetroFibra: rec.raw.precioFibra,
                            //-------------------PROYECTOS/CLIENTES EXCEPCIÒN------------- 
                            valorPredeterminado: Ext.getCmp("txt_ValorPredeterminado").getValue() == '' ? 0 : Ext.getCmp("txt_ValorPredeterminado").getValue(),
                            metrajeInpeccion: Ext.getCmp("txt_MetrajeInpeccion").getValue() == '' ? 0 : Ext.getCmp("txt_MetrajeInpeccion").getValue(),
                            diferenciaDeFibra: Ext.getCmp("txt_DiferenciaDeFibra").getValue() == '' ? 0 : Ext.getCmp("txt_DiferenciaDeFibra").getValue(),
                            subTotalProyectos: Ext.getCmp("txt_SubTotalProyectos").getValue() == '' ? 0 : Ext.getCmp("txt_SubTotalProyectos").getValue(),
                            // -----------OTROS CLIENTES-------------
                            metrosFibra: Ext.getCmp("txt_FibraMetros").getValue() == '' ? 0 : Ext.getCmp("txt_FibraMetros").getValue(),
                            precioFibra: Ext.getCmp("txt_PrecioFibra").getValue(),
                            precioObraCivil: Ext.getCmp("txt_PrecioObraCivil").getValue() == '' ? 0 : Ext.getCmp("txt_PrecioObraCivil").getValue(),
                            precioOtrosMate: Ext.getCmp("txt_PrecioOtrosMate").getValue() == '' ? 0 : Ext.getCmp("txt_PrecioOtrosMate").getValue(),
                            subTotalOtrosClientes: Ext.getCmp("txt_SubTotalOtrosClientes").getValue() == '' ? 0 : Ext.getCmp("txt_SubTotalOtrosClientes").getValue(),
                             // -----------COPAGOS-------------    
                            canceladoPorCliente: Ext.getCmp("txt_CanceladoPorCliente").getValue() == '' ? 0 : Ext.getCmp("txt_CanceladoPorCliente").getValue(),
                            asumeCliente: Ext.getCmp("txt_AsumeCliente").getValue() == '' ? 0 : Ext.getCmp("txt_AsumeCliente").getValue(),
                            asumeEmpresa: Ext.getCmp("txt_AsumeEmpresa").getValue() == '' ? 0 : Ext.getCmp("txt_AsumeEmpresa").getValue(),
                            observacion:  Ext.getCmp("txt_Observacion").getValue()  == '' ? 0 : Ext.getCmp("txt_Observacion").getValue(),

                            //------ COMPROBAR DE QUE MODULO ENVÍA EL FORMULARIO
                            modulo:       Ext.getCmp("txt_Modulo").getValue()  == '' ? 0 : Ext.getCmp("txt_Modulo").getValue(),
                            
                            detalleSolId: rec.data.id_factibilidad,
                            totalPagar: Ext.getCmp("txt_Total").getValue(),
                        },
                        beforeSend: function () {
                            Ext.get(winValidadorExcedente.getId()).mask('Enviando datos...');
                        },
                        complete: function () {
                            Ext.get(winValidadorExcedente.getId()).unmask();
                        },
                        success: function (data) {
                            Ext.Msg.alert('Mensaje', data.mensaje, function (btn) {
                                if (btn == 'ok') {
                                    winValidadorExcedente.close();
                                    store.load();
                                }
                            });

                        },
                        failure: function (result) {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            },
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function () {
                    winValidadorExcedente.close();
                    winValidadorExcedente.destroy();
                }
            },
        ]
    });

        winValidadorExcedente = Ext.widget('window', {
            title: 'Validador de Excedente de Material '+ strModulo,
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: false,
            items: [formPanelCreacionTarea]
        });
        winValidadorExcedente.show();

}

// Funcion para ver archivos de evidencia en excedente de materiales cuando el cliente acepta
function verDocumento(rec)
{
    var id_servicio = rec.raw.id_servicio;
    var cantidadDocumentos = 1;
    var connDocumentos = new Ext.data.Connection
    ({
        listeners: 
        {
            'beforerequest': 
            {
                fn: function (con, opt) 
                { 
                    Ext.MessageBox.show
                    ( {
                        msg: 'Consultando documentos, Por favor espere!!',
                        progressText: 'Consultando...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                        }
                    );
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': 
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connDocumentos.request
    ( {
        url: url_verifica_documentos,
        method: 'post',
        params:{ idServicio: id_servicio },
        success: function (response)
        {
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if (cantidadDocumentos > 0)
            {
                var storeDocumentos = new Ext.data.Store
                (
                    {
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: url_verDocumentos,
                        reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams:
                        {
                            idServicio: id_servicio
                        }
                    },
                    fields:
                    [
                        {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                        {name: 'feCreacion', mapping: 'feCreacion'},
                        {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                        {name: 'idDocumento', mapping: 'idDocumento'}
                    ]
                }
                );

                Ext.define('Documentos', 
                {
                    extend: 'Ext.data.Model',
                    fields:
                    [
                        {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                        {name: 'feCreacion', mapping: 'feCreacion'},
                        {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                        {name: 'idDocumento', mapping: 'idDocumento'}
                    ]
                });

                //grid de documentos
                gridDocumentos = Ext.create('Ext.grid.Panel',
                {
                    id: 'gridMaterialesPunto',
                    store: storeDocumentos,
                    columnLines: true,
                    columns: 
                    [
                        {
                            header: 'Nombre Archivo',
                            dataIndex: 'ubicacionLogica',
                            width: 260
                        },
                        {
                            header: 'Fecha de Carga',
                            dataIndex: 'feCreacion',
                            width: 120
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Acciones',
                            width: 100,
                            items:
                            [
                                {
                                    iconCls: 'button-grid-show',
                                    tooltip: 'Ver Archivo Digital',
                                    handler: function (grid, rowIndex, colIndex) 
                                    {
                                        var rec = storeDocumentos.getAt(rowIndex);
                                        verArchivoDigital(rec);
                                    }
                                }
                            ]
                        }
                    ],
                    viewConfig:
                    {
                        stripeRows: true,
                        enableTextSelection: true
                    },
                    frame: true,
                    height: 200
                }
                );

                function verArchivoDigital(rec)
                {
                    var idDocumento = rec.get('idDocumento');
                    window.location = url_descargaDocumentos + '?idDocumento=' + idDocumento;
                }

                var formPanel = Ext.create('Ext.form.Panel',
                {
                    bodyPadding: 2,
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
                            title: '',
                            defaultType: 'textfield',
                            defaults: 
                            {
                                width: 510
                            },
                            items: 
                            [
                                gridDocumentos
                            ]
                        }
                    ],
                    buttons: 
                    [{
                        text: 'Cerrar',
                        handler: function ()
                        {
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window',
                {
                    title: 'Documentos Cargados',
                    modal: true,
                    width: 550,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
                
            } 
            else
            {
                Ext.Msg.show
                ({
                    title: 'Mensaje',
                    msg: 'El servicio seleccionado no posee archivos adjuntos.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                });
            }

        },
        failure: function (result)
        {
            Ext.Msg.show
            ( {
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            }
            );
        }
    });
}


function verTareasClientes(login) 
{
    
    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function () {
            winTareasClientes.destroy();
        }
    });

    storeTareasClientes = new Ext.data.Store({
        //pageSize: 1000,
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: strUrlVerTareasClientes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'result'
            },
            extraParams: 
            {
                login: login
            }
        },
        fields:
            [               
                { name: 'numeroTarea', mapping: 'numeroTarea' },
                { name: 'nombreProceso', mapping: 'nombreProceso' },
                { name: 'nombreTarea', mapping: 'nombreTarea' },
                { name: 'estado', mapping: 'estado' },
                { name: 'fechaCreacion', mapping: 'fechaCreacion' },
                { name: 'fechaEstado', mapping: 'fechaEstado' },
                { name: 'nombreDepartamento', mapping: 'nombreDepartamento' },
                { name: 'empleado', mapping: 'empleado' },
                { name: 'observacion', mapping: 'observacion' }
            ]
    });
    gridTareasCliente = Ext.create('Ext.grid.Panel', {
        id: 'gridTareasCliente',
        store: storeTareasClientes,
        columnLines: true,
        columns: [
            {
                id: 'numeroTarea',
                header: 'Número Tarea',
                dataIndex: 'numeroTarea',
                width: 80,
                sortable: true
            },
            {
                id: 'nombreProceso',
                header: 'Nombre Proceso',
                dataIndex: 'nombreProceso',
                width: 260,
                sortable: true
            },
            {
                id: 'nombreTarea',
                header: 'Nombre Tarea',
                dataIndex: 'nombreTarea',
                width: 260,
                sortable: true
            },
            {
                id: 'observacionTarea',
                header: 'Observación',
                dataIndex: 'observacion',
                width: 330,
                sortable: true
            },
            {
                id: 'estadoTarea',
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true,
                renderer: function (value, p, r) {
                    return value.charAt(0).toUpperCase() + value.slice(1);
                }
            },
            {
                id: 'fechaCreacion',
                header: 'Fecha Creación',
                dataIndex: 'fechaCreacion',
                width: 100,
                sortable: true
            },
            {
                id: 'fechaEstado',
                header: 'Fecha Gestión',
                dataIndex: 'fechaEstado',
                width: 100,
                sortable: true
            },
            {
                id: 'nombreDepartamento',
                header: 'Departamento',
                dataIndex: 'nombreDepartamento',
                width: 150,
                sortable: true
            }
            ,
            {
                id: 'empleadoAsignado',
                header: 'Empleado Asignado',
                dataIndex: 'empleado',
                width: 250,
                sortable: true
            }
        ],
        width: 1200,
        height: 300,
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
            },
            viewready: function (grid) {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
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
        }
    });

    formPanelTareasClientes = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: 300,
        width: 1200,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'left',            
            msgTarget: 'side'
        },

        items: [{
            xtype: 'fieldset',
            defaultType: 'textfield',
            items: [
                gridTareasCliente
            ]
        }]
    });

    winTareasClientes = Ext.create('Ext.window.Window', {
        title: 'Tareas Cliente : '+'<b>'+login+'</b>',
        modal: true,
        width: 1250,
        height: 400,
        resizable: true,
        layout: 'fit',
        items: [formPanelTareasClientes],
        buttonAlign: 'center',
        buttons: [btncancelar]
    }).show();
    }    

function showSeguimiento(rec, origen, opcion)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";
    if (!winAsignacionIndividual)
    {
        var id_servicio = rec.get("id_servicio");
        if(Ext.isEmpty(id_servicio))
        {
            id_servicio = rec.get("servicioId");
        }
        
        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            bodyStyle: "none",
            frame: true,
            heigth:710,
            items:
                [{
                        xtype: 'panel',
                        border: false,
                        //layout: {type: 'hbox', align: 'stretch'},
                        title: 'Seguimiento',
                        id:'panel2',
                        html:"<div  class=seguimiento_content id=seguimiento_content_"+id_servicio+">\n\
                                </div><table width=100% cellpadding=1 cellspacing=0  border=0><tr><td><div overflow=scroll, id=getPanelSeguimiento"+id_servicio+"></div></td></tr></table>",
                        width:1200,
                        heigth:710,
                       listeners:
                                {
                                    afterrender: function(cmp)
                                    {
                                        var idServicio = rec.get("id_servicio");
                                        if(Ext.isEmpty(idServicio))
                                        {
                                            idServicio = rec.get("servicioId");
                                        }
                                        grafica(idServicio);
                                        
                                        //setTimeout((panel2.getView().refresh()),300000);
                                        cmp.doLayout();
                                    }
                                }
                    }
                ],
            buttons: [
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ]       
        });
        
        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Seguimiento de Servicios',
                            //layout: 'fit',
                            resizable: true,
                            height: 425,
                            modal: true,
                            closable: false,
                            readOnly: true,
                            autoShow: true,
                            items: (formPanelAsignacionIndividual),
                            
                                    });
         winAsignacionIndividual.show();
    }

}
function grafica (objServicio)
{
    entidadSolicitudSeguimiento.initSeguimiento(objServicio, 'seguimiento_content'+"_"+objServicio,'getPanelSeguimiento'+objServicio);
    //tablaGrid(objServicio);
}

Ext.define('Task', {
    extend: 'Ext.data.Model',
    idProperty: 'taskId',
    fields: [
        {name: 'pedidoId', type: 'int'},
        {name: 'departamento', type: 'string'},
        {name: 'taskId', type: 'int'},
        {name: 'articulo', type: 'string'},
        {name: 'cantidad', type: 'float'},
        {name: 'estado', type: 'string'},
        {name: 'codArticulo', type: 'string'},
        {name: 'usrAsignado', type: 'string'}
    ]
});

function showPedidos(rec, origen, opcion)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";
    if (!winAsignacionIndividual)
    {
        var id_servicio = rec.get("id_servicio");
        if(Ext.isEmpty(id_servicio))
        {
            id_servicio = rec.get("servicioId");
        }
            
        var store = Ext.create('Ext.data.Store', {
            model: 'Task',
            autoLoad: true,
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: urlDetallePedido,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'pedidos'
                },
                extraParams: {
                    idServicio: id_servicio
                }
            },
        sorters: {property: 'due', direction: 'ASC'},
        groupField: 'departamento'
        });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    var showSummary = true;
        
        formPanelAsignacionIndividual = Ext.create('Ext.grid.Panel', {
        width: 800,
        height: 450,
        frame: true,
        title: 'Pedidos por Departamento',
        iconCls: 'icon-grid',
        renderTo: document.body,
        store: store,
        plugins: [cellEditing],

        features: [{
            id: 'group',
            ftype: 'groupingsummary',
            groupHeaderTpl: '{name}',
            hideGroupedHeader: true,
            enableGroupingMenu: false
        }],
        columns: [{
            text: 'Departamento / Articulo',
            flex: 1,
            tdCls: 'task',
            sortable: true,
            dataIndex: 'articulo',
            hideable: false,
            summaryType: 'count',
            summaryRenderer: function(value, summaryData, dataIndex) {
                return ((value === 0 || value > 1) ? '(' + value + ' Articulos)' : '(1 Articulo)');
            }
        }, {
            header: 'Departamento',
            width: 180,
            sortable: true,
            dataIndex: 'departamento'
        },{
            header: 'Cod_Articulo',
            width: 120,
            sortable: true,
            dataIndex: 'codArticulo'
        } , {
            header: 'Cantidad',
            width: 75,
            sortable: true,
            dataIndex: 'cantidad',
            summaryType: 'sum',
            renderer: function(value, metaData, record, rowIdx, colIdx, store, view){
                return value ;
            },
            summaryRenderer: function(value, summaryData, dataIndex) {
                return value ;
            },
            field: {
                xtype: 'numberfield'
            }
        }, {
            header: 'Estado',
            width: 180,
            sortable: true,
            dataIndex: 'estado'
        },{
            header: 'Usr_Asignado',
            width: 180,
            sortable: true,
            dataIndex: 'usrAsignado'
        }, ],buttons: [
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ] 
    });
        
        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Pedidos',
                            //layout: 'fit',
                            resizable: true,
                            height: 478,
                            modal: true,
                            closable: false,
                            readOnly: true,
                            autoShow: true,
                            items: (formPanelAsignacionIndividual),
                            
                                    });
         winAsignacionIndividual.show();
    }
}