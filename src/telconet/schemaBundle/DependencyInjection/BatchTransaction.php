<?php

namespace telconet\schemaBundle\DependencyInjection;

use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clase de ayuda para manejar transacciones por lotes (batch)
 */
class BatchTransaction
{
    private $currentBatchSize = 0;
    private $maxBatchSize = 50;
    private $entityManagers = array();
    private $isLog = FALSE;
    private $output = NULL;

    public function __construct($maxBatchSize, array $entityManagers, OutputInterface $output = NULL)
    {
        $this->currentBatchSize = 0;
        $this->maxBatchSize = $maxBatchSize;
        $this->entityManagers = $entityManagers;
        if (empty($output))
        {
            $output = new NullOutput();
            $this->isLog = false;
        }
        else
        {
            $this->isLog = true;
        }
        $this->output = $output;
    }

    /**
     * Inicia una nueva transaccion en los entityManagers, si currentBatchSize es cero
     */
    public function beginTransaction()
    {
        if ($this->currentBatchSize == 0)
        {
            foreach ($this->entityManagers as $entityManager)
            {
                /* @var $entityManager \Doctrine\ORM\EntityManager */
                $entityManager->beginTransaction();
            }
            if ($this->isLog)
            {
                $this->output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' BEGIN');
            }
        }
    }

    /**
     * Hace flush, clear y commit en los entityManagers, si currentBatchSize es mayor o igual a maxBatchSize.
     * En ese caso encera currentBatchSize.
     * NOTA: Luego debe hacerse merge en el entityManager correspondiente, de los entity que se necesite volver managed para siguientes transacciones.
     */
    public function commitTransaction()
    {
        $this->currentBatchSize++;
        if ($this->currentBatchSize >= $this->maxBatchSize)
        {
            foreach ($this->entityManagers as $entityManager)
            {
                /* @var $entityManager \Doctrine\ORM\EntityManager */
                // guardar cambios en la base
                $entityManager->flush();
                $entityManager->clear();
                // confirmar cambios guardados
                $entityManager->commit();
            }
            $this->currentBatchSize = 0;
            if ($this->isLog)
            {
                $this->output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' COMMIT');
            }
        }
    }

    /**
     * Hace flush, clear y commit en los entityManagers, si currentBatchSize es mayor que cero.
     * Encera currentBatchSize.
     * NOTA: Luego debe hacerse merge en el entityManager correspondiente, de los entity que se necesite volver managed para siguientes transacciones.
     */
    public function endTransaction()
    {
        if ($this->currentBatchSize > 0)
        {
            foreach ($this->entityManagers as $entityManager)
            {
                /* @var $entityManager \Doctrine\ORM\EntityManager */
                // guardar cambios en la base
                $entityManager->flush();
                $entityManager->clear();
                // confirmar cambios guardados
                $entityManager->commit();
            }
            $this->currentBatchSize = 0;
            if ($this->isLog)
            {
                $this->output->writeln((new \DateTime('now'))->format('Y-m-d H:i:s') . ' END');
            }
        }
    }

}
