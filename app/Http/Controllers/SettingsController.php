<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = SystemSetting::current();

        return view('settings.index', [
            'settings' => $setting->toArray(),
            'logoUrl' => $setting->logo_path ? asset('storage/'.$setting->logo_path) : null,
        ]);
    }

    public function update(Request $request)
    {
        $settings = $request->validate([
            'organization_name' => ['required', 'string', 'max:120'],
            'organization_unit' => ['required', 'string', 'max:120'],
            'asset_prefix' => ['required', 'string', 'max:20'],
            'asset_separator' => ['required', 'string', 'max:3'],
            'asset_digits' => ['required', 'integer', 'min:2', 'max:8'],
            'asset_next_number' => ['required', 'integer', 'min:1'],
            'timezone' => ['required', 'in:Asia/Jakarta (GMT+07:00),Asia/Makassar (GMT+08:00),Asia/Jayapura (GMT+09:00)'],
            'date_format' => ['required', 'string', 'max:30'],
            'email_notifications' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:4096'],
        ]);

        $settings['email_notifications'] = $request->boolean('email_notifications');
        $setting = SystemSetting::current();

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $settings['logo_path'] = $request->file('logo')->store('system', 'public');
        }

        $setting->update($settings);

        return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }

    public function reset()
    {
        $setting = SystemSetting::current();

        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $setting->update(array_merge(SystemSetting::defaults(), ['logo_path' => null]));

        return back()->with('success', 'Pengaturan dikembalikan ke nilai default.');
    }
}