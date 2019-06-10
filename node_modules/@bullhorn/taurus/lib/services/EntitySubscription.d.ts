import { AxiosInstance } from 'axios';
import { BullhornSubscriptionResponse, BullhornSubscriptionEvent } from '../types';
import { EntityMessageBroker } from '../broker';
/**
 * A Class that defines the base Entity Model
 * @example
 * ```
 * //Most methods on the entity are fluent (chainable)
 * var job = new Entity('JobOrder').fields('title');
 * //the 'fields' function created a getter and setter for 'title'
 * job.title = 'My New Job';
 * job.save();
 * ```
 */
export declare class EntitySubscription {
    private readonly subscriptionId;
    types: string[];
    http: AxiosInstance;
    readonly subscriptionType: string;
    readonly eventTypes: string[];
    protected broker: EntityMessageBroker;
    _lastRequestId: number;
    /**
     * constructor
     * @param  subscriptionId name of the subscription
     * @param  types List of Entity events to listen to
     */
    constructor(subscriptionId: string, types?: string[]);
    readonly endpoint: string;
    subscribe(): Promise<BullhornSubscriptionResponse>;
    unsubscribe(): Promise<BullhornSubscriptionResponse>;
    /**
     * Make http request to get entity. Objects 'data' property will be set to response, then promise will be resolved.
     * @param id - Id of the Model to retrieve
     */
    get(): Promise<BullhornSubscriptionEvent[]>;
    private _setUpObservable();
}
