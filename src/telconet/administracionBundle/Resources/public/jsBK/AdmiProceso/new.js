/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validarFormulario()
{    
    var procesopadre = Ext.getCmp('cmb_procesopadre').getValue();
    
    if(procesopadre=="" || !procesopadre){  procesopadre = 0; }
    Ext.get('escogido_procesopadre_id').dom.value = procesopadre;
      
    if(procesopadre==0)
    {
        //alert("No se ha escogido el Proceso Padre");
        return true;
    }
  
    return true;
}

Ext.onReady(function() {
    
    /* ****************** PROCESO PADRE ************************ */
    Ext.define('ProcesosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_proceso', type:'int'},
            {name:'nombre_proceso', type:'string'}
        ]
    });
    storeProcesos = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'ProcesosList',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'getProcesos',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    
    combo_procesos = new Ext.form.ComboBox({
		id: 'cmb_procesopadre',
		name: 'cmb_procesopadre',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Proceso',
		store:storeProcesos,
		displayField: 'nombre_proceso',
		valueField: 'id_proceso',
		renderTo: 'combo_procesopadre'
    });
});