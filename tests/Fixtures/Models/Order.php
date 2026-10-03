<?php

declare(strict_types=1);

namespace FelipeArnold\FilamentSteppedTabs\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

final class Order extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
