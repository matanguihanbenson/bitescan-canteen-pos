<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Storage;

class SettingsService
{
    private string $settingsPath = 'private/scholar_settings.json';

    public function getScholarSettings(): array
    {
        // Prefer DB settings; fallback to file/defaults
        $db = [
            'default_allowance' => (float) (AppSetting::get('scholar.default_allowance') ?? 70.00),
            'auto_grant_allowance' => (bool) (AppSetting::get('scholar.auto_grant_allowance') ?? true),
            'auto_reset' => (bool) (AppSetting::get('scholar.auto_reset') ?? true),
            'reset_time' => AppSetting::get('scholar.reset_time', '04:00'),
        ];

        if (Storage::exists($this->settingsPath)) {
            $data = json_decode(Storage::get($this->settingsPath), true) ?: [];
            // File is fallback only – DB always takes precedence
            $db = array_merge($data, $db);
        }

        return $db;
    }

    public function updateScholarSettings(array $settings): void
    {
        try {
            // Validate settings before saving
            $this->validateScholarSettings($settings);
            
            // Persist to DB; also mirror to file for fallback
            if (array_key_exists('default_allowance', $settings)) {
                AppSetting::set('scholar.default_allowance', $settings['default_allowance']);
            }
            if (array_key_exists('auto_grant_allowance', $settings)) {
                AppSetting::set('scholar.auto_grant_allowance', (int)$settings['auto_grant_allowance']);
            }
            if (array_key_exists('auto_reset', $settings)) {
                AppSetting::set('scholar.auto_reset', (int)$settings['auto_reset']);
            }
            if (array_key_exists('reset_time', $settings)) {
                AppSetting::set('scholar.reset_time', $settings['reset_time']);
            }

            // Mirror to file for fallback
            $current = $this->getScholarSettings();
            Storage::put($this->settingsPath, json_encode($current));
        } catch (\Exception $e) {
            throw new \Exception('Failed to update scholar settings: ' . $e->getMessage());
        }
    }
    
    private function validateScholarSettings(array $settings): void
    {
        if (isset($settings['default_allowance'])) {
            if (!is_numeric($settings['default_allowance']) || $settings['default_allowance'] < 0) {
                throw new \InvalidArgumentException('Default allowance must be a positive number');
            }
        }
        
        if (isset($settings['reset_time'])) {
            if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $settings['reset_time'])) {
                throw new \InvalidArgumentException('Reset time must be in HH:MM format (24-hour)');
            }
        }
    }

    public function getGeneralSettings(): array
    {
        return [
            'app_name' => AppSetting::get('general.app_name', 'Canteen BiteScan POS'),
            'company_name' => AppSetting::get('general.company_name', ''),
            'receipt_footer' => AppSetting::get('general.receipt_footer', 'Thank you for your purchase!'),
            'tax_rate' => (float) AppSetting::get('general.tax_rate', 0),
            'currency' => AppSetting::get('general.currency', 'PHP'),
        ];
    }

    public function updateGeneralSettings(array $settings): void
    {
        foreach (['app_name', 'company_name', 'receipt_footer', 'tax_rate', 'currency'] as $key) {
            if (array_key_exists($key, $settings)) {
                AppSetting::set('general.' . $key, $settings[$key]);
            }
        }
    }

    public function getPOSSettings(): array
    {
        return [
            'departments' => json_decode(AppSetting::get('pos.departments', '["Staff","Faculty"]'), true),
            'discount_types' => json_decode(AppSetting::get('pos.discount_types', '[{"name":"Senior Citizen","value":20},{"name":"PWD","value":20}]'), true),
            'credit_user_types' => json_decode(AppSetting::get('pos.credit_user_types', '["Staff","Faculty"]'), true),
        ];
    }

    public function updatePOSSettings(array $settings): void
    {
        \Illuminate\Support\Facades\Log::info('SettingsService::updatePOSSettings - Starting', [
            'settings' => $settings
        ]);
        
        try {
            if (isset($settings['departments'])) {
                $json = json_encode($settings['departments']);
                \Illuminate\Support\Facades\Log::info('Saving departments', ['json' => $json]);
                AppSetting::set('pos.departments', $json);
            }
            
            if (isset($settings['discount_types'])) {
                $json = json_encode($settings['discount_types']);
                \Illuminate\Support\Facades\Log::info('Saving discount_types', ['json' => $json]);
                AppSetting::set('pos.discount_types', $json);
            }
            
            if (isset($settings['credit_user_types'])) {
                $json = json_encode($settings['credit_user_types']);
                \Illuminate\Support\Facades\Log::info('Saving credit_user_types', ['json' => $json]);
                AppSetting::set('pos.credit_user_types', $json);
            }
            
            \Illuminate\Support\Facades\Log::info('SettingsService::updatePOSSettings - Completed successfully');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SettingsService::updatePOSSettings - Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}

