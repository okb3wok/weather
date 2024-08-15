# 🧐 About

The single page web site with 24 hours temperature history and 
online weather condition in 4 main places. The data are providing by https://openweathermap.org/

[![Screen](https://github.com/okb3wok/weather/blob/master/screen.png?raw=true)](https://upc.aviaavtomatika.ru/)

## Project structure

```text
├── icons                       - Svg icons
├─┬ templates                   - Templates
│ └── main.html   
├── index.php                   - Entry point
├── flags.js                    - EMOJI flags
├── charts.js                   - Chart.js v4.4.3
├── weather.css                 - CSS Styles
├── weather.js                  - Main JS Script
├── settings.json               - JSON settings
├── open_weather_api.php        - API access key
├── openweather.php             - Class for getting JSON from OpenWeather API
└── weather_upadate_cron.php    - Update jsons every 5 minutes
```
