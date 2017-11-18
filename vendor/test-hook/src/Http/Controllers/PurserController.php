<?php

namespace TestHook\Http\Controllers;

use TCG\Voyager\Http\Controllers\VoyagerDatabaseController as BaseVoyagerDatabaseController;
use Illuminate\Http\Request;
use Voyager;
use Storage;
use Artisan;

class PurserController extends BaseVoyagerDatabaseController
{
    public function storeDatabaseTable(Request $request) {

        // Process request variables
        Voyager::canOrFail('browse_database');
        $table = json_decode($request->table);
        $table->name = strtolower($table->name);
        $table->plural_name = str_plural($table->name);

        // Create and execute migration
        Artisan::call('make:migration', ['name' => $table->plural_name, '--create' => $table->plural_name]);
        $migrationFileName = $this->getMigrationFileName(Artisan::output());
        $this->addFieldsToMigration($migrationFileName, $table); 
        Artisan::call('migrate');

        return redirect('/admin/database');
    }

    public function getMigrationFileName($artisanOutput) {
        $migrationFileName = str_replace("Created Migration: ", "", $artisanOutput);
        $migrationFileName = str_replace("\n", "", $migrationFileName);
        $migrationFileName .= ".php";
        return $migrationFileName;
    }

    public function addFieldsToMigration($migrationFileName, $table) {
        $migrationFile = Storage::disk('migrations')->get($migrationFileName);
        $migrationFile = $this->generateUpFunction($table, $migrationFile);
        $migrationFile = $this->generateDownFunction($table, $migrationFile);
        Storage::disk('migrations')->put($migrationFileName, $migrationFile);
    }

    public function generateUpFunction($table, $migrationFile) {
        $upFunction = "";

        foreach ($table->columns as $index => $column) {
            $name = $column->name;
            $type = $this->convertToMigrationTypes($column->type->name, $column);
            if($index > 0) { $upFunction .= "\t\t\t"; }
            $upFunction .= '$table->' . $type . '("' . $column->name . '");' . "\n";
        }

        $migrationFile = str_replace('$' . "table->increments('id');\n", $upFunction, $migrationFile);
        return $migrationFile;
    }

    public function generateDownFunction($table, $migrationFile) {
        $downFunction = 'Schema::dropIfExists("' . $table->plural_name . '");';
        $migrationFile = str_replace("//", $downFunction, $migrationFile);
        return $migrationFile;
    }

    public function convertToMigrationTypes($type, $column) {
        switch ($type) {
            case "bigint":
                $type = "bigInteger";
                break;
            case "longblob":
                $type = "binary";
                break;
            case "blobmedium":
                $type = "binary";
                break;
            case "blobtiny":
                $type = "binary";
                break;
            case "blob":
                $type = "binary";
                break;
            case "varbinary":
                $type = "binary";
                break;
            case "blobmedium":
                $type = "binary";
                break;
            case "bit":
                $type = "boolean";
                break;
            case "datetime":
                $type = "dateTime";
                break;
            case "geometrycollection":
                $type = "geometryCollection";
                break;
            case "linestring":
                $type = "lineString";
                break;
            case "longtext":
                $type = "longText";
                break;
            case "mediumint":
                $type = "mediumInteger";
                break;
            case "mediumtext":
                $type = "mediumText";
                break;
            case "multilinestring":
                $type = "multiLineString";
                break;
            case "multipoint":
                $type = "multiPoint";
                break;
            case "multipolygon":
                $type = "multiPolygon";
                break;
            case "smallint":
                $type = "smallInteger";
                break;
            case "varchar":
                $type = "string";
                break;
            case "tinyint":
                $type = "tinyInteger";
                break;
        }

        // Exception Types
        if($type == "integer") {
            if($column->autoincrement == true) {
                $type = "increments";
            }
        }

        return $type;
    }
}
