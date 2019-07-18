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
- 基础封装：封装了工具函数、Helpers、BaseController、Services、Validate、Redis基类、StaticModel
    - 工具函数：app/_common/Utils：对常用函数进行简单封装
    - Helpers：app/_common/Helpers：封装了一些较复杂的工具函数。区别与工具：`util`一般定义为static，单个方法实现单个功能，与其他方法没有关联。
    `helper`一般是对某个功能进行的一系列封装，使用对象的形式(如：EmailHelper的封装)。
    - BaseController：对`Controller`的响应格式、参数获取等进行封装
    - Services：在`controller`和`model`之间提供一层中间层，用于处理业务逻辑。common/services用于存放公共的逻辑，`api`和`admin`下各自新建`services`继承`common`
    - Validate：对表单验证进行封装。在`api`和`admin`目录下提供`Rules`模块用来处理表单验证逻辑
    - Redis基类：app/_common/Redis：对`redis`的`key`行统一管理，并提供有关业务的redis逻辑处理。注：`helper` 下的 `LockHelper` 提供了redis公共锁的实现
    - StaticModel：封装了常用`model`处理逻辑，提供`StaticModel`这个`trait`进行`model`功能扩展
- 测试用例优化：对部分测试方法进行封装优化
- 其他：cors处理
