import { api } from '../utils/api.js';

// Initialize API on startup
api.init();

// Offline queueing
async function queueOfflineBookmark(data) {
  const { offlineQueue = [] } = await chrome.storage.local.get('offlineQueue');
  offlineQueue.push(data);
  await chrome.storage.local.set({ offlineQueue });
}

async function processOfflineQueue() {
  if (!navigator.onLine) return;
  const { offlineQueue = [] } = await chrome.storage.local.get('offlineQueue');
  if (offlineQueue.length === 0) return;

  const remainingQueue = [];
  await api.init();
  if (!api.isAuthenticated()) return;

  for (const data of offlineQueue) {
    try {
      await api.createBookmark(data);
    } catch (err) {
      if (!navigator.onLine || err.message.includes('Failed to fetch')) {
        remainingQueue.push(data);
      }
    }
  }

  await chrome.storage.local.set({ offlineQueue: remainingQueue });
}

self.addEventListener('online', processOfflineQueue);
// Process queue when extension starts up or wakes up
processOfflineQueue();

// Context menu for right-click "Save to BrainVault"
chrome.runtime.onInstalled.addListener(() => {
  chrome.contextMenus.create({
    id: 'save-to-brainvault',
    title: 'Save to BrainVault',
    contexts: ['page', 'link'],
  });

  chrome.contextMenus.create({
    id: 'save-selection-brainvault',
    title: 'Save Selection as Highlight',
    contexts: ['selection'],
  });

  chrome.contextMenus.create({
    id: 'save-image-brainvault',
    title: 'Save Image to BrainVault',
    contexts: ['image'],
  });
});

// Handle context menu clicks
chrome.contextMenus.onClicked.addListener(async (info, tab) => {
  await api.init();

  if (!api.isAuthenticated()) {
    chrome.notifications.create({
      type: 'basic',
      iconUrl: 'icons/icon-48.png',
      title: 'BrainVault',
      message: 'Please log in to save bookmarks.',
    });
    return;
  }

  try {
    if (info.menuItemId === 'save-to-brainvault') {
      const url = info.linkUrl || info.pageUrl;
      const title = tab.title || '';

      await api.createBookmark({ url, title });

      chrome.notifications.create({
        type: 'basic',
        iconUrl: 'icons/icon-48.png',
        title: 'BrainVault',
        message: 'Bookmark saved successfully!',
      });

      updateBadge(tab.id);
    }

    if (info.menuItemId === 'save-image-brainvault') {
      const url = info.srcUrl || info.pageUrl;
      const title = 'Image from ' + (tab.title || 'Web');
      await api.createBookmark({ url, title });
      chrome.notifications.create({ type: 'basic', iconUrl: 'icons/icon-48.png', title: 'BrainVault', message: 'Image saved!' });
      updateBadge(tab.id);
    }

    if (info.menuItemId === 'save-selection-brainvault' && info.selectionText) {
      // Send message to content script to get selection details
      chrome.tabs.sendMessage(tab.id, {
        type: 'GET_SELECTION_DETAILS',
        text: info.selectionText,
      });
    }
  } catch (error) {
    chrome.notifications.create({
      type: 'basic',
      iconUrl: 'icons/icon-48.png',
      title: 'BrainVault Error',
      message: error.message || 'Failed to save.',
    });
  }
});

// Handle messages from popup and content scripts
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  handleMessage(message, sender).then(sendResponse).catch(err => {
    sendResponse({ error: err.message });
  });
  return true; // Keep message channel open for async response
});

async function handleMessage(message, sender) {
  await api.init();

  switch (message.type) {
    case 'SAVE_BOOKMARK': {
      try {
        const bookmark = await api.createBookmark(message.data);
        return { success: true, bookmark };
      } catch (err) {
        if (!navigator.onLine || err.message === 'Failed to fetch') {
          await queueOfflineBookmark(message.data);
          return { success: true, queued: true };
        }
        throw err;
      }
    }

    case 'CHECK_URL_SAVED': {
      const bookmark = await api.checkUrlSaved(message.url);
      return { success: true, bookmark };
    }

    case 'SAVE_HIGHLIGHT': {
      const highlight = await api.createHighlight(message.data);
      return { success: true, highlight };
    }

    case 'SAVE_NOTE': {
      const note = await api.createNote(message.data);
      return { success: true, note };
    }

    case 'GET_BOOKMARKS': {
      const bookmarks = await api.getBookmarks(message.params);
      return { success: true, bookmarks };
    }

    case 'GET_COLLECTIONS': {
      const collections = await api.getCollections();
      return { success: true, collections };
    }

    case 'GET_HIGHLIGHTS_FOR_URL': {
      const highlights = await api.getHighlights({ page_url: message.url });
      return { success: true, highlights };
    }

    case 'GET_USER': {
      const user = await api.getUser();
      return { success: true, user };
    }

    case 'CHECK_AUTH': {
      return { authenticated: api.isAuthenticated() };
    }

    case 'SET_TOKEN': {
      await api.setToken(message.token);
      return { success: true };
    }

    case 'LOGOUT': {
      await chrome.storage.sync.remove('apiToken');
      api.token = null;
      return { success: true };
    }

    case 'SET_REMINDER': {
      // Create alarm for the reminder (delayInMinutes)
      chrome.alarms.create(`remind-bookmark-${Date.now()}`, { delayInMinutes: message.delayInMinutes || 2880 /* 48 hours */ });
      return { success: true };
    }

    default:
      return { error: 'Unknown message type' };
  }
}

// Alarms
chrome.alarms.onAlarm.addListener((alarm) => {
  if (alarm.name.startsWith('remind-bookmark-')) {
    chrome.notifications.create({
      type: 'basic',
      iconUrl: 'icons/icon-48.png',
      title: 'BrainVault Reminder',
      message: 'Time to read a bookmark you saved for later!',
    });
  }
});

// Omnibox
chrome.omnibox.onInputChanged.addListener(async (text, suggest) => {
  if (!text || text.length < 2) return;
  await api.init();
  if (!api.isAuthenticated()) return;
  
  try {
    const res = await api.getBookmarks({ search: text, per_page: 5 });
    if (res && res.data && res.data.length > 0) {
      const suggestions = res.data.map(b => ({
        content: b.url,
        description: `<match>${escapeXml(b.title || b.url)}</match> - <url>${escapeXml(b.url)}</url>`
      }));
      suggest(suggestions);
    }
  } catch (e) {}
});

chrome.omnibox.onInputEntered.addListener((text) => {
  const url = text.startsWith('http') ? text : `https://brainvault.allocore.de/dashboard?search=${encodeURIComponent(text)}`;
  chrome.tabs.create({ url });
});

function escapeXml(unsafe) {
  return (unsafe || '').replace(/[<>&'"]/g, function (c) {
    switch (c) {
      case '<': return '&lt;';
      case '>': return '&gt;';
      case '&': return '&amp;';
      case '\'': return '&apos;';
      case '"': return '&quot;';
    }
  });
}

// Update badge with saved status
async function updateBadge(tabId) {
  chrome.action.setBadgeBackgroundColor({ color: '#6366F1' });
  chrome.action.setBadgeText({ text: '1', tabId });

  setTimeout(() => {
    chrome.action.setBadgeText({ text: '', tabId });
  }, 2000);
}

// Open side panel when extension icon is clicked (with Shift)
chrome.action.onClicked.addListener((tab) => {
  chrome.sidePanel.open({ tabId: tab.id });
});
