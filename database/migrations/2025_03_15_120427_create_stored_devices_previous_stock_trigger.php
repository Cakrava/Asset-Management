<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Schema;

class CreateStoredDevicesPreviousStockTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_stored_devices_previous_stock
            BEFORE UPDATE ON stored_devices
            FOR EACH ROW
            BEGIN
                IF NEW.stock <> OLD.stock THEN
                    SET NEW.previous_stock = OLD.stock;
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_stored_devices_previous_stock');
    }
}