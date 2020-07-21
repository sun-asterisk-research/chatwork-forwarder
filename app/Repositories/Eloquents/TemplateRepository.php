<?php
namespace App\Repositories\Eloquents;

use Auth;
use App\Models\Template;
use App\Enums\TemplateStatus;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\TemplateRepositoryInterface;

class TemplateRepository extends BaseRepository implements TemplateRepositoryInterface
{
    public function getModel()
    {
        return Template::class;
    }

    public function getTemplate()
    {
        return Template::where('user_id', Auth::user()->id)->orWhere('status', TemplateStatus::STATUS_PUBLIC)->get();
    }

    public function getAllByUser($perPage)
    {
        return Auth::user()->templates()
            ->orderBy('templates.status', 'desc')
            ->orderBy('templates.created_at', 'desc')
            ->paginate($perPage);
    }

    public function getAllAndSearch($perPage, $searchParams)
    {
        $query =$this->model->orderBy('templates.created_at', 'desc');

        if ($searchParams) {
            $searchParams = $this->handleSearchParams(['name', 'status'], $searchParams);

            return $query->search($searchParams, $perPage);
        } else {
            return $query->paginate($perPage);
        }
    }
}
