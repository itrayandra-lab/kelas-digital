<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Turns the paragraphs of a test document into questions. The format follows the RayAcademy test template:
 *
 *   1. Question text            numbered multiple-choice question
 *   A. Option ... D. Option     its options
 *   Kunci: B                    the answer, or a key table with "No | Kunci" columns anywhere after
 *   SOAL ESSAY 1: ...           an essay (post-test only), everything up to the next essay or answer section
 *   PANDUAN JAWABAN IDEAL ESSAY 1 ...   the essay's answer guide, used by the AI grader
 */
class QuestionImport
{
    /**
     * @return array<int, array{type: string, question: string, options?: array<int, array{text: string, is_correct: bool}>, answer_key?: string}>
     */
    public static function parse(array $paragraphs, string $kind): array
    {
        $choices = $keys = $essays = $guides = [];
        $mode = null;
        $current = null;
        $paragraphs = array_values($paragraphs);

        foreach ($paragraphs as $i => $text) {
            if (preg_match('/^SOAL\s+ESSAY\s+(\d+)/i', $text, $m)) {
                [$mode, $current] = ['essay', (int) $m[1]];
                $essays[$current] = [$text];

                continue;
            }
            if (preg_match('/^PANDUAN\s+JAWABAN\b.*?\bESSAY\s+(\d+)/i', $text, $m)) {
                [$mode, $current] = ['guide', (int) $m[1]];
                $guides[$current] = [$text];

                continue;
            }
            if (preg_match('/^(KUNCI\s+JAWABAN|PANDUAN\s+JAWABAN)/i', $text)) {
                [$mode, $current] = ['keys', null];

                continue;
            }
            if ($mode === 'essay') {
                $essays[$current][] = $text;

                continue;
            }
            if ($mode === 'guide') {
                $guides[$current][] = $text;

                continue;
            }

            // A key table row: one cell with the number, the next with the letter.
            if (preg_match('/^\d+$/', $text) && preg_match('/^[A-H]$/i', $paragraphs[$i + 1] ?? '')) {
                $keys[(int) $text] = strtoupper($paragraphs[$i + 1]);

                continue;
            }
            if ($mode === 'keys') {
                continue;
            }
            if ($current !== null && preg_match('/^Kunci(?:\s+jawaban)?\s*:\s*([A-H])\b/i', $text, $m)) {
                $keys[$current] = strtoupper($m[1]);

                continue;
            }
            if (preg_match('/^(\d+)[.)]\s+(.+)$/s', $text, $m)) {
                $current = (int) $m[1];
                $choices[$current] = ['question' => trim($m[2]), 'options' => []];

                continue;
            }
            if ($current !== null && isset($choices[$current]) && preg_match('/^([A-H])[.)]\s+(.+)$/s', $text, $m)) {
                $choices[$current]['options'][strtoupper($m[1])] = trim($m[2]);
            }
        }

        $problems = [];
        $questions = [];

        foreach ($choices as $no => $q) {
            $key = $keys[$no] ?? null;

            if (count($q['options']) < 2) {
                $problems[] = "Soal no. {$no}: opsi jawaban (A., B., ...) tidak ditemukan.";
            } elseif (! $key || ! isset($q['options'][$key])) {
                $problems[] = "Soal no. {$no}: kunci jawaban tidak ditemukan atau tidak cocok dengan opsinya.";
            }

            $questions[] = [
                'type' => 'single',
                'question' => $q['question'],
                'options' => collect($q['options'])->map(fn ($text, $letter) => ['text' => $text, 'is_correct' => $letter === $key])->values()->all(),
            ];
        }

        foreach ($essays as $no => $lines) {
            if ($kind === 'pre') {
                $problems[] = 'Pre-test tidak boleh berisi essay. Pindahkan essay ke post-test.';

                break;
            }
            if (empty($guides[$no])) {
                $problems[] = "Essay {$no}: bagian \"PANDUAN JAWABAN IDEAL ESSAY {$no}\" tidak ditemukan. AI butuh panduan ini untuk menilai.";
            }

            $questions[] = ['type' => 'essay', 'question' => implode("\n", $lines), 'answer_key' => implode("\n", $guides[$no] ?? [])];
        }

        if (! $questions) {
            $problems[] = 'Tidak ada soal yang terbaca. Pastikan soal bernomor "1. ..." dengan opsi "A. ...", atau essay berjudul "SOAL ESSAY 1: ...".';
        }

        if ($problems) {
            throw new InvalidArgumentException(implode(' ', $problems));
        }

        return $questions;
    }
}
