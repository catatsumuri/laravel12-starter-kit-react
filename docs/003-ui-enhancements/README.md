# UI Enhancements

## 新しい環境変数設定

### SHOW_PASSWORD_TOGGLE
- **デフォルト**: `true`
- **機能**: ログイン・登録フォームでパスワード表示切替ボタンの制御
- **設定**: `config/ui.php`の`show_password_toggle`
- **使用箇所**: 
  - `FortifyServiceProvider.php`
  - `RegisteredUserController.php`

```env
SHOW_PASSWORD_TOGGLE=false  # パスワード表示切替ボタンを非表示
```

### DISABLE_WELCOME_PAGE
- **デフォルト**: `false`
- **機能**: ウェルカムページの無効化制御
- **設定**: `config/ui.php`の`disable_welcome_page`
- **使用箇所**: `routes/web.php`

```env
DISABLE_WELCOME_PAGE=true   # ウェルカムページをスキップしてログインページへ直接リダイレクト
```
