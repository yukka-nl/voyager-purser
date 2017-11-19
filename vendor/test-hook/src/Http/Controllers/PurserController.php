<?php

namespace TestHook\Http\Controllers;

use TestHook\Database\VoyagerMigrations;
use TCG\Voyager\Http\Controllers\VoyagerDatabaseController as BaseVoyagerDatabaseController;
use Illuminate\Http\Request;

use Voyager;
use Artisan;

class PurserController extends BaseVoyagerDatabaseController
{
    public function storeDatabaseTable(Request $request) {
        Voyager::canOrFail('browse_database');

        (new VoyagerMigrations)->createMigration(json_decode($request->table));
        Artisan::call('migrate');

        return redirect('/admin/database');
    }

    public function updateDatabaseTable(Request $request) {
        // Add own implementation of DatabaseUpdater class here
    }

}
