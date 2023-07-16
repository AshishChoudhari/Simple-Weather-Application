<!DOCTYPE html>
<html>
<head>
    <title>Weather Data</title>
    <script>
        function validateForm() {
            const city = document.getElementById("city").value;
            if (city.trim() === "") {
                alert("Please enter a city name");
                return false;
            }
            return true;
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #222;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            display: flex;
            justify-content: space-between;
            width: 100%;
            background-color: #333;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .weather-app {
            background-color: brown;
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            border-radius: 3px;
            cursor: pointer;
        }
        .weather-app:hover {
            background-color: red;
        }
        form {
            margin: 0;
        }
        label {
            font-weight: bold;
            margin-right: 10px;
        }
        input[type=text] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            width: 200px;
            background-color: #444;
            color: #fff;
        }
        input[type=submit] {
            padding: 5px 10px;
            background-color: brown;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 10px;
        }
        input[type=submit]:hover {
            background-color: red;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #555;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border: 1px solid #555;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #333;
        }
        tr:nth-child(odd) {
            background-color: #444;
        }
        img {
            max-height: 32px;
        }
        @media screen and (max-width: 768px) {
            .header {
                flex-direction: column;
            }
            form {
                padding: 15px;
                padding-left: 0;
            }
            input[type=text] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <button class="weather-app" onclick="location.href='AshishChoudhari2331910.html'">Weather App</button>
        <form action="" method="get" onsubmit="return validateForm();">
            <label for="city">Enter a city:</label>
            <input type="text" id="city" name="city">
            <input type="submit" value="Search">
        </form>
        <form method="post" action="">
            <input type="submit" name="delete_all_data" value="Delete OUTDATED and Current Data" >
        </form>
    </div>
    
</body>
</html>
<?php
// Establish a connection to the database
$servername = "localhost";
$username = "id20746268_root";
$password = "Ashish2001@2057";
$dbname = "id20746268_weather";
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if (isset($_POST['delete_all_data'])) {
    $sql = "DELETE FROM weather_data";
    if (mysqli_query($conn, $sql)) {
        echo "All Outdated and Current Data Deleted, Data Updated<br>";
    } else {
        echo "Error deleting data: " . mysqli_error($conn) . "<br>";
    }
}
// By default, get the weather data for Chickasaw
$city = "Chickasaw";
// If the user searches for a city, get the weather data for that city instead
if (isset($_GET['city'])) {
    $city = $_GET['city'];
}
// Set API key
$apiKey = "ce0d464fb8dc4d789b070310232503";
 // Loop through the past 7 days and fetch the weather data for each day
for ($i = 1; $i <= 7; $i++) {
    $date = date('Y-m-d', strtotime('-' . $i . ' days'));
     // Check if the data already exists in the database
    $sql = "SELECT * FROM weather_data WHERE city = '$city' AND date = '$date'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        // Data doesn't exist, fetch it from the API
        $url = "http://api.weatherapi.com/v1/history.json?key=" . $apiKey . "&q=" . $city . "&dt=" . $date;
        $response = json_decode(file_get_contents($url));
         // If response is ok, retrieve the weather data and insert it into the database
        if (isset($response->forecast->forecastday)) {
            $dayData = $response->forecast->forecastday[0];
            $astro = $dayData->astro;
            $day = $dayData->day;
            $cond = $dayData->day->condition;
            $iconUrl = $cond->icon;
            if (strpos($iconUrl, "//") === 0) {
                $iconUrl = "http:" . $iconUrl;
            }
             // Insert or update the data in the weather_data table
            $sql = "INSERT INTO weather_data (date, city, weather_description, temperature, feels_like, humidity, wind_speed, visibility, sunrise_time, sunset_time, weather_icon, totalprecip_mm)
                    VALUES ('$date', '$city', '$cond->text', '$day->avgtemp_c', '$day->avgtemp_c', '$day->avghumidity', '$day->maxwind_kph', '$day->avgvis_km', '$astro->sunrise', '$astro->sunset', '$iconUrl', '$day->totalprecip_mm')
                    ON DUPLICATE KEY UPDATE 
                        weather_description = VALUES(weather_description),
                        temperature = VALUES(temperature),
                        feels_like = VALUES(feels_like),
                        humidity = VALUES(humidity),
                        wind_speed = VALUES(wind_speed),
                        visibility = VALUES(visibility),
                        sunrise_time = VALUES(sunrise_time),
                        sunset_time = VALUES(sunset_time),
                        weather_icon = VALUES(weather_icon),
                        totalprecip_mm = VALUES(totalprecip_mm)";
                        if (mysqli_query($conn, $sql)) {
                echo "";
            } else {
                echo "Error inserting or updating data: " . mysqli_error($conn) . "<br>";
            }
        } else {
            // If there's an error, display an error message in the browser
            echo "Error fetching weather data for " . $city . " on " . $date . "<br>";
        }
    }
}
// Retrieve the data from the weather_data table and display it on the webpage
$sql = "SELECT * FROM weather_data WHERE city = '$city'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    echo "Data fetched from database<br>";
    // Construct the table header
    echo "<table>";
    echo "<tr>
            <th>Date</th>
            <th>City Name</th>
            <th>Weather Description</th>
            <th>Temperature (°C)</th>
            <th>Feels Like (°C)</th>
            <th>Humidity (%)</th>
            <th>Wind Speed (km/h)</th>
            <th>Visibility (km)</th>
            <th>Sunrise Time</th>
            <th>Sunset Time</th>
            <th>Total Precipitation (mm)</th>
            <th>Weather Icon</th>
        </tr>";
     // Display the data in the table
    while ($row = mysqli_fetch_assoc($result)) {
        $sunriseDateTime = new DateTime($row['sunrise_time']);
        $sunsetDateTime = new DateTime($row['sunset_time']);
        $sunriseFormatted = $sunriseDateTime->format('h:i A');
        $sunsetFormatted = $sunsetDateTime->format('h:i') . ' PM';
        echo "<tr>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['city'] . "</td>";
        echo "<td>" . $row['weather_description'] . "</td>";
        echo "<td>" . $row['temperature'] . "</td>";
        echo "<td>" . $row['feels_like'] . "</td>";
        echo "<td>" . $row['humidity'] . "</td>";
        echo "<td>" . $row['wind_speed'] . "</td>";
        echo "<td>" . $row['visibility'] . "</td>";
        echo "<td>" . $sunriseFormatted . "</td>";
        echo "<td>" . $sunsetFormatted . "</td>";
        echo "<td>" . $row['totalprecip_mm'] . "</td>";
        echo "<td><img src='" . $row['weather_icon'] . "' alt='" . $row['weather_description'] . "'></td>";
        echo "</tr>";
    }
     // Close the table
    echo "</table>";
} else {
    // If there's an error, display an error message in the browser
    echo "Error retrieving weather data from the database<br>";
}
 // Close the connection
mysqli_close($conn);
?>