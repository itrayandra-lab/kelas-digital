@extends('layouts.admin')

@section('title', 'Manage Payments')

@section('content')

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Pending Payments for Approval</h2>
            <p class="text-sm text-gray-600 mt-1">Review and approve these payments to grant course access to users.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User &
                            Course</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Enrollment
                            Date</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingEnrollments as $enrollment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->user->name) }}&background=1474bc&color=ffffff"
                                            alt="Avatar">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                        <div class="text-sm text-gray-500">for
                                            "{{ Str::limit($enrollment->course->title, 40) }}"</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Rp
                                    {{ number_format($enrollment->course->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $enrollment->enrolled_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ route('admin.payments.approve', $enrollment->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to approve this payment?');">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                Great job! No pending payments to approve.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">All Payments History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Course</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allEnrollments as $enrollment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $enrollment->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700 line-clamp-1">{{ $enrollment->course->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">Rp
                                    {{ number_format($enrollment->course->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($enrollment->payment_status === 'completed')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $enrollment->enrolled_at->format('d M Y') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No payment history found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $allEnrollments->links() }}
    </div>

@endsection
