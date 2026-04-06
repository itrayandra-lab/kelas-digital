<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareDomain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShareDomainController extends Controller
{
    public function index()
    {
        $shareDomains = ShareDomain::orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.share-domains.index', compact('shareDomains'));
    }

    public function create()
    {
        return view('admin.share-domains.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'domain_name' => 'required|string|max:255|unique:share_domains,domain_name',
            'webhook_url' => 'required|url|max:255',
            'api_key' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        ShareDomain::create($data);

        return redirect()
            ->route('admin.share-domains.index')
            ->with('success', 'Share Domain created successfully.');
    }

    public function show(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        return view('admin.share-domains.show', compact('shareDomain'));
    }

    public function edit(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        return view('admin.share-domains.edit', compact('shareDomain'));
    }

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
            'api_key' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $shareDomain->update($data);

        return redirect()
            ->route('admin.share-domains.index')
            ->with('success', 'Share Domain updated successfully.');
    }

    public function destroy(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);
        $shareDomain->delete();

        return redirect()
            ->route('admin.share-domains.index')
            ->with('success', 'Share Domain deleted successfully.');
    }

    public function activate(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        if ($shareDomain->isActive()) {
            return redirect()->back()->with('error', 'This domain is already active.');
        }

        $shareDomain->activate();

        return redirect()->back()->with('success', 'Share Domain activated successfully.');
    }

    public function deactivate(string $id)
    {
        $shareDomain = ShareDomain::findOrFail($id);

        if ($shareDomain->isInactive()) {
            return redirect()->back()->with('error', 'This domain is already inactive.');
        }

        $shareDomain->deactivate();

        return redirect()->back()->with('success', 'Share Domain deactivated successfully.');
    }
}