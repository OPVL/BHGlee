import { Observable } from 'rxjs';
export interface SerializedListOptions {
    [key: string]: any;
    fields?: string[];
    layout?: string;
    meta?: string;
    orderBy?: string;
    startAt?: number;
    limitTo?: number;
    filter?: Object;
    params?: Object;
}
export interface EntityListOptions {
    [key: string]: any;
    fields?: string[] | Observable<string[]>;
    layout?: string | Observable<string>;
    meta?: string | Observable<string>;
    orderBy?: string | Observable<string> | any;
    startAt?: number | Observable<number>;
    limitTo?: number | Observable<number>;
    filter?: Object | Observable<Object>;
    params?: Object | Observable<Object>;
}
export declare function observeListOptions(options: EntityListOptions, audit?: boolean): Observable<SerializedListOptions | null>;
