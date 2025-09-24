<?php

namespace App\Console\Commands;

use App\Importers\PracticeImporter;
use App\Models\Practice;
use Illuminate\Console\Command;

class ResetPracticeImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-practice-importer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PracticeImporter::reset();

        return Command::SUCCESS;
    }
}
