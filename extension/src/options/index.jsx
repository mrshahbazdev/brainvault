import { render } from 'preact';
import { useState, useEffect } from 'preact/hooks';
import { getSettings, saveSetting } from '../utils/storage.js';

function Options() {
  const [settings, setSettings] = useState({});
  const [saved, setSaved] = useState(false);
  const [user, setUser] = useState(null);

  useEffect(() => {
    loadSettings();
    loadUser();
  }, []);

  async function loadSettings() {
    const s = await getSettings();
    setSettings(s);
  }

  async function loadUser() {
    try {
      const res = await chrome.runtime.sendMessage({ type: 'GET_USER' });
      if (res.success) setUser(res.user);
    } catch (e) { /* not authenticated */ }
  }

  async function updateSetting(key, value) {
    await saveSetting(key, value);
    setSettings(prev => ({ ...prev, [key]: value }));
    setSaved(true);
    setTimeout(() => setSaved(false), 2000);
  }

  return (
    <div class="max-w-2xl mx-auto p-8">
      <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">BrainVault Settings</h1>
          <p class="text-sm text-gray-500">Configure your extension preferences</p>
        </div>
      </div>

      {saved && (
        <div class="mb-6 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
          Settings saved!
        </div>
      )}

      {/* Account */}
      <section class="mb-8 p-6 bg-white border border-gray-200 rounded-2xl">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Account</h2>
        {user ? (
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-semibold">
              {user.name?.charAt(0)?.toUpperCase()}
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">{user.name}</p>
              <p class="text-xs text-gray-500">{user.email}</p>
            </div>
            <button
              onClick={async () => {
                await chrome.runtime.sendMessage({ type: 'LOGOUT' });
                setUser(null);
              }}
              class="ml-auto px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            >
              Disconnect
            </button>
          </div>
        ) : (
          <div>
            <p class="text-sm text-gray-500 mb-3">Not connected. Enter your API token to connect.</p>
            <div class="flex gap-2">
              <input
                type="text"
                placeholder="Paste API token..."
                class="flex-1 px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                id="token-input"
              />
              <button
                onClick={async () => {
                  const input = document.getElementById('token-input');
                  if (input.value) {
                    await chrome.runtime.sendMessage({ type: 'SET_TOKEN', token: input.value });
                    loadUser();
                  }
                }}
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors"
              >
                Connect
              </button>
            </div>
          </div>
        )}
      </section>

      {/* Server */}
      <section class="mb-8 p-6 bg-white border border-gray-200 rounded-2xl">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Server</h2>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">API Base URL</label>
          <input
            type="url"
            value={settings.apiBaseUrl || ''}
            onChange={(e) => updateSetting('apiBaseUrl', e.target.value)}
            class="w-full px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          />
          <p class="mt-1 text-xs text-gray-400">Default: https://brainvault.allocore.de</p>
        </div>
      </section>

      {/* Highlighting */}
      <section class="mb-8 p-6 bg-white border border-gray-200 rounded-2xl">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Highlighting</h2>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Default Color</label>
          <div class="flex gap-2">
            {['#FBBF24', '#22C55E', '#3B82F6', '#EC4899', '#8B5CF6'].map(color => (
              <button
                key={color}
                onClick={() => updateSetting('highlightColor', color)}
                class={`w-8 h-8 rounded-full transition-transform ${settings.highlightColor === color ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : 'hover:scale-110'}`}
                style={{ backgroundColor: color }}
              />
            ))}
          </div>
        </div>
      </section>

      {/* Behavior */}
      <section class="p-6 bg-white border border-gray-200 rounded-2xl">
        <h2 class="text-base font-semibold text-gray-900 mb-4">Behavior</h2>
        <div class="space-y-4">
          <label class="flex items-center justify-between">
            <div>
              <span class="text-sm font-medium text-gray-700">Auto-save bookmarks</span>
              <p class="text-xs text-gray-400">Automatically save pages you visit</p>
            </div>
            <input
              type="checkbox"
              checked={settings.autoSave}
              onChange={(e) => updateSetting('autoSave', e.target.checked)}
              class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500"
            />
          </label>
          <label class="flex items-center justify-between">
            <div>
              <span class="text-sm font-medium text-gray-700">Show notifications</span>
              <p class="text-xs text-gray-400">Browser notifications when bookmarks are saved</p>
            </div>
            <input
              type="checkbox"
              checked={settings.notifications}
              onChange={(e) => updateSetting('notifications', e.target.checked)}
              class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500"
            />
          </label>
        </div>
      </section>
    </div>
  );
}

render(<Options />, document.getElementById('app'));
