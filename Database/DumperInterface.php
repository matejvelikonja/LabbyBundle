<?php

namespace Velikonja\LabbyBundle\Database;

interface DumperInterface
{
    /**
     * Returns database dump.
     *
     * @return string
     */
    public function dump();
} 