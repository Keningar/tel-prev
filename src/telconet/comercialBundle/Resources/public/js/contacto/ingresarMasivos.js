function guardar()
{

    if (document.form_contactos.file_contactos.value == "" || document.form_contactos.file_contactos.value == null)
    {
        alert("Favor seleccione un archivo.");
        return false;
    }
    else
    {

        var str = (document.form_contactos.file_contactos.value).toLowerCase();
        var ext = getFileExt(str);
        if (ext == "csv") 
        {
            document.form_contactos.submit();
        } else 
        {
            alert("Solo se aceptan archivos con extension .csv");
            return false;
        }
    }


}
function cancelar()
{
    document.form_contactos.file_contactos.value = "";
    return false;
}

// Obtener extensión
function getFileExt(sPTF, bDot) 
{
    if (bDot != true)
    {
        bDot = false;
    }
    return sPTF.substr(sPTF.lastIndexOf('.') + ((!bDot) ? 1 : 0));
}



