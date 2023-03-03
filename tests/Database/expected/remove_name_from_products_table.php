<?php

use Baubyte\Database\DB;
use Baubyte\Database\Migrations\Migration;

return new class () implements Migration {
    public function up() {
        DB::statement('ALTER TABLE products');
    }

    public function down() {
        DB::statement('ALTER TABLE products');
    }
};
