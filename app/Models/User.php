<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * ユーザーモデル
 *
 * アプリケーションのユーザーを表すモデル。
 * 認証、権限管理、二要素認証、アクティビティログ機能を提供します。
 *
 * @property int $id ユーザーID
 * @property string $name ユーザー名
 * @property string $email メールアドレス
 * @property \Illuminate\Support\Carbon|null $email_verified_at メール確認日時
 * @property string $password パスワード（ハッシュ化）
 * @property string|null $two_factor_secret 二要素認証シークレット
 * @property string|null $two_factor_recovery_codes 二要素認証リカバリーコード
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at 二要素認証確認日時
 * @property string|null $remember_token ログイン記憶トークン
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 */
class User extends Authenticatable
{
    /**
     * 使用するトレイト
     *
     * @use HasFactory<\Database\Factories\UserFactory> ファクトリー機能
     * @use Notifiable 通知機能
     * @use TwoFactorAuthenticatable 二要素認証機能
     * @use HasRoles ロール・権限管理機能（Spatie Permission）
     * @use LogsActivity アクティビティログ機能（Spatie Activity Log）
     */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, LogsActivity;

    /**
     * 複数代入可能な属性
     *
     * これらの属性は、create()やupdate()メソッドで一括代入が可能です。
     * セキュリティのため、ユーザーが直接入力できる属性のみを指定しています。
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',      // ユーザー名
        'email',     // メールアドレス
        'password',  // パスワード（自動的にハッシュ化されます）
    ];

    /**
     * シリアライズ時に隠蔽する属性
     *
     * JSON形式やArray形式に変換する際に、これらの属性は除外されます。
     * 機密情報を外部に漏らさないための設定です。
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',                    // パスワードハッシュ
        'two_factor_secret',           // 二要素認証シークレットキー
        'two_factor_recovery_codes',   // 二要素認証リカバリーコード
        'remember_token',              // ログイン記憶トークン
    ];

    /**
     * 属性のキャスト設定
     *
     * データベースから取得した値を特定の型に自動変換します。
     * これにより、属性へのアクセス時に適切な型として扱えます。
     *
     * @return array<string, string> キャスト設定の配列
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',        // メール確認日時をCarbonインスタンスに変換
            'password' => 'hashed',                   // パスワードを自動的にハッシュ化
            'two_factor_confirmed_at' => 'datetime',  // 二要素認証確認日時をCarbonインスタンスに変換
        ];
    }

    /**
     * アクティビティログのオプション設定
     *
     * Spatie Activity Logパッケージの設定を定義します。
     * ユーザーモデルの作成、更新、削除が自動的に記録されます。
     *
     * 記録される操作:
     * - created: ユーザーが作成された時
     * - updated: ユーザー情報が更新された時（name、emailの変更時のみ）
     * - deleted: ユーザーが削除された時
     *
     * 記録される属性: name, email
     * 除外される属性: password, two_factor_secret, remember_token など
     *
     * @return LogOptions ログオプション設定
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])    // nameとemailの変更のみ記録
            ->logOnlyDirty()                // 実際に変更された属性のみ記録（変更前後の値を保存）
            ->dontSubmitEmptyLogs();        // 変更がない場合はログを作成しない（効率化）
    }

    /**
     * ユーザーが管理者かどうかを判定
     *
     * @return bool 管理者の場合true
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
