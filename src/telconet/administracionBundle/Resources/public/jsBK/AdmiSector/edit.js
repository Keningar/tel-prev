/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function isInteger(n) {
    return (typeof n == 'number' && /^-?\d+$/.test(n+''));
}

function validarFormulario()
{    
    var provincia = Ext.getCmp('cmb_provincia').getValue();
    var canton = Ext.getCmp('cmb_canton').getValue();
    var parroquia = Ext.getCmp('cmb_parroquia').getValue();
    
    if(provincia=="" || !provincia) {  provincia = 0; }
    if(canton=="" || !canton) {  canton = 0; }
    if(parroquia=="" || !parroquia) {  parroquia = 0; }	
	
	if(isInteger(parroquia))
	{
	    Ext.get('escogido_provincia_id').dom.value = provincia;
	    Ext.get('escogido_canton_id').dom.value = canton;
	    Ext.get('escogido_parroquia_id').dom.value = parroquia;
	    
	    if(parroquia==0)
	    {
	        alert("No se ha escogido la Parroquia");
	        return false;
	    }
		
		return true;
	} 
	else
	{
		if(Ext.get('escogido_parroquia_id').dom.value > 0) 
		{
			return true;
		}
		else
		{
	        alert("No se ha escogido la Parroquia");
			return false;
		}
	}
	
    return true;
}

Ext.onReady(function() {
    
    /* ****************** PROVINCIA ************************ */
    Ext.define('ProvinciaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_provincia', type:'int'},
            {name:'nombre_provincia', type:'string'}
        ]
    });
    storeProvincias = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'ProvinciaList',
		proxy: {
			type: 'ajax',
			url : '../getProvincias',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    
    combo_provincias = new Ext.form.ComboBox({
		id: 'cmb_provincia',
		name: 'cmb_provincia',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Provincia',
		store:storeProvincias,
		displayField: 'nombre_provincia',
		valueField: 'id_provincia',
		renderTo: 'combo_provincias',
		listeners:{
			select:{fn:function(combo, value) {
				Ext.getCmp('cmb_canton').reset();  
				Ext.getCmp('cmb_parroquia').reset();  
				
				storeCantones.proxy.extraParams = {idProvincia: combo.getValue()};
				storeCantones.load();				
											 
				storeParroquias.proxy.extraParams = { idCanton: '', idProvincia: combo.getValue() };
				storeParroquias.load();
			}}
		}
    });
   
    /* ************* Canton ************************ */
    Ext.define('CantonesList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_canton', type:'int'},
            {name:'nombre_canton', type:'string'}
        ]
    });
    storeCantones = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'CantonesList',
		proxy: {
			type: 'ajax',
			url : '../getCantones',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    combo_cantones = new Ext.form.ComboBox({
		id: 'cmb_canton',
		name: 'cmb_canton',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Canton',
		store: storeCantones,
		displayField: 'nombre_canton',
		valueField: 'id_canton',
		renderTo: 'combo_cantones',
		listeners:{
			select:{fn:function(combo, value) {  
				Ext.getCmp('cmb_parroquia').reset();  
					
				storeParroquias.proxy.extraParams = { idCanton: combo.getValue() }; 
				storeParroquias.load();
			}}
		}
    });
        
    /* ****************** PARROQUIAS ************************ */
    Ext.define('ParroquiasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_parroquia', type:'int'},
            {name:'nombre_parroquia', type:'string'}
        ]
    });
    storeParroquias = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'ParroquiasList',
		proxy: {
			type: 'ajax',
			url : '../getParroquias',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    combo_parroquias = new Ext.form.ComboBox({
		id: 'cmb_parroquia',
		name: 'cmb_parroquia',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Parroquia',
		store: storeParroquias,
		displayField: 'nombre_parroquia',
		valueField: 'id_parroquia',
		renderTo: 'combo_parroquias'
    });    
});