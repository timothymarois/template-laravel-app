/**
 * Transform a string/number into a comma seperated number
 * like 10000 into 10,000
 * @param { String|Number } n
 * @param { Number } decimals
 * @returns
 */
export const transformNumber = (n, decimals = 2) => {
    if (n === '' || n === null || n === undefined ) return '';
    if (isNaN(n)) return 'Invalid';
    const num = Number(n).toFixed(decimals);
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

/**
 * Formats a string or number into a currency
 * like: 1000000 into $1,000,000
 * @param { String|Number } v
 * @param { String<USD>} currency
 * @returns
 */
export const formatCurrency = (v, currency = 'USD') => {
    if (isNaN(v)) return 'Invalid';
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency }).format(v);
};

/**
 * Creates a safe slug for URL
 * @param { String } str
 * @returns a formatted string
 */
export const formatSlug = (str) => {
    return str
      .toLowerCase()
      .replace(/[^a-z0-9-]+/g, '-')
      .replace(/^-+|-+$/g, '');
};

/**
 * Formats a string and ensures its a URL
 * @param { String } url
 * @returns formatted URL string
 */
export const formatURL = (url) => {
    // remove any whitespace at the start or end of the URL
    url = url.trim();
    // check if the URL starts with 'http://' or 'https://'
    if (!/^https?:\/\//i.test(url)) {
        // if it doesn't, add 'https://' to the start of the URL
        url = 'https://' + url;
    }
    return url;
}
