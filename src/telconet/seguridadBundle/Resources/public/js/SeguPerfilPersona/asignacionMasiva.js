
function obtenerAsignaciones()
{
    Ext.get('perfiles_asignados').dom.value = "";
    var array_asignaciones = new Object();
    array_asignaciones['total'] = gridAsignaciones.getStore().getCount();
    array_asignaciones['asignaciones'] = new Array();
    var array_data = new Array();
    for (var i = 0; i < gridAsignaciones.getStore().getCount(); i++)
    {

        array_data.push(gridAsignaciones.getStore().getAt(i).data);
    }

    array_asignaciones['asignaciones'] = array_data;
    Ext.get('perfiles_asignados').dom.value = Ext.JSON.encode(array_asignaciones);


    Ext.get('empleados_seleccionados').dom.value = "";
    var array_empleados = new Object();
    array_empleados['total'] = gridEmpleadosSeleccionados.getStore().getCount();
    array_empleados['empleados'] = new Array();
    var array_data_emp = new Array();
    for (var i = 0; i < gridEmpleadosSeleccionados.getStore().getCount(); i++)
    {

        array_data_emp.push(gridEmpleadosSeleccionados.getStore().getAt(i).data);
    }

    array_empleados['empleados'] = array_data_emp;
    Ext.get('empleados_seleccionados').dom.value = Ext.JSON.encode(array_empleados);

}

function validarFormulario()
{
    var asignaciones = gridAsignaciones.getStore().getCount();
    var empleados = gridEmpleadosSeleccionados.getStore().getCount();

    if (asignaciones == 0)
    {
        alert("No se han registrado las asignaciones");
        return false;
    }
    else if (empleados == 0)
    {
        alert("No han seleccionado Empleados");
        return false;
    }

    obtenerAsignaciones();
    return true;
}
function ingresarAsignacion()
{
    if (sm.getSelection().length > 0)
    {
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            var r = Ext.create('Asignacion', {
                perfil_id: '',
                id_perfil: sm.getSelection()[i].get('id_perfil'),
                nombre_perfil: sm.getSelection()[i].get('nombre_perfil')
            });
            if (!existeAsignacion(r, gridAsignaciones))
                storeAsignaciones.insert(0, r);
        }
    }
    else
    {
        alert('Seleccione por lo menos una accion de la lista');
    }
}

function ingresarEmpleado()
{
    if (smEmp.getSelection().length > 0)
    {
        for (var i = 0; i < smEmp.getSelection().length; ++i)
        {
            var r = Ext.create('Empleado', {
                id_empleado: smEmp.getSelection()[i].get('id_empleado'),
                nombre_empleado: smEmp.getSelection()[i].get('nombre_empleado')
            });
            if (!existeEmpleado(r, gridEmpleadosSeleccionados))
                storeEmpleadosSeleccionados.insert(0, r);
        }
    }
    else
    {
        alert('Seleccione por lo menos una accion de la lista');
    }
}

function existeAsignacion(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var perfil = grid.getStore().getAt(i).get('id_perfil');
        if (perfil == myRecord.get('id_perfil'))
        {
            existe = true;
            break;
        }
    }
    return existe;
}

function existeEmpleado(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var perfil = grid.getStore().getAt(i).get('id_empleado');
        if (perfil == myRecord.get('id_empleado'))
        {
            existe = true;
            break;
        }
    }
    return existe;
}

function eliminarSeleccion(datosSelect)
{
    var xRowSelMod = datosSelect.getSelectionModel().getSelection();
    for (var i = 0; i < xRowSelMod.length; i++)
    {
        var RowSel = xRowSelMod[i];
        datosSelect.getStore().remove(RowSel);
    }
}


Ext.onReady(function() {

    // **************** EMPLEADOS ******************
    Ext.define('EmpleadosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_empleado', type: 'int'},
            {name: 'nombre_empleado', type: 'string'}
        ]
    });

    storeEmpleados = Ext.create('Ext.data.Store', {
        model: 'EmpleadosList',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: 'getEmpleados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                tipoAsignacion: 'Masiva'
            }
        }
    });


    storeEmpleadosSeleccionados = new Ext.data.Store({
        total: 'total',
        fields:
            [
                {name: 'id_empleado', mapping: 'id_empleado'},
                {name: 'nombre_empleado', mapping: 'nombre_empleado'}
            ]
    });

    var storeEmpresas = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'valor'],
        data:
            [{
                    "opcion": "MEGADATOS",
                    "valor": "MD"
                }, {
                    "opcion": "TRANSTELCO",
                    "valor": "TTCO"
                },
                {
                    "opcion": "TELCONET",
                    "valor": "TN"
                }
            ]
    });

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: 'getCiudadesPorEmpresa',
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
            url: 'getDepartamentosPorEmpresaYCiudad',
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

    function presentarCiudades(empresa) {

        storeCiudades.proxy.extraParams = {empresa: empresa};
        storeCiudades.load();

    }

    function presentarDepartamentosPorCiudad(id_canton, empresa) {

        storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
        storeDepartamentosCiudad.load();

    }

    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_perfil', mapping: 'id_perfil'},
            {name: 'nombre_perfil', mapping: 'nombre_perfil'}
        ]
    });

    Ext.define('Empleado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_empleado', mapping: 'id_empleado'},
            {name: 'nombre_empleado', mapping: 'nombre_empleado'}
        ]
    });

    storeAsignaciones = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: 'gridAsignaciones',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'asignaciones'
            }
        },
        fields:
            [
                {name: 'id_perfil', mapping: 'id_perfil'},
                {name: 'nombre_perfil', mapping: 'nombre_perfil'}
            ]
    });


    storePerfiles = new Ext.data.Store({
        pageSize: 20,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: 'gridPerfiles',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_perfil', mapping: 'id_perfil'},
                {name: 'nombre_perfil', mapping: 'nombre_perfil'}
            ],
        autoLoad: true
    });

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    smEmp = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    grid = Ext.create('Ext.grid.Panel', {
        width: 370,
        height: 310,
        store: storePerfiles,
        loadMask: true,
        selModel: sm,
        iconCls: 'icon-grid',
        // grid columns
        columns: [
            {
                header: 'PerfilId',
                dataIndex: 'id_perfil',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Perfil',
                dataIndex: 'nombre_perfil',
                width: 343,
                sortable: true
            }
        ],
        title: 'Perfiles',
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePerfiles,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridPerfiles'
    });

    gridEmpleados = Ext.create('Ext.grid.Panel', {
        width: 370,
        height: 310,
        store: storeEmpleados,
        loadMask: true,
        selModel: smEmp,
        iconCls: 'icon-grid',
        // grid columns
        columns: [
            {
                header: 'EmpId',
                dataIndex: 'id_empleado',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Empleado',
                dataIndex: 'nombre_empleado',
                width: 335,
                sortable: true
            }
        ],
        title: 'Empleados',
        renderTo: 'gridEmpleados'
    });


    /////////// Asiganaciones /////////////////////
    sm2 = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    })

    /////////// Empleados /////////////////////
    sm2Emp = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridEmpleadosSeleccionados.down('#removeEmpleadoButton').setDisabled(selections.length == 0);
            }
        }
    })

    gridAsignaciones = Ext.create('Ext.grid.Panel', {
        width: 370,
        height: 350,
        store: storeAsignaciones,
        loadMask: true,
        selModel: sm2,
        iconCls: 'icon-grid',
        // grid columns
        columns: [
            {
                header: 'PerfilId',
                dataIndex: 'id_perfil',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Perfil',
                dataIndex: 'nombre_perfil',
                width: 343,
                sortable: true
            }
        ],
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el perfil seleccionado',
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridAsignaciones);
                        }
                    }]
            }],
        title: 'Perfiles seleccionados',
        frame: true,
        renderTo: 'gridAsignaciones'
    });

    gridEmpleadosSeleccionados = Ext.create('Ext.grid.Panel', {
        width: 370,
        height: 350,
        store: storeEmpleadosSeleccionados,
        loadMask: true,
        selModel: sm2Emp,
        iconCls: 'icon-grid',
        // grid columns
        columns: [
            {
                header: 'EmpleadoId',
                dataIndex: 'id_empleado',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Empleado',
                dataIndex: 'nombre_empleado',
                width: 343,
                sortable: true
            }
        ],
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeEmpleadoButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el empleado seleccionado',
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridEmpleadosSeleccionados);
                        }
                    }]
            }],
        title: 'Empleados seleccionados',
        frame: true,
        renderTo: 'gridEmpleadosSeleccionados'
    });


    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 370,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }
        ],
        items: [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px'
            }
        ],
        renderTo: 'filtroPerfiles'
    });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelEmpleados = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 370,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarEmpleado();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiarEmpleado();
                }
            }
        ],
        items: [
            
            {
                xtype: 'textfield',
                id: 'txtNombreEmpleados',
                fieldLabel: 'Nombre',
                value: '',
                labelWidth: '7',
                width: '100%',
                
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Empresa Asignado:',
                id: 'sltEmpresa',
                name: 'sltEmpresa',
                store: storeEmpresas,
                displayField: 'opcion',
                valueField: 'valor',
                queryMode: "remote",
                emptyText: '',
                width: '100%',
                listeners: {
                    select: function(combo) {

                        Ext.getCmp('comboCiudad').reset();
                        Ext.getCmp('comboDepartamento').reset();

                        Ext.getCmp('comboCiudad').setDisabled(false);
                        Ext.getCmp('comboDepartamento').setDisabled(true);

                        presentarCiudades(combo.getValue());
                    }
                },
                forceSelection: true
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Ciudad Asignado',
                id: 'comboCiudad',
                name: 'comboCiudad',
                store: storeCiudades,
                displayField: 'nombre_canton',
                valueField: 'id_canton',
                queryMode: "remote",
                emptyText: '',
                width: '100%',
                disabled: true,
                listeners: {
                    select: function(combo) {
                        Ext.getCmp('comboDepartamento').reset();

                        Ext.getCmp('comboDepartamento').setDisabled(false);

                        empresa = Ext.getCmp('sltEmpresa').getValue();

                        presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                    }
                },
                forceSelection: true
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Departamento Asignado',
                id: 'comboDepartamento',
                name: 'comboDepartamento',
                store: storeDepartamentosCiudad,
                displayField: 'nombre_departamento',
                valueField: 'id_departamento',
                queryMode: "remote",
                emptyText: '',
                width: '100%',
                disabled: true,
                forceSelection: true
            }
        ],
        renderTo: 'filtroEmpleados'
    });
});
/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */
function buscar(tipo) {
    storePerfiles.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    storePerfiles.load();
}

function buscarEmpleado(tipo) {
    storeEmpleados.getProxy().extraParams.nombre = Ext.getCmp('txtNombreEmpleados').value;
    storeEmpleados.getProxy().extraParams.empresa = Ext.getCmp('sltEmpresa').value;
    storeEmpleados.getProxy().extraParams.ciudad = Ext.getCmp('comboCiudad').value;
    storeEmpleados.getProxy().extraParams.departamento = Ext.getCmp('comboDepartamento').value;
    storeEmpleados.getProxy().extraParams.tipoAsignacion = "Masiva";
    storeEmpleados.load();
}


function limpiar(tipo) {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    storePerfiles.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    storePerfiles.load();
}

function limpiarEmpleado() {
    Ext.getCmp('txtNombreEmpleados').value = "";
    Ext.getCmp('txtNombreEmpleados').setRawValue("");
    Ext.getCmp('sltEmpresa').value = "";
    Ext.getCmp('sltEmpresa').setRawValue("");
    Ext.getCmp('comboCiudad').value = "";
    Ext.getCmp('comboCiudad').setRawValue("");
    Ext.getCmp('comboDepartamento').value = "";
    Ext.getCmp('comboDepartamento').setRawValue("");

    storeEmpleados.getProxy().extraParams.nombre         = Ext.getCmp('txtNombreEmpleados').value;
    storeEmpleados.getProxy().extraParams.empresa        = Ext.getCmp('sltEmpresa').value;
    storeEmpleados.getProxy().extraParams.ciudad         = Ext.getCmp('comboCiudad').value;
    storeEmpleados.getProxy().extraParams.departamento   = Ext.getCmp('comboDepartamento').value;
    storeEmpleados.getProxy().extraParams.tipoAsignacion = "Masiva";
    storeEmpleados.load();
}