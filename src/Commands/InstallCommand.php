<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
        $this->storageBuild();
        $this->moveStubs();

        $data = [
            'attention' => 'Answer the question or the installation process will be interrupted.',
            'interrupted' => 'The installation process interrupted.',
            'user_interrupted' => 'User creation process interrupted. Continue installation...'
        ];
        $this->novaFolder();
        $this->info('CMS Maia installing...');
        $this->info('Publishing...');
        $this->call('maia:publish');
        $this->info('CMS Maia successfully installed');
        $this->appName($data);
        $appUrl = $this->appURL($data);
        $this->horizon($appUrl);
        $this->appPath($data);
        $this->createAdmin($data);
    }

    public function novaFolder()
    {
        if (!\File::exists(base_path('nova'))) {
            $this->error('Missing installation folder for Laravel Nova ("./nova").');
            return false;
        }
        return true;
    }

    public function createAdmin($data)
    {
        if ($this->confirm('You need to create an admin user. Do you wish to continue?')) {
            $name = $this->ask('What is your user login? (ex: admin)');
            if($name === '' || empty($name)) {
                $this->info('User login is required.');
                $name = $this->ask('What is your user login?');
                if($name === '' || empty($name)) {
                    $this->error($data['user_interrupted']);
                    return false;
                }
            }

            $email = $this->ask('What is your user email?');
            $user = User::where('email', $email)->exists();
            if($email === '' || empty($email)) {
                $this->info('User email is required.');
                $email = $this->ask('What is your user email?');
                if($email === '' || empty($email)) {
                    $this->error($data['user_interrupted']);
                    return false;
                }
            } elseif ($user) {
                $this->info('User with such email is already exists.');
                $email = $this->ask('What is your user email?');
                if($user) {
                    $this->error($data['user_interrupted']);
                    return false;
                }
            }

            $password = $this->secret('What is the password?');
            $repeat = $this->secret('Repeat password');
            if($password === '' || empty($password) || $repeat === '' || empty($repeat) || $repeat !== $password) {
                $this->info('Let\'s try again!');
                $password = $this->secret('What is the password?');
                $repeat = $this->secret('Repeat password');
                if($password === '' || empty($password) || $repeat === '' || empty($repeat) || $repeat !== $password) {
                    $this->error($data['user_interrupted']);
                    return false;
                }
            }

            User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password), 'created_at' => new \DateTime(), 'updated_at' => new \DateTime()]);
            $this->info('Admin user successfully created.');
            $this->info('You may login after several seconds.');

            return true;
        }
        $this->info('Enjoy using CMS Maia. Have a nice day )))');
        $this->info('Best regards, SpaceCode Team');
    }

    public function appPath($data)
    {
        $appPath = $this->ask('What is your application full path? (ex: /var/www/example/data/www/you_application/)');
        if($appPath === '' || empty($appPath)) {
            $this->info('Application path is required option.');
            $appPath = $this->ask('What is your application full path? ' . $data['attention']);
            if($appPath === '' || empty($appPath)) {
                $this->error($data['interrupted']);
                return false;
            }
        }
        $this->putEnv('COMPOSER_HOME', $appPath);
        $this->putEnv('HOME', $appPath);
        return true;
    }

    public function appName($data)
    {
        $appName = $this->ask('What is your application name?');
        if($appName === '' || empty($appName)) {
            $this->info('Application name is required option.');
            $appName = $this->ask('What is your application name? ' . $data['attention']);
            if($appName === '' || empty($appName) || $appName === 'Laravel') {
                $this->error($data['interrupted']);
                return false;
            }
        } elseif ($appName === 'Laravel') {
            $this->info('Application name cannot be `Laravel`. Name the option differently.');
            $appName = $this->ask('What is your application name? ' . $data['attention']);
            if($appName === '' || empty($appName) || $appName === 'Laravel') {
                $this->error($data['interrupted']);
                return false;
            }
        }
        $this->putEnv('APP_NAME', $appName);
        $this->putSetting('system_name', $appName);
        return true;
    }

    public function appURL($data)
    {
        $appURL = $this->ask('What is your application URL?');
        if($appURL === '' || empty($appURL)) {
            $this->info('Application url is required option.');
            $appURL = $this->ask('What is your application URL? ' . $data['attention']);
            if($appURL === '' || empty($appURL) || $appURL === 'http://localhost') {
                $this->error($data['interrupted']);
                return false;
            }
        } elseif ($appURL === 'http://localhost') {
            $this->info('Application URL cannot be `http://localhost`. Name the option differently.');
            $appURL = $this->ask('What is your application URL? ' . $data['attention']);
            if($appURL === '' || empty($appURL) || $appURL === 'Laravel') {
                $this->error($data['interrupted']);
                return false;
            }
        }
        $this->putEnv('APP_URL', $appURL);
        $this->putSetting('site_url', $appURL);
        return $appURL;
    }

    public function horizon($appUrl)
    {
        $this->putEnv('HORIZON_PREFIX', str_replace('.', '-', class_basename($appUrl)) . '-horizon:');
    }

    public function putEnv($key, $value)
    {
        $env = file_get_contents(base_path() . '/.env');
        if(!Str::contains($env, $key)) {
            if(Str::contains($value, ' ') || Str::contains($value, '{') && Str::contains($value, '}') && Str::contains($value, '$'))
                setEnv($key . '="' . $value . '"');
            else
                setEnv($key . '=' . $value);
        } else {
            changeEnv($key, $value);
        }
    }

    public function putSetting($key, $value)
    {
        $set = Settings::where('key', $key)->first();
        if(isset($set))
            $set->update(['value' => $value]);
        else
            Settings::create(['key' => $key, 'value' => $value]);
    }

    public function moveStubs()
    {
        $stubPath = __DIR__.'/../../stub';
        $array = [
            $stubPath . '/app/Http/Controllers/MaiaIndexController.php.stub' => app_path('Http/Controllers/MaiaIndexController.php'),
            $stubPath . '/app/Http/Controllers/MaiaRobotsController.php.stub' => app_path('Http/Controllers/MaiaRobotsController.php'),
            $stubPath . '/app/Http/Controllers/MaiaSitemapController.php.stub' => app_path('Http/Controllers/MaiaSitemapController.php'),
            $stubPath . '/app/User.php.stub' => app_path('User.php'),
            $stubPath . '/app/Nova/User.php.stub' => app_path('Nova/User.php'),
            $stubPath . '/config/app.php.stub' => config_path('app.php'),
            $stubPath . '/config/nova.php.stub' => config_path('nova.php'),
            $stubPath . '/resources/views/vendor/nova/partials/footer.blade.php.stub' => resource_path('views/vendor/nova/partials/footer.blade.php'),
            $stubPath . '/resources/views/vendor/nova/partials/logo.blade.php.stub' => resource_path('views/vendor/nova/partials/logo.blade.php'),
            $stubPath . '/resources/views/vendor/nova/partials/meta.blade.php.stub' => resource_path('views/vendor/nova/partials/meta.blade.php'),
            $stubPath . '/resources/views/vendor/nova/partials/user.blade.php.stub' => resource_path('views/vendor/nova/partials/user.blade.php')
        ];
        foreach ($array as $key => $value) {
            (new Filesystem)->copy($key, $value);
        }
    }

    public function storageBuild() {
        if (!\File::exists(public_path('storage')))
            $this->call('storage:link');
        return true;
    }
}