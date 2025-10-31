# 002/feature-breadcrumbs

## 目的

Laravel 12 React Starter Kit にパンくずリスト機能を実装するためのブランチ。
Diglactic Laravel Breadcrumbs と Inertia Breadcrumbs を統合し、サーバーサイドでパンくずリストを定義してフロントエンドで表示する。

## 実装内容

### パッケージ追加
- `diglactic/laravel-breadcrumbs`: サーバーサイドでのパンくずリスト定義
- `robertboes/inertia-breadcrumbs`: Inertiaとの統合

### 主な変更ファイル

#### バックエンド
- `breadcrumbs/routes.php`: パンくずリスト定義（dashboard, profile.edit, password.edit, appearance.edit, two-factor.show）
- `config/breadcrumbs.php`: Breadcrumbsパッケージ設定
- `config/inertia-breadcrumbs.php`: Inertia統合設定

#### フロントエンド
- `resources/js/components/breadcrumbs.tsx`: `href` → `url` にリネーム
- `resources/js/layouts/app-layout.tsx`: `usePage().props.breadcrumbs` から取得
- `resources/js/types/index.d.ts`: `BreadcrumbItem` 型定義更新、`SharedData` に `breadcrumbs` 追加

#### ページ更新（ハードコードされたパンくずリストを削除）
- `resources/js/pages/dashboard.tsx`
- `resources/js/pages/settings/profile.tsx`
- `resources/js/pages/settings/password.tsx`
- `resources/js/pages/settings/appearance.tsx`
- `resources/js/pages/settings/two-factor.tsx`

### 破壊的変更
- `BreadcrumbItem.href` → `BreadcrumbItem.url` にリネーム

## Git履歴
```
b4fa734 feat(breadcrumbs): integrate Diglactic + Inertia sharing; remove page-local crumbs; rename href→url
ddad9e5 chore: update README
fb96a03 chore: bootstrap from laravel/react-starter-kit (b164c3e snapshot)
```

## テスト項目

### tests/Feature/ExampleTest.php

| テスト内容 (英語) | テスト内容 (日本語) |
|---|---|
| it returns a successful response | 成功レスポンスを返す |

**合計**: 1テスト
