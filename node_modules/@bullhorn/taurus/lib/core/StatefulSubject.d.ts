import { Observable, Subject, Subscription } from 'rxjs';
export declare const EMPTY: Symbol;
/**
 * Observable subject that may have an initial value
 */
export declare class StatefulSubject<T> extends Subject<T> {
    private _value;
    constructor(_value?: T | Symbol);
    readonly snapshot: T;
    readonly value: T;
    _subscribe(subscriber: any): Subscription;
    getValue(): T;
    hasValue(): boolean;
    next(value: T): void;
    once(): Observable<T>;
}
