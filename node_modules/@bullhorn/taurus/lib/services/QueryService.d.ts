import { AxiosInstance } from 'axios';
import { BullhornListResponse } from '../types';
import { MetaService } from './MetaService';
/**
 * A base class for making Query calls via Rest
 */
export declare class QueryService<T> {
    entity: string;
    http: AxiosInstance;
    meta: MetaService;
    records: any[];
    parameters: any;
    protected _page: number;
    protected _endpoint: string;
    protected _lastResponse: BullhornListResponse<T>;
    /**
     * constructor description
     * @param endpoint - Base Url for all relative http calls eg. 'query/JobOrder'
     */
    constructor(entity: string);
    endpoint: string;
    readonly total: Promise<number>;
    readonly snapshot: BullhornListResponse<T>;
    fields(...args: any[]): this;
    sort(...args: any[]): this;
    where(value: any): this;
    query(value: any): this;
    count(value: number): this;
    page(value: number): this;
    nextpage(): Promise<BullhornListResponse<T>>;
    params(object: any): this;
    get(add: any): Promise<BullhornListResponse<T>>;
    run(add: any): Promise<BullhornListResponse<T>>;
    then(done: any, fail?: any): Promise<any>;
}
