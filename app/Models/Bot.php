<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }
}
