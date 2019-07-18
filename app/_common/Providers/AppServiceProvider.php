<?php

namespace Common\Providers;

use Carbon\Carbon;
use Common\Validators\Validation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * boot
     */
    public function boot()
    {
        // 处理 Specified key was too long error
        Schema::defaultStringLength(191);

        // 设置 Carbon 默认时区
        Carbon::setLocale('zh');

        // 注册自定义验证
        Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new Validation($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        if (config('config.app_sql_log')) {
            // 此处注册下events容器，不然 DB::listen 不能正常监听
            $this->app['events'];
            DB::listen(function (QueryExecuted $query) {
                $sqlWithPlaceholders = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
                $bindings = $query->connection->prepareBindings($query->bindings);
                $pdo = $query->connection->getPdo();
                Log::info(vsprintf($sqlWithPlaceholders, array_map([$pdo, 'quote'], $bindings)));
            });
        }

        // 邮箱
        $this->app->singleton('mailer', function ($app) {
            return $app->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
        });

        $this->app->singleton(
            \Illuminate\Contracts\Filesystem\Factory::class,
            function ($app) {
                return new \Illuminate\Filesystem\FilesystemManager($app);
            }
        );
    }
}
