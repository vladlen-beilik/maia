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
        $path = $this->ask('What is your application full path? (ex: /var/www/example/data/www/you_application/)');
        if($path === '' || empty($path)) {
            $this->error('Application full path for composer is required.');
        }
        putenv("COMPOSER_HOME={$path}");
        putenv("HOME={$path}");
        $this->info('CMS Maia install started');
        $this->info('Download files...');
        $req = new Process(['/usr/local/bin/composer', 'require', 'spacecode-dev/maia']);
        $req->setTimeout(null)->run();
        if($req->isSuccessful()) {
            $this->info('All files successfully download');
            $this->info('Publishing...');
            $this->callSilent('maia:publish');
            $this->info('CMS Maia successfully installed');
            if ($this->confirm('You need to create an admin user. Do you wish to continue?')) {
                $name = $this->ask('What is your user login? (ex: admin)');
                $email = $this->ask('What is your user email?');
                $password = $this->secret('What is the password?');
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
                $this->info('Admin user successfully created.');
                $this->info('You may login after several seconds.');
                $this->info('Enjoy using CMS Maia.');
                $this->info('Have a nice day )))');
                $this->info('Best regards, SpaceCode Team');
            }
        } else {
            throw new ProcessFailedException($req);
        }
    }
}
