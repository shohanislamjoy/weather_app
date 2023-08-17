<?php

$city_pic='./img/defult.jpg';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $apiKey = '90dfe8c16d20d81a6b365f55111568ea';
    $cityId = $_POST['city'];

    if($cityId=='1337178'){
        $city_pic='./img/dhaka.jpg';
    }
    elseif($cityId=='2643743'){
        $city_pic='./img/london.jpg';
    }
    elseif($cityId=='5128581'){
        $city_pic='./img/newyork.jpg';
    }
    elseif($cityId=='1850147'){
        $city_pic='./img/tokyo.jpg';
    }    
    elseif($cityId=='1337200'){
        $city_pic='./img/chittagong.jpg';
    }
    elseif($cityId=='1261481'){
        $city_pic='./img/delhi.jpg';
    }
    elseif($cityId=='1816670'){
        $city_pic='./img/beijing.jpg';
    }
    elseif($cityId=='4876353'){
        $city_pic='./img/sidney.jpg';
    }
    else{
        $city_pic='./img/defult.jpg';
    }

    $url = "http://api.openweathermap.org/data/2.5/forecast?id={$cityId}&appid={$apiKey}&units=metric";
    $data = file_get_contents($url);
    $weatherData = json_decode($data, true);

    if ($weatherData) {
        $city = $weatherData['city']['name'];
        $country = $weatherData['city']['country'];

        // Group the weather data by day
        $forecast = array();
        foreach ($weatherData['list'] as $item) {
            $date = date('d-m-Y', $item['dt']);
            $forecast[$date][] = array(
                'temperature' => $item['main']['temp'],
                'description' => ucfirst($item['weather'][0]['description']),
                'icon'=>ucfirst($item['weather'][0]['icon']),
                'humidity' => $item['main']['humidity'],
                'wind_speed' => $item['wind']['speed']
            );
        }

        // Calculate the average values for each day
        $averageForecast = array();
        foreach ($forecast as $date => $dailyForecast) {
            $temperatures = array_column($dailyForecast, 'temperature');
        
            $averageForecast[$date] = array(
                'max_temp' => max($temperatures),
                'min_temp' => min($temperatures),
                'temperature' => round(array_sum($temperatures) / count($temperatures), 1),
                'description' => $dailyForecast[0]['description'],
                'icon' => $dailyForecast[0]['icon'],
                'humidity' => round(array_sum(array_column($dailyForecast, 'humidity')) / count($dailyForecast)),
                'wind_speed' => round(array_sum(array_column($dailyForecast, 'wind_speed')) / count($dailyForecast), 1)
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
    <link rel="icon" href="./img/Shohan_fav.ico">
   
</head>
<body style="background-image: url(<?php echo $city_pic; ?>);">
    <div class="weather-app">
        <h1>Weather App</h1>
        <form action="index.php" method="post">
            <label for="city">Select a city <b>: </b></label>
            <select id="city" name="city">
                <option value="1337178">Dhaka, BD</option>
                <option value="2643743">London, UK</option>
                <option value="5128581">New York, US</option>
                <option value="1850147">Tokyo, JP</option>
                <option value="1337200">Chittagong, BD</option>
                <option value="1261481">New Delhi, IN</option>
                <option value="1816670">Beijing, CN</option>
                <option value="4876353">Sidney, AS</option>

            </select>
            <button type="submit">Get Weather</button>
        </form>
        <div class="weather-info">
            <?php if (isset($city)&& isset($country)):?>
            <h2><?php echo "{$city}, {$country}"; ?></h2>
            <?php foreach ($averageForecast as $date => $day): ?>
                <div class="weather-day">
                    <img src="http://openweathermap.org/img/wn/<?php echo $day['icon'];?>.png">
                <h2><?php 
                    $datetime=new Datetime($date);
                    $dayName = $datetime->format('l');
                       echo $dayName;?></h2>
                    <h3><?php echo $date; ?></h3>
                    <p>Temperature: <?php echo "{$day['temperature']} °C"; ?></p>
                    <p>Description: <?php echo $day['description']; ?></p>
                    <p>Humidity: <?php echo "{$day['humidity']}%"; ?></p>
                    <p>Wind Speed: <?php echo "{$day['wind_speed']} m/s"; ?></p>
                    <p>Min Temperature <?php echo"{$day['min_temp']}°C"; ?></p>
                    <p>Max Temperature <?php echo"{$day['max_temp']}°C"; ?></p>

                </div>
                <?php endforeach; ?>
             <?php else: ?>
                <div class="city_not_selected">
                <h2> Please select the city and country</h2>
                </div>
            <?php endif ;?>

            <div class="footer">
            <p>Copyright © 2023 Shohan_islam_Joy All rights reserved</p>
        </div>
        </div>
        
    </div>

</body>
</html>