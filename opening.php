<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening</title>
    <link href="https://fonts.googleapis.com/css2?family=Angkor&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to right, #C1C1C1, #FFFFFF);
            overflow: hidden;
        }

        .container {
            /* display: flex; */
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            position: relative;
            padding: 20px;
            opacity: 1;
            transition: opacity 1s ease-out;
            text-align: center;
        }

        .content-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            max-width: 100%;
            max-height: 90%;
            opacity: 1;
            transition: opacity 1s ease-out;
        }

        .container h1 {
            color: #FFA500;
            margin-top: 100px;
            font-family: 'Angkor', sans-serif;
            font-weight: bold;
            font-size: 3em;
            /* Menambahkan properti font-size */
        }

        .container h2 {
            color: #FFA500;
            margin-top: 20px;
            font-size: 1.5em;
            /* Memperbesar ukuran font */
        }

        .container img {
            width: 25%;
            height: auto;
            border-radius: 10px;
        }

        .fade-out {
            opacity: 0;
        }
    </style>
    <script>
        // Function to redirect to index.php after 2 seconds
        setTimeout(function() {
            document.querySelector('.container').classList.add('fade-out');
            setTimeout(function() {
                window.location.href = 'welcome_page.php';
            }, 1000); // Allow time for fade-out transition
        }, 3000);
    </script>
</head>

<body>
    <div class="container">
        <!-- <div class="content-box"> -->
        <h1>MEAL DEALS</h1>
        <img src="opening/opening.png" alt="Opening Meal Deals">
        <!-- </div> -->
    </div>
</body>

</html>