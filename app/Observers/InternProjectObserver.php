<?php

namespace App\Observers;

use App\Models\InternProject;
use App\Models\ProjectChat;

class InternProjectObserver
{
    /**
     * Handle the InternProject "created" event.
     * This runs automatically the moment a new project is saved to the database.
     */
    public function created(InternProject $internProject): void
    {
        // Automatically generate a chat room for this new project
        ProjectChat::create([
            'project_id' => $internProject->project_id
        ]);
    }

    /**
     * Handle the InternProject "deleted" event.
     * Optional: Clean up the chat if the project is ever deleted.
     */
    public function deleted(InternProject $internProject): void
    {
        // The chat will automatically delete if you set up the 'cascade' on your migration,
        // but it is good practice to explicitly handle it if needed.
    }

    // You can ignore updated, restored, and forceDeleted for now.
}