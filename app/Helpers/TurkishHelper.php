<?php

namespace App\Helpers;

class TurkishHelper
{
    /**
     * Türkçe bulunma hali eki (locative suffix) hesapla
     * Sesli uyumu + sessiz uyumu kurallarına göre
     *
     * Örnekler:
     *   turkishLocativeSuffix('Kadıköy')  → "'de"
     *   turkishLocativeSuffix('Beşiktaş') → "'ta"
     *   turkishLocativeSuffix('Beyoğlu')  → "'nda"
     *   turkishLocativeSuffix('Fatih')    → "'te"
     */
    public static function locativeSuffix(string $name): string
    {
        $name = trim($name);
        $lastChar = mb_substr($name, -1, 1, 'UTF-8');
        $lower = mb_strtolower($name, 'UTF-8');

        // Son ünlüyü bul (sesli uyumu için)
        $backVowels = ['a', 'ı', 'o', 'u'];
        $frontVowels = ['e', 'i', 'ö', 'ü'];
        $allVowels = array_merge($backVowels, $frontVowels);

        $lastVowel = '';
        $len = mb_strlen($lower, 'UTF-8');
        for ($i = $len - 1; $i >= 0; $i--) {
            $ch = mb_substr($lower, $i, 1, 'UTF-8');
            if (in_array($ch, $allVowels)) {
                $lastVowel = $ch;
                break;
            }
        }

        // Sesli uyumu: son ünlü kalın → a, ince → e
        $vowelPart = in_array($lastVowel, $backVowels) ? 'a' : 'e';

        // Son harf ünlü mü? (buffer 'n' gerekir)
        $lastCharLower = mb_strtolower($lastChar, 'UTF-8');
        $needsBuffer = in_array($lastCharLower, $allVowels);

        // Sessiz uyumu: p, ç, t, k, f, h, s, ş → t, diğer → d
        $hardConsonants = ['p', 'ç', 't', 'k', 'f', 'h', 's', 'ş'];
        $consonantPart = in_array($lastCharLower, $hardConsonants) ? 't' : 'd';

        if ($needsBuffer) {
            // Ünlüyle biten: 'nda / 'nde
            return "'n" . $consonantPart . $vowelPart;
        }

        return "'" . $consonantPart . $vowelPart;
    }
}
