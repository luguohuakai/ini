# ini

非常简单的ini配置文件读取/写入方法<br>
simple ini get and set.<br>

# 使用方法

>
>
> 只暴露4个方法和1个属性 `load()` `get()` `set()` `setAll()` `all`

## 初始化

* new
* load

```php
// new 或者 load
$ini = new Ini('./config.ini')
// 或者这样写
$ini = Ini::load('./config.ini')
```

## 获取值

* get

```php
// 基础用法
$ini->get('name')
// 使用默认值
$ini->get('name', 'Rose')
// 获取深层次下的值
$ini->get('a.b')
$ini->get('a.b.c')
$ini->get('a.b.c.d')
```

### 获取全部值

* all

```php
$all = $ini->all
```

## 设置值(即时写入文件)

* set
* setAll

```php
// 设置一个值
$ini->set('age', 18)
// 设置多个值
$ini->setAll(['name' => 'Tim', 'age' => 17])
```