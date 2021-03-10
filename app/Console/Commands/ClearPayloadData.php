<?php

namespace App\Console\Commands;

use App\Models\PayloadHistory;
use App\Models\Webhook;
use Illuminate\Console\Command;

class ClearPayloadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cw:clear-payload-history-data
        {--keep= : Pages to keep for each webhook. Default: 10}
        {--webhookIds= : List WebhookIds. Example: 1,2,3}';

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

        $webhookIdsOpt = $this->option('webhookIds');
        $webhookIds = $webhookIdsOpt && $webhookIdsOpt !== null ? explode(',', $webhookIdsOpt) : [];

        $this->clearPayloadData($keep, $webhookIds);
    }

    /**
     * @param Int $keep
     * @param Array $webhookIds
     */
    protected function clearPayloadData($keep, $webhookIds = [])
    {
        $records = config('paginate.perPage') * $keep;

        $webhooks = Webhook::when(!empty($webhookIds), function ($q) use ($webhookIds) {
            $q->whereIn('id', $webhookIds);
        })->get();

        foreach ($webhooks as $webhook) {
            $ids = $webhook->payloadHistories()->orderByDesc('created_at')->pluck('id')->toArray();
            $notDeleteIds = array_slice($ids, 0, $records);

            PayloadHistory::where('webhook_id', $webhook->id)->whereNotIn('id', $notDeleteIds)->forceDelete();
        }

        $this->info('Clear successfully!');
    }
}
