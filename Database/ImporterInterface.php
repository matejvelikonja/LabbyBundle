<?php

namespace Velikonja\LabbyBundle\Database;

interface ImporterInterface
{
    /**
     * Import dump to database.
     *
     * @param string        $file
     * @param null|callable $callback
     *
     * @return void
     */
    public function import($file, $callback = null);
}
