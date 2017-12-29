<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Command01 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command01';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Command 01';

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
        DB::table('tests')
            ->select(DB::raw('count(*) as name, name'))
            ->where('name', '<>', 'new@email.com')
            ->groupBy('name')
            ->get();

        DB::table('tests')
            ->selectRaw('id * ? as id_multi', [1.0825])
            ->get();

        DB::table('tests')
            ->orderByRaw('updated_at - created_at DESC')
            ->get();

        $first = DB::table('tests')
            ->whereNull('name');

        DB::table('tests')
            ->whereNull('name')
            ->union($first)
            ->get();

        DB::table('tests')
            ->where('name', 'like', '%T')
            ->get();

        DB::table('tests')->where([
            ['id', '=', '1'],
            ['name', '<>', '1'],
        ])->get();

        DB::table('tests')
            ->whereBetween('id', [1, 100])->get();

        DB::table('tests')
            ->whereNotIn('id', [1, 2, 3])
            ->get();

        DB::table('tests')
            ->where('name', '=', 'John')
            ->orWhere(function ($query) {
                $query->where('name', '>', 100)
                    ->where('name', '<>', 'Admin');
            })
            ->get();

        DB::table('tests')->skip(10)->take(5)->get();
    }
}
