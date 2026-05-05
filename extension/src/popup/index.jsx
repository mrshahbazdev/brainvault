import { render } from 'preact';
import { useState, useEffect } from 'preact/hooks';

function App() {
  const [authenticated, setAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [offlineQueued, setOfflineQueued] = useState(false);
  const [isSaved, setIsSaved] = useState(false);
  const [tab, setTab] = useState(null);
  const [collections, setCollections] = useState([]);
  const [selectedCollection, setSelectedCollection] = useState('');
  const [title, setTitle] = useState('');
  const [tags, setTags] = useState('');
  const [note, setNote] = useState('');
  const [token, setToken] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    init();
  }, []);

  useEffect(() => {
    function handleKeyDown(e) {
      if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'Enter')) {
        e.preventDefault();
        if (authenticated && !saving && !saved) {
          handleSave();
        }
      }
    }
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [authenticated, saving, saved, tab, title, note, tags, selectedCollection]);

  async function init() {
    try {
      const [activeTab] = await chrome.tabs.query({ active: true, currentWindow: true });
      setTab(activeTab);
      setTitle(activeTab?.title || '');

      try {
        const selRes = await chrome.tabs.sendMessage(activeTab.id, { type: 'GET_SELECTION_TEXT' });
        if (selRes && selRes.text) {
          setNote(selRes.text);
        }
      } catch (e) {
        // Content script might not be injected
      }

      const authRes = await chrome.runtime.sendMessage({ type: 'CHECK_AUTH' });
      setAuthenticated(authRes.authenticated);

      if (authRes.authenticated) {
        const colRes = await chrome.runtime.sendMessage({ type: 'GET_COLLECTIONS' });
        if (colRes.success) setCollections(colRes.collections);

        const saveCheck = await chrome.runtime.sendMessage({ type: 'CHECK_URL_SAVED', url: activeTab.url });
        if (saveCheck.success && saveCheck.bookmark) {
          setIsSaved(true);
          setTitle(saveCheck.bookmark.title || activeTab.title);
          if (saveCheck.bookmark.tags && saveCheck.bookmark.tags.length > 0) {
            setTags(saveCheck.bookmark.tags.map(t => t.name).join(', '));
          }
          if (saveCheck.bookmark.collections && saveCheck.bookmark.collections.length > 0) {
            setSelectedCollection(saveCheck.bookmark.collections[0].id.toString());
          }
        }
      }
    } catch (e) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }

  async function handleLogin(e) {
    e.preventDefault();
    setError('');
    try {
      await chrome.runtime.sendMessage({ type: 'SET_TOKEN', token });
      const userRes = await chrome.runtime.sendMessage({ type: 'GET_USER' });
      if (userRes.success) {
        setAuthenticated(true);
        const colRes = await chrome.runtime.sendMessage({ type: 'GET_COLLECTIONS' });
        if (colRes.success) setCollections(colRes.collections);
      } else {
        setError('Invalid token');
        await chrome.runtime.sendMessage({ type: 'LOGOUT' });
      }
    } catch (e) {
      setError('Failed to authenticate');
    }
  }

  async function handleSave() {
    if (!tab) return;
    setSaving(true);
    setError('');

    try {
      const data = {
        url: tab.url,
        title: title || tab.title,
      };
      if (selectedCollection) {
        data.collection_ids = [parseInt(selectedCollection)];
      }
      if (tags.trim()) {
        data.tags = tags.split(',').map(t => t.trim()).filter(Boolean);
      }

      const res = await chrome.runtime.sendMessage({ type: 'SAVE_BOOKMARK', data });
      if (res.success) {
        setSaved(true);
        if (res.queued) {
          setOfflineQueued(true);
        }
        // Save note if provided and online
        if (note.trim() && !res.queued && res.bookmark) {
          await chrome.runtime.sendMessage({
            type: 'SAVE_NOTE',
            data: {
              title: `Note on: ${title}`,
              content: note,
              content_plain: note,
              bookmark_id: res.bookmark.id,
            },
          });
        }
      } else {
        setError(res.error || 'Failed to save');
      }
    } catch (e) {
      setError(e.message);
    } finally {
      setSaving(false);
    }
  }

  if (loading) {
    return (
      <div class="flex items-center justify-center min-h-[480px]">
        <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (!authenticated) {
    return (
      <div class="p-6 min-h-[480px] flex flex-col">
        <div class="text-center mb-6">
          <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
          </div>
          <h1 class="text-lg font-bold text-gray-900">BrainVault</h1>
          <p class="text-sm text-gray-500 mt-1">Connect your account to start saving</p>
        </div>

        <form onSubmit={handleLogin} class="space-y-4 flex-1">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">API Token</label>
            <input
              type="text"
              value={token}
              onInput={(e) => setToken(e.target.value)}
              placeholder="Paste your API token..."
              class="w-full px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            />
            <p class="mt-1 text-xs text-gray-400">
              Generate a token in Settings &rarr; API Tokens
            </p>
          </div>

          {error && (
            <div class="p-2 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-xs text-red-600">{error}</p>
            </div>
          )}

          <button
            type="submit"
            disabled={!token}
            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition-colors"
          >
            Connect Account
          </button>
        </form>

        <div class="mt-4 text-center">
          <a
            href="https://brainvault.allocore.de/register"
            target="_blank"
            class="text-xs text-indigo-600 hover:text-indigo-700"
          >
            Don't have an account? Sign up
          </a>
        </div>
      </div>
    );
  }

  if (saved) {
    return (
      <div class="flex flex-col items-center justify-center min-h-[480px] p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 mb-1">
          {offlineQueued ? 'Queued Offline!' : (isSaved ? 'Updated!' : 'Saved!')}
        </h2>
        <p class="text-sm text-gray-500 mb-6">
          {offlineQueued 
            ? 'Bookmark will sync when internet is restored' 
            : 'Bookmark safely stored in BrainVault'}
        </p>
        <div class="flex flex-col gap-2 w-full max-w-[200px]">
          {!offlineQueued && (
            <a
              href="https://brainvault.allocore.de/dashboard"
              target="_blank"
              class="w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2"
            >
              View in BrainVault
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
            </a>
          )}
          <button
            onClick={() => { setSaved(false); setNote(''); setIsSaved(true); setOfflineQueued(false); }}
            class="w-full px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    );
  }

  return (
    <div class="p-4 min-h-[480px] flex flex-col">
      {/* Header */}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
          </div>
          <h1 class="text-sm font-bold text-gray-900">Save Bookmark</h1>
        </div>
        <button
          onClick={async () => {
            await chrome.runtime.sendMessage({ type: 'LOGOUT' });
            setAuthenticated(false);
          }}
          class="text-xs text-gray-400 hover:text-gray-600"
        >
          Logout
        </button>
      </div>

      {/* Page Preview */}
      <div class="bg-gray-50 rounded-xl p-3 mb-4">
        <div class="flex items-start gap-2">
          {tab?.favIconUrl && (
            <img src={tab.favIconUrl} alt="" class="w-4 h-4 rounded mt-0.5" />
          )}
          <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-400 truncate">{tab?.url}</p>
          </div>
        </div>
      </div>

      {/* Form */}
      <div class="space-y-3 flex-1">
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
          <input
            type="text"
            value={title}
            onInput={(e) => setTitle(e.target.value)}
            class="w-full px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          />
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Collection</label>
          <select
            value={selectedCollection}
            onChange={(e) => setSelectedCollection(e.target.value)}
            class="w-full px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          >
            <option value="">No collection</option>
            {collections.map(c => (
              <option key={c.id} value={c.id}>{c.name}</option>
            ))}
          </select>
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Tags (comma separated)</label>
          <input
            type="text"
            value={tags}
            onInput={(e) => setTags(e.target.value)}
            placeholder="e.g. design, inspiration, ai"
            class="w-full px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          />
        </div>

        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Quick Note (optional)</label>
          <textarea
            value={note}
            onInput={(e) => setNote(e.target.value)}
            placeholder="Add a note about this page..."
            rows="3"
            class="w-full px-3 py-2 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"
          />
        </div>

        {error && (
          <div class="p-2 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-xs text-red-600">{error}</p>
          </div>
        )}
      </div>

      {/* Save Button */}
      <button
        onClick={handleSave}
        disabled={saving}
        title="Cmd/Ctrl + Enter to save"
        class="w-full mt-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2"
      >
        {saving ? (
          <>
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            Saving...
          </>
        ) : (
          <>
            {isSaved ? (
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            ) : (
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
            )}
            {isSaved ? 'Update Bookmark' : 'Save Bookmark'}
          </>
        )}
      </button>
    </div>
  );
}

render(<App />, document.getElementById('app'));
