<?php
require_once __DIR__ . '/config.php';
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ✅ Controllo autenticazione
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

class InspirationManager {
    private string $quotesFile;
    private string $imagesFile;

    public function __construct() {
        // Reference data lives in shared /data (not user-specific)
        $this->quotesFile = DATA_DIR . '/quotes.json';
        $this->imagesFile = DATA_DIR . '/images.json';
    }

    public function getInspiration(string $cycle): array {
        $quote = $this->getQuoteForCycle($cycle);
        $image = $this->getImageForCycle($cycle);

        return [
            'quote' => $quote,
            'image' => $image,
            'cycle' => $cycle
        ];
    }

    private function getQuoteForCycle(string $cycle): array {
        $quotes = $this->loadQuotes();

        if (!isset($quotes[$cycle]) || empty($quotes[$cycle])) {
            $cycle = 'general';
        }

        $cycleQuotes = $quotes[$cycle] ?? [];
        if (empty($cycleQuotes)) {
            return ['text' => 'Keep going.', 'author' => ''];
        }

        $randomIndex = array_rand($cycleQuotes);
        return $cycleQuotes[$randomIndex];
    }

    private function getImageForCycle(string $cycle): array {
        $images = $this->loadImages();

        if (!isset($images[$cycle]) || empty($images[$cycle])) {
            $cycle = 'general';
        }

        $cycleImages = $images[$cycle] ?? [];
        if (empty($cycleImages)) {
            return ['url' => '', 'description' => ''];
        }

        $randomIndex = array_rand($cycleImages);
        return $cycleImages[$randomIndex];
    }

    private function loadQuotes(): array {
        if (!file_exists($this->quotesFile)) {
            return $this->createDefaultQuotes();
        }

        $data = json_decode(file_get_contents($this->quotesFile), true);
        return is_array($data) ? $data : [];
    }

    private function loadImages(): array {
        if (!file_exists($this->imagesFile)) {
            return $this->createDefaultImages();
        }

        $data = json_decode(file_get_contents($this->imagesFile), true);
        return is_array($data) ? $data : [];
    }

    private function createDefaultQuotes(): array {
        $defaultQuotes = [
            'physical' => [
                ['text' => 'Take care of your body. It\'s the only place you have to live.', 'author' => 'Jim Rohn'],
                ['text' => 'Physical fitness is not only one of the most important keys to a healthy body, it is the basis of dynamic and creative intellectual activity.', 'author' => 'John F. Kennedy'],
                ['text' => 'The groundwork for all happiness is good health.', 'author' => 'Leigh Hunt'],
                ['text' => 'Your body can stand almost anything. It\'s your mind that you have to convince.', 'author' => 'Unknown'],
                ['text' => 'Energy and persistence conquer all things.', 'author' => 'Benjamin Franklin']
            ],
            'emotional' => [
                ['text' => 'The best way to take care of the future is to take care of the present moment.', 'author' => 'Thich Nhat Hanh'],
                ['text' => 'Emotional intelligence is when you finally realize it\'s not all about you.', 'author' => 'Peter Stark'],
                ['text' => 'The emotion that can break your heart is sometimes the very one that heals it.', 'author' => 'Nicholas Sparks'],
                ['text' => 'Your emotions are the slaves to your thoughts, and you are the slave to your emotions.', 'author' => 'Elizabeth Gilbert'],
                ['text' => 'The greatest revolution of our generation is the discovery that human beings can alter their lives by altering their attitudes of mind.', 'author' => 'William James']
            ],
            'intellectual' => [
                ['text' => 'The mind is not a vessel to be filled, but a fire to be kindled.', 'author' => 'Plutarch'],
                ['text' => 'Intelligence is the ability to adapt to change.', 'author' => 'Stephen Hawking'],
                ['text' => 'The important thing is not to stop questioning. Curiosity has its own reason for existing.', 'author' => 'Albert Einstein'],
                ['text' => 'An investment in knowledge pays the best interest.', 'author' => 'Benjamin Franklin'],
                ['text' => 'The beautiful thing about learning is that no one can take it away from you.', 'author' => 'B.B. King']
            ],
            'general' => [
                ['text' => 'Life is what happens to you while you\'re busy making other plans.', 'author' => 'John Lennon'],
                ['text' => 'The future belongs to those who believe in the beauty of their dreams.', 'author' => 'Eleanor Roosevelt'],
                ['text' => 'It is during our darkest moments that we must focus to see the light.', 'author' => 'Aristotle'],
                ['text' => 'Success is not final, failure is not fatal: it is the courage to continue that counts.', 'author' => 'Winston Churchill'],
                ['text' => 'The only way to do great work is to love what you do.', 'author' => 'Steve Jobs']
            ]
        ];

        if (!is_dir(DATA_DIR)) {
            mkdir(DATA_DIR, 0755, true);
        }

        file_put_contents($this->quotesFile, json_encode($defaultQuotes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $defaultQuotes;
    }

    private function createDefaultImages(): array {
        $defaultImages = [
            'physical' => [
                ['url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=250&fit=crop', 'description' => 'Person running at sunrise'],
                ['url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400&h=250&fit=crop', 'description' => 'Yoga pose in nature'],
                ['url' => 'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?w=400&h=250&fit=crop', 'description' => 'Gym workout equipment'],
                ['url' => 'https://images.unsplash.com/photo-1506629905607-d3e42536bcbc?w=400&h=250&fit=crop', 'description' => 'Mountain hiking trail'],
                ['url' => 'https://images.unsplash.com/photo-1593079831268-3381b0db4a77?w=400&h=250&fit=crop', 'description' => 'Healthy lifestyle concept']
            ],
            'emotional' => [
                ['url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=250&fit=crop', 'description' => 'Peaceful sunset over water'],
                ['url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=250&fit=crop', 'description' => 'Serene forest path'],
                ['url' => 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=250&fit=crop', 'description' => 'Calm ocean waves'],
                ['url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=400&h=250&fit=crop', 'description' => 'Peaceful mountain lake'],
                ['url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=250&fit=crop', 'description' => 'Heart-shaped hands at sunset']
            ],
            'intellectual' => [
                ['url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=250&fit=crop', 'description' => 'Stack of books and reading'],
                ['url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=250&fit=crop', 'description' => 'Light bulb representing ideas'],
                ['url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=400&h=250&fit=crop', 'description' => 'Workspace with laptop and notes'],
                ['url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400&h=250&fit=crop', 'description' => 'Technology and innovation'],
                ['url' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=400&h=250&fit=crop', 'description' => 'Chess pieces strategy']
            ],
            'general' => [
                ['url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=250&fit=crop', 'description' => 'Beautiful sunrise inspiration'],
                ['url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=400&h=250&fit=crop', 'description' => 'Nature pathway forward'],
                ['url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=400&h=250&fit=crop', 'description' => 'Cosmic starry sky'],
                ['url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=250&fit=crop', 'description' => 'Motivational landscape'],
                ['url' => 'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=250&fit=crop', 'description' => 'Peaceful natural scene']
            ]
        ];

        if (!is_dir(DATA_DIR)) {
            mkdir(DATA_DIR, 0755, true);
        }

        file_put_contents($this->imagesFile, json_encode($defaultImages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $defaultImages;
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['cycle']) || empty($input['cycle'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cycle type is required']);
        exit;
    }

    try {
        $inspirationManager = new InspirationManager();
        $inspiration = $inspirationManager->getInspiration($input['cycle']);

        // ✅ User data folder convention: /data/users/{user_id}/
        // Log user-specific inspiration history to: /data/users/{user_id}/inspiration.json
        $userDir = DATA_DIR . '/users/' . $_SESSION['user_id'];
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }

        $logFile = $userDir . '/inspiration.json';
        $logs = [];
        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true);
            if (!is_array($logs)) {
                $logs = [];
            }
        }

        $logs[] = [
            'timestamp' => date('c'),
            'cycle' => $inspiration['cycle'],
            'quote' => $inspiration['quote'],
            'image' => $inspiration['image']
        ];

        if (count($logs) > 200) {
            $logs = array_slice($logs, -200);
        }

        file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        echo json_encode([
            'success' => true,
            'quote' => $inspiration['quote'],
            'image' => $inspiration['image'],
            'cycle' => $inspiration['cycle'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Only POST method allowed']);
}
