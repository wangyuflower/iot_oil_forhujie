小直播相关配置请参考:https://www.qcloud.com/document/product/454/7999

1.开通直播码服务（包括直播和点播服务），并复制开通页面的直播码相关key，填入live_demo_service/conf/OutDefine.php中：
define('APP_BIZID',0);  //请替换为您申请的直播服务的bizid
define('APP_ID’,0); //请替换为您申请的直播服务的appid
define('PUSH_URL_KEY','');  //请替换为您申请的直播服务的推流key
define('CALL_BACK_KEY','');  //回调api key

2.开通对象和存储服务（COS），新建一个bucket以及一对secrectid和secrectkey，并填入live_demo_service/conf/OutDefine.php中：
define('COSKEY_BUCKET',''); //请替换为对象和存储服务（COS）中您所新建的bucket
define('COSKEY_SECRECTKEY',''); //请替换为对象和存储服务（COS）中您所新建的secrectkey
define('COSKEY_APPID',0); //请替换为对象和存储服务（COS）的appid
define('COSKEY_SECRECTID',''); //请替换为对象和存储服务（COS）中您所新建的secrectid（和secrectkey配对）

3.准备一台云服务器（推荐选择腾讯云的“云服务器”服务，并选择服务市场里面的nginx+php+mysql的镜像）
如果您的服务器上已经有mysql,php,nginx，请跳过相应的安装步骤
a、安装mysql5.5以上版本，启动mysql。按照文档createDB.sh 创建db
b、安装php，修改php配置文件php-fpm.conf中的监听端口（例如demo用的端口是9000，修改 listen = 127.0.0.1:9000, 并运行命令重启服务:service php-fpm restart）
c、安装nginx ,参照nginx.conf和live_demo.nginx 修改配置，重新reload nginx(运行命令nginx -s reload)
d、拷贝demo代码到/data目录（也可以是其他目录，需要相应修改live_demo.nginx的目录位置），并修改live_demo_service/conf/cdn.route.ini中的数据库相关配置（根据createDB.sh中创建的数据库名、用户名及密码等）
e、在/etc/crontab文件上增加一行配置（此配置的作用是增加一个定时任务，每分钟轮询直播列表中的在线状态，用于清理僵尸频道）：
* * * * *  php /data/live_demo_service/callback/Check_online_status.php
