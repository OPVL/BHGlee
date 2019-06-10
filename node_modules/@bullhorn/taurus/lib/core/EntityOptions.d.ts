import { Observable } from 'rxjs';
export declare type Primitive = number | string | boolean | string[] | Object;
export interface SerializedOptions {
    [key: string]: any;
    id?: number;
    fields?: string[];
    layout?: string;
    meta?: string;
    showEditable?: boolean;
    showReadOnly?: boolean;
    executeFormTriggers?: boolean;
    params?: Object;
}
export interface EntityOptions {
    [key: string]: any;
    id?: number | Observable<number>;
    fields?: string[] | Observable<string[]>;
    layout?: string | Observable<string>;
    meta?: string | Observable<string>;
    showEditable?: boolean | Observable<boolean>;
    showReadOnly?: boolean | Observable<boolean>;
    executeFormTriggers?: boolean | Observable<boolean>;
    params?: Object | Observable<Object>;
}
export declare function observeOptions(options: EntityOptions, audit?: boolean): Observable<SerializedOptions | null>;
export declare function getOrCreateObservable(key: string, options: EntityOptions): Observable<Primitive>;
