<?php

namespace App\Helpers;

class Utils
{
    public static function getTipoPropriedade($codigo)
    {
        $tipos = [
            "Re" => "Residencial",
            "Co" => "Comercial",
            "In" => "Industrial",
            "Mi" => "Misto",
            "Fa" => "Fazenda",
            "Si" => "Sítio",
            "Ch" => "Chácara",
            "He" => "Herdade",
            "Te" => "Terreno",
            "Ar" => "Armazém/Depósito"
        ];

        return $tipos[$codigo] ?? "Tipo desconhecido";
    }

    // Função nova para gerar o link embed
    public static function getYouTubeEmbedUrl($youtubeUrl)
    {
        // Parse a URL para pegar o parâmetro "v"
        parse_str(parse_url($youtubeUrl, PHP_URL_QUERY), $urlParams);

        // Verifica se a chave "v" existe e retorna o código de incorporação
        return isset($urlParams['v']) ? 'https://www.youtube.com/embed/' . $urlParams['v'] : null;
    }
}
