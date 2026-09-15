// The Worker in front of the published wiki: every request, static files
// included, must carry the user name and password held in the WIKI_USER and
// WIKI_PASSWORD secrets before the ASSETS binding serves it. Adapted from
// Cloudflare's HTTP Basic Authentication example.

const encoder = new TextEncoder();

// Compares in constant time, so the time taken reveals nothing about the secret.
function timingSafeEqual(a, b) {
    const aBytes = encoder.encode(a);
    const bBytes = encoder.encode(b);
    if (aBytes.byteLength !== bBytes.byteLength) {
        return !crypto.subtle.timingSafeEqual(aBytes, aBytes);
    }
    return crypto.subtle.timingSafeEqual(aBytes, bBytes);
}

function signIn() {
    return new Response('Sign in to read the documentation.', {
        status: 401,
        headers: { 'WWW-Authenticate': 'Basic realm="docs", charset="UTF-8"' },
    });
}

export default {
    async fetch(request, env) {
        const { WIKI_USER, WIKI_PASSWORD } = env;
        if (!WIKI_USER || !WIKI_PASSWORD) {
            return new Response('WIKI_USER and WIKI_PASSWORD are not set.', { status: 500 });
        }
        const [scheme, encoded] = (request.headers.get('Authorization') || '').split(' ');
        if ((scheme || '').toLowerCase() !== 'basic' || !encoded) {
            return signIn();
        }
        let credentials;
        try {
            credentials = atob(encoded);
        } catch {
            return signIn();
        }
        const colon = credentials.indexOf(':');
        const user = credentials.substring(0, colon);
        const password = credentials.substring(colon + 1);
        if (colon < 0 || !timingSafeEqual(WIKI_USER, user) || !timingSafeEqual(WIKI_PASSWORD, password)) {
            return signIn();
        }
        return env.ASSETS.fetch(request);
    },
};
