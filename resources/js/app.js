import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Highlight from '@tiptap/extension-highlight';
import TaskList from '@tiptap/extension-task-list';
import TaskItem from '@tiptap/extension-task-item';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import Chart from 'chart.js/auto';

window.Chart = Chart;

// Register Alpine stores and components before Livewire's Alpine starts
document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;

    // Dark mode toggle
    Alpine.store('darkMode', {
        on: localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggle() {
            this.on = !this.on;
            localStorage.setItem('darkMode', this.on);
            document.documentElement.classList.toggle('dark', this.on);
        },
        init() {
            document.documentElement.classList.toggle('dark', this.on);
        }
    });

    // Tiptap Editor Alpine component
    Alpine.data('tiptapEditor', (content = '', placeholder = 'Start writing...') => ({
        editor: null,
        content: content,
        init() {
            this.editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit.configure({
                        heading: { levels: [1, 2, 3] },
                    }),
                    Highlight,
                    TaskList,
                    TaskItem.configure({ nested: true }),
                    Link.configure({ openOnClick: false }),
                    Placeholder.configure({ placeholder }),
                ],
                content: this.content,
                editorProps: {
                    attributes: {
                        class: 'prose dark:prose-invert prose-sm max-w-none focus:outline-none min-h-[200px] px-4 py-3',
                    },
                },
                onUpdate: ({ editor }) => {
                    this.content = editor.getHTML();
                    this.$dispatch('editor-update', { html: this.content, text: editor.getText() });
                },
            });
        },
        isActive(type, attrs = {}) {
            return this.editor?.isActive(type, attrs) ?? false;
        },
        toggleBold() { this.editor?.chain().focus().toggleBold().run(); },
        toggleItalic() { this.editor?.chain().focus().toggleItalic().run(); },
        toggleStrike() { this.editor?.chain().focus().toggleStrike().run(); },
        toggleHighlight() { this.editor?.chain().focus().toggleHighlight().run(); },
        toggleBulletList() { this.editor?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { this.editor?.chain().focus().toggleOrderedList().run(); },
        toggleTaskList() { this.editor?.chain().focus().toggleTaskList().run(); },
        toggleCode() { this.editor?.chain().focus().toggleCodeBlock().run(); },
        toggleBlockquote() { this.editor?.chain().focus().toggleBlockquote().run(); },
        setHeading(level) { this.editor?.chain().focus().toggleHeading({ level }).run(); },
        setLink() {
            const url = prompt('URL:');
            if (url) { this.editor?.chain().focus().setLink({ href: url }).run(); }
        },
        destroy() { this.editor?.destroy(); },
    }));

    // Drag and Drop Alpine component for bookmarks to collections
    Alpine.data('bookmarkDragDrop', () => ({
        dragging: null,
        dragOver: null,
        handleDragStart(e, bookmarkId) {
            this.dragging = bookmarkId;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', bookmarkId);
        },
        handleDragEnd() {
            this.dragging = null;
            this.dragOver = null;
        },
        handleDragOver(e, collectionId) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.dragOver = collectionId;
        },
        handleDragLeave() {
            this.dragOver = null;
        },
        handleDrop(e, collectionId) {
            e.preventDefault();
            const bookmarkId = e.dataTransfer.getData('text/plain');
            this.dragOver = null;
            this.dragging = null;
            if (bookmarkId && collectionId) {
                this.$wire.moveBookmarkToCollection(parseInt(bookmarkId), collectionId);
            }
        },
    }));
});
