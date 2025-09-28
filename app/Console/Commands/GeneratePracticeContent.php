<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePracticeContentJob;
use App\Models\Practice;
use Illuminate\Console\Command;

class GeneratePracticeContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-practice-content';

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
        $practices = Practice::query()->whereHas('location')->with(['location'])->get()->filter(function (Practice $practice) {
            return !isset($practice->content);
        });
        $bar = $this->output->createProgressBar($practices->count());

        $practices->each(function (Practice $practice) use ($bar) {
            $this->output->info("Starting $practice->name...\n");
            GeneratePracticeContentJob::dispatchSync($practice, null);
            $practice->touch();
            $bar->advance();
        });

        $bar->finish();
    }

}
