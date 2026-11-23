<?php

/*
 * Homepage hero slides, in order. Text, colors and button links live here so they can be changed without
 * touching the page. `image` is the number of public/img/hero/hero-{n}.webp (plus hero-{n}-m.webp for phones);
 * when the file is missing the slide falls back to a plain navy background.
 *
 * align: which side the text sits on. theme: "dark" (white text) or "light" (navy text).
 * accent: color of the small bar and of every heading line after the first. italic: slant those lines.
 * button: pill style, one of white, navy, rose, orange, green. arrow: show → after the button text.
 */
return [
    'hero' => [
        [
            'image' => 1, 'align' => 'left', 'theme' => 'dark', 'accent' => '#38c8f5', 'italic' => true, 'bar' => '#f5b544',
            'label' => 'WELCOME TO RAYACADEMY',
            'lines' => ['WHERE KNOWLEDGE', 'BECOMES CAPABILITY.'],
            'text' => 'RayAcademy adalah ekosistem pendidikan dan pengembangan kompetensi bagi individu, profesional, dan perusahaan. Jelajahi Sekolah Kosmetik Indonesia untuk pendidikan industri kosmetik, doBetter Class untuk branding dan marketing, amAIzing Class untuk pembelajaran dan penerapan Artificial Intelligence (AI), serta LevelUp Class untuk entrepreneurship dan pengembangan diri.',
            'cta' => 'Kenali RayAcademy', 'href' => '#program', 'button' => 'white', 'focus' => '72% center', 'arrow' => true,
        ],
        [
            'image' => 2, 'align' => 'left', 'theme' => 'light', 'accent' => '#4a86b8', 'italic' => false, 'bar' => '#c98a4b',
            'label' => 'INTELLIGENT LEARNING EXPERIENCE',
            'lines' => ['A SMARTER WAY', 'TO LEARN'],
            'text' => 'Di RayAcademy, pembelajaran tidak berhenti pada materi. AI Smart Book, interaksi bersama pakar, dan pengalaman praktik membantu mengubah pengetahuan menjadi kemampuan nyata.',
            'cta' => 'Kenali Cara Belajar', 'href' => '#faq', 'button' => 'navy', 'focus' => '75% center',
            'cards' => [['icon' => 'fa-book-open', 'label' => 'AI Smart Book'], ['icon' => 'fa-flask', 'label' => 'Interaksi Pakar dan Praktik']],
            'steps' => [['icon' => 'fa-book-open', 'label' => 'LEARN'], ['icon' => 'fa-magnifying-glass', 'label' => 'EXPLORE'], ['icon' => 'fa-comments', 'label' => 'DISCUSS'], ['icon' => 'fa-flask', 'label' => 'APPLY']],
        ],
        [
            'image' => 3, 'align' => 'right', 'theme' => 'dark', 'accent' => '#f3b7a8', 'italic' => true, 'bar' => '#f3b7a8',
            'label' => 'SEKOLAH KOSMETIK INDONESIA · RAYACADEMY',
            'lines' => ['LEARN COSMETIC SCIENCE.', 'UNDERSTAND THE INDUSTRY.'],
            'text' => 'Pelajari sains kosmetik, formulasi, pengembangan produk, dan proses manufaktur bersama akademisi, peneliti, serta praktisi industri. Melalui Sekolah Kosmetik Indonesia di RayAcademy, kembangkan pengetahuan dan keterampilan yang relevan dengan kebutuhan profesional dan perkembangan industri kosmetik.',
            'cta' => 'Jelajahi Sekolah Kosmetik Indonesia', 'href' => '/courses', 'button' => 'rose', 'arrow' => true, 'focus' => '22% center',
        ],
        [
            'image' => 4, 'align' => 'right', 'theme' => 'dark', 'accent' => '#38c8f5', 'italic' => true, 'bar' => '#f5b544',
            'label' => 'amAIzing CLASS · BY RAYACADEMY',
            'lines' => ['MAKE AI', 'WORK FOR YOU.'],
            'text' => 'amAIzing Class dari RayAcademy menghadirkan pembelajaran Artificial Intelligence (AI) yang praktis bagi individu, profesional, dan perusahaan. Pelajari cara menggunakan AI untuk riset, analisis informasi, pembuatan konten, penyusunan strategi, hingga otomatisasi pekerjaan—agar AI dapat membantu menyelesaikan tugas nyata dalam pekerjaan dan bisnis di berbagai industri.',
            'cta' => 'Jelajahi amAIzing Class', 'href' => '/courses?category=technology-ai', 'button' => 'white', 'focus' => '18% center', 'arrow' => true,
        ],
        [
            'image' => 5, 'align' => 'right', 'theme' => 'dark', 'accent' => '#f5a04a', 'italic' => false, 'bar' => '#f5a04a',
            'label' => 'doBetter CLASS · BY RAYACADEMY',
            'lines' => ['THINK BETTER.', 'BUILD STRONGER BRANDS.', 'CREATE BETTER MARKETING.'],
            'text' => 'doBetter Class dari RayAcademy menghadirkan pembelajaran branding dan marketing bagi individu, profesional, entrepreneur, dan perusahaan. Pelajari cara melakukan riset pasar, memahami perilaku konsumen, membangun positioning brand, serta merancang strategi dan campaign pemasaran berdasarkan insight konsumen dan bukti pasar.',
            'cta' => 'Jelajahi doBetter Class', 'href' => '/courses?category=digital-marketing', 'button' => 'orange', 'arrow' => true, 'focus' => '22% center',
        ],
        [
            'image' => 6, 'align' => 'left', 'theme' => 'dark', 'accent' => '#a9c779', 'italic' => false, 'bar' => '#a9c779',
            'label' => 'LevelUp CLASS · BY RAYACADEMY',
            'lines' => ['GROW YOUR SKILLS.', 'LEAD YOUR FUTURE.'],
            'text' => 'LevelUp Class dari RayAcademy menghadirkan pembelajaran kewirausahaan, pengembangan usaha, leadership, dan keterampilan profesional bagi pelaku usaha, profesional, serta individu yang ingin berkembang. Pelajari cara merencanakan dan mengelola bisnis, memimpin tim, membangun komunikasi yang efektif, serta mengambil keputusan dalam menghadapi tantangan pekerjaan dan usaha.',
            'cta' => 'Jelajahi LevelUp Class', 'href' => '/courses?category=business-strategy', 'button' => 'green', 'arrow' => true, 'focus' => '75% center',
        ],
        [
            'image' => 7, 'align' => 'left', 'theme' => 'light', 'accent' => '#4a86b8', 'italic' => false, 'bar' => '#c98a4b',
            'label' => 'INDIVIDUAL & PROFESSIONAL EDUCATION',
            'lines' => ['FROM KNOWLEDGE', 'TO EXPERTISE'],
            'text' => 'Kembangkan pengetahuan dan keahlian di bidang kosmetik melalui pembelajaran bertahap, dari Beginner hingga Expert, bersama akademisi dan praktisi industri.',
            'cta' => 'Temukan Kelas Anda', 'href' => '/courses', 'button' => 'navy', 'focus' => '75% center',
            'journey' => ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
        ],
    ],
];
