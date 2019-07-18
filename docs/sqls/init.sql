-- ----------------------------
-- Table structure for upload
-- ----------------------------
DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload`
(
  `id`         bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id`    bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `root_type`  tinyint(4)          NOT NULL DEFAULT '1' COMMENT '用户来源类型 1:前台 2:管理后台',
  `source_id`  bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '来源id(关联id)',
  `type`       tinyint(4)          NOT NULL DEFAULT '1' COMMENT '类型 1:默认 ',
  `storage`    varchar(10)         NOT NULL DEFAULT '' COMMENT '文件存储位置 local:本地  oss:oss',
  `filename`   varchar(255)        NOT NULL DEFAULT '' COMMENT '文件名',
  `path`       varchar(255)        NOT NULL DEFAULT '' COMMENT '文件路径',
  `ext_info`   varchar(255)        NOT NULL DEFAULT '' COMMENT '额外信息',
  `status`     tinyint(4)          NOT NULL COMMENT '状态 1:正常  -1:已删除  -2:文件已清理  ',
  `created_at` datetime            NOT NULL COMMENT '创建时间',
  `updated_at` datetime            NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `upload_user_id_index` (`user_id`),
  KEY `upload_source_id_index` (`source_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='文件上传表';
