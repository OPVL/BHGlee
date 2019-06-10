import { QueryService } from './QueryService';
/**
 * A class for making Search calls via Rest
 */
export declare class SearchService<T> extends QueryService<T> {
    /**
     * constructor description
     * @param endpoint - Base Url for all relative http calls eg. 'search/JobOrder'
     */
    constructor(entity: any);
    endpoint: string;
    sort(...args: any[]): this;
    where(value: any): this;
    query(value: any): this;
    then(done: any, fail?: any): Promise<any>;
}
