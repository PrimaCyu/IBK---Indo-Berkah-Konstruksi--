<?php

namespace App\Support;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class Terbilang
{
    private const WORDS = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima',
        'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
    ];

    public static function rupiah(string|int|float $amount): string
    {
        $decimal = BigDecimal::of((string) $amount)->abs()->toScale(2, RoundingMode::HalfUp);
        [$wholeStr, $fractionStr] = array_pad(explode('.', (string) $decimal, 2), 2, '00');

        $wholeWords = self::convertIntegerString($wholeStr);
        $result = trim($wholeWords).' rupiah';

        $cents = (int) $fractionStr;
        if ($cents > 0) {
            $result .= ' '.trim(self::convertSmallInt($cents)).' sen';
        }

        return ucwords(preg_replace('/\s+/', ' ', trim($result)));
    }

    public static function formatRupiah(string|int|float $amount, bool $withDecimals = false): string
    {
        $decimal = BigDecimal::of((string) $amount)->toScale(2, RoundingMode::HalfUp);
        $negative = $decimal->isNegative();
        [$whole, $fraction] = array_pad(explode('.', ltrim((string) $decimal, '-'), 2), 2, '00');
        $whole = ltrim($whole, '0') ?: '0';
        $formattedWhole = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $whole);
        $prefix = $negative && ($whole !== '0' || $fraction !== '00') ? '-Rp ' : 'Rp ';

        if ($withDecimals || $fraction !== '00') {
            return $prefix.$formattedWhole.','.$fraction;
        }

        return $prefix.$formattedWhole;
    }

    private static function convertIntegerString(string $digits): string
    {
        $digits = ltrim($digits, '0');
        if ($digits === '') {
            return 'nol';
        }

        $scales = ['', 'ribu', 'juta', 'miliar', 'triliun', 'kuadriliun'];
        $chunks = [];
        while ($digits !== '') {
            $chunks[] = (int) substr($digits, -3);
            $digits = substr($digits, 0, -3);
        }

        $parts = [];
        for ($i = count($chunks) - 1; $i >= 0; $i--) {
            $chunk = $chunks[$i];
            if ($chunk === 0) {
                continue;
            }
            if ($i === 1 && $chunk === 1) {
                $parts[] = 'seribu';
                continue;
            }
            $scale = $scales[$i] ?? '';
            $parts[] = trim(self::convertSmallInt($chunk).' '.$scale);
        }

        return implode(' ', $parts);
    }

    private static function convertSmallInt(int $number): string
    {
        if ($number < 12) {
            return self::WORDS[$number];
        }
        if ($number < 20) {
            return self::WORDS[$number - 10].' belas';
        }
        if ($number < 100) {
            $tens = intdiv($number, 10);
            $rem = $number % 10;

            return trim(self::WORDS[$tens].' puluh '.self::convertSmallInt($rem));
        }
        if ($number < 200) {
            return trim('seratus '.self::convertSmallInt($number - 100));
        }
        $hundreds = intdiv($number, 100);
        $rem = $number % 100;

        return trim(self::WORDS[$hundreds].' ratus '.self::convertSmallInt($rem));
    }
}
