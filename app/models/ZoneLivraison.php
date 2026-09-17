<?php
class ZoneLivraison
{
    // Coordonnées du centre de Bordeaux (référence, pas de majoration ici)
    private const LAT_BORDEAUX = 44.837789;
    private const LNG_BORDEAUX = -0.579180;

    // Rayon maximal de livraison en km — au-delà : zone non desservie
    private const MAX_DISTANCE_KM = 80;

    /**
     * Géocode une adresse complète (adresse + code postal) via l'API Adresse
     * (api-adresse.data.gouv.fr, gratuite, sans clé) et calcule la distance
     * par rapport au centre de Bordeaux.
     *
     * @return array|null ['distance_km','ville','zone_desservie','lat','lng'] ou null si non géocodable
     */
    public function getDistanceByAdresse(string $adresse, string $codePostal): ?array
    {
        $requete = trim($adresse . ' ' . $codePostal);

        $feature = $this->geocoder($requete);

        if ($feature === null) {
            return null;
        }

        // Score de pertinence renvoyé par l'API (0 à 1) : filtre les résultats peu fiables
        $score = $feature['properties']['score'] ?? 0;
        if ($score < 0.3) {
            return null;
        }

        $lng = (float)$feature['geometry']['coordinates'][0];
        $lat = (float)$feature['geometry']['coordinates'][1];
        $ville = $feature['properties']['city'] ?? '';

        $distance = $this->calculerDistanceHaversine(self::LAT_BORDEAUX, self::LNG_BORDEAUX, $lat, $lng);

        return [
            'distance_km'    => round($distance, 2),
            'ville'          => $ville,
            'zone_desservie' => $distance <= self::MAX_DISTANCE_KM,
            'lat'            => $lat,
            'lng'            => $lng,
        ];
    }

    //  Appel HTTP à l'API Adresse via cURL (plus fiable que file_get_contents, ne dépend pas d'allow_url_fopen).
     
    private function geocoder(string $requete): ?array
    {
        $url = 'https://api-adresse.data.gouv.fr/search/?' . http_build_query([
            'q'            => $requete,
            'limit'        => 1,
            'autocomplete' => 0,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_USERAGENT      => 'VITEGourmand/1.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false || $httpCode !== 200) {
            error_log("Geocodage echoue (HTTP $httpCode) pour la requete : $requete");
            return null;
        }

        $data = json_decode($response, true);

        return $data['features'][0] ?? null;
    }

    // Formule de Haversine : distance orthodromique entre 2 points (km)
     
    private function calculerDistanceHaversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $rayonTerre = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $rayonTerre * $c;
    }

    //  Bordeaux même = pas de majoration de livraison
     
    public function estBordeaux(string $ville): bool
    {
        return mb_strtolower(trim($ville)) === 'bordeaux';
    }
}