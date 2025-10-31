# 001/toastify

## 目的

Laravel 12 React Starter Kit の最新版(2025-10-31時点)をマージし、toast通知システムを実装するためのブランチ。



## テスト項目

### tests/Feature/Auth/AuthenticationTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| login screen can be rendered | ログイン画面が表示される |
| users can authenticate using the login screen | ログイン画面からユーザーが認証できる |
| users with two factor enabled are redirected to two factor challenge | 2FA有効ユーザーは2FAチャレンジにリダイレクトされる |
| users can not authenticate with invalid password | 無効なパスワードでは認証できない |
| users can logout | ユーザーがログアウトできる |
| users are rate limited | レート制限が機能する |

### tests/Feature/Auth/EmailVerificationTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| email verification screen can be rendered | メール確認画面が表示される |
| email can be verified | メールアドレスを確認できる |
| email is not verified with invalid hash | 無効なハッシュではメールが確認されない |
| email is not verified with invalid user id | 無効なユーザーIDではメールが確認されない |
| verified user is redirected to dashboard from verification prompt | 確認済みユーザーは確認画面からダッシュボードにリダイレクトされる |
| already verified user visiting verification link is redirected without firing event again | 確認済みユーザーが確認リンクを訪問してもイベントは再発火しない |

### tests/Feature/Auth/PasswordConfirmationTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| confirm password screen can be rendered | パスワード確認画面が表示される |
| password confirmation requires authentication | パスワード確認には認証が必要 |

### tests/Feature/Auth/PasswordResetTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| reset password link screen can be rendered | パスワードリセットリンク画面が表示される |
| reset password link can be requested | パスワードリセットリンクをリクエストできる |
| reset password screen can be rendered | パスワードリセット画面が表示される |
| password can be reset with valid token | 有効なトークンでパスワードをリセットできる |
| password cannot be reset with invalid token | 無効なトークンではパスワードをリセットできない |

### tests/Feature/Auth/RegistrationTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| registration screen can be rendered | 登録画面が表示される |
| new users can register | 新規ユーザーが登録できる |

### tests/Feature/Auth/TwoFactorChallengeTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| two factor challenge redirects to login when not authenticated | 未認証時は2FAチャレンジからログインにリダイレクトされる |
| two factor challenge can be rendered | 2FAチャレンジ画面が表示される |

### tests/Feature/Auth/VerificationNotificationTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| sends verification notification | 確認通知を送信する |
| does not send verification notification if email is verified | メールが確認済みの場合は確認通知を送信しない |

### tests/Feature/DashboardTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| guests are redirected to the login page | ゲストはログインページにリダイレクトされる |
| authenticated users can visit the dashboard | 認証済みユーザーはダッシュボードにアクセスできる |

### tests/Feature/ExampleTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| it returns a successful response | 成功レスポンスを返す |

### tests/Feature/Http/HandleInertiaRequestsTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| flash messages are shared with inertia responses | フラッシュメッセージがInertiaレスポンスと共有される |
| flash property is null when no flash messages are present | フラッシュメッセージがない場合はflashプロパティがnull |
| flash property filters out null values | flashプロパティはnull値をフィルタリングする |
| multiple flash message types can be set simultaneously | 複数のフラッシュメッセージタイプを同時に設定できる |

### tests/Feature/Settings/PasswordUpdateTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| password update page is displayed | パスワード更新ページが表示される |
| password can be updated | パスワードを更新できる |
| correct password must be provided to update password | パスワード更新には正しい現在のパスワードが必要 |

### tests/Feature/Settings/ProfileUpdateTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| profile page is displayed | プロフィールページが表示される |
| profile information can be updated | プロフィール情報を更新できる |
| email verification status is unchanged when the email address is unchanged | メールアドレスが変更されない場合は確認ステータスが維持される |
| user can delete their account | ユーザーがアカウントを削除できる |
| correct password must be provided to delete account | アカウント削除には正しいパスワードが必要 |

### tests/Feature/Settings/TwoFactorAuthenticationTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| two factor settings page can be rendered | 2FA設定ページが表示される |
| two factor settings page requires password confirmation when enabled | 2FA有効時はパスワード確認が必要 |
| two factor settings page does not requires password confirmation when disabled | 2FA無効時はパスワード確認が不要 |
| two factor settings page returns forbidden response when two factor is disabled | 2FA機能が無効の場合は403レスポンスを返す |

**合計**: 45テスト (198アサーション)
