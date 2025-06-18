<?php

use Illuminate\Support\Facades\Config;

class Helper
{
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
}
