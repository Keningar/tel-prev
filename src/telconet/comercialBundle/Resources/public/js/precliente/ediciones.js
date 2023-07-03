/**
 * Documentación para el método 'accionActualizaNombre'.
 * @author Unknow
 * @version 1.0 Unknow
 *
 * Envia mediante post el id de la persona y la informacion del cliente que sera enviada
 * al controlador que realiza la actualizacion.
 * 
 * @param integer    idPersona          
 * @param integer    idPersonaEmpresaRol   
 * @param integer    idOficina  
 * @param string     nombreOficina
 * @param string     nombre
 * @param string     tipoEmpresa
 * @param string     tipoTributario
 * @param string     representanteLegal
 *
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.1 15-07-2016 
 *
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.2 08-08-2016 
 * Se agrega parametro de idPersonaEmpresaRol para el envio en la edicion y poder guardar el Historial
 * se aumenta edicion de Oficina de Facturacion
 */
function accionActualizaNombre(idPersona, idPersonaEmpresaRol, id_oficina, nombre_oficina, nombre_cliente, tipoEmpresa, tipoTributario,
                               representanteLegal) 
{    
    Ext.onReady(function(){
    if(prefijoEmpresa == 'MD')
    {
        var textNombre = new Ext.form.TextField(
            {
                xtype: 'textfield',
                fieldLabel: 'Nuevo(s) Nombre(s)',
                name: 'text_nombre',
                id: 'text_nombre',
                allowBlank: false,
                width: 400
            });
            var textApellido = new Ext.form.TextField(
            {
                xtype: 'textfield',
                fieldLabel: 'Nuevo(s) Apellido(s)',
                name: 'text_apellido',
                id: 'text_apellido',
                allowBlank: false,
                width: 400
            });        
    }else
    {
        var textNombre = new Ext.form.TextField(
            {
                xtype: 'textfield',
                fieldLabel: 'Nuevo Nombre',
                name: 'text_nombre',
                id: 'text_nombre',
                allowBlank: false,
                width: 400
            });
            var textApellido = new Ext.form.TextField(
            {
                xtype: 'textfield',
                fieldLabel: 'Nuevo Apellido',
                name: 'text_apellido',
                id: 'text_apellido',
                allowBlank: false,
                width: 400
            });
    }    
    
    var textRazonSocial = new Ext.form.field.TextArea(
    {
        xtype     : 'textareafield',
        grow      : true,
        name      : 'text_razon_social',
        id      : 'text_razon_social',
        fieldLabel: 'Nueva Razón Social',
        allowBlank: false,
        anchor    : '95%'
    });
    var textRepresentanteLegal = new Ext.form.TextField(
    {
        xtype: 'textfield',
        fieldLabel: 'Representante Legal',
        name: 'text_representante_legal',
        id: 'text_representante_legal',
        allowBlank: false,
        width: 400,
        value: representanteLegal,
    });
    var estadoTipoEmpresa = Ext.create('Ext.data.Store', {
          fields: ['abbr', 'textTipoEmpresa'],
          data: [	
          {
               "abbr": "Privada",
               "textTipoEmpresa": "Privada"
          },
          {
              "abbr": "Publica",
              "textTipoEmpresa": "Pública"
          }]
         });

     var cmbTipoEmpresa= Ext.create('Ext.form.ComboBox', {
         xtype: 'combobox',
         fieldLabel: 'Tipo Empresa',
         store: estadoTipoEmpresa,
         queryMode: 'local',
         id:'textTipoEmpresa',
         name: 'textTipoEmpresa',
         valueField: 'abbr',
         displayField:'textTipoEmpresa',		  
         width: 325,
         triggerAction: 'all',
         selectOnFocus:true,
         lastQuery: '',
         mode: 'local',
         allowBlank: false,
         value: tipoEmpresa, 
        });   

      var estadoTipoTributario = Ext.create('Ext.data.Store', {
        fields: ['abbr', 'textTipoTributario'],
        data: [
            {
                "abbr": "NAT",
                "textTipoTributario": "Natural"
            },
            {
                "abbr": "JUR",
                "textTipoTributario": "Jurídico"
            }]
      });
      
      var cmbTipoTributario = Ext.create('Ext.form.ComboBox', {
        xtype: 'combobox',
        fieldLabel: 'Tipo Tributario',
        store: estadoTipoTributario,
        queryMode: 'local',
        displayField: 'textTipoTributario',
        id: 'textTipoTributario',
        name: 'textTipoTributario',
        valueField: 'abbr',        
        width: 325,        
        allowBlank: false,
        value: tipoTributario,
    });
    
      Ext.define('modelOficina', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idOficina', type: 'int'},
                    {name: 'nombre',  type: 'string'}
                ]
      });

      var oficina_store = Ext.create('Ext.data.Store', {          
          model: "modelOficina",
          autoLoad: true,
          proxy: {
                  type: 'ajax',
                  url : url_lista_oficinas,
          reader: {
                    type: 'json',
                    root: 'oficinas'
                  }
          }
      });      
      var cmbOficinas = new Ext.form.ComboBox({
          xtype: 'combobox',
          store: oficina_store,
          labelAlign : 'left',
          id:'idOficina',
          name: 'idOficina',
          valueField:'idOficina',
          displayField:'nombre',
          fieldLabel: 'Oficina',
          width: 400,
          triggerAction: 'all',
          selectOnFocus:true,
          lastQuery: '',
          queryMode: 'local',        
          allowBlank: false,         
          
          listeners: {
                       select:
                       function(combo, value) {                            
                             $('#infopersonarolextratype_oficinaId').val(combo.getValue());
                       },
                         click: {
                         element: 'el', 
                         fn: function(){                             
                             oficina_store.removeAll();
                             oficina_store.load();
                         }
                       }
                  }
            });         
      oficina_store.on('load', function()
      {                           
          cmbOficinas.setRawValue(nombre_oficina);  
          $('#infopersonarolextratype_oficinaId').val(id_oficina);
      });
      
    panelCliente = Ext.create('Ext.form.Panel', {
        title: '',
        renderTo: Ext.getBody(),
        bodyPadding: 5,
        width: 480,
        items:[
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 250
                },
                items: [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre Actual:',
                    name: 'nombre_actual',
                    value: nombre_cliente
                },    
                cmbOficinas,
                cmbTipoEmpresa,
                cmbTipoTributario,
                textRepresentanteLegal,                
                textNombre,
                textApellido,
                textRazonSocial,                
              ],
            }
        ],
        buttons:
        [
            {
                text: 'Guardar',
                name: 'guardarBtn',
                disabled: false,
                handler: function() 
                {
                    var form1 = this.up('form').getForm();
                    if (form1.isValid()) {

                        Ext.MessageBox.show({
                            msg: 'Guardando datos...',
                            title: 'Procesando',
                            progressText: 'Mensaje',
                            progress: true,
                            closable: false,
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });

                        Ext.Ajax.request({
                            url: url_editar_nombre_ajax,
                            method: 'POST',
                            params: 
                            {
                                idPersona: idPersona,
                                idPersonaRol: idPersonaEmpresaRol,
                                idOficina: $('#infopersonarolextratype_oficinaId').val(),
                                tipoEmpresa: tipoEmpresa,
                                nombre:Ext.getCmp('text_nombre').value,
                                apellido: Ext.getCmp('text_apellido').value,
                                razonsocial: Ext.getCmp('text_razon_social').value,
                                representanteLegal: Ext.getCmp('text_representante_legal').value,
                                tipoEmpresaNuevo: Ext.getCmp('textTipoEmpresa').getValue(),
                                tipoTributarioNuevo: Ext.getCmp('textTipoTributario').getValue()
                            },
                            success: function(response, request) 
                            {
                                Ext.MessageBox.hide();
                                var obj = Ext.decode(response.responseText);
                                if (obj.success) 
                                {
                                    listView.getStore().load();
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'Guardado correctamente.',
                                        width: 300,
                                        icon: Ext.MessageBox.INFO,
                                        buttons: Ext.Msg.OK
                                    });
                                    form1.reset();
                                    ventanaEditarNombre.destroy();
                                } 
                                else 
                                {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Error',
                                        msg: 'Error al guardar.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }

                            },
                            failure: function() 
                            {
                                Ext.MessageBox.show({
                                    modal: true,
                                    title: 'Error',
                                    msg: 'Error al guardar.',
                                    width: 300,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });

                    }
                    else 
                    {
                        Ext.MessageBox.show({
                            modal: true,
                            title: 'Información',
                            msg: 'Ingrese todos los campos solicitados',
                            width: 300,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }
        ]
    });

    if(tipoEmpresa==='' || tipoEmpresa === null)
    {
        textRazonSocial.setVisible(false);
        textRepresentanteLegal.setVisible(false);
        cmbTipoEmpresa.setVisible(false);        
        textRazonSocial.allowBlank=true;
        textRepresentanteLegal.allowBlank=true;
        cmbTipoEmpresa.allowBlank=true;
        textNombre.allowBlank=false;
        textApellido.allowBlank=false;
    }    
    else
    {
        textNombre.setVisible(false);
        textApellido.setVisible(false);
        textRazonSocial.allowBlank=false;
        textRepresentanteLegal.allowBlank=false;
        cmbTipoEmpresa.allowBlank=false;
        textNombre.allowBlank=true;
        textApellido.allowBlank=true;        
    }    
    if(prefijoEmpresa == 'MD')
    {
        cmbOficinas.setVisible(false);        
    }
    ventanaEditarNombre = Ext.widget('window', {
        title: 'Editar Nombre o Razon Social',
        closeAction: 'hide',
        closable: true,
        width: 480,
        height: 350,
        minHeight: 350,
        autoScroll: true,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: panelCliente
    });

    ventanaEditarNombre.show();
 });    
}

/**
 * Documentación para el método 'accionActualizaUsuarioCreacion'.
 *
 * Envia mediante post  la informacion del pre-cliente que sera enviada
 * al controlador que realiza la actualizacion.de usuario creación
 * 
 * @param integer    intIdPersona          
 * @param integer    intIdPersonaEmpresaRol   
 * @param string     strUsuarioCreacion
 * @param string     strLoginUserCreacion
 * @param string     strNombreCliente
 *
 * @author Kevin Baque <kbaque@telconet.ec>
 * @version 1.0 25-01-2019 
 *
 */
function accionActualizaUsuarioCreacion(intIdPersona,intIdPersonaEmpresaRol,strUsuarioCreacion,strLoginUserCreacion,strNombreCliente)
{    
    Ext.onReady(function(){
    Ext.define('modelEmpleado', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'login',type: 'string'},
                {name: 'nombre',type: 'string'}
            ]
    });

      var objProspectoStore = Ext.create('Ext.data.Store', {
          model: "modelEmpleado",
          autoLoad: true,
          proxy: {
                  type: 'ajax',
                  url : url_empleados,
          reader: {
                    type: 'json',
                    root: 'empleados'
                  }
          }
      });
      var objComboVendedores = new Ext.form.ComboBox({
          xtype: 'combobox',
          store: objProspectoStore,
          labelAlign : 'left',
          id:'login',
          name: 'login',
          valueField:'login',
          displayField:'nombre',
          fieldLabel: 'Nuevo usuario',
          width: 400,
          triggerAction: 'all',
          selectOnFocus:true,
          lastQuery: '',
          queryMode: 'local',
          allowBlank: false,
          
          listeners: {
                       select:
                       function(combo, value) {
                            $('#strIdLogin').val(combo.getValue());
                       },
                         click: {
                         element: 'el', 
                         fn: function(){                             
                             objProspectoStore.removeAll();
                             objProspectoStore.load();
                         }
                       }
                  }
            });         
      objProspectoStore.on('load', function()
      {
          objComboVendedores.setRawValue(strUsuarioCreacion);
          $('#strIdLogin').val(strLoginUserCreacion);
      });
      
    panelCliente = Ext.create('Ext.form.Panel', {
        title: '',
        renderTo: Ext.getBody(),
        bodyPadding: 5,
        width: 250,
        items:[
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 300
                },
                items: [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre:',
                    name: 'nombre_actual',
                    value: strNombreCliente
                },
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Usuario creación:',
                    name: 'strUsuarioCreacion',
                    value: strUsuarioCreacion
                },
                objComboVendedores,
              ],
            }
        ],
        buttons:
        [
            {
                text: 'Guardar',
                name: 'guardarBtn',
                disabled: false,
                handler: function() 
                {
                    var form1 = this.up('form').getForm();
                    if (form1.isValid()) {

                        Ext.MessageBox.show({
                            msg: 'Guardando datos...',
                            title: 'Procesando',
                            progressText: 'Mensaje',
                            progress: true,
                            closable: false,
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });

                        Ext.Ajax.request({
                            url: url_user_creacion_ajax,
                            method: 'POST',
                            params: 
                            {
                                intIdPersona:intIdPersona,
                                intIdPersonaEmpresaRol:intIdPersonaEmpresaRol,
                                strUsCreacionAntes:strLoginUserCreacion,
                                strIdLogin: $('#strIdLogin').val()
                            },
                            success: function(response, request) 
                            {
                                Ext.MessageBox.hide();
                                var obj = Ext.decode(response.responseText);
                                if (obj.success) 
                                {
                                    listView.getStore().load();
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'Guardado correctamente.',
                                        width: 300,
                                        icon: Ext.MessageBox.INFO,
                                        buttons: Ext.Msg.OK
                                    });
                                    form1.reset();
                                    ventanaEditarUserCreacion.destroy();
                                } 
                                else 
                                {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Error',
                                        msg: 'Error al guardar.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }

                            },
                            failure: function() 
                            {
                                Ext.MessageBox.show({
                                    modal: true,
                                    title: 'Error',
                                    msg: 'Error al guardar.',
                                    width: 300,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });

                    }
                    else 
                    {
                        Ext.MessageBox.show({
                            modal: true,
                            title: 'Información',
                            msg: 'Ingrese todos los campos solicitados',
                            width: 300,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }
        ]
    });

    ventanaEditarUserCreacion = Ext.widget('window', {
        title: 'Editar usuario creación',
        closeAction: 'hide',
        closable: true,
        width: 480,
        height: 200,
        autoScroll: true,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: panelCliente
    });

    ventanaEditarUserCreacion.show();
 });
}


