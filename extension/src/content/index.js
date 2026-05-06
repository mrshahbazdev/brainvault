/**
 * BrainVault Content Script
 * Handles text highlighting, tooltip display, and highlight rendering.
 */

const HIGHLIGHT_COLORS = [
  { name: 'yellow', value: '#FBBF24' },
  { name: 'green', value: '#22C55E' },
  { name: 'blue', value: '#3B82F6' },
  { name: 'pink', value: '#EC4899' },
  { name: 'purple', value: '#8B5CF6' },
];

let tooltip = null;
let selectedRange = null;
let selectedColor = 'yellow';

// Listen for text selection
document.addEventListener('mouseup', handleMouseUp);
document.addEventListener('keyup', handleKeyUp);

function handleMouseUp(e) {
  // Ignore if clicking on our own tooltip
  if (e.target.closest('.brainvault-tooltip')) return;

  setTimeout(() => {
    const selection = window.getSelection();
    if (!selection || selection.isCollapsed || !selection.toString().trim()) {
      removeTooltip();
      return;
    }

    selectedRange = selection.getRangeAt(0);
    const text = selection.toString().trim();

    if (text.length < 3) {
      removeTooltip();
      return;
    }

    showTooltip(selectedRange, text);
  }, 10);
}

function handleKeyUp(e) {
  if (e.key === 'Escape') {
    removeTooltip();
  }
}

function showTooltip(range, text) {
  removeTooltip();

  const rect = range.getBoundingClientRect();

  tooltip = document.createElement('div');
  tooltip.className = 'brainvault-tooltip';
  tooltip.innerHTML = `
    ${HIGHLIGHT_COLORS.map(c => `
      <button class="color-btn" data-color="${c.name}" title="Highlight ${c.name}" style="background-color: ${c.value}33; border-color: ${c.value};">
      </button>
    `).join('')}
    <button class="save-btn" title="Save highlight">
      <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
      Save
    </button>
  `;

  // Position above selection
  const top = rect.top + window.scrollY - 44;
  const left = rect.left + window.scrollX + (rect.width / 2) - 100;

  tooltip.style.position = 'absolute';
  tooltip.style.top = `${Math.max(8, top)}px`;
  tooltip.style.left = `${Math.max(8, left)}px`;

  // Color button handlers - highlight AND save
  tooltip.querySelectorAll('.color-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      selectedColor = btn.dataset.color;
      saveHighlight(text, selectedColor);
    });
  });

  // Save button handler
  tooltip.querySelector('.save-btn').addEventListener('click', (e) => {
    e.stopPropagation();
    saveHighlight(text, selectedColor);
  });

  document.body.appendChild(tooltip);
}

function removeTooltip() {
  if (tooltip) {
    tooltip.remove();
    tooltip = null;
  }
}

function highlightSelection(text, color) {
  if (!selectedRange) return;

  try {
    const mark = document.createElement('mark');
    mark.className = 'brainvault-highlight';
    mark.dataset.color = color;
    mark.dataset.text = text;

    selectedRange.surroundContents(mark);
    window.getSelection()?.removeAllRanges();
    removeTooltip();
  } catch (e) {
    // surroundContents fails if selection spans elements
    console.warn('BrainVault: Could not highlight complex selection', e);
  }
}

async function saveHighlight(text, color) {
  if (!selectedRange) return;

  // Check authentication first
  try {
    const authCheck = await chrome.runtime.sendMessage({ type: 'CHECK_AUTH' });
    if (!authCheck?.authenticated) {
      showErrorNotification('Please log in to BrainVault extension first');
      removeTooltip();
      return;
    }
  } catch (e) {
    showErrorNotification('BrainVault extension error — try reloading the page');
    removeTooltip();
    return;
  }

  const data = {
    text,
    color: HIGHLIGHT_COLORS.find(c => c.name === color)?.value || '#FBBF24',
    page_url: normalizeUrl(window.location.href),
    start_xpath: getXPath(selectedRange.startContainer),
    start_offset: selectedRange.startOffset,
    end_xpath: getXPath(selectedRange.endContainer),
    end_offset: selectedRange.endOffset,
    surrounding_text: getSurroundingText(selectedRange),
  };

  // Save via API first, then highlight locally on success
  try {
    const response = await chrome.runtime.sendMessage({
      type: 'SAVE_HIGHLIGHT',
      data,
    });

    if (response?.success) {
      highlightSelection(text, color);
      showSavedNotification();
    } else {
      showErrorNotification(response?.error || 'Failed to save highlight');
    }
  } catch (e) {
    showErrorNotification('Failed to save — check your connection');
    console.warn('BrainVault: Failed to save highlight', e);
  }
}

function getXPath(node) {
  if (!node) return '';

  if (node.nodeType === Node.TEXT_NODE) {
    let textIndex = 1;
    let sib = node.previousSibling;
    while (sib) {
      if (sib.nodeType === Node.TEXT_NODE) textIndex++;
      sib = sib.previousSibling;
    }
    return getXPath(node.parentNode) + '/text()[' + textIndex + ']';
  }

  const parts = [];
  let current = node;

  while (current && current !== document.body) {
    let index = 1;
    let sibling = current.previousSibling;

    while (sibling) {
      if (sibling.nodeType === Node.ELEMENT_NODE && sibling.nodeName === current.nodeName) {
        index++;
      }
      sibling = sibling.previousSibling;
    }

    parts.unshift(`${current.nodeName.toLowerCase()}[${index}]`);
    current = current.parentNode;
  }

  return '//' + parts.join('/');
}

function getSurroundingText(range) {
  const container = range.commonAncestorContainer;
  const text = container.textContent || '';
  const start = Math.max(0, range.startOffset - 100);
  const end = Math.min(text.length, range.endOffset + 100);
  return text.substring(start, end);
}

function showSavedNotification() {
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed; bottom: 20px; right: 20px; z-index: 2147483647;
    background: #6366F1; color: white; padding: 12px 20px;
    border-radius: 12px; font-size: 14px; font-family: Inter, sans-serif;
    box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
    display: flex; align-items: center; gap: 8px;
    animation: brainvault-fadein 0.2s ease-out;
  `;
  notification.innerHTML = `
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    Highlight saved to BrainVault
  `;

  document.body.appendChild(notification);
  setTimeout(() => notification.remove(), 3000);
}

function showErrorNotification(msg) {
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed; bottom: 20px; right: 20px; z-index: 2147483647;
    background: #EF4444; color: white; padding: 12px 20px;
    border-radius: 12px; font-size: 14px; font-family: Inter, sans-serif;
    box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
    display: flex; align-items: center; gap: 8px;
    animation: brainvault-fadein 0.2s ease-out;
  `;
  notification.innerHTML = `
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    ${msg}
  `;

  document.body.appendChild(notification);
  setTimeout(() => notification.remove(), 5000);
}

function normalizeUrl(url) {
  try {
    const u = new URL(url);
    u.hash = '';
    return u.toString().replace(/\/+$/, '');
  } catch (e) {
    return url.split('#')[0].replace(/\/+$/, '');
  }
}

// Render saved highlights on page load
async function renderSavedHighlights() {
  const highlights = await fetchHighlightsForCurrentPage();
  if (!highlights || highlights.length === 0) return;

  let restored = restoreHighlights(highlights);

  // Retry after a delay for dynamically loaded content
  if (restored < highlights.length) {
    setTimeout(() => {
      restoreHighlights(highlights);
    }, 1500);
  }
}

async function fetchHighlightsForCurrentPage() {
  try {
    const response = await chrome.runtime.sendMessage({
      type: 'GET_HIGHLIGHTS_FOR_URL',
      url: normalizeUrl(window.location.href),
    });

    if (!response?.success || !response.highlights?.data) return [];
    return response.highlights.data;
  } catch (e) {
    return [];
  }
}

function restoreHighlights(highlights) {
  let restored = 0;
  for (const highlight of highlights) {
    // Skip if already restored
    if (document.querySelector(`[data-highlight-id="${highlight.id}"]`)) {
      restored++;
      continue;
    }
    try {
      if (restoreHighlightByXPath(highlight) || restoreHighlightByText(highlight)) {
        restored++;
      }
    } catch (e) {
      try {
        if (restoreHighlightByText(highlight)) restored++;
      } catch (e2) {}
    }
  }
  return restored;
}

function restoreHighlightByXPath(highlight) {
  if (!highlight.start_xpath) return false;

  try {
    const startResult = document.evaluate(
      highlight.start_xpath,
      document,
      null,
      XPathResult.FIRST_ORDERED_NODE_TYPE,
      null
    );

    const startNode = startResult.singleNodeValue;
    if (!startNode) return false;

    const startTextNode = startNode.nodeType === Node.TEXT_NODE ? startNode : startNode.firstChild;
    if (!startTextNode || startTextNode.nodeType !== Node.TEXT_NODE) return false;

    let endTextNode = startTextNode;
    if (highlight.end_xpath && highlight.end_xpath !== highlight.start_xpath) {
      const endResult = document.evaluate(
        highlight.end_xpath,
        document,
        null,
        XPathResult.FIRST_ORDERED_NODE_TYPE,
        null
      );
      const endNode = endResult.singleNodeValue;
      if (endNode) {
        endTextNode = endNode.nodeType === Node.TEXT_NODE ? endNode : endNode.firstChild;
      }
    }
    if (!endTextNode) endTextNode = startTextNode;

    const startOffset = Math.min(highlight.start_offset, startTextNode.length);
    const endOffset = Math.min(highlight.end_offset, endTextNode.length);
    if (startOffset >= startTextNode.length) return false;

    const range = document.createRange();
    range.setStart(startTextNode, startOffset);
    range.setEnd(endTextNode, endOffset);

    const rangeText = range.toString().trim();
    const highlightText = (highlight.text || '').trim();
    if (highlightText && rangeText && !rangeText.includes(highlightText.substring(0, 20)) && !highlightText.includes(rangeText.substring(0, 20))) {
      return false;
    }

    return applyHighlightMark(range, highlight);
  } catch (e) {
    return false;
  }
}

function restoreHighlightByText(highlight) {
  if (!highlight.text) return false;

  const searchText = highlight.text.trim();
  if (searchText.length < 3) return false;

  const treeWalker = document.createTreeWalker(
    document.body,
    NodeFilter.SHOW_TEXT,
    null
  );

  let node;
  while ((node = treeWalker.nextNode())) {
    const nodeText = node.textContent;
    const index = nodeText.indexOf(searchText);
    if (index === -1) continue;

    // Skip if already highlighted
    if (node.parentElement?.closest('.brainvault-highlight')) continue;

    const range = document.createRange();
    range.setStart(node, index);
    range.setEnd(node, index + searchText.length);

    return applyHighlightMark(range, highlight);
  }

  // Multi-node text search: the highlighted text may span across elements
  const bodyText = document.body.innerText;
  const idx = bodyText.indexOf(searchText);
  if (idx === -1) return false;

  // Use window.find as a last resort (highlights first visible match)
  const sel = window.getSelection();
  sel.removeAllRanges();
  if (window.find(searchText, false, false, false, false, false, false)) {
    const foundRange = sel.getRangeAt(0);
    sel.removeAllRanges();
    if (foundRange.startContainer.parentElement?.closest('.brainvault-highlight')) return false;
    return applyHighlightMark(foundRange, highlight);
  }

  return false;
}

function applyHighlightMark(range, highlight) {
  const mark = document.createElement('mark');
  mark.className = 'brainvault-highlight';
  mark.dataset.color = HIGHLIGHT_COLORS.find(c => c.value === highlight.color)?.name || 'yellow';
  mark.dataset.highlightId = highlight.id;

  try {
    range.surroundContents(mark);
    return true;
  } catch (e) {
    try {
      const fragment = range.extractContents();
      mark.appendChild(fragment);
      range.insertNode(mark);
      return true;
    } catch (e2) {
      return false;
    }
  }
}

// Handle messages from background
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === 'GET_SELECTION_DETAILS') {
    const selection = window.getSelection();
    if (selection && !selection.isCollapsed) {
      const range = selection.getRangeAt(0);
      saveHighlight(message.text, selectedColor);
    }
    sendResponse({ success: true });
  } else if (message.type === 'GET_SELECTION_TEXT') {
    const selection = window.getSelection();
    sendResponse({ text: selection && !selection.isCollapsed ? selection.toString().trim() : '' });
  } else if (message.type === 'GET_PAGE_DATA') {
    const selection = window.getSelection();
    const selectedText = selection && !selection.isCollapsed ? selection.toString().trim() : '';
    
    let bodyText = '';
    try {
      const clone = document.body.cloneNode(true);
      clone.querySelectorAll('script, style, nav, footer, iframe, noscript').forEach(el => el.remove());
      bodyText = clone.innerText.replace(/\s+/g, ' ').trim().substring(0, 5000);
    } catch (e) {}

    let videoTimestamp = null;
    if (window.location.hostname.includes('youtube.com')) {
      const video = document.querySelector('video');
      if (video && !isNaN(video.currentTime)) {
        videoTimestamp = Math.floor(video.currentTime);
      }
    }

    sendResponse({ selectedText, bodyText, videoTimestamp });
  }
});

// Initialize - render highlights after page loads
if (document.readyState === 'complete') {
  renderSavedHighlights();
} else {
  window.addEventListener('load', renderSavedHighlights);
}
