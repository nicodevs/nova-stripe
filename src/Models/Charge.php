<?php

namespace Nicodevs\NovaStripe\Models;

use Illuminate\Database\Eloquent\Model;
use Nicodevs\NovaStripe\Traits\SyncsWithStripe;
use Sushi\Sushi;

class Charge extends Model
{
    use Sushi, SyncsWithStripe;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $rows = [];

    protected $schema = [
        'id' => 'string',
        'amount' => 'integer',
    ];

    protected $fillable = [
        'id',
        'amount',
    ];

    protected $service = 'charges';
}
