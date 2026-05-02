<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $number = abs((int) $number);
        
        if ($number == 0) {
            return 'Zero';
        }

        $words = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
        ];
        
        $tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
        ];

        $result = '';

        // Crore (10 million)
        if ($number >= 10000000) {
            $result .= self::convertTwoDigits((int)($number / 10000000)) . ' Crore ';
            $number %= 10000000;
        }

        // Lakh (100 thousand)
        if ($number >= 100000) {
            $result .= self::convertTwoDigits((int)($number / 100000)) . ' Lakh ';
            $number %= 100000;
        }

        // Thousand
        if ($number >= 1000) {
            $result .= self::convertTwoDigits((int)($number / 1000)) . ' Thousand ';
            $number %= 1000;
        }

        // Hundred
        if ($number >= 100) {
            $result .= $words[(int)($number / 100)] . ' Hundred ';
            $number %= 100;
        }

        // Remaining two digits
        if ($number > 0) {
            $result .= self::convertTwoDigits($number);
        }

        return trim($result);
    }

    private static function convertTwoDigits($number)
    {
        $words = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
        ];
        
        $tens = [
            '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
        ];

        if ($number < 20) {
            return $words[$number];
        }

        return $tens[(int)($number / 10)] . ' ' . $words[$number % 10];
    }
}