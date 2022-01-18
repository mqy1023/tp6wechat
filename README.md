ThinkPHP 6.0 + 小程序 

> ---------
>使用项目
### 《一》数据库文件
`tp6wechat_db_2022-01-18.sql`

### 《二》thinkphp后端代码
##### 一、下载代码后执行`composer install`

##### 二、修改`.env`中的环境变量

##### 三、修改`config/wechat.php`中的`app_id`和`app_secret` 

### 《三》小程序代码
`wxdemo` 目录下


> ---------
>代码从零开始全纪录
### 《一》、创建tp项目

##### 一、安装thinkphp6项目

* 1、初始化thinkphp项目代码
~~~
composer create-project topthink/think tp6wechat
~~~
* 2、安装多应用模式

~~~
composer require topthink/think-multi-app
~~~

### 《二》、自定义cores核心目录

##### 一、使用PSR-4配合composer autoload 自动加载文件夹，自定义cores核心目录
* 1、根目录下创建cores核心目录，用于放程序基本核心代码
* 2、`composer.json`添加`autoload`自动加载`cores`模块
```php
"autoload": {
    "psr-4": {
        "cores\\": "cores"
    }
},
```
* 3、用composer加载包

执行`composer install` 命令

##### 二、cores核心目录其他部分

* 1、错误信息Trait类

use ErrorTrait; // 既可以继承ErrorTrait类的属性和方法
* 2、自定义异常类的基类 BaseException
* 3、应用异常处理类 ExceptionHandle
* 4、中间件：应用日志 AppLog
* 5、控制器基础类 BaseController
* 6、模型基类 BaseModel