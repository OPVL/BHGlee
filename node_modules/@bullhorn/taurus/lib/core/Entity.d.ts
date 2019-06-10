import { AxiosResponse } from 'axios';
import { StatefulSubject } from './StatefulSubject';
import { EntityService } from '../services';
import { EntityMessageBroker } from '../broker';
import { EntityOptions } from './EntityOptions';
export interface Identity {
    id?: number;
}
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
export declare class Entity<T extends Identity> extends StatefulSubject<T> {
    protected _fields: string[];
    [propName: string]: any;
    type: string;
    descriptor: any;
    protected $entity: EntityService<T>;
    protected broker: EntityMessageBroker;
    /**
     * constructor
     * @param  state data to inflate object with
     */
    constructor(type: string, options?: EntityOptions, state?: T);
    /**
     * Define the fields to set or retrieve for the given entity. Getter and Setter methods will automagically be set up on the entity once the fields are defined.
     * @param args - Fields can either be sent as a list of arguments or as an Array.
     */
    fields(...args: any[]): Entity<T>;
    private _proxy(field);
    /**
     * Will merge object into the entity's parameter to be sent in any http request.
     * @param object - all additional parameters
     */
    params(object: any): Entity<T>;
    /**
     * Used to replace the current entitys source data
     * @param data - packet of data to replace object with
     */
    set(value: any): Entity<T>;
    /**
     * Used to update only properties sent in the packet
     * @param data - packet of data to update object with
     */
    patch(data: any, ignoreEvent?: boolean): Entity<T>;
    /**
     * Make http request to get entity. Objects 'data' property will be set to response, then promise will be resolved.
     * @param id - Id of the Model to retrieve
     */
    get(id: number): Entity<T>;
    /**
     * Make http request to get entity's many relationship. Response will update the value of the entity object.
     * @param property - The TO_MANY Association field
     * @param fields - Additional fields to retrieve on the TO_MANY field
     */
    many(property: string, fields: string[]): Entity<T>;
    /**
     * Create or Updates the entity based on the presence of an 'id' property
     */
    save(): Promise<AxiosResponse>;
    /**
     * Sends a request to delete the entity
     */
    remove(): Promise<AxiosResponse>;
    private _setUpObservable(state);
}
