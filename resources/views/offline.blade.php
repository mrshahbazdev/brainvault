<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline - BrainVault</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Inter, -apple-system, sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { text-align: center; padding: 2rem; max-width: 28rem; }
        .icon { width: 4rem; height: 4rem; margin: 0 auto 1.5rem; background: rgba(99, 102, 241, 0.2); border-radius: 1rem; display: flex; align-items: center; justify-content: center; }
        .icon svg { width: 2rem; height: 2rem; color: #818cf8; }
        h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        p { color: #94a3b8; font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem; }
        button { background: #6366f1; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 600; cursor: pointer; font-size: 0.875rem; }
        button:hover { background: #4f46e5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3" /></svg>
        </div>
        <h1>You're Offline</h1>
        <p>It seems you've lost your internet connection. Your saved bookmarks and notes will sync once you're back online.</p>
        <button onclick="window.location.reload()">Try Again</button>
    </div>
</body>
</html>
