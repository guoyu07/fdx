# fdx

基于 swoole 扩展和 `fastd/swoole` 组件的 PHP 服务开发包，提供高性能底层通讯服务，数据传输封装，异步客户端，异步服务器等许多高级特性。

### ＃composer

```json
composer create-project fastd/fdx
```

### ＃使用

```
php bin/server fdx <action {start|reload|stop|watch|status|dump}> [--host|-h IP地址] [--port|-p 端口] [--daemon|-d 守护进程] [--conf|-c 配置文件] [--dir]
```

程序会自动读取 ./src 目录 (如果你在启动的时候没有指定目录), 对类进行自动加载, 添加到服务容器中。

容器使用 `fastd/container` 进行管理, 类支持依赖注入。

命名规则: 系统会加载类的所有方法到系统中(常驻内存), 方法名通过 `php bin/server fdx dump` 进行查看。

### Todo

* Http 协议支持
* monitor 管理支持

## License MIT
