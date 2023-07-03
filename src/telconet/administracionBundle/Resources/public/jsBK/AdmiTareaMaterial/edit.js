/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validarFormulario()
{    
    var material = Ext.get('escogido_material').dom.value;
    if(material=="" || !material) {  material = 0; }
    
    
    if(material==0 || material=="")
    {
        alert("No se ha escogido el Material");
        return false;
    }
  
    return true;
}

Ext.onReady(function() {
    
    /* ****************** MODULOS ************************ */
    Ext.define('MaterialList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_material', type:'string'},
            {name:'nombre_material', type:'string'}
        ]
    });
    storeMaterial = Ext.create('Ext.data.Store', {
            model: 'MaterialList',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : '../getListadoMateriales',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    
    combo_materiales = new Ext.form.ComboBox({
            id: 'cmb_material',
            name: 'cmb_material',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 500,
            emptyText: 'Seleccione Material',
            store:storeMaterial,
            displayField: 'nombre_material',
            valueField: 'id_material',
            renderTo: 'combo_material',
            listeners:{
                select:{fn:function(combo, value) {
                        var valueEscogido = combo.getValue();
                        var n = valueEscogido.split("@@");
                        var id = n[0];
                        var costo_precio = parseFloat(n[1]);
                        
                        $('#escogido_material').val(id);
                        $('#telconet_schemabundle_admitareamaterialtype_costoMaterial').val(costo_precio);
                        $('#telconet_schemabundle_admitareamaterialtype_precioVentaMaterial').val(costo_precio);
                }}
            }
    });
    
    
    
});