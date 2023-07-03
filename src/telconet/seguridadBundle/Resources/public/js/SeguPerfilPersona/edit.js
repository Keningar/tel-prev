
var connEsperaAccion = new Ext.data.Connection({
	listeners: {
		'beforerequest': {
			fn: function (con, opt) {						
				Ext.MessageBox.show({
				   msg: 'Grabando los datos, Por favor espere!!',
				   progressText: 'Saving...',
				   width:300,
				   wait:true,
				   waitConfig: {interval:200}
				});
				//Ext.get(document.body).mask('Loading...');
			},
			scope: this
		},
		'requestcomplete': {
			fn: function (con, res, opt) {
				Ext.MessageBox.hide();
				//Ext.get(document.body).unmask();
			},
			scope: this
		},
		'requestexception': {
			fn: function (con, res, opt) {
				Ext.MessageBox.hide();
				//Ext.get(document.body).unmask();
			},
			scope: this
		}
	}
});

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

    if (asignaciones == 0)
    {
        alert("No se han registrado las asignaciones");
        return false;
    }
    obtenerAsignaciones();
    return true;
}

function ingresarAsignacion()
{

    var param = '';
    if (sm.getSelection().length > 0)
    {
        Ext.Msg.confirm('Alerta', 'Se asignaran los perfiles. Desea continuar?', function(btn) {
            if (btn == 'yes') {
                for (var i = 0; i < sm.getSelection().length; ++i)
                {
                    param = param + sm.getSelection()[i].get('id_perfil');
                    if (i < (sm.getSelection().length - 1))
                    {
                        param = param + '|';
                    }
                }
                
                connEsperaAccion.request
                ({
                    url: urlAsignarPerfilPersona,
                    method: 'post',
                    params: 
                    {
                        personaId: personaIdentificador,
                        perfilesId: param,
                        intIdEmpresa: intIdEmpresa,
                    },
                    success: function(response)
                    {
							var text = response.responseText;
                            Ext.Msg.alert('Mensaje ',text);
							storeAsignaciones.load();
					},
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ', 'Se presentaron errores al asignar los perfiles, favor notificar a sistemas.');
                        
                    }
                });
            }
        });
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
    var param = '';
    Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function(btn) {
        if (btn == 'yes') {
            var xRowSelMod = datosSelect.getSelectionModel().getSelection();
            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];
                param = param + RowSel.get('id_perfil');
                if(i < (xRowSelMod.length -1))
                {
                    param = param + '|';
                }
            }
            
                connEsperaAccion.request({
                    url: urlDeletePerfilPersona,
                    method: 'post',
                    params: {personaId: personaIdentificador ,perfilesId: param},
                    success: function(response)
                    {
							var text = response.responseText;
							storeAsignaciones.load();
					},
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ', 'Se presentaron errores al eliminar, favor notificar a sistemas.');
                        
                    }
                });
               
            
        }
    });

}


Ext.onReady(function() {

    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_perfil', mapping: 'id_perfil'},
            {name: 'nombre_perfil', mapping: 'nombre_perfil'}
        ]
    });

    storeAsignaciones = new Ext.data.Store({
        pageSize: 18,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGridAsignaciones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'asignaciones'
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
        autoLoad: true,
    });

    storePerfiles = new Ext.data.Store({
        pageSize: 20,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGridPerfiles,
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
            displayMsg: 'Desde {0} - {1} de {2}',
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
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeAsignaciones,
            displayInfo: true,
            displayMsg: 'Desde {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
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
                    buscar('perfiles');
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar('perfiles');
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
                style: 'margin: 5px',
                listeners: {
                    specialkey: function (field, event) {
                        if (event.getKey() == event.ENTER && field.inputEl.dom.value) {
                            buscar('perfiles');
                        }
                    }
                }
            }
        ],
        renderTo: 'filtroPerfiles'
    });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelAsignaciones = Ext.create('Ext.panel.Panel', {
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
                    buscar('asignaciones');
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar('asignaciones');
                }
            }
        ],
        items: [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombreAsignacion',
                fieldLabel: 'Nombre',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px',
                listeners: {
                    specialkey: function (field, event) {
                        if (event.getKey() == event.ENTER && field.inputEl.dom.value) {
                            buscar('asignaciones');
                        }
                    }
                }
            }
        ],
        renderTo: 'filtroPerfilesAsignacion'
    });

});


/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */
function buscar(tipo) {
    if (tipo == 'perfiles')
    {
        storePerfiles.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
        storePerfiles.load();
    }
    else
    {
        storeAsignaciones.getProxy().extraParams.nombre = Ext.getCmp('txtNombreAsignacion').value;
        storeAsignaciones.load();
    }
}
function limpiar(tipo) {
    if (tipo == 'perfiles')
    {
        Ext.getCmp('txtNombre').value = "";
        Ext.getCmp('txtNombre').setRawValue("");

        storePerfiles.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
        storePerfiles.load();
    }
    else
    {
        Ext.getCmp('txtNombreAsignacion').value = "";
        Ext.getCmp('txtNombreAsignacion').setRawValue("");

        storeAsignaciones.getProxy().extraParams.nombre = Ext.getCmp('txtNombreAsignacion').value;
        storeAsignaciones.load();
    }
}