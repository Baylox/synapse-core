import { Controller } from '@hotwired/stimulus';

/*
 * Drives the Synapse chat UI: posts questions to /api/chat, uploads documents
 * to /api/documents, and renders answers with their source citations.
 */
export default class extends Controller {
    static targets = ['messages', 'question', 'submit', 'uploadForm', 'documents'];

    async ask(event) {
        event.preventDefault();

        const question = this.questionTarget.value.trim();
        if (question === '') {
            return;
        }

        this.#appendMessage('user', question);
        this.questionTarget.value = '';
        this.#setBusy(true);

        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question }),
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error ?? 'Request failed');
            }

            this.#appendMessage('assistant', data.answer, data.sources ?? []);
        } catch (error) {
            this.#appendMessage('assistant', `⚠️ ${error.message}`);
        } finally {
            this.#setBusy(false);
        }
    }

    async upload(event) {
        event.preventDefault();

        const formData = new FormData(this.uploadFormTarget);
        const response = await fetch('/api/documents', { method: 'POST', body: formData });

        if (response.ok) {
            this.uploadFormTarget.reset();
            this.#appendMessage('assistant', '📄 Document received — ingesting in the background.');
        } else {
            this.#appendMessage('assistant', '⚠️ Upload failed.');
        }
    }

    #appendMessage(role, text, sources = []) {
        const item = document.createElement('div');
        item.className = `message message--${role}`;
        item.textContent = text;

        if (sources.length > 0) {
            const cite = document.createElement('small');
            cite.className = 'message__sources';
            cite.textContent = 'Sources: ' + sources.map((s) => s.document).join(', ');
            item.appendChild(cite);
        }

        this.messagesTarget.appendChild(item);
        this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight;
    }

    #setBusy(busy) {
        this.submitTarget.disabled = busy;
        this.submitTarget.textContent = busy ? '…' : 'Ask';
    }
}
