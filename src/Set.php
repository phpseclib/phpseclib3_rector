<?php
namespace phpseclib\phpseclib3Rector;

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

if (!class_exists('\phpseclib\phpseclib3Rector\Set'))
{
    class Set
    {
        const PATH = __FILE__;
    }
}

return RectorConfig::configure()
    ->withRules([
        RSAMethodRector::class,
        RSANewRector::class,
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        'phpseclib\Crypt\RSA' => 'phpseclib3\Crypt\RSA',
        'phpseclib\Net\SSH2' => 'phpseclib3\Net\SSH2',
        'phpseclib\Net\SFTP' => 'phpseclib3\Net\SFTP',
        'phpseclib\Math\BigInteger' => 'phpseclib3\Math\BigInteger',
    ])
    ->withImportNames(removeUnusedImports: true);