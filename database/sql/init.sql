SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`
(
  `id`         bigint(20) UNSIGNED                                       NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
  `queue`      text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
  `payload`    longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at`  timestamp(0)                                              NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`
(
  `id`           bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
  `queue`        varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
  `attempts`     tinyint(3) UNSIGNED                                           NOT NULL,
  `reserved_at`  int(10) UNSIGNED                                              NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED                                              NOT NULL,
  `created_at`   int(10) UNSIGNED                                              NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index` (`queue`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`
(
  `id`        int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch`     int(11)                                                       NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for upload
-- ----------------------------
DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload`
(
  `id`         bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id`    bigint(20) UNSIGNED                                           NOT NULL DEFAULT 0 COMMENT '用户id',
  `root_type`  tinyint(4)                                                    NOT NULL DEFAULT 1 COMMENT '存储端类型 1:前台 2:管理后台',
  `source_id`  bigint(20) UNSIGNED                                           NOT NULL DEFAULT 0 COMMENT '来源id(关联id)',
  `type`       tinyint(4)                                                    NOT NULL DEFAULT 1 COMMENT '类型 1:默认 ',
  `storage`    varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT '' COMMENT '文件存储位置 local:本地  oss:oss',
  `filename`   varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `path`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `ext_info`   varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '额外信息',
  `status`     tinyint(4)                                                    NOT NULL COMMENT '状态 1:正常  -1:已删除  -2:文件已清理  ',
  `created_at` datetime(0)                                                   NOT NULL COMMENT '创建时间',
  `updated_at` datetime(0)                                                   NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `upload_user_id_index` (`user_id`) USING BTREE,
  INDEX `upload_source_id_index` (`source_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT = '文件上传表'
  ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`
(
  `id`            bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username`      varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT '' COMMENT '用户名',
  `email`         varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL COMMENT 'Email',
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码hash',
  `status`        tinyint(4)                                                    NOT NULL DEFAULT 2 COMMENT '状态 -1:禁用 1:正常 2:待激活 -2:已删除',
  `created_at`    datetime(0)                                                   NOT NULL COMMENT '创建时间',
  `updated_at`    datetime(0)                                                   NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_email_index` (`email`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT = '用户表'
  ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user_ext
-- ----------------------------
DROP TABLE IF EXISTS `user_ext`;
CREATE TABLE `user_ext`
(
  `id`         bigint(20) UNSIGNED                                          NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id`    bigint(20) UNSIGNED                                          NOT NULL COMMENT 'user_id',
  `key`        varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'key',
  `value`      text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci        NOT NULL COMMENT 'value',
  `created_at` datetime(0)                                                  NOT NULL COMMENT '创建时间',
  `updated_at` datetime(0)                                                  NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `user_id_key_index` (`user_id`, `key`) USING BTREE COMMENT '用户id & key 唯一索引'
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT = '用户扩展表'
  ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for send_log
-- ----------------------------
DROP TABLE IF EXISTS `send_log`;
CREATE TABLE `send_log`
(
  `id`         bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT COMMENT 'id',
  `type`       tinyint(4)                                                    NOT NULL COMMENT '类型 1:邮件 2:短信 ',
  `channel`    varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL COMMENT '发送渠道(邮件存储发送人，其他存渠道...)',
  `receiver`   varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL COMMENT '接收者',
  `desc`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '其他说明',
  `status`     tinyint(4)                                                    NOT NULL COMMENT '状态 1:创建 2:发送中 3:发送成功 4:发送失败 5:获取结果超时',
  `send_time`  datetime(0)                                                   NULL DEFAULT NULL COMMENT '发送时间',
  `call_time`  datetime(0)                                                   NULL DEFAULT NULL COMMENT '回调时间(邮件存储成功时间)',
  `created_at` datetime(0)                                                   NOT NULL COMMENT '创建时间',
  `updated_at` datetime(0)                                                   NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `send_log_receiver_index` (`receiver`) USING BTREE COMMENT '接收者列索引'
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_general_ci COMMENT = '推送记录表'
  ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
