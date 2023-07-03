Ext.require([
	'*',
	'Ext.tip.QuickTipManager',
		'Ext.window.MessageBox'
]);
var boolTrasladar = false;
let boolProcesoContinuo = strTipoProceso === 'continuo';

Ext.onReady(function(){
    
  
    
    sm = new Ext.selection.CheckboxModel({
        allowDeselect: true
    });
    
	storeLogins = new Ext.data.Store({ 
		total: 'total',
		pageSize: 10000,
		proxy: {
			type: 'ajax',
			url : url_Logins,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'listado'
			},
			extraParams: {
				idCliente: '',
				estado: ''
			}
		},
		fields:
				[
					{name:'idPunto', mapping:'idPunto'},
					{name:'login', mapping:'login'}
				],
		autoLoad: true
	});

    if(boolProcesoContinuo)
    {
        storeLogins.on('load', function(){
            let loginRecord = storeLogins.findRecord('login', strLoginAnterior);
            let comboLogin = Ext.getCmp('cmb_LOGIN');
            comboLogin.setValue(loginRecord);
            comboLogin.fireEvent('select', comboLogin, loginRecord);
            comboLogin.setDisabled(true);
        });
    }
			
    storeServiciosByLogin = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        listeners:
            {
                load: function(records)
                {
                    var i = 0;
                    var idsServiciosTrasladar = document.getElementById('idsServiciosTrasladar');
                    var serviciosActivos = "";
                    var j = 1;
                    idsServiciosTrasladar.value = serviciosActivos;
                    Ext.each(records, function(record)
                    {
                        var fin = record.data.length;
                        for (i = 0; i < fin; i++)
                        {
                            var permiteSeleccion = record.data.get(i).data.permiteSeleccion;
                            if (permiteSeleccion === "SI")
                            {
                                boolTrasladar = true;
                                sm.select(i, true);
                                var idServicio = record.data.get(i).data.idServicio;
                                if (j === 1)
                                {
                                    serviciosActivos = idServicio;
                                }
                                else
                                {
                                    serviciosActivos = serviciosActivos + "," + idServicio;
                                }
                                j++;
                            }
                        }
                    });
                    idsServiciosTrasladar.value = serviciosActivos;
                    sm.setLocked(true);
                }
            },
        proxy: 
            {
            type: 'ajax',
            url: url_servicios,
            reader: 
                {
                type: 'json',
                totalProperty: 'total',
                root: 'listado'
            },
            extraParams: 
                {
                idPunto: '',
                estado: ''
            }
        },
        fields:
            [
                {name: 'idServicio',        mapping: 'idServicio'},
                {name: 'servicio',          mapping: 'servicio'},
                {name: 'estado',            mapping: 'estado'},
                {name: 'permiteSeleccion',  mapping: 'permiteSeleccion'}
            ]
    });
		
		listView = Ext.create('Ext.grid.Panel', {
			height:200,
			collapsible:false,
			title: '',
			selModel: sm,
			dockedItems: [ {
							xtype: 'toolbar',
							layout:{
								type:'table',
								columns: 1,
								align: 'left',
							},
							dock: 'top',
							align: '->',
							bodyStyle: "background: white; padding:10px; border: 0px none;",
							items: [{html:"&nbsp;",border:false,height:10},
								{
									xtype: 'combobox',
									id: 'cmb_LOGIN',
									name: 'cmb_LOGIN',
									fieldLabel: '* Login',
									typeAhead: true,
									triggerAction: 'all',
									displayField:'login',
									queryMode: "local",
									valueField: 'idPunto',
									selectOnTab: true,
									store: storeLogins,              
									lazyRender: true,
									listClass: 'x-combo-list-small',
									labelStyle: "color:red;",
									forceSelection: true,
									emptyText: 'Seleccione un Login..',
									minChars: 3, 
									listeners:{
										select:{fn:function(combo, value) {
                                                                                        
                                            var idPuntoTraslado   = document.
                                                                    getElementById('idPuntoTraslado');
                                            idPuntoTraslado.value = combo.getValue();
                                            var strPermiteTraslado = "SI";
                                            var strExisteDeuda = "NO";
                                            var strMensajeValidacionTn = "";
                                            if (strEmpresaCod == 'TN')
                                            {
                                                Ext.get(listView.getId()).mask('Validando Información...');
                                                Ext.Ajax.request({
                                                    async: false,
                                                    url: url_validaTrasladoTn,
                                                    method: 'post',
                                                    timeout: 400000,
                                                    params: {
                                                        idPunto : combo.getValue() , 
                                                        puntoId : punto_id,
                                                        estado  : 'Todos' 
                                                    },
                                                    success: function(response) {
                                                        var datosValidacionTrasladoTn = Ext.JSON.decode(response.responseText);
                                                        if (datosValidacionTrasladoTn.strStatus == "OK")
                                                        {
                                                            if (datosValidacionTrasladoTn.strPermiteTraslado == "SI")
                                                            {
                                                                if (datosValidacionTrasladoTn.strTieneDeuda == "SI")
                                                                {
                                                                    strExisteDeuda         = "SI";
                                                                    strPermiteTraslado     = "SI";
                                                                    strMensajeValidacionTn = "No se permite el traslado porque el punto " +
                                                                                             "registra una deuda de $"+datosValidacionTrasladoTn.strDeuda + ", ¿Desea solicitar la autorización del Traslado?";
                                                                }
                                                            }
                                                            else
                                                            {
                                                                strPermiteTraslado     = datosValidacionTrasladoTn.strPermiteTraslado;
                                                                strMensajeValidacionTn = datosValidacionTrasladoTn.strMensaje + datosValidacionTrasladoTn.strEstadoServicios;
                                                                strMensajeValidacionTn = strMensajeValidacionTn.trim()+".";
                                                                if (datosValidacionTrasladoTn.strTieneDeuda == "SI")
                                                                {
                                                                    strMensajeValidacionTn = strMensajeValidacionTn + " No se permite el traslado porque el punto seleccionado" +
                                                                                             " registra una deuda de $"+datosValidacionTrasladoTn.strDeuda + ".";
                                                                }
                                                            }
                                                        }
                                                        else
                                                        {
                                                            strPermiteTraslado = "NO";
                                                            Ext.Msg.alert('Mensaje ', datosValidacionTrasladoTn.strMensaje);
                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });
                                                Ext.get(listView.getId()).unmask();
                                            }
                                            else
                                            {
                                                /*
                                                 * Se elimina restricción de creación de solicitudes
                                                 * de traslado cuando existan solicitudes de cambio
                                                 * de equipo por soporte en estado Pendiente
                                                 * ya que se procede a migrarlas con nuevo método
                                                 */
                                                strPermiteTraslado = "SI";
                                            }

                                            if (strPermiteTraslado == "SI")
                                            {
                                                if(strExisteDeuda == "SI")
                                                {
                                                    Ext.Msg.confirm('Mensaje',strMensajeValidacionTn, function(btn){
                                                    if(btn=='yes'){

                                                        $('#banderaAutorizarSol').val("S");

                                                        var precioTraslado      = document.getElementById('precioTrasladoTn').value;
                                                        var descripcionTraslado = document.getElementById('descripcionTrasladoTn').value;
                                                        var tipoNegocio         = document.getElementById('tipoNegocio').value;
                                                        var intIdMotivo         = document.getElementById("objListadoMotivos").value
                                                                                  ? document.getElementById("objListadoMotivos").value:"";
                                                        Ext.get(listView.getId()).mask('Obteniendo servicios a trasladar...');
                                                        Ext.Ajax.request({
                                                            async: false,
                                                            url: url_servicios,
                                                            method: 'post',
                                                            timeout: 400000,
                                                            params: {
                                                                idPunto  : combo.getValue(),
                                                                puntoId  : punto_id,
                                                                estado   : 'Todos'
                                                            },
                                                            success: function(response) {
                                                                var respuesta = Ext.JSON.decode(response.responseText);
                                                                var cadenaServiciosTrasladar = "";
                                                                var i = 0;
                                                                var m = 1;
                                                                var arrayListado = respuesta.listado;

                                                                for (i = 0; i < respuesta.total; i++)
                                                                {
                                                                    var permiteSeleccion = arrayListado[i].permiteSeleccion;
                                                                    if(permiteSeleccion === "SI")
                                                                    {
                                                                        var idServicio = arrayListado[i].idServicio;
                                                                        if (m === 1)
                                                                        {
                                                                            cadenaServiciosTrasladar = idServicio;
                                                                        }
                                                                        else
                                                                        {
                                                                            cadenaServiciosTrasladar = cadenaServiciosTrasladar + "," + idServicio;
                                                                        }
                                                                        m++;
                                                                    }
                                                                }


                                                                Ext.get(listView.getId()).mask('Generando Solicitud de Traslado...');

                                                                Ext.Ajax.request({
                                                                    async: false,
                                                                    url: url_generarSolicitud,
                                                                    method: 'post',
                                                                    timeout: 400000,
                                                                    params: {
                                                                        idPunto               : combo.getValue(),
                                                                        precioTraslado        : precioTraslado,
                                                                        descripcionTraslado   : descripcionTraslado,
                                                                        idsServiciosTrasladar : cadenaServiciosTrasladar,
                                                                        tipoNegocio           : tipoNegocio,
                                                                        intIdMotivo           : intIdMotivo,
                                                                        estado                : 'Todos'
                                                                    },
                                                                    success: function(response) {
                                                                        var datosRespuestaCreacionSol = Ext.JSON.decode(response.responseText);
                                                                        if (datosRespuestaCreacionSol.strStatus == "OK")
                                                                        {
                                                                            Ext.Msg.alert('Mensaje ', datosRespuestaCreacionSol.strMensaje);
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert('Error ', datosRespuestaCreacionSol.strMensaje);
                                                                        }
                                                                    },
                                                                    failure: function(result)
                                                                    {
                                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                                    }
                                                                });
                                                                storeServiciosByLogin.removeAll();
                                                                Ext.get(listView.getId()).unmask();
                                                            },
                                                            failure: function(result)
                                                            {
                                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                            }
                                                        });
                                                    }
                                                    else
                                                    {
                                                        $('#banderaAutorizarSol').val("N");
                                                        storeServiciosByLogin.removeAll();
                                                     }
                                                    });
                                                }
                                                else
                                                {
                                                    $('#banderaAutorizarSol').val("N");
                                                    storeServiciosByLogin.proxy.extraParams =
                                                    { idPunto : combo.getValue() ,
                                                      puntoId : punto_id,
                                                      estado  : 'Todos' };
                                                    storeServiciosByLogin.load({params: {}});
                                                }
                                            }
                                            else
                                            {
                                                storeServiciosByLogin.removeAll();
                                                Ext.Msg.alert('Mensaje ', strMensajeValidacionTn);
                                            }
                                                                                        
										}},
									}
								},{html:"&nbsp;",border:false,width:10}
								]}],
			renderTo: Ext.get('servicios_traslado'),
			// paging bar on the bottom
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeServiciosByLogin,
				displayInfo: true,
				displayMsg: 'Mostrando servicios {0} - {1} of {2}',
				emptyMsg: "No hay datos para mostrar"
			}),	
			store: storeServiciosByLogin,
			multiSelect: false,
			viewConfig: {
				emptyText: 'No hay datos para mostrar'
			},
			columns: [new Ext.grid.RowNumberer(),  
					{
				text: 'Servicio',
				width: 650,
				dataIndex: 'servicio',
				align: 'center',
			},{
				text: 'Estado',
				dataIndex: 'estado',
				align: 'center',
				width: 173			
			}]
		});
		
});



function enviarFormulario() {
    let formulario = document.getElementsByName("formulario")[0];
    formulario.onsubmit = validarFormulario(true);
    formulario.submit();
    Ext.MessageBox.wait("Trasladando Servicios", 'Mensaje');
}

/**
 * validarFormulario
 * 
 * Documentación para el método 'validarFormulario'.
 *
 * Valida si los servicios a trasaladar tienen un estado permitido
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.1 10-08-2016   Se agrega estado Rechazada por solicitud en ticket 37257
 * 
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
 * @version 1.2 09-09-2016   
 * Se agrega validación por registros seleccionados en el grid.
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.3 22-01-2018    Se agregan filtros para validar información de traslados TN
 * @since 1.2 
 * 
 * @since 1.0
 */
 function validarFormulario(boolEnviarFormulario)
 {
     if(boolEnviarFormulario === void 0)
     {
         boolEnviarFormulario = false;
     }
    var param    = '';
    var login    = Ext.getCmp('cmb_LOGIN').value;
    var logintxt = Ext.getCmp('cmb_LOGIN').getRawValue();
    
    if(!boolTrasladar)
    {
        alert('No existen servicios trasladables');
        return false;
    }
    
    if (login > 0) 
    {
        if (storeServiciosByLogin.getCount() > 0)
        {
            var estado = 0;
            for (var i = 0; i < storeServiciosByLogin.getCount(); i++)
            {
                var estado = storeServiciosByLogin.getAt(i).data.estado;
                var param  = param + storeServiciosByLogin.getAt(i).data.estado;
                let permiteSeleccion = storeServiciosByLogin.getAt(i).data.permiteSeleccion;
                //KJ
                //validacion de que se quite In-Corte por pedido de vrodriguez
                if (strEmpresaCod == "MD")
                {
                    //El contains no funciona en este js, por lo que no se realizará modificación alguna en esta parte respecto a servicios W+AP
                    let arrayEstados = ['In-Corte', 'Cancel', 'Reubicado', 'Anulado', 'Rechazado', 'Rechazada', 'Trasladado', 'Activo'];
                    if (!arrayEstados.includes(estado) && permiteSeleccion === "SI")
                    {
                        alert("Imposible Trasladar Servicios del Login " + logintxt +
                               ", debido a que existen servicios con estados diferentes a: " +
                               "Activo , Cancel , Reubicado , Trasladado, Rechazado, Rechazada");
                        return false;
                    }
                }
                
            }
            if (strEmpresaCod == "TN")
            {
                var precioTraslado      = document.getElementById('precioTrasladoTn').value;
                var descripcionTraslado = document.getElementById('descripcionTrasladoTn').value;
                if (precioTraslado == "" || descripcionTraslado == "")
                {
                    alert("Imposible Trasladar Servicios del Login, información financiera incompleta (Precio de traslado / Descripción de traslado)");
                    return false;
                }
                if (parseInt(precioTraslado) <= 0 || parseInt(precioTraslado) > 999)
                {
                    alert("Imposible Trasladar Servicios del Login, el precio de traslado debe ser mayor a 0 y menor 999.");
                    return false;
                }
            }
        }
        else
        {
            alert('Login escogido no tiene Servicios para Trasladar');
            return false;
        }
    }
    else 
    {
        alert('Favor Escoger un Login');
        return false;
    }
    
    if (boolProcesoContinuo){
        if (boolEnviarFormulario) 
        {
            return true;
        }
       
        let mensaje = '<b>¿Está seguro de generar el Traslado?</b> <br><br> <b>Datos del traslado</b> <br>' +
            '<b>Login Origen:</b> '+ strLoginAnterior + '<br>'+
            '<b>Dirección Origen:</b> ' + strDireccionAnterior  + '<br>'+
            '<b>Login Traslado:</b> ' + strLoginTraslado + '<br>'+
            '<b>Dirección Traslado:</b> ' + strDireccionTraslado;

        Ext.Msg.show({
            title : 'Alerta!',
            msg : mensaje,
            width : 400,
            closable : false,
            buttons : Ext.Msg.YESNO,
            defaultTextHeight: 100,
            buttonText :
                {
                    yes : 'Aceptar',
                    no : 'Cancelar'
                },
            multiline : false,
            fn : function(buttonValue){
                if (buttonValue === 'yes'){
                    enviarFormulario();
                }
            }
        });
        return false;
    }
    else
    {
        if (confirm("Esta seguro(a) de realizar el traslado de los servicios seleccionados ?"))
        {
            Ext.MessageBox.wait("Trasladando Servicios", 'Mensaje');
            return true;
        }
        else
        {
            return false;
        }
    }



}

/**
 * isNumberKey
 * 
 * Documentación para el método 'isNumberKey'.
 *
 * Función que solo permite el ingreso de numeros decimales o enteros en un campo
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 22-01-2018
 * @since 1.0
 */
function isNumberKey(txt, evt) {

    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46) {
        //Check if the text already contains the . character
        if (txt.value.indexOf('.') === -1) {
            return true;
        } else {
            return false;
        }
    } else {
        if (charCode > 31
             && (charCode < 48 || charCode > 57))
            return false;
    }
    return true;
}

function anularPunto(id) 
{
    Ext.Msg.confirm('Alerta', 'Se anulara el proceso de traslado generado hasta el momento. ¿Desea continuar?', function(btn) {
        if (btn === 'yes') 
        {
            Ext.get(document.body).mask('Anulado Traslado...');
            Ext.Ajax.request({
                url: url_anula_punto_ajax,
                method: 'post',
                params: {
                    idPunto: id,
                    strTipo: 'continuo'
                },
                success: function(response) 
                {
                    Ext.get(document.body).unmask();
                    let text = response.responseText;
                    Ext.Msg.alert('Mensaje',text , function(btn) 
                    {
                        if (btn === 'ok') 
                        {
                            Ext.get(document.body).mask('Redirigiendo al punto original...');
                            window.location = "/comercial/punto/"+intIdPuntoAnterior+"/Cliente/show";
                        }
                    });

                },
                failure: function(result)
                {
                    if (result.statusText==='Forbidden')
                    {
                        Ext.Msg.alert('Error ', 'Error: No tiene credenciales para realizar esta accion.');
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                    Ext.get(document.body).unmask();
                   
                }
            });
        }
    });
}