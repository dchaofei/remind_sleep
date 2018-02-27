我就默认你们安装 PHP 了哈 : )

1. 执行 `composer install`
2. 配置 `mail_config.php` 中的邮件服务器、收件人 
3. crontab 定时任务(晚上 10:00 10:15 10:30 10:40 提醒)
    ```bash
    0,15,30,40 22 * * * php /data/remind_sleep/mail.php >> /var/tmp/mail.log 2>&1
    ```
    