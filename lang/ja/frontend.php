<?php

declare(strict_types=1);

return [
    // Common translations
    'common' => [
        'dashboard' => 'ダッシュボード',
        'name' => '名前',
        'name_placeholder' => '山田 太郎',
        'email' => 'メールアドレス',
        'email_address' => 'メールアドレス',
        'email_placeholder' => 'you@example.com',
        'password' => 'パスワード',
        'password_placeholder' => '••••••••',
        'confirm_password' => 'パスワード確認',
        'confirm_password_placeholder' => '••••••••',
        'save' => '保存',
        'saved' => '保存しました',
        'cancel' => 'キャンセル',
        'continue' => '続ける',
        'log_out' => 'ログアウト',
        'two_factor_authentication' => '二要素認証',
        'enabled' => '有効',
        'disabled' => '無効',
        'appearance' => [
            'toggle' => 'テーマ切替',
            'light' => 'ライト',
            'dark' => 'ダーク',
            'system' => 'システム',
        ],
    ],

    // Authentication pages
    'auth' => [
        'login' => [
            'title' => 'アカウントにログイン',
            'description' => 'メールアドレスとパスワードを入力してください',
            'head_title' => 'ログイン',
            'forgot_password' => 'パスワードをお忘れですか?',
            'remember_me' => 'ログイン状態を保持する',
            'submit' => 'ログイン',
            'no_account' => 'アカウントをお持ちではありませんか?',
            'sign_up' => '新規登録',
        ],
        'register' => [
            'title' => 'アカウント作成',
            'description' => '新しいアカウントを作成するために、以下の情報を入力してください',
            'head_title' => 'アカウント作成',
            'submit' => 'アカウント作成',
            'have_account' => '既にアカウントをお持ちですか?',
            'login_link' => 'ログイン',
        ],
        'forgot_password' => [
            'title' => 'パスワードをお忘れですか?',
            'description' => 'メールアドレスを入力してください。パスワード再設定用のリンクをお送りします。',
            'head_title' => 'パスワード再設定',
            'submit' => 'パスワード再設定リンクを送信',
            'back_prompt' => 'ログイン画面に戻る場合は',
            'back_link' => 'こちら',
        ],
        'reset_password' => [
            'title' => 'パスワード再設定',
            'description' => '新しいパスワードを入力してください',
            'head_title' => 'パスワード再設定',
            'submit' => 'パスワードを再設定',
        ],
        'verify_email' => [
            'title' => 'メールアドレスの確認',
            'description' => 'アカウント作成ありがとうございます! メールアドレスを確認するため、登録したメールアドレスに確認メールを送信しました。メールが届かない場合は、再送信してください。',
            'head_title' => 'メールアドレス確認',
            'link_sent' => '新しい確認リンクをメールアドレスに送信しました。',
            'resend' => '確認メールを再送信',
        ],
        'confirm_password' => [
            'title' => 'パスワードの確認',
            'description' => '続行する前に、パスワードを入力してください。',
            'head_title' => 'パスワード確認',
            'submit' => '確認',
        ],
        'two_factor_challenge' => [
            'code' => [
                'title' => '二要素認証',
                'description' => '認証アプリに表示されている6桁のコードを入力してください',
                'toggle_text' => 'リカバリーコードを使用',
            ],
            'recovery' => [
                'title' => 'リカバリーコードで認証',
            ],
            'or_you_can' => 'または',
        ],
    ],

    // Navigation
    'navigation' => [
        'menu' => 'ナビゲーションメニュー',
        'repository' => 'リポジトリ',
        'documentation' => 'ドキュメント',
        'settings' => '設定',
    ],

    // Welcome page
    'welcome' => [
        'head_title' => 'ようこそ',
        'heading' => 'さあ、始めましょう',
        'description' => "Laravelには非常に豊富なエコシステムがあります。\n以下から始めることをお勧めします。",
        'cta_dashboard' => 'ダッシュボード',
        'cta_login' => 'ログイン',
        'cta_register' => '新規登録',
        'read_documentation' => 'ドキュメントを読む',
        'documentation_link' => 'ドキュメント',
        'watch_tutorials' => 'ビデオチュートリアルを見る',
        'laracasts_link' => 'Laracasts',
        'deploy_now' => '今すぐデプロイ',
    ],

    // Settings pages
    'settings' => [
        'layout' => [
            'title' => '設定',
            'description' => 'アカウント設定を管理します。',
            'nav' => [
                'profile' => 'プロフィール',
                'password' => 'パスワード',
                'two_factor' => '二要素認証',
                'appearance' => '外観',
            ],
        ],
        'profile' => [
            'breadcrumb' => 'プロフィール',
            'head_title' => 'プロフィール',
            'section_title' => 'プロフィール情報',
            'section_description' => 'アカウントのプロフィール情報とメールアドレスを更新します。',
            'email_unverified' => 'メールアドレスが確認されていません。',
            'resend_verification' => '確認メールを再送信する',
            'verification_sent' => '新しい確認リンクをメールアドレスに送信しました。',
        ],
        'password' => [
            'breadcrumb' => 'パスワード',
            'head_title' => 'パスワード',
            'section_title' => 'パスワード変更',
            'current' => '現在のパスワード',
            'new' => '新しいパスワード',
            'submit' => 'パスワードを更新',
        ],
        'appearance' => [
            'breadcrumb' => '外観',
            'head_title' => '外観',
            'section_title' => '外観設定',
        ],
        'two_factor' => [
            'breadcrumb' => '二要素認証',
            'section_title' => '二要素認証',
            'enabled_description' => '二要素認証が有効になっています。',
            'continue' => '続ける',
            'enable' => '有効にする',
            'disable' => '無効にする',
        ],
    ],

    // Components
    'components' => [
        'delete_user' => [
            'title' => 'アカウント削除',
            'description' => 'アカウントを完全に削除します。',
            'warning_title' => '警告',
            'warning_message' => 'アカウントを削除すると、すべてのリソースとデータが完全に削除されます。アカウントを削除する前に、保存しておきたいデータや情報をダウンロードしてください。',
            'trigger' => 'アカウントを削除',
            'modal_title' => 'アカウントを削除してもよろしいですか?',
            'modal_description' => 'アカウントを削除すると、すべてのリソースとデータが完全に削除されます。この操作を確認するため、パスワードを入力してください。',
            'confirm' => '削除',
        ],
        'two_factor_modal' => [
            'title_enabled' => '二要素認証が有効になりました',
            'description_enabled' => '二要素認証が有効になっています。QRコードをスキャンするか、セットアップキーを認証アプリに入力してください。',
            'title_verify' => '認証コードの確認',
            'description_verify' => '認証アプリに表示されている6桁のコードを入力してください',
            'title_enable' => '二要素認証を有効にする',
            'description_enable' => '二要素認証を有効にするには、QRコードをスキャンするか、セットアップキーを認証アプリに入力してください',
            'button_continue' => '続ける',
            'button_close' => '閉じる',
            'or_manual' => 'または、手動でコードを入力',
            'back' => '戻る',
            'confirm' => '確認',
        ],
    ],
];
