<?php

namespace telconet\comunicacionesBundle\Service;



class InfoDocumentoService {
    
    
    /**
     * 
     * Documentación para el método 'validacionesDocumentosObligatorios'.
     * 
     * Valida que se hayan subido todos los tipos de documentos obligatorios.
     * 
     * @param array $arrayTiposDocumentoBase 
     * @param array $arrayIdsTiposDocumentosFinales
     * @param array $arrayIdsTipoDocumentosBase
     * 
     * @return array['boolOk','strMsg'] //si aprueba o no las validaciones con su respectivo mensaje de error si así fuera el caso.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-12-2015
     */
    public function validacionesDocumentosObligatorios($arrayTiposDocumentoBase,$arrayIdsTiposDocumentosFinales,$arrayIdsTipoDocumentosBase)
    {
        $boolOk = true;
        $strMsg = '';
 
        $arrayIdsTipoDocumentosSinSubir = array_diff($arrayIdsTipoDocumentosBase,$arrayIdsTiposDocumentosFinales); 
        $intNumDocsSinSubir             = count($arrayIdsTipoDocumentosSinSubir);
        
        if($intNumDocsSinSubir>0)
        {
           
            $boolOk  = false;
            $strMsg .= 'Debe agregar inicialmente los siguientes documentos: ';
            
            $intContDocs=0;
            foreach($arrayIdsTipoDocumentosSinSubir as $idSinSubir)
            {
                if($intNumDocsSinSubir==1)
                {
                    $strMsg = 'Debe agregar inicialmente el siguiente documento: '
                            .$arrayTiposDocumentoBase[$idSinSubir]['descripcionTipoDocumento'];
                }
                else if($intContDocs==($intNumDocsSinSubir-1))
                {
                    $strMsg .= 'y '.$arrayTiposDocumentoBase[$idSinSubir]['descripcionTipoDocumento'];  
                }
                else
                {
                    $strMsg .= $arrayTiposDocumentoBase[$idSinSubir]['descripcionTipoDocumento'] .', ';  
                }
                
                $intContDocs++;
            }
        }
        else
        {
             $boolOk = true;
        }

        return array('boolOk' => $boolOk, 'strMsg' => $strMsg);
    }
    
    
    /**
     * 
     * Documentación para el método 'validacionesFechasDocumentosObligatorios'.
     * 
     * Valida que las fechas para los documentos cuyo tipo de documento es obligatorio, también sean obligatorias.
     * 
     * @param array $arrayFechasObligatorias
     * 
     * @return array['boolOk','strMsg'] //si aprueba o no las validaciones con su respectivo mensaje de error si así fuera el caso.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function validacionesFechasDocumentosObligatorios($arrayFechasObligatorias)
    {
        $boolOk                     = true;
        $strMsg                     = '';
        $strMsgTiposDoc             = '';
        $intContFechasIncorrectas   = 0;
        $arrayTiposDocsObligatorios = array();
        
        foreach($arrayFechasObligatorias as $fecha)
        {
            list($year,$month,$day)=explode('-',$fecha['valorFechaDocumento']);
            if($year=="" || $month=="" || $day=="")
            {
                $intContFechasIncorrectas++;
                if(!in_array($fecha['descripcionTipoDocumento'],$arrayTiposDocsObligatorios))
                {
                    $arrayTiposDocsObligatorios[]=$fecha['descripcionTipoDocumento'];
                }
            }
        }
        
        if($intContFechasIncorrectas>0)
        {
            $boolOk = false;
            
            for($i = 0; $i < count($arrayTiposDocsObligatorios); $i++)
            {
                if($i==(count($arrayTiposDocsObligatorios)-1))
                {
                    $strMsgTiposDoc.= "".$arrayTiposDocsObligatorios[$i].".";
                }
                else
                {
                    $strMsgTiposDoc.= "".$arrayTiposDocsObligatorios[$i].",";
                } 
            }
            
            $strMsg.="Por favor verifique las fechas para los siguientes tipos de documentos: ".$strMsgTiposDoc;
            
        }
        return array('boolOk' => $boolOk, 'strMsg' => $strMsg);          
    }
    
    
}
