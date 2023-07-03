/**
 * Documentación para el método 'enlazar'.
 * @author Unknow
 * @version 1.0 Unknow
 *
 * Envia mediante post el id del Punto, id del servicio, estado y la informacion del Concentrador previamente seleccionada
 * que sera enviada al controlador para realizar la creacion o actualizacion del Enlace (Extremo-Concentrador). 
 * 
 * @param integer   idPunto          
 * @param integer   idServicio   
 * @param string    estadoServicio   
 *
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.1 28-09-2016 
 *
 * Se modifica Opcion para permitir seleccionar el TIPO DE CONCENTRADOR a escoger, de tal forma que se permite realizar
 * Enlace de Túneles híbidros por tipo categorización.
 * Se envia parametros para la busqueda de "Datos del nuevo Concentrador", permitiendo buscar entre razon Social, Direccion,
 * Login, y Tipo de Concentrador. 
 * Se modifica para que permita "Crear Nuevo" Concentrador y escoger su categorizacion o Tipo.
 * Se corrige para que la busqueda de Concentradores existentes solo busque Productos de Tipo Concentrador "Es_Concentrador" = "SI"
 */
function enlazar(idPunto,idServicio,estadoServicio){
    Ext.MessageBox.wait('Consultando datos. Favor espere..');
    Ext.Ajax.request({
        url: url_get_info_enlace_datos,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: idServicio
        },
        success: function(response){
            Ext.MessageBox.hide();
            
            var datosEnlace = Ext.JSON.decode(response.responseText);
            var disabledEnlace = (estadoServicio == 'Cancel') ? true : false;
            
            var checkboxModel = new Ext.selection.CheckboxModel( {
                checkOnly: true,
                mode: 'SINGLE',
                renderer: function(value,metaData,record,rowIndex,colIndex,store,view){
                    if (disabledEnlace)
                        return '<div>&#160;</div>';
                    else                     
                        return '<div class="x-grid-row-checker">&#160;</div>';
                }
            });
                
            var storePuntos = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url : url_get_puntos_para_enlazar,
                    reader: {
                        type: 'json',
                        root: 'data'
                    },
                    extraParams: {
                        idPunto: idPunto                        
                    }
                },
                fields:
                [
                { name:'id'   , mapping:'id'},
                { name:'login', mapping:'login'  }
                ]
            });
                        
            var storeServiciosDatosByPunto = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url : url_get_servicios_datos,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'data'
                    },
                    extraParams: {
                        idServicio:     idServicio ,
                        esConcentrador: 'SI' 
                    }
                },
                fields:
                        [
                            {name:'id'                  , mapping: 'id'                  },
                            {name:'producto'            , mapping: 'producto'            },
                            {name:'descripcionProducto' , mapping: 'descripcionProducto' },
                            {name:'ultimaMillaId'       , mapping: 'ultimaMillaId'       },
                            {name:'tipoNombreTecnico'   , mapping: 'tipoNombreTecnico'   },
                            {name:'estado'              , mapping: 'estado'              },
                            {name:'ip'                  , mapping: 'ip'                  },
                            {name:'loginAux'            , mapping: 'loginAux'            },
                            {name:'tipoEnlace'          , mapping: 'tipoEnlace'          },
                            {name:'enable'              , mapping: 'enable'              }
                        ]
            });
            
            var storeAdmiTipoMedio = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url : url_get_admi_tipo_medio,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                autoLoad: true,
                fields:
                        [
                            {name:'idTipoMedio'     , mapping: 'idTipoMedio'      },
                            {name:'nombreTipoMedio' , mapping: 'nombreTipoMedio'  }                            
                        ]
            });
            
            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 2
            });
            
            var comboTipoMedio = new Ext.form.field.ComboBox({                                    
                typeAhead: true,                            
                selectOnTab: true,
                name: 'ultimaMilla',
                valueField:'idTipoMedio',
                displayField:'nombreTipoMedio',
                store: storeAdmiTipoMedio,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                allowBlank: false
            });
            //Se agrega combo para definir e tipo de concentrador
            var storeTipoConcentrador = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url : url_get_tipos_concentradores,
                    reader: {
                        type: 'json',                        
                        root: 'encontrados'
                    },
                    extraParams: {                        
                        idServicio: idServicio
                    }
                },
                autoLoad: true,
                fields:
                [
                { name:'idTipoConcentrador' , mapping:'idTipoConcentrador'},
                { name:'tipoConcentrador'   , mapping:'tipoConcentrador'  }
                ]
            });              
            var comboTipoConcentrador = new Ext.form.field.ComboBox({                                    
                typeAhead: true,                            
                selectOnTab: true,
                name: 'nombreTipoConcentrador',
                valueField:'idTipoConcentrador',
                displayField:'tipoConcentrador',
                store: storeTipoConcentrador,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                allowBlank: false
            });
            
            var gridServicios = Ext.create('Ext.grid.Panel', {
                    collapsible: false,
                    title: 'Servicios',
                    header: 'Servicios',
                    selModel: checkboxModel,
                    store: storeServiciosDatosByPunto,
                    multiSelect: false,
                    height: 300,
                    frame: true,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar',
                        enableTextSelection: true
                    },
                    columns: [ new Ext.grid.RowNumberer(),
                    {
                        text: 'Enable',
                        dataIndex: 'enable',
                        hidden: true
                    },{
                        text: 'Producto',
                        dataIndex: 'producto',
                        align: 'center',
                        flex : 1
                    },{
                        text: 'Descripción Producto',
                        dataIndex: 'descripcionProducto',
                        align: 'center',
                        flex : 1,
                        editor: {                            
                            allowBlank: false,
                            maxLength: 200
                        }
                    },{
                        text: 'Última Milla',
                        dataIndex: 'ultimaMillaId',                        
                        align: 'center',
                        flex : 1,
                        editor: comboTipoMedio,
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) {                                                            
                                var ultimaMillaIdTmp = record.data.ultimaMillaId;
                                for (var i = 0; i < storeAdmiTipoMedio.data.items.length; i++)
                                {
                                    if (storeAdmiTipoMedio.data.items[i].data.idTipoMedio == ultimaMillaIdTmp)
                                    {                                        
                                        record.data.ultimaMillaId = storeAdmiTipoMedio.data.items[i].data.idTipoMedio;
                                        return storeAdmiTipoMedio.data.items[i].data.nombreTipoMedio;
                                    }
                                }                                
                            return value;
                        }

                    },{
                        text: 'Tipo de Concentrador',
                        dataIndex: 'tipoNombreTecnico',                        
                        align: 'center',
                        flex : 1,
                        editor: comboTipoConcentrador,
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) {                                                            
                                var tipoNombreTecnicoTmp = record.data.tipoNombreTecnico;                                        
                                for (var i = 0; i < storeTipoConcentrador.data.items.length; i++)
                                {                                   
                                    if (storeTipoConcentrador.data.items[i].data.idTipoConcentrador == tipoNombreTecnicoTmp)
                                    {                                        
                                        record.data.tipoNombreTecnico = storeTipoConcentrador.data.items[i].data.idTipoConcentrador;
                                        return storeTipoConcentrador.data.items[i].data.tipoConcentrador;
                                    }
                                }                                
                            return value;
                        }

                    },
                    {
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'center',    
                        flex : 1
                    },
                    {
                        text: 'Login Aux',
                        dataIndex: 'loginAux',
                        align: 'center',    
                        flex : 1
                    },                           
                    {
                        text: 'Tipo Enlace',
                        dataIndex: 'tipoEnlace',
                        align: 'center',    
                        flex : 1
                    }],                    
                    plugins: [cellEditing],
                    listeners: {
                        beforeedit: function(editor,e){                            
                            e.cancel = e.record.get('enable') === "false";
                        }
                    }                    
            });
            
            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 10,
                buttonAlign: 'center',
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [
                    //informacion del Enlace
                    {
                        xtype: 'container',
                        defaultType: 'textfield',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 2,
                            pack: 'center'
                        },
                        items: [

                            {
                                xtype: 'fieldset',
                                title: 'Informacion del servicio Actual',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cliente',
                                        fieldLabel: 'Cliente',
                                        displayField: datosEnlace.destino.cliente,
                                        value: datosEnlace.destino.cliente,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: datosEnlace.destino.login,
                                        value: datosEnlace.destino.login,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        displayField: datosEnlace.destino.producto,
                                        value: datosEnlace.destino.producto,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'login_aux',
                                        fieldLabel: 'Login Aux',
                                        displayField: datosEnlace.destino.loginAux,
                                        value: datosEnlace.destino.login,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'capacidadUno',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: datosEnlace.destino.capacidadUno,
                                        value: datosEnlace.destino.capacidadUno,
                                        readOnly: true,
                                        width: 150
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'capacidadDos',
                                        fieldLabel: 'Capacidad Dos',
                                        displayField: datosEnlace.destino.capacidadDos,
                                        value: datosEnlace.destino.capacidadDos,
                                        readOnly: true,
                                        width: 150
                                    },
                                    { width: '10%', border: false},

                                ]
                            },
                            {
                                xtype: 'fieldset',
                                title: 'Informacion del Concentrador Actual',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cliente',
                                        fieldLabel: 'Cliente',
                                        displayField: datosEnlace.origen.cliente,
                                        value: datosEnlace.origen.cliente,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 50, border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: datosEnlace.origen.login,
                                        value: datosEnlace.origen.login,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        displayField: datosEnlace.origen.producto,
                                        value: datosEnlace.origen.producto,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'login_aux',
                                        fieldLabel: 'Login Aux',
                                        displayField: datosEnlace.origen.loginAux,
                                        value: datosEnlace.origen.login,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'capacidadUno',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: datosEnlace.origen.capacidadUno,
                                        value: datosEnlace.origen.capacidadUno,
                                        readOnly: true,
                                        width: 150
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'capacidadDos',
                                        fieldLabel: 'Capacidad Dos',
                                        displayField: datosEnlace.origen.capacidadDos,
                                        value: datosEnlace.origen.capacidadDos,
                                        readOnly: true,
                                        width: 150
                                    },
                                    { width: '10%', border: false}
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto                    
                    {
                        xtype: 'fieldset',
                        title: 'Datos para el Nuevo Concentrador',                        
                        style: "font-weight:bold; margin-bottom: 15px;",
                        defaults: {
                            width: '500px'
                        },
                        items: [
                            {
                                xtype: 'fieldset',                                                            
                                defaults: {
                                    width: '350px'
                                },
                                style: "border: 0px;",
                                layout: {
                                    type: 'table',
                                    columns: 3,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        name: 'razonSocial',
                                        id: 'razonSocial',
                                        fieldLabel: 'Razón Social',
                                        enableKeyEvents: true,                                                            
                                        maxLength: 150,
                                        listeners: {
                                            keypress : {
                                                fn: function(field,event){
                                                    if (event.getKey() == event.ENTER){                                                        
                                                        storePuntos.proxy.extraParams = { razonSocial : field.value,
                                                                                          idPunto     : idPunto,
                                                                                          direccion   : Ext.getCmp('direccion').value
                                                                                        };
                                                        storePuntos.load(Ext.getCmp('cmb_origen_enlace').expand());                                                        
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    { width: 50, border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'direccion',
                                        id: 'direccion',
                                        fieldLabel: 'Dirección',
                                        enableKeyEvents: true,                                                            
                                        maxLength: 200,
                                        listeners: {
                                            keypress : {
                                                fn: function(field,event){
                                                    if (event.getKey() == event.ENTER){                                                        
                                                        storePuntos.proxy.extraParams = { direccion  : field.value,
                                                                                          idPunto    : idPunto,
                                                                                          razonSocial: Ext.getCmp('razonSocial').value
                                                                                        };                                                                                                        
                                                        storePuntos.load(Ext.getCmp('cmb_origen_enlace').expand()); 
                                                    }
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'combobox',
                                        id: 'cmb_origen_enlace',
                                        name: 'cmb_origen_enlace',
                                        fieldLabel: '* Login:',
                                        typeAhead: true,
                                        triggerAction: 'all',
                                        displayField:'login',
                                        queryMode: 'remote',
                                        queryParam: 'login',
                                        queryCaching: true,
                                        triggerAction: 'all',
                                        allowBlank: false,
                                        valueField: 'id',
                                        store: storePuntos,
                                        listClass: 'x-combo-list-small',
                                        labelStyle: "font-weight: bold;color:red;",
                                        forceSelection: true,
                                        emptyText: 'ingrese por lo menos 3 letras..',
                                        minChars: 4, 
                                        width: 300,
                                        listeners:{
                                            select:{
                                                fn:function(combo, value) {
                                                    storeServiciosDatosByPunto.proxy.extraParams = { idPunto        : combo.getValue(), 
                                                                                                     idServicio     :  idServicio,
                                                                                                     esConcentrador : 'SI'
                                                    };
                                                    storeServiciosDatosByPunto.load({params: {}});
                                                }
                                            }
                                        }
                                    }                                                        
                                ]
                            },
                            {
                                xtype: 'label',
                                html: '<label style="text-align: left; font-weight: normal;">( Se pueden buscar logins del mismo cliente o entre clientes, por dirección y razón social )</label>'
                            },
                            {   xtype: 'label',
                                html: '<label style="padding:10px !important;">&nbsp</label>'
                            },
                            gridServicios
                        ]                                            
                    }                    
                ],
                buttons: [
                    {
                        text: 'Guardar',
                        disabled: disabledEnlace,
                        handler: function() {
                            var idServicioOrigen    = "";
                            var txtProducto         = "";
                            var ultimaMillaId       = "";
                            var tipoNombreTecnico   = "";
                            var descripcionProducto = "";
                            var tipoEnlace          = "";
                            var loginAux            = "";
                            var origen              = Ext.getCmp('cmb_origen_enlace');
                            var selection           = gridServicios.getView().getSelectionModel().getSelection();
                            flagValidaServicio      = false;
                            
                            Ext.each(selection, function (item) {
                                //validado que solo este uno seleccionado
                                idServicioOrigen    = item.data.id;
                                txtProducto         = item.data.producto;
                                ultimaMillaId       = item.data.ultimaMillaId;
                                tipoNombreTecnico   = item.data.tipoNombreTecnico;
                                descripcionProducto = item.data.descripcionProducto;
                                tipoEnlace          = item.data.tipoEnlace;
                                loginAux            = item.data.loginAux;
                            });
                            

                            if(txtProducto == "Crear Nuevo")
                            {
                                if(Ext.isEmpty(ultimaMillaId) || Ext.isEmpty(descripcionProducto) || Ext.isEmpty(tipoNombreTecnico))
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Favor ingrese descripción de producto, última milla y Tipo de Concentrador",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    return;                            
                                }
                                else
                                {
                                    flagValidaServicio = true;
                                }                          
                            }
                            else
                            {
                                if(idServicioOrigen > 0)
                                    flagValidaServicio = true;
                            }
                            
                            if (origen.getValue() > 0 && flagValidaServicio)
                            {
                                Ext.Msg.show({
                                title:'Confirmar',
                                msg: 'Esta seguro de definir como Concentrador a <b>'+origen.getRawValue()+'</b> ?',
                                buttons: Ext.Msg.YESNOCANCEL,
                                icon: Ext.MessageBox.QUESTION,
                                buttonText: {
                                    yes: 'si', no: 'no', cancel: 'cancelar'
                                },
                                fn: function(btn){
                                    if(btn=='yes'){
                                        Ext.MessageBox.wait('Guardando datos...');
                                        Ext.Ajax.request({
                                            url: url_crear_enlace_datos,
                                            timeout: 400000,
                                            method: 'post',
                                            params: { 
                                                idPuntoOrigen       : origen.getValue(),
                                                idServicioDestino   : idServicio,
                                                idServicioOrigen    : idServicioOrigen,
                                                tipoNombreTecnico   : tipoNombreTecnico,
                                                ultimaMillaId       : ultimaMillaId,
                                                descripcionProducto : descripcionProducto,
                                                tipoEnlace          : tipoEnlace
                                            },
                                            success: function(response){
                                                Ext.MessageBox.hide();
                                                win.destroy();
                                                Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                                    if (btn === 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            },
                                            failure: function(response)
                                            {
                                                Ext.MessageBox.hide();
                                                win.destroy();
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: response.responseText,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        });
                                    }
                                }});
                            }
                            else 
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "Favor seleccione un login y servicio de la lista.",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }
                ]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Definir Concentrador',
                y : 150,
                modal: true,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
        }//cierre Success
        ,
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.statusText, function(btn){});
        }
    });       
}