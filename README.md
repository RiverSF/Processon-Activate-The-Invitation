# ProcessOn-Activate-the-invitation
批量激活通过 ProcessOn 邀请链接注册邮箱账户

# 一些说明
> 背景：因为 PrecessOn 注册是通过邮件并且需要进入邮箱进行激活，因此我们需要读取到 ProcessOn 发送的激活邮件并访问激活链接

> 重点：因为要读取邮件，所以需要开启 Imap 服务，进入 php.ini 加载 php_imap.dll 扩展配置项 （Linux 环境中如果没有需要编译安装此扩展）

> 过程：由核心文件可以看出，我是在 Laravel 框架中来进行核心文件整合的，当然还有 composer 工具，最重要的是 引入了两个很重要的扩展包：laravel-imap 和 guzzle

> 结果：为了调试方便，解决整合过程产生的坑，因此使用浏览器作为访问客户端，当然如果你感兴趣，可以改用 CLI 效果更好