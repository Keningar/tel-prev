
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

}

function validarFormulario()
{
    var asignaciones = gridAsignaciones.getStore().getCount();

    var personaId = Ext.getCmp('cmbPersona').getValue();
    if (personaId == "" || !personaId) {
        personaId = 0;
    }

    if (personaId != 0)
    {
        Ext.get('cmbPersonaId').dom.value = personaId;
    }

    if (asignaciones == 0)
    {
        alert("No se han registrado las asignaciones");
        return false;
    }
    else if (personaId == 0 || personaId == "")
    {
        alert("No ha escojido la persona");
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
            //alert('Ya existe una asignacion similar');
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
                tipoAsignacion: 'Normal'
            }
        }
    });
    combo_empleados = new Ext.form.ComboBox({
        id: 'cmbPersona',
        name: 'cmbPersona',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 400,
        emptyText: 'Escoja una opcion',
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        renderTo: 'div_cmbPersona'
    });


    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_perfil', mapping: 'id_perfil'},
            {name: 'nombre_perfil', mapping: 'nombre_perfil'}
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

    grid = Ext.create('Ext.grid.Panel', {
        width: 370,
        height: 510,
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

    /////////// Asiganaciones /////////////////////
    sm2 = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    })

    gridAsignaciones = Ext.create('Ext.grid.Panel', {
        width: 370,
        height: 510,
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
        title: 'Perfiles asignados al Usuario',
        frame: true,
        renderTo: 'gridAsignaciones'
    });


    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
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
});
/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */
function buscar(tipo) {
    storePerfiles.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    storePerfiles.load();
}

function limpiar(tipo) {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    storePerfiles.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    storePerfiles.load();
}