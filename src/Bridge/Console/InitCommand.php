<?php

namespace Bridge\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected $container = null;
    
    public function configure()
    {
        $this->setName('init');
        $this->setDescription("init project");
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $database = dirname(dirname(dirname(__DIR__))) . '/app/equipment.db';
    
        if (is_file($database)) {
            $output->writeln('<error>Initialization has been completed.</error>');
            exit;
        }
        
        $sqlite = new \SQLite3($database);
        
        $success = $sqlite->exec($this->getCreateTableDDL('counter'));
        
        if ($success) {
            $message = '<info>create table success.</info>';
        } else {
            $message = '<error>create table failed.</error>';
        }
        
        $sqlite->close();
        $output->writeln($message);
    }
    
    private function getCreateTableDDL($table)
    {
        return "CREATE TABLE {$table} (
            id INT PRIMARY KEY NOT NULL,
            total INT NOT NULL,
            max_asset char(20)
        )";
    }
}