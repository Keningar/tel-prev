/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    Ext.require([
        '*',
        'Ext.tip.QuickTipManager',
            'Ext.window.MessageBox'
    ]);

    var itemsPerPage = 10;
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
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    //format: 'd/m/Y',
                    width:200,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    //format: 'd/m/Y',
                    width:200,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            
            var mystore = new Ext.data.Store({ 
                total: 'total',
                autoLoad:true,
                proxy: {
                    timeout:1000000,
                    type: 'ajax',
                    url : url_cargar_numeracion,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                      [
                        {name:'id_numeracion', mapping:'id_numeracion'},
                        {name:'numeracion', mapping:'numeracion'}
                      ]
            });

            var cbxSelNumeracion = new Ext.form.ComboBox({
                hiddenName: 'Numeracion',
                name: 'numeracion',
                id: 'numeracion',
                displayField: 'numeracion',
                valueField: 'numeracion',
                fieldLabel:'Numeracion', 
                mode: 'local',
                triggerAction: 'all',
                listClass: 'comboalign',
                typeAhead: true,
                forceSelection: true,
                selectOnFocus: true,
                emptyText:'Seleccione...',
                store: mystore
            });
            
            var mystore_tipos = new Ext.data.Store({ 
                total: 'total',
                autoLoad:true,
                proxy: {
                    timeout:1000000,
                    type: 'ajax',
                    url : url_cargar_tipos,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                      [
                        {name:'codigoTipoDocumento', mapping:'codigoTipoDocumento'},
                        {name:'descripcion', mapping:'descripcion'}
                      ]
            });

            var cbxSelTipos = new Ext.form.ComboBox({
                hiddenName: 'Cod. Tipo Documento',
                name: 'codigoTipoDocumento',
                id: 'codigoTipoDocumento',
                displayField: 'descripcion',
                valueField: 'codigoTipoDocumento',
                fieldLabel:'Cod. Tipo Documento', 
                mode: 'local',
                triggerAction: 'all',
                listClass: 'comboalign',
                typeAhead: true,
                forceSelection: true,
                selectOnFocus: true,
                emptyText:'Seleccione...',
                store: mystore_tipos
            });
            
            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 1,
                    align: 'left',
                    border:1,
                },
                bodyStyle: {
                    background: '#fff'
                },                     
                collapsible : true,
                collapsed: false,
                width: 1100,
                title: 'Criterios de busqueda',
                buttons: 
                [
                    {
                        text: 'Procesar',
                        //xtype: 'button',
                        iconCls: "icon_search",
                        handler: Buscar,
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function(){ limpiar();}
                    }
                ],                
                items: 
                [
                    DTFechaDesde,
                    DTFechaHasta,
                    cbxSelNumeracion,
                    cbxSelTipos,
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Rango Uno',
                        name: 'nuno',
                        id: 'nuno',
                        width: 200
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Rango Dos',
                        name: 'ndos',
                        id: 'ndos',
                        width: 200
                    },
                    {html:"&nbsp;",border:false,width:10},
                    {html:"&nbsp;",border:false,width:10},
                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
     
        function Buscar()
        {
            var fechaDesde=Ext.getCmp('fechaDesde').getValue();
            var fechaHasta=Ext.getCmp('fechaHasta').getValue();
            var cmbNumeracion = cbxSelNumeracion.getValue();
            var cmbTipos = cbxSelTipos.getValue();
            var nuno=Ext.getCmp('nuno').getValue();
            var ndos=Ext.getCmp('ndos').getValue();
            
            if(Ext.getCmp('fechaDesde').isValid() && Ext.getCmp('fechaHasta').isValid())
            {
                if(fechaDesde!=null && fechaHasta!=null && cmbNumeracion!=null)
                {
                    Ext.Msg.confirm('Alerta','Se generara el archivo para el Courier. Desea continuar?', function(btn)
                    {
                        if(btn=='yes')
                        {
                            Ext.Ajax.request({
                                url: direccion,
                                timeout:600000,
                                method: 'post',
                                params: { 
                                    fechaDesde : fechaDesde, 
                                    fechaHasta:fechaHasta,
                                    cmbNumeracion:cmbNumeracion,
                                    cmbTipos:cmbTipos,
                                    nuno:nuno,
                                    ndos:ndos,
                                },
                                success: function(response){
                                     Ext.Msg.alert('Generacion archivo', 'Se envio a generar el archivo excel');
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });
                        }
                    });
                }
                else
                    Ext.Msg.alert('Error', 'Ingresar campo [Fecha Desde], [Fecha Hasta], [Numeracion]');
            }
            else
                    Ext.Msg.alert('Error', 'Fechas ingresadas no son validas, favor verificar');
        }
    

        function eliminar(direccion)
        {
            //alert(direccion);
            Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                if(btn=='yes'){
                    Ext.Ajax.request({
                        url: direccion,
                        method: 'post',
                        success: function(response){
                            var text = response.responseText;
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
            Ext.getCmp('nuno').setRawValue("");
            Ext.getCmp('ndos').setRawValue("");
            Ext.getCmp('numeracion').setValue("");
            Ext.getCmp('codigoTipoDocumento').setValue("");
        }
    
});

    


