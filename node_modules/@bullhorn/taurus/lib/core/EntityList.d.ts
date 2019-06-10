import { StatefulSubject } from './StatefulSubject';
import { Entity } from './Entity';
import { QueryService, SearchService } from '../services';
import { EntityListOptions } from './EntityListOptions';
import { EntityMessageBroker } from '../broker';
export declare type EntityListReference<T> = SearchService<T> | QueryService<T>;
export declare type EntityOperation = number | Entity<any>;
export interface ListResults {
    start: number;
    count: number;
    data: any[];
    total?: number;
}
export declare class EntityList<T> extends StatefulSubject<T[]> {
    static useSearch: boolean;
    type: string;
    descriptor: any;
    private $latest;
    private readonly $ref;
    private readonly $list;
    protected broker: EntityMessageBroker;
    constructor(type: string, options?: EntityListOptions, state?: T[]);
    protected getSearcher(type: string): EntityListReference<T>;
    readonly lastAdded: Entity<T>;
    readonly info: any;
    findById(pk: number): Entity<T>;
    push(item: T): Promise<any>;
    update(item: EntityOperation, value: T): Promise<any>;
    remove(item: EntityOperation): Promise<any>;
    private _setUpObservable();
    private _eventHook(eventType);
    private _checkOperationCases(item, cases);
}
