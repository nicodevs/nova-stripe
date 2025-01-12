<?php

namespace Nicodevs\NovaStripe\Models;

use Illuminate\Database\Eloquent\Model;
use Nicodevs\NovaStripe\Traits\SyncsWithStripe;
use Sushi\Sushi;

class Subscription extends Model
{
    use Sushi, SyncsWithStripe;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $rows = [];

    protected $schema = [
        'id' => 'string',
        'description' => 'string',
    ];

    protected $fillable = [
        'id',
        'description',
    ];

    protected $service = 'subscriptions';
}
