@props(['name', 'id' => null, 'value' => '', 'placeholder' => 'Describe the dish…'])
@php($editorId = $id ?? $name . '_' . uniqid())

<div class="rte-wrap" data-rte>
    <div class="rte-toolbar" role="toolbar" aria-label="Formatting">
        <button type="button" class="rte-btn" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
        <button type="button" class="rte-btn" data-cmd="italic" title="Italic"><i
                class="bi bi-type-italic"></i></button>
        <button type="button" class="rte-btn" data-cmd="underline" title="Underline"><i
                class="bi bi-type-underline"></i></button>
        <span class="rte-sep"></span>
        <button type="button" class="rte-btn" data-cmd="insertUnorderedList" title="Bullet list"><i
                class="bi bi-list-ul"></i></button>
        <button type="button" class="rte-btn" data-cmd="insertOrderedList" title="Numbered list"><i
                class="bi bi-list-ol"></i></button>
        <span class="rte-sep"></span>
        <button type="button" class="rte-btn" data-cmd="removeFormat" title="Clear formatting"><i
                class="bi bi-eraser"></i></button>
    </div>
    <div class="rte-editor" id="{{ $editorId }}_editor" contenteditable="true" data-placeholder="{{ $placeholder }}">
        {!! $value !!}</div>
    <input type="hidden" name="{{ $name }}" id="{{ $editorId }}_input" value="{{ $value }}">
</div>

@once
    <style>
        .rte-wrap {
            border: 1px solid var(--border, #ced4da);
            border-radius: var(--radius-sm, 6px);
            overflow: hidden;
        }

        .rte-toolbar {
            display: flex;
            align-items: center;
            gap: 2px;
            padding: 4px;
            background: #f8f9fa;
            border-bottom: 1px solid var(--border, #ced4da);
        }

        .rte-btn {
            border: 0;
            background: transparent;
            width: 28px;
            height: 28px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #495057;
            cursor: pointer;
        }

        .rte-btn:hover {
            background: #e9ecef;
        }

        .rte-btn.active {
            background: #dee2e6;
            color: #000;
        }

        .rte-sep {
            width: 1px;
            height: 18px;
            background: var(--border, #ced4da);
            margin: 0 4px;
        }

        .rte-editor {
            min-height: 80px;
            padding: 8px 10px;
            font-size: 0.9rem;
            outline: none;
        }

        .rte-editor:empty:before {
            content: attr(data-placeholder);
            color: #adb5bd;
        }

        .rte-editor ul,
        .rte-editor ol {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }
    </style>

    <script>
        (function () {
             function initEditor(wrap) {
                if (wrap.dataset.rteInit) return;

                const editor = wrap.querySelector('.rte-editor');
                const input = wrap.querySelector('input[type="hidden"]');
                const buttons = wrap.querySelectorAll('.rte-btn');

                // Guard: if this specific instance is missing a piece it needs,
                // skip it quietly instead of throwing and blocking every other
                // editor/modal on the page from finishing setup.
                if (!editor || !input) {
                    console.warn('Rich text editor: missing editor or input element, skipping init for this instance.', wrap);
                    return;
                }

                wrap.dataset.rteInit = '1';

                function sync() {
                    input.value = editor.innerHTML;
                }

                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        editor.focus();
                        document.execCommand(btn.dataset.cmd, false, null);
                        sync();
                    });
                });

                editor.addEventListener('input', sync);
                editor.addEventListener('blur', sync);

                // Keep the hidden input in sync with whatever the editor started with.
                sync();

                // If this editor lives inside a Bootstrap modal, focusing it before the
                // modal has fully opened can misplace the caret — re-sync once shown.
                const modal = wrap.closest('.modal');
                if (modal) {
                    modal.addEventListener('shown.bs.modal', sync);
                }
            }
            function initAll() {
                document.querySelectorAll('[data-rte]').forEach(initEditor);
            }

            document.addEventListener('DOMContentLoaded', initAll);
            // Menu item list re-renders modals per row on load, but in case content is
            // swapped in later (e.g. via AJAX elsewhere), watch for new editors too.
            new MutationObserver(initAll).observe(document.body, { childList: true, subtree: true });
        })();
    </script>
@endonce