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

  // Color button handlers
  tooltip.querySelectorAll('.color-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      selectedColor = btn.dataset.color;
      highlightSelection(text, selectedColor);
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

  const data = {
    text,
    color: HIGHLIGHT_COLORS.find(c => c.name === color)?.value || '#FBBF24',
    page_url: window.location.href,
    start_xpath: getXPath(selectedRange.startContainer),
    start_offset: selectedRange.startOffset,
    end_xpath: getXPath(selectedRange.endContainer),
    end_offset: selectedRange.endOffset,
    surrounding_text: getSurroundingText(selectedRange),
  };

  // First highlight locally
  highlightSelection(text, color);

  // Then send to background to save via API
  try {
    const response = await chrome.runtime.sendMessage({
      type: 'SAVE_HIGHLIGHT',
      data,
    });

    if (response?.success) {
      showSavedNotification();
    }
  } catch (e) {
    console.warn('BrainVault: Failed to save highlight', e);
  }
}

function getXPath(node) {
  if (!node) return '';

  if (node.nodeType === Node.TEXT_NODE) {
    return getXPath(node.parentNode) + '/text()';
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

// Render saved highlights on page load
async function renderSavedHighlights() {
  try {
    const response = await chrome.runtime.sendMessage({
      type: 'GET_HIGHLIGHTS_FOR_URL',
      url: window.location.href,
    });

    if (!response?.success || !response.highlights?.data) return;

    for (const highlight of response.highlights.data) {
      try {
        restoreHighlight(highlight);
      } catch (e) {
        // Silently skip highlights that can't be restored
      }
    }
  } catch (e) {
    // Extension context may not be available
  }
}

function restoreHighlight(highlight) {
  // Try to find the text node using XPath
  const result = document.evaluate(
    highlight.start_xpath,
    document,
    null,
    XPathResult.FIRST_ORDERED_NODE_TYPE,
    null
  );

  const node = result.singleNodeValue;
  if (!node) return;

  const range = document.createRange();
  const textNode = node.nodeType === Node.TEXT_NODE ? node : node.firstChild;
  if (!textNode) return;

  range.setStart(textNode, Math.min(highlight.start_offset, textNode.length));
  range.setEnd(textNode, Math.min(highlight.end_offset, textNode.length));

  const mark = document.createElement('mark');
  mark.className = 'brainvault-highlight';
  mark.dataset.color = HIGHLIGHT_COLORS.find(c => c.value === highlight.color)?.name || 'yellow';
  mark.dataset.highlightId = highlight.id;

  try {
    range.surroundContents(mark);
  } catch (e) {
    // Can't wrap complex selections
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
