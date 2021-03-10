<?php

namespace App\Console\Commands;

use App\Models\PayloadHistory;
use Illuminate\Console\Command;

class ClearPayloadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cw:clear-payload-history-data
        {--keep= : Pages to keep for each webhook. Default: 10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear payload histories and message histories, keep 10 pages for each webhook.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $keepOpt = $this->option('keep');
        $keep = $keepOpt !== null ? (Int) $keepOpt : 10;

        $this->clearPayloadData($keep);
    }

    /**
     * @param Int $keep
     */
    protected function clearPayloadData($keep)
    {
        $records = config('paginate.perPage') * 10;
        PayloadHistory::withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy('webhook_id')
            ->each(function ($histories) use ($records) {
                $webhookId = $histories[0]->webhook_id;
                $ids = array_slice($histories->pluck('id')->toArray(), 0, $records);
                PayloadHistory::where('webhook_id', $webhookId)->whereNotIn('id', $ids)->forceDelete();
            });
    }
}
