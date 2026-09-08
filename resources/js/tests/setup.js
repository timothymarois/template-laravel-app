import { enableAutoUnmount } from '@vue/test-utils';
import { afterEach } from 'vitest';

// mount() does not tear down when a case ends. A component that binds a listener on
// window or document therefore keeps answering events while the NEXT case runs, and the
// assertion reports the leftover component's behaviour instead of the one under test —
// which reads as the component doing the opposite of its own code.
// DialogConfirmation binds a document-level capture listener, so this is live here.
enableAutoUnmount(afterEach);
