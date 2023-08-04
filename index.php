<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Replace 'YOUR_API_KEY' with your actual OpenWeatherMap API key
    $apiKey = '90dfe8c16d20d81a6b365f55111568ea';
    $cityId = $_POST['city'];
    $url = "http://api.openweathermap.org/data/2.5/forecast?id={$cityId}&appid={$apiKey}&units=metric";

    $data = file_get_contents($url);
    $weatherData = json_decode($data, true);

    if ($weatherData) {
        $city = $weatherData['city']['name'];
        $country = $weatherData['city']['country'];

        // Group the weather data by day
        $forecast = array();
        foreach ($weatherData['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);
            $forecast[$date][] = array(
                'temperature' => $item['main']['temp'],
                'description' => ucfirst($item['weather'][0]['description']),
                'humidity' => $item['main']['humidity'],
                'wind_speed' => $item['wind']['speed'],
                'rain_possibility' => isset($item['rain']['3h']) ? $item['rain']['3h'] : 'N/A',
            );
        }

        // Calculate the average values for each day
        $averageForecast = array();
        foreach ($forecast as $date => $dailyForecast) {
            $averageForecast[$date] = array(
                'temperature' => round(array_sum(array_column($dailyForecast, 'temperature')) / count($dailyForecast), 1),
                'description' => $dailyForecast[0]['description'], 
                'humidity' => round(array_sum(array_column($dailyForecast, 'humidity')) / count($dailyForecast)),
                'wind_speed' => round(array_sum(array_column($dailyForecast, 'wind_speed')) / count($dailyForecast), 1),
                'rain_possibility' => $dailyForecast[0]['rain_possibility'], 
            );
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="weather-app">
        <h1>Weather App</h1>
        <form action="index.php" method="post">
            <label for="city">Select a city:</label>
            <select id="city" name="city">
                <option value="2643743">London, UK</option>
                <option value="5128581">New York, US</option>
                <option value="1850147">Tokyo, JP</option>
            
            </select>
            <button type="submit">Get Weather</button>
        </form>
        <div class="weather-info">
            <?php if (isset($city)&& isset($country)):?>
            <h2><?php echo "{$city}, {$country}"; ?></h2>
            <?php foreach ($averageForecast as $date => $day): ?>
                <div class="weather-day">
                    <h3><?php echo $date; ?></h3>
                    <p>Temperature: <?php echo "{$day['temperature']} °C"; ?></p>
                    <p>Description: <?php echo $day['description']; ?></p>
                    <p>Humidity: <?php echo "{$day['humidity']}%"; ?></p>
                    <p>Wind Speed: <?php echo "{$day['wind_speed']} m/s"; ?></p>
                    <p>Rain Possibility: <?php echo ($day['rain_possibility'] !== 'N/A') ? "{$day['rain_possibility']} mm" : 'N/A'; ?></p>
                </div>
                <?php endforeach; ?>
            
             <?php else: ?>
                <h2> Please select the city and country</h2>
            <?php endif ;?>

            <div class="footer">
            <p>Copyright © 2023 Shohan_islam_Joy All rights reserved</p>
        </div>
        </div>
        
    </div>


    
</body>
</html>