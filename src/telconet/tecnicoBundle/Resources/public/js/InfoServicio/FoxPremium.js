/** @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
 * @version 1.1 28-09-2020
 * @since 1.1
 * Se modificó mensaje, ya que ahora se usa el método para Paramount y Noggin
 * 
 * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
 * @version 1.2 07-12-2020
 * @since 1.2  Se Modifica valores de entrada del metodo (strNombreProducto)-> se ontiene el nombre tecnico del producto.
 * 
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.3 17-08-2022
 * @since 1.3  Se valida si los productos no crea una nueva contraseña.
 */
function restablecerContrasenia(intIdServicio, strNombreProducto)
{
    Ext.Msg.confirm('Alerta', 'Desea restablecer la contraseña del servicio?', function (btn) {
        if ('yes' == btn)
        {
            Ext.MessageBox.wait("Procesando el cambio de contraseña...");
            Ext.Ajax.request({
            url: urlFoxRestableceContrasenia,
            method: 'POST',
            timeout: 1000000,
            params: {
                intIdServicio    : intIdServicio,
                strNombreProducto: strNombreProducto
            },
            success: function (response) {
                Ext.MessageBox.hide();
                var variable = response.responseText;
                if ("OK" == variable)
                {
                    if (strNombreProducto === "E-LEARN" || strNombreProducto === "HBO-MAX") {
                      Ext.Msg.alert('Mensaje del Sistema ', 'Se envío correctamente el correo para restablecer la contraseña.');
                    }
                    else
                    {
                      Ext.Msg.alert('Mensaje del Sistema ', 'Se realiza el cambio de contraseña exitosamente');
                    }
                }
                else
                {
                    Ext.Msg.alert('Error ', variable);
                }
            },
            failure: function (result) {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
            }
        });
        }
    });
}

/** @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
 * @version 1.1 28-09-2020
 * @since 1.1
 * Se modificó mensaje, ya que ahora se usa el método para Paramount y Noggin
 * 
 * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
 * @version 1.2 07-12-2020
 * @since 1.2  Se Modifica valores de entrada del metodo (strNombreProducto)-> se ontiene el nombre tecnico del producto.
 */
function clearCacheToolbox(intIdServicio, strNombreProducto)
{
    Ext.Msg.confirm('Alerta', 'Desea establecer una comunicación con el servicio?', function(btn) {
        if ('yes' == btn)
        {
            Ext.MessageBox.wait("Limpiando la caché en el servidor...");
            Ext.Ajax.request({
                url: urlFoxClearCacheToolbox,
                method: 'POST',
                timeout: 1000000,
                params:
                        {
                            intIdServicio    : intIdServicio,
                            strNombreProducto: strNombreProducto
                        },
                success: function(response) {
                    Ext.MessageBox.hide();
                    var variable = response.responseText;
                    if ("OK" == variable)
                    {
                        Ext.Msg.alert('Mensaje del Sistema ', 'Se ha actualizado satisfactoriamente la información en la plataforma.');
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', variable);
                    }
                },
                failure: function(result) {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}
/**
 * reenviarContrasenia, función que reenvía credenciales actuales al cliente por servicio FOX PREMIUM.
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 08-05-2019
 * @since 1.0
 * 
 * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
 * @version 1.1 28-09-2020
 * @since 1.1
 * Se modificó mensaje, ya que ahora se usa el método para Paramount y Noggin
 * 
 * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
 * @version 1.2 07-12-2020
 * @since 1.2  Se Modifica valores de entrada del metodo (strNombreProducto)-> se ontiene el nombre tecnico del producto.
 */
function reenviarContrasenia(intIdServicio, strNombreProducto)
{
    Ext.Msg.confirm('Alerta', 'Desea reenviar la contraseña del servicio?', function (btn) {
        if ('yes' == btn)
        {
            Ext.MessageBox.wait("Procesando el reenvío de contraseña...");
            Ext.Ajax.request({
            url: urlFoxReenvioContrasenia,
            method: 'POST',
            timeout: 1000000,
            params: {
                intIdServicio    : intIdServicio,
                strNombreProducto: strNombreProducto
            },
            success: function (response) {
                Ext.MessageBox.hide();
                var variable = response.responseText;
                if ("OK" == variable)
                {
                    Ext.Msg.alert('Mensaje del Sistema ', 'Se realiza el reenvío de usuario y contraseña exitosamente.');
                }
                else
                {
                    Ext.Msg.alert('Error ', variable);
                }
            },
            failure: function (result) {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error ', 'Error al realizar el reenvío de usuario y contraseña, por favor consulte con el Administrador.');
            }
        });
        }
    });
}
/**
 * Se crea funcion que permite el ingreso de Contactos para productos Paramount y Noggin
 * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
 * @version 1.0 07-12-2020
 * @since 1.0
 */
function ingresarContacto(arrayData, strNombreProducto)
{
    var intIdServicio   = arrayData.idServicio;
    var nombreProducto  = strNombreProducto;
    var personaid       = arrayData.intPuntoId;

    Ext.onReady(function()
    {
        Ext.define('GridCaractCorreo',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'formaContacto'},
                    {name: 'valor', type: 'string'}
                ]
        });
        storePersonaFormasContacto = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'GridCaractCorreo',
            proxy:
                {
                    type: 'ajax',
                    url: UrlGridCorreo,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaid: '',
                            intIdServicio:'',
                            nombreProducto:''
                        },
                    simpleSortMode: true
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        store.getProxy().extraParams.personaid = personaid;
                        store.getProxy().extraParams.intIdServicio = intIdServicio;
                        store.getProxy().extraParams.nombreProducto = nombreProducto;
                    }
                }
        });
        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
        {
            clicksToEdit: 2
        });
        var gridFormasContacto = Ext.create('Ext.grid.Panel',{
            store: storePersonaFormasContacto,
            columns : 
            [
                {
                    text: 'Correo',
                    dataIndex: 'valor',
                    width: 400,
                    align: 'center',
                    editor:
                        {
                            width: '80%',
                            xtype: 'textfield',
                            allowBlank: false
                        }
                },
                {
                    text: 'Accion',
                    xtype: 'actioncolumn',
                    align: 'center',
                    width: 70,
                    sortable: false,
                    items:
                        [
                            {
                                getClass: function()
                                {
                                    strEliminarFormaContacto = 'button-grid-invisible';
                                    if (rolEliminarContacto == 1)
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
            width: 300,
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
                            var r = Ext.create('GridCaractCorreo',
                                {
                                    formaContacto: '',
                                    valor: ''
                                });
                            storePersonaFormasContacto.insert(0, r);
                        }
                        cellEditing.startEditByPosition({row: fila, column: columna});
                    }
                }
            ],
            plugins: [cellEditing],
            bbar:
            [
                
                {
                    text: 'Guardar',
                    xtype: 'button',
                    style: 'margin-left:170px',
                    handler: function()
                    {
                        var array_data = grabarFormasContacto();
                        var boolValidaGrid = validaFormasContacto();
                        if(array_data && boolValidaGrid != false)
                        {
                            Ext.Msg.confirm('Alerta', 'Seguro desea guardar los cambios?', function (btn) {
                                if(btn=='yes')
                                {
                                    connActCaracteristicas.request({
                                        url: urlIngresoCorreoParamNoggin,
                                        method: 'POST',
                                        waitMsg: 'Esperando Respuesta...',
                                        dataType: 'json',
                                        timeout: 400000,
                                        params:
                                            {
                                                intIdServicio      : arrayData.idServicio,
                                                intPuntoId         : personaid,
                                                nombreProducto     : nombreProducto,
                                                array_data         : array_data
                                            },
                                            success: function (response)
                                            {
                                                  var variable = response.responseText;
                                                  if ("OK" == variable)
                                                  {
                                                      Ext.Msg.alert('Mensaje del Sistema', 'Los Contactos han sido guardado correctamente');
                                                      storePersonaFormasContacto.load();
                                                  }
                                                  else
                                                  {
                                                      Ext.Msg.alert('Error', 'Error al Guardar los Contactos, por favor consulte con el Administrador.');
                                                  }
                                            },
                                            failure: function (response) {
                                                  Ext.Msg.alert('Error', 'Error al Guardar los Contactos, por favor consulte con el Administrador.');
                                            }
                                    });
                                }
                            })
                        }
                    }
                },
                {
                    text: 'Cancelar',
                    xtype: 'button',
                    style: 'margin-left:10px',
                    handler: function()
                    {
                        winActualizarCorreo.close();
                    }
                }
            ]
    
        });
        storePersonaFormasContacto.load();
        var winActualizarCorreo = Ext.create('Ext.window.Window',
            {
               title: 'Ingreso de Correo Electronico',
               modal: true,
               width: 485,
               closable: true,
               layout: 'fit',
               items: [gridFormasContacto]
            }).show();
            
        function trimAll(texto)
            {
                return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ').trim();
            }
        function grabarFormasContacto()
            {
                var variable = new Array();
                for (var i = 0; i < gridFormasContacto.getStore().getCount(); i++)
                {
                    variable[i] = gridFormasContacto.getStore().getAt(i).data;
                    if(variable[i]['formaContacto']=="")
                    {
                        variable[i]['formaContacto'] = 'Correo Electronico';
                    }
                    if(variable[i]['valor'] == '' || variable[i]['valor'] == null)
                    {
                        Ext.Msg.alert("Error",'El campo Correo se encuentra vacio, por favor corregir.');
                        return null;
                    }
                }
                return Ext.JSON.encode(variable);
            }
        function validaFormasContacto()
            {
                return Utils.validaFormasContacto(gridFormasContacto);
            }
    });
}

function validaCorreoECDF(correoElectronico) 
{
  let result = {mensaje: "Ocurrió un error", estado: false};
        Ext.Ajax.request({
          url: url_valida_correo_electronicoECDF,
          method: 'post',
          async: false,
          timeout: 400000,
          params: {
              correoElectronico: correoElectronico
          },
          success: function(response) 
          {
              if (response.responseText != 'NO EXISTENTE')
              {
                  if (response.responseText == 'EXISTENTE')
                  {
                      result.mensaje = "El correo electrónico ingresado ya fue usado en " +
                          "otra Suscripción del producto El Canal del Futbol, favor ingresar otro correo.";
                      result.estado = true;
                  }
                  else if (response.responseText == 'ERROR')
                  {
                    result.mensaje = "Se presentaron errores al validar el correo electrónico" +
                          " ingresado, favor notificar a Sistemas.";
                    result.estado = true;
                  }
              }
              else 
              {
                result.mensaje = "Correo válido";
                result = false;
              }
          },
          failure: function()
          {
            result.mensaje = "Se presentaron errores al validar el correo electrónico" +
                  " ingresado, favor notificar a Sistemas.";
            result.estado = true;
          }
      });
      return result;
}

function activarSerivicioECFD(intIdServicio, idPersonaEmpresaRol, boolEliminarCorreo, boolActualizar) 
{
    Ext.MessageBox.wait("Procesando la activacón del servicio mediante el correo electrónico...");
    Ext.Ajax.request({
      url: url_activa_servicioECDF,
      method: "POST",
      timeout: 1000000,
      params: {
        intIdServicio:        intIdServicio,
        idPersonaEmpresaRol:  idPersonaEmpresaRol,
        boolEliminarCorreo:   boolEliminarCorreo,
        boolActualizar:       boolActualizar
      },
      success: function (response) {
          Ext.MessageBox.hide();
          var variable = response.responseText;
          if ("OK" == variable) 
          {
              Ext.Msg.alert("Mensaje", "Se realizó mediante correo electrónico la activación del producto correctamente.", function(btn)
              {
                  store.load();
              });
          }
          else 
          {
              Ext.Msg.alert("Mensaje", variable, function(btn)
              {
                  if (!boolActualizar) {
                      store.load();
                  }
              });
          }
      },
      failure: function (result) {
          Ext.MessageBox.hide();
          Ext.Msg.alert("Error ", "Error al activar el producto mediante correo electrónico, por favor consulte con el Administrador."
          );
      },
    });
}


function agregarCorreoElectronicoECDF(arrayData, strNombreProducto)
{
    var intIdServicio   = arrayData.idServicio;
    var nombreProducto  = strNombreProducto;
    var personaid       = arrayData.intPuntoId;
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 4,
            waitMsgTarget: true,
            border: false,
            width: "100%",
            height: "100%",
            bodyStyle: {
              background: "#fff"
            },
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 110,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 2
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 305
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'correoElectronico',
                                    name: 'correoElectronico',
                                    fieldLabel: 'Correo electrónico',
                                    displayField: '',
                                    value: arrayData.strCorreoMcAfee,
                                    valueField: '',
                                    maxLength: 1900,
                                    width: 305,
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Grabar',
                        formBind: true,
                        handler: function ()
                        {
                            Ext.Msg.confirm('Alerta', '¿Seguro desea guardar el correo?', function (btn) 
                            {
                                if(btn=='yes')
                                {
                                    var strCorreoNuevo        = Ext.getCmp('correoElectronico').value;
                                    var booleanCorreoValido   = validaCorreo(strCorreoNuevo);
                                    let exiteCorreo           = validaCorreoECDF(strCorreoNuevo);
                                    if (Ext.isEmpty(strCorreoNuevo))
                                    {
                                        winActualizarCorreo.hide();
                                        Ext.Msg.alert("Alerta", "Favor ingrese el correo correspondiente!", function(opcion) 
                                        {
                                            winActualizarCorreo.show();
                                            Ext.getCmp("correoElectronico").focus(false, 200);
                                        });
                                    }
                                    else if (!booleanCorreoValido)
                                    {
                                        winActualizarCorreo.hide();
                                        Ext.Msg.alert("Alerta", "Favor ingrese un correo válido!", function(opcion) 
                                        {
                                            winActualizarCorreo.show();
                                            Ext.getCmp("correoElectronico").focus(false, 200);
                                        });
                                    }
                                    else if (exiteCorreo.estado) 
                                    {
                                        winActualizarCorreo.hide();
                                        Ext.Msg.alert('Error', exiteCorreo.mensaje,function(opcion) 
                                        {
                                            winActualizarCorreo.show();
                                            Ext.getCmp("correoElectronico").focus(false, 200);
                                        });
                                    }
                                    else
                                    {
                                        let array_data = [{formaContacto: "Correo Electronico", valor: strCorreoNuevo}]
                                        connActCaracteristicas.request({
                                                url: urlIngresoCorreoParamNoggin,
                                                method: 'POST',
                                                waitMsg: 'Esperando Respuesta...',
                                                dataType: 'json',
                                                timeout: 400000,
                                                params:
                                                {
                                                    intIdServicio      : intIdServicio,
                                                    intPuntoId         : personaid,
                                                    nombreProducto     : nombreProducto,
                                                    array_data         : JSON.stringify(array_data)
                                                },
                                                success: function (response)
                                                {
                                                    var variable = response.responseText;
                                                    if ("OK" == variable)
                                                    {
                                                        if (nombreProducto === "ECDF")
                                                        {
                                                          winActualizarCorreo.destroy();
                                                          activarSerivicioECFD(arrayData.idServicio, arrayData.idPersonaEmpresaRol, true, false);
                                                        }
                                                    }
                                                },
                                                failure: function (result)
                                                {
                                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                }
                                            });
                                        winActualizarCorreo.destroy();
                                    }
                                }
                            })
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function ()
                        {
                            winActualizarCorreo.destroy();
                        }
                    }
                ]
        });
    Ext.getCmp("correoElectronico").focus(false, 200);
    var winActualizarCorreo = Ext.create('Ext.window.Window',
        {
            title: 'Ingresar Correo Electrónico',
            modal: true,
            width: 350,
            closable: false,
            resizable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
}
function validaCorreo(correo) {
    var respuesta = false;
    var RegExPattern = Utils.REGEX_MAIL;
    if ((correo.match(RegExPattern)) && (correo.value != '')) {
        respuesta = true;
    }
    return respuesta;
}
function actualizarCorreoElectronicoECDF(arrayData, strCorreoECDF) 
{
    let strMensaje = `Se activará el siguiente servicio. <br /><br />
                      <b>Producto:</b> ${arrayData.nombreProducto}<br />
                      <b>Correo Electrónico:</b> ${strCorreoECDF}<br /><br />¿Desea continuar?`;
    Ext.Msg.confirm('Alerta', strMensaje, function (btn) 
    {
      if(btn == "yes")
      {
          activarSerivicioECFD(arrayData.idServicio, arrayData.idPersonaEmpresaRol, false, true);
      }
    })
}

function activarSerivicioSinCredenciales(arrayData) 
{
    Ext.MessageBox.wait("Procesando la activacón del servicio...");
    Ext.Ajax.request({
      url: url_activa_servicio_sin_credenciales,
      method: "POST",
      timeout: 1000000,
      params: {
        intIdServicio:        arrayData.idServicio
      },
      success: function (response) {
          Ext.MessageBox.hide();
          var variable = response.responseText;
          if ("OK" == variable) 
          {
              Ext.Msg.alert("Mensaje", "Se activó correctamente el producto. <br />Las indicaciones para crear la contraseña se enviaron al correo del cliente", function(btn)
              {
                  store.load();
              });
          }
          else 
          {
              Ext.Msg.alert("Error", variable);
          }
      },
      failure: function (result) {
          Ext.MessageBox.hide();
          Ext.Msg.alert("Error ", "Error al activar el producto, por favor consulte con el Administrador."
          );
      },
    });
}
function reenviarCorreoPassword(arrayData) 
{
    Ext.MessageBox.wait("Enviando correo para creación de contraseña...");
    Ext.Ajax.request({
      url: url_reenvia_correo_password,
      method: "POST",
      timeout: 1000000,
      params: {
        intIdServicio:        arrayData.idServicio
      },
      success: function (response) {
          Ext.MessageBox.hide();
          var variable = response.responseText;
          if ("OK" == variable) 
          {
              Ext.Msg.alert("Mensaje", "Proceso exitoso. <br />Las indicaciones para crear la contraseña se enviaron al correo del cliente");
          }
          else 
          {
              Ext.Msg.alert("Error", variable);
          }
      },
      failure: function (result) {
          Ext.MessageBox.hide();
          Ext.Msg.alert("Error ", "Error al enviar el correo, por favor consulte con el Administrador."
          );
      },
    });
}