<?php namespace LaravelSeed\Commands;

use Illuminate\Console\Command;
use LaravelSeed\Exceptions\SeederException;
use LaravelSeed\Laravel5SeedServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Seeder extends Command {

    private $availableArgs = ['run', 'make'];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'yaml:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed an database from yaml.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        if( !in_array($this->argument('operation'), $this->availableArgs) )
            return $this->error(
                sprintf('Please provider an operation! Use follow commands: %s.', implode(', ', $this->availableArgs))
            );

        if( $this->argument('operation') != 'run' )
            if( ! class_exists('App\\' . ucfirst(strtolower($this->option('model')))) )
                return $this->error('Invalid model class');

        try {
            // by default for the moment we will using only yaml provider to parse data from yaml files ..
            $provider = app(Laravel5SeedServiceProvider::IOC_ALIAS)->factory('yaml');

            if( $this->argument('operation') == 'run' ) {

                // need to be run all of the registered seeds ...

            } elseif( $this->argument('operation') == 'make' ) {
                if( $file = $provider->makeFile( $this->option('model'), $this->option('model'), $this->option('model') ) ) {
                    return $this->info(sprintf('File "%s" created successfully!', $file));
                }
            }

        } catch(SeederException $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['operation', InputArgument::OPTIONAL, 'An operation to run.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'Eloquent class model.', null],
            ['class', null, InputOption::VALUE_OPTIONAL, 'An default DbClassSeeder.', null],
            ['path', null, InputOption::VALUE_OPTIONAL, 'An custom path to create yaml seeders.', null],
        ];
    }
}
