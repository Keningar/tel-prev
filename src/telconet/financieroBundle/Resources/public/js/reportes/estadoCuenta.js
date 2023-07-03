/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 1000000;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){
                
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'F.Emisión Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'F.Emisión Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
            });
                

                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [
                            {name:'movimiento', type: 'string'},
                            {name:'documento', type: 'string'},
                            {name:'punto', type: 'string'},
                            {name:'valor_ingreso', type: 'string'},
                            {name:'valor_egreso', type: 'string'},
                            {name:'acumulado', type: 'string'},
                            {name:'Fecreacion', type: 'string'},
                            {name:'strFeEmision', type: 'string'},
                            {name:'strFeAutorizacion', type: 'string'},
                            {name:'tipoDocumento', type: 'string'},
                            {name:'oficina', type: 'string'},
                            {name:'referencia', type: 'string'},
                            {name:'formaPago', type: 'string'},
                            {name:'numero', type: 'string'},
                            {name:'saldoActual', type: 'string'},
                            {name:'boolSumatoriaValorTotal', type: 'string'},
                            {name:'boolDocDependeDePago', type: 'boolean'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store_grid,
                        timeout:100000000,
                        reader: {
                            type: 'json',
                            root: 'documentos',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', idcliente:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
						store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
						store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                        },
                    }
                });

                store.load();    

                 var sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                            });			
                        }
                    }
                });


                var listView = Ext.create('Ext.grid.Panel', {
                    width:1300,
                    height:1300,
                    collapsible:false,
                    title: 'Estado de Cuenta Por Cliente',
                    dockedItems: [
                        {
                            dock: 'top',
                            xtype: 'toolbar',
                            items: 
                                [
                                    {xtype: 'tbfill'},
                                    {
                                        xtype: 'button',
                                        itemId: 'grid-excel-button',
                                        iconCls: 'x-btn-icon icon_exportar',
                                        //hidden : true,
                                        text: 'Exportar',
                                        handler: function() {
                                            var vExportContent = listView.getExcelXml();
                                            document.location = 'data:application/vnd.ms-excel;base64,' + Base64.encode(vExportContent);
                                        }
                                    }
                                ]
                        }
                    ],
                    renderTo: Ext.get('lista_prospectos'),
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        stripeRows: true,
                        enableTextSelection: true,
                        emptyText: 'No hay datos para mostrar',
                        getRowClass: function(record, index) {
                            var cls = '';
                            if (record.data.referencia == 'Saldo anterior:')
                            {
                                cls = 'estado_cta';
                            }
                            
                            if (record.data.oficina != '' && record.data.oficina != null)
                            {
                                cls = 'multilineColumn';
                            }
                            
                            if (record.data.numero != '' && record.data.numero != null)
                            {
                                cls = 'multilineColumn';
                            }
                            
                            if (record.data.documento == 'RESUMEN DEL CLIENTE:' ||
                                record.data.documento == 'Saldo:' ||
                                record.data.documento == 'Saldo Final:' ||
                                record.data.documento == 'Anticipos pendientes:')
                            {
                                cls = 'estado_cta';
                            }
                            
                            //Se marca en otro color en el estado de cuenta para el caso de ANTC que no sumarizan el saldo en el estado de cuenta.
                            if(record.data.boolSumatoriaValorTotal=='false')
                            {
                                cls = 'antc_estado_cta'; 
                            }
                            return cls;
                        }
                    },
                    listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            }
                    },
                    columns: [
                    {
                        text: 'Mov.',
                        width: 35,
                        dataIndex: 'movimiento',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'F. Creacion',
                        dataIndex: 'Fecreacion',
                        width: 100,
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'F. Emision',
                        dataIndex: 'strFeEmision',
                        width: 100,
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'F. Autorizacion',
                        dataIndex: 'strFeAutorizacion',
                        width: 100,
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago    
                    },{
                        text: 'Pto cliente',
                        width: 130,
                        dataIndex: 'punto',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Oficina',
                        dataIndex: 'oficina',
                        tdCls: 'x-change-cell x-grid-cell-inner',
                        width: 100,
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'T. Doc',
                        width: 50,
                        dataIndex: 'tipoDocumento',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'No. Documento',
                        width: 150,
                        dataIndex: 'documento',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'F. Pago',
                        width: 50,
                        dataIndex: 'formaPago',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: '',
                        width: 100,
                        dataIndex: 'numero',
                        tdCls: 'x-change-cell  x-grid-cell-inner',
                        flex: 1,
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Referencia',
                        width: 100,
                        dataIndex: 'referencia',
                        tdCls: 'x-change-cell',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Saldo Act. Factura',
                        dataIndex: 'saldoActual',
                        align: 'right',
                        width: 80,
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Ingreso',
                        width: 80,
                        align: 'right',
                        dataIndex: 'valor_ingreso',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Egreso',
                        width: 80,
                        align: 'right',
                        dataIndex: 'valor_egreso',
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Saldo',
                        dataIndex: 'acumulado',
                        align: 'right',
                        width: 80,
                        tdCls: 'x-change-cell',
                        renderer:   renderPintaFilaDependienteDePago
                    }]
                });            


            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';			
                    iconos=iconos+'<b><a href="#" onClick="eliminar(\''+record.data.linkEliminar+'\')" title="Eliminar" class="button-grid-delete"></a></b>';	
                    return Ext.String.format(iconos,value,'1','nada');
            }

            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  
                border:false,
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 5,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: false,
                width: 1300,
                title: 'Criterios de busqueda',
                buttons: [

                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: Buscar,
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function(){ limpiar();}
                    }

                ],
                items: [
                        DTFechaDesde,
                        {html:"&nbsp;",border:false,width:10},
                        DTFechaHasta,
                        {html:"&nbsp;",border:false,width:10},
                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
    });

    function Buscar()
    {
        store.load({params: {start: 0, limit: 10}});
    }

    function eliminar(direccion)
    {
        Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: direccion,
                    method: 'post',
                    success: function(response){
                        var text = response.responseText;
                        store.load();
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                });
            }
        });
    }

    function limpiar(){
        Ext.getCmp('fechaDesde').setRawValue("");
        Ext.getCmp('fechaHasta').setRawValue("");

    }
    
    /**
     * Documentación para el método 'renderPintaFilaDependienteDePago'.
     *
     *  Agrega color de fondo a la celda del pago dependiente en el grid.
     *
     * @param integer value  Contiene el valor de la fila
     * @param object  record Contiene los datos enviados desde el controlador
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 08-08-2017
     */
    function renderPintaFilaDependienteDePago(value, meta, record)
    {
         if(record.data.boolDocDependeDePago)
         {
             meta.style = "background-color: #e6ffcc;";                                 
         }
                            
         return value;
    }
