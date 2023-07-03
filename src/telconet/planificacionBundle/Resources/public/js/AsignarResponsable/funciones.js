/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var winMenuAsignacion;
var winAsignacion;
var winAsignacionIndividual;
var winRecursoDeRed;
var gridIpPublica;
var gridIpMonitoreo;
var tareasJS;
var cuadrillaAsignada = "S";


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
    var boolError = true;
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
            boolError = false;
            Ext.Msg.alert('Alerta', 'No existe el id Detalle Solicitud');
        }
    } else
    {
        boolError = false;
        Ext.Msg.alert('Alerta', 'No hay opcion escogida');
    }

    if (boolError)
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

function asignarResponsableIndividual(rec, origen, id)
{
    var param = '';
    var boolError = true;
    var boolErrorTecnico = false;
    var idPerTecnico = 0;

    if (origen == "local")
    {
        id = rec.data.id_factibilidad;
        param = rec.data.id_factibilidad;
        if (prefijoEmpresa == "TN" && rec.data.descripcionSolicitud == "Solicitud Planificacion")
        {
            idPerTecnico = Ext.getCmp('cmb_tecnico').value;
            if (!idPerTecnico)
            {
                boolErrorTecnico = true;
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
            }//FIN FOR

            if (!boolErrorTareas)
            {
                Ext.Msg.confirm('Alerta', 'Se asignará el responsable. Desea continuar?', function(btn) {
                    if (btn == 'yes') {
                        connAsignarResponsable.request({
                            url: "../../planificar/asignar_responsable/asignarIndividualmenteAjax",
                            method: 'post',
                            timeout:600000,
                            params: { origen                : origen, 
                                      id                    : id, 
                                      param                 : param, 
                                      paramResponsables     : paramResponsables,
                                      idPerTecnico          : idPerTecnico
                            },
                            success: function(response) {
                                var text = response.responseText;
                                if (text == "Se asignaron la(s) Tarea(s) Correctamente.")
                                {
                                    cierraVentanaAsignacionIndividual();
                                    Ext.Msg.alert('Mensaje', text, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                            // showRecursoDeRed(rec, id,"asignarResponsable");
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
                Ext.Msg.alert('Alerta', 'Por favor seleccione todos los combos de los responsables de cada tarea.<br><br>' +
                    'En esta lista menciona los combos que no han sido seleccionados:<br><br>' + mensajeError);

            }
        }
    }
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
                            value: rec.get("producto"),
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
/************************************************************************ */
function showAsignacionIndividual(rec, origen, id, panelAsignados)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";

    if (!winAsignacionIndividual)
    {
        var id_servicio = rec.get("id_servicio");
        var id_factibilidad = rec.get("id_factibilidad");
        var tipo_solicitud = rec.get("descripcionSolicitud");
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
//            width: 600,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
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
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }
        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
//            width:700,
//            height:750,
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [
                {
                    xtype: 'panel',
                    border: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
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
                                    value: rec.get("producto"),
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
                                itemTercerizadora
                            ]
                        }
                    ]
                },
                Vacio1
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        asignarResponsableIndividual(rec, origen, id);
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

        if (prefijoEmpresa == "TN")
        {
            if (tipo_solicitud == "Solicitud Planificacion")
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
                                    origen: 'IngenieroL2',
                                    departamento: 'IPCCL2'
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



                combo_tecnicos = new Ext.form.ComboBox({
                    id: 'cmb_tecnico',
                    name: 'cmb_tecnico',
                    fieldLabel: "Ingeniero IPCCL2",
                    anchor: '100%',
                    queryMode: 'remote',
                    emptyText: 'Seleccione Ingeniero IPCCL2',
                    width: 350,
                    store: storeTecnicos,
                    displayField: 'nombre_tecnico',
                    valueField: 'id_tecnico',
                    layout: 'anchor',
                    disabled: false
                });
            }
        }

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
                                text: tareasJS[i]["nombreTarea"]
                            });

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
                                width: 600,
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
                                width: 400,
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

                            formPanelAsignacionIndividual.items.add(hidden_tarea);
                            formPanelAsignacionIndividual.items.add(text_tarea);
                            formPanelAsignacionIndividual.items.add(RadiosTiposResponsable);
                            formPanelAsignacionIndividual.items.add(combo_empleados);
                            formPanelAsignacionIndividual.items.add(combo_cuadrillas);
                            formPanelAsignacionIndividual.items.add(combo_empresas_externas);
                            formPanelAsignacionIndividual.items.add(formPanel);
                            formPanelAsignacionIndividual.items.add(Vacio);
                            if (prefijoEmpresa == "TN" && tipo_solicitud == "Solicitud Planificacion")
                            {
                                formPanelAsignacionIndividual.items.add(combo_tecnicos);
                                formPanelAsignacionIndividual.items.add(Vacio);
                            }
                            formPanelAsignacionIndividual.doLayout();


                            Ext.getCmp('cmb_empleado_' + i).setVisible(true);
                            Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
                            Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
                            Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);

                        }

                        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Formulario Asignacion Individual',
                            //            width: 740,
                            //            height:660,
                            //            minHeight: 380,
                            layout: 'fit',
                            resizable: false,
                            modal: true,
                            closable: false,
                            items: [formPanelAsignacionIndividual]
                        });

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



        /*
         if(origen == "otro2" && panelAsignados)
         {
         winAsignacionIndividual = Ext.widget('window', {
         title: 'Formulario Asignacion Individual',
         width: 740,
         height:800,
         minHeight: 800,
         layout: 'fit',
         resizable: false,
         modal: true,
         closabled: false,
         items: [formPanelAsignacionIndividual]
         });
         }
         else
         {
         winAsignacionIndividual = Ext.widget('window', {
         title: 'Formulario Asignacion Individual',
         width: 740,
         height:660,
         minHeight: 380,
         layout: 'fit',
         resizable: false,
         modal: true,
         closabled: false,
         items: [formPanelAsignacionIndividual]
         });

         }*/
    }

}

function cierraVentanaAsignacionIndividual() {
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}