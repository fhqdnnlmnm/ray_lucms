## lucms

## 项目概述

- 产品名称：lucms
- demo: http://lucms.codehaoshi.com/dashboard  账号：dev@lucms.com  密码： 123456

lucms 是一个基于 `laravel5.5*` 与 `iviewjs` 开发的一套后台管理系统。

<p align="center">
  <br>
  <b>Ucer-admin</b>
  <br>
  <a href="https://www.codehaoshi.com">
    <img src="https://gitee.com/uploads/images/2018/0628/193946_711f853f_923445.png "lucms-1.png" width=800>
  </a>
  <br>
  <a href="https://www.codehaoshi.com">
    <img src="https://gitee.com/uploads/images/2018/0628/194014_117a03f4_923445.png "lucms-2.png" width=800>
  </a>
  <a href="https://www.codehaoshi.com">
    <img src="https://gitee.com/uploads/images/2018/0628/194036_ec75ea27_923445.png "lucms3.png" width=800>
  </a>
  <a href="https://www.codehaoshi.com">
    <img src="https://gitee.com/uploads/images/2018/0628/194105_bb523b94_923445.png "lucms4.png" width=800>
  </a>
</p>

## 功能如下

- 用户认证 —— 登录、退出
- 用户认证 —— 多表登录
- 用户管理 —— 头像上传、资料修改
- 权限系统 —— 多角色、多权限管理
- 附件管理 —— 服务器文件管理
- 新闻系统 —— 基础新闻管理
- 系统安全 —— 日志记录、ip 过滤

## 开发环境部署/安装

本项目代码使用 PHP 框架 Laravel 5.5 开发，本地开发环境使用 Laravel Homestead。

下文将在假定读者已经安装好了 Homestead 的情况下进行说明。如果您还未安装 Homestead，可以参照 Homestead 安装与设置 进行安装配置。

### 基础安装

- 克隆源代码

克隆 lucms 源代码到本地：

> git@gitee.com:zhjaa/lucms.git

- php 配置修改

1). 配置本地环境，根目录指向 `public`

2). 安装 composer
```html
composer install
```

2). 生成配置文件
```html
cp .env.example .env
你可以根据情况修改 .env 文件里的内容，如数据库连接、缓存、邮件设置等：
```

2). 目录访问权限配置

```text
  $ chmod -R 777 storage
```


3). 配置 .env  ，修改数据库信息 . ....
```sh
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:IKlBaIonliiolP7yK0QWP8Ixwgc1Z5R2ylxEA6CD3nA=
APP_DEBUG=true
APP_LOG_LEVEL=debug
APP_URL=http://lucms.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lucms
DB_USERNAME=root
DB_PASSWORD=route
.

.

.
QUEUE_DRIVER=sync 「同步模式，不使用任何队列」 => redis

.

.

.
```

4). 生成数据表及生成测试数据

```sh
$ art migrate
$ art db:seed
```

5). 安装 passport 客户端, vue api 请求 token 认证要用到
```sh
 $ art passport:install
 
 # 以下内容复制到 .env 中
 Client ID: 2
 Client Secret: qtbbnoYSKM1QkAfbcs614iCiWmMvBWNdRloJNbDi

```

6). 配置 .env  ，修改数据库信息 . ....
```sh
.

.

.
OAUTH_GRANT_TYPE=password
OAUTH_CLIENT_ID=2
OAUTH_CLIENT_SECRET=p7XaeU3D9RASxQ18eiF5CT1uL9xUJRYjT6O8BJFt
OAUTH_SCOPE=*

.

.

.
```


7). 生成密钥
```html
art key:generate
```

8). 设定图片上传软链接 `storage/app/public/*` 到 `public/storage/images`
```
$ cd public
$ mkdir storage
$ ln -s /srv/wwwroot/homestead-code/lucms/storage/app/public/* ./storage/
```


- 修改 js 配置

1). 全局修改基本域名 http://lucms.test => https://xxxxx

`lucms/lu/src/libs/util.js`
```js
const ajaxUrl = env === 'development'
    ? 'http://lucms.test/api'
    : env === 'production'
        ? 'http://lucms.test/api'
        : 'http://lucms.test/api';
```

`lucms/lu/src/main.js`
```js
const app_url = '//lucms.test/api';
```

`lucms/lu/build/webpack.prod.config.js`
```js
.
.
.
    output: {
        //publicPath: 'http://lucms.test/lu/dist/',  // 修改 https://iv...admin 这部分为你的服务器域名
        publicPath: 'http://lucms.codehaoshi.com/lu/dist/',  // 修改 https://iv...admin 这部分为你的服务器域名
        filename: '[name].[hash].js',
        chunkFilename: '[name].[hash].chunk.js'
    },
. 
.
.
```

### vuejs 安装与运行

1). 开发环境
```
$ cd lu
$ cnpm install
$ npm run dev
```

2). 开发环境跨域配置
chrome 安装插件 Access-Control-Allow_Credentials

服务器跨域，Apache请自行搜索处理或在 php 文件中处理。此处不建议在 php 中处理:

nginx 配置如下：
```sh
.
.
.
  add_header Access-Control-Allow-Origin http://localhost:8080;
  add_header Access-Control-Allow-Headers *;
  add_header Access-Control-Allow-Methods GET,POST,OPTIONS,PATCH,PUT;
  .
  .
  .

```

> $ service nginx restart

3). 生产环境
```
$ cd lu 
$ npm run build
$ mv ../public/dashboard.blade.php ../resources/views
```

## 扩展包使用情况

| 扩展包	| 一句话描述	| 本项目应用场景|
| --- | --- | --- |
| [laravel/passport](https://github.com/laravel/passport)     | jwt 用户认证包          | api 登录认证|
| [Intervention/image](https://github.com/Intervention/image)     | 图片处理包     | 图片上传裁剪|
| [laravel-permission:~2.7](https://github.com/spatie/laravel-permission)     | 权限管理包     | 权限管理|
| [mews/purifier](https://github.com/mewebstudio/Purifier)     | xss过滤     | 富文本编辑器|
| [overtrue/pinyin](https://github.com/overtrue/pinyin)     | 基于 CC-CEDICT 词典的中文转拼音工具     | 文章 seo 友好的 url|
| [nrk/predis](https://github.com/nrk/predis)     | redis 队列驱动器     | 队列管理 |
| [laravel/horizon](https://laravel-china.org/docs/laravel/5.5/horizon/1345)     | 队列监控     | 队列监控 |
| [rap2hpoutre/laravel-log-viewer](https://github.com/rap2hpoutre/laravel-log-viewer)     | laravel 日志查看     | 查看日志 |


## 队列

| Jobs | 一句话描述|
|--- | --- |
| TranslateSlug | 翻译文章 title |


