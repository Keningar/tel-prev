Ext.require([
    '*'
]);

Ext.onReady(function()
{
    Ext.create('Ext.panel.Panel',
        {
            style: 'padding-top:10px; padding-left: 100px; ',
            border: false,
            buttonAlign: 'center',
            renderTo: 'form_persona_empleado_update',
            layout:
                {
                    type: 'vbox'
                },
            buttons:
                [
                    {
                        id: 'btnGuardar',
                        name: 'btnGuardar',
                        text: 'Guardar',
                        ui: 'lo-que-sea',
                        cls: 'button-crud',
                        handler: function()
                        {
                            if (validarFormasContacto($('#personaempleadotype_formas_contacto')))
                            {
                                Ext.getCmp('btnGuardar').disable();
                                Ext.getCmp('btnCancelar').disable();
                                connEsperaAccion.request(
                                    {
                                        url: urlActualizarPersonaEmpleado,
                                        method: 'POST',
                                        timeout: 60000,
                                        params:
                                            {
                                                intPersonaEmpresaRol: $('#perEmpRolId').val(),
                                                intPersonaEmpleadoId: $('#personaempleadotype_personaid').val(),
                                                strIdentificacion: $('#personaempleadotype_identificacionCliente').val(),
                                                strTipoIdentificacion: $('#personaempleadotype_tipoIdentificacion').val(),
                                                strNombres: $('#personaempleadotype_nombres').val(),
                                                strApellidos: $('#personaempleadotype_apellidos').val(),
                                                strGenero: $('#personaempleadotype_genero').val(),
                                                strEstadoCivil: $('#personaempleadotype_estadoCivil').val(),
                                                strTitulo: $('#personaempleadotype_tituloId').val(),
                                                strNacionalidad: $('#personaempleadotype_nacionalidad').val(),
                                                strDireccion: $('#personaempleadotype_direccion').val(),
                                                datFechaInstitucionY: $('#personaempleadotype_fechaNacimiento_year').val(),
                                                datFechaInstitucionM: $('#personaempleadotype_fechaNacimiento_month').val(),
                                                datFechaInstitucionD: $('#personaempleadotype_fechaNacimiento_day').val(),
                                                lstFormasContacto: $('#personaempleadotype_formas_contacto').val()
                                            },
                                        success: function(response)
                                        {
                                            var text = Ext.decode(response.responseText);
                                            if (text.estatus)
                                            {
                                                Ext.Msg.show({
                                                    title: 'Información',
                                                    msg: text.msg,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.INFO
                                                });
                                                document.forms[0].submit();
                                            }
                                            else
                                            {
                                                Ext.Msg.show({
                                                    title: 'Error',
                                                    msg: text.msg,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                                Ext.getCmp('btnGuardar').enable();
                                                Ext.getCmp('btnCancelar').enable();
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            Ext.getCmp('btnGuardar').enable();
                                            Ext.getCmp('btnCancelar').enable();
                                        }
                                    });
                            }
                        }
                    },
                    {
                        id: 'btnCancelar',
                        name: 'btnCancelar',
                        text: 'Cancelar',
                        ui: 'lo-que-sea',
                        cls: 'button-crud',
                        handler: function()
                        {
                            Ext.getCmp('btnGuardar').disable();
                            Ext.getCmp('btnCancelar').disable();
                            window.location = urlIndex;
                        }
                    }
                ]
        });


    Ext.define('PersonaFormasContactoModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idPersonaFormaContacto', type: 'int'},
                {name: 'formaContacto'},
                {name: 'valor', type: 'string'}
            ]
        });

    Ext.define('FormasContactoModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'descripcion', type: 'string'}
            ]
        });

    storeFormaContactoPersona = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto_persona,
                    simpleSortMode: true,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaid: ''
                        }
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        if (personaid != "")
                        {
                            store.getProxy().extraParams.personaid = personaid;
                        }
                    }
                }
        });

    storeFormasContacto = Ext.create('Ext.data.Store',
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

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1});

    gridFormasContacto = Ext.create('Ext.grid.Panel',
        {
            store: storeFormaContactoPersona,
            renderTo: Ext.get('lista_formas_contacto_grid'),
            width: 600,
            height: 300,
            title: '',
            plugins: [cellEditing],
            listeners:
                {
                    edit: function(ed, context)
                    {
                        context.record.set('valor', trimAll(context.record.get('valor')));
                    }
                },
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
                                editable: false,
                                lazyRender: true,
                                listClass: 'x-combo-list-small'
                            }
                        )
                    },
                    {
                        text: 'Valor',
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
                                    iconCls: "button-grid-delete",
                                    tooltip: 'Borrar Forma Contacto',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        storeFormaContactoPersona.removeAt(rowIndex);
                                    }
                                }
                            ]
                    }
                ],
            selModel:
                {
                    selType: 'cellmodel'
                },
            tbar:
                [
                    {
                        text: 'Agregar',
                        handler: function()
                        {
                            var boolError = false;
                            var indice = 0;
                            for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
                            {
                                variable = gridFormasContacto.getStore().getAt(i).data;
                                boolError = trimAll(variable['formaContacto']) == '';

                                if (boolError)
                                {
                                    break;
                                }
                                else
                                {
                                    boolError = trimAll(variable['valor']) == '';
                                    if (boolError)
                                    {
                                        indice = 1;
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
                                storeFormaContactoPersona.insert(0, r);
                            }
                            cellEditing.startEditByPosition({row: 0, column: indice});
                        }
                    }
                ]
        });

    storeFormaContactoPersona.load();
    new Ext.TabPanel(
        {
            height: 390,
            renderTo: 'my-tabs',
            activeTab: 0,
            plain: true,
            autoRender: true,
            autoShow: true,
            items: [
                {
                    contentEl: 'tab1',
                    title: 'Datos Principales'
                },
                {
                    contentEl: 'tab2',
                    title: 'Formas de contacto',
                    listeners:
                        {
                            activate: function(tab)
                            {
                                gridFormasContacto.view.refresh();
                            }

                        }
                }
            ]
        });

    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 10,
            border: false,
            buttonAlign: 'center',
            renderTo: 'form_accionesPersonaEmpleado',
            layout:
                {
                    type: 'vbox',
                    align: 'left',
                    pack: 'center'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            items:
                [
                    {
                        xtype: 'button',
                        id: 'btnEliminar',
                        text: 'Eliminar Personal Empleado',
                        ui: 'lo-que-sea',
                        cls: 'button-eliminar',
                        margin: '15 0 0 0',
                        handler: function()
                        {
                            Ext.Msg.confirm('Alerta', 'Se eliminará el Personal Empleado.<br> ¿Desea continuar?', function(btn)
                            {
                                if (btn === 'yes')
                                {
                                    Ext.getCmp('btnEliminar').disable();
                                    connEsperaAccion.request(
                                        {
                                            url: urlEliminarPersonaEmpleado,
                                            method: 'POST',
                                            timeout: 60000,
                                            success: function(response)
                                            {
                                                var text = Ext.decode(response.responseText);
                                                console.log(text);
                                                if (text.estatus)
                                                {
                                                    Ext.Msg.show(
                                                        {
                                                            title: 'Información',
                                                            msg: text.msg,
                                                            buttons: Ext.Msg.OK,
                                                            icon: Ext.MessageBox.INFO
                                                        });
                                                    window.location = urlShow;
                                                }
                                                else
                                                {
                                                    Ext.Msg.show(
                                                        {
                                                            title: 'Error',
                                                            msg: text.msg,
                                                            buttons: Ext.Msg.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                        });
                                                    Ext.getCmp('btnEliminar').enable();
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.show(
                                                    {
                                                        title: 'Error',
                                                        msg: 'Error: ' + result.statusText,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                Ext.getCmp('btnEliminar').enable();
                                            }
                                        });
                                }
                            });
                        }
                    }
                ]
        });

    if ($('#Estado').val() == 'Eliminado')
    {
        Ext.getCmp('btnGuardar').hide();
        Ext.getCmp('btnEliminar').disable();
        Ext.Msg.show(
            {
                title: 'Error',
                msg: 'No se puede editar el Personal Empleado <br>\"' +
                    $('#personaempleadotype_identificacionCliente').val() + '-' +
                    $('#personaempleadotype_apellidos').val() + ' ' +
                    $('#personaempleadotype_nombres').val() +
                    '" <br> porque su estado es [Eliminado].',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
    }

    var permisoEliminacion = $("#ROLE_182-8");
    var boolPermisoEliminacion = (typeof permisoEliminacion === 'undefined') ? false : (permisoEliminacion.val() == 1 ? true : false);

    if (!boolPermisoEliminacion)
    {
        Ext.getCmp('btnEliminar').hide();
    }

});


function validaTelefono(telefono)
{
    var RegExPattern = /^[0-9]{8,10}$/;
    if ((telefono.match(RegExPattern)) && (telefono.value != ''))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validaCorreo(correo)
{
    var RegExPattern = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    if ((correo.match(RegExPattern)) && (correo.value != ''))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validarIdentificacion(identificacion, tipoIdentificacion)
{
    var RegExPattern = null;
    tipoIdentificacion = tipoIdentificacion.toUpperCase();
    if (tipoIdentificacion == 'RUC')
    {
        RegExPattern = /^[0-9]{1,13}$/;
    }
    else if (tipoIdentificacion == 'CED')
    {
        RegExPattern = /^[0-9]{1,10}$/;
    }
    if (RegExPattern != null && !(identificacion.value.match(RegExPattern)))
    {
        identificacion.value = '';
    }
}

function buscarPorIdentificacion(identificacion)
{
    var tipoIdentificacion = $('#personaempleadotype_tipoIdentificacion').val();
    validarIdentificacion(identificacion, tipoIdentificacion);
    if (tipoIdentificacion != "" && identificacion.value != "")
    {
        Ext.Ajax.request(
            {
                url: urlBuscarPersonaEmpleadoPorIdentificacion,
                method: 'post',
                params:
                    {
                        tipoIdentificacion: tipoIdentificacion,
                        identificacion: identificacion.value
                    },
                success: function(parResponse)
                {
                    var response = parResponse.responseText;
                    var parsedJSON = eval('(' + response + ')');
                    if (parsedJSON.msg == '')
                    {
                        var results = parsedJSON.persona;
                        $('#diverrorident').attr('style', 'display:none');
                        $('#diverrorident').html('');
                        if (results !== false)
                        {
                            var resultPersExt = results[0];

                            var persona_empleado_id = (resultPersExt.id ? parseInt(resultPersExt.id * 1) : "");
                            var nombres = (resultPersExt.nombres ? resultPersExt.nombres : "");
                            var apellidos = (resultPersExt.apellidos ? resultPersExt.apellidos : "");
                            var tituloId = (resultPersExt.tituloId ? resultPersExt.tituloId : "");
                            var genero = (resultPersExt.genero ? resultPersExt.genero : "");
                            var estadoCivil = (resultPersExt.estadoCivil ? resultPersExt.estadoCivil : "");
                            var fechaNacimiento_mes = (resultPersExt.fechaNacimiento_mes ? parseInt(resultPersExt.fechaNacimiento_mes * 1) : "");
                            var fechaNacimiento_dia = (resultPersExt.fechaNacimiento_dia ? parseInt(resultPersExt.fechaNacimiento_dia * 1) : "");
                            var fechaNacimiento_anio = (resultPersExt.fechaNacimiento_anio ? parseInt(resultPersExt.fechaNacimiento_anio * 1) : "");
                            var nacionalidad = (resultPersExt.nacionalidad ? resultPersExt.nacionalidad : "");
                            var direccion = (resultPersExt.direccion ? resultPersExt.direccion : "");

                            $('#personaempleadotype_personaid').val(persona_empleado_id);
                            $('#personaempleadotype_nombres').val(nombres);
                            $('#personaempleadotype_apellidos').val(apellidos);
                            $('#personaempleadotype_tituloId').val(tituloId);
                            $('#personaempleadotype_genero').val(genero);
                            $('#personaempleadotype_estadoCivil').val(estadoCivil);
                            $('#personaempleadotype_fechaNacimiento_month').val(fechaNacimiento_mes);
                            $('#personaempleadotype_fechaNacimiento_day').val(fechaNacimiento_dia);
                            $('#personaempleadotype_fechaNacimiento_year').val(fechaNacimiento_anio);
                            $('#personaempleadotype_nacionalidad').val(nacionalidad);
                            $('#personaempleadotype_direccion').val(direccion);

                            storeFormaContactoPersona.getProxy().extraParams.personaid = persona_empleado_id;
                            storeFormaContactoPersona.load();
                        }
                        else
                        {
                            $('#personaempleadotype_personaid').val("");
                            storeFormaContactoPersona.removeAll(true);
                        }
                    }
                    else
                    {
                        $('#diverrorident').attr('style', '');
                        $('#diverrorident').html(parsedJSON.msg);
                        $('#personaempleadotype_identificacionCliente:text').focus();
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: result.statusText,
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            });

    }
    else
    {
        if (tipoIdentificacion == "")
        {
            Ext.Msg.show({
                title: 'Error',
                msg: 'Por favor seleccione el tipo de Identificación e ingrese la Identificación',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    }
}

function validarFormasContacto(campo)
{
    var array_data = new Array();
    var variable = '';
    for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
    {
        variable = gridFormasContacto.getStore().getAt(i).data;
        for (var key in variable)
        {
            var valor = variable[key];
            array_data.push(valor);
        }
    }
    $(campo).val(array_data);

    if (($(campo).val() == '0,,') || ($(campo).val() == ''))
    {
        Ext.Msg.show({
            title: 'Error',
            msg: 'No hay formas de contacto ingresadas',
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR
        });
        $(campo).val('');
        return false;
    }
    else
    {
        return validarDataFormasContacto();
    }
}

function validarDataFormasContacto()
{
    var array_telefonos = new Array();
    var array_correos = new Array();
    var i = 0;
    var variable = '';
    var formaContacto = '';
    var hayTelefono = false;
    var hayCorreo = false;
    var esTelefono = false;
    var esCorreo = false;
    var telefonosOk = false;
    var correosOk = false;

    for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
    {
        variable = gridFormasContacto.getStore().getAt(i).data;
        esTelefono = false;
        esCorreo = false;
        for (var key in variable)
        {
            var valor = variable[key];
            if (key == 'formaContacto')
            {
                formaContacto = variable[key];
                formaContacto = formaContacto.toUpperCase();
                if (formaContacto.match(/^TELEFONO.*$/))
                {
                    hayTelefono = true;
                    esTelefono = true;
                }
                if (formaContacto.match(/^CORREO.*$/))
                {
                    hayCorreo = true;
                    esCorreo = true;
                }
            }
            if (esTelefono)
            {
                array_telefonos.push(valor);
            }
            if (esCorreo)
            {
                array_correos.push(valor);
            }
        }
    }
    if (hayCorreo)
    {
        for (i = 0; i < array_correos.length; i++)
        {
            if (i % 2 != 0)
            {
                correosOk = validaCorreo(array_correos[i]);
            }
        }
        if (correosOk)
        {
            if (hayTelefono)
            {
                for (i = 0; i < array_telefonos.length; i++)
                {
                    if (i % 2 != 0)
                    {
                        telefonosOk = validaTelefono(array_telefonos[i]);
                    }
                }
                if (telefonosOk)
                {
                    return true;
                }
                else
                {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: 'Hay números de teléfono que tienen errores, por favor corregir.',
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                    return false;
                }
            }
            else
            {
                return true;
            }
        }
        else {
            Ext.Msg.show({
                title: 'Error',
                msg: 'Hay correos que tienen errores, por favor corregir.',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
            return false;
        }
    }
    else
    {
        Ext.Msg.show({
            title: 'Error',
            msg: 'Debe Ingresar al menos 1 Correo',
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
}

function ajustarTexto(componente)
{
    componente.value = componente.value.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
}

function cambiarAMayusculas(componente)
{
    componente.onkeyup = function()
    {
        componente.value = componente.value.toUpperCase();
    };
}

var connEsperaAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Actualizando los datos, Por favor espere!!',
                                    progressText: 'Saving...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }


    });


function trimAll(texto)
{
    return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ').trim();
}

function validarEspeciales(evento)
{
    key = evento.keyCode || evento.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz0123456789";
    especiales = "8-37-39-46";
    tecla_especial = false;
    
    for (var i in especiales)
    {
        if (key == especiales[i])
        {
            tecla_especial = true;
            break;
        }
    }
    if (letras.indexOf(tecla) == -1 && !tecla_especial) 
    {
        return false;
    }
}