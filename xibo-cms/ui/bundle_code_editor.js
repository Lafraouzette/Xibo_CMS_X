

// CodeMirror
import {basicSetup} from 'codemirror';
import {EditorView, keymap} from '@codemirror/view';
import {indentWithTab} from '@codemirror/commands';
import {javascript} from '@codemirror/lang-javascript';
import {css} from '@codemirror/lang-css';
import {html} from '@codemirror/lang-html';
import {twig} from '@ssddanbrown/codemirror-lang-twig';

window.CodeMirror = {
  basicSetup,
  EditorView,
  keymap,
  indentWithTab,
  javascript,
  css,
  html,
  twig,
};
