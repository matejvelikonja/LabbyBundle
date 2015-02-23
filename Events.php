<?php

namespace Velikonja\LabbyBundle;

final class Events
{
    const PRE_SYNC     = 'velikonja_labby.pre_sync';
    const POST_SYNC    = 'velikonja_labby.post_sync';
    const PRE_SYNC_FS  = 'velikonja_labby.pre_sync.fs';
    const POST_SYNC_FS = 'velikonja_labby.post_sync.fs';
    const PRE_SYNC_DB  = 'velikonja_labby.pre_sync.db';
    const POST_SYNC_DB = 'velikonja_labby.post_sync.db';

    /**
     * Private constructor. This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    public static function all()
    {
        $reflection = new \ReflectionClass(get_class());
        $constants  = array();

        //search for all constants in this class that start with $prefix
        foreach ($reflection->getConstants() as $constName => $constValue) {
            $constants[$constName] = $constValue;
        }

        return $constants;
    }

}
