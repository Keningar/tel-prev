/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validarFormulario()
{    
    var empresa = Ext.getCmp('cmb_empresa').getValue();
    var oficina = Ext.getCmp('cmb_oficina').getValue();
    
    if(empresa=="" || !empresa) {  empresa = 0; }
    if(oficina=="" || !oficina) {  oficina = 0; }
    Ext.get('escogido_empresa_id').dom.value = empresa;
    Ext.get('escogido_oficina_id').dom.value = oficina;
  
            
    if(empresa==0)
    {
        alert("No se ha escogido la Empresa");
        return false;
    }
    else if(oficina==0)
    {
        alert("No se ha escogido la Oficina");
        return false;
    }
  
    return true;
}

Ext.onReady(function() {
    
    /* ****************** MODULOS ************************ */
    Ext.define('EmpresaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_empresa', type:'int'},
            {name:'nombre_empresa', type:'string'}
        ]
    });
    storeEmpresa = Ext.create('Ext.data.Store', {
            model: 'EmpresaList',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : 'getListadoEmpresas',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    
    combo_empresas = new Ext.form.ComboBox({
            id: 'cmb_empresa',
            name: 'cmb_empresa',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 400,
            emptyText: 'Seleccione Empresa',
            store:storeEmpresa,
            displayField: 'nombre_empresa',
            valueField: 'id_empresa',
            renderTo: 'combo_empresa',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_oficina').reset();   
                    
                    storeOficina.proxy.extraParams = {id_empresa: combo.getValue()};
                    storeOficina.load({params: {}});
                }}
            }
    });
   
    /* ************* ITEM MENU ************************ */
    Ext.define('OficinaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_oficina', type:'int'},
            {name:'nombre_oficina', type:'string'}
        ]
    });
    storeOficina = Ext.create('Ext.data.Store', {
            model: 'OficinaList',
            proxy: {
                type: 'ajax',
                url : 'getListadoOficinas',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    combo_oficinas = new Ext.form.ComboBox({
            id: 'cmb_oficina',
            name: 'cmb_oficina',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 400,
            emptyText: 'Seleccione Oficina',
            store: storeOficina,
            displayField: 'nombre_oficina',
            valueField: 'id_oficina',
            renderTo: 'combo_oficina'
    });
        
    
    
    
});