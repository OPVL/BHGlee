/**
 * A Class that implements the basic storage interface, used as an alternative to localStorage.
 */
export declare class Memory implements Storage {
    private dataStore;
    [key: string]: any;
    [index: number]: string;
    /**
     * Will store the item in memory
     * @param key - The key used to identify the stored value
     * @param value - The value to be stored
     */
    setItem(key: string, value: any): void;
    /**
     * Will retrieve the value from storage and return it.
     * @param key - The key for the value to be returned
     * @returns value - The value associated with the key
     */
    getItem(key: string): string | null;
    /**
     * Will remove the item from storage
     * @param key - The key for the value to be removed
     */
    removeItem(key: string): void;
    /**
     * Will remove the all items from storage
     */
    clear(): void;
    /**
     * The size of the storage object
     */
    readonly length: number;
    key(index: number): string | null;
}
