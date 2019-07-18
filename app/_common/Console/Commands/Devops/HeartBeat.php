<?php

namespace Common\Console\Commands\Devops;

use Common\Helpers\Email\EmailHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HeartBeat extends command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'devops:heart-beat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '心跳检测';

    public function handle()
    {
        /** 数据库 & Redis 连接 */
        $result = $this->heartBeat();

        dd($result);
    }

    protected function heartBeat()
    {
        $data = [
            'mysql' => [
                config('database.connections.mysql.database') => [
                    'host' => config('database.connections.mysql.host'),
                    'port' => config('database.connections.mysql.port'),
                    'username' => config('database.connections.mysql.username'),
                    'connection_status' => $this->tryCatch(function () {
                        return DB::select('SELECT 1');
                    }),
                ],
            ],
            'redis' => [
                'host' => config('database.redis.default.host'),
                'port' => config('database.redis.default.port'),
                'database' => config('database.redis.default.database'),
                'connection_status' => $this->tryCatch(function () {
                    return Redis::select(config('database.redis.default.database'));
                }),
            ],
        ];

        EmailHelper::sendInner($data, 'queue test');

        EmailHelper::mailer(var_export($data, true), 'heart beat email~');

        return $data;
    }

    /**
     * @param $fun
     * @return int
     */
    protected function tryCatch($fun)
    {
        try {
            return $fun() ? 1 : 0;
        } catch (\Exception $e) {
            //EmailHelper::sendException($e, '心跳检测');
            return 0;
        }
    }
}
