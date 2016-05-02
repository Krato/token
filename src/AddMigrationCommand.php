<?php namespace Infinety\Token;

use Illuminate\Console\Command;

class AddMigrationCommand extends Command
{
    protected $name = 'token:migration';

    protected $description = 'Add database migration for Infinety\Token';

    public function handle()
    {
        // Create a new migration
        $name = 'create_tokens_table';
        $ds = DIRECTORY_SEPARATOR;
        $path = database_path().$ds.'migrations';
        $filename = $this->laravel['migration.creator']->create($name, $path);
        // Overwrite with migration content
        $content = file_get_contents(__DIR__ . $ds .$ds.'migration.stub');
        file_put_contents($filename, $content);
    }
}
