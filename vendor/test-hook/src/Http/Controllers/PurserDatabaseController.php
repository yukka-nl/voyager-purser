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
        $table = json_decode($request->table);
      
        $upFunction = $this->generateMigrationUpFunction($table);
        $downFunction = $this->generateMigrationDownFunction($table);

        $this->createMigration($table, $upFunction, $downFunction);

        Artisan::call('migrate');

        return redirect('/admin/database');
    }

    public function createMigration($table, $upFunction, $downFunction) {

        $fileName = '2017_09_16_100000_create_'.$table->name.'_table.php';
        $className = 'Create' . ucfirst(strtolower($table->name)) . 'Table';
        $tableName = strtolower($table->name);

        $migrationContent = '
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ' . $className .' extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("' . $tableName . '", function (Blueprint $table) {
        	' . $upFunction . '
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create("' . $tableName . '", function (Blueprint $table) {
            ' . $downFunction . '
        });
    }
}';

        $migrationContent = trim($migrationContent);

		Storage::disk('migrations')->put($fileName, $migrationContent);
    }

    public function generateMigrationUpFunction($table) {
        $upFunction = "";

        foreach ($table->columns as $index => $column) {
            $name = $column->name;
            $type = $this->convertToMigrationTypes($column->type->name, $column);
            $upFunction .= '$table->' . $type . '("' . $column->name . '");
            ';
        }

        return $upFunction;
    }

    public function generateMigrationDownFunction($table) {
        return 'Schema::dropIfExists("' . $table->name . '");';
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
