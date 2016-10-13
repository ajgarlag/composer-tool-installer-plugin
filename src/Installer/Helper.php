<?php

namespace Tooly\Composer\Installer;

use Tooly\Composer\Installer\Helper\Downloader;
use Tooly\Composer\Installer\Helper\Filesystem;
use Tooly\Composer\Installer\Helper\Verifier;
use TM\GPG\Verification\Verifier as GpgVerifier;

class Helper
{
    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @return Downloader
     */
    public function getDownloader()
    {
        if (!$this->downloader instanceof Downloader) {
            $this->downloader = new Downloader;
        }

        return $this->downloader;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        if (!$this->filesystem instanceof Filesystem) {
            $this->downloader = new Filesystem;
        }

        return $this->filesystem;
    }

    /**
     * @return Verifier
     */
    public function getVerifier()
    {
        if ($this->verifier instanceof Verifier) {
            return $this->verifier;
        }

        $gpgVerifier = null;

        if (true === class_exists(GpgVerifier::class)) {
            $gpgVerifier = new GpgVerifier;
        }

        $this->verifier = new Verifier($gpgVerifier);

        return $this->verifier;
    }

    /**
     * @param string $filename
     * @param string $targetFile
     *
     * @return bool
     */
    public function isFileAlreadyExist($filename, $targetFile)
    {
        $alreadyExist = $this->getFilesystem()->isFileAlreadyExist($filename);
        $verification = $this->getVerifier()->checkFileSum($filename, $targetFile);

        if (true === $alreadyExist && true === $verification) {
            return true;
        }

        return false;
    }
    /**
     * @param string $signatureUrl
     * @param string $fileUrl
     *
     * @return bool
     */
    public function isVerified($signatureUrl, $fileUrl)
    {
        $data = $this->getDownloader()->download($fileUrl);
        $signatureData = $this->getDownloader()->download($signatureUrl);

        $tmpFile = rtrim(sys_get_temp_dir(), '/') . '/_tool';
        $this->getFilesystem()->createFile($tmpFile, $data);

        $tmpSignFile = rtrim(sys_get_temp_dir(), '/') . '/_tool.sign';
        $this->getFilesystem()->createFile($tmpSignFile, $signatureData);

        $result = $this->getVerifier()->checkGPGSignature($tmpSignFile, $tmpFile);

        unlink($tmpFile);
        unlink($tmpSignFile);

        return $result;
    }
}
