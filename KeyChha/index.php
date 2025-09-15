<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'keychha');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// List of offensive words to filter out
$offensiveWords = [
    'anal',
    'ass',
    'bastard',
    'bitch',
    'boob',
    'blowjob',
    'bdsm',
    'bondage',
    'cunt',
    'cum',
    'cumshot',
    'dick',
    'dildo',
    'fuck',
    'fucking',
    'fucked',
    'fucker',
    'gay',
    'gays',
    'goddamn',
    'horny',
    'incest',
    'jerk',
    'lesbian',
    'lesbians',
    'masturbation',
    'masturbating',
    'milf',
    'milfs',
    'nude',
    'nudist',
    'nudity',
    'orgasm',
    'orgy',
    'porn',
    'porno',
    'pussy',
    'rape',
    'raping',
    'sex',
    'sexcam',
    'sexo',
    'sexual',
    'sexuality',
    'sexually',
    'sexy',
    'shit',
    'shitting',
    'slut',
    'sluts',
    'sucking',
    'sucks',
    'tits',
    'titten',
    'vagina',
    'whore',
    'xxx'
];

// Load and categorize words by character frequency (a-z)
$filename = __DIR__ . "/components/words.txt";
$wordLevels = [];

if (file_exists($filename)) {
    $words = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($words as $word) {
        $word = trim($word);
        if (empty($word))
            continue;

        // Filter out offensive words
        if (in_array(strtolower($word), $offensiveWords)) {
            continue;
        }

        $wordLower = strtolower($word);

        // Count frequency of each letter in the word
        for ($i = 0; $i < 26; $i++) {
            $char = chr(ord('a') + $i);
            $charCount = substr_count($wordLower, $char);

            // Add word to level if it contains the character
            if ($charCount > 0) {
                // Weight words by character frequency (more occurrences = higher priority)
                for ($j = 0; $j < $charCount; $j++) {
                    $wordLevels[$char][] = $word;
                }
            }
        }
    }
}

// Ensure we have at least 20 words for each level (a-z), supplement with fallback words
$fallbackWords = [
    'a' => ['apple', 'about', 'above', 'after', 'again', 'always', 'around', 'another', 'animal', 'answer', 'anyone', 'anything', 'anywhere', 'awesome', 'amazing', 'adventure', 'academy', 'activity', 'address', 'advance', 'already', 'although', 'another', 'anyway', 'appear'],
    'b' => ['beautiful', 'because', 'before', 'believe', 'between', 'business', 'building', 'brother', 'brought', 'become', 'better', 'beyond', 'brown', 'black', 'blue', 'bright', 'bring', 'break', 'bread', 'brain', 'brand', 'bridge', 'brief', 'broad', 'budget'],
    'c' => ['computer', 'company', 'country', 'children', 'change', 'course', 'create', 'center', 'common', 'could', 'called', 'coming', 'certain', 'complete', 'control', 'consider', 'continue', 'current', 'customer', 'culture', 'career', 'capital', 'careful', 'carry', 'catch'],
    'd' => ['different', 'development', 'during', 'director', 'decision', 'department', 'describe', 'discuss', 'disease', 'display', 'dollar', 'doctor', 'driver', 'dinner', 'dance', 'danger', 'dark', 'data', 'date', 'daughter', 'day', 'deal', 'death', 'deep', 'design'],
    'e' => ['everything', 'education', 'experience', 'especially', 'environment', 'economic', 'employee', 'equipment', 'establish', 'evidence', 'example', 'exercise', 'expect', 'explain', 'express', 'extend', 'extra', 'early', 'earth', 'east', 'easy', 'eat', 'edge', 'effect', 'eight'],
    'f' => ['following', 'function', 'financial', 'furniture', 'favorite', 'friendly', 'freedom', 'forward', 'feature', 'federal', 'feeling', 'figure', 'finger', 'finish', 'first', 'floor', 'flower', 'focus', 'force', 'forest', 'forget', 'formal', 'former', 'fortune', 'found'],
    'g' => ['government', 'generation', 'guidance', 'guarantee', 'gallery', 'garden', 'garage', 'gather', 'general', 'gentle', 'gift', 'girl', 'give', 'glass', 'global', 'glory', 'goal', 'gold', 'good', 'great', 'green', 'ground', 'group', 'grow', 'guard'],
    'h' => ['however', 'hospital', 'holiday', 'history', 'hundred', 'happen', 'happy', 'health', 'heart', 'heavy', 'height', 'help', 'here', 'high', 'hill', 'hold', 'home', 'hope', 'horse', 'hot', 'hour', 'house', 'human', 'hunt', 'hurt'],
    'i' => ['important', 'information', 'including', 'increase', 'industry', 'interest', 'international', 'investment', 'involve', 'island', 'issue', 'item', 'idea', 'image', 'imagine', 'impact', 'improve', 'include', 'income', 'indeed', 'individual', 'inside', 'instead', 'institute', 'insurance'],
    'j' => ['journal', 'journey', 'judge', 'jump', 'just', 'justice', 'join', 'job', 'joke', 'joy', 'jacket', 'jazz', 'jeans', 'jewel', 'jewelry', 'jewish', 'jim', 'joe', 'john', 'jones', 'jordan', 'jose', 'joseph', 'josh', 'joshua'],
    'k' => ['kitchen', 'knowledge', 'korean', 'kentucky', 'kansas', 'karen', 'karl', 'kate', 'kathy', 'katie', 'kay', 'keen', 'keep', 'keith', 'kelly', 'ken', 'kennedy', 'kenneth', 'kenny', 'kent', 'kept', 'kernel', 'kerry', 'kevin', 'key'],
    'l' => ['language', 'learning', 'library', 'location', 'looking', 'lovely', 'lunch', 'large', 'last', 'late', 'later', 'laugh', 'launch', 'law', 'lay', 'lead', 'learn', 'leave', 'left', 'leg', 'less', 'let', 'letter', 'level', 'life'],
    'm' => ['management', 'marketing', 'materials', 'medicine', 'military', 'minister', 'mountain', 'movement', 'machine', 'magic', 'mail', 'main', 'make', 'man', 'many', 'map', 'mark', 'market', 'marriage', 'master', 'match', 'matter', 'may', 'mean', 'meet'],
    'n' => ['national', 'natural', 'network', 'nothing', 'notice', 'nuclear', 'number', 'nurse', 'name', 'near', 'need', 'never', 'new', 'next', 'nice', 'night', 'nine', 'no', 'north', 'nose', 'not', 'note', 'now', 'null', 'narrow'],
    'o' => ['operation', 'organization', 'opportunity', 'otherwise', 'outdoor', 'overall', 'office', 'often', 'oil', 'old', 'on', 'once', 'one', 'only', 'open', 'operate', 'opinion', 'opposite', 'order', 'other', 'our', 'out', 'over', 'own', 'owner'],
    'p' => ['personal', 'political', 'population', 'position', 'possible', 'practice', 'pressure', 'probably', 'process', 'product', 'program', 'project', 'provide', 'public', 'purpose', 'page', 'pain', 'paint', 'pair', 'palace', 'pale', 'pan', 'paper', 'park', 'part'],
    'q' => ['quality', 'quarter', 'question', 'quick', 'quiet', 'quite', 'queen', 'queens', 'queensland', 'queries', 'query', 'quest', 'queue', 'quick', 'quickly', 'quiet', 'quilt', 'quit', 'quiz', 'quizzes', 'quotation', 'quote', 'quoted', 'quotes', 'qu'],
    'r' => ['research', 'resource', 'response', 'responsible', 'restaurant', 'relationship', 'religious', 'remember', 'remove', 'report', 'represent', 'require', 'result', 'return', 'review', 'right', 'ring', 'rise', 'risk', 'river', 'road', 'rock', 'role', 'roll', 'room'],
    's' => ['security', 'situation', 'something', 'sometimes', 'somewhere', 'southern', 'specific', 'standard', 'statement', 'station', 'storage', 'strange', 'strength', 'stretch', 'strong', 'student', 'subject', 'success', 'suggest', 'summer', 'support', 'surface', 'surprise', 'surround', 'system'],
    't' => ['technology', 'television', 'themselves', 'therefore', 'thousand', 'throughout', 'together', 'tomorrow', 'tonight', 'towards', 'training', 'transport', 'treatment', 'trouble', 'tuesday', 'table', 'take', 'talk', 'tall', 'tank', 'tape', 'target', 'task', 'taste', 'tax'],
    'u' => ['university', 'understand', 'unemployment', 'unexpected', 'unfortunately', 'unified', 'uniform', 'union', 'unique', 'unit', 'united', 'units', 'unity', 'universal', 'universe', 'unknown', 'unless', 'unlike', 'unlikely', 'unlimited', 'unlock', 'unnecessary', 'unsigned', 'unsubscribe', 'until', 'unusual'],
    'v' => ['various', 'vehicle', 'version', 'village', 'violence', 'virtual', 'visible', 'vision', 'visit', 'visitor', 'voice', 'volume', 'volunteer', 'vote', 'voting', 'vulnerable', 'valley', 'valuable', 'value', 'valued', 'values', 'valve', 'valves', 'vampire', 'van'],
    'w' => ['wonderful', 'washington', 'wednesday', 'welcome', 'wellington', 'whatever', 'whenever', 'wherever', 'wikipedia', 'willing', 'winston', 'winter', 'wireless', 'wisconsin', 'wisdom', 'wish', 'wishes', 'wishlist', 'wit', 'witch', 'with', 'within', 'without', 'witness', 'wives'],
    'x' => ['xbox', 'xerox', 'xhtml', 'xanax', 'xenon', 'xerox', 'xylophone', 'xenophobia', 'xenon', 'xerox', 'xylophone', 'xenophobia', 'xenon', 'xerox', 'xylophone', 'xenophobia', 'xenon', 'xerox', 'xylophone', 'xenophobia', 'xenon', 'xerox', 'xylophone', 'xenophobia', 'xenon'],
    'y' => ['yesterday', 'yourself', 'youth', 'yacht', 'yahoo', 'yale', 'yamaha', 'yang', 'yard', 'yards', 'yarn', 'yea', 'yeah', 'year', 'yearly', 'years', 'yeast', 'yellow', 'yemen', 'yen', 'yes', 'yet', 'yield', 'yields', 'yoga'],
    'z' => ['zimbabwe', 'zinc', 'zip', 'zoloft', 'zone', 'zones', 'zoning', 'zoo', 'zoom', 'zope', 'zshops', 'zu', 'zum', 'zimbabwe', 'zinc', 'zip', 'zoloft', 'zone', 'zones', 'zoning', 'zoo', 'zoom', 'zope', 'zshops', 'zu']
];

for ($i = 0; $i < 26; $i++) {
    $char = chr(ord('a') + $i);
    if (empty($wordLevels[$char]) || count($wordLevels[$char]) < 20) {
        $existingWords = $wordLevels[$char] ?? [];
        $neededWords = array_slice($fallbackWords[$char] ?? [], 0, 25 - count($existingWords));
        $wordLevels[$char] = array_merge($existingWords, $neededWords);
    }
}

$wordPatterns = [];
for ($i = 0; $i < 26; $i++) {
    $char = chr(ord('a') + $i);
    $wordPatterns[$char] = [
        "name" => "Level " . strtoupper($char),
        "length" => 25,
        "words" => $wordLevels[$char] ?? []
    ];
}



// Get current level from URL parameter, default to 'a'
$currentLevel = isset($_GET['level']) ? strtolower($_GET['level']) : 'a';
if (!ctype_alpha($currentLevel) || strlen($currentLevel) !== 1) {
    $currentLevel = 'a';
}

// Handle saving stats
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_stats') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'User not logged in']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $level = filter_input(INPUT_POST, 'level', FILTER_VALIDATE_INT);
    $correct = filter_input(INPUT_POST, 'correct', FILTER_VALIDATE_INT);
    $errors = filter_input(INPUT_POST, 'errors', FILTER_VALIDATE_INT);
    $accuracy = filter_input(INPUT_POST, 'accuracy', FILTER_VALIDATE_FLOAT);
    $wpm = filter_input(INPUT_POST, 'wpm', FILTER_VALIDATE_FLOAT);

    if ($level === false || $correct === false || $errors === false || $accuracy === false || $wpm === false) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }

    $query = $db->prepare("INSERT INTO user_stats (user_id, level, correct, errors, accuracy, wpm, complete_date_time) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $query->bind_param("iiiidd", $userId, $level, $correct, $errors, $accuracy, $wpm);

    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Stats saved successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => $db->error]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>KeyChha - Typing Practice</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="styles/index.css" />
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container my-4">
        <!-- Welcome Message -->
        <div class="welcome-message">
            <h2>
                <i class="bi bi-emoji-smile"></i>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['username'])): ?>
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                <?php else: ?>
                    Welcome, Guest!
                <?php endif; ?>
            </h2>
            <p class="mb-0">
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_SESSION['username'])): ?>
                    Keep going, every keystroke brings you closer to perfection!
                <?php else: ?>
                    Start typing today and unlock your true potential!
                <?php endif; ?>
            </p>
        </div>

        <div id="gameContainer" class="typing-active">
            <div class="typing-section">
                <div class="level-display">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0"><i class="bi bi-trophy"></i> Level <span
                                    id="currentLevelNumber"><?php echo strtoupper($currentLevel); ?></span>
                            </h5>
                            <small id="levelDescription">Words with more
                                '<?php echo strtoupper($currentLevel); ?>' letters</small>
                        </div>
                        <div class="text-end">
                            <small>Complete with 90%+ accuracy to advance</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="navigateToLevel('<?php echo $currentLevel > 'a' ? chr(ord($currentLevel) - 1) : 'a'; ?>')"
                                    <?php echo $currentLevel == 'a' ? 'disabled' : ''; ?>>← Previous</button>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="navigateToLevel('<?php echo $currentLevel < 'z' ? chr(ord($currentLevel) + 1) : 'z'; ?>')"
                                    <?php echo $currentLevel == 'z' ? 'disabled' : ''; ?>>Next →</button>
                                <select class="btn btn-sm btn-outline-secondary ms-2"
                                    onchange="navigateToLevel(this.value)">
                                    <?php for ($i = 0; $i < 26; $i++):
                                        $char = chr(ord('a') + $i); ?>
                                        <option value="<?php echo $char; ?>" <?php echo $char == $currentLevel ? 'selected' : ''; ?>>Level <?php echo strtoupper($char); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-2">
                        <div id="levelProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Stats Panel -->
                <div class="stats-panel">
                    <div class="row text-center">
                        <div class="col-6 col-md-2 stat-item">
                            <div class="stat-value" id="wordCount">0</div>
                            <div class="stat-label">Words</div>
                        </div>
                        <div class="col-6 col-md-2 stat-item">
                            <div class="stat-value" id="charCount">0</div>
                            <div class="stat-label">Characters</div>
                        </div>
                        <div class="col-6 col-md-2 stat-item">
                            <div class="stat-value" id="correctCount">0</div>
                            <div class="stat-label">Correct</div>
                        </div>
                        <div class="col-6 col-md-2 stat-item">
                            <div class="stat-value" id="errorCount">0</div>
                            <div class="stat-label">Errors</div>
                        </div>
                        <div class="col-6 col-md-2 stat-item">
                            <div class="stat-value" id="accuracyDisplay">0%</div>
                            <div class="stat-label">Accuracy</div>
                        </div>
                        <div class="col-6 col-md-2 stat-item">
                            <div class="stat-value" id="wpmCount">0</div>
                            <div class="stat-label">WPM</div>
                        </div>
                    </div>
                </div>

                <!-- Keyboard -->
                <div class="keyboard-container">
                    <!-- Row 1 -->
                    <div class="keyboard-row">
                        <div class="key" data-key="`">~<span class="symbol">`</span></div>
                        <div class="key" data-key="1">!<span class="symbol">1</span></div>
                        <div class="key" data-key="2">@<span class="symbol">2</span></div>
                        <div class="key" data-key="3">#<span class="symbol">3</span></div>
                        <div class="key" data-key="4">$<span class="symbol">4</span></div>
                        <div class="key" data-key="5">%<span class="symbol">5</span></div>
                        <div class="key" data-key="6">^<span class="symbol">6</span></div>
                        <div class="key" data-key="7">&<span class="symbol">7</span></div>
                        <div class="key" data-key="8">*<span class="symbol">8</span></div>
                        <div class="key" data-key="9">(<span class="symbol">9</span></div>
                        <div class="key" data-key="0">)<span class="symbol">0</span></div>
                        <div class="key" data-key="-">_<span class="symbol">-</span></div>
                        <div class="key" data-key="=">+<span class="symbol">=</span></div>
                        <div class="key double" data-key="Backspace">←</div>
                    </div>

                    <!-- Row 2 -->
                    <div class="keyboard-row">
                        <div class="key" data-key="q">Q</div>
                        <div class="key" data-key="w">W</div>
                        <div class="key" data-key="e">E</div>
                        <div class="key" data-key="r">R</div>
                        <div class="key" data-key="t">T</div>
                        <div class="key" data-key="y">Y</div>
                        <div class="key" data-key="u">U</div>
                        <div class="key" data-key="i">I</div>
                        <div class="key" data-key="o">O</div>
                        <div class="key" data-key="p">P</div>
                        <div class="key" data-key="[">{<span class="symbol">[</span></div>
                        <div class="key" data-key="]">}<span class="symbol">]</span></div>
                        <div class="key" data-key="\\">|<span class="symbol">\</span></div>
                    </div>

                    <!-- Row 3 -->
                    <div class="keyboard-row">
                        <div class="key" data-key="a">A</div>
                        <div class="key" data-key="s">S</div>
                        <div class="key" data-key="d">D</div>
                        <div class="key" data-key="f">F</div>
                        <div class="key" data-key="g">G</div>
                        <div class="key" data-key="h">H</div>
                        <div class="key" data-key="j">J</div>
                        <div class="key" data-key="k">K</div>
                        <div class="key" data-key="l">L</div>
                        <div class="key" data-key=";">:<span class="symbol">;</span></div>
                        <div class="key" data-key="'">"<span class="symbol">'</span></div>
                    </div>

                    <!-- Row 4 -->
                    <div class="keyboard-row">
                        <div class="key quad" data-key="Shift">Shift</div>
                        <div class="key" data-key="z">Z</div>
                        <div class="key" data-key="x">X</div>
                        <div class="key" data-key="c">C</div>
                        <div class="key" data-key="v">V</div>
                        <div class="key" data-key="b">B</div>
                        <div class="key" data-key="n">N</div>
                        <div class="key" data-key="m">M</div>
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

                <!-- Words Display -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-primary">
                        <i class="bi bi-lightning"></i> Words to Type
                    </label>
                    <div id="wordsDisplay" class="words-display"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Pass PHP data to JavaScript -->
    <script>
        window.wordPatterns = <?php echo json_encode($wordPatterns); ?>;
        window.userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
        window.currentLevel = '<?php echo $currentLevel; ?>';

        // Debug: Log the data
        console.log('PHP Data passed to JavaScript:');
        console.log('wordPatterns:', window.wordPatterns);
        console.log('currentLevel:', window.currentLevel);
        console.log('Available levels:', Object.keys(window.wordPatterns || {}));
    </script>

    <!-- Main JavaScript -->
    <script src="scripts/typingLogic.js"></script>
</body>

</html>