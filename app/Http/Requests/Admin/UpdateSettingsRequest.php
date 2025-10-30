<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Admin settings are protected by auth middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:255'],
            'app_url' => ['required', 'string', 'url', 'max:255'],
            'app_debug' => ['nullable', 'boolean'],
            'app_locale' => ['required', 'string', 'in:ja,en'],
            'app_fallback_locale' => ['required', 'string', 'in:ja,en'],
            'aws_access_key_id' => ['nullable', 'string', 'max:255'],
            'aws_secret_access_key' => ['nullable', 'string', 'max:255'],
            'aws_default_region' => ['nullable', 'string', 'max:255'],
            'aws_bucket' => ['nullable', 'string', 'max:255'],
            'aws_use_path_style_endpoint' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get validated data with proper type conversion and masking.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        // If a specific key was requested, return it as-is
        if ($key !== null) {
            return $validated;
        }

        // Convert booleans (Laravel's boolean validation doesn't auto-convert)
        $validated['app_debug'] = $this->boolean('app_debug');
        $validated['aws_use_path_style_endpoint'] = $this->boolean('aws_use_path_style_endpoint');

        // Handle masked secret key - if it's the mask, treat as null (don't update)
        if (isset($validated['aws_secret_access_key']) && $validated['aws_secret_access_key'] === '********') {
            $validated['aws_secret_access_key'] = null;
        }

        return $validated;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'app_name' => 'application name',
            'app_url' => 'application URL',
            'app_debug' => 'debug mode',
            'app_locale' => 'locale',
            'app_fallback_locale' => 'fallback locale',
            'aws_access_key_id' => 'AWS access key ID',
            'aws_secret_access_key' => 'AWS secret access key',
            'aws_default_region' => 'AWS default region',
            'aws_bucket' => 'AWS bucket',
            'aws_use_path_style_endpoint' => 'AWS use path style endpoint',
        ];
    }
}
