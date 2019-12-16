<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Mapping;
use Illuminate\Http\Request;
use App\Http\Requests\MappingCreateRequest;
use App\Repositories\Interfaces\MappingRepositoryInterface as MappingRepository;
use Illuminate\Database\QueryException;

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
    public function store(MappingCreateRequest $request, Webhook $webhook)
    {
        $this->authorize('create', [new Mapping(), $webhook]);

        $attributes = $request->except('_token');
        $attributes['webhook_id'] = $webhook->id;

        try {
            $mapping = $this->mappingRepository->create($attributes);

            return redirect()->route('webhooks.mappings.edit', ['webhook' => $webhook, 'mapping' => $mapping])
                ->with('messageSuccess', 'This mapping successfully created');
        } catch (QueryException $e) {
            return back()->with('messageFail', 'Create failed. Something went wrong');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Webhook $webhook, Mapping $mapping
     * @return \Illuminate\Http\Response
     */
    public function edit(Webhook $webhook, Mapping $mapping)
    {
        return view('mappings.edit', compact('webhook', 'mapping'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Webhook $webhook, Mapping $mapping
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Webhook $webhook, Mapping $mapping)
    {
        //
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
                ->with('messageSuccess', 'This mapping successfully deleted');
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', 'Delete failed. Something went wrong');
        }
    }
}
