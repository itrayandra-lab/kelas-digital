{{-- The one call-to-action for a course, shared by the sticky card and the mobile bottom bar. --}}
@php
    // A started checkout leaves the enrollment pending; "Mulai gratis" rows stay active until then.
    $isPending = $userEnrollment && $userEnrollment->payment_status === 'pending' && $userEnrollment->status === 'pending' && ! $course->isFreeClass();
    $partlyFree = $course->isPartlyFree();
@endphp

@guest
    <a href="{{ route('login') }}" class="cta-btn">{{ $course->isFreeClass() ? 'Login untuk Daftar Gratis' : ($partlyFree ? 'Login untuk Mulai Gratis' : 'Login untuk Membeli') }}</a>
@else
    @if($progress?->reviewer)
        <a href="{{ route('course.learn', $course->slug) }}" class="cta-btn"><i class="fas fa-eye"></i> Lihat Isi Kelas</a>
    @elseif($userHasAccess)
        @if($progress?->isCourseFinished())
            <a href="{{ route('course.completed', $course->slug) }}" class="cta-btn cta-success"><i class="fas fa-trophy"></i> Lihat Hasil</a>
        @else
            <a href="{{ route('course.learn', $course->slug) }}" class="cta-btn"><i class="fas fa-play"></i> {{ ($progress?->percent() ?? 0) > 0 ? 'Lanjut Belajar' : 'Mulai Belajar' }}</a>
        @endif
    @elseif($course->isFreeClass())
        <form method="POST" action="{{ route('course.enroll', $course->slug) }}" style="margin:0;">
            @csrf
            <button type="submit" class="cta-btn">Daftar Gratis</button>
        </form>
    @elseif($partlyFree)
        @if($progress)
            <a href="{{ route('course.learn', $course->slug) }}" class="cta-btn"><i class="fas fa-play"></i> {{ $progress->percent() > 0 ? 'Lanjut Belajar' : 'Mulai Belajar' }}</a>
        @else
            <form method="POST" action="{{ route('course.enroll', $course->slug) }}" style="margin:0;">
                @csrf
                <button type="submit" class="cta-btn">Mulai Gratis</button>
            </form>
        @endif
        <button type="button" class="cta-btn cta-outline btn-pay" data-slug="{{ $course->slug }}">{{ $isPending ? 'Selesaikan Pembayaran' : 'Beli Kelas Lengkap' }}</button>
    @elseif($isPending)
        <button type="button" class="cta-btn cta-warn btn-pay" data-slug="{{ $course->slug }}"><i class="fas fa-credit-card"></i> Selesaikan Pembayaran</button>
    @else
        <button type="button" class="cta-btn btn-pay" data-slug="{{ $course->slug }}">Beli Sekarang</button>
    @endif
@endguest
