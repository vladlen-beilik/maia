<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\User;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maia:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install CMS Maia';

    public function handle()
    {
        if (\File::exists(base_path('nova'))) {
            $this->error('Missing installation folder for Laravel Nova ("./nova").');
        }
        if ($this->confirm('You need to check you database connections charset. It can be `utf8`. Do you wish to continue?')) {
            if(config('database.connections.mysql.charset') !== 'utf8') {
                $this->error('Your database connections charset does not fit the requirements.');
            }
            if ($this->confirm('You need to check you database connections collation. It can be `utf8_unicode_ci`. Do you wish to continue?')) {
                if(config('database.connections.mysql.collation') !== 'utf8_unicode_ci') {
                    $this->error('Your database connections collation does not fit the requirements.');
                }
                $path = $this->ask('What is your application full path? (ex: /var/www/example/data/www/you_application/)');
                if($path === '' || empty($path)) {
                    $this->error('Application full path for composer is required.');
                }
                putenv("COMPOSER_HOME={$path}");
                putenv("HOME={$path}");
                $this->info('Download files...');
                $req = new Process(['/usr/local/bin/composer', 'require', 'spacecode-dev/maia']);
                $req->setTimeout(null)->run();
                if($req->isSuccessful()) {
                    $this->info('Publishing...');
                    $this->callSilent('maia:publish');
                    $this->info('CMS Maia successfully installed');
                    if ($this->confirm('You need to create you first user. He will be an Admin. Do you wish to continue?')) {
                        $name = $this->ask('What is your user login? (ex: admin)');
                        $email = $this->ask('What is your user email?');
                        $password = $this->secret('What is the password?');
                        $this->info($password);
                        if($name === '' || empty($name)) {
                            $this->error('User login is required.');
                        }
                        if($email === '' || empty($email)) {
                            $this->error('User email is required.');
                        }
                        if($password === '' || empty($password)) {
                            $this->error('Password is required.');
                        }
                        User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'created_at' => new \DateTime(), 'updated_at' => new \DateTime()]);
                    }
                } else {
                    throw new ProcessFailedException($req);
                }
            }
        }
    }
}
