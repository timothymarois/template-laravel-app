/**
 * Normalizes and ensures a URL includes a valid protocol.
 * @param url - Input string (e.g. 'google.com')
 * @param https - Force HTTPS (default: false)
 * @returns A valid HTTP(S) URL (e.g. 'https://google.com')
 */
export const formatValidURL = (url: string, https = false): string => {
    if (!url) return '';
    url = url.trim();

    let protocol = https ? 'https' : 'http';
    let rest = url;

    const hasProtocol = /^https?:\/\//i.test(rest);
    if (hasProtocol) {
        const isHttpsURL = /^https:\/\//i.test(rest);
        protocol = https || isHttpsURL ? 'https' : 'http';
        rest = rest.replace(/^https?:\/\//i, '');
    }

    const hostEnd = rest.search(/[/?#]/);
    const hostname = hostEnd === -1 ? rest : rest.slice(0, hostEnd);
    const tail = hostEnd === -1 ? '' : rest.slice(hostEnd);

    return `${protocol}://${hostname.toLowerCase()}${tail}`;
};
