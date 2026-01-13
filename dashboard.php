<?php
ini_set('session.name', 'LC_IDENTIFIER');
session_start();

if (!isset($_SESSION['user_id'])) {
header('Location: index.html');
exit;
}

$username = $_SESSION['name'] ?? $_SESSION['email'] ?? 'User';
$userEmail = $_SESSION['email'] ?? '';
$userPicture = $_SESSION['profile_picture'] ?? '';
$birthdate = $_SESSION['data_nascita'] ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crescita Quotidiana 2.0 - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 60px 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 0 0 30px 30px;
            margin-bottom: 50px;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, #ffffff, #e8f4ff, #f0e6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            text-shadow: 0 4px 20px rgba(255, 255, 255, 0.3);
            letter-spacing: -2px;
        }

        .header p {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        /* Tools Grid */
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 0 0 60px 0;
        }

        .tool-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .tool-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 20px 20px 0 0;
        }

        .tool-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            background: rgba(255, 255, 255, 0.2);
        }

        .tool-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .tool-card h3 {
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .tool-card p {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .tool-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            width: 100%;
        }

        .tool-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a6fd8, #6a42a0);
        }

        /* Footer */
        .footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 30px 30px 0 0;
            padding: 40px 0;
            text-align: center;
            margin-top: 50px;
            box-shadow: 0 -8px 32px rgba(102, 126, 234, 0.2);
        }

        .footer-content {
            color: rgba(255, 255, 255, 0.9);
        }

        .footer h4 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .home-link {
            display: inline-block;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-top: 10px;
        }

        .home-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
        }
            
            .modal {
display:none;
position:fixed;
top:0; left:0; right:0; bottom:0;
background:rgba(0,0,0,0.6);
justify-content:center;
align-items:center;
}
.modal-content {
background:#fff;
padding:20px;
border-radius:8px;
min-width:300px;
}

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }
            
            .header p {
                font-size: 1.1rem;
            }
            
            .tools-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .tool-card {
                padding: 25px;
            }
        }

        /* Subtle animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .tool-icon {
            animation: float 3s ease-in-out infinite;
        }

        .tool-card:nth-child(2) .tool-icon { animation-delay: 0.5s; }
        .tool-card:nth-child(3) .tool-icon { animation-delay: 1s; }
        .tool-card:nth-child(4) .tool-icon { animation-delay: 1.5s; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Crescita Quotidiana 2.0 <small>(versione multi-utente)</small></h1>
            <p>Il tuo percorso verso una versione migliore di te stesso</p>
                <h2>Benvenuto, <?php echo htmlspecialchars($username); ?></h2>
                <button id="editProfileBtn" class="home-link">Modifica Profilo</button>
                <a class="home-link" href="./logout.php">Disconnetti &#8594;</a>
            
        </header>

        <!-- Tools Grid -->
        <main class="tools-grid">
            <div class="tool-card">
                <div class="tool-icon">📋</div>
                <h3>Planner Annuale</h3>
                <p>Organizza le tue giornate con efficacia. Pianifica, prioritizza e raggiungi i tuoi obiettivi quotidiani con il nostro sistema di gestione task intelligente.</p>
                <button class="tool-button" onclick="navigateToTool('planner.html')">Accedi al Planner</button>
            </div>

            <div class="tool-card">
                <div class="tool-icon">🌊</div>
                <h3>Calcolo Bioritmi</h3>
                <p>Scopri i tuoi cicli naturali di energia fisica, emotiva e intellettuale. Ottimizza le tue performance conoscendo i tuoi ritmi biologici personali.</p>
                <button class="tool-button" onclick="navigateToTool('bioritmi.html')">Calcola Bioritmi</button>
            </div>

            <div class="tool-card">
                <div class="tool-icon">💝</div>
                <h3>Emotion Tracker</h3>
                <p>Monitora il tuo benessere emotivo giorno dopo giorno. Identifica pattern, trigger e migliora la tua intelligenza emotiva attraverso l'auto-osservazione.</p>
                <button class="tool-button" onclick="navigateToTool('emotracker.html')">Traccia Emozioni</button>
            </div>
                
            <div class="tool-card">
                <div class="tool-icon">🏵️</div>
                <h3>Generatore di Mandala</h3>
                <p>Genera e scarica Mandala floreali e geometrici per migliorare il tuo focus e la tua meditazione; guardali animarsi e pulsare davanti a te.</p>
                <button class="tool-button" onclick="navigateToTool('mandala.html')">Genera Mandala</button>
            </div> 
                
            <div class="tool-card">
                <div class="tool-icon">♒</div>
                <h3>Zodiaco</h3>
                <p>Scopri il tuo Segno Zodiacale! Dalla tua data di nascita puoi scoprire il tuo Segno, le sue caratteristiche, i punti di forza e deboli.</p>
                <button class="tool-button" onclick="navigateToTool('zodiaco.html')">Scopri il tuo Segno</button>
            </div>                 
                
            <div class="tool-card">
                <div class="tool-icon">🏥</div>
                <h3>Assistente Alimentare</h3>
                <p>Scopri il tuo Indice di Massa Corporea (BMI), inserisci le tue patologie ed abitudini alimentari per ricevere consigli personalizzati.</p>
                <button class="tool-button" onclick="navigateToTool('alimentazione.html')">Avvia l'Assistente</button>
            </div>                  

            <div class="tool-card">
                <div class="tool-icon">📖</div>
                <h3>Diario Personale</h3>
                <p>Scrivi il tuo diario personale con colori personalizzati. Registra i tuoi pensieri, emozioni e riflessioni quotidiane in un ambiente privato e sicuro.</p>
                <button class="tool-button" onclick="navigateToTool('diario.html')">Apri Diario</button>
            </div>


        </main>

<!-- Modale Profilo -->
<div id="editModal" class="modal">
<div class="modal-content">
<h3>Modifica Profilo</h3>
<form id="profileForm">

<label>Data di nascita:</label><br>
<input type="date" name="data_nascita" required><br>

<label>Ora di nascita:</label><br>
<input type="time" name="ora_nascita" required><br>

<label>Città di nascita:</label><br>
<input type="text" name="citta_nascita" required><br><br>

<button type="submit">Salva</button>
<button type="button" id="closeModal">Chiudi</button>
</form>
</div>
</div>            
            
            
        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <h4>Crescita Quotidiana</h4>
                <p>La tua crescita personale inizia qui, un giorno alla volta.</p>
                <a href="./logout.php" class="home-link">Disconnetti &#8594;</a>
            </div>
        </footer>
    </div>

    <script>
        function navigateToTool(tool) {
            // Simula la navigazione verso il tool specifico
            //alert(`Navigazione verso: ${tool.charAt(0).toUpperCase() + tool.slice(1)}`);
            // In una vera applicazione, qui faresti:
            window.location.href = tool;
        }

        function navigateHome() {
            // Simula la navigazione verso la homepage
            //alert('Navigazione verso la Homepage');
            // In una vera applicazione, qui faresti:
            window.location.href = 'https://www.vivacitydesign.net/index.php';
        }

        // Aggiungi un effetto di parallasse leggero al mouse
        document.addEventListener('mousemove', (e) => {
            const cards = document.querySelectorAll('.tool-card');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            cards.forEach((card, index) => {
                const speed = (index + 1) * 0.5;
                const xOffset = (x - 0.5) * speed;
                const yOffset = (y - 0.5) * speed;
                
                card.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
            });
        });
            
            const modal = document.getElementById('editModal');
document.getElementById('editProfileBtn').onclick = () => modal.style.display = 'flex';
document.getElementById('closeModal').onclick = () => modal.style.display = 'none';

document.getElementById('profileForm').addEventListener('submit', function(e){
e.preventDefault();
const formData = new FormData(this);
fetch('php/update_user.php', {
method:'POST',
body: formData
}).then(res => res.json()).then(resp => {
if(resp.success) {
alert('Dati salvati!');
modal.style.display='none';
} else {
alert('Errore: ' + (resp.error || 'impossibile salvare.'));
}
});
});
            
    </script>
</body>
</html>
