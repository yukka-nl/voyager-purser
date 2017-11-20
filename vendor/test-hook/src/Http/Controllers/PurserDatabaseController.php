<?php

namespace TestHook\Http\Controllers;

use TestHook\Database\VoyagerMigrations;
use TCG\Voyager\Http\Controllers\VoyagerDatabaseController as BaseVoyagerDatabaseController;
use Illuminate\Http\Request;

use Voyager;
use Artisan;

class PurserDatabaseController extends BaseVoyagerDatabaseController
{
    public function storeDatabaseTable(Request $request) {
        Voyager::canOrFail('browse_database');

        (new VoyagerMigrations)->createMigration($request->table);
        Artisan::call('migrate');

        return redirect('/admin/database');
    }

    public function updateDatabaseTable(Request $request) {
        (new PurserDatabaseUpdater)->updateTable($request->table);
    }
}
