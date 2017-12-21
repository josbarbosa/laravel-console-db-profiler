<?php namespace PackageTests\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PackageTests\Database\Test;

class Command02 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command02';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command02';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call("test:command01");
        Test::count();
    }
}
