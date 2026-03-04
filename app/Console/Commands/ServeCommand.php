<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

class ServeCommand extends BaseServeCommand
{
    public function handle()
    {
        $host = $this->input->getOption('host') ?: '127.0.0.1';
        $port = $this->input->getOption('port') ?: '8000';

        $this->newLine();
        $this->info('  HRIS SDI: http://' . $host . ':' . $port . '/login');
        $this->newLine();

        return parent::handle();
    }
}
