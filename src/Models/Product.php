<?php

namespace Nicodevs\NovaStripe\Models;

use Illuminate\Database\Eloquent\Model;
use Nicodevs\NovaStripe\Traits\SyncsWithStripe;
use Sushi\Sushi;

class Product extends Model
{
    use Sushi, SyncsWithStripe;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $rows = [];

    protected $schema = [
        'id' => 'string',
        'name' => 'string',
    ];

    protected $fillable = [
        'id',
        'name',
    ];

    protected $service = 'products';
}
