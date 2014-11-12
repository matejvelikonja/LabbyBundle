<?php

namespace Velikonja\LabbyBundle\Database;

interface ImporterInterface
{
    /**
     * Import dump to database.
     *
     * @param string $file
     */
    public function import($file);
} 