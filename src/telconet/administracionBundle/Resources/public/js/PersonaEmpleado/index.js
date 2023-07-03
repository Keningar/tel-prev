/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var idDepartamento     = $('#idDepartamentoSesion').val();
    var nombreDepartamento = $('#nombreDepartamentoSesion').val();
    var codigoEmpresa      = $('#codigoEmpresa').val();

    var permiso = $("#ROLE_171-7957");
    var boolPermiso = (permiso.val() == 1);

    if(boolPermiso || codigoEmpresa == "18")
    {
        idDepartamento = "Todos";
    }

    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[
            {name:'id_persona', mapping:'id_persona'},
            {name:'login', mapping:'login'},
            {name:'id_persona_empresa_rol', mapping:'id_persona_empresa_rol'},
            {name:'id_empresa', mapping:'id_empresa'},
            {name:'tipo_identificacion', mapping:'tipo_identificacion'},
            {name:'identificacion', mapping:'identificacion'},
            {name:'nombres', mapping:'nombres'},
            {name:'apellidos', mapping:'apellidos'},
            {name:'direccion', mapping:'direccion'},
            {name:'id_departamento', mapping:'id_departamento'},
            {name:'nombre_departamento', mapping:'nombre_departamento'},
            {name:'id_canton', mapping:'id_canton'},
            {name:'nombre_canton', mapping:'nombre_canton'},
            {name:'action1', mapping:'action1'},
            {name:'action2', mapping:'action2'},
            {name:'action3', mapping:'action3'},
            {name:'action4', mapping:'action4'}
		]
       //idProperty: 'id_area'
    });
    
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombres: '',
                apellidos: '',
                identificacion: '',
                departamento: idDepartamento,
                canton: 'Todos'
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        height: 365,
        store: store,
        viewConfig: {
            emptyText: 'No hay datos para mostrar',
			enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' }
                ]}
        ], 
        columns:[
                {
                  id: 'id_persona_empresa_rol',
                  header: 'IdPersonaEmpresaRol',
                  dataIndex: 'id_persona_empresa_rol',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_persona',
                  header: 'IdPersona',
                  dataIndex: 'id_persona',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_empresa',
                  header: 'IdEmpresa',
                  dataIndex: 'id_empresa',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'tipo_identificacion',
                  header: 'Tipo Ident.',
                  dataIndex: 'tipo_identificacion',
                  width: 80,
                  sortable: true
                },
                {
                  id: 'identificacion',
                  header: 'Identificacion',
                  dataIndex: 'identificacion',
                  width: 80,
                  sortable: true
                },
                {
                  id: 'nombres',
                  header: 'Nombres',
                  dataIndex: 'nombres',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'apellidos',
                  header: 'Apellidos',
                  dataIndex: 'apellidos',
                  width: 180,
                  sortable: true
                },
                {
                  id: 'direccion',
                  header: 'Direccion',
                  dataIndex: 'direccion',
                  width: 180,
                  sortable: true
                },
                {
                  id: 'id_departamento',
                  header: 'IdDepartamento',
                  dataIndex: 'id_departamento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_departamento',
                  header: 'Nombre Departamento',
                  dataIndex: 'nombre_departamento',
                  width: 180,
                  sortable: true
                },
                {
                  id: 'id_canton',
                  header: 'IdCanton',
                  dataIndex: 'id_canton',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_canton',
                  header: 'Nombre Cantón',
                  dataIndex: 'nombre_canton',
                  width: 150,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 150,
                    items: [
                            {
                                getClass: function(v, meta, rec) 
                                {
                                    var permiso = $("#ROLE_171-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }

                                    if (rec.get('action1') == "icon-invisible") 
                                        this.items[0].tooltip = '';
                                    else 
                                        this.items[0].tooltip = 'Ver Empleado';

                                    return rec.get('action1');
                                },
                                tooltip: 'Ver Empleado',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec = store.getAt(rowIndex);
									
                                    var permiso = $("#ROLE_171-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																
                                    if(rec.get('action1')!="icon-invisible")
                                        window.location = rec.get('id_persona_empresa_rol')+"/show";
                                    else
                                        Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) 
                                {
                                    var permiso = $("#ROLE_171-4");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                                    if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }

                                    if (rec.get('action2') == "icon-invisible") 
                                        this.items[1].tooltip = '';
                                    else 
                                        this.items[1].tooltip = 'Editar Empleado';

                                    return rec.get('action2');
                                },
                                tooltip: 'Editar Empleado',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec = store.getAt(rowIndex);
                                    var permiso = $("#ROLE_171-4");

                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }

                                    if(rec.get('action2')!="icon-invisible")
                                        window.location = rec.get('id_persona_empresa_rol')+"/edit";
                                    else
                                        Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var permiso = $("#ROLE_171-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }
                                    var permiso = $("#ROLE_171-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }

                                    if (rec.get('action3') == "icon-invisible")
                                    {
                                        this.items[2].tooltip = '';
                                    }
                                    else
                                    {
                                        this.items[2].tooltip = 'Eliminar Empleado';
                                    }

                                    return rec.get('action3');
                                },
                                tooltip: 'Eliminar Empleado',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_171-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }

                                    if (rec.get('action3') != "icon-invisible")
                                    {
                                        Ext.Msg.confirm('Alerta', 'Se eliminará el registro. Desea continuar?', function(btn)
                                        {
                                            if (btn == 'yes')
                                            {
                                                Ext.Ajax.request(
                                                {
                                                    url: urlDeleteAjax,
                                                    method: 'post',
                                                    params: {param: rec.get('id_persona_empresa_rol')},
                                                    success: function(response)
                                                    {
                                                    var text = response.responseText;
                                                    Ext.Msg.show({
                                                        title: 'Información',
                                                        msg: text,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.INFO
                                                    });
                                                    store.load();
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
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta acción',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                    
                                }
                            },
                            {
                                getClass: function(v, meta, rec) 
                                {
                                    var strClassButton = 'button-grid-pdf';
                                    var permiso = $("#ROLE_171-3997");
                                    var boolPermiso = (permiso.val() == 1);							
                                    if(!boolPermiso){ strClassButton = ""; }

                                    if (strClassButton == "") 
                                    {
                                        strClassButton        = '';
                                        this.items[3].tooltip = '';
                                    }  
                                    else 
                                    {

                                        this.items[3].tooltip = 'Ver Archivos Digitales';
                                    }
                                    return strClassButton;
                                },
                                tooltip: 'Ver Archivos Digitales',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec = store.getAt(rowIndex);
                                    var strClassButton = 'button-grid-pdf';
                                    var strUrlVerDocumentosTransporte = rec.get('strUrlShowDocumentosTransporte');

                                    var permiso = $("#ROLE_171-3997");
                                    var boolPermiso = (permiso.val() == 1);
                                    if(!boolPermiso){ strClassButton = ""; }

                                    if(strClassButton!="")
                                    {
                                        if(strUrlVerDocumentosTransporte != "")
                                        {
                                            var url_showDocumentosPersonaEmpleado=rec.get('id_persona_empresa_rol')+"/showDocumentosEmpleado";
                                            verDocumentos(url_showDocumentosPersonaEmpleado);
                                        }
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                                    }
                                }
                            },
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var strClassButton = 'button-grid-adminstrarMaquinaVirtual';
                                    var permiso = $("#ROLE_171-7937");
                                    var boolPermiso = (permiso.val() == 1);
                                    if(!boolPermiso){ strClassButton = ""; }

                                    if (strClassButton == "" || rec.data.id_empresa !== "10" )
                                    {
                                        strClassButton        = '';
                                        this.items[4].tooltip = '';
                                    }
                                    else
                                    {

                                        this.items[4].tooltip = 'Actualizar usuario en TACACS Database';
                                    }
                                    return strClassButton;
                                },
                                tooltip: 'Editar Usuario en BD Tacacs',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = store.getAt(rowIndex);
                                    actualizarUsuarioTacacs(rec);
                                }
                            }
                            ]
                        }
                    ],
                    bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
                renderTo: 'grid'
    });
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    
        
    var storeDepartamentos = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlDepartamentosEmpleados,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'strValue',  mapping: 'strValue'},
            {name: 'strNombre', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
        });
        
    var storeCantones = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlCantonesEmpleados,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'strValue',  mapping: 'strValue'},
            {name: 'strNombre', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
            
        
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        collapsible : true,
        collapsed: true,
        width: '100%',
        title: 'Criterios de busqueda',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        defaults: 
        {
            bodyStyle: 'padding:10px'
        },


        buttons: [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function(){ buscar();}
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function(){ limpiar();}
                    }

                ],                
        items: 
                [	
                    {html:"&nbsp;",border:false,width:100},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Departamento:',
                        id: 'cmbDepartamento',
                        name: 'cmbDepartamento',
                        store: storeDepartamentos,
                        displayField: 'strNombre',
                        valueField: 'strValue',
                        queryMode: 'remote',
                        emptyText: 'Seleccione',
                        forceSelection: true
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Canton:',
                        id: 'cmbCanton',
                        name: 'cmbCanton',
                        store: storeCantones,
                        displayField: 'strNombre',
                        valueField: 'strValue',
                        queryMode: 'remote',
                        emptyText: 'Seleccione',
                        forceSelection: true
                    },
                    {html:"&nbsp;",border:false,width:100},

                    {html:"&nbsp;",border:false,width:100},
                    {
                        xtype: 'textfield',
                        id: 'txtNombres',
                        fieldLabel: 'Nombres',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtApellidos',
                        fieldLabel: 'Apellidos',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:100},


                    {html:"&nbsp;",border:false,width:100},
                    {
                        xtype: 'textfield',
                        id: 'txtIdentificacion',
                        fieldLabel: 'Identificacion',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150}
                ],	
                renderTo: 'filtro'
            });

        Ext.getCmp('cmbDepartamento').value = idDepartamento;
        Ext.getCmp('cmbDepartamento').setRawValue(nombreDepartamento);
        Ext.getCmp('cmbDepartamento').setDisabled(true);

        if(boolPermiso || codigoEmpresa == "18")
        {
            Ext.getCmp('cmbDepartamento').value = "";
            Ext.getCmp('cmbDepartamento').setRawValue("");
            Ext.getCmp('cmbDepartamento').setDisabled(false);
        }

    });


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.departamento = Ext.getCmp('cmbDepartamento').value;
    store.getProxy().extraParams.canton = Ext.getCmp('cmbCanton').value;
    store.load();

    Ext.getCmp('cmbDepartamento').setDisabled(true);

    var permiso = $("#ROLE_171-7957");
    var boolPermiso   = (permiso.val() == 1);
    var codigoEmpresa = $('#codigoEmpresa').val();

    if(boolPermiso || codigoEmpresa == "18")
    {
        Ext.getCmp('cmbDepartamento').value="Todos";
        Ext.getCmp('cmbDepartamento').setRawValue("Todos");
        Ext.getCmp('cmbDepartamento').setDisabled(false);
    }
}

function limpiar(){
    Ext.getCmp('txtNombres').value="";
    Ext.getCmp('txtNombres').setRawValue("");
    Ext.getCmp('txtApellidos').value="";
    Ext.getCmp('txtApellidos').setRawValue("");
    Ext.getCmp('txtIdentificacion').value="";
    Ext.getCmp('txtIdentificacion').setRawValue("");
    Ext.getCmp('cmbDepartamento').setDisabled(true);

    var permiso = $("#ROLE_171-7957");
    var boolPermiso   = (permiso.val() == 1);
    var codigoEmpresa = $('#codigoEmpresa').val();

    if(boolPermiso || codigoEmpresa == "18")
    {
        Ext.getCmp('cmbDepartamento').value="Todos";
        Ext.getCmp('cmbDepartamento').setRawValue("Todos");
        Ext.getCmp('cmbDepartamento').setDisabled(false);
    }

    Ext.getCmp('cmbCanton').value="Todos";
    Ext.getCmp('cmbCanton').setRawValue("Todos");
    
    
    
    store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.departamento = Ext.getCmp('cmbDepartamento').value;
    store.getProxy().extraParams.canton = Ext.getCmp('cmbCanton').value;
    
    store.loadData([],false);
    store.currentPage = 1;
    store.load();
}

function verDocumentos(url_showDocumentosPersonaEmpleado){    
    var store = new Ext.data.Store({ 
                    id:'verDocumentosDigitalesStore',
                    total: 'total',
                    pageSize: 10,
                    autoLoad: true,
                    proxy: {
                            type: 'ajax',                
                            url: url_showDocumentosPersonaEmpleado,               
                            reader: {
                                type: 'json', 
                                totalProperty: 'total', 
                                root: 'logs'
                            }
                    },
                    fields:
                    [
                        {name:'id', mapping:'id'},                                      
                        {name:'ubicacionLogicaDocumento', mapping:'ubicacionLogicaDocumento'},
                        {name:'tipoDocumentoGeneral', mapping:'tipoDocumentoGeneral'},
                        {name:'feCreacion', mapping:'feCreacion'},
                        {name:'feCaducidad', mapping:'feCaducidad'},
                        {name:'usrCreacion', mapping:'usrCreacion'},
                        {name:'linkVerDocumento', mapping: 'linkVerDocumento'}
                    ]
    });
                
    var gridDocumentosDigitalesPersonaEmpleado = Ext.create('Ext.grid.Panel', {
        id: 'gridDocumentosDigitalesPersonaEmpleado',
        store: store,
        timeout: 60000,
        dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [
                            { xtype: 'tbfill' }
                    ]}
        ],                  
        columns:[
                {
                    id: 'id',
                    header: 'id',
                    dataIndex: 'id',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Archivo Digital',
                    dataIndex: 'ubicacionLogicaDocumento',
                    width: 300
                },
                {
                    header: 'Tipo Documento',
                    dataIndex: 'tipoDocumentoGeneral',
                    width: 150
                },                  
                {
                    header: 'Fecha de Creacion',
                    dataIndex: 'feCreacion',
                    width: 160,
                    sortable: true
                },
                {
                    header: 'Fecha de Caducidad',
                    dataIndex: 'feCaducidad',
                    width: 160,
                    sortable: true
                },
                {
                    header: 'Creado por',
                    dataIndex: 'usrCreacion',
                    width: 80,
                    sortable: true
                },
                {
                    text: 'Acciones',
                    width: 80,
                    renderer: renderAcciones,
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
            })
        });
        
        
        
    function renderAcciones(value, p, record) {
        var iconos='';
        iconos=iconos+'<b><a href="'+record.data.linkVerDocumento+'" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';	                                       
        return Ext.String.format(
            iconos,
            value,
            '1',
            'nada'
        );
    }
    var pop = Ext.create('Ext.window.Window', {
        title: 'Archivos Digitales',
        height: 400,
        width: 800,
        modal: true,
        layout:{
                type:'fit',
                align:'stretch',
                pack:'start'
        },
        floating: true,
        shadow: true,
        shadowOffset:20,
        items: [gridDocumentosDigitalesPersonaEmpleado] 
    });


    pop.show();

}

function actualizarUsuarioTacacs(data)
{
    var usuarioActivoEnTacacs    = false;
    var tituloBotonActUserTacacs = "";
    var estadoUsuarioTacacs      = "";

    var conn = new Ext.data.Connection({
      listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                Ext.get(document.body).mask('Consultando el usuario en el servidor del TACACS...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    conn.request(
    {
        url: urlConsultarUserTacacs,
        method: 'POST',
        timeout: 60000,
        params: {
            usuario : data.get('login')
        },
        success: function(response)
        {
            var text = Ext.JSON.decode(response.responseText);
            if(text.success)
            {
                usuarioActivoEnTacacs = text.respuesta;

                if(usuarioActivoEnTacacs)
                {
                    tituloBotonActUserTacacs = "DESACTIVAR";
                    estadoUsuarioTacacs      = "Activo";
                }
                else
                {
                    tituloBotonActUserTacacs = "ACTIVAR";
                    estadoUsuarioTacacs      = "Inactivo";
                }

                btncancelar = Ext.create('Ext.Button', {
                        text: 'Cerrar',
                        cls: 'x-btn-rigth',
                        handler: function() {
                          winSeguimientoTarea.destroy();
                        }
                });


                formPanelTrazabilidad = Ext.create('Ext.form.Panel', {
                        bodyPadding: 3,
                        waitMsgTarget: true,
                        width:630,
                        height: 280,
                        layout: 'fit',
                        fieldDefaults: {
                            labelAlign: 'left',
                            msgTarget: 'side'
                        },
                        items: [
                            {
                                xtype: 'fieldset',
                                defaultType: 'textfield',
                                items: [
                                            {
                                                xtype: 'fieldset',
                                                title: '<b>Información del Empleado</b>',
                                                defaultType: 'textfield',
                                                layout: {
                                                    type: 'table',
                                                    columns: 3,
                                                    pack: 'center'
                                                },
                                                items: [
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: 'Nombres:',
                                                            width:250,
                                                            style: Utils.STYLE_BOLD,
                                                            id: 'nombres_empleado',
                                                            name: 'nombres_empleado',
                                                            value: data.get('nombres')
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:10,
                                                            id: 'espacio1',
                                                            name: 'espacio1',
                                                            value: ''
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: 'Apellidos:',
                                                            width:250,
                                                            style: Utils.STYLE_BOLD,
                                                            id: 'apellidos_empleado',
                                                            name: 'apellidos_empleado',
                                                            value: data.get('apellidos')
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: 'Departamento:',
                                                            width:250,
                                                            style: Utils.STYLE_BOLD,
                                                            id: 'departamento_empleado',
                                                            name: 'departamento_empleado',
                                                            value: data.get('nombre_departamento')
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:10,
                                                            id: 'espacio2',
                                                            name: 'espacio2',
                                                            value: ''
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: 'Jurisdicción:',
                                                            width:250,
                                                            style: Utils.STYLE_BOLD,
                                                            id: 'jurisdiccion_empleado',
                                                            name: 'jurisdiccion_empleado',
                                                            value: data.get('nombre_canton')
                                                        }, 
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: 'Acceso a TACACS:',
                                                            width:250,
                                                            style: Utils.STYLE_BOLD,
                                                            id: 'permisos_bd_tacacs',
                                                            name: 'permisos_bd_tacacs',
                                                            value: estadoUsuarioTacacs
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:10,
                                                            id: 'espacio3',
                                                            name: 'espacio3',
                                                            value: ''
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: 'Usuario:',
                                                            width:250,
                                                            style: Utils.STYLE_BOLD,
                                                            id: 'usuario_bd_tacacs',
                                                            name: 'usuario_bd_tacacs',
                                                            value: data.get('login')
                                                        },
                                                       ]
                                            },
                                            {
                                                xtype: 'fieldset',
                                                title: '<b>Acciones sobre el servidor del TACACS</b>',
                                                defaultType: 'textfield',
                                                layout: {
                                                    type: 'table',
                                                    columns: 3,
                                                    pack: 'center'
                                                },
                                                items: [
                                                        {
                                                            xtype: 'label',
                                                            html: 'Acceso a TACACS:',
                                                            style: Utils.STYLE_BOLD
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:175,
                                                            id: 'espacio4',
                                                            name: 'espacio4',
                                                            value: ''
                                                        },
                                                        {
                                                            xtype: 'button',
                                                            text : tituloBotonActUserTacacs,
                                                            cls: 'button-eliminar',
                                                            handler: function()
                                                            {
                                                                var titulo                = "";
                                                                var mensajeAlerta         = "";

                                                                if(tituloBotonActUserTacacs == "DESACTIVAR")
                                                                {
                                                                    mensajeAlerta = "Se desactivará el acceso a TACACS de "+data.get('login')+", \n\
                                                                                    ¿Desea continuar?'";
                                                                }
                                                                else
                                                                {
                                                                    mensajeAlerta = "Se activará el acceso a TACACS de "+data.get('login')+", \n\
                                                                                    ¿Desea continuar?'";
                                                                }

                                                                Ext.Msg.confirm('Alerta',mensajeAlerta,

                                                                    function(btn)
                                                                    {
                                                                        if (btn === 'yes')
                                                                        {
                                                                            Ext.get(formPanelTrazabilidad.getId()).mask('Actualizando usuario en el Tacacs...');

                                                                            Ext.Ajax.request(
                                                                                {
                                                                                    url: urlHabilitarDesabilidarUserTacacs,
                                                                                    method: 'POST',
                                                                                    timeout: 60000,
                                                                                    params: {
                                                                                        usuario               : data.get('login'),
                                                                                        usuarioActivoEnTacacs : estadoUsuarioTacacs,
                                                                                        personaEmpresaRolId   : data.get('id_persona_empresa_rol')
                                                                                    },
                                                                                    success: function(response)
                                                                                    {
                                                                                        var text = Ext.JSON.decode(response.responseText);
                                                                                        if(text.success)
                                                                                        {
                                                                                            titulo = "Respuesta";
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            titulo = "Error";
                                                                                        }
                                                                                        Ext.Msg.show({
                                                                                            title: titulo,
                                                                                            msg: text.respuesta,
                                                                                            buttons: Ext.Msg.OK,
                                                                                            icon: Ext.MessageBox.INFO
                                                                                        });

                                                                                        winSeguimientoTarea.destroy();
                                                                                    },
                                                                                    failure: function(result)
                                                                                    {
                                                                                        var text = Ext.JSON.decode(result.responseText);
                                                                                        Ext.Msg.show({
                                                                                            title: 'Error',
                                                                                            msg: text.respuesta,
                                                                                            buttons: Ext.Msg.OK,
                                                                                            icon: Ext.MessageBox.ERROR
                                                                                        });
                                                                                        winSeguimientoTarea.destroy();
                                                                                    }
                                                                                });
                                                                        }
                                                                    });
                                                            }
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:175,
                                                            id: 'espacio6',
                                                            value: ''
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:175,
                                                            id: 'espacio7',
                                                            value: ''
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            fieldLabel: '',
                                                            width:175,
                                                            id: 'espacio8',
                                                            value: ''
                                                        }
                                                       ]
                                            }
                                        ]
                            }
                        ]
                     });

                winSeguimientoTarea = Ext.create('Ext.window.Window', {
                        title: 'Actualizar permisos de usuario a TACACS',
                        modal: true,
                        width: 650,
                        height: 320,
                        resizable: true,
                        layout: 'fit',
                        items: [formPanelTrazabilidad],
                        buttonAlign: 'center',
                        buttons:[btncancelar]
                }).show();
            }
            else
            {
                    Ext.Msg.show({
                    title: "Error",
                    msg: text.respuesta,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.INFO
                });
            }
        },
        failure: function(result)
        {
            var text = Ext.JSON.decode(result.responseText);
            Ext.Msg.show({
                title: 'Error',
                msg: text.respuesta,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
            winSeguimientoTarea.destroy();
        }
    });
}