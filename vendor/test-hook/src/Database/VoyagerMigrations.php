<?php

namespace TestHook\Database;

use Storage;
use Artisan;

class VoyagerMigrations {

	/**
     * Create a migration file based on a JSON table
     *
     * @return void
     */
	public function createMigration($table) {
        $table->name = strtolower($table->name);
        $table->plural_name = str_plural($table->name);
        Artisan::call('make:migration', ['name' => $table->plural_name, '--create' => $table->plural_name]);
        $migrationFileName = $this->getMigrationFileName(Artisan::output());
        $this->addFieldsToMigration($migrationFileName, $table); 
	}

	/**
     * Retrieve migration file name based on make:migration output
     *
     * @return string
     */
    public function getMigrationFileName($artisanOutput) {
        $migrationFileName = str_replace("Created Migration: ", "", $artisanOutput);
        $migrationFileName = str_replace("\n", "", $migrationFileName);
        $migrationFileName .= ".php";
        return $migrationFileName;
    }

    /**
     * Insert up and down functions to a migration file
     *
     * @return void
     */
    public function addFieldsToMigration($migrationFileName, $table) {
        $migrationFile = Storage::disk('migrations')->get($migrationFileName);
        $migrationFile = $this->generateUpFunction($table, $migrationFile);
        $migrationFile = $this->generateDownFunction($table, $migrationFile);
        Storage::disk('migrations')->put($migrationFileName, $migrationFile);
    }

    /**
     * Generate up function based on table JSON and add to migration file
     *
     * @return void
     */
    public function generateUpFunction($table, $migrationFile) {
        $upFunction = "";

        foreach ($table->columns as $index => $column) {
            $name = $column->name;
            $type = $this->voyagerTypesToMigrationTypes($column->type->name, $column);
            if($index > 0) { $upFunction .= "\t\t\t"; }
            $upFunction .= '$table->' . $type . '("' . $column->name . '");' . "\n";
        }

        $migrationFile = str_replace('$' . "table->increments('id');\n", $upFunction, $migrationFile);
        return $migrationFile;
    }

    /**
     * Generate down function based on table JSON and add to migration file
     *
     * @return void
     */
    public function generateDownFunction($table, $migrationFile) {
        $downFunction = 'Schema::dropIfExists("' . $table->plural_name . '");';
        $migrationFile = str_replace("//", $downFunction, $migrationFile);
        return $migrationFile;
    }

    /**
     * Convert Voyager field type names to migration field type names
     *
     * @return string
     */
    public function voyagerTypesToMigrationTypes($type, $column) {
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
