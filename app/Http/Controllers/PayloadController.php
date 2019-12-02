<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayloadCreateRequest;
use App\Models\Payload;
use App\Models\Webhook;
use App\Repositories\Interfaces\PayloadRepositoryInterface as PayloadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayloadController extends Controller
{
    private $payloadRepository;

    public function __construct(PayloadRepository $payloadRepository)
    {
        $this->payloadRepository = $payloadRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($webhookId)
    {
        return view('payloads.create', compact('webhookId'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayloadCreateRequest $request, $webhookId)
    {
        $webhook = Webhook::findOrFail($webhookId);
        $data = $request->only(['content']);
        $data['webhook_id'] = $webhook->id;

        DB::beginTransaction();
        try {
            $payload = $this->payloadRepository->create($data);
            $conditions = $request->only('conditions');

            if ($conditions) {
                foreach ($conditions['conditions'] as $condition) {
                    $payload->conditions()->create(['condition' => $condition]);
                }
            }

            DB::commit();
            $request->session()->flash('messageSuccess', 'This payload successfully created');

            return $payload->id;
        } catch (QueryException $exception) {
            DB::rollBack();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payload  $payload
     * @return \Illuminate\Http\Response
     */
    public function edit(Webhook $webhook, Payload $payload)
    {
        return view('payloads.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payload  $payload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payload $payload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payload  $payload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payload $payload)
    {
        //
    }
}
