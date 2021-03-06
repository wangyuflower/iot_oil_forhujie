server{
    listen      80;
    server_name $*;
    root /a/domains/${DomainName}/public_html;
    index index.html index.htm index.shtml index.php;

    error_page  404               /404.html;
    #自定义伪静态内容开始
    #
    #自定义伪静态内容结束
    location = /500.html {
        root   /usr/share/nginx/html;
    }

    location ~ \.php$ {
        fastcgi_pass   unix:/dev/shm/php.sock;
        include        fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  \$document_root\$fastcgi_script_name;
        access_log     /a/apps/nginx/logs/${DomainName}.access.log main;
    }

    location ~ /\.ht {
        deny  all;
    }
}
EOF
echo ${DomainName}" vhost create success , website directory : /a/domains/"${DomainName}                                              ainName}"/public_html/"
service nginx reload



MYSQL root:
f4ab662a
FTP default ftpdef :
42b43b07

http://139.199.9.166/proj/iot/
http://139.199.9.166/live_demo_service/interface.php
139.199.9.166/phpmyadmin

nginx -s reload
service nginx reload

139.199.9.166/live_demo
http://139.199.9.166/interface.php
http://139.199.9.166/live_demo/interface.php
http://139.199.9.166/live_demo_service/interface.php








user  nginx;
worker_processes  1;
worker_rlimit_nofile 65535;
events {
    use epoll;
    worker_connections 65535;
}

http {
    include       mime.types;
    default_type  application/octet-stream;
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';
    
    access_log /a/apps/nginx/logs/access.log  main;
    error_log  /a/apps/nginx/logs/error.log warn;
    
    sendfile            on;
    send_timeout        120s;
    tcp_nopush          on;
    tcp_nodelay         on;
    server_tokens       off;
    keepalive_timeout   360s;
    keepalive_requests  1000;
    
    gzip                on;
    gzip_min_length     1k; 
    gzip_buffers        4 16k; 
    output_buffers      1 512k;
    postpone_output     1460;
    gzip_comp_level     4;
    gzip_vary           on;
    
    fastcgi_connect_timeout      300;
    fastcgi_send_timeout         300;
    fastcgi_read_timeout         300;
    fastcgi_buffer_size          512k;
    fastcgi_buffers              8 512k;
    fastcgi_busy_buffers_size    512k;
    fastcgi_temp_file_write_size 512k;
    fastcgi_intercept_errors     on;
    
    server_names_hash_bucket_size 128;
    client_header_buffer_size     32k;
    large_client_header_buffers   4 32k;
    client_max_body_size          8m;
    client_header_timeout         120s;
    client_body_timeout           120s;

    proxy_connect_timeout      60;
    proxy_read_timeout         60;
    proxy_send_timeout         60;
    proxy_buffer_size          64k;
    proxy_buffers              64 64k;
    proxy_busy_buffers_size    128k;
    proxy_temp_file_write_size 128k;
    
    ssi on;
    ssi_silent_errors on;
    ssi_types text/shtml;

    server {
        listen       80 default;
        server_name  "";

        root /a/apps/linuxdef;
        index index.html index.htm index.shtml index.php;
        
        location ~ \.php$ {
            fastcgi_pass   unix:/dev/shm/php.sock;
            include        fastcgi_params;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        }
    }

    include vhosts/*.conf;
}




