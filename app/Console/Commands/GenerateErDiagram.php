<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class GenerateErDiagram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-er-diagram {--output=er-diagram.md : The output file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Mermaid ER diagram from the database schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = Schema::getTables();
        
        $mermaid = "```mermaid\nerDiagram\n";
        
        $foreignKeysMermaid = "";
        
        $processedTables = [];

        foreach ($tables as $tableInfo) {
            $tableName = $tableInfo['name'];
            
            if (in_array($tableName, $processedTables)) {
                continue;
            }
            $processedTables[] = $tableName;

            $queryName = $tableInfo['schema_qualified_name'] ?? $tableName;
            $columns = Schema::getColumns($queryName);
            $foreignKeys = Schema::getForeignKeys($queryName);

            $indexes = Schema::getIndexes($queryName);
            $uniqueColumns = [];
            $primaryColumns = [];
            foreach ($indexes as $index) {
                if ($index['primary']) {
                    $primaryColumns = array_merge($primaryColumns, $index['columns']);
                } elseif ($index['unique']) {
                    $uniqueColumns = array_merge($uniqueColumns, $index['columns']);
                }
            }

            // Table declaration and columns
            $mermaid .= "    {$tableName} {\n";
            
            // Emphasize documents and payments as pivot tables
            if (in_array($tableName, ['documents', 'payments'])) {
                $mermaid .= "        note PIVOT_TABLE\n";
            }

            foreach ($columns as $column) {
                // Remove spaces and special chars for Mermaid type, but keep parens if possible? Mermaid allows parens? 
                // Actually alphanumeric is safer.
                $type = preg_replace('/[^a-zA-Z0-9_]/', '_', $column['type_name']);
                
                // If you want the full type with length (e.g. varchar(255)), replace spaces.
                $fullType = preg_replace('/[^a-zA-Z0-9_\(\)]/', '_', $column['type']);
                
                $name = $column['name'];
                
                $isPk = (in_array($name, $primaryColumns) || (isset($column['auto_increment']) && $column['auto_increment'])) ? ' PK' : '';
                $isUk = (!$isPk && in_array($name, $uniqueColumns)) ? ' UK' : '';
                
                $notes = [];
                if (isset($column['nullable']) && $column['nullable']) {
                    $notes[] = 'nullable';
                }
                
                $comment = !empty($notes) ? ' "' . implode(', ', $notes) . '"' : '';

                $mermaid .= "        {$fullType} {$name}{$isPk}{$isUk}{$comment}\n";
            }
            $mermaid .= "    }\n";

            // Foreign keys relationships
            foreach ($foreignKeys as $fk) {
                $foreignTable = $fk['foreign_table'];
                
                // Represent a one-to-many relationship as default for simplicity in ERD
                $foreignKeysMermaid .= "    {$foreignTable} ||--o{ {$tableName} : \"uses\"\n";
            }
        }
        
        $mermaid .= $foreignKeysMermaid;
        $mermaid .= "```\n";

        $outputFile = $this->option('output');
        File::put(base_path($outputFile), $mermaid);

        $this->info("ER Diagram successfully generated at: {$outputFile}");
        $this->info("You can preview the file in an online Mermaid viewer or directly in Github/VSCode.");
    }
}
