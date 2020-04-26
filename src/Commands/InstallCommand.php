<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Str;
use SpaceCode\Maia\Models\Settings;
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
        $data = [
            'attention' => 'Answer the question or the installation process will be interrupted.',
            'interrupted' => 'The installation process interrupted.',
            'user_interrupted' => 'User creation process interrupted. Continue installation...',
        ];
        if($this->novaFolder()) {
            $this->info('CMS Maia installing...');
            $this->info('Publishing...');
            $this->call('maia:publish');
            $this->info('CMS Maia successfully installed');

            $this->appName($data);
            $this->appURL($data);
            $this->horizon();
            $this->appPath($data);
            $this->createAdmin($data);
        }
    }

    public function novaFolder()
    {
        if (\File::exists(base_path('nova'))) {
            $this->error('Missing installation folder for Laravel Nova ("./nova").');
            return false;
        }
        return true;
    }

    public function createAdmin($data)
    {
        if ($this->confirm('You need to create an admin user. Do you wish to continue?')) {
            $continue = false;
            $email = '';
            $password = '';
            $name = $this->ask('What is your user login? (ex: admin)');
            if($name === '' || empty($name)) {
                $this->info('User login is required.');
                $name = $this->ask('What is your user login?');
                if($name === '' || empty($name)) {
                    $this->info($data['user_interrupted']);
                    $continue = true;
                }
            }
            if(!$continue) {
                $email = $this->ask('What is your user email?');
                if($email === '' || empty($email)) {
                    $this->info('User email is required.');
                    $email = $this->ask('What is your user email?');
                    if($email === '' || empty($email)) {
                        $this->info($data['user_interrupted']);
                        $continue = true;
                    }
                }
            }
            if(!$continue) {
                $password = $this->secret('What is the password?');
                $repeat = $this->secret('Repeat password');
                if($password === '' || empty($password) || $repeat === '' || empty($repeat) || $repeat !== $password) {
                    $this->info('Let\'s try again!');
                    $password = $this->secret('What is the password?');
                    $repeat = $this->secret('Repeat password');
                    if($password === '' || empty($password) || $repeat === '' || empty($repeat) || $repeat !== $password) {
                        $this->info($data['user_interrupted']);
                        $continue = true;
                    }
                }
            }
            if($continue) {
                User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'created_at' => new \DateTime(), 'updated_at' => new \DateTime()]);
                $this->info('Admin user successfully created.');
                $this->info('You may login after several seconds.');
            }
            $this->info('Enjoy using CMS Maia.');
            $this->info('Have a nice day )))');
            $this->info('Best regards, SpaceCode Team');
        }
    }

    public function appPath($data)
    {
        $appPath = $this->ask('What is your application full path? (ex: /var/www/example/data/www/you_application/)');
        if($appPath === '' || empty($appPath)) {
            $this->info('Application path is required option.');
            $appPath = $this->ask('What is your application full path? ' . $data['attention']);
            if($appPath === '' || empty($appPath)) {
                $this->error($data['interrupted']);
            }
            $this->putEnv('COMPOSER_HOME', $appPath);
            $this->putEnv('HOME', $appPath);
        }
    }

    public function appName($data)
    {
        $appName = $this->ask('What is your application name?');
        if($appName === '' || empty($appName)) {
            $this->info('Application name is required option.');
            $appName = $this->ask('What is your application name? ' . $data['attention']);
            if($appName === '' || empty($appName) || $appName === 'Laravel') {
                $this->error($data['interrupted']);
            }
            $this->putEnv('APP_NAME', $appName);
            $this->putSetting('system_name', $appName);
        } elseif ($appName === 'Laravel') {
            $this->info('Application name cannot be `Laravel`. Name the option differently.');
            $appName = $this->ask('What is your application name? ' . $data['attention']);
            if($appName === '' || empty($appName) || $appName === 'Laravel') {
                $this->error($data['interrupted']);
            }
            $this->putEnv('APP_NAME', $appName);
            $this->putSetting('system_name', $appName);
        }
    }

    public function appURL($data)
    {
        $appURL = $this->ask('What is your application URL?');
        if($appURL === '' || empty($appURL)) {
            $this->info('Application url is required option.');
            $appURL = $this->ask('What is your application URL? ' . $data['attention']);
            if($appURL === '' || empty($appURL) || $appURL === 'http://localhost') {
                $this->error($data['interrupted']);
            }
            $this->putEnv('APP_URL', $appURL);
            $this->putSetting('site_url', $appURL);
        } elseif ($appURL === 'http://localhost') {
            $this->info('Application URL cannot be `http://localhost`. Name the option differently.');
            $appURL = $this->ask('What is your application URL? ' . $data['attention']);
            if($appURL === '' || empty($appURL) || $appURL === 'Laravel') {
                $this->error($data['interrupted']);
            }
            $this->putEnv('APP_URL', $appURL);
            $this->putSetting('site_url', $appURL);
        }
    }

    public function horizon()
    {
        $this->putEnv('HORIZON_PREFIX', str_replace('.', '-', class_basename(setting('site_url'))) . '-horizon:');
    }

    public function putEnv($key, $value)
    {
        $env = file_get_contents(base_path() . '/.env');
        if(!Str::contains($env, $key)) {
            if(Str::contains($value, ' ') || Str::contains($value, '{') && Str::contains($value, '}') && Str::contains($value, '$')) {
                setEnv($key . '="' . $value . '"');
            } else {
                setEnv($key . '=' . $value);
            }
        } else {
            changeEnv($key, $value);
        }
    }

    public function putSetting($key, $value)
    {
        $set = Settings::where('key', $key)->first();
        if(isset($set)) {
            $set->update(['value' => $value]);
        } else {
            Settings::create(['key' => $key, 'value' => $value]);
        }
    }
}