<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KeyChha Typing Practice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .keyboard-container {
            background-color: #f0f2f5;
            border-radius: 10px;
            padding: 15px 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 850px;
            margin-inline: auto;
            overflow-x: auto;
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .key {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 0;
            font-size: 14px;
            height: 50px;
            flex: 1 1 40px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.08s ease;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            user-select: none;
        }

        .key.space {
            flex: 1 1 180px;
        }

        .key.double {
            flex: 1 1 60px;
        }

        .key.quad {
            flex: 1 1 80px;
        }

        .key.active {
            background-color: #9d50bb;
            color: white;
            box-shadow: 0 0 10px #9d50bb;
        }

        .symbol {
            font-size: 12px;
            opacity: 0.6;
        }

        @media (max-width: 576px) {
            .key {
                font-size: 12px;
                height: 40px;
                flex: 1 1 28px;
            }

            .key.space {
                flex: 1 1 120px;
            }

            .key.double,
            .key.quad {
                flex: 1 1 50px;
            }

            .keyboard-container {
                padding: 8px 5px;
            }
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-5">
        <h2 class="text-center mb-4 fw-bold">KeyChha Typing Practice</h2>
        <textarea id="typing-area" class="form-control mb-4" rows="5" placeholder="Start typing here..."></textarea>

        <div class="keyboard-container">
            <!-- Row 1 -->
            <div class="keyboard-row">
                <div class="key" data-key="~">~<span class="symbol">`</span></div>
                <div class="key" data-key="!">!<span class="symbol">1</span></div>
                <div class="key" data-key="@">@<span class="symbol">2</span></div>
                <div class="key" data-key="#">#<span class="symbol">3</span></div>
                <div class="key" data-key="$">$<span class="symbol">4</span></div>
                <div class="key" data-key="%">%<span class="symbol">5</span></div>
                <div class="key" data-key="^">^<span class="symbol">6</span></div>
                <div class="key" data-key="&">&<span class="symbol">7</span></div>
                <div class="key" data-key="*">*<span class="symbol">8</span></div>
                <div class="key" data-key="("> (<span class="symbol">9</span></div>
                <div class="key" data-key=")">)<span class="symbol">0</span></div>
                <div class="key" data-key="_">_<span class="symbol">-</span></div>
                <div class="key" data-key="+">+<span class="symbol">=</span></div>
                <div class="key double" data-key="Backspace">Backspace</div>
            </div>

            <!-- Row 2 -->
            <div class="keyboard-row">
                <div class="key" data-key="Q">Q</div>
                <div class="key" data-key="W">W</div>
                <div class="key" data-key="E">E</div>
                <div class="key" data-key="R">R</div>
                <div class="key" data-key="T">T</div>
                <div class="key" data-key="Y">Y</div>
                <div class="key" data-key="U">U</div>
                <div class="key" data-key="I">I</div>
                <div class="key" data-key="O">O</div>
                <div class="key" data-key="P">P</div>
                <div class="key" data-key="{">{<span class="symbol">[</span></div>
                <div class="key" data-key="}">}<span class="symbol">]</span></div>
                <div class="key" data-key="|">|<span class="symbol">\</span></div>
            </div>

            <!-- Row 3 -->
            <div class="keyboard-row">
                <div class="key" data-key="A">A</div>
                <div class="key" data-key="S">S</div>
                <div class="key" data-key="D">D</div>
                <div class="key" data-key="F">F</div>
                <div class="key" data-key="G">G</div>
                <div class="key" data-key="H">H</div>
                <div class="key" data-key="J">J</div>
                <div class="key" data-key="K">K</div>
                <div class="key" data-key="L">L</div>
                <div class="key" data-key=";">;<span class="symbol">:</span></div>
                <div class="key" data-key="'">'<span class="symbol">"</span></div>
            </div>

            <!-- Row 4 -->
            <div class="keyboard-row">
                <div class="key quad" data-key="Shift">Shift</div>
                <div class="key" data-key="Z">Z</div>
                <div class="key" data-key="X">X</div>
                <div class="key" data-key="C">C</div>
                <div class="key" data-key="V">V</div>
                <div class="key" data-key="B">B</div>
                <div class="key" data-key="N">N</div>
                <div class="key" data-key="M">M</div>
                <div class="key" data-key=",">,<span class="symbol">
                        < </span>
                </div>
                <div class="key" data-key=".">.<span class="symbol">></span></div>
                <div class="key" data-key="/">/<span class="symbol">?</span></div>
                <div class="key quad" data-key="Shift">Shift</div>
            </div>

            <!-- Row 5 -->
            <div class="keyboard-row">
                <div class="key space" data-key=" ">Space</div>
            </div>
        </div>
    </div>

    <script>
        const keys = document.querySelectorAll(".key");
        const textarea = document.getElementById("typing-area");

        const keyMap = {
            " ": " ",
            "ShiftLeft": "Shift",
            "ShiftRight": "Shift",
            "Backspace": "Backspace",
        };

        let activeKeys = new Set();

        textarea.addEventListener("keydown", function (e) {
            let key = keyMap[e.code] || keyMap[e.key] || (e.key.length === 1 ? e.key.toUpperCase() : e.key);
            activeKeys.add(key);

            keys.forEach(k => {
                const dataKey = k.getAttribute("data-key");
                k.classList.toggle("active", activeKeys.has(dataKey));
            });
        });

        textarea.addEventListener("keyup", function (e) {
            let key = keyMap[e.code] || keyMap[e.key] || (e.key.length === 1 ? e.key.toUpperCase() : e.key);
            activeKeys.delete(key);

            keys.forEach(k => {
                const dataKey = k.getAttribute("data-key");
                k.classList.toggle("active", activeKeys.has(dataKey));
            });
        });
    </script>
</body>

</html>