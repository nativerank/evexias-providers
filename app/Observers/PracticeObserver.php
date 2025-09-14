<?php

namespace App\Observers;

use App\Models\Practice;
use NativeRank\InventorySync\Enums\ItemChangeType;
use NativeRank\InventorySync\Events\ItemSaved;
use NativeRank\InventorySync\Events\ItemUpdated;

class PracticeObserver
{
    /**
     * Handle the Practice "created" event.
     */
    public function created(Practice $practice): void
    {
        
    }

    /**
     * Handle the Practice "updated" event.
     */
    public function updated(Practice $practice): void
    {
        //
    }

    public function saved(Practice $practice): void
    {
        event(new ItemSaved($practice));
    }

    public function deleting(Practice $practice): void
    {
        $practice->practitioners()->detach();
    }

    /**
     * Handle the Practice "deleted" event.
     */
    public function deleted(Practice $practice): void
    {
        event(new ItemUpdated($practice, ItemChangeType::DROP));
    }

    /**
     * Handle the Practice "restored" event.
     */
    public function restored(Practice $practice): void
    {
        //
    }

    /**
     * Handle the Practice "force deleted" event.
     */
    public function forceDeleted(Practice $practice): void
    {
        //
    }
}
