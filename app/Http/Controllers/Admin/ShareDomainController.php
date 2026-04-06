<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareDomain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShareDomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shareDomains = ShareDomain::orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.share-domains.index', compact('shareDomains'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.share-domains.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'domain_name' => 'required|string|max:255|unique:share_domains,domain_name',
            'webhook_url' => 'required|url|max:255',
            'api_key' => 'nullable|string|max:64',
            'status' => 'required|in:active,inactive',
        ]);

        // Generate API key if not provided
        if (empty($data['api_key'])) {
            $data['api_key'] = bin2hex(random_bytes(32));
        }

        $shareDomain = ShareDomain::create($data);

        return redirect()
            ->route('admin.share-domains.index')
            ->with('success', 'Share Domain created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        return view('admin.share-domains.show', compact('shareDomain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        return view('admin.share-domains.edit', compact('shareDomain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        $data = $request->validate([
            'domain_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('share_domains', 'domain_name')->ignore($id),
            ],
            'webhook_url' => 'required|url|max:255',
            'api_key' => 'nullable|string|max:64',
            'status' => 'required|in:active,inactive',
        ]);

        $shareDomain->update($data);

        return redirect()
            ->route('admin.share-domains.index')
            ->with('success', 'Share Domain updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);
        $shareDomain->delete();

        return redirect()
            ->route('admin.share-domains.index')
            ->with('success', 'Share Domain deleted successfully.');
    }

    /**
     * Activate a share domain
     */
    public function activate(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        if ($shareDomain->isActive()) {
            return redirect()->back()->with('error', 'This domain is already active.');
        }

        $shareDomain->activate();

        return redirect()->back()->with('success', 'Share Domain activated successfully.');
    }

    /**
     * Deactivate a share domain
     */
    public function deactivate(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        if ($shareDomain->isInactive()) {
            return redirect()->back()->with('error', 'This domain is already inactive.');
        }

        $shareDomain->deactivate();

        return redirect()->back()->with('success', 'Share Domain deactivated successfully.');
    }

    /**
     * Regenerate API key for a share domain
     */
    public function regenerateApiKey(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);
        $newApiKey = $shareDomain->generateApiKey();

        return redirect()
            ->back()
            ->with('success', 'API Key regenerated successfully.')
            ->with('new_api_key', $newApiKey);
    }
}