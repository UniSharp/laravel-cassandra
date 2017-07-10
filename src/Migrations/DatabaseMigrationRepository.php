<?php

namespace Unisharp\Cassandra\Migrations;

class DatabaseMigrationRepository extends \Illuminate\Database\Migrations\DatabaseMigrationRepository
{
    public function getRan()
    {
        return $this->table()
            ->get()
            ->sortBy('batch')
            ->pluck('migration')
            ->all();
    }

    public function createRepository()
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        $schema->create($this->table, function ($table) {
            // The migrations table is responsible for keeping track of which of the
            // migrations have actually run for the application. We'll create the
            // table to hold the migration file's path as well as the batch ID.
            $table->uuid('id')->primary();
            $table->string('migration');
            $table->integer('batch');
        });
    }

    public function log($file, $batch)
    {
        $record = ['id' => $this->getConnection()->raw('uuid()'), 'migration' => $file, 'batch' => $batch];

        $this->table()->insert($record);
    }
}
