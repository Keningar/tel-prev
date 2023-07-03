/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var entidadSolicitudSeguimiento = new Seguimiento();

var winMenuAsignacion;
var winAsignacion;
var winAsignacionIndividual;
var winRecursoDeRed;
var gridIpPublica;
var gridIpMonitoreo;
var tareasJS;
var cuadrillaAsignada = "S";
var seleccionaHal     = false;
var esHal             = 'N';
var tipoHal;
var itemSelectHorario=null;


Ext.override(Ext.data.Connection, {

        timeout:45000

});



let containerHorario = [];

let itemsHal=[];

let jsonOriginal= {
    "cronograma": [ ]};
  
  
  jsonOriginal.cronograma.forEach(function(itemHeader){
    itemHeader.forEach(function(item){
      item.isSelected=false;
    });
  });
  
  let jsonBuild = JSON.parse(JSON.stringify(jsonOriginal))
  

















    
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





let requestAJax = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Consultando información!!',
                    progressText: 'Espere...',
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
                Ext.Msg.alert('Alerta', 'No hay parametros parseados.');
            }

        } else
        {
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
        Vacio1 = Ext.create('Ext.Component', {
            html: '',
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
        Vacio1 = Ext.create('Ext.Component', {
            html: '',
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



function showProgramar(rec, origen, opcion)
{
    winAsignacionIndividual = "";
    
    if (!winAsignacionIndividual)
    {
        let id_servicio     = rec.get("id_servicio");
        let id_factibilidad = rec.get("id_factibilidad");

        let boolEsHousing   = false; //(rec.get('nombreTecnico') === 'HOUSING' || rec.get('nombreTecnico') === 'HOSTING');
        
        //******** html vacio...
        Vacio1 = Ext.create('Ext.Component', {
            html: '',
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
                                                style: "padding: 10px; text-align:justify; margin-left: auto; margin-right: auto; margin-bottom: 10px; margin-top: 10px",
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
                            //******* id del departamento
                            var intIdDepartamento = tareasJS[i]["idDepartamento"]
                            //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
                            var strIniHtml = '';
                            strIniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                            '&nbsp;&nbsp;' +
                            '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="cuadrilla" name="tipoResponsable_' + i + '">&nbsp;Cuadrilla' +
                            '&nbsp;&nbsp;' +
                            '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="empresaExterna" name="tipoResponsable_' + i + '">&nbsp;Contratista' +
                            '';
                            
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
                            combo_tecnicos.setVisible(false);

                            
                            if(rec.get("muestraIngL2") == "N")
                            {
                                combo_tecnicos.setVisible(false);
                            }    

                            Ext.getCmp('cmb_empleado_' + i).setVisible(true);
                            Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
                            Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
                            Ext.getCmp('panelLiderCuadrilla_' + i).setVisible(false);

                            container.doLayout();

                        }
                        formPanelHalPrincipal = crearFormPanelHal('planificar', rec, origen, opcion, false);

                        var tabs = new Ext.TabPanel({
                            xtype     :'tabpanel',
                            activeTab : 0,
                            autoScroll: false,
                            layoutOnTabChange: true,
                            items: [formPanelHalPrincipal]
                        });

                        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Formulario Asignacion Individual',
                            layout: 'fit',
                            resizable: false,
                            modal: true,
                            closable: false,
                            items: [tabs]
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




function opcionesHal(tipo, idDetalleSolicitud)
{

    tipoHal=tipo;
    seleccionaHal=true;
    itemSelectHorario=null
    Ext.getCmp('notificacionHal').setValue(null);

    if (tipo == 2)
    {
     
        formPanelHalPrincipal.doLayout();
        Ext.getCmp("cmbMotivosNoPlanificacion").setValue(0);
        Ext.getCmp("cmbMotivosNoPlanificacion").setReadOnly(true);
        Ext.getCmp("checkPlanificado").setValue(false);     
        Ext.getCmp("idSinHorario").setVisible(false);             
        getCronogramaComercial(idDetalleSolicitud);
    }else if(tipo==3){
        jsonOriginal= {
            "cronograma": [ ]};
        const jsonCopy = JSON.parse(JSON.stringify(jsonOriginal))
        actualizarHorario(jsonCopy);
        Ext.getCmp("idSinHorario").setVisible(true);   
        Ext.getCmp("checkPlanificado").setValue(false);        
        Ext.getCmp("cmbMotivosNoPlanificacion").setValue(0);
        Ext.getCmp("cmbMotivosNoPlanificacion").setReadOnly(true);
    }
    
}



function crearFormPanelHal(tipoAccion, rec, origen, opcion, boolPermisoOpu)
{
    
    
    var componenteVacio = Ext.create('Ext.Component', {
        html: '',
        width: 200,
        padding: 8,
        layout: 'anchor',
        style: {color: '#000000'}
    });
   
    cmbMotivosRePlanificacion = componenteVacio;
    cmbTecnicos               = componenteVacio;
    cmbMotivosNoPlanificacion = Ext.create('Ext.data.comboMotivosNoPlanificacion', {
        id: 'cmbMotivosNoPlanificacion',
        name: 'cmbMotivosNoPlanificacion',
        fieldLabel: '* Motivo',
        readOnly: true,
        labelStyle: "color:red;"});



    
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
  
    let radioButtonSugerencia='<input type="radio" onchange="opcionesHal(2,'+rec.get("id_factibilidad")+');"'+
    'value="halSugiere" name="radioCuadrilla" id="radio_a">&nbsp;Sugerencias&nbsp;&nbsp;&nbsp;&nbsp;';

    let radioButtonSinHorario='<input type="radio" onchange="opcionesHal(3,'+rec.get("id_factibilidad")+');"'+
    'value="halSugiere" name="radioCuadrilla" id="radio_b">&nbsp;Sin Horario&nbsp;&nbsp;&nbsp;&nbsp;</div>';

    let radbuttonHal = '<div align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
        radioButtonSugerencia+radioButtonSinHorario+
        '</div>';


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
        minValue : strFechaMinima,
        maxValue : strFechaMaxima,
        value: strFechaMinima,
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


    containerHorario = [];
    llenarHorario();



    itemsHal=[];


    itemsHal= [
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
            ]
        },
        {
            xtype: 'fieldset',
            title: 'Motivo de No Planificación',
            id:   'idSinHorario',
            hidden: true,
            items:
            [
                {
                   xtype: 'checkbox',
                   boxLabel: 'No se asignan horarios',
                   width: 150,
                   cls: 'red',
                   name : 'checkPlanificado',
                   id: 'checkPlanificado',
                   listeners:
                   {
                    
                       change: function(checkPlanificado){
                           if (checkPlanificado.value){
                               Ext.getCmp("cmbMotivosNoPlanificacion").setReadOnly(false);
                           }
                           else{
                            Ext.getCmp("cmbMotivosNoPlanificacion").setReadOnly(true);
                            Ext.getCmp("cmbMotivosNoPlanificacion").setValue(null);
                           }
                          
                       }
                   }
                },
                cmbMotivosNoPlanificacion

            ]
        },
        
        {
            id:'idHorarioComercial',
            hidden:true,
            html:containerHorario,
        },
        panelInfoAdicionalSolCoordinar
    ];



    formPanelHalDatosHal = Ext.create('Ext.form.Panel',
        {
            xtype: 'panel',
            id:'formPanelHalDatosHal',
            border: false,
            layout: {type: 'vbox', align: 'stretch'},
            items: itemsHal
           
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
                }
            },
            {
                text: 'Cancelar',
                handler: function()
                {
                    seleccionaHal = false;
                     containerHorario = [];
                     itemsHal=[];
                     jsonOriginal= {
                        "cronograma": [ ]};
                   
                    if (tipoAccion === 'planificar' )
                    {
                        winAsignacionIndividual.destroy();
                    }
                    else if(tipoAccion === 'replanificar' )
                    {
                        winRePlanificar.destroy();
                    }

                  
                }
            }
        ]
    });

    return formPanelHalPrincipal;
}



function llenarHorario(){
   

    let styleCard=`style="text-align: center; width:150px; height: 40px; background-color: white; line-height: 40px; border: 1px solid #bacdd8; border-radius: 5px; margin-top:7px;  margin-right: 7px; margin-left: 7px;  margin-bottom:7px; "`;

    let styleContainerHorario=`style="text-align: center; border-radius: 5px; `;

    let styleContainerGeneral=`style="display: flex;  align-items: flex-start; width: auto; height: auto;"`;

    let styleScroll=`style="width: auto; padding:10px  height: auto; overflow-x: scroll; overflow-y: scroll; background-color: white;
    border: 1px solid black;"`;


  

    containerHorario.push(`<div  `+styleScroll+`">`);
    containerHorario.push(`<div id="container-general" `+styleContainerGeneral+`">`);

    jsonBuild.cronograma.forEach(function(itemHeader){ 


        let cabecera = [];

        
        cabecera.push("<div class=\"vertical\">");
        itemHeader.forEach(function(item){
         let disponible="";
         cabecera.push("<div "+styleCard+" >");
         if(item.isHeader){
            cabecera.push("<div "+styleContainerHorario+" background-color: #dddddd \">");

           var valor=item.descripcion;

           cabecera.push("<div\ style=\"font-size: 8px  color: white;   \";> <b\> "+valor.replace(" ", '\n')+"  </b\> </div>");
         }else{
           if(item.isDisponible){
            disponible="Disponible";
           let indiceExterno=jsonBuild.cronograma.indexOf(itemHeader);
           let indiceInterno=itemHeader.indexOf(item);
           if(item.isSelected){
             cabecera.push("<div "+styleContainerHorario+" background-color: #C5E1A5\"  >");
           }else{
             cabecera.push("<div  "+styleContainerHorario+" background-color: #FFFFFF\"   onClick=\"clickItemCronograma(" + indiceExterno +","+indiceInterno+")\"   >");
           }
          }else{
            cabecera.push("<div  "+styleContainerHorario+" background-color: #ffbfaa\">");
             disponible="No Disponible";
          } 
         }
         cabecera.push("<h4\">"+disponible+"</h4>");
        ///General
         cabecera.push("</div>");
        ///Card
         cabecera.push("</div>"); 
       });
       ///Vertical
       cabecera.push("</div>");



       containerHorario.push(cabecera.join(""));
     });


     containerHorario.push(' </div>');
     containerHorario.push(' </div>');



}
   



 function clickItemCronograma(indexExterno,indiceInterno){
    const jsonCopy = JSON.parse(JSON.stringify(jsonOriginal))
    jsonCopy.cronograma[indexExterno][indiceInterno].isSelected=true;

    itemSelectHorario=  jsonCopy.cronograma[indexExterno][indiceInterno].horario;

    
    actualizarHorario(jsonCopy);
}
   

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
function grabarProgramarHal(rec, prefijoEmpresa, origen, opcion)
{
    let param            = rec.data.id_factibilidad;
    let boolError        = false;
    let boolErrorTecnico = false;
    let idPerTecnico     = 0;
    esHal                = 'S';
    let id               = rec.data.id_factibilidad;


    if (!seleccionaHal)
    {
                    Ext.Msg.alert("Alerta","Debe escoger una opción de Hal...!!");
                    return;
    }     

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


        
            let txtObservacion ="";
            let id_factibilidad = rec.get("id_factibilidad");
            let atenderAntes = "N";
            let idSugerencia;
            let fechaVigencia;
            let fecha;
            let hora;
            let horaFin;
            let strMensaje="";

            if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
            {
                boolError = true;
            }
           
            if(boolError){
                Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos');
                return;
            }
            atenderAntes = "S";

            if (tipoHal == 2)
            {
               strMensaje = "Se asignará el responsable. Desea continuar?";
               if(itemSelectHorario==null){
                Ext.Msg.alert("Alerta","Debe seleccionar un horario...!!");
                return; 
               }
               idSugerencia  = itemSelectHorario.idSugerencia;
               fecha         = itemSelectHorario.fecha;
               hora          = itemSelectHorario.horaIni;
               fechaVigencia = itemSelectHorario.fechaVigencia;
               horaFin       = itemSelectHorario.horaVigencia;
            }else{
                strMensaje = "No se asignará responsable . Desea continuar?";
                if( Ext.getCmp("cmbMotivosNoPlanificacion").getValue()===0)
                {
                    Ext.Msg.alert("Alerta","Debe escoger un motivo...!!");
                    return;
                }
            }




                
                Ext.Msg.confirm('Alerta', strMensaje, function(btn) {
                    if (btn == 'yes') {
                        connAsignarResponsable.request({
                            url: "../../planificar/coordinarComercial/programar",
                            method: 'post',
                            timeout: 450000,
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
                                esHal         : 'S',
                                fechaProgramacion : fecha,
                                ho_inicio         : hora,
                                ho_fin            : horaFin,
                                idMotivo          : Ext.getCmp("cmbMotivosNoPlanificacion").getValue()
                            },
                            success: function(response) {


                                const text        = response.responseText;
                                const intPosicion = text.indexOf("Correctamente");
                                if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                    text == "Se coordinó la solicitud" || 
                                    text == "No se asignará responsable." ||intPosicion !== -1)
                                {
                                    Ext.Msg.alert('Mensaje', text, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });

                                    cierraVentanaAsignacionIndividual();
                                  
                                } else {

                                    const text        = response.responseText;


                                    Ext.Msg.show({
                                                title:'Mensaje del sistema',
                                                msg: text,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.Msg.ERROR
                                             });
        
                                }
                            },
                            failure: function(result) {

                                Ext.Msg.alert('Alerta', result);
                            }
                        });
                    }
                });
            
            
        
    }


    
}



function getCronogramaComercial(idDetalleSolicitud)
    {
        requestAJax.request({
            url: "../../planificar/coordinarComercial/getCronogramaComercial",
            method: 'post',
            timeout: 450000,
            params: {
                idDetalleSolicitud        : idDetalleSolicitud
                
            },
            success: function(response) {

               if(response.status===200)
               {
                    const jsonResponse = JSON.parse(response.responseText);
                    if(jsonResponse.status==="OK"){
                        jsonOriginal=jsonResponse.data;
                        Ext.getCmp('notificacionHal').setValue(null);

                    }else{

                         jsonOriginal= {
                            "cronograma": [ ]};
                            const mensaje = '<b style="color:red";>'+jsonResponse.message+'</b>';
                            Ext.getCmp('notificacionHal').setValue(mensaje);

                    }                    
                    const jsonCopy = JSON.parse(JSON.stringify(jsonOriginal))
                    actualizarHorario(jsonCopy);

               }else{
                Ext.getCmp('notificacionHal').setValue("Ocurrió un error al realizar la petición");
               }


            },
            failure: function(error) {
                Ext.getCmp('notificacionHal').setValue(""+error);

            }
        });
    }



    function actualizarHorario( jsonCopy){
        jsonBuild= JSON.parse(JSON.stringify(jsonCopy));    
        containerHorario= [];
        llenarHorario();        
        Ext.getCmp('formPanelHalDatosHal').remove(3);
        Ext.getCmp('formPanelHalDatosHal').insert(
            3,
            {
                id:'idHorarioComercial',
                hidden:true,
                html:containerHorario,
            },); 
        if(jsonBuild.cronograma.length===0){
            Ext.getCmp("idHorarioComercial").setVisible(false);    
            itemSelectHorario=null;         
        }else{
            Ext.getCmp("idHorarioComercial").setVisible(true);             
        }


    }


