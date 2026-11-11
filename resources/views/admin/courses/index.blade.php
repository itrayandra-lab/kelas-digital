@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div class="flex-1">
        </div>
        @can('create courses')
        <a href="{{ route('admin.courses.create') }}" data-tour="add-course"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
            <i class="fas fa-plus mr-2 -ml-1 text-base"></i>
            Add New Course
        </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Title</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Instructor
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Price</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Category</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Level</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ $course->instructor }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">Rp {{ number_format($course->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ optional($course->category)->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">
                                    {{ $course->level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                <a href="{{ route('admin.courses.show', $course->id) }}"
                                    class="text-gray-500 hover:text-gray-800">View</a>
                                @can('edit courses')
                                <a href="{{ route('admin.curriculum.show', $course) }}" data-tour="curriculum-btn"
                                    class="inline-flex items-center px-2.5 py-1 bg-primary-600 text-white rounded-md hover:bg-primary-700"><i class="fas fa-list-ol mr-1"></i> Kurikulum &amp; Tes</a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" data-tour="edit-course"
                                    class="text-primary-600 hover:text-primary-800">Edit</a>
                                @endcan
                                @can('delete courses')
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this course and all of its related lessons? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                No courses found. Click "Add New Course" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>

@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('admin-courses', [
  { popover: { title: 'Kelola Kelas', description: 'Di sini semua kelas dibuat dan diatur. Alurnya cuma 2 langkah: isi detail kelas, lalu susun kurikulum & soal tesnya.' } },
  { element: '[data-tour="add-course"]', popover: { title: '1. Buat kelas baru', description: 'Isi judul, deskripsi, harga (atau gratis), trailer YouTube, dan thumbnail. Ini yang dilihat calon peserta di halaman kelas.', side: 'bottom' } },
  { element: '[data-tour="curriculum-btn"]', popover: { title: '2. Kurikulum & Tes', description: 'Setelah kelas dibuat, klik ini untuk menyusun bab, materi video/tulisan, dan soal pre-test & post-test.', side: 'left' } },
  { element: '[data-tour="edit-course"]', popover: { title: 'Ubah detail kelas', description: 'Klik Edit kalau mau mengganti judul, harga, thumbnail, atau deskripsi.', side: 'left' } },
  { popover: { title: 'Kategori kelas', description: 'Menu "Course Categories" di samping dipakai untuk mengelompokkan kelas (mis. Personal Branding, Digital Marketing). Kategori muncul sebagai filter di halaman Kursus.' } }
]);
</script>
@endpush
