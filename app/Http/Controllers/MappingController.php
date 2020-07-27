<?php

namespace App\Http\Controllers;

use Exception;
use File;
use Response;
use App\Models\Mapping;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Requests\MappingCreateRequest;
use App\Http\Requests\MappingUpdateRequest;
use App\Http\Requests\MappingImportRequest;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\MappingRepositoryInterface as MappingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        $this->authorize('update', $webhook);

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
        $this->authorize('update', $webhook);

        DB::beginTransaction();
        try {
            $keys = $request->keys;
            $values = $request->values;
            if ($keys && $values) {
                for ($i = 0; $i < count($keys); $i++) {
                    $attributes['webhook_id'] = $webhook->id;
                    $attributes['key'] = $keys[$i];
                    $attributes['value'] = $values[$i];
                    $mapping = Mapping::where('webhook_id', $webhook->id)->updateOrCreate([
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
        $this->authorize('update', $webhook);
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
    public function update(MappingCreateRequest $request, Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        DB::beginTransaction();
        try {
            $mappings = $webhook->mappings()->delete();
            $keys = $request->keys;
            $values = $request->values;
            if ($keys && $values) {
                for ($i = 0; $i < count($keys); $i++) {
                    $attributes['webhook_id'] = $webhook->id;
                    $attributes['key'] = $keys[$i];
                    $attributes['value'] = $values[$i];
                    $mapping = Mapping::where('webhook_id', $webhook->id)->updateOrCreate([
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

    public function import(Webhook $webhook, MappingImportRequest $request)
    {
        $this->authorize('update', $webhook);
        DB::beginTransaction();
        try {
            $keys = $this->mappingRepository->getKeys($webhook);
            $data = json_decode(file_get_contents($request->file), true);

            Validator::make($data, [
                '*.key' => 'required|max:100',
                '*.value' => 'required|max:100',
            ])->validate();

            $newData = [];
            foreach ($data as $item) {
                if (in_array($item['key'], $keys)) {
                    return response()->json([
                        'error' => true,
                        'message' => "Key {$item['key']} exists.",
                    ]);
                }

                array_push($newData, array_merge($item, ['webhook_id' => $webhook->id]));
            }

            $this->mappingRepository->insert($newData);
            DB::commit();
            $request->session()->flash('messageSuccess', [
                'status' => 'Import file success',
                'message' => 'This mapping successfully Import file',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'Import file failed. Something went wrong',
            ]);
        }
    }

    public function exportJson(Webhook $webhook, Request $request)
    {
        $this->authorize('update', $webhook);
        DB::beginTransaction();
        try {
            $keyValues = $this->mappingRepository->getKeyAndValues($webhook);
            $fileName = time() . "_$webhook->name.json";

            $data = json_encode($keyValues, JSON_PRETTY_PRINT);
            File::put(public_path('/json/'.$fileName), $data);

            return Response::download(public_path('/json/'. $fileName))
                ->deleteFileAfterSend();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => 'Export file failed. Something went wrong',
            ]);
        }
    }
}
