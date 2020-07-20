<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TemplateCreateRequest;
use App\Http\Requests\TemplateUpdateRequest;
use Auth;
use App\Enums\TemplateStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Template;
use App\Repositories\Interfaces\TemplateRepositoryInterface as TemplateRepository;

class TemplateController extends Controller
{
    private $templateRepository;

    public function __construct(TemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    public function index(Request $request)
    {
        $searchParams = $request->get('search');
        $perPage = config('paginate.perPage');
        $templates = $this->templateRepository->getAllAndSearch($perPage, $searchParams);

        return view('admins.templates.index', compact('templates'));
    }

    public function destroy(Request $request, Template $template)
    {
        $page = $request->page ? ['page' => $request->page] : null;
        try {
            $this->templateRepository->delete($template->id);

            return redirect()->route('admin.template.index', $page)->with('messageSuccess', [
                'status' => 'Delete success',
                'message' => __('message.notification.delete.success', ['object' => 'template']),
            ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => __('message.notification.delete.fail', ['object' => 'template']),
            ]);
        }
    }

    public function changeStatus(Request $request, Template $template)
    {
        if ($request->status == TemplateStatus::STATUS_PUBLIC
            || $request->status == TemplateStatus::STATUS_UNPUBLIC
            || $request->status == TemplateStatus::STATUS_REVIEWING
        ) {
            $result = $this->templateRepository->update($template->id, ['status' => $request->status]);
            if ($result) {
                return 'This template was updated status successfully';
            }
        }

        return response()->json([
            'status' => 'Updated failed',
            'message' => 'Updated failed. Something went wrong',
        ], 400);
    }
}
