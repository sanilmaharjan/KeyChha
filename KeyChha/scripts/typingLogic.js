document.addEventListener("DOMContentLoaded", function () {
    const gameContainer = document.getElementById("gameContainer");
    const wordsDisplay = document.getElementById("wordsDisplay");
    const wordCountDisplay = document.getElementById("wordCount");
    const charCountDisplay = document.getElementById("charCount");
    const correctCountDisplay = document.getElementById("correctCount");
    const errorCountDisplay = document.getElementById("errorCount");
    const accuracyDisplay = document.getElementById("accuracyDisplay");
    const wpmDisplay = document.getElementById("wpmCount");
    const levelProgress = document.getElementById("levelProgress");
    const currentLevelNumber = document.getElementById("currentLevelNumber");
    const levelDescription = document.getElementById("levelDescription");
    const keyboardKeys = document.querySelectorAll('.key');

    let currentWordIndex = 0;
    let currentCharIndex = 0;
    let startTime = null;
    let correctChars = 0;
    let errorChars = 0;
    let currentLevel = window.currentLevel || 'a';
    let targetText = "";
    let isTypingActive = true;
    let firstKeyPressed = false;
    let charStates = [];
    let isLevelCompleted = false;
    
    // Access global variables
    const wordPatterns = window.wordPatterns || {};

    function init() {
        // Debug: Log available data
        console.log('Current level:', currentLevel);
        console.log('Word patterns:', wordPatterns);
        console.log('Available levels:', Object.keys(wordPatterns));
        
        // Initialize level display with current level
        currentLevelNumber.textContent = currentLevel.toUpperCase();
        levelDescription.textContent = `Words with more '${currentLevel.toUpperCase()}' letters`;
        
        setupEventListeners();
        startPractice();
    }

    function generatePracticeText(level) {
        console.log('generatePracticeText called with level:', level);
        console.log('Available wordPatterns keys:', Object.keys(wordPatterns));
        
        const pattern = wordPatterns[level] || wordPatterns['a'];
        console.log('Selected pattern:', pattern);
        
        const words = pattern.words;
        const wordCount = pattern.length || 25;
        
        console.log('Generating text for level:', level);
        console.log('Available words:', words);
        console.log('Word count:', words ? words.length : 0);
        
        if (!words || words.length === 0) {
            console.error('No words available for level', level);
            console.log('Using fallback words');
            return 'apple about above after again always around another animal answer anyone anything anywhere awesome amazing adventure academy activity address advance already although another anyway appear beautiful because before believe between business building brother brought become better beyond brown black blue bright bring break bread brain brand bridge brief broad budget computer company country children change course create center common could called coming certain complete control consider continue current customer culture career capital careful carry catch';
        }
        
        const shuffled = [...words].sort(() => 0.5 - Math.random());
        const selectedWords = shuffled.slice(0, wordCount);
        console.log('Selected words:', selectedWords);
        const result = selectedWords.join(" ");
        console.log('Final generated text:', result);
        return result;
    }

    function startPractice() {
        isLevelCompleted = false; // Reset completion flag
        targetText = generatePracticeText(currentLevel);
        console.log('Generated text:', targetText);
        resetStats();
        initializeCharStates();
        renderWords();
        wordsDisplay.classList.add("active");
        gameContainer.classList.add("typing-active");
        window.focus();
    }

    function resetStats() {
        currentWordIndex = 0;
        currentCharIndex = 0;
        startTime = null;
        correctChars = 0;
        errorChars = 0;
        firstKeyPressed = false;
        wordCountDisplay.textContent = "0";
        charCountDisplay.textContent = "0";
        correctCountDisplay.textContent = "0";
        errorCountDisplay.textContent = "0";
        accuracyDisplay.textContent = "0%";
        wpmDisplay.textContent = "0";
        levelProgress.style.width = "0%";
    }

    function initializeCharStates() {
        charStates = new Array(targetText.length).fill(null);
    }

    function renderWords() {
        console.log('renderWords called');
        console.log('targetText:', targetText);
        console.log('wordsDisplay element:', wordsDisplay);
        
        if (!wordsDisplay) {
            console.error('wordsDisplay element not found!');
            return;
        }
        
        wordsDisplay.innerHTML = "";
        const words = targetText.split(" ");
        let globalCharIndex = 0;
        
        console.log('Rendering words:', words);
        console.log('Number of words:', words.length);

        words.forEach((word, wordIndex) => {
            const wordSpan = document.createElement("span");
            wordSpan.className = "word";

            if (wordIndex === currentWordIndex) {
                wordSpan.classList.add("current-word");
                for (let i = 0; i < word.length; i++) {
                    const charSpan = document.createElement("span");
                    charSpan.textContent = word[i];

                    if (charStates[globalCharIndex + i] === 'correct') {
                        charSpan.classList.add("correct");
                    } else if (charStates[globalCharIndex + i] === 'error') {
                        charSpan.classList.add("error");
                    }

                    if (i === currentCharIndex) {
                        charSpan.classList.add("current-char");
                    }

                    wordSpan.appendChild(charSpan);
                }
            } else {
                if (wordIndex < currentWordIndex) {
                    for (let i = 0; i < word.length; i++) {
                        const charSpan = document.createElement("span");
                        charSpan.textContent = word[i];

                        if (charStates[globalCharIndex + i] === 'correct') {
                            charSpan.classList.add("correct");
                        } else if (charStates[globalCharIndex + i] === 'error') {
                            charSpan.classList.add("error");
                        }

                        wordSpan.appendChild(charSpan);
                    }
                    wordSpan.style.opacity = "0.6";
                } else {
                    wordSpan.textContent = word;
                    wordSpan.style.opacity = "0.4";
                }
            }

            wordsDisplay.appendChild(wordSpan);

            if (wordIndex < words.length - 1) {
                const spaceSpan = document.createElement("span");
                spaceSpan.textContent = " ";

                if (charStates[globalCharIndex + word.length] === 'error') {
                    spaceSpan.classList.add("error");
                }
                // Don't add 'correct' class to spaces

                wordsDisplay.appendChild(spaceSpan);
                globalCharIndex += word.length + 1;
            } else {
                globalCharIndex += word.length;
            }
        });
    }

    function getGlobalCharIndex() {
        let globalIndex = 0;
        const words = targetText.split(" ");

        for (let i = 0; i < currentWordIndex; i++) {
            globalIndex += words[i].length + 1;
        }
        globalIndex += currentCharIndex;

        return globalIndex;
    }

    function updateAccuracy() {
        const total = correctChars + errorChars;
        const accuracy = total > 0 ? Math.round((correctChars / total) * 100) : 0;
        accuracyDisplay.textContent = accuracy + "%";
        return accuracy;
    }

    function updateWPM() {
        if (startTime) {
            const minutes = (new Date() - startTime) / 60000;
            const wpm = Math.round(correctChars / 5 / minutes);
            wpmDisplay.textContent = isFinite(wpm) && wpm > 0 ? wpm : 0;
            return wpm;
        }
        return 0;
    }

    function updateLevelProgress() {
        const words = targetText.split(" ");
        const progress = (currentWordIndex / words.length) * 100;
        levelProgress.style.width = `${progress}%`;
    }

    async function saveStats(level, correct, errors, accuracy, wpm) {
        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=save_stats&level=${level}&correct=${correct}&errors=${errors}&accuracy=${accuracy}&wpm=${wpm}`
            });
            const data = await response.json();
            console.log('Stats saved:', data);
            return data.success;
        } catch (error) {
            console.error('Error saving stats:', error);
            return false;
        }
    }

    async function showCompletion() {
        // Prevent duplicate completion calls
        if (isLevelCompleted) {
            return;
        }
        isLevelCompleted = true;
        
        const accuracy = updateAccuracy();
        const wpm = updateWPM();
        let message = `Level ${currentLevel} completed!\nAccuracy: ${accuracy}%\nWPM: ${wpm}`;
        let shouldAdvance = accuracy >= 90;

        // Save stats to database if user is logged in
        if (userId) {
            const correct = correctChars;
            const errors = errorChars;
            await saveStats(currentLevel, correct, errors, accuracy, wpm);
        }

        if (shouldAdvance) {
            if (currentLevel < 'z') {
                currentLevel = String.fromCharCode(currentLevel.charCodeAt(0) + 1);
                currentLevelNumber.textContent = currentLevel.toUpperCase();
                levelDescription.textContent = `Words with more '${currentLevel.toUpperCase()}' letters`;
                message += `\n\nGreat! Advancing to Level ${currentLevel.toUpperCase()}`;
            } else {
                message += `\n\nCongratulations! You've completed all levels!`;
            }
        } else {
            message += `\n\nAccuracy below 90%. Please try this level again.`;
        }

        setTimeout(() => {
            alert(message);
            if (shouldAdvance && currentLevel <= 'z') {
                // Navigate to next level
                window.location.href = `?level=${currentLevel}`;
            } else {
                // Reset to current level if not advancing
                isLevelCompleted = false;
                startPractice();
            }
        }, 500);
    }

    function checkLevelCompletion() {
        const words = targetText.split(" ");
        if (currentWordIndex >= words.length) {
            showCompletion();
            return true;
        }
        return false;
    }

    function highlightKey(key, isActive) {
        // Handle special cases for key mapping
        let dataKey = key;
        
        // Map backslash key - the HTML has data-key="\\" but the event key is "\"
        if (key === "\\") {
            dataKey = "\\\\";
        }
        // Map backspace key
        else if (key === "Backspace") {
            dataKey = "Backspace";
        }
        
        const keyElements = document.querySelectorAll(`.key[data-key="${dataKey}"]`);
        keyElements.forEach(keyElement => {
            if (keyElement) {
                keyElement.classList.toggle("active", isActive);
            }
        });
    }

    function setupEventListeners() {
        document.addEventListener("keydown", function (e) {
            if (!isTypingActive) return;

            // Handle Shift key
            if (e.key === "Shift") {
                highlightKey("Shift", true);
                return;
            }

            // Handle Backspace key
            if (e.key === "Backspace") {
                highlightKey("Backspace", true);
                return;
            }

            if (checkLevelCompletion()) return;

            const words = targetText.split(" ");
            if (currentWordIndex >= words.length) return;

            const currentWord = words[currentWordIndex];
            let expectedChar = currentWord ? currentWord[currentCharIndex] : "";

            // Handle space key
            if (e.key === " ") {
                e.preventDefault();
                e.stopPropagation();
                highlightKey(" ", true);

                const globalIndex = getGlobalCharIndex();

                if (currentCharIndex === currentWord.length) {
                    // Don't mark space as correct, just count it
                    correctChars++;
                } else {
                    charStates[globalIndex] = 'error';
                    errorChars++;
                }

                currentWordIndex++;
                currentCharIndex = 0;
                wordCountDisplay.textContent = currentWordIndex;
                updateLevelProgress();

                charCountDisplay.textContent = correctChars + errorChars;
                correctCountDisplay.textContent = correctChars;
                errorCountDisplay.textContent = errorChars;
                updateAccuracy();
                updateWPM();
                renderWords();
                return;
            }

            // Ignore non-character keys and modifier combinations
            if (e.key.length !== 1 || e.ctrlKey || e.altKey || e.metaKey) return;

            // Start timing on first key press
            if (!firstKeyPressed && (e.key === expectedChar || e.key === " ")) {
                startTime = new Date();
                firstKeyPressed = true;
            }

            // Highlight the pressed key and shift if pressed
            highlightKey(e.key, true);
            if (e.shiftKey) {
                highlightKey("Shift", true);
            }

            const globalIndex = getGlobalCharIndex();

            if (e.key === expectedChar) {
                charStates[globalIndex] = 'correct';
                correctChars++;
                currentCharIndex++;
            } else {
                charStates[globalIndex] = 'error';
                errorChars++;
                currentCharIndex++;

                if (currentCharIndex >= currentWord.length) {
                    currentWordIndex++;
                    currentCharIndex = 0;
                    wordCountDisplay.textContent = currentWordIndex;
                    updateLevelProgress();
                }
            }

            charCountDisplay.textContent = correctChars + errorChars;
            correctCountDisplay.textContent = correctChars;
            errorCountDisplay.textContent = errorChars;
            updateAccuracy();
            updateWPM();
            renderWords();
        });

        document.addEventListener("keyup", function (e) {
            if (e.key === "Shift") {
                highlightKey("Shift", false);
            } else if (e.key === "Backspace") {
                highlightKey("Backspace", false);
            } else {
                highlightKey(e.key, false);
                // Also unhighlight shift if it was pressed with this key
                if (!e.shiftKey) {
                    highlightKey("Shift", false);
                }
            }
        });

        // Handle keyboard key clicks - only for visual feedback
        keyboardKeys.forEach(key => {
            key.addEventListener("mousedown", function (event) {
                const keyValue = this.getAttribute("data-key");
                
                // Convert data-key back to actual key for highlighting
                let actualKey = keyValue;
                if (keyValue === "\\\\") {
                    actualKey = "\\";
                }
                
                highlightKey(actualKey, true);
                
                // If shift is pressed, also highlight shift
                if (event.shiftKey) {
                    highlightKey("Shift", true);
                }
            });

            key.addEventListener("mouseup", function (event) {
                const keyValue = this.getAttribute("data-key");
                
                // Convert data-key back to actual key for highlighting
                let actualKey = keyValue;
                if (keyValue === "\\\\") {
                    actualKey = "\\";
                }
                
                highlightKey(actualKey, false);
                
                // If shift is not pressed, unhighlight shift
                if (!event.shiftKey) {
                    highlightKey("Shift", false);
                }
            });

            key.addEventListener("mouseleave", function (event) {
                const keyValue = this.getAttribute("data-key");
                
                // Convert data-key back to actual key for highlighting
                let actualKey = keyValue;
                if (keyValue === "\\\\") {
                    actualKey = "\\";
                }
                
                highlightKey(actualKey, false);
                
                // If shift is not pressed, unhighlight shift
                if (!event.shiftKey) {
                    highlightKey("Shift", false);
                }
            });
        });
    }

    // Global function for level navigation
    window.navigateToLevel = function(level) {
        window.location.href = `?level=${level}`;
    };

    init();
});
