<?php

namespace Nicodevs\NovaStripe\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Nicodevs\NovaStripe\Actions\Sync;

abstract class BaseResource extends Resource
{
    public static $displayInNavigation = false;

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function actions(NovaRequest $request)
    {
        return [
            Sync::make()->standalone(),
        ];
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    protected function formatAmount($value)
    {
        if (! $value) {
            return '—';
        }
        $currency = Str::upper($this->currency) ?? '';

        return $currency . ' ' . number_format($value / 100, 2);
    }

    protected function formatDateTime($value)
    {
        if (! $value) {
            return '—';
        }

        return Carbon::parse($value)->format('M j, Y, g:i A');
    }
}
