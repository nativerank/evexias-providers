<?php

namespace App\Console\Commands;

use App\Importers\PracticeImporter;
use Illuminate\Console\Command;

class PullPractices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pull-practices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull practices from myevexias.com';

    /**
     * Execute the console command.
     */
    public function handle(PracticeImporter $importer)
    {
        $importer->import();
    }
}
