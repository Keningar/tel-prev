Ext.onReady(function()
{
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

    storeFormaContactoEmpresa = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto_empresa,
                    simpleSortMode: true,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaempresaexternaID: $('#admiempresaexternatype_personaempresaexternaId').val()
                        }
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        if ($('#admiempresaexternatype_personaempresaexternaId').val() != "")
                        {
                            store.getProxy().extraParams.personaempresaexternaID = $('#admiempresaexternatype_personaempresaexternaId').val();
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
            store: storeFormaContactoEmpresa,
            renderTo: Ext.get('lista_formas_contacto_grid'),
            width: 600,
            height: 250,
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
                                        storeFormaContactoEmpresa.removeAt(rowIndex);
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
                                storeFormaContactoEmpresa.insert(0, r);
                            }
                            cellEditing.startEditByPosition({row: 0, column: indice});
                        }
                    }
                ]
        });

    storeFormaContactoEmpresa.load();
    new Ext.TabPanel(
        {
            height: 300,
            widht: 400,
            renderTo: 'my-tabs',
            activeTab: 0,
            plain: true,
            autoRender: true,
            autoShow: true,
            items: [
                {contentEl: 'tab1', title: 'Datos Principales'},
                {contentEl: 'tab2', title: 'Formas de contacto',
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

    var storeCategoria = Ext.create('Ext.data.Store', {
        fields: ['nombre_categoria', 'id_categoria'],
        data: [
            {"nombre_categoria": "Distribuidor", "id_categoria": "DISTRIBUIDOR"},
            {"nombre_categoria": "Partner", "id_categoria": "PARTNER"},
            {"nombre_categoria": "SubDistribuidor", "id_categoria": "SUBDISTRIBUIDOR"}
        ]
    });
        var cmbCategoriaEmpresaExterna = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'left',
        store: storeCategoria,
        id: 'cmbCategoriaEmpresaExterna',
        name: 'cmbCategoriaEmpresaExterna',
        valueField: 'id_categoria',
        displayField: 'nombre_categoria',
        fieldLabel: '* Categoria',
        width: 400,
        mode: 'local',
        renderTo: "divCmbCategoriaEmpresaExterna",
        allowBlank: false,
        blankText:  'Seleccione la categoria por favor',
        listeners: {
            render: function(combobox) {
                combobox.setValue("Distribuidor");
            }
        }
    });
    
        var storeOficina = new Ext.data.Store
        ({
            id:       'storeIdOficina',
            autoLoad: true,
            pageSize: 1000,
            total: 'total',
            proxy:
            {
                type: 'ajax',
                timeout: 900000,
                url : urlGetOficina,
                reader:
                {
                    type: 'json',
                    root: 'objDatos'
                }
            },
            fields:
            [
                {name:'intIdOficina',     mapping:'intIdOficina'},
                {name:'strNombreOficina', mapping:'strNombreOficina'}
            ]
        }); 
    
        var cmbOficinaEmpresaExterna = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'left',
        store: storeOficina,
        id: 'cmbOficinaEmpresaExterna',
        name: 'cmbOficinaEmpresaExterna',
        valueField: 'intIdOficina',
        displayField: 'strNombreOficina',
        fieldLabel: '* Oficina',
        width: 400,
        mode: 'local',
        renderTo: "divCmbOficina",
        allowBlank: false,
        blankText:  'Seleccione la oficina por favor',
    });

    Ext.create('Ext.panel.Panel',
        {
            style: 'padding-top:10px; padding-left: 100px; ',
            border: false,
            buttonAlign: 'center',
            renderTo: 'form_empresa_externa_save',
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
                            if (validarFormasContacto($('#admiempresaexternatype_formas_contacto')))
                            {
                                Ext.getCmp('btnGuardar').disable();
                                Ext.getCmp('btnCancelar').disable();
                                var strCategoria          = null;
                                var strOficina            = null;
                                strCategoria = Ext.getCmp('cmbCategoriaEmpresaExterna').getValue();
                                strOficina = Ext.getCmp('cmbOficinaEmpresaExterna').getValue();
                                connEsperaAccion.request(
                                    {
                                        url: urlGuardarActualizarEmpresaExterna,
                                        method: 'POST',
                                        timeout: 60000,
                                        params:
                                            {
                                                intEmpresaExterna: $('#admiempresaexternatype_admiempresaexternaId').val(),
                                                intPersonaEmpresa: $('#admiempresaexternatype_personaempresaexternaId').val(),
                                                strNombreEmpresa: $('#admiempresaexternatype_nombres').val(),
                                                strRazonSocial: $('#admiempresaexternatype_razonSocial').val(),
                                                datFechaInstitucionY: $('#admiempresaexternatype_fechaNacimiento_year').val(),
                                                datFechaInstitucionM: $('#admiempresaexternatype_fechaNacimiento_month').val(),
                                                datFechaInstitucionD: $('#admiempresaexternatype_fechaNacimiento_day').val(),
                                                strRuc: $('#admiempresaexternatype_identificacionCliente').val(),
                                                strNacionalidad: $('#admiempresaexternatype_nacionalidad').val(),
                                                strDireccion: $('#admiempresaexternatype_direccion').val(),
                                                lstFormasContacto: $('#admiempresaexternatype_formas_contacto').val(),
                                                strCategoria: strCategoria,
                                                strOficina: strOficina
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
                                                if (text.new)
                                                {
                                                    window.location = "" + text.id + "/show";
                                                }
                                                else
                                                {
                                                    document.forms[0].submit();
                                                }
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
                                            Ext.Msg.show({
                                                title: 'Error',
                                                msg: result.statusText,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
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

function validarIdentificacion(identificacion)
{
    var RegExPattern = /^[0-9]{1,13}$/;
    if (!((identificacion.value.match(RegExPattern)) && (identificacion.value != '')))
    {
        identificacion.value = '';
    }
}

function buscarPorIdentificacion(identificacion)
{
    validarIdentificacion(identificacion);
    if (identificacion.value != "")
    {
        connEsperaBuscarAccion.request(
            {
                url: urlBuscarEmpresaExternaPorIdentificacion,
                method: 'post',
                params:
                    {
                        tipoIdentificacion: 'RUC',
                        identificacion: identificacion.value
                    },
                success: function(parResponse)
                {
                    var response = parResponse.responseText;
                    var parsedJSON = eval('(' + response + ')');
                    if (parsedJSON.msg == '')
                    {
                        var results = parsedJSON.empresasExternas;
                        $('#diverrorident').attr('style', 'display:none');
                        $('#diverrorident').html('');
                        if (results !== false)
                        {
                            var empresa = results[0];

                            var empresa_externa_id = (empresa.id ? parseInt(empresa.id * 1) : "");
                            var nombres = (empresa.nombres ? empresa.nombres : "");
                            var razonSocial = (empresa.razonSocial ? empresa.razonSocial : "");
                            var tituloId = (empresa.tituloId ? empresa.tituloId : "");
                            var genero = (empresa.genero ? empresa.genero : "");
                            var estadoCivil = (empresa.estadoCivil ? empresa.estadoCivil : "");
                            var fechaNacimiento_mes = (empresa.fechaNacimiento_mes ? parseInt(empresa.fechaNacimiento_mes * 1) : "");
                            var fechaNacimiento_dia = (empresa.fechaNacimiento_dia ? parseInt(empresa.fechaNacimiento_dia * 1) : "");
                            var fechaNacimiento_anio = (empresa.fechaNacimiento_anio ? parseInt(empresa.fechaNacimiento_anio * 1) : "");
                            var nacionalidad = (empresa.nacionalidad ? empresa.nacionalidad : "");
                            var direccion = (empresa.direccion ? empresa.direccion : "");

                            $('#admiempresaexternatype_personaempresaexternaId').val(empresa_externa_id);
                            $('#admiempresaexternatype_nombres').val(nombres);
                            $('#admiempresaexternatype_razonSocial').val(razonSocial);
                            $('#admiempresaexternatype_tituloId').val(tituloId);
                            $('#admiempresaexternatype_genero').val(genero);
                            $('#admiempresaexternatype_estadoCivil').val(estadoCivil);
                            $('#admiempresaexternatype_fechaNacimiento_month').val(fechaNacimiento_mes);
                            $('#admiempresaexternatype_fechaNacimiento_day').val(fechaNacimiento_dia);
                            $('#admiempresaexternatype_fechaNacimiento_year').val(fechaNacimiento_anio);
                            $('#admiempresaexternatype_nacionalidad').val(nacionalidad);
                            $('#admiempresaexternatype_direccion').val(direccion);

                            storeFormaContactoEmpresa.getProxy().extraParams.personaempresaexternaID = empresa_externa_id;
                            storeFormaContactoEmpresa.load();
                        }
                        else
                        {
                            $('#admiempresaexternatype_personaempresaexternaId').val("");
                            storeFormaContactoEmpresa.removeAll(true);
                            ;
                        }
                    }
                    else
                    {
                        $('#diverrorident').attr('style', '');
                        $('#diverrorident').html(parsedJSON.msg);
                        $('#admiempresaexternatype_identificacionCliente:text').focus();
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
                                    msg: 'Grabando los datos, Por favor espere!!',
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

var connEsperaBuscarAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Buscando datos, Por favor espere!!',
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
