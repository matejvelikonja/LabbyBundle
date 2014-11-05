<?php

namespace Velikonja\LabbyBundle\Util;

class ZipArchive
{
    /**
     * @var string
     */
    private $tmpDir;

    /**
     * @var string
     */
    private $compressedFileName;

    public function __construct()
    {
        $this->tmpDir              = sys_get_temp_dir();
        $this->compressedFileName = 'dump.sql';

        if (! is_writable($this->tmpDir)) {
            throw new \Exception(
                sprintf('Temporary directory `%s` is unreadable.', $this->tmpDir)
            );
        }
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function isZipped($filePath)
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $type     = finfo_file($fileInfo, $filePath);

        finfo_close($fileInfo);

        return 'application/zip' === $type;
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function unzip($filePath)
    {
        $tmpFile = $this->tmpDir . '/labby_' . uniqid() . '.sql';

        $zip = new \ZipArchive();
        $zip->open($filePath);

        if (false === $zip->locateName($this->compressedFileName)) {
            $zip->close();

            throw new \InvalidArgumentException(
                sprintf(
                    'File `%s` not found in ZIP archive `%s`.',
                    $this->compressedFileName,
                    $filePath
                )
            );
        }

        $zip->extractTo($this->tmpDir, $this->compressedFileName);
        $zip->close();

        rename($this->tmpDir . '/' . $this->compressedFileName, $tmpFile);

        return $tmpFile;
    }
} 