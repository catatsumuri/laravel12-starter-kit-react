# User Registration & Account Deletion Toggle

This feature allows administrators to control whether users can self-register and delete their own accounts through environment configuration.

## Configuration

Add to `.env`:

```env
USER_REGISTRATION_ENABLED=true
USER_ACCOUNT_DELETION_ENABLED=true
```

## Implementation

### Backend

**Config**: `config/user.php`
- `registration_enabled` - Controls user registration
- `account_deletion_enabled` - Controls account deletion

**Middleware**: `app/Http/Middleware/CheckFeatureEnabled.php`
- Protects routes when features are disabled
- Returns 403 error if feature is off

**Routes**:
- `routes/auth.php` - Registration routes protected by `feature:registration`
- `routes/settings.php` - Deletion route protected by `feature:account-deletion`

**Shared Data**: `app/Http/Middleware/HandleInertiaRequests.php`
```php
'features' => [
    'registration' => config('user.registration_enabled'),
    'accountDeletion' => config('user.account_deletion_enabled'),
],
```

### Frontend

**Usage**:
```tsx
const { features } = usePage<SharedData>().props;

{features.registration && (
    <Link href={register()}>Register</Link>
)}

{features.accountDeletion && (
    <DeleteUserButton />
)}
```

**Files**:
- `resources/js/pages/auth/login.tsx` - Hide registration link
- `resources/js/pages/auth/register.tsx` - Redirect if disabled
- `resources/js/pages/welcome.tsx` - Hide registration link
- `resources/js/components/delete-user.tsx` - Hide delete button

## Tests

- `tests/Feature/Auth/RegistrationToggleTest.php`
- `tests/Feature/Settings/AccountDeletionToggleTest.php`
- `tests/Feature/Middleware/CheckFeatureEnabledTest.php`

## Upstream Conflict Resolution

When merging upstream changes, prefer `features.registration` from `HandleInertiaRequests` over individual `canRegister` props passed from controllers. This provides:
- Centralized configuration
- Consistent behavior across all pages
- No need to pass props from each controller
