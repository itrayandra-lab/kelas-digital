<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dokumentasi Sistem Ray Academy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1a1a2e;
        }

        /* Cover */
        .cover {
            text-align: center;
            padding: 120px 0 60px;
        }
        .cover .logo {
            font-size: 36px;
            font-weight: 800;
            color: #0056D2;
            margin-bottom: 8px;
        }
        .cover .subtitle {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 40px;
        }
        .cover .meta {
            font-size: 12px;
            color: #718096;
            margin-top: 60px;
        }
        .cover .line {
            width: 80px;
            height: 4px;
            background: #0056D2;
            margin: 20px auto;
            border-radius: 2px;
        }

        /* TOC */
        .toc { page-break-before: always; }
        .toc h2 {
            font-size: 20px;
            color: #0056D2;
            border-bottom: 2px solid #0056D2;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .toc ul { list-style: none; padding: 0; }
        .toc li {
            padding: 6px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 12px;
        }
        .toc li span.num {
            display: inline-block;
            width: 28px;
            height: 28px;
            background: #0056D2;
            color: #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 10px;
        }

        /* Sections */
        .section { page-break-before: always; }
        .section:first-of-type { page-break-before: avoid; }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0056D2;
        }
        .section-header .icon {
            font-size: 28px;
        }
        .section-header h2 {
            font-size: 20px;
            color: #0056D2;
            font-weight: 700;
        }

        .feature-card {
            background: #f8fafc;
            border-left: 4px solid #0056D2;
            padding: 12px 16px;
            margin-bottom: 14px;
            border-radius: 0 6px 6px 0;
        }
        .feature-card h3 {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .feature-card p {
            font-size: 11px;
            color: #4a5568;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }
        .footer span { margin: 0 10px; }

        @page {
            margin: 30px 35px 50px;
        }
    </style>
</head>
<body>

    {{-- Cover --}}
    <div class="cover">
        <div class="logo">RAY ACADEMY</div>
        <div class="subtitle">Dokumentasi Sistem — Admin Panel</div>
        <div class="line"></div>
        <div class="meta">
            <div>Ray Academy v1.0</div>
            <div>Dibuat: {{ $generated_at }}</div>
        </div>
    </div>

    {{-- TOC --}}
    <div class="toc">
        <h2>Daftar Isi</h2>
        <ul>
            @foreach($features as $i => $feature)
                <li>
                    <span class="num">{{ $i + 1 }}</span>
                    {{ $feature['icon'] }} {{ $feature['section'] }}
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Features --}}
    @foreach($features as $feature)
        <div class="section">
            <div class="section-header">
                <span class="icon">{{ $feature['icon'] }}</span>
                <h2>{{ $feature['section'] }}</h2>
            </div>

            @foreach($feature['items'] as $item)
                <div class="feature-card">
                    <h3>{{ $item['name'] }}</h3>
                    <p>{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        <span>Ray Academy</span>
        <span>|</span>
        <span>{{ $generated_at }}</span>
        <span>|</span>
        <span>Halaman {PAGE_NUM}</span>
    </div>

</body>
</html>