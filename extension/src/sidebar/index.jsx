import { render } from 'preact';
import { useState, useEffect } from 'preact/hooks';

function Sidebar() {
  const [tab, setTab] = useState('bookmarks');
  const [highlights, setHighlights] = useState([]);
  const [notes, setNotes] = useState([]);
  const [bookmarks, setBookmarks] = useState([]);
  const [search, setSearch] = useState('');
  const [currentUrl, setCurrentUrl] = useState('');
  const [authenticated, setAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);
  const [darkMode, setDarkMode] = useState(false);
  const [newNote, setNewNote] = useState('');

  useEffect(() => {
    init();
  }, []);

  async function init() {
    try {
      const { bvDarkMode } = await chrome.storage.local.get('bvDarkMode');
      if (bvDarkMode) setDarkMode(true);

      const authRes = await chrome.runtime.sendMessage({ type: 'CHECK_AUTH' });
      setAuthenticated(authRes.authenticated);

      if (authRes.authenticated) {
        const [activeTab] = await chrome.tabs.query({ active: true, currentWindow: true });
        if (activeTab) {
          setCurrentUrl(activeTab.url);
          loadHighlights(activeTab.url);
        }
        loadBookmarks();
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  }

  async function loadBookmarks(query = '') {
    try {
      const res = await chrome.runtime.sendMessage({
        type: 'GET_BOOKMARKS',
        params: { search: query, per_page: 20 }
      });
      if (res.success && res.bookmarks?.data) {
        setBookmarks(res.bookmarks.data);
      }
    } catch (e) {
      console.error(e);
    }
  }

  useEffect(() => {
    if (tab === 'bookmarks') {
      const timeoutId = setTimeout(() => loadBookmarks(search), 300);
      return () => clearTimeout(timeoutId);
    }
  }, [search, tab]);

  function toggleDarkMode() {
    const newMode = !darkMode;
    setDarkMode(newMode);
    chrome.storage.local.set({ bvDarkMode: newMode });
  }

  async function loadHighlights(url) {
    try {
      const res = await chrome.runtime.sendMessage({
        type: 'GET_HIGHLIGHTS_FOR_URL',
        url,
      });
      if (res.success && res.highlights?.data) {
        setHighlights(res.highlights.data);
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function saveNote() {
    if (!newNote.trim()) return;
    try {
      await chrome.runtime.sendMessage({
        type: 'SAVE_NOTE',
        data: {
          title: `Note from ${new URL(currentUrl).hostname}`,
          content: newNote,
          content_plain: newNote,
        },
      });
      setNewNote('');
    } catch (e) {
      console.error(e);
    }
  }

  if (loading) {
    return (
      <div class="flex items-center justify-center h-screen">
        <div class="w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (!authenticated) {
    return (
      <div class="p-4 text-center mt-8">
        <p class="text-sm text-gray-500 mb-3">Please connect your account to view highlights.</p>
        <p class="text-xs text-gray-400">Open the extension popup to log in.</p>
      </div>
    );
  }

  return (
    <div class={`flex flex-col h-screen transition-colors ${darkMode ? 'dark bg-gray-900 text-white' : 'bg-white text-gray-900'}`}>
      {/* Header */}
      <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-indigo-500 rounded-lg flex items-center justify-center">
              <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white">BrainVault</span>
          </div>
          <button onClick={toggleDarkMode} class="text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200">
            {darkMode ? '☀️' : '🌙'}
          </button>
        </div>

        {/* Tab Switcher */}
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5">
          {['bookmarks', 'highlights', 'notes'].map(t => (
            <button
              key={t}
              onClick={() => setTab(t)}
              class={`flex-1 py-1.5 text-xs font-medium rounded-md transition-colors ${
                tab === t ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
              }`}
            >
              {t.charAt(0).toUpperCase() + t.slice(1)}
            </button>
          ))}
        </div>
      </div>

      {/* Content */}
      <div class="flex-1 overflow-y-auto">
        {tab === 'bookmarks' && (
          <div class="p-4 flex flex-col gap-3">
            <input
              type="text"
              placeholder="Search bookmarks..."
              value={search}
              onInput={(e) => setSearch(e.target.value)}
              class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white focus:outline-none"
            />
            <div class="space-y-2 mt-2">
              {bookmarks.length === 0 ? (
                <p class="text-center text-sm text-gray-500">No bookmarks found.</p>
              ) : (
                bookmarks.map(b => (
                  <a key={b.id} href={b.url} target="_blank" class="block p-3 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1">{b.title || b.url}</h3>
                    <p class="text-xs text-gray-500 truncate mt-1">{b.url}</p>
                    {b.tags && b.tags.length > 0 && (
                      <div class="flex flex-wrap gap-1 mt-2">
                        {b.tags.map(t => (
                          <span class="text-[10px] px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 rounded text-gray-700 dark:text-gray-300">{t.name}</span>
                        ))}
                      </div>
                    )}
                  </a>
                ))
              )}
            </div>
          </div>
        )}

        {tab === 'highlights' && (
          <div class="p-4">
            {highlights.length === 0 ? (
              <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No highlights on this page</p>
                <p class="text-xs text-gray-400 mt-1">Select text to highlight it</p>
              </div>
            ) : (
              <div class="space-y-3">
                {highlights.map(h => (
                  <div key={h.id} class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl border-l-4" style={{ borderLeftColor: h.color || '#FBBF24' }}>
                    <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed">"{h.text}"</p>
                    {h.note && (
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">{h.note}</p>
                    )}
                    <span class="text-[10px] text-gray-400 mt-1 block">{new Date(h.created_at).toLocaleDateString()}</span>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {tab === 'notes' && (
          <div class="p-4">
            <div class="mb-4">
              <textarea
                value={newNote}
                onInput={(e) => setNewNote(e.target.value)}
                placeholder="Write a quick note..."
                rows="3"
                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none transition-colors"
              />
              <button
                onClick={saveNote}
                disabled={!newNote.trim()}
                class="mt-2 w-full py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-semibold rounded-xl transition-colors"
              >
                Save Note
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

render(<Sidebar />, document.getElementById('app'));
