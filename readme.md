# Lumen Basic

这是一个基于 [Lumen](https://github.com/laravel/lumen) 的模板仓库

This is an [Lumen](https://github.com/laravel/lumen) based template repository

### 集成

- redis
- mail
- [laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)
- [laravel/helpers](https://github.com/laravel/helpers)：laravel5.8移除了部分helper函数，提供了helpers包
- jwt-auth：对[jwt-auth 1.0.0-rc.4](https://github.com/tymondesigns/jwt-auth/tree/1.0.0-rc.4)进行了集成实现并优化
- lumen-yaml-swagger：使用了[yaml-swagger](https://github.com/soliangD/lumen-yaml-swagger)来进行文档编写，支持yaml格式

### 其他

- 目录结构优化：对目录结构进行优化，进行了 `api` 和 `admin` 的分离
- 基础封装：封装了工具函数、BaseController、Services、Validate、Redis基类、Response、StaticModel
- 测试用例优化：对部分测试方法进行封装优化
- 其他：cors处理
