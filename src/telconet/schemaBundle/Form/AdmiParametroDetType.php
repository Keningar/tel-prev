<?php

namespace telconet\schemaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdmiParametroDetType extends AbstractType
{
    private $arrayValor;
    public function __construct($arrayOptions) 
    {
        $this->arrayValor = $arrayOptions['arrayValor'];
    }   
   
    public function buildForm(FormBuilderInterface $objBuilder, array $arrayOptions)
    {
        $strTipo=$this->arrayValor[count($this->arrayValor)-1];

        if( $strTipo =='meta')
        {
            $strValor='*Valor de meta MRC:';
            $strValorID='Valor de meta I/D';
            $strValorBs='Valor de meta Bs';
            $strAnio=date('Y');
            unset($this->arrayValor[count($this->arrayValor)-1]);
            $arrayMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $strMes= (string)$arrayMeses[date('n')-1];
        }
        else if( $strTipo =='editarMeta')
        {
            $strValor='*Valor de meta MRC:';
            $strValorID='Valor de meta I/D';
            $strValorBs='Valor de meta Bs';
            $intValorID = $this->arrayValor[count($this->arrayValor)-5];
            $intValorBs = $this->arrayValor[count($this->arrayValor)-4];
            $strMes   = $this->arrayValor[count($this->arrayValor)-3];
            $strAnio  = $this->arrayValor[count($this->arrayValor)-2];
            unset($this->arrayValor[count($this->arrayValor)-1],
                $this->arrayValor[count($this->arrayValor)-2],
                $this->arrayValor[count($this->arrayValor)-3],
                $this->arrayValor[count($this->arrayValor)-4],
                $this->arrayValor[count($this->arrayValor)-5]);
        }        
        else if( $strTipo =='crearBase')
        {
            $strValor='*Valor de base:';
            $strValorID='Valor de base I/D';
            $strValorBs='Valor de base Bs';
            unset($this->arrayValor[count($this->arrayValor)-1]);
            $strAnio=date('Y');
            $arrayMeses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $strMes= (string)$arrayMeses[date('n')-1];
        }
        else if( $strTipo =='editarBase')
        {
            $strValor = '*Valor de base:';
            $strValorID='Valor de base I/D';
            $strValorBs='Valor de base Bs';
            $strMes   = $this->arrayValor[count($this->arrayValor)-3];
            $strAnio  = $this->arrayValor[count($this->arrayValor)-2];
            unset($this->arrayValor[count($this->arrayValor)-1],
                $this->arrayValor[count($this->arrayValor)-2],
                $this->arrayValor[count($this->arrayValor)-3]);
        }
        
        if( $this->arrayValor && count($this->arrayValor)>0 )
        {
            foreach($this->arrayValor as $value)
            {
                $arrayVendedores[$value["LOGIN"]] = $value["VENDEDOR"];                
            }

        }
        $objBuilder
            ->add('valor5', 'choice', 
                    array(  'choices' => $arrayVendedores,
                            'required' => true,
                            'empty_value'   => 'Seleccione vendedor...',
                            'label' => '*Vendedores:')
                )
            ->add('valor3', 'text', 
                    array('label' => $strValor,
                            'label_attr' => array('class' => 'campo-obligatorio'), 
                            'attr' => array('maxLength' => 10,
                            'class' => 'campo-obligatorio', 
                            'onChange' => '')))
            ->add('valor6', 'text', 
                    array('label' => $strValorID,
                            'attr' => array('maxLength' => 10,
                            'value' => $intValorID,
                            'onChange' => 'calculaBase()')))
            ->add('valor7', 'text', 
                    array('label' => $strValorBs,
                            'attr' => array('maxLength' => 10,
                            'value' => $intValorBs,
                            'onChange' => 'calculaBase()')))
            ->add('valor1', 'text', 
                    array('label' => '*Mes vigente:',                            
                            'attr' => array('maxLength' => 10,
                                            'readonly'  => 'readonly',
                                            'value' => $strMes,
                                            'class' => 'campo-obligatorio')
                                            )
                        )

            ->add('valor2', 'text', 
                    array('label' => '*Año vigente:',                            
                            'attr' => array('maxLength' => 10,
                                            'readonly'  => 'readonly',
                                            'value' => $strAnio,
                                            'class' => 'campo-obligatorio')
                                            )
                        )
            ->add('valor4', 'text', 
                    array('label' => '*Valor de meta NRC:',
                            'label_attr' => array('class' => 'campo-obligatorio'), 
                            'attr' => array('maxLength' => 10,
                            'class' => 'campo-obligatorio', 
                            'onChange' => '')))                                                                           
        ;
    }

    public function getName()
    {
        return 'AdmiParametroDet';
    }
}
