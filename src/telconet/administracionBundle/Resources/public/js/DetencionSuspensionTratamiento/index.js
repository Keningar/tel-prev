Ext.require([
    '*'
]);

let dataConfigGridPoliticas;

var global = {

    panel: null,
    panelPolitica_Clausula: null
}

/**
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @since 1.0
 * @version 1.1 28-12-2022
 * Se agrega validación para Identificacion y consulta el Rol de la identificacion enviada.
 *
 *
 */
function validaIdentificacion(isValidarIdentificacionTipo)
{ 
    var identificacionEsCorrecta = false;
    currenIdentificacion = $(input).val();
    

    if ($('#clientetype_tipoIdentificacion').val() !== 'Seleccione...' && $('#clientetype_tipoIdentificacion').val() !== '')
    {
        if (strNombrePais === 'PANAMA') {
            identificacionEsCorrecta = true;
        }
        if (strNombrePais === 'GUATEMALA' && $('#clientetype_tipoIdentificacion').val() === 'NIT' && currenIdentificacion === 'C/F') {
            identificacionEsCorrecta = true;
        }        
        if (/^[\w]+$/.test(currenIdentificacion) && ($('#clientetype_tipoIdentificacion').val() === 'PAS')) 
        {
            identificacionEsCorrecta = true;
        }
        if (/^\d+$/.test(currenIdentificacion) && ($('#clientetype_tipoIdentificacion').val() === 'RUC' || $('#clientetype_tipoIdentificacion').val() === 'CED'
                                               || $('#clientetype_tipoIdentificacion').val() === 'NIT'
                                               || $('#clientetype_tipoIdentificacion').val() === 'DPI'))
        {
            identificacionEsCorrecta = true;
        }
    }

    if (identificacionEsCorrecta === true) 
    {    
        ocultarDiv('diverrorident');
        if (isValidarIdentificacionTipo && typeof validarIdentificacionTipo == typeof Function)
        {
            validarIdentificacionTipo();
        }
        $.ajax({
            type: "POST",
            data: "identificacion=" + currenIdentificacion,
            url: url_valida_identificacion,
            beforeSend: function() 
            {
                $('#img-valida-identificacion').attr("src", url_img_loader);
            },
            success: function(msg) 
            {   
                if (msg != '') {  
                    
                    if (msg == "no") { 
                        $('#img-valida-identificacion').attr("src", url_img_delete);
                        Ext.Msg.alert('Error','La identificación no pertenece a un cliente.', function(btn){
                            if(btn=='ok'){
                                limpiaCampos(numCorreoAgregado);
                            }
                        });
                        
                    }else {         
                           
                        //existe en base 
                        $('#img-valida-identificacion').attr("title", "identificacion ya existe");
                        $('#img-valida-identificacion').attr("src", url_img_check); 
                        $(input).focus();          
                                
                        //obtiene roles de la persona
                        var obj = JSON.parse(msg);
                        var roles = obj[0].roles;                       
                        
                        var arr_roles = roles.split("|");
                            
                        if(arr_roles.includes('Cliente')) 
                        {
                            mostrarDiv('tabPoliticas_Clausulas');
                            CreaGridPoliticasClausula(currenIdentificacion);
                        }
                        else
                        {
                            $('#img-valida-identificacion').attr("src", url_img_delete);
                            Ext.Msg.alert('Error','La identificación no pertenece a un cliente.', function(btn){
                            if(btn=='ok'){
                                limpiaCampos(numCorreoAgregado);
                            }
                            });
                        }                                 
                    }
                } else {
                    Ext.Msg.alert('Error','No se pudo validar la identificacion ingresada.', function(btn){
                        if(btn=='ok'){
                            limpiaCampos(numCorreoAgregado);
                        }
                    });
                }  
            }
        });
    }
    else 
    {
        if ($('#clientetype_tipoIdentificacion').val() === 'Seleccione...' || $('#clientetype_tipoIdentificacion').val() === '') 
        {
            mostrarDiv('dividentificacion');
            $("#dividentificacion").html("Antes de ingresar identificación seleccione tipo de identificación");
        }
        else 
        {
            $("#diverrorident").html("Identificación es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales");
            mostrarDiv('diverrorident');
            limpiaCampos(numCorreoAgregado);  
        }
        $(input).val("");
    }
}

Ext.onReady(function() { 

    
});


function CreaGridPoliticasClausula(currenIdentificacion){

    Ext.define('modalRespuestaAnterior', {
        extend: 'Ext.data.Model',
        fields: [
                { name: 'idEnunciado', type: 'integer' },
                { name: 'idDocRespuesta', type: 'integer' },
                { name: 'idPersona', type: 'integer' },
    
                { name: 'codigoEnunciado', type: 'string' },
                { name: 'descripcionEnunciado', type: 'string' },
    
                { name: 'lista', type: 'string' },
    
                { name: 'identificacion', type: 'string' },
                { name: 'tipoIdentificacion', type: 'string' },
                { name: 'tipoTributario', type: 'string' },
                { name: 'tipoPersona', type: 'string' },
                { name: 'nombres', type: 'string' },
                { name: 'apellido', type: 'string' },
    
                { name: 'feCreacion', type: 'string' },
    
                { name: 'politica', type: 'string' },
                { name: 'clausula', type: 'string' },
    
                { name: 'valor', type: 'string' },
    
                { name: 'contactos', type: 'json' },
               
        ]
    });
    let strParametros = JSON.stringify({ identificacion: currenIdentificacion });
     var storeRespuestaAnterior = Ext.create('Ext.data.Store', {
        id:'storeRespuestaAnterior',
        autoLoad: true,
        model: 'modalRespuestaAnterior',
        proxy: {
            type: 'ajax',
            url: urlManagerAdminDocEnunciadoResp,
            reader: {
                type: 'json',
                root: 'data',
                message: 'message',
                statusProperty: 'status',
            },
            extraParams: {
                strMetodo: 'listRespuestas',
                strParametros: strParametros
            },
        },
        listeners: {
            load: function(store, records, successful) {
                
                if (store.getCount()>0)
                {
                global.panel = new Ext.TabPanel({

                    id: 'tabPoliticas_Clausulas',
                    renderTo: 'tabPoliticas_Clausulas',
                    activeTab: 0,
                    height: 300,
                    style: { width: '-webkit-fill-available' },
            
                    fullscreen: true,
                    plain: true,
                    autoRender: true,
                    autoShow: true,
                    items: [{
                        id: 'idTabPolitica_Clausula',
                        title: 'Politica_Clausula',
                        contentEl: 'divPoliticas_Clausulas',
                        listeners: {
                            activate: function (tab) {
                                if (tab.showOnParentShow != false) {
                                renderizarTabPolitica_Clausula();
                                }
                            }
                        }
                    },
                    ]
                });
            
                /**
                 * Renderiza componentes en tab Politica_Clausula
                 * @author Jessenia Piloso <jpiloso@telconet.ec>
                 * @version 1.0 22-12-2022
                 * @since 1.0
                 */
                function renderizarTabPolitica_Clausula() {
                
                    let view = Ext.get('tabPoliticas_Clausulas');
                    let tab = Ext.get('divPoliticas_Clausulas');
                    var valRespAnterior = '';
                    let id_index = 'idEnunciado';
                    let strParametros = JSON.stringify({});
                        dataConfigGridPoliticas = {
                        name: 'AdminPoliticas_Clausulas',
                        id: 'AdminPoliticas_Clausulas',
                        height: view.getHeight() - 30,
                        autoScroll: true,
                        view: view,
                        autoLoad: true,
                        pageSize: 10,
                        ajax: {
                            url: url_getPoliticas,
                            reader: {
                                type: 'json',
                                root: 'data',
                                message: 'message',
                                statusProperty: 'status',
                            },
                            extraParams: {
                                strMetodo: 'listPoliticaClausula',
                                strParametros: strParametros
                            },
                        },
                        fields: [
                            { name: id_index, type: 'integer' },
                            { name: 'procesoId', type: 'integer' },
                            { name: 'respuestaId', type: 'integer' },
                            { name: 'respuestaIdAnterior', type: 'integer' },
                            { name: 'descripcionEnunciado', type: 'string' },
                            { name: 'aplicaDocumento', type: 'json' },
                            { name: 'respuestas', type: 'json' },
                            { name: 'valorRespuesta', type: 'string' },
                            { name: 'clausulas', type: 'json' },
                            { name: 'tipoRespuesta', type: 'string' }
                        
            
                        ],
                        columns: [
                            {
                                dataIndex: id_index,
                                hidden: true,
                                hideable: false,
                            },
                            {
                                header: 'Política - Claúsula',
                                dataIndex: 'clausulas',
                                flex: 1,
                                width: 800,
                                renderer: function (
                                    values,
                                    metaData,
                                    record,
                                    rowIndex,
                                    colIndex,
                                    store
                                ) {
                                    let raw = record.raw;
                                    let clausulas = raw.clausulas || [];
                                    let html = '<ul>';
                                    html += '<ul>'
                                    if (clausulas.length) {
                                        clausulas.forEach((el, i) => {
                                            html += '<li>' + el.descripcionEnunciado + ' </li>'
                                        });
                                    } else {
                                        let data = record.data;
                                        html += '<li>'+ data.descripcionEnunciado +'</li>';
                                    }
                                    html += ' </ul>'
                                    html += ' </ul>'
                                    return html;
                                    
                                },
            
                            },
                            {   
                                header: 'Respuesta Actual',
                                dataIndex: 'respuestaIdAnterior',
                                id: 'respuestaIdAnterior', 
                                flex: 1, 
                                width: 100,
                                align: 'center',
                                store: storeRespuestaAnterior,
                                renderer: function (
                                    values,
                                    metaData,
                                    record,
                                    rowIndex,
                                    colIndex,
                                    store
                                ) {
            
                                    let items = storeRespuestaAnterior.data.items;
                                    
                                    let raw = record.raw;
                                    let clausulas = raw.clausulas || [];
                                    let html = '<ul>';
                                    let idRespAnterior = 'IdRespAnterior-'+rowIndex;
                                    if (clausulas.length) {
                                        clausulas.forEach((enunciadoGrid, i) => {
                                            for (let index = 0; index < items.length; index++) {
                                                const enunciadoStore = items[index];
                                                let raw = enunciadoStore.raw;
                                                if (raw['idEnunciado'] == enunciadoGrid.idEnunciado) {
                                                    html += '<li style="text-align:center" id = "'+idRespAnterior+'" value="'+raw.valor+'">' + raw.valor + ' </li>'
                                                valRespAnterior = raw.valor;
                                                }
                                            }
                                            
                                        });
                                    } else {
                                        for (let index = 0; index < items.length; index++) {
                                            const enunciadoStore = items[index];
                                            let raw = enunciadoStore.raw;
                                            let data = record.data;
                                            if (raw['idEnunciado'] == data.idEnunciado) {
                                                html += '<li style="text-align:center" id = "'+idRespAnterior+'" value="'+raw.valor+'">' + raw.valor + ' </li>'
                                                valRespAnterior = raw.valor;
                                            }
                                        }
                                    }
                                    html += ' </ul>'
                                    return html;
                                    
                                },
                            },
                            {
                                header: 'Respuesta Nueva',
                                dataIndex: 'respuestaId',
                                id: 'respuestaId', 
                                flex: 1, 
                                width: 100,
                                align: 'center',
                                renderer: function (
                                    values,
                                    metaData,
                                    record,
                                    rowIndex,
                                    colIndex,
                                    store
                                ) {
            
                                    let idRespAnterior = 'IdRespAnterior-'+rowIndex;
                                    let raw = record.raw;
                                    let attEnunciado = raw.attEnunciado || [];
                                    let clausulas = raw.clausulas || [];
                
                                    let html = '<ul>';
                                    
                                    if (clausulas.length) {
                                        clausulas.forEach(idxAttEnunciado=> {
                                            let attEnunciado = (idxAttEnunciado.attEnunciado || []);
                                            attEnunciado.forEach(idxResp => { 
                                                if (idxResp.nombreCabEnunciado == 'Opción de respuesta')
                                                {   
                                                    let valorRespuesta = (idxResp.data || []);
                                                    valorRespuesta.forEach(idxValResp => {
                                                        if (valRespAnterior == null || valRespAnterior == '')
                                                        {
                                                            html += '<li style="text-align:center" id = "" value=""> </li>'
                                                        }
                                                        else if (idxValResp.valor != valRespAnterior){
                                                            html += '<li style="text-align:center" id = "'+idRespAnterior+'" value="'+idxValResp.id+'">' + idxValResp.valor  + ' </li>'
                                                        }
                                                    });
                                                }
                                            });
                                        });
                                        
                                    }
                                    else{                          
                                        attEnunciado.forEach(el => {
                                            if (el.nombreCabEnunciado == 'Opción de respuesta')
                                            {
                                                let valorRespuesta = (el.data || []);
                                                valorRespuesta.forEach(val => { 
                                                    if (valRespAnterior == null || valRespAnterior == '')
                                                    {
                                                        html += '<li style="text-align:center" id = "" value=""> </li>'
                                                    }
                                                    else if (val.valor != valRespAnterior){
                                                        html += '<li style="text-align:center" id = "'+idRespAnterior+'" value="'+val.id+'">' + val.valor + ' </li>' 
                                                    } 
                                                });
                                            }    
                                        });
                                    }
                                    html += ' </ul>'   
                                    valRespAnterior = '';    
                                    return html;  
                                },                
                            
                            },   
                        ],
                        cellEditing: true,
                    }
            
                    let item = generateGridManager(dataConfigGridPoliticas);
            
                    let objPanel = Ext.create('Ext.form.Panel', {
                        renderTo: tab,
                        height: view.getHeight() - 25,
                        fullscreen: true,
                        layout: 'anchor',
                        items: item
                    });
            
                    objPanel.show();
            
                    global.panelPolitica_Clausula = objPanel;
            
                    }
            }
            else
            {
                Ext.Msg.alert('Información: ', 'El titular no tiene suspendido el tratamiento de datos.', function(btn){
                    if(btn=='ok'){
                        limpiaCampos(numCorreoAgregado);
                    }
                });
            }
            }
        }
        
        });
        
    
}


function trimAll(texto)
{
    return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
}

    
//Agregar caja de Textos que permite ingresar mas correos
    const contenedor = document.querySelector('#correo_0');
    const btnAgregar = document.querySelector('#agregar');
    // Variable para el total de elementos agregados
    let numCorreoAgregado = 1;
    /**
     * Método que se ejecuta cuando se da clic al botón de agregar
     */
    btnAgregar.addEventListener('click', e => {
        var correoSelect     = document.getElementById("clientetype_correo_electronico_0").value;
        if(correoSelect !== ''){
            let div = document.createElement('div');
            div.innerHTML = `<label>* Correo Electrónico:</label>`+
                            `<input type="text" id="clientetype_correo_electronico_${numCorreoAgregado}" name="clientetype_correo_electronico_${numCorreoAgregado}" required="required" class="campo-obligatorio" onchange=validaCorreo(${numCorreoAgregado})>`+
                            `<img id="img-valida-correo-${numCorreoAgregado}" src="/public/images/check.png"  title="correo valido" width="25" height="25"/>`+
                            `<button type="button" class="addDetalle btn btn-outline-dark btn-sm" onclick="eliminar(this)" title="Eliminar Detalle"><i class="fa fa-trash-o"></i></button>`;
            contenedor.appendChild(div);
            
            numCorreoAgregado++;
        }
        else{
            Ext.Msg.alert('Error', 'Ingrese Correo Electrónico para agregar otro correo'); 
        }
    }) 

/**
 * Método para eliminar el div contenedor del input
 * @param {this} e 
 */
const eliminar = (e) => {
    const divPadre = e.parentNode;
    contenedor.removeChild(divPadre);
    numCorreoAgregado--;
};



function guardar(){ 
    //Valida campos vacios
    if(validaCamposVacios(numCorreoAgregado))
    {   
        var strListaCorreos = '';
        strListaCorreos = obtenerCorreos(numCorreoAgregado);
        Ext.Msg.confirm('Alerta','¿Está seguro de detener '+
        'la suspensión de tratamiento del titular '+ $(input).val()+
        ' con correo '+strListaCorreos+'?', function(btn){
        if(btn=='yes'){
            ejecutaDetencion();
        }
        });
    }   
}

function ejecutaDetencion(){
    var strListaCorreos = '';
    Ext.MessageBox.wait("Guardando datos...");
    let tipoidentificacion = $('#clientetype_tipoIdentificacion').val() ; 
    let identificacion = $(input).val(); 
    strListaCorreos = obtenerCorreos(numCorreoAgregado);
    let params = obtenerDatosGridPoliticas();
    let strParametros = JSON.stringify(params); 
    $.ajax({
        type: "POST",
        data: { 
            correo: strListaCorreos,
            identificacion: identificacion,
            tipoidentificacion: tipoidentificacion,
            strMetodo: 'GuardarRespPoliticaClausula',
            strOpcion: strOpcion,
            strParametros: strParametros,
        },
        url: url_ejecuta_derechos_titular,
        success: function(response)
        {
            Ext.MessageBox.hide();
            var obj = JSON.parse(response); 
            var strMsjValidacion = obj.strMsjValidacion; 
            if(strMsjValidacion === "OK")
            {
                Ext.Msg.alert('Información: ', 'Se guardaron los cambios con éxito', function(btn){
                    if(btn=='ok'){
                        limpiaCampos(numCorreoAgregado);
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error: ', obj.strMsjObservacion, function(btn){
                    if(btn=='ok'){
                        limpiaCampos(numCorreoAgregado);
                    }
                }); 
            }
        },
        failure: function(response)
        {
            var obj = JSON.parse(response); 
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ',obj.strMsjObservacion); 
            limpiaCampos(numCorreoAgregado);
        }
    });
}

function obtenerDatosGridPoliticas()
{   
    let idDocumento = '';
    let respuestas = [];
    let referenciasDocumento = [];
    let storeAdmiPoliticaClausula = Ext.StoreMgr.lookup('idStoreAdminPoliticas_Clausulas');
    var countDataGrid = storeAdmiPoliticaClausula.getCount();
    let storeRespuestaAnterior = Ext.StoreMgr.lookup('storeRespuestaAnterior');

    if (storeRespuestaAnterior.getCount() == countDataGrid)
    {
        for (var i = 0; i < countDataGrid; i++)
        {   
        objDataAplicaDocumentoP = storeAdmiPoliticaClausula.getAt(i).data.aplicaDocumento;
        objDataClausula = storeAdmiPoliticaClausula.getAt(i).data.clausulas;
        var strValorRespuestaAnt = storeRespuestaAnterior.getAt(i).data.valor;
        var strIdEnunciadoAnt = storeRespuestaAnterior.getAt(i).data.idEnunciado;       
        if (objDataClausula.length) {
            objDataClausula.forEach(idxClausula => {
                let objDataAplicaDocumento = (idxClausula.aplicaDocumento || []);
                objDataAplicaDocumento.forEach(idxAplicaDoc => { 
                    let objDataRespuestaC = (idxAplicaDoc.respuestas || []);
                    objDataRespuestaC.forEach(idxResp => {                       
                        if (idxClausula.idEnunciado == strIdEnunciadoAnt)     
                        {
                            if(idxResp.valorRespuesta !== strValorRespuestaAnt)
                            {
                                respuestas.push({
                                    "idEnunciado" : idxClausula.idEnunciado,
                                    "idRespuesta" : idxResp.idRespuesta,
                                    "idDocEnunciadoResp" : idxResp.idDocEnunciadoResp
                                }) 
                            }
                        }
                    });
                    if (idDocumento.length == 0)
                    {idDocumento = idxAplicaDoc.idDocumento;}
                });
            });
            
        }else{
            
            objDataAplicaDocumentoP.forEach(idxAplicaDoc => { 
                let objDataRespuestaP = (idxAplicaDoc.respuestas || []);
                objDataRespuestaP.forEach(idxResp => { 
                    var strIdEnunciadoPol = storeAdmiPoliticaClausula.getAt(i).data.idEnunciado;
                   
                    if (strIdEnunciadoPol == strIdEnunciadoAnt)                    
                    {
                        if (idxResp.valorRespuesta !== strValorRespuestaAnt)
                        {
                            respuestas.push({
                                "idEnunciado" : storeAdmiPoliticaClausula.getAt(i).data.idEnunciado,
                                "idRespuesta" : idxResp.idRespuesta,
                                "idDocEnunciadoResp" : idxResp.idDocEnunciadoResp
                            }) 
                        }                        
                    }
                });
                if (idDocumento.length == 0)
                {idDocumento = idxAplicaDoc.idDocumento;}
            });
        }  
        }
    }else
    {
        for (var l = 0; l < countDataGrid; l++)
        {   
            objDataAplicaDocumentoP = storeAdmiPoliticaClausula.getAt(l).data.aplicaDocumento;
            objDataClausula = storeAdmiPoliticaClausula.getAt(l).data.clausulas; 
            var strIdEnunciadoPol = storeAdmiPoliticaClausula.getAt(l).data.idEnunciado;    
            if (objDataClausula.length) {
                objDataClausula.forEach(idxClausula => {
                    let objDataAplicaDocumento = (idxClausula.aplicaDocumento || []);
                    objDataAplicaDocumento.forEach(idxAplicaDoc => { 
                        let objDataRespuestaC = (idxAplicaDoc.respuestas || []);
                        objDataRespuestaC.forEach(idxResp => { 
                            for (var k = 0; k < storeRespuestaAnterior.getCount(); k++)
                            { 
                                var strValorRespuestaAnt = storeRespuestaAnterior.getAt(k).data.valor;
                                var strIdEnunciadoAnt = storeRespuestaAnterior.getAt(k).data.idEnunciado;                      
                                if (idxClausula.idEnunciado == strIdEnunciadoAnt)     
                                {
                                    if(idxResp.valorRespuesta !== strValorRespuestaAnt)
                                    {
                                        respuestas.push({
                                            "idEnunciado" : idxClausula.idEnunciado,
                                            "idRespuesta" : idxResp.idRespuesta,
                                            "idDocEnunciadoResp" : idxResp.idDocEnunciadoResp
                                        }) 
                                    }
                                }
                            }  
                            
                        });
                        if (idDocumento.length == 0)
                        {idDocumento = idxAplicaDoc.idDocumento;}
                    });
                });
                
            }else{
                
                objDataAplicaDocumentoP.forEach(idxAplicaDoc => { 
                    let objDataRespuestaP = (idxAplicaDoc.respuestas || []);
                    objDataRespuestaP.forEach(idxResp => { 
                    
                        for (var j = 0; j < storeRespuestaAnterior.getCount(); j++)
                        {
                            var strValorRespuestaAnt = storeRespuestaAnterior.getAt(j).data.valor;
                            var strIdEnunciadoAnt = storeRespuestaAnterior.getAt(j).data.idEnunciado;
                            if (strIdEnunciadoPol == strIdEnunciadoAnt)                    
                            {
                                if (idxResp.valorRespuesta !== strValorRespuestaAnt)
                                {
                                    respuestas.push({
                                        "idEnunciado" : storeAdmiPoliticaClausula.getAt(l).data.idEnunciado,
                                        "idRespuesta" : idxResp.idRespuesta,
                                        "idDocEnunciadoResp" : idxResp.idDocEnunciadoResp
                                    }) 
                                }                        
                            }
                        }
                    });
                    if (idDocumento.length == 0)
                    {idDocumento = idxAplicaDoc.idDocumento;}
                });
            }  
        } 
    }
    
    referenciasDocumento.push({
        "nombreReferencia" : "PERSONA",
        "valor" : currenIdentificacion
    }) 
    let params = {
        "idDocumento": idDocumento, 
        "respuestaCliente": respuestas,
        "referenciasDocumento": referenciasDocumento
    }
   
    return params;
}


