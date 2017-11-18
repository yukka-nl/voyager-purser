<?php

namespace TestHook\Http\Controllers;

use TCG\Voyager\Http\Controllers\VoyagerDatabaseController as BaseVoyagerDatabaseController;
use Illuminate\Http\Request;
use Voyager;
use Storage;
use Artisan;

class PurserDatabaseController extends BaseVoyagerDatabaseController
{
    public function purser(Request $request) {
        Voyager::canOrFail('browse_database');

        // Retrieve request variables
        $table = json_decode($request->table);
        $table->name = strtolower($table->name);

        // Create new migration
        Artisan::call('make:migration', [
            'name' => $table->name
        ]);

        // Retrieve migration filename based on the Artisan output
        $migrationFileName = Artisan::output();
        $migrationFileName = str_replace("Created Migration: ", "", $migrationFileName);
        $migrationFileName = str_replace("\n", "", $migrationFileName);
        $migrationFileName .= ".php";

        // Assemble new migration file
        $migrationFile = Storage::disk('migrations')->get($migrationFileName);
        $migrationFile = $this->addUpFunction($table, $migrationFile);
        $migrationFile = $this->addDownFunction($table, $migrationFile);
        Storage::disk('migrations')->put($migrationFileName, $migrationFile);
        

        // Execute migration
        Artisan::call('migrate');
        return redirect('/admin/database');
    }


    public function addUpFunction($table, $migrationFile) {

        // Generate function content
        $upFunction = 'Schema::create("' . str_plural($table->name) . '", function (Blueprint $table) {';
        $upFunction .= "\n";
        foreach ($table->columns as $index => $column) {
            $name = $column->name;
            $type = $this->convertToMigrationTypes($column->type->name, $column);
            $upFunction .= '$table->' . $type . '("' . $column->name . '");';
            $upFunction .= "\n";
        }
        $upFunction .= "\n});";

        // Add function to migration file
        $pos = strpos($migrationFile, "//");
        if ($pos !== false) {
            $migrationFile = substr_replace($migrationFile, $upFunction, $pos, strlen("//"));
        }

        return $migrationFile;
    }

    public function addDownFunction($table, $migrationFile) {
        // Generate function content
        $downFunction = 'Schema::dropIfExists("' . str_plural($table->name) . '");';

         // Add function to migration file
        $pos = strrpos($migrationFile, "//");
        if ($pos !== false) {
            $migrationFile = substr_replace($migrationFile, $downFunction, $pos, strlen("//"));
        }

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
