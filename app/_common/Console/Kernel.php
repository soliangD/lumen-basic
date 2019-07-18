<?php

namespace Common\Console;

use Common\Console\Commands\Devops\HeartBeat;
use Common\Console\Commands\Devops\KeyGenerateCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        KeyGenerateCommand::class,
        HeartBeat::class,
    ];

    /**
     * Define the application's command schedule.
     * tips:
     * 使用withoutOverlapping缓存默认一天(执行完删除),设置过期时间->withoutOverlapping(3) 单位minute,
     * 如果任务执行失败没有删除掉缓存,过期后缓存也会自动失效,不然会导致任务一直不执行
     * lumen schedule时间设置尽量用以下方式
     * ->everyMinute(); 每分钟运行一次任务
     * ->everyFiveMinutes(); 每五分钟运行一次任务
     * ->everyTenMinutes(); 每十分钟运行一次任务
     * ->everyThirtyMinutes(); 每三十分钟运行一次任务
     * ->hourly(); 每小时运行一次任务
     * ->daily(); 每天凌晨零点运行任务
     * ->dailyAt('13:00'); 每天13:00运行任务
     * ->twiceDaily(1, 13); 每天1:00 & 13:00运行任务
     * ->weekly(); 每周运行一次任务
     * ->monthly(); 每月运行一次任务
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /** test */
        $schedule->command(HeartBeat::class)->everyThirtyMinutes()->withoutOverlapping(5)->runInBackground()->onOneServer();
    }
}
