/**
 * A utility class to manage Query String paramaters, handles encoding and arrays
 * @param url - url to append parameters to
 * @param params - parameter to append to url
 */
export declare class QueryString {
    url: string;
    parts: string[];
    constructor(url: string, params?: Object);
    static destruct(location: any): Object;
    static encodeParams(params: any): string[];
    static stringify(params: any): string;
    /**
     * Convert to string
     */
    toString(): string;
}
