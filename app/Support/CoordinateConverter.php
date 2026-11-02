<?php

namespace App\Support;

class CoordinateConverter
{
    /**
     * Convert UTM zone 49S coordinates on WGS84 to latitude and longitude.
     *
     * @return array{latitude: float, longitude: float}
     */
    public static function utm49SToLatitudeLongitude(float $easting, float $northing): array
    {
        $semiMajorAxis = 6378137.0;
        $eccentricitySquared = 0.00669438;
        $scaleFactor = 0.9996;
        $eccentricityPrimeSquared = $eccentricitySquared / (1 - $eccentricitySquared);
        $x = $easting - 500000.0;
        $y = $northing - 10000000.0;
        $meridionalArc = $y / $scaleFactor;
        $mu = $meridionalArc / ($semiMajorAxis * (
            1 - $eccentricitySquared / 4
            - 3 * $eccentricitySquared ** 2 / 64
            - 5 * $eccentricitySquared ** 3 / 256
        ));
        $eccentricity = (1 - sqrt(1 - $eccentricitySquared)) / (1 + sqrt(1 - $eccentricitySquared));
        $e1Squared = $eccentricity ** 2;
        $e1Cubed = $eccentricity ** 3;
        $e1Fourth = $eccentricity ** 4;
        $footprintLatitude = $mu
            + (3 * $eccentricity / 2 - 27 * $e1Cubed / 32) * sin(2 * $mu)
            + (21 * $e1Squared / 16 - 55 * $e1Fourth / 32) * sin(4 * $mu)
            + (151 * $e1Cubed / 96) * sin(6 * $mu)
            + (1097 * $e1Fourth / 512) * sin(8 * $mu);
        $sinLatitude = sin($footprintLatitude);
        $cosLatitude = cos($footprintLatitude);
        $tanLatitude = tan($footprintLatitude);
        $primeVerticalRadius = $semiMajorAxis / sqrt(1 - $eccentricitySquared * $sinLatitude ** 2);
        $tangentSquared = $tanLatitude ** 2;
        $curvatureTerm = $eccentricityPrimeSquared * $cosLatitude ** 2;
        $meridionalRadius = $semiMajorAxis * (1 - $eccentricitySquared)
            / (1 - $eccentricitySquared * $sinLatitude ** 2) ** 1.5;
        $normalizedEasting = $x / ($primeVerticalRadius * $scaleFactor);

        $latitude = $footprintLatitude - ($primeVerticalRadius * $tanLatitude / $meridionalRadius) * (
            $normalizedEasting ** 2 / 2
            - (5 + 3 * $tangentSquared + 10 * $curvatureTerm
                - 4 * $curvatureTerm ** 2 - 9 * $eccentricityPrimeSquared)
                * $normalizedEasting ** 4 / 24
            + (61 + 90 * $tangentSquared + 298 * $curvatureTerm
                + 45 * $tangentSquared ** 2 - 252 * $eccentricityPrimeSquared
                - 3 * $curvatureTerm ** 2)
                * $normalizedEasting ** 6 / 720
        );
        $longitude = deg2rad(111.0) + (
            $normalizedEasting
            - (1 + 2 * $tangentSquared + $curvatureTerm) * $normalizedEasting ** 3 / 6
            + (5 - 2 * $curvatureTerm + 28 * $tangentSquared
                - 3 * $curvatureTerm ** 2 + 8 * $eccentricityPrimeSquared
                + 24 * $tangentSquared ** 2)
                * $normalizedEasting ** 5 / 120
        ) / $cosLatitude;

        return [
            'latitude' => rad2deg($latitude),
            'longitude' => rad2deg($longitude),
        ];
    }
}
