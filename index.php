<?php
/**
 * Control Center v3 - Greenfield SPA Shell
 * PRJ-2026-0001_ControlCenter_v3 | EPIC-2026-CCV3-GREENFIELD
 * 
 * Schlanke SPA-Shell mit dynamischem View-Router für views/*.html
 */

$default_view = 'dashboard';
$view = isset($_GET['view']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['view']) : $default_view;
if (empty($view)) {
    $view = $default_view;
}

$views_dir = __DIR__ . '/views';
$view_file = $views_dir . '/' . $view . '.html';

if (!file_exists($view_file)) {
    $view = '404';
    $view_file = $views_dir . '/404.html';
}

$page_title = 'Control Center v3 • ' . ucfirst($view);
?>
<!DOCTYPE html>
<html lang="de" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: {
                            950: '#0b0f19',
                            900: '#111827',
                            850: '#151e32',
                            800: '#1f2937',
                            700: '#374151',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Segoe UI', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'monospace']
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Cyber Design System -->
    <link rel="stylesheet" href="00_brand_dna/cyber_design_system.css">
    <style>
        body {
            background-color: var(--cc-bg, #0A0A0F);
            color: var(--cc-text-main, #F4F4F5);
        }
        .nav-link.active {
            background: rgba(0, 240, 255, 0.12);
            color: #00F0FF;
            border-color: rgba(0, 240, 255, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-slate-900/90 backdrop-blur border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 flex items-center justify-center font-black text-slate-950 text-sm shadow-lg shadow-cyan-500/20">
                    V3
                </div>
                <div>
                    <h1 class="text-sm font-black text-white tracking-wider flex items-center gap-2">
                        CONTROL CENTER <span class="text-[10px] font-mono font-normal text-cyan-400 bg-cyan-950/80 px-2 py-0.5 rounded border border-cyan-800/40">POD v3</span>
                    </h1>
                    <p class="text-[10px] text-slate-400 font-mono">AIFoxHUB/Control_Center_v3</p>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-1 font-mono text-xs">
                <a href="?view=dashboard" class="nav-link px-3 py-1.5 rounded-lg border border-transparent transition-all <?= $view === 'dashboard' ? 'active' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-chart-line mr-1.5"></i> Dashboard
                </a>
                <a href="?view=agents" class="nav-link px-3 py-1.5 rounded-lg border border-transparent transition-all <?= $view === 'agents' ? 'active' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-robot mr-1.5"></i> Agent Swarm
                </a>
                <a href="?view=telemetry" class="nav-link px-3 py-1.5 rounded-lg border border-transparent transition-all <?= $view === 'telemetry' ? 'active' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                    <i class="fa-solid fa-satellite mr-1.5"></i> Telemetry
                </a>
            </nav>

            <!-- User & Node Indicator -->
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold text-slate-200">Patrice Fuchs</div>
                    <div class="text-[10px] font-mono text-cyan-400">IT-Signs Master Node</div>
                </div>
                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-cyan-400 font-bold text-xs">
                    PF
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8" id="spa-content">
        <?php
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo '<div class="p-6 text-rose-400 font-mono">Fehler: View konnte nicht geladen werden.</div>';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-850 py-4 text-center text-xs text-slate-500 font-mono">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>IT-Signs • Brückenstraße 65, 66557 Illingen • Mobil: +49 152 33537341</span>
            <span class="text-cyan-500/80">PRJ-2026-0001_ControlCenter_v3 • 100% Zero-Defect Architecture</span>
        </div>
    </footer>

</body>
</html>
