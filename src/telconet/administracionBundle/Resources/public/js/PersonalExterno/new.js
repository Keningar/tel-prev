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
            renderTo: 'form_personal_externo_create',
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
                            if (grabar($('#personalexternotype_formas_contacto')))
                            {
                                Ext.getCmp('btnGuardar').disable();
                                Ext.getCmp('btnCancelar').disable();
                                
                                var intIdDepartamentoEmpresaSession = null;
                                var intIdArea          = null;
                                var strPimerNombre     = '';
                                var strSegundoNombre   = '';
                                var strPrimerApellido  = '';
                                var strSegundoApellido = '';
                                var strOficina         = null;
                                
                                intIdDepartamentoEmpresaSession = Ext.getCmp('cmbDepartamentoEmpresaSession').value;
                                intIdArea          = Ext.getCmp('cmbAreaEmpresaSession').value;
                                strPimerNombre     = Ext.getCmp('txtPrimerNombre').value;
                                strSegundoNombre   = Ext.getCmp('txtSegundoNombre').value;
                                strPrimerApellido  = Ext.getCmp('txtPrimerApellido').value;
                                strSegundoApellido = Ext.getCmp('txtSegundoApellido').value;
                                strOficina =         Ext.getCmp('cmbOficinaEmpresaExterna').getValue();
                                
                                connEsperaGrabarAccion.request(
                                    {
                                        url: urlGuardarPersonalExterno,
                                        method: 'POST',
                                        timeout: 60000,
                                        params:
                                            {
                                                intPersonalExternoId: $('#personalexternotype_personaid').val(),
                                                strIdentificacion: $('#personalexternotype_identificacionCliente').val(),
                                                strTipoIdentificacion: $('#personalexternotype_tipoIdentificacion').val(),
                                                strPrimerNombre: strPimerNombre,
                                                strSegundoNombre: strSegundoNombre,
                                                strPrimerApellido: strPrimerApellido,
                                                strSegundoApellido:strSegundoApellido,
                                                strGenero: $('#personalexternotype_genero').val(),
                                                strEstadoCivil: $('#personalexternotype_estadoCivil').val(),
                                                strTitulo: $('#personalexternotype_tituloId').val(),
                                                strNacionalidad: $('#personalexternotype_nacionalidad').val(),
                                                strDireccion: $('#personalexternotype_direccion').val(),
                                                intEmpresaExterna: $('#personalexternotype_empresaExterna').val(),
                                                datFechaInstitucionY: $('#personalexternotype_fechaNacimiento_year').val(),
                                                datFechaInstitucionM: $('#personalexternotype_fechaNacimiento_month').val(),
                                                datFechaInstitucionD: $('#personalexternotype_fechaNacimiento_day').val(),
                                                lstFormasContacto: $('#personalexternotype_formas_contacto').val(),
                                                intIdDepartamentoEmpresaSession: intIdDepartamentoEmpresaSession,
                                                intIdArea: intIdArea,
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
                                                window.location = "" + text.id + "/show";
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
    
    var storeAreaEmpresaSession = new Ext.data.Store
        ({
            autoLoad: true,
            pageSize: 1000,
            total: 'total',
            proxy:
            {
                type: 'ajax',
                timeout: 900000,
                url : strUrlGetAreaByEmpresa,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name:'id_area',     mapping:'id_area'},
                {name:'nombre_area', mapping:'nombre_area'}
            ]
        }); 

    var cmbAreaEmpresaSession = new Ext.form.ComboBox
    ({
        id:'cmbAreaEmpresaSession',
        name: 'cmbAreaEmpresaSession',
        displayField:'nombre_area',
        valueField: 'id_area',
        store: storeAreaEmpresaSession,
        loadingText: 'Buscando ...',
        fieldLabel: '* Area',
        queryMode: "remote",
        allowBlank: false,
        blankText:  'Seleccione el area por favor',
        renderTo: "divCmbAreaEmpresaSession",
        width:400,
        listeners: {
            select: function(records) {
                 storeDepartamentoEmpresaSession.load({params: {
                    intIdArea: cmbAreaEmpresaSession.getValue()
                }});
            },
            change: function() { cmbDepartamentosEmpresaSession.clearValue();}
        },
    });

        
    var storeDepartamentoEmpresaSession = new Ext.data.Store
        ({
            autoLoad: true,
            pageSize: 1000,
            total: 'total',
            proxy:
            {
                type: 'ajax',
                timeout: 900000,
                url : strUrlGetDepartamentosByEmpresaYArea,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name:'id_departamento',     mapping:'id_departamento'},
                {name:'nombre_departamento', mapping:'nombre_departamento'}
            ]
        }); 

    var cmbDepartamentosEmpresaSession = new Ext.form.ComboBox
        ({
            id:'cmbDepartamentoEmpresaSession',
            name: 'cmbDepartamentoEmpresaSession',
            displayField:'nombre_departamento',
            valueField: 'id_departamento',
            store: storeDepartamentoEmpresaSession,
            loadingText: 'Buscando ...',
            fieldLabel: '* Depto. ',
            queryMode: "remote",
            allowBlank: false,
            blankText:  'Seleccione el departamento por favor',
            renderTo: "divCmbDepartamentoEmpresaSession",
            width:400
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
    
    var txtPrimerNombre = new Ext.form.Text
        ({
            id:         'txtPrimerNombre',
            name:       'txtPrimerNombre',
            fieldLabel: '* P. Nombre',
            renderTo:   "divTxtPrimerNombre",
            allowBlank: false,
            blankText:  'Ingrese Primer Nombre por favor',
            width:      400
        });
    var txtSegundoNombre = new Ext.form.Text
        ({
            id:         'txtSegundoNombre',
            name:       'txtSegundoNombre',
            fieldLabel: '* S. Nombre',
            renderTo:   "divTxtSegundoNombre",
            allowBlank: false,
            blankText:  'Ingrese Segundo Nombre por favor',
            width:      400
        });
    var txtPrimerApellido = new Ext.form.Text
        ({
            id:         'txtPrimerApellido',
            name:       'txtPrimerApellido',
            fieldLabel: '* Apellido P.',
            renderTo:   "divTxtPrimerApellido",
            allowBlank: false,
            blankText:  'Ingrese Apellido Paterno por favor',
            width:      400
        });
        
    var txtSegundoApellido = new Ext.form.Text
        ({
            id:         'txtSegundoApellido',
            name:       'txtSegundoApellido',
            fieldLabel: '* Apellido M.',
            renderTo:   "divTxtSegundoApellido",
            allowBlank: false,
            blankText:  'Ingrese Apellido Materno por favor',
            width:      400
        });    
     
        
    getEmpresasExternas(0);

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
                        editor: new Ext.form.field.ComboBox({
                            typeAhead: true,
                            triggerAction: 'all',
                            selectOnTab: true,
                            id: 'cbxFormaContacto',
                            name: 'formaContacto',
                            valueField: 'descripcion',
                            displayField: 'descripcion',
                            store: storeFormasContacto,
                            editable: false,
                            lazyRender: true,
                            listClass: 'x-combo-list-small'
                        })
                    },
                    {
                        text: 'Valor',
                        dataIndex: 'valor',
                        width: 400,
                        align: 'right',
                        editor:
                            {
                                xtype: 'textfield',
                                width: '80%',
                                id: 'txtValor',
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
            items:
                [
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
    var tipoIdentificacion = $('#personalexternotype_tipoIdentificacion').val();
    validarIdentificacion(identificacion, tipoIdentificacion);
    if (tipoIdentificacion != "" && identificacion.value != "")
    {
        connEsperaAccion.request(
            {
                url: urlBuscarPersonalExternoPorIdentificacion,
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
                            var personal = results[0];

                            var personal_externo_id = (personal.id ? parseInt(personal.id * 1) : "");
                            var tituloId = (personal.tituloId ? personal.tituloId : "");
                            var genero = (personal.genero ? personal.genero : "");
                            var estadoCivil = (personal.estadoCivil ? personal.estadoCivil : "");
                            var fechaNacimiento_mes = (personal.fechaNacimiento_mes ? parseInt(personal.fechaNacimiento_mes * 1) : "");
                            var fechaNacimiento_dia = (personal.fechaNacimiento_dia ? parseInt(personal.fechaNacimiento_dia * 1) : "");
                            var fechaNacimiento_anio = (personal.fechaNacimiento_anio ? parseInt(personal.fechaNacimiento_anio * 1) : "");
                            var nacionalidad = (personal.nacionalidad ? personal.nacionalidad : "");
                            var direccion = (personal.direccion ? personal.direccion : "");
                            var strPimerNombre     = (personal.strPimerNombre ? personal.strPimerNombre : "");
                            var strSegundoNombre   = (personal.strSegundoNombre ? personal.strSegundoNombre : "");
                            var strPrimerApellido  = (personal.strPrimerApellido ? personal.strPrimerApellido : "");
                            var strSegundoApellido = (personal.strSegundoApellido ? personal.strSegundoApellido : "");
                            var strOficina         = (personal.intOficinaId ? personal.intOficinaId : "");
                            var intIdDepartamento  = (personal.intDepartamentoId ? personal.intDepartamentoId : "");
                            
                            $('#personalexternotype_personaid').val(personal_externo_id);
                            Ext.getCmp('txtPrimerNombre').setValue(strPimerNombre);
                            Ext.getCmp('txtSegundoNombre').setValue(strSegundoNombre);
                            Ext.getCmp('txtPrimerApellido').setValue(strPrimerApellido);
                            Ext.getCmp('txtSegundoApellido').setValue(strSegundoApellido);
                            Ext.getCmp('cmbOficinaEmpresaExterna').setValue(strOficina);
                            Ext.getCmp('cmbDepartamentoEmpresaSession').setValue(intIdDepartamento);
                            $('#personalexternotype_tituloId').val(tituloId);
                            $('#personalexternotype_genero').val(genero);
                            $('#personalexternotype_estadoCivil').val(estadoCivil);
                            $('#personalexternotype_fechaNacimiento_month').val(fechaNacimiento_mes);
                            $('#personalexternotype_fechaNacimiento_day').val(fechaNacimiento_dia);
                            $('#personalexternotype_fechaNacimiento_year').val(fechaNacimiento_anio);
                            $('#personalexternotype_nacionalidad').val(nacionalidad);
                            $('#personalexternotype_direccion').val(direccion);

                            getEmpresasExternas(personal.personaEmpresaRolId);
                            storeFormaContactoPersona.getProxy().extraParams.personaid = personal_externo_id;
                            storeFormaContactoPersona.load();
                            
                            if (personal.rol === 'Personal Externo' || personal.rol === 'Empleado' )
                            {
                                Ext.getCmp('txtPrimerNombre').setReadOnly(true);
                                Ext.getCmp('txtSegundoNombre').setReadOnly(true);
                                Ext.getCmp('txtPrimerApellido').setReadOnly(true);
                                Ext.getCmp('txtSegundoApellido').setReadOnly(true);
                                $('#personalexternotype_direccion').attr('readonly', true);
                                
                                $('#personalexternotype_nacionalidad').attr("disabled", true); 
                                $('#personalexternotype_genero').attr("disabled", true); 
                                $('#personalexternotype_estadoCivil').attr("disabled", true); 
                                $('#personalexternotype_fechaNacimiento_month').attr("disabled", true); 
                                $('#personalexternotype_fechaNacimiento_day').attr("disabled", true); 
                                $('#personalexternotype_fechaNacimiento_year').attr("disabled", true);
                                $('#personalexternotype_tituloId').attr("disabled", true);
                                
                                Ext.getCmp('cmbAreaEmpresaSession').setDisabled(true);
                                Ext.getCmp('cmbDepartamentoEmpresaSession').setDisabled(true);
                                Ext.getCmp('cmbOficinaEmpresaExterna').setDisabled(true);
                                
                                Ext.getCmp('btnGuardar').disable();
                                if (personal.rol === 'Personal Externo')
                                {
                                    alert('El numero de identificacion corresponde aun Personal Externo Activo.');                                      
                                }
                                if (personal.rol === 'Empleado')
                                {
                                    alert('El número de identificación corresponde a un empleado Activo.');                                      
                                }
                                                           
                            }
                            if (personal.rol === 'Otros')
                            {
                                Ext.getCmp('txtPrimerNombre').setReadOnly(true);
                                Ext.getCmp('txtSegundoNombre').setReadOnly(true);
                                Ext.getCmp('txtPrimerApellido').setReadOnly(true);
                                Ext.getCmp('txtSegundoApellido').setReadOnly(true);
                                $('#personalexternotype_direccion').attr('readonly', true);
                                
                                $('#personalexternotype_nacionalidad').attr("disabled", true); 
                                $('#personalexternotype_genero').attr("disabled", true); 
                                $('#personalexternotype_estadoCivil').attr("disabled", true); 
                                $('#personalexternotype_fechaNacimiento_month').attr("disabled", true); 
                                $('#personalexternotype_fechaNacimiento_day').attr("disabled", true); 
                                $('#personalexternotype_fechaNacimiento_year').attr("disabled", true);
                                $('#personalexternotype_tituloId').attr("disabled", true);
                                
                                Ext.getCmp('cmbAreaEmpresaSession').setDisabled(false);
                                Ext.getCmp('cmbDepartamentoEmpresaSession').setDisabled(false);
                                Ext.getCmp('cmbOficinaEmpresaExterna').setDisabled(false);
                                
                                Ext.getCmp('btnGuardar').enable();
                                
                            }
                            if (personal.rol === 'Nuevo')
                            {
                                Ext.getCmp('txtPrimerNombre').setReadOnly(false);
                                Ext.getCmp('txtSegundoNombre').setReadOnly(false);
                                Ext.getCmp('txtPrimerApellido').setReadOnly(false);
                                Ext.getCmp('txtSegundoApellido').setReadOnly(false);
                                $('#personalexternotype_direccion').attr('readonly', false);
                                
                                $('#personalexternotype_nacionalidad').attr("disabled", false); 
                                $('#personalexternotype_genero').attr("disabled", false); 
                                $('#personalexternotype_estadoCivil').attr("disabled", false); 
                                $('#personalexternotype_fechaNacimiento_month').attr("disabled", false); 
                                $('#personalexternotype_fechaNacimiento_day').attr("disabled", false); 
                                $('#personalexternotype_fechaNacimiento_year').attr("disabled", false);
                                $('#personalexternotype_tituloId').attr("disabled", false);
                                
                                Ext.getCmp('cmbAreaEmpresaSession').setDisabled(false);
                                Ext.getCmp('cmbDepartamentoEmpresaSession').setDisabled(false);
                                Ext.getCmp('cmbOficinaEmpresaExterna').setDisabled(false);
                                
                                Ext.getCmp('btnGuardar').enable();
                            
                                
                            }
                            
                            
                        }
                        else
                        {
                            
                            $('#personalexternotype_personaid').val("");
                            storeFormaContactoPersona.removeAll(true);

                        }
                    }
                    else
                    {
                        $('#diverrorident').attr('style', '');
                        $('#diverrorident').html(parsedJSON.msg);
                        $('#personalexternotype_identificacionCliente:text').focus();
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
                                    msg: 'Buscando Personal Externo, Por favor espere!!',
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

var connEsperaGrabarAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Guardando datos, Por favor espere!!',
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

var connEsperaEmpresaExternaAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Buscando Empresas Externas, Por favor espere!!',
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

function grabar(campo)
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

function trimAll(texto)
{
    return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
}

function cambiarAMayusculas(componente)
{
    componente.onkeyup = function()
    {
        componente.value = componente.value.toUpperCase();
    };
}

function getEmpresasExternas(personaEmpresaRolId)
{
    connEsperaEmpresaExternaAccion.request(
        {
            type: "POST",
            url: urlGetEmpresasExternas,
            params:
                {
                    personaEmpresaRolId: personaEmpresaRolId,
                    nuevo: 1
                },
            success: function(response)
            {
                var resp = Ext.decode(response.responseText);
                if (resp.msg == 'ok')
                {
                    document.getElementById("personalexternotype_empresaExterna").innerHTML = resp.div;
                    if (resp.bloqueado)
                    {
                        $("#personalexternotype_empresaExterna").attr('disabled', true);
                        $("#personalexternotype_empresaExterna").attr('style', '-moz-appearance: none; text-indent: 0.01px; text-overflow:"" ');
                    }
                    else
                    {
                        $("#personalexternotype_empresaExterna").attr('disabled', false);
                        $("#personalexternotype_empresaExterna").attr('style', '');
                    }
                }
                else
                {
                    document.getElementById("personalexternotype_empresaExterna").innerHTML = resp.msg;
                }
            }
        });
}