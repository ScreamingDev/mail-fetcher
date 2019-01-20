<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

declare(strict_types=1);

namespace ScreamingDev\MailReader;

use PhpMimeMailParser\Parser;

class MailDir
{
    /**
     * @var string
     */
    private $mailDir;

    public function __construct(string $mailDir)
    {
        $this->mailDir = rtrim($mailDir, '/') . '/';
    }

    /**
     * @return Parser[]|\Generator
     */
    public function getMails()
    {
        $fileList = glob($this->mailDir . '*');
        natsort($fileList);
        $fileList = array_reverse($fileList);

        foreach ($fileList as $mailPathname) {
            $parser = new Parser();
            $parser->setPath($mailPathname);

            yield basename($mailPathname) => $parser;
        }
    }
}