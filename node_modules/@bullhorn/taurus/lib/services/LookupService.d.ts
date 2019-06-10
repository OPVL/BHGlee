import { AxiosInstance } from 'axios';
import { BullhornLookupItem } from '../types';
export interface LookupOptions {
    entity?: string[];
    filter?: string;
    count?: number;
    isCountPerEntity?: boolean;
}
/**
 * A base class for making Options calls via Rest
 */
export declare class LookupService {
    types: string[];
    private readonly options;
    http: AxiosInstance;
    records: BullhornLookupItem[];
    parameters: any;
    protected _endpoint: string;
    /**
     * constructor description
     * @param endpoint - Base Url for all relative http calls eg. 'options/JobOrder'
     */
    constructor(types: string[], options?: LookupOptions);
    endpoint: string;
    filter(value: string): this;
    count(value: number): this;
    params(object: LookupOptions): this;
    get(): Promise<BullhornLookupItem[]>;
    run(): Promise<BullhornLookupItem[]>;
    then(done: any, fail?: any): Promise<any>;
}
