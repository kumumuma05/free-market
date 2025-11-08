# Coachtechフリマ

商品の出品と購入を行うことができるフリーマーケットWebアプリケーションです。

## 機能概要

- アプリ概要
  - このアプリはユーザー同士が商品を出品・購入できるフリマアプリです。
  - ユーザー登録した会員は、商品の購入のほか、商品を出品し、商品説明を登録できます。
  - 購入時はユーザーの住所以外の配送先住所に変更することが可能です
  - 会員以外のユーザーは商品一覧と商品詳細を閲覧することはできますが、ほかの機能は使えません。


- 主な機能
  - ユーザー登録・ログイン（メール認証付き）
  - プロフィール編集（名前、アイコン画像選択）
  - 商品出品（タイトル、ブランド、商品カテゴリ、価格、商品説明、画像アップロード、商品コンディション）
  - 商品一覧表示・商品詳細表示（いいね数とコメント数の確認も可能）
  - お気に入り登録/解除
  - コメント登録
  - 商品購入（注文・配送先登録・支払いはコンビニ払いとカード支払いを選択可能）
  - 購入履歴・出品履歴の確認
  
## 環境構築

### Dockerビルド
1. リポジトリをクローン  
コマンドライン上
```bash
git clone git@github.com:kumumuma05/check-test.git
cd check-test
```
2. コンテナ起動  
コマンドライン上
```bash
docker-compose up -d --build
```

### Laravel 環境構築
1. PHPコンテナへ入る  
コマンドライン上
```bash
docker-compose exec php bash
```
2. パッケージのインストール  
PHPコンテナ上
```bash
composer install
```
3. 環境ファイル作成  
PHPコンテナ上
```bash
cp .env.example .env
```
4. アプリキー作成  
PHPコンテナ上
```bash
php artisan key:generate
```
5. マイグレーション実行  
PHPコンテナ上
```bash
php artisan migrate
```
6. 初期データ投入  
PHPコンテナ上
```bash
php artisan db:seed
```

> ※ 使用しているOSによりファイル権限が原因のエラーが発生する場合があります。
>その際は環境に合わせて権限を調整してください。  

## ログイン情報
- 本アプリではレビュー用にseederで登録されたユーザーが3名分存在しています。  
（開発環境での動作確認用であり、実際の利用時はユーザー自身が登録します）
  - ユーザー1  
   メールアドレス:user1@test.com  
   パスワード:password
   - ユーザー2  
   メールアドレス:user2@test.com  
   パスワード:password
   - ユーザー3  
   メールアドレス:user3@test.com  
   パスワード:password  
- 本アプリではメール認証機能のレビュー用にMailHogを使用しているため、メール認証時は、同サイトへ遷移するよう設定されています。

## テスト実施要領
  本アプリではPHPUnitを用いたFeatureテストを用意しています。テストにはテスト専用のデータベースを使用します。環境構築はの手順は下記のとおりです。  
  （コードの変更にはVS Codeなどのテキストエディタを使用してください）
### 環境構築
1. MySQLコンテナに入る  
コマンドライン上
```bash
docker-compose exec mysql bash
```
2. MySQLに接続  
MySQLコンテナ上
```bash
mysql -u root -p
```
> ※ パスワードはMYSQL_ROOT_PASSWORD:に設定されているrootを入力する。
>
3. テスト用データベースを作成  
Mysqlシェル内
```sql
CREATE DATABASE demo_test;
```
4. configファイルの編集  
- src\configディレクトリ内のdatabase.phpを開く
- mysqlの配列の下に下記のコードをコピーして挿入する
```
'mysql_test' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'demo_test',
            'username' => 'root',
            'password' => 'root',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
],
```
5. テスト用環境ファイル作成  
PHPコンテナ上
```bash
cp .env .env.testing
```
6. 環境ファイルの編集  
- srcディレクトリ内の.env.testingを開く  
- ファイル内のAPP＿ENVを'local'から'test'に編集
- APP＿KEYを空欄に編集  
- DB_DATABASEを'laravel_db'から'demo_test'に編集  
- DB_USERNAMEを'laravel_user'から'root'に編集  
- DB_PASSWORDを'laravel_pass'から'root'に編集  

- 編集例
```
- APP_ENV=local
- APP_KEY=*****:****************************************=
+ APP_ENV=test
+ APP_KEY=

- DB_DATABASE=laravel_db
- DB_USERNAME=laravel_user
- DB_PASSWORD=laravel_pass
+ DB_DATABASE=demo_test
+ DB_USERNAME=root
+ DB_PASSWORD=root
```
7. テスト用アプリキー作成  
PHPコンテナ上
```bash
php artisan key:generate --env=testing
```
8. テスト用マイグレーション実行(同時にデータ投入)  
PHPコンテナ上
```bash
php artisan migrate --seed --env=testing
```

### テスト実行方法
PHPコンテナ上で
```bash
php artisan test
```
を実行してください

## 使用技術

- PHP 8.1-fpm  
- Laravel 8.83.29  
- MySQL 8.0.26  
- Nginx 1.21.1  

## ER図

![ER図](./erd.png)

## URL

- お問い合わせ画面 : http://localhost/  
- ユーザー登録 : http://localhost/register/  
- phpMyAdmin : http://localhost:8080/  
