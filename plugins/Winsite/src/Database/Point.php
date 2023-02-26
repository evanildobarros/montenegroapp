<?php
declare(strict_types=1);

namespace Winsite\Database;

/**
 * Class Point
 */
class Point
{
    /**
     * @var float
     */
    private $latitude;

    /**
     * @var array
     */
    private $DMSLatitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var array
     */
    private $DMSLongitude;

    /**
     * @param array $value Value
     * @return static
     */
    public static function parse($value)
    {
        return new static($value[0], $value[1]);
    }

    /**
     * Point constructor.
     *
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        $this->DMSLatitude = $this->DDtoDMS($latitude);
        $this->DMSLongitude = $this->DDtoDMS($longitude);
    }

    /**
     * Returns the latitude
     *
     * @return float
     */
    public function latitude()
    {
        return $this->latitude;
    }

    /**
     * Returns an array with the latitude in the format degrees, minutes and seconds
     *
     * @return array
     */
    public function DMSLatitude()
    {
        return $this->DMSLatitude;
    }

    /**
     * Returns the longitude
     *
     * @return float
     */
    public function longitude()
    {
        return $this->longitude;
    }

    /**
     * Returns an array with the longitude in the format degrees, minutes and seconds
     *
     * @return array
     */
    public function DMSLongitude()
    {
        return $this->DMSLongitude;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Converts decimal format to DMS (Degrees/minutes/seconds)
     *
     * @param float $decimal Decimal degrees
     * @return array
     */
    private function DDtoDMS($decimal)
    {
        $vars = explode('.', $decimal);
        $deg = (float)$vars[0];
        $tempma = '0.' . $vars[1];

        $tempma = $tempma * 3600;
        $min = floor($tempma / 60);
        $sec = $tempma - ($min * 60);

        return [
            'deg' => $deg,
            'min' => $min,
            'sec' => $sec,
        ];
    }
}
