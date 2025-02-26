<?php


namespace Xibo\Event;

/**
 * Event raised when XMDS receives a request for weather data.
 */
class XmdsWeatherRequestEvent extends Event
{
    public static $NAME = 'xmds.weather.request';

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var float
     */
    private $weatherData;

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * Sets the weather data.
     *
     * @param string $weatherData
     */
    public function setWeatherData(string $weatherData): void
    {
        $this->weatherData = $weatherData;
    }

    /**
     * Gets the weather data.
     *
     * @return string
     */
    public function getWeatherData(): string
    {
        return $this->weatherData;
    }
}
