# flea-market（フリーマーケットアプリ）
## 環境構築
### Dockerビルド
1.`git clone git@github.com:Mika-F620/flea-market.git`<br>
2.DockerDesktopアプリを立ち上げる<br>
3.`docker-compose up -d --build`

### Laravel環境構築
1.`docker-compose exec php bash`<br>
2.`composer install`<br>
3.「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4..envに以下の環境変数を追加<br>
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5.アプリケーションキーの作成<br>
`php artisan key:generate`<br>
6.マイグレーションの実行<br>
`php artisan migrate`<br>
7.シーディングの実行<br>
`php artisan db:seed`

### MailHog環境構築
1.`docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog`<br>
2..envに以下の環境変数を追加<br>
```
MAIL_MAILER=smtp
MAIL_HOST=host.docker.internal
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 使用技術(実行環境)
PHP8.3.13<br>
Laravel8.83.28<br>
MySQL10.3.39

## ER図
![er](https://github.com/user-attachments/assets/46a053f0-fb71-4d5f-be98-67f279ae35d2)

## URL
開発環境：http://localhost:8085/<br>
phpMyAdmin:http://localhost:8080/<br>
MailHog:http://localhost:8025/

## 出品者のログイン情報
ユーザー名：出品者ユーザー1  
メールアドレス：user1@example.com  
パスワード：password

ユーザー名：出品者ユーザー2  
メールアドレス：user2@example.com  
パスワード：password
