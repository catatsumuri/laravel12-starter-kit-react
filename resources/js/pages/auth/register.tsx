import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { Eye, EyeOff } from 'lucide-react';
import { useState } from 'react';

interface RegisterProps {
    showPasswordToggle: boolean;
}

export default function Register({ showPasswordToggle }: RegisterProps) {
    const [showPassword, setShowPassword] = useState(false);
    const [showPasswordConfirmation, setShowPasswordConfirmation] =
        useState(false);

    const getPasswordFieldType = (isVisible: boolean) => {
        if (!showPasswordToggle) return 'password';
        return isVisible ? 'text' : 'password';
    };
    return (
        <AuthLayout
            title="Create an account"
            description="Enter your details below to create your account"
        >
            <Head title="Register" />
            <Form
                {...RegisteredUserController.store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Full name"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <div className="relative">
                                    <Input
                                        id="password"
                                        type={getPasswordFieldType(
                                            showPassword,
                                        )}
                                        required
                                        tabIndex={3}
                                        autoComplete="new-password"
                                        name="password"
                                        placeholder="Password"
                                        className={
                                            showPasswordToggle ? 'pr-10' : ''
                                        }
                                    />
                                    {showPasswordToggle && (
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowPassword(!showPassword)
                                            }
                                            className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground transition-colors hover:text-foreground"
                                            aria-label={
                                                showPassword
                                                    ? 'Hide password'
                                                    : 'Show password'
                                            }
                                            aria-pressed={showPassword}
                                            data-test="password-toggle"
                                        >
                                            {showPassword ? (
                                                <EyeOff
                                                    className="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            ) : (
                                                <Eye
                                                    className="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            )}
                                        </button>
                                    )}
                                </div>
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirm password
                                </Label>
                                <div className="relative">
                                    <Input
                                        id="password_confirmation"
                                        type={getPasswordFieldType(
                                            showPasswordConfirmation,
                                        )}
                                        required
                                        tabIndex={4}
                                        autoComplete="new-password"
                                        name="password_confirmation"
                                        placeholder="Confirm password"
                                        className={
                                            showPasswordToggle ? 'pr-10' : ''
                                        }
                                    />
                                    {showPasswordToggle && (
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowPasswordConfirmation(
                                                    !showPasswordConfirmation,
                                                )
                                            }
                                            className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground transition-colors hover:text-foreground"
                                            aria-label={
                                                showPasswordConfirmation
                                                    ? 'Hide password confirmation'
                                                    : 'Show password confirmation'
                                            }
                                            aria-pressed={
                                                showPasswordConfirmation
                                            }
                                            data-test="password-confirmation-toggle"
                                        >
                                            {showPasswordConfirmation ? (
                                                <EyeOff
                                                    className="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            ) : (
                                                <Eye
                                                    className="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            )}
                                        </button>
                                    )}
                                </div>
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                tabIndex={5}
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={6}>
                                Log in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
