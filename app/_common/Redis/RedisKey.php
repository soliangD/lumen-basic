<?php

namespace Common\Redis;

class RedisKey
{
    /***************************************** ☟ admin **************************************************************/

    /** @var string admin前缀 */
    const PREFIX_ADMIN = 'admin:';

    /** @var string example */
    const ADMIN_EXAMPLE = self::PREFIX_ADMIN . 'example:';

    /***************************************** 分割线 ☝ admin ☟ api **************************************************/

    /** @var string api前缀 */
    const PREFIX_API = 'api:';

    /** @var string 用户注册 */
    const API_USER_REGISTER = self::PREFIX_API . 'user:register:';

    /***************************************** 分割线 ☝ api ☟ common **************************************************/

    const PREFIX_COMMON = 'common:';

    /**---------- lock -----------*/
    /** @var string 锁 */
    const API_LOCK = self::PREFIX_COMMON . 'lock:';
}
