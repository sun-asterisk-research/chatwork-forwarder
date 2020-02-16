<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Mapping;
use App\Models\Webhook;
use App\Http\Requests\MappingCreateRequest;
use App\Repositories\Interfaces\MappingRepositoryInterface as MappingRepository;
use Illuminate\Support\Facades\DB;

class MappingController extends Controller
{
    private $mappingRepository;

    public function __construct(MappingRepository $mappingRepository)
    {
        $this->mappingRepository = $mappingRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Webhook $webhook
     * @return \Illuminate\Http\Response
     */
    public function create(Webhook $webhook)
    {
        $this->authorize('create', [new Mapping(), $webhook]);

        return view('mappings.create', compact('webhook'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, Webhook $webhook
     * @return \Illuminate\Http\Response
     */
    public function store(MappingCreateRequest $request, $webhookId)
    {
        $webhook = Webhook::findOrFail($webhookId);
        $this->authorize('create', [new Mapping(), $webhook]);

        DB::beginTransaction();
        try {
            $keys = $request->keys;
            $values = $request->values;
            if ($keys && $values) {
                for ($i = 0; $i < count($keys); $i++) {
                    $attributes['webhook_id'] = $webhookId;
                    $attributes['key'] = $keys[$i];
                    $attributes['value'] = $values[$i];
                    Mapping::where('webhook_id', $webhookId)->updateOrCreate([
                        'key' => $attributes['key'],
                    ], $attributes);
                }
            }

            DB::commit();
            $request->session()->flash('messageSuccess', [
                'status' => 'Create success',
                'message' => 'This mapping successfully created',
            ]);

            return response()->json([
                'error' => false,
                'webhook_id' => $webhook->id,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'Create failed. Something went wrong',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Webhook $webhook, Mapping $mapping
     * @return \Illuminate\Http\Response
     */
    public function edit(Webhook $webhook)
    {
        $mappings = $webhook->mappings()->get();
        return view('mappings.edit', compact('webhook', 'mappings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Webhook $webhook, Mapping $mapping
     * @return \Illuminate\Http\Response
     */
    public function update(MappingCreateRequest $request, $webhookId)
    {
        $webhook = Webhook::findOrFail($webhookId);
        $this->authorize('update', $webhook);

        DB::beginTransaction();
        try {
            $webhook->mappings()->delete();
            $keys = $request->keys;
            $values = $request->values;
            if ($keys && $values) {
                for ($i = 0; $i < count($keys); $i++) {
                    $attributes['webhook_id'] = $webhookId;
                    $attributes['key'] = $keys[$i];
                    $attributes['value'] = $values[$i];
                    Mapping::where('webhook_id', $webhookId)->updateOrCreate([
                        'key' => $attributes['key'],
                    ], $attributes);
                }
            }

            DB::commit();
            $request->session()->flash('messageSuccess', [
                'status' => 'Update success',
                'message' => 'This mapping successfully updated',
            ]);

            return response()->json([
                'error' => false,
                'webhook_id' => $webhook->id,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'Update failed. Something went wrong',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Webhook $webhook, Mapping $mapping
     * @return \Illuminate\Http\Response
     */
    public function destroy(Webhook $webhook, Mapping $mapping)
    {
        $this->authorize('delete', [$mapping, $webhook]);

        try {
            $this->mappingRepository->delete($mapping->id);

            return redirect()->route('webhooks.edit', $webhook)
                ->with('messageSuccess', [
                    'status' => 'Delete success',
                    'message' => 'This mapping successfully deleted',
                ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => 'Delete failed. Something went wrong',
            ]);
        }
    }
}
