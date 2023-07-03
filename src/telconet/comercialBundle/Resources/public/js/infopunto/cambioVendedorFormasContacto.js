Ext.require([
    '*'
]);

Ext.onReady(function()
{
    Ext.define('PersonaFormasContactoModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idPersonaFormaContacto', type: 'int'},
                    {name: 'formaContacto'},
                    {name: 'valor', type: 'string'}
                ]
        });

    Ext.define('FormasContactoModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id', type: 'int'},
                    {name: 'descripcion', type: 'string'}
                ]
        });

    storePersonaFormasContacto = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto_persona,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaid: ''
                        },
                    simpleSortMode: true
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        store.getProxy().extraParams.personaid = personaid;
                    }
                }
        });

    var storeFormasContacto = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'FormasContactoModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto,
                    reader:
                        {
                            type: 'json',
                            root: 'formasContacto'
                        }
                }
        });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
        {
            clicksToEdit: 2
        });

    gridFormasContacto = Ext.create('Ext.grid.Panel',
        {
            store: storePersonaFormasContacto,
            columns:
                [
                    {
                        text: 'Forma Contacto',
                        header: 'Forma Contacto',
                        dataIndex: 'formaContacto',
                        width: 150,
                        editor: new Ext.form.field.ComboBox(
                            {
                                typeAhead: true,
                                triggerAction: 'all',
                                selectOnTab: true,
                                id: 'id',
                                name: 'formaContacto',
                                valueField: 'descripcion',
                                displayField: 'descripcion',
                                store: storeFormasContacto,
                                lazyRender: true,
                                listClass: 'x-combo-list-small'
                            })
                    },
                    {
                        text: 'Valor',
                        //header: 'Valor',
                        dataIndex: 'valor',
                        width: 400,
                        align: 'right',
                        editor:
                            {
                                width: '80%',
                                xtype: 'textfield',
                                allowBlank: false
                            }
                    },
                    {
                        xtype: 'actioncolumn',
                        width: 45,
                        sortable: false,
                        items:
                            [
                                {
                                    getClass: function()
                                    {
                                        strEliminarFormaContacto = 'button-grid-invisible';
                                        var permiso = $("#ROLE_281-3097");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (boolPermiso)
                                        {
                                            strEliminarFormaContacto = 'button-grid-delete';
                                        }
                                        return strEliminarFormaContacto;
                                    },
                                    tooltip: 'Eliminar Bin',
                                    style: 'cursor:pointer',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        storePersonaFormasContacto.removeAt(rowIndex);
                                    }
                                }
                            ]
                    }
                ],
            selModel:
                {
                    selType: 'cellmodel'
                },
            renderTo: Ext.get('lista_formas_contacto_grid'),
            width: 600,
            height: 300,
            title: '',
            tbar:
                [
                    {
                        text: 'Agregar',
                        handler: function()
                        {
                            var boolError = false;
                            var columna = 0;
                            var fila = 0;
                            for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
                            {
                                variable = gridFormasContacto.getStore().getAt(i).data;
                                boolError = trimAll(variable['formaContacto']) == '';

                                if (boolError)
                                {
                                    fila = i;
                                    break;
                                }
                                else
                                {
                                    boolError = trimAll(variable['valor']) == '';
                                    if (boolError)
                                    {
                                        columna = 1;
                                        fila = i;
                                        break;
                                    }
                                }
                            }
                            if (!boolError)
                            {
                                var r = Ext.create('PersonaFormasContactoModel',
                                    {
                                        idPersonaFormaContacto: '',
                                        formaContacto: '',
                                        valor: ''
                                    });
                                storePersonaFormasContacto.insert(0, r);
                            }
                            cellEditing.startEditByPosition({row: fila, column: columna});
                        }
                    }
                ],
            plugins: [cellEditing]
        });

    // manually trigger the data store load
    storePersonaFormasContacto.load();
});

function grabarFormasContacto(campo)
{
    var array_data = new Array();
    var variable = '';
    var valoresVacios = false;
    for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
    {
        variable = gridFormasContacto.getStore().getAt(i).data;
        for (var key in variable)
        {
            var valor = variable[key];
            if (key == 'valor' && valor == '')
            {
                valoresVacios = true;
            } else
            {
                array_data.push(valor);
            }
        }
    }
    $(campo).val(array_data);
    if (($(campo).val() == '0,,') || ($(campo).val() == ''))
    {
        alert('No hay formas de contacto aun ingresadas.');
        $(campo).val('');
    }
    else
    {
        if (valoresVacios == true)
        {
            alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');
            $(campo).val('');
        }
    }
}

/**
 * Permite validar las formas de contactos.
 *
 * @version 1.00
 * 
 * Se llama a validación de formas de contactos centralizada.
 *
 * @author Héctor Ortega <haortega@telconet.ec>
 * @version 1.01, 29/11/2016
 */
function validaFormasContacto(){
    return Utils.validaFormasContacto(gridFormasContacto);
}

function validacionesForm()
{
    if (!validaFormasContacto())
    {
        return false;
    }

    if ($('#infopuntoextratype_sectorId').val() == '')
    {
        mostrarDiv('div_errorsector');
        return false;
    }
    if ($('#infopuntoextratype_loginVendedor').val() == '')
    {
        mostrarDiv('div_errorvendedor');
        return false;
    }
    return true;
}

function trimAll(texto)
{
    return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ').trim();
}
