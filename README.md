# FDX

基于 swoole 扩展和 `fastd/swoole` 组件的 PHP 服务开发包。

**仅支持 Linux 系统**

### Composer

```
composer require "fastd/fdx:1.0.x-dev"
```

##### Requirements

* Swoole > 1.8
* Redis

### Usage

##### 开启服务发现

```php
php examples/discovery.php
```

##### 开启服务

```php
php examples/server.php
```

##### 调用客户端

```php
php examples/client.php
```

## License MIT
