<?php

// RAYA, the AI learning advisor chat. Uses the same DeepSeek and Groq keys as essay grading (config/services.php).
return [
    // Tried in this order; a provider without an API key is skipped.
    'providers' => array_filter(explode(',', env('RAYA_PROVIDERS', 'deepseek,groq'))),

    'max_tokens' => (int) env('RAYA_MAX_TOKENS', 700),
    'temperature' => 0.5,
    'timeout' => 25,
    'connect_timeout' => 8,

    'history_turns' => 8,
    'context_chunks' => 6,
    'max_message_chars' => 800,

    // Cost and abuse protection.
    'limits' => [
        'per_minute' => (int) env('RAYA_PER_MINUTE', 8),
        'per_day' => (int) env('RAYA_PER_DAY', 120),
        'global_daily' => (int) env('RAYA_GLOBAL_DAILY', 2500),
    ],
];
