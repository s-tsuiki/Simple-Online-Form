# Simple-Online-Form
TECH-BASEインターンで作ったシンプルなWeb掲示板です。  
HTML, PHP, MySQLを用いており、コメント投稿・削除・編集機能のみを持ちます。  
<div align="center">
  <img src="https://raw.github.com/wiki/s-tsuiki/Simple-Online-Form/images/mission5.png" alt="掲示板画像" border="1">
</div>

## 概要
コメント投稿・削除・編集機能のみを持つシンプルなWeb掲示板です。  
ユーザーはユーザー名とそのコメントに対するパスワードを入力し、コメントを投稿できます。  
コメントを削除・編集する際は、投稿番号とそのコメントのパスワードが一致しないとできません。  
webサイトのデザインも指定せず、デフォルトのままです。  
また、XSS, CSRF対策などのセキュリティ対策もしていません。  
これらのことを改良したものが「Original-Online-Form」です。

## 構成
フロントエンドはHTMLのみで実装しています。  
バックエンドはPHPのみで実装しています。  
コメントはサーバーのデータベース上に保存されます。

## 開発言語
フロントエンド・・・HTML  
バックエンド・・・PHP  
データベース・・・MySQL

## 開発環境
エディタ・・・TeraPad  
ブラウザ・・・Chrome  
サーバー環境・・・Linux, Apache, PHP
