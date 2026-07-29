<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Validation\Validator;

class Helper
{
    public static function ToIso(?string $value): ?string
    {
        return $value !== null ? mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8') : null;
    }

    public static function ConvertFormatText($results)
    {
        // Converter cada objeto ou array e manter o tipo objeto no retorno
        $results = array_map(function ($result) {
            // Converte valores individuais para UTF-8
            $converted = array_map(function ($value) {
                return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
            }, (array) $result);

            // Retorna como objeto
            return (object) $converted;
        }, $results);

        return $results;
    }

    public static function RemoveSpecialChar($str)
    {
        return preg_replace('/[@\.\;\_\ \&\/\-\(\)]+/', '', $str);
    }

    public static function is_empty_object($object)
    {
        foreach ($object as $o) return false;

        return true;
    }
    public static function formatDateMysql($value)
    {
        //$value = date('d/m/Y', $value);
        // Utiliza a classe de Carbon para converter ao formato de data ou hora desejado
        return Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
    public static function formatDateHour($value)
    {
        // 2025-03-03 16:08:50
        // Utiliza a classe de Carbon para converter ao formato de data ou hora desejado
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i:s');
    }
    public static function formatDate($value)
    {
        // 2025-03-03 16:08:50
        // Utiliza a classe de Carbon para converter ao formato de data ou hora desejado
        return Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
    public static function codigoBarrasHtml($codigo_barras)
    {
        $codigo_barras = (strlen($codigo_barras) % 2 != 0 ? '0' : '') . $codigo_barras;
        $barcodes = ['00110', '10001', '01001', '11000', '00101', '10100', '01100', '00011', '10010', '01010'];
        for ($f1 = 9; $f1 >= 0; $f1--) {
            for ($f2 = 9; $f2 >= 0; $f2--) {
                $f = ($f1 * 10) + $f2;
                $texto = "";
                for ($i = 1; $i < 6; $i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
                }
                $barcodes[$f] = $texto;
            }
        }

        // Guarda inicial
        $retorno = '<div class="barcode">' .
            '<div class="black thin"></div>' .
            '<div class="white thin"></div>' .
            '<div class="black thin"></div>' .
            '<div class="white thin"></div>';

        // Draw dos dados
        while (strlen($codigo_barras) > 0) {
            $i = round(substr($codigo_barras, 0, 2));
            $codigo_barras = substr($codigo_barras, strlen($codigo_barras) - (strlen($codigo_barras) - 2), strlen($codigo_barras) - 2);
            $f = $barcodes[$i];
            for ($i = 1; $i < 11; $i += 2) {
                if (substr($f, ($i - 1), 1) == "0") {
                    $f1 = 'thin';
                } else {
                    $f1 = 'large';
                }
                $retorno .= "<div class='black {$f1}'></div>";
                if (substr($f, $i, 1) == "0") {
                    $f2 = 'thin';
                } else {
                    $f2 = 'large';
                }
                $retorno .= "<div class='white {$f2}'></div>";
            }
        }

        // Final
        return $retorno . '<div class="black large"></div>' .
            '<div class="white thin"></div>' .
            '<div class="black thin"></div>' .
            '</div>';
    }

    public static function formatErrorsAsHtml(Validator $validator)
    {
        $error = '<ul>';
        foreach ($validator->errors()->all() as $e) {
            $error .= '<li>' . $e . '</li>';
        }
        $error .= '</ul>';
        return response()->json(
            [
                'success' => false,
                'errors' => $error
            ]
        );
    }
}
