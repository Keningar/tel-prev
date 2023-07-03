/* 
 * 
 * @author  Ronny Morán Chancay. <rmoranc@telconet.ec>
 * @version 1.0 23-10-2019
 * 
 * 
 * 
 */

var controlActivosPantalla;
var responseStatusProgress;
var booleanredisenioTarea = false; //bandera para tarea rediseño filopez

function registroFibraMaterial(data, callback){
    
    var msgAlerta               = 'ALERTA';
    var errorGetFibraMaterial   = 'Ocurrió un error al obtener la fibra y materiales.';
    var msgLoginNoTecnico       = 'Login ingresado no corresponde a un técnico.'; 
    var intTipoMedioId;             
    var tieneFibra;              
    var tieneMateriales;         
    var idEmpresa;
    var strEmpresaTareaCod;
    var strLogincliente;         
    var strUtilizado;            
    var strTipoCustodio;         
    var intIdDetalle;
    var intNumeroTarea; 
    var intIdServicio;
    var intDetalleSolId;
    var intPersonaId;
    var intCasoId = 0;
    var strLoginSesion;
    var numBobinaVisualizar;
    var estadoNumBobinaVisual;
    var cantidadFibraInstMd;
    var esInterdepartamental;
    var boolPantallaFibra = 'N';
    var requiereFibraTarea = 'S';
    booleanredisenioTarea = data.redisenioTarea;
    
    if(data.permiteRegistroActivos !== undefined)

    {

        tieneFibra              = data.tieneProgresoRuta;
        tieneMateriales         = data.tieneProgresoMateriales;
        idEmpresa               = 10;
        strEmpresaTareaCod      = data.strEmpresaTarea;
        strLogincliente         = data.clientes;
        intIdDetalle            = data.id_detalle;
        intNumeroTarea          = data.numero_tarea;
        intCasoId               = data.id_caso;
        numBobinaVisualizar     = data.numBobinaVisualizar;
        estadoNumBobinaVisual   = data.estadoNumBobinaVisual;
        esInterdepartamental    = data.esInterdepartamental;
        strDescripcionProducto  = data.descripcionProducto;
                
        if(intCasoId !== 0 || esInterdepartamental)
        {
            strUtilizado            = 'Soporte';
            strTipoCustodio         = 'CLIENTE';     
        }
        else
        { 
            strUtilizado            = 'Instalacion';
            strTipoCustodio         = 'EMPLEADO';  
        }
        
        if(strEmpresaTareaCod !== 'TN'){
            idEmpresa               = 18;
        }
        
        intIdServicio           = data.servicioId;
        intDetalleSolId         = null;
        intPersonaId            = data.personaId;
        intTipoMedioId          = data.tipoMedioId;
        strLoginSesion          = data.loginSesion;
        cantidadFibraInstMd     = null;
        
    }
    else
    { 
        tieneFibra              = data.tieneProgresoRuta;
        tieneMateriales         = data.tieneProgresoMateriales;
        idEmpresa               = data.idEmpresa;
        strLogincliente         = data.login;
        intNumeroTarea          = data.comunicacionId;
        intIdDetalle            = data.detalleId;
        intIdServicio           = data.idServicio;
        strEmpresaTareaCod      = "MD";
        strUtilizado            = 'Instalacion';
        strTipoCustodio         = 'EMPLEADO';
        intDetalleSolId         = data.tieneSolicitudPlanificacion;
        intPersonaId            = data.personaId;
        strLoginSesion          = data.loginSesion;
        numBobinaVisualizar     = data.numeroBobinaInstal;
        estadoNumBobinaVisual   = data.estadoNumeroBobinaInstal;
        cantidadFibraInstMd     = data.cantidadFibraInstMd;
        esInterdepartamental    = false;
        intTipoMedioId          = data.tipoMedioId;
        boolPantallaFibra       = data.boolVisualizarPantallaFibra;
        requiereFibraTarea      = data.requiereFibraTarea;
    }   
    
    if(intTipoMedioId === 2 || 
       intTipoMedioId === 104 || 
       intTipoMedioId === 105
       ){
        tieneFibra = 'SI';
    }
    
    if(intTipoMedioId === 1 && boolPantallaFibra === 'S' && requiereFibraTarea === 'N')
    {
        tieneFibra = 'SI';
    }
    
    if(intTipoMedioId === 107 && boolPantallaFibra === 'S' && requiereFibraTarea === 'N')
    {
        tieneFibra = 'SI';
    }
    
    if(intTipoMedioId === null && boolPantallaFibra === 'S' && requiereFibraTarea === 'N')
    {
        tieneFibra = 'SI';
    }

    var formPanelFMBtn = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [
                    {
                         xtype: 'container',
                         layout: 'hbox',
                         items: [
                         
						 ]                        
                      }
                ]
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [
                    {
                         xtype: 'container',
                         layout: 'hbox',
                         items: [
                                {
                                    xtype: 'textfield',
                                    name: 'loginTecnicoIn',
                                    id:'loginTecnicoIn',
                                    fieldLabel: 'Login técnico',
                                    allowBlank: false,
                                    flex: 1,
                                    style: 'margin: 20px'
                                }, 
                                {
                                    xtype: 'button',
                                    name: 'search',
                                    fieldLabel: 'Buscar',
                                    text: 'Buscar',
                                    style: 'margin: 20px',
                                    width: 50,
                                    handler: function (button, e){
                                        
                                        var msgLoginTecnico             = 'Favor ingrese login del técnico';
                                        var titleErrorPutFibraMaterial  = 'ALERTA';
                                        var strloginTecnico             = Ext.getCmp('loginTecnicoIn').getValue().toLowerCase();
                                        
                                        var arrayGetActivos             = 
                                        {
                                            idEmpresa:              idEmpresa,
                                            strUtilizado:           strUtilizado,  
                                            strTipoCustodio:        strTipoCustodio,
                                            strloginTecnico:        strloginTecnico            
                                        };

                                        if(strloginTecnico === '' || strloginTecnico === null){
                                            Ext.Msg.alert(titleErrorPutFibraMaterial,msgLoginTecnico);
                                        }else
                                        {

                                            Ext.get('registro_activos').mask('Consultando...');
                                            var urlGetActivos = '/tecnico/clientes/getFibraMaterialesTecnico';
                                            
                                            Ext.Ajax.request({
                                                url: urlGetActivos,
                                                method: 'post',
                                                timeout: 400000,
                                                params : {
                                                    data: Ext.encode(arrayGetActivos)
                                                },
                                                success: function(response){

                                                    Ext.get('registro_activos').unmask();
                                            
                                                    var containerFM = Ext.create('Ext.container.Container', {
                                                                            xtype: 'container',
                                                                            layout: 'hbox',
                                                                            align: 'stretch',
                                                                            items: [

                                                                            ]
                                                       });      

                                                    var json                            = Ext.JSON.decode(response.responseText);
                                                    var datosMateFibraEquip             = json.data;
                                                    var mensajeMateFibraEquip           = json.mensaje;
                                                    //Limpiando Panel 
                                                    formPanelFMBtn.removeAll();                                                    
                                                    if(mensajeMateFibraEquip === 'Login no tecnico')
                                                    {
                                                        Ext.Msg.alert(msgAlerta, msgLoginNoTecnico);
                                                    }
                                                    else
                                                    {
                                                        if(datosMateFibraEquip !== null)
                                                        {
                                                            var lstMaterialesFibraNaf       = datosMateFibraEquip.materialesFibraNaf;
                                                            var lstmaterialesMaterialesNaf  = datosMateFibraEquip.materialesMaterialesNaf;
                                                            var idOficina                   = datosMateFibraEquip.idOficina;
                                                            var idPersonaRol                = datosMateFibraEquip.idPersonaRol;
                                                            var idDepartamento              = datosMateFibraEquip.idDepartamento;                                                            
                                                            var nombresTecnico              = datosMateFibraEquip.nombresTecnico;


                                                            var containerInfoLogin          = Ext.create('Ext.container.Container', {
                                                                                                            xtype: 'container',
                                                                                                            layout : {
                                                                                                               type : 'hbox',
                                                                                                               pack : 'left'
                                                                                                            },
                                                                                                            align: 'stretch',
                                                                                                            items: [
                                                                                                                {   xtype: 'label',
                                                                                                                    name : 'nombresLoginTitle',
                                                                                                                    html: 'Nombres:',
                                                                                                                    style: 'margin: 20px'
                                                                                                                },
                                                                                                                {
                                                                                                                    xtype: 'label',
                                                                                                                    name : 'nombresLoginInfo',
                                                                                                                    html: nombresTecnico,
                                                                                                                    style: 'margin: 20px'
                                                                                                                }
                                                                                                            ]
                                                                            });

                                                            formPanelFMBtn.add(containerInfoLogin);

                                                            //Fibra
                                                            var arrayViewFibra = 
                                                            {
                                                                lstMaterialesFibraNaf:      lstMaterialesFibraNaf,
                                                                containerFM:                containerFM,
                                                                numBobinaVisualizar:        numBobinaVisualizar,
                                                                estadoNumBobinaVisual:      estadoNumBobinaVisual
                                                            };
                                                            
                                                            if(tieneFibra === 'NO'){
                                                                viewFibraTecnico(arrayViewFibra);
                                                            }

                                                            //Materiales
                                                            var arrayViewMateriales = 
                                                            {
                                                                lstmaterialesMaterialesNaf:  lstmaterialesMaterialesNaf,
                                                                containerFM:                 containerFM
                                                            };

                                                            if(tieneMateriales === 'NO'){
                                                                viewMaterialesTecnico(arrayViewMateriales);
                                                            }
                                                                    
                                                            
                                                            formPanelFMBtn.add(containerFM);

                                                            if(tieneFibra === 'NO'|| tieneMateriales === 'NO'){
                                                                
                                                                var containerBtnGuardar = Ext.create('Ext.container.Container', {

                                                                                    xtype: 'container',
                                                                                    layout : {
                                                                                       type : 'hbox',
                                                                                       pack : 'center'
                                                                                    },
                                                                                    align: 'stretch',
                                                                                    items: [
                                                                                       {
                                                                                           xtype: 'button',
                                                                                           text: 'Guardar',
                                                                                           width: 150,
                                                                                           style: 'margin: 20px',
                                                                                           handler: function (button, e) {  
                                                                                               
                                                                                               var arrayValidaGuardarActivos = 
                                                                                                   {
                                                                                                        lstmaterialesMaterialesNaf: lstmaterialesMaterialesNaf,
                                                                                                        tieneFibra:                 tieneFibra,
                                                                                                        tieneMateriales:            tieneMateriales,
                                                                                                        lstMaterialesFibraNaf:      lstMaterialesFibraNaf
                                                                                                   };
                                                                                               
                                                                                               if(validarEnvioActivos(arrayValidaGuardarActivos)){
                                                                                                   
                                                                                                   

                                                                                                   var arrayGuardarActivos = 
                                                                                                   {
                                                                                                       lstmaterialesMaterialesNaf:  lstmaterialesMaterialesNaf,
                                                                                                       lstMaterialesFibraNaf:       lstMaterialesFibraNaf,
                                                                                                       strLogincliente:             strLogincliente,
                                                                                                       idOficina:                   idOficina,
                                                                                                       idPersonaRol:                idPersonaRol,
                                                                                                       idDepartamento:              idDepartamento,
                                                                                                       tieneFibra:                  tieneFibra,
                                                                                                       tieneMateriales:             tieneMateriales,
                                                                                                       idEmpresa:                   idEmpresa,
                                                                                                       strEmpresaTareaCod:          strEmpresaTareaCod,
                                                                                                       strloginTecnico:             strloginTecnico,
                                                                                                       intIdServicio:               intIdServicio,
                                                                                                       intDetalleSolId:             intDetalleSolId,
                                                                                                       intIdDetalle:                intIdDetalle,
                                                                                                       strUtilizado:                strUtilizado,
                                                                                                       intNumeroTarea:              intNumeroTarea,
                                                                                                       intPersonaId:                intPersonaId,
                                                                                                       intCasoId:                   intCasoId,
                                                                                                       strLoginSesion:              strLoginSesion,
                                                                                                       esInterdepartamental:        esInterdepartamental
                                                                                                       
                                                                                                   };
                                                                                                   
                                                                                                   
                                                                                                   
                                                                                                   if (cantidadFibraInstMd !== null && tieneFibra === 'NO'){
                                                                                                       
                                                                                                       var intPuntaInicial             = Ext.getCmp('puntai_num').getValue();    
                                                                                                       var intPuntaFinal               = Ext.getCmp('puntaf_num').getValue(); 
                                                                                                       var diferenciaPuntas;
                                                                                                   
                                                                                                       diferenciaPuntas = intPuntaInicial - intPuntaFinal;
                                                                                                        
                                                                                                        if(diferenciaPuntas > cantidadFibraInstMd){
                                                                                                        var cantidadExcedenteMd     = diferenciaPuntas - cantidadFibraInstMd ;
                                                                                                        var msgCantidadExcedente    = "Tiene excedente de "+ cantidadExcedenteMd +" metros de Fibra. ¿Desea continuar ?";
                                                                                                        
                                                                                                        arrayGuardarActivos['cantidadExcedenteMd'] = cantidadExcedenteMd;
                                                                                                        
                                                                                                        Ext.MessageBox.show({
                                                                                                            title: 'Mensaje',
                                                                                                            msg: msgCantidadExcedente,
                                                                                                            buttons: Ext.MessageBox.OKCANCEL,
                                                                                                            icon: Ext.MessageBox.WARNING,
                                                                                                            fn: function(btn) {
                                                                                                                if (btn === 'ok') {
                                                                                                                    guardarFibraMateriales(arrayGuardarActivos,callback);
                                                                                                                } else {
                                                                                                                    return;
                                                                                                                }
                                                                                                            }
                                                                                                        });
                                                                                                            
                                                                                                       }else{
                                                                                                           guardarFibraMateriales(arrayGuardarActivos,callback);
                                                                                                       }
                                                                                                   }
                                                                                                   else{
                                                                                                       guardarFibraMateriales(arrayGuardarActivos,callback);   
                                                                                                   }

                                                                                               }
                                                                                           }						
                                                                                       }
                                                                                   ]
                                                                       });

                                                                
                                                            }
                                                            formPanelFMBtn.add(containerBtnGuardar);

                                                            formPanel.add(formPanelFMBtn);
                                                            
                                                        }
                                                        else{
                                                            Ext.Msg.alert(msgAlerta, errorGetFibraMaterial);
                                                        }

                                                        
                                                    }
                                                }//cierre response
                                            });       
                                        
                                        }
                                        
                                    
                                    }
                                }
                         ]                        
                      }
                ],
                buttons: [
                {
                    text: 'Cancelar',
                    handler: function(){
                        controlActivosPantalla.destroy();

                    }
                }]
    });
    
    controlActivosPantalla = Ext.create('Ext.window.Window', {
                id: 'registro_activos',
                title: 'Registro de Activos',
                modal: true,
                width: 680,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();    

}

function viewMaterialesTecnico(arrayViewMateriales){
    
    var lstmaterialesMaterialesNaf  = arrayViewMateriales.lstmaterialesMaterialesNaf;
    var containerFM                 = arrayViewMateriales.containerFM;
    var strNotieneMateriales        = 'No tiene materiales para esta tarea';
    
    var containerMaterial           = Ext.create('Ext.form.FieldSet', {
                                        xtype: 'fieldset',
                                        title: 'Materiales',
                                        margins:'0 0 5 0',
                                        flex:1,
                                        layout: 'anchor',
                                        //autoHeight: true,
                                        autoScroll: true,
                                        height: 270, 
                                        items: [

                                        ]                                  
    });
            
    if(lstmaterialesMaterialesNaf.length >0)
    {
        for (var i = 0; i < lstmaterialesMaterialesNaf.length; i++)
        {
                containerMaterial.add(new Ext.form.Number({
                    id: '' +lstmaterialesMaterialesNaf[i].id_control,
                    value: '0',
                    name: '' +lstmaterialesMaterialesNaf[i].id_control,
                    fieldLabel: '' +lstmaterialesMaterialesNaf[i].nombre_material,
                    labelWidth: 180,
                    anchor: '98%',
                    allowBlank: false,
                    maxValue: 999,
                    minValue: 0
                }));
        }
    }
    else
    {
        containerMaterial.add(new Ext.form.Label({
            text: strNotieneMateriales,
            style: {
              'text-align': 'center'
            }
        }));
    }
    //containerMaterial.doLayout();
    containerFM.add(containerMaterial);
}

function viewFibraTecnico(arrayViewFibra){
   
    var lstMaterialesFibraNaf   = arrayViewFibra.lstMaterialesFibraNaf;
    var containerFM             = arrayViewFibra.containerFM;
    var numBobinaVisualizar     = arrayViewFibra.numBobinaVisualizar;
    var estadoNumBobinaVisual   = arrayViewFibra.estadoNumBobinaVisual;
    
    var arrayFibras             = [];
    var dataContainerFibra      = [];
    var strNotieneFibra         = 'No tiene fibra para esta tarea';
    var strCodFibraAutogen      = '00-00-00-000';  
    var strColorBloqueado       = '#E1E1E1';
    var strBloqueada            = 'Bloqueada';
    var strColorTextBloqueado   = '#929292';
                
    
    if(lstMaterialesFibraNaf.length > 0)
    {
        for (var i = 0; i < lstMaterialesFibraNaf.length; i++)
        {
            var strColorItemCombo       = '#AEAEAEs';
            var strColorText            = '#353535';
            
            if(lstMaterialesFibraNaf[i].bobinaAsignada === strBloqueada)
            {
                strColorItemCombo   = strColorBloqueado;
                strColorText        = strColorTextBloqueado;
            }
            var objFibra = {
                abbr:               '' +lstMaterialesFibraNaf[i].idControl,
                name:               '' +lstMaterialesFibraNaf[i].articuloId,
                cantidad:           '' +lstMaterialesFibraNaf[i].cantidad,
                bobinaAsignada:     '' +lstMaterialesFibraNaf[i].bobinaAsignada,
                color:              strColorItemCombo,
                colortext:          strColorText
            };
            
            if(lstMaterialesFibraNaf[i].codMaterial !== strCodFibraAutogen)
            {
                if((estadoNumBobinaVisual === 'Activo'))
                {
                    if(arrayFibras.length < numBobinaVisualizar)
                    {
                        arrayFibras.push(objFibra);  
                    }
                }
                else
                {
                    arrayFibras.push(objFibra); 
                }
            }
        }

        var arrayBobinasCmb = Ext.create('Ext.data.Store', {
                                     fields: ['abbr', 'name', 'cantidad', 'bobinaAsignada', 'color', 'colortext'],
                                     data: arrayFibras
                              });

        dataContainerFibra = 
        [
            {
                xtype: 'combobox',
                fieldLabel: 'Bobina',
                labelWidth: 120,
                anchor: '98%',
                store: arrayBobinasCmb,
                queryMode: 'local',
                displayField: 'name',
                valueField: 'abbr',
                id: 'cmbBobina',
                forceSelection: false,
                multiSelect: false,
                typeahead: true,
                allowBlank: false,
                editable: false,
                tpl: [
                    '<tpl for=".">',
                    '<li class="x-boundlist-item listItmes" style="background-color:{color}; color: {colortext}">{name}</li>',
                    '</tpl>'
                ],
                listeners:{
                    select:{
                        fn:function(combo, value) {
                            $('input[name="lblCantidad"]').val(combo.valueModels[0].data.cantidad);
                        }
                    },
                    beforeselect: function (combo, record, index, eopts) {
                        if (record.get('bobinaAsignada') === strBloqueada) 
                        {
                            return false;
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                name: 'lblCantidad',
                id:'lblCantidad',
                labelWidth: 120,
                anchor: '98%',
                fieldLabel: 'Cantidad disponible',
                allowBlank: false,
                readOnly: true
            },
            {
                 xtype: 'numberfield',
                 labelWidth: 120,
                 fieldLabel: 'Punta Inicial:',
                 anchor: '98%',
                 name: 'puntai_num',
                 id: 'puntai_num',
                 allowBlank: false,
                 maxValue: 9999,
                 minValue: 0
            },
            {
                 xtype: 'numberfield',
                 labelWidth: 120,
                 fieldLabel: 'Punta Final:',
                 anchor: '98%',
                 name: 'puntaf_num',
                 id: 'puntaf_num',
                 allowBlank: false,
                 maxValue: 9999,
                 minValue: 0
            }     
        ];
    }
    else
    {
        dataContainerFibra = 
        [
            {
                 xtype: 'label',
                 labelWidth: '98%',
                 text: strNotieneFibra,
                 anchor: '98%',
                 name: 'puntaf_num',
                 forId: 'myFieldId',

            }     
        ];
    }
    
    var containerFibra = Ext.create('Ext.form.FieldSet', {
                                       xtype: 'fieldset',
                                       flex:1,
                                       title: 'Fibra',
                                       margins:'0 5 0 0',
                                       layout: 'anchor',                          
                                       autoHeight: true,
                                       items: dataContainerFibra                  
                });
    //containerFibra.doLayout();
    containerFM.add(containerFibra);
}


function guardarFibraMateriales(arrayGuardarActivos, callback){
    
    var lstmaterialesMaterialesNaf      = arrayGuardarActivos.lstmaterialesMaterialesNaf;
    var lstMaterialesFibraNaf           = arrayGuardarActivos.lstMaterialesFibraNaf;
    var strLogincliente                 = arrayGuardarActivos.strLogincliente;
    var idOficina                       = arrayGuardarActivos.idOficina;
    var idDepartamento                  = arrayGuardarActivos.idDepartamento;
    var idPersonaRol                    = arrayGuardarActivos.idPersonaRol;
    var tieneFibra                      = arrayGuardarActivos.tieneFibra;
    var tieneMateriales                 = arrayGuardarActivos.tieneMateriales;
    var idEmpresa                       = arrayGuardarActivos.idEmpresa;
    var strEmpresaTareaCod              = arrayGuardarActivos.strEmpresaTareaCod;
    var strloginTecnico                 = arrayGuardarActivos.strloginTecnico;
    var intIdServicio                   = arrayGuardarActivos.intIdServicio;
    var intDetalleSolId                 = arrayGuardarActivos.intDetalleSolId;
    var intIdDetalle                    = arrayGuardarActivos.intIdDetalle; 
    var arrayMaterialFibraFact          = [];
    var arraycontrolCustodio            = [];
    var strUtilizado                    = arrayGuardarActivos.strUtilizado;
    var intNumeroTarea                  = arrayGuardarActivos.intNumeroTarea;
    var intPersonaId                    = arrayGuardarActivos.intPersonaId;
    var intCasoId                       = arrayGuardarActivos.intCasoId;
    var msgErrorPutFibraMaterial        = 'Ocurrió un error al guardar, vuelva a intentar.';
    var titleErrorPutFibraMaterial      = 'ALERTA';
    var strOpMetodo                     = 'putMaterialesInstalacion';
    var strTipoServicio                 = '';
    var strObservacion                  = 'Proceso ejecutado desde Telcos+';
    var strLoginSesion                  = arrayGuardarActivos.strLoginSesion;
    var strCliente                      = 'CLIENTE';
    var strRecibe                       = '';
    var cantidadExcedenteMd             = 0;
    
    
    
    if(strUtilizado ===  'Instalacion')
    {
        strTipoServicio     = 'NUEVO';
        strRecibe           = strCliente;
    }
    else
    {
        strTipoServicio     = 'SOPORTE';
        strRecibe           = strCliente;
    }
    
    if(arrayGuardarActivos.cantidadExcedenteMd !== undefined){
        cantidadExcedenteMd = arrayGuardarActivos.cantidadExcedenteMd;
    }
    
    
        if(lstmaterialesMaterialesNaf.length > 0)
        {
            for ( var i = 0; i < lstmaterialesMaterialesNaf.length; i++)
            {
                if(tieneMateriales === 'NO'){
                var intCantidadMaterial = Ext.getCmp(lstmaterialesMaterialesNaf[i].id_control).getValue(); 
                
                        var objMaterialGuardarFact = {
                            cantidad_cliente: parseFloat(intCantidadMaterial+'.0'),
                            cantidad_empresa: lstmaterialesMaterialesNaf[i].cantidad_empresa,
                            cantidad_estimada: lstmaterialesMaterialesNaf[i].cantidad_estimada,
                            cantidad_excedente: parseFloat(lstmaterialesMaterialesNaf[i].cantidad_excedente+'.0'),
                            cantidad_usada: parseFloat(intCantidadMaterial+'.0'),
                            cod_material: lstmaterialesMaterialesNaf[i].cod_material,
                            costo_material:'$ '+lstmaterialesMaterialesNaf[i].costo_material+'.0',
                            custodioId: lstmaterialesMaterialesNaf[i].custodioId,
                            empresaId: lstmaterialesMaterialesNaf[i].empresaId,
                            facturar:lstmaterialesMaterialesNaf[i].facturar,
                            id_detalle_sol_material:lstmaterialesMaterialesNaf[i].id_detalle_sol_material,
                            id_tarea_material:lstmaterialesMaterialesNaf[i].id_tarea_material,
                            id_control: lstmaterialesMaterialesNaf[i].id_control,
                            login:lstmaterialesMaterialesNaf[i].login,
                            no_articulo:lstmaterialesMaterialesNaf[i].no_articulo,
                            nombre_material: lstmaterialesMaterialesNaf[i].nombre_material,
                            precio_venta_material:'$ '+lstmaterialesMaterialesNaf[i].precio_venta_material+'.0',
                            subgrupo_material: lstmaterialesMaterialesNaf[i].subgrupo_material,
                            tipoActividad:lstmaterialesMaterialesNaf[i].tipoActividad,
                            tipoTransaccionId:lstmaterialesMaterialesNaf[i].tipoTransaccionId,
                            tipo_articulo: lstmaterialesMaterialesNaf[i].tipo_articulo,
                            transaccionId:lstmaterialesMaterialesNaf[i].transaccionId
                        };

                        var objMaterialGuardarCc   = {
                                                        cantidadEnt: parseFloat(intCantidadMaterial+'.0'),
                                                        cantidadRec: parseFloat(intCantidadMaterial+'.0'),
                                                        empresaId: lstmaterialesMaterialesNaf[i].empresaId,
                                                        idControl: lstmaterialesMaterialesNaf[i].id_control,
                                                        login: strLogincliente,
                                                        loginEmpleado: lstmaterialesMaterialesNaf[i].login,
                                                        numeroSerie: lstmaterialesMaterialesNaf[i].cod_material,
                                                        tipoActividad: lstmaterialesMaterialesNaf[i].tipoActividad,
                                                        tipoArticulo: lstmaterialesMaterialesNaf[i].tipo_articulo,
                                                        tipoTransaccion: strTipoServicio,////tareaSeleccionada.getTipoServicio()
                                                        transaccionId: lstmaterialesMaterialesNaf[i].transaccionId
                        };

                        arrayMaterialFibraFact.push(objMaterialGuardarFact);  
                        arraycontrolCustodio.push(objMaterialGuardarCc);  
                    
                }
            }
            
        }
        
        var arrayMateriales = 
        {
          total:        lstmaterialesMaterialesNaf.length,
          materiales:   arrayMaterialFibraFact
        };

        if(lstMaterialesFibraNaf.length > 0)
        {
            if(tieneFibra === 'NO')
            {
            var cmbBobinaSelect             = Ext.getCmp('cmbBobina').getValue();    
            var intPuntaInicial             = Ext.getCmp('puntai_num').getValue();    
            var intPuntaFinal               = Ext.getCmp('puntaf_num').getValue();               
            var intfibraManual              = intPuntaInicial - intPuntaFinal;
            
                for (var j = 0; j < lstMaterialesFibraNaf.length; j++)
                {
                    if(cmbBobinaSelect === lstMaterialesFibraNaf[j].idControl){
                        var objFibraGuardarFact = {
                            cantidad_cliente: parseFloat(intfibraManual+'.00'),
                            cantidad_empresa: parseInt(lstMaterialesFibraNaf[j].cantidad),
                            cantidad_estimada: 0,//cero!
                            cantidad_excedente: parseFloat(cantidadExcedenteMd+'.0'),
                            cantidad_usada: parseFloat(intfibraManual+'.00'),
                            cod_material: lstMaterialesFibraNaf[j].codMaterial,
                            empresaId: lstMaterialesFibraNaf[j].empresaId,
                            id_control: parseInt(lstMaterialesFibraNaf[j].idControl),
                            nombre_material: lstMaterialesFibraNaf[j].nombreMaterial,
                            subgrupo_material: lstMaterialesFibraNaf[j].subgrupoMaterial,
                            tipo_articulo: lstMaterialesFibraNaf[j].tipoArticulo
                        };

                        var objFibraGuardarCc   = {
                                                        cantidadEnt: parseFloat(intfibraManual+'.0'),
                                                        cantidadRec: parseFloat(intfibraManual+'.0'),
                                                        caracteristicaId: lstMaterialesFibraNaf[j].idCaracteristica,
                                                        casoId: intCasoId, //para fibra
                                                        empresaId: lstMaterialesFibraNaf[j].empresaId,
                                                        idControl: lstMaterialesFibraNaf[j].idControl,
                                                        login: strLogincliente,
                                                        loginEmpleado: lstMaterialesFibraNaf[j].login,
                                                        numeroSerie: lstMaterialesFibraNaf[j].articuloId,
                                                        tipoActividad: strTipoServicio,
                                                        tipoArticulo: lstMaterialesFibraNaf[j].tipoArticulo,
                                                        tipoTransaccion: strTipoServicio,
                                                        transaccionId: lstMaterialesFibraNaf[j].transaccionId
                                                };
                        arrayMaterialFibraFact.push(objFibraGuardarFact);  
                        arraycontrolCustodio.push(objFibraGuardarCc);  
                    }
                }
            }
            
        }

        var jsonDataRequestFM = 
        {
            materiales:         arrayMateriales,
            codEmpresa:         idEmpresa,
            idDepartamento:     idDepartamento,
            idOficina:          idOficina,
            prefijoEmpresa:     strEmpresaTareaCod,
            detSolicitudId:     intDetalleSolId,
            idDetalle:          intIdDetalle,
            controlCustodio:    arraycontrolCustodio,
            custodioEntregaId:  idPersonaRol,
            tareaEmpresaId:     idEmpresa,
            recibe:             strRecibe,
            cantidadExcedida:   parseFloat('0.0'),
            custodioRecibeId:   intPersonaId,
            observacion:        strObservacion,
            tipoActividad:      strUtilizado
        };
        
        var dataJsonRequest = 
        {
            data: jsonDataRequestFM,
            op: strOpMetodo,
            user: strLoginSesion
        };
    
    
    Ext.get('registro_activos').mask('Registrando Activos...');
    Ext.Ajax.request({
        url: '../../rs/tecnico/ws/rest/procesar',
        method: 'post',
        timeout: 400000,
        headers: { 'Content-Type': 'application/json' },
        params : Ext.JSON.encode(dataJsonRequest),
        success: function(conn, response, options, eOpts) {
    
                Ext.get('registro_activos').unmask();

                var result = Ext.JSON.decode(conn.responseText);
                var status = result.status;
                if(status === null || status === 'ERROR' || status === 206)
                {
                    Ext.Msg.alert(titleErrorPutFibraMaterial,msgErrorPutFibraMaterial);     
                }
                else
                {    
                    //Insertar Progreso
                    var strCodigoTipoProgreso;
                    var intTotalProgresosIn  = 0;
                    
                    if(tieneFibra === 'NO')
                    {
                        strCodigoTipoProgreso = 'INGRESO_FIBRA';
                        intTotalProgresosIn++;   
                        var arrayGuardarProgresoRuta =
                        {
                            strCodEmpresa:          idEmpresa,
                            intNumeroTarea:         intNumeroTarea,
                            intIdDetalle:           intIdDetalle,
                            strCodigoTipoProgreso:  strCodigoTipoProgreso,
                            intIdServicio:          intIdServicio,
                            strloginTecnico:        strloginTecnico,
                            intTotalProgresosIn:    intTotalProgresosIn,
                            tieneFibra:             tieneFibra,
                            tieneMateriales:        tieneMateriales,
                            strTipoServicio:        strTipoServicio,
                            strLoginSesion:         strLoginSesion
                        };

                        guardarProgreso(arrayGuardarProgresoRuta, callback);

                    }
                    if(tieneMateriales === 'NO')
                    {
                        strCodigoTipoProgreso = 'INGRESO_MATERIALES';
                        intTotalProgresosIn++;   
                        var arrayGuardarProgresoMat =
                        {
                            strCodEmpresa:          idEmpresa,
                            intNumeroTarea:         intNumeroTarea,
                            intIdDetalle:           intIdDetalle,
                            strCodigoTipoProgreso:  strCodigoTipoProgreso,
                            intIdServicio:          intIdServicio,
                            strloginTecnico:        strloginTecnico,
                            intTotalProgresosIn:    intTotalProgresosIn,
                            tieneFibra:             tieneFibra,
                            tieneMateriales:        tieneMateriales,
                            strTipoServicio:        strTipoServicio,
                            strLoginSesion:         strLoginSesion
                        };
                        guardarProgreso(arrayGuardarProgresoMat, callback);

                    }
                    
                }
            },
        failure: function(conn, response, options, eOpts) {
                Ext.Msg.alert(titleErrorPutFibraMaterial,msgErrorPutFibraMaterial);
        }
    });
    
}

function validarEnvioActivos(arrayValidaGuardarActivos){
        
        var lstmaterialesMaterialesNaf  = arrayValidaGuardarActivos.lstmaterialesMaterialesNaf;       
        var tieneFibra                  = arrayValidaGuardarActivos.tieneFibra;       
        var tieneMateriales             = arrayValidaGuardarActivos.tieneMateriales; 
        var msgLlenarCamposF            = 'Favor complete los campos en sección Fibra';
        var msgPuntas                   = 'Favor verificar las puntas ingresadas';
        var msgPuntasDiferencia         = 'La diferencia entre la punta inicial y la punta final no debe exceder los 2000 metros';
        var msgVerificarCamposM         = 'Favor verificar los campos en sección Materiales';
        var msgVerificarCantidades      = 'Cantidades de materiales deben ser mayores a 0';
        var titleErrorPutFibraMaterial  = 'ALERTA';
        var intCantidadSuma             = 0;
                
        if(tieneFibra === 'NO'){
            var cmbBobinaSelect             = Ext.getCmp('cmbBobina').getValue();    
            var intPuntaInicial             = Ext.getCmp('puntai_num').getValue();    
            var intPuntaFinal               = Ext.getCmp('puntaf_num').getValue();  
            var diferenciaPuntas;
            
            if((intPuntaInicial  === undefined || intPuntaInicial === null) ||
                (intPuntaFinal   === undefined || intPuntaFinal   === null) ||
                (cmbBobinaSelect === undefined || cmbBobinaSelect === null)){

                Ext.Msg.alert(titleErrorPutFibraMaterial,msgLlenarCamposF);
                return false;
            } 
            
            if(intPuntaFinal > intPuntaInicial){
                Ext.Msg.alert(titleErrorPutFibraMaterial,msgPuntas);
                    return false;
            }
            
            diferenciaPuntas = intPuntaInicial - intPuntaFinal;
            
            if(diferenciaPuntas >= 2000){
                Ext.Msg.alert(titleErrorPutFibraMaterial,msgPuntasDiferencia);
                    return false;
            }
            
            
        }
        
        
        if(tieneMateriales === 'NO'){
            for (var i = 0; i < lstmaterialesMaterialesNaf.length; i++)
            {
                var intMaterialCant = Ext.getCmp(lstmaterialesMaterialesNaf[i].id_control).getValue(); 
                if(intMaterialCant === null || intMaterialCant === undefined){
                    Ext.Msg.alert(titleErrorPutFibraMaterial,msgVerificarCamposM);
                    return false;
                }
                intCantidadSuma = intCantidadSuma + intMaterialCant;
            }
            if(intCantidadSuma <= 0 )
            {
                Ext.Msg.alert(titleErrorPutFibraMaterial,msgVerificarCantidades);
                return false;
            }     
        }
        
        return true;
}


function guardarProgreso(arrayGuardarProgreso, callback){
    
    var strCodEmpresa           = arrayGuardarProgreso.strCodEmpresa;
    var intNumeroTarea          = arrayGuardarProgreso.intNumeroTarea;
    var intIdDetalle            = arrayGuardarProgreso.intIdDetalle;
    var strCodigoTipoProgreso   = arrayGuardarProgreso.strCodigoTipoProgreso;
    var intIdServicio           = arrayGuardarProgreso.intIdServicio;
    var intTotalProgresosIn     = arrayGuardarProgreso.intTotalProgresosIn;
    var tieneFibra              = arrayGuardarProgreso.tieneFibra;
    var tieneMateriales         = arrayGuardarProgreso.tieneMateriales;
    var strTipoServicio         = arrayGuardarProgreso.strTipoServicio;
    var strProgresoGuardado     = 'Se ingresó el progreso de la tarea!';
    var strProgresSaveAlert     = 'No se pudo guardar el progreso, vuelva a intentar';
    var strOpMetodo             = 'putIngresarProgresoTarea';
    var titleAlertProgreso      = 'Alerta';
    var sucessMsjTitle          = 'Éxito';
    var sucessMsjContent        = 'Activos ingresados correctamente';
    var strLoginSesion          = arrayGuardarProgreso.strLoginSesion;
    
	var jsonDataRequest = 
        {
            strCodEmpresa:          parseInt(strCodEmpresa),
            intIdTarea:             intNumeroTarea,
            intIdDetalle:           parseInt(intIdDetalle),
            strCodigoTipoProgreso:  strCodigoTipoProgreso,
            intIdServicio:          intIdServicio,
            strOrigen:              'WEB'
        };
        
        var dataJsonRequest = 
        {
            data:   jsonDataRequest,
            op:     strOpMetodo,
            user:   strLoginSesion
        };

            
    Ext.get('registro_activos').mask('Ingresando Progreso...');
    Ext.Ajax.request({
        url: '../../rs/soporte/ws/rest/procesar',
        method: 'post',
        timeout: 400000,
        headers: { 'Content-Type': 'application/json' },
        params : Ext.JSON.encode(dataJsonRequest),
        success: function(conn, response, options, eOpts){

                Ext.get('registro_activos').unmask();

                var result = Ext.JSON.decode(conn.responseText);
                var mensaje = result.mensaje;
                
                if(mensaje === strProgresoGuardado)
                {
                   
                   if((intTotalProgresosIn === 1 && tieneMateriales !== 'NO')||
                            (intTotalProgresosIn === 1 && tieneFibra !== 'NO')||
                            (intTotalProgresosIn === 2 && tieneMateriales === 'NO' && tieneFibra === 'NO'))
                    {   
                         Ext.Msg.alert(sucessMsjTitle, sucessMsjContent, function(btn, text) {
                            if (btn == "ok")
                            {
                                responseStatusProgress = 'OK';
                                if(strTipoServicio === 'SOPORTE'){
                                     callback(responseStatusProgress);    
                                     controlActivosPantalla.destroy();
                                }else{
                                    controlActivosPantalla.destroy();
                                    if(booleanredisenioTarea)
                                    {
                                        gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                    }else
                                    {
                                        store.load();
                                    }
                                }                                
                            }
                          });
                    }
                }
                else
                {   
                    responseStatusProgress = 'ERROR';
                    Ext.Msg.alert(titleAlertProgreso,strProgresSaveAlert);
                }

            }
    });
}
