export async function getSettings() {
  const defaults = {
    apiToken: '',
    apiBaseUrl: 'http://localhost:8000',
    highlightColor: '#FBBF24',
    autoSave: false,
    showSidebar: true,
    notifications: true,
  };

  const data = await chrome.storage.sync.get(Object.keys(defaults));
  return { ...defaults, ...data };
}

export async function saveSetting(key, value) {
  await chrome.storage.sync.set({ [key]: value });
}

export async function getHighlightsForUrl(url) {
  const data = await chrome.storage.local.get(`highlights_${url}`);
  return data[`highlights_${url}`] || [];
}

export async function cacheHighlightsForUrl(url, highlights) {
  await chrome.storage.local.set({ [`highlights_${url}`]: highlights });
}
