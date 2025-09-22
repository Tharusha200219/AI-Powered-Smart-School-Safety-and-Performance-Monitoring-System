<?php

namespace App\Http\Controllers\Admin\Setup;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Setup\SettingsRepository;
use App\Repositories\Interfaces\Admin\Setup\SettingsRepositoryInterface;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected SettingsRepositoryInterface $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    protected function index(Request $request)
    {
        $setting = $this->settingsRepository->getLatest();
        return view('admin.pages.setup.settings.index', compact(['setting']));
    }

    protected function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'company_email' => 'required|email',
            'company_phone' => 'required',
            'company_address' => 'required',
            'mail_signature' => 'nullable',
        ]);

        dd($request->all());
        $this->settingsRepository->update($request->all());
        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
