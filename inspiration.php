<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

class InspirationManager {
    private $quotesFile = 'data/quotes.json';
    private $imagesFile = 'data/images.json';
    
    public function getInspiration($cycle) {
        $quote = $this->getQuoteForCycle($cycle);
        $image = $this->getImageForCycle($cycle);
        
        return [
            'quote' => $quote,
            'image' => $image,
            'cycle' => $cycle
        ];
    }
    
    private function getQuoteForCycle($cycle) {
        $quotes = $this->loadQuotes();
        
        if (!isset($quotes[$cycle]) || empty($quotes[$cycle])) {
            // Fallback to general quotes
            $cycle = 'general';
        }
        
        $cycleQuotes = $quotes[$cycle];
        $randomIndex = array_rand($cycleQuotes);
        
        return $cycleQuotes[$randomIndex];
    }
    
    private function getImageForCycle($cycle) {
        $images = $this->loadImages();
        
        if (!isset($images[$cycle]) || empty($images[$cycle])) {
            // Fallback to general images
            $cycle = 'general';
        }
        
        $cycleImages = $images[$cycle];
        $randomIndex = array_rand($cycleImages);
        
        return $cycleImages[$randomIndex];
    }
    
    private function loadQuotes() {
        if (!file_exists($this->quotesFile)) {
            $this->createDefaultQuotes();
        }
        
        return json_decode(file_get_contents($this->quotesFile), true);
    }
    
    private function loadImages() {
        if (!file_exists($this->imagesFile)) {
            $this->createDefaultImages();
        }
        
        return json_decode(file_get_contents($this->imagesFile), true);
    }
    
    private function createDefaultQuotes() {
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
        
        if (!is_dir('data')) {
            mkdir('data', 0755, true);
        }
        
        file_put_contents($this->quotesFile, json_encode($defaultQuotes, JSON_PRETTY_PRINT));
        return $defaultQuotes;
    }
    
    private function createDefaultImages() {
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
        
        if (!is_dir('data')) {
            mkdir('data', 0755, true);
        }
        
        file_put_contents($this->imagesFile, json_encode($defaultImages, JSON_PRETTY_PRINT));
        return $defaultImages;
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['cycle']) || empty($input['cycle'])) {
        echo json_encode(['success' => false, 'error' => 'Cycle type is required']);
        exit;
    }
    
    try {
        $inspirationManager = new InspirationManager();
        $inspiration = $inspirationManager->getInspiration($input['cycle']);
        
        echo json_encode([
            'success' => true,
            'quote' => $inspiration['quote'],
            'image' => $inspiration['image'],
            'cycle' => $inspiration['cycle'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Only POST method allowed']);
}
?>