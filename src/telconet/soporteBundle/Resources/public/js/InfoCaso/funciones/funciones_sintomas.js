var arrayPaneles = new Array();
var arrayTituloPaneles = new Array();
var tipoCaso;
var idPuntoCliente;
var afectoServicio;
var jsonAfectadosServicios;
var casoIdAfectado;
var sintomaExiste = true;

//Limite para agregar afectados a la vez
var limiteAgregados = 500;

function existeRecordSintoma(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();

    for (var i = 0; i < num; i++)
    {
        nombre = grid.getStore().getAt(i).get('nombre_sintoma');
        if (nombre != "")
        {
            if ((nombre == myRecord.get('nombre_sintoma')))
            {
                existe = true;
                break;
            }
        }
    }
    return existe;
}

function obtenerSintomas()
{
    var array = new Object();
    array['total'] = gridSintomas.getStore().getCount();
    array['sintomas'] = new Array();
    var array_data = new Array();
    for (var i = 0; i < gridSintomas.getStore().getCount(); i++)
    {
        array_data.push(gridSintomas.getStore().getAt(i).data);
    }
    array['sintomas'] = array_data;
    return Ext.JSON.encode(array);
}

function validarSintomas()
{
    //RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN SINTOMA ANTERIOR... ANTES DE CREAR OTRO..
    var storeValida = Ext.getCmp("gridSintomas").getStore();
    var boolSigue = false;
    var boolSigue2 = false;

    if (storeValida.getCount() > 0)
    {
        var boolSigue_vacio = true;
        var boolSigue_igual = true;
        for (var i = 0; i < storeValida.getCount(); i++)
        {
            var id_sintoma = storeValida.getAt(i).data.id_sintoma;
            var nombre_sintoma = storeValida.getAt(i).data.nombre_sintoma;

            if (id_sintoma != "" && nombre_sintoma != "") { /*NADA*/
            }
            else {
                boolSigue_vacio = false;
            }

            if (i > 0)
            {
                for (var j = 0; j < i; j++)
                {
                    var id_sintoma_valida = storeValida.getAt(j).data.id_sintoma;
                    var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintoma;

                    if (id_sintoma_valida == id_sintoma || nombre_sintoma_valida == nombre_sintoma)
                    {
                        boolSigue_igual = false;
                    }
                }
            }
        }

        if (boolSigue_vacio) {
            boolSigue = true;
        }
        if (boolSigue_igual) {
            boolSigue2 = true;
        }
    }
    else
    {
        boolSigue = true;
        boolSigue2 = true;
    }

    if (boolSigue && boolSigue2)
    {
        return true;
    }
    else if (!boolSigue)
    {
        Ext.Msg.alert('Alerta ', "Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
        return false;
    }
    else if (!boolSigue2)
    {
        Ext.Msg.alert('Alerta ', "No puede ingresar el mismo sintoma! Debe modificar el registro repetido, antes de solicitar un nuevo sintoma");
        return false;
    }
    else
    {
        Ext.Msg.alert('Alerta ', "Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
        return false;
    }
}

function obtenerCriterios(sintoma)
{
    var array_criterios = new Object();
    array_criterios['total'] = gridCriterios.getStore().getCount();
    array_criterios['criterios'] = new Array();
    var array_data = new Array();
    for (var i = 0; i < gridCriterios.getStore().getCount(); i++)
    {
        array_data.push(gridCriterios.getStore().getAt(i).data);
    }

    array_criterios['criterios'] = array_data;

    for (var i = 0; i < gridSintomas.getStore().getCount(); i++)
    {
        if (gridSintomas.getStore().getAt(i).data.nombre_sintoma == sintoma)
            gridSintomas.getStore().getAt(i).data.criterios_sintoma = Ext.JSON.encode(array_criterios);

    }


}

function obtenerAfectados(sintoma)
{
    var array_afectados = new Object();
    array_afectados['total'] = gridAfectados.getStore().getCount();
    array_afectados['afectados'] = new Array();
    var array_data = new Array();
    for (var i = 0; i < gridAfectados.getStore().getCount(); i++)
    {
        array_data.push(gridAfectados.getStore().getAt(i).data);
    }

    array_afectados['afectados'] = array_data;

    if(afectoServicio === true)
    {
        // Implementar lógica
        jsonAfectadosServicios = Ext.JSON.encode(array_data);
    }
    else
    {
        for (var i = 0; i < gridSintomas.getStore().getCount(); i++)
        {
            if (gridSintomas.getStore().getAt(i).data.nombre_sintoma == sintoma)
                gridSintomas.getStore().getAt(i).data.afectados_sintoma = Ext.JSON.encode(array_afectados);

        }
    }
}

function obtenerAfectadosCaso()
{
    var numeroAfectados = gridAfectados.getStore().getCount();
    
    if(numeroAfectados>0)
    {
        var array_afectados = {};
        array_afectados['total']     = numeroAfectados;
        array_afectados['resultado'] = new Array();
        var array_data = new Array();
        for (var i = 0; i < gridAfectados.getStore().getCount(); i++)
        {
            array_data.push(JSON.parse(gridAfectados.getStore().getAt(i).data.json_afectados));           
        }

        array_afectados['resultado'] = array_data;
        
        return array_afectados;
    }
    else
    {
        return '';
    }
}

function presentarElementosPe(cantonId, tipoElemento)
{
    gridEncontrados.getStore().removeAll;

    storeRouter.proxy.extraParams = {cantonId: cantonId, tipoElemento: tipoElemento};
    storeRouter.load();
}

function presentarEncontrados(id, name, objId, objName, band, networkingWs, nombreElemento, anillo)
{
    var prefijoEmpresa    = "";
    var wsNetWorking      = "";
    var varNombreElemento = "";
    var varAnillo         = "";

    if (band === "Elemento")
    {        
        cri_opcion = 'Elementos';
     
        cri_detalle = 'Elemento: ' + name + ' | OPCION: ' + objName;

        cri_query = '';
        cri_query_2 = '';

        if(objName == "Ciudad")
        {
            wsNetWorking      = networkingWs;
            varNombreElemento = nombreElemento;
            varAnillo         = anillo;
        }
    }
    if (band === "planProducto")
    {
        cri_opcion = 'Productos';
     
        cri_detalle = 'Producto: ' + name + ' | OPCION: ' + objName;

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "ActivoFijo")
    {
        cri_opcion = 'Elemento';
     
        cri_detalle = 'Elemento: ' + name;


        cri_query = '';
        cri_query_2 = '';
    }
    if (band == "empleado")
    {
        prefijoEmpresa = Ext.getCmp('comboEmpresas').getValue();        
        cri_opcion = 'Empleado';
        
        cri_detalle = 'Empleado: ' + name;

        cri_query = '';
        cri_query_2 = '';
    }
    if (band == "servidor")
    {
        cri_opcion = 'Servidor';
     
        cri_detalle = 'Servidor: ' + name;

        cri_query = '';
        cri_query_2 = '';
    }
    if (band == "empleadoDepartamento")
    {
        prefijoEmpresa = Ext.getCmp('comboEmpresas').getValue();        
        cri_opcion = 'Departamento';
        
        cri_detalle = 'Departamento: ' + name;

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "empleado")
    {
        prefijoEmpresa = Ext.getCmp('comboEmpresas').getValue();        
        cri_opcion = 'Empleado';
        
        cri_detalle = 'Empleado: ' + name + ' | OPCION: Empleados';

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "servidor")
    {        
        cri_opcion = 'Servidor';
        
        cri_detalle = 'Servidor: ' + name + ' | OPCION: Servidores';

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "empleadoDepartamento")
    {
        prefijoEmpresa = Ext.getCmp('comboEmpresas').getValue();        
        cri_opcion = 'Departamento';
        
        cri_detalle = 'Departamento: ' + name;

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "servicios")
    {
        cri_opcion = 'Servicio';
        
        cri_detalle = 'Servicio: ' + name + ' | OPCION: Servicios';

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "proveedores")
    {        
        cri_opcion = 'Proveedor';
        
        cri_detalle = 'Proveedor: ' + name + ' | OPCION: Proveedores';

        cri_query = '';
        cri_query_2 = '';
    }
    if (band === "cliente")
    {        
        cri_opcion = 'Clientes';
        
        cri_detalle = 'Cliente: ' + name + ' | OPCION: Punto Cliente';

        cri_query = '';
        cri_query_2 = '';
    }

    gridEncontrados.getStore().removeAll;

    storeEncontrados.proxy.extraParams = {id_param: id, name_param: name, tipo_param: objId, band: band, prefijoEmpresa: prefijoEmpresa,
                                          wsNetWorking: wsNetWorking, nombreElemento: varNombreElemento, numeroAnillo: varAnillo};
    storeEncontrados.load();
}

function mostrarAfectados(tipoAfectado)
{
    if(tipoAfectado == 1)
    {
        if (Ext.getCmp("comboCiudadesBackbone").value != null)
        {
            presentarEncontrados(Ext.getCmp("comboCiudadesBackbone").getValue(),
                "SWITCH - " + Ext.getCmp("comboCiudadesBackbone").getRawValue(),
                'Ciudad',
                "Ciudad",
                "Elemento",
                "N",
                "",
                "");
        }
        else
        {
            Ext.Msg.alert('Alerta ', "Debe seleccionar una ciudad");
            return false;
        }
    }
    else if(tipoAfectado == 2)
    {
        presentarEncontrados(Ext.getCmp("comboCiudadesBackbone").getValue(),
            "SWITCH - "+Ext.getCmp("comboCiudadesBackbone").getRawValue(),
            'Ciudad',
            "Ciudad",
            "Elemento",
            "S",
            Ext.getCmp("comboRouter").getRawValue(),
            Ext.getCmp("comboAnillo").getValue());
    }
}

function ingresarCriterioAfectados()
{
    var arrayCriterios = {};
    
    if (cri_opcion)
    {
        arrayCriterios['criterio'] = cri_opcion;
        arrayCriterios['opcion']   = cri_detalle; 
        
        var cantidadAfectadosEnviar = smEncontrados.getSelection().length;                

        if (cantidadAfectadosEnviar > 0)
        {
            if(cantidadAfectadosEnviar <= limiteAgregados)
            {
                id_criterio = getIdCriterio();
                var r = Ext.create('Criterio', {
                    id_criterio_afectado: id_criterio,
                    caso_id: '',
                    criterio: cri_opcion,
                    opcion: cri_detalle
                });
                if (!existeCriterio(r, gridCriterios))
                {
                    storeCriterios.insert(0, r);
                }
                else
                {
                    id_criterio = getIdCriterioGuardado(r, gridCriterios);
                }

                arrayCriterios['id_criterio']   = id_criterio; 

                var arrayClientesAfectados = [];

                for (var i = 0; i < smEncontrados.getSelection().length; ++i)
                {               
                   arrayClientesAfectados.push({id_criterio  :id_criterio,
                                                id           :smEncontrados.getSelection()[i].get('id_parte_afectada'),
                                                afectado     :smEncontrados.getSelection()[i].get('nombre_parte_afectada'),
                                                idDescripcion:smEncontrados.getSelection()[i].get('id_descripcion_1'),
                                                descripcion  :smEncontrados.getSelection()[i].get('nombre_descripcion_1')
                                               });
                }

                arrayCriterios['afectados'] = arrayClientesAfectados;                                   

                var rAfectados = Ext.create('Afectado', {
                    id_criterio         : id_criterio,
                    id_afectado         : '',                
                    nombre_afectado     : "Afectados Criterio "+id_criterio,
                    json_afectados      : JSON.stringify(arrayCriterios),
                    descripcion_afectado: cri_opcion
                });                                 

                //Se guardan los afectados que no se hayan repetido
                //si se trata de un mismo criterio padre de sustituye la ultima seleccion para no repetir los registros
                if (!existeAfectado(rAfectados, gridAfectados))
                {
                    storeAfectados.removeAt(id_criterio-1);
                    storeAfectados.insert(id_criterio-1, rAfectados);
                }
                else
                {
                    Ext.Msg.alert("Alerta", "El Afectado ya fue agregado");
                }
            }
            else
            {
                Ext.Msg.alert("Alerta", "Solo puede agregar a la vez maximo "+limiteAgregados+" afectados");
            }
        }
        else
        {
            alert('Seleccione por lo menos un afectado de la lista');
        }
    }
    else
    {
        alert('Primero debe seleccionar algun criterio de busqueda para los afectados');
    }
}

function ingresarCriterio()
{    
    if (cri_opcion)
    {
        if (smEncontrados.getSelection().length > 0)
        {
            id_criterio = getIdCriterio();
            var r = Ext.create('Criterio', {
                id_criterio_afectado: id_criterio,
                caso_id: '',
                criterio: cri_opcion,
                opcion: cri_detalle
            });
            if (!existeCriterio(r, gridCriterios))
            {
                storeCriterios.insert(0, r);
            }
            else
            {
                id_criterio = getIdCriterioGuardado(r, gridCriterios);
            }

            var rAfectados = [];
            for (var i = 0; i < smEncontrados.getSelection().length; ++i)
            {
                var rAfectadosCreate = Ext.create('Afectado', {
                    id                  : '',
                    id_afectado         : smEncontrados.getSelection()[i].get('id_parte_afectada'),
                    id_criterio         : id_criterio,
                    id_afectado_descripcion: smEncontrados.getSelection()[i].get('id_descripcion_1'),
                    nombre_afectado     : smEncontrados.getSelection()[i].get('nombre_parte_afectada'),
                    descripcion_afectado: smEncontrados.getSelection()[i].get('nombre_descripcion_1'),
                    json_afectados      : ''
                });

                if (!existeAfectado(rAfectadosCreate, gridAfectados))
                {
                    rAfectados[i] = Ext.create('Afectado', {
                        id                  : '',
                        id_afectado         : smEncontrados.getSelection()[i].get('id_parte_afectada'),
                        id_criterio         : id_criterio,
                        id_afectado_descripcion: smEncontrados.getSelection()[i].get('id_descripcion_1'),
                        nombre_afectado     : smEncontrados.getSelection()[i].get('nombre_parte_afectada'),
                        descripcion_afectado: smEncontrados.getSelection()[i].get('nombre_descripcion_1'),
                        json_afectados      : ''
                    });
                }
            }
            storeAfectados.insert(0, rAfectados);
        }
        else
        {
            alert('Seleccione por lo menos un afectado de la lista');
        }
    }
    else
        alert('Primero debe seleccionar algun criterio de busqueda para los afectados');
}

function getIdCriterio() {
    var id = 0;
    if (storeCriterios.getCount() == 0)
        return 1;
    else {
        for (var i = 0; i < storeCriterios.getCount(); i++)
        {
            if (storeCriterios.getAt(i).get('id_criterio_afectado') > id)
                id = storeCriterios.getAt(i).get('id_criterio_afectado');
        }
    }
    return id + 1;
}

function getIdCriterioGuardado(myRecord, grid) {
    var id = 0;

    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var id_criterio_afectado = grid.getStore().getAt(i).get('id_criterio_afectado');
        var criterio = grid.getStore().getAt(i).get('criterio');
        var detalle = grid.getStore().getAt(i).get('opcion');

        if (criterio == myRecord.get('criterio') && detalle == myRecord.get('opcion'))
        {
            id = id_criterio_afectado;
            break;
        }
    }
    return id;
}

function existeCriterio(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var criterio = grid.getStore().getAt(i).get('criterio');
        var detalle = grid.getStore().getAt(i).get('opcion');

        if (criterio == myRecord.get('criterio') && detalle == myRecord.get('opcion'))
        {
            existe = true;
            //alert('Ya existe un criterio similar');
            break;
        }
    }
    return existe;
}

function existeAfectado(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var id_criterio          = grid.getStore().getAt(i).get('id_criterio');        
        var nombre_afectado      = grid.getStore().getAt(i).get('nombre_afectado');
        var descripcion_afectado = grid.getStore().getAt(i).get('descripcion_afectado');
        var json_afectado        = grid.getStore().getAt(i).get('json_afectados');                

        if (id_criterio == myRecord.get('id_criterio') && nombre_afectado == myRecord.get('nombre_afectado') && 
            descripcion_afectado == myRecord.get('descripcion_afectado') && json_afectado == myRecord.get('json_afectados'))
        {
            existe = true;            
            break;
        }
    }
    return existe;
}

function eliminarCriterio(datosSelect, storeAfectados)
{
    for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {
        var num = storeAfectados.getCount();
        for (var j = 0; j < storeAfectados.getCount(); j++)
        {
            if (storeAfectados.getAt(j).get('id_criterio') == datosSelect.getSelectionModel().getSelection()[i].get('id_criterio_afectado')) {
                storeAfectados.removeAt(j);
                j--;
            }

        }

        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }

}

function agregarAfectadosXSintoma(sintoma, panel) {

    var titulo1 = "Descripcion 1";
    var titulo2 = "Descripcion 2";
    var titulo3 = "Parte afectada";
    var titulo4 = "Equipos Afectados";
    var titulo5 = "Elementos";

    if (tipoCaso === 'Movilizacion')
    {
        titulo1 = "Modelo";
        titulo2 = "Marca";
        titulo3 = "Placa";
        titulo4 = "Vehiculos Afectados";
        titulo5 = "Activo Fijo";
    }
    else if (tipoCaso === 'Seguridad' && panel === "PanelEmpleados")
    {
        titulo1 = "Departamento";
        titulo2 = "Ciudad";
        titulo3 = "Nombre";
        titulo4 = "Empleados Afectados";
    }
    else if (tipoCaso === 'Seguridad' && panel === "PanelElementos")
    {
        titulo1 = "Modelo";
        titulo2 = "Marca";
        titulo3 = "Nombre";
        titulo5 = "Servidor";
    }
    else if (panel === "PanelServicios")
    {
        titulo3 = "Servicio/Producto";
        titulo1 = "Descripcion";
        titulo2 = "Estado";
    }
    else if (panel === "PanelProveedores")
    {
        titulo3 = "Proveedores";
        titulo1 = "Descripcion";
        titulo2 = "Estado";
    }

    if (seleccionarTipoCaso == "1")
    {
        if (sintoma == '')
        {
            Ext.Msg.alert('Alerta', 'Debe escoger un sintoma primero.');
            return;
        }

        string_html = "<table width='100%' border='0' class='box-section-content' >";
        string_html += "    <tr>";
        string_html += "        <td width='80%' colspan='6'><b>Buscar Afectados:</b></td>";
        string_html += "    </tr>";
        string_html += "    <tr><td colspan='6'>&nbsp;</td></tr>";
        string_html += "    <tr>";
        string_html += "        <td colspan='6'>";
        string_html += "            <table width='100%' border='0'>";
        string_html += "                <tr>";
        //Tabla para buscar afectados
        string_html += "                  <table width='100%' border='0' style='border-spacing: 10px;border-collapse: separate;'>";
        string_html += "                     <tr>";

        if (panel == "PanelElementos")
        {
            if (tipoCaso === 'Movilizacion' || tipoCaso === 'Seguridad')
            {
                string_html += "                    <td id='elementos' width='100%'>";
                string_html += "                        <table width='55%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                        style='vertical-align:top'>";
            }
            else
            {
                string_html += "                    <td id='elementos' width='100px'>";
                string_html += "                        <table width='100%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                        style='vertical-align:top'>";
            }
            string_html += "                            <tr>";
            string_html += "                                <td colspan='3' class='titulo-secundario'><b>" + titulo5 + "</b></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'>Elemento:</td>";
            string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
            string_html += "                                     <input style='width: 150px !important;' type='text' id='elemento' \n\
                                                                        class='x-form-field x-form-text' style=' -moz-user-select: text;' readonly>";
            string_html += "                                         <a onclick='buscarElementoPanel(true)' style='cursor:pointer;'>";
            string_html += "                                          <img src='/public/images/search.png' />\n\
                                                              </div></td>";
            string_html += "                            </tr>";
            if (tipoCaso !== 'Movilizacion' && tipoCaso !== 'Seguridad')
            {
                string_html += "                            <tr>";
                string_html += "                                <td width='40%'> Opcion:</td>";
                string_html += "                                <td width='60%'><div id='searchOpciones'></div></td>";
                string_html += "                            </tr>";
            }
            string_html += "                            <tr>";
            string_html += "                                <td ><input type='hidden' id='idElementohd' name='idElementohd'/></td>";
            string_html += "                            </tr>";
            string_html += "                        </table>";
            string_html += "                    </td>";
            if (tipoCaso !== 'Movilizacion' && tipoCaso !== 'Seguridad')
            {
                string_html += "                    <td id='productos' width='200px'>";

                string_html += "                        <table width='100%' height='95px' border='0' class='box-section-subcontent' \n\
                                                            style='vertical-align:top'>";
                string_html += "                            <tr>";
                string_html += "                                <td colspan='3' class='titulo-secundario'><b>Servicios/Productos</b></td>";
                string_html += "                            </tr>";
                string_html += "                            <tr>";
                string_html += "                                <td style='text-align:center'><input type='radio' name='afectadosSP' \n\
                                                                    value='planes'>Planes</td>";
                string_html += "                                <td style='text-align:center'><input type='radio' name='afectadosSP' \n\
                                                                    value='productos'>Productos</td>";
                string_html += "                            </tr>";
                string_html += "                                <td style='text-align:center' width='40%'>Opcion:</td>";
                string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
                string_html += "                                     <input style='width: 150px !important;' type='text' id='elementoPlanProd' \n\
                                                                        class='x-form-field x-form-text' style='-moz-user-select: text;' readonly>";
                string_html += "                                        <a onclick='buscarServiciosProductosPanel()' style='cursor:pointer;'>";
                string_html += "                                        <img src='/public/images/search.png'/>\n\
                                                                  </div></td>";
                string_html += "                            </tr>";
                string_html += "                            <tr>";
                string_html += "                                <td ><input type='hidden' id='idElementohd' name='idElementohd'/></td>";
                string_html += "                            </tr>";
                string_html += "                        </table>";
                string_html += "                    </td>";
            }
        }

        if (panel === "PanelEmpleados")
        {
            string_html += "                    <td id='elementos' width='100%'>";
            string_html += "                        <table width='120%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                         style='vertical-align:top'>";
            string_html += "                            <tr>";
            string_html += "                                <td colspan='3' class='titulo-secundario'><b>Empleado</b></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'> Empresa:</td>";
            string_html += "                                <td width='60%'><div id='searchEmpresa'></div></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'> Ciudad:</td>";
            string_html += "                                <td width='60%'><div id='searchCiudad'></div></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'> Departamento:</td>";
            string_html += "                                <td width='60%'><div id='searchDepartamento'></div></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'> Empleado:</td>";
            string_html += "                                <td width='60%'><div id='searchEmpleado'></div></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td></td>";
            string_html += "                                <td colspan='2' width='60%'></td>";
            string_html += "                            </tr>";
            string_html += "                        </table>";
            string_html += "                    </td>";
        }

        //Servicio de Clientes
        if (panel === "PanelServicios")
        {
            string_html += "                    <td id='elementos' width='100%'>";
            string_html += "                        <table width='55%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                        style='vertical-align:top'>";
            string_html += "                            <tr>";
            string_html += "                                <td colspan='3' class='titulo-secundario'><b>Servicios Afectados</b></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'>Servicios:</td>";
            string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
            string_html += "                                     <input style='width: 150px !important;' type='text' id='elemento' \n\
                                                                        class='x-form-field x-form-text' style='-moz-user-select: text;' readonly>";
            string_html += "                                         <a onclick='buscarServiciosClientePanel()' style='cursor:pointer;'>";
            string_html += "                                          <img src='/public/images/search.png' />\n\
                                                              </div></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td ><input type='hidden' id='idElementohd' name='idElementohd'/></td>";
            string_html += "                            </tr>";
            string_html += "                        </table>";
            string_html += "                    </td>";
        }

        //Proveedores Telconet
        if (panel === "PanelProveedores")
        {
            string_html += "                    <td id='elementos' width='100%'>";
            string_html += "                        <table width='55%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                        style='vertical-align:top'>";
            string_html += "                            <tr>";
            string_html += "                                <td colspan='3' class='titulo-secundario'>\n\
                                                                <b>Listado de Proveedores</b></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td width='40%'>Proveedores:</td>";
            string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
            string_html += "                                     <input style='width: 150px !important;' type='text' id='proveedores' \n\
                                                                        class='x-form-field x-form-text' style='-moz-user-select: text;' readonly>";
            string_html += "                                         <a onclick='buscarProveedoresPanel()' style='cursor:pointer;'>";
            string_html += "                                          <img src='/public/images/search.png' />\n\
                                                              </div></td>";
            string_html += "                            </tr>";
            string_html += "                            <tr>";
            string_html += "                                <td ><input type='hidden' id='idElementohd' name='idElementohd'/></td>";
            string_html += "                            </tr>";
            string_html += "                        </table>";
            string_html += "                    </td>";
        }

        string_html += "                    </tr>";
        string_html += "                  </table>";
        //Fin tabla para buscar afectados
        string_html += "                </tr>";
        string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
        string_html += "                <tr style='height:200px'>";
        string_html += "                    <td colspan='8'><div id='encontrados'></div></td>";
        string_html += "                </tr>";
        string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
        string_html += "                <tr>";
        string_html += "                    <td colspan='8' align='center'><input name='btn3' type='button' value='Agregar' class='btn-form' \n\
                                             onclick='ingresarCriterio()'/></td>";
        string_html += "                </tr>";
        string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
        string_html += "                <tr style='height:200px'>";
        string_html += "                    <td colspan='4'><div id='criterios'></div><input type='hidden' id='caso_criterios' \n\
                                                name='caso_criterios' value='' /></td>";
        string_html += "                    <td colspan='4'><div id='afectados'></div><input type='hidden' id='caso_afectados' \n\
                                                name='caso_afectados' value='' /></td>";
        string_html += "                </tr>";
        string_html += "            </table>";
        string_html += "        </td>";
        string_html += "    </tr>";
        string_html += "</table>";

        btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                if(afectoServicio !== undefined && afectoServicio)
                {
                    //Tengo que crear la logica para cambiar el estado del caso.
                    obtenerAfectados(sintoma);
                    Ext.Msg.show({
                                title: 'Confirmar',
                                msg: 'Está seguro de realizar esta acción ?',
                                buttons: Ext.Msg.YESNO,
                                icon: Ext.MessageBox.QUESTION,
                                buttonText: {
                                    yes: 'si', no: 'no'
                                },
                                fn: function(btn) {
                                    if (btn == 'yes') {
                                        Ext.MessageBox.wait('Guardando datos...');
                                        Ext.Ajax.request({
                                            timeout: 900000,
                                            url: url_putServicioAfectado,
                                            method: 'post',
                                            params: 
                                            {
                                                jsonAfectadosServicios  : jsonAfectadosServicios,
                                                casoId                  : casoIdAfectado
                                            },
                                            success: function(response) {
                                                Ext.MessageBox.hide();
                                                if(response.status !== 200) {
                                                    Ext.Msg.alert('Alerta', 'No se pudo afectar el servicio en el caso.');
                                                }
                                                win2.destroy();
                                            },
                                            failure: function(response)
                                            {
                                                Ext.MessageBox.hide();
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
                    obtenerCriterios(sintoma);
                    obtenerAfectados(sintoma);

                    criterios = gridCriterios.getStore().getCount();               

                    if (gridCriterios.getStore().getCount() == 0 || gridAfectados.getStore().getCount() == 0)
                        Ext.Msg.alert("Alerta", "Debe ingresar al menos una afectación para crear un caso.");
                    else
                        win2.destroy();
                }
            }
        });
        btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                win2.destroy();
            }
        });

        win2 = Ext.create('Ext.window.Window', {
            title: 'Agregar Afectados',
            modal: true,
            width: 940,
            height: 680,
            resizable: false,
            layout: 'fit',
            items: [
                {
                    xtype: 'panel',
                    width: 400,
                    html: '<div style="padding:6px;">' + string_html + '</div>'
                }
            ],
            buttonAlign: 'center',
            buttons: [btnguardar2, btncancelar2]
        }).show();

        Ext.define('Criterio', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_criterio_afectado', mapping: 'id_criterio_afectado'},
                {name: 'caso_id', mapping: 'caso_id'},
                {name: 'criterio', mapping: 'criterio'},
                {name: 'opcion', mapping: 'opcion'}
            ]
        });
        Ext.define('Afectado', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', mapping: 'id'},
                {name: 'id_afectado', mapping: 'id_afectado'},
                {name: 'id_criterio', mapping: 'id_criterio'},
                {name: 'id_afectado_descripcion', mapping: 'id_afectado_descripcion'},
                {name: 'nombre_afectado', mapping: 'nombre_afectado'},
                {name: 'descripcion_afectado', mapping: 'descripcion_afectado'},
                {name: 'json_afectados', mapping: 'json_afectados'}
            ]
        });


        // Grid encontrados
        storeEncontrados = new Ext.data.Store({
            pageSize: 200,
            total: 'total',
            proxy: {
                type: 'ajax',
                timeout: 1200000,
                url: 'getEncontrados',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'id_parte_afectada', mapping: 'id_parte_afectada'},
                    {name: 'nombre_parte_afectada', mapping: 'nombre_parte_afectada'},
                    {name: 'id_descripcion_1', mapping: 'id_descripcion_1'},
                    {name: 'nombre_descripcion_1', mapping: 'nombre_descripcion_1'},
                    {name: 'id_descripcion_2', mapping: 'id_descripcion_2'},
                    {name: 'nombre_descripcion_2', mapping: 'nombre_descripcion_2'}
                ]
        });
        smEncontrados = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true
        });
        gridEncontrados = Ext.create('Ext.grid.Panel', {
            width: 910,
            height: 200,
            store: storeEncontrados,
            viewConfig: {enableTextSelection: true},
            forceFit: true,
            autoRender: true,
            id: 'gridEncontrados',
            loadMask: true,
            frame: true,
            resizable: false,
            enableColumnResize: false,
            iconCls: 'icon-grid',
            selModel: smEncontrados,
            columns: [
                Ext.create('Ext.grid.RowNumberer'),
                {
                    id: 'id_parte_afectada',
                    header: 'IdItemMenu',
                    dataIndex: 'id_parte_afectada',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'nombre_parte_afectada',
                    header: titulo3,
                    dataIndex: 'nombre_parte_afectada',
                    width: 400,
                    sortable: true
                },
                {
                    id: 'id_descripcion_1',
                    header: 'IdItemMenu',
                    dataIndex: 'id_descripcion_1',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'nombre_descripcion_1',
                    header: titulo1,
                    dataIndex: 'nombre_descripcion_1',
                    width: 250,
                    sortable: true
                },
                {
                    id: 'id_descripcion_2',
                    header: 'IdItemMenu',
                    dataIndex: 'id_descripcion_2',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'nombre_descripcion_2',
                    header: titulo2,
                    dataIndex: 'nombre_descripcion_2',
                    width: 250,
                    sortable: true
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeEncontrados,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'encontrados'
        });

        ////////////////Grid  Criterios////////////////
        storeCriterios = new Ext.data.JsonStore(
            {
                pageSize: 200,
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: 'getCriterios2',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        id: '',
                        id_sintoma: sintoma,
                        id_hipotesis: 'NO'
                    }
                },
                fields:
                    [
                        {name: 'id_criterio_afectado', mapping: 'id_criterio_afectado'},
                        {name: 'caso_id', mapping: 'caso_id'},
                        {name: 'criterio', mapping: 'criterio'},
                        {name: 'opcion', mapping: 'opcion'}
                    ]
            });
        smCriterios = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                selectionchange: function(sm, selections) {
                    gridCriterios.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        })
        gridCriterios = Ext.create('Ext.grid.Panel', {
            title: 'Criterios de Seleccion',
            width: 450,
            height: 200,
            autoRender: true,
            enableColumnResize: false,
            id: 'gridCriterios',
            store: storeCriterios,
            viewConfig: {enableTextSelection: true},
            loadMask: true,
            frame: true,
            forceFit: true,
            iconCls: 'icon-grid',
            selModel: smCriterios,
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarCriterio(gridCriterios, storeAfectados);
                            }
                        }]
                }],
            columns: [
                {
                    id: 'id_criterio_afectado',
                    header: 'id_criterio_afectado',
                    dataIndex: 'id_criterio_afectado',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'caso_id',
                    header: 'caso_id',
                    dataIndex: 'caso_id',
                    hidden: true,
                    sortable: true
                },
                {
                    id: 'criterio',
                    header: 'Criterio',
                    dataIndex: 'criterio',
                    width: 100,
                    hideable: false
                },
                {
                    id: 'opcion',
                    header: 'Opcion',
                    dataIndex: 'opcion',
                    width: 300,
                    sortable: true
                }
            ],
            renderTo: 'criterios'
        });


        ////////////////Grid  Afectados////////////////
        storeAfectados = new Ext.data.JsonStore(
            {
                pageSize: 4000,
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: 'getAfectados2',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        id: '',
                        id_sintoma: sintoma,
                        id_hipotesis: 'NO'
                    }
                },
                fields:
                    [
                        {name: 'id', mapping: 'id'},
                        {name: 'id_afectado', mapping: 'id_afectado'},
                        {name: 'id_criterio', mapping: 'id_criterio'},
                        {name: 'id_afectado_descripcion', mapping: 'id_afectado_descripcion'},
                        {name: 'nombre_afectado', mapping: 'nombre_afectado'},
                        {name: 'descripcion_afectado', mapping: 'descripcion_afectado'},
                        {name: 'json_afectados', mapping: 'json_afectados'}
                    ]
            });

        gridAfectados = Ext.create('Ext.grid.Panel', {
            title: titulo4,
            width: 450,
            height: 200,
            sortableColumns: false,
            store: storeAfectados,
            viewConfig: {enableTextSelection: true},
            id: 'gridAfectados',
            enableColumnResize: false,
            loadMask: true,
            frame: true,
            forceFit: true,
            iconCls: 'icon-grid',
            columns: [
                Ext.create('Ext.grid.RowNumberer'),
                {
                    id: 'id',
                    header: 'id',
                    dataIndex: 'id',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_afectado',
                    header: 'id_afectado',
                    dataIndex: 'id_afectado',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_criterio',
                    header: 'id_criterio',
                    dataIndex: 'id_criterio',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_afectado_descripcion',
                    header: 'id_afectado_descripcion',
                    dataIndex: 'id_afectado_descripcion',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'nombre_afectado',
                    header: titulo3,
                    dataIndex: 'nombre_afectado',
                    width: 250
                },
                {
                    id: 'descripcion_afectado',
                    header: titulo1,
                    dataIndex: 'descripcion_afectado',
                    width: 150
                }
            ],
            renderTo: 'afectados'
        });

        if (tipoCaso !== 'Movilizacion' && panel == "PanelElementos" && tipoCaso !== 'Seguridad')
        {
            /* STORES CAPA 2 --- ELEMENTOS */
            storeOpciones = Ext.create('Ext.data.Store', {
                fields: ['opcion', 'nombre'],
                data: [
                    {"opcion": "Puertos", "nombre": "Puertos"},
                    {"opcion": "Logines", "nombre": "Punto Cliente"},
                    {"opcion": "Ninguna", "nombre": "Ninguna"}
                ]
            });
            comboOpciones = Ext.create('Ext.form.ComboBox', {
                id: 'comboOpciones',
                store: storeOpciones,
                queryMode: 'local',
                valueField: 'opcion',
                displayField: 'nombre',
                listeners: {
                    select: function(combo) {
                        presentarEncontrados(document.getElementById("idElementohd").value,
                            document.getElementById("elemento").value,
                            combo.getValue(),
                            combo.getRawValue(),
                            "Elemento");
                    }
                },
                renderTo: 'searchOpciones'
            });
            Ext.getCmp('comboOpciones').setDisabled(true);
        }

        if (panel === "PanelEmpleados")
        {
            var storeEmpresas = new Ext.data.Store({
                total: 'total',
                pageSize: 200,
                proxy: {
                    type: 'ajax',
                    method: 'post',
                    url: url_empresaPorSistema,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        app: 'TELCOS'
                    }
                },
                fields:
                    [
                        {name: 'opcion', mapping: 'nombre_empresa'},
                        {name: 'valor', mapping: 'prefijo'}
                    ]
            });

            comboEmpresa = Ext.create('Ext.form.ComboBox', {
                id: 'comboEmpresas',
                store: storeEmpresas,
                displayField: 'opcion',
                valueField: 'valor',
                fieldLabel: false,
                queryMode: "remote",
                emptyText: '',
                width: 200,
                border: 0,
                margin: 0,
                listeners: {
                    select: function(combo) {
                        Ext.getCmp('comboCiudades').setDisabled(false);
                        Ext.getCmp('comboCiudades').setRawValue("");
                        Ext.getCmp('comboCiudades').value = "";
                        Ext.getCmp('comboDepartamento').setDisabled(true);
                        Ext.getCmp('comboDepartamento').setRawValue("");
                        Ext.getCmp('comboDepartamento').value = "";
                        Ext.getCmp('comboEmpleado').setDisabled(true);
                        Ext.getCmp('comboEmpleado').setRawValue("");
                        Ext.getCmp('comboEmpleado').value = "";
                        presentarCiudades(combo.getValue());
                    }
                },
                renderTo: 'searchEmpresa'
            });

            storeCiudades = new Ext.data.Store({
                total: 'total',
                pageSize: 200,
                proxy: {
                    type: 'ajax',
                    method: 'post',
                    url: 'getCiudadesPorEmpresa',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'Activo'
                    }
                },
                fields:
                    [
                        {name: 'id_canton', mapping: 'id_canton'},
                        {name: 'nombre_canton', mapping: 'nombre_canton'}
                    ]
            });
            comboCiudades = Ext.create('Ext.form.ComboBox', {
                id: 'comboCiudades',
                store: storeCiudades,
                displayField: 'nombre_canton',
                valueField: 'id_canton',
                fieldLabel: false,
                queryMode: "remote",
                emptyText: '',
                disabled: true,
                width: 200,
                border: 0,
                margin: 0,
                listeners: {
                    select: function(combo) {
                        Ext.getCmp('comboDepartamento').setDisabled(false);
                        Ext.getCmp('comboDepartamento').setRawValue("");
                        Ext.getCmp('comboDepartamento').value = "";
                        Ext.getCmp('comboEmpleado').setDisabled(true);
                        Ext.getCmp('comboEmpleado').setRawValue("")
                        empresa = Ext.getCmp('comboEmpresas').getValue();
                        presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                    }
                },
                renderTo: 'searchCiudad'
            });
            storeDepartamentosCiudad = new Ext.data.Store({
                total: 'total',
                pageSize: 200,
                proxy: {
                    type: 'ajax',
                    method: 'post',
                    url: 'getDepartamentosPorEmpresaYCiudad',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'Activo'
                    }
                },
                fields:
                    [
                        {name: 'id_departamento', mapping: 'id_departamento'},
                        {name: 'nombre_departamento', mapping: 'nombre_departamento'}
                    ]
            });

            comboDepartamento = Ext.create('Ext.form.ComboBox', {
                id: 'comboDepartamento',
                store: storeDepartamentosCiudad,
                displayField: 'nombre_departamento',
                valueField: 'id_departamento',
                fieldLabel: false,
                queryMode: "remote",
                minChars: 3,
                emptyText: '',
                disabled: true,
                width: 200,
                border: 0,
                margin: 0,
                listeners: {
                    select: function(combo) {
                        Ext.getCmp('comboEmpleado').setDisabled(false);
                        empresa = Ext.getCmp('comboEmpresas').getValue();
                        canton = Ext.getCmp('comboCiudades').getValue();
                        Ext.getCmp('comboEmpleado').value = "";
                        Ext.getCmp('comboEmpleado').setRawValue("");
                        presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, '', 'no');
                        presentarEncontrados(combo.getValue(), combo.getRawValue(), canton, '', "empleadoDepartamento");

                    }
                },
                renderTo: 'searchDepartamento'
            });

            storeEmpleados = new Ext.data.Store({
                total: 'total',
                pageSize: 1000,
                proxy: {
                    type: 'ajax',
                    url: url_empleadosDepartamento,
                    reader: {
                        type: 'json',
                        totalProperty: 'result.total',
                        root: 'result.encontrados',
                        metaProperty: 'myMetaData'
                    },
                    extraParams: {
                        nombre: ''
                    }
                },
                fields:
                    [
                        {name: 'id_empleado', mapping: 'id_empleado'},
                        {name: 'nombre_empleado', mapping: 'nombre_empleado'}
                    ]
            });


            comboEmpleados = Ext.create('Ext.form.ComboBox', {
                id: 'comboEmpleado',
                store: storeEmpleados,
                displayField: 'nombre_empleado',
                valueField: 'id_empleado',
                fieldLabel: false,
                queryMode: "remote",
                emptyText: '',
                disabled: true,
                width: 250,
                border: 0,
                margin: 0,
                listeners: {
                    select: function() {
                        var comboEmpleado = Ext.getCmp('comboEmpleado').value;
                        var valoresComboEmpleado = comboEmpleado.split("@@");
                        var idEmpleado = valoresComboEmpleado[0];
                        var nombreEmpleado = Ext.getCmp('comboEmpleado').getRawValue();
                        presentarEncontrados(idEmpleado, nombreEmpleado, '', '', "empleado");
                    }
                },
                renderTo: 'searchEmpleado'
            });

        }

    }
    else
    {
        Ext.Msg.alert('Alerta', 'Debe seleccionar un Tipo de Caso');
        return;
    }
}

function presentarCiudades(empresa)
{
    storeCiudades.proxy.extraParams = {empresa: empresa};
    storeCiudades.load();
}

function presentarDepartamentosPorCiudad(id_canton, empresa)
{
    storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
    storeDepartamentosCiudad.load();
}

function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, esJefe)
{
    storeEmpleados.proxy.extraParams = {id_canton: id_canton, empresa: empresa, id_departamento: id_departamento, es_jefe: esJefe};
    storeEmpleados.load()
}

function presentarDepartamentosPorCiudad(id_canton, empresa)
{
    storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
    storeDepartamentosCiudad.load();
}

function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, esJefe)
{
    storeEmpleados.proxy.extraParams = {id_canton: id_canton, empresa: empresa, id_departamento: id_departamento, es_jefe: esJefe};
    storeEmpleados.load();
}

function buscarServiciosProductosPanel()
{
    document.getElementById('elemento').value = "";
    Ext.getCmp('comboOpciones').value = "";
    Ext.getCmp('comboOpciones').setRawValue("");
    Ext.getCmp('comboOpciones').setDisabled(true);

    var rdbtnCked = ($('input:radio[name=afectadosSP]:checked').val());

    if (typeof rdbtnCked === 'undefined')
    {
        Ext.Msg.alert('Alerta', 'Debe seleccionar si desea buscar Planes o Productos');
        return;
    }

    storePlanesProductos = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        autoLoad: true,
        proxy: {
            timeout: 100000,
            type: 'ajax',
            url: url_getPlanesProductos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                esPlan: rdbtnCked === 'planes' ? true : false
            }
        },
        fields:
            [
                {name: 'codigo', mapping: 'codigo'},
                {name: 'nombrePlanProducto', mapping: 'nombre'},
                {name: 'descripcionPlanProducto', mapping: 'descripcion'},
                {name: 'idPlanProducto', mapping: 'id'}
            ]
    });

    gridPlanesProductos = Ext.create('Ext.grid.Panel', {
        width: 550,
        height: 300,
        store: storePlanesProductos,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idPlanProducto',
                header: 'idPlanProducto',
                dataIndex: 'idPlanProducto',
                hidden: true,
                hideable: false
            },
            {
                header: 'Codigo',
                dataIndex: 'codigo',
                width: 70,
                sortable: true
            },
            {
                header: 'Nombre Plan/Producto',
                dataIndex: 'nombrePlanProducto',
                width: 200,
                sortable: true
            },
            {
                header: 'Descripcion',
                dataIndex: 'descripcionPlanProducto',
                width: 200,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            if (rec.get('nombrePlanProducto') !== "Todos" || rec.get('nombrePlanProducto') !== "") {
                                return 'button-grid-seleccionar';
                            }
                            else {
                                return 'button-grid-invisible';
                            }
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            document.getElementById('idElementohd').value = grid.getStore().getAt(rowIndex).data.idPlanProducto;
                            document.getElementById('elementoPlanProd').value = grid.getStore().getAt(rowIndex).data.nombrePlanProducto;

                            presentarEncontrados(document.getElementById("idElementohd").value,
                                document.getElementById("elementoPlanProd").value,
                                rdbtnCked === 'planes' ? true : false,
                                'Punto Cliente',
                                "planProducto");

                            winPlanesProducto.destroy();
                        }
                    }
                ]
            }
        ]
    });

    var formPanelPlanProducto = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 550
                },
                items: [
                    gridPlanesProductos
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    winPlanesProducto.destroy();
                }
            }]
    });

    var winPlanesProducto = Ext.create('Ext.window.Window', {
        title: "Planes/Productos",
        modal: true,
        width: 600,
        closable: true,
        layout: 'fit',
        items: [formPanelPlanProducto]
    }).show();
}

function buscarServiciosClientePanel()
{
    storeServcios = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        autoLoad: true,
        proxy: {
            timeout: 100000,
            type: 'ajax',
            url: url_getServiciosPorCliente,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idPunto: idPuntoCliente
            }
        },
        fields:
            [
                {name: 'idServicio', mapping: 'idServicio'},
                {name: 'nombreProducto', mapping: 'nombreProducto'},
                {name: 'estadoServicio', mapping: 'estado'},
                {name: 'loginAux', mapping: 'loginAux'}
            ]
    });

    gridServicios = Ext.create('Ext.grid.Panel', {
        width: 500,
        height: 294,
        store: storeServcios,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idServicio',
                header: 'idServicio',
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Servicio/Producto',
                dataIndex: 'nombreProducto',
                width: 160,
                sortable: true
            },
            {
                header: 'Login Aux',
                dataIndex: 'loginAux',
                width: 160,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estadoServicio',
                width: 90,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            if (rec.get('nombreProducto') !== "Todos" || rec.get('nombreProducto') !== "") {
                                return 'button-grid-seleccionar';
                            }
                            else {
                                return 'button-grid-invisible';
                            }
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            document.getElementById('idElementohd').value = grid.getStore().getAt(rowIndex).data.idServicio;
                            document.getElementById('elemento').value = grid.getStore().getAt(rowIndex).data.nombreProducto;

                            presentarEncontrados(document.getElementById("idElementohd").value,
                                document.getElementById("elemento").value,
                                idPuntoCliente,
                                '',
                                "servicios");

                            winServicios.destroy();
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeServcios,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 354
                },
                items: [
                    gridServicios
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    winServicios.destroy();
                }
            }]
    });

    var winServicios = Ext.create('Ext.window.Window', {
        title: "Servicios/Productos",
        modal: true,
        width: 550,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

//Busqueda de afectacion por proveedores
function buscarProveedoresPanel()
{
    storeProveedores = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        autoLoad: true,
        proxy: {
            timeout: 100000,
            type: 'ajax',
            url: url_getProveedores,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                rol: 'Proveedor Internacional'
            }
        },
        fields:
            [
                {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
            ]
    });

    gridProveedores = Ext.create('Ext.grid.Panel', {
        width: 480,
        height: 294,
        store: storeProveedores,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idProveedor',
                header: 'id_empresa_externa',
                dataIndex: 'id_empresa_externa',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Proveedor',
                dataIndex: 'nombre_empresa_externa',
                width: 420,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            if (rec.get('nombre_empresa_externa') !== "")
                            {
                                return 'button-grid-seleccionar';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            document.getElementById('idElementohd').value = grid.getStore().getAt(rowIndex).data.id_empresa_externa;
                            document.getElementById('proveedores').value = grid.getStore().getAt(rowIndex).data.nombre_empresa_externa;

                            presentarEncontrados(document.getElementById("idElementohd").value,
                                document.getElementById("proveedores").value,
                                '',
                                '',
                                "proveedores");

                            winServicios.destroy();
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeProveedores,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 480
                },
                items: [
                    gridProveedores
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    winServicios.destroy();
                }
            }]
    });

    var winServicios = Ext.create('Ext.window.Window', {
        title: "Proveedores",
        modal: true,
        width: 520,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function buscarElementoPanel(esAgregadoInicial)
{
    if (tipoCaso !== 'Movilizacion' && tipoCaso !== 'Seguridad' && esAgregadoInicial)
    {
        document.getElementById("elementoPlanProd").value = "";
    }
    var valorActivo = "";
    if (tipoCaso === 'Movilizacion')
        valorActivo = "S";
    if (tipoCaso === 'Seguridad')
        valorActivo = "Se";

    storeTipoElementos = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getTipoElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                activoFijo: valorActivo
            }
        },
        fields:
            [
                {name: 'idTipoElemento', mapping: 'idTipoElemento'},
                {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'}
            ]
    });

    storeTipoElementos.load();

    comboTipoElemento = Ext.create('Ext.form.ComboBox', {
        id: 'comboTipoElemento',
        store: storeTipoElementos,
        displayField: 'nombreTipoElemento',
        valueField: 'idTipoElemento',
        fieldLabel: 'Tipo Elemento',
        queryMode: "remote",
        emptyText: '',
        height: 30,
        border: 0,
        margin: 0,
        listeners: {
            select: function() {
                if (tipoCaso !== 'Movilizacion' && tipoCaso !== 'Seguridad')
                {
                    document.getElementById('idElemento').value = "";
                    document.getElementById('elemento').value = "";
                    Ext.getCmp("txtNombre").setValue("");
                    buscarElemento("N");
                }
                else if (tipoCaso === 'Movilizacion')
                {
                    document.getElementById('idElemento').value = "";
                    document.getElementById('elemento').value = "";
                    buscarElemento("S");
                }
            }
        }
    });


    storeElementos = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        proxy: {
            timeout: 100000,
            type: 'ajax',
            url: url_getElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'estado', mapping: 'estado'}
            ]
    });

    gridElementosBusq = Ext.create('Ext.grid.Panel', {
        width: 530,
        height: 294,
        store: storeElementos,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Elemento',
                dataIndex: 'nombreElemento',
                width: 160,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 90,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            if (rec.get('nombreElemento') !== "Todos") {
                                return 'button-grid-seleccionar';
                            }
                            else {
                                return 'button-grid-invisible';
                            }
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            document.getElementById('idElementohd').value = grid.getStore().getAt(rowIndex).data.idElemento;
                            document.getElementById('elemento').value = grid.getStore().getAt(rowIndex).data.nombreElemento;
                            if (tipoCaso !== 'Movilizacion' && tipoCaso !== 'Seguridad')
                            {
                                Ext.getCmp('comboOpciones').setDisabled(false);
                                Ext.getCmp('comboOpciones').reset();
                            }
                            else if (tipoCaso === 'Movilizacion' || tipoCaso === 'Seguridad')
                            {
                                var tipo = '';
                                if (tipoCaso === 'Movilizacion')
                                    tipo = 'ActivoFijo';
                                else if (tipoCaso === 'Seguridad')
                                    tipo = 'servidor';
                                presentarEncontrados(document.getElementById("idElementohd").value, document.getElementById("elemento").value, '',
                                    '', tipo);
                            }
                            win.destroy();
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeElementos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    textField = new Ext.form.TextField({
        id: 'txtNombre',
        fieldLabel: 'Elemento',
        value: '',
        width: '30%',
        enableKeyEvents: true,
        listeners: {
            keyup: function(form, e) {
                if (tipoCaso !== 'Movilizacion')
                    buscarElemento("N");
                else if (tipoCaso === 'Movilizacion')
                    buscarElemento("S");
            }
        }
    });

    filterPanelElementosBusq = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders        
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 530,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    if (tipoCaso !== 'Movilizacion')
                        buscarElemento("N");
                    else if (tipoCaso === 'Movilizacion')
                        buscarElemento("S");
                }
            }

        ],
        items: [
            {width: '10%', border: false},
            comboTipoElemento,
            {width: '20%', border: false},
            textField,
            {width: '10%', border: false}
        ]
    });


    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 530
                },
                items: [
                    filterPanelElementosBusq,
                    gridElementosBusq
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: "Elementos",
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function buscarElemento(band)
{
    if (Ext.getCmp("comboTipoElemento").getValue())
    {
        storeElementos.proxy.extraParams = {
            tipoElemento: Ext.getCmp("comboTipoElemento").getValue(),
            nombreElemento: Ext.getCmp("txtNombre").getValue(),
            activoFijo: band
        };
        storeElementos.load();
    }
    else
    {
        Ext.Msg.alert("Alerta", "Debe escoger un tipo de elemento");
    }
}

var seleccionarTipoCaso = '0';

function setearTipoCasoId(id)
{
    //Se setea la variable para identificar que se ha seleccionado un Tipo de Caso
    seleccionarTipoCaso = '1';
    var connTipoCaso = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Consultando Sintomas',
                        progressText: 'Cargando...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connTipoCaso.request({
        url: url_bandera_Panel,
        method: 'post',
        params:
            {
                tipoCaso: id.value
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);

            arrayPaneles = new Array();
            arrayTituloPaneles = new Array();

            tipoCaso = $("#telconet_schemabundle_infocasotype_tipoCasoId option:selected").text();

            for (var i = 0; i < text.length; i++)
            {
                arrayPaneles.push(text[i].panel);
                arrayTituloPaneles.push(text[i].titulo);
            }

            criterios = 0;
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });

    storeSintomas.removeAll();
    comboSintomaStore.proxy.extraParams = {tipoCaso: id.value, estado: 'Activo'};
    comboSintomaStore.load();

    comboTipoBackbone = Ext.create('Ext.form.ComboBox',
    {
        id:'comboTipoBackbone',
        value:'URBANOS',
        store: [
                ['URBANOS', 'URBANOS'],
                ['INTERURBANOS', 'INTERURBANOS']
               ],
        displayField: 'nombreTipoBackbone',
        valueField: 'idTipoBackbone',
        fieldLabel: false,
        width: 145,
        queryMode: "remote",
        emptyText: '',
        renderTo: 'combo_tipo_backbone',
        hidden: true
    });

    if($("#telconet_schemabundle_infocasotype_tipoCasoId option:selected").text() === "Backbone" && $('#empresa').val() === "TN")
    {
        $("#label_tipo_backbone").html("* Tipo Backbone: ");
        Ext.getCmp('comboTipoBackbone').setVisible(true);
    }
    else
    {
        $("#label_tipo_backbone").html("");
        $("#combo_tipo_backbone").html("");
        Ext.getCmp('comboTipoBackbone').setVisible(false);
    }

    if($("#telconet_schemabundle_infocasotype_tipoCasoId option:selected").text() === "Backbone" 
       && ($('#empresa').val() === "MD"||$('#empresa').val() === "EN"))
    {
        $("#label_mante_programado").css("display", "block");
        $("#check_mante_programado").css("display", "block");
    }
    else
    { 
        $("#check_mante_programado").prop("checked", false);
        $("#check_mante_programado").prop("value", "N");
        $("#label_mante_programado").css("display", "none");
        $("#check_mante_programado").css("display", "none");
    } 
}
//psvelez
$(document).ready(function(){
 
    $("#check_mante_programado").click(function(){
        if ($("#check_mante_programado").attr("checked")){
            $("#check_mante_programado").prop("value", "S");
            creaGridManteProgramado();
         }
    });  

 });    

function creaGridManteProgramado()
{
    if (typeof  storeTipoAfectacion == 'undefined')
    {
        var storeTipoAfectacion = new Ext.data.Store({
            autoLoad: false,
            proxy: {
                    type: 'ajax',
                    method: 'post',
                    url: url_getTipoAfectacion,
                    reader: {
                        type: 'json'
                    }
                },
                fields:
		        [
			        {name:'idNotificacion', mapping:'idParametro'},
	                {name:'nombreTipoNotificacion', mapping:'nombreParametro'}
		        ],
            }); 
    }

    if (typeof  storeTipoNotificacion == 'undefined')
    {
        var storeTipoNotificacion= new Ext.data.Store({
            autoLoad: false,
            proxy: {
                    type: 'ajax',
                    method: 'post',
                    url: url_getTipoNotificacion,
                    reader: {
                        type: 'json'
                    }
                },
                fields:
		        [
			        {name:'idNotificacion', mapping:'idParametro'},
	                {name:'nombreTipoNotificacion', mapping:'nombreParametro'}
		        ],
            }); 
    }

    if (typeof  Ext.getCmp('wincreaGridManteProgram') !== 'undefined')
    {
        Ext.getCmp('idTipoAfectacion').setValue("");
        Ext.getCmp('idTipoNotificacion').setValue("");
        Ext.getCmp('idFechaInicio').setValue("");
        Ext.getCmp('idFechaFin').setValue("");
        Ext.getCmp('idHoraInicio').setValue("");
        Ext.getCmp('idHoraFin').setValue("");
        Ext.getCmp('idTiempoAfectacion').setValue("");
        Ext.getCmp('wincreaGridManteProgram').show();
    }
    else
    {    
        var cmbTipoAfectacion = Ext.create('Ext.form.ComboBox', {
            id:'idTipoAfectacion',
            store: storeTipoAfectacion,
            displayField: 'nombreTipoNotificacion',
            valueField: 'idNotificacion',
            border:0,
            margin:0,
            fieldLabel: 'Tipo Afectación:',
            queryMode: 'remote'
            });

            var cmbTipoNotificacion = Ext.create('Ext.form.ComboBox', {
            id:'idTipoNotificacion',
            store: storeTipoNotificacion,
            displayField: 'nombreTipoNotificacion',
            valueField: 'idNotificacion',
            border:0,
            margin:0,
            fieldLabel: 'Tipo Notificación:',
            queryMode: 'remote'
            });
        var txtFechaInicio = Ext.create('Ext.form.DateField',{  
            id:  'idFechaInicio',
            name:'idFechaInicio',
            fieldLabel: '*Fecha Inicio:',     
            format: 'Y-m-d',
            editable: false,
            value:new Date(),
            minValue: new Date()
        });

        var txtFechaFin = Ext.create('Ext.form.DateField',{  
            id:  'idFechaFin',
            name:'idFechaFin',
            fieldLabel: '*Fecha Fin:',     
            format: 'Y-m-d',
            editable: false,
            value:new Date(),
            minValue: new Date()
        });

        var txtHoraInicio = Ext.create('Ext.form.TimeField',{  
            id: 'idHoraInicio', 
            name: 'idHoraInicio',
            fieldLabel: '*Hora Inicio:',     
            format: 'H:i',
            minValue: '00:01 AM',
            maxValue: '23:59 PM',
            increment: 1,						
            editable: true,
            value: new Date()
        });

        var txtHoraFin = Ext.create('Ext.form.TimeField',{  
            id: 'idHoraFin',
            fieldLabel: '*Hora Fin:',     
            format: 'H:i',
            name: 'idHoraFin',
            minValue: '00:01 AM',
            maxValue: '23:59 PM',
            increment: 1,						
            editable: true,
            value:new Date()
        });

        var txtTiempoAfectacion = Ext.create('Ext.form.TimeField',{   
            id: 'idTiempoAfectacion',
            name: 'idTiempoAfectacion',
            fieldLabel: '*Tiempo Afectación:',     
            format: 'H:i',
            minValue: '00:00',
            maxValue: '23:30',
            editable: true,
            increment: 30,
            value: '01:00'
        });

        formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            id: 'idFormPanel',
            name: 'idFormPanel',
            waitMsgTarget: true,
            height: 300,
            width: 350,
            layout: 'fit',
            fieldDefaults: {
            labelAlign: 'left',
            msgTarget: 'side'
            },
            items:
            [
                {
                    xtype: 'fieldset',
                    title: 'Información',
                    defaultType: 'textfield',
                    items:
                    [
                        txtFechaInicio,                   
                        txtFechaFin,
                        txtHoraInicio,
                        txtHoraFin,
                        txtTiempoAfectacion,
                        cmbTipoAfectacion,
                        cmbTipoNotificacion           
                    ]
                }
            ]
        });
        var btnGuardarDatos = Ext.create('Ext.Button', {
            text: '<label style="color:green;"><i class="fa fa-floppy-o" aria-hidden="true"></i></label>'+
                '&nbsp;<b> Guardar Datos</b>',
            handler: function()
            {
                if(validarMantProgramado())
                {
                    if (typeof arrayMantProgram == 'undefined')
                    {
                        var arrayMantProgram = {};
                    }
                    arrayMantProgram['fechaInicio']      = Ext.getCmp('idFechaInicio').getRawValue();
                    arrayMantProgram['fechaFin']         = Ext.getCmp('idFechaFin').getRawValue();
                    arrayMantProgram['horaInicio']       = Ext.getCmp('idHoraInicio').getRawValue();
                    arrayMantProgram['horaFin']          = Ext.getCmp('idHoraFin').getRawValue();
                    arrayMantProgram['tiempoAfectacion'] = Ext.getCmp('idTiempoAfectacion').getRawValue();
                    arrayMantProgram['tipoAfectacion']   = Ext.getCmp('idTipoAfectacion').getValue();
                    arrayMantProgram['tipoNotificacion'] = Ext.getCmp('idTipoNotificacion').getValue(); 
                    
                    $('#mantProgramado').val(Ext.JSON.encode(arrayMantProgram));
    
                    wincreaGridManteProgram.destroy(); 
                }           
            }
        });
        
        var btnCancelar = Ext.create('Ext.Button', {
            text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
                '&nbsp;<b>Cancelar</b>',
            handler: function() {
               
                if (typeof arrayMantProgram !== 'undefined')
                {
                    arrayMantProgram = null;
                } 
                
                $("#check_mante_programado").prop("value", "N");
                $("#check_mante_programado").removeAttr("checked");
                
                wincreaGridManteProgram.destroy();                
            }
        });

        var wincreaGridManteProgram = new Ext.Window ({
            id         : 'wincreaGridManteProgram',
            title      : 'Mantenimiento Programado',
            layout     : 'fit',
            buttonAlign: 'center',
            resizable  :  false,
            closable   :  false,
            modal      :  true,
            items      :  [formPanel],
            buttons    :  [btnGuardarDatos,btnCancelar]
        }).show();
    } 
}

function validarMantProgramado() 
{   
    if(Ext.getCmp('idHoraInicio').getValue()=="" || 
       Ext.getCmp('idHoraInicio').getValue()== null){
        Ext.Msg.alert("Alerta","El campo Hora Inicio es requerido.");
        return false;
    }
    if(Ext.getCmp('idHoraFin').getValue()=="" || 
       Ext.getCmp('idHoraFin').getValue()== null){
        Ext.Msg.alert("Alerta","El campo Hora Fin es requerido.");
        return false;
    }
    if(Ext.getCmp('idTiempoAfectacion').getValue()=="" || 
       Ext.getCmp('idTiempoAfectacion').getValue()== null){
        Ext.Msg.alert("Alerta","El campo Tiempo Afectacion es requerido.");
        return false;
    }
    if(Ext.getCmp('idTipoAfectacion').getValue()=="" || 
    Ext.getCmp('idTipoAfectacion').getValue()== null)
    {
     Ext.Msg.alert("Alerta","El campo Tipo Afectacion es requerido.");
     return false;
    }
    if(Ext.getCmp('idTipoNotificacion').getValue()=="" || 
    Ext.getCmp('idTipoNotificacion').getValue()== null)
    {
     Ext.Msg.alert("Alerta","El campo Tipo Notificacion es requerido.");
     return false;
    }
    if (Ext.getCmp('idFechaInicio').getValue() > Ext.getCmp('idFechaFin').getValue())
    {
        Ext.Msg.alert("Alerta","El campo Fecha Fin debe ser mayor al campo Fecha Inicio.");
        return false;
    }

    return true;    
}

//Agregar Sintomas Post Caso creado desde el INDEX y el SHOW
function agregarSintoma(data)
{
    var id_caso          = data.id_caso;
    var numero           = data.numero_caso;
    var fecha            = data.fecha_apertura;
    var hora             = data.hora_apertura;
    var version_inicial  = data.version_ini;
    var flagCreador      = data.flagCreador;
    var flagBoolAsignado = data.flagBoolAsignado;
    var visualizacion    = data.visualizacion;

    winSintomas = "";
    var formPanel = "";

    if (winSintomas)
    {
        cierraVentanaByIden(winSintomas);
        winSintomas = "";
    }

    if (winHipotesis)
    {
        cierraVentanaByIden(winHipotesis);
        winHipotesis = "";
    }

    if (!winSintomas)
    {

        Ext.MessageBox.show({
            msg: 'Cargando los datos, Por favor espere!!',
            progressText: 'Saving...',
            width: 300,
            wait: true,
            waitConfig: {interval: 200}
        });

        var conn = new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function(con, opt) {
                        Ext.get(winSintomas.getId()).mask('Guardando Sintomas...');
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function(con, res, opt) {
                        Ext.get(winSintomas.getId()).unmask();
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function(con, res, opt) {
                        Ext.get(winSintomas.getId()).unmask();
                    },
                    scope: this
                }
            }
        });
        btnguardar = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                var valorBool = validarSintomas();

                if (valorBool)
                {
                    json_sintomas = obtenerSintomas();

                    sintomas = Ext.JSON.decode(json_sintomas);
                    criterios = sintomas['sintomas'][0];
                    total = sintomas['total'];

                    conn.request({
                        method: 'POST',
                        params: {
                            id_caso: id_caso,
                            sintomas: json_sintomas
                        },
                        url: url_actualizarSintoma,
                        success: function(response) {

                            Ext.Msg.alert('Mensaje', 'Se actualizaron los sintomas.', function(btn) {
                                if (btn == 'ok')
                                {
                                    cierraVentanaByIden(winSintomas);
                                    if (visualizacion && visualizacion === 'show')
                                    {
                                        //Si la accion se ejecuta desde el show se redirecciona para actualizar la informacion de resumen del caso
                                        window.location = "../" + id_caso + "/show";
                                    }
                                }
                            });

                        },
                        failure: function(rec, op) {
                            var json = Ext.JSON.decode(op.response.responseText);
                            Ext.Msg.alert('Alerta ', json.mensaje);
                        }
                    });
                }
            }
        });
        btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                cierraVentanaByIden(winSintomas);
            }
        });


        storeSintomas = new Ext.data.Store({
            pageSize: 10,
            autoLoad: true,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: url_getSintomasPorCaso,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    id: id_caso,
                    nombre: '',
                    estado: 'Todos',
                    boolCriteriosAfectados: ''
                }
            },
            fields:
                [
                    {name: 'id_sintoma', mapping: 'id_sintoma'},
                    {name: 'nombre_sintoma', mapping: 'nombre_sintoma'},
                    {name: 'criterios_sintoma', mapping: 'criterios_sintoma'},
                    {name: 'afectados_sintoma', mapping: 'afectados_sintoma'}
                ]
        });
        selModelSintomas = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                selectionchange: function(sm, selections) {
                    gridSintomas.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        })
        cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                edit: function() {
                    gridSintomas.getView().refresh();
                }
            }
        });
        comboSintomaStore = new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: url_admisintomaGrid,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'Activo',
                    caso: id_caso
                }
            },
            fields:
                [
                    {name: 'id_sintoma', mapping: 'id_sintoma'},
                    {name: 'nombre_sintoma', mapping: 'nombre_sintoma'}
                ]
        });

        // Create the combo box, attached to the states data store
        comboSintoma = Ext.create('Ext.form.ComboBox', {
            id: 'comboSintoma',
            store: comboSintomaStore,
            displayField: 'nombre_sintoma',
            valueField: 'id_sintoma',
            height: 30,
            border: 0,
            margin: 0,
            fieldLabel: false,
            queryMode: "remote",
            emptyText: ''
        });
        Ext.define('Sintoma', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_sintoma', type: 'string'},
                {name: 'nombre_sintoma', type: 'string'}
            ]
        });
        gridSintomas = Ext.create('Ext.grid.Panel', {
            id: 'gridSintomas',
            store: storeSintomas,
            viewConfig: {enableTextSelection: true, stripeRows: true},
            columnLines: true,
            columns: [{
                    id: 'id_sintoma',
                    header: 'SintomaId',
                    dataIndex: 'id_sintoma',
                    hidden: true,
                    hideable: false
                }, {
                    id: 'nombre_sintoma',
                    header: 'Sintoma',
                    dataIndex: 'nombre_sintoma',
                    width: 320,
                    sortable: true,
                    renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                        record.data.id_sintoma = record.data.nombre_sintoma;
                        for (var i = 0; i < comboSintomaStore.data.items.length; i++)
                        {
                            if (comboSintomaStore.data.items[i].data.id_sintoma == record.data.id_sintoma)
                            {
                                gridSintomas.getStore().getAt(rowIndex).data.id_sintoma = record.data.id_sintoma;
                                record.data.id_sintoma = comboSintomaStore.data.items[i].data.id_sintoma;
                                record.data.nombre_sintoma = comboSintomaStore.data.items[i].data.nombre_sintoma;
                                break;
                            }
                        }
                        return record.data.nombre_sintoma;
                    },
                    editor: {
                        id: 'searchSintoma_cmp',
                        xtype: 'combobox',
                        displayField: 'nombre_sintoma',
                        valueField: 'id_sintoma',
                        loadingText: 'Buscando ...',
                        store: comboSintomaStore,
                        fieldLabel: false,
                        queryMode: "remote",
                        emptyText: '',
                        listClass: 'x-combo-list-small'
                    }
                }, {
                    id: 'criterios_sintoma',
                    header: 'criterios_sintoma',
                    dataIndex: 'criterios_sintoma',
                    hidden: true,
                    hideable: false
                }, {
                    id: 'afectado_sintoma',
                    header: 'afectado_sintoma',
                    dataIndex: 'afectado_sintoma',
                    hidden: true,
                    hideable: false
                }
            ],
            selModel: selModelSintomas,
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [
                        {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridSintomas, 'gridSintomas', selModelSintomas);
                            }
                        }, '-',
                        {
                            itemId: 'addButton',
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            handler: function() {
                                if (flagCreador && !flagBoolAsignado)
                                {
                                    //RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN SINTOMA ANTERIOR... ANTES DE CREAR OTRO..
                                    var storeValida = Ext.getCmp("gridSintomas").getStore();
                                    var boolSigue = false;
                                    var boolSigue2 = false;

                                    if (storeValida.getCount() > 0)
                                    {

                                        var boolSigue_vacio = true;
                                        var boolSigue_igual = true;
                                        for (var i = 0; i < storeValida.getCount(); i++)
                                        {
                                            var id_sintoma = storeValida.getAt(i).data.id_sintoma;
                                            var nombre_sintoma = storeValida.getAt(i).data.nombre_sintoma;

                                            if (id_sintoma != "" && nombre_sintoma != "") { /*NADA*/
                                            }
                                            else {
                                                boolSigue_vacio = false;
                                            }

                                            if (i > 0)
                                            {
                                                for (var j = 0; j < i; j++)
                                                {
                                                    var id_sintoma_valida = storeValida.getAt(j).data.id_sintoma;
                                                    var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintoma;

                                                    if (id_sintoma_valida == id_sintoma || nombre_sintoma_valida == nombre_sintoma)
                                                    {
                                                        boolSigue_igual = false;
                                                    }
                                                }
                                            }
                                        }

                                        if (boolSigue_vacio) {
                                            boolSigue = true;
                                        }
                                        if (boolSigue_igual) {
                                            boolSigue2 = true;
                                        }
                                    }
                                    else
                                    {
                                        boolSigue = true;
                                        boolSigue2 = true;
                                    }

                                    if (boolSigue && boolSigue2)
                                    {
                                        // Create a model instance
                                        var r = Ext.create('Sintoma', {
                                            id_sintoma: '',
                                            nombre_sintoma: '',
                                            criterios_sintoma: '',
                                            afectados_sintoma: ''
                                        });
                                        storeSintomas.insert(0, r);
                                    }
                                    else if (!boolSigue)
                                    {
                                        Ext.Msg.alert('Alerta ', "Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
                                    }
                                    else if (!boolSigue2)
                                    {
                                        Ext.Msg.alert('Alerta ', "No puede ingresar el mismo sintoma! Debe modificar el registro repetido, \n\
                                                                  antes de solicitar un nuevo sintoma");
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta ', "Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
                                    }
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ', "No tiene permisos para crear Sintomas, porque el caso fue asignado a otra persona");
                                }
                            }
                        }]
                }],
            width: 600,
            height: 170,
            frame: true,
            title: 'Agregar Informacion de Sintomas',
            plugins: [cellEditing]
        });

        formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 300,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 200,
                msgTarget: 'side'
            },
            items: [{
                    xtype: 'fieldset',
                    title: 'Información del Caso',
                    defaultType: 'textfield',
                    items: [
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Caso:',
                            id: 'numero_casoSintoma',
                            name: 'numero_casoSintoma',
                            value: numero
                        },
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Fecha apertura:',
                            id: 'fechacaso',
                            name: 'fechaCaso',
                            value: fecha + " " + hora
                        },
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Version Inicial:',
                            id: 'version_inicialSintoma',
                            name: 'version_inicialSintoma',
                            rows: 3,
                            cols: 57,
                            value: version_inicial,
                            readOnly: true
                        },
                        gridSintomas
                    ]
                }]
        });

        winSintomas = Ext.create('Ext.window.Window', {
            title: 'Agregar Sintomas',
            modal: true,
            width: 660,
            height: 440,
            resizable: false,
            layout: 'fit',
            closabled: false,
            items: [formPanel],
            buttonAlign: 'center',
            buttons: [btnguardar, btncancelar]
        }).show();

        Ext.MessageBox.hide();
    }
}

function agregarAfectadosCaso(id_caso,visualizacion)
{
    string_html = "<table width='100%' border='0' class='box-section-content' >";
    string_html += "    <tr>";
    string_html += "        <td width='80%' colspan='6'><b>Buscar Afectados:</b></td>";
    string_html += "    </tr>";
    string_html += "    <tr><td colspan='6'>&nbsp;</td></tr>";
    string_html += "    <tr>";
    string_html += "        <td colspan='6'>";
    string_html += "            <table width='100%' border='0'>";
    string_html += "                <tr>";
    string_html += "                  <table width='100%' border='0' style='border-spacing: 10px;border-collapse: separate;'>";
    string_html += "                     <tr>";
    string_html += "                        <td id='elementos' width='100px'>";
    string_html += "                        <table width='100%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='3' class='titulo-secundario'><b>Elementos de Red</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Elemento:</td>";
    string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
    string_html += "                                     <input style='width: 150px !important;' type='text' id='elemento' \n\
                                                                class='x-form-field x-form-text' style=' -moz-user-select: text;' readonly>";
    string_html += "                                         <a onclick='buscarElementoPanel(false)' style='cursor:pointer;'>";
    string_html += "                                          <img src='/public/images/search.png' />\n\
                                                      </div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Opcion:</td>";
    string_html += "                                <td width='60%'><div id='searchOpciones'></div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td ><input type='hidden' id='idElementohd' name='idElementohd'/></td>";
    string_html += "                            </tr>";
    string_html += "                        </table>";
    string_html += "                    </td>";
        
    string_html += "                    <td id='clientes' width='200px'>";
    string_html += "                        <table width='100%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='3' class='titulo-secundario'><b>Clientes</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Login:</td>";
    string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
    string_html += "                                     <input style='width: 150px !important;' type='text' id='txtLogin' \n\
                                                                class='x-form-field x-form-text' style=' -moz-user-select: text;'>";
    string_html += "                                         <a onclick='buscarClientesAfectados(1)' style='cursor:pointer;'>";
    string_html += "                                          <img src='/public/images/search.png' />\n\
                                                      </div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Razon Social:</td>";
    string_html += "                                <td width='60%' colspan='3'><div style='width: 170px;'>";
    string_html += "                                     <input style='width: 150px !important;' type='text' id='txtRazonSocial' \n\
                                                                class='x-form-field x-form-text' style=' -moz-user-select: text;'>";
    string_html += "                                         <a onclick='buscarClientesAfectados(2)' style='cursor:pointer;'>";
    string_html += "                                          <img src='/public/images/search.png' />\n\
                                                      </div></td>";
    string_html += "                            </tr>";
    string_html += "                        </table>";
    string_html += "                    </td>";
        
    string_html += "                    <td id='ciudad' width='200px'>";
    string_html += "                        <table width='100%' height='95px' border='0' class='box-section-subcontent'  \n\
                                                style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='3' class='titulo-secundario'><b>Ciudad</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Nombre:</td>";

    string_html += "                                <td width='60%'><div id='searchCiudadBackbone'></div></td>";
    string_html += "                                <td><a onclick='mostrarAfectados(1)'\n\
                                                        style='cursor:pointer;'><img src='/public/images/search.png' /></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Pe:</td>";

    string_html += "                                <td width='60%'><div id='searchRouter'></div></td>";
    string_html += "                            </tr>";

    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Anillo:</td>";

    string_html += "                                <td width='60%'><div id='searchAnillo'></div></td>";

    string_html += "                            </tr>";

    string_html += "                        </table>";
    string_html += "                    </td>";

    string_html += "                    </tr>";
    string_html += "                  </table>";
    //Fin tabla para buscar afectados
    string_html += "                </tr>";
    string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
    string_html += "                <tr style='height:200px'>";
    string_html += "                    <td colspan='8'><div id='encontrados'></div></td>";
    string_html += "                </tr>";
    string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
    string_html += "                <tr>";
    string_html += "                    <td colspan='8' align='center'><input name='btn3' type='button' value='Agregar' class='btn-form' \n\
                                         onclick='ingresarCriterioAfectados()'/></td>";
    string_html += "                </tr>";
    string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
    string_html += "                <tr style='height:200px'>";
    string_html += "                    <td colspan='4'><div id='criterios'></div><input type='hidden' id='caso_criterios' \n\
                                            name='caso_criterios' value='' /></td>";
    string_html += "                    <td colspan='4'><div id='afectados'></div><input type='hidden' id='caso_afectados' \n\
                                            name='caso_afectados' value='' /></td>";
    string_html += "                </tr>";
    string_html += "            </table>";
    string_html += "        </td>";
    string_html += "    </tr>";
    string_html += "</table>";

    btnguardar2 = Ext.create('Ext.Button', {
        text: 'Guardar',
        cls: 'x-btn-rigth',
        handler: function() 
        {            
            var arrayData = obtenerAfectadosCaso();                                        
            
            if (arrayData==='')
            {
                Ext.Msg.alert("Alerta", "Debe ingresar al menos una afectacion para crear un caso.");
            }
            else
            {
                var jsonAfectados = JSON.stringify(arrayData);
                
                if(jsonAfectados!=='')
                {
                    //Realizar la peticion ajax
                    var connAgregarAfectados = new Ext.data.Connection({
                        listeners: {
                            'beforerequest': {
                                fn: function(con, opt) {
                                    Ext.MessageBox.show({
                                        msg: 'Agregando Afectados',
                                        progressText: 'Cargando...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {interval: 800}
                                    });
                                },
                                scope: this
                            },
                            'requestcomplete': {
                                fn: function(con, res, opt) {
                                    Ext.MessageBox.hide();
                                },
                                scope: this
                            },
                            'requestexception': {
                                fn: function(con, res, opt) {
                                    Ext.MessageBox.hide();
                                },
                                scope: this
                            }
                        }
                    });
                   
                    connAgregarAfectados.request({
                        url: url_agregarAfectados,
                        method: 'post',
                        timeout:9000000,
                        params:
                            {
                                idCaso       : id_caso,
                                jsonAfectados: jsonAfectados
                            },
                        success: function(response) 
                        {
                            var text = Ext.decode(response.responseText); 
                            if(text.status=="OK")
                            {
                                win2.destroy();
                                Ext.Msg.alert('Mensaje',text.mensaje, function(btn) {
                                    if (btn == 'ok')
                                    {               
                                        if (visualizacion === 'show')
                                        {                                            
                                            storeCriterios_show.proxy.extraParams = { todos: 'YES'};
                                            storeCriterios_show.load();  
                                            storeAfectados_show.proxy.extraParams = { todos: 'YES'};
                                            storeAfectados_show.load();                                                                            
                                        }
                                    }
                                });                          
                            }
                            else
                            {
                                Ext.Msg.alert('Mensaje',text.mensaje, function(btn) {                                   
                                });
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.show({
                                title: 'Error',
                                msg: result.statusText,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    });                                  
                }
                else
                {
                    Ext.Msg.alert("Alerta", "Debe ingresar al menos una afectacion para crear un caso.");
                }
            }
        }
    });
    btncancelar2 = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            win2.destroy();
        }
    });

    win2 = Ext.create('Ext.window.Window', {
        title: 'Agregar Afectados',
        modal: true,
        width: 940,
        height: 680,
        resizable: false,
        layout: 'fit',
        items: [
            {
                xtype: 'panel',
                width: 400,
                html: '<div style="padding:6px;">' + string_html + '</div>'
            }
        ],
        buttonAlign: 'center',
        buttons: [btnguardar2, btncancelar2]
    }).show();

    Ext.define('Criterio', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_criterio_afectado', mapping: 'id_criterio_afectado'},
            {name: 'caso_id', mapping: 'caso_id'},
            {name: 'criterio', mapping: 'criterio'},
            {name: 'opcion', mapping: 'opcion'}
        ]
    });
    Ext.define('Afectado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'id_afectado', mapping: 'id_afectado'},
            {name: 'id_criterio', mapping: 'id_criterio'},
            {name: 'id_afectado_descripcion', mapping: 'id_afectado_descripcion'},
            {name: 'nombre_afectado', mapping: 'nombre_afectado'},
            {name: 'descripcion_afectado', mapping: 'descripcion_afectado'},
            {name: 'json_afectados', mapping: 'json_afectados'}
        ]
    });


    // Grid encontrados
    storeEncontrados = new Ext.data.Store({
        pageSize: 500,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 1200000,
            url: url_getEncontrados,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        sorters: {
            property: 'nombre_parte_afectada',
            direction: 'ASC'
        },
        fields:
            [
                {name: 'id_parte_afectada', mapping: 'id_parte_afectada'},
                {name: 'nombre_parte_afectada', mapping: 'nombre_parte_afectada'},
                {name: 'id_descripcion_1', mapping: 'id_descripcion_1'},
                {name: 'nombre_descripcion_1', mapping: 'nombre_descripcion_1'},
                {name: 'id_descripcion_2', mapping: 'id_descripcion_2'},
                {name: 'nombre_descripcion_2', mapping: 'nombre_descripcion_2'}
            ]
    });
    smEncontrados = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
    gridEncontrados = Ext.create('Ext.grid.Panel', {
        width: 910,
        height: 200,
        store: storeEncontrados,
        viewConfig: {enableTextSelection: true},
        forceFit: true,
        autoRender: true,
        id: 'gridEncontrados',
        loadMask: true,
        frame: true,
        resizable: false,
        enableColumnResize: false,
        iconCls: 'icon-grid',
        selModel: smEncontrados,
        columns: [
            Ext.create('Ext.grid.RowNumberer'),
            {
                id: 'id_parte_afectada',
                header: 'IdItemMenu',
                dataIndex: 'id_parte_afectada',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombre_parte_afectada',
                header: 'Afectado',
                dataIndex: 'nombre_parte_afectada',
                width: 400,
                sortable: true
            },
            {
                id: 'id_descripcion_1',
                header: 'IdItemMenu',
                dataIndex: 'id_descripcion_1',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombre_descripcion_1',
                header: 'Descripcion',
                dataIndex: 'nombre_descripcion_1',
                width: 250,
                sortable: true
            },
            {
                id: 'id_descripcion_2',
                header: 'IdItemMenu',
                dataIndex: 'id_descripcion_2',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombre_descripcion_2',
                header: 'Estado',
                dataIndex: 'nombre_descripcion_2',
                width: 250,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeEncontrados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'encontrados'
    });

    ////////////////Grid  Criterios////////////////
    storeCriterios = new Ext.data.JsonStore(
        {
            pageSize: 200,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: 'getCriterios2',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    id: '',
                    id_sintoma: '',
                    id_hipotesis: 'NO'
                }
            },
            fields:
                [
                    {name: 'id_criterio_afectado', mapping: 'id_criterio_afectado'},
                    {name: 'caso_id', mapping: 'caso_id'},
                    {name: 'criterio', mapping: 'criterio'},
                    {name: 'opcion', mapping: 'opcion'}
                ]
        });
    smCriterios = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridCriterios.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    })
    gridCriterios = Ext.create('Ext.grid.Panel', {
        title: 'Criterios de Seleccion',
        width: 450,
        height: 200,
        autoRender: true,
        enableColumnResize: false,
        id: 'gridCriterios',
        store: storeCriterios,
        viewConfig: {enableTextSelection: true},
        loadMask: true,
        frame: true,
        forceFit: true,
        iconCls: 'icon-grid',
        selModel: smCriterios,
        dockedItems: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina',
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarCriterio(gridCriterios, storeAfectados);
                        }
                    }]
            }],
        columns: [
            {
                id: 'id_criterio_afectado',
                header: 'id_criterio_afectado',
                dataIndex: 'id_criterio_afectado',
                hidden: true,
                hideable: false
            },
            {
                id: 'caso_id',
                header: 'caso_id',
                dataIndex: 'caso_id',
                hidden: true,
                sortable: true
            },
            {
                id: 'criterio',
                header: 'Criterio',
                dataIndex: 'criterio',
                width: 100,
                hideable: false
            },
            {
                id: 'opcion',
                header: 'Opcion',
                dataIndex: 'opcion',
                width: 300,
                sortable: true
            }
        ],
        renderTo: 'criterios'
    });


    ////////////////Grid  Afectados////////////////
    storeAfectados = new Ext.data.JsonStore(
        {
            pageSize: 4000,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: 'getAfectados2',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    id: '',
                    id_sintoma: '',
                    id_hipotesis: 'NO'
                }
            },
            fields:
                [
                    {name: 'id', mapping: 'id'},
                    {name: 'id_afectado', mapping: 'id_afectado'},
                    {name: 'id_criterio', mapping: 'id_criterio'},
                    {name: 'id_afectado_descripcion', mapping: 'id_afectado_descripcion'},
                    {name: 'nombre_afectado', mapping: 'nombre_afectado'},
                    {name: 'descripcion_afectado', mapping: 'descripcion_afectado'},
                    {name: 'json_afectados',mapping : 'json_afectados'}
                ]
        });

    gridAfectados = Ext.create('Ext.grid.Panel', {
        title: 'Afectados',
        width: 450,
        height: 200,
        sortableColumns: false,
        store: storeAfectados,
        viewConfig: {enableTextSelection: true},
        id: 'gridAfectados',
        enableColumnResize: false,
        loadMask: true,
        frame: true,
        forceFit: true,
        iconCls: 'icon-grid',
        columns: [
            Ext.create('Ext.grid.RowNumberer'),
            {
                id: 'id',
                header: 'id',
                dataIndex: 'id',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_afectado',
                header: 'id_afectado',
                dataIndex: 'id_afectado',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_criterio',
                header: 'id_criterio',
                dataIndex: 'id_criterio',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_afectado_descripcion',
                header: 'id_afectado_descripcion',
                dataIndex: 'id_afectado_descripcion',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombre_afectado',
                header: 'Afectado',
                dataIndex: 'nombre_afectado',
                width: 250
            },
            {
                id: 'descripcion_afectado',
                header: 'Descripcion',
                dataIndex: 'descripcion_afectado',
                width: 150
            },
            {
                id: 'json_afectados',
                header: 'json_afectados',
                dataIndex: 'json_afectados',
                hidden: true,
                hideable: false
            }
        ],
        renderTo: 'afectados'
    });
    storeOpciones = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'nombre'],
        data: [
            {"opcion": "Puertos", "nombre": "Puertos"},
            {"opcion": "Logines", "nombre": "Punto Cliente"},
            {"opcion": "Ninguna", "nombre": "Ninguna"}
        ]
    });
    comboOpciones = Ext.create('Ext.form.ComboBox', {
        id: 'comboOpciones',
        store: storeOpciones,
        queryMode: 'local',
        valueField: 'opcion',
        displayField: 'nombre',
        listeners: {
            select: function(combo) {
                presentarEncontrados(document.getElementById("idElementohd").value,
                    document.getElementById("elemento").value,
                    combo.getValue(),
                    combo.getRawValue(),
                    "Elemento");
            }
        },
        renderTo: 'searchOpciones'
    });
    
    Ext.getCmp('comboOpciones').setDisabled(true);

    storeCiudadesBackbone = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 1200000,
            type: 'ajax',
            method: 'post',
            url: strUrlCiudadesEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });

    comboCiudades = Ext.create('Ext.form.ComboBox', {
        id: 'comboCiudadesBackbone',
        store: storeCiudadesBackbone,
        displayField: 'nombre_canton',
        valueField: 'id_canton',
        fieldLabel: false,
        queryMode: "remote",
        emptyText: '',
        disabled: false,
        width: 150,
        border: 0,
        margin: 0,
        listeners: {
            select: function(combo) {
                Ext.getCmp("comboRouter").setValue("");
                Ext.getCmp("comboRouter").setDisabled(false);
                Ext.getCmp("comboAnillo").setValue("");
                presentarElementosPe(combo.getValue(),"ROUTER");
            }
        },
        renderTo: 'searchCiudadBackbone'
    });

    storeRouter = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 1200000,
            type: 'ajax',
            method: 'post',
            url: strUrlElementosTipoEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                tipoElemento: 'ROUTER'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });

    comboRouter = Ext.create('Ext.form.ComboBox', {
        id: 'comboRouter',
        store: storeRouter,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        fieldLabel: false,
        queryMode: "remote",
        emptyText: '',
        disabled: true,
        width: 150,
        border: 0,
        margin: 0,
        listeners: {
            select: function(combo) {
                Ext.getCmp("comboAnillo").setValue("");
                Ext.getCmp("comboAnillo").setDisabled(false);
                gridEncontrados.getStore().removeAll;
            }
        },
        renderTo: 'searchRouter'
    });

    storeAnillos = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 1200000,
            type: 'ajax',
            method: 'post',
            url: strUrlGetAnillos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        fields:
            [
                {name: 'idAnillo', mapping: 'idAnillo'},
                {name: 'numeroAnillo', mapping: 'numeroAnillo'}
            ]
    });

    comboAnillo = Ext.create('Ext.form.ComboBox', {
        id: 'comboAnillo',
        store: storeAnillos,
        displayField: 'numeroAnillo',
        valueField: 'idAnillo',
        fieldLabel: false,
        queryMode: "remote",
        emptyText: '',
        disabled: true,
        width: 150,
        border: 0,
        margin: 0,
        listeners: {
            select: function(combo) {
                mostrarAfectados(2);
            }
        },
        renderTo: 'searchAnillo'
    });
}

function buscarClientesAfectados(tipo)
{    
    var filtro;        
    
    if(tipo===1)
    {
        filtro = document.getElementById("txtLogin").value;
        tipo   = "login";
        if(filtro=="")
        {
            Ext.Msg.alert('Alerta ', "Debe ingresar el Login");
            return false;
        }
    }
    else
    {
        filtro = document.getElementById("txtRazonSocial").value;
        tipo   = "razonSocial";
        if(filtro=="")
        {
            Ext.Msg.alert('Alerta ', "Debe ingresar la Razon Social");
            return false;
        }
    }
    
    presentarEncontrados("",filtro,tipo,"","cliente");
}