<?php

namespace App\Libraries;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Pdf
{
    /**
     * The command.
     * 
     * @var string
     */
    protected $command = '%s --headless --disable-gpu --print-to-pdf=%s %s 2>&1';

    /**
     * The binary.
     * 
     * @var string
     */
    protected $binary = 'google-chrome-stable';

    /**
     * Render the PDF.
     *
     * @param  string $htmlContent
     * @return string
     */
    public function render($htmlContent)
    {        
        $process = new Process(sprintf(
            $this->command,
            escapeshellarg($this->binary),
            escapeshellarg($path = tempnam(sys_get_temp_dir(), Str::random())),
            escapeshellarg('data:text/html,'.rawurlencode($htmlContent))
        ));

        try {
            $process->mustRun();

            return File::get($path);
        } catch (ProcessFailedException $exception) {
            throw $exception;
        }
    }
}
