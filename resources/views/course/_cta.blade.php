{{-- The one call-to-action for a course, shared by the sticky card and the mobile bottom bar. --}}
@php $isPending = $userEnrollment && $userEnrollment->payment_status === 'pending' && ! $course->isFreeClass(); @endphp

@guest
    <a href="{{ route('login') }}" class="cta-btn">{{ $course->isFreeClass() ? 'Login untuk Daftar Gratis' : 'Login untuk Membeli' }}</a>
@else
    @if($userHasAccess)
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
    @elseif($isPending)
        <button type="button" class="cta-btn cta-warn btn-pay" data-slug="{{ $course->slug }}"><i class="fas fa-credit-card"></i> Selesaikan Pembayaran</button>
    @else
        <button type="button" class="cta-btn btn-pay" data-slug="{{ $course->slug }}">Beli Sekarang</button>
    @endif
@endguest
