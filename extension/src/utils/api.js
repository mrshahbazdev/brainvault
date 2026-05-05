const DEFAULT_BASE_URL = 'https://brainvault.allocore.de';

class BrainVaultAPI {
  constructor() {
    this.baseUrl = DEFAULT_BASE_URL;
    this.token = null;
  }

  async init() {
    const data = await chrome.storage.sync.get(['apiToken', 'apiBaseUrl']);
    this.token = data.apiToken || null;
    this.baseUrl = data.apiBaseUrl || DEFAULT_BASE_URL;
  }

  async setToken(token) {
    this.token = token;
    await chrome.storage.sync.set({ apiToken: token });
  }

  async setBaseUrl(url) {
    this.baseUrl = url;
    await chrome.storage.sync.set({ apiBaseUrl: url });
  }

  isAuthenticated() {
    return !!this.token;
  }

  async request(method, path, body = null) {
    if (!this.token) {
      throw new Error('Not authenticated');
    }

    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${this.token}`,
    };

    const options = { method, headers };
    if (body && method !== 'GET') {
      options.body = JSON.stringify(body);
    }

    const url = method === 'GET' && body
      ? `${this.baseUrl}/api${path}?${new URLSearchParams(body)}`
      : `${this.baseUrl}/api${path}`;

    const response = await fetch(url, options);

    if (response.status === 401) {
      await chrome.storage.sync.remove('apiToken');
      this.token = null;
      throw new Error('Unauthorized');
    }

    if (!response.ok) {
      const error = await response.json().catch(() => ({}));
      throw new Error(error.message || `HTTP ${response.status}`);
    }

    if (response.status === 204) return null;
    return response.json();
  }

  // Bookmarks
  async getBookmarks(params = {}) {
    return this.request('GET', '/bookmarks', params);
  }

  async createBookmark(data) {
    return this.request('POST', '/bookmarks', data);
  }

  async updateBookmark(id, data) {
    return this.request('PUT', `/bookmarks/${id}`, data);
  }

  async deleteBookmark(id) {
    return this.request('DELETE', `/bookmarks/${id}`);
  }

  // Collections
  async getCollections() {
    return this.request('GET', '/collections');
  }

  // Notes
  async createNote(data) {
    return this.request('POST', '/notes', data);
  }

  // Highlights
  async getHighlights(params = {}) {
    return this.request('GET', '/highlights', params);
  }

  async createHighlight(data) {
    return this.request('POST', '/highlights', data);
  }

  async deleteHighlight(id) {
    return this.request('DELETE', `/highlights/${id}`);
  }

  // Tags
  async getTags() {
    return this.request('GET', '/tags');
  }

  // User
  async getUser() {
    return this.request('GET', '/user');
  }
}

export const api = new BrainVaultAPI();
