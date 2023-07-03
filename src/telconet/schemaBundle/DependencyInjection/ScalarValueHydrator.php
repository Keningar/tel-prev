<?php

namespace telconet\schemaBundle\DependencyInjection;

use Doctrine\ORM\Internal\Hydration\ScalarHydrator;

/**
 * Hydrator que produce un array con solamente los valores escalares del result set.
 */
class ScalarValueHydrator extends ScalarHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData(array $data, array &$cache, array &$result)
    {
        $res = $this->gatherScalarRowData($data, $cache);
        foreach ($res as $key => $value)
        {
            $result[] = $value;
        }
    }
}
