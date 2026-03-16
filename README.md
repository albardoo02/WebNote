# WebNote
目標管理と週ごとの反省を記録し、PDFとして出力できるシンプルなWebアプリケーションです。

## 動作環境
- PHP 8.2 以上
- PHP 拡張機能: `ext-gd`, `ext-pdo_sqlite`
- Composer (ライブラリ管理用)

## セットアップ手順

### 1. リポジトリのクローン
```bash
git clone [https://github.com/albardoo02/WebNote.git](https://github.com/albardoo02/WebNote.git)
cd WebNote
```

### 2. 依存関係のインストール
`mPDF` ライブラリをインストールします。

```bash
composer install
```

### 3. 日本語フォントの配置
PDFで日本語を正しく表示するために、IPAexフォントが必要です。

1. [IPAexフォント(ipaexg.ttf)](https://moji.or.jp/ipafont/ipaex00401/) をダウンロード。
2. 解凍した`ipaexg.ttf`をプロジェクト内の`fonts/`フォルダに配置してください。

### 4. 実行
XAMPPなどのローカルサーバー環境、または以下のコマンドでビルトインサーバーを起動してアクセスしてください。
```bash
php -S localhost:8000
```
`http://localhost:8000`にアクセスし、`register.php`からユーザー登録を行ってください。

## フォルダ構成
* `actions/`: データ更新、削除などのロジック処理
* `css/`: スタイルシート (`style.css`)
* `fonts/`: 日本語フォント配置場所（.gitignore対象）
* `includes/`: ヘッダー・フッターなどの共通パーツ
* `vendor/`: Composerライブラリ（.gitignore対象）
* `db.php`: データベース接続・テーブル初期化
* `download_pdf.php`: PDF生成処理

## ライセンス / LICENSE
このプロジェクト自体は[MITライセンス](./LICENSE) で公開しています。
使用しているライブラリ(mPDF)およびフォント(IPAexフォント)については、それぞれのライセンスをご確認ください。
