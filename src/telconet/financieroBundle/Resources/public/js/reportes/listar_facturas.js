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
            Ext.define('Mes', {
				extend: 'Ext.data.Model',
				fields: [
					{type: 'string', name: 'name'},
					{type: 'string', name: 'valor'}
				]
			});
            
            var meses = [
				{"name":"Enero","valor":"01"},
				{"name":"Febrero","valor":"02"},
				{"name":"Marzo","valor":"03"},
				{"name":"Abril","valor":"04"},
				{"name":"Mayo","valor":"05"},
				{"name":"Junio","valor":"06"},
				{"name":"Julio","valor":"07"},
				{"name":"Agosto","valor":"08"},
				{"name":"Septiembre","valor":"09"},
				{"name":"Octubre","valor":"10"},
				{"name":"Noviembre","valor":"11"},
				{"name":"Diciembre","valor":"12"},
			];
			
			 var store = Ext.create('Ext.data.Store', {
				model: 'Mes',
				data: meses
			});
			
            Ext.define('Ano', {
				extend: 'Ext.data.Model',
				fields: [
					{type: 'string', name: 'name'},
					{type: 'string', name: 'valor'}
				]
			});

			var simpleCombo = Ext.create('Ext.form.field.ComboBox', {
				fieldLabel: 'Seleccione el mes',
				displayField: 'name',
				width: 320,
				labelWidth: 130,
				store: store,
				queryMode: 'local',
				typeAhead: true
			});
			
			var anio = [
				{"name":"2010","valor":"2010"},
				{"name":"2011","valor":"2011"},
				{"name":"2012","valor":"2012"},
				{"name":"2013","valor":"2013"},
				{"name":"2014","valor":"2014"},
				{"name":"2015","valor":"2015"},
				{"name":"2016","valor":"2016"},
				{"name":"2017","valor":"2017"},
				{"name":"2018","valor":"2018"},
				{"name":"2019","valor":"2019"},
				{"name":"2020","valor":"2020"},
				{"name":"2021","valor":"2021"},
			];
			
			var store_ano = Ext.create('Ext.data.Store', {
				model: 'Ano',
				data: anio
			});
			
			var simpleComboDos = Ext.create('Ext.form.field.ComboBox', {
				fieldLabel: 'Seleccione el anio',
				displayField: 'name',
				width: 320,
				labelWidth: 130,
				store: store_ano,
				queryMode: 'local',
				typeAhead: true
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
                
                    buttons: [
                        
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

                        items: [
                                simpleCombo,
                                simpleComboDos,
                                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
           
    });

    function Buscar()
    {
		var mesCombo=simpleCombo.getValue();
		var anioCombo=simpleComboDos.getValue();
		
		Ext.Msg.confirm('Alerta','Se generara el reporte de facturas. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: direccion,
						timeout:600000,
						method: 'post',
						params: { 
							mes : mesCombo, 
							anio:anioCombo
						},
						success: function(response){
							//alert(response);
							url = descarga+"?nombre="+response.responseText;
							//method: 'post',
							//params: { 
								//nombre_archivo : response.archivo_nombre, 
							//},
							window.location = url;
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


