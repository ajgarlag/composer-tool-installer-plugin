<?php

namespace ToolInstaller\Composer\Command\Autocomplete;

use Composer\Factory;
use ToolInstaller\Composer\Defaults;

class SignUrlSuggester extends AbstractSuggester
{
    public function __construct()
    {
        $configuration = Factory::createConfig();
        $file = $configuration->get('cache-dir') . DIRECTORY_SEPARATOR . Defaults::AUTOCOMPLETE_SIGN_URL_FILE;

        parent::__construct($file);
    }
}
