Ext.require([
    '*'
]);

Ext.onReady(function(){
  
    var connActualizaDetPago = new Ext.data.Connection({
	    listeners: {
		    'beforerequest': {
			    fn: function (con, opt) {						
				    Ext.MessageBox.show({
				      msg: 'Grabando los datos, Por favor espere!!',
				      progressText: 'Saving...',
				      width:300,
				      wait:true,
				      waitConfig: {interval:200}
				    });
				    //Ext.get(document.body).mask('Loading...');
			    },
			    scope: this
		    },
		    'requestcomplete': {
			    fn: function (con, res, opt) {
				    Ext.MessageBox.hide();
				    //Ext.get(document.body).unmask();
			    },
			    scope: this
		    },
		    'requestexception': {
			    fn: function (con, res, opt) {
				    Ext.MessageBox.hide();
				    //Ext.get(document.body).unmask();
			    },
			    scope: this
		    }
	    }
    });
    
    Ext.define('InfoPagoDetModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPago', type: 'string'},
	    {name: 'idPagoDet', type: 'string'},
            {name: 'formaPago', type: 'string'},
	    {name: 'feDeposito', type: 'string'},
            {name: 'factura', type:'string'},
            {name: 'tipoCuenta', type: 'string'},
            {name: 'referencia', type: 'string'},
            {name: 'valor', type: 'float'},
            {name: 'comentario', type: 'string'}
        ]
    });
    
    storeDetalle = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        model: 'InfoPagoDetModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                root: 'detalles'
            }             
        }
    });

    grid = Ext.create('Ext.grid.Panel', {
        store: storeDetalle,
        columns: [ 
        {
	  id: 'idPago',
	  header: 'idPago',
	  dataIndex: 'idPago',
	  hidden: true,
	  hideable: false
	},
	{
	  id: 'idPagoDet',
	  header: 'idPagoDet',
	  dataIndex: 'idPagoDet',
	  hidden: true,
	  hideable: false
	},
        {
            text: 'Factura',
            dataIndex: 'factura',
            width: 100,
            align: 'right'
        }, {
            text: 'Forma Pago',
            dataIndex: 'formaPago',
            width: 150,
            align: 'right'
        },  {
            text: 'Tipo Cuenta',
            dataIndex: 'tipoCuenta',
            width: 230,
            align: 'right'
        },{
            text: 'Numero',
            dataIndex: 'referencia',
            width: 70,
            align: 'right'
        },{
            text: 'Fecha Deposito',
            dataIndex: 'feDeposito',
            width: 115,
            align: 'right'
        },  {
            text: 'Observacion',
            dataIndex: 'comentario',
            width: 165,
            align: 'right'
        }, {
            text: 'Valor',
            dataIndex: 'valor',
            width: 50,
            align: 'right'
        },
	  {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 55,
                    items: [
                         {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-edit'
                        },
                        tooltip: 'Editar Detalle',
                        handler: function(grid, rowIndex, colIndex) {
			    var rec = storeDetalle.getAt(rowIndex);
                             showEditarDetalle(rec);
                            }
                        }
                    ]    
                }
	],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_detalles'),
        width: 950,
        height: 200,
        title: ''
    });
    
    

    /**
     * Documentacion para funcion showEditarDetalle(rec)
     * Opcion que permite presentar detalle de campos del detalle del anticipo
     * Actualizacion: se agrega la nueva forma de pago RETENCION FUENTE 1%
     * @param object $rec (objeto con el registro para el grid)
     * @since 11/02/2015
     * @author amontero@telconet.ec
     * @version 1.1 05/05/2016 amontero@telconet.ec 
     */    
    function showEditarDetalle(rec)
    {   
        winEditarDetalle="";
        formPanelEditarDetalle = "";
	
        if (!winEditarDetalle)
        {
            labelNumeroReferencia = "";

            if (rec.data.formaPago == 'CHEQUE')
                labelNumeroReferencia = "Numero Cheque";
            if (rec.data.formaPago == 'DEPOSITO' || 
                rec.data.formaPago == 'TRANSFERENCIA' || 
                rec.data.formaPago == 'DEPOSITO MESES ANTERIORES' || 
                rec.data.formaPago == 'TRANSFERENCIA MESES ANTERIORES')
                labelNumeroReferencia = "Numero Documento";
            if (rec.data.formaPago == 'RETENCION FUENTE 1%'  
                || rec.data.formaPago == 'RETENCION FUENTE 2%' || rec.data.formaPago == 'RETENCION FUENTE 8%' 
                || rec.data.formaPago == 'RETENCION IVA 10%' || rec.data.formaPago == 'RETENCION IVA 20%'
                || rec.data.formaPago == 'RETENCION IVA 70%' || rec.data.formaPago == 'RETENCION IVA 100%')
                labelNumeroReferencia = "Numero Retencion";
            if (rec.data.formaPago == 'TARJETA DE CREDITO')
                labelNumeroReferencia = "Numero Tarjeta";
            if (rec.data.formaPago == 'RECAUDACION' || rec.data.formaPago == 'DEBITO BANCARIO')
                labelNumeroReferencia = "Numero Papeleta";

            Ext.define('BancosList', {
                extend: 'Ext.data.Model',
                fields: [
                {name:'id_banco', type:'int'},
                {name:'descripcion_banco', type:'string'}
                ]
            });

            storeBancosContables = Ext.create('Ext.data.Store', {
                model: 'BancosList',
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : url_lista_bancos_contables,
                    reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                    },
                    extraParams: {
                      es_tarjeta : 'N'
                      }
                }
            });
            Ext.define('TiposCuentaList', {
                extend: 'Ext.data.Model',
                fields: [
                {name:'id_cuenta', type:'int'},
                {name:'descripcion_cuenta', type:'string'}
                ]
            });

            storeCuentasBancosContables = Ext.create('Ext.data.Store', {
                model: 'TiposCuentaList',
                proxy: {
                    type: 'ajax',
                    url : url_lista_cuentas_bancos_contables,
                    reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                    }
                }
            });

            fechaDeposito = new Ext.form.DateField({
                id: 'fechaDeposito',
                name: 'fechaDeposito',
                fieldLabel: 'Fecha',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                editable: false,
                hidden: true,
                value:new Date()
            });


            Ext.define('datosContratoModel', {
                extend: 'Ext.data.Model',
                fields: [
                {name: 'numero', type: 'string'},
                {name: 'formaPago', type: 'string'}
                ]
            });

            storeBancosTarjeta = Ext.create('Ext.data.Store', {
                model: 'BancosList',
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : url_lista_bancos_tarjeta,
                    reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                    },
                    extraParams: {
                    es_tarjeta : 'S'
                    }
                }
            });

            storeTipoTarjeta = Ext.create('Ext.data.Store', {
                model: 'TiposCuentaList',
                proxy: {
                    type: 'ajax',
                    url : url_lista_tipos_cuenta,
                    reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                    }
                }
            });

            formPanelEditarDetalle = Ext.create('Ext.form.Panel', 
            {
                buttonAlign: 'center',
                BodyPadding: 10,
                bodyStyle: "background: white; padding:10px; border: 0px none;",
                frame: true,
                items: 
                [{
                    xtype: 'fieldset',
                    title: 'Datos a Editar',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: { width: '350px'},
                    items: 
                    [
                        {
                            xtype: 'combobox',
                            id: 'cmbBancos',
                            fieldLabel: 'Banco',
                            typeAhead: true,
                            hidden: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione Banco',
                            store:storeBancosContables,
                            displayField: 'descripcion_banco',
                            valueField: 'id_banco',
                            selectOnTab: true,
                            lazyRender: true,
                            queryMode: "local",
                            listClass: 'x-combo-list-small',
                            listeners:
                            {
                                select:
                                {
                                    fn:function(combo, value) 
                                    {
                                        Ext.getCmp('cmbTipoCuenta').reset();  
                                        storeCuentasBancosContables.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'N'};
                                        storeCuentasBancosContables.load({params: {}});
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbTipoCuenta',
                            fieldLabel: 'Tipo Cuenta',
                            typeAhead: true,
                            hidden: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione Tipo',
                            store:storeCuentasBancosContables,
                            displayField: 'descripcion_cuenta',
                            valueField: 'id_cuenta',
                            selectOnTab: true,
                            lazyRender: true,
                            queryMode: "local",
                            listClass: 'x-combo-list-small',
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbBancosTarjeta',
                            fieldLabel: 'Banco',
                            typeAhead: true,
                            hidden: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione Banco',
                            store:storeBancosTarjeta,
                            displayField: 'descripcion_banco',
                            valueField: 'id_banco',
                            selectOnTab: true,
                            lazyRender: true,
                            queryMode: "local",
                            listClass: 'x-combo-list-small',
                            listeners:
                            {
                                select:
                                {
                                    fn:function(combo, value) 
                                    {
                                        Ext.getCmp('cmbTipoCuentaTarjeta').reset();  
                                        storeTipoTarjeta.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'S'};
                                        storeTipoTarjeta.load({params: {}});
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbTipoCuentaTarjeta',
                            fieldLabel: 'Tipo Tarjeta',
                            typeAhead: true,
                            hidden: true,
                            triggerAction: 'all',
                            emptyText: 'Seleccione Tipo',
                            store:storeTipoTarjeta,
                            displayField: 'descripcion_cuenta',
                            valueField: 'id_cuenta',
                            selectOnTab: true,
                            lazyRender: true,
                            queryMode: "local",
                            listClass: 'x-combo-list-small',
                        },
                        { 
                            xtype: 'textfield',
                            hidden: true,
                            fieldLabel: labelNumeroReferencia,
                            name: 'new_numero_referencia',
                            id: 'new_numero_referencia',
                            allowBlank: true,
                        },
                        fechaDeposito,
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Observacion del Detalle Pago',
                            name: 'new_comentario',
                            id: 'new_comentario'
                        }
                    ]
                }],
                buttons:
                [{
                    text: 'Guardar',
                    handler: function()
                    {
                        var parametros = "";

                        if(rec.data.formaPago == 'CHEQUE')
                        {
                            parametros = { comentario: Ext.getCmp('new_comentario').value,
                                formaPago: rec.data.formaPago, idPago: rec.data.idPago, idPagoDet: rec.data.idPagoDet,
                                idBanco: Ext.getCmp('cmbBancos').value , numeroReferencia: Ext.getCmp('new_numero_referencia').value 
                            };
                        }

                        if(rec.data.formaPago == 'DEPOSITO' || 
                            rec.data.formaPago == 'TRANSFERENCIA'|| 
                            rec.data.formaPago == 'DEPOSITO MESES ANTERIORES' || 
                            rec.data.formaPago == 'TRANSFERENCIA MESES ANTERIORES')
                        {
                            parametros = { comentario: Ext.getCmp('new_comentario').value,formaPago: rec.data.formaPago,
                                idPago: rec.data.idPago, idPagoDet: rec.data.idPagoDet, idBanco: Ext.getCmp('cmbBancos').value , 
                                tipoCuenta: Ext.getCmp('cmbTipoCuenta').value ,numeroReferencia: Ext.getCmp('new_numero_referencia').value , 
                                fechaDeposito: Ext.getCmp('fechaDeposito').value 
                            };
                        }
                        if(rec.data.formaPago == 'RECAUDACION' || rec.data.formaPago == 'DEBITO BANCARIO')
                        {
                            parametros = { formaPago: rec.data.formaPago,idPago: rec.data.idPago, idPagoDet: rec.data.idPagoDet, 
                                fechaDeposito: Ext.getCmp('fechaDeposito').value,
                                numeroReferencia: Ext.getCmp('new_numero_referencia').value  
                            };
                        }
                        if(rec.data.formaPago == 'RETENCION FUENTE 1%' 
                            || rec.data.formaPago == 'RETENCION FUENTE 2%' || rec.data.formaPago == 'RETENCION FUENTE 8%'
                            || rec.data.formaPago == 'RETENCION IVA 10%' || rec.data.formaPago == 'RETENCION IVA 20%'
                            || rec.data.formaPago == 'RETENCION IVA 70%' || rec.data.formaPago == 'RETENCION IVA 100%')
                        {
                            parametros = { comentario: Ext.getCmp('new_comentario').value,formaPago: rec.data.formaPago,
                                idPago: rec.data.idPago, idPagoDet: rec.data.idPagoDet,
                                numeroReferencia: Ext.getCmp('new_numero_referencia').value 
                            };
                        }

                        if(rec.data.formaPago == 'TARJETA DE CREDITO'){
                            parametros = { 
                                comentario: Ext.getCmp('new_comentario').value,formaPago: rec.data.formaPago,
                                idPago: rec.data.idPago, idPagoDet: rec.data.idPagoDet,idBanco: Ext.getCmp('cmbBancosTarjeta').value , 
                                tipoCuenta: Ext.getCmp('cmbTipoCuentaTarjeta').value ,numeroReferencia: Ext.getCmp('new_numero_referencia').value 
                            };
                        }

                        boolError = false;
                        if(!boolError)
                        {
                          connActualizaDetPago.request({
                            method: 'POST',
                            params: parametros,
                            url: '../ajaxActualizaDetAnticipo',
                            success: function(response){			
                                var text = response.responseText;
                                winEditarDetalle.close();
                                if(text == "OK")
                                {		    
                                      Ext.Msg.alert('Mensaje', 'Se Actualizo el Detalle del Anticipo', function(btn){
                                        if(btn=='ok'){
                                          storeDetalle.load();
                                          }
                                      });
                                }
                                else{
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: text,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            },
                            failure: function(result) {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: result.responseText,
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });
                        }
                        else{
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: mensajeError,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        }                         
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        winEditarDetalle.close();
                    }
                }]
            });

            if(rec.data.formaPago == 'CHEQUE')
            {
                Ext.getCmp('cmbBancos').setVisible( true );
                Ext.getCmp('new_numero_referencia').setVisible( true );
            }
            if(rec.data.formaPago == 'DEPOSITO' || 
                rec.data.formaPago == 'TRANSFERENCIA'|| 
                rec.data.formaPago == 'DEPOSITO MESES ANTERIORES' || 
                rec.data.formaPago == 'TRANSFERENCIA MESES ANTERIORES')
            {
                Ext.getCmp('cmbBancos').setVisible( true );
                Ext.getCmp('cmbTipoCuenta').setVisible( true );
                Ext.getCmp('new_numero_referencia').setVisible( true );
                Ext.getCmp('fechaDeposito').setVisible( true );
            }
            if(rec.data.formaPago == 'RETENCION FUENTE 1%' 
                || rec.data.formaPago == 'RETENCION FUENTE 2%' || rec.data.formaPago == 'RETENCION FUENTE 8%' 
                || rec.data.formaPago == 'RETENCION IVA 10%' || rec.data.formaPago == 'RETENCION IVA 20%'
                || rec.data.formaPago == 'RETENCION IVA 70%' || rec.data.formaPago == 'RETENCION IVA 100%')
            {
                Ext.getCmp('new_numero_referencia').setVisible( true );
            }
            if(rec.data.formaPago == 'TARJETA DE CREDITO')
            {
                Ext.getCmp('cmbBancosTarjeta').setVisible( true );
                Ext.getCmp('cmbTipoCuentaTarjeta').setVisible( true );
                Ext.getCmp('new_numero_referencia').setVisible( true );
            }
            if(rec.data.formaPago == 'RECAUDACION' || rec.data.formaPago == 'DEBITO BANCARIO')
            {
                Ext.getCmp('new_comentario').setVisible( false );
                Ext.getCmp('fechaDeposito').setVisible( true );
                Ext.getCmp('new_numero_referencia').setVisible( true );
            }
            winEditarDetalle = Ext.widget('window', {
            title: 'Editar Detalle Pago',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelEditarDetalle]
            });
        }

        winEditarDetalle.show();    
    }


});
