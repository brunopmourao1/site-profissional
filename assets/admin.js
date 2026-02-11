function wrapSelection(textarea, before, after) {
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const value = textarea.value;
  const selected = value.slice(start, end);
  const replacement = `${before}${selected}${after}`;
  textarea.value = value.slice(0, start) + replacement + value.slice(end);
  textarea.focus();
  textarea.selectionStart = start + before.length;
  textarea.selectionEnd = start + before.length + selected.length;
}

function convertList(textarea, ordered) {
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const value = textarea.value;
  const selected = value.slice(start, end).trim();
  if (!selected) return;

  const lines = selected
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

  if (!lines.length) return;
  const items = lines.map((line) => `<li>${line}</li>`).join('');
  const tag = ordered ? 'ol' : 'ul';
  const replacement = `<${tag}>${items}</${tag}>`;

  textarea.value = value.slice(0, start) + replacement + value.slice(end);
  textarea.focus();
}

document.querySelectorAll('.editor-toolbar').forEach((toolbar) => {
  const textarea = toolbar.parentElement.querySelector('.rich-text-target');
  if (!textarea) return;

  toolbar.querySelectorAll('.format-btn').forEach((button) => {
    button.addEventListener('click', () => {
      const command = button.dataset.command;
      if (command === 'bold') wrapSelection(textarea, '<strong>', '</strong>');
      if (command === 'italic') wrapSelection(textarea, '<em>', '</em>');
      if (command === 'underline') wrapSelection(textarea, '<u>', '</u>');
      if (command === 'insertUnorderedList') convertList(textarea, false);
      if (command === 'insertOrderedList') convertList(textarea, true);
    });
  });
});
