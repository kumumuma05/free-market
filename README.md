# Coachtechフリマ

商品の出品と購入を行うことができるフリーマーケットWebアプリケーションです。

## 機能概要

- アプリ概要
  - このアプリはユーザー同士が商品を出品・購入できるフリマアプリです。
  - ユーザー登録した会員は、商品の購入のほか、商品を出品し、商品説明を登録できます。
  - 会員以外のユーザーは出品商品を閲覧し、気に入った商品をお気に入り登録したり、購入することができます。
  - 購入時は配送先住所を入力し、支払方法を選択することができ、取引が完了した商品にはコメントや評価を残すことが可能です。

- 主な機能
  - ユーザー登録・ログイン（メール認証あり）
  - プロフィール編集（名前、自己紹介、アイコン画像）
  - 商品出品（タイトル、ブランド、価格、商品説明、画像アップロード、商品コンディション）
  - 商品一覧表示・商品詳細表示
  - お気に入り登録/解除
  - コメント登校・評価
  - 商品購入（注文・配送先登録・支払い）
  - 購入履歴・出品履歴の確認
  - 出品商品のステータス管理
  
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
# free-market
