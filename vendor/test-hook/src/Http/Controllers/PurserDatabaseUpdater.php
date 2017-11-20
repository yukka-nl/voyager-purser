<?php

namespace TestHook\Http\Controllers;

use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;

use Artisan;
use Schema;

class PurserDatabaseUpdater
{
    protected $newTable;
    protected $originalTable;

    public function updateTable($table) {
        $table = json_decode($table);
        $this->newTable = $table;
        $this->originalTable = SchemaManager::listTableDetails($table->oldName);


        $this->checkRenamedTable();
        $this->checkRenamedColumns();
        $this->checkDeletedTables();
        $this->checkDeletedColumns();
        $this->checkAlteredColumns();
        $this->checkAddedColumns();

        Artisan::call('migrate');
    }

    public function checkRenamedTable() {
        if($this->newTable->name != $this->originalTable->name) {
           // Create migration for renaming
        }
    }

    public function checkRenamedColumns() {
        foreach($this->newTable->columns as $column) {
            echo($column->name) . "<br>";
        }
        echo '<br><br>';

        foreach($this->originalTable->columns as $column) {
            var_dump($column) . "<br>";
        }
    }

    public function checkDeletedTables() {
       // Drop table
    }

    public function checkDeletedColumns() {
        // Drop column
    }

    public function checkAlteredColumns() {
        // Compare original with new table
        // Add modifier field for each change
    }

    public function checkAddedColumns() {
        // Check if columns have been added
    }
}
