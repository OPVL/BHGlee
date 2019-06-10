import { AxiosInstance } from 'axios';
import { BullhornListResponse, FieldMapOption } from '../types';
/**
 * A base class for making Options calls via Rest
 */
export declare class OptionsService {
    optionType: string;
    http: AxiosInstance;
    records: any[];
    parameters: any;
    protected _page: number;
    protected _endpoint: string;
    protected _lastResponse: BullhornListResponse<FieldMapOption>;
    /**
     * constructor description
     * @param endpoint - Base Url for all relative http calls eg. 'options/JobOrder'
     */
    constructor(optionType: string);
    endpoint: string;
    readonly snapshot: BullhornListResponse<FieldMapOption>;
    filter(value: string): this;
    count(value: number): this;
    page(value: number): this;
    nextpage(): Promise<BullhornListResponse<FieldMapOption>>;
    params(object: any): this;
    get(add: any): Promise<BullhornListResponse<FieldMapOption>>;
    run(add: any): Promise<BullhornListResponse<FieldMapOption>>;
    then(done: any, fail?: any): Promise<any>;
}
