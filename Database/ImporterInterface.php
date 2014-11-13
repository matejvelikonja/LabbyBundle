<?php

namespace Velikonja\LabbyBundle\Database;

interface ImporterInterface
{
    /**
     * Import dump to database.
     *
     * @param string $file
     *
     * @return void
     */
    public function import($file);
}
