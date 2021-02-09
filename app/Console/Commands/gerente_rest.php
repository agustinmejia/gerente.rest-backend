<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class gerente_rest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gerente:install
                            {--r|reset : Reset database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Gerente.Rest';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if($this->option('reset')){
            $this->call('migrate:fresh');
            $this->call('db:seed');
            $this->call('passport:install');
            $this::call('passport:client', ['--personal' => true]);
            $this->info('La base de datos de Gerente.rest ha sido reiniciada ;)');
        }else{
            
            $empty_database = false;
            try {
                DB::table('users')->get();
            } catch (\Throwable $th) {
                $empty_database = true;
            }

            if($empty_database){
                $this->call('key:generate');
                $this->call('migrate');
                $this->call('db:seed');
                $this->call('storage:link');
                $this->call('passport:install');
                $this::call('passport:client', ['--personal' => true]);
                $this->info('Gracias por instalar Gerente.rest!!!');
$this->info('
                ( ͡▀̿ ̿ ͜ʖ ͡▀̿ ̿ )

█▀▀ █▀▀ █▀█ █▀▀ █▄░█ ▀█▀ █▀▀ ░ █▀█ █▀▀ █▀ ▀█▀
█▄█ ██▄ █▀▄ ██▄ █░▀█ ░█░ ██▄ ▄ █▀▄ ██▄ ▄█ ░█░
');
            }else{
                if ($this->confirm('La base de datos de Gerente.rest no está vacía, ¿Deseas reiniciarla?')) {
                    $this->call('migrate:fresh');
                    $this->call('db:seed');
                    $this->call('passport:install');
                    $this::call('passport:client', ['--personal' => true]);
                    $this->info('La base de datos de Gerente.rest ha sido reiniciada ;)');
                }
            }
        }
    }
}
