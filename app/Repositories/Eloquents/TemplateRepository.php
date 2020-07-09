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
        return Template::where('user_id', Auth::user()->id)->orWhere('status', TemplateStatus::PUBLIC)->get();
    }
}
