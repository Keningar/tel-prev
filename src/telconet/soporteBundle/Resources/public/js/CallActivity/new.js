var strClase = "";

Ext.onReady(function()
{
    
    $.ajax({
       url: urlComboPuntosAtencion,
       type: 'POST',
       success: function (response)
       {
           var arrayPuntoAtencion = jQuery.parseJSON(response.jsonPuntosAtencion);
           var strOpcion = '';
           strOpcion += '<option value="">Escoja una opcion</option>'
           $.each(arrayPuntoAtencion, function (key, value) 
           {
               strOpcion += '<option value=' + value.idPuntoAtencion + '>' + value.nombrePuntoAtencion + '</option>';
           });

           $(".intPuntoAtencion").html(strOpcion);
       },
       error: function (jqXHR, textStatus, errorThrown)
       {
          Ext.Msg.alert("Error al cargar información del combo de puntos de atención");
       }
    });
    
    
    
   $("#telconet_schemabundle_callactivitytype_tipo").change(function(){
      
       var intIdOrigen = $(this).val();
       
       var parametro = {
            "intIdOrigen": intIdOrigen,
       }
        
       $.ajax({
          data: parametro,
          url: urlObtenerNombreOrigen,
          type: 'POST',
          success: function (response)
          {
              if(response.strNombreFormaContacto==="ATC" && (strPrefijoEmpresaSession === "MD" || strPrefijoEmpresaSession === "EN"))
              {
                  $("#bloquePuntoAtencion").css({display:"table-row"});
              }
              else
              {
                  $("#bloquePuntoAtencion").css({display:"none"});
              }
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
            alert("Error al obtener el nombre de la forma de contacto");
          }
           
       });
       
       
   });
    
    $("#valorAFacturar").maskMoney();
    
    document.getElementById("asignar_radio").style.display        = "none";
    document.getElementById("radioLabel").style.display           = "none";
    document.getElementById("esFacturableCheckbox").style.display = "none";
    document.getElementById("esFacturable").style.display         = "none";
    document.getElementById("valorAFacturar").style.display       = "none";
    cambiarTipoActividad('T','','N');
}

);


function mostrarAsignarTarea()
{

    presentaAsignacion = document.getElementById("presentaAsignacion");

    if(presentaAsignacion.value == "S")
    {
        $("#presentaAsignacion").val("N");
        var storeEmpresas = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlEmpresaPorSistema,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    app: 'TELCOS'
                }
            },
            fields:
            [
                {name: 'opcion', mapping: 'nombre_empresa'},
                {name: 'valor', mapping: 'prefijo'}
            ]
        });
	         
        storeCiudades = new Ext.data.Store
        ({ 
            total: 'total',
            pageSize: 200,
            proxy: 
            {
                type: 'ajax',
                method: 'post',
                url: strUrlCiudadesEmpresa,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    nombre: '',
                    estado: 'Activo'		
                }
            },
            fields:
            [
                {name:'id_canton',      mapping:'id_canton'},
                {name:'nombre_canton',  mapping:'nombre_canton'}
            ]
        });   
      
      
        storeDepartamentosCiudad = new Ext.data.Store
        ({ 
            total: 'total',
            pageSize: 200,
            proxy: 
            {
                type: 'ajax',
                method: 'post',
                url: strUrlDepartamentosEmpresaCiudad,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    nombre: '',
                    estado: 'Activo'		
                }
            },
            fields:
            [
                {name:'id_departamento',     mapping:'id_departamento'},
                {name:'nombre_departamento', mapping:'nombre_departamento'}
            ]
        });   
      
      
        storeAsignaEmpleado = new Ext.data.Store
        ({ 
            total: 'total',
            autoLoad:true,
            proxy: 
            {
                type: 'ajax',
                url : strUrlEmpleadosDepartamentCiudad,

                reader: 
                {
                    type: 'json',
                    totalProperty: 'result.total',
                    root: 'result.encontrados',
                    metaProperty: 'myMetaData'
                }
            },
            fields:
            [
                {name:'id_empleado',     mapping:'id_empleado'},
                {name:'nombre_empleado', mapping:'nombre_empleado'}
            ]
        }); 


        function presentarCiudades(empresa)
        {
            storeCiudades.proxy.extraParams = { empresa:empresa};
            storeCiudades.load();
        }


        function presentarDepartamentosPorCiudad(id_canton , empresa)
        {
            storeDepartamentosCiudad.proxy.extraParams = { id_canton:id_canton,empresa:empresa};
            storeDepartamentosCiudad.load();
        }


        function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento)
        {
            storeAsignaEmpleado.proxy.extraParams = { id_canton:id_canton,
                                                      empresa:empresa,
                                                      id_departamento:id_departamento,
                                                      departamento_caso:valorIdDepartamento };
            storeAsignaEmpleado.load();
        }
    
    
        combo_empleados = new Ext.form.ComboBox
        ({
            id: 'comboAsignadoEmpleado',
            name: 'comboAsignadoEmpleado',
            fieldLabel: "Empleado",
            store: storeAsignaEmpleado,
            displayField: 'nombre_empleado',
            valueField: 'id_empleado',
            queryMode: "remote",
            emptyText: '',
            disabled: true
        });

        formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults: 
            {
                labelAlign: 'left',
                labelWidth: 200,                                
                msgTarget: 'side'
            },
            items: 
            [
                {
                    xtype: 'fieldset',
                    defaults: { width: 500 },
                    items: 
                    [
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Empresa:',
                            id: 'comboEmpresa',
                            name: 'comboEmpresa',
                            store: storeEmpresas,
                            displayField: 'opcion',
                            valueField: 'valor',
                            queryMode: "remote",
                            emptyText: '',
                            listeners:
                            {
                                select: function(combo)
                                {		
                                    Ext.getCmp('comboCiudad').reset();									
                                    Ext.getCmp('comboDepartamento').reset();
                                    Ext.getCmp('comboAsignadoEmpleado').reset();

                                    Ext.getCmp('comboCiudad').setDisabled(false);								
                                    Ext.getCmp('comboDepartamento').setDisabled(true);
                                    Ext.getCmp('comboAsignadoEmpleado').setDisabled(true);

                                    presentarCiudades(combo.getValue());
                                }
                            },
                            forceSelection: true
                        }, 
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Ciudad',
                            id: 'comboCiudad',
                            name: 'comboCiudad',
                            store: storeCiudades,
                            displayField: 'nombre_canton',
                            valueField: 'id_canton',
                            queryMode: "remote",
                            emptyText: '',
                            disabled: true,
                            listeners:
                            {
                                select: function(combo)
                                {															
                                    Ext.getCmp('comboDepartamento').reset();
                                    Ext.getCmp('comboAsignadoEmpleado').reset();

                                    Ext.getCmp('comboDepartamento').setDisabled(false);
                                    Ext.getCmp('comboAsignadoEmpleado').setDisabled(true);

                                    empresa = Ext.getCmp('comboEmpresa').getValue();

                                    presentarDepartamentosPorCiudad(combo.getValue(),empresa);
                                }
                            },
                            forceSelection: true
                        }, 
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Departamento',
                            id: 'comboDepartamento',
                            name: 'comboDepartamento',
                            store: storeDepartamentosCiudad,
                            displayField: 'nombre_departamento',
                            valueField: 'id_departamento',
                            queryMode: "remote",
                            emptyText: '',
                            minChars: 3,
                            disabled: true,
                            listeners: 
                            {
                                afterRender: function(combo) {
                                    if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                    {
                                        storeEmpresas.load(function() {
                                            Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                            storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };
                                            storeCiudades.load(function() {
                                                Ext.getCmp('comboCiudad').setDisabled(false);
                                                if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                                {
                                                    Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                    storeDepartamentosCiudad.proxy.extraParams = { id_canton:   strIdCantonUsrSession,
                                                                                                   empresa  :   strPrefijoEmpresaSession};
                                                    storeDepartamentosCiudad.load(function() {
                                                        Ext.getCmp('comboDepartamento').setDisabled(false);
                                                        combo.setValue(Number(strIdDepartamentoUsrSession));
                                                        Ext.getCmp('comboAsignadoEmpleado').setDisabled(false);
                                                        presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                              strIdCantonUsrSession, 
                                                                                              strPrefijoEmpresaSession);
                                                        elWinAsignarCaso.unmask();
                                                    });
                                                }
                                                else
                                                {
                                                    elWinAsignarCaso.unmask();
                                                }
                                            });
                                        });
                                    }
                                    else
                                    {
                                        elWinAsignarCaso.unmask();
                                    }
                                },
                                select: function(combo)
                                {			
                                    Ext.getCmp('comboAsignadoEmpleado').reset();

                                    Ext.getCmp('comboAsignadoEmpleado').setDisabled(false);

                                    empresa = Ext.getCmp('comboEmpresa').getValue();
                                    canton  = Ext.getCmp('comboCiudad').getValue();
                                    presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa);
                                }
                            },
                            forceSelection: true
                        }, 
                        combo_empleados
                    ]
                }
            ],
            buttons:
            [
                {
                    text: 'Guardar',
                    formBind: true,
                    handler: function()
                    {
                        var array_data = new Array();

                        if(Ext.getCmp('comboAsignadoEmpleado') && Ext.getCmp('comboAsignadoEmpleado').value)
                        {				
                            combo_empleados.setVisible(true);                                          

                            comboAsignadoEmpleado.setValue(combo_empleados.getRawValue());
                            comboAsignadoEmpleado.setVisible(true);
                            comboAsignadoEmpleado.setReadOnly(true);

                            winAsignarCaso.hide();
                            document.getElementById("asignar_radio").checked              = false;
                            document.getElementById("asignar_radio").tittle               = "Asignar";
                            document.getElementById("departamento_asignado").value        = Ext.getCmp('comboDepartamento').getValue();
                            document.getElementById("departamento_asignado_nombre").value = Ext.getCmp('comboDepartamento').getRawValue();
                        }
                        else
                        {
                            Ext.Msg.alert('Alerta ', 'Por favor escoja el empleado');
                        }
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function()
                    {
                        winAsignarCaso.hide();
                        document.getElementById("asignar_radio").checked = false;
                    }
                }
            ]
        });               
    }    
   
   
    winAsignarCaso = Ext.create('Ext.window.Window', 
    {
        title: 'Asignar Tarea',
        modal: true,
        closable: false,
        width: 650,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
    elWinAsignarCaso = winAsignarCaso.getEl();
    elWinAsignarCaso.mask('Cargando...');
}


function mostrarAfectadoElemento(varSeleccionar)
{
    if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() != "Escoja una opcion")
    {
        storeTipoElementos = new Ext.data.Store({
            pageSize: 200,
            total: 'total',
            proxy: {
                type: 'ajax',
                url : strUrlTipoElementos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
            },
            fields:
                  [
                    {name:'idTipoElemento', mapping:'idTipoElemento'},
                    {name:'nombreTipoElemento', mapping:'nombreTipoElemento'}
                  ]
        });


        comboTipoElementos= Ext.create('Ext.form.ComboBox',
        {
            id:'comboTipoElementos',
            store: storeTipoElementos,
            displayField: 'nombreTipoElemento',
            valueField: 'idTipoElemento',
            fieldLabel: false,
            width: 260,
            queryMode: "remote",
            emptyText: '',
            hidden: true,
            renderTo: 'combo_tipo_elementos',
            listeners:
            {
                select: function (combo, records, eOpts)
                {
                    $("#label_Elementos").html("Elemento: ");
                    Ext.getCmp('comboElementos').setVisible(true);
                    Ext.getCmp('comboElementos').setValue("");
                    Ext.getCmp('btnAgregaElemento').setVisible(true);
                    storeElementos.removeAll();
                    presentarElementos(combo.getValue());
                }
            }
        });


        storeElementos = new Ext.data.Store({
            pageSize: 10000,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: strUrlElementos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    estado: 'Activo'
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ]
        });


        comboElementos= Ext.create('Ext.form.ComboBox',
        {
            id:'comboElementos',
            store: storeElementos,
            displayField: 'nombreElemento',
            valueField: 'idElemento',
            fieldLabel: false,
            width: 300,
            queryMode: "remote",
            emptyText: '',
            hidden: true,
            renderTo: 'combo_elementos',
            listeners:
            {
                select: function (combo, records, eOpts)
                {
                    document.getElementById("idElemento").value = combo.getValue();
                }
            }
        });

        if(varSeleccionar == "1")
        {
            $("#label_tipoElementos").html("Tipo Elemento");
            Ext.getCmp('comboTipoElementos').setVisible(true);
            Ext.getCmp('gridElementos').setVisible(true);
        }
        else
        {
            $("#label_tipoElementos").html("");
            $("#combo_tipo_elementos").html("");
            Ext.getCmp('comboTipoElementos').setVisible(false);
            $("#label_Elementos").html("");
            $("#combo_elementos").html("");
            Ext.getCmp('comboElementos').setVisible(false);
            Ext.getCmp('gridElementos').setVisible(false);
            Ext.getCmp('btnAgregaElemento').setVisible(false);
        }
    }
    else
    {
        document.getElementById('no_mostrar_elementos').checked = true;
        Ext.Msg.alert("Alerta","Por favor seleccionar la clase antes de seguir con esta opción.");
    }
}

function mostrarEmpresa(clase)
{
    if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() == "Requerimiento entre Empresas")
    {
        $("#label_empresas").html("* Empresas");
        $("#combo_tarea_sintoma").html("");
        $("#label_procesos").html("");
        $("#combo_procesos").html("");
        $("#label_tarea_sintoma").html("");

        StoreEmpresa = new Ext.data.Store
        ({
            total: 'total',
            autoLoad:true,
            proxy: 
            {
                type: 'ajax',
                url : url_empresasDiferentes,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    app: 'TELCOS'
                }
            },
            fields:
            [
                {name:'nombre_empresa', mapping:'nombre_empresa'},
                {name:'prefijo',        mapping:'prefijo'}
            ],
        });


        comboEmpresaDiferente = Ext.create('Ext.form.ComboBox',
        {
            id:'comboEmpresaDiferente',
            store: StoreEmpresa,
            displayField: 'nombre_empresa',
            valueField: 'prefijo',
            fieldLabel: false,
            width: 350,
            queryMode: "remote",
            emptyText: '',
            renderTo: 'combo_empresas',
            listeners: 
            {
                select: function(combo,records,eOpts)
                {
                    cambiarTipoActividad("T",combo.getValue(),"S");
                    storeProcesos.proxy.extraParams = {prefijoEmpresa: combo.getValue()};
                    storeProcesos.load();
                }
            }
        });
    }
    else
    {
        storeProcesos.proxy.extraParams = {prefijoEmpresa: "N/A"};
        storeProcesos.load();
        $("#label_empresas").html("");
        $("#combo_empresas").html("");
        comboTarea.setValue("");
        comboProcesos.setValue("");
        Ext.getCmp('comboProcesos').setRawValue("");
        $("#label_procesos").html("");
        $("#combo_procesos").html("");
        cambiarTipoActividad("T","","N");
    }
}

                         
function cambiarTipoActividad(tipo,prefijoEmpresa,mostrarEmpresa)
{      
   if(tipo == "T")
   {
        //Se cambia el nombre de la etiqueta para indicar que cuando se crea un tarea el login del cliente no es obligatorio
		$("#combo_tarea_sintoma").html("");
		$("#label_tarea_sintoma").html("");
        $("#label_cliente").html(" Cliente: ");
        $("#empleado_combo").html("");
        $("#label_procesos").html("* Procesos");

        if(mostrarEmpresa == "N")
        {
            $("#label_empresas").html("");
            $("#combo_empresas").html("");

            if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() == "Requerimiento entre Empresas")
            {
                document.getElementById("telconet_schemabundle_callactivitytype_claseDocumento").value = "Escoja una opcion";
            }
        }
    
        comboAsignadoEmpleado = Ext.create('Ext.form.ComboBox', 
        {
            id:'tarea_asignada',
            store:'',
            displayField: '',
            valueField: '',
            fieldLabel: '',
            width: 250,
            queryMode: "remote",
            emptyText: '',
            renderTo: 'empleado_combo',
            hidden: true
        });
        

        comboTareaStore = new Ext.data.Store
        ({ 
            total: 'total',
            autoLoad:true,
            proxy: 
            {
                type: 'ajax',
                url : strUrlGetTareasByProcesos,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    nombreProceso: 'TAREAS SOPORTE',
                    estado: 'Activo'
                }
            },
            fields:
            [
                {name:'idTarea',     mapping:'idTarea'},
                {name:'nombreTarea', mapping:'nombreTarea'}
            ],
        });

        comboTarea = Ext.create('Ext.form.ComboBox',
        {
            id:'comboTarea',
            store: comboTareaStore,
            displayField: 'nombreTarea',
            valueField: 'idTarea',
            fieldLabel: false,	
            width: 300,
            queryMode: "remote",
            emptyText: '',
            renderTo: 'combo_tarea_sintoma',
            hidden: true,
            listeners: 
            {
                select: function(  combo, records, eOpts) 
                {
                    document.getElementById("asignar_radio").style.display = "table-cell";
                    document.getElementById("radioLabel").style.display    = "table-cell";               
                }
            }
        });

        if(comboTareaStore.count()==1)
        {
            comboTarea.select(comboTarea.getStore().data.items[0]);
        }


        storeProcesos=new Ext.data.Store
        ({ 
            total: 'total',
            proxy: 
            {
                type: 'ajax',
                url : strUrlGetProcesos,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'registros'
                }
            },
            extraParams: 
            {
                prefijoEmpresa: prefijoEmpresa
            },
            fields:
            [
                {name:'id',            mapping:'id'},
                {name:'nombreProceso', mapping:'nombreProceso'}
            ]

        });
        /////////////fin store de procesos/////////

        
        ///////////combo de procesos/////
        comboProcesos= Ext.create('Ext.form.ComboBox', 
        {
            id:'comboProcesos',
            store: storeProcesos,
            displayField: 'nombreProceso',
            valueField: 'id',
            fieldLabel: false,	
            width: 300,
            queryMode: "remote",
            emptyText: '',
            renderTo: 'combo_procesos',
            listeners: 
            {
                select: function (combo, records, eOpts)
                {
                    if(combo.rawValue === "TAREAS DE HORAS EXTRA")
                    {
                        $("#strLabel").css({visibility:"hidden"});
                        $("#strLabel2").css({visibility:"hidden"});
                        
                        
                        document.getElementById('cboxTareaRapida').checked = false;
                    }
                    else
                    {
                        $("#strLabel").css({visibility:"visible"});
                        $("#strLabel2").css({visibility:"visible"});
                    }
                    $("#label_tarea_sintoma").html("* Tarea: ");
                    comboTarea.setValue("");
                    
                    if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() == "Requerimiento entre Empresas")
                    {
                        paramPrefijo = prefijoEmpresa;
                    }
                    else
                    {
                        paramPrefijo = "N/A";
                    }
                    comboTareaStore.proxy.extraParams = {id: combo.getValue(), prefijoEmpresa: paramPrefijo };
                    comboTareaStore.load(); 
                    Ext.getCmp('comboTarea').setVisible(true);
                    
                    if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() != "Escoja una opcion")
                    {
                        Ext.getCmp('comboTarea').setVisible(true);
                        $("#label_tarea_sintoma").html("* Tarea: ");
                        comboTarea.setValue("");

                        if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() ==
                            "Requerimiento entre Empresas")
                        {
                            paramPrefijo = prefijoEmpresa;
                        }
                        else
                        {
                            paramPrefijo = "N/A";
                        }

                        comboTareaStore.proxy.extraParams = {id: combo.getValue(),prefijoEmpresa: paramPrefijo};
                        comboTareaStore.load();
                    }
                    else
                    {
                        Ext.Msg.alert("Alerta","Por favor seleccionar la clase antes de seguir con esta opción.");
                    }
                }   
            }
        });
        ///////////fin combo de procesos/////


        if(storeProcesos.count()==1)
        {
            comboProcesos.select(comboProcesos.getStore().data.items[0]);
        }
    }
    else if(tipo == "C")
    {
        $("#combo_tarea_sintoma").html("");
        $("#label_tarea_sintoma").html("* Sintoma: ");
        $("#label_cliente").html("* Cliente: ");
        $("#empleado_combo").html("");
        $("#label_procesos").html("");
        $("#combo_procesos").html("");
        $("#asignar_radio").html("");
        $("#label_empresas").html("");
        $("#combo_empresas").html("");
        
        if($("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text() == "Requerimiento entre Empresas")
        {
            document.getElementById("telconet_schemabundle_callactivitytype_claseDocumento").value = "Escoja una opcion";
        }
        
        comboSintomaStore = new Ext.data.Store
        ({ 
            total: 'total',
            proxy: 
            {
                type: 'ajax',
                url : strUrlSintomasGrid,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    nombre: '',
                    estado: 'Activo'
                }
            },
            fields:
            [
                {name:'id_sintoma',     mapping:'id_sintoma'},
                {name:'nombre_sintoma', mapping:'nombre_sintoma'}
            ]
        });
        
        comboSintoma = Ext.create('Ext.form.ComboBox',
        {
            id:'comboSintoma',
            store: comboSintomaStore,
            displayField: 'nombre_sintoma',
            valueField: 'id_sintoma',
            fieldLabel: false,	
            width: 350,
            queryMode: "remote",
            emptyText: '',
            renderTo: 'combo_tarea_sintoma'
        });
        
        
        if(comboSintomaStore.count()==1)
        {
            comboSintoma.select(comboSintoma.getStore().data.items[0]);
        }
    }
    else
    {
        $("#combo_tarea_sintoma").html("");
        $("#label_tarea_sintoma").html("");	
        $("#label_cliente").html("* Cliente: ");
        $("#label_procesos").html("");
        $("#combo_procesos").html("");
        $("#asignar_radio").html("");

        $("#label_procesos").html("");
        $("#label_empresas").html("");
        $("#combo_empresas").html("");
        document.getElementById("telconet_schemabundle_callactivitytype_claseDocumento").value = "Escoja una opcion";
    }
}

function presentarElementos(tipoElemento)
{
    storeElementos.proxy.extraParams = {tipoElemento: tipoElemento,actividades:"S"};
    storeElementos.load();
}

function validarFormulario()
{	       
    fecha                             = document.getElementById("fecha_apertura");
    fecha.value                       = Ext.getCmp('fe_apertura').getValues().fe_apertura_value;
    var permisoFacturarReqCliente     = $("#ROLE_8-4017");
    var boolPermisoFacturarReqCliente = (typeof permisoFacturarReqCliente === 'undefined') ? false : (permisoFacturarReqCliente.val() == 1 
                                        ? true : false);
        
    if(fecha.value=="")
    {
        Ext.Msg.alert("Alerta","El campo fecha de apertura es requerido.");
        return false;
    }
    
    hora        = document.getElementById("hora_apertura");
    hora.value  = Ext.getCmp('ho_apertura').getValues().ho_apertura_value;
    
    if(hora.value=="")
    {
        Ext.Msg.alert("Alerta","El campo hora de apertura es requerido.");
        return false;
    }

    if( document.getElementById("telconet_schemabundle_callactivitytype_tipo").value == "Escoja una opcion" )
    {
        Ext.Msg.alert("Alerta","Debe escoger un origen.");
        return false;   
    }
    
    if( document.getElementById("telconet_schemabundle_callactivitytype_claseDocumento").value == "Escoja una opcion" )
    {
        Ext.Msg.alert("Alerta","Debe escoger un detalle/clase.");
        return false;   
    }
    
    var tipoGenerar = $("#tipoGeneraActividad").val();
    
    if(tipoGenerar == "N")
    {
        Ext.Msg.alert("Alerta","Debe escoger si genera un Caso o una Tarea.");
        return false;   
    }
    else if(tipoGenerar == "C")
    {
        if(comboClientes.getRawValue()=="")
        {
            Ext.Msg.alert("Alerta","Debe escoger un cliente");
            return false;   
        }
        else if(comboLogins.getRawValue()=="")
        {
            Ext.Msg.alert("Alerta","Debe escoger un login");
            return false;   
        }
        
        if(Ext.getCmp('comboSintoma').getRawValue()=="")
        {
            Ext.Msg.alert("Alerta","Debe escoger un Sintoma");
            return false;
        }
        else
        {
            document.getElementById("sintoma").value = comboSintoma.getValue();
        }
    }
	else if(tipoGenerar == "T")
    {
        strClase = $("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text();
        
        if( strClase == strRequerimientosClientes )
        {
            if(comboClientes.getRawValue()=="")
            {
                Ext.Msg.alert("Alerta","Debe escoger un cliente");
                return false;   
            }
            else if(comboLogins.getRawValue()=="")
            {
                Ext.Msg.alert("Alerta","Debe escoger un login");
                return false;   
            }
            
            if(document.getElementById('esFacturable').checked && boolPermisoFacturarReqCliente) 
            {
                var strValorAFacturar = $("#valorAFacturar").val();
                
                if( strValorAFacturar == '0.00' || strValorAFacturar == null || strValorAFacturar == '')
                {
                    Ext.Msg.alert("Alerta","Debe ingresar un valor a facturar");
                    return false;
                }
            }
        }

        if(Ext.getCmp('comboTarea').getRawValue()=="")
        {
            Ext.Msg.alert("Alerta","Debe escoger una Tarea");
            return false;
        }
		   
        if(comboAsignadoEmpleado.getRawValue()!="")
        {
            if(document.getElementById("empleado").value == "")
            {
               document.getElementById("empleado").value = combo_empleados.getValue();
            }	
        }    
    }
    
    
   //Validación para obligar a ingresar un Login en caso se escogiese un cliente 
    if(comboClientes.getRawValue()!="" && comboLogins.getRawValue()=="" )
    {
        Ext.Msg.alert("Alerta","Debe escoger un Login");
        return false;	
    }
          
    
    if(comboClientes.getRawValue()!="" && idCliente=='' )
    {
        if(document.getElementById("cliente").value == "")
        {
            document.getElementById("cliente").value = comboClientes.getValue();
        }
    }
        
    if(comboLogins.getRawValue()!="")
    {
        if(document.getElementById("login_cliente").value == "")
        {
            document.getElementById("login_cliente").value = comboLogins.getValue();
        }	
    }
    
    
    if( Ext.getCmp('comboDepartamento') )
    {
        if( Ext.getCmp('comboDepartamento').getRawValue()!="" )
        {
            if(document.getElementById("departamento_asignado").value == "")
            {
                document.getElementById("departamento_asignado").value        = Ext.getCmp('comboDepartamento').getValue();
                document.getElementById("departamento_asignado_nombre").value = Ext.getCmp('comboDepartamento').getRawValue();
            }	
        }
    }
    
    var strObservacion = document.getElementById("telconet_schemabundle_callactivitytype_observacion").value;
    
    if( strObservacion.trim() == '' || strObservacion.trim() == null )
    {
        Ext.Msg.alert("Alerta","Debe escribir una observación");
        
        return false;
    }
    
    document.getElementById("observacion_contenido").value = strObservacion;
    document.getElementById("tarea").value                 = comboTarea.getValue();
    
    if(document.getElementById("tarea").value == "")
    {
        Ext.Msg.alert("Alerta","Debe escoger una Tarea");
        return false;
    }

    document.getElementById("idElemento").value = "";

    //obtiene los elementos seleccionados y guarda en campo del formulario
    for (var i = 0; i < gridElementos.getStore().getCount(); i++)
    {
        document.getElementById("idElemento").value = document.getElementById("idElemento").value + 
                                                      gridElementos.getStore().getAt(i).data.idElemento+"|";
    }

console.log(document.getElementById("idElemento").value);
//return false;
    if(document.getElementById("mostrar_elementos").checked && document.getElementById("idElemento").value == "")
    {
        Ext.Msg.alert("Alerta","Debe Seleccionar un elemento cuando opción Seleccionar Elemento es 'Si'");
        return false;
    }

    //obtener nombre de forma de contacto
    var intIdOrigen = document.getElementById("telconet_schemabundle_callactivitytype_tipo").value;
    
    var parametro = {
        "intIdOrigen": intIdOrigen,
    }
    
    $.ajax({
        data: parametro,
        url: urlObtenerNombreOrigen,
        type: 'POST',
        success: function (response)
        {
           console.log(strPrefijoEmpresaSession);
           var intIdPuntoAtencion = $("#intPuntoAtencion").val();
           if(response.strNombreFormaContacto === "ATC" && intIdPuntoAtencion === "" && (strPrefijoEmpresaSession === "MD" || strPrefijoEmpresaSession === "EN"))
           {
                    Ext.Msg.alert("Alerta","Debe escoger un Punto de Atencion.");
                    $("#intPuntoAtencion").val("");
                    return false;
                
           }
           else
           {
            
              if( document.getElementById("asignacionSolicitud") !== null && document.getElementById("asignacionSolicitud").value === ""){
                     Ext.Msg.confirm('Alerta','Tiene asignaciones pendientes y no ha asociado ninguna a la tarea, '+
                                     '¿está seguro(a) de crear la tarea sin asignación?',function(btn)
                     {
                         if (btn == "no"){
                            return false;
                         }
                         if (btn == "yes"){
                            enviarSubmit(strClase, strRequerimientosClientes, boolPermisoFacturarReqCliente)
                         }
                     });
              }
              else
              {
                   enviarSubmit(strClase, strRequerimientosClientes, boolPermisoFacturarReqCliente);
              }
           }
           
           
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
           Ext.Msg.alert("Error al obtener el nombre de la forma de contacto");
        }
           
    });
    
}

/**
 * 
 * Ejecuta el submit para enviar a grabar los datos de una tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @param strClase                      - Clase de actividad
 * @param strRequerimientosClientes     - Requerimiento de cliente
 * @param boolPermisoFacturarReqCliente - indica si tiene permiso para perfil FacturarRequerimientoCliente
 * @version 1.0 06-11-2018
 * @since 1.0
 */
function enviarSubmit(strClase, strRequerimientosClientes, boolPermisoFacturarReqCliente)
{
    if( strClase == strRequerimientosClientes )
    {
        if(document.getElementById('esFacturable').checked && boolPermisoFacturarReqCliente) 
        {
            Ext.Msg.confirm('Alerta','Este requerimiento de cliente se va a facturar. Desea continuar?', function(btn)
            {
                if(btn=='yes')
                {
                    Ext.MessageBox.wait("Guardando datos...");
                    document.forms[0].submit();
                }
            });
        }
        else
        {
            Ext.MessageBox.wait("Guardando datos...");
            document.forms[0].submit();
        }
    }
    else
    {
        Ext.MessageBox.wait("Guardando datos...");
        document.forms[0].submit();
    }
}


Ext.onReady(function()
{                 
    fecha = Ext.create('Ext.form.Panel',
    {
        renderTo: 'div_fe_apertura',
        id: 'fe_apertura',
        name: 'fe_apertura',
        width: 140,
        frame: false,
        bodyPadding: 0,
        height: 30,
        border: 0,
        margin: 0,
        items: 
        [{
            xtype: 'datefield',
            id: 'fe_apertura_value',
            name: 'fe_apertura_value',
            editable: false,
            anchor: '100%',
            format: 'Y-m-d',
            value: fechaBase,
            minValue: fechaBase            
        }]
    });
    
    hora = Ext.create('Ext.form.Panel',
    {
        width: 140,
        frame: false,
        height: 30,
        id: 'ho_apertura',
        name: 'ho_apertura',
        border: 0,
        margin: 0,
        renderTo: 'div_hora_apertura',
        items: 
        [{
            xtype: 'timefield',
            format: 'H:i',
            id: 'ho_apertura_value',
            name: 'ho_apertura_value',
            maxValue: '23:59 PM',
            increment: 1,
            value: horaBase,
            minValue: '00:01 AM',
            anchor: '100%'           
        }]
    });
   
 
    $("#nota_busqueda").html("Ingrese minimo 4 letras");
    var itemsPerPage = 100;
  
    storeClientes = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: 
        {
            type: 'ajax',
            method: 'get',
            timeout: 700000,
            url: strUrlGetNombreLoginClientes,
            reader: 
            {
                type: 'json',
                root: 'registros',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        fields:
        [
           {name:'id',                    mapping:'id'},
           {name:'cliente',               mapping:'cliente'},
           {name:'identificacionCliente', mapping:'identificacionCliente'}
        ]    
    });
        
    if (idCliente == '')
    {   
        comboClientes = Ext.create('Ext.form.ComboBox',
        {
            id:'cliente',
            store: storeClientes, 
            displayField: 'cliente',
            valueField: 'id',
            height:30,
            width: 260, 
            border:0,
            margin:0,
            fieldLabel: false,	
            queryMode: "remote",
            emptyText: '',		
            renderTo: 'cliente_combo',
            enableKeyEvents: true,
            typeAhead: false,
            hideLabel: true,
            hideTrigger:true,
            listConfig:
            {
                 loadingText: 'Buscando...',
                 emptyText: 'Parametros no encontrados.'
            },
            listeners:
            {
                select: function(  combo, records, eOpts)
                {
                    Ext.getCmp('comboLogins').setValue(null);
                    storeLogins.proxy.extraParams = { idPersona : combo.getValue() };                    
                    storeLogins.load({params: {}});
                    Ext.getCmp('comboLogins').setVisible(true);
                    $("#label_login").html("Login");
                    document.getElementById("login_cliente").value = combo.getValue();
                    $("#nota_busqueda").html("");

                    if(combo.getValue()===-1)
                    {
                        Ext.getCmp('comboLogins').setVisible(false);
                        combo.setValue("");
                        $("#nota_busqueda").html("Ingrese minimo 4 letras");
                    }
                },                                       
                keyup: function(  combo, records, eOpts) 
                {
                    if(combo.getValue()=== null)
                    {
                        Ext.getCmp('comboLogins').setValue(null);
                        Ext.getCmp('comboLogins').setVisible(false);
                        document.getElementById("login_cliente").value = '';
                        $("#nota_login").html("");
                        $("#label_login").html("");
                    }
                }
            }                                   
        });
  
        if(storeClientes.count()==1)
        {
            comboClientes.select(comboClientes.getStore().data.items[0]);
        }
    }
    else
    {
        $("#nota_busqueda").html("");
        
        comboClientes = Ext.create('Ext.form.TextField', 
        {
            id:'cliente',
            name: 'cliente',
            displayField: 'cliente',
            value: nombresCliente,
            width: 244,
            allowBlank: false,
            renderTo: 'cliente_combo',
            readOnly:true
        });
    }
        
    var itemsPerPage = 100;

    if (idCliente=='' && idPunto== '')
    {
        storeLogins = Ext.create('Ext.data.JsonStore',
        {
            model: 'listaLogins',
            pageSize: itemsPerPage,
            proxy: 
            {
                type: 'ajax',
                method: 'get',
                timeout: 700000,
                url: strUrlGetLoginClientes,
                reader: {
                    type: 'json',
                    root: 'registros',
                    totalProperty: 'total'
                },

                simpleSortMode: true
            },
             fields:
             [
                {name:'id', mapping:'id'},
                {name:'login', mapping:'login'}
            ]
        });
        
        
        comboLogins = Ext.create('Ext.form.ComboBox', 
        {
            id:'comboLogins',                             
            name:'comboLogins',
            fieldLabel: '',
            labelAlign : 'left',
            store: storeLogins, 
            displayField: 'login',         
            valueField: 'id',
            height:30,
            width: 300, 
            border:0,
            margin:0,
            queryMode: "remote",
            emptyText: '',
            hidden: true,
            readOnly:false,
            renderTo: 'login_combo',
            listeners:
            {
                select: function (combo, records, eOpts)
                {
                    $("#nota_login").html("");
                    document.getElementById("login_cliente").value = combo.getValue();
                    $("#nota_login").html("");                   
                } 
            }                             
        });
        
        if(storeLogins.count()==1)
        {
            comboLogins.select(comboLogins.getStore().data.items[0]); 
        }
    }
    else
    {
        var storeLogin = Ext.create('Ext.data.Store',
        {
            fields: ['id', 'login'],
            data : 
            [
                {"id":idPunto, "login":loginPunto}
            ]
        });
        
        comboLogins = Ext.create('Ext.form.ComboBox', 
        {
            id:'comboLogins',
            name:'comboLogins',
            fieldLabel: '*Login:',
            labelAlign : 'left',
            store:storeLogin, 
            displayField: 'login',
            valueField: 'id',
            height:30,
            width: 300, 
            border:0,
            margin:0,
            readOnly:true,	
            queryMode: "remote",
            emptyText: '',
            renderTo: 'login_combo'                              
        });

        if(storeLogin.count()==1)
        {
            comboLogins.select(comboLogins.getStore().data.items[0]);      
        }  
    }
    
    var panelMultiupload = Ext.create('widget.multiupload',{ fileslist: [] });
    formPanelArchivos = Ext.create('Ext.form.Panel',
    {
       width: 780,
       frame: true,
       bodyPadding: '10 10 0',
       renderTo: "div_archivos_subir",
       defaults: {
           anchor: '100%',
           allowBlank: false,
           msgTarget: 'side',
           labelWidth: 50
       },
       items: [panelMultiupload]
   });

   Ext.create('Ext.Button', 
   {
       id: 'btnAgregaElemento',
       hidden: true,
        text: 'Agregar',
        renderTo: "div_boton_agregar_elemento",
        handler: function() 
        {
            if (document.getElementById("idElemento").value > 0)
            {
                var recordDetalleElemento = Ext.create('detalleModelElementos', 
                {
                        idElemento     : document.getElementById("idElemento").value,
                        tipoElemento   : comboTipoElementos.getRawValue(),
                        nombreElemento : comboElementos.getRawValue()
                });

                storeDetalleElementos.insert(0, recordDetalleElemento);
                $("#label_Elementos").html("");
                Ext.getCmp('btnAgregaElemento').setVisible(false);
                Ext.getCmp('comboElementos').setVisible(false);
                Ext.getCmp('comboTipoElementos').setValue("");
                Ext.getCmp('comboElementos').setValue("");
                storeElementos.removeAll();
            }
            else
            {
                Ext.MessageBox.show({
                    title: 'Mensaje',
                    msg: 'No se pudo agregar, por favor volver a seleccionar el elemento',
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.INFO
                  });
            }
        }
   });

   Ext.define('detalleModelElementos', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'idElemento', type: 'integer'},
        {name: 'tipoElemento',    type: 'string'},
        {name: 'nombreElemento',   type: 'string'}
        ]
    });

    storeDetalleElementos = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoDestroy: true,
        model: 'detalleModelElementos',
        proxy: {
            type: 'memory'
        }
    });


    gridElementos = Ext.create('Ext.grid.Panel', {
        id:'gridElementos',
        width: 780,
        height: 160,
        store: storeDetalleElementos,
        loadMask: true,
        hidden: true,
        renderTo: 'divGridElementos',
        columns: [
            {
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                header: '<b>Tipo Elemento</b>',
                dataIndex: 'tipoElemento',
                width: 230,
                sortable: true
            },
            {
                header: '<b>Elemento</b>',
                dataIndex: 'nombreElemento',
                width: 500,
                sortable: true
            },
            {
                header: '<i class="fa fa-cogs" aria-hidden="true"></i>',
                xtype: 'actioncolumn',
                width: 45,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-delete';
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            storeDetalleElementos.remove(grid.getStore().getAt(rowIndex));
                        }
                    }
                ]
            }
        ]
    });

});


sumaFecha = function(d, fecha)
{
    var Fecha = new Date();
    var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() + 1) + "/" + Fecha.getFullYear());
    var sep = sFecha.indexOf('/') !== -1 ? '/' : '-';
    var aFecha = sFecha.split(sep);
    var fecha = aFecha[0] + '/' + aFecha[1] + '/' + aFecha[2];
    fecha = new Date(fecha);
    fecha.setDate(fecha.getDate() + parseInt(d));
    var anno = fecha.getFullYear();
    var mes = fecha.getMonth() + 1;
    var dia = fecha.getDate();
    mes = (mes < 10) ? ("0" + mes) : mes;
    dia = (dia < 10) ? ("0" + dia) : dia;
    var fechaFinal = anno + sep + mes + sep + dia;
    return (fechaFinal);
}


function mostrarEsFacturable()
{
    var strClase    = $("#telconet_schemabundle_callactivitytype_claseDocumento option:selected").text();
    var tipoGenerar = $("#tipoGeneraActividad").val();
    var permiso     = $("#ROLE_8-4017");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    
    if( strClase == strRequerimientosClientes && tipoGenerar == "T" && boolPermiso )
    {
        document.getElementById("esFacturableCheckbox").style.display = "";
        document.getElementById("esFacturable").style.display         = "";
        document.getElementById("valorAFacturar").style.display       = "";
        document.getElementById("esFacturable").checked               = true;
    }
    else
    {
        document.getElementById("esFacturableCheckbox").style.display = "none";
        document.getElementById("esFacturable").style.display         = "none";
        document.getElementById("valorAFacturar").style.display       = "none";
        document.getElementById("esFacturable").checked               = false;
    }
}


function checkFactura()
{
    if(document.getElementById('esFacturable').checked) 
    {
        document.getElementById("valorAFacturar").disabled = false;
    } 
    else 
    {
        document.getElementById("valorAFacturar").disabled = true;
    }
    
    document.getElementById("valorAFacturar").value    = '';
}