<?php

namespace Baubyte\Database\Migrations;

interface Migration {
    /**
     * Run migration
     *
     * @return void
     */
    public function up();

    /**
     * Reverse migration
     *
     * @return void
     */
    public function down();
}
