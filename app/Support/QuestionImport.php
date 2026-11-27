<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Turns the paragraphs of a test document into questions. Two formats are read: the assessment package (see
 * parsePackage) and the original RayAcademy test template:
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
        $paragraphs = array_values($paragraphs);

        if (preg_grep('/^SOAL\s+Q\d+\b/i', $paragraphs)) {
            return self::parsePackage($paragraphs, $kind);
        }

        $choices = $keys = $essays = $guides = [];
        $mode = null;
        $current = null;

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

        return self::build($choices, $keys, $essays, $guides, $kind);
    }

    /**
     * The RayAcademy assessment package (2026):
     *
     *   SOAL Q01 · TOPIC (Alokasi: 60 Detik)   then the question in the next paragraph, or "Soal Q01: question" in one
     *   [ A ]  Option ... [ D ]  Option
     *   EVALUASI Q01 · KUNCI: [ A ]            or "• Q01 (Kunci: A · Modul: ...)", in the evaluator part
     *   SOAL ESAI E01 ...                      an essay, up to the next essay or "BAGIAN II"
     *   PANDUAN EVALUASI ESAI E01 ...          its answer guide; the "Rubrik Baku" table is added to every guide
     */
    private static function parsePackage(array $paragraphs, string $kind): array
    {
        $choices = $keys = $essays = $guides = $rubric = [];
        $mode = null;
        $current = null;

        foreach ($paragraphs as $text) {
            if (preg_match('/^(?:•\s*)?(?:EVALUASI\s+)?Q(\d+)\b.{0,40}?KUNCI\s*:\s*\[?\s*([A-H])\b/iu', $text, $m)) {
                $keys[(int) $m[1]] = strtoupper($m[2]);

                continue;
            }
            if (preg_match('/^SOAL\s+ESA[IY]\s+E?(\d+)/i', $text, $m)) {
                [$mode, $current] = ['essay', (int) $m[1]];
                $essays[$current] = [$text];

                continue;
            }
            if (preg_match('/^PANDUAN\s+EVALUASI\s+ESA[IY]\s+E?(\d+)/i', $text, $m)) {
                [$mode, $current] = ['guide', (int) $m[1]];
                $guides[$current] = [$text];

                continue;
            }
            if (preg_match('/^Rubrik\s+Baku/i', $text)) {
                $mode = 'rubric';
                $rubric[] = $text;

                continue;
            }
            if (preg_match('/^(BAGIAN\s+II\b|Skema\s+Kontrak)/i', $text)) {
                $mode = 'skip';

                continue;
            }

            match ($mode) {
                'essay' => $essays[$current][] = $text,
                'guide' => $guides[$current][] = $text,
                'rubric' => $rubric[] = $text,
                'skip' => null,
                default => self::readChoiceLine($text, $choices, $current),
            };
        }

        foreach ($guides as $no => $lines) {
            $guides[$no] = array_merge($lines, $rubric);
        }

        return self::build($choices, $keys, $essays, $guides, $kind);
    }

    private static function readChoiceLine(string $text, array &$choices, ?int &$current): void
    {
        if (preg_match('/^SOAL\s+Q(\d+)\b(?:\s*:\s*(.+))?/is', $text, $m)) {
            $current = (int) $m[1];
            $choices[$current] = ['question' => trim($m[2] ?? ''), 'options' => []];
        } elseif ($current !== null && preg_match('/^\[\s*([A-H])\s*\]\s*(.+)$/s', $text, $m)) {
            $choices[$current]['options'][strtoupper($m[1])] = trim($m[2]);
        } elseif ($current !== null && ! $choices[$current]['options']) {
            // The question text follows its "SOAL Q01 · TOPIC" header.
            $choices[$current]['question'] = trim($choices[$current]['question']."\n".$text);
        }
    }

    private static function build(array $choices, array $keys, array $essays, array $guides, string $kind): array
    {
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
