<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dokumentasi Sistem Ray Academy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
        }

        /* Cover page */
        .cover {
            text-align: center;
            padding: 140px 0 60px;
            page-break-after: always;
        }
        .cover h1 {
            font-size: 32pt;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #000;
        }
        .cover .line {
            width: 60px;
            height: 3px;
            background: #000;
            margin: 18px auto;
        }
        .cover .subtitle {
            font-size: 14pt;
            color: #444;
            margin-bottom: 6px;
        }
        .cover .meta {
            font-size: 9pt;
            color: #666;
            margin-top: 50px;
        }

        /* TOC */
        .toc {
            page-break-after: always;
        }
        .toc h2 {
            font-size: 16pt;
            font-weight: 700;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
            text-transform: uppercase;
        }
        .toc-list {
            list-style: none;
            padding: 0;
        }
        .toc-list li {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 10pt;
        }
        .toc-list li .num {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 1px solid #000;
            text-align: center;
            line-height: 22px;
            font-size: 9pt;
            font-weight: 700;
            margin-right: 8px;
        }

        /* Sections */
        .section {
            page-break-before: always;
        }
        .section:first-of-type {
            page-break-before: avoid;
        }

        .section-header {
            font-size: 16pt;
            font-weight: 700;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 18px;
            text-transform: uppercase;
        }

        .feature-card {
            margin-bottom: 18px;
            border: 1px solid #ccc;
            padding: 12px 14px;
            page-break-inside: avoid;
        }
        .feature-card h3 {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .feature-card .desc {
            font-size: 9.5pt;
            color: #333;
            margin-bottom: 6px;
        }
        .feature-card .howto-label {
            font-size: 9pt;
            font-weight: 700;
            margin-top: 6px;
            color: #000;
        }
        .feature-card .howto {
            font-size: 9pt;
            color: #444;
            margin-bottom: 6px;
        }
        .feature-card .notes {
            font-size: 8.5pt;
            color: #666;
            font-style: italic;
            margin-top: 4px;
            padding: 4px 6px;
            background: #f5f5f5;
        }

        /* Field tables */
        .fields-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 8.5pt;
        }
        .fields-table th {
            background: #000;
            color: #fff;
            padding: 5px 6px;
            text-align: left;
            font-weight: 700;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .fields-table td {
            padding: 4px 6px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        .fields-table tr:nth-child(even) td {
            background: #f9f9f9;
        }
        .fields-table .req {
            font-weight: 700;
        }
        .fields-table .req-y {
            color: #000;
            font-weight: 700;
        }
        .fields-table .req-n {
            color: #888;
        }
        .fields-table .opts {
            font-size: 7.5pt;
            color: #666;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #888;
            padding: 8px 0;
            border-top: 1px solid #ccc;
        }
        .footer span { margin: 0 8px; }

        @page {
            margin: 30px 30px 55px;
        }
    </style>
</head>
<body>

    {{-- Cover --}}
    <div class="cover">
        <h1>Ray Academy</h1>
        <div class="line"></div>
        <div class="subtitle">Dokumentasi Sistem Admin Panel</div>
        <div class="subtitle" style="font-size:11pt;color:#666;">Ray Academy v1.0</div>
        <div class="meta">
            <div>Dokumen ini berisi panduan lengkap penggunaan fitur-fitur</div>
            <div>yang tersedia di panel admin Ray Academy.</div>
            <div style="margin-top:20px;">Dibuat: {{ $generated_at }}</div>
        </div>
    </div>

    {{-- TOC --}}
    <div class="toc">
        <h2>Daftar Isi</h2>
        <ol class="toc-list">
            @foreach($features as $i => $feature)
                <li><span class="num">{{ $i + 1 }}</span>{{ $feature['section'] }}</li>
            @endforeach
        </ol>
    </div>

    {{-- Features --}}
    @foreach($features as $feature)
        <div class="section">
            <div class="section-header">{{ $feature['section'] }}</div>

            @foreach($feature['items'] as $item)
                <div class="feature-card">
                    <h3>{{ $item['name'] }}</h3>
                    <div class="desc">{{ $item['desc'] }}</div>

                    @if(!empty($item['howto']))
                        <div class="howto-label">Cara Penggunaan:</div>
                        <div class="howto">{{ $item['howto'] }}</div>
                    @endif

                    @if(!empty($item['fields']))
                        <table class="fields-table">
                            <thead>
                                <tr>
                                    <th style="width:22%;">Field</th>
                                    <th style="width:10%;">Wajib</th>
                                    <th style="width:10%;">Tipe</th>
                                    <th style="width:48%;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item['fields'] as $field)
                                    <tr>
                                        <td class="req">{{ $field['label'] }}</td>
                                        <td class="{{ $field['required'] ? 'req-y' : 'req-n' }}">
                                            {{ $field['required'] ? 'Ya' : 'Tidak' }}
                                        </td>
                                        <td>{{ $field['type'] }}</td>
                                        <td>
                                            {{ $field['desc'] }}
                                            @if(!empty($field['max']))
                                                <br><span class="opts">Max: {{ $field['max'] }} karakter</span>
                                            @endif
                                            @if(!empty($field['min']))
                                                <br><span class="opts">Min: {{ $field['min'] }} karakter</span>
                                            @endif
                                            @if(!empty($field['options']))
                                                <br><span class="opts">Pilihan: {{ implode(', ', $field['options']) }}</span>
                                            @endif
                                            @if(!empty($field['if']))
                                                <br><span class="opts">(hanya jika {{ $field['if'] }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if(!empty($item['notes']))
                        <div class="notes">{{ $item['notes'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        <span>Ray Academy — Dokumentasi Sistem</span>
        <span>|</span>
        <span>{{ $generated_at }}</span>
        <span>|</span>
        <span>Halaman {PAGE_NUM}</span>
    </div>

</body>
</html>