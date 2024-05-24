<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TapSwap Game</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #2c2c2c;
            color: white;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .balance-container {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .balance {
            font-size: 2em;
            margin-right: 20px;
        }
        .coin {
            width: 200px;
            height: 200px;
            background-image: url('https://i.imgur.com/5YFrVeA.png');
            background-size: cover;
            border-radius: 50%;
            cursor: pointer;
        }
        .stats-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .stats {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .level-up {
            margin-top: 20px;
            font-size: 1.5em;
            cursor: pointer;
            background-color: #4caf50;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="balance-container">
        <div class="balance"><h3> 🪙<span id="coin-balance">0</span></h3></div>
    </div>
    <div class="coin" id="tap-coin"></div>
    <div class="stats-container">
        <div class="stats"><h3> ⚡<span id="energy-balance">0</span>/500</h3></div>
        <div class="stats"><h3> Level: <span id="level">1</span></h3></div>
        <button class="level-up" id="level-up-btn">Level Up (Cost: 500 coins)</button>
    </div>

    <script>
        let coinBalance = localStorage.getItem('coinBalance') || 0;
        let energyBalance = localStorage.getItem('energyBalance') || 0;
        let level = localStorage.getItem('level') || 1;
        let lastUpdate = localStorage.getItem('lastUpdate') || Date.now();
        let inGame = true; // Track whether the user is in the game

        // Calculate the time difference and update energy balance
        let now = Date.now();
        let timeDiff = Math.floor((now - lastUpdate) / 1000); // in seconds
        if (inGame) {
            energyBalance -= timeDiff;
            if (energyBalance < 0) energyBalance = 0;
        }

        updateBalances();

        function updateBalances() {
            document.getElementById('coin-balance').textContent = coinBalance;
            document.getElementById('energy-balance').textContent = energyBalance;
            document.getElementById('level').textContent = level;
            let cost = level * 500;
            document.getElementById('level-up-btn').textContent = `Level Up  ${cost} coins`;
            localStorage.setItem('lastUpdate', Date.now());
        }

        function increaseCoin() {
            if (energyBalance < 500) { // Add this condition to check if energy balance is less than 500
                let coinIncrease = level * 1; // Each level adds 1 coin per tap
                coinBalance = parseInt(coinBalance) + coinIncrease;
                energyBalance = parseInt(energyBalance) + coinIncrease; // Increase energy balance by the same amount as coin increase
                if (energyBalance > 500) {
                    energyBalance = 500;
                }
                updateBalances();
                localStorage.setItem('coinBalance', coinBalance);
                localStorage.setItem('energyBalance', energyBalance);
                if (energyBalance > 0) {
                    startEnergyDecrease();
                }
            } else {
                alert("Energy balance is full. Please use some energy before earning more coins.");
            }
        }

        function levelUp() {
            let cost = level * 500; // Cost increases with each level
            if (coinBalance >= cost && level < 10) {
                coinBalance -= cost;
                level++;
                updateBalances();
                localStorage.setItem('coinBalance', coinBalance);
                localStorage.setItem('level', level);
            } else if (level >= 10) {
                alert("Already at max level.");
            } else {
                alert(`Not enough coins to level up. You need ${cost - coinBalance} more coins.`);
            }
        }

        document.getElementById('tap-coin').addEventListener('click', function() {
            increaseCoin();
        });

        document.getElementById('level-up-btn').addEventListener('click', function() {
            levelUp();
        });

        function startEnergyDecrease() {
            clearInterval(window.energyDecreaseInterval);
            window.energyDecreaseInterval = setInterval(function() {
                if (energyBalance > 0) {
                    energyBalance--;
                    updateBalances();
                    localStorage.setItem('energyBalance', energyBalance);
                } else {
                    clearInterval(window.energyDecreaseInterval);
                }
            }, 1000);
        }

        // Ensure energy decrease starts immediately
        startEnergyDecrease();

        // Listen for visibility change events
        document.addEventListener("visibilitychange", function() {
            if (document.visibilityState === "hidden") {
                inGame = false; // User has left the game
                localStorage.setItem('lastUpdate', Date.now());
            } else {
                inGame = true; // User is back in the game
                now = Date.now();
                timeDiff = Math.floor((now - lastUpdate) / 1000); // Recalculate time difference
                energyBalance -= timeDiff;
                if (energyBalance < 0) energyBalance = 0;
                updateBalances();
            }
        });
    </script>
</body>
</html>
